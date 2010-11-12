<?php
interface IKalturaVersion extends IKalturaBase
{
	/**
	 * @return KalturaVersion
	 */
	public static function getVersion();
}