<?php
/**
 * An event consumer implements the event consumer interfaces according to the events it desires to consume. 
 * The consumer interface always requires implementing the method that is called whenever the event is raised. 
 * Implementing the method enables the plugin the react to the event raised in that method.
 * 
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaEventConsumers extends IKalturaBase
{
	/**
	 * Retrieves the event consumers used by the plugin.
	 * 
	 * @return array The list of event consumers
	 */
	public static function getEventConsumers();	
}