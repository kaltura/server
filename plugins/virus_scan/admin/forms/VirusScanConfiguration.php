<?php 
/**
 * @package plugins.virusScan
 * @subpackage Admin
 */
class Form_Partner_VirusScanConfiguration extends Infra_Form
{
	public function init()
	{
		// Set the method for the display form to POST
		$this->setMethod('post');
		$this->setAttrib('id', 'frmVirusScanProfileConfig');
		
		$this->addElement('text', 'partnerId', array(
			'label' 		=> '*Related Publisher ID:',
			'required'		=> true,
			'filters' 		=> array('StringTrim'),	
		));
		
		$this->addElement('text', 'name', array(
			'label' 		=> '*Virus Scan Profile Name:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
		));
		
		$engineType = new Kaltura_Form_Element_EnumSelect('engineType', array('enum' => 'Kaltura_Client_VirusScan_Enum_VirusScanEngineType'));
		$engineType->setLabel('Engine Type:');
		$engineType->setRequired(true);
		$this->addElements(array($engineType));
		
		
		$actionIfInfected = new Kaltura_Form_Element_EnumSelect('actionIfInfected', array('enum' => 'Kaltura_Client_VirusScan_Enum_VirusFoundAction'));
		$actionIfInfected->setLabel('Cleaning Policy:');
		$actionIfInfected->setRequired(true);
		$this->addElements(array($actionIfInfected));
		
		 
		$this->addElement('multiselect', 'entryTypeToFilter', array(
			'label'			=> 'Entry Type:',
			'size' => 3,
			'filters'		=> array('StringTrim'),
		));
						
		
		//entry type to filter drop down list
		$arr = array(
			Kaltura_Client_Enum_EntryType::DATA => 'Data',
			Kaltura_Client_Enum_EntryType::MEDIA_CLIP => 'Media Clip',
			Kaltura_Client_Enum_EntryType::DOCUMENT => 'Document',
		);
		$this->getElement('entryTypeToFilter')->setMultiOptions($arr);
	}
	
}