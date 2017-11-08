<?php
/**
 * @package plugins.schedule_task
 * @subpackage Admin
 */
class Form_MediaRepurposingTaskDataSubForm extends ConfigureSubForm
{	
	
	private $ignore = array('relatedObjects', 'type', 'stopProcessingOnError');
	private $areaStringFields = array('message', 'footer');
	private $prefix = "";

	private $type;

	public function __construct($type)
	{
		$this->type = $type;
		parent::__construct();
	}



	public function init()
	{
		$this->setAttrib('id', 'frmMediaRepurposingTaskDataSubForm');
		$this->setMethod('post');

		$obj = MediaRepurposingUtils::objectTaskFactory($this->type);
		$this->addObjectProperties($obj, $this->ignore, $this->prefix);
	}

	
	public function populateFromObject($object, $add_underscore = true)
	{
		/* @var $object Kaltura_Client_ScheduledTask_Type_ObjectTask */
		KalturaLog::info(print_r($object,true));

		$reflectClass = new ReflectionClass(get_class($object));
		$properties = $reflectClass	->getProperties(ReflectionProperty::IS_PUBLIC);

		foreach($properties as $property) {
			$propertyName = $property->name;
			if (!in_array($propertyName, $this->ignore)) {
				$tag = $this->prefix. $propertyName;
				$this->setDefault($tag, $object->$propertyName);
			}
		}
	}

	protected function addStringElement($name, $prefix) {
		if (in_array($name,$this->areaStringFields))
			return $this->addStringAreaElement($name, $prefix);

		return parent::addStringElement($name, $prefix);
	}
	
}