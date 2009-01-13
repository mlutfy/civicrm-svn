<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
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
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'CRM/Core/OptionGroup.php';        
require_once "CRM/Case/PseudoConstant.php";
require_once "CRM/Case/BAO/Case.php";
require_once 'CRM/Case/XMLProcessor/Process.php';
require_once "CRM/Activity/Form/Activity.php";
require_once 'CRM/Contact/BAO/Contact.php';

/**
 * This class create activities for a case
 * 
 */
class CRM_Case_Form_Activity extends CRM_Activity_Form_Activity
{
    /**
     * The default variable defined
     *
     * @var int
     */
    public $_caseId;

    /**
     * The default values of an activity
     *
     * @var array
     */
    public $_defaults = array();

    /**
     * The array of releted contact info  
     *
     * @var array
     */
    public $_relatedContacts;

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    function preProcess( ) 
    { 
        $this->_caseId  = CRM_Utils_Request::retrieve( 'caseid', 'Positive', $this );
        $this->_context = 'caseActivity';
        $this->_crmDir  = 'Case';
        $this->assign( 'context', $this->_context );
        
        $result = parent::preProcess( );
        
        if ( $this->_cdType  || $this->_addAssigneeContact || $this->_addTargetContact  ) {
            return $result;
        }
        
        if ( !$this->_caseId && $this->_activityId ) {
            $this->_caseId  = CRM_Core_DAO::getFieldValue( 'CRM_Case_DAO_CaseActivity', $this->_activityId,
                                                           'case_id', 'activity_id' );
        }
        if ( $this->_caseId ) {
            $this->assign( 'caseId', $this->_caseId );
        }

        if ( !$this->_caseId ||
             (!$this->_activityId && !$this->_activityTypeId) ) {
            CRM_Core_Error::fatal('required params missing.');            
        }

        $caseType  = CRM_Case_PseudoConstant::caseTypeName( $this->_caseId );
        $this->_caseType  = $caseType['name'];
        $this->assign('caseType', $this->_caseType);

        $clientName = $this->_getDisplayNameById( $this->_currentlyViewedContactId );
        $this->assign( 'client_name', $clientName );
        
        if ( !$this->_activityId ) { 
            // check if activity count is within the limit
            $xmlProcessor  = new CRM_Case_XMLProcessor_Process( );
            $activityInst  = $xmlProcessor->getMaxInstance($this->_caseType);

            // If not bounce back and also provide activity edit link
            if ( isset( $activityInst[$this->_activityTypeName] ) ) {
                $activityCount = CRM_Case_BAO_Case::getCaseActivityCount( $this->_caseId, $this->_activityTypeId );
                if ( $activityCount >= $activityInst[$this->_activityTypeName] ) {
                    if ( $activityInst[$this->_activityTypeName] == 1 ) {
                        $activities = 
                            CRM_Case_BAO_Case::getCaseActivity( $this->_caseId, 
                                                                array('activity_type_id' => 
                                                                      $this->_activityTypeId), 
                                                                $this->_currentUserId );
                        $activities = array_keys($activities);
                        $activities = $activities[0];
                        $editUrl    = 
                            CRM_Utils_System::url( 'civicrm/case/activity', 
                                                   "reset=1&cid={$this->_currentlyViewedContactId}&id={$this->_caseId}&aid={$activities}" );
                    }
                    CRM_Core_Error::statusBounce( ts("You can not add another '%1' activity to this case. %2", 
                                                     array( 1 => $this->_activityTypeName,
                                                            2 => "Do you want to <a href='$editUrl'>edit the existing activity</a> ?" )) );
                }
            }
        }

        CRM_Utils_System::setTitle( $this->_activityTypeName );

        // set context
        $url = CRM_Utils_System::url( 'civicrm/contact/view/case',
                                      "reset=1&action=view&cid={$this->_currentlyViewedContactId}&id={$this->_caseId}&show=1" );
        $session =& CRM_Core_Session::singleton( );
        $session->pushUserContext( $url );
    }
    
