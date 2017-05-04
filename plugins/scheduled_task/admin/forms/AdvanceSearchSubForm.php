<?php
/**
 * @package plugins.schedule_task
 * @subpackage Admin
 */
class Form_AdvanceSearchSubForm extends ConfigureSubForm
{
	const Search_PARAMS_PREFIX = 'searchParams_';

	private $ignore = array('relatedObjects', 'type', 'gs');
	private $prefix = "Cond_";

	private $type;

	public function __construct($type)
	{
		$this->type = $type;
		parent::__construct();
	}



	public function init()
	{
		$this->setAttrib('id', 'frmAdvanceSearchSubForm');
		$this->setMethod('post');

		$this->addDecorator('ViewScript', array(
			'viewScript' => 'condition-sub-form.phtml',
		));

		$obj = new $this->type();
		$this->addStringElement("conditionType", $this->prefix);
		$this->addStringElement("MetadataProfileId", $this->prefix);
		$this->addObjectProperties($obj, $this->ignore, $this->prefix);
	}

	
	public function populateFromObject($object, $add_underscore = true)
	{
		/* @var $object Kaltura_Client_Type_SearchCondition */
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
	
}