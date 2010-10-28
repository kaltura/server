<?php
interface IKalturaPermissionsPlugin extends IKalturaPlugin
{
	/**
	 * @param int $partnerId
	 * @return bool
	 */
	public static function isAllowedPartner($partnerId);
}