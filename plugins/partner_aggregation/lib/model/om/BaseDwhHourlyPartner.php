<?php

/**
 * Base class that represents a row from the 'dwh_hourly_partner' table.
 *
 * 
 *
 * @package plugins.partnerAggregation
 * @subpackage model.om
 */
abstract class BaseDwhHourlyPartner extends BaseObject  {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        DwhHourlyPartnerPeer
	 */
	protected static $peer;

	/**
	 * The value for the partner_id field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $partner_id;

	/**
	 * The value for the date_id field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $date_id;

	/**
	 * The value for the hour_id field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $hour_id;

	/**
	 * The value for the sum_time_viewed field.
	 * @var        string
	 */
	protected $sum_time_viewed;

	/**
	 * The value for the count_time_viewed field.
	 * @var        int
	 */
	protected $count_time_viewed;

	/**
	 * The value for the count_plays field.
	 * @var        int
	 */
	protected $count_plays;

	/**
	 * The value for the count_loads field.
	 * @var        int
	 */
	protected $count_loads;

	/**
	 * The value for the count_plays_25 field.
	 * @var        int
	 */
	protected $count_plays_25;

	/**
	 * The value for the count_plays_50 field.
	 * @var        int
	 */
	protected $count_plays_50;

	/**
	 * The value for the count_plays_75 field.
	 * @var        int
	 */
	protected $count_plays_75;

	/**
	 * The value for the count_plays_100 field.
	 * @var        int
	 */
	protected $count_plays_100;

	/**
	 * The value for the count_edit field.
	 * @var        int
	 */
	protected $count_edit;

	/**
	 * The value for the count_viral field.
	 * @var        int
	 */
	protected $count_viral;

	/**
	 * The value for the count_download field.
	 * @var        int
	 */
	protected $count_download;

	/**
	 * The value for the count_report field.
	 * @var        int
	 */
	protected $count_report;

	/**
	 * The value for the count_media field.
	 * @var        int
	 */
	protected $count_media;

	/**
	 * The value for the count_video field.
	 * @var        int
	 */
	protected $count_video;

	/**
	 * The value for the count_image field.
	 * @var        int
	 */
	protected $count_image;

	/**
	 * The value for the count_audio field.
	 * @var        int
	 */
	protected $count_audio;

	/**
	 * The value for the count_mix field.
	 * @var        int
	 */
	protected $count_mix;

	/**
	 * The value for the count_mix_non_empty field.
	 * @var        int
	 */
	protected $count_mix_non_empty;

	/**
	 * The value for the count_playlist field.
	 * @var        int
	 */
	protected $count_playlist;

	/**
	 * The value for the count_bandwidth field.
	 * @var        string
	 */
	protected $count_bandwidth;

	/**
	 * The value for the count_storage field.
	 * @var        string
	 */
	protected $count_storage;

	/**
	 * The value for the count_users field.
	 * @var        int
	 */
	protected $count_users;

	/**
	 * The value for the count_widgets field.
	 * @var        int
	 */
	protected $count_widgets;

	/**
	 * The value for the flag_active_site field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $flag_active_site;

	/**
	 * The value for the flag_active_publisher field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $flag_active_publisher;

	/**
	 * The value for the aggr_storage field.
	 * @var        string
	 */
	protected $aggr_storage;

	/**
	 * The value for the aggr_bandwidth field.
	 * @var        string
	 */
	protected $aggr_bandwidth;

	/**
	 * The value for the count_buf_start field.
	 * @var        int
	 */
	protected $count_buf_start;

	/**
	 * The value for the count_buf_end field.
	 * @var        int
	 */
	protected $count_buf_end;

	/**
	 * The value for the count_open_full_screen field.
	 * @var        int
	 */
	protected $count_open_full_screen;

	/**
	 * The value for the count_close_full_screen field.
	 * @var        int
	 */
	protected $count_close_full_screen;

	/**
	 * The value for the count_replay field.
	 * @var        int
	 */
	protected $count_replay;

	/**
	 * The value for the count_seek field.
	 * @var        int
	 */
	protected $count_seek;

	/**
	 * The value for the count_open_upload field.
	 * @var        int
	 */
	protected $count_open_upload;

	/**
	 * The value for the count_save_publish field.
	 * @var        int
	 */
	protected $count_save_publish;

	/**
	 * The value for the count_close_editor field.
	 * @var        int
	 */
	protected $count_close_editor;

	/**
	 * The value for the count_pre_bumper_played field.
	 * @var        int
	 */
	protected $count_pre_bumper_played;

	/**
	 * The value for the count_post_bumper_played field.
	 * @var        int
	 */
	protected $count_post_bumper_played;

	/**
	 * The value for the count_bumper_clicked field.
	 * @var        int
	 */
	protected $count_bumper_clicked;

	/**
	 * The value for the count_preroll_started field.
	 * @var        int
	 */
	protected $count_preroll_started;

	/**
	 * The value for the count_midroll_started field.
	 * @var        int
	 */
	protected $count_midroll_started;

	/**
	 * The value for the count_postroll_started field.
	 * @var        int
	 */
	protected $count_postroll_started;

	/**
	 * The value for the count_overlay_started field.
	 * @var        int
	 */
	protected $count_overlay_started;

	/**
	 * The value for the count_preroll_clicked field.
	 * @var        int
	 */
	protected $count_preroll_clicked;

	/**
	 * The value for the count_midroll_clicked field.
	 * @var        int
	 */
	protected $count_midroll_clicked;

	/**
	 * The value for the count_postroll_clicked field.
	 * @var        int
	 */
	protected $count_postroll_clicked;

	/**
	 * The value for the count_overlay_clicked field.
	 * @var        int
	 */
	protected $count_overlay_clicked;

	/**
	 * The value for the count_preroll_25 field.
	 * @var        int
	 */
	protected $count_preroll_25;

	/**
	 * The value for the count_preroll_50 field.
	 * @var        int
	 */
	protected $count_preroll_50;

	/**
	 * The value for the count_preroll_75 field.
	 * @var        int
	 */
	protected $count_preroll_75;

	/**
	 * The value for the count_midroll_25 field.
	 * @var        int
	 */
	protected $count_midroll_25;

	/**
	 * The value for the count_midroll_50 field.
	 * @var        int
	 */
	protected $count_midroll_50;

	/**
	 * The value for the count_midroll_75 field.
	 * @var        int
	 */
	protected $count_midroll_75;

	/**
	 * The value for the count_postroll_25 field.
	 * @var        int
	 */
	protected $count_postroll_25;

	/**
	 * The value for the count_postroll_50 field.
	 * @var        int
	 */
	protected $count_postroll_50;

	/**
	 * The value for the count_postroll_75 field.
	 * @var        int
	 */
	protected $count_postroll_75;

	/**
	 * The value for the count_streaming field.
	 * Note: this column has a database default value of: '0'
	 * @var        string
	 */
	protected $count_streaming;

	/**
	 * The value for the aggr_streaming field.
	 * Note: this column has a database default value of: '0'
	 * @var        string
	 */
	protected $aggr_streaming;

	/**
	 * Flag to prevent endless save loop, if this object is referenced
	 * by another object which falls in this transaction.
	 * @var        boolean
	 */
	protected $alreadyInSave = false;

	/**
	 * Flag to prevent endless validation loop, if this object is referenced
	 * by another object which falls in this transaction.
	 * @var        boolean
	 */
	protected $alreadyInValidation = false;

	/**
	 * Store columns old values before the changes
	 * @var        array
	 */
	protected $oldColumnsValues = array();
	
