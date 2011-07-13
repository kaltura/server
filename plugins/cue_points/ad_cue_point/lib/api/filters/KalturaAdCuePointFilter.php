<?php
/**
 * @package plugins.adCuePoint
 * @subpackage api.filters
 */
class KalturaAdCuePointFilter extends KalturaAdCuePointBaseFilter
{
	private $map_between_objects = array
	(
		"protocolTypeEqual" => "_eq_sub_type",
		"protocolTypeIn" => "_in_sub_type",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), $this->map_between_objects);
	}

	/**
	 * 
	 * 
	 * @var KalturaAdProtocolType
	 */
	public $protocolTypeEqual;

	/**
	 * 
	 * 
	 * @dynamicType KalturaAdProtocolType
	 * @var string
	 */
	public $protocolTypeIn;
}
