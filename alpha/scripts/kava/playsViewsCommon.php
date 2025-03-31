<?php

define('CONF_TOPICS_PATH', 'analytics_plays_views_topics_path');
define('PLAYSVIEWS_TOPIC', 'playsViews');
define('MEMC_KEY_LAST_PLAYED_AT', 'plays_views_last_played_at');
define('MEMC_KEY_PREFIX', 'plays_views_');
define('CLUSTER_ID_VAR', 'CLUSTER_ID');
define('BULK_SIZE_VAR', 'BULK_SIZE');
define('MEMCACHE_VAR', 'MEMCACHE');
define('QC_MEMCACHE_VAR', 'QC_MEMCACHE');

function normalizeEntryId($entryId)
{
	// modern entry id - 0_abcd1234
	if (preg_match('/^[0-9]_[0-9a-z]{8}$/D', $entryId))
	{
		return strtolower($entryId);
	}

	// old entry id - a1b2c3d4e5
	if (preg_match('/^[0-9a-z]{10}$/D', $entryId))
	{
		return strtolower($entryId);
	}

	// antique entry id - 12345
	if (preg_match('/^[0-9]{1,6}$/D', $entryId))
	{
		return null;
	}

	// special entry id - _KMCLOGO
	if (preg_match('/^_KMCLOGO\d?$/D', $entryId))
	{
		return $entryId;
	}

	return null;
}

