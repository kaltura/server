<?php
interface IKalturaEventConsumersPlugin extends IKalturaPlugin
{
	/**
	 * @return array
	 */
	public static function getEventConsumers();	
}