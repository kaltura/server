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
		
		$this->bandwidth 		= @$arr[0];
		$this->storage 		= @$arr[1];
		//$item->totalStorage 	= @$arr[15];
		$this->peakStorage =  @$arr[2];
        $this->avgStorage = @$arr[3];
        $this->combinedStorageBandwidth = @$arr[4];
			
		//return $item;
	}
}