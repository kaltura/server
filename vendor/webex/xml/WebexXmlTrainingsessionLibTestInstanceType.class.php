<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTrainingsessionLibTestInstanceType extends WebexXmlRequestType
{
	/**
	 *
	 * @var long
	 */
	protected $testID;
	
	/**
	 *
	 * @var WebexXmlTrainShareType
	 */
	protected $type;
	
	/**
	 *
	 * @var string
	 */
	protected $title;
	
	/**
	 *
	 * @var string
	 */
	protected $description;
	
	/**
	 *
	 * @var string
	 */
	protected $author;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'testID':
				return 'long';
	
			case 'type':
				return 'WebexXmlTrainShareType';
	
			case 'title':
				return 'string';
	
			case 'description':
				return 'string';
	
			case 'author':
				return 'string';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'testID',
			'type',
			'title',
			'description',
			'author',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'testID',
			'type',
			'title',
			'description',
			'author',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'libTestInstanceType';
	}
	
	/**
	 * @param long $testID
	 */
	public function setTestID($testID)
	{
		$this->testID = $testID;
	}
	
	/**
	 * @return long $testID
	 */
	public function getTestID()
	{
		return $this->testID;
	}
	
	/**
	 * @param WebexXmlTrainShareType $type
	 */
	public function setType(WebexXmlTrainShareType $type)
	{
		$this->type = $type;
	}
	
	/**
	 * @return WebexXmlTrainShareType $type
	 */
	public function getType()
	{
		return $this->type;
	}
	
	/**
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}
	
	/**
	 * @return string $title
	 */
	public function getTitle()
	{
		return $this->title;
	}
	
	/**
	 * @param string $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}
	
	/**
	 * @return string $description
	 */
	public function getDescription()
	{
		return $this->description;
	}
	
	/**
	 * @param string $author
	 */
	public function setAuthor($author)
	{
		$this->author = $author;
	}
	
	/**
	 * @return string $author
	 */
	public function getAuthor()
	{
		return $this->author;
	}
	
}

