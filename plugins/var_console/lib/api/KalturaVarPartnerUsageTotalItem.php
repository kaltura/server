<?php
/**
 * @package plugins.varConsole
 * @subpackage api.objects
 */
class KalturaVarPartnerUsageTotalItem extends KalturaVarPartnerUsageItem
{
	/**
	 * Function which parses a report line into an object
	 * @param string $header - comma separated fields names	
	 * @param string $str - comma separated fields
	 * @return KalturaVarPartnerUsageItem
	 */
	public function fromString ( $header , $arr )
	{
		if ( ! $arr ) return null ;
		
		$this->bandwidth 		= ceil(@$arr[0]);
        $this->avgStorage = ceil(@$arr[1]);
		//$item->totalStorage 	= @$arr[15];
		$this->peakStorage =  ceil(@$arr[2]);
		$this->storage 		= ceil(@$arr[3]);
		$this->deletedStorage = ceil(@$arr[4]);
        $this->combinedStorageBandwidth = ceil(@$arr[5]);
		$this->transcodingUsage = ceil(@$arr[6]);
			
		//return $item;
	}
}