<?php
/**
 * File containing the ezcBaseInitInvalidCallbackClassException class
 *
 * @package Configuration
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception that is thrown if an invalid class is passed as callback class for
 * delayed object configuration.
 *
 * @package Configuration
 * @version 1.3.1
 */
class ezcBaseInitInvalidCallbackClassException extends ezcBaseException
{
    /**
     * Constructs a new ezcBaseInitInvalidCallbackClassException for the $callbackClass.
     *
     * @param string $callbackClass
     * @return void
     */
    function __construct( $callbackClass )
    {
        parent::__construct( "Class '{$callbackClass}' does not exist, or does not implement the 'ezcBaseConfigurationInitializer' interface." );
    }
}
?>
