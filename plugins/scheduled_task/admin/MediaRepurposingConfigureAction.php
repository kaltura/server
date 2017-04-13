<?php
/**
 * @package plugins.schedule_task
 * @subpackage Admin
 */
class MediaRepurposingConfigureAction extends KalturaApplicationPlugin
{	
	/**
	 * @return string - absolute file path of the phtml template
	 */
	public function getTemplatePath()
	{
		return realpath(dirname(__FILE__));
	}



	public function doAction(Zend_Controller_Action $action)
	{
		$action->getHelper('layout')->disableLayout();
		$request = $action->getRequest();
		$mediaRepurposingId = $this->_getParam('media_repurposing_id');
		$partnerId = $this->_getParam('new_partner_id');
		$filterTypeEngine = $this->_getParam('new_media_repurposing_filter_type');
		$filterType = MediaRepurposingUtils::filterFactory($filterTypeEngine);
		
		$action->view->formValid = false;


		try
		{
			
			if ($request->isPost())
			{
				$formData = $request->getPost();
				$filterType = $formData['filterTypeStr'];
				$mediaRepurposingForm = new Form_MediaRepurposingConfigure($partnerId, $filterType);
				$action->view->formValid = $this->processForm($mediaRepurposingForm, $formData, $filterTypeEngine, $mediaRepurposingId);
			}
			else
			{
				if (!is_null($mediaRepurposingId))
				{

					$mr = $this->getMrById($partnerId, $mediaRepurposingId);
					$filterEngineType = $mr->objectFilterEngineType;
					$filterType = MediaRepurposingUtils::filterFactory($filterEngineType);

					$mediaRepurposingForm = new Form_MediaRepurposingConfigure($partnerId, $filterType, $mediaRepurposingId);
					$mediaRepurposingForm->populateFromObject($mr, false);
				}
				else
				{
					$mediaRepurposingForm = new Form_MediaRepurposingConfigure($partnerId, $filterType, null);
					$mediaRepurposingForm->getElement('partnerId')->setValue($partnerId);
					$mediaRepurposingForm->getElement('filterTypeStr')->setValue($filterType);
				}
			}
		}
		catch(Exception $e)
		{
		    $action->view->formValid = false;
			KalturaLog::err($e->getMessage() . "\n" . $e->getTraceAsString());
			$action->view->errMessage = $e->getMessage();
		}
		
		$action->view->form = $mediaRepurposingForm;
	}

	private function getMrById($partnerId, $MrId)
	{
		$mediaRepurposingProfiles = MediaRepurposingUtils::getMrs($partnerId);
		foreach ($mediaRepurposingProfiles as $m)
			if ($m->id == $MrId)
				return $m;
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

	private function buildTasksArray($tasksData)
	{
		$taskArray = array();
		$tasksData = json_decode($tasksData);

		foreach ($tasksData as $task) {
			$taskArray[] = $this->buildTask($task);
			$taskArray[] = $task->taskTimeToNext;
		}
		return $taskArray;
	}

	private function buildTask($taskObj)
	{
		$typeInt = constant("Kaltura_Client_ScheduledTask_Enum_ObjectTaskType::". $taskObj->taskType);
		$objectTask = MediaRepurposingUtils::objectTaskFactory($typeInt);
		$objectTask->relatedObjects = $taskObj->taskId; //as the scheduleTask Id of this task
		$objectTask->stopProcessingOnError = 0; //as default value
		MediaRepurposingUtils::addParamToObjectTask($objectTask, $taskObj->taskData);
		return $objectTask;
	}


	
	private function processForm(Form_MediaRepurposingConfigure $form, $formData, $filterTypeEngine, $mediaRepurposingId = null)
	{
		$name = $formData['media_repurposing_name'];

		$filterType = $formData['filterTypeStr'];
		if (!$filterType)
			$filterType = MediaRepurposingUtils::filterFactory($filterTypeEngine);





		$filter = new $filterType();
		$prefix = Form_MediaRepurposingConfigure::FILTER_PREFIX;
		$filterFields = $this->getSubArrayByPrefix($formData, $prefix);

		foreach ($filterFields as $key => $value) {
			$filter->$key = $value;
		}
		
		$taskArray = $this->buildTasksArray($formData['TasksData']);

		KalturaLog::info("asdf - 3");
		KalturaLog::info(print_r($formData, true));
		KalturaLog::info(print_r($taskArray, true));




		if ($form->isValid($formData))
		{
			$partnerId = $formData['partnerId'];
			
			if (!$mediaRepurposingId)
				MediaRepurposingUtils::createNewMr($name, $filterTypeEngine, $filter, $taskArray, $partnerId);
			else
				MediaRepurposingUtils::UpdateMr($mediaRepurposingId, $name, $filterTypeEngine, $filter, $taskArray, $partnerId);

			return true;
		}
		else
		{
			KalturaLog::info('Form was not valid - keep the form open for changing');
			$form->populate($formData);
			return false;
		}
	}

}

