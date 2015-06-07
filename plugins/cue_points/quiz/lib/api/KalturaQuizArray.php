<?php
/**
 * @package plugins.quiz
 * @subpackage api.objects
 */
class KalturaQuizArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaQuizArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			$kQuiz = QuizPlugin::getQuizData($obj);
			if ( !is_null($kQuiz) ) {
				$quiz = new KalturaQuiz();
				$quiz->fromObject( $kQuiz, $responseProfile );
				$newArr[] = $quiz;
			}
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaQuiz");
	}
}