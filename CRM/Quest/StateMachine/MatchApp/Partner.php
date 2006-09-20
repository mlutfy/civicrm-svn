<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Quest/StateMachine/MatchApp.php';

/**
 * State machine for managing different states of the Quest process.
 *
 */
class CRM_Quest_StateMachine_MatchApp_Partner extends CRM_Quest_StateMachine_MatchApp {

    static $_dependency = null;

    static $_partners   = null;

    static $_validPartners = null;

    static function &partners( ) {
        if ( ! self::$_partners ) {
            self::$_partners = 
                array(
                      'Amherst' => array(
                                         'title' => 'Amherst College',
                                         'steps' => array( 'AmhApplicant' => 'Applicant Information',
                                                           'AmhEssay'     => 'Essay',
                                                           'AmhAthletics' => 'Athletics Supplement',
                                                           'AmhArts'      => 'Arts Supplement' ),
                                         ),

                      'Bowdoin' => array(
                                         'title' => 'Bowdoin College',
                                         'steps' => array( 'BowApplicant' => 'Applicant Information',
                                                           'BowAthletics' => 'Athletics Supplement',
                                                           'BowArts'      => 'Arts Supplement' ),
                                         ),
                      'Columbia' => array(
                                          'title' => 'Columbia University',
                                          'steps' => array( 'ColApplicant'    => 'Applicant Information',
                                                            'ColInterest'       => 'Interests',
                                                            'ColPersonal'       => 'Personal Essay',
                                                            'ColRecommendation' => 'Recommendations' ),
                                          ),
                      'Pomona' => array(
                                        'title' => 'Pomona College',
                                        'steps' => array( 'PomApplicant' => 'Applicant Information', ),
                                        ),
                      'Princeton'=> array(
                                          'title' => 'Princeton University',
                                          'steps' => array( 'PrApplicant' => 'Applicant Information',
                                                            'PrShortAnswer' => 'Short Answers',
                                                            'PrEssay'       => 'Essay',
                                                            //'PrEnggEssay'   => 'Enginering Essay' 
                                                            ),
                                          ),
                      'Rice'   => array(
                                        'title' => 'Rice University',
                                        'steps' => array( 'RiceApplicant' => 'Applicant Information', ),
                                        ),
                  
                      'Stanford'=> array(
                                         'title' => 'Stanford University',
                                         'steps' => array( 'StfApplicant'  => 'Applicant Information',
                                                           'StfShortEssay' => 'Short Essay',
                                                           'StfEssay'      => 'Essay',
                                                           'StfArts'       => 'Arts Supplement', ),
                                         ),
                  
                      'Wellesley'   => array(
                                             'title' => 'Wellesley College',
                                             'steps' => array( 'WellApplicant' => 'Applicant Information', 
                                                               'WellEssay'     => 'Essay', ),
                                             ),

                      'Wheaton' => array(
                                         'title' => 'Wheaton College',
                                         'steps' => array( 'WheApplicant'      => 'Applicant Information',
                                                           'WheRecommendation' => 'Recommendations', ),
                                         ),

                      );

        }
        return self::$_partners;
    }

    public function rebuild( &$controller, $action = CRM_Core_Action::NONE ) {
        // ensure the states array is reset
        $this->_states = array( );

        $this->_pages = array( );
        self::setPages( $this->_pages, $this, $controller );

        parent::rebuild( $controller, $action );
    }

    static public function setPages( &$pages, &$stateMachine, &$controller ) {
        $pages['CRM_Quest_Form_MatchApp_Partner_PartnerIntro'] = null;
        $partners =& self::partners( );

        $dynamic = array( 'Princeton' => 'PrApplicant' );
        foreach ( $dynamic as $d => $v ) {
            require_once "CRM/Quest/Form/MatchApp/Partner/$d/$v.php";
            eval( '$newPages =& CRM_Quest_Form_MatchApp_Partner_' . $d . '_'  . $v . '::getPages( $controller );' );
            $partners[$d]['steps'] = array_merge( $partners[$d]['steps'], $newPages );
        }

        $validPartners =& $stateMachine->getValidPartners( );

        foreach ( $partners as $name => $values ) {
            if ( $validPartners[$values['title']] ) {
                foreach ( $values['steps'] as $key => $title ) {
                    $pages["{$name}-{$key}"] = array( 'className' => "CRM_Quest_Form_MatchApp_Partner_{$name}_{$key}",
                                                      'title'     => $title,
                                                      'options'   => array( ) );
                }
            }
        }
        $pages['CRM_Quest_Form_MatchApp_Partner_PartnerSubmit'] = null;
    }
    
    public function &getDependency( ) {
        if ( self::$_dependency == null ) {
            self::$_dependency = array( );
        }
        return self::$_dependency;
    }

    public function getValidPartners( ) {
        if ( ! self::$_validPartners ) {
            self::$_validPartners = $this->_controller->get( 'validPartners' );
            if ( ! self::$_validPartners ) {
                $cid = $this->_controller->get( 'contactID' );
                require_once 'CRM/Quest/BAO/Partner.php';
                self::$_validPartners = CRM_Quest_BAO_Partner::getPartnersForContact( $cid );
            }
        }
        return self::$_validPartners;
    }

}

?>
