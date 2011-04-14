<?php 
class Form_Batch_TasksFailed extends Form_Base
{
    public function init()
    {
		$this->setTemplatePath('forms/failed-tasks.phtml');
		
		$oClass = new ReflectionClass('Kaltura_Client_Enum_BatchJobErrorTypes');
		$failureTypes = $oClass->getConstants();
	
    	$statuses = array(
    		Kaltura_Client_Enum_BatchJobStatus::FAILED => $failureTypes,
    		Kaltura_Client_Enum_BatchJobStatus::ABORTED => true,
    		Kaltura_Client_Enum_BatchJobStatus::FATAL => true,
    	);
    	
		$this->addViewParam('failStatuses', $statuses);
    		
		$oClass = new ReflectionClass('Kaltura_Client_Enum_ConversionEngineType');
		$convertSubTypes = $oClass->getConstants();
		unset($convertSubTypes['Kaltura_COM']);
		
		$oClass = new ReflectionClass('Kaltura_Client_Enum_BatchJobType');
		$jobTypes = array_fill_keys($oClass->getConstants(), false);
		
		$jobTypes[Kaltura_Client_Enum_BatchJobType::CONVERT] = array_fill_keys($convertSubTypes, true);
		$jobTypes[Kaltura_Client_Enum_BatchJobType::IMPORT] = true;
		$jobTypes[Kaltura_Client_Enum_BatchJobType::BULKUPLOAD] = true;
		
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
        $this->setAttrib('id', 'frmTasksFailed');

		// Add an createdAt element
        $this->addElement('checkbox', 'createdAt');
        
		// Add an createdAtFrom element
        $this->addElement('text', 'createdAtFrom');
        
		// Add an createdAtTo element
        $this->addElement('text', 'createdAtTo', array(
            'filters'    => array('StringTrim'),
        ));
        
		// Add an entryId element
        $this->addElement('text', 'entryId', array(
            'filters'    => array('StringTrim'),
        ));
        
		// Add an partnerId element
        $this->addElement('text', 'partnerId', array(
            'filters'    => array('StringTrim'),
        ));
        
		// Add an allJobs element
        $this->addElement('checkbox', 'allReasons', array('value' => true));
    
        foreach($statuses as $status => $errorTypes)
        {
			// Add an allJobs element
	        $this->addElement('checkbox', "status_$status");
	        
        	if(is_array($errorTypes))
        	{
	        	foreach($errorTypes as $errorType)
			        $this->addElement('checkbox', "status_{$status}_{$errorType}");
        	}
        }
        
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
        	'label'    => 'jobs failed filter button label',
            'ignore'   => true,
        ));
        
        $this->addElement('hidden', 'submitAction');        
        $this->addElement('hidden', 'actionJobs');
        
		$this->addElement('hidden', 'page', array());
        $this->addElement('hidden', 'pageSize', array('value' => 10));
    }
}