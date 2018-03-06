<?php
/**
 * @package plugins.contentDistribution 
 * @subpackage admin
 */
class Form_DistributionFieldConfig_SubForm extends Zend_Form_SubForm
{
	public function init()
	{
		$this->setDecorators(array(
			'FormElements',
			array('ViewScript', array(
				'viewScript' => 'distribution-field-config-sub-form.phtml',
				'placement' => false
			))
		));
		
		$this->addElement('checkbox', 'override', array(
			'filters' 		=> array('StringTrim'),
			'class' 		=> 'override',
			'decorators'    => array('ViewHelper'),
		));

		// we add fieldName as 2 fields (1 hidden, 1 disabled text) because 
		// disabled fields are not sent by the browser when the form is submitted 
		$this->addElement('text', 'fieldNameForView', array(
			'filters' 		=> array('StringTrim'),
			'disabled'		=> true,
			'class'			=> 'field-name',
			'decorators'    => array('ViewHelper'),
		));
		
		$this->addElement('hidden', 'fieldName', array(
			'filters' 		=> array('StringTrim'),
			'decorators'    => array('ViewHelper'),
		));
	
		$this->addElement('text', 'userFriendlyFieldName', array(
			'filters' 		=> array('StringTrim'),
			'label'			=> 'Name:',
		));	
		
		$this->addElement('text', 'entryMrssXslt', array(
			'filters' 		=> array('StringTrim'),
			'label'			=> 'MRSS XSLT:',
		));
		
		$isRequired = new Kaltura_Form_Element_EnumSelect('isRequired', array('enum' => 'Kaltura_Client_ContentDistribution_Enum_DistributionFieldRequiredStatus'));
		$isRequired->setLabel('Required:');
		$isRequired->setFilters(array('StringTrim'));
		$this->addElements(array($isRequired));

		$type = new Kaltura_Form_Element_EnumSelect('type', array('enum' => 'Kaltura_Client_ContentDistribution_Enum_DistributionFieldType'));
		$type->setLabel('Type:');
		$type->setFilters(array('StringTrim'));
		$this->addElements(array($type));

		$this->addElement('checkbox', 'updateOnChange', array(
			'filters'		=> array('StringTrim'),
			'decorators'	=> array('ViewHelper'),
			'class' 		=> 'update-on-change',
			'label' 		=> 'Trigger Update:', 
		));

		$this->addElement('text', 'updateParamsArrayString', array(
			'filters'		=> array('StringTrim'),
			'class'			=> 'update-param',
			'decorators'	=> array('ViewHelper'),
		));
		
		$this->addElement('checkbox', 'triggerDeleteOnError', array(
			'filters'		=> array('StringTrim'),
			'decorators'	=> array('ViewHelper'),
			'class' 		=> 'trigger-delete-on-error',
			'label' 		=> 'Trigger data deletion on invalidation:', 
		));
	}
	
	public function populateFromObject($object, $add_underscore = true)
	{
		$props = $object;
		if(is_object($object))
			$props = get_object_vars($object);
			
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
		
		$updateParams = isset($props['updateParams']) ? $props['updateParams'] : null;
		if ($updateParams) {
		    $updateParamsString = '';
		    foreach ($updateParams as $updateParam) {
		        $updateParamsString .= $updateParam->value.',';
		    }
		    $updateParamsString = trim($updateParamsString, ',');
			$this->setDefault('updateParamsArrayString', $updateParamsString);		
		}
		
		$requiredStatus = isset($props['isRequired']) ? $props['isRequired'] : null;
		if ($requiredStatus == Kaltura_Client_ContentDistribution_Enum_DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER) {
			$this->getElement('isRequired')->setAttrib('disabled', 'disabled');
		}
		else 
		{
			// required by provider shouldn't be selectable by the user
			$this->getElement('isRequired')->removeMultiOption(Kaltura_Client_ContentDistribution_Enum_DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		}

		$fieldType = isset($props['type']) ? $props['type'] : null;
		$this->setDefault('type', $this->getStringToType($fieldType));
		$this->setDefault('fieldNameForView', $object->fieldName);
		
		if (!$object->isDefault)
			$this->setDefault('override', true);
	}
	
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = new $objectType;
		foreach($properties as $prop => $value)
		{
			if($add_underscore)
			{
				$parts = explode('_', strtolower($prop));
				$prop = '';
				foreach ($parts as $part) 
					$prop .= ucfirst(trim($part));
				$prop[0] = strtolower($prop[0]);
			}

			if ($value !== '' || $include_empty_fields)
			{
				try{
					$object->$prop = $value;
				}catch(Exception $e){}
			}
		}
		
		$updateParamsArray = explode(',',$properties['updateParamsArrayString']);
		$updateParams = array();
		foreach ($updateParamsArray as $updateParam)
		{
			if (!empty($updateParam)) {
				$newString = new Kaltura_Client_Type_String();
				$newString->value = $updateParam;
				$updateParams[] = $newString;
			}
		}
		$object->updateParams = $updateParams;

		$type = isset($properties['type']) ? $properties['type'] : null;
		$object->type = $this->getTypeAsString($type);
		return $object;
	}

	private function getTypeAsString($type)
	{
		switch ($type)
		{
			case Kaltura_Client_ContentDistribution_Enum_DistributionFieldType::INT:
				return "int";
			case Kaltura_Client_ContentDistribution_Enum_DistributionFieldType::STRING:
				return "string";
			case Kaltura_Client_ContentDistribution_Enum_DistributionFieldType::LONG:
				return "long";
			case Kaltura_Client_ContentDistribution_Enum_DistributionFieldType::TIMESTAMP:
				return "timestamp";
			default:
				return "string";
		}
	}

	private function getStringToType($type)
	{
		switch ($type)
		{
			case "int":
				return Kaltura_Client_ContentDistribution_Enum_DistributionFieldType::INT;
			case "string":
				return Kaltura_Client_ContentDistribution_Enum_DistributionFieldType::STRING;
			case "long";
				return Kaltura_Client_ContentDistribution_Enum_DistributionFieldType::LONG;
			case "timestamp":
				return Kaltura_Client_ContentDistribution_Enum_DistributionFieldType::TIMESTAMP;
			default:
				return Kaltura_Client_ContentDistribution_Enum_DistributionFieldType::STRING;
		}
	}


}