<?php
/**
 * @package api
 * @subpackage cache
 */

require_once(dirname(__FILE__) . '/../../../api_v3/lib/KalturaResponseCacher.php');

/**
 * Class LiveStreamStatsActions
 *
 * Package and location is not indicated
 * Should not include any kaltura dependency in this class - to enable it to run in cache only mode
 */
class LiveStreamStatsActions
{
	const ENTRY_ID_ARG = 'entryId';
	const CONF_LIVE_STATS_INTERVAL = 'liveStatsInterval';
	const LIVE_SETTINGS = 'live';
	const DEFAULT_TTL = 20;

	/**
	 * @var LiveStreamStatsCacheHandler
	 */
	protected $liveStreamStatsCacheHandler;

	/**
	 * @var int
	 */
	protected $liveStreamStatsCacheTTL;

	/**
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->liveStreamStatsCacheHandler = null;
		$this->liveStreamStatsCacheTTL = kConf::get(LiveStreamStatsActions::CONF_LIVE_STATS_INTERVAL,LiveStreamStatsActions::LIVE_SETTINGS, LiveStreamStatsActions::DEFAULT_TTL);

		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_PLAYS_VIEWS);
		if (!$cache)
		{
			throw new Exception("Could not initiate cache instance (needed for Live Stream Stats)");
		}

		$this->liveStreamStatsCacheHandler = new LiveStreamStatsCacheHandler($this->liveStreamStatsCacheTTL, $cache);
	}

	protected function responseHandlingGetStats(): void
	{
		KalturaResponseCacher::setExpiry($this->liveStreamStatsCacheTTL);
		KalturaResponseCacher::setHeadersCacheExpiry($this->liveStreamStatsCacheTTL);
	}

	public function doGetLiveStreamStats(string $entryId): int
	{
		$this->responseHandlingGetStats();
		return $this->liveStreamStatsCacheHandler->getNumberOfViewers($entryId);
	}

	public static function getLiveStreamStats_validate($params)
	{
		if (is_null($params) || !array_key_exists(LiveStreamStatsActions::ENTRY_ID_ARG, $params))
		{
			return 'Missing parameter for getLiveStreamStats action';
		}

		return true;
	}

	public static function getLiveStreamStats($params)
	{
		$entryId = $params[LiveStreamStatsActions::ENTRY_ID_ARG];
		$instance = new LiveStreamStatsActions();
		return $instance->doGetLiveStreamStats($entryId);
	}
}

class LiveStreamStatsCacheHandler
{
	const CACHE_KEY_SEPARATOR = '_';
	const LIVE_VIEWERS_PREFIX = 'live_viewers';

	protected $cache;

	protected $cacheTTL;

	public function __construct($cacheTTL, $cache)
	{
		$this->cache = $cache;
		$this->cacheTTL = $cacheTTL;
	}

	public function getNumberOfViewers($entryId)
	{
		$numberOfViewers = 0;
		$liveViewersCacheKey = $this->getLiveViewersCacheKey();
		if ($this->cache)
		{
			$numberOfViewers = $this->cache->get($liveViewersCacheKey . $entryId);
		}

		return $numberOfViewers;
	}

	public function getTTL()
	{
		return $this->cacheTTL;
	}

	/* Cache keys functions */
	protected function getLiveViewersCacheKey(): string
	{
		return LiveStreamStatsCacheHandler::LIVE_VIEWERS_PREFIX . LiveStreamStatsCacheHandler::CACHE_KEY_SEPARATOR;
	}
}