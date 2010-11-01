<?php
interface IKalturaEventConsumers extends IKalturaBase
{
	/**
	 * @return array
	 */
	public static function getEventConsumers();	
}