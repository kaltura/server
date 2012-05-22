<?php
/**
 * @package plugins.document
 * @subpackage model
 */
interface ImageFlavorParamsInterface
{
	
	/**
	 * 
	 * @param int $densityWidth
	 */
	public function setDensityWidth($densityWidth);
	
	/**
	 * @return int
	 */
	public function getDensityWidth();
	
	/**
	 * 
	 * @param int $densityHeight
	 */
	public function setDensityHeight($densityHeight);
	
	/**
	 * @return int
	 */
	public function getDensityHeight();
	
		/**
	 * 
	 * @param int $sizeWidth
	 */
	public function setSizeWidth($sizeWidth);
	
	/**
	 * @return int
	 */
	public function getSizeWidth();
	
	/**
	 * 
	 * @param int $sizeHeight
	 */
	public function setSizeHeight($sizeHeight);
	
	/**
	 * @return int
	 */
	public function getSizeHeight();
	
	/**
	 * 
	 * @param int $depth
	 */
	public function setDepth($depth);
	
	/**
	 * @return int
	 */
	public function getDepth();
	
	
}