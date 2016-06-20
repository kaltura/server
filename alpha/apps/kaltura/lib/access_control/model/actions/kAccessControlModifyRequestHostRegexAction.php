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
	
	/**
	 * @var int
	 */
	protected $replacmenServerNodeId;
	
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
		$replacement = "";
		
		if(isset($this->replacement) && strpos($this->replacement, "{hostname}") == false)
		{
			$replacement = $this->replacement;
		}
		elseif(isset($this->replacmenServerNodeId))
		{
			$serverNode = ServerNodePeer::retrieveByPK($this->replacmenServerNodeId);
			if($serverNode)
			{
				if(isset($this->replacement) && strpos($this->replacement, "{hostname}") >= 0)
				{
					$replacement = str_replace("{hostname}", $serverNode->getHostName(), $this->replacement);
				}
				else 
				{
					$replacement = "$1//" . $serverNode->getHostName() . "/" . EdgeServerNode::EDGE_SERVER_DEFAULT_KAPI_APPLICATION_NAME ."/$2";
				}
			}
		}
		
		return $replacement;
	}
	/**
	 * @param string $replacement
	 */
	public function setReplacement($replacement)
	{
		$this->replacement = $replacement;
	}
	
	/**
	 * @return string
	 */
	public function getReplacmenServerNodeId()
	{
		return $this->replacmenServerNodeId;
	}
	/**
	 * @param string $replacement
	 */
	public function setReplacmenServerNodeId($replacmenServerNodeId)
	{
		$this->replacmenServerNodeId = $replacmenServerNodeId;
	}
}
