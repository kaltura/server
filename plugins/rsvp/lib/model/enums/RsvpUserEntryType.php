<?php
/**
 * @package plugins.rsvp
 * @subpackage model.enum
 */

class RsvpUserEntryType implements IKalturaPluginEnum, UserEntryType
{
	const RSVP = 'RSVP';

	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalValues()
	 */
	public static function getAdditionalValues()
	{
		return array(
			'RSVP' => self::RSVP,
		);
	}

	/* (non-PHPdoc)
	 * @see IKalturaPluginEnum::getAdditionalDescriptions()
	 */
	public static function getAdditionalDescriptions()
	{
		return array(
			self::RSVP => 'RSVP User Entry Type',
		);
	}
}
