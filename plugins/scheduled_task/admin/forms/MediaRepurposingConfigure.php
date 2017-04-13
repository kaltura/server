<?php
/**
 * @package plugins.schedule_task
 * @subpackage Admin
 */
class Form_MediaRepurposingConfigure extends ConfigureForm
{
	protected $newPartnerId;
	protected $filterType;
	protected $mediaRepurposingId;

	const FILTER_PREFIX = 'FilterParams_';
	const TASK_OBJECT__PREFIX = 'TaskObjectParams_';

	const EXTENSION_SUBFORM_NAME = 'extensionSubForm';

	public function __construct($partnerId, $filterType, $mediaRepurposingId = null)
	{
		$this->newPartnerId = $partnerId;
		$this->filterType = $filterType;
		$this->mediaRepurposingId = $mediaRepurposingId;
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

		$this->addElement('text', 'mrId', array(
			'label' 		=> 'MR ID:',
			'required'		=> true,
			'filters' 		=> array('StringTrim'),
			'placement' => 'prepend',
			'readonly'		=> true,
		));

		$this->addElement('text', 'MR_tasksDataTemplate_TaskData', array(
			//'label' 		=> 'TaskData:',
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


		if ($this->filterType)
			$this->addFilterElementElement();


		//add tasks section
		$this->addTaskSection();



		// template who will not be shown on form
		$this->addTaskDataTemplate();

	}



	private function addTaskDataTemplate() {

		$options = $this->getElement('TaskTypeChoose')->options;

		foreach ($options as $key => $val) {
			$TasksSubForm = new Form_MediaRepurposingTaskDataSubForm($key);
			$this->addSubForm($TasksSubForm, "MR_tasksDataTemplate_$key");
		}

	}

	private function addTaskSection() {

		$this->addElement('hidden', 'crossLine2', array(
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'hr', 'class' => 'crossLine')))
		));

		$titleElement = new Zend_Form_Element_Hidden('TasksData');
		$titleElement->setLabel('Tasks Data');
		$titleElement->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElement($titleElement);

		

		$elem = new Kaltura_Form_Element_EnumSelect("TaskTypeChoose", array(
			'enum' => 'Kaltura_Client_ScheduledTask_Enum_ObjectTaskType',
			'excludes' => array(),
		));
		$elem->setLabel("Task Type:");
		$elem->setRequired(true);
		$this->addElement($elem);
		$this->removeClassName("TaskTypeChoose");


		$TasksSubForm = new Zend_Form_SubForm(array('DisableLoadDefaultDecorators' => true));
		$TasksSubForm->addDecorator('ViewScript', array(
			'viewScript' => 'tasks-data-sub-form.phtml',
		));
		$this->addSubForm($TasksSubForm, 'MR_tasks');

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
		
		/* @var $object KalturaScheduledTaskProfile */
		$this->setDefault('partnerId', $this->newPartnerId);
		$this->setDefault('mrId', $this->mediaRepurposingId);


		$this->setDefault('media_repurposing_name', $object->name);
		$this->setDefault('filterTypeStr', get_class($object->objectFilter));

		foreach ($object->objectFilter as $key => $value)
			$this->setDefault(self::FILTER_PREFIX.$key, $value);


		$this->populateTasks($object);


		//$result = MediaRepurposingUtils::getScheduleTaskById($ids[0]);
		//$objectTask = $result->objectTasks[0]; // for the first ST which is the first in the array
		//foreach ($objectTask as $key => $value)
		//	$this->setDefault(self::TASK_OBJECT__PREFIX.$key, $value);

	}

	private function populateTasks($object)
	{
		$tasks = $object->objectTasks;
		$tasks[0]->id = $object->id . '[]'; //in first task no time to last task

		$scheduledtaskPlugin = MediaRepurposingUtils::getPluginByName('Kaltura_Client_ScheduledTask_Plugin', $this->newPartnerId);
		$ids = explode(',', $object->description);
		foreach($ids as $stId) {
			if (strlen($stId) == 0)
				continue;
			$arr = explode("[", $stId, 2); // get only the ID without the time
			$result = $scheduledtaskPlugin->scheduledTaskProfile->get($arr[0]);
			$taskArr = $result->objectTasks;
			$taskArr[0]->id = $stId;
			$tasks = array_merge($tasks, $taskArr);
		}

		KalturaLog::info(print_r($tasks,true));
		$this->setDefault('MR_tasksDataTemplate_TaskData',  json_encode($tasks));

	}



	/**
	 * Validate the form
	 *
	 * @param  array $data
	 * @return boolean
	 */
	public function isValid($data)
	{
		
		$tasksData = json_decode($data['TasksData']);
		if (count($tasksData) < 1)
			return false;

		$name = $data['media_repurposing_name'];
		if (strlen($name) < 3)
			return false;

		return true;
	}


}