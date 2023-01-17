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

	public function list($partnerId, $filter, $pager = [])
	{
		return $this->serve($partnerId,'list', ['filter' => $filter, 'pager' => $pager]);
	}
}