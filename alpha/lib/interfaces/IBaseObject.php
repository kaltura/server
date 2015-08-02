<?php
/**
 * @package Core
 * @subpackage model.interfaces
 */ 
interface IBaseObject extends Persistent
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
	
	/**
	 * return instance of object peer class
	 */
	public function getPeer();
}