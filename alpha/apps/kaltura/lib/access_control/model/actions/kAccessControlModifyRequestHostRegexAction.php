<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kAccessControlModifyRequestHostRegexAction extends kRuleAction 
{
	/**
	 * @var string
	 */
	protected $pattern;
	
	/**
	 * @var string
	 */
	protected $replacement;
	
	public function __construct() 
	{
		parent::__construct(RuleActionType::REQUEST_HOST_REGEX);
	}
	
	/**
	 * @return string
	 */
	public function getPattern() 
	{
		return $this->pattern;
	}
	/**
	 * @param string $pattern
	 */
	public function setPattern($pattern) 
	{
		$this->pattern = $pattern;
	}
	
	/**
	 * @return string
	 */
	public function getReplacement()
	{
		return $this->replacement;
	}
	/**
	 * @param string $replacement
	 */
	public function setReplacement($replacement)
	{
		$this->replacement = $replacement;
	}
}
