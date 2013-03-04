<?php
/**
 * @package plugins.dropFolder
 * @subpackage Admin
 */
class Form_ContentFileHandlerConfig extends Zend_Form_SubForm
{
	public function init()
	{
		$fileDeletePolicies = new Kaltura_Form_Element_EnumSelect('contentMatchPolicy', array('enum' => 'Kaltura_Client_DropFolder_Enum_DropFolderContentFileHandlerMatchPolicy'));
		$fileDeletePolicies->setLabel('Content Match Policy:');
		$fileDeletePolicies->setRequired(true);
		$this->addElement($fileDeletePolicies);

		$this->addElement('text', 'slugRegex', array(
			'label' 		=> 'Slug Regex:',
		    'value' 		=> '/(?P<referenceId>.+)[.]\w{2,}/',
			'filters'		=> array('StringTrim'),
		));

		$this->setDecorators(array(
	        'FormElements',
	        array('HtmlTag', array('tag' => 'span', 'id' => 'frmContentFileHandlerConfig')),
        ));
	}

	/**
	 * @param Kaltura_Client_ObjectBase $object
	 * @param boolean $add_underscore
	 */
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
	}

	/**
	 * @param string $objectType Kaltura client class name
	 * @param array $properties
	 * @param boolean $add_underscore
	 * @param boolean $include_empty_fields
	 * @return Kaltura_Client_ObjectBase
	 */
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

		return $object;
	}
}