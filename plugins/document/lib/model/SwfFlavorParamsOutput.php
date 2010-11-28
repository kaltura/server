<?php

class SwfFlavorParamsOutput extends flavorParamsOutput implements SwfFlavorParamsInterface
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
		$this->type = DocumentAssetType::get()->coreValue(DocumentAssetType::SWF);
	}
	
	
	/**
	 * 
	 * @param string $version
	 */
	public function setFlashVersion($version)
	{
		parent::putInCustomData('flashVersion', $version);
	}
	
	/**
	 * @return string
	 */
	public function getFlashVersion()
	{
		return parent::getFromCustomData('flashVersion');
	}
	
	/**
	 * 
	 * @param int $zoom
	 */
	public function setZoom($zoom)
	{
		parent::putInCustomData('zoom', $zoom);
	}
	
	/**
	 * @return int
	 */
	public function getZoom()
	{
		return parent::getFromCustomData('zoom');
	}
	
	/**
	 * 
	 * @param boolean $zlib
	 */
	public function setZlib($zlib)
	{
		parent::putInCustomData('zlib', $zlib);
	}
	
	/**
	 * @return boolean
	 */
	public function getZlib()
	{
		return parent::getFromCustomData('zlib');
	}
	

	/**
	 * 
	 * @param int $quality
	 */
	public function setJpegQuality($quality)
	{
		parent::putInCustomData('jpegQuality', $quality);
	}
	
	/**
	 * @return int
	 */
	public function getJpegQuality()
	{
		return parent::getFromCustomData('jpegQuality');
	}
	
	/**
	 * 
	 * @param boolean $sameWindow
	 */
	public function setSameWindow($sameWindow)
	{
		parent::putInCustomData('sameWindow', $sameWindow);
	}
	
	/**
	 * @return boolean
	 */
	public function getSameWindow()
	{
		return parent::getFromCustomData('sameWindow');
	}
	
	/**
	 * 
	 * @param boolean $stop
	 */
	public function setInsertStop($stop)
	{
		parent::putInCustomData('insertStop', $stop);
	}
	
	/**
	 * @return boolean
	 */
	public function getInsertStop()
	{
		return parent::getFromCustomData('insertStop');
	}
			
	/**
	 *
	 * @param boolean $useShapes
	 */
	public function setUseShapes($useShapes)
	{
		parent::putInCustomData('useShapes', $useShapes);
	}
	
	/**
	 * @return boolean
	 */
	public function getUseShapes()
	{
		return parent::getFromCustomData('useShapes');
	}
	
	/**
	 * 
	 * @param boolean $storeFonts
	 */
	public function setStoreFonts($storeFonts)
	{
		parent::putInCustomData('storeFonts', $storeFonts);
	}
	
	/**
	 * @return boolean
	 */
	public function getStoreFonts()
	{
		return parent::getFromCustomData('storeFonts');
	}
	
	/**
	 * 
	 * @param boolean $flatten
	 */
	public function setFlatten($flatten)
	{
		parent::putInCustomData('flatten', $flatten);
	}
	
	/**
	 * @return boolean
	 */
	public function getFlatten()
	{
		return parent::getFromCustomData('flatten');
	}
		
	
}