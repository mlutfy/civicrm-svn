<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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



require_once 'HTML/QuickForm/Rule/Email.php';

class CRM_Utils_Rule 
{

    static function title( $str ) 
    {
    
        // check length etc
        if ( empty( $str ) || strlen( $str ) < 3 || strlen( $str ) > 127 ) {
            return false;
        }
    
        // Make sure it include valid characters, alpha numeric and underscores
        if ( ! preg_match('/^[a-z][\w\s\'\&\,\$\#\-\.\"\?]+$/i', $str ) ) {
            return false;
        }

        return true;
    }

    static function longTitle( $str ) 
    {
        
        // check length etc
        if ( empty( $str ) || strlen( $str ) < 3 || strlen( $str ) > 255 ) {
            return false;
        }
        
        // Make sure it consists of valid characters, alpha numeric and underscores (and !)
        if ( ! preg_match('/^[a-z][\w\s\'\&\,\$\#\-\.\"\!\?]+$/i', $str ) ) {
            return false;
        }
        
        return true;
    }
    
    static function variable( $str ) 
    {
        // check length etc
        if ( empty( $str ) || strlen( $str ) > 31 ) {
            return false;
        }
        
        // make sure it include valid characters, alpha numeric and underscores
        if ( ! preg_match('/^[\w]+$/i', $str ) ) {
            return false;
        }

        return true;
    }

    static function qfVariable( $str ) 
    {
        // check length etc 
        //if ( empty( $str ) || strlen( $str ) > 31 ) {  
        if (  strlen(trim($str)) == 0 || strlen( $str ) > 31 ) {  
            return false; 
        } 
        
        // make sure it include valid characters, alpha numeric and underscores 
        // added (. and ,) option (CRM-1336)
        if ( ! preg_match('/^[\w\s\.\,]+$/i', $str ) ) { 
            return false; 
        } 
 
        return true; 
    } 

    static function phone( $phone ) 
    {
        // check length etc
        if ( empty( $phone ) || strlen( $phone ) > 16 ) {
            return false;
        }
    
        // make sure it include valid characters, (, \s and numeric
        if ( preg_match('/^[\d\(\)\-\.\s]+$/', $phone ) ) {
            return true;
        }
        return false;
    }


    static function query( $query ) 
    {

        // check length etc
        if ( empty( $query ) || strlen( $query ) < 3 || strlen( $query ) > 127 ) {
            return false;
        }
    
        // make sure it include valid characters, alpha numeric and underscores
        if ( ! preg_match('/^[\w\s\%\'\&\,\$\#]+$/i', $query ) ) {
            return false;
        }

        return true;
    }

    static function url( $url, $checkDomain = false) 
    {
        $options = array( 'domain_check'    => $checkDomain,
                          'allowed_schemes' => array( 'http', 'https', 'mailto', 'ftp' ) );

        require_once 'Validate.php';
        return Validate::uri( $url, $options );
    }

    static function wikiURL( $string ) {
        $items = explode( ' ', trim( $string ), 2 );
        return self::url( $items[0] );
    }

    static function domain( $domain ) 
    {
        // not perfect, but better than the previous one; see CRM-1502
        if ( ! preg_match('/^[A-Za-z0-9]([A-Za-z0-9\.\-]*[A-Za-z0-9])?$/', $domain ) ) {
            return false;
        }
        return true;
    }

    static function date($value, $default = null) 
    {
        if (is_string($value) &&
            preg_match('/^\d\d\d\d-?\d\d-?\d\d$/', $value)) {
            return $value;
        }
        return $default;
    }

    /**
     * check the validity of the date (in qf format)
     * note that only a year is valid, or a mon-year is
     * also valid in addition to day-mon-year
     *
     * @param array $date
     *
     * @return bool true if valid date
     * @static
     * @access public
     */
    static function qfDate($date) 
    {
        $config =& CRM_Core_Config::singleton( );

        $d = CRM_Utils_Array::value( 'd', $date );
        $m = CRM_Utils_Array::value( $config->dateformatMonthVar, $date );
        $y = CRM_Utils_Array::value( 'Y', $date );
        if( $date['h'] || $date['g'] ){
            $m = CRM_Utils_Array::value( $config->datetimeformatMonthVar, $date );
        }

        if ( ! $d && ! $m && ! $y ) {
            return true; 
        } 
 
        $day = $mon = 1; 
        $year = 0;
        if ( $d ) $day  = $d;
        if ( $m ) $mon  = $m;
        if ( $y ) $year = $y;

        // if we have day we need mon, and if we have mon we need year 
        if ( ( $d && ! $m ) || 
             ( $d && ! $y ) || 
             ( $m && ! $y ) ) { 
            return false; 
        } 

        if ( ! empty( $day ) || ! empty( $mon ) || ! empty( $year ) ) {
            return checkdate( $mon, $day, $year );
        }
        return false;
    }

