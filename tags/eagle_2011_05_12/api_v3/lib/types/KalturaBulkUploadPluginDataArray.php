<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaBulkUploadPluginDataArray extends KalturaTypedArray
{
	public function __construct()
	{
		return parent::__construct("KalturaBulkUploadPluginData");
	}
	
	public function toValuesArray()
	{
		$ret = array();
		foreach($this as $pluginData)
			$ret[$pluginData->field] = $pluginData->value;
			
		return $ret;
	}
}
?>