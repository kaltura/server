<?php
interface IKalturaDatabaseConfigPlugin extends IKalturaPlugin
{
	/**
	 * @return array
	 */
	public static function getDatabaseConfig();	
}