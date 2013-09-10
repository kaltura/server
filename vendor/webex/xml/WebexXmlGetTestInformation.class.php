<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');
require_once(__DIR__ . '/WebexXmlArray.class.php');
require_once(__DIR__ . '/WebexXmlTrainSubmittedTestType.class.php');

class WebexXmlGetTestInformation extends WebexXmlObject
{
	/**
	 *
	 * @var string
	 */
	protected $description;
	
	/**
	 *
	 * @var string
	 */
	protected $startDate;
	
	/**
	 *
	 * @var integer
	 */
	protected $timeLimit;
	
	/**
	 *
	 * @var string
	 */
	protected $author;
	
	/**
	 *
	 * @var integer
	 */
	protected $numQuestions;
	
	/**
	 *
	 * @var integer
	 */
	protected $numSubmitted;
	
	/**
	 *
	 * @var integer
	 */
	protected $numStarted;
	
	/**
	 *
	 * @var integer
	 */
	protected $numSubmittedUnscroed;
	
	/**
	 *
	 * @var integer
	 */
	protected $numSubmittedUnscored;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlTrainSubmittedTestType>
	 */
	protected $submittedTest;
	
	/**
	 *
	 * @var integer
	 */
	protected $maxScore;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'description':
				return 'string';
	
			case 'startDate':
				return 'string';
	
			case 'timeLimit':
				return 'integer';
	
			case 'author':
				return 'string';
	
			case 'numQuestions':
				return 'integer';
	
			case 'numSubmitted':
				return 'integer';
	
			case 'numStarted':
				return 'integer';
	
			case 'numSubmittedUnscroed':
				return 'integer';
	
			case 'numSubmittedUnscored':
				return 'integer';
	
			case 'submittedTest':
				return 'WebexXmlArray<WebexXmlTrainSubmittedTestType>';
	
			case 'maxScore':
				return 'integer';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return string $description
	 */
	public function getDescription()
	{
		return $this->description;
	}
	
	/**
	 * @return string $startDate
	 */
	public function getStartDate()
	{
		return $this->startDate;
	}
	
	/**
	 * @return integer $timeLimit
	 */
	public function getTimeLimit()
	{
		return $this->timeLimit;
	}
	
	/**
	 * @return string $author
	 */
	public function getAuthor()
	{
		return $this->author;
	}
	
	/**
	 * @return integer $numQuestions
	 */
	public function getNumQuestions()
	{
		return $this->numQuestions;
	}
	
	/**
	 * @return integer $numSubmitted
	 */
	public function getNumSubmitted()
	{
		return $this->numSubmitted;
	}
	
	/**
	 * @return integer $numStarted
	 */
	public function getNumStarted()
	{
		return $this->numStarted;
	}
	
	/**
	 * @return integer $numSubmittedUnscroed
	 */
	public function getNumSubmittedUnscroed()
	{
		return $this->numSubmittedUnscroed;
	}
	
	/**
	 * @return integer $numSubmittedUnscored
	 */
	public function getNumSubmittedUnscored()
	{
		return $this->numSubmittedUnscored;
	}
	
	/**
	 * @return WebexXmlArray $submittedTest
	 */
	public function getSubmittedTest()
	{
		return $this->submittedTest;
	}
	
	/**
	 * @return integer $maxScore
	 */
	public function getMaxScore()
	{
		return $this->maxScore;
	}
	
}

