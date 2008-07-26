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
 * This file contains the various menus of the CiviCRM module
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/I18n.php';

class CRM_Core_Menu 
{
    /**
     * the list of menu items
     * 
     * @var array
     * @static
     */
    static $_items = null;

    /**
     * the list of permissioned menu items
     * 
     * @var array
     * @static
     */
    static $_permissionedItems = null;

    static $_serializedElements = array( 'access_arguments',
                                         'access_callback' ,
                                         'page_arguments'  ,
                                         'page_callback'   ,
                                         'breadcrumb'      );

    static $_menuCache = null;

    const
        MENU_ITEM  = 1;

    static function &xmlItems( ) {
        if ( ! self::$_items ) {
            $config =& CRM_Core_Config::singleton( );
            $coreMenuFiles = array( 'Activity', 'Contact', 'Import', 
                                    'Profile', 'Admin', 'Group', 'Misc', );

            $files = array( $config->templateDir . 'Menu/Activity.xml',
                            $config->templateDir . 'Menu/Contact.xml',
                            $config->templateDir . 'Menu/Custom.xml',
                            $config->templateDir . 'Menu/Import.xml',
                            $config->templateDir . 'Menu/Profile.xml',
                            $config->templateDir . 'Menu/Admin.xml',
                            $config->templateDir . 'Menu/Group.xml',
                            $config->templateDir . 'Menu/Misc.xml',
                            );

            $files = array_merge( $files,
                                  CRM_Core_Component::xmlMenu( ) );

            // lets call a hook and get any additional files if needed
            require_once 'CRM/Utils/Hook.php';
            CRM_Utils_Hook::xmlMenu( $files );

            self::$_items = array( );
            foreach ( $files as $file ) {
                self::read( $file, self::$_items );
            }
        }

        return self::$_items;
    }
    
    static function read( $name, &$menu ) {

        $config =& CRM_Core_Config::singleton( );

        $xml = simplexml_load_file( $name );
        foreach ( $xml->item as $item ) {
            if ( ! (string ) $item->path ) {
                CRM_Core_Error::debug( 'i', $item );
                CRM_Core_Error::fatal( );
            }
            $path = (string ) $item->path;
            $menu[$path] = array( );
            unset( $item->path );
            foreach ( $item as $key => $value ) {
                $key   = (string ) $key;
                $value = (string ) $value;
                if ( strpos( $key, '_callback' ) &&
                     strpos( $value, '::' ) ) {
                    $value = explode( '::', $value );
                } else if ( $key == 'access_arguments' ) {
                    if ( strpos( $value, ',' ) ||
                         strpos( $value, ';' ) ) {
                        if ( strpos( $value, ',' ) ) {
                            $elements = explode( ',', $value );
                            $op = 'and';
                        } else {
                            $elements = explode( ';', $element );
                            $op = 'or';
                        }
                        $items = array( );
                        foreach ( $elements as $element ) {
                            $items[] = $element;
                        }
                        $value = array( $items, $op );
                    } else {
                        $value = array( array( $value ), 'and' );
                    }
                } else if ( $key == 'is_public' ) {
                    $value = ( $value == 'true' || $value == 1 ) ? 1 : 0;
                }
                $menu[$path][$key] = $value;
            }
        }
    }

    /**
     * This function defines information for various menu items
     *
     * @static
     * @access public
     */
    static function &items( ) 
    {
        return self::xmlItems( );
    }

    static function isArrayTrue( &$values ) {
        foreach ( $values as $name => $value ) {
            if ( ! $value ) {
                return false;
            }
        }
        return true;
    }

    static function fillMenuValues( &$menu, $path ) {
        $fieldsToPropagate = array( 'access_callback',
                                    'access_arguments',
                                    'page_callback',
                                    'page_arguments' );
        $fieldsPresent = array( );
        foreach ( $fieldsToPropagate as $field ) {
            $fieldsPresent[$field] = CRM_Utils_Array::value( $field, $menu[$path] ) ?
                true : false;
        }

        $args = explode( '/', $path );
        while ( ! self::isArrayTrue( $fieldsPresent ) &&
                ! empty( $args ) ) {

            array_pop( $args );
            $parentPath = implode( '/', $args );

            foreach ( $fieldsToPropagate as $field ) {
                if ( ! $fieldsPresent[$field] ) {
                    if ( CRM_Utils_Array::value( $field, $menu[$parentPath] ) ) {
                        $fieldsPresent[$field] = true;
                        $menu[$path][$field] = $menu[$parentPath][$field];
                    }
                }
            }
        }

        if ( self::isArrayTrue( $fieldsPresent ) ) {
            return;
        }

        $messages = array( );
        foreach ( $fieldsToPropagate as $field ) {
            if ( ! $fieldsPresent[$field] ) {
                $messages[] = ts( "Could not find %1 in path tree",
                                  array( 1 => $field ) );
            }
        }
        CRM_Core_Error::fatal( "'$path': " . implode( ', ', $messages ) );
    }

