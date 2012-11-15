<?php
/**
 * @package plugins.document
 * @subpackage api.filters
 */
class KalturaDocumentEntryFilter extends KalturaDocumentEntryBaseFilter
{
	private $map_between_objects = array
	(
		"assetParamsIdsMatchOr" => "_matchor_flavor_params_ids",
		"assetParamsIdsMatchAnd" => "_matchand_flavor_params_ids",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), $this->map_between_objects);
	}
}
