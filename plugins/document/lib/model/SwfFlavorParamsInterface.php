<?php

interface SwfFlavorParamsInterface
{
	
	/**
	 * 
	 * @param string $version
	 */
	public function setFlashVersion($version);
	
	/**
	 * @return string
	 */
	public function getFlashVersion();
	
	/**
	 * 
	 * @param int $zoom
	 */
	public function setZoom($zoom);
	
	/**
	 * @return int
	 */
	public function getZoom();
	
	/**
	 * 
	 * @param boolean $zlib
	 */
	public function setZlib($zlib);
	
	/**
	 * @return boolean
	 */
	public function getZlib();
	
	/**
	 * 
	 * @param int $quality
	 */
	public function setJpegQuality($quality);
	
	/**
	 * @return int
	 */
	public function getJpegQuality();
	
	/**
	 * 
	 * @param boolean $sameWindow
	 */
	public function setSameWindow($sameWindow);
	
	/**
	 * @return boolean
	 */
	public function getSameWindow();
	
	/**
	 * 
	 * @param boolean $stop
	 */
	public function setInsertStop($stop);
	
	/**
	 * @return boolean
	 */
	public function getInsertStop();
	

	/**
	 * 
	 * @param string $swfEntryId
	 */
	public function setPreloader($swfEntryId);
	
	/**
	 * @return string
	 */
	public function getPreloader();
	
	/**
	 *
	 * @param boolean $useShapes
	 */
	public function setUseShapes($useShapes);
	
	/**
	 * @return boolean
	 */
	public function getUseShapes();
	
	/**
	 * 
	 * @param boolean $storeFonts
	 */
	public function setStoreFonts($storeFonts);
	
	/**
	 * @return boolean
	 */
	public function getStoreFonts();
	
	/**
	 * 
	 * @param boolean $flatten
	 */
	public function setFlatten($flatten);
	
	/**
	 * @return boolean
	 */
	public function getFlatten();
		
	
}