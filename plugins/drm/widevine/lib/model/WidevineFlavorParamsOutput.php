<?php
/**
 * @package plugins.widevine
 * @subpackage model
 */
class WidevineFlavorParamsOutput extends flavorParamsOutput
{
	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or
	 * equivalent initialization method).
	 * @see        __construct()
	 */
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->type = WidevinePlugin::getAssetTypeCoreValue(WidevineAssetType::WIDEVINE_FLAVOR);
	}
}