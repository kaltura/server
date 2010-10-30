<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaProfesionalServicesPartnerFilter extends KalturaPartnerFilter
{
	private $map_between_objects = array
	(
		"commercialUseEqual" => "_eq_commercial_use",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), $this->map_between_objects);
	}

	/**
	 * 
	 * 
	 * @var KalturaCommercialUseType
	 */
	public $commercialUseEqual;
}
