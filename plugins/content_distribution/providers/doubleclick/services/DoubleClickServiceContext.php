<?php

class DoubleClickServiceContext extends ContentDistributionServiceContext 
{
	
	public $page = 1;
	public $period;
	public $state;
	public $hash;
	public $version;
	
	public $totalCount;
	public $hasNextPage;
	public $stateLastEntryTimeMark = null;
	public $stateLastEntryIds = array();
	public $nextPageStateLastEntryTimeMark;
	public $nextPageStateLastEntryIds;
	
	public function __construct($hash, $page = 1, $period = -1, $state = '', $ignoreScheduling = false, $version = 2)
	{
		if($page && $page >= 1)
			$this->page =  $page;
		$this->period = $period;
		$this->state = $state;
		$this->ignoreScheduling = $ignoreScheduling;
		$this->hash = $hash;
		$this->version = $version;
	}
}

?>