<?php
/**
 * @package plugins.partnerAggregation
 * @subpackage api.objects
 */
class KalturaDwhHourlyPartner extends KalturaObject implements IFilterable 
{
	/**
	 * @var int
	 * @filter eq
	 * @readonly
	 */
	public $partnerId;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $aggregatedTime;

	/**
	 * @var string
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $sumTimeViewed;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countTimeViewed;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countPlays;

	/**
	 * Media loaded (viewed)
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countLoads;

	/**
	 * Play reached 25%
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countPlays25;

	/**
	 * Play reached 50%
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countPlays50;

	/**
	 * Play reached 75%
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countPlays75;

	/**
	 * Play reached 100%
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countPlays100;

	/**
	 * Open Edit
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countEdit;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countShares;

	/**
	 * Open Download
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countDownload;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countReportAbuse;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countMediaEntries;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countVideoEntries;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countImageEntries;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countAudioEntries;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countMixEntries;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countMixNonEmpty;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countPlaylists;

	/**
	 * Is bigint - in KB, aggregated daily in the first hour of every day
	 * @var string
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countBandwidth;

	/**
	 * Is bigint - in MB, aggregated daily in the first hour of every day
	 * @var string
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countStorage;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countUsers;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countWidgets;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $flagActiveSite;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $flagActivePublisher;

	/**
	 * Is bigint - in MB, aggregated daily in the first hour of every day
	 * @var string
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $aggregatedStorage;

	/**
	 * Is bigint - in KB, aggregated daily in the first hour of every day
	 * @var string
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $aggregatedBandwidth;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countBufferStart;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countBufferEnd;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countOpenFullScreen;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countCloseFullScreen;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countReplay;

	/**
	 * Seek
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countSeek;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countOpenUpload;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countSavePublish;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countCloseEditor;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countPreBumperPlayed;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countPostBumperPlayed;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countBumperClicked;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countPrerollStarted;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countMidrollStarted;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countPostrollStarted;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countOverlayStarted;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countPrerollClicked;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countMidrollClicked;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countPostrollClicked;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countOverlayClicked;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countPreroll25;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countPreroll50;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countPreroll75;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countMidroll25;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countMidroll50;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countMidroll75;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countPostroll25;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countPostroll50;

	/**
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countPostroll75;

	/**
	 * Is bigint - in KB, aggregated daily in the first hour of every day
	 * @var string
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countLiveStreamingBandwidth;

	/**
	 * Is bigint - in MB, aggregated daily in the first hour of every day
	 * @var string
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $aggregatedLiveStreamingBandwidth;

	
	private static $map_between_objects = array
	(
		'partnerId',
		'aggregatedTime',
		'sumTimeViewed',
		'countTimeViewed',
		'countPlays',
		'countLoads',
		'countPlays25',
		'countPlays50',
		'countPlays75',
		'countPlays100',
		'countEdit',
		'countShares' => 'countViral',
		'countDownload',
		'countReportAbuse' => 'countReport',
		'countMediaEntries' => 'countMedia',
		'countVideoEntries' => 'countVideo',
		'countImageEntries' => 'countImage',
		'countAudioEntries' => 'countAudio',
		'countMixEntries' => 'countMix',
		'countMixNonEmpty',
		'countPlaylists' => 'countPlaylist',
		'countBandwidth',
		'countStorage',
		'countUsers',
		'countWidgets',
		'flagActiveSite',
		'flagActivePublisher',
		'aggregatedStorage' => 'aggrStorage',
		'aggregatedBandwidth' => 'aggrBandwidth',
		'countBufferStart' => 'countBufStart',
		'countBufferEnd' => 'countBufEnd',
		'countOpenFullScreen',
		'countCloseFullScreen',
		'countReplay',
		'countSeek',
		'countOpenUpload',
		'countSavePublish',
		'countCloseEditor',
		'countPreBumperPlayed',
		'countPostBumperPlayed',
		'countBumperClicked',
		'countPrerollStarted',
		'countMidrollStarted',
		'countPostrollStarted',
		'countOverlayStarted',
		'countPrerollClicked',
		'countMidrollClicked',
		'countPostrollClicked',
		'countOverlayClicked',
		'countPreroll25',
		'countPreroll50',
		'countPreroll75',
		'countMidroll25',
		'countMidroll50',
		'countMidroll75',
		'countPostroll25',
		'countPostroll50',
		'countPostroll75',
		'countLiveStreamingBandwidth' => 'countStreaming',
		'aggregatedLiveStreamingBandwidth' => 'aggrStreaming',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function getExtraFilters()
	{
		return array();
	}
	
	public function getFilterDocs()
	{
		return array();
	}
}