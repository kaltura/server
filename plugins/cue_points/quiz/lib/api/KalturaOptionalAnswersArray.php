<?php
/**
 *
 * Associative array of KalturaOptionalAnswer
 *
 * @package plugins.quiz
 * @subpackage api.objects
 */

class KalturaOptionalAnswersArray extends KalturaTypedArray {

	public function __construct()
	{
		return parent::__construct("KalturaOptionalAnswer");
	}

	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaOptionalAnswersArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			$answerObj = new KalturaOptionalAnswer();
			$answerObj->fromObject($obj, $responseProfile);
			$newArr[] = $answerObj;
		}

		return $newArr;
	}
}