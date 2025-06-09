<?php

/**
 * Subclass for representing a row from the 'flavor_asset' table.
 *
 *
 *
 * @package Core
 * @subpackage model
 */
class AudioDescriptionAsset extends flavorAsset
{
	const CUSTOM_DATA_FIELD_ORDER = "order";

	public function getOrder()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_ORDER);
	}
	
	public function setOrder($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_FIELD_ORDER, $v);
	}
}
