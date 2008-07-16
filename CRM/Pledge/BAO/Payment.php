<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Pledge/DAO/Payment.php';

class CRM_Pledge_BAO_Payment extends CRM_Pledge_DAO_Payment
{

    /**
     * class constructor
     */
    function __construct( ) 
    {
        parent::__construct( );
    }
    
    /**
     * Function to get pledge payment details
     *  
     * @param int $pledgeId pledge id
     *
     * @return array associated array of pledge payment details
     * @static
     */
    static function getPledgePayments( $pledgeId )
    {
        $query = "
SELECT civicrm_pledge_payment.id id, scheduled_amount, scheduled_date, reminder_date, reminder_count,
        total_amount, receive_date, civicrm_option_value.name as status
FROM civicrm_pledge_payment
LEFT JOIN civicrm_contribution ON civicrm_pledge_payment.contribution_id = civicrm_contribution.id
LEFT JOIN civicrm_option_group ON ( civicrm_option_group.name = 'contribution_status' )
LEFT JOIN civicrm_option_value ON ( civicrm_pledge_payment.status_id = civicrm_option_value.value
AND civicrm_option_group.id = civicrm_option_value.option_group_id )
WHERE pledge_id = %1
";

        $params[1] = array( $pledgeId, 'Integer' );
        $payment = CRM_Core_DAO::executeQuery( $query, $params );

        $paymentDetails = array( );
        while ( $payment->fetch( ) ) {
            $paymentDetails[$payment->id]['scheduled_amount'] = $payment->scheduled_amount;
            $paymentDetails[$payment->id]['scheduled_date'  ] = $payment->scheduled_date;
            $paymentDetails[$payment->id]['reminder_date'   ] = $payment->reminder_date;
            $paymentDetails[$payment->id]['reminder_count'  ] = $payment->reminder_count;
            $paymentDetails[$payment->id]['total_amount'    ] = $payment->total_amount;
            $paymentDetails[$payment->id]['receive_date'    ] = $payment->receive_date;
            $paymentDetails[$payment->id]['status'          ] = $payment->status;
            $paymentDetails[$payment->id]['id'              ] = $payment->id;
        }
        
        return $paymentDetails;
    }

    static function create( $params )
    { 
        require_once 'CRM/Core/Transaction.php';
        $transaction = new CRM_Core_Transaction( );

        //calculation of schedule date according to frequency day of period
        //frequency day is not applicable for daily installments
        if ( $params['frequency_unit'] != 'day' ) {
            if ( $params['frequency_unit'] != 'week' ) {

                //for month use day of next month & for year use day of month Jan of next year as next payment date 
                $params['scheduled_date']['d'] = $params['frequency_day'];
                if ( $params['frequency_unit'] == 'year' ) {
                    $params['scheduled_date']['M'] = 1;
                }   
            } else if ( $params['frequency_unit'] == 'week' ) {

                //for week calculate day of week ie. Sunday,Monday etc. as next payment date
                $dayOfWeek = date('w',mktime(0, 0, 0, $params['scheduled_date']['M'], $params['scheduled_date']['d'], $params['scheduled_date']['Y'] ));
                $frequencyDay =  $dayOfWeek - $params['frequency_day'];
                if ( $frequencyDay < 0 ) {
                    $frequencyDay += 1;
                }
                $scheduleDate =  explode ( "-", date( 'n-j-Y', mktime ( 0, 0, 0, $params['scheduled_date']['M'], $params['scheduled_date']['d'] - $frequencyDay, $params['scheduled_date']['Y'] )) );
                $params['scheduled_date']['M'] = $scheduleDate[0];
                $params['scheduled_date']['d'] = $scheduleDate[1];
                $params['scheduled_date']['Y'] = $scheduleDate[2];
            }
        } 

        $prevScheduledDate =  $params['scheduled_date'];
        $params['scheduled_amount'] = ceil($params['scheduled_amount']);
        for ( $i = 1; $i <= $params['installments']; $i++ ) {
            //calculate the scheduled amount for every installment
            $params['scheduled_date'] =  CRM_Utils_Date::format(CRM_Utils_Date::intervalAdd( $params['frequency_unit'], $i, $prevScheduledDate ));
            
            if ( $i == $params['installments'] ) {
                $params['scheduled_amount'] = $params['amount'] - ($i-1) * $params['scheduled_amount'];
            }
            
            $payment = self::add( $params );
            if ( is_a( $payment, 'CRM_Core_Error') ) {
                $transaction->rollback( );
                return $payment;
            }
            
            // we should add contribution id to only first payment record
            if ( isset( $params['contribution_id'] ) ){
                unset( $params['contribution_id'] );
            }
        }

        $transaction->commit( );
               
        return $payment;
    }

    /**
     * Add pledge payment
     *
     * @param array $params associate array of field
     *
     * @return pledge payment id 
     * @static
     */
    static function add( $params )
    {
        require_once 'CRM/Pledge/DAO/Payment.php';
        $payment =& new CRM_Pledge_DAO_Payment( );
        $payment->copyValues( $params );
        $result = $payment->save( );
        
        return $result;
    }

