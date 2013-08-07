<?php 
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_Partner_StorageConfiguration extends Form_Partner_BaseStorageConfiguration
{
	public function init()
	{
		parent::init();
		
		$this->addElement('text', 'storageBaseDir', array(
			'label'			=> 'Storage Base Directory:',
			'filters'		=> array('StringTrim'),
		
		));
		
		$this->addElementToDisplayGroup('storage_info', 'storageBaseDir');
		
		 
		$this->addElement('text', 'storageUsername', array(
			'label'			=> '*Storage Username:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
		
		));
		$this->addElementToDisplayGroup('storage_info', 'storageUsername');
		 
		$this->addElement('text', 'storagePassword', array(
			'label'			=> '*Storage Password:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
		
		));
		$this->addElementToDisplayGroup('storage_info', 'storagePassword');
		 
		 
		$this->addElement('checkbox', 'storageFtpPassiveMode', array(
			'label'			=> 'Storage FTP Passive Mode',
			'filters'		=> array('StringTrim'),
						
		));
		$this->addElementToDisplayGroup('storage_info', 'storageFtpPassiveMode');
		KalturaLog::debug('1');
				
		$this->addElement('select', 'filesPermissionInS3', array(
			'label'			=> 'Files Permission In S3:',
			'filters'		=> array('StringTrim'),
			'multiOptions'  => array(Kaltura_Client_Enum_AmazonS3StorageProfileFilesPermissionLevel::ACL_PRIVATE => 'Private',
									 Kaltura_Client_Enum_AmazonS3StorageProfileFilesPermissionLevel::ACL_PUBLIC_READ => 'Public Read',
									),										
		));
		
		$this->addElementToDisplayGroup('playback_info', 'filesPermissionInS3');
		
		$this->addElement('text', 'deliveryHttpBaseUrl', array(
			'label'			=> '*HTTP Delivery Base URL:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
			
		));
		$this->addElementToDisplayGroup('playback_info', 'deliveryHttpBaseUrl'); 

		$this->addElement('text', 'deliveryRmpBaseUrl', array(
			'label'			=> 'RTMP Delivery Base URL:',
			'filters'		=> array('StringTrim'),
			
		));
		$this->addElementToDisplayGroup('playback_info', 'deliveryRmpBaseUrl'); 
		
		$this->addElement('text', 'rtmpPrefix', array(
		    'label'        =>  'RTMP stream URL prefix:',
		    'filters'      =>   array('StringTrim'),
		));
		$this->addElementToDisplayGroup('playback_info', 'rtmpPrefix'); 
		
		 
		$this->addElement('text', 'deliveryIisBaseUrl', array(
			'label'			=> 'IIS Delivery Base URL:',
			'filters'		=> array('StringTrim'),
			
		));
		$this->addElementToDisplayGroup('playback_info', 'deliveryIisBaseUrl'); 
		 
		$this->addElement('select', 'pathManagerClass', array(
			'label'			=> 'Path Manager:',
			'filters'		=> array('StringTrim'),
			'multiOptions'  => array('kPathManager' => 'Kaltura Path',
									'kExternalPathManager' => 'External Path',
		    						'kXslPathManager' => 'XSL Path',
									),					
		));
		$this->getElement('pathManagerClass')->setRegisterInArrayValidator(false);
		$this->addElementToDisplayGroup('storage_info', 'pathManagerClass'); 
		 
		$this->addElement('textarea', 'pathManagerParamsJson', array(
			'label'			=> 'Path Manager Params (JSON):',
			'cols'			=> 48,
			'rows'			=> 2,
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElementToDisplayGroup('advanced', 'pathManagerParamsJson'); 

	}
	
	
	public function populateFromObject($object, $add_underscore = true)
	{
	    // add actual urlManager & pathManager so the form will not overwrite values which are missing from the combo box and were set through the API
	    $this->addMultiOptionIfMissing('urlManagerClass', $object->urlManagerClass);
	    $this->addMultiOptionIfMissing('pathManagerClass', $object->pathManagerClass);
	    
	    parent::populateFromObject($object, $add_underscore);
	    $this->setDefault('urlManagerParamsJson', json_encode($object->urlManagerParams));
	    $this->setDefault('pathManagerParamsJson', json_encode($object->pathManagerParams));
	}
	
    public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
		$object->urlManagerParams = json_decode($properties['urlManagerParamsJson'], true);
		$object->pathManagerParams = json_decode($properties['pathManagerParamsJson'], true);
		return $object;
	}
	
	public function addMultiOptionIfMissing($elementName, $newOption)
	{
		$currentOptions = $this->getElement($elementName)->getMultiOptions();
		if (!isset($currentOptions[$newOption]))
		{
		    $this->getElement($elementName)->addMultiOption($newOption, $newOption)->setRegisterInArrayValidator(false);
		}
	}
}