<?php 
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
		 
		$this->addElement('textarea', 'desciption', array(
			'label'			=> 'Description:',
			'cols'			=> 48,
			'rows'			=> 2,
			'filters'		=> array('StringTrim'),
		
		));
		 
		$this->addElement('select', 'protocol', array(
			'label'			=> 'Transfer Protocol:',
			'filters'		=> array('StringTrim'),
		
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
		 
		$this->addElement('text', 'deliveryHttpBaseUrl', array(
			'label'			=> '*HTTP Delivery Base URL:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
			
		));
		 
		$this->addElement('text', 'deliveryRmpBaseUrl', array(
			'label'			=> 'RTMP Delivery Base URL:',
			'filters'		=> array('StringTrim'),
			
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
			
		));
		 
		$this->addElement('select', 'urlManagerClass', array(
			'label'			=> 'Delivery URL format :',
			'filters'		=> array('StringTrim'),
			
		));
		 
		$this->addElement('select', 'trigger', array(
			'label'			=> 'Trigger:',
			'filters'		=> array('StringTrim'),
			
		));
	}
	
	public function addFlavorParamsFields(Kaltura_Client_Type_FlavorParamsListResponse $flavorParams, array $selectedFlavorParams = array())
	{
		foreach($flavorParams->objects as $index => $flavorParamsItem)
		{
			$checked = in_array($flavorParamsItem->id, $selectedFlavorParams);
			$this->addElement('checkbox', 'flavorParamsId_' . $flavorParamsItem->id, array(
				'label'			=> "Flavor Params $flavorParamsItem->name",
				'checked'		=> $checked,
			    'indicator'		=> 'dynamic',
				'decorators' => array('ViewHelper', array('Label', array('placement' => 'append'))),
			));
		}
	}
}