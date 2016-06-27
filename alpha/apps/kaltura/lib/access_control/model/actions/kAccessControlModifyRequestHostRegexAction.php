<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kAccessControlModifyRequestHostRegexAction extends kRuleAction 
{
	const REPLACMENT_HOST_NAME_TOKEN = "{hostname}";
	
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
		$hasTokenInReplacment = strpos($this->replacement, self::REPLACMENT_HOST_NAME_TOKEN);
		
		if(isset($this->replacement) && $hasTokenInReplacment === false)
		{
			$replacement = $this->replacement;
		}
		elseif(isset($this->replacmenServerNodeId))
		{
			$serverNode = ServerNodePeer::retrieveByPK($this->replacmenServerNodeId);
			if($serverNode)
			{
				if(isset($this->replacement) && $hasTokenInReplacment >= 0)
				{
					$replacement = str_replace(self::REPLACMENT_HOST_NAME_TOKEN, $serverNode->getHostName(), $this->replacement);
				}
				else 
				{
					$replacement = $this->buildDefaultReplacmentString($serverNode->getHostName());
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
	 * @return int
	 */
	public function getReplacmenServerNodeId()
	{
		return $this->replacmenServerNodeId;
	}
	
	/**
	 * @param int $replacmenServerNodeId
	 */
	public function setReplacmenServerNodeId($replacmenServerNodeId)
	{
		$this->replacmenServerNodeId = $replacmenServerNodeId;
	}
	
	private function buildDefaultReplacmentString($hostname)
	{
		return "$1://" . $hostname . "/" . EdgeServerNode::EDGE_SERVER_DEFAULT_KAPI_APPLICATION_NAME ."/$2";
	}
}
