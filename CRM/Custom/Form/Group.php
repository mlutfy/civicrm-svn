<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2010                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2010
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';

/**
 * form to process actions on the group aspect of Custom Data
 */
class CRM_Custom_Form_Group extends CRM_Core_Form 
{

    /**
     * the group id saved to the session for an update
     *
     * @var int
     * @access protected
     */
    protected $_id;

    /**
     *  group is empty or not
     *
     * @var bool
     * @access protected
     */
    protected $_isGroupEmpty = true;
    
    /**
     * array of existing subtypes set for a custom group
     *
     * @var array
     * @access protected
     */
    protected $_subtypes = array( );

    /**
     * array of default params
     *
     * @var array
     * @access protected
     */
    protected $_defaults = array( );

    /**
     * Function to set variables up before form is built
     * 
     * @param null
     * 
     * @return void
     * @access public
     */
    public function preProcess()
    {
        require_once 'CRM/Core/BAO/CustomGroup.php';
        // current group id
        $this->_id = $this->get('id');

        // setting title for html page
        if ($this->_action == CRM_Core_Action::UPDATE) {
            $title = CRM_Core_BAO_CustomGroup::getTitle($this->_id);
            CRM_Utils_System::setTitle(ts('Edit %1', array(1 => $title)));
        } else if ($this->_action == CRM_Core_Action::VIEW) {
            $title = CRM_Core_BAO_CustomGroup::getTitle($this->_id);
            CRM_Utils_System::setTitle(ts('Preview %1', array(1 => $title)));
        } else {
            CRM_Utils_System::setTitle(ts('New Custom Data Group'));
        }

        if ( isset($this->_id) ) {
            $params = array( 'id' => $this->_id );
            CRM_Core_BAO_CustomGroup::retrieve( $params, $this->_defaults );

            $subExtends = CRM_Utils_Array::value( 'extends_entity_column_value', $this->_defaults );
            if ( !empty( $subExtends ) ) {
                $this->_subtypes = 
                    explode( CRM_Core_DAO::VALUE_SEPARATOR, substr($subExtends,1,-1) );
            } 
        }
    }
     
    /**
     * global form rule
     *
     * @param array $fields  the input form values
     * @param array $files   the uploaded files if any
     * @param array $options additional user data
     *
     * @return true if no errors, else array of errors
     * @access public
     * @static
     */
    static function formRule(&$fields, &$files, $self) 
    {
        $errors = array();

        if ( CRM_Utils_Array::value(1, $fields['extends']) ) {
            if ( !$self->_isGroupEmpty ) {
                $updates = array_diff($self->_subtypes, array_intersect($self->_subtypes, $fields['extends'][1]));
                if ( ! empty($updates) ) {
                    $errors['extends'] = ts("Removing any existing subtypes is not allowed at this moment. However you can add more subtypes.");
                } 
            }
            
            if( in_array('', $fields['extends'][1]) && count($fields['extends'][1]) > 1) {
                $errors['extends'] = ts("Cannot combine other option with 'Any'.");
            }  
        }
        
        if ( empty( $fields['extends'][0] ) ) {
            $errors['extends'] = ts("You need to select the type of record that this group of custom fields is applicable for.");
        }

        $extends = array('Activity','Relationship','Group','Contribution','Membership', 'Event','Participant');
        if(in_array($fields['extends'][0],$extends) && $fields['style'] == 'Tab' ) {
            $errors['style'] = ts("Display Style should be Inline for this Class");
            $self->assign( 'showStyle', true );
        }

        if ( CRM_Utils_Array::value('is_multiple',  $fields ) ) {
            // if ( isset( $fields['min_multiple'] ) && isset( $fields['max_multiple'] ) 
            //      && ( $fields['min_multiple'] > $fields['max_multiple'] ) ) {
            //     $errors['max_multiple'] = ts("Maximum limit should be higher than minimum limit");
            // }
            
            if ( $fields['style'] == 'Inline' ) {
                $errors['style'] = ts("'Multiple records' feature is not supported for the 'Inline' display style. Please select 'Tab' as the display style if you want to use this feature.");
                $self->assign( 'showMultiple', true );
            }
        }
        
        //checks the given custom group doesnot start with digit
        $title = $fields['title']; 
        if ( ! empty( $title ) ) {
            $asciiValue = ord( $title{0} );//gives the ascii value
            if( $asciiValue >= 48 && $asciiValue <= 57 ) {
                $errors['title'] = ts("Group's Name should not start with digit");
            } 
        }

        return empty($errors) ? true : $errors;
    }

