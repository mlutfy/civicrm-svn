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
 * Component stores all the static and dynamic information of the various
 * CiviCRM components
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

class CRM_Core_Component 
{

    /*
     * End part (filename) of the component information class'es name 
     * that needs to be present in components main directory.
     */
    const COMPONENT_INFO_CLASS = 'Info';

    private static $_info = null;

    static $_contactSubTypes = null;

    private function &_info( ) {
        if( self::$_info == null ) {
            self::$_info = array( );
            $c = array();
            
            $config =& CRM_Core_Config::singleton( );
            
            /* FIXME: hack to bypass getComponents, if running upgrade to avoid
               any serious non-recoverable error which might hinder the
               upgrade process. */
            $args = array( );
            if ( isset( $_GET[$config->userFrameworkURLVar] ) ) {
                $args = explode( '/', $_GET[$config->userFrameworkURLVar] );
            }

            if ( CRM_Utils_Array::value( 1, $args ) != 'upgrade' ) {
                $c =& self::getComponents();
            }

            foreach( $c as $name => $comp ) {
                if ( in_array( $name, $config->enableComponents ) ) {
                    self::$_info[$name] = $comp;
                }
            }
        }
        
        return self::$_info;
    }

    static function get( $name, $attribute = null) 
    {
        $comp = CRM_Utils_Array::value( $name, self::_info() );
        if ( $attribute ) {
            return CRM_Utils_Array::value( $attribute, $comp->info );
        }
        return $comp;
    }

    public function &getComponents( $force = false )
    {
        static $_cache = null;

        if ( ! $_cache || $force ) {
            $_cache = array( );

            require_once 'CRM/Core/DAO/Component.php';
            $cr =& new CRM_Core_DAO_Component();
            $cr->find( false );
            while ( $cr->fetch( ) ) {
                $infoClass = $cr->namespace . '_' . self::COMPONENT_INFO_CLASS;
                require_once( str_replace( '_', DIRECTORY_SEPARATOR, $infoClass ) . '.php' );
                $infoObject = new $infoClass( $cr->name, $cr->namespace, $cr->id );
                if( $infoObject->info['name'] !== $cr->name ) {
                    CRM_Core_Error::fatal( "There is a discrepancy between name in component registry and in info file ({$cr->name})." );
                }
                $_cache[$cr->name] = $infoObject;
                unset( $infoObject );
            }
        }

        return $_cache;
    }

    public function &getEnabledComponents( )
    {
        return self::_info();
    }

    static function invoke( &$args, $type ) 
    {
        $info =& self::_info( );
        $config =& CRM_Core_Config::singleton( );

        $firstArg  = CRM_Utils_Array::value( 1, $args, '' ); 
        $secondArg = CRM_Utils_Array::value( 2, $args, '' ); 
        foreach ( $info as $name => $comp ) {
            if ( in_array( $name, $config->enableComponents ) &&
                 ( ( $comp->info['url'] === $firstArg  && $type == 'main' )  ||
                   ( $comp->info['url'] === $secondArg && $type == 'admin' ) ) ) {
                if ( $type == 'main' ) {
                    // also set the smarty variables to the current component
                    $template =& CRM_Core_Smarty::singleton( );
                    $template->assign( 'activeComponent', $name );
                    if( CRM_Utils_Array::value( 'formTpl', $comp->info[$name] ) ) {
                        $template->assign( 'formTpl', $comp->info[$name]['formTpl'] );
                    }
                    if( CRM_Utils_Array::value( 'css', $comp->info[$name] ) ) {
                        $styleSheets = '<style type="text/css">@import url(' . 
                                       "{$config->resourceBase}css/{$comp->info[$name]['css']});</style>";
                        CRM_Utils_System::addHTMLHead( $styleSheet );
                    }
                }
                $inv =& $comp->getInvokeObject();
                $inv->$type( $args );
                return true;
            }
        }
        return false;
    }

    static function xmlMenu( ) {

        // lets build the menu for all components
        $info =& self::getComponents( true );

        $files = array( );
        foreach( $info as $name => $comp ) {
            $files = array_merge( $files,
                                  $comp->menuFiles( ) );
        }
        return $files;
    }

    static function &menu( ) 
    {
        $info =& self::_info( );
        $items = array( );
        foreach( $info as $name => $comp ) {
            $mnu   =& $comp->getMenuObject( );

            $ret   = $mnu->permissioned( );
            $items = array_merge( $items, $ret );

            $ret   = $mnu->main( $task );
            $items = array_merge( $items, $ret );
        }
        return $items;
    }

