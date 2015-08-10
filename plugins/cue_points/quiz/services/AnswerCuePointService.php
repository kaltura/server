<?php

/**
 * Allows actions on AnswerCuePoint with different permissions than regular CuePointService
 *
 * @service answerCuePoint
 * @package plugins.quiz
 * @subpackage api.services
 */

class AnswerCuePointService extends KalturaBaseService{

	/**
	 * Allow adding only KalturaAnswerCuePoint
	 *
	 * @action addAnswer
	 * @param KalturaAnswerCuePoint $answerCuePoint
	 * @return KalturaCuePoint
	 * @throws KalturaAPIException
	 */
	function addAnswerAction(KalturaAnswerCuePoint $answerCuePoint)
	{
		$cuePointService = new CuePointService();
		$cuePointService->initService($this->serviceId,$this->serviceName, $this->actionName);
		$kalturaAnswerCuePoint = $cuePointService->addAction($answerCuePoint);
		$kalturaAnswerCuePoint->isCorrect = null;
		return $kalturaAnswerCuePoint;
	}

	/**
	 * Allow adding only KalturaAnswerCuePoint
	 *
	 * @action updateAnswer
	 * @param string $id
	 * @param KalturaAnswerCuePoint $answerCuePoint
	 * @return KalturaCuePoint
	 * @throws KalturaAPIException
	 */
	function updateAnswerAction($id, KalturaAnswerCuePoint $answerCuePoint)
	{
		$cuePointService = new CuePointService();
		$cuePointService->initService($this->serviceId,$this->serviceName, $this->actionName);
		$kalturaAnswerCuePoint = $cuePointService->updateAction($id, $answerCuePoint);//The validation is called during the toUpdatableObject
		$kalturaAnswerCuePoint->isCorrect = null;
		return $kalturaAnswerCuePoint;
	}


}