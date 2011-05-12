<?php
/**
 * @package plugins.document
 * @subpackage model
 */
class PdfFlavorParamsOutput extends flavorParamsOutput implements PdfFlavorParamsInterface
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
		$this->type = DocumentPlugin::getAssetTypeCoreValue(DocumentAssetType::PDF);
	}
	
	
	// -- Conversion Parameters --
	
	public function setResolution($resolution)
	{
		parent::putInCustomData('resolution', $resolution);
	}
	
	public function getResolution()
	{
		return parent::getFromCustomData('resolution');
	}
	
	
	// -- Paper size --
	
	public function setPaperHeight($height)
	{
		parent::putInCustomData('paperHeight', $height);
	}
	
	public function getPaperHeight()
	{
		return parent::getFromCustomData('paperHeight');
	}
	
	public function setPaperWidth($width)
	{
		parent::putInCustomData('paperWidth', $width);
	}
	
	public function getPaperWidth()
	{
		return parent::getFromCustomData('paperWidth');
	}
	
	/**
	 * @param bool $isReadonly
	 */
	public function setReadonly($isReadonly)
	{
		parent::putInCustomData(PdfFlavorParams::CUSTOM_DATA_FIELD_READONLY, $isReadonly);
	}
	
	/**
	 * @return bool
	 */
	public function getReadonly()
	{
		return parent::getFromCustomData(PdfFlavorParams::CUSTOM_DATA_FIELD_READONLY, null, false);
	}
}