    /** 
     * check the validity of the date (in qf format) 
     * note that only a year is valid, or a mon-year is 
     * also valid in addition to day-mon-year. The date
     * specified has to be beyond today. (i.e today or later)
     * 
     * @param array $date 
     * 
     * @return bool true if valid date 
     * @static 
     * @access public 
     */
    static function currentDate( $date ) 
    {
        $d = CRM_Utils_Array::value( 'd', $date );
        $m = CRM_Utils_Array::value( 'M', $date );
        $y = CRM_Utils_Array::value( 'Y', $date );

        if ( ! $d && ! $m && ! $y ) {
            return true; 
        } 
 
        $day = $mon = 1; 
        $year = 0; 
        if ( $d ) $day  = $d;
        if ( $m ) $mon  = $m;
        if ( $y ) $year = $y;
 
        // if we have day we need mon, and if we have mon we need year 
        if ( ( $d && ! $m ) || 
             ( $d && ! $y ) || 
             ( $m && ! $y ) ) { 
            return false; 
        } 

        $result = false;
        if ( ! empty( $day ) || ! empty( $mon ) || ! empty( $year ) ) { 
            $result = checkdate( $mon, $day, $year ); 
        }

        if ( ! $result ) {
            return false;
        }

        // now make sure this date is greater that today
        $currentDate = getdate( );
        if ( $year > $currentDate['year'] ) {
            return true;
        } else if ( $year < $currentDate['year'] ) {
            return false;
        }

        if ( $m ) {
            if ( $mon > $currentDate['mon'] ) {
                return true;
            } else if ( $mon < $currentDate['mon'] ) {
                return false;
            }
        }

        if ( $d ) {
            if ( $day > $currentDate['mday'] ) {
                return true;
            } else if ( $day < $currentDate['mday'] ) {
                return false;
            }
        }

        return true;
    }

    static function integer($value) 
    {
        if ( is_int($value)) {
            return true;
        }
        
        if (($value < 0)) {
            $negValue = -1 * $value;
            if(is_int($negValue)) {
                return true;
            }
        }

        if (is_numeric($value) && preg_match('/^\d+$/', $value)) {
            return true;
        }

        return false;
    }

    static function positiveInteger($value) 
    {
        if ( is_int($value) ) {
            return ( $value < 0 ) ? false : true;
        }

        if (is_numeric($value) && preg_match('/^\d+$/', $value)) {
            return true;
        }
        
        return false;
    }
    
    static function numeric($value) 
    {
        return preg_match( '/(^-?\d\d*\.\d*$)|(^-?\d\d*$)|(^-?\.\d\d*$)/', $value ) ? true : false;
    }

    static function numberOfDigit($value, $noOfDigit) 
    {
        return preg_match( '/^\d{'.$noOfDigit.'}$/', $value ) ? true : false;
    }

    static function cleanMoney( $value ) {
        // first remove all white space
        $value = str_replace( array( ' ', "\t", "\n" ), '', $value );

        $config =& CRM_Core_Config::singleton( );
        setlocale( LC_ALL, $config->lcMessages );
        $localeInfo = localeconv( );

        if ( array_key_exists( 'mon_thousands_sep', $localeInfo ) ) {
            $mon_thousands_sep = $localeInfo['mon_thousands_sep'];
        } else {
            $mon_thousands_sep = ',';
        }

        $value = str_replace( $mon_thousands_sep, '', $value );

        if ( array_key_exists( 'mon_decimal_point', $localeInfo ) ) {
            $mon_decimal_point = $localeInfo['mon_decimal_point'];
        } else {
            $mon_decimal_point = '.';
        }
        $value = str_replace( $mon_decimal_point, '.', $value );

        return $value;
    }

    static function money($value) 
    {
        $value = self::cleanMoney( $value );

        if ( self::integer( $value ) ) {
            return true;
        }

        return preg_match( '/(^\d+\.\d?\d?$)|(^\.\d\d?$)/', $value ) ? true : false;
    }

    static function string($value, $maxLength = 0) 
    {
        if (is_string($value) &&
            ($maxLength === 0 || strlen($value) <= $maxLength)) {
            return true;
        }
        return false;
    }

    static function boolean($value) 
    {
        return preg_match( 
            '/(^(1|0)$)|(^(Y(es)?|N(o)?)$)|(^(T(rue)?|F(alse)?)$)/i', $value) ?
            true : false;
    }

