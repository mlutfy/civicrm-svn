<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.2                                                |
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
require_once 'CRM/Campaign/BAO/Survey.php';

/**
 * This class generates form components for processing a survey 
 * 
 */

class CRM_Campaign_Form_Survey extends CRM_Core_Form
{
    /**
     * The id of the object being edited
     *
     * @var int
     */
    protected $_surveyId;
    
    /**
     * action
     *
     * @var int
     */
    protected $_action;
    
    /* values
     *
     * @var array
     */
    public $_values;

    /**
     * context
     *
     * @var string
     */
    protected $_context;

    /**
     * Function to set variables up before form is built
     * 
     * @param null
     * 
     * @return void
     * @access public
     */

    const
        NUM_OPTION = 11;
    
    public function preProcess()
    {
        if ( !CRM_Core_Permission::check( 'administer CiviCampaign' ) ) {
            CRM_Utils_System::permissionDenied( );
        }
        
        $this->_context = CRM_Utils_Request::retrieve( 'context', 'String', $this );
        
        if ( $this->_context ) {
            $this->assign( 'context', $this->_context );
        }

        $this->_action   = CRM_Utils_Request::retrieve('action', 'String', $this );
        
        if ( $this->_action & ( CRM_Core_Action::UPDATE | CRM_Core_Action::DELETE ) ) {
            $this->_surveyId = CRM_Utils_Request::retrieve('id', 'Positive', $this, true);
        }

        $session = CRM_Core_Session::singleton();
        $url     = CRM_Utils_System::url('civicrm/campaign', 'reset=1&subPage=survey'); 
        $session->pushUserContext( $url );

        $this->_values = array( );

        if ( $this->_surveyId ) {
            $this->assign( 'surveyId', $this->_surveyId );

            $values = $this->get( 'values');
            // get contact values.
            if ( !empty( $values ) ) {
                $this->_values = $values;
            } else {
                $params = array( 'id' => $this->_surveyId );
                CRM_Campaign_BAO_Survey::retrieve( $params, $this->_values, true );
                $this->set( 'values', $this->_values );
            }
        } 

        $this->assign( 'action', $this->_action );
    }
    
