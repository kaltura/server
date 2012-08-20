<?php
/**
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaBase
{
	/**
	 * Return an instance implementing the interface
	 * @param string $interface
	 * @return IKalturaBase
	 */
	public function getInstance($interface);
}