    /**
     * This function is used to add the rules (mainly global rules) for form.
     * All local rules are added near the element
     *
     * @param null
     * 
     * @return void
     * @access public
     * @see valid_date
     */
    function addRules( )
    {
        $this->addFormRule( array( 'CRM_Custom_Form_Group', 'formRule' ), $this ); 
    }
    
    /**
     * Function to actually build the form
     * 
     * @param null
     * 
     * @return void
     * @access public
     */
    public function buildQuickForm()
    {
        $this->applyFilter('__ALL__', 'trim');
        
        $attributes = CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_CustomGroup' );
        
        //title
        $this->add('text', 'title', ts('Group Name'), $attributes['title'], true);
        $this->addRule( 'title',
                        ts( 'Name already exists in Database.' ),
                        'objectExists',
                        array( 'CRM_Core_DAO_CustomGroup', $this->_id, 'title' ) );   
        
        //Fix for code alignment, CRM-3058
        require_once "CRM/Contribute/PseudoConstant.php";
        require_once "CRM/Member/BAO/MembershipType.php";
        require_once 'CRM/Event/PseudoConstant.php';
        require_once "CRM/Contact/BAO/Relationship.php";
        require_once 'CRM/Core/OptionGroup.php';
        require_once 'CRM/Contact/BAO/ContactType.php';
        $contactTypes = array( 'Contact', 'Individual', 'Household', 'Organization' );
        $this->assign( 'contactTypes', json_encode($contactTypes) );
              
        $sel1 = array( "" => "- select -" ) + CRM_Core_SelectValues::customGroupExtends( );
        $sel2 = array( );
        $activityType    = CRM_Core_PseudoConstant::activityType( false, true );
        $eventType       = CRM_Core_OptionGroup::values( 'event_type' );
        $membershipType  = CRM_Member_BAO_MembershipType::getMembershipTypes( false );
        $participantRole = CRM_Core_OptionGroup::values( 'participant_role' );
        $relTypeInd      = CRM_Contact_BAO_Relationship::getContactRelationshipType( null, 'null', null, 'Individual' );
        $relTypeOrg      = CRM_Contact_BAO_Relationship::getContactRelationshipType( null, 'null', null, 'Organization' );
        $relTypeHou      = CRM_Contact_BAO_Relationship::getContactRelationshipType( null, 'null', null, 'Household' );

        ksort( $sel1 );
        asort( $activityType );
        asort( $eventType );
        asort( $membershipType );
        asort( $participantRole );
        $allRelationshipType = array();
        $allRelationshipType = array_merge(  $relTypeInd , $relTypeOrg);        
        $allRelationshipType = array_merge( $allRelationshipType, $relTypeHou);

        //adding subtype specific relationships CRM-5256
        $subTypes = CRM_Contact_BAO_ContactType::subTypeInfo( );
        
        foreach ( $subTypes as $subType => $val ) {
            $subTypeRelationshipTypes = CRM_Contact_BAO_Relationship::getContactRelationshipType( null, null, null, $val['parent'], 
                                                                                                  false, 'label', true, $subType );
            $allRelationshipType = array_merge( $allRelationshipType, $subTypeRelationshipTypes);
        }

        $sel2['Event']                = $eventType;
        $sel2['Activity']             = $activityType;
        $sel2['Membership']           = $membershipType;
        $sel2['ParticipantRole']      = $participantRole;
        $sel2['ParticipantEventName'] = 
            CRM_Event_PseudoConstant::event( null, false, "( is_template IS NULL OR is_template != 1 )" );
        $sel2['ParticipantEventType'] = $eventType;
        $sel2['Contribution']         = CRM_Contribute_PseudoConstant::contributionType( );
        $sel2['Relationship']         = $allRelationshipType;

        $sel2['Individual']           = CRM_Contact_BAO_ContactType::subTypePairs( 'Individual', false, null );
        $sel2['Household' ]           = CRM_Contact_BAO_ContactType::subTypePairs( 'Household', false, null );
        $sel2['Organization']         = CRM_Contact_BAO_ContactType::subTypePairs( 'Organization', false, null );

        foreach ( $sel2 as $main => $sub ) {
            if ( !empty($sel2[$main]) ) {
                $sel2[$main] = array( '' => ts("- Any -") ) + $sel2[$main]; 
            }
        }
        
        require_once "CRM/Core/Component.php";
        $cSubTypes = CRM_Core_Component::contactSubTypes();
       
        if ( !empty( $cSubTypes ) ) {
            $contactSubTypes = array( );
            foreach($cSubTypes as $key => $value ) {
                $contactSubTypes[$key] = $key;
            }
            $sel2['Contact']  =  array("" => "-- Any --") + $contactSubTypes;
        } else {
            if( !isset( $this->_id ) ){
                $formName = 'document.forms.' . $this->_name;
                
                $js  = "<script type='text/javascript'>\n";
                $js .= "{$formName}['extends[1]'].style.display = 'none';\n";
                $js .= "</script>";
                $this->assign( 'initHideBlocks', $js );
            }
        }
        
        $sel =& $this->add('hierselect',
                           'extends',
                           ts('Used For'),
                           array('onClick' => 'showHideStyle();',
                                 'name'    => 'extends[0]',
                                 'style'   => 'vertical-align: top;'),
                           true);
        $sel->setOptions( array( $sel1, $sel2 ) );
        if ( is_a($sel->_elements[1], 'HTML_QuickForm_select') ) {
            // make second selector a multi-select -
            $sel->_elements[1]->setMultiple(true);
            $sel->_elements[1]->setSize(5);
        }
        if ($this->_action == CRM_Core_Action::UPDATE) {
            $subName = CRM_Utils_Array::value( 'extends_entity_column_id', $this->_defaults );
            if ( $this->_defaults['extends'] == 'Participant') {
                if ( $subName == 1 ) {
                    $this->_defaults['extends'] = 'ParticipantRole';
                } elseif ( $subName == 2 ) {
                    $this->_defaults['extends'] = 'ParticipantEventName';
                } elseif ( $subName == 3 ) {
                    $this->_defaults['extends'] = 'ParticipantEventType';
                }
            }

            //allow to edit settings if custom group is empty CRM-5258
            $this->_isGroupEmpty = CRM_Core_BAO_CustomGroup::isGroupEmpty( $this->_id );
            if ( !$this->_isGroupEmpty ) {
                if ( !empty($this->_subtypes) &&
                     (count(array_intersect($this->_subtypes, $sel2[$this->_defaults['extends']])) < 
                      count($sel2[$this->_defaults['extends']])) ) {
                    // we want to allow adding subtypes for this case, 
                    // and therefore freeze the first selector only.
                    $sel->_elements[0]->freeze();
                } else {
                    // freeze both the selectors
                    $sel->freeze();
                }
            }
            $this->assign('gid', $this->_id);
        }
        
        // help text
        $this->addWysiwyg( 'help_pre', ts('Pre-form Help'), $attributes['help_pre']);
        $this->addWysiwyg( 'help_post', ts('Post-form Help'), $attributes['help_post']);

        // weight
        $this->add('text', 'weight', ts('Order'), $attributes['weight'], true);
        $this->addRule('weight', ts('is a numeric field') , 'numeric');

        // display style
        $this->add('select', 'style', ts('Display Style'), CRM_Core_SelectValues::customGroupStyle());
       
        // is this group collapsed or expanded ?
        $this->addElement('checkbox', 'collapse_display', ts('Collapse this group on initial display'));

        // is this group collapsed or expanded ? in advanced search
        $this->addElement('checkbox', 'collapse_adv_display', ts('Collapse this group in Advanced Search'));

        // is this group active ?
        $this->addElement('checkbox', 'is_active', ts('Is this Custom Data Group active?') );
        
        // does this group have multiple record?
        $multiple = $this->addElement('checkbox', 
                                      'is_multiple', 
                                      ts('Does this Custom Data Group allow multiple records?'),
                                      null,
                                      array( 'onclick' => "showRange();"));

        // $min_multiple = $this->add('text', 'min_multiple', ts('Minimum number of multiple records'), $attributes['min_multiple'] );
        // $this->addRule('min_multiple', ts('is a numeric field') , 'numeric');
        
        $max_multiple = $this->add('text', 'max_multiple', ts('Maximum number of multiple records'), $attributes['max_multiple'] );
        $this->addRule('max_multiple', ts('is a numeric field') , 'numeric');

        //allow to edit settings if custom group is empty CRM-5258
        $this->assign( 'isGroupEmpty', $this->_isGroupEmpty );
        if ( !$this->_isGroupEmpty ) {
            $multiple->freeze();
            //$min_multiple->freeze();
            $max_multiple->freeze();
        }

        $this->assign( 'showStyle', false );
        $this->assign( 'showMultiple', false );
        $this->addButtons(array(
                                array ( 'type'      => 'next',
                                        'name'      => ts('Save'),
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                        'isDefault' => true   ),
                                array ( 'type'      => 'cancel',
                                        'name'      => ts('Cancel') ),
                                )
                          );
        
        // views are implemented as frozen form
        if ($this->_action & CRM_Core_Action::VIEW) {
            $this->freeze();
            $this->addElement('button', 'done', ts('Done'), array('onclick' => "location.href='civicrm/admin/custom/group?reset=1&action=browse'"));
        }
    }

