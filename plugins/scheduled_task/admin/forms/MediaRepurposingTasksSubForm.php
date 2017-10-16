<?php
/**
 * @package plugins.schedule_task
 * @subpackage Admin
 */
class Form_MediaRepurposingTasksSubForm extends ConfigureSubForm
{

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

		$this->addTextElement('Task Type:', 'TaskType', array('required'=> true, 'readonly'=> true));
		$this->addTextElement('Task ID:', 'TaskId', array('required'=> true, 'readonly'=> true));
		$this->addTextElement('Time to next Task (in days):', 'taskTime', array('oninput'	=> 'checkNumValid(this.value)'));
		
		$this->getElement('TaskType')->setValue($this->type);
	}


	public function populateFromDataObject($object, $time)
	{
		$this->setDefault('taskTime', $time);
		$newFormData = new Form_MediaRepurposingTaskDataSubForm($object->type);
		$newFormData->populateFromObject($object);
		return $newFormData;
	}


	


}