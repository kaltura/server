<?php 
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_Partner_LocalStorageConfiguration extends Form_Partner_BaseStorageConfiguration
{
	public function init()
	{
		parent::init();
		
		$this->removeElement('storageUrl');
		
		$this->addElement('text', 'storageBaseDir', array(
			'label'			=> 'Storage Base Directory:',
			'filters'		=> array('StringTrim'),
		
		));
		
		$this->addElementToDisplayGroup('storage_info', 'storageBaseDir');		 

		$this->addElement('select', 'pathManagerClass', array(
			'label'			=> 'Path Manager:',
			'filters'		=> array('StringTrim'),
			'multiOptions'  => array('kPathManager' => 'Kaltura Path',
									'kExternalPathManager' => 'External Path',
		    						'kXslPathManager' => 'XSL Path',
									'kS3PathManager' => 'S3 Path',
									),
		));
		$this->getElement('pathManagerClass')->setRegisterInArrayValidator(false);
		$this->addElementToDisplayGroup('storage_info', 'pathManagerClass'); 
				 
		$this->addElement('checkbox', 'createFileLink', array(
			'label'			=> 'Create as Link:',
			'filters'		=> array('StringTrim'),
						
		));
		$this->addElementToDisplayGroup('storage_info', 'createFileLink');
		
		$this->addElement('textarea', 'pathManagerParams', array(
			'label'			=> 'Path Manager Params (JSON):',
			'cols'			=> 48,
			'rows'			=> 2,
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElementToDisplayGroup('advanced', 'pathManagerParams'); 
	}
	
	
	public function populateFromObject($object, $add_underscore = true)
	{
	    // add actual urlManager & pathManager so the form will not overwrite values which are missing from the combo box and were set through the API
	    $this->addMultiOptionIfMissing('pathManagerClass', $object->pathManagerClass);
	    
	    parent::populateFromObject($object, $add_underscore);
	    $this->setDefault('pathManagerParams', json_encode($object->pathManagerParams));
	}
	
    public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
		$object->pathManagerParams = json_decode($properties['pathManagerParams'], true);
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