    static function addConfig( &$config, $oldMode = false ) 
    {
        $info =& self::_info( );

        foreach( $info as $name => $comp ) {
            $cfg =& $comp->getConfigObject( );
            $cfg->add( $config, $oldMode );
        }
        return;
    }

    static function &getQueryFields( ) 
    {
        $info =& self::_info( );
        $fields = array( );
        foreach( $info as $name => $comp ) {
            if( $comp->usesSearch( ) ) {
                $bqr =& $comp->getBAOQueryObject( );
                $flds =& $bqr->getFields( );
                $fields = array_merge( $fields, $flds );
            }
        }
        return $fields;
    }

    static function alterQuery( &$query, $fnName ) 
    {
        $info =& self::_info( );

        foreach( $info as $name => $comp ) {
            if( $comp->usesSearch( ) ) {
                $bqr =& $comp->getBAOQueryObject( );
                $bqr->$fnName( $query );
            }
        }
    }

    static function from( $fieldName, $mode, $side ) 
    {
        $info =& self::_info( );

        $from = null;
        foreach( $info as $name => $comp ) {
            if( $comp->usesSearch( ) ) {
                $bqr =& $comp->getBAOQueryObject( );
                $from = $bqr->from( $fieldName, $mode, $side );
                if( $from ) {
                    return $from;
                }
            }
        }
        return $from;
    }

    static function &defaultReturnProperties( $mode ) 
    {
        $info =& self::_info( );

        $properties = null;
        foreach( $info as $name => $comp ) {
            if( $comp->usesSearch( ) ) {
                $bqr =& $comp->getBAOQueryObject( );
                $properties =& $bqr->defaultReturnProperties( $mode );
                if( $properties ) {
                    return $properties;
                }
            }
        }
        return $properties;
    }

    static function &buildSearchForm( &$form ) 
    {
        $info =& self::_info( );

        foreach( $info as $name => $comp ) {
            if( $comp->usesSearch( ) ) {
                $bqr =& $comp->getBAOQueryObject( );
                $bqr->buildSearchForm( $form );
            }
        }
    }

    static function &addShowHide( &$showHide ) 
    {
        $info =& self::_info( );

        foreach( $info as $name => $comp ) {
            if( $comp->usesSearch( ) ) {
                $bqr =& $comp->getBAOQueryObject( );
                $bqr->addShowHide( $showHide );
            }
        }
    }

    static function searchAction( &$row, $id ) 
    {
        $info =& self::_info( );

        foreach( $info as $name => $comp ) {
            if( $comp->usesSearch( ) ) {
                $bqr =& $comp->getBAOQueryObject( );
                $bqr->searchAction( $row, $id );
            }
        }
    }

    static function &contactSubTypes( ) 
    {
        if( self::$_contactSubTypes == null ) {
            self::$_contactSubTypes = array( );

            if( CRM_Core_Permission::access( 'Quest' ) ) {
            
            // Generalize this at some point
            self::$_contactSubTypes =
                array(
                     'Student' =>
                      array( 'View' => 
                             array( 'file'  => 'CRM/Quest/Page/View/Student.php',
                                    'class' => 'CRM_Quest_Page_View_Student' ),
                             )
                      );
            }
        }
        return self::$_contactSubTypes;
    }

    
    static function &contactSubTypeProperties( $subType, $op ) 
    {
        $properties =& self::contactSubTypes( );
        if( array_key_exists( $subType, $properties ) &&
             array_key_exists( $op, $properties[$subType] ) ) {
            return $properties[$subType][$op];
        }
        return CRM_Core_DAO::$_nullObject;
    }

    static function &taskList( ) 
    {
        $info =& self::_info( );
        
        $tasks = array( );
        foreach( $info as $name => $value ) {
            if( CRM_Utils_Array::value( 'task', $info[$name] ) ) {
                $tasks += $info[$name]['task'];
            }
        }
        return $tasks;
    }

    /**
     * Function to handle table dependencies of components
     *
     * @param array $tables  array of tables
     *
     * @return null
     * @access public
     * @static
     */
    static function tableNames( &$tables ) 
    {
        $info =& self::_info( );

        foreach( $info as $name => $comp ) {
            if( $comp->usesSearch( ) ) {
                $bqr =& $comp->getBAOQueryObject( );
                $bqr->tableNames( $tables );
            }
        }
    }

}


