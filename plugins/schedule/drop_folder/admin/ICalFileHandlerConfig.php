<?php
/**
 * @package plugins.dropFolder
 * @subpackage Admin
 */
class Form_ICalFileHandlerConfig extends Form_BaseFileHandlerConfig
{
	/**
	 * {@inheritDoc}
	 * @see Form_BaseFileHandlerConfig::getFileHandlerType()
	 */
	protected function getFileHandlerType()
	{
		return Kaltura_Client_DropFolder_Enum_DropFolderFileHandlerType::ICAL;
	}

	/**
	 * {@inheritDoc}
	 * @see Form_BaseFileHandlerConfig::applyObjectAttributes()
	 */
	public function applyObjectAttributes(Kaltura_Client_DropFolder_Type_DropFolder &$object)
	{
		KalturaLog::debug('object: ' . print_r($object, true));
		if (isset ($object->fileHandlerConfigscheduleDropFolderICAL['eventsType']))
			$object->fileHandlerConfig->eventsType = $object->fileHandlerConfigscheduleDropFolderICAL['eventsType'];
		KalturaLog::debug('fileHandlerConfig: ' . print_r($object->fileHandlerConfig, true));
	}
	
	/**
	 * {@inheritDoc}
	 * @see Form_BaseFileHandlerConfig::init()
	 */
	public function init()
	{
		$eventsType = new Kaltura_Form_Element_EnumSelect('eventsType', array('enum' => 'Kaltura_Client_Schedule_Enum_ScheduleEventType'));
		$eventsType->setLabel('Default event type:');
		$eventsType->setRequired(true);
		$this->addElement($eventsType);
		
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
		
		/* @var $dropFolderObject Kaltura_Client_DropFolder_Type_DropFolder */
		$fileHandlerConfig = $dropFolderObject->fileHandlerConfig;
		/* @var $fileHandlerConfig Kaltura_Client_ScheduleDropFolder_Type_DropFolderICalBulkUploadFileHandlerConfig */
		$this->setDefault ('eventsType', $fileHandlerConfig->eventsType);
	}
}