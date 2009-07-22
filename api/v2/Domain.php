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
 * File for the CiviCRM APIv2 domain functions
 *
 * @package CiviCRM_APIv2
 * @subpackage API_Domain
 *
 * @copyright CiviCRM LLC (c) 2004-2009
 * @version $Id$
 *
 */

/**
 * Include utility functions
 */
require_once 'api/v2/utils.php';

/**
 * Generic file to retrieve all the constants and
 * pseudo constants used in CiviCRM
 *
 */
function civicrm_domain_get( ) {
    require_once 'CRM/Core/BAO/Domain.php';
    $dao = CRM_Core_BAO_Domain::getDomain();
    $values = array();
    $params = array(
                    'entity_id'    => $dao->id,
                    'entity_table' => 'civicrm_domain'
    );
    require_once 'CRM/Core/BAO/Location.php';
    $values['location'] = CRM_Core_BAO_Location::getValues( $params, true );
    $address_array = array ( 'street_address', 'supplemental_address_1', 'supplemental_address_2',
                             'city', 'state_province_id', 'postal_code', 'country_id', 'geo_code_1', 'geo_code_2' );
    $domain = array(
                    'id'           => $dao->id,
                    'domain_name'  => $dao->name,
                    'description'  => $dao->description,
                    'domain_email' => CRM_Utils_Array::value( 'email', $values['location']['email'][1] ),
                    'domain_phone' => array(
                                            'phone_type'=> CRM_Core_OptionGroup::getLabel( 'phone_type', CRM_Utils_Array::value('phone_type_id',$values['location']['phone'][1] ) ),
                                            'phone'     => CRM_Utils_Array::value( 'phone', $values['location']['phone'][1] )
        )
    );
    foreach ( $address_array as $value ) {
        $domain['domain_address'][$value] = CRM_Utils_Array::value( $value, $values['location']['address'][1] );
    }
    list( $domain['from_name'], $domain['from_email'] ) = CRM_Core_BAO_Domain::getNameAndEmail();
    return $domain;
}