    /**
     * This function sets the default values for the form. Note that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @param null
     * 
     * @return array   array of default values
     * @access public
     */
    function setDefaultValues()
    {
        $defaults =& $this->_defaults;
        $this->assign('showMaxMultiple', true);
        if ($this->_action == CRM_Core_Action::ADD) {
            $defaults['weight'] = CRM_Utils_Weight::getDefaultWeight('CRM_Core_DAO_CustomGroup');

            $defaults['is_multiple'] = $defaults['min_multiple'] = 0;
            $defaults['is_active']   = 1;
            $defaults['style']       = 'Inline';
        } elseif ( !CRM_Utils_Array::value('max_multiple', $defaults) && !$this->_isGroupEmpty) {
            $this->assign('showMaxMultiple', false);
        }

        if ( isset ($defaults['extends'] ) ) {
            $extends = $defaults['extends'];
            unset($defaults['extends']);

            $defaults['extends'][0] = $extends;
            
            if ( !empty( $this->_subtypes ) ) {
                $defaults['extends'][1] = $this->_subtypes;
            } else {
                $defaults['extends'][1] = array( 0 => '' );  
            } 
            
            
            $subName = CRM_Utils_Array::value( 'extends_entity_column_id', $defaults );
			
			if ( $extends == 'Relationship' && !empty($this->_subtypes) ) {
                $relationshipDefaults = array ( );
                foreach ( $defaults['extends'][1] as $donCare => $rel_type_id ) {
                    $relationshipDefaults[] = $rel_type_id.'_a_b';
                }
                
                $defaults['extends'][1] = $relationshipDefaults;
            }
            
        }
        
        return $defaults;
    }
    
