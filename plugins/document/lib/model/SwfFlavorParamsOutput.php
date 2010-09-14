<?php

class SwfFlavorParamsOutput extends flavorParamsOutput implements SwfFlavorParamsInterface
{
	
	//TODO: which TAGS are valid ??
	
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
		parent::getFromCustomData('flashVersion');
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
		parent::getFromCustomData('zoom');
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
		parent::getFromCustomData('zlib');
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
		parent::getFromCustomData('jpegQuality');
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
		parent::getFromCustomData('sameWindow');
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
		parent::getFromCustomData('insertStop');
	}
		
	/**
	 * 
	 * @param string $swfEntryId
	 */
	public function setPreloader($swfEntryId)
	{
		parent::putInCustomData('preloaderEntryId', $swfEntryId);
	}
	
	/**
	 * @return string
	 */
	public function getPreloader()
	{
		parent::getFromCustomData('preloaderEntryId');
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
		parent::getFromCustomData('useShapes');
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
		parent::getFromCustomData('storeFonts');
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
		parent::getFromCustomData('flatten');
	}
		
	
}