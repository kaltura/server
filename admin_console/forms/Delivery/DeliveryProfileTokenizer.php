<?php
/**
 * @package Admin
 * @subpackage Partners
 */
abstract class Form_Delivery_DeliveryProfileTokenizer extends Zend_Form_SubForm
{
	public function init()
	{
 		$this->setLegend("Tokenizer Configuration");
 		$this->setName("tokenizer");
 		
 		$this->addElement('select', 'objectType', array(
 				'label'			=> 'Tokenizer type:',
 				'filters'		=> array('StringTrim'),
 				'multiOptions'  => array(),
 		));
		
		$this->addElement('text', 'key', array(
			'label'			=> 'Key:',
			'filters'		=> array('StringTrim'),
		));		
		
		$this->addElement('text', 'window', array(
				'label'			=> 'Window:',
				'validators'	=> array('Int'),
		));
	}
	
	public function updateTokenizerOptions(array $options) {
		$this->getElement("objectType")->setAttrib('options', $options);
	}
	
	
	public function populateFromObject($tokenizer, $add_underscore = false)
	{
		$this->getElement("objectType")->setValue(get_class($tokenizer));
		
		if(is_null($tokenizer))
			return;
		
		$props = $tokenizer;
		if(is_object($tokenizer))
			$props = get_object_vars($tokenizer);
		
		foreach($props as $prop => $value)
		{
			if($add_underscore)
			{
				$pattern = '/(.)([A-Z])/';
				$replacement = '\1_\2';
				$prop = strtolower(preg_replace($pattern, $replacement, $prop));
			}
			$this->setDefault($prop, $value);
		}
	}
	
	public function getObject($properties) {
		$objectClass = $properties["objectType"];
		if($objectClass == "Null")
			return new Kaltura_Client_Type_UrlTokenizer();
		
		$object = new $objectClass();
		
		foreach($properties as $prop => $value) {
			if($prop == "objectType")
				continue;
			$object->$prop = $value;
		}
		
		return $object;
	}
}