<?php

/**
 * @package Core
 * @subpackage model
 */
class QuizUserEntry extends UserEntry{

	/**
	 * @var int
	 */
	protected $score;

	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->setType(QuizUserEntryType::KALTURA_QUIZ_USER_ENTRY);
	}


	public function setScore($v){ $this->putInCustomData("score", $v);}
	public function getScore($v){ return $this->getFromCustomData("score");}

}