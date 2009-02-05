<?php

// escape early if called directly
defined('_JEXEC') or die('No direct access allowed'); 

global $civicrmUpgrade;
$civicrmUpgrade = false;

function civicrm_setup( ) {
    global $adminPath, $compileDir;

    $adminPath =
        JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR .
        'components'        . DIRECTORY_SEPARATOR .
        'com_civicrm';

    $jConfig =& JFactory::getConfig( );
    set_time_limit(4000);

    // ensure that the site has native zip, else abort
    if ( ! function_exists('zip_open') ||
         ! function_exists('zip_read') ) {
        echo "Your PHP version is missing  zip functionality. Please ask your system administrator / hosting provider to recompile PHP with zip support.<p>";
        echo "If this is a new install, you will need to uninstall CiviCRM from the Joomla Extension Manager.<p>";
        exit( );
    }

    // Path to the archive
    $archivename = $adminPath . DIRECTORY_SEPARATOR . 'civicrm.zip';

    $extractdir  = $adminPath;
    JArchive::extract( $archivename, $extractdir);

    $scratchDir   = JPATH_SITE . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'civicrm';
    if ( ! is_dir( $scratchDir ) ) {
        JFolder::create( $scratchDir, 0777 );
    }
    
    $compileDir   = $scratchDir . DIRECTORY_SEPARATOR . 'templates_c';
    if ( ! is_dir( $compileDir ) ) {
        JFolder::create( $compileDir, 0777 );
    }

    $db =& JFactory::getDBO();
    $db->setQuery(' SELECT count( * )
FROM information_schema.tables
WHERE table_name LIKE "civicrm_domain"
AND table_schema = "' . $jConfig->getValue('config.db') .'" ');

    global $civicrmUpgrade;
    $civicrmUpgrade = ( $db->loadResult() == 0 ) ? false : true;
}

function civicrm_write_file( $name, &$buffer ) {
    JFile::write( $name, $buffer );
}

function civicrm_main( ) {
    global $civicrmUpgrade, $adminPath;

    civicrm_setup( );
    
    // setup vars
    $configFile = $adminPath . DIRECTORY_SEPARATOR . 'civicrm.settings.php';
    
    // generate backend config file
    $string = "
<?php
require_once '$configFile';
";
    $string = trim( $string );
    civicrm_write_file( $adminPath . DIRECTORY_SEPARATOR . 
                        'civicrm'  . DIRECTORY_SEPARATOR . 
                        'civicrm.config.php',
                        $string );

    // generate backend settings file
    $string = civicrm_config( false );
    civicrm_write_file( $configFile, $string );
        
    // generate frontend settings file
    $string = civicrm_config( true ); 
    civicrm_write_file( JPATH_SITE    . DIRECTORY_SEPARATOR . 
                        'components'  . DIRECTORY_SEPARATOR . 
                        'com_civicrm' . DIRECTORY_SEPARATOR . 
                        'civicrm.settings.php',
                        $string );

    include_once $configFile;
    
    // for install case only
    if ( ! $civicrmUpgrade ) {
        $sqlPath = 
            $adminPath . DIRECTORY_SEPARATOR . 
            'civicrm'  . DIRECTORY_SEPARATOR .
            'sql';
        
        civicrm_source( $sqlPath . DIRECTORY_SEPARATOR . 'civicrm.mysql'     );
        civicrm_source( $sqlPath . DIRECTORY_SEPARATOR . 'civicrm_data.mysql');

        require_once 'CRM/Core/Config.php';
        $config =& CRM_Core_Config::singleton( );
        
        // now also build the menu
        require_once 'CRM/Core/Menu.php';
        CRM_Core_Menu::store( );
    }
}

function civicrm_source( $fileName ) {

    $dsn = CIVICRM_DSN;

    require_once 'DB.php';

    $db  =& DB::connect( $dsn );
    if ( PEAR::isError( $db ) ) {
        die( "Cannot open $dsn: " . $db->getMessage( ) );
    }

    $string = JFile::read( $fileName );

    //get rid of comments starting with # and --
    $string = preg_replace("/^#[^\n]*$/m", "\n", $string );
    $string = preg_replace("/^\-\-[^\n]*$/m", "\n", $string );
    
    $queries  = explode( ';', $string );
    foreach ( $queries as $query ) {
        $query = trim( $query );
        if ( ! empty( $query ) ) {
            $res =& $db->query( $query );
            if ( PEAR::isError( $res ) ) {
                die( "Cannot execute $query: " . $res->getMessage( ) );
            }
        }
    }
}

function civicrm_config( $frontend = false ) {
    global $adminPath, $compileDir;

    $jConfig = new JConfig( );
    
    $liveSite = substr_replace(JURI::root(), '', -1, 1);
    $params = array(
                    'cms'        => 'Joomla',
                    'crmRoot'    => $adminPath . DIRECTORY_SEPARATOR . 'civicrm',
                    'templateCompileDir' => $compileDir,
                    'baseURL'    => $liveSite . '/administrator/',
                    'dbUser'     => $jConfig->user,
                    'dbPass'     => $jConfig->password,
                    'dbHost'     => $jConfig->host,
                    'dbName'     => $jConfig->db,
                    'CMSdbUser'  => $jConfig->user,
                    'CMSdbPass'  => $jConfig->password,
                    'CMSdbHost'  => $jConfig->host,
                    'CMSdbName'  => $jConfig->db,
                    );

    if ( $frontend ) {
        $params['baseURL']  = $liveSite . '/';
    }

    $str = JFile::read( $adminPath  . DIRECTORY_SEPARATOR . 
                        'civicrm'   . DIRECTORY_SEPARATOR . 
                        'templates' . DIRECTORY_SEPARATOR . 
                        'CRM'       . DIRECTORY_SEPARATOR . 
                        'common'    . DIRECTORY_SEPARATOR . 
                        'civicrm.settings.php.tpl' );
    foreach ( $params as $key => $value ) { 
        $str = str_replace( '%%' . $key . '%%', $value, $str ); 
    } 
    return trim( $str );
}

civicrm_main( );
