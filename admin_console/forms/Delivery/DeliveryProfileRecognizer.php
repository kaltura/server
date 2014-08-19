<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_Delivery_DeliveryProfileRecognizer extends Zend_Form_SubForm
{
	public function init()
	{
 		$this->setLegend("Recognizer Configuration");
 		$this->setName("recognizer");
 		
 		$this->addElement('select', 'objectType', array(
 				'label'			=> 'Recognizer type:',
 				'filters'		=> array('StringTrim'),
 				'multiOptions'  => array(),
 		));
		
		$this->addElement('text', 'hosts', array(
			'label'			=> 'Hosts:',
			'filters'		=> array('StringTrim'),
		));		
		
		$this->addElement('text', 'uriPrefix', array(
				'label'			=> 'URI prefix:',
				'filters'		=> array('StringTrim'),
		));
	}
	
	public function updateRecognizerOptions(array $options) {
		$this->getElement("objectType")->setAttrib('options', $options);
	}
	
	
	public function populateFromObject($recognizer, $add_underscore = false)
	{
		$this->getElement("objectType")->setValue(get_class($recognizer));
		
		if(is_null($recognizer))
			return;
		
		$props = $recognizer;
		if(is_object($recognizer))
			$props = get_object_vars($recognizer);
		
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
			return new Kaltura_Client_Type_UrlRecognizer();
		
		$object = new $objectClass();
	
		foreach($properties as $prop => $value) {
			if($prop == "objectType")
				continue;
			$object->$prop = $value;
		}
	
		return $object;
	}
}