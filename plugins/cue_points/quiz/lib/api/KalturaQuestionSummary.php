<?php
/**
 * @package plugins.quiz
 * @subpackage api.objects
 */
class KalturaQuestionSummary extends KalturaObject
{
	/**
	 *
	 * @var float
	 */
	public $percentage;

	private static $mapBetweenObjects = array
	(
		"version",
		"uiAttributes",
		"showResultOnAnswer",
		"showCorrectKeyOnAnswer",
		"allowAnswerUpdate",
		"showCorrectAfterSubmission",
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($dbObject = null, $propsToSkip = array())
	{
		if (!$dbObject)
		{
			$dbObject = new kQuiz();
		}

		return parent::toObject($dbObject, $propsToSkip);
	}
}