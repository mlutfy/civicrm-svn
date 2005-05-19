<?php
/*
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Contact/DAO/Address.php';

/**
 * BAO object for crm_address table
 */
class CRM_Contact_BAO_Address extends CRM_Contact_DAO_Address {
    function __construct()
    {
        parent::__construct();
    }


    /**
     * takes an associative array and creates a contact object
     *
     * the function extract all the params it needs to initialize the create a
     * contact object. the params array could contain additional unused name/value
     * pairs
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     * @param array  $ids            the array that holds all the db ids
     * @param array  $locationId     
     *
     * @return object CRM_Contact_BAO_Address object
     * @access public
     * @static
     */
    static function add(&$params, &$ids, $locationId)
    {
        if (!self::dataExists($params, $locationId, $ids)) {
            return null;
        }

        $address = new CRM_Contact_BAO_Address();
        $address->location_id = $params['location'][$locationId]['id'];
        $address->copyValues($params['location'][$locationId]['address']);

        // currently copy values populates empty fields with the string "NULL"
        // and hence need to check for the string NULL
        //if ($address->state_province_id && (!$address->country_id)) {
        if ($address->state_province_id && ($address->country_id == 'NULL')) {
            // since state id present and country id not present, hence lets populate it
            // jira issue http://objectledge.org/jira/browse/CRM-56
            $stateProvinceDAO = new CRM_Core_DAO_StateProvince();
            $stateProvinceDAO->id = $address->state_province_id; 
            $stateProvinceDAO->find(true);
            $address->country_id = $stateProvinceDAO->country_id;
        }

        $address->county_id = $address->geo_coord_id = 1;
        $address->id = CRM_Utils_Array::value('address', $ids['location'][$locationId]);
        return $address->save();
    }

    /**
     * Check if there is data to create the object
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     * @param array  $locationId     
     * @param array  $ids            the array that holds all the db ids
     *
     * @return boolean
     * @access public
     * @static
     */
    static function dataExists(&$params, $locationId, &$ids = '')
    {

        // return if no data present

        if (! array_key_exists('address' , $params['location'][$locationId])) {
            return false;
        }

        if (is_array($ids)) {
            if (CRM_Utils_Array::value('address', $ids['location'][$locationId])) {
                return true;
            }
        }

        foreach ($params['location'][$locationId]['address'] as $name => $value) {
            /*
            if ($name == 'country_id' || $name == 'state_province_id') {
                return false;
            }
            */

            if (!empty($value)) {
                return true;
            }
        }
        
        return false;
    }


    /**
     * Given the list of params in the params array, fetch the object
     * and store the values in the values array
     *
     * @param array $params        input parameters to find object
     * @param array $values        output values of the object
     * @param array $ids           the array that holds all the db ids
     * @param int   $blockCount    number of blocks to fetch
     *
     * @return void
     * @access public
     * @static
     */
    static function getValues(&$params, &$values, &$ids, $blockCount=0)
    {
        $address = new CRM_Contact_BAO_Address();
        $address->copyValues($params);

        $flatten = false;
        if (empty($blockCount)) {
            $flatten = true;
        }
        
        // we first get the primary location due to the order by clause
        if ($address->find(true)) {
            $ids['address'] = $address->id;
            if ($flatten) {
                $address->storeValues($values);
            } else {
                $values['address'] = array();
                $address->storeValues($values['address']);
            }
            return $address;
        }
        return null;
    }

}

?>