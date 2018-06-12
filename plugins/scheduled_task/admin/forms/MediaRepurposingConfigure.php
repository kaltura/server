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
	protected $allowed;

	const FILTER_PREFIX = 'FilterParams_';
	const DEFAULT_MAX_TOTAL_COUNT_ALLOWED = 500;
	const MAX_TASKS_ON_MR = 10;
	const MAX_MR_ON_PARTNER = 10;

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

		$this->addTitle('General', 'generalTitle');
		$this->addTextElement('Publisher ID:', 'partnerId', array('required'=> true, 'readonly'=> true));
		$this->addTextElement('MR profile ID:', 'mrId', array('required'=> true, 'readonly'=> true));
		$this->addTextElement('Engine Type:', 'engineType', array('required'=> true, 'readonly'=> true));
		$this->addTextElement('Filter Type:', 'filterTypeStr', array('required'=> true, 'readonly'=> true));
		$this->addTextElement('Media Repurposing Name:', 'media_repurposing_name', array('required' => true));

		$this->addSafetyMechanismSection();

		$this->addFilterSection();

		//add tasks section
		$this->addTaskSection();

		// template who will not be shown on form
		$this->addTaskDataTemplate();

	}

	private function addSafetyMechanismSection()
	{
		$this->addLine("SafetyMechanismLine");
		$this->addTitle('Safety Mechanism');
		$this->addTextElement('Max Entries Allowed in MR:', 'max_entries_allowed', array('required'=>true));
		$this->getElement('max_entries_allowed')->setValue(self::DEFAULT_MAX_TOTAL_COUNT_ALLOWED); //as default
	}

	private function addTaskDataTemplate()
	{
		$options = $this->getElement('TaskTypeChoose')->options;
		foreach ($options as $key => $val) {
			$TasksSubForm = new Form_MediaRepurposingTaskDataSubForm($key);
			$this->addSubForm($TasksSubForm, "MR_tasksDataTemplate_$key");
		}
	}

	private function addTaskSection()
	{
		$this->addLine("taskLine");
		$this->addTitle('Tasks Data');


		$TasksSubForm = new Zend_Form_SubForm(array('DisableLoadDefaultDecorators' => true));
		$TasksSubForm->addDecorator('ViewScript', array(
			'viewScript' => 'tasks-data-sub-form.phtml',
		));
		$this->addSubForm($TasksSubForm, 'MR_tasks');

		$this->addLine("taskLine2");
		$this->addEnumElement('TaskTypeChoose', '', 'Kaltura_Client_ScheduledTask_Enum_ObjectTaskType');
		$this->addElement('button', 'addTaskButton', array(
			'ignore'	=> true,
			'label'		=> 'Add Task',
			'onclick'		=> "addTaskButtonExe()",
		));

		// template who will not be shown on form
		$this->addTaskDataTemplate();
	}

	private function addFilterSection()
	{
		$filter = new $this->filterType;
		//TODO add all field that we want to block the user to filter by in MR
		$ignore = array('relatedObjects', 'orderBy');
		$this->addObjectSection('Filter', $filter , $ignore, self::FILTER_PREFIX);

		$this->addTitle('Instruction Note:', 'instructionNote');
		$this->addComment('relativeTimes', 'For relative times insert -/+ and seconds');
		$this->addComment('absoluteTimes', 'For absolute times YYYY.MM.DD');
		$this->addComment('NAExplain', 'In order to disable field insert: "N/A" ');
		$this->addElement('button', 'expandFilter', array(
			'ignore'	=> true,
			'label'		=> 'Expand Filter',
			'onclick'		=> "changeFilterStatus()",
			'decorators' => array('ViewHelper')
		));

		$this->addAdvanceSearchSection();
		$this->addAdvanceSearchTemplate();
	}

	private function addAdvanceSearchSection()
	{
		$this->addLine("3");
		$this->addTitle('Advance Search:');

		$options = self::$advanceSearchMap;
		$this->addSelectElement("conditionType", $options);

		
		$conditionsSubForm = new Zend_Form_SubForm(array('DisableLoadDefaultDecorators' => true));
		$conditionsSubForm->addDecorator('ViewScript', array(
			'viewScript' => 'conditions-sub-form.phtml',
		));
		$this->addSubForm($conditionsSubForm, 'AdvanceSearch_');
	}
	
	public static $advanceSearchMap = array("Kaltura_Client_Type_SearchMatchCondition" => "match",
												"Kaltura_Client_Type_SearchComparableCondition" => "compare");
	
	private function addAdvanceSearchTemplate()
	{
		foreach(self::$advanceSearchMap as $name => $class) {
			$advanceSearchSubForm = new Form_AdvanceSearchSubForm($name);
			$this->addSubForm($advanceSearchSubForm, "MR_SearchConditionTemplate_" . $class);
		}
	}
	

	public function populateFromObject($object, $add_underscore = true)
	{
		parent::populateFromObject($object, $add_underscore);
		
		/* @var $object KalturaScheduledTaskProfile */
		$this->setDefault('partnerId', $this->newPartnerId);
		$this->setDefault('mrId', $this->mediaRepurposingId);

		$this->setDefault('max_entries_allowed', $object->maxTotalCountAllowed);
		$this->setDefault('media_repurposing_name', $object->name);
		$this->setDefault('engineType', $object->objectFilterEngineType);
		$this->setDefault('filterTypeStr', get_class($object->objectFilter));

		foreach ($object->objectFilter as $key => $value)
			if ($value)
				$this->setDefault(self::FILTER_PREFIX.$key, $value);

		$this->populateAdvanceSearch($object->objectFilter);
		$this->populateTasks($object);
	}

	private function populateAdvanceSearch($filter) {
		$metadataItems = array();
		if (!$filter->advancedSearch)
			return;
		
		$items = $filter->advancedSearch->items;
		array_shift($items); // first element is the MR mechanism
		foreach($items as $item)
			$metadataItems = array_merge($metadataItems, $this->populateMetadataItem($item));

		$this->setDefault('AdvanceSearch',  json_encode($metadataItems));
	}

	private function populateMetadataItem($item)
	{
		$metadataSearch = array();
		$condition = $item->items[0];
		$metadataSearch['conditionType'] = self::$advanceSearchMap[get_class($condition)];
		$metadataSearch['MetadataProfileId'] = $item->metadataProfileId;
		foreach ($condition as $key => $value)
			if ($value)
				$metadataSearch[$key] = $value;

		return array($metadataSearch);
	}

	private function populateTasks($object)
	{
		$tasks = $this->populateTask($object);

		$scheduledtaskPlugin = MediaRepurposingUtils::getPluginByName('Kaltura_Client_ScheduledTask_Plugin');
		$ids = explode(',', $object->description);
		foreach($ids as $stId) {
			if (strlen($stId) == 0)
				continue;
			$arr = explode("[", $stId, 2); // get only the ID without the time
			$result = $scheduledtaskPlugin->scheduledTaskProfile->get($arr[0]);
			$newTaskArr = $this->populateTask($result);

			$taskTimeToNext = substr($arr[1], 0, -1); // set the time in last task
			$tasks[count($tasks) - 1]['taskTimeToNext'] = strval($taskTimeToNext);
			$tasks = array_merge($tasks, $newTaskArr);
		}
		$this->setDefault('TasksData',  json_encode($tasks));

	}

	private function populateTask($object)
	{
		$tasks = array();
		foreach ($object->objectTasks as $currentTask)
		{
			$newTask = array();
			if ($object->partnerId != MediaRepurposingUtils::ADMIN_CONSOLE_PARTNER)
				$newTask['id'] = strval($object->id);
			$newTask['type'] = MediaRepurposingUtils::getDescriptionForType($currentTask->type);
			$newTask['taskTimeToNext'] = 0;
			$ignore = array('id', 'type', 'taskTimeToNext', 'relatedObjects');
			$newTask['taskData'] = MediaRepurposingUtils::getParamToTask($currentTask, $ignore);
			$tasks[] = $newTask;
		}
		return $tasks;
	}

	private static function getSubArrayByPrefix($array, $prefix)
	{
		$prefixLen = strlen($prefix);
		$subArray = array();
		foreach ($array as $key => $value)
			if (strpos($key, $prefix) === 0)
				$subArray[substr($key, $prefixLen)] = $value;
		return $subArray;
	}

	private function buildMetadataSearchArray($advanceSearchData)
	{
		$metadataSearchArray = array();
		foreach (json_decode($advanceSearchData) as $advanceSearch)
			$metadataSearchArray[] = $this->buildMetadataSearchItem($advanceSearch);
		return $metadataSearchArray;
	}

	private function buildMetadataSearchItem($advanceSearch)
	{
		$searchItem = new Kaltura_Client_Metadata_Type_MetadataSearchItem();
		$searchItem->type = Kaltura_Client_Enum_SearchOperatorType::SEARCH_AND;
		$searchItem->metadataProfileId = $advanceSearch->MetadataProfileId;
		$type = $advanceSearch->conditionType;

		unset($advanceSearch->conditionType);
		unset($advanceSearch->MetadataProfileId);

		$class = array_search($type, Form_MediaRepurposingConfigure::$advanceSearchMap);
		/* @var $condition Kaltura_Client_Type_SearchCondition  */
		$condition = new $class();
		foreach($advanceSearch as $key => $value)
			$condition->$key = $value;

		$searchItem->items = array($condition);
		return $searchItem;
	}

	public function getFilterFromData($formData)
	{
		$filterType = $formData['filterTypeStr'];
		if (!$filterType)
			return null;
		$filter = new $filterType();
		$filterFields = self::getSubArrayByPrefix($formData, Form_MediaRepurposingConfigure::FILTER_PREFIX);
		foreach ($filterFields as $key => $value)
			if ($value != 'N/A')
				$filter->$key = $value;

		if ($filterType != 'Kaltura_Client_Reach_Type_EntryVendorTaskFilter')
		{
			$metadataSearchArray = $this->buildMetadataSearchArray($formData['AdvanceSearch']);
			$filter->advancedSearch = MediaRepurposingUtils::createSearchOperator($metadataSearchArray);
		}
		return $filter;
	}


	private function isFilterValid($filter)
	{
		// check if filter not empty
		foreach($filter as $prop)
		{
			if ($prop instanceof Kaltura_Client_Type_SearchOperator)
			{
				if (count($prop->items))
					return true;
			} elseif ($prop)
				return true;
		}
		return false;
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
		if (count(MediaRepurposingUtils::getMrs($partnerId)) > self::MAX_MR_ON_PARTNER)
			return false;
		
		$tasksData = json_decode($data['TasksData']);
		if (count($tasksData) < 1 || count($tasksData) > self::MAX_TASKS_ON_MR)
			return false;
		
		$filter = $this->getFilterFromData($data);
		if (!$this->isFilterValid($filter))
			return false;

		$name = $data['media_repurposing_name'];
		if (strlen($name) < 3)
			return false;

		return true;
	}


}