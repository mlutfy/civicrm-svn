<?php
require_once('AuditConfig.php');

class Audit
{
	private $auditConfig;
	private $xmlString;
	private $sortBy;
	
	public function __construct($xmlString, $confFilename)
	{
		$this->xmlString = $xmlString;
		$this->auditConfig = new AuditConfig($confFilename);
	}
		
	public function getSortBy()
	{
		return $this->sortBy;
	}
	
	public function getActivities()
	{
		$retval = array();
	
		/*
		 * Loop through the activities in the file and add them to the appropriate region array.
		 */
		$doc = new DOMDocument();
		if ($doc->loadXML($this->xmlString))
		{
			$regionList = $this->auditConfig->getRegions();
			
			$includeAll = $doc->getElementsByTagName("IncludeActivities")->item(0)->nodeValue;
			$includeAll = ($includeAll == 'All');
			
			$this->sortBy = $doc->getElementsByTagName("SortBy")->item(0)->nodeValue;
			
			
			$activityindex = 0;
			$activityList = $doc->getElementsByTagName("Activity");
			foreach($activityList as $activity)
			{
				$retval[$activityindex] = array();
				
				$completed = false;
				$sortValues = array('1970-01-01');
				$category = '';
				$fieldindex = 1;
				$fields = $activity->getElementsByTagName("Field");
				foreach($fields as $field)
				{
					$datatype_elements = $field->getElementsByTagName("Type");
					$datatype = $datatype_elements->item(0)->nodeValue;
					
					$label_elements = $field->getElementsByTagName("Label");
					$label = $label_elements->item(0)->nodeValue;
				
					$value_elements = $field->getElementsByTagName("Value");
					$value = $value_elements->item(0)->nodeValue;

					$category_elements = $field->getElementsByTagName("Category");
					if (! empty($category_elements))
					{
						$category = $category_elements->item(0)->nodeValue;
					}
					
					// Based on the config file, does this field's label and value indicate a completed activity?							
					if ($label == $this->auditConfig->getCompletionLabel() && $value == $this->auditConfig->getCompletionValue())
					{
						$completed = true;
					}

					// Based on the config file, does this field's label match the one to use for sorting activities?							
					if (in_array($label, $this->auditConfig->getSortByLabels()))
					{
						$sortValues[$label] = $value;
					}
					
					foreach($regionList as $region)
					{
						if ($this->auditConfig->includeInRegion($label, $region))
						{
							$retval[$activityindex][$region][$fieldindex] = array();
							$retval[$activityindex][$region][$fieldindex]['label'] = $label;
							$retval[$activityindex][$region][$fieldindex]['datatype'] = $datatype;
							$retval[$activityindex][$region][$fieldindex]['value'] = $value;
							if ($datatype == 'Date')
							{
								$retval[$activityindex][$region][$fieldindex]['includeTime'] = $this->auditConfig->includeTime($label, $region);
							}
						}
					}
	
					$fieldindex++;
				}

				if ($includeAll || !$completed)
				{	
					$retval[$activityindex]['completed'] = $completed;
					$retval[$activityindex]['category'] = $category;
					$retval[$activityindex]['sortValues'] = $sortValues;
		
					// Now sort the fields based on the order in the config file.
					foreach($regionList as $region)
					{
						$this->auditConfig->sort($retval[$activityindex][$region], $region);
					}				
						
					$retval[$activityindex]['editurl'] = $activity->getElementsByTagName("EditURL")->item(0)->nodeValue;
								
					$activityindex++;
				}
				else
				{
					/* This is a little bit inefficient, but the alternative is to do two passes
					because we don't know until we've examined all the field values whether the activity
					is completed, since the field that determines it and its value is configurable,
					so either way isn't ideal. */
					unset($retval[$activityindex]);
				}
			}
			
			uasort($retval, array(&$this, "compareActivities"));
		}		
            
		return $retval;
	}
	
	/* compareActivities
	 * 
	 * This is intended to be called as a sort callback function, returning whether an activity's date is earlier or later than another's.
	 * The type of date to use is specified in the config.
	 * 
	 */
	public function compareActivities($a, $b)
	{
		// This should work
		foreach ($this->auditConfig->getSortByLabels() as $label)
		{
			$aval .= empty($a['sortValues']) ? "" : (empty($a['sortValues'][$label]) ? "" : $a['sortValues'][$label]);
			$bval .= empty($b['sortValues']) ? "" : (empty($b['sortValues'][$label]) ? "" : $b['sortValues'][$label]);
		}
		
		if ($aval < $bval)
		{
			return -1;
		}
		elseif ($aval > $bval)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}
	
    static function run( $xmlString ) {

$fh = fopen('C:/temp/lasfj.xml', 'w');
fwrite($fh, $xmlString);
fclose($fh);

        $audit = new Audit( $xmlString,
                            'audit.conf.xml' );
        $activities = $audit->getActivities();

        $template = CRM_Core_Smarty::singleton( );
        $template->assign_by_ref( 'activities', $activities );
		$template->assign( 'sortBy', $audit->getSortBy() );
		
        $contents = $template->fetch( 'CRM/Case/Audit/Audit.tpl' );
        return $contents;
    }
}

// Audit::run( file_get_contents( 'CaseReport.xml' ) );