    /**
     * This function sets the default values for the form. Note that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @param null
     * 
     * @return array    array of default values
     * @access public
     */
    function setDefaultValues()
    {
        $defaults = $this->_values;

        if ( $this->_surveyId ) {
            require_once 'CRM/Core/BAO/UFJoin.php';

            if ( CRM_Utils_Array::value('result_id', $defaults) &&
                 CRM_Utils_Array::value('recontact_interval', $defaults) ) {
                require_once 'CRM/Core/OptionValue.php';
                
                $resultId          = $defaults['result_id'];
                $recontactInterval = unserialize($defaults['recontact_interval']);

                unset($defaults['recontact_interval']);
                $defaults['option_group_id'] = $resultId;
            } 
                
            $ufJoinParams = array( 'entity_table' => 'civicrm_survey',
                                   'entity_id'    => $this->_surveyId,
                                   'weight'       => 1);

            if ( $ufGroupId = CRM_Core_BAO_UFJoin::findUFGroupId( $ufJoinParams ) ) {
                $defaults['profile_id'] = $ufGroupId;
            }
        }
        if ( !isset($defaults['is_active']) ) {
            $defaults['is_active'] = 1;
        }

        $defaultSurveys = CRM_Campaign_BAO_Survey::getSurvey(false, false, true);
        if ( !isset($defaults['is_default'] ) && empty($defaultSurveys) ) {
            $defaults['is_default'] = 1;  
        }
        return $defaults;
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

        if ( $this->_action & CRM_Core_Action::DELETE ) {
            
            $this->addButtons( array(
                                     array ( 'type'      => 'next',
                                             'name'      => ts('Delete'),
                                             'isDefault' => true   ),
                                     array ( 'type'      => 'cancel',
                                             'name'      => ts('Cancel') ),
                                     )
                               );
            return;
        }

        require_once 'CRM/Event/PseudoConstant.php';
        require_once 'CRM/Core/BAO/UFGroup.php';
        require_once 'CRM/Core/BAO/CustomField.php';
       
        $this->add('text', 'title', ts('Survey Title'), CRM_Core_DAO::getAttribute('CRM_Campaign_DAO_Survey', 'title'), true );

        $surveyActivityTypes = CRM_Campaign_BAO_Survey::getSurveyActivityType( );
        // Activity Type id
        $this->add('select', 'activity_type_id', ts('Select Activity Type'), array( '' => ts('- select -') ) + $surveyActivityTypes, true );
        
        // Campaign id
        require_once 'CRM/Campaign/BAO/Campaign.php';
        $campaigns = CRM_Campaign_BAO_Campaign::getAllCampaign( );
        $this->add('select', 'campaign_id', ts('Select Campaign'), array( '' => ts('- select -') ) + $campaigns );
        
        $customProfiles = CRM_Core_BAO_UFGroup::getProfiles( array('Activity') );
        // custom group id
        $this->add('select', 'profile_id', ts('Select Profile'), 
                   array( '' => ts('- select -')) + $customProfiles );


        
        $optionGroups = CRM_Campaign_BAO_Survey::getResultSets( );

        if ( empty($optionGroups) ) {
            $optionTypes = array( '1' => ts( 'Create a new Result set' ));
        } else {
            $optionTypes = array( '1' => ts( 'Create a new Result set' ),
                                  '2' => ts( 'Reuse an existing Result Set' ) );
        }

        $this->add( 'select', 
                        'option_group_id', 
                        ts( 'Result Set' ),
                        array( '' => ts( '- select -' ) ) + $optionGroups, false, 
                        array('onChange' => 'loadOptionGroup( )' ) );
        
        $this->assign( 'existingOptions', $existingOptions );
        
        $element =& $this->addRadio( 'option_type', 
                                     ts('Option Type'), 
                                     $optionTypes,
                                      array( 'onclick' => "showOptionSelect();"), '<br/>', true );

        if ( empty($optionGroups) || !CRM_Utils_Array::value('result_id', $this->_values) ) {
            $this->setdefaults( array( 'option_type' => 1 ) );
        } else if ( CRM_Utils_Array::value('result_id', $this->_values) ) {
            $this->setdefaults( array( 'option_type'     => 2 ,
                                       'option_group_id' => $this->_values['result_id'] ) );
            
        }

        // form fields of Custom Option rows
        $defaultOption = array();
        require_once 'CRM/Core/ShowHideBlocks.php';
        $_showHide = new CRM_Core_ShowHideBlocks('','');
                    
        $optionAttributes =& CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_OptionValue' );
        $optionAttributes['label']['size'] = $optionAttributes['value']['size'] = 25;

        for($i = 1; $i <= self::NUM_OPTION; $i++) {
            
            //the show hide blocks
            $showBlocks = 'optionField_'.$i;
            if ($i > 2) {
                $_showHide->addHide($showBlocks);
                if ($i == self::NUM_OPTION)
                    $_showHide->addHide('additionalOption');
            } else {
                $_showHide->addShow($showBlocks);
            }
            
            $this->add('text','option_label['.$i.']', ts('Label'),
                       $optionAttributes['label']);

            // value
            $this->add('text', 'option_value['.$i.']', ts('Value'),
                       $optionAttributes['value'] );

            // weight
            $this->add('text', "option_weight[$i]", ts('Order'),
                       $optionAttributes['weight']);
            
            $this->add('text', 'option_interval['.$i.']', ts('Recontact Interval'),
                       CRM_Core_DAO::getAttribute('CRM_Campaign_DAO_Survey', 'release_frequency') );
            
            $defaultOption[$i] = $this->createElement('radio', null, null, null, $i);

        }

        //default option selection
        $this->addGroup($defaultOption, 'default_option');
        
        $_showHide->addToTemplate();      

        // script / instructions
        $this->add( 'textarea', 'instructions', ts('Instructions for volunteers'), array( 'rows' => 5, 'cols' => 40 ) );
        
        // release frequency
        $this->add('text', 'release_frequency', ts('Release Frequency'), CRM_Core_DAO::getAttribute('CRM_Campaign_DAO_Survey', 'release_frequency') );

        $this->addRule('release_frequency', ts('Frequenct interval should be a positive number') , 'positiveInteger');

        // max number of contacts
        $this->add('text', 'max_number_of_contacts', ts('Maximum number of contacts '), CRM_Core_DAO::getAttribute('CRM_Campaign_DAO_Survey', 'max_number_of_contacts') );

        $this->addRule('max_number_of_contacts', ts('Maximum number of contacts should be a positive number') , 'positiveInteger');
        
        // default number of contacts
        $this->add('text', 'default_number_of_contacts', ts('Default number of contacts'), CRM_Core_DAO::getAttribute('CRM_Campaign_DAO_Survey', 'default_number_of_contacts') );
        $this->addRule('default_number_of_contacts', ts('Default number of contacts should be a positive number') , 'positiveInteger');    
        
        // is active ?
        $this->add('checkbox', 'is_active', ts('Is Active?'));
        
        // is default ?
        $this->add('checkbox', 'is_default', ts('Is Default?'));

        // add buttons
        if ( $this->_context == 'dialog' )  {
            $this->addButtons(array(
                                    array ('type'      => 'next',
                                           'name'      => ts('Save'),
                                           'isDefault' => true),
                                    ));
        } else {
            $this->addButtons(array(
                                    array ('type'      => 'next',
                                           'name'      => ts('Save'),
                                           'isDefault' => true),
                                    array ('type'      => 'next',
                                           'name'      => ts('Save and New'),
                                       'subName'   => 'new'),
                                    array ('type'      => 'cancel',
                                           'name'      => ts('Cancel')),
                                    )
                              ); 
        }

        // add a form rule to check default value
        $this->addFormRule( array( 'CRM_Campaign_Form_Survey', 'formRule' ),$this );

    }
    
