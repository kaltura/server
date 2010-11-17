<?php
class Form_Batch_TasksInProgress extends Form_Base
{
	public function init()
	{
		$this->setTemplatePath('forms/in-progress-tasks.phtml');
		
		$oClass = new ReflectionClass('KalturaConversionEngineType');
		$convertSubTypes = $oClass->getConstants();
		unset($convertSubTypes['KALTURA_COM']);
		
		$oClass = new ReflectionClass('KalturaBatchJobType');
		$jobTypes = array_fill_keys($oClass->getConstants(), false);
		
		$jobTypes[KalturaBatchJobType::CONVERT] = array_fill_keys($convertSubTypes, true);
		$jobTypes[KalturaBatchJobType::IMPORT] = true;
		$jobTypes[KalturaBatchJobType::BULKUPLOAD] = true;
		$jobTypes[KalturaBatchJobType::CONVERT_PROFILE] = true;
		$jobTypes[KalturaBatchJobType::POSTCONVERT] = true;
		$jobTypes[KalturaBatchJobType::EXTRACT_MEDIA] = true;
		
		unset($jobTypes[KalturaBatchJobType::DVDCREATOR]);
		unset($jobTypes[KalturaBatchJobType::OOCONVERT]);
		unset($jobTypes[KalturaBatchJobType::CLEANUP]);
		unset($jobTypes[KalturaBatchJobType::SCHEDULER_HELPER]);
		unset($jobTypes[KalturaBatchJobType::PULL]);
		unset($jobTypes[KalturaBatchJobType::REMOTE_CONVERT]);
		unset($jobTypes[KalturaBatchJobType::DELETE]);
		
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