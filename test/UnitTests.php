<?php

require_once 'config.inc.php';

require_once 'SimpleTest/unit_tester.php';
require_once 'SimpleTest/reporter.php';

//require_once 'CRM/Core/Error.php';
require_once 'CRM/Core/Config.php';
require_once 'CRM/Core/I18n.php';

if ( !defined( 'TEST' ) ) {
    define( 'TEST', __FILE__ );
}

class UnitTests extends GroupTest {
    
    function __construct( ) {
        parent::__construct( 'Unit Tests for CRM' );
        
        /**
        $this->addTestFile( CRM_TEST_DIR . 'CRM/api/CreateContact.php' );
        $this->addTestFile( CRM_TEST_DIR . 'CRM/api/GetContact.php'    );
        $this->addTestFile( CRM_TEST_DIR . 'CRM/api/UpdateContact.php' );
        $this->addTestFile( CRM_TEST_DIR . 'CRM/api/DeleteContact.php'    );
        $this->addTestFile( CRM_TEST_DIR . 'CRM/api/CreateLocation.php'    );
        $this->addTestFile( CRM_TEST_DIR . 'CRM/api/GetLocation.php'    );
        $this->addTestFile( CRM_TEST_DIR . 'CRM/api/UpdateLocation.php'    );
        $this->addTestFile( CRM_TEST_DIR . 'CRM/api/DeleteLocation.php'    );
        **/

        //$this->addTestFile( CRM_TEST_DIR . 'CRM/api/CreateActivityHistory.php' );
        $this->addTestFile( CRM_TEST_DIR . 'CRM/api/GetActivityHistory.php' );

    }

}

if ( TEST == __FILE__ ) {
    $test =& new UnitTests( );

    $config = CRM_Core_Config::singleton();
    CRM_Core_DAO::init($config->dsn, $config->daoDebug);
    $factoryClass = 'CRM_Contact_DAO_Factory';
    CRM_Core_DAO::setFactory(new $factoryClass());

    // set error handling
    // PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, array('CRM_Core_Error', 'handle'));
    PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, array('CRM_Core_Error', 'callback'));


    if ( SimpleReporter::inCli( ) ) {
        exit( $test->run( new TextReporter( ) ) ? 0 : 1 );
    }
    $test->run( new HtmlReporter( ) );
}

?>