    /**
     * Takes a bunch of params that are needed to match certain criteria and
     * retrieves the relevant objects. Typically the valid params are only
     * pledge id. We'll tweak this function to be more full featured over a period
     * of time. This is the inverse function of create. It also stores all the retrieved
     * values in the default array
     *
     * @param array $params   (reference ) an assoc array of name/value pairs
     * @param array $defaults (reference ) an assoc array to hold the flattened values
     *
     * @return object CRM_Pledge_BAO_Payment object
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults ) 
    {
        $payment =& new CRM_Pledge_DAO_Payment;
        $payment->copyValues( $params );
        if ( $payment->find( true ) ) {
            CRM_Core_DAO::storeValues( $payment, $defaults );
            return $payment;
        }
        return null;
    }
    
    /**
     * update Pledge Payment Status
     *
     * @param int $pledgeID, id of pledge
     * @param int $paymentID, id of payment
     * @param int $status, payment status
     *
     * @return void
     */
    function updatePledgePaymentStatus( $pledgeID, $paymentID = null, $status = null )
    {
        //get all status
        require_once 'CRM/Contribute/PseudoConstant.php';
        $allStatus = CRM_Contribute_PseudoConstant::contributionStatus( );
        
        //get all payments.
        $allPayments = self::getPledgePayments( $pledgeID );
        
        //build payment ids.
        $paymentIDs = array( );
        if ( $paymentID ) {
            $paymentIDs[] = $paymentID;
        } else {
            foreach( $allPayments as $payID => $values ) {
                $paymentIDs[] = $values['id'];
            }
        }
        
        //update pledge and payment status if status is Completed/Cancelled.
        $skipDBUpdate  = true;
        if ( $status ) {
            $skipDBUpdate = false;
            if ( $status == array_search( 'Completed', $allStatus ) ) {
                $isOverdue     = false;
                $allCompleted  = true;
                $paymentStatus = $status;
                
                foreach( $allPayments as $payID => $values ) {
                    //ignore current and Completed Payments.
                    if ( ($payID != $paymentIDs[0]) && ($values['status'] != 'Completed') ) {
                        $allCompleted = false;
                        if ( $values['status'] == 'Overdue' ) {
                            $isOverdue = true;
                        }
                    }
                }
                
                //decide the pledge status.
                if ( $allCompleted ) {
                    $pledgeStatus = array_search( 'Completed', $allStatus );
                } else if ( $isOverdue ) {
                    $pledgeStatus = array_search( 'Overdue', $allStatus );
                } else {
                    $pledgeStatus = array_search( 'In Progress', $allStatus );
                }
            } else if ( $status == array_search( 'Cancelled', $allStatus ) ) {
                $paymentStatus = $pledgeStatus = $status;
            }
        } else {
            //Keep Pledge and Pledge Payment statuses updated
            $overdueIDs = array( );
            foreach ( $allPayments as $key => $value ) {
                if ( $value['status'] != 'Completed' && CRM_Utils_Date::overdue( $value['scheduled_date'], null, false ) ) {
                    $overdueIDs[] = $value['id'];
                }
            }
            
            if ( !empty( $overdueIDs ) ) {
                $skipDBUpdate = false;
                $paymentIDs    = $overdueIDs;
                $paymentStatus = $pledgeStatus = array_search( 'Overdue', $allStatus );
            }
        }
        
        if ( ! $skipDBUpdate ) {
            //update pledge and pledge payment status.
            $params = array( 1 => array( $paymentStatus, 'Integer' ),
                             2 => array( $pledgeStatus, 'Integer' ),
                             3 => array( array_search( 'Completed', $allStatus ),
                                         'Integer') );
            $payments = implode( ',', $paymentIDs );
            
            $query = "
UPDATE civicrm_pledge_payment, civicrm_pledge
SET    civicrm_pledge_payment.status_id = %1, civicrm_pledge.status_id = %2
WHERE  civicrm_pledge_payment.id IN ( {$payments} )
  AND  civicrm_pledge_payment.pledge_id = civicrm_pledge.id 
  AND  civicrm_pledge_payment.status_id != %3
";
            $dao = CRM_Core_DAO::executeQuery( $query, $params );
        }
    }

    /**
     * Function to update pledge payment table when reminder is sent
     * @param int $paymentId payment id 
     * 
     * @static
     */
    static function updateReminderDetails( $paymentId )
    {
        $query = "
UPDATE civicrm_pledge_payment
SET civicrm_pledge_payment.reminder_date = CURRENT_TIMESTAMP,
    civicrm_pledge_payment.reminder_count = civicrm_pledge_payment.reminder_count + 1
WHERE  civicrm_pledge_payment.id = {$paymentId}
";
        $dao = CRM_Core_DAO::executeQuery( $query );
    }
}

