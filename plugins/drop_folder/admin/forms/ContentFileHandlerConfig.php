<?php
/**
 * @package plugins.dropFolder
 * @subpackage Admin
 */
class Form_ContentFileHandlerConfig extends Form_BaseFileHandlerConfig
{
	/**
	 * {@inheritDoc}
	 * @see Form_BaseFileHandlerConfig::getFileHandlerType()
	 */
	protected function getFileHandlerType()
	{
		return Kaltura_Client_DropFolder_Enum_DropFolderFileHandlerType::CONTENT;
	}

	/**
	 * {@inheritDoc}
	 * @see Form_BaseFileHandlerConfig::applyObjectAttributes()
	 */
	public function applyObjectAttributes(Kaltura_Client_DropFolder_Type_DropFolder &$object)
	{
		if (isset ($object->fileHandlerConfig1['metadataProfileId']))
			$object->metadataProfileId = $object->fileHandlerConfig1['metadataProfileId'];
			
		if (isset ($object->fileHandlerConfig1['categoriesMetadataFieldName']))
			$object->categoriesMetadataFieldName = $object->fileHandlerConfig1['categoriesMetadataFieldName'];
			
		if (isset ($object->fileHandlerConfig1['enforceEntitlement']))
			$object->enforceEntitlement = $object->fileHandlerConfig1['enforceEntitlement'];

		if (isset ($object->fileHandlerConfig1['contentMatchPolicy']))
			$object->fileHandlerConfig->contentMatchPolicy = $object->fileHandlerConfig1['contentMatchPolicy'];

		if (isset ($object->fileHandlerConfig1['slugRegex']))
			$object->fileHandlerConfig->slugRegex = $object->fileHandlerConfig1['slugRegex'];
	}
	
	/**
	 * {@inheritDoc}
	 * @see Form_BaseFileHandlerConfig::init()
	 */
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
		
		parent::init();
	}

	/**
	 * @param Kaltura_Client_ObjectBase $object
	 * @param boolean $add_underscore
	 */
	public function populateFromObject($object, $dropFolderObject, $add_underscore = true)
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
		
		$this->setDefault ('metadataProfileId', $dropFolderObject->metadataProfileId);
		$this->setDefault ('categoriesMetadataFieldName', $dropFolderObject->categoriesMetadataFieldName);	
		$this->setDefault ('enforceEntitlement', $dropFolderObject->enforceEntitlement);
	}
}