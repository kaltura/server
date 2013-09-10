<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTrainingsessionSubmittedTestType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $name;
	
	/**
	 *
	 * @var string
	 */
	protected $email;
	
	/**
	 *
	 * @var string
	 */
	protected $submitDate;
	
	/**
	 *
	 * @var integer
	 */
	protected $score;
	
	/**
	 *
	 * @var string
	 */
	protected $grade;
	
	/**
	 *
	 * @var integer
	 */
	protected $registerID;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'name':
				return 'string';
	
			case 'email':
				return 'string';
	
			case 'submitDate':
				return 'string';
	
			case 'score':
				return 'integer';
	
			case 'grade':
				return 'string';
	
			case 'registerID':
				return 'integer';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'name',
			'email',
			'submitDate',
			'score',
			'grade',
			'registerID',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'name',
			'email',
			'submitDate',
			'score',
			'grade',
			'registerID',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'submittedTestType';
	}
	
	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}
	
	/**
	 * @return string $name
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * @param string $email
	 */
	public function setEmail($email)
	{
		$this->email = $email;
	}
	
	/**
	 * @return string $email
	 */
	public function getEmail()
	{
		return $this->email;
	}
	
	/**
	 * @param string $submitDate
	 */
	public function setSubmitDate($submitDate)
	{
		$this->submitDate = $submitDate;
	}
	
	/**
	 * @return string $submitDate
	 */
	public function getSubmitDate()
	{
		return $this->submitDate;
	}
	
	/**
	 * @param integer $score
	 */
	public function setScore($score)
	{
		$this->score = $score;
	}
	
	/**
	 * @return integer $score
	 */
	public function getScore()
	{
		return $this->score;
	}
	
	/**
	 * @param string $grade
	 */
	public function setGrade($grade)
	{
		$this->grade = $grade;
	}
	
	/**
	 * @return string $grade
	 */
	public function getGrade()
	{
		return $this->grade;
	}
	
	/**
	 * @param integer $registerID
	 */
	public function setRegisterID($registerID)
	{
		$this->registerID = $registerID;
	}
	
	/**
	 * @return integer $registerID
	 */
	public function getRegisterID()
	{
		return $this->registerID;
	}
	
}
		
