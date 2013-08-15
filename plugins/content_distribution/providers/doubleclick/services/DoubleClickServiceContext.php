<?php

class DoubleClickServiceContext extends ContentDistributionServiceContext 
{
	
	public $page = 1;
	public $period;
	public $state;
	public $hash;
	
	public $totalCount;
	public $hasNextPage;
	public $stateLastEntryCreatedAt = null;
	public $stateLastEntryIds = array();
	public $nextPageStateLastEntryCreatedAt;
	public $nextPageStateLastEntryIds;
	
	public function __construct($hash, $page = 1, $period = -1, $state = '', $ignoreScheduling = false) 
	{
		if((!$page || $page < 1))
			$this->page =  $page;
		$this->period = $period;
		$this->state = $state;
		$this->ignoreScheduling = $ignoreScheduling;
		$this->hash = $hash;
	}
}

?>