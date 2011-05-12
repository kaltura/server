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
	 * Events aggregation time as Unix timestamp (In seconds) represent one hour
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $aggregatedTime;

	/**
	 * Summary of all entries play time (in seconds)
	 * @var float
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $sumTimeViewed;

	/**
	 * Average of all entries play time (in seconds)
	 * @var float
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $averageTimeViewed;

	/**
	 * Number of all played entries
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countPlays;

	/**
	 * Number of all loaded entry players
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countLoads;

	/**
	 * Number of plays that reached 25%
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countPlays25;

	/**
	 * Number of plays that reached 50%
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countPlays50;

	/**
	 * Number of plays that reached 75%
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countPlays75;

	/**
	 * Number of plays that reached 100%
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countPlays100;

	/**
	 * Number of times that editor opened
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countEdit;

	/**
	 * Number of times that share button clicked
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countShares;

	/**
	 * Number of times that download button clicked
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countDownload;

	/**
	 * Number of times that report abuse button clicked
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countReportAbuse;

	/**
	 * Count of new created media entries
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countMediaEntries;

	/**
	 * Count of new created video entries
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countVideoEntries;

	/**
	 * Count of new created image entries
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countImageEntries;

	/**
	 * Count of new created audio entries
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countAudioEntries;

	/**
	 * Count of new created mix entries
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countMixEntries;

	/**
	 * Count of new created playlists
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
	 * Count of new created users
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countUsers;

	/**
	 * Count of new created widgets
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countWidgets;

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
	 * Count of times that player entered buffering state
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countBufferStart;

	/**
	 * Count of times that player left buffering state
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countBufferEnd;

	/**
	 * Count of times that player fullscreen state opened
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countOpenFullScreen;

	/**
	 * Count of times that player fullscreen state closed
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countCloseFullScreen;

	/**
	 * Count of times that replay button clicked
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countReplay;

	/**
	 * Count of times that seek option used
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countSeek;

	/**
	 * Count of times that upload dialog opened in the editor
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countOpenUpload;

	/**
	 * Count of times that save and publish button clicked in the editor
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countSavePublish;

	/**
	 * Count of times that the editor closed
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countCloseEditor;

	/**
	 * Count of times that pre-bumper entry played
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countPreBumperPlayed;

	/**
	 * Count of times that post-bumper entry played
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countPostBumperPlayed;

	/**
	 * Count of times that bumper entry clicked
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countBumperClicked;

	/**
	 * Count of times that pre-roll ad started
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countPrerollStarted;

	/**
	 * Count of times that mid-roll ad started
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countMidrollStarted;

	/**
	 * Count of times that post-roll ad started
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countPostrollStarted;

	/**
	 * Count of times that overlay ad started
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countOverlayStarted;

	/**
	 * Count of times that pre-roll ad clicked
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countPrerollClicked;

	/**
	 * Count of times that mid-roll ad clicked
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countMidrollClicked;

	/**
	 * Count of times that post-roll ad clicked
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countPostrollClicked;

	/**
	 * Count of times that overlay ad clicked
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countOverlayClicked;

	/**
	 * Count of pre-roll ad plays that reached 25%
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countPreroll25;

	/**
	 * Count of pre-roll ad plays that reached 50%
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countPreroll50;

	/**
	 * Count of pre-roll ad plays that reached 75%
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countPreroll75;

	/**
	 * Count of mid-roll ad plays that reached 25%
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countMidroll25;

	/**
	 * Count of mid-roll ad plays that reached 50%
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countMidroll50;

	/**
	 * Count of mid-roll ad plays that reached 75%
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countMidroll75;

	/**
	 * Count of post-roll ad plays that reached 25%
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countPostroll25;

	/**
	 * Count of post-roll ad plays that reached 50%
	 * @var int
	 * @filter lte,gte,order
	 * @readonly
	 */
	public $countPostroll50;

	/**
	 * Count of post-roll ad plays that reached 75%
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
		'averageTimeViewed',
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
		'countPlaylists' => 'countPlaylist',
		'countBandwidth',
		'countStorage',
		'countUsers',
		'countWidgets',
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