    /**
     * This function sets the default values for the form. For edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) 
    {
        $this->_defaults = parent::setDefaultValues( );
        
        //return form for ajax
        if ( $this->_cdType  || $this->_addAssigneeContact || $this->_addTargetContact ) {
            return $this->_defaults;
        }

        if ( !isset($this->_defaults['due_date_time']) ) {
            $this->_defaults['due_date_time'] = array( );
            CRM_Utils_Date::getAllDefaultValues( $this->_defaults['due_date_time'] );
        }
        return $this->_defaults;
    }
    
    public function buildQuickForm( ) 
    {
        // modify core Activity fields
        $this->_fields['activity_date_time']['label']    = 'Actual Date'; 
        $this->_fields['activity_date_time']['required'] = false;
        $this->_fields['subject']['required']            = false;
        $this->_fields['source_contact_id']['label']     = 'Reported By'; 
            
        if ( $this->_caseType ) {
            $xmlProcessor = new CRM_Case_XMLProcessor_Process( );
            $aTypes       = $xmlProcessor->get( $this->_caseType, 'ActivityTypes' );
            
            // remove Open Case activity type since we're inside an existing case
            $openCaseID = CRM_Core_OptionGroup::getValue('activity_type', 'Open Case', 'name' );
            unset( $aTypes[$openCaseID] );
            asort( $aTypes );        
            $this->_fields['followup_activity_type_id']['attributes'] = 
                array('' => '- select activity type -') + $aTypes;
        }

        $result = parent::buildQuickForm( );

        if ( $this->_action & ( CRM_Core_Action::DELETE | CRM_Core_Action::DETACH |  CRM_Core_Action::RENEW ) ) {
            return;
        }

        if ( $this->_cdType || $this->_addAssigneeContact || $this->_addTargetContact ) {
            return $result;
        }

        $this->assign( 'urlPath', 'civicrm/case/activity' );

        $this->add('select', 'medium_id',  ts( 'Medium' ), 
                   CRM_Core_OptionGroup::values('encounter_medium'), true);
        
        $this->add('date', 'due_date_time', ts('Due Date'), CRM_Core_SelectValues::date('activityDatetime'), true);
        $this->addRule('due_date_time', ts('Select a valid date.'), 'qfDate');
        
        $this->_relatedContacts = CRM_Case_BAO_Case::getRelatedContacts( $this->_caseId );
        if ( ! empty($this->_relatedContacts) ) {
            $checkBoxes = array( );
            foreach ( $this->_relatedContacts as $id => $row ) {
                $checkBoxes[$id] = $this->addElement('checkbox', $id, null, '' );
            }
            
            $this->addGroup  ( $checkBoxes, 'contact_check' );
            $this->addElement( 'checkbox', 'toggleSelect', null, null, 
                               array( 'onclick' => "return toggleCheckboxVals('contact_check',this.form);" ) );
            $this->assign    ('searchRows', $this->_relatedContacts );
        }

        $this->addFormRule( array( 'CRM_Case_Form_Activity', 'formRule' ), $this );
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
    static function formRule( &$fields, &$files, $self ) 
    {  
        // skip form rule if deleting
        if  ( CRM_Utils_Array::value( '_qf_Activity_next_',$fields) == 'Delete' || CRM_Utils_Array::value( '_qf_Activity_next_',$fields) == 'Restore' ) {
            return true;
        }
        
        return parent::formrule( $fields, $files, $self );
    }
    
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        if ( $this->_action & CRM_Core_Action::DELETE ) {
            $statusMsg = null;
            $params = array( 'id' => $this->_activityId );
            $activityDelete = CRM_Activity_BAO_Activity::deleteActivity( $params, true );
            if ( $activityDelete ) {
                $statusMsg = ts('The selected activity has been moved to the Trash. You can view and / or restore deleted activities by checking "Deleted Activities" from the Case Activities search filter (under Manage Case).<br />');
            }
            CRM_Core_Session::setStatus( $statusMsg );
            return;
        }

        if ( $this->_action & CRM_Core_Action::RENEW ) {
            $statusMsg = null;
            $params = array( 'id' => $this->_activityId );
            $activityRestore = CRM_Activity_BAO_Activity::restoreActivity( $params );
            if ( $activityRestore ) {
                $statusMsg = ts('The selected activity has been restored.<br />');
            }
            CRM_Core_Session::setStatus( $statusMsg );
            return;
        }
        
        // store the submitted values in an array
        $params = $this->controller->exportValues( $this->_name );
        //set parent id if its edit mode
        if ( $parentId = CRM_Utils_Array::value( 'parent_id', $this->_defaults ) ) {
            $params['parent_id'] = $parentId;
        }

        $params['now'] = date("YmdhisA");

        if( !CRM_Utils_Array::value( 'activity_date_time', $params ) ) {
            $params['activity_date_time'] = $params['now'];
        } 
        // required for status msg
        $recordStatus = 'created';

        // store the dates with proper format
        $params['activity_date_time'] = CRM_Utils_Date::format( $params['activity_date_time'] );
        $params['due_date_time']      = CRM_Utils_Date::format( $params['due_date_time'] );
        $params['activity_type_id']   = $this->_activityTypeId;
        $params['target_contact_id']  = $this->_currentlyViewedContactId;

        // format activity custom data
        if ( CRM_Utils_Array::value( 'hidden_custom', $params ) ) {
            if ( $this->_activityId && $this->_defaults['is_auto'] == 0 ) {
                // unset custom fields-id from params since we want custom 
                // fields to be saved for new activity.
                foreach ( $params as $key => $value ) {
                    $match = array( );
                    if ( preg_match('/^(custom_\d+_)(\d+)$/', $key, $match) ) {
                        $params[$match[1] . '-1'] = $params[$key];
                        unset($params[$key]);
                    }
                }
            }
			// build custom data getFields array
			$customFields = CRM_Core_BAO_CustomField::getFields( 'Activity', false, false, $this->_activityTypeId );
			$customFields = 
                CRM_Utils_Array::crmArrayMerge( $customFields, 
                                                CRM_Core_BAO_CustomField::getFields( 'Activity', false, false, 
                                                                                     null, null, true ) );
	        $params['custom'] = CRM_Core_BAO_CustomField::postProcess( $params,
	                                                                   $customFields,
	                                                                   $this->_activityId,
	                                                                   'Activity' );
        }

        if ( isset($this->_activityId) ) { 
            $params['id'] = $this->_activityId;

            // activity which hasn't been modified by a user yet
            if ( $this->_defaults['is_auto'] == 1 ) { 
                $params['is_auto'] = 0;
            }

            // activity which has been created or modified by a user
            if ( $this->_defaults['is_auto'] == 0 ) {
                $newActParams = $params;
                $params = array('id' => $this->_activityId);
                $params['is_current_revision'] = 0;
            }
            
            // record status for status msg
            $recordStatus = 'updated';
        }
        
        if ( ! isset($newActParams) ) {
            // add more attachments if needed for old activity
            CRM_Core_BAO_File::formatAttachment( $params,
                                                 $params,
                                                 'civicrm_activity' );

            // call begin post process, before the activity is created/updated.
            $this->beginPostProcess( $params );

            // activity create/update
            $activity = CRM_Activity_BAO_Activity::create( $params );

            // call end post process, after the activity has been created/updated.
            $this->endPostProcess( $params, $activity );
        } else {
            // since the params we need to set are very few, and we don't want rest of the 
            // work done by bao create method , lets use dao object to make the changes 
            $activity =& new CRM_Activity_DAO_Activity( );
            $activity->copyValues( $params );
            $activity->save( );        
        }

        // create a new version of activity if activity was found to
        // have been modified/created by user
        if ( isset($newActParams) ) {
            unset($newActParams['id']);
            // set proper original_id
            if ( CRM_Utils_Array::value('original_id', $this->_defaults) ) {
                $newActParams['original_id'] = $this->_defaults['original_id'];
            } else {
                $newActParams['original_id'] = $activity->id;
            }
            //is_current_revision will be set to 1 by default.
            
            // add attachments if any
            CRM_Core_BAO_File::formatAttachment( $newActParams,
                                                 $newActParams,
                                                 'civicrm_activity' );
            
            // call begin post process, before the activity is created/updated.
            $this->beginPostProcess( $newActParams );

            $activity = CRM_Activity_BAO_Activity::create( $newActParams );
            
            // call end post process, after the activity has been created/updated.
            $this->endPostProcess( $newActParams, $activity );

            // copy files attached to old activity if any, to new one,
            // as long as users have not selected the 'delete attachment' option.  
            if ( ! CRM_Utils_Array::value( 'is_delete_attachment', $newActParams ) ) {
                CRM_Core_BAO_File::copyEntityFile( 'civicrm_activity', $this->_activityId, 
                                                   'civicrm_activity', $activity->id );
            }

            // copy back params to original var
            $params = $newActParams;
        }

        // update existing case record if needed
        $caseParams       = $params;
        $caseParams['id'] = $this->_caseId;
        if ( CRM_Utils_Array::value('case_type_id', $caseParams ) ) {
            $caseParams['case_type_id'] = CRM_Case_BAO_Case::VALUE_SEPERATOR .
                $caseParams['case_type_id'] . CRM_Case_BAO_Case::VALUE_SEPERATOR;
        }
        if ( CRM_Utils_Array::value('case_status_id', $caseParams) ) {
            $caseParams['status_id'] = $caseParams['case_status_id'];
        }
        // unset params intended for activities only
        unset($caseParams['subject'], $caseParams['details'], 
              $caseParams['status_id'], $caseParams['custom']);
        $case = CRM_Case_BAO_Case::create( $caseParams );


        // create case activity record
        $caseParams = array( 'activity_id' => $activity->id,
                             'case_id'     => $this->_caseId   );
        CRM_Case_BAO_Case::processCaseActivity( $caseParams );

        // create activity assignee records
        $assigneeParams = array( 'activity_id' => $activity->id );
        if (! empty($params['assignee_contact']) ) {
            foreach ( $params['assignee_contact'] as $key => $id ) {
                $assigneeParams['assignee_contact_id'] = $id;
                CRM_Activity_BAO_Activity::createActivityAssignment( $assigneeParams );
            }
        }

        // Insert civicrm_log record for the activity (e.g. store the
        // created / edited by contact id and date for the activity)
        // Note - civicrm_log is already created by CRM_Activity_BAO_Activity::create()


        // send copy to selected contacts.        
        $mailStatus = '';
        if ( array_key_exists('contact_check', $params) ) {
            $mailToContacts = array();
            foreach( $params['contact_check'] as $cid => $dnc ) {
                $mailToContacts[$cid] = $this->_relatedContacts[$cid];
            }
            $result = CRM_Case_BAO_Case::sendActivityCopy( $this->_currentlyViewedContactId, 
                                                           $activity->id, $mailToContacts );
            $mailStatus = "A copy of the activity has also been sent to selected contacts(s).";
        }

        // create follow up activity if needed
        $followupStatus = '';
        if ( CRM_Utils_Array::value('followup_activity_type_id', $params) ) {
            $followupActivity = CRM_Activity_BAO_Activity::createFollowupActivity( $activity->id, $params, true );

            if ( $followupActivity ) {
                $caseParams = array( 'activity_id' => $followupActivity->id,
                                     'case_id'     => $this->_caseId   );
                CRM_Case_BAO_Case::processCaseActivity( $caseParams );
                $followupStatus = "A followup activity has been scheduled.";
            }
        }
        
        CRM_Core_Session::setStatus( ts("'%1' activity has been %2. %3 %4", 
                                        array(1 => $this->_activityTypeName, 
                                              2 => $recordStatus,
                                              3 => $followupStatus,
                                              4 => $mailStatus)) );
    }
}
