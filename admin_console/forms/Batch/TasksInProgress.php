<?php
class Form_Batch_TasksInProgress extends Form_Base
{
	public function init()
	{
		$this->setTemplatePath('forms/in-progress-tasks.phtml');
		
		$oClass = new ReflectionClass('Kaltura_Client_Enum_ConversionEngineType');
		$convertSubTypes = $oClass->getConstants();
		unset($convertSubTypes['Kaltura_COM']);
		
		$oClass = new ReflectionClass('Kaltura_Client_Enum_BatchJobType');
		$jobTypes = array_fill_keys($oClass->getConstants(), false);
		
		$jobTypes[Kaltura_Client_Enum_BatchJobType::CONVERT] = array_fill_keys($convertSubTypes, true);
		$jobTypes[Kaltura_Client_Enum_BatchJobType::IMPORT] = true;
		$jobTypes[Kaltura_Client_Enum_BatchJobType::BULKUPLOAD] = true;
		$jobTypes[Kaltura_Client_Enum_BatchJobType::CONVERT_PROFILE] = true;
		$jobTypes[Kaltura_Client_Enum_BatchJobType::POSTCONVERT] = true;
		$jobTypes[Kaltura_Client_Enum_BatchJobType::EXTRACT_MEDIA] = true;
		
		unset($jobTypes[Kaltura_Client_Enum_BatchJobType::DVDCREATOR]);
		unset($jobTypes[Kaltura_Client_Enum_BatchJobType::OOCONVERT]);
		unset($jobTypes[Kaltura_Client_Enum_BatchJobType::CLEANUP]);
		unset($jobTypes[Kaltura_Client_Enum_BatchJobType::SCHEDULER_HELPER]);
		unset($jobTypes[Kaltura_Client_Enum_BatchJobType::PULL]);
		unset($jobTypes[Kaltura_Client_Enum_BatchJobType::REMOTE_CONVERT]);
		unset($jobTypes[Kaltura_Client_Enum_BatchJobType::DELETE]);
		
		$this->addViewParam('jobTypes', $jobTypes);
		
		// Set the method for the display form to POST
		$this->setMethod('post');
		$this->setAttrib('id', 'frmTasksInProgress');
		
		// Add an createdAt element
		$this->addElement('checkbox', 'createdAt');
		
		// Add an createdAtFrom element
		$this->addElement('text', 'createdAtFrom');
		
		// Add an createdAtTo element
		$this->addElement('text', 'createdAtTo');
		
		// Add an cmdClear element
		$this->addElement('button', 'clearDates', array('label' => 'jobs in-progress filter created clear'));
		
		// Add an entryId element
		$this->addElement('text', 'entryId', array('filters' => array('StringTrim')));
		
		// Add an partnerId element
		$this->addElement('text', 'partnerId', array('filters' => array('StringTrim')));
		
		// Add an allJobs element
		$this->addElement('checkbox', 'allJobs');
		
		foreach($jobTypes as $jobType => $jobSubTypes)
		{
			if(is_array($jobSubTypes))
			{
				foreach($jobSubTypes as $jobSubType => $checked)
				{
	        		$fieldName = 'job_' . str_replace('.', '_', $jobType) . '_' . str_replace('.', '_', $jobSubType);
					$this->addElement('checkbox', $fieldName, array('value' => $checked));
				}
			}
			else
			{
				$checked = $jobSubTypes;
        		$fieldName = 'job_' . str_replace('.', '_', $jobType);
				$this->addElement('checkbox', $fieldName, array('value' => $checked));
			}
		}
		
		// Add the search button
		$this->addElement('button', 'search', array(
			'type' => 'submit',
			'ignore' => true, 
			'label' => 'entry-history search button'
		));
		
		// Add the page buttons
		$this->addElement('hidden', 'inProgressPage', array());
		$this->addElement('hidden', 'inQueuePage', array());
		$this->addElement('hidden', 'pageSize', array('value' => 10));
	}
}