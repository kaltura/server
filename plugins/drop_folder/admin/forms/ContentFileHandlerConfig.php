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

		$this->addElement('text', 'metadataProfileId', array(
			'label'			=> 'Metadata Profile ID:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'categoriesMetadataFieldName', array(
			'label'			=> 'Categories Metadata Field Name:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('checkbox', 'enforceEntitlement', array(
			'label'	  => 'Enforce Entitlement',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'div', 'class' => 'rememeber')))
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
}