	/**
	 * @return array
	 */
	public function getColumnsOldValues()
	{
		return $this->oldColumnsValues;
	}

	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or
	 * equivalent initialization method).
	 * @see        __construct()
	 */
	public function applyDefaultValues()
	{
		$this->partner_id = 0;
		$this->date_id = 0;
		$this->hour_id = 0;
		$this->flag_active_site = 0;
		$this->flag_active_publisher = 0;
		$this->count_streaming = '0';
		$this->aggr_streaming = '0';
	}

	/**
	 * Initializes internal state of BaseDwhHourlyPartner object.
	 * @see        applyDefaults()
	 */
	public function __construct()
	{
		parent::__construct();
		$this->applyDefaultValues();
	}

	/**
	 * Get the [partner_id] column value.
	 * 
	 * @return     int
	 */
	public function getPartnerId()
	{
		return $this->partner_id;
	}

	/**
	 * Get the [date_id] column value.
	 * 
	 * @return     int
	 */
	public function getDateId()
	{
		return $this->date_id;
	}

	/**
	 * Get the [hour_id] column value.
	 * 
	 * @return     int
	 */
	public function getHourId()
	{
		return $this->hour_id;
	}

	/**
	 * Get the [sum_time_viewed] column value.
	 * 
	 * @return     string
	 */
	public function getSumTimeViewed()
	{
		return $this->sum_time_viewed;
	}

	/**
	 * Get the [count_time_viewed] column value.
	 * 
	 * @return     int
	 */
	public function getCountTimeViewed()
	{
		return $this->count_time_viewed;
	}

	/**
	 * Get the [count_plays] column value.
	 * 
	 * @return     int
	 */
	public function getCountPlays()
	{
		return $this->count_plays;
	}

	/**
	 * Get the [count_loads] column value.
	 * 
	 * @return     int
	 */
	public function getCountLoads()
	{
		return $this->count_loads;
	}

	/**
	 * Get the [count_plays_25] column value.
	 * 
	 * @return     int
	 */
	public function getCountPlays25()
	{
		return $this->count_plays_25;
	}

	/**
	 * Get the [count_plays_50] column value.
	 * 
	 * @return     int
	 */
	public function getCountPlays50()
	{
		return $this->count_plays_50;
	}

	/**
	 * Get the [count_plays_75] column value.
	 * 
	 * @return     int
	 */
	public function getCountPlays75()
	{
		return $this->count_plays_75;
	}

	/**
	 * Get the [count_plays_100] column value.
	 * 
	 * @return     int
	 */
	public function getCountPlays100()
	{
		return $this->count_plays_100;
	}

	/**
	 * Get the [count_edit] column value.
	 * 
	 * @return     int
	 */
	public function getCountEdit()
	{
		return $this->count_edit;
	}

	/**
	 * Get the [count_viral] column value.
	 * 
	 * @return     int
	 */
	public function getCountViral()
	{
		return $this->count_viral;
	}

	/**
	 * Get the [count_download] column value.
	 * 
	 * @return     int
	 */
	public function getCountDownload()
	{
		return $this->count_download;
	}

	/**
	 * Get the [count_report] column value.
	 * 
	 * @return     int
	 */
	public function getCountReport()
	{
		return $this->count_report;
	}

	/**
	 * Get the [count_media] column value.
	 * 
	 * @return     int
	 */
	public function getCountMedia()
	{
		return $this->count_media;
	}

	/**
	 * Get the [count_video] column value.
	 * 
	 * @return     int
	 */
	public function getCountVideo()
	{
		return $this->count_video;
	}

	/**
	 * Get the [count_image] column value.
	 * 
	 * @return     int
	 */
	public function getCountImage()
	{
		return $this->count_image;
	}

	/**
	 * Get the [count_audio] column value.
	 * 
	 * @return     int
	 */
	public function getCountAudio()
	{
		return $this->count_audio;
	}

	/**
	 * Get the [count_mix] column value.
	 * 
	 * @return     int
	 */
	public function getCountMix()
	{
		return $this->count_mix;
	}

	/**
	 * Get the [count_mix_non_empty] column value.
	 * 
	 * @return     int
	 */
	public function getCountMixNonEmpty()
	{
		return $this->count_mix_non_empty;
	}

	/**
	 * Get the [count_playlist] column value.
	 * 
	 * @return     int
	 */
	public function getCountPlaylist()
	{
		return $this->count_playlist;
	}

	/**
	 * Get the [count_bandwidth] column value.
	 * 
	 * @return     string
	 */
	public function getCountBandwidth()
	{
		return $this->count_bandwidth;
	}

	/**
	 * Get the [count_storage] column value.
	 * 
	 * @return     string
	 */
	public function getCountStorage()
	{
		return $this->count_storage;
	}

	/**
	 * Get the [count_users] column value.
	 * 
	 * @return     int
	 */
	public function getCountUsers()
	{
		return $this->count_users;
	}

	/**
	 * Get the [count_widgets] column value.
	 * 
	 * @return     int
	 */
	public function getCountWidgets()
	{
		return $this->count_widgets;
	}

	/**
	 * Get the [flag_active_site] column value.
	 * 
	 * @return     int
	 */
	public function getFlagActiveSite()
	{
		return $this->flag_active_site;
	}

	/**
	 * Get the [flag_active_publisher] column value.
	 * 
	 * @return     int
	 */
	public function getFlagActivePublisher()
	{
		return $this->flag_active_publisher;
	}

	/**
	 * Get the [aggr_storage] column value.
	 * 
	 * @return     string
	 */
	public function getAggrStorage()
	{
		return $this->aggr_storage;
	}

	/**
	 * Get the [aggr_bandwidth] column value.
	 * 
	 * @return     string
	 */
	public function getAggrBandwidth()
	{
		return $this->aggr_bandwidth;
	}

	/**
	 * Get the [count_buf_start] column value.
	 * 
	 * @return     int
	 */
	public function getCountBufStart()
	{
		return $this->count_buf_start;
	}

	/**
	 * Get the [count_buf_end] column value.
	 * 
	 * @return     int
	 */
	public function getCountBufEnd()
	{
		return $this->count_buf_end;
	}

	/**
	 * Get the [count_open_full_screen] column value.
	 * 
	 * @return     int
	 */
	public function getCountOpenFullScreen()
	{
		return $this->count_open_full_screen;
	}

	/**
	 * Get the [count_close_full_screen] column value.
	 * 
	 * @return     int
	 */
	public function getCountCloseFullScreen()
	{
		return $this->count_close_full_screen;
	}

	/**
	 * Get the [count_replay] column value.
	 * 
	 * @return     int
	 */
	public function getCountReplay()
	{
		return $this->count_replay;
	}

	/**
	 * Get the [count_seek] column value.
	 * 
	 * @return     int
	 */
	public function getCountSeek()
	{
		return $this->count_seek;
	}

	/**
	 * Get the [count_open_upload] column value.
	 * 
	 * @return     int
	 */
	public function getCountOpenUpload()
	{
		return $this->count_open_upload;
	}

	/**
	 * Get the [count_save_publish] column value.
	 * 
	 * @return     int
	 */
	public function getCountSavePublish()
	{
		return $this->count_save_publish;
	}

	/**
	 * Get the [count_close_editor] column value.
	 * 
	 * @return     int
	 */
	public function getCountCloseEditor()
	{
		return $this->count_close_editor;
	}

	/**
	 * Get the [count_pre_bumper_played] column value.
	 * 
	 * @return     int
	 */
	public function getCountPreBumperPlayed()
	{
		return $this->count_pre_bumper_played;
	}

	/**
	 * Get the [count_post_bumper_played] column value.
	 * 
	 * @return     int
	 */
	public function getCountPostBumperPlayed()
	{
		return $this->count_post_bumper_played;
	}

	/**
	 * Get the [count_bumper_clicked] column value.
	 * 
	 * @return     int
	 */
	public function getCountBumperClicked()
	{
		return $this->count_bumper_clicked;
	}

	/**
	 * Get the [count_preroll_started] column value.
	 * 
	 * @return     int
	 */
	public function getCountPrerollStarted()
	{
		return $this->count_preroll_started;
	}

	/**
	 * Get the [count_midroll_started] column value.
	 * 
	 * @return     int
	 */
	public function getCountMidrollStarted()
	{
		return $this->count_midroll_started;
	}

	/**
	 * Get the [count_postroll_started] column value.
	 * 
	 * @return     int
	 */
	public function getCountPostrollStarted()
	{
		return $this->count_postroll_started;
	}

	/**
	 * Get the [count_overlay_started] column value.
	 * 
	 * @return     int
	 */
	public function getCountOverlayStarted()
	{
		return $this->count_overlay_started;
	}

	/**
	 * Get the [count_preroll_clicked] column value.
	 * 
	 * @return     int
	 */
	public function getCountPrerollClicked()
	{
		return $this->count_preroll_clicked;
	}

	/**
	 * Get the [count_midroll_clicked] column value.
	 * 
	 * @return     int
	 */
	public function getCountMidrollClicked()
	{
		return $this->count_midroll_clicked;
	}

	/**
	 * Get the [count_postroll_clicked] column value.
	 * 
	 * @return     int
	 */
	public function getCountPostrollClicked()
	{
		return $this->count_postroll_clicked;
	}

	/**
	 * Get the [count_overlay_clicked] column value.
	 * 
	 * @return     int
	 */
	public function getCountOverlayClicked()
	{
		return $this->count_overlay_clicked;
	}

	/**
	 * Get the [count_preroll_25] column value.
	 * 
	 * @return     int
	 */
	public function getCountPreroll25()
	{
		return $this->count_preroll_25;
	}

	/**
	 * Get the [count_preroll_50] column value.
	 * 
	 * @return     int
	 */
	public function getCountPreroll50()
	{
		return $this->count_preroll_50;
	}

	/**
	 * Get the [count_preroll_75] column value.
	 * 
	 * @return     int
	 */
	public function getCountPreroll75()
	{
		return $this->count_preroll_75;
	}

	/**
	 * Get the [count_midroll_25] column value.
	 * 
	 * @return     int
	 */
	public function getCountMidroll25()
	{
		return $this->count_midroll_25;
	}

	/**
	 * Get the [count_midroll_50] column value.
	 * 
	 * @return     int
	 */
	public function getCountMidroll50()
	{
		return $this->count_midroll_50;
	}

	/**
	 * Get the [count_midroll_75] column value.
	 * 
	 * @return     int
	 */
	public function getCountMidroll75()
	{
		return $this->count_midroll_75;
	}

	/**
	 * Get the [count_postroll_25] column value.
	 * 
	 * @return     int
	 */
	public function getCountPostroll25()
	{
		return $this->count_postroll_25;
	}

	/**
	 * Get the [count_postroll_50] column value.
	 * 
	 * @return     int
	 */
	public function getCountPostroll50()
	{
		return $this->count_postroll_50;
	}

	/**
	 * Get the [count_postroll_75] column value.
	 * 
	 * @return     int
	 */
	public function getCountPostroll75()
	{
		return $this->count_postroll_75;
	}

	/**
	 * Get the [count_streaming] column value.
	 * 
	 * @return     string
	 */
	public function getCountStreaming()
	{
		return $this->count_streaming;
	}

	/**
	 * Get the [aggr_streaming] column value.
	 * 
	 * @return     string
	 */
	public function getAggrStreaming()
	{
		return $this->aggr_streaming;
	}

	/**
	 * Set the value of [partner_id] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setPartnerId($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::PARTNER_ID]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::PARTNER_ID] = $this->partner_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->partner_id !== $v || $this->isNew()) {
			$this->partner_id = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::PARTNER_ID;
		}

		return $this;
	} // setPartnerId()

	/**
	 * Set the value of [date_id] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setDateId($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::DATE_ID]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::DATE_ID] = $this->date_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->date_id !== $v || $this->isNew()) {
			$this->date_id = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::DATE_ID;
		}

		return $this;
	} // setDateId()

	/**
	 * Set the value of [hour_id] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setHourId($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::HOUR_ID]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::HOUR_ID] = $this->hour_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->hour_id !== $v || $this->isNew()) {
			$this->hour_id = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::HOUR_ID;
		}

		return $this;
	} // setHourId()

	/**
	 * Set the value of [sum_time_viewed] column.
	 * 
	 * @param      string $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setSumTimeViewed($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::SUM_TIME_VIEWED]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::SUM_TIME_VIEWED] = $this->sum_time_viewed;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->sum_time_viewed !== $v) {
			$this->sum_time_viewed = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::SUM_TIME_VIEWED;
		}

		return $this;
	} // setSumTimeViewed()

	/**
	 * Set the value of [count_time_viewed] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountTimeViewed($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_TIME_VIEWED]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_TIME_VIEWED] = $this->count_time_viewed;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_time_viewed !== $v) {
			$this->count_time_viewed = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_TIME_VIEWED;
		}

		return $this;
	} // setCountTimeViewed()

	/**
	 * Set the value of [count_plays] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountPlays($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_PLAYS]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_PLAYS] = $this->count_plays;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_plays !== $v) {
			$this->count_plays = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_PLAYS;
		}

		return $this;
	} // setCountPlays()

	/**
	 * Set the value of [count_loads] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountLoads($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_LOADS]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_LOADS] = $this->count_loads;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_loads !== $v) {
			$this->count_loads = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_LOADS;
		}

		return $this;
	} // setCountLoads()

	/**
	 * Set the value of [count_plays_25] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountPlays25($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_PLAYS_25]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_PLAYS_25] = $this->count_plays_25;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_plays_25 !== $v) {
			$this->count_plays_25 = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_PLAYS_25;
		}

		return $this;
	} // setCountPlays25()

	/**
	 * Set the value of [count_plays_50] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountPlays50($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_PLAYS_50]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_PLAYS_50] = $this->count_plays_50;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_plays_50 !== $v) {
			$this->count_plays_50 = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_PLAYS_50;
		}

		return $this;
	} // setCountPlays50()

	/**
	 * Set the value of [count_plays_75] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountPlays75($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_PLAYS_75]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_PLAYS_75] = $this->count_plays_75;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_plays_75 !== $v) {
			$this->count_plays_75 = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_PLAYS_75;
		}

		return $this;
	} // setCountPlays75()

	/**
	 * Set the value of [count_plays_100] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountPlays100($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_PLAYS_100]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_PLAYS_100] = $this->count_plays_100;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_plays_100 !== $v) {
			$this->count_plays_100 = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_PLAYS_100;
		}

		return $this;
	} // setCountPlays100()

	/**
	 * Set the value of [count_edit] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountEdit($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_EDIT]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_EDIT] = $this->count_edit;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_edit !== $v) {
			$this->count_edit = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_EDIT;
		}

		return $this;
	} // setCountEdit()

	/**
	 * Set the value of [count_viral] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountViral($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_VIRAL]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_VIRAL] = $this->count_viral;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_viral !== $v) {
			$this->count_viral = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_VIRAL;
		}

		return $this;
	} // setCountViral()

	/**
	 * Set the value of [count_download] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountDownload($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_DOWNLOAD]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_DOWNLOAD] = $this->count_download;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_download !== $v) {
			$this->count_download = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_DOWNLOAD;
		}

		return $this;
	} // setCountDownload()

	/**
	 * Set the value of [count_report] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountReport($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_REPORT]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_REPORT] = $this->count_report;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_report !== $v) {
			$this->count_report = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_REPORT;
		}

		return $this;
	} // setCountReport()

	/**
	 * Set the value of [count_media] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountMedia($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_MEDIA]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_MEDIA] = $this->count_media;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_media !== $v) {
			$this->count_media = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_MEDIA;
		}

		return $this;
	} // setCountMedia()

	/**
	 * Set the value of [count_video] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountVideo($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_VIDEO]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_VIDEO] = $this->count_video;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_video !== $v) {
			$this->count_video = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_VIDEO;
		}

		return $this;
	} // setCountVideo()

	/**
	 * Set the value of [count_image] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountImage($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_IMAGE]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_IMAGE] = $this->count_image;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_image !== $v) {
			$this->count_image = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_IMAGE;
		}

		return $this;
	} // setCountImage()

	/**
	 * Set the value of [count_audio] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountAudio($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_AUDIO]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_AUDIO] = $this->count_audio;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_audio !== $v) {
			$this->count_audio = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_AUDIO;
		}

		return $this;
	} // setCountAudio()

	/**
	 * Set the value of [count_mix] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountMix($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_MIX]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_MIX] = $this->count_mix;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_mix !== $v) {
			$this->count_mix = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_MIX;
		}

		return $this;
	} // setCountMix()

	/**
	 * Set the value of [count_mix_non_empty] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountMixNonEmpty($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_MIX_NON_EMPTY]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_MIX_NON_EMPTY] = $this->count_mix_non_empty;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_mix_non_empty !== $v) {
			$this->count_mix_non_empty = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_MIX_NON_EMPTY;
		}

		return $this;
	} // setCountMixNonEmpty()

	/**
	 * Set the value of [count_playlist] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountPlaylist($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_PLAYLIST]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_PLAYLIST] = $this->count_playlist;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_playlist !== $v) {
			$this->count_playlist = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_PLAYLIST;
		}

		return $this;
	} // setCountPlaylist()

	/**
	 * Set the value of [count_bandwidth] column.
	 * 
	 * @param      string $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountBandwidth($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_BANDWIDTH]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_BANDWIDTH] = $this->count_bandwidth;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->count_bandwidth !== $v) {
			$this->count_bandwidth = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_BANDWIDTH;
		}

		return $this;
	} // setCountBandwidth()

	/**
	 * Set the value of [count_storage] column.
	 * 
	 * @param      string $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountStorage($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_STORAGE]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_STORAGE] = $this->count_storage;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->count_storage !== $v) {
			$this->count_storage = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_STORAGE;
		}

		return $this;
	} // setCountStorage()

	/**
	 * Set the value of [count_users] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountUsers($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_USERS]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_USERS] = $this->count_users;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_users !== $v) {
			$this->count_users = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_USERS;
		}

		return $this;
	} // setCountUsers()

	/**
	 * Set the value of [count_widgets] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountWidgets($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_WIDGETS]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_WIDGETS] = $this->count_widgets;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_widgets !== $v) {
			$this->count_widgets = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_WIDGETS;
		}

		return $this;
	} // setCountWidgets()

	/**
	 * Set the value of [flag_active_site] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setFlagActiveSite($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::FLAG_ACTIVE_SITE]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::FLAG_ACTIVE_SITE] = $this->flag_active_site;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->flag_active_site !== $v || $this->isNew()) {
			$this->flag_active_site = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::FLAG_ACTIVE_SITE;
		}

		return $this;
	} // setFlagActiveSite()

	/**
	 * Set the value of [flag_active_publisher] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setFlagActivePublisher($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::FLAG_ACTIVE_PUBLISHER]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::FLAG_ACTIVE_PUBLISHER] = $this->flag_active_publisher;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->flag_active_publisher !== $v || $this->isNew()) {
			$this->flag_active_publisher = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::FLAG_ACTIVE_PUBLISHER;
		}

		return $this;
	} // setFlagActivePublisher()

	/**
	 * Set the value of [aggr_storage] column.
	 * 
	 * @param      string $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setAggrStorage($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::AGGR_STORAGE]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::AGGR_STORAGE] = $this->aggr_storage;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->aggr_storage !== $v) {
			$this->aggr_storage = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::AGGR_STORAGE;
		}

		return $this;
	} // setAggrStorage()

	/**
	 * Set the value of [aggr_bandwidth] column.
	 * 
	 * @param      string $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setAggrBandwidth($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::AGGR_BANDWIDTH]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::AGGR_BANDWIDTH] = $this->aggr_bandwidth;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->aggr_bandwidth !== $v) {
			$this->aggr_bandwidth = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::AGGR_BANDWIDTH;
		}

		return $this;
	} // setAggrBandwidth()

	/**
	 * Set the value of [count_buf_start] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountBufStart($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_BUF_START]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_BUF_START] = $this->count_buf_start;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_buf_start !== $v) {
			$this->count_buf_start = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_BUF_START;
		}

		return $this;
	} // setCountBufStart()

	/**
	 * Set the value of [count_buf_end] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountBufEnd($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_BUF_END]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_BUF_END] = $this->count_buf_end;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_buf_end !== $v) {
			$this->count_buf_end = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_BUF_END;
		}

		return $this;
	} // setCountBufEnd()

	/**
	 * Set the value of [count_open_full_screen] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountOpenFullScreen($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_OPEN_FULL_SCREEN]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_OPEN_FULL_SCREEN] = $this->count_open_full_screen;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_open_full_screen !== $v) {
			$this->count_open_full_screen = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_OPEN_FULL_SCREEN;
		}

		return $this;
	} // setCountOpenFullScreen()

	/**
	 * Set the value of [count_close_full_screen] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountCloseFullScreen($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_CLOSE_FULL_SCREEN]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_CLOSE_FULL_SCREEN] = $this->count_close_full_screen;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_close_full_screen !== $v) {
			$this->count_close_full_screen = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_CLOSE_FULL_SCREEN;
		}

		return $this;
	} // setCountCloseFullScreen()

	/**
	 * Set the value of [count_replay] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountReplay($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_REPLAY]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_REPLAY] = $this->count_replay;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_replay !== $v) {
			$this->count_replay = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_REPLAY;
		}

		return $this;
	} // setCountReplay()

	/**
	 * Set the value of [count_seek] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountSeek($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_SEEK]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_SEEK] = $this->count_seek;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_seek !== $v) {
			$this->count_seek = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_SEEK;
		}

		return $this;
	} // setCountSeek()

	/**
	 * Set the value of [count_open_upload] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountOpenUpload($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_OPEN_UPLOAD]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_OPEN_UPLOAD] = $this->count_open_upload;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_open_upload !== $v) {
			$this->count_open_upload = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_OPEN_UPLOAD;
		}

		return $this;
	} // setCountOpenUpload()

	/**
	 * Set the value of [count_save_publish] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountSavePublish($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_SAVE_PUBLISH]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_SAVE_PUBLISH] = $this->count_save_publish;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_save_publish !== $v) {
			$this->count_save_publish = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_SAVE_PUBLISH;
		}

		return $this;
	} // setCountSavePublish()

	/**
	 * Set the value of [count_close_editor] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountCloseEditor($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_CLOSE_EDITOR]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_CLOSE_EDITOR] = $this->count_close_editor;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_close_editor !== $v) {
			$this->count_close_editor = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_CLOSE_EDITOR;
		}

		return $this;
	} // setCountCloseEditor()

	/**
	 * Set the value of [count_pre_bumper_played] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountPreBumperPlayed($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_PRE_BUMPER_PLAYED]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_PRE_BUMPER_PLAYED] = $this->count_pre_bumper_played;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_pre_bumper_played !== $v) {
			$this->count_pre_bumper_played = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_PRE_BUMPER_PLAYED;
		}

		return $this;
	} // setCountPreBumperPlayed()

	/**
	 * Set the value of [count_post_bumper_played] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountPostBumperPlayed($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_POST_BUMPER_PLAYED]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_POST_BUMPER_PLAYED] = $this->count_post_bumper_played;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_post_bumper_played !== $v) {
			$this->count_post_bumper_played = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_POST_BUMPER_PLAYED;
		}

		return $this;
	} // setCountPostBumperPlayed()

	/**
	 * Set the value of [count_bumper_clicked] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountBumperClicked($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_BUMPER_CLICKED]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_BUMPER_CLICKED] = $this->count_bumper_clicked;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_bumper_clicked !== $v) {
			$this->count_bumper_clicked = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_BUMPER_CLICKED;
		}

		return $this;
	} // setCountBumperClicked()

	/**
	 * Set the value of [count_preroll_started] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountPrerollStarted($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_PREROLL_STARTED]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_PREROLL_STARTED] = $this->count_preroll_started;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_preroll_started !== $v) {
			$this->count_preroll_started = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_PREROLL_STARTED;
		}

		return $this;
	} // setCountPrerollStarted()

	/**
	 * Set the value of [count_midroll_started] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountMidrollStarted($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_MIDROLL_STARTED]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_MIDROLL_STARTED] = $this->count_midroll_started;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_midroll_started !== $v) {
			$this->count_midroll_started = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_MIDROLL_STARTED;
		}

		return $this;
	} // setCountMidrollStarted()

	/**
	 * Set the value of [count_postroll_started] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountPostrollStarted($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_POSTROLL_STARTED]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_POSTROLL_STARTED] = $this->count_postroll_started;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_postroll_started !== $v) {
			$this->count_postroll_started = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_POSTROLL_STARTED;
		}

		return $this;
	} // setCountPostrollStarted()

	/**
	 * Set the value of [count_overlay_started] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountOverlayStarted($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_OVERLAY_STARTED]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_OVERLAY_STARTED] = $this->count_overlay_started;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_overlay_started !== $v) {
			$this->count_overlay_started = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_OVERLAY_STARTED;
		}

		return $this;
	} // setCountOverlayStarted()

	/**
	 * Set the value of [count_preroll_clicked] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountPrerollClicked($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_PREROLL_CLICKED]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_PREROLL_CLICKED] = $this->count_preroll_clicked;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_preroll_clicked !== $v) {
			$this->count_preroll_clicked = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_PREROLL_CLICKED;
		}

		return $this;
	} // setCountPrerollClicked()

	/**
	 * Set the value of [count_midroll_clicked] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountMidrollClicked($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_MIDROLL_CLICKED]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_MIDROLL_CLICKED] = $this->count_midroll_clicked;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_midroll_clicked !== $v) {
			$this->count_midroll_clicked = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_MIDROLL_CLICKED;
		}

		return $this;
	} // setCountMidrollClicked()

	/**
	 * Set the value of [count_postroll_clicked] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountPostrollClicked($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_POSTROLL_CLICKED]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_POSTROLL_CLICKED] = $this->count_postroll_clicked;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_postroll_clicked !== $v) {
			$this->count_postroll_clicked = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_POSTROLL_CLICKED;
		}

		return $this;
	} // setCountPostrollClicked()

	/**
	 * Set the value of [count_overlay_clicked] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountOverlayClicked($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_OVERLAY_CLICKED]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_OVERLAY_CLICKED] = $this->count_overlay_clicked;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_overlay_clicked !== $v) {
			$this->count_overlay_clicked = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_OVERLAY_CLICKED;
		}

		return $this;
	} // setCountOverlayClicked()

	/**
	 * Set the value of [count_preroll_25] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountPreroll25($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_PREROLL_25]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_PREROLL_25] = $this->count_preroll_25;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_preroll_25 !== $v) {
			$this->count_preroll_25 = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_PREROLL_25;
		}

		return $this;
	} // setCountPreroll25()

	/**
	 * Set the value of [count_preroll_50] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountPreroll50($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_PREROLL_50]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_PREROLL_50] = $this->count_preroll_50;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_preroll_50 !== $v) {
			$this->count_preroll_50 = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_PREROLL_50;
		}

		return $this;
	} // setCountPreroll50()

	/**
	 * Set the value of [count_preroll_75] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountPreroll75($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_PREROLL_75]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_PREROLL_75] = $this->count_preroll_75;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_preroll_75 !== $v) {
			$this->count_preroll_75 = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_PREROLL_75;
		}

		return $this;
	} // setCountPreroll75()

	/**
	 * Set the value of [count_midroll_25] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountMidroll25($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_MIDROLL_25]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_MIDROLL_25] = $this->count_midroll_25;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_midroll_25 !== $v) {
			$this->count_midroll_25 = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_MIDROLL_25;
		}

		return $this;
	} // setCountMidroll25()

	/**
	 * Set the value of [count_midroll_50] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountMidroll50($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_MIDROLL_50]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_MIDROLL_50] = $this->count_midroll_50;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_midroll_50 !== $v) {
			$this->count_midroll_50 = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_MIDROLL_50;
		}

		return $this;
	} // setCountMidroll50()

	/**
	 * Set the value of [count_midroll_75] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountMidroll75($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_MIDROLL_75]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_MIDROLL_75] = $this->count_midroll_75;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_midroll_75 !== $v) {
			$this->count_midroll_75 = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_MIDROLL_75;
		}

		return $this;
	} // setCountMidroll75()

	/**
	 * Set the value of [count_postroll_25] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountPostroll25($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_POSTROLL_25]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_POSTROLL_25] = $this->count_postroll_25;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_postroll_25 !== $v) {
			$this->count_postroll_25 = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_POSTROLL_25;
		}

		return $this;
	} // setCountPostroll25()

	/**
	 * Set the value of [count_postroll_50] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountPostroll50($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_POSTROLL_50]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_POSTROLL_50] = $this->count_postroll_50;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_postroll_50 !== $v) {
			$this->count_postroll_50 = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_POSTROLL_50;
		}

		return $this;
	} // setCountPostroll50()

	/**
	 * Set the value of [count_postroll_75] column.
	 * 
	 * @param      int $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountPostroll75($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_POSTROLL_75]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_POSTROLL_75] = $this->count_postroll_75;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->count_postroll_75 !== $v) {
			$this->count_postroll_75 = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_POSTROLL_75;
		}

		return $this;
	} // setCountPostroll75()

	/**
	 * Set the value of [count_streaming] column.
	 * 
	 * @param      string $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setCountStreaming($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_STREAMING]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::COUNT_STREAMING] = $this->count_streaming;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->count_streaming !== $v || $this->isNew()) {
			$this->count_streaming = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::COUNT_STREAMING;
		}

		return $this;
	} // setCountStreaming()

	/**
	 * Set the value of [aggr_streaming] column.
	 * 
	 * @param      string $v new value
	 * @return     DwhHourlyPartner The current object (for fluent API support)
	 */
	public function setAggrStreaming($v)
	{
		if(!isset($this->oldColumnsValues[DwhHourlyPartnerPeer::AGGR_STREAMING]))
			$this->oldColumnsValues[DwhHourlyPartnerPeer::AGGR_STREAMING] = $this->aggr_streaming;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->aggr_streaming !== $v || $this->isNew()) {
			$this->aggr_streaming = $v;
			$this->modifiedColumns[] = DwhHourlyPartnerPeer::AGGR_STREAMING;
		}

		return $this;
	} // setAggrStreaming()

	/**
	 * Indicates whether the columns in this object are only set to default values.
	 *
	 * This method can be used in conjunction with isModified() to indicate whether an object is both
	 * modified _and_ has some values set which are non-default.
	 *
	 * @return     boolean Whether the columns in this object are only been set with default values.
	 */
	public function hasOnlyDefaultValues()
	{
			if ($this->partner_id !== 0) {
				return false;
			}

			if ($this->date_id !== 0) {
				return false;
			}

			if ($this->hour_id !== 0) {
				return false;
			}

			if ($this->flag_active_site !== 0) {
				return false;
			}

			if ($this->flag_active_publisher !== 0) {
				return false;
			}

			if ($this->count_streaming !== '0') {
				return false;
			}

			if ($this->aggr_streaming !== '0') {
				return false;
			}

		// otherwise, everything was equal, so return TRUE
		return true;
	} // hasOnlyDefaultValues()

	/**
	 * Hydrates (populates) the object variables with values from the database resultset.
	 *
	 * An offset (0-based "start column") is specified so that objects can be hydrated
	 * with a subset of the columns in the resultset rows.  This is needed, for example,
	 * for results of JOIN queries where the resultset row includes columns from two or
	 * more tables.
	 *
	 * @param      array $row The row returned by PDOStatement->fetch(PDO::FETCH_NUM)
	 * @param      int $startcol 0-based offset column which indicates which restultset column to start with.
	 * @param      boolean $rehydrate Whether this object is being re-hydrated from the database.
	 * @return     int next starting column
	 * @throws     PropelException  - Any caught Exception will be rewrapped as a PropelException.
	 */
	public function hydrate($row, $startcol = 0, $rehydrate = false)
	{
		try {

			$this->partner_id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
			$this->date_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
			$this->hour_id = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
			$this->sum_time_viewed = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->count_time_viewed = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
			$this->count_plays = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
			$this->count_loads = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
			$this->count_plays_25 = ($row[$startcol + 7] !== null) ? (int) $row[$startcol + 7] : null;
			$this->count_plays_50 = ($row[$startcol + 8] !== null) ? (int) $row[$startcol + 8] : null;
			$this->count_plays_75 = ($row[$startcol + 9] !== null) ? (int) $row[$startcol + 9] : null;
			$this->count_plays_100 = ($row[$startcol + 10] !== null) ? (int) $row[$startcol + 10] : null;
			$this->count_edit = ($row[$startcol + 11] !== null) ? (int) $row[$startcol + 11] : null;
			$this->count_viral = ($row[$startcol + 12] !== null) ? (int) $row[$startcol + 12] : null;
			$this->count_download = ($row[$startcol + 13] !== null) ? (int) $row[$startcol + 13] : null;
			$this->count_report = ($row[$startcol + 14] !== null) ? (int) $row[$startcol + 14] : null;
			$this->count_media = ($row[$startcol + 15] !== null) ? (int) $row[$startcol + 15] : null;
			$this->count_video = ($row[$startcol + 16] !== null) ? (int) $row[$startcol + 16] : null;
			$this->count_image = ($row[$startcol + 17] !== null) ? (int) $row[$startcol + 17] : null;
			$this->count_audio = ($row[$startcol + 18] !== null) ? (int) $row[$startcol + 18] : null;
			$this->count_mix = ($row[$startcol + 19] !== null) ? (int) $row[$startcol + 19] : null;
			$this->count_mix_non_empty = ($row[$startcol + 20] !== null) ? (int) $row[$startcol + 20] : null;
			$this->count_playlist = ($row[$startcol + 21] !== null) ? (int) $row[$startcol + 21] : null;
			$this->count_bandwidth = ($row[$startcol + 22] !== null) ? (string) $row[$startcol + 22] : null;
			$this->count_storage = ($row[$startcol + 23] !== null) ? (string) $row[$startcol + 23] : null;
			$this->count_users = ($row[$startcol + 24] !== null) ? (int) $row[$startcol + 24] : null;
			$this->count_widgets = ($row[$startcol + 25] !== null) ? (int) $row[$startcol + 25] : null;
			$this->flag_active_site = ($row[$startcol + 26] !== null) ? (int) $row[$startcol + 26] : null;
			$this->flag_active_publisher = ($row[$startcol + 27] !== null) ? (int) $row[$startcol + 27] : null;
			$this->aggr_storage = ($row[$startcol + 28] !== null) ? (string) $row[$startcol + 28] : null;
			$this->aggr_bandwidth = ($row[$startcol + 29] !== null) ? (string) $row[$startcol + 29] : null;
			$this->count_buf_start = ($row[$startcol + 30] !== null) ? (int) $row[$startcol + 30] : null;
			$this->count_buf_end = ($row[$startcol + 31] !== null) ? (int) $row[$startcol + 31] : null;
			$this->count_open_full_screen = ($row[$startcol + 32] !== null) ? (int) $row[$startcol + 32] : null;
			$this->count_close_full_screen = ($row[$startcol + 33] !== null) ? (int) $row[$startcol + 33] : null;
			$this->count_replay = ($row[$startcol + 34] !== null) ? (int) $row[$startcol + 34] : null;
			$this->count_seek = ($row[$startcol + 35] !== null) ? (int) $row[$startcol + 35] : null;
			$this->count_open_upload = ($row[$startcol + 36] !== null) ? (int) $row[$startcol + 36] : null;
			$this->count_save_publish = ($row[$startcol + 37] !== null) ? (int) $row[$startcol + 37] : null;
			$this->count_close_editor = ($row[$startcol + 38] !== null) ? (int) $row[$startcol + 38] : null;
			$this->count_pre_bumper_played = ($row[$startcol + 39] !== null) ? (int) $row[$startcol + 39] : null;
			$this->count_post_bumper_played = ($row[$startcol + 40] !== null) ? (int) $row[$startcol + 40] : null;
			$this->count_bumper_clicked = ($row[$startcol + 41] !== null) ? (int) $row[$startcol + 41] : null;
			$this->count_preroll_started = ($row[$startcol + 42] !== null) ? (int) $row[$startcol + 42] : null;
			$this->count_midroll_started = ($row[$startcol + 43] !== null) ? (int) $row[$startcol + 43] : null;
			$this->count_postroll_started = ($row[$startcol + 44] !== null) ? (int) $row[$startcol + 44] : null;
			$this->count_overlay_started = ($row[$startcol + 45] !== null) ? (int) $row[$startcol + 45] : null;
			$this->count_preroll_clicked = ($row[$startcol + 46] !== null) ? (int) $row[$startcol + 46] : null;
			$this->count_midroll_clicked = ($row[$startcol + 47] !== null) ? (int) $row[$startcol + 47] : null;
			$this->count_postroll_clicked = ($row[$startcol + 48] !== null) ? (int) $row[$startcol + 48] : null;
			$this->count_overlay_clicked = ($row[$startcol + 49] !== null) ? (int) $row[$startcol + 49] : null;
			$this->count_preroll_25 = ($row[$startcol + 50] !== null) ? (int) $row[$startcol + 50] : null;
			$this->count_preroll_50 = ($row[$startcol + 51] !== null) ? (int) $row[$startcol + 51] : null;
			$this->count_preroll_75 = ($row[$startcol + 52] !== null) ? (int) $row[$startcol + 52] : null;
			$this->count_midroll_25 = ($row[$startcol + 53] !== null) ? (int) $row[$startcol + 53] : null;
			$this->count_midroll_50 = ($row[$startcol + 54] !== null) ? (int) $row[$startcol + 54] : null;
			$this->count_midroll_75 = ($row[$startcol + 55] !== null) ? (int) $row[$startcol + 55] : null;
			$this->count_postroll_25 = ($row[$startcol + 56] !== null) ? (int) $row[$startcol + 56] : null;
			$this->count_postroll_50 = ($row[$startcol + 57] !== null) ? (int) $row[$startcol + 57] : null;
			$this->count_postroll_75 = ($row[$startcol + 58] !== null) ? (int) $row[$startcol + 58] : null;
			$this->count_streaming = ($row[$startcol + 59] !== null) ? (string) $row[$startcol + 59] : null;
			$this->aggr_streaming = ($row[$startcol + 60] !== null) ? (string) $row[$startcol + 60] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 61; // 61 = DwhHourlyPartnerPeer::NUM_COLUMNS - DwhHourlyPartnerPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating DwhHourlyPartner object", $e);
		}
	}

	/**
	 * Checks and repairs the internal consistency of the object.
	 *
	 * This method is executed after an already-instantiated object is re-hydrated
	 * from the database.  It exists to check any foreign keys to make sure that
	 * the objects related to the current object are correct based on foreign key.
	 *
	 * You can override this method in the stub class, but you should always invoke
	 * the base method from the overridden method (i.e. parent::ensureConsistency()),
	 * in case your model changes.
	 *
	 * @throws     PropelException
	 */
	public function ensureConsistency()
	{

	} // ensureConsistency

	/**
	 * Array of ValidationFailed objects.
	 * @var        array ValidationFailed[]
	 */
	protected $validationFailures = array();

	/**
	 * Gets any ValidationFailed objects that resulted from last call to validate().
	 *
	 *
	 * @return     array ValidationFailed[]
	 * @see        validate()
	 */
	public function getValidationFailures()
	{
		return $this->validationFailures;
	}

	/**
	 * Validates the objects modified field values and all objects related to this table.
	 *
	 * If $columns is either a column name or an array of column names
	 * only those columns are validated.
	 *
	 * @param      mixed $columns Column name or an array of column names.
	 * @return     boolean Whether all columns pass validation.
	 * @see        doValidate()
	 * @see        getValidationFailures()
	 */
	public function validate($columns = null)
	{
		$res = $this->doValidate($columns);
		if ($res === true) {
			$this->validationFailures = array();
			return true;
		} else {
			$this->validationFailures = $res;
			return false;
		}
	}

	/**
	 * This function performs the validation work for complex object models.
	 *
	 * In addition to checking the current object, all related objects will
	 * also be validated.  If all pass then <code>true</code> is returned; otherwise
	 * an aggreagated array of ValidationFailed objects will be returned.
	 *
	 * @param      array $columns Array of column names to validate.
	 * @return     mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objets otherwise.
	 */
	protected function doValidate($columns = null)
	{
		if (!$this->alreadyInValidation) {
			$this->alreadyInValidation = true;
			$retval = null;

			$failureMap = array();


			if (($retval = DwhHourlyPartnerPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}



			$this->alreadyInValidation = false;
		}

		return (!empty($failureMap) ? $failureMap : true);
	}

	/**
	 * Retrieves a field from the object by name passed in as a string.
	 *
	 * @param      string $name name
	 * @param      string $type The type of fieldname the $name is of:
	 *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @return     mixed Value of field.
	 */
	public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = DwhHourlyPartnerPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		$field = $this->getByPosition($pos);
		return $field;
	}

	/**
	 * Retrieves a field from the object by Position as specified in the xml schema.
	 * Zero-based.
	 *
	 * @param      int $pos position in xml schema
	 * @return     mixed Value of field at $pos
	 */
	public function getByPosition($pos)
	{
		switch($pos) {
			case 0:
				return $this->getPartnerId();
				break;
			case 1:
				return $this->getDateId();
				break;
			case 2:
				return $this->getHourId();
				break;
			case 3:
				return $this->getSumTimeViewed();
				break;
			case 4:
				return $this->getCountTimeViewed();
				break;
			case 5:
				return $this->getCountPlays();
				break;
			case 6:
				return $this->getCountLoads();
				break;
			case 7:
				return $this->getCountPlays25();
				break;
			case 8:
				return $this->getCountPlays50();
				break;
			case 9:
				return $this->getCountPlays75();
				break;
			case 10:
				return $this->getCountPlays100();
				break;
			case 11:
				return $this->getCountEdit();
				break;
			case 12:
				return $this->getCountViral();
				break;
			case 13:
				return $this->getCountDownload();
				break;
			case 14:
				return $this->getCountReport();
				break;
			case 15:
				return $this->getCountMedia();
				break;
			case 16:
				return $this->getCountVideo();
				break;
			case 17:
				return $this->getCountImage();
				break;
			case 18:
				return $this->getCountAudio();
				break;
			case 19:
				return $this->getCountMix();
				break;
			case 20:
				return $this->getCountMixNonEmpty();
				break;
			case 21:
				return $this->getCountPlaylist();
				break;
			case 22:
				return $this->getCountBandwidth();
				break;
			case 23:
				return $this->getCountStorage();
				break;
			case 24:
				return $this->getCountUsers();
				break;
			case 25:
				return $this->getCountWidgets();
				break;
			case 26:
				return $this->getFlagActiveSite();
				break;
			case 27:
				return $this->getFlagActivePublisher();
				break;
			case 28:
				return $this->getAggrStorage();
				break;
			case 29:
				return $this->getAggrBandwidth();
				break;
			case 30:
				return $this->getCountBufStart();
				break;
			case 31:
				return $this->getCountBufEnd();
				break;
			case 32:
				return $this->getCountOpenFullScreen();
				break;
			case 33:
				return $this->getCountCloseFullScreen();
				break;
			case 34:
				return $this->getCountReplay();
				break;
			case 35:
				return $this->getCountSeek();
				break;
			case 36:
				return $this->getCountOpenUpload();
				break;
			case 37:
				return $this->getCountSavePublish();
				break;
			case 38:
				return $this->getCountCloseEditor();
				break;
			case 39:
				return $this->getCountPreBumperPlayed();
				break;
			case 40:
				return $this->getCountPostBumperPlayed();
				break;
			case 41:
				return $this->getCountBumperClicked();
				break;
			case 42:
				return $this->getCountPrerollStarted();
				break;
			case 43:
				return $this->getCountMidrollStarted();
				break;
			case 44:
				return $this->getCountPostrollStarted();
				break;
			case 45:
				return $this->getCountOverlayStarted();
				break;
			case 46:
				return $this->getCountPrerollClicked();
				break;
			case 47:
				return $this->getCountMidrollClicked();
				break;
			case 48:
				return $this->getCountPostrollClicked();
				break;
			case 49:
				return $this->getCountOverlayClicked();
				break;
			case 50:
				return $this->getCountPreroll25();
				break;
			case 51:
				return $this->getCountPreroll50();
				break;
			case 52:
				return $this->getCountPreroll75();
				break;
			case 53:
				return $this->getCountMidroll25();
				break;
			case 54:
				return $this->getCountMidroll50();
				break;
			case 55:
				return $this->getCountMidroll75();
				break;
			case 56:
				return $this->getCountPostroll25();
				break;
			case 57:
				return $this->getCountPostroll50();
				break;
			case 58:
				return $this->getCountPostroll75();
				break;
			case 59:
				return $this->getCountStreaming();
				break;
			case 60:
				return $this->getAggrStreaming();
				break;
			default:
				return null;
				break;
		} // switch()
	}

	/**
	 * Exports the object as an array.
	 *
	 * You can specify the key type of the array by passing one of the class
	 * type constants.
	 *
	 * @param      string $keyType (optional) One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                        BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. Defaults to BasePeer::TYPE_PHPNAME.
	 * @param      boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns.  Defaults to TRUE.
	 * @return     an associative array containing the field names (as keys) and field values
	 */
	public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true)
	{
		$keys = DwhHourlyPartnerPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getPartnerId(),
			$keys[1] => $this->getDateId(),
			$keys[2] => $this->getHourId(),
			$keys[3] => $this->getSumTimeViewed(),
			$keys[4] => $this->getCountTimeViewed(),
			$keys[5] => $this->getCountPlays(),
			$keys[6] => $this->getCountLoads(),
			$keys[7] => $this->getCountPlays25(),
			$keys[8] => $this->getCountPlays50(),
			$keys[9] => $this->getCountPlays75(),
			$keys[10] => $this->getCountPlays100(),
			$keys[11] => $this->getCountEdit(),
			$keys[12] => $this->getCountViral(),
			$keys[13] => $this->getCountDownload(),
			$keys[14] => $this->getCountReport(),
			$keys[15] => $this->getCountMedia(),
			$keys[16] => $this->getCountVideo(),
			$keys[17] => $this->getCountImage(),
			$keys[18] => $this->getCountAudio(),
			$keys[19] => $this->getCountMix(),
			$keys[20] => $this->getCountMixNonEmpty(),
			$keys[21] => $this->getCountPlaylist(),
			$keys[22] => $this->getCountBandwidth(),
			$keys[23] => $this->getCountStorage(),
			$keys[24] => $this->getCountUsers(),
			$keys[25] => $this->getCountWidgets(),
			$keys[26] => $this->getFlagActiveSite(),
			$keys[27] => $this->getFlagActivePublisher(),
			$keys[28] => $this->getAggrStorage(),
			$keys[29] => $this->getAggrBandwidth(),
			$keys[30] => $this->getCountBufStart(),
			$keys[31] => $this->getCountBufEnd(),
			$keys[32] => $this->getCountOpenFullScreen(),
			$keys[33] => $this->getCountCloseFullScreen(),
			$keys[34] => $this->getCountReplay(),
			$keys[35] => $this->getCountSeek(),
			$keys[36] => $this->getCountOpenUpload(),
			$keys[37] => $this->getCountSavePublish(),
			$keys[38] => $this->getCountCloseEditor(),
			$keys[39] => $this->getCountPreBumperPlayed(),
			$keys[40] => $this->getCountPostBumperPlayed(),
			$keys[41] => $this->getCountBumperClicked(),
			$keys[42] => $this->getCountPrerollStarted(),
			$keys[43] => $this->getCountMidrollStarted(),
			$keys[44] => $this->getCountPostrollStarted(),
			$keys[45] => $this->getCountOverlayStarted(),
			$keys[46] => $this->getCountPrerollClicked(),
			$keys[47] => $this->getCountMidrollClicked(),
			$keys[48] => $this->getCountPostrollClicked(),
			$keys[49] => $this->getCountOverlayClicked(),
			$keys[50] => $this->getCountPreroll25(),
			$keys[51] => $this->getCountPreroll50(),
			$keys[52] => $this->getCountPreroll75(),
			$keys[53] => $this->getCountMidroll25(),
			$keys[54] => $this->getCountMidroll50(),
			$keys[55] => $this->getCountMidroll75(),
			$keys[56] => $this->getCountPostroll25(),
			$keys[57] => $this->getCountPostroll50(),
			$keys[58] => $this->getCountPostroll75(),
			$keys[59] => $this->getCountStreaming(),
			$keys[60] => $this->getAggrStreaming(),
		);
		return $result;
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(DwhHourlyPartnerPeer::DATABASE_NAME);

		if ($this->isColumnModified(DwhHourlyPartnerPeer::PARTNER_ID)) $criteria->add(DwhHourlyPartnerPeer::PARTNER_ID, $this->partner_id);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::DATE_ID)) $criteria->add(DwhHourlyPartnerPeer::DATE_ID, $this->date_id);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::HOUR_ID)) $criteria->add(DwhHourlyPartnerPeer::HOUR_ID, $this->hour_id);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::SUM_TIME_VIEWED)) $criteria->add(DwhHourlyPartnerPeer::SUM_TIME_VIEWED, $this->sum_time_viewed);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_TIME_VIEWED)) $criteria->add(DwhHourlyPartnerPeer::COUNT_TIME_VIEWED, $this->count_time_viewed);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_PLAYS)) $criteria->add(DwhHourlyPartnerPeer::COUNT_PLAYS, $this->count_plays);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_LOADS)) $criteria->add(DwhHourlyPartnerPeer::COUNT_LOADS, $this->count_loads);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_PLAYS_25)) $criteria->add(DwhHourlyPartnerPeer::COUNT_PLAYS_25, $this->count_plays_25);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_PLAYS_50)) $criteria->add(DwhHourlyPartnerPeer::COUNT_PLAYS_50, $this->count_plays_50);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_PLAYS_75)) $criteria->add(DwhHourlyPartnerPeer::COUNT_PLAYS_75, $this->count_plays_75);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_PLAYS_100)) $criteria->add(DwhHourlyPartnerPeer::COUNT_PLAYS_100, $this->count_plays_100);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_EDIT)) $criteria->add(DwhHourlyPartnerPeer::COUNT_EDIT, $this->count_edit);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_VIRAL)) $criteria->add(DwhHourlyPartnerPeer::COUNT_VIRAL, $this->count_viral);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_DOWNLOAD)) $criteria->add(DwhHourlyPartnerPeer::COUNT_DOWNLOAD, $this->count_download);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_REPORT)) $criteria->add(DwhHourlyPartnerPeer::COUNT_REPORT, $this->count_report);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_MEDIA)) $criteria->add(DwhHourlyPartnerPeer::COUNT_MEDIA, $this->count_media);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_VIDEO)) $criteria->add(DwhHourlyPartnerPeer::COUNT_VIDEO, $this->count_video);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_IMAGE)) $criteria->add(DwhHourlyPartnerPeer::COUNT_IMAGE, $this->count_image);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_AUDIO)) $criteria->add(DwhHourlyPartnerPeer::COUNT_AUDIO, $this->count_audio);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_MIX)) $criteria->add(DwhHourlyPartnerPeer::COUNT_MIX, $this->count_mix);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_MIX_NON_EMPTY)) $criteria->add(DwhHourlyPartnerPeer::COUNT_MIX_NON_EMPTY, $this->count_mix_non_empty);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_PLAYLIST)) $criteria->add(DwhHourlyPartnerPeer::COUNT_PLAYLIST, $this->count_playlist);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_BANDWIDTH)) $criteria->add(DwhHourlyPartnerPeer::COUNT_BANDWIDTH, $this->count_bandwidth);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_STORAGE)) $criteria->add(DwhHourlyPartnerPeer::COUNT_STORAGE, $this->count_storage);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_USERS)) $criteria->add(DwhHourlyPartnerPeer::COUNT_USERS, $this->count_users);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_WIDGETS)) $criteria->add(DwhHourlyPartnerPeer::COUNT_WIDGETS, $this->count_widgets);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::FLAG_ACTIVE_SITE)) $criteria->add(DwhHourlyPartnerPeer::FLAG_ACTIVE_SITE, $this->flag_active_site);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::FLAG_ACTIVE_PUBLISHER)) $criteria->add(DwhHourlyPartnerPeer::FLAG_ACTIVE_PUBLISHER, $this->flag_active_publisher);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::AGGR_STORAGE)) $criteria->add(DwhHourlyPartnerPeer::AGGR_STORAGE, $this->aggr_storage);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::AGGR_BANDWIDTH)) $criteria->add(DwhHourlyPartnerPeer::AGGR_BANDWIDTH, $this->aggr_bandwidth);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_BUF_START)) $criteria->add(DwhHourlyPartnerPeer::COUNT_BUF_START, $this->count_buf_start);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_BUF_END)) $criteria->add(DwhHourlyPartnerPeer::COUNT_BUF_END, $this->count_buf_end);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_OPEN_FULL_SCREEN)) $criteria->add(DwhHourlyPartnerPeer::COUNT_OPEN_FULL_SCREEN, $this->count_open_full_screen);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_CLOSE_FULL_SCREEN)) $criteria->add(DwhHourlyPartnerPeer::COUNT_CLOSE_FULL_SCREEN, $this->count_close_full_screen);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_REPLAY)) $criteria->add(DwhHourlyPartnerPeer::COUNT_REPLAY, $this->count_replay);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_SEEK)) $criteria->add(DwhHourlyPartnerPeer::COUNT_SEEK, $this->count_seek);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_OPEN_UPLOAD)) $criteria->add(DwhHourlyPartnerPeer::COUNT_OPEN_UPLOAD, $this->count_open_upload);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_SAVE_PUBLISH)) $criteria->add(DwhHourlyPartnerPeer::COUNT_SAVE_PUBLISH, $this->count_save_publish);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_CLOSE_EDITOR)) $criteria->add(DwhHourlyPartnerPeer::COUNT_CLOSE_EDITOR, $this->count_close_editor);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_PRE_BUMPER_PLAYED)) $criteria->add(DwhHourlyPartnerPeer::COUNT_PRE_BUMPER_PLAYED, $this->count_pre_bumper_played);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_POST_BUMPER_PLAYED)) $criteria->add(DwhHourlyPartnerPeer::COUNT_POST_BUMPER_PLAYED, $this->count_post_bumper_played);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_BUMPER_CLICKED)) $criteria->add(DwhHourlyPartnerPeer::COUNT_BUMPER_CLICKED, $this->count_bumper_clicked);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_PREROLL_STARTED)) $criteria->add(DwhHourlyPartnerPeer::COUNT_PREROLL_STARTED, $this->count_preroll_started);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_MIDROLL_STARTED)) $criteria->add(DwhHourlyPartnerPeer::COUNT_MIDROLL_STARTED, $this->count_midroll_started);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_POSTROLL_STARTED)) $criteria->add(DwhHourlyPartnerPeer::COUNT_POSTROLL_STARTED, $this->count_postroll_started);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_OVERLAY_STARTED)) $criteria->add(DwhHourlyPartnerPeer::COUNT_OVERLAY_STARTED, $this->count_overlay_started);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_PREROLL_CLICKED)) $criteria->add(DwhHourlyPartnerPeer::COUNT_PREROLL_CLICKED, $this->count_preroll_clicked);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_MIDROLL_CLICKED)) $criteria->add(DwhHourlyPartnerPeer::COUNT_MIDROLL_CLICKED, $this->count_midroll_clicked);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_POSTROLL_CLICKED)) $criteria->add(DwhHourlyPartnerPeer::COUNT_POSTROLL_CLICKED, $this->count_postroll_clicked);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_OVERLAY_CLICKED)) $criteria->add(DwhHourlyPartnerPeer::COUNT_OVERLAY_CLICKED, $this->count_overlay_clicked);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_PREROLL_25)) $criteria->add(DwhHourlyPartnerPeer::COUNT_PREROLL_25, $this->count_preroll_25);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_PREROLL_50)) $criteria->add(DwhHourlyPartnerPeer::COUNT_PREROLL_50, $this->count_preroll_50);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_PREROLL_75)) $criteria->add(DwhHourlyPartnerPeer::COUNT_PREROLL_75, $this->count_preroll_75);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_MIDROLL_25)) $criteria->add(DwhHourlyPartnerPeer::COUNT_MIDROLL_25, $this->count_midroll_25);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_MIDROLL_50)) $criteria->add(DwhHourlyPartnerPeer::COUNT_MIDROLL_50, $this->count_midroll_50);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_MIDROLL_75)) $criteria->add(DwhHourlyPartnerPeer::COUNT_MIDROLL_75, $this->count_midroll_75);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_POSTROLL_25)) $criteria->add(DwhHourlyPartnerPeer::COUNT_POSTROLL_25, $this->count_postroll_25);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_POSTROLL_50)) $criteria->add(DwhHourlyPartnerPeer::COUNT_POSTROLL_50, $this->count_postroll_50);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_POSTROLL_75)) $criteria->add(DwhHourlyPartnerPeer::COUNT_POSTROLL_75, $this->count_postroll_75);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::COUNT_STREAMING)) $criteria->add(DwhHourlyPartnerPeer::COUNT_STREAMING, $this->count_streaming);
		if ($this->isColumnModified(DwhHourlyPartnerPeer::AGGR_STREAMING)) $criteria->add(DwhHourlyPartnerPeer::AGGR_STREAMING, $this->aggr_streaming);

		return $criteria;
	}

	/**
	 * Builds a Criteria object containing the primary key for this object.
	 *
	 * Unlike buildCriteria() this method includes the primary key values regardless
	 * of whether or not they have been modified.
	 *
	 * @return     Criteria The Criteria object containing value(s) for primary key(s).
	 */
	public function buildPkeyCriteria()
	{
		$criteria = new Criteria(DwhHourlyPartnerPeer::DATABASE_NAME);

		$criteria->add(DwhHourlyPartnerPeer::PARTNER_ID, $this->partner_id);
		$criteria->add(DwhHourlyPartnerPeer::DATE_ID, $this->date_id);
		$criteria->add(DwhHourlyPartnerPeer::HOUR_ID, $this->hour_id);

		return $criteria;
	}

	/**
	 * Returns the composite primary key for this object.
	 * The array elements will be in same order as specified in XML.
	 * @return     array
	 */
	public function getPrimaryKey()
	{
		$pks = array();

		$pks[0] = $this->getPartnerId();

		$pks[1] = $this->getDateId();

		$pks[2] = $this->getHourId();

		return $pks;
	}

	/**
	 * Set the [composite] primary key.
	 *
	 * @param      array $keys The elements of the composite key (order must match the order in XML file).
	 * @return     void
	 */
	public function setPrimaryKey($keys)
	{

		$this->setPartnerId($keys[0]);

		$this->setDateId($keys[1]);

		$this->setHourId($keys[2]);

	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of DwhHourlyPartner (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setPartnerId($this->partner_id);

		$copyObj->setDateId($this->date_id);

		$copyObj->setHourId($this->hour_id);

		$copyObj->setSumTimeViewed($this->sum_time_viewed);

		$copyObj->setCountTimeViewed($this->count_time_viewed);

		$copyObj->setCountPlays($this->count_plays);

		$copyObj->setCountLoads($this->count_loads);

		$copyObj->setCountPlays25($this->count_plays_25);

		$copyObj->setCountPlays50($this->count_plays_50);

		$copyObj->setCountPlays75($this->count_plays_75);

		$copyObj->setCountPlays100($this->count_plays_100);

		$copyObj->setCountEdit($this->count_edit);

		$copyObj->setCountViral($this->count_viral);

		$copyObj->setCountDownload($this->count_download);

		$copyObj->setCountReport($this->count_report);

		$copyObj->setCountMedia($this->count_media);

		$copyObj->setCountVideo($this->count_video);

		$copyObj->setCountImage($this->count_image);

		$copyObj->setCountAudio($this->count_audio);

		$copyObj->setCountMix($this->count_mix);

		$copyObj->setCountMixNonEmpty($this->count_mix_non_empty);

		$copyObj->setCountPlaylist($this->count_playlist);

		$copyObj->setCountBandwidth($this->count_bandwidth);

		$copyObj->setCountStorage($this->count_storage);

		$copyObj->setCountUsers($this->count_users);

		$copyObj->setCountWidgets($this->count_widgets);

		$copyObj->setFlagActiveSite($this->flag_active_site);

		$copyObj->setFlagActivePublisher($this->flag_active_publisher);

		$copyObj->setAggrStorage($this->aggr_storage);

		$copyObj->setAggrBandwidth($this->aggr_bandwidth);

		$copyObj->setCountBufStart($this->count_buf_start);

		$copyObj->setCountBufEnd($this->count_buf_end);

		$copyObj->setCountOpenFullScreen($this->count_open_full_screen);

		$copyObj->setCountCloseFullScreen($this->count_close_full_screen);

		$copyObj->setCountReplay($this->count_replay);

		$copyObj->setCountSeek($this->count_seek);

		$copyObj->setCountOpenUpload($this->count_open_upload);

		$copyObj->setCountSavePublish($this->count_save_publish);

		$copyObj->setCountCloseEditor($this->count_close_editor);

		$copyObj->setCountPreBumperPlayed($this->count_pre_bumper_played);

		$copyObj->setCountPostBumperPlayed($this->count_post_bumper_played);

		$copyObj->setCountBumperClicked($this->count_bumper_clicked);

		$copyObj->setCountPrerollStarted($this->count_preroll_started);

		$copyObj->setCountMidrollStarted($this->count_midroll_started);

		$copyObj->setCountPostrollStarted($this->count_postroll_started);

		$copyObj->setCountOverlayStarted($this->count_overlay_started);

		$copyObj->setCountPrerollClicked($this->count_preroll_clicked);

		$copyObj->setCountMidrollClicked($this->count_midroll_clicked);

		$copyObj->setCountPostrollClicked($this->count_postroll_clicked);

		$copyObj->setCountOverlayClicked($this->count_overlay_clicked);

		$copyObj->setCountPreroll25($this->count_preroll_25);

		$copyObj->setCountPreroll50($this->count_preroll_50);

		$copyObj->setCountPreroll75($this->count_preroll_75);

		$copyObj->setCountMidroll25($this->count_midroll_25);

		$copyObj->setCountMidroll50($this->count_midroll_50);

		$copyObj->setCountMidroll75($this->count_midroll_75);

		$copyObj->setCountPostroll25($this->count_postroll_25);

		$copyObj->setCountPostroll50($this->count_postroll_50);

		$copyObj->setCountPostroll75($this->count_postroll_75);

		$copyObj->setCountStreaming($this->count_streaming);

		$copyObj->setAggrStreaming($this->aggr_streaming);


		$copyObj->setNew(true);

	}

	/**
	 * Makes a copy of this object that will be inserted as a new row in table when saved.
	 * It creates a new object filling in the simple attributes, but skipping any primary
	 * keys that are defined for the table.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @return     DwhHourlyPartner Clone of current object.
	 * @throws     PropelException
	 */
	public function copy($deepCopy = false)
	{
		// we use get_class(), because this might be a subclass
		$clazz = get_class($this);
		$copyObj = new $clazz();
		$this->copyInto($copyObj, $deepCopy);
		$copyObj->setCopiedFrom($this);
		return $copyObj;
	}
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @var     DwhHourlyPartner Clone of current object.
	 */
	protected $copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      DwhHourlyPartner $copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(DwhHourlyPartner $copiedFrom)
	{
		$this->copiedFrom = $copiedFrom;
	}

	/**
	 * Returns a peer instance associated with this om.
	 *
	 * Since Peer classes are not to have any instance attributes, this method returns the
	 * same instance for all member of this class. The method could therefore
	 * be static, but this would prevent one from overriding the behavior.
	 *
	 * @return     DwhHourlyPartnerPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new DwhHourlyPartnerPeer();
		}
		return self::$peer;
	}

	/**
	 * Resets all collections of referencing foreign keys.
	 *
	 * This method is a user-space workaround for PHP's inability to garbage collect objects
	 * with circular references.  This is currently necessary when using Propel in certain
	 * daemon or large-volumne/high-memory operations.
	 *
	 * @param      boolean $deep Whether to also clear the references on all associated objects.
	 */
	public function clearAllReferences($deep = false)
	{
		if ($deep) {
		} // if ($deep)

	}

} // BaseDwhHourlyPartner
