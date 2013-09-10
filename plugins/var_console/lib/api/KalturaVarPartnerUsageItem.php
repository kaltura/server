<?php
/**
 * @package plugins.varConsole
 * @subpackage api.objects
 */
class KalturaVarPartnerUsageItem extends KalturaObject
{
	/**
	 * Partner ID
	 * 
	 * @var int
	 */
	public $partnerId;
	
	/**
	 * Partner name
	 * 
	 * @var string
	 */
	public $partnerName;

	/**
	 * Partner status
	 * 
	 * @var KalturaPartnerStatus
	 */
	public $partnerStatus;
	
	/**
	 * Partner package
	 * 
	 * @var int
	 */
	public $partnerPackage;
	
	/**
	 * Partner creation date (Unix timestamp)
	 * 
	 * @var int
	 */
	public $partnerCreatedAt;
	
	/**
	 * Number of player loads in the specific date range
	 * 
	 * @var int
	 */
	public $views;
	
	/**
	 * Number of plays in the specific date range
	 * 
	 * @var int
	 */
	public $plays;
	
	/**
	 * Number of new entries created during specific date range
	 * 
	 * @var int
	 */
	public $entriesCount;
	
	/**
	 * Total number of entries
	 *  
	 * @var int
	 */
	public $totalEntriesCount;
	
	/**
	 * Number of new video entries created during specific date range
	 * 
	 * @var int
	 */
	public $videoEntriesCount;
	
	/**
	 * Number of new image entries created during specific date range
	 * 
	 * @var int
	 */
	public $imageEntriesCount;
	
	/**
	 * Number of new audio entries created during specific date range
	 * 
	 * @var int
	 */
	public $audioEntriesCount;
	
	/**
	 * Number of new mix entries created during specific date range
	 * 
	 * @var int
	 */
	public $mixEntriesCount;
	
	/**
	 * The total bandwidth usage during the given date range (in MB)
	 * 
	 * @var float
	 */
	public $bandwidth;
	
	/**
	 * The total storage consumption (in MB)
	 *  
	 * @var float
	 */
	public $totalStorage;
	
	/**
	 * The added storage consumption (new uploads) during the given date range (in MB)
	 *  
	 * @var float
	 */
	public $storage;
	
	/**
	 * The deleted storage consumption (new uploads) during the given date range (in MB)
	 * @var float
	 */
	public $deletedStorage;
	
	/**
	 * The peak amount of storage consumption during the given date range for the specific publisher
	 * @var float
	 */
	public $peakStorage;
	
	/**
	 * The average amount of storage consumption during the given date range for the specific publisher
	 * @var float
	 */
	public $avgStorage;
	
	/**
	 * The combined amount of bandwidth and storage consumed during the given date range for the specific publisher
	 * @var float
	 */
	public $combinedStorageBandwidth;
	
	/**
	 * Amount of transcoding usage in MB
	 * @var float
	 */
	public $transcodingUsage;
	
	/**
	 * TGhe date at which the report was taken - Unix Timestamp
	 * @var string 
	 */
	public $dateId;
	
	/**
	 * Function which parses a report line into an object
	 * @param string $header - comma separated fields names	
	 * @param string $str - comma separated fields
	 * @return KalturaVarPartnerUsageItem
	 */
	public function fromString ( $header , $arr )
	{
		if ( ! $arr ) return null ;
		
		//$item = new KalturaVarPartnerUsageItem();
		
		$this->dateId         = @$arr[0];
		$this->partnerStatus 	= @$arr[1];
		$this->partnerName 		= @$arr[2];
		$this->partnerId  		= @$arr[3];
		$this->partnerCreatedAt = @$arr[4];
		$this->bandwidth 		= ceil(@$arr[5]);
        $this->avgStorage = ceil(@$arr[6]);
		$this->peakStorage =  ceil(@$arr[7]);
		$this->storage 		= ceil(@$arr[8]);
		$this->deletedStorage = ceil(@$arr[9]);
        $this->combinedStorageBandwidth = ceil(@$arr[10]);
		$this->transcodingUsage = ceil(@$arr[11]);
		//return $item;
	}
	
	public function fromPartner(Partner $partner)
	{
		//$item = new KalturaVarPartnerUsageItem();
		if ($partner)
		{
			$this->partnerStatus 	= $partner->getStatus();
			$this->partnerId  		= $partner->getId();
			$this->partnerName 		= $partner->getPartnerName();
			$this->partnerCreatedAt = $partner->getCreatedAt(null);
			$this->partnerPackage	= $partner->getPartnerPackage();
		}
		//return $item;
	}
}