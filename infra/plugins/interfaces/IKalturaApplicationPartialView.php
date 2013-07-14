<?php
/**
 * Enable the plugin to add phtml view to existing page
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaApplicationPartialView extends IKalturaBase
{
	/**
	 * @return array<Kaltura_View_Helper_PartialViewPlugin>
	 */
	public static function getApplicationPartialViews($controller, $action);
}