    /**
     * global validation rules for the form
     *
     */
    static function formRule( $fields, $files, $form ) {
        
        $errors = array( );
        
        if ( $fields['option_type'] == 2 && 
             !CRM_Utils_Array::value( 'option_group_id', $fields) ) {
            $errors['option_group_id'] = ts("Please select Result set.");
            return $errors;
        }

        //capture duplicate Custom option values
        if ( ! empty($fields['option_value']) ) {
            $countValue = count($fields['option_value']);
            $uniqueCount = count(array_unique($fields['option_value']));
            
            if ( $countValue > $uniqueCount) {
                $start=1;
                while ($start < self::NUM_OPTION) { 
                    $nextIndex = $start + 1;
                    
                    while ($nextIndex <= self::NUM_OPTION) {
                        if ( $fields['option_value'][$start] == $fields['option_value'][$nextIndex] &&
                             !empty($fields['option_value'][$nextIndex]) ) {
                            
                            $errors['option_value['.$start.']']     = ts( 'Duplicate Option values' );
                            $errors['option_value['.$nextIndex.']'] = ts( 'Duplicate Option values' );
                            $_flagOption = 1;
                        }
                        $nextIndex++;
                    }
                    $start++;
                }
            }
        }
        
        //capture duplicate Custom Option label
        if ( ! empty( $fields['option_label'] ) ) {
            $countValue = count($fields['option_label']);
            $uniqueCount = count(array_unique($fields['option_label']));
            
            if ( $countValue > $uniqueCount) {
                $start=1;
                while ($start < self::NUM_OPTION) { 
                    $nextIndex = $start + 1;
                    
                    while ($nextIndex <= self::NUM_OPTION) {
                        if ( $fields['option_label'][$start] == $fields['option_label'][$nextIndex] && !empty($fields['option_label'][$nextIndex]) ) {
                            $errors['option_label['.$start.']']     =  ts( 'Duplicate Option label' );
                            $errors['option_label['.$nextIndex.']'] = ts( 'Duplicate Option label' );
                            $_flagOption = 1;
                        }
                        $nextIndex++;
                    }
                    $start++;
                }
            }
        }
        
        for($i=1; $i<= self::NUM_OPTION; $i++) {
            if (!$fields['option_label'][$i]) {
                if ($fields['option_value'][$i]) {
                    $errors['option_label['.$i.']'] = ts( 'Option label cannot be empty' );
                    $_flagOption = 1;
                } else {
                    $_emptyRow = 1;
                }
            } else if (!strlen(trim($fields['option_value'][$i]))) {
                if (!$fields['option_value'][$i]) {
                    $errors['option_value['.$i.']'] = ts( 'Option value cannot be empty' );
                    $_flagOption = 1;
                }
            } else if (!strlen(trim($fields['option_interval'][$i]))) {
                if (!$fields[''][$i]) {
                    $errors['option_interval['.$i.']'] = ts( 'Recontact Interval cannot be empty' );
                    $_flagOption = 1;
                }
            }
            if ( CRM_Utils_Array::value($i, $fields['option_interval']) && !CRM_Utils_Rule::integer( $fields['option_interval'][$i] ) ) {
                $_flagOption = 1;
                $errors['option_interval['.$i.']'] = ts( 'Please enter a valid integer.' );
            }
            
        }
        
        return empty($errors) ? true : $errors;
    }   
    
