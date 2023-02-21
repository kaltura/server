<?php
/**
 * App Registry Micro Service
 */
class MicroServiceAppRegistry extends MicroServiceBaseService
{
	public function __construct()
	{
		parent::__construct('app-registry','app-registry');
	}

	public function list($partnerId, $filter, $pager = array())
	{
		return $this->serve($partnerId,'list', array('filter' => $filter, 'pager' => $pager));
	}
}