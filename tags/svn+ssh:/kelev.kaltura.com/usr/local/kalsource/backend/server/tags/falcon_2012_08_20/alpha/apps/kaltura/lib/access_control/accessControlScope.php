<?php
/**
 * @package Core
 * @subpackage model.data
 */
class accessControlScope extends kScope
{
	/**
	 * @var string
	 */
	protected $entryId;
	
	/**
	 * Indicates what contexts should be tested 
	 * No contexts means any context
	 * 
	 * @var array of accessControlContextType
	 */
	protected $contexts = array(accessControlContextType::PLAY);
	
	/**
	 * @param string $v
	 */
	public function setEntryId($v)
	{
		$this->entryId = $v;
	}
	
	/**
	 * @param array $contexts array of accessControlContextType
	 */
	public function setContexts(array $contexts) 
	{
		$this->contexts = $contexts;
	}
	
	/**
	 * @return string
	 */
	public function getEntryId()
	{
		return $this->entryId;
	}	
	
	/**
	 * @return array of accessControlContextType
	 */
	public function getContexts() 
	{
		return $this->contexts;
	}

	/**
	 * @param int $context enum of accessControlContextType
	 * @return bool
	 */
	public function isInContext($context)
	{
		if(!is_array($this->contexts) || !count($this->contexts))
			return true;
			
		return in_array($context, $this->contexts);
	}
}