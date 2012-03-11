<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kAccessControlPreviewAction extends kAccessControlAction 
{
	/**
	 * @var int
	 */
	protected $limit;
	
	public function __construct() 
	{
		parent::__construct(accessControlActionType::PREVIEW);
	}
	
	/**
	 * @return int
	 */
	public function getLimit() 
	{
		return $this->limit;
	}

	/**
	 * @param int $limit
	 */
	public function setLimit($limit) 
	{
		$this->limit = $limit;
	}
}
