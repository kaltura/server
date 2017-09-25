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
		
		$this->addElement('text', 'storageUsername', array(
			'label'			=> 'Storage Username*:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
		));
		$this->addElementToDisplayGroup('storage_info', 'storageUsername');
		 
		$this->addElement('text', 'storagePassword', array(
			'label'			=> 'Storage Password:',
			'filters'		=> array('StringTrim'),
		));
		$this->addElementToDisplayGroup('storage_info', 'storagePassword');
		 
		$this->addElement('text', 's3Region', array(
				'label'			=> 'S3 Region:',
				'filters'		=> array('StringTrim'),
		));
		$this->addElementToDisplayGroup('storage_info', 's3Region');

		$this->addElement('select', 'sseType', array(
				'label'			=> 'Server-Side Encryption(SSE) Type:',
				'filters'		=> array('StringTrim'),
				'multiOptions'	=> array('None' => 'None',
									'KMS' => 'KMS',
									'AES256' => 'AES256'
									),
		));
		$this->addElementToDisplayGroup('storage_info', 'sseType');
		
		$this->addElement('text', 'sseKmsKeyId', array(
				'label'			=> 'SSE KMS Key ID:',
				'filters'		=> array('StringTrim'),
		));
		$this->addElementToDisplayGroup('storage_info', 'sseKmsKeyId');

		$this->addElement('select', 'signatureType', array(
				'label'			=> 'Signature Type:',
				'filters'		=> array('StringTrim'),
				'multiOptions'	=> array('' => 'Default (v4)',
						's3' => 's3',
				),
		));
		$this->addElementToDisplayGroup('storage_info', 'signatureType');
		
		$this->addElement('text', 'endPoint', array(
				'label'			=> 'Service endpoint:',
				'filters'		=> array('StringTrim'),
		));
		$this->addElementToDisplayGroup('storage_info', 'endPoint');
		
		
		$this->addElement('checkbox', 'storageFtpPassiveMode', array(
			'label'			=> 'Storage FTP Passive Mode:',
			'filters'		=> array('StringTrim'),
		));
		$this->addElementToDisplayGroup('storage_info', 'storageFtpPassiveMode');
		
		// Support for key pair
		
		$this->addElement('file', 'sshPublicKey', array(
				'label' => 'SSH Public Key:',
		));
		$this->addElementToDisplayGroup('storage_info', 'sshPublicKey');
		
		$this->addElement('textarea', 'publicKey', array(
				'label' => 'SSH Public Key Data:',
				'rows' => '2',
				'cols' => '50',
				'readonly' => '1'
		));
		$this->addElementToDisplayGroup('storage_info', 'publicKey');
		
		$this->addElement('file', 'sshPrivateKey', array(
				'label' => 'SSH Private Key:',
		));
		$this->addElementToDisplayGroup('storage_info', 'sshPrivateKey');
		
		$this->addElement('textarea', 'privateKey', array(
				'label' => 'SSH Private Key Data:',
				'rows' => '2',
				'cols' => '50',
				'readonly' => '1'
		));
		$this->addElementToDisplayGroup('storage_info', 'privateKey');
		
		$this->addElement('text', 'passPhrase', array(
				'label'			=> 'SSH Pass Phrase:',
				'filters'		=> array('StringTrim'),
		));
		$this->addElementToDisplayGroup('storage_info', 'passPhrase');
		
		// -- End of support for key pair 
		
		
				
		$this->addElement('select', 'filesPermissionInS3', array(
			'label'			=> 'Files Permission In S3:',
			'filters'		=> array('StringTrim'),
			'multiOptions'  => array(Kaltura_Client_Enum_AmazonS3StorageProfileFilesPermissionLevel::ACL_PRIVATE => 'Private',
									 Kaltura_Client_Enum_AmazonS3StorageProfileFilesPermissionLevel::ACL_PUBLIC_READ => 'Public Read',
									),	
		));
		
		$this->addElementToDisplayGroup('playback_info', 'filesPermissionInS3');
		
		$this->addElement('textarea', 'pathManagerParams', array(
			'label'			=> 'Path Manager Params (JSON):',
			'cols'			=> 48,
			'rows'			=> 2,
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElementToDisplayGroup('advanced', 'pathManagerParams');

		$this->addElement('checkbox', 'shouldExportThumbnails', array(
			'label'			=> "Should export thumbnail assets",
			'checked'		=> false,
			'indicator'		=> 'dynamic',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt')))
		));
		$this->addElementToDisplayGroup('advanced', 'shouldExportThumbnails');
	}
	
	
	public function populateFromObject($object, $add_underscore = true)
	{
	    // add actual urlManager & pathManager so the form will not overwrite values which are missing from the combo box and were set through the API
	    $this->addMultiOptionIfMissing('pathManagerClass', $object->pathManagerClass);
	    
	    parent::populateFromObject($object, $add_underscore);
	    $this->setDefault('pathManagerParams', json_encode($object->pathManagerParams));
		$this->setDefault('shouldExportThumbnails', $object->shouldExportThumbs);
	}
	
    public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
		
		// Add file content if avilable
		$upload = new Zend_File_Transfer_Adapter_Http();
		$files = $upload->getFileInfo();
		 
		if(isset($files['sshPublicKey']))
		{
			$file = $files['sshPublicKey'];
			if ($file['size'])
			{
				$content = file_get_contents($file['tmp_name']);
				$object->publicKey = $content;
			}
		}
			
		if(isset($files['sshPrivateKey']))
		{
			$file = $files['sshPrivateKey'];
			if ($file['size'])
			{
				$content = file_get_contents($file['tmp_name']);
				$object->privateKey = $content;
			}
		}
		
		$object->pathManagerParams = json_decode($properties['pathManagerParams'], true);
		$object->shouldExportThumbs = $properties['shouldExportThumbnails'];
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
