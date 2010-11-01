<?php

interface IKalturaBase
{
	/**
	 * Return an instance implementing the interface
	 * @param string $interface
	 * @return IKalturaBase
	 */
	public function getInstance($interface);
}