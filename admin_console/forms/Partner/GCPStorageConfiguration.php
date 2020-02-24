<?php 
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_Partner_GCPStorageConfiguration extends Form_Partner_BaseStorageConfiguration
{
	public function init()
	{
		parent::init();

		$this->removeElement("storageUrl");

		$this->addElement('text', 'bucketName', array(
			'label'			=> 'Bucket Name*:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
		));

		$this->addElementToDisplayGroup('storage_info', 'bucketName');

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
			),
		));
		$this->getElement('pathManagerClass')->setRegisterInArrayValidator(false);
		$this->addElementToDisplayGroup('storage_info', 'pathManagerClass');

		$this->addElement('textarea', 'keyFile', array(
			'label'			=> 'Key File (JSON):',
			'cols'			=> 110,
			'rows'			=> 10,
			'filters'		=> array('StringTrim'),
		));

		$this->addElementToDisplayGroup('storage_info', 'keyFile');

		$this->addElement('select', 'filesPermissionInGCP', array(
			'label'			=> 'Files Permission In GCP:',
			'filters'		=> array('StringTrim'),
			'multiOptions'  => array(Kaltura_Client_Enum_GCPStorageProfileFilesPermissionLevel::ACL_PRIVATE => 'Private',
				Kaltura_Client_Enum_GCPStorageProfileFilesPermissionLevel::ACL_PUBLIC_READ => 'Public Read',
			),
		));

		$this->addElementToDisplayGroup('playback_info', 'filesPermissionInGCP');

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