    /**
     * We use this function to
     * 
     * 1. Compute the breadcrumb
     * 2. Compute local tasks value if any
     * 3. Propagate access argument, access callback, page callback to the menu item
     * 4. Build the global navigation block
     * 
     */
    static function build( &$menu ) {
        foreach ( $menu as $path => $menuItems ) {
            self::buildBreadcrumb ( $menu, $path );
            self::fillMenuValues  ( $menu, $path );
            self::fillComponentIds( $menu, $path );
            self::buildReturnUrl  ( $menu, $path );

            // add add page_type if not present
            if ( ! isset( $path['page_type'] ) ) {
                $path['page_type'] = 0;
            }

        }
        
        self::buildNavigation( $menu );

        self::buildAdminLinks( $menu );
    }

    static function store( ) {
        // first clean up the db
        $query = 'TRUNCATE civicrm_menu';
        CRM_Core_DAO::executeQuery( $query );

        $menu =& self::items( );

        self::build( $menu );

        require_once "CRM/Core/DAO/Menu.php";

        foreach ( $menu as $path => $item ) {
            $menu  =& new CRM_Core_DAO_Menu( );
            $menu->path      = $path;

            $menu->find( true );
            
            $menu->copyValues( $item );

            foreach ( self::$_serializedElements as $element ) {
                if ( ! isset( $item[$element] ) ||
                     $item[$element] == 'null' ) {
                    $menu->$element = null;
                } else {
                    $menu->$element = serialize( $item[$element] );
                }
            }

            $menu->save( );
        }
    }

    static function buildNavigation( &$menu ) {

        $components = array( ts( 'CiviContribute' ) => 1,
                             ts( 'CiviEvent'      ) => 1,
                             ts( 'CiviMember'     ) => 1,
                             ts( 'CiviMail'       ) => 1,
                             ts( 'Import'         ) => 1,
                             ts( 'CiviGrant'      ) => 1,
                             ts( 'PledgeBank'     ) => 1,
                             ts( 'CiviPledge'     ) => 1,
                             ts( 'Logout'         ) => 1);

        $values = array( );
        foreach ( $menu as $path => $item ) {
            if ( ! CRM_Utils_Array::value( 'page_type', $item ) ) {
                continue;
            }

            if ( $item['page_type'] ==  CRM_Core_Menu::MENU_ITEM ) {
                $query = CRM_Utils_Array::value( 'path_arguments', $item ) 
                    ? str_replace(',', '&', $item['path_arguments']) . '&reset=1' : 'reset=1';
                
                $value = array( );
                $value['url'  ]  = CRM_Utils_System::url( $path, $query );
                $value['title']  = $item['title'];
                $value['path']   = $path;
                $value['access_callback' ] = $item['access_callback' ];
                $value['access_arguments'] = $item['access_arguments'];
                $value['component_id'    ] = $item['component_id'    ];
                
                if ( array_key_exists( $item['title'], $components ) ) {
                    $value['class']  = 'collapsed';
                } else {
                    $value['class']  = 'leaf';
                }
                $value['parent'] = null;
                $value['start']  = $value['end'] = null;
                $value['active'] = '';

                // check if there is a parent
                foreach ( $values as $weight => $v ) {
                    if ( strpos( $path, $v['path'] ) !== false) {
                        $value['parent'] = $weight;

                        // only reset if still a leaf
                        if ( $values[$weight]['class'] == 'leaf' ) {
                            $values[$weight]['class'] = 'collapsed';
                        }
                    }
                }
                
                $values[$item['weight'] . '.' . $item['title']] = $value;
            }
        }

        $menu['navigation'] = array( 'breadcrumb' => $values );
    }

    static function buildAdminLinks( &$menu ) {
        $values = array( );

        foreach ( $menu as $path => $item ) {
            if ( ! CRM_Utils_Array::value( 'adminGroup', $item ) ) {
                continue;
            }

            $query = CRM_Utils_Array::value( 'path_arguments', $item ) 
                ? str_replace(',', '&', $item['path_arguments']) . '&reset=1' : 'reset=1';
            
            $value = array( 'title' => $item['title'],
                            'desc'  => $item['desc'],
                            'id'    => strtr($item['title'], array('('=>'_', ')'=>'', ' '=>'',
                                                                   ','=>'_', '/'=>'_' 
                                                                   )
                                             ),
                            'url'   => CRM_Utils_System::url( $path, $query ), 
                            'icon'  => $item['icon'],
                            'extra' => CRM_Utils_Array::value( 'extra', $item ) );
            if ( ! array_key_exists( $item['adminGroup'], $values ) ) {
                $values[$item['adminGroup']] = array( );
                $values[$item['adminGroup']]['fields'] = array( );
            }
            $values[$item['adminGroup']]['fields'][$item['weight'] . '.' . $item['title']] = $value;
            $values[$item['adminGroup']]['component_id'] = $item['component_id'];
        }

        foreach( $values as $group => $dontCare ) {
            $values[$group]['perColumn'] = round( count( $values[$group]['fields'] ) / 2 );
            ksort( $values[$group] );
        }

        // CRM_Core_Error::debug( 'v', $values );
        $menu['admin'] = array( 'breadcrumb' => $values );
    }