    /**
     * Process the form
     * 
     * @param null
     * 
     * @return void
     * @access public
     */
    public function postProcess()
    {
        // store the submitted values in an array
        $params = $this->controller->exportValues( $this->_name );
               
        $session = CRM_Core_Session::singleton( );

        $params['last_modified_id'] = $session->get( 'userID' );
        $params['last_modified_date'] = date('YmdHis');
        
        require_once 'CRM/Core/BAO/OptionValue.php';
        require_once 'CRM/Core/BAO/OptionGroup.php';
        
        $updateResultSet = false;
        if ( (CRM_Utils_Array::value('option_type', $params) == 2) &&
             CRM_Utils_Array::value('option_group_id', $params) ) {
            if ( $params['option_group_id'] == CRM_Utils_Array::value('result_id', $this->_values) ) {
                $updateResultSet  = true;
            }
        }

        if ( $this->_surveyId ) {

            if ( $this->_action & CRM_Core_Action::DELETE ) {
                CRM_Campaign_BAO_Survey::del( $this->_surveyId );
                CRM_Core_Session::setStatus(ts(' Survey has been deleted.'));
                $session->replaceUserContext( CRM_Utils_System::url('civicrm/campaign', 'reset=1&subPage=survey' ) ); 
                return;
            }

            $params['id'] = $this->_surveyId;

        } else { 
            $params['created_id']   = $session->get( 'userID' );
            $params['created_date'] = date('YmdHis');
        } 

        $params['is_active' ] = CRM_Utils_Array::value('is_active', $params, 0);
        $params['is_default'] = CRM_Utils_Array::value('is_default', $params, 0);

        $recontactInterval =  array( );

        
        if ( $updateResultSet ) {
            $optionValue = new CRM_Core_DAO_OptionValue( );
            $optionValue->option_group_id =  $this->_values['result_id'];
            $optionValue->delete();

            $params['result_id'] = $this->_values['result_id'];
            
        } else {
            $opGroupName = 'civicrm_survey_'.rand(10,1000).'_'.date( 'YmdHis' );
            
            $optionGroup            = new CRM_Core_DAO_OptionGroup( );
            $optionGroup->name      =  $opGroupName;
            $optionGroup->label     =  $params['title']. ' Result Set';
            $optionGroup->is_active = 1;
            $optionGroup->save( );

            $params['result_id'] = $optionGroup->id;
        }

        foreach ($params['option_value'] as $k => $v) {
            if (strlen(trim($v))) {
                $optionValue                  = new CRM_Core_DAO_OptionValue( );
                $optionValue->option_group_id = $params['result_id'];
                $optionValue->label           = $params['option_label'][$k];
                $optionValue->name            = CRM_Utils_String::titleToVar( $params['option_label'][$k] );
                $optionValue->value           = trim($v);
                $optionValue->weight          = $params['option_weight'][$k];
                $optionValue->is_active       = 1;
                
                if ( CRM_Utils_Array::value('default_option', $params) &&
                     $params['default_option'] == $k ) {
                    $optionValue->is_default = 1;
                }
                
                $optionValue->save( );
                $recontactInterval[$optionValue->label]  = $params['option_interval'][$k];
            }
        }
        
        $params['recontact_interval'] = serialize($recontactInterval);
        $surveyId = CRM_Campaign_BAO_Survey::create( $params  );
        
        if ( CRM_Utils_Array::value('result_id', $this->_values) && !$updateResultSet ) {
            $query       = "SELECT COUNT(*) FROM civicrm_survey WHERE result_id = %1";
            $countSurvey = CRM_Core_DAO::singleValueQuery( $query, array( 1 => array($this->_values['result_id'], 'Integer') ) );

            // delete option group if no any survey is using it.
            if ( !($countSurvey >= 1) ) {
                CRM_Core_BAO_OptionGroup::del($this->_values['result_id']);
            }
        }

        require_once 'CRM/Core/BAO/UFJoin.php';
        
        // also update the ProfileModule tables 
        $ufJoinParams = array( 'is_active'    => 1, 
                               'module'       => 'CiviCampaign',
                               'entity_table' => 'civicrm_survey', 
                               'entity_id'    => $surveyId->id );
        
        // first delete all past entries
        if ( $this->_surveyId ) {
            CRM_Core_BAO_UFJoin::deleteAll( $ufJoinParams );
        }    
        if ( CRM_Utils_Array::value('profile_id' , $params) ) {

            $ufJoinParams['weight'     ] = 1;
            $ufJoinParams['uf_group_id'] = $params['profile_id'];
            CRM_Core_BAO_UFJoin::create( $ufJoinParams ); 
        }
        
        if( ! is_a( $surveyId, 'CRM_Core_Error' ) ) {
            CRM_Core_Session::setStatus(ts('Survey has been saved.'));
        }
        
        if ( $this->_context == 'dialog' )  {
            $returnArray = array( 'returnSuccess' => true );
            echo json_encode( $returnArray );
            CRM_Utils_System::civiExit( );
        }

        $buttonName = $this->controller->getButtonName( );
        if ( $buttonName == $this->getButtonName( 'next', 'new' ) ) {
            CRM_Core_Session::setStatus(ts(' You can add another Survey.'));
            $session->replaceUserContext( CRM_Utils_System::url('civicrm/survey/add', 'reset=1&action=add' ) );
        } else {
            $session->replaceUserContext( CRM_Utils_System::url('civicrm/campaign', 'reset=1&subPage=survey' ) ); 
        }
    }
 }

