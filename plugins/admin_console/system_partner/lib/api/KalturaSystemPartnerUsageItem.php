<?php
/**
 * @package plugins.systemPartner
 * @subpackage api.objects
 */
class KalturaSystemPartnerUsageItem extends KalturaObject
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
	 * The change in storage consumption (new uploads) during the given date range (in MB)
	 *  
	 * @var float
	 */
	public $storage;
	
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
	public $combinedBandwidthStorage;
	
	/**
	 * Amount of deleted storage in MB
	 * @var float
	 */
	public $deletedStorage;
	
	/**
	 * Amount of transcoding usage in MB
	 * @var float
	 */
	public $transcodingUsage;
	
	
	
	
	/**
	 * Enter description here...
	 * @param string $header - comma separated fields names	
	 * @param string $str - comma separated fields
	 * @return KalturaSystemPartnerUsageItem
	 */
	public static function fromString ( $header , $arr )
	{
		if ( ! $arr ) return null ;
		
		$item = new KalturaSystemPartnerUsageItem();
		
		$item->partnerStatus 	        = @$arr[0];
        $item->partnerId  		        = @$arr[1];
        $item->partnerName 		        = @$arr[2];
        $item->partnerCreatedAt         = @$arr[3];
        $item->partnerPackage	        = @$arr[4];
        $item->views 			        = @$arr[5];
        $item->plays 			        = @$arr[6];
        $item->entriesCount 	        = @$arr[7];
        $item->totalEntriesCount        = @$arr[8];
        $item->videoEntriesCount        = @$arr[9];
        $item->imageEntriesCount        = @$arr[10];
        $item->audioEntriesCount        = @$arr[11];
        $item->mixEntriesCount	        = @$arr[12];
        $item->bandwidth 		        = ceil(@$arr[13]);
        $item->storage 		            = ceil(@$arr[14]);
        $item->deletedStorage 	        = ceil(@$arr[15]);
        //$item->totalStorage 	        = ceil(@$arr[15]);
        $item->peakStorage              = ceil(@$arr[16]);
        $item->avgStorage               = ceil(@$arr[17]);
        $item->combinedBandwidthStorage = ceil(@$arr[18]);
		$item->transcodingUsage = ceil(@$arr[19]);
		
		return $item;
	}
	
	public static function fromPartner(Partner $partner)
	{
		$item = new KalturaSystemPartnerUsageItem();
		if ($partner)
		{
			$item->partnerStatus 	= $partner->getStatus();
			$item->partnerId  		= $partner->getId();
			$item->partnerName 		= $partner->getPartnerName();
			$item->partnerCreatedAt = $partner->getCreatedAt(null);
			$item->partnerPackage	= $partner->getPartnerPackage();
		}
		return $item;
	}
}