    static function &getNavigation( ) {
        if ( ! self::$_menuCache ) {
            self::get( 'navigation' );
        }

        if ( ! array_key_exists( 'navigation', self::$_menuCache ) ) {
            CRM_Core_Error::fatal( );
        }
        $nav =& self::$_menuCache['navigation'];

        if ( ! $nav ||
             ! isset( $nav['breadcrumb'] ) ) {
            return null;
        }

        $values =& $nav['breadcrumb'];
        $config =& CRM_Core_Config::singleton( );
        foreach ( $values as $index => $item ) {
            if ( strpos( CRM_Utils_Array::value( $config->userFrameworkURLVar, $_REQUEST ),
                         $item['path'] ) === 0 ) {
                $values[$index]['active'] = 'class="active"';
            } else {
                $values[$index]['active'] = '';
            }

            if ( $values[$index]['parent'] ) {
                $parent = $values[$index]['parent'];

                // only reset if still a leaf
                if ( $values[$parent]['class'] == 'leaf' ) {
                    $values[$parent]['class'] = 'collapsed';
                }

                // if a child or the parent is active, expand the menu
                if ( $values[$index ]['active'] ||
                     $values[$parent]['active'] ) {
                    $values[$parent]['class'] = 'expanded';
                }
                    
                // make the parent inactive if the child is active
                if ( $values[$index ]['active'] &&
                     $values[$parent]['active'] ) { 
                    $values[$parent]['active'] = '';
                }
            }
        }

        // remove all collapsed menu items from the array
        foreach ( $values as $weight => $v ) {
            if ( $v['parent'] &&
                 $values[$v['parent']]['class'] == 'collapsed' ) {
                unset( $values[$weight] );
            }
        }

        // check permissions for the rest
        $activeChildren = array( );
        foreach ( $values as $weight => $v ) {
            if ( CRM_Core_Permission::checkMenuItem( $v ) ) {
                if ( $v['parent'] ) {
                    $activeChildren[] = $weight;
                }
            } else {
                unset( $values[$weight] );
            }
        }

        // add the start / end tags
        $len = count($activeChildren) - 1;
        if ( $len >= 0 ) {
            $values[$activeChildren[0   ]]['start'] = true;
            $values[$activeChildren[$len]]['end'  ] = true;
        }

        ksort($values, SORT_NUMERIC );
        $i18n =& CRM_Core_I18n::singleton();
        $i18n->localizeTitles($values);
        return $values;
    }

    static function &getAdminLinks( ) {
        $links =& self::get( 'admin' );

        if ( ! $links ||
             ! isset( $links['breadcrumb'] ) ) {
            return null;
        }

        $values =& $links['breadcrumb'];
        return $values;
    }

    /**
     * Get the breadcrumb for a given path.
     *
     * @param  array   $menu   An array of all the menu items.
     * @param  string  $path   Path for which breadcrumb is to be build.
     *
     * @return array  The breadcrumb for this path
     *
     * @static
     * @access public
     */
    static function buildBreadcrumb( &$menu, $path ) {
        $crumbs       = array( );

        $pathElements = explode('/', $path);
        array_pop( $pathElements );

        $currentPath = null;
        while ( $newPath = array_shift($pathElements) ) {
            $currentPath = $currentPath ? ($currentPath . '/' . $newPath) : $newPath;
            
            // add to crumb, if current-path exists in params.
            if ( array_key_exists( $currentPath, $menu ) &&
                 isset( $menu[$currentPath]['title'] ) ) {
                $urlVar = CRM_Utils_Array::value('path_arguments', $menu[$currentPath]) ? 
                    '&' . $menu[$currentPath]['path_arguments'] : '';
                $crumbs[] = array('title' => $menu[$currentPath]['title'], 
                                  'url'   => CRM_Utils_System::url( $currentPath, 
                                                                    'reset=1' . $urlVar ));
            }
        }
        $menu[$path]['breadcrumb'] = $crumbs;

        return $crumbs;
    }

    static function buildReturnUrl( &$menu, $path ) {
        if ( ! isset($menu[$path]['return_url']) ) {
            list( $menu[$path]['return_url'], $menu[$path]['return_url_args'] ) = 
                self::getReturnUrl( $menu, $path );
        }
    }
    
