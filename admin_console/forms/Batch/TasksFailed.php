<?php 
class Form_Batch_TasksFailed extends Form_Base
{
    public function init()
    {
		$this->setTemplatePath('forms/failed-tasks.phtml');
		
		$oClass = new ReflectionClass('KalturaBatchJobErrorTypes');
		$failureTypes = $oClass->getConstants();
	
    	$statuses = array(
    		KalturaBatchJobStatus::FAILED => $failureTypes,
    		KalturaBatchJobStatus::ABORTED => true,
    		KalturaBatchJobStatus::FATAL => true,
    	);
    	
		$this->addViewParam('failStatuses', $statuses);
    		
		$oClass = new ReflectionClass('KalturaConversionEngineType');
		$convertSubTypes = $oClass->getConstants();
		unset($convertSubTypes['KALTURA_COM']);
		
		$oClass = new ReflectionClass('KalturaBatchJobType');
		$jobTypes = array_fill_keys($oClass->getConstants(), false);
		
		$jobTypes[KalturaBatchJobType::CONVERT] = array_fill_keys($convertSubTypes, true);
		$jobTypes[KalturaBatchJobType::IMPORT] = true;
		$jobTypes[KalturaBatchJobType::BULKUPLOAD] = true;
		
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
			        $this->addElement('checkbox', "job_{$jobType}_{$jobSubType}", array('value' => $checked));
        	}
        	else
        	{
				$checked = $jobSubTypes;
				$this->addElement('checkbox', "job_$jobType", array('value' => $checked));
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