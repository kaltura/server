<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaContributionPolicyType extends KalturaEnum implements ContributionPolicyType
{
	public static function getDescriptions()
	{
		return array(
			self::ALL => "<i>ALL</i> users can assign entries to a specific category.",
			self::MEMBERS_WITH_CONTRIBUTION_PERMISSION => "Only Category's users with membership type <i>MODERATOR</i> can assign entries to a specific category.",
		);
	}
}
