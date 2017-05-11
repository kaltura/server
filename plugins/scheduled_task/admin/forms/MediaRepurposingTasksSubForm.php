<?php
/**
 * @package plugins.schedule_task
 * @subpackage Admin
 */
class Form_MediaRepurposingTasksSubForm extends ConfigureSubForm

{	const TASK_OBJECT__PREFIX = 'TaskObjectParams_';
	const EXTENSION_SUBFORM_NAME = 'extensionSubForm';

	private $type;
	
	public function __construct($type)
	{
		$this->type = $type;
		parent::__construct();
	}



	public function init()
	{
		$this->setAttrib('id', 'frmMediaRepurposingTaskSubForm');
		$this->setMethod('post');

		$this->addTitle('Task:', 'taskTitle');

		$this->addDecorator('ViewScript', array(
			'viewScript' => 'task-data-sub-form.phtml',
		));

		
		$this->addElement('text', 'TaskType', array(
			'label' 		=> 'Task Type:',
			'required'		=> true,
			'filters' 		=> array('StringTrim'),
			'placement' => 'prepend',
			'readonly'		=> true,
		));

		$this->addElement('text', 'TaskId', array(
			'label' 		=> 'Task ID:',
			'required'		=> true,
			'filters' 		=> array('StringTrim'),
			'placement' => 'prepend',
			'readonly'		=> true,
		));

		$this->getElement('TaskType')->setValue($this->type);

		$this->addElement('text', "taskTime", array(
			'label' 		=> 'Time to next (in days)',
			'required'		=> false,
			'filters'		=> array('StringTrim'),
		));

	}


	public function populateFromDataObject($object, $time)
	{
		$this->setDefault('taskTime', $time);
		$newFormData = new Form_MediaRepurposingTaskDataSubForm($object->type);
		$newFormData->populateFromObject($object);
		return $newFormData;

	}


	


}