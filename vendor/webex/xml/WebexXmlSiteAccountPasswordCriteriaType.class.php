<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteAccountPasswordCriteriaType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $mixedCase;
	
	/**
	 *
	 * @var integer
	 */
	protected $minLength;
	
	/**
	 *
	 * @var integer
	 */
	protected $minNumeric;
	
	/**
	 *
	 * @var integer
	 */
	protected $minAlpha;
	
	/**
	 *
	 * @var integer
	 */
	protected $minSpecial;
	
	/**
	 *
	 * @var boolean
	 */
	protected $disallow3XRepeatedChar;
	
	/**
	 *
	 * @var boolean
	 */
	protected $disallowWebTextAccounts;
	
	/**
	 *
	 * @var boolean
	 */
	protected $disallowList;
	
	/**
	 *
	 * @var WebexXmlArray<string>
	 */
	protected $disallowValue;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'mixedCase':
				return 'boolean';
	
			case 'minLength':
				return 'integer';
	
			case 'minNumeric':
				return 'integer';
	
			case 'minAlpha':
				return 'integer';
	
			case 'minSpecial':
				return 'integer';
	
			case 'disallow3XRepeatedChar':
				return 'boolean';
	
			case 'disallowWebTextAccounts':
				return 'boolean';
	
			case 'disallowList':
				return 'boolean';
	
			case 'disallowValue':
				return 'WebexXmlArray<string>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'mixedCase',
			'minLength',
			'minNumeric',
			'minAlpha',
			'minSpecial',
			'disallow3XRepeatedChar',
			'disallowWebTextAccounts',
			'disallowList',
			'disallowValue',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'mixedCase',
			'minLength',
			'minNumeric',
			'minAlpha',
			'minSpecial',
			'disallow3XRepeatedChar',
			'disallowWebTextAccounts',
			'disallowList',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'accountPasswordCriteriaType';
	}
	
	/**
	 * @param boolean $mixedCase
	 */
	public function setMixedCase($mixedCase)
	{
		$this->mixedCase = $mixedCase;
	}
	
	/**
	 * @return boolean $mixedCase
	 */
	public function getMixedCase()
	{
		return $this->mixedCase;
	}
	
	/**
	 * @param integer $minLength
	 */
	public function setMinLength($minLength)
	{
		$this->minLength = $minLength;
	}
	
	/**
	 * @return integer $minLength
	 */
	public function getMinLength()
	{
		return $this->minLength;
	}
	
	/**
	 * @param integer $minNumeric
	 */
	public function setMinNumeric($minNumeric)
	{
		$this->minNumeric = $minNumeric;
	}
	
	/**
	 * @return integer $minNumeric
	 */
	public function getMinNumeric()
	{
		return $this->minNumeric;
	}
	
	/**
	 * @param integer $minAlpha
	 */
	public function setMinAlpha($minAlpha)
	{
		$this->minAlpha = $minAlpha;
	}
	
	/**
	 * @return integer $minAlpha
	 */
	public function getMinAlpha()
	{
		return $this->minAlpha;
	}
	
	/**
	 * @param integer $minSpecial
	 */
	public function setMinSpecial($minSpecial)
	{
		$this->minSpecial = $minSpecial;
	}
	
	/**
	 * @return integer $minSpecial
	 */
	public function getMinSpecial()
	{
		return $this->minSpecial;
	}
	
	/**
	 * @param boolean $disallow3XRepeatedChar
	 */
	public function setDisallow3XRepeatedChar($disallow3XRepeatedChar)
	{
		$this->disallow3XRepeatedChar = $disallow3XRepeatedChar;
	}
	
	/**
	 * @return boolean $disallow3XRepeatedChar
	 */
	public function getDisallow3XRepeatedChar()
	{
		return $this->disallow3XRepeatedChar;
	}
	
	/**
	 * @param boolean $disallowWebTextAccounts
	 */
	public function setDisallowWebTextAccounts($disallowWebTextAccounts)
	{
		$this->disallowWebTextAccounts = $disallowWebTextAccounts;
	}
	
	/**
	 * @return boolean $disallowWebTextAccounts
	 */
	public function getDisallowWebTextAccounts()
	{
		return $this->disallowWebTextAccounts;
	}
	
	/**
	 * @param boolean $disallowList
	 */
	public function setDisallowList($disallowList)
	{
		$this->disallowList = $disallowList;
	}
	
	/**
	 * @return boolean $disallowList
	 */
	public function getDisallowList()
	{
		return $this->disallowList;
	}
	
	/**
	 * @param WebexXmlArray<string> $disallowValue
	 */
	public function setDisallowValue($disallowValue)
	{
		if($disallowValue->getType() != 'string')
			throw new WebexXmlException(get_class($this) . "::disallowValue must be of type string");
		
		$this->disallowValue = $disallowValue;
	}
	
	/**
	 * @return WebexXmlArray $disallowValue
	 */
	public function getDisallowValue()
	{
		return $this->disallowValue;
	}
	
}
		
