<?php 
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_Partner_StorageConfiguration extends Infra_Form
{
	public function init()
	{
		// Set the method for the display form to POST
		$this->setMethod('post');
		$this->setAttrib('id', 'frmStorageConfig');

		$this->addElement('text', 'partnerId', array(
			'label' 		=> '*Related Publisher ID:',
			'required'		=> true,
			'filters' 		=> array('StringTrim'),
			'validators' 	=> array()
		
		));
		
		$this->addElement('text', 'name', array(
			'label' 		=> '*Remote Storage Name:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'systemName', array(
			'label' 		=> 'System Name:',
			'filters'		=> array('StringTrim'),
		));
		
		 
		$deliveryStatus = new Kaltura_Form_Element_EnumSelect('deliveryStatus', array('enum' => 'Kaltura_Client_Enum_StorageProfileDeliveryStatus'));
		$deliveryStatus->setLabel('Delivery Status:');
		$this->addElements(array($deliveryStatus));	
		
		$this->addElement('text', 'deliveryPriority', array(
			'label' 		=> 'Delivery Priority:',
			'required'		=> false,
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('textarea', 'desciption', array(
			'label'			=> 'Description:',
			'cols'			=> 60,
			'rows'			=> 3,
			'filters'		=> array('StringTrim'),
		));
		 
		$this->addElement('select', 'protocol', array(
			'label'			=> 'Transfer Protocol:',
			'filters'		=> array('StringTrim'),
			'multiOptions'  => array(Kaltura_Client_Enum_StorageProfileProtocol::FTP => 'FTP',
									Kaltura_Client_Enum_StorageProfileProtocol::SFTP => 'SFTP',
									Kaltura_Client_Enum_StorageProfileProtocol::SCP => 'SCP',
									Kaltura_Client_Enum_StorageProfileProtocol::S3 => 'Amazon S3'
									),
		
		));
		 
		$this->addElement('text', 'storageUrl', array(
			'label'			=> '*Storage URL:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
		));
		 
		$this->addElement('text', 'storageBaseDir', array(
			'label'			=> 'Storage Base Directory:',
			'filters'		=> array('StringTrim'),
		
		));
		 
		$this->addElement('text', 'storageUsername', array(
			'label'			=> '*Storage Username:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
		
		));
		 
		$this->addElement('text', 'storagePassword', array(
			'label'			=> '*Storage Password:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
		
		));
		 
		$this->addElement('checkbox', 'storageFtpPassiveMode', array(
			'label'			=> 'Storage FTP Passive Mode',
			'filters'		=> array('StringTrim'),
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append'))),			
		));
		
		$this->addElement('checkbox', 'allowAutoDelete', array(
			'label'			=> 'Allow auto-deletion of files',
			'filters'		=> array('StringTrim'),
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append'))),			
		));
		 
				
		$this->addElement('select', 'filesPermissionInS3', array(
			'label'			=> 'Files Permission In S3:',
			'filters'		=> array('StringTrim'),
			'multiOptions'  => array(Kaltura_Client_Enum_AmazonS3StorageProfileFilesPermissionLevel::ACL_PRIVATE => 'Private',
									 Kaltura_Client_Enum_AmazonS3StorageProfileFilesPermissionLevel::ACL_PUBLIC_READ => 'Public Read',
									),										
		));
		
		$this->addElement('text', 'deliveryHttpBaseUrl', array(
			'label'			=> '*HTTP Delivery Base URL:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
			
		));
		 
		$this->addElement('text', 'deliveryRmpBaseUrl', array(
			'label'			=> 'RTMP Delivery Base URL:',
			'filters'		=> array('StringTrim'),
			
		));
		
		$this->addElement('text', 'rtmpPrefix', array(
		    'label'        =>  'RTMP stream URL prefix:',
		    'filters'      =>   array('StringTrim'),
		));
		 
		$this->addElement('text', 'deliveryIisBaseUrl', array(
			'label'			=> 'IIS Delivery Base URL:',
			'filters'		=> array('StringTrim'),
			
		));
		 
		$this->addElement('text', 'minFileSize', array(
			'label'			=> 'Export only files bigger than:',
			'filters'		=> array('Digits'),
			
		));
		 
		$this->addElement('text', 'maxFileSize', array(
			'label'			=> 'Export only files smaller than:',
			'filters'		=> array('Digits'),
			
		));
		 
		$this->addElement('select', 'pathManagerClass', array(
			'label'			=> 'Path Manager:',
			'filters'		=> array('StringTrim'),
			'multiOptions'  => array('kPathManager' => 'Kaltura Path',
									'kExternalPathManager' => 'External Path',
		    						'kXslPathManager' => 'XSL Path',
									),					
		));
		$this->getElement('pathManagerClass')->setRegisterInArrayValidator(false);
		 
		
		$this->addElement('select', 'urlManagerClass', array(
			'label'			=> 'Delivery URL format :',
			'filters'		=> array('StringTrim'),
			'multiOptions'  => array('' => 'Kaltura Delivery URL Format',
									'kLocalPathUrlManager' => 'QA FMS Server',
									'kLimeLightUrlManager' => 'Lime Light CDN',
									'kAkamaiUrlManager' => 'Akamai CDN',
									'kLevel3UrlManager' => 'Level 3 CDN',
		    						'kMirrorImageUrlManager' => 'Mirror Image CDN',
									),		
			
		));
		$this->getElement('urlManagerClass')->setRegisterInArrayValidator(false);
		 
		
		$this->addElement('select', 'trigger', array(
			'label'			=> 'Trigger:',
			'filters'		=> array('StringTrim'),
			'multiOptions'  => array(3 => 'Flavor Ready',
									2 => 'Moderation Approved',
							  ),	
		));
		
		$readyBehavior = new Kaltura_Form_Element_EnumSelect('readyBehavior', array('enum' => 'Kaltura_Client_Enum_StorageProfileReadyBehavior'));
		$readyBehavior->setLabel('Ready Behavior:');
		$this->addElements(array($readyBehavior));
		
		$this->addElement('textarea', 'urlManagerParamsJson', array(
			'label'			=> 'URL Manager Params (JSON):',
			'cols'			=> 48,
			'rows'			=> 2,
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('textarea', 'pathManagerParamsJson', array(
			'label'			=> 'Path Manager Params (JSON):',
			'cols'			=> 48,
			'rows'			=> 2,
			'filters'		=> array('StringTrim'),
		));
	}
	
	public function addFlavorParamsFields(Kaltura_Client_Type_FlavorParamsListResponse $flavorParams, array $selectedFlavorParams = array())
	{
		foreach($flavorParams->objects as $index => $flavorParamsItem)
		{
			$checked = in_array($flavorParamsItem->id, $selectedFlavorParams);
			$this->addElement('checkbox', 'flavorParamsId_' . $flavorParamsItem->id, array(
				'label'			=> "Flavor Params {$flavorParamsItem->name} ({$flavorParamsItem->id})",
				'checked'		=> $checked,
			    'indicator'		=> 'dynamic',
				'decorators' => array('ViewHelper', array('Label', array('placement' => 'append'))),
			));
		}
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