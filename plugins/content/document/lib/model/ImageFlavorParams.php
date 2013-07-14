<?php
/**
 * @package plugins.document
 * @subpackage model
 */
class ImageFlavorParams extends flavorParams
{
	const CUSTOM_DATA_FIELD_DENSITY_WIDTH = 'densityWidth';
	const CUSTOM_DATA_FIELD_DENSITY_HEIGHT = 'densityHeight';
	const CUSTOM_DATA_FIELD_SIZE_WIDTH = 'sizeWidth';
	const CUSTOM_DATA_FIELD_SIZE_HEIGHT = 'sizeHeight';
	const CUSTOM_DATA_FIELD_DEPTH = 'depth';

	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or
	 * equivalent initialization method).
	 * @see        __construct()
	 */
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->type = DocumentPlugin::getAssetTypeCoreValue(DocumentAssetType::IMAGE);
	}

	public function setDensityWidth($densityWidth) {
		parent::putincustomData(self::CUSTOM_DATA_FIELD_DENSITY_WIDTH, $densityWidth);		
	}

	public function getDensityWidth() {
		return parent::getFromCustomData(self::CUSTOM_DATA_FIELD_DENSITY_WIDTH);		
	}
	
	public function setDensityHeight($densityHeight) {
		parent::putincustomData(self::CUSTOM_DATA_FIELD_DENSITY_HEIGHT, $densityHeight);		
	}

	public function getDensityHeight() {
		return parent::getFromCustomData(self::CUSTOM_DATA_FIELD_DENSITY_HEIGHT);		
	}

	public function setSizeWidth($sizeWidth) {
		parent::putInCustomData(self::CUSTOM_DATA_FIELD_SIZE_WIDTH, $sizeWidth);		
	}

	public function getSizeWidth() {
		return parent::getFromCustomData(self::CUSTOM_DATA_FIELD_SIZE_WIDTH);		
	}

	public function setSizeHeight($sizeHeight) {
		parent::putInCustomData(self::CUSTOM_DATA_FIELD_SIZE_HEIGHT, $sizeHeight);		
	}

	public function getSizeHeight() {
		return parent::getFromCustomData(self::CUSTOM_DATA_FIELD_SIZE_HEIGHT);		
	}

	public function setDepth($depth) {
		parent::putInCustomData(self::CUSTOM_DATA_FIELD_DEPTH, $depth);		
	}

	public function getDepth() {
		return parent::getFromCustomData(self::CUSTOM_DATA_FIELD_DEPTH);		
	}
	
	
}