<?php
/**
 * @package plugins.viewHistory
 * @subpackage model.enum
 */
class ViewHistoryExtendedStatus implements IKalturaPluginEnum, UserEntryExtendedStatus
{
	const PLAYBACK_COMPLETE = "PLAYBACK_COMPLETE";
	const PLAYBACK_STARTED = "PLAYBACK_STARTED";
	const VIEWED = "VIEWED";
	
	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalValues()
	 */
	public static function getAdditionalValues()
	{
		return array(
			"PLAYBACK_COMPLETE" => self::PLAYBACK_COMPLETE,
			"PLAYBACK_STARTED" => self::PLAYBACK_STARTED,
			"VIEWED" => self::VIEWED,
		);
	}

	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalDescriptions()
	 */
	public static function getAdditionalDescriptions()
	{
		return array(
			self::PLAYBACK_COMPLETE => 'Status indicating that user has finished playback of the entry',
			self::PLAYBACK_STARTED => 'Status indicating that user has started playback the entry, but has not finished',
			self::VIEWED => 'Status indicating that user has viewed the entry page without playback',
		);
	}
}