    static function getReturnUrl( &$menu, $path ) {
        if ( ! isset($menu[$path]['return_url']) ) {
            $pathElements   = explode('/', $path);
            array_pop( $pathElements );
            
            if ( empty($pathElements) ) {
                return array( null, null );
            }
            $newPath = implode( '/', $pathElements );

            return self::getReturnUrl( $menu, $newPath );
        } else {
            return array( CRM_Utils_Array::value( 'return_url',
                                                  $menu[$path] ),
                          CRM_Utils_Array::value( 'return_url_args',
                                                  $menu[$path] ) );
        }
    }

    static function fillComponentIds( &$menu, $path ) {
        static $cache = array( );

        if (array_key_exists('component_id', $menu[$path])) {
            return;
        }
        
        $args = explode('/', $path);

        if ( count($args) > 1 ) {
            $compPath  = $args[0] . '/' . $args[1];
        } else {
            $compPath  = $args[0];
        }    
        
        $componentId = null;

        if ( array_key_exists($compPath, $cache) ) {
            $menu[$path]['component_id'] = $cache[$compPath];
        } else {
            if ( CRM_Utils_Array::value( 'component', $menu[$compPath] ) ) {
                $componentId = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_Component', 
                                                            $menu[$compPath]['component'], 
                                                            'id', 'name' );
            }
            $menu[$path]['component_id'] = $componentId ? $componentId : null;
            $cache[$compPath] = $menu[$path]['component_id'];
        }
    }

    static function get( $path )
    {
        if ( $path == 'civicrm/upgrade' ) {
            return self::getUpgradeItem( $path );
        }

        // return null if menu rebuild
        $config =& CRM_Core_Config::singleton( );
        if ( strpos( CRM_Utils_Array::value( $config->userFrameworkURLVar, $_REQUEST ),
                     'civicrm/menu/rebuild' ) !== false ) {
            return null;
        }

        $params = array( );

        $args = explode( '/', $path );

        $elements = array( );
        while ( ! empty( $args ) ) {
            $elements[] = "'" . implode( '/', $args ) . "'";
            array_pop( $args );
        }

        $queryString = implode( ', ', $elements );
        
        $query = "
( 
  SELECT * 
  FROM     civicrm_menu 
  WHERE    path in ( $queryString )
  ORDER BY length(path) DESC
  LIMIT    1 
)
";

        if ( $path != 'navigation' ) {
            $query .= "
UNION ( 
  SELECT *
  FROM   civicrm_menu 
  WHERE   path IN ( 'navigation' )
)
";
        }
        
        require_once "CRM/Core/DAO/Menu.php";
        $menu  =& new CRM_Core_DAO_Menu( );
        $menu->query( $query );

        self::$_menuCache = array( );
        $menuPath = null;
        while ( $menu->fetch( ) ) {
            self::$_menuCache[$menu->path] = array( );
            CRM_Core_DAO::storeValues( $menu, self::$_menuCache[$menu->path] );

            foreach ( self::$_serializedElements as $element ) {
                self::$_menuCache[$menu->path][$element] = unserialize( $menu->$element );
                
                if ( strpos( $path, $menu->path ) !== false ) {
                    $menuPath =& self::$_menuCache[$menu->path];
                }
            }
        }
        
        $i18n =& CRM_Core_I18n::singleton();
        $i18n->localizeTitles($menuPath);
        return $menuPath;
    }

    static function getArrayForPathArgs( $pathArgs )
    {
        if (! is_string($pathArgs)) {
            return;
        }
        $args = array();

        $elements = explode( ',', $pathArgs );
        //CRM_Core_Error::debug( 'e', $elements );
        foreach ( $elements as $keyVal ) {
            list($key, $val) = explode( '=', $keyVal );
            $arr[$key] = $val;
        }

        if (array_key_exists('urlToSession', $arr)) {
            $urlToSession = array( );

            $params = explode( ';', $arr['urlToSession'] );
            $count  = 0;
            foreach ( $params as $keyVal ) {
                list($urlToSession[$count]['urlVar'], 
                     $urlToSession[$count]['sessionVar'], 
                     $urlToSession[$count]['type'], 
                     $urlToSession[$count]['default'] ) = explode( ':', $keyVal );
                $count++;
            }
            $arr['urlToSession'] = $urlToSession; 
        }
        return $arr;
    }

    /* Since we won't have the menu table during upgrade, 
     * for that particular case we 'll return the hard coded menu item 
     */
    static function getUpgradeItem( $path )
    {
        return $item = array (
                              'path'            => 'civicrm/upgrade',
                              'access_callback' => 1,
                              'page_callback'   => 'CRM_Upgrade_TwoOne_Controller'
                              );
    }
}


