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

	public function setScore($v){ $this->putInCustomData("score", $v);}
	public function getScore($v){ return $this->getFromCustomData("score");}

}