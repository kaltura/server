<?php
/**
 * Quiz data on entry
 *
 * @package plugins.quiz
 * @subpackage model
 *
 */

class kQuiz {

	/**
	 *
	 * @var int
	 */
	protected $version = 1;


	/**
	 * Array of key value ui related objects
	 * @var KalturaKeyValueArray
	 */
	protected $uiAttributes;

	/**
	 * @var boolean
	 */
	protected $showResultOnAnswer;

	/**
	 * @var boolean
	 */
	protected $showCorrectKeyOnAnswer;

	/**
	 * @var boolean
	 */
	protected $allowAnswerUpdate;

	/**
	 * @var boolean
	 */
	protected $showCorrectAfterSubmission;

	/**
	 * @return int
	 */
	public function getVersion()
	{
		return $this->version;
	}

	/**
	 * @param int $version
	 */
	public function setVersion($version)
	{
		$this->version = $version;
	}

	/**
	 * @return KalturaKeyValueArray
	 */
	public function getUiAttributes()
	{
		return $this->uiAttributes;
	}

	/**
	 * @param KalturaKeyValueArray $uiAttributes
	 */
	public function setUiAttributes($uiAttributes)
	{
		$this->uiAttributes = $uiAttributes;
	}

	/**
	 * @return boolean
	 */
	public function getShowResultOnAnswer()
	{
		return $this->showResultOnAnswer;
	}

	/**
	 * @param boolean $showResultOnAnswer
	 */
	public function setShowResultOnAnswer($showResultOnAnswer)
	{
		$this->showResultOnAnswer = $showResultOnAnswer;
	}

	/**
	 * @return boolean
	 */
	public function getShowCorrectKeyOnAnswer()
	{
		return $this->showCorrectKeyOnAnswer;
	}

	/**
	 * @param boolean $showCorrectKeyOnAnswer
	 */
	public function setShowCorrectKeyOnAnswer($showCorrectKeyOnAnswer)
	{
		$this->showCorrectKeyOnAnswer = $showCorrectKeyOnAnswer;
	}

	/**
	 * @return boolean
	 */
	public function getAllowAnswerUpdate()
	{
		return $this->allowAnswerUpdate;
	}

	/**
	 * @param boolean $allowAnswerUpdate
	 */
	public function setAllowAnswerUpdate($allowAnswerUpdate)
	{
		$this->allowAnswerUpdate = $allowAnswerUpdate;
	}

	/**
	 * @return boolean
	 */
	public function getShowCorrectAfterSubmission()
	{
		return $this->showCorrectAfterSubmission;
	}

	/**
	 * @param boolean $showCorrectAfterSubmission
	 */
	public function setShowCorrectAfterSubmission($showCorrectAfterSubmission)
	{
		$this->showCorrectAfterSubmission = $showCorrectAfterSubmission;
	}


}