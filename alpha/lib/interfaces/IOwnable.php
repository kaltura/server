<?php
/**
 * @package Core
 * @subpackage model.interfaces
 */ 
interface IOwnable extends IBaseObject
{
	/**
	 * @return string
	 */
	public function getPuserId();
	
	/**
	 * @return int
	 */
	public function getKuserId();

	/**
	 * @return boolean
	 */
	public function isEntitledKuserEdit( $kuserId );
}