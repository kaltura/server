<?php
/**
 * @package Core
 * @subpackage model.data
 */
class userAgentRestriction extends baseRestriction
{	
	const USER_AGENT_REGEX_LIST_DELIMETER = ',';
			
	/**
	 * Allow or restrict
	 * 
	 * @var int
	 */
	protected $type;
	
	/**
	 * Comma seperated list of user agent regular expressions.
	 * @var string
	 */
	private $userAgentRegexList;
	
	/**
	 * @param int $type accessControlListRestrictionType
	 */
	public function setType($type)
	{
		$this->type = $type;
	}
	
	/**
	 * @return int IP_RESTRICTION_TYPE_RESTRICT_LIST / IP_RESTRICTION_TYPE_ALLOW_LIST
	 */
	public function getType()
	{
		return $this->type;	
	}
	
	/**
	 * @param string $regexList
	 */
	public function setUserAgentRegexList($regexList)
	{
		// keep as an array so it won't be done later
		$list = array_map('trim', explode(self::USER_AGENT_REGEX_LIST_DELIMETER, $regexList));
	
		$newList = array();
		foreach($list as $item)
		{
			if (!is_null($item) && strlen($item) > 0)
			{
				$newList[$item] = $item; // to remove duplicates
			}
		}
		
		$this->userAgentRegexList = implode(self::USER_AGENT_REGEX_LIST_DELIMETER, $newList);
	}
	
	/**
	 * @return array
	 */
	public function getUserAgentRegexList()
	{
		return $this->userAgentRegexList;
	}
		
	
	/* (non-PHPdoc)
	 * @see baseRestriction::isValid()
	 */
	public function isValid()
	{
		if ($this->type == accessControlListRestrictionType::ALLOW_LIST) {
			return $this->matchUserAgent();
		}
		else if ($this->type == accessControlListRestrictionType::RESTRICT_LIST) {
			return !$this->matchUserAgent();
		}
		else {
			return false;
		}
	}
	
	/**
	 * Return true if the current request's user agent matches the saved user agent regexs
	 * @return bool
	 */
	protected function matchUserAgent()
	{
		$accessControlScope = $this->getAccessControlScope();
		$userAgent = $accessControlScope->getUserAgent();
		
		$userAgentRegexs = explode(self::USER_AGENT_REGEX_LIST_DELIMETER, $this->getUserAgentRegexList());
		foreach ($userAgentRegexs as $regex)
		{
			if ($regex && preg_match('/'.$regex.'/', $userAgent))
			{
				return true;
			}
		}
		return false;
	}
	
	public function toString()
	{
		$strArray['type'] = $this->getType();
		$strArray['userAgentRegexList'] = $this->getUserAgentRegexList();
		return serialize($strArray);	
	}
	
	public function populateFromString($str)
	{
		$strArray = unserialize($str);
		$this->setType($strArray['type']);
		$this->setUserAgentRegexList($strArray['userAgentRegexList']);
	}	
	
}