    static function email($value, $checkDomain = false) 
    {
        static $qfRule = null;
        if ( ! isset( $qfRule ) ) {
            $qfRule =& new HTML_QuickForm_Rule_Email();
        }
        return $qfRule->validate( $value, $checkDomain );
    }

    static function emailList( $list, $checkDomain = false ) 
    {
        $emails = explode( ',', $list );
        foreach ( $emails as $email ) {
            $email = trim( $email );
            if ( ! self::email( $email, $checkDomain ) ) {
                return false;
            }
        }
        return true;
    }

    // allow between 4-6 digits as postal code since india needs 6 and US needs 5 (or 
    // if u disregard the first 0, 4 (thanx excel!)
    // FIXME: we need to figure out how to localize such rules
    static function postalCode($value) 
    {
        if ( preg_match('/^\d{4,6}(-\d{4})?$/', $value) ) {
            return true;
        }
        return false;
    }

    /**
     * see how file rules are written in HTML/QuickForm/file.php
     * Checks to make sure the uploaded file is ascii
     *
     * @param     array     Uploaded file info (from $_FILES)
     * @access    private
     * @return    bool      true if file has been uploaded, false otherwise
     */
    static function asciiFile( $elementValue ) 
    {
        if ((isset($elementValue['error']) && $elementValue['error'] == 0) ||
            (!empty($elementValue['tmp_name']) && $elementValue['tmp_name'] != 'none')) {
            return CRM_Utils_File::isAscii($elementValue['tmp_name']);
        }
        return false;
    }

    /**
     * Checks to make sure the uploaded file is in UTF-8, recodes if it's not
     *
     * @param     array     Uploaded file info (from $_FILES)
     * @access    private
     * @return    bool      whether file has been uploaded properly and is now in UTF-8
     */
    static function utf8File( $elementValue ) 
    {
        $success = false;

        if ((isset($elementValue['error']) && $elementValue['error'] == 0) ||
            (!empty($elementValue['tmp_name']) && $elementValue['tmp_name'] != 'none')) {

            $success = CRM_Utils_File::isAscii($elementValue['tmp_name']);

            // if it's a file, but not UTF-8, let's try and recode it
            // and then make sure it's an UTF-8 file in the end
            if (!$success) {
                $success = CRM_Utils_File::toUtf8($elementValue['tmp_name']);
                if ($success) {
                    $success = CRM_Utils_File::isAscii($elementValue['tmp_name']);
                }
            }
        }
        return $success;
    }

    /**
     * see how file rules are written in HTML/QuickForm/file.php
     * Checks to make sure the uploaded file is html
     *
     * @param     array     Uploaded file info (from $_FILES)
     * @access    private
     * @return    bool      true if file has been uploaded, false otherwise
     */
    static function htmlFile( $elementValue ) 
    {
        if ((isset($elementValue['error']) && $elementValue['error'] == 0) ||
            (!empty($elementValue['tmp_name']) && $elementValue['tmp_name'] != 'none')) {
            return CRM_Utils_File::isHtmlFile($elementValue['tmp_name']);
        }
        return false;
    }

    /**
     * Check if there is a record with the same name in the db
     *
     * @param string $value     the value of the field we are checking
     * @param array  $options   the daoName and fieldName (optional )
     *
     * @return boolean     true if object exists
     * @access public
     * @static
     */
    static function objectExists( $value, $options ) 
    {
        $name = 'name';
        if ( isset($options[2]) ) {
            $name = $options[2];
        }
        return CRM_Core_DAO::objectExists( $value, $options[0], $options[1], CRM_Utils_Array::value( 2, $options, $name ) );
    }
    
    static function optionExists( $value, $options ) 
    {
        require_once 'CRM/Core/OptionValue.php';
        return CRM_Core_OptionValue::optionExists( $value, $options[0], $options[1], $options[2], CRM_Utils_Array::value( 3, $options, 'name' ) );
    }
    
    static function creditCardNumber( $value, $type ) 
    {
        require_once 'Validate/Finance/CreditCard.php';

        return Validate_Finance_CreditCard::number( $value, $type );
    }

    static function cvv( $value, $type ) 
    {
        require_once 'Validate/Finance/CreditCard.php';

        return Validate_Finance_CreditCard::cvv( $value, $type );
    }

    static function currencyCode($value) 
    {
        static $currencyCodes = null;
        if (!$currencyCodes) {
            $currencyCodes =& CRM_Core_PseudoConstant::currencyCode();
        }
        if (in_array($value, $currencyCodes)) {
            return true;
        }
        return false;
    }

    static function xssString( $value ) {
        return preg_match( '!<(vb)?script[^>]*>.*</(vb)?script.*>!ims',
                           $value ) ? false : true;
    }
}

?>
