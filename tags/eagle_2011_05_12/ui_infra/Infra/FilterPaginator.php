<?php
class Infra_FilterPaginator implements Zend_Paginator_Adapter_Interface
{
	/**
	 * @var Kaltura_Client_ServiceBase
	 */
	protected $service;
	
	/**
	 * @var string
	 */
	protected $action;
	
	/**
	 * @var int
	 */
	protected $impersonatedPartnerId;
	
	/**
	 * @var array
	 */
	protected $args;
	
	/**
	 * @var int
	 */
	protected $totalCount;
	
	/**
	 * Constructor.
	 *
	 * @param Zend_Db_Select $select The select query
	 */
	public function __construct(Kaltura_Client_ServiceBase $service, $action, $impersonatedPartnerId/* $args*/)
	{
		$this->service = $service;
		$this->action = $action;
		$this->impersonatedPartnerId = $impersonatedPartnerId;
		$this->args = array_slice(func_get_args(), 3);
	}

	/**
	 * Returns an array of items for a page.
	 *
	 * @param  integer $offset Page offset
	 * @param  integer $itemCountPerPage Number of items per page
	 * @return array
	 */
	public function getItems($offset, $itemCountPerPage)
	{
		$objects = $this->callService($offset, $itemCountPerPage);
		if($objects)
			return $objects;
			
		return array();
	}
	
	/**
	 * @return int
	 */
	public function count()
	{
		return $this->totalCount;
	}
	
	/**
	 * 
	 * @param int $offset
	 * @param int $itemCountPerPage
	 */
	protected function callService($offset, $itemCountPerPage)
	{
		$client = Infra_ClientHelper::getClient();
		if ($this->impersonatedPartnerId) {
			Infra_ClientHelper::impersonate($this->impersonatedPartnerId);
		}
		$pager = new Kaltura_Client_Type_FilterPager();
		$pager->pageIndex = (int)($offset / $itemCountPerPage) + 1;
		$pager->pageSize = $itemCountPerPage;
		$action = $this->action;
		$params = $this->args;
		$params[] = $pager;
		$response = call_user_func_array(array($this->service, $action), $params);
		$this->totalCount = $response->totalCount;
		
		if(!$response->objects)
			return array();
			
		return $response->objects;
	}
	
	/**
	 * @return int
	 */
	public function getTotalCount()
	{
		return $this->totalCount;
	}
}