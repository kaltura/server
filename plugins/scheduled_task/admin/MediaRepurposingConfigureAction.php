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
		$mediaRepurposingName = $this->_getParam('media_repurposing_name');
		$partnerId = $this->_getParam('new_partner_id');
		$mediaRepurposingType = $this->_getParam('new_media_repurposing_type');

		$filterType = $this->_getParam('new_media_repurposing_filter_type');
		$filterType = MediaRepurposingUtils::filterFactory($filterType);


		
		$action->view->formValid = false;

		try
		{
			if ($request->isPost())
			{
				$formData = $request->getPost();
				$mediaRepurposingTypeStr = $formData['type'];
				$mediaRepurposingType = constant("Kaltura_Client_ScheduledTask_Enum_ObjectTaskType::$mediaRepurposingTypeStr");
				$filterType = $formData['filterTypeStr'];
				$mediaRepurposingForm = new Form_MediaRepurposingConfigure($partnerId, $mediaRepurposingType, $filterType);

				$action->view->formValid = $this->processForm($mediaRepurposingForm, $formData, $mediaRepurposingType, $filterType);
			}
			else
			{
				if (!is_null($mediaRepurposingName))
				{
					$mediaRepurposingProfiles = MediaRepurposingUtils::getMrs($partnerId);
					$mr = null;
					foreach ($mediaRepurposingProfiles as $m)
						if ($m->name == $mediaRepurposingName)
							$mr = $m;
					$mediaRepurposingForm = new Form_MediaRepurposingConfigure($partnerId, $mr->taskType, $mr->objectFilter);
					$mediaRepurposingForm->populateFromObject($mr, false);
				}
				else
				{
					$mediaRepurposingForm = new Form_MediaRepurposingConfigure($partnerId, $mediaRepurposingType, $filterType);

					$mediaRepurposingForm->getElement('partnerId')->setValue($partnerId);
					$typeDescription = MediaRepurposingUtils::getDescriptionForType($mediaRepurposingType);
					$mediaRepurposingForm->getElement('type')->setValue($typeDescription);
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

	private static function getSubArrayByPrefix($array, $prefix) {
		$prefixLen = strlen($prefix);
		$subArray = array();
		foreach ($array as $key => $value)
			if (strpos($key, $prefix) === 0)
				$subArray[substr($key, $prefixLen)] = $value;
		return $subArray;
	}
	
	private function processForm($form, $formData, $mediaRepurposingType, $filterType)
	{
		$name = $formData['media_repurposing_name'];

		$time1 = $formData['first_status_change'];
		$notify1 = $formData['first_notification'];
		$time2 = $formData['second_status_change'];
		$notify2 = $formData['second_notification'];

		KalturaLog::info('asdf - 6');
		//KalturaLog::info(print_r($formData,true));
		
		$statusChanges = $time1. ',' .$notify1. ','.$time2. ','.$notify2;
		$statusChangesArray = explode(',', $statusChanges);
		KalturaLog::info(print_r($statusChangesArray,true));


		$prefix = Form_MediaRepurposingConfigure::TASK_OBJECT__PREFIX;
		$extraParamForType = $this->getSubArrayByPrefix($formData, $prefix);

		$filter = new $filterType();
		$prefix = Form_MediaRepurposingConfigure::FILTER_PREFIX;
		$filterFields = $this->getSubArrayByPrefix($formData, $prefix);

		foreach ($filterFields as $key => $value) {
			$filter->$key = $value;
		}

//		KalturaLog::info('qwer');
//		KalturaLog::info("type is : $mediaRepurposingType");
//		KalturaLog::info("filter type  is : $filterType");
//		KalturaLog::info(print_r($filter, true));
//		KalturaLog::info(print_r($extraParamForType, true));
//		return true;


		if ($form->isValid($formData))
		{
			$partnerId = $formData['partnerId'];
			$mr = MediaRepurposingUtils::createNewMr($name, $mediaRepurposingType, $filter, $statusChanges, $extraParamForType, $partnerId);


			$mediaRepurposingProfiles = MediaRepurposingUtils::getMrs($partnerId);
			$mediaRepurposingProfiles[] = $mr;


			MediaRepurposingUtils::updateMrs($partnerId, $mediaRepurposingProfiles);
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

