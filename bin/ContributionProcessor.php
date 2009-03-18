<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
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
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

class CiviContributeProcessor {
    static $_paypalParamsMapper = 
        array(
              //category    => array(paypal_param    => civicrm_param);
              'contact'     => array(
                                     'salutation'    => 'prefix_id',
                                     'firstname'     => 'first_name',
                                     'lastname'      => 'last_name',
                                     'middlename'    => 'middle_name',
                                     'suffix'        => 'suffix_id',
                                     'ordertime'     => 'receive_date',
                                     'email'         => 'email',
                                     ),
              'location'    => array(
                                     'shiptoname'    => 'address_name',
                                     'shiptostreet'  => 'street_address',
                                     'shiptostreet2' => 'supplemental_address_1',
                                     'shiptocity'    => 'city',
                                     'shiptostate'   => 'state',
                                     'shiptozip'     => 'postal_code',
                                     'countrycode'   => 'country',
                                     ),
              'transaction' => array(
                                     'amt'           => 'total_amount',
                                     'feeamt'        => 'fee_amount',
                                     'transactionid' => 'trxn_id',
                                     'currencycode'  => 'currencyID',
                                     'source'        => 'contribution_source',
                                     'note'          => 'note',
                                     'is_test'       => 'is_test',
                                     ),
              );

    static $_googleParamsMapper = 
        array(
              'contact'     => array(
                                     'contact-name'  => 'display_name',
                                     'contact-name'  => 'sort_name',
                                     'email'         => 'email',
                                     ),
              'location'    => array(
                                     'address1'     => 'street_address',
                                     'city'         => 'city',
                                     'postal-code'  => 'postal_code',
                                     'country-code' => 'country',
                                     ),
              'transaction' => array(
                                     'total-charge-amount' => 'total_amount',
                                     'google-order-number' => 'trxn_id',
                                     ),
              );

    static function paypal( $paymentProcessor, $paymentMode, $start, $end ) {
        $url       = "{$paymentProcessor['url_api']}nvp";

        $keyArgs = array( 'user'      => $paymentProcessor['user_name'],
                          'pwd'       => $paymentProcessor['password'] ,
                          'signature' => $paymentProcessor['signature'], 
                          'version'   => 3.0,
                          );

        $args =  $keyArgs;
        $args += array( 'method'    => 'TransactionSearch',
                        'startdate' => $start,
                        'enddate'   => $end );

        require_once 'CRM/Core/Payment/PayPalImpl.php';
        $result = CRM_Core_Payment_PayPalImpl::invokeAPI( $args, $url );

        require_once "CRM/Contribute/BAO/Contribution/Utils.php";

        $keyArgs['method'] = 'GetTransactionDetails';
        foreach ( $result as $name => $value ) {
            if ( substr( $name, 0, 15 ) == 'l_transactionid' ) {
                $keyArgs['transactionid'] = $value;
                $details = CRM_Core_Payment_PayPalImpl::invokeAPI( $keyArgs, $url );

                // only process completed emails
                if ( strtolower( $details['paymentstatus'] ) != 'completed' ) {
                    continue;
                }

                // add source
                $details['source'] = ts( 'ContributionProcessor: Paypal API' );

                if ( $paymentMode == 'test' ) {
                    $details['is_test'] = 1;
                } else {
                    $details['is_test'] = 0;
                }
                if ( CRM_Contribute_BAO_Contribution_Utils::processAPIContribution( $details, 
                                                                                    self::$_paypalParamsMapper ) ) {
                    echo "Processing {$details['email']}, {$details['amt']}, {$details['transactionid']}<p>";
                } else {
                    echo "Skipped {$details['email']}, {$details['amt']}, {$details['transactionid']}<p>";
                }
            }
        }
    }

    static function google( $paymentProcessor, $paymentMode, $start, $end ) {
        $searchParams = array( 'start' => $start, 
                               'end'   => $end  );

        require_once 'CRM/Core/Payment/Google.php';
        $result = CRM_Core_Payment_Google::invokeAPI( $paymentProcessor, $searchParams );
        //CRM_Core_Error::debug( '$result', $result );

        $result = CRM_Core_Payment_Google::processAPIContribution( $result, self::$_googleParamsMapper );
    }

    static function process( ) {
        require_once 'CRM/Utils/Request.php';

        $type = CRM_Utils_Request::retrieve( 'type', 'String', CRM_Core_DAO::$_nullObject, false, 'csv' );
        $type = strtolower( $type );

        
        switch ( $type ) {
        case 'paypal':
        case 'google':
            $start = CRM_Utils_Request::retrieve( 'start', 'String', CRM_Core_DAO::$_nullObject, false,
                                                  date( 'Y-m-d', time( ) - 365 * 24 * 60 * 60 ) . 'T00:00:00.00Z' );
            // google expects end date to be atleast 30 mins past
            $end   = CRM_Utils_Request::retrieve( 'end', 'String', CRM_Core_DAO::$_nullObject, false,
                                                  date( 'Y-m-d', time( ) - 24 * 60 * 60 ) . 'T23:59:00.00Z' );
            $ppID  = CRM_Utils_Request::retrieve( 'ppID'  , 'Integer', CRM_Core_DAO::$_nullObject, true  );
            $mode  = CRM_Utils_Request::retrieve( 'ppMode', 'String', CRM_Core_DAO::$_nullObject, false, 'live' );

            require_once 'CRM/Core/BAO/PaymentProcessor.php';
            $paymentProcessor = CRM_Core_BAO_PaymentProcessor::getPayment( $ppID,
                                                                           $mode );

            return self::$type( $paymentProcessor, $mode, $start, $end );

        case 'csv':
            return self::csv( );
        }
    }

}

// bootstrap the environment and run the processor
session_start();
require_once '../civicrm.config.php';
require_once 'CRM/Core/Config.php';
$config =& CRM_Core_Config::singleton();

// CRM_Utils_System::authenticateScript(true);

require_once 'CRM/Core/Lock.php';
$lock = new CRM_Core_Lock('CiviContributeProcessor');

if ($lock->isAcquired()) {
    // try to unset any time limits
    if (!ini_get('safe_mode')) set_time_limit(0);

    CiviContributeProcessor::process( );
} else {
    throw new Exception('Could not acquire lock, another CiviMailProcessor process is running');
}

$lock->release();
