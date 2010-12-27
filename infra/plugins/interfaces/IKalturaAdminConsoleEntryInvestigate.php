<?php
interface IKalturaAdminConsoleEntryInvestigate extends IKalturaBase
{
	/**
	 * @return array<Kaltura_View_Helper_EntryInvestigatePlugin>
	 */
	public static function getEntryInvestigatePlugins();
}