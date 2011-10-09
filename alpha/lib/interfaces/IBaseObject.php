<?php
/**
 * @package Core
 * @subpackage model.interfaces
 */ 
interface IBaseObject
{
	/**
	 * Is the id as used and known by Kaltura
	 * @return string
	 */
	public function getId();
	
	/**
	 * @return int
	 */
	public function getPartnerId();
}