<?php
/**
 * @package plugins.quiz
 * @subpackage api.objects
 */
class KalturaQuiz extends KalturaObject
{
	/**
	 *
	 * @var int
	 * @readonly
	 */
	public $version;

	/**
	 * Array of key value ui related objects
	 * @var KalturaKeyValueArray
	 */
	public $uiAttributes;

	/**
	 * @var KalturaNullableBoolean
	 */
	public $showResultOnAnswer;

	/**
	 * @var KalturaNullableBoolean
	 */
	public $showCorrectKeyOnAnswer;

	/**
	 * @var KalturaNullableBoolean
	 */
	public $allowAnswerUpdate;

	/**
	 * @var KalturaNullableBoolean
	 */
	public $showCorrectAfterSubmission;


	/**
	 * @var KalturaNullableBoolean
	 */
	public $allowDownload;

	/**
	 * @var KalturaNullableBoolean
	 */
	public $showGradeAfterSubmission;


	private static $mapBetweenObjects = array
	(
		"version",
		"uiAttributes",
		"showResultOnAnswer" => "showCorrect",
		"showCorrectKeyOnAnswer" => "showCorrectKey",
		"allowAnswerUpdate",
		"showCorrectAfterSubmission",
		"allowDownload",
		"showGradeAfterSubmission",
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
