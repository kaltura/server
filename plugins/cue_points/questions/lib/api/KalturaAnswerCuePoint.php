<?php
/**
 * @package plugins.questions
 * @subpackage api.objects
 */
class KalturaAnswerCuePoint extends KalturaCuePoint
{

	/**
	 * @var string
	 * @requiresPermission insert,update
	 */
	public $answersUserEntryId;

	/**
	 * @var string
	 * @requiresPermission insert,update
	 */
	public $answerKey;



	public function __construct()
	{
		$this->cuePointType = questionsPlugin::getApiValue(QuestionsCuePointType::QUESTION);
	}

	private static $map_between_objects = array
	(
		"answersUserEntryId",
		"answerKey",
	);

	/* (non-PHPdoc)
	 * @see KalturaCuePoint::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::toInsertableObject()
	 */
	public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
	{
		if(is_null($object_to_fill))
			$object_to_fill = new AdCuePoint();

		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}

	/* (non-PHPdoc)
	 * @see KalturaCuePoint::validateForInsert()
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		parent::validateForInsert($propertiesToSkip);

		$this->validateEndTime();
	}

	/* (non-PHPdoc)
	 * @see KalturaCuePoint::validateForUpdate()
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		$this->validateEndTime($sourceObject);

		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}
}
