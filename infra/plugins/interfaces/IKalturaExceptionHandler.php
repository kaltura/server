<?php
/**
 * Interface which allows plugin to add its own Exceptions handler
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaExceptionHandler extends IKalturaBase
{

	/**
	 * get Exception map - exceptionClass => array(exceptionClass , callback)
	 * @return array
	 */
	public function getExceptionMap();

}
