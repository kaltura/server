<?php

class myCloudUtils
{
	const CLOUD_STORAGE_MAP = 'cloud_storage';
	const CLOUD_DCS_PARAM = 'cloud_dcs';
	const PREFERRED_CLOUD_STORAGE_ID_PARAM = 'preferred_cloud_storage_id';
	public static function isCloudDc($dcId)
	{
		$cloudDcs = kConf::get(self::CLOUD_DCS_PARAM, self::CLOUD_STORAGE_MAP, array());
		return in_array($dcId, $cloudDcs);
	}

	public static function getCloudPreferredStorage()
	{
		return kConf::get(self::PREFERRED_CLOUD_STORAGE_ID_PARAM, self::CLOUD_STORAGE_MAP, null);

	}
}