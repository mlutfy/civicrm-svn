<?php

session_start( );

require_once '../civicrm.config.php';
require_once 'CRM/Core/Config.php';


// build the query
function invoke( ) {
    // intialize the system
    $config =& CRM_Core_Config::singleton( );

    // also use SOAP as your base class
    $config->userFramework          = 'Soap';
    $config->userFrameworkClass     = 'CRM_Utils_System_Soap';
    $config->userHookClass          = 'CRM_Utils_Hook_Soap';
    $config->userPermissionClass    = 'CRM_Core_Permission_Soap';
    $q = $_GET['q'];
    $args = explode( '/', $q );
    if ( $args[0] != 'civicrm' ) {
        exit( );
    }

    switch ( $args[1] ) {

    case 'help':
        return help( $config );

    case 'search':
        return search( $config );

    case 'status':
        return status( $config );

    default:
        return;
    }

}

function help( &$config ) {
    $id   = urldecode( $_GET['id'] );
    $file = urldecode( $_GET['file'] );

    $template =& CRM_Core_Smarty::singleton( );
    $file = str_replace( '.tpl', '.hlp', $file );

    $template->assign( 'id', $id );
    echo $template->fetch( $file );
}

function search( &$config ) {
    require_once 'CRM/Utils/Type.php';
    $domainID = CRM_Utils_Type::escape( $_GET['d'], 'Integer' );
    $name     = strtolower( CRM_Utils_Type::escape( $_GET['s'], 'String'  ) );

    $query = "
SELECT sort_name
  FROM civicrm_contact
 WHERE domain_id = $domainID
   AND LOWER( sort_name ) LIKE '$name%'";
    $dao = CRM_Core_DAO::executeQuery( $query, CRM_Core_DAO::$_nullArray );

    $count = 0;
    $elements = array( );
    while ( $dao->fetch( ) && $count < 5 ) {
        $n = '"' . $dao->sort_name . '"';
        $elements[] = "[ $n, $n ]";
        $count++;
    }

    echo '[' . implode( ',', $elements ) . ']';
}

function status( &$config ) {
    // make sure we get an id
    if ( ! isset( $_GET['id'] ) ) {
        return;
    }

    $file = "{$config->uploadDir}status_{$_GET['id']}.txt";
    if ( file_exists( $file ) ) {
        $str = file_get_contents( $file );
        echo $str;
    } else {
        echo "No status recorded for $file as yet<p>";
    }
    
}

invoke( );

exit( );
?>