<?php
/**
 * @package plugins.schedule_task
 * @subpackage Admin
 */
class Form_MediaRepurposingConfigure extends ConfigureForm
{
	protected $newPartnerId;
	protected $mediaRepurposingType;
	protected $filterType;

	const FILTER_PREFIX = 'FilterParams_';
	const TASK_OBJECT__PREFIX = 'TaskObjectParams_';

	const EXTENSION_SUBFORM_NAME = 'extensionSubForm';

	public function __construct($partnerId, $type, $filterType)
	{
		$this->newPartnerId = $partnerId;
		$this->mediaRepurposingType = $type;
		$this->filterType = $filterType;
		parent::__construct();
	}


	public function init()
	{
		$this->setAttrib('id', 'frmMediaRepurposingConfigure');
		$this->setMethod('post');

		$titleElement = new Zend_Form_Element_Hidden('generalTitle');
		$titleElement->setLabel('General');
		$titleElement->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElement($titleElement);
		

		$this->addElement('text', 'partnerId', array(
			'label' 		=> 'Related Publisher ID:',
			'required'		=> true,
			'filters' 		=> array('StringTrim'),
			'placement' => 'prepend',
			'readonly'		=> true,
		));

		$this->addElement('text', 'type', array(
			'label' 		=> 'Task:',
			'required'		=> true,
			'filters' 		=> array('StringTrim'),
			'placement' => 'prepend',
			'readonly'		=> true,
		));

		$this->addElement('text', 'filterTypeStr', array(
			'label' 		=> 'Filter Type:',
			'required'		=> true,
			'filters' 		=> array('StringTrim'),
			'placement' => 'prepend',
			'readonly'		=> true,
		));



		$this->addElement('text', 'media_repurposing_name', array(
			'label' 		=> 'Media Repurposing Name:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
			'placement' => 'prepend',
		));

		//add all field according the type
		$this->addTaskElement();


		$this->addStatusChanges();

		if ($this->filterType)
			$this->addFilterElementElement();
	}

	private function addStatusChanges() {

		$this->addElement('hidden', 'crossLine1', array(
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'hr', 'class' => 'crossLine')))
		));

		$titleElement = new Zend_Form_Element_Hidden('alarmAndNotification');
		$titleElement->setLabel('Change status Times');
		$titleElement->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElement($titleElement);

		$this->addElement('text', 'first_status_change', array(
			'label' 		=> 'First change (days)',
			'required'		=> false,
			'filters'		=> array('StringTrim'),
		));
		$this->addElement('checkbox', 'first_notification', array(
			'label'	  => 'Send Notification',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'div', 'class' => 'rememeber')))
		));

		$this->addElement('text', 'second_status_change', array(
			'label' 		=> 'Second change (days)',
			'required'		=> false,
			'filters'		=> array('StringTrim'),
		));
		$this->addElement('checkbox', 'second_notification', array(
			'label'	  => 'Send Notification',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'div', 'class' => 'rememeber')))
		));

		$this->addElement('text', 'third_status_change', array(
			'label' 		=> 'Third change (days)',
			'required'		=> false,
			'filters'		=> array('StringTrim'),
		));
		$this->addElement('checkbox', 'third_notification', array(
			'label'	  => 'Send Notification',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'div', 'class' => 'rememeber')))
		));
	}

	private function addTaskElement() {
		$task = MediaRepurposingUtils::objectTaskFactory($this->mediaRepurposingType);
		$ignore = array('type', 'relatedObjects');
		$this->addObjectSection('Spacial Params For The Specific Task', $task , $ignore, self::TASK_OBJECT__PREFIX);
	}

	private function addFilterElementElement()
	{
		$filter = new $this->filterType;
		//TODO add all field that we want to block the user to filter by in MR
		$ignore = array('relatedObjects');
		$this->addObjectSection('Filter', $filter , $ignore, self::FILTER_PREFIX);
	}


	public function populateFromObject($object, $add_underscore = true)
	{
		parent::populateFromObject($object, $add_underscore);
		
		/* @var $object KalturaMediaRepurposingProfile */
		$this->setDefault('partnerId', $this->newPartnerId);

		$typeDescription = MediaRepurposingUtils::getDescriptionForType($object->taskType);
		$this->setDefault('type', $typeDescription);
		$this->setDefault('media_repurposing_name', $object->name);
		$this->setDefault('filterTypeStr', get_class($object->objectFilter));

		foreach ($object->objectFilter as $key => $value)
			$this->setDefault(self::FILTER_PREFIX.$key, $value);

		$ids = explode(',', $object->scheduleTasksIds);

		$result = MediaRepurposingUtils::getScheduleTaskById($ids[0]);
		$objectTask = $result->objectTasks[0]; // for the first ST which is the first in the array
		foreach ($objectTask as $key => $value)
			$this->setDefault(self::TASK_OBJECT__PREFIX.$key, $value);

	}

	/**
	 * Validate the form
	 *
	 * @param  array $data
	 * @return boolean
	 */
	public function isValid($data)
	{
		$partnerId = $data['partnerId'];
		$name = $data['media_repurposing_name'];
		
		if (strlen($name) < 3)
			return false;
		$mediaRepurposingProfiles = MediaRepurposingUtils::getMrs($partnerId);

		return true;
		return MediaRepurposingUtils::checkForNameInMRs($name, $mediaRepurposingProfiles);
	}


}