    /**
     * Process the form
     * 
     * @param null
     * 
     * @return void
     * @access public
     */
    public function postProcess( )
    {
        // get the submitted form values.
        $params = $this->controller->exportValues('Group');
        $params['overrideFKConstraint'] = 0;
        if ($this->_action & CRM_Core_Action::UPDATE) {
            $params['id'] = $this->_id;
            if ($this->_defaults['extends'][0] != $params['extends'][0]) {
                $params['overrideFKConstraint'] = 1;
            }
        } elseif ($this->_action & CRM_Core_Action::ADD) {
            //new custom group, so lets set the created_id
            $session =& CRM_Core_Session::singleton( );
            $params['created_id']   = $session->get( 'userID' );
            $params['created_date'] = date('YmdHis');
        } 
        
        $group = CRM_Core_BAO_CustomGroup::create( $params );

        // reset the cache
        require_once 'CRM/Core/BAO/Cache.php';
        CRM_Core_BAO_Cache::deleteGroup( 'contact fields' );
      
        if ($this->_action & CRM_Core_Action::UPDATE) {
            CRM_Core_Session::setStatus(ts('Your custom data group \'%1 \' has been saved.', array(1 => $group->title)));
        } else {
            $url = CRM_Utils_System::url( 'civicrm/admin/custom/group/field', 'reset=1&action=add&gid=' . $group->id);
            CRM_Core_Session::setStatus(ts('Your custom data group \'%1\' has been added. You can add custom fields to this group now.',
                                           array(1 => $group->title)));
            $session =& CRM_Core_Session::singleton( );
            $session->replaceUserContext($url);
        }
    }
}

