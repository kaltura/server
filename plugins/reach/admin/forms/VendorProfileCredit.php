<?php
/**
 * @package Admin
 * @subpackage Reach
 */
class Form_VendorProfileCredit extends Zend_Form_SubForm
{
	public function init()
	{
 		$this->setLegend("Credit Configuration");
 		$this->setName("vendorProfileCredit");
 		$this->addElement('select', 'objectType', array(
		'label'			=> 'Credit type: (Mandatory) ',
		'filters'		=> array('StringTrim'),
		'multiOptions'  => array(),
	));
		
		$this->addElement('text', 'credit', array(
			'label'			=> 'Credit:',
			'required'      => true,
			'filters'		=> array('StringTrim'),
			'validators'	=> array('Int'),
		));

		$this->addElement('text', 'overageCredit', array(
			'label'			=> 'Overage Credit:',
			'required'      => true,
			'filters'		=> array('StringTrim'),
			'validators' => array(),
		));

		$this->addElement('text', 'fromDate', array(
				'label'			=> 'From Date: (MM.DD.YYYY)',
				'innerType'     => 'DateElement',
				'required'      => true,
				'filters'	=> array('StringTrim'),
				'oninput'	=> 'checkNumValid(this.value)',
				'validators' => array('Int'),
		));
		$this->setDefault('fromDate',  "Enter Date");
	}

	public function updateCreditOptions(array $options) {
		$this->getElement("objectType")->setAttrib('options', $options);
	}
	
	
	public function populateFromObject($creditObject, $add_underscore = false)
	{
		$this->getElement("objectType")->setValue(get_class($creditObject));
		
		if(is_null($creditObject))
			return;
		
		$props = $creditObject;
		if(is_object($creditObject))
			$props = get_object_vars($creditObject);
		
		foreach($props as $prop => $value)
		{
			if ($value)
			{
				if ($add_underscore)
				{
					$pattern = '/(.)([A-Z])/';
					$replacement = '\1_\2';
					$prop = strtolower(preg_replace($pattern, $replacement, $prop));
				}
				$this->setDefault($prop, $value);
			}
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