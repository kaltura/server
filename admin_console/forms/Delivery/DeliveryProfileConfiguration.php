<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_Delivery_DeliveryProfileConfiguration extends Infra_Form
{
	public function init()
	{
		// Set the method for the display form to POST
		$this->setMethod('post');
		$this->setAttrib('id', 'frmDeliveryProfileConfig');
		
		$this->addElement('text', 'deliveryProfileId', array(
				'label' 		=> 'Delivery Profile ID:',
				'filters' 		=> array('StringTrim'),
				'validators' 	=> array(),
				'readonly'		=> true,
		));
		
		$this->addElement('text', 'partnerId', array(
				'label' 		=> 'Related Publisher ID*:',
				'required'		=> true,
				'filters' 		=> array('StringTrim'),
				'validators' 	=> array(),
		));
		
		$this->addElement('text', 'name', array(
				'label' 		=> 'Delivery profile name*:',
				'required'		=> true,
				'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'systemName', array(
				'label' 		=> 'System Name:',
				'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'description', array(
				'label' 		=> 'Description:',
				'filters'		=> array('StringTrim'),
		));
		
		$deliveryStatus = new Kaltura_Form_Element_EnumSelect('status', array('enum' => 'Kaltura_Client_Enum_DeliveryStatus'));
		$deliveryStatus->setLabel('Delivery Status:');
		$this->addElements(array($deliveryStatus));
		
		$this->addDisplayGroup(array('deliveryProfileId', 'partnerId', 'name', 'systemName', 'description', 'status'), 'general_info', array(
				'legend' => 'General',
		));
		
		$this->addElement('hidden', 'crossLine0', array(
				'lable'			=> 'line',
				'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'hr', 'class' => 'crossLine')))
		));
		
		$type = new Kaltura_Form_Element_EnumSelect('type', array('enum' => 'Kaltura_Client_Enum_DeliveryProfileType'));
		$type->setLabel('Delivery profile Type*:');
		$this->addElements(array($type));
		
		$streamerType = new Kaltura_Form_Element_EnumSelect('streamerType', array('enum' => 'Kaltura_Client_Enum_PlaybackProtocol'));
		$streamerType->setLabel('Streamer Type*:');
		$this->addElements(array($streamerType));
		
		$this->addElement('text', 'mediaProtocols', array(
				'label' 		=> 'Supported Media Protocols:',
				'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'url', array(
				'label' 		=> 'Delivery profile URL*:',
				'required'		=> true,
				'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'isDefault', array(
				'label' 		=> 'Is Default Delivery Profile:',
				'filters'		=> array('StringTrim'),
				'readonly'		=> true,
		));
		
		
		$this->addDisplayGroup(array('type', 'streamerType', 'mediaProtocols', 'url', 'isDefault'), 'delivery_info', array(
				'legend' => 'Delivery Info',
		));
		
		$this->addElement('hidden', 'crossLine1', array(
				'lable'			=> 'line',
				'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'hr', 'class' => 'crossLine')))
		));
		
		$element2 = new Infra_Form_Html ( 'place_holder2', array ('content' => '<span/>' ) );
		$this->addElement ( $element2 );
		
		$this->addDisplayGroup(array('place_holder2'), 'recognizer', array(
				'legend' => 'Recognizer configuration',
		));
		
		$this->addElement('hidden', 'crossLine2', array(
				'lable'			=> 'line',
				'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'hr', 'class' => 'crossLine')))
		));
		
		$element3 = new Infra_Form_Html ( 'place_holder3', array ('content' => '<span/>' ) );
		$this->addElement ( $element3 );
		
		$this->addDisplayGroup(array('place_holder3'), 'tokenizer', array(
				'legend' => 'Tokenizer configuration',
		));
		
		$this->addElement('hidden', 'crossLine3', array(
				'lable'			=> 'line',
				'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'hr', 'class' => 'crossLine')))
		));
		
		$advancedSettings = $this->getAdvancedSettings();
		$this->addDisplayGroup ($advancedSettings, 'advanced', array ('legend' => 'Advanced' ) );
		
		// -----------------------
		$displayGroups = $this->getDisplayGroups();
		foreach ($displayGroups as $displayGroup)
		{
			$displayGroup->removeDecorator ('label');
			$displayGroup->removeDecorator('DtDdWrapper');
		}
		
		$openLeftDisplayGroup = $this->getDisplayGroup('general_info');
		$openLeftDisplayGroup->setDecorators(array(
				'FormElements',
				'Fieldset',
				array('HtmlTag',array('tag'=>'div','openOnly'=>true,'class'=> 'deliveryProfileConfigureFormPanel'))
		));
		
		$closeLeftDisplayGroup = $this->getDisplayGroup('advanced');
		$closeLeftDisplayGroup->setDecorators(array(
				'FormElements',
				'Fieldset',
				array('HtmlTag',array('tag'=>'div','closeOnly'=>true))
		));
		
    	// -----------
	}
	
	protected function getAdvancedSettings() {
		$element = new Infra_Form_Html ( 'place_holder1', array ('content' => '<span/>' ) );
		$this->addElement ( $element );
		return array ('place_holder1' );
	}
	
	public function populateFromObject($object, $add_underscore = true)
	{
		parent::populateFromObject($object, $add_underscore);
		$this->getSubForm("tokenizer")->populateFromObject($object->tokenizer);
		$this->getSubForm("recognizer")->populateFromObject($object->recognizer);
	}
	
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
		$object->tokenizer = $this->getSubForm("tokenizer")->getObject($properties["tokenizer"]);
		$object->recognizer = $this->getSubForm("recognizer")->getObject($properties["recognizer"]);
		
		return $object;
	}
}