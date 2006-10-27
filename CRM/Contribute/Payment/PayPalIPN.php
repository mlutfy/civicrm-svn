<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions      |
 | about the Affero General Public License or the licensing  of       |
 | CiviCRM, see the CiviCRM license FAQ at                            |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

class CRM_Contribute_Payment_PayPalIPN {
    static function recur( ) {
    }

    static function single( ) {
        $store = null;

        require_once 'CRM/Utils/Request.php';

        // get the contribution, contact and contributionType ids from the GET params
        $contactID          = CRM_Utils_Request::retrieve( 'contactID', 'Integer', $store,
                                                           false, null, 'GET' );
        $contributionID     = CRM_Utils_Request::retrieve( 'contributionID', 'Integer', $store,
                                                           false, null, 'GET' );
        $contributionTypeID = CRM_Utils_Request::retrieve( 'contributionTypeID', 'Integer', $store,
                                                         false, null, 'GET' );


        // make sure the invoice is valid and matches what we have in the contribution record
        require_once 'CRM/Contribute/DAO/Contribution.php';
        $contribution =& new CRM_Contribute_DAO_Contribution( );
        $contribution->id = $contributionID;
        if ( ! $contribution->find( true ) ) {
            CRM_Core_Error::debug_log_message( "Could not find contribution record: $contributionID" );
            return;
        }

        require_once 'CRM/Contribute/DAO/ContributionType.php';
        $contributionType =& new CRM_Contribute_DAO_ContributionType( );
        $contributionType->id = $contributionTypeID;
        if ( ! $contributionType->find( true ) ) {
            CRM_Core_Error::debug_log_message( "Could not find contribution type record: $contributionTypeID" );
            return;
        }

        $invoice = CRM_Utils_Request::retrieve( 'invoice', 'String', $store,
                                                false, null, 'POST' );
        if ( $contribution->invoice_id != $invoice ) {
            CRM_Core_Error::debug_log_message( "Invoice values dont match between database and IPN request" );
            return;
        }

        $amount = CRM_Utils_Request::retrieve( 'payment_gross', 'Money', $store,
                                               false, null, 'POST' );
        if ( $contribution->total_amount != $amount ) {
            CRM_Core_Error::debug_log_message( "Amount values dont match between database and IPN request" );
            return;
        }
        
        CRM_Core_DAO::transaction( 'BEGIN' );

        $status = CRM_Utils_Request::retrieve( 'payment_status', 'String', $store,
                                               false, 0, 'POST' );
        if ( $status == 'Denied' || $status == 'Failed' || $status == 'Voided' ) {
            $contribution->status_id = 4;
            $contribution->save( );
            CRM_Core_DAO::transaction( 'COMMIT' );
            CRM_Core_Error::debug_log_message( "Setting contribution status to failed" );
            return;
        } else if ( $status == 'Pending' ) {
            CRM_Core_Error::debug_log_message( "returning since contribution status is pending" );
            return;
        } else if ( $status != 'Completed' ) {
            // we dont handle this as yet
            CRM_Core_Error::debug_log_message( "returning since contribution status: $status is not handled" );
            return;
        }

        $contribution->status_id  = 1;
        $contribution->is_test    = CRM_Utils_Request::retrieve( 'test_ipn', 'Integer', $store,
                                                              false, 0, 'POST' );
        $contribution->fee_amount = CRM_Utils_Request::retrieve( 'payment_fee', 'Money', $store, 
                                                                 false, 0, 'POST' );
        $contribution->net_amount = CRM_Utils_Request::retrieve( 'settle_amount', 'Money', $store,  
                                                                 false, 0, 'POST' ); 
        $contribution->trxn_id    = CRM_Utils_Request::retrieve( 'txn_id', 'String', $store,
                                                                 false, 0, 'POST' );
        $now = date( 'YmdHis' );
        $contribution->receive_date = null; // lets keep this the same
        $contribution->receipt_date = date( 'YmdHis' );
        $contribution->save( );

        // next create the transaction record
        $trxnParams = array(
                            'entity_table'      => 'civicrm_contribution',
                            'entity_id'         => $contribution->id,
                            'trxn_date'         => $now,
                            'trxn_type'         => 'Debit',
                            'total_amount'      => $amount,
                            'fee_amount'        => $contribution->fee_amount,
                            'net_amount'        => $contribution->net_amount,
                            'currency'          => $contribution->currency,
                            'payment_processor' => $config->paymentProcessor,
                            'trxn_id'           => $contribution->trxn_id,
                            );
        
        require_once 'CRM/Contribute/BAO/FinancialTrxn.php';
        $trxn =& CRM_Contribute_BAO_FinancialTrxn::create( $trxnParams );

        // get the title of the contribution page
        $title = CRM_Core_DAO::getFieldValue( 'CRM_Contribution_DAO_ContributionPage',
                                              $contribution->contribution_page_id,
                                              'title' );

        // also create an activity history record
        require_once 'CRM/Utils/Money.php';
        $ahParams = array('entity_table'     => 'civicrm_contact', 
                          'entity_id'        => $contactID, 
                          'activity_type'    => $contributionType->name,
                          'module'           => 'CiviContribute', 
                          'callback'         => 'CRM_Contribute_Page_Contribution::details',
                          'activity_id'      => $contribution->id, 
                          'activity_summary' => CRM_Utils_Money::format($amount). ' - ' . $title . ' (online)',
                          'activity_date'    => $now,
                          );

        if ( is_a( crm_create_activity_history($params), 'CRM_Core_Error' ) ) { 
            CRM_Core_Error::debug_log_message( "error in updating activity" );
        }

        CRM_Core_Error::debug_log_message( "Contribution record updated successfully" );
        CRM_Core_DAO::transaction( 'COMMIT' );
    }

    static function main( ) {
        CRM_Core_Error::debug_var( 'GET' , $_GET , true, true );
        CRM_Core_Error::debug_var( 'POST', $_POST, true, true );

        if ( array_key_exists( 'recur', $_GET ) &&
             $_GET['recur'] ) {
            return self::recur( );
        }

        return self::single( );
    }

}

?>
