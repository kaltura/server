<?php

/**
 * Base class that represents a row from the 'entry' table.
 *
 * 
 *
 * @package    lib.model.om
 */
abstract class Baseentry extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        entryPeer
	 */
	protected static $peer;

	/**
	 * The value for the id field.
	 * @var        string
	 */
	protected $id;

	/**
	 * The value for the kshow_id field.
	 * @var        string
	 */
	protected $kshow_id;

	/**
	 * The value for the kuser_id field.
	 * @var        int
	 */
	protected $kuser_id;

	/**
	 * The value for the name field.
	 * @var        string
	 */
	protected $name;

	/**
	 * The value for the type field.
	 * @var        int
	 */
	protected $type;

	/**
	 * The value for the media_type field.
	 * @var        int
	 */
	protected $media_type;

	/**
	 * The value for the data field.
	 * @var        string
	 */
	protected $data;

	/**
	 * The value for the thumbnail field.
	 * @var        string
	 */
	protected $thumbnail;

	/**
	 * The value for the views field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $views;

	/**
	 * The value for the votes field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $votes;

	/**
	 * The value for the comments field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $comments;

	/**
	 * The value for the favorites field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $favorites;

	/**
	 * The value for the total_rank field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $total_rank;

	/**
	 * The value for the rank field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $rank;

	/**
	 * The value for the tags field.
	 * @var        string
	 */
	protected $tags;

	/**
	 * The value for the anonymous field.
	 * @var        int
	 */
	protected $anonymous;

	/**
	 * The value for the status field.
	 * @var        int
	 */
	protected $status;

	/**
	 * The value for the source field.
	 * @var        int
	 */
	protected $source;

	/**
	 * The value for the source_id field.
	 * @var        string
	 */
	protected $source_id;

	/**
	 * The value for the source_link field.
	 * @var        string
	 */
	protected $source_link;

	/**
	 * The value for the license_type field.
	 * @var        int
	 */
	protected $license_type;

	/**
	 * The value for the credit field.
	 * @var        string
	 */
	protected $credit;

	/**
	 * The value for the length_in_msecs field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $length_in_msecs;

	/**
	 * The value for the created_at field.
	 * @var        string
	 */
	protected $created_at;

	/**
	 * The value for the updated_at field.
	 * @var        string
	 */
	protected $updated_at;

	/**
	 * The value for the partner_id field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $partner_id;

	/**
	 * The value for the display_in_search field.
	 * @var        int
	 */
	protected $display_in_search;

	/**
	 * The value for the subp_id field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $subp_id;

	/**
	 * The value for the custom_data field.
	 * @var        string
	 */
	protected $custom_data;

	/**
	 * The value for the search_text field.
	 * @var        string
	 */
	protected $search_text;

	/**
	 * The value for the screen_name field.
	 * @var        string
	 */
	protected $screen_name;

	/**
	 * The value for the site_url field.
	 * @var        string
	 */
	protected $site_url;

	/**
	 * The value for the permissions field.
	 * Note: this column has a database default value of: 1
	 * @var        int
	 */
	protected $permissions;

	/**
	 * The value for the group_id field.
	 * @var        string
	 */
	protected $group_id;

	/**
	 * The value for the plays field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $plays;

	/**
	 * The value for the partner_data field.
	 * @var        string
	 */
	protected $partner_data;

	/**
	 * The value for the int_id field.
	 * @var        int
	 */
	protected $int_id;

	/**
	 * The value for the indexed_custom_data_1 field.
	 * @var        int
	 */
	protected $indexed_custom_data_1;

	/**
	 * The value for the description field.
	 * @var        string
	 */
	protected $description;

	/**
	 * The value for the media_date field.
	 * @var        string
	 */
	protected $media_date;

	/**
	 * The value for the admin_tags field.
	 * @var        string
	 */
	protected $admin_tags;

	/**
	 * The value for the moderation_status field.
	 * @var        int
	 */
	protected $moderation_status;

	/**
	 * The value for the moderation_count field.
	 * @var        int
	 */
	protected $moderation_count;

	/**
	 * The value for the modified_at field.
	 * @var        string
	 */
	protected $modified_at;

	/**
	 * The value for the puser_id field.
	 * @var        string
	 */
	protected $puser_id;

	/**
	 * The value for the access_control_id field.
	 * @var        int
	 */
	protected $access_control_id;

	/**
	 * The value for the conversion_profile_id field.
	 * @var        int
	 */
	protected $conversion_profile_id;

	/**
	 * The value for the categories field.
	 * @var        string
	 */
	protected $categories;

	/**
	 * The value for the categories_ids field.
	 * @var        string
	 */
	protected $categories_ids;

	/**
	 * The value for the start_date field.
	 * @var        string
	 */
	protected $start_date;

	/**
	 * The value for the end_date field.
	 * @var        string
	 */
	protected $end_date;

	/**
	 * The value for the search_text_discrete field.
	 * @var        string
	 */
	protected $search_text_discrete;

	/**
	 * The value for the flavor_params_ids field.
	 * @var        string
	 */
	protected $flavor_params_ids;

	/**
	 * The value for the available_from field.
	 * @var        string
	 */
	protected $available_from;

	/**
	 * @var        kshow
	 */
	protected $akshow;

	/**
	 * @var        kuser
	 */
	protected $akuser;

	/**
	 * @var        accessControl
	 */
	protected $aaccessControl;

	/**
	 * @var        conversionProfile2
	 */
	protected $aconversionProfile2;

	/**
	 * @var        array kvote[] Collection to store aggregation of kvote objects.
	 */
	protected $collkvotes;

	/**
	 * @var        Criteria The criteria used to select the current contents of collkvotes.
	 */
	private $lastkvoteCriteria = null;

	/**
	 * @var        array conversion[] Collection to store aggregation of conversion objects.
	 */
	protected $collconversions;

	/**
	 * @var        Criteria The criteria used to select the current contents of collconversions.
	 */
	private $lastconversionCriteria = null;

	/**
	 * @var        array WidgetLog[] Collection to store aggregation of WidgetLog objects.
	 */
	protected $collWidgetLogs;

	/**
	 * @var        Criteria The criteria used to select the current contents of collWidgetLogs.
	 */
	private $lastWidgetLogCriteria = null;

	/**
	 * @var        array moderationFlag[] Collection to store aggregation of moderationFlag objects.
	 */
	protected $collmoderationFlags;

	/**
	 * @var        Criteria The criteria used to select the current contents of collmoderationFlags.
	 */
	private $lastmoderationFlagCriteria = null;

	/**
	 * @var        array roughcutEntry[] Collection to store aggregation of roughcutEntry objects.
	 */
	protected $collroughcutEntrysRelatedByRoughcutId;

	/**
	 * @var        Criteria The criteria used to select the current contents of collroughcutEntrysRelatedByRoughcutId.
	 */
	private $lastroughcutEntryRelatedByRoughcutIdCriteria = null;

	/**
	 * @var        array roughcutEntry[] Collection to store aggregation of roughcutEntry objects.
	 */
	protected $collroughcutEntrysRelatedByEntryId;

	/**
	 * @var        Criteria The criteria used to select the current contents of collroughcutEntrysRelatedByEntryId.
	 */
	private $lastroughcutEntryRelatedByEntryIdCriteria = null;

	/**
	 * @var        array widget[] Collection to store aggregation of widget objects.
	 */
	protected $collwidgets;

	/**
	 * @var        Criteria The criteria used to select the current contents of collwidgets.
	 */
	private $lastwidgetCriteria = null;

	/**
	 * @var        array flavorParamsOutput[] Collection to store aggregation of flavorParamsOutput objects.
	 */
	protected $collflavorParamsOutputs;

	/**
	 * @var        Criteria The criteria used to select the current contents of collflavorParamsOutputs.
	 */
	private $lastflavorParamsOutputCriteria = null;

	/**
	 * @var        array flavorAsset[] Collection to store aggregation of flavorAsset objects.
	 */
	protected $collflavorAssets;

	/**
	 * @var        Criteria The criteria used to select the current contents of collflavorAssets.
	 */
	private $lastflavorAssetCriteria = null;

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
		$this->views = 0;
		$this->votes = 0;
		$this->comments = 0;
		$this->favorites = 0;
		$this->total_rank = 0;
		$this->rank = 0;
		$this->length_in_msecs = 0;
		$this->partner_id = 0;
		$this->subp_id = 0;
		$this->permissions = 1;
		$this->plays = 0;
	}

	/**
	 * Initializes internal state of Baseentry object.
	 * @see        applyDefaults()
	 */
	public function __construct()
	{
		parent::__construct();
		$this->applyDefaultValues();
	}

	/**
	 * Get the [id] column value.
	 * 
	 * @return     string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get the [kshow_id] column value.
	 * 
	 * @return     string
	 */
	public function getKshowId()
	{
		return $this->kshow_id;
	}

	/**
	 * Get the [kuser_id] column value.
	 * 
	 * @return     int
	 */
	public function getKuserId()
	{
		return $this->kuser_id;
	}

	/**
	 * Get the [name] column value.
	 * 
	 * @return     string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Get the [type] column value.
	 * 
	 * @return     int
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * Get the [media_type] column value.
	 * 
	 * @return     int
	 */
	public function getMediaType()
	{
		return $this->media_type;
	}

	/**
	 * Get the [data] column value.
	 * 
	 * @return     string
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * Get the [thumbnail] column value.
	 * 
	 * @return     string
	 */
	public function getThumbnail()
	{
		return $this->thumbnail;
	}

	/**
	 * Get the [views] column value.
	 * 
	 * @return     int
	 */
	public function getViews()
	{
		return $this->views;
	}

	/**
	 * Get the [votes] column value.
	 * 
	 * @return     int
	 */
	public function getVotes()
	{
		return $this->votes;
	}

	/**
	 * Get the [comments] column value.
	 * 
	 * @return     int
	 */
	public function getComments()
	{
		return $this->comments;
	}

	/**
	 * Get the [favorites] column value.
	 * 
	 * @return     int
	 */
	public function getFavorites()
	{
		return $this->favorites;
	}

	/**
	 * Get the [total_rank] column value.
	 * 
	 * @return     int
	 */
	public function getTotalRank()
	{
		return $this->total_rank;
	}

	/**
	 * Get the [rank] column value.
	 * 
	 * @return     int
	 */
	public function getRank()
	{
		return $this->rank;
	}

	/**
	 * Get the [tags] column value.
	 * 
	 * @return     string
	 */
	public function getTags()
	{
		return $this->tags;
	}

	/**
	 * Get the [anonymous] column value.
	 * 
	 * @return     int
	 */
	public function getAnonymous()
	{
		return $this->anonymous;
	}

	/**
	 * Get the [status] column value.
	 * 
	 * @return     int
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * Get the [source] column value.
	 * 
	 * @return     int
	 */
	public function getSource()
	{
		return $this->source;
	}

	/**
	 * Get the [source_id] column value.
	 * 
	 * @return     string
	 */
	public function getSourceId()
	{
		return $this->source_id;
	}

	/**
	 * Get the [source_link] column value.
	 * 
	 * @return     string
	 */
	public function getSourceLink()
	{
		return $this->source_link;
	}

	/**
	 * Get the [license_type] column value.
	 * 
	 * @return     int
	 */
	public function getLicenseType()
	{
		return $this->license_type;
	}

	/**
	 * Get the [credit] column value.
	 * 
	 * @return     string
	 */
	public function getCredit()
	{
		return $this->credit;
	}

	/**
	 * Get the [length_in_msecs] column value.
	 * 
	 * @return     int
	 */
	public function getLengthInMsecs()
	{
		return $this->length_in_msecs;
	}

	/**
	 * Get the [optionally formatted] temporal [created_at] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getCreatedAt($format = 'Y-m-d H:i:s')
	{
		if ($this->created_at === null) {
			return null;
		}


		if ($this->created_at === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->created_at);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->created_at, true), $x);
			}
		}

		if ($format === null) {
			// We cast here to maintain BC in API; obviously we will lose data if we're dealing with pre-/post-epoch dates.
			return (int) $dt->format('U');
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $dt->format('U'));
		} else {
			return $dt->format($format);
		}
	}

	/**
	 * Get the [optionally formatted] temporal [updated_at] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getUpdatedAt($format = 'Y-m-d H:i:s')
	{
		if ($this->updated_at === null) {
			return null;
		}


		if ($this->updated_at === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->updated_at);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->updated_at, true), $x);
			}
		}

		if ($format === null) {
			// We cast here to maintain BC in API; obviously we will lose data if we're dealing with pre-/post-epoch dates.
			return (int) $dt->format('U');
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $dt->format('U'));
		} else {
			return $dt->format($format);
		}
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
	 * Get the [display_in_search] column value.
	 * 
	 * @return     int
	 */
	public function getDisplayInSearch()
	{
		return $this->display_in_search;
	}

	/**
	 * Get the [subp_id] column value.
	 * 
	 * @return     int
	 */
	public function getSubpId()
	{
		return $this->subp_id;
	}

	/**
	 * Get the [custom_data] column value.
	 * 
	 * @return     string
	 */
	public function getCustomData()
	{
		return $this->custom_data;
	}

	/**
	 * Get the [search_text] column value.
	 * 
	 * @return     string
	 */
	public function getSearchText()
	{
		return $this->search_text;
	}

	/**
	 * Get the [screen_name] column value.
	 * 
	 * @return     string
	 */
	public function getScreenName()
	{
		return $this->screen_name;
	}

	/**
	 * Get the [site_url] column value.
	 * 
	 * @return     string
	 */
	public function getSiteUrl()
	{
		return $this->site_url;
	}

	/**
	 * Get the [permissions] column value.
	 * 
	 * @return     int
	 */
	public function getPermissions()
	{
		return $this->permissions;
	}

	/**
	 * Get the [group_id] column value.
	 * 
	 * @return     string
	 */
	public function getGroupId()
	{
		return $this->group_id;
	}

	/**
	 * Get the [plays] column value.
	 * 
	 * @return     int
	 */
	public function getPlays()
	{
		return $this->plays;
	}

	/**
	 * Get the [partner_data] column value.
	 * 
	 * @return     string
	 */
	public function getPartnerData()
	{
		return $this->partner_data;
	}

	/**
	 * Get the [int_id] column value.
	 * 
	 * @return     int
	 */
	public function getIntId()
	{
		return $this->int_id;
	}

	/**
	 * Get the [indexed_custom_data_1] column value.
	 * 
	 * @return     int
	 */
	public function getIndexedCustomData1()
	{
		return $this->indexed_custom_data_1;
	}

	/**
	 * Get the [description] column value.
	 * 
	 * @return     string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * Get the [optionally formatted] temporal [media_date] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getMediaDate($format = 'Y-m-d H:i:s')
	{
		if ($this->media_date === null) {
			return null;
		}


		if ($this->media_date === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->media_date);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->media_date, true), $x);
			}
		}

		if ($format === null) {
			// We cast here to maintain BC in API; obviously we will lose data if we're dealing with pre-/post-epoch dates.
			return (int) $dt->format('U');
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $dt->format('U'));
		} else {
			return $dt->format($format);
		}
	}

	/**
	 * Get the [admin_tags] column value.
	 * 
	 * @return     string
	 */
	public function getAdminTags()
	{
		return $this->admin_tags;
	}

	/**
	 * Get the [moderation_status] column value.
	 * 
	 * @return     int
	 */
	public function getModerationStatus()
	{
		return $this->moderation_status;
	}

	/**
	 * Get the [moderation_count] column value.
	 * 
	 * @return     int
	 */
	public function getModerationCount()
	{
		return $this->moderation_count;
	}

	/**
	 * Get the [optionally formatted] temporal [modified_at] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getModifiedAt($format = 'Y-m-d H:i:s')
	{
		if ($this->modified_at === null) {
			return null;
		}


		if ($this->modified_at === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->modified_at);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->modified_at, true), $x);
			}
		}

		if ($format === null) {
			// We cast here to maintain BC in API; obviously we will lose data if we're dealing with pre-/post-epoch dates.
			return (int) $dt->format('U');
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $dt->format('U'));
		} else {
			return $dt->format($format);
		}
	}

	/**
	 * Get the [puser_id] column value.
	 * 
	 * @return     string
	 */
	public function getPuserId()
	{
		return $this->puser_id;
	}

	/**
	 * Get the [access_control_id] column value.
	 * 
	 * @return     int
	 */
	public function getAccessControlId()
	{
		return $this->access_control_id;
	}

	/**
	 * Get the [conversion_profile_id] column value.
	 * 
	 * @return     int
	 */
	public function getConversionProfileId()
	{
		return $this->conversion_profile_id;
	}

	/**
	 * Get the [categories] column value.
	 * 
	 * @return     string
	 */
	public function getCategories()
	{
		return $this->categories;
	}

	/**
	 * Get the [categories_ids] column value.
	 * 
	 * @return     string
	 */
	public function getCategoriesIds()
	{
		return $this->categories_ids;
	}

	/**
	 * Get the [optionally formatted] temporal [start_date] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getStartDate($format = 'Y-m-d H:i:s')
	{
		if ($this->start_date === null) {
			return null;
		}


		if ($this->start_date === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->start_date);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->start_date, true), $x);
			}
		}

		if ($format === null) {
			// We cast here to maintain BC in API; obviously we will lose data if we're dealing with pre-/post-epoch dates.
			return (int) $dt->format('U');
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $dt->format('U'));
		} else {
			return $dt->format($format);
		}
	}

	/**
	 * Get the [optionally formatted] temporal [end_date] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getEndDate($format = 'Y-m-d H:i:s')
	{
		if ($this->end_date === null) {
			return null;
		}


		if ($this->end_date === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->end_date);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->end_date, true), $x);
			}
		}

		if ($format === null) {
			// We cast here to maintain BC in API; obviously we will lose data if we're dealing with pre-/post-epoch dates.
			return (int) $dt->format('U');
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $dt->format('U'));
		} else {
			return $dt->format($format);
		}
	}

	/**
	 * Get the [search_text_discrete] column value.
	 * 
	 * @return     string
	 */
	public function getSearchTextDiscrete()
	{
		return $this->search_text_discrete;
	}

	/**
	 * Get the [flavor_params_ids] column value.
	 * 
	 * @return     string
	 */
	public function getFlavorParamsIds()
	{
		return $this->flavor_params_ids;
	}

	/**
	 * Get the [optionally formatted] temporal [available_from] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getAvailableFrom($format = 'Y-m-d H:i:s')
	{
		if ($this->available_from === null) {
			return null;
		}


		if ($this->available_from === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->available_from);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->available_from, true), $x);
			}
		}

		if ($format === null) {
			// We cast here to maintain BC in API; obviously we will lose data if we're dealing with pre-/post-epoch dates.
			return (int) $dt->format('U');
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $dt->format('U'));
		} else {
			return $dt->format($format);
		}
	}

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      string $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::ID]))
			$this->oldColumnsValues[entryPeer::ID] = $this->id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = entryPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [kshow_id] column.
	 * 
	 * @param      string $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setKshowId($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::KSHOW_ID]))
			$this->oldColumnsValues[entryPeer::KSHOW_ID] = $this->kshow_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->kshow_id !== $v) {
			$this->kshow_id = $v;
			$this->modifiedColumns[] = entryPeer::KSHOW_ID;
		}

		if ($this->akshow !== null && $this->akshow->getId() !== $v) {
			$this->akshow = null;
		}

		return $this;
	} // setKshowId()

	/**
	 * Set the value of [kuser_id] column.
	 * 
	 * @param      int $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setKuserId($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::KUSER_ID]))
			$this->oldColumnsValues[entryPeer::KUSER_ID] = $this->kuser_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->kuser_id !== $v) {
			$this->kuser_id = $v;
			$this->modifiedColumns[] = entryPeer::KUSER_ID;
		}

		if ($this->akuser !== null && $this->akuser->getId() !== $v) {
			$this->akuser = null;
		}

		return $this;
	} // setKuserId()

	/**
	 * Set the value of [name] column.
	 * 
	 * @param      string $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setName($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::NAME]))
			$this->oldColumnsValues[entryPeer::NAME] = $this->name;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->name !== $v) {
			$this->name = $v;
			$this->modifiedColumns[] = entryPeer::NAME;
		}

		return $this;
	} // setName()

	/**
	 * Set the value of [type] column.
	 * 
	 * @param      int $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setType($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::TYPE]))
			$this->oldColumnsValues[entryPeer::TYPE] = $this->type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->type !== $v) {
			$this->type = $v;
			$this->modifiedColumns[] = entryPeer::TYPE;
		}

		return $this;
	} // setType()

	/**
	 * Set the value of [media_type] column.
	 * 
	 * @param      int $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setMediaType($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::MEDIA_TYPE]))
			$this->oldColumnsValues[entryPeer::MEDIA_TYPE] = $this->media_type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->media_type !== $v) {
			$this->media_type = $v;
			$this->modifiedColumns[] = entryPeer::MEDIA_TYPE;
		}

		return $this;
	} // setMediaType()

	/**
	 * Set the value of [data] column.
	 * 
	 * @param      string $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setData($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::DATA]))
			$this->oldColumnsValues[entryPeer::DATA] = $this->data;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->data !== $v) {
			$this->data = $v;
			$this->modifiedColumns[] = entryPeer::DATA;
		}

		return $this;
	} // setData()

	/**
	 * Set the value of [thumbnail] column.
	 * 
	 * @param      string $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setThumbnail($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::THUMBNAIL]))
			$this->oldColumnsValues[entryPeer::THUMBNAIL] = $this->thumbnail;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->thumbnail !== $v) {
			$this->thumbnail = $v;
			$this->modifiedColumns[] = entryPeer::THUMBNAIL;
		}

		return $this;
	} // setThumbnail()

	/**
	 * Set the value of [views] column.
	 * 
	 * @param      int $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setViews($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::VIEWS]))
			$this->oldColumnsValues[entryPeer::VIEWS] = $this->views;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->views !== $v || $this->isNew()) {
			$this->views = $v;
			$this->modifiedColumns[] = entryPeer::VIEWS;
		}

		return $this;
	} // setViews()

	/**
	 * Set the value of [votes] column.
	 * 
	 * @param      int $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setVotes($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::VOTES]))
			$this->oldColumnsValues[entryPeer::VOTES] = $this->votes;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->votes !== $v || $this->isNew()) {
			$this->votes = $v;
			$this->modifiedColumns[] = entryPeer::VOTES;
		}

		return $this;
	} // setVotes()

	/**
	 * Set the value of [comments] column.
	 * 
	 * @param      int $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setComments($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::COMMENTS]))
			$this->oldColumnsValues[entryPeer::COMMENTS] = $this->comments;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->comments !== $v || $this->isNew()) {
			$this->comments = $v;
			$this->modifiedColumns[] = entryPeer::COMMENTS;
		}

		return $this;
	} // setComments()

	/**
	 * Set the value of [favorites] column.
	 * 
	 * @param      int $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setFavorites($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::FAVORITES]))
			$this->oldColumnsValues[entryPeer::FAVORITES] = $this->favorites;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->favorites !== $v || $this->isNew()) {
			$this->favorites = $v;
			$this->modifiedColumns[] = entryPeer::FAVORITES;
		}

		return $this;
	} // setFavorites()

	/**
	 * Set the value of [total_rank] column.
	 * 
	 * @param      int $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setTotalRank($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::TOTAL_RANK]))
			$this->oldColumnsValues[entryPeer::TOTAL_RANK] = $this->total_rank;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->total_rank !== $v || $this->isNew()) {
			$this->total_rank = $v;
			$this->modifiedColumns[] = entryPeer::TOTAL_RANK;
		}

		return $this;
	} // setTotalRank()

	/**
	 * Set the value of [rank] column.
	 * 
	 * @param      int $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setRank($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::RANK]))
			$this->oldColumnsValues[entryPeer::RANK] = $this->rank;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->rank !== $v || $this->isNew()) {
			$this->rank = $v;
			$this->modifiedColumns[] = entryPeer::RANK;
		}

		return $this;
	} // setRank()

	/**
	 * Set the value of [tags] column.
	 * 
	 * @param      string $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setTags($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::TAGS]))
			$this->oldColumnsValues[entryPeer::TAGS] = $this->tags;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->tags !== $v) {
			$this->tags = $v;
			$this->modifiedColumns[] = entryPeer::TAGS;
		}

		return $this;
	} // setTags()

	/**
	 * Set the value of [anonymous] column.
	 * 
	 * @param      int $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setAnonymous($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::ANONYMOUS]))
			$this->oldColumnsValues[entryPeer::ANONYMOUS] = $this->anonymous;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->anonymous !== $v) {
			$this->anonymous = $v;
			$this->modifiedColumns[] = entryPeer::ANONYMOUS;
		}

		return $this;
	} // setAnonymous()

	/**
	 * Set the value of [status] column.
	 * 
	 * @param      int $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setStatus($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::STATUS]))
			$this->oldColumnsValues[entryPeer::STATUS] = $this->status;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->status !== $v) {
			$this->status = $v;
			$this->modifiedColumns[] = entryPeer::STATUS;
		}

		return $this;
	} // setStatus()

	/**
	 * Set the value of [source] column.
	 * 
	 * @param      int $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setSource($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::SOURCE]))
			$this->oldColumnsValues[entryPeer::SOURCE] = $this->source;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->source !== $v) {
			$this->source = $v;
			$this->modifiedColumns[] = entryPeer::SOURCE;
		}

		return $this;
	} // setSource()

	/**
	 * Set the value of [source_id] column.
	 * 
	 * @param      string $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setSourceId($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::SOURCE_ID]))
			$this->oldColumnsValues[entryPeer::SOURCE_ID] = $this->source_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->source_id !== $v) {
			$this->source_id = $v;
			$this->modifiedColumns[] = entryPeer::SOURCE_ID;
		}

		return $this;
	} // setSourceId()

	/**
	 * Set the value of [source_link] column.
	 * 
	 * @param      string $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setSourceLink($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::SOURCE_LINK]))
			$this->oldColumnsValues[entryPeer::SOURCE_LINK] = $this->source_link;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->source_link !== $v) {
			$this->source_link = $v;
			$this->modifiedColumns[] = entryPeer::SOURCE_LINK;
		}

		return $this;
	} // setSourceLink()

	/**
	 * Set the value of [license_type] column.
	 * 
	 * @param      int $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setLicenseType($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::LICENSE_TYPE]))
			$this->oldColumnsValues[entryPeer::LICENSE_TYPE] = $this->license_type;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->license_type !== $v) {
			$this->license_type = $v;
			$this->modifiedColumns[] = entryPeer::LICENSE_TYPE;
		}

		return $this;
	} // setLicenseType()

	/**
	 * Set the value of [credit] column.
	 * 
	 * @param      string $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setCredit($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::CREDIT]))
			$this->oldColumnsValues[entryPeer::CREDIT] = $this->credit;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->credit !== $v) {
			$this->credit = $v;
			$this->modifiedColumns[] = entryPeer::CREDIT;
		}

		return $this;
	} // setCredit()

	/**
	 * Set the value of [length_in_msecs] column.
	 * 
	 * @param      int $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setLengthInMsecs($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::LENGTH_IN_MSECS]))
			$this->oldColumnsValues[entryPeer::LENGTH_IN_MSECS] = $this->length_in_msecs;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->length_in_msecs !== $v || $this->isNew()) {
			$this->length_in_msecs = $v;
			$this->modifiedColumns[] = entryPeer::LENGTH_IN_MSECS;
		}

		return $this;
	} // setLengthInMsecs()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     entry The current object (for fluent API support)
	 */
	public function setCreatedAt($v)
	{
		// we treat '' as NULL for temporal objects because DateTime('') == DateTime('now')
		// -- which is unexpected, to say the least.
		if ($v === null || $v === '') {
			$dt = null;
		} elseif ($v instanceof DateTime) {
			$dt = $v;
		} else {
			// some string/numeric value passed; we normalize that so that we can
			// validate it.
			try {
				if (is_numeric($v)) { // if it's a unix timestamp
					$dt = new DateTime('@'.$v, new DateTimeZone('UTC'));
					// We have to explicitly specify and then change the time zone because of a
					// DateTime bug: http://bugs.php.net/bug.php?id=43003
					$dt->setTimeZone(new DateTimeZone(date_default_timezone_get()));
				} else {
					$dt = new DateTime($v);
				}
			} catch (Exception $x) {
				throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
			}
		}

		if ( $this->created_at !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->created_at !== null && $tmpDt = new DateTime($this->created_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->created_at = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = entryPeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     entry The current object (for fluent API support)
	 */
	public function setUpdatedAt($v)
	{
		// we treat '' as NULL for temporal objects because DateTime('') == DateTime('now')
		// -- which is unexpected, to say the least.
		if ($v === null || $v === '') {
			$dt = null;
		} elseif ($v instanceof DateTime) {
			$dt = $v;
		} else {
			// some string/numeric value passed; we normalize that so that we can
			// validate it.
			try {
				if (is_numeric($v)) { // if it's a unix timestamp
					$dt = new DateTime('@'.$v, new DateTimeZone('UTC'));
					// We have to explicitly specify and then change the time zone because of a
					// DateTime bug: http://bugs.php.net/bug.php?id=43003
					$dt->setTimeZone(new DateTimeZone(date_default_timezone_get()));
				} else {
					$dt = new DateTime($v);
				}
			} catch (Exception $x) {
				throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
			}
		}

		if ( $this->updated_at !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->updated_at !== null && $tmpDt = new DateTime($this->updated_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->updated_at = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = entryPeer::UPDATED_AT;
			}
		} // if either are not null

		return $this;
	} // setUpdatedAt()

	/**
	 * Set the value of [partner_id] column.
	 * 
	 * @param      int $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setPartnerId($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::PARTNER_ID]))
			$this->oldColumnsValues[entryPeer::PARTNER_ID] = $this->partner_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->partner_id !== $v || $this->isNew()) {
			$this->partner_id = $v;
			$this->modifiedColumns[] = entryPeer::PARTNER_ID;
		}

		return $this;
	} // setPartnerId()

	/**
	 * Set the value of [display_in_search] column.
	 * 
	 * @param      int $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setDisplayInSearch($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::DISPLAY_IN_SEARCH]))
			$this->oldColumnsValues[entryPeer::DISPLAY_IN_SEARCH] = $this->display_in_search;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->display_in_search !== $v) {
			$this->display_in_search = $v;
			$this->modifiedColumns[] = entryPeer::DISPLAY_IN_SEARCH;
		}

		return $this;
	} // setDisplayInSearch()

	/**
	 * Set the value of [subp_id] column.
	 * 
	 * @param      int $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setSubpId($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::SUBP_ID]))
			$this->oldColumnsValues[entryPeer::SUBP_ID] = $this->subp_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->subp_id !== $v || $this->isNew()) {
			$this->subp_id = $v;
			$this->modifiedColumns[] = entryPeer::SUBP_ID;
		}

		return $this;
	} // setSubpId()

	/**
	 * Set the value of [custom_data] column.
	 * 
	 * @param      string $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setCustomData($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->custom_data !== $v) {
			$this->custom_data = $v;
			$this->modifiedColumns[] = entryPeer::CUSTOM_DATA;
		}

		return $this;
	} // setCustomData()

	/**
	 * Set the value of [search_text] column.
	 * 
	 * @param      string $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setSearchText($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::SEARCH_TEXT]))
			$this->oldColumnsValues[entryPeer::SEARCH_TEXT] = $this->search_text;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->search_text !== $v) {
			$this->search_text = $v;
			$this->modifiedColumns[] = entryPeer::SEARCH_TEXT;
		}

		return $this;
	} // setSearchText()

	/**
	 * Set the value of [screen_name] column.
	 * 
	 * @param      string $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setScreenName($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::SCREEN_NAME]))
			$this->oldColumnsValues[entryPeer::SCREEN_NAME] = $this->screen_name;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->screen_name !== $v) {
			$this->screen_name = $v;
			$this->modifiedColumns[] = entryPeer::SCREEN_NAME;
		}

		return $this;
	} // setScreenName()

	/**
	 * Set the value of [site_url] column.
	 * 
	 * @param      string $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setSiteUrl($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::SITE_URL]))
			$this->oldColumnsValues[entryPeer::SITE_URL] = $this->site_url;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->site_url !== $v) {
			$this->site_url = $v;
			$this->modifiedColumns[] = entryPeer::SITE_URL;
		}

		return $this;
	} // setSiteUrl()

	/**
	 * Set the value of [permissions] column.
	 * 
	 * @param      int $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setPermissions($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::PERMISSIONS]))
			$this->oldColumnsValues[entryPeer::PERMISSIONS] = $this->permissions;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->permissions !== $v || $this->isNew()) {
			$this->permissions = $v;
			$this->modifiedColumns[] = entryPeer::PERMISSIONS;
		}

		return $this;
	} // setPermissions()

	/**
	 * Set the value of [group_id] column.
	 * 
	 * @param      string $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setGroupId($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::GROUP_ID]))
			$this->oldColumnsValues[entryPeer::GROUP_ID] = $this->group_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->group_id !== $v) {
			$this->group_id = $v;
			$this->modifiedColumns[] = entryPeer::GROUP_ID;
		}

		return $this;
	} // setGroupId()

	/**
	 * Set the value of [plays] column.
	 * 
	 * @param      int $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setPlays($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::PLAYS]))
			$this->oldColumnsValues[entryPeer::PLAYS] = $this->plays;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->plays !== $v || $this->isNew()) {
			$this->plays = $v;
			$this->modifiedColumns[] = entryPeer::PLAYS;
		}

		return $this;
	} // setPlays()

	/**
	 * Set the value of [partner_data] column.
	 * 
	 * @param      string $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setPartnerData($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::PARTNER_DATA]))
			$this->oldColumnsValues[entryPeer::PARTNER_DATA] = $this->partner_data;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->partner_data !== $v) {
			$this->partner_data = $v;
			$this->modifiedColumns[] = entryPeer::PARTNER_DATA;
		}

		return $this;
	} // setPartnerData()

	/**
	 * Set the value of [int_id] column.
	 * 
	 * @param      int $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setIntId($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::INT_ID]))
			$this->oldColumnsValues[entryPeer::INT_ID] = $this->int_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->int_id !== $v) {
			$this->int_id = $v;
			$this->modifiedColumns[] = entryPeer::INT_ID;
		}

		return $this;
	} // setIntId()

	/**
	 * Set the value of [indexed_custom_data_1] column.
	 * 
	 * @param      int $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setIndexedCustomData1($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::INDEXED_CUSTOM_DATA_1]))
			$this->oldColumnsValues[entryPeer::INDEXED_CUSTOM_DATA_1] = $this->indexed_custom_data_1;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->indexed_custom_data_1 !== $v) {
			$this->indexed_custom_data_1 = $v;
			$this->modifiedColumns[] = entryPeer::INDEXED_CUSTOM_DATA_1;
		}

		return $this;
	} // setIndexedCustomData1()

	/**
	 * Set the value of [description] column.
	 * 
	 * @param      string $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setDescription($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::DESCRIPTION]))
			$this->oldColumnsValues[entryPeer::DESCRIPTION] = $this->description;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->description !== $v) {
			$this->description = $v;
			$this->modifiedColumns[] = entryPeer::DESCRIPTION;
		}

		return $this;
	} // setDescription()

	/**
	 * Sets the value of [media_date] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     entry The current object (for fluent API support)
	 */
	public function setMediaDate($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::MEDIA_DATE]))
			$this->oldColumnsValues[entryPeer::MEDIA_DATE] = $this->media_date;

		// we treat '' as NULL for temporal objects because DateTime('') == DateTime('now')
		// -- which is unexpected, to say the least.
		if ($v === null || $v === '') {
			$dt = null;
		} elseif ($v instanceof DateTime) {
			$dt = $v;
		} else {
			// some string/numeric value passed; we normalize that so that we can
			// validate it.
			try {
				if (is_numeric($v)) { // if it's a unix timestamp
					$dt = new DateTime('@'.$v, new DateTimeZone('UTC'));
					// We have to explicitly specify and then change the time zone because of a
					// DateTime bug: http://bugs.php.net/bug.php?id=43003
					$dt->setTimeZone(new DateTimeZone(date_default_timezone_get()));
				} else {
					$dt = new DateTime($v);
				}
			} catch (Exception $x) {
				throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
			}
		}

		if ( $this->media_date !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->media_date !== null && $tmpDt = new DateTime($this->media_date)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->media_date = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = entryPeer::MEDIA_DATE;
			}
		} // if either are not null

		return $this;
	} // setMediaDate()

	/**
	 * Set the value of [admin_tags] column.
	 * 
	 * @param      string $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setAdminTags($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::ADMIN_TAGS]))
			$this->oldColumnsValues[entryPeer::ADMIN_TAGS] = $this->admin_tags;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->admin_tags !== $v) {
			$this->admin_tags = $v;
			$this->modifiedColumns[] = entryPeer::ADMIN_TAGS;
		}

		return $this;
	} // setAdminTags()

	/**
	 * Set the value of [moderation_status] column.
	 * 
	 * @param      int $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setModerationStatus($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::MODERATION_STATUS]))
			$this->oldColumnsValues[entryPeer::MODERATION_STATUS] = $this->moderation_status;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->moderation_status !== $v) {
			$this->moderation_status = $v;
			$this->modifiedColumns[] = entryPeer::MODERATION_STATUS;
		}

		return $this;
	} // setModerationStatus()

	/**
	 * Set the value of [moderation_count] column.
	 * 
	 * @param      int $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setModerationCount($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::MODERATION_COUNT]))
			$this->oldColumnsValues[entryPeer::MODERATION_COUNT] = $this->moderation_count;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->moderation_count !== $v) {
			$this->moderation_count = $v;
			$this->modifiedColumns[] = entryPeer::MODERATION_COUNT;
		}

		return $this;
	} // setModerationCount()

	/**
	 * Sets the value of [modified_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     entry The current object (for fluent API support)
	 */
	public function setModifiedAt($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::MODIFIED_AT]))
			$this->oldColumnsValues[entryPeer::MODIFIED_AT] = $this->modified_at;

		// we treat '' as NULL for temporal objects because DateTime('') == DateTime('now')
		// -- which is unexpected, to say the least.
		if ($v === null || $v === '') {
			$dt = null;
		} elseif ($v instanceof DateTime) {
			$dt = $v;
		} else {
			// some string/numeric value passed; we normalize that so that we can
			// validate it.
			try {
				if (is_numeric($v)) { // if it's a unix timestamp
					$dt = new DateTime('@'.$v, new DateTimeZone('UTC'));
					// We have to explicitly specify and then change the time zone because of a
					// DateTime bug: http://bugs.php.net/bug.php?id=43003
					$dt->setTimeZone(new DateTimeZone(date_default_timezone_get()));
				} else {
					$dt = new DateTime($v);
				}
			} catch (Exception $x) {
				throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
			}
		}

		if ( $this->modified_at !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->modified_at !== null && $tmpDt = new DateTime($this->modified_at)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->modified_at = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = entryPeer::MODIFIED_AT;
			}
		} // if either are not null

		return $this;
	} // setModifiedAt()

	/**
	 * Set the value of [puser_id] column.
	 * 
	 * @param      string $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setPuserId($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::PUSER_ID]))
			$this->oldColumnsValues[entryPeer::PUSER_ID] = $this->puser_id;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->puser_id !== $v) {
			$this->puser_id = $v;
			$this->modifiedColumns[] = entryPeer::PUSER_ID;
		}

		return $this;
	} // setPuserId()

	/**
	 * Set the value of [access_control_id] column.
	 * 
	 * @param      int $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setAccessControlId($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::ACCESS_CONTROL_ID]))
			$this->oldColumnsValues[entryPeer::ACCESS_CONTROL_ID] = $this->access_control_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->access_control_id !== $v) {
			$this->access_control_id = $v;
			$this->modifiedColumns[] = entryPeer::ACCESS_CONTROL_ID;
		}

		if ($this->aaccessControl !== null && $this->aaccessControl->getId() !== $v) {
			$this->aaccessControl = null;
		}

		return $this;
	} // setAccessControlId()

	/**
	 * Set the value of [conversion_profile_id] column.
	 * 
	 * @param      int $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setConversionProfileId($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::CONVERSION_PROFILE_ID]))
			$this->oldColumnsValues[entryPeer::CONVERSION_PROFILE_ID] = $this->conversion_profile_id;

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->conversion_profile_id !== $v) {
			$this->conversion_profile_id = $v;
			$this->modifiedColumns[] = entryPeer::CONVERSION_PROFILE_ID;
		}

		if ($this->aconversionProfile2 !== null && $this->aconversionProfile2->getId() !== $v) {
			$this->aconversionProfile2 = null;
		}

		return $this;
	} // setConversionProfileId()

	/**
	 * Set the value of [categories] column.
	 * 
	 * @param      string $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setCategories($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::CATEGORIES]))
			$this->oldColumnsValues[entryPeer::CATEGORIES] = $this->categories;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->categories !== $v) {
			$this->categories = $v;
			$this->modifiedColumns[] = entryPeer::CATEGORIES;
		}

		return $this;
	} // setCategories()

	/**
	 * Set the value of [categories_ids] column.
	 * 
	 * @param      string $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setCategoriesIds($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::CATEGORIES_IDS]))
			$this->oldColumnsValues[entryPeer::CATEGORIES_IDS] = $this->categories_ids;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->categories_ids !== $v) {
			$this->categories_ids = $v;
			$this->modifiedColumns[] = entryPeer::CATEGORIES_IDS;
		}

		return $this;
	} // setCategoriesIds()

	/**
	 * Sets the value of [start_date] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     entry The current object (for fluent API support)
	 */
	public function setStartDate($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::START_DATE]))
			$this->oldColumnsValues[entryPeer::START_DATE] = $this->start_date;

		// we treat '' as NULL for temporal objects because DateTime('') == DateTime('now')
		// -- which is unexpected, to say the least.
		if ($v === null || $v === '') {
			$dt = null;
		} elseif ($v instanceof DateTime) {
			$dt = $v;
		} else {
			// some string/numeric value passed; we normalize that so that we can
			// validate it.
			try {
				if (is_numeric($v)) { // if it's a unix timestamp
					$dt = new DateTime('@'.$v, new DateTimeZone('UTC'));
					// We have to explicitly specify and then change the time zone because of a
					// DateTime bug: http://bugs.php.net/bug.php?id=43003
					$dt->setTimeZone(new DateTimeZone(date_default_timezone_get()));
				} else {
					$dt = new DateTime($v);
				}
			} catch (Exception $x) {
				throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
			}
		}

		if ( $this->start_date !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->start_date !== null && $tmpDt = new DateTime($this->start_date)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->start_date = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = entryPeer::START_DATE;
			}
		} // if either are not null

		return $this;
	} // setStartDate()

	/**
	 * Sets the value of [end_date] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     entry The current object (for fluent API support)
	 */
	public function setEndDate($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::END_DATE]))
			$this->oldColumnsValues[entryPeer::END_DATE] = $this->end_date;

		// we treat '' as NULL for temporal objects because DateTime('') == DateTime('now')
		// -- which is unexpected, to say the least.
		if ($v === null || $v === '') {
			$dt = null;
		} elseif ($v instanceof DateTime) {
			$dt = $v;
		} else {
			// some string/numeric value passed; we normalize that so that we can
			// validate it.
			try {
				if (is_numeric($v)) { // if it's a unix timestamp
					$dt = new DateTime('@'.$v, new DateTimeZone('UTC'));
					// We have to explicitly specify and then change the time zone because of a
					// DateTime bug: http://bugs.php.net/bug.php?id=43003
					$dt->setTimeZone(new DateTimeZone(date_default_timezone_get()));
				} else {
					$dt = new DateTime($v);
				}
			} catch (Exception $x) {
				throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
			}
		}

		if ( $this->end_date !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->end_date !== null && $tmpDt = new DateTime($this->end_date)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->end_date = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = entryPeer::END_DATE;
			}
		} // if either are not null

		return $this;
	} // setEndDate()

	/**
	 * Set the value of [search_text_discrete] column.
	 * 
	 * @param      string $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setSearchTextDiscrete($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::SEARCH_TEXT_DISCRETE]))
			$this->oldColumnsValues[entryPeer::SEARCH_TEXT_DISCRETE] = $this->search_text_discrete;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->search_text_discrete !== $v) {
			$this->search_text_discrete = $v;
			$this->modifiedColumns[] = entryPeer::SEARCH_TEXT_DISCRETE;
		}

		return $this;
	} // setSearchTextDiscrete()

	/**
	 * Set the value of [flavor_params_ids] column.
	 * 
	 * @param      string $v new value
	 * @return     entry The current object (for fluent API support)
	 */
	public function setFlavorParamsIds($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::FLAVOR_PARAMS_IDS]))
			$this->oldColumnsValues[entryPeer::FLAVOR_PARAMS_IDS] = $this->flavor_params_ids;

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->flavor_params_ids !== $v) {
			$this->flavor_params_ids = $v;
			$this->modifiedColumns[] = entryPeer::FLAVOR_PARAMS_IDS;
		}

		return $this;
	} // setFlavorParamsIds()

	/**
	 * Sets the value of [available_from] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     entry The current object (for fluent API support)
	 */
	public function setAvailableFrom($v)
	{
		if(!isset($this->oldColumnsValues[entryPeer::AVAILABLE_FROM]))
			$this->oldColumnsValues[entryPeer::AVAILABLE_FROM] = $this->available_from;

		// we treat '' as NULL for temporal objects because DateTime('') == DateTime('now')
		// -- which is unexpected, to say the least.
		if ($v === null || $v === '') {
			$dt = null;
		} elseif ($v instanceof DateTime) {
			$dt = $v;
		} else {
			// some string/numeric value passed; we normalize that so that we can
			// validate it.
			try {
				if (is_numeric($v)) { // if it's a unix timestamp
					$dt = new DateTime('@'.$v, new DateTimeZone('UTC'));
					// We have to explicitly specify and then change the time zone because of a
					// DateTime bug: http://bugs.php.net/bug.php?id=43003
					$dt->setTimeZone(new DateTimeZone(date_default_timezone_get()));
				} else {
					$dt = new DateTime($v);
				}
			} catch (Exception $x) {
				throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
			}
		}

		if ( $this->available_from !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->available_from !== null && $tmpDt = new DateTime($this->available_from)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->available_from = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = entryPeer::AVAILABLE_FROM;
			}
		} // if either are not null

		return $this;
	} // setAvailableFrom()

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
			if ($this->views !== 0) {
				return false;
			}

			if ($this->votes !== 0) {
				return false;
			}

			if ($this->comments !== 0) {
				return false;
			}

			if ($this->favorites !== 0) {
				return false;
			}

			if ($this->total_rank !== 0) {
				return false;
			}

			if ($this->rank !== 0) {
				return false;
			}

			if ($this->length_in_msecs !== 0) {
				return false;
			}

			if ($this->partner_id !== 0) {
				return false;
			}

			if ($this->subp_id !== 0) {
				return false;
			}

			if ($this->permissions !== 1) {
				return false;
			}

			if ($this->plays !== 0) {
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

			$this->id = ($row[$startcol + 0] !== null) ? (string) $row[$startcol + 0] : null;
			$this->kshow_id = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->kuser_id = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
			$this->name = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->type = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
			$this->media_type = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
			$this->data = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->thumbnail = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->views = ($row[$startcol + 8] !== null) ? (int) $row[$startcol + 8] : null;
			$this->votes = ($row[$startcol + 9] !== null) ? (int) $row[$startcol + 9] : null;
			$this->comments = ($row[$startcol + 10] !== null) ? (int) $row[$startcol + 10] : null;
			$this->favorites = ($row[$startcol + 11] !== null) ? (int) $row[$startcol + 11] : null;
			$this->total_rank = ($row[$startcol + 12] !== null) ? (int) $row[$startcol + 12] : null;
			$this->rank = ($row[$startcol + 13] !== null) ? (int) $row[$startcol + 13] : null;
			$this->tags = ($row[$startcol + 14] !== null) ? (string) $row[$startcol + 14] : null;
			$this->anonymous = ($row[$startcol + 15] !== null) ? (int) $row[$startcol + 15] : null;
			$this->status = ($row[$startcol + 16] !== null) ? (int) $row[$startcol + 16] : null;
			$this->source = ($row[$startcol + 17] !== null) ? (int) $row[$startcol + 17] : null;
			$this->source_id = ($row[$startcol + 18] !== null) ? (string) $row[$startcol + 18] : null;
			$this->source_link = ($row[$startcol + 19] !== null) ? (string) $row[$startcol + 19] : null;
			$this->license_type = ($row[$startcol + 20] !== null) ? (int) $row[$startcol + 20] : null;
			$this->credit = ($row[$startcol + 21] !== null) ? (string) $row[$startcol + 21] : null;
			$this->length_in_msecs = ($row[$startcol + 22] !== null) ? (int) $row[$startcol + 22] : null;
			$this->created_at = ($row[$startcol + 23] !== null) ? (string) $row[$startcol + 23] : null;
			$this->updated_at = ($row[$startcol + 24] !== null) ? (string) $row[$startcol + 24] : null;
			$this->partner_id = ($row[$startcol + 25] !== null) ? (int) $row[$startcol + 25] : null;
			$this->display_in_search = ($row[$startcol + 26] !== null) ? (int) $row[$startcol + 26] : null;
			$this->subp_id = ($row[$startcol + 27] !== null) ? (int) $row[$startcol + 27] : null;
			$this->custom_data = ($row[$startcol + 28] !== null) ? (string) $row[$startcol + 28] : null;
			$this->search_text = ($row[$startcol + 29] !== null) ? (string) $row[$startcol + 29] : null;
			$this->screen_name = ($row[$startcol + 30] !== null) ? (string) $row[$startcol + 30] : null;
			$this->site_url = ($row[$startcol + 31] !== null) ? (string) $row[$startcol + 31] : null;
			$this->permissions = ($row[$startcol + 32] !== null) ? (int) $row[$startcol + 32] : null;
			$this->group_id = ($row[$startcol + 33] !== null) ? (string) $row[$startcol + 33] : null;
			$this->plays = ($row[$startcol + 34] !== null) ? (int) $row[$startcol + 34] : null;
			$this->partner_data = ($row[$startcol + 35] !== null) ? (string) $row[$startcol + 35] : null;
			$this->int_id = ($row[$startcol + 36] !== null) ? (int) $row[$startcol + 36] : null;
			$this->indexed_custom_data_1 = ($row[$startcol + 37] !== null) ? (int) $row[$startcol + 37] : null;
			$this->description = ($row[$startcol + 38] !== null) ? (string) $row[$startcol + 38] : null;
			$this->media_date = ($row[$startcol + 39] !== null) ? (string) $row[$startcol + 39] : null;
			$this->admin_tags = ($row[$startcol + 40] !== null) ? (string) $row[$startcol + 40] : null;
			$this->moderation_status = ($row[$startcol + 41] !== null) ? (int) $row[$startcol + 41] : null;
			$this->moderation_count = ($row[$startcol + 42] !== null) ? (int) $row[$startcol + 42] : null;
			$this->modified_at = ($row[$startcol + 43] !== null) ? (string) $row[$startcol + 43] : null;
			$this->puser_id = ($row[$startcol + 44] !== null) ? (string) $row[$startcol + 44] : null;
			$this->access_control_id = ($row[$startcol + 45] !== null) ? (int) $row[$startcol + 45] : null;
			$this->conversion_profile_id = ($row[$startcol + 46] !== null) ? (int) $row[$startcol + 46] : null;
			$this->categories = ($row[$startcol + 47] !== null) ? (string) $row[$startcol + 47] : null;
			$this->categories_ids = ($row[$startcol + 48] !== null) ? (string) $row[$startcol + 48] : null;
			$this->start_date = ($row[$startcol + 49] !== null) ? (string) $row[$startcol + 49] : null;
			$this->end_date = ($row[$startcol + 50] !== null) ? (string) $row[$startcol + 50] : null;
			$this->search_text_discrete = ($row[$startcol + 51] !== null) ? (string) $row[$startcol + 51] : null;
			$this->flavor_params_ids = ($row[$startcol + 52] !== null) ? (string) $row[$startcol + 52] : null;
			$this->available_from = ($row[$startcol + 53] !== null) ? (string) $row[$startcol + 53] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 54; // 54 = entryPeer::NUM_COLUMNS - entryPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating entry object", $e);
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

		if ($this->akshow !== null && $this->kshow_id !== $this->akshow->getId()) {
			$this->akshow = null;
		}
		if ($this->akuser !== null && $this->kuser_id !== $this->akuser->getId()) {
			$this->akuser = null;
		}
		if ($this->aaccessControl !== null && $this->access_control_id !== $this->aaccessControl->getId()) {
			$this->aaccessControl = null;
		}
		if ($this->aconversionProfile2 !== null && $this->conversion_profile_id !== $this->aconversionProfile2->getId()) {
			$this->aconversionProfile2 = null;
		}
	} // ensureConsistency

	/**
	 * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
	 *
	 * This will only work if the object has been saved and has a valid primary key set.
	 *
	 * @param      boolean $deep (optional) Whether to also de-associated any related objects.
	 * @param      PropelPDO $con (optional) The PropelPDO connection to use.
	 * @return     void
	 * @throws     PropelException - if this object is deleted, unsaved or doesn't have pk match in db
	 */
	public function reload($deep = false, PropelPDO $con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("Cannot reload a deleted object.");
		}

		if ($this->isNew()) {
			throw new PropelException("Cannot reload an unsaved object.");
		}

		if ($con === null) {
			$con = Propel::getConnection(entryPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = entryPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->akshow = null;
			$this->akuser = null;
			$this->aaccessControl = null;
			$this->aconversionProfile2 = null;
			$this->collkvotes = null;
			$this->lastkvoteCriteria = null;

			$this->collconversions = null;
			$this->lastconversionCriteria = null;

			$this->collWidgetLogs = null;
			$this->lastWidgetLogCriteria = null;

			$this->collmoderationFlags = null;
			$this->lastmoderationFlagCriteria = null;

			$this->collroughcutEntrysRelatedByRoughcutId = null;
			$this->lastroughcutEntryRelatedByRoughcutIdCriteria = null;

			$this->collroughcutEntrysRelatedByEntryId = null;
			$this->lastroughcutEntryRelatedByEntryIdCriteria = null;

			$this->collwidgets = null;
			$this->lastwidgetCriteria = null;

			$this->collflavorParamsOutputs = null;
			$this->lastflavorParamsOutputCriteria = null;

			$this->collflavorAssets = null;
			$this->lastflavorAssetCriteria = null;

		} // if (deep)
	}

	/**
	 * Removes this object from datastore and sets delete attribute.
	 *
	 * @param      PropelPDO $con
	 * @return     void
	 * @throws     PropelException
	 * @see        BaseObject::setDeleted()
	 * @see        BaseObject::isDeleted()
	 */
	public function delete(PropelPDO $con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("This object has already been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(entryPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				entryPeer::doDelete($this, $con);
				$this->postDelete($con);
				$this->setDeleted(true);
				$con->commit();
			} else {
				$con->commit();
			}
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Persists this object to the database.
	 *
	 * If the object is new, it inserts it; otherwise an update is performed.
	 * All modified related objects will also be persisted in the doSave()
	 * method.  This method wraps all precipitate database operations in a
	 * single transaction.
	 *
	 * @param      PropelPDO $con
	 * @return     int The number of rows affected by this insert/update and any referring fk objects' save() operations.
	 * @throws     PropelException
	 * @see        doSave()
	 */
	public function save(PropelPDO $con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("You cannot save an object that has been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(entryPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		$isInsert = $this->isNew();
		try {
			$ret = $this->preSave($con);
			if ($isInsert) {
				$ret = $ret && $this->preInsert($con);
			} else {
				$ret = $ret && $this->preUpdate($con);
			}
			if ($ret) {
				$affectedRows = $this->doSave($con);
				if ($isInsert) {
					$this->postInsert($con);
				} else {
					$this->postUpdate($con);
				}
				$this->postSave($con);
				entryPeer::addInstanceToPool($this);
			} else {
				$affectedRows = 0;
			}
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Performs the work of inserting or updating the row in the database.
	 *
	 * If the object is new, it inserts it; otherwise an update is performed.
	 * All related objects are also updated in this method.
	 *
	 * @param      PropelPDO $con
	 * @return     int The number of rows affected by this insert/update and any referring fk objects' save() operations.
	 * @throws     PropelException
	 * @see        save()
	 */
	protected function doSave(PropelPDO $con)
	{
		$affectedRows = 0; // initialize var to track total num of affected rows
		if (!$this->alreadyInSave) {
			$this->alreadyInSave = true;

			// We call the save method on the following object(s) if they
			// were passed to this object by their coresponding set
			// method.  This object relates to these object(s) by a
			// foreign key reference.

			if ($this->akshow !== null) {
				if ($this->akshow->isModified() || $this->akshow->isNew()) {
					$affectedRows += $this->akshow->save($con);
				}
				$this->setkshow($this->akshow);
			}

			if ($this->akuser !== null) {
				if ($this->akuser->isModified() || $this->akuser->isNew()) {
					$affectedRows += $this->akuser->save($con);
				}
				$this->setkuser($this->akuser);
			}

			if ($this->aaccessControl !== null) {
				if ($this->aaccessControl->isModified() || $this->aaccessControl->isNew()) {
					$affectedRows += $this->aaccessControl->save($con);
				}
				$this->setaccessControl($this->aaccessControl);
			}

			if ($this->aconversionProfile2 !== null) {
				if ($this->aconversionProfile2->isModified() || $this->aconversionProfile2->isNew()) {
					$affectedRows += $this->aconversionProfile2->save($con);
				}
				$this->setconversionProfile2($this->aconversionProfile2);
			}


			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = entryPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setNew(false);
				} else {
					$affectedRows += entryPeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collkvotes !== null) {
				foreach ($this->collkvotes as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collconversions !== null) {
				foreach ($this->collconversions as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collWidgetLogs !== null) {
				foreach ($this->collWidgetLogs as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collmoderationFlags !== null) {
				foreach ($this->collmoderationFlags as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collroughcutEntrysRelatedByRoughcutId !== null) {
				foreach ($this->collroughcutEntrysRelatedByRoughcutId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collroughcutEntrysRelatedByEntryId !== null) {
				foreach ($this->collroughcutEntrysRelatedByEntryId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collwidgets !== null) {
				foreach ($this->collwidgets as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collflavorParamsOutputs !== null) {
				foreach ($this->collflavorParamsOutputs as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collflavorAssets !== null) {
				foreach ($this->collflavorAssets as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			$this->alreadyInSave = false;

		}
		return $affectedRows;
	} // doSave()

	/**
	 * Code to be run before persisting the object
	 * @param PropelPDO $con
	 * @return bloolean
	 */
	public function preSave(PropelPDO $con = null)
	{
		$this->setCustomDataObj();
    	
		return parent::preSave($con);
	}

	/**
	 * Code to be run after persisting the object
	 * @param PropelPDO $con
	 */
	public function postSave(PropelPDO $con = null) 
	{
		$this->oldColumnsValues = array();
		$this->oldCustomDataValues = array();
    	 
	}
	
	/**
	 * Code to be run before inserting to database
	 * @param PropelPDO $con
	 * @return boolean
	 */
	public function preInsert(PropelPDO $con = null)
	{
    	$this->setCreatedAt(time());
    	
		$this->setUpdatedAt(time());
		return true;
	}
	
	/**
	 * Code to be run after inserting to database
	 * @param PropelPDO $con 
	 */
	public function postInsert(PropelPDO $con = null)
	{
		entryPeer::setUseCriteriaFilter(false);
		$this->reload();
		entryPeer::setUseCriteriaFilter(true);
		
		kEventsManager::raiseEvent(new kObjectCreatedEvent($this));
		
		if($this->copiedFrom)
			kEventsManager::raiseEvent(new kObjectCopiedEvent($this->copiedFrom, $this));
	}

	/**
	 * Code to be run before updating the object in database
	 * @param PropelPDO $con
	 * @return boolean
	 */
	public function preUpdate(PropelPDO $con = null)
	{
		if($this->isModified())
		{
			$this->setUpdatedAt(time());
			kEventsManager::raiseEvent(new kObjectChangedEvent($this, $this->modifiedColumns));
		}
		return true;
	}
	
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


			// We call the validate method on the following object(s) if they
			// were passed to this object by their coresponding set
			// method.  This object relates to these object(s) by a
			// foreign key reference.

			if ($this->akshow !== null) {
				if (!$this->akshow->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->akshow->getValidationFailures());
				}
			}

			if ($this->akuser !== null) {
				if (!$this->akuser->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->akuser->getValidationFailures());
				}
			}

			if ($this->aaccessControl !== null) {
				if (!$this->aaccessControl->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aaccessControl->getValidationFailures());
				}
			}

			if ($this->aconversionProfile2 !== null) {
				if (!$this->aconversionProfile2->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aconversionProfile2->getValidationFailures());
				}
			}


			if (($retval = entryPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collkvotes !== null) {
					foreach ($this->collkvotes as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collconversions !== null) {
					foreach ($this->collconversions as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collWidgetLogs !== null) {
					foreach ($this->collWidgetLogs as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collmoderationFlags !== null) {
					foreach ($this->collmoderationFlags as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collroughcutEntrysRelatedByRoughcutId !== null) {
					foreach ($this->collroughcutEntrysRelatedByRoughcutId as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collroughcutEntrysRelatedByEntryId !== null) {
					foreach ($this->collroughcutEntrysRelatedByEntryId as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collwidgets !== null) {
					foreach ($this->collwidgets as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collflavorParamsOutputs !== null) {
					foreach ($this->collflavorParamsOutputs as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collflavorAssets !== null) {
					foreach ($this->collflavorAssets as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
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
		$pos = entryPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getId();
				break;
			case 1:
				return $this->getKshowId();
				break;
			case 2:
				return $this->getKuserId();
				break;
			case 3:
				return $this->getName();
				break;
			case 4:
				return $this->getType();
				break;
			case 5:
				return $this->getMediaType();
				break;
			case 6:
				return $this->getData();
				break;
			case 7:
				return $this->getThumbnail();
				break;
			case 8:
				return $this->getViews();
				break;
			case 9:
				return $this->getVotes();
				break;
			case 10:
				return $this->getComments();
				break;
			case 11:
				return $this->getFavorites();
				break;
			case 12:
				return $this->getTotalRank();
				break;
			case 13:
				return $this->getRank();
				break;
			case 14:
				return $this->getTags();
				break;
			case 15:
				return $this->getAnonymous();
				break;
			case 16:
				return $this->getStatus();
				break;
			case 17:
				return $this->getSource();
				break;
			case 18:
				return $this->getSourceId();
				break;
			case 19:
				return $this->getSourceLink();
				break;
			case 20:
				return $this->getLicenseType();
				break;
			case 21:
				return $this->getCredit();
				break;
			case 22:
				return $this->getLengthInMsecs();
				break;
			case 23:
				return $this->getCreatedAt();
				break;
			case 24:
				return $this->getUpdatedAt();
				break;
			case 25:
				return $this->getPartnerId();
				break;
			case 26:
				return $this->getDisplayInSearch();
				break;
			case 27:
				return $this->getSubpId();
				break;
			case 28:
				return $this->getCustomData();
				break;
			case 29:
				return $this->getSearchText();
				break;
			case 30:
				return $this->getScreenName();
				break;
			case 31:
				return $this->getSiteUrl();
				break;
			case 32:
				return $this->getPermissions();
				break;
			case 33:
				return $this->getGroupId();
				break;
			case 34:
				return $this->getPlays();
				break;
			case 35:
				return $this->getPartnerData();
				break;
			case 36:
				return $this->getIntId();
				break;
			case 37:
				return $this->getIndexedCustomData1();
				break;
			case 38:
				return $this->getDescription();
				break;
			case 39:
				return $this->getMediaDate();
				break;
			case 40:
				return $this->getAdminTags();
				break;
			case 41:
				return $this->getModerationStatus();
				break;
			case 42:
				return $this->getModerationCount();
				break;
			case 43:
				return $this->getModifiedAt();
				break;
			case 44:
				return $this->getPuserId();
				break;
			case 45:
				return $this->getAccessControlId();
				break;
			case 46:
				return $this->getConversionProfileId();
				break;
			case 47:
				return $this->getCategories();
				break;
			case 48:
				return $this->getCategoriesIds();
				break;
			case 49:
				return $this->getStartDate();
				break;
			case 50:
				return $this->getEndDate();
				break;
			case 51:
				return $this->getSearchTextDiscrete();
				break;
			case 52:
				return $this->getFlavorParamsIds();
				break;
			case 53:
				return $this->getAvailableFrom();
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
		$keys = entryPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getKshowId(),
			$keys[2] => $this->getKuserId(),
			$keys[3] => $this->getName(),
			$keys[4] => $this->getType(),
			$keys[5] => $this->getMediaType(),
			$keys[6] => $this->getData(),
			$keys[7] => $this->getThumbnail(),
			$keys[8] => $this->getViews(),
			$keys[9] => $this->getVotes(),
			$keys[10] => $this->getComments(),
			$keys[11] => $this->getFavorites(),
			$keys[12] => $this->getTotalRank(),
			$keys[13] => $this->getRank(),
			$keys[14] => $this->getTags(),
			$keys[15] => $this->getAnonymous(),
			$keys[16] => $this->getStatus(),
			$keys[17] => $this->getSource(),
			$keys[18] => $this->getSourceId(),
			$keys[19] => $this->getSourceLink(),
			$keys[20] => $this->getLicenseType(),
			$keys[21] => $this->getCredit(),
			$keys[22] => $this->getLengthInMsecs(),
			$keys[23] => $this->getCreatedAt(),
			$keys[24] => $this->getUpdatedAt(),
			$keys[25] => $this->getPartnerId(),
			$keys[26] => $this->getDisplayInSearch(),
			$keys[27] => $this->getSubpId(),
			$keys[28] => $this->getCustomData(),
			$keys[29] => $this->getSearchText(),
			$keys[30] => $this->getScreenName(),
			$keys[31] => $this->getSiteUrl(),
			$keys[32] => $this->getPermissions(),
			$keys[33] => $this->getGroupId(),
			$keys[34] => $this->getPlays(),
			$keys[35] => $this->getPartnerData(),
			$keys[36] => $this->getIntId(),
			$keys[37] => $this->getIndexedCustomData1(),
			$keys[38] => $this->getDescription(),
			$keys[39] => $this->getMediaDate(),
			$keys[40] => $this->getAdminTags(),
			$keys[41] => $this->getModerationStatus(),
			$keys[42] => $this->getModerationCount(),
			$keys[43] => $this->getModifiedAt(),
			$keys[44] => $this->getPuserId(),
			$keys[45] => $this->getAccessControlId(),
			$keys[46] => $this->getConversionProfileId(),
			$keys[47] => $this->getCategories(),
			$keys[48] => $this->getCategoriesIds(),
			$keys[49] => $this->getStartDate(),
			$keys[50] => $this->getEndDate(),
			$keys[51] => $this->getSearchTextDiscrete(),
			$keys[52] => $this->getFlavorParamsIds(),
			$keys[53] => $this->getAvailableFrom(),
		);
		return $result;
	}

	/**
	 * Sets a field from the object by name passed in as a string.
	 *
	 * @param      string $name peer name
	 * @param      mixed $value field value
	 * @param      string $type The type of fieldname the $name is of:
	 *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @return     void
	 */
	public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = entryPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		return $this->setByPosition($pos, $value);
	}

	/**
	 * Sets a field from the object by Position as specified in the xml schema.
	 * Zero-based.
	 *
	 * @param      int $pos position in xml schema
	 * @param      mixed $value field value
	 * @return     void
	 */
	public function setByPosition($pos, $value)
	{
		switch($pos) {
			case 0:
				$this->setId($value);
				break;
			case 1:
				$this->setKshowId($value);
				break;
			case 2:
				$this->setKuserId($value);
				break;
			case 3:
				$this->setName($value);
				break;
			case 4:
				$this->setType($value);
				break;
			case 5:
				$this->setMediaType($value);
				break;
			case 6:
				$this->setData($value);
				break;
			case 7:
				$this->setThumbnail($value);
				break;
			case 8:
				$this->setViews($value);
				break;
			case 9:
				$this->setVotes($value);
				break;
			case 10:
				$this->setComments($value);
				break;
			case 11:
				$this->setFavorites($value);
				break;
			case 12:
				$this->setTotalRank($value);
				break;
			case 13:
				$this->setRank($value);
				break;
			case 14:
				$this->setTags($value);
				break;
			case 15:
				$this->setAnonymous($value);
				break;
			case 16:
				$this->setStatus($value);
				break;
			case 17:
				$this->setSource($value);
				break;
			case 18:
				$this->setSourceId($value);
				break;
			case 19:
				$this->setSourceLink($value);
				break;
			case 20:
				$this->setLicenseType($value);
				break;
			case 21:
				$this->setCredit($value);
				break;
			case 22:
				$this->setLengthInMsecs($value);
				break;
			case 23:
				$this->setCreatedAt($value);
				break;
			case 24:
				$this->setUpdatedAt($value);
				break;
			case 25:
				$this->setPartnerId($value);
				break;
			case 26:
				$this->setDisplayInSearch($value);
				break;
			case 27:
				$this->setSubpId($value);
				break;
			case 28:
				$this->setCustomData($value);
				break;
			case 29:
				$this->setSearchText($value);
				break;
			case 30:
				$this->setScreenName($value);
				break;
			case 31:
				$this->setSiteUrl($value);
				break;
			case 32:
				$this->setPermissions($value);
				break;
			case 33:
				$this->setGroupId($value);
				break;
			case 34:
				$this->setPlays($value);
				break;
			case 35:
				$this->setPartnerData($value);
				break;
			case 36:
				$this->setIntId($value);
				break;
			case 37:
				$this->setIndexedCustomData1($value);
				break;
			case 38:
				$this->setDescription($value);
				break;
			case 39:
				$this->setMediaDate($value);
				break;
			case 40:
				$this->setAdminTags($value);
				break;
			case 41:
				$this->setModerationStatus($value);
				break;
			case 42:
				$this->setModerationCount($value);
				break;
			case 43:
				$this->setModifiedAt($value);
				break;
			case 44:
				$this->setPuserId($value);
				break;
			case 45:
				$this->setAccessControlId($value);
				break;
			case 46:
				$this->setConversionProfileId($value);
				break;
			case 47:
				$this->setCategories($value);
				break;
			case 48:
				$this->setCategoriesIds($value);
				break;
			case 49:
				$this->setStartDate($value);
				break;
			case 50:
				$this->setEndDate($value);
				break;
			case 51:
				$this->setSearchTextDiscrete($value);
				break;
			case 52:
				$this->setFlavorParamsIds($value);
				break;
			case 53:
				$this->setAvailableFrom($value);
				break;
		} // switch()
	}

	/**
	 * Populates the object using an array.
	 *
	 * This is particularly useful when populating an object from one of the
	 * request arrays (e.g. $_POST).  This method goes through the column
	 * names, checking to see whether a matching key exists in populated
	 * array. If so the setByName() method is called for that column.
	 *
	 * You can specify the key type of the array by additionally passing one
	 * of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
	 * BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
	 * The default key type is the column's phpname (e.g. 'AuthorId')
	 *
	 * @param      array  $arr     An array to populate the object from.
	 * @param      string $keyType The type of keys the array uses.
	 * @return     void
	 */
	public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
	{
		$keys = entryPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setKshowId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setKuserId($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setName($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setType($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setMediaType($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setData($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setThumbnail($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setViews($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setVotes($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setComments($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setFavorites($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setTotalRank($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setRank($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setTags($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setAnonymous($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setStatus($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setSource($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setSourceId($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setSourceLink($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setLicenseType($arr[$keys[20]]);
		if (array_key_exists($keys[21], $arr)) $this->setCredit($arr[$keys[21]]);
		if (array_key_exists($keys[22], $arr)) $this->setLengthInMsecs($arr[$keys[22]]);
		if (array_key_exists($keys[23], $arr)) $this->setCreatedAt($arr[$keys[23]]);
		if (array_key_exists($keys[24], $arr)) $this->setUpdatedAt($arr[$keys[24]]);
		if (array_key_exists($keys[25], $arr)) $this->setPartnerId($arr[$keys[25]]);
		if (array_key_exists($keys[26], $arr)) $this->setDisplayInSearch($arr[$keys[26]]);
		if (array_key_exists($keys[27], $arr)) $this->setSubpId($arr[$keys[27]]);
		if (array_key_exists($keys[28], $arr)) $this->setCustomData($arr[$keys[28]]);
		if (array_key_exists($keys[29], $arr)) $this->setSearchText($arr[$keys[29]]);
		if (array_key_exists($keys[30], $arr)) $this->setScreenName($arr[$keys[30]]);
		if (array_key_exists($keys[31], $arr)) $this->setSiteUrl($arr[$keys[31]]);
		if (array_key_exists($keys[32], $arr)) $this->setPermissions($arr[$keys[32]]);
		if (array_key_exists($keys[33], $arr)) $this->setGroupId($arr[$keys[33]]);
		if (array_key_exists($keys[34], $arr)) $this->setPlays($arr[$keys[34]]);
		if (array_key_exists($keys[35], $arr)) $this->setPartnerData($arr[$keys[35]]);
		if (array_key_exists($keys[36], $arr)) $this->setIntId($arr[$keys[36]]);
		if (array_key_exists($keys[37], $arr)) $this->setIndexedCustomData1($arr[$keys[37]]);
		if (array_key_exists($keys[38], $arr)) $this->setDescription($arr[$keys[38]]);
		if (array_key_exists($keys[39], $arr)) $this->setMediaDate($arr[$keys[39]]);
		if (array_key_exists($keys[40], $arr)) $this->setAdminTags($arr[$keys[40]]);
		if (array_key_exists($keys[41], $arr)) $this->setModerationStatus($arr[$keys[41]]);
		if (array_key_exists($keys[42], $arr)) $this->setModerationCount($arr[$keys[42]]);
		if (array_key_exists($keys[43], $arr)) $this->setModifiedAt($arr[$keys[43]]);
		if (array_key_exists($keys[44], $arr)) $this->setPuserId($arr[$keys[44]]);
		if (array_key_exists($keys[45], $arr)) $this->setAccessControlId($arr[$keys[45]]);
		if (array_key_exists($keys[46], $arr)) $this->setConversionProfileId($arr[$keys[46]]);
		if (array_key_exists($keys[47], $arr)) $this->setCategories($arr[$keys[47]]);
		if (array_key_exists($keys[48], $arr)) $this->setCategoriesIds($arr[$keys[48]]);
		if (array_key_exists($keys[49], $arr)) $this->setStartDate($arr[$keys[49]]);
		if (array_key_exists($keys[50], $arr)) $this->setEndDate($arr[$keys[50]]);
		if (array_key_exists($keys[51], $arr)) $this->setSearchTextDiscrete($arr[$keys[51]]);
		if (array_key_exists($keys[52], $arr)) $this->setFlavorParamsIds($arr[$keys[52]]);
		if (array_key_exists($keys[53], $arr)) $this->setAvailableFrom($arr[$keys[53]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(entryPeer::DATABASE_NAME);

		if ($this->isColumnModified(entryPeer::ID)) $criteria->add(entryPeer::ID, $this->id);
		if ($this->isColumnModified(entryPeer::KSHOW_ID)) $criteria->add(entryPeer::KSHOW_ID, $this->kshow_id);
		if ($this->isColumnModified(entryPeer::KUSER_ID)) $criteria->add(entryPeer::KUSER_ID, $this->kuser_id);
		if ($this->isColumnModified(entryPeer::NAME)) $criteria->add(entryPeer::NAME, $this->name);
		if ($this->isColumnModified(entryPeer::TYPE)) $criteria->add(entryPeer::TYPE, $this->type);
		if ($this->isColumnModified(entryPeer::MEDIA_TYPE)) $criteria->add(entryPeer::MEDIA_TYPE, $this->media_type);
		if ($this->isColumnModified(entryPeer::DATA)) $criteria->add(entryPeer::DATA, $this->data);
		if ($this->isColumnModified(entryPeer::THUMBNAIL)) $criteria->add(entryPeer::THUMBNAIL, $this->thumbnail);
		if ($this->isColumnModified(entryPeer::VIEWS)) $criteria->add(entryPeer::VIEWS, $this->views);
		if ($this->isColumnModified(entryPeer::VOTES)) $criteria->add(entryPeer::VOTES, $this->votes);
		if ($this->isColumnModified(entryPeer::COMMENTS)) $criteria->add(entryPeer::COMMENTS, $this->comments);
		if ($this->isColumnModified(entryPeer::FAVORITES)) $criteria->add(entryPeer::FAVORITES, $this->favorites);
		if ($this->isColumnModified(entryPeer::TOTAL_RANK)) $criteria->add(entryPeer::TOTAL_RANK, $this->total_rank);
		if ($this->isColumnModified(entryPeer::RANK)) $criteria->add(entryPeer::RANK, $this->rank);
		if ($this->isColumnModified(entryPeer::TAGS)) $criteria->add(entryPeer::TAGS, $this->tags);
		if ($this->isColumnModified(entryPeer::ANONYMOUS)) $criteria->add(entryPeer::ANONYMOUS, $this->anonymous);
		if ($this->isColumnModified(entryPeer::STATUS)) $criteria->add(entryPeer::STATUS, $this->status);
		if ($this->isColumnModified(entryPeer::SOURCE)) $criteria->add(entryPeer::SOURCE, $this->source);
		if ($this->isColumnModified(entryPeer::SOURCE_ID)) $criteria->add(entryPeer::SOURCE_ID, $this->source_id);
		if ($this->isColumnModified(entryPeer::SOURCE_LINK)) $criteria->add(entryPeer::SOURCE_LINK, $this->source_link);
		if ($this->isColumnModified(entryPeer::LICENSE_TYPE)) $criteria->add(entryPeer::LICENSE_TYPE, $this->license_type);
		if ($this->isColumnModified(entryPeer::CREDIT)) $criteria->add(entryPeer::CREDIT, $this->credit);
		if ($this->isColumnModified(entryPeer::LENGTH_IN_MSECS)) $criteria->add(entryPeer::LENGTH_IN_MSECS, $this->length_in_msecs);
		if ($this->isColumnModified(entryPeer::CREATED_AT)) $criteria->add(entryPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(entryPeer::UPDATED_AT)) $criteria->add(entryPeer::UPDATED_AT, $this->updated_at);
		if ($this->isColumnModified(entryPeer::PARTNER_ID)) $criteria->add(entryPeer::PARTNER_ID, $this->partner_id);
		if ($this->isColumnModified(entryPeer::DISPLAY_IN_SEARCH)) $criteria->add(entryPeer::DISPLAY_IN_SEARCH, $this->display_in_search);
		if ($this->isColumnModified(entryPeer::SUBP_ID)) $criteria->add(entryPeer::SUBP_ID, $this->subp_id);
		if ($this->isColumnModified(entryPeer::CUSTOM_DATA)) $criteria->add(entryPeer::CUSTOM_DATA, $this->custom_data);
		if ($this->isColumnModified(entryPeer::SEARCH_TEXT)) $criteria->add(entryPeer::SEARCH_TEXT, $this->search_text);
		if ($this->isColumnModified(entryPeer::SCREEN_NAME)) $criteria->add(entryPeer::SCREEN_NAME, $this->screen_name);
		if ($this->isColumnModified(entryPeer::SITE_URL)) $criteria->add(entryPeer::SITE_URL, $this->site_url);
		if ($this->isColumnModified(entryPeer::PERMISSIONS)) $criteria->add(entryPeer::PERMISSIONS, $this->permissions);
		if ($this->isColumnModified(entryPeer::GROUP_ID)) $criteria->add(entryPeer::GROUP_ID, $this->group_id);
		if ($this->isColumnModified(entryPeer::PLAYS)) $criteria->add(entryPeer::PLAYS, $this->plays);
		if ($this->isColumnModified(entryPeer::PARTNER_DATA)) $criteria->add(entryPeer::PARTNER_DATA, $this->partner_data);
		if ($this->isColumnModified(entryPeer::INT_ID)) $criteria->add(entryPeer::INT_ID, $this->int_id);
		if ($this->isColumnModified(entryPeer::INDEXED_CUSTOM_DATA_1)) $criteria->add(entryPeer::INDEXED_CUSTOM_DATA_1, $this->indexed_custom_data_1);
		if ($this->isColumnModified(entryPeer::DESCRIPTION)) $criteria->add(entryPeer::DESCRIPTION, $this->description);
		if ($this->isColumnModified(entryPeer::MEDIA_DATE)) $criteria->add(entryPeer::MEDIA_DATE, $this->media_date);
		if ($this->isColumnModified(entryPeer::ADMIN_TAGS)) $criteria->add(entryPeer::ADMIN_TAGS, $this->admin_tags);
		if ($this->isColumnModified(entryPeer::MODERATION_STATUS)) $criteria->add(entryPeer::MODERATION_STATUS, $this->moderation_status);
		if ($this->isColumnModified(entryPeer::MODERATION_COUNT)) $criteria->add(entryPeer::MODERATION_COUNT, $this->moderation_count);
		if ($this->isColumnModified(entryPeer::MODIFIED_AT)) $criteria->add(entryPeer::MODIFIED_AT, $this->modified_at);
		if ($this->isColumnModified(entryPeer::PUSER_ID)) $criteria->add(entryPeer::PUSER_ID, $this->puser_id);
		if ($this->isColumnModified(entryPeer::ACCESS_CONTROL_ID)) $criteria->add(entryPeer::ACCESS_CONTROL_ID, $this->access_control_id);
		if ($this->isColumnModified(entryPeer::CONVERSION_PROFILE_ID)) $criteria->add(entryPeer::CONVERSION_PROFILE_ID, $this->conversion_profile_id);
		if ($this->isColumnModified(entryPeer::CATEGORIES)) $criteria->add(entryPeer::CATEGORIES, $this->categories);
		if ($this->isColumnModified(entryPeer::CATEGORIES_IDS)) $criteria->add(entryPeer::CATEGORIES_IDS, $this->categories_ids);
		if ($this->isColumnModified(entryPeer::START_DATE)) $criteria->add(entryPeer::START_DATE, $this->start_date);
		if ($this->isColumnModified(entryPeer::END_DATE)) $criteria->add(entryPeer::END_DATE, $this->end_date);
		if ($this->isColumnModified(entryPeer::SEARCH_TEXT_DISCRETE)) $criteria->add(entryPeer::SEARCH_TEXT_DISCRETE, $this->search_text_discrete);
		if ($this->isColumnModified(entryPeer::FLAVOR_PARAMS_IDS)) $criteria->add(entryPeer::FLAVOR_PARAMS_IDS, $this->flavor_params_ids);
		if ($this->isColumnModified(entryPeer::AVAILABLE_FROM)) $criteria->add(entryPeer::AVAILABLE_FROM, $this->available_from);

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
		$criteria = new Criteria(entryPeer::DATABASE_NAME);

		$criteria->add(entryPeer::ID, $this->id);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     string
	 */
	public function getPrimaryKey()
	{
		return $this->getId();
	}

	/**
	 * Generic method to set the primary key (id column).
	 *
	 * @param      string $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setId($key);
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of entry (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setId($this->id);

		$copyObj->setKshowId($this->kshow_id);

		$copyObj->setKuserId($this->kuser_id);

		$copyObj->setName($this->name);

		$copyObj->setType($this->type);

		$copyObj->setMediaType($this->media_type);

		$copyObj->setData($this->data);

		$copyObj->setThumbnail($this->thumbnail);

		$copyObj->setViews($this->views);

		$copyObj->setVotes($this->votes);

		$copyObj->setComments($this->comments);

		$copyObj->setFavorites($this->favorites);

		$copyObj->setTotalRank($this->total_rank);

		$copyObj->setRank($this->rank);

		$copyObj->setTags($this->tags);

		$copyObj->setAnonymous($this->anonymous);

		$copyObj->setStatus($this->status);

		$copyObj->setSource($this->source);

		$copyObj->setSourceId($this->source_id);

		$copyObj->setSourceLink($this->source_link);

		$copyObj->setLicenseType($this->license_type);

		$copyObj->setCredit($this->credit);

		$copyObj->setLengthInMsecs($this->length_in_msecs);

		$copyObj->setCreatedAt($this->created_at);

		$copyObj->setUpdatedAt($this->updated_at);

		$copyObj->setPartnerId($this->partner_id);

		$copyObj->setDisplayInSearch($this->display_in_search);

		$copyObj->setSubpId($this->subp_id);

		$copyObj->setCustomData($this->custom_data);

		$copyObj->setSearchText($this->search_text);

		$copyObj->setScreenName($this->screen_name);

		$copyObj->setSiteUrl($this->site_url);

		$copyObj->setPermissions($this->permissions);

		$copyObj->setGroupId($this->group_id);

		$copyObj->setPlays($this->plays);

		$copyObj->setPartnerData($this->partner_data);

		$copyObj->setIndexedCustomData1($this->indexed_custom_data_1);

		$copyObj->setDescription($this->description);

		$copyObj->setMediaDate($this->media_date);

		$copyObj->setAdminTags($this->admin_tags);

		$copyObj->setModerationStatus($this->moderation_status);

		$copyObj->setModerationCount($this->moderation_count);

		$copyObj->setModifiedAt($this->modified_at);

		$copyObj->setPuserId($this->puser_id);

		$copyObj->setAccessControlId($this->access_control_id);

		$copyObj->setConversionProfileId($this->conversion_profile_id);

		$copyObj->setCategories($this->categories);

		$copyObj->setCategoriesIds($this->categories_ids);

		$copyObj->setStartDate($this->start_date);

		$copyObj->setEndDate($this->end_date);

		$copyObj->setSearchTextDiscrete($this->search_text_discrete);

		$copyObj->setFlavorParamsIds($this->flavor_params_ids);

		$copyObj->setAvailableFrom($this->available_from);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach ($this->getkvotes() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addkvote($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getconversions() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addconversion($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getWidgetLogs() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addWidgetLog($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getmoderationFlags() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addmoderationFlag($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getroughcutEntrysRelatedByRoughcutId() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addroughcutEntryRelatedByRoughcutId($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getroughcutEntrysRelatedByEntryId() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addroughcutEntryRelatedByEntryId($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getwidgets() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addwidget($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getflavorParamsOutputs() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addflavorParamsOutput($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getflavorAssets() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addflavorAsset($relObj->copy($deepCopy));
				}
			}

		} // if ($deepCopy)


		$copyObj->setNew(true);

		$copyObj->setIntId(NULL); // this is a auto-increment column, so set to default value

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
	 * @return     entry Clone of current object.
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
	 * @var     entry Clone of current object.
	 */
	protected $copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      entry $copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(entry $copiedFrom)
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
	 * @return     entryPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new entryPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a kshow object.
	 *
	 * @param      kshow $v
	 * @return     entry The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setkshow(kshow $v = null)
	{
		if ($v === null) {
			$this->setKshowId(NULL);
		} else {
			$this->setKshowId($v->getId());
		}

		$this->akshow = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the kshow object, it will not be re-added.
		if ($v !== null) {
			$v->addentry($this);
		}

		return $this;
	}


	/**
	 * Get the associated kshow object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     kshow The associated kshow object.
	 * @throws     PropelException
	 */
	public function getkshow(PropelPDO $con = null)
	{
		if ($this->akshow === null && (($this->kshow_id !== "" && $this->kshow_id !== null))) {
			$this->akshow = kshowPeer::retrieveByPk($this->kshow_id);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->akshow->addentrys($this);
			 */
		}
		return $this->akshow;
	}

	/**
	 * Declares an association between this object and a kuser object.
	 *
	 * @param      kuser $v
	 * @return     entry The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setkuser(kuser $v = null)
	{
		if ($v === null) {
			$this->setKuserId(NULL);
		} else {
			$this->setKuserId($v->getId());
		}

		$this->akuser = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the kuser object, it will not be re-added.
		if ($v !== null) {
			$v->addentry($this);
		}

		return $this;
	}


	/**
	 * Get the associated kuser object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     kuser The associated kuser object.
	 * @throws     PropelException
	 */
	public function getkuser(PropelPDO $con = null)
	{
		if ($this->akuser === null && ($this->kuser_id !== null)) {
			$this->akuser = kuserPeer::retrieveByPk($this->kuser_id);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->akuser->addentrys($this);
			 */
		}
		return $this->akuser;
	}

	/**
	 * Declares an association between this object and a accessControl object.
	 *
	 * @param      accessControl $v
	 * @return     entry The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setaccessControl(accessControl $v = null)
	{
		if ($v === null) {
			$this->setAccessControlId(NULL);
		} else {
			$this->setAccessControlId($v->getId());
		}

		$this->aaccessControl = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the accessControl object, it will not be re-added.
		if ($v !== null) {
			$v->addentry($this);
		}

		return $this;
	}


	/**
	 * Get the associated accessControl object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     accessControl The associated accessControl object.
	 * @throws     PropelException
	 */
	public function getaccessControl(PropelPDO $con = null)
	{
		if ($this->aaccessControl === null && ($this->access_control_id !== null)) {
			$this->aaccessControl = accessControlPeer::retrieveByPk($this->access_control_id);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aaccessControl->addentrys($this);
			 */
		}
		return $this->aaccessControl;
	}

	/**
	 * Declares an association between this object and a conversionProfile2 object.
	 *
	 * @param      conversionProfile2 $v
	 * @return     entry The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setconversionProfile2(conversionProfile2 $v = null)
	{
		if ($v === null) {
			$this->setConversionProfileId(NULL);
		} else {
			$this->setConversionProfileId($v->getId());
		}

		$this->aconversionProfile2 = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the conversionProfile2 object, it will not be re-added.
		if ($v !== null) {
			$v->addentry($this);
		}

		return $this;
	}


	/**
	 * Get the associated conversionProfile2 object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     conversionProfile2 The associated conversionProfile2 object.
	 * @throws     PropelException
	 */
	public function getconversionProfile2(PropelPDO $con = null)
	{
		if ($this->aconversionProfile2 === null && ($this->conversion_profile_id !== null)) {
			$this->aconversionProfile2 = conversionProfile2Peer::retrieveByPk($this->conversion_profile_id);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aconversionProfile2->addentrys($this);
			 */
		}
		return $this->aconversionProfile2;
	}

	/**
	 * Clears out the collkvotes collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addkvotes()
	 */
	public function clearkvotes()
	{
		$this->collkvotes = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collkvotes collection (array).
	 *
	 * By default this just sets the collkvotes collection to an empty array (like clearcollkvotes());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initkvotes()
	{
		$this->collkvotes = array();
	}

	/**
	 * Gets an array of kvote objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this entry has previously been saved, it will retrieve
	 * related kvotes from storage. If this entry is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array kvote[]
	 * @throws     PropelException
	 */
	public function getkvotes($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(entryPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collkvotes === null) {
			if ($this->isNew()) {
			   $this->collkvotes = array();
			} else {

				$criteria->add(kvotePeer::ENTRY_ID, $this->id);

				kvotePeer::addSelectColumns($criteria);
				$this->collkvotes = kvotePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(kvotePeer::ENTRY_ID, $this->id);

				kvotePeer::addSelectColumns($criteria);
				if (!isset($this->lastkvoteCriteria) || !$this->lastkvoteCriteria->equals($criteria)) {
					$this->collkvotes = kvotePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastkvoteCriteria = $criteria;
		return $this->collkvotes;
	}

	/**
	 * Returns the number of related kvote objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related kvote objects.
	 * @throws     PropelException
	 */
	public function countkvotes(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(entryPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collkvotes === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(kvotePeer::ENTRY_ID, $this->id);

				$count = kvotePeer::doCount($criteria, false, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(kvotePeer::ENTRY_ID, $this->id);

				if (!isset($this->lastkvoteCriteria) || !$this->lastkvoteCriteria->equals($criteria)) {
					$count = kvotePeer::doCount($criteria, false, $con);
				} else {
					$count = count($this->collkvotes);
				}
			} else {
				$count = count($this->collkvotes);
			}
		}
		return $count;
	}

	/**
	 * Method called to associate a kvote object to this object
	 * through the kvote foreign key attribute.
	 *
	 * @param      kvote $l kvote
	 * @return     void
	 * @throws     PropelException
	 */
	public function addkvote(kvote $l)
	{
		if ($this->collkvotes === null) {
			$this->initkvotes();
		}
		if (!in_array($l, $this->collkvotes, true)) { // only add it if the **same** object is not already associated
			array_push($this->collkvotes, $l);
			$l->setentry($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this entry is new, it will return
	 * an empty collection; or if this entry has previously
	 * been saved, it will retrieve related kvotes from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in entry.
	 */
	public function getkvotesJoinkshowRelatedByKshowId($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(entryPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collkvotes === null) {
			if ($this->isNew()) {
				$this->collkvotes = array();
			} else {

				$criteria->add(kvotePeer::ENTRY_ID, $this->id);

				$this->collkvotes = kvotePeer::doSelectJoinkshowRelatedByKshowId($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(kvotePeer::ENTRY_ID, $this->id);

			if (!isset($this->lastkvoteCriteria) || !$this->lastkvoteCriteria->equals($criteria)) {
				$this->collkvotes = kvotePeer::doSelectJoinkshowRelatedByKshowId($criteria, $con, $join_behavior);
			}
		}
		$this->lastkvoteCriteria = $criteria;

		return $this->collkvotes;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this entry is new, it will return
	 * an empty collection; or if this entry has previously
	 * been saved, it will retrieve related kvotes from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in entry.
	 */
	public function getkvotesJoinkshowRelatedByKuserId($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(entryPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collkvotes === null) {
			if ($this->isNew()) {
				$this->collkvotes = array();
			} else {

				$criteria->add(kvotePeer::ENTRY_ID, $this->id);

				$this->collkvotes = kvotePeer::doSelectJoinkshowRelatedByKuserId($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(kvotePeer::ENTRY_ID, $this->id);

			if (!isset($this->lastkvoteCriteria) || !$this->lastkvoteCriteria->equals($criteria)) {
				$this->collkvotes = kvotePeer::doSelectJoinkshowRelatedByKuserId($criteria, $con, $join_behavior);
			}
		}
		$this->lastkvoteCriteria = $criteria;

		return $this->collkvotes;
	}

	/**
	 * Clears out the collconversions collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addconversions()
	 */
	public function clearconversions()
	{
		$this->collconversions = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collconversions collection (array).
	 *
	 * By default this just sets the collconversions collection to an empty array (like clearcollconversions());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initconversions()
	{
		$this->collconversions = array();
	}

	/**
	 * Gets an array of conversion objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this entry has previously been saved, it will retrieve
	 * related conversions from storage. If this entry is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array conversion[]
	 * @throws     PropelException
	 */
	public function getconversions($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(entryPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collconversions === null) {
			if ($this->isNew()) {
			   $this->collconversions = array();
			} else {

				$criteria->add(conversionPeer::ENTRY_ID, $this->id);

				conversionPeer::addSelectColumns($criteria);
				$this->collconversions = conversionPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(conversionPeer::ENTRY_ID, $this->id);

				conversionPeer::addSelectColumns($criteria);
				if (!isset($this->lastconversionCriteria) || !$this->lastconversionCriteria->equals($criteria)) {
					$this->collconversions = conversionPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastconversionCriteria = $criteria;
		return $this->collconversions;
	}

	/**
	 * Returns the number of related conversion objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related conversion objects.
	 * @throws     PropelException
	 */
	public function countconversions(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(entryPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collconversions === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(conversionPeer::ENTRY_ID, $this->id);

				$count = conversionPeer::doCount($criteria, false, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(conversionPeer::ENTRY_ID, $this->id);

				if (!isset($this->lastconversionCriteria) || !$this->lastconversionCriteria->equals($criteria)) {
					$count = conversionPeer::doCount($criteria, false, $con);
				} else {
					$count = count($this->collconversions);
				}
			} else {
				$count = count($this->collconversions);
			}
		}
		return $count;
	}

	/**
	 * Method called to associate a conversion object to this object
	 * through the conversion foreign key attribute.
	 *
	 * @param      conversion $l conversion
	 * @return     void
	 * @throws     PropelException
	 */
	public function addconversion(conversion $l)
	{
		if ($this->collconversions === null) {
			$this->initconversions();
		}
		if (!in_array($l, $this->collconversions, true)) { // only add it if the **same** object is not already associated
			array_push($this->collconversions, $l);
			$l->setentry($this);
		}
	}

	/**
	 * Clears out the collWidgetLogs collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addWidgetLogs()
	 */
	public function clearWidgetLogs()
	{
		$this->collWidgetLogs = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collWidgetLogs collection (array).
	 *
	 * By default this just sets the collWidgetLogs collection to an empty array (like clearcollWidgetLogs());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initWidgetLogs()
	{
		$this->collWidgetLogs = array();
	}

	/**
	 * Gets an array of WidgetLog objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this entry has previously been saved, it will retrieve
	 * related WidgetLogs from storage. If this entry is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array WidgetLog[]
	 * @throws     PropelException
	 */
	public function getWidgetLogs($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(entryPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collWidgetLogs === null) {
			if ($this->isNew()) {
			   $this->collWidgetLogs = array();
			} else {

				$criteria->add(WidgetLogPeer::ENTRY_ID, $this->id);

				WidgetLogPeer::addSelectColumns($criteria);
				$this->collWidgetLogs = WidgetLogPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(WidgetLogPeer::ENTRY_ID, $this->id);

				WidgetLogPeer::addSelectColumns($criteria);
				if (!isset($this->lastWidgetLogCriteria) || !$this->lastWidgetLogCriteria->equals($criteria)) {
					$this->collWidgetLogs = WidgetLogPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastWidgetLogCriteria = $criteria;
		return $this->collWidgetLogs;
	}

	/**
	 * Returns the number of related WidgetLog objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related WidgetLog objects.
	 * @throws     PropelException
	 */
	public function countWidgetLogs(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(entryPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collWidgetLogs === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(WidgetLogPeer::ENTRY_ID, $this->id);

				$count = WidgetLogPeer::doCount($criteria, false, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(WidgetLogPeer::ENTRY_ID, $this->id);

				if (!isset($this->lastWidgetLogCriteria) || !$this->lastWidgetLogCriteria->equals($criteria)) {
					$count = WidgetLogPeer::doCount($criteria, false, $con);
				} else {
					$count = count($this->collWidgetLogs);
				}
			} else {
				$count = count($this->collWidgetLogs);
			}
		}
		return $count;
	}

	/**
	 * Method called to associate a WidgetLog object to this object
	 * through the WidgetLog foreign key attribute.
	 *
	 * @param      WidgetLog $l WidgetLog
	 * @return     void
	 * @throws     PropelException
	 */
	public function addWidgetLog(WidgetLog $l)
	{
		if ($this->collWidgetLogs === null) {
			$this->initWidgetLogs();
		}
		if (!in_array($l, $this->collWidgetLogs, true)) { // only add it if the **same** object is not already associated
			array_push($this->collWidgetLogs, $l);
			$l->setentry($this);
		}
	}

	/**
	 * Clears out the collmoderationFlags collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addmoderationFlags()
	 */
	public function clearmoderationFlags()
	{
		$this->collmoderationFlags = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collmoderationFlags collection (array).
	 *
	 * By default this just sets the collmoderationFlags collection to an empty array (like clearcollmoderationFlags());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initmoderationFlags()
	{
		$this->collmoderationFlags = array();
	}

	/**
	 * Gets an array of moderationFlag objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this entry has previously been saved, it will retrieve
	 * related moderationFlags from storage. If this entry is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array moderationFlag[]
	 * @throws     PropelException
	 */
	public function getmoderationFlags($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(entryPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collmoderationFlags === null) {
			if ($this->isNew()) {
			   $this->collmoderationFlags = array();
			} else {

				$criteria->add(moderationFlagPeer::FLAGGED_ENTRY_ID, $this->id);

				moderationFlagPeer::addSelectColumns($criteria);
				$this->collmoderationFlags = moderationFlagPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(moderationFlagPeer::FLAGGED_ENTRY_ID, $this->id);

				moderationFlagPeer::addSelectColumns($criteria);
				if (!isset($this->lastmoderationFlagCriteria) || !$this->lastmoderationFlagCriteria->equals($criteria)) {
					$this->collmoderationFlags = moderationFlagPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastmoderationFlagCriteria = $criteria;
		return $this->collmoderationFlags;
	}

	/**
	 * Returns the number of related moderationFlag objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related moderationFlag objects.
	 * @throws     PropelException
	 */
	public function countmoderationFlags(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(entryPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collmoderationFlags === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(moderationFlagPeer::FLAGGED_ENTRY_ID, $this->id);

				$count = moderationFlagPeer::doCount($criteria, false, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(moderationFlagPeer::FLAGGED_ENTRY_ID, $this->id);

				if (!isset($this->lastmoderationFlagCriteria) || !$this->lastmoderationFlagCriteria->equals($criteria)) {
					$count = moderationFlagPeer::doCount($criteria, false, $con);
				} else {
					$count = count($this->collmoderationFlags);
				}
			} else {
				$count = count($this->collmoderationFlags);
			}
		}
		return $count;
	}

	/**
	 * Method called to associate a moderationFlag object to this object
	 * through the moderationFlag foreign key attribute.
	 *
	 * @param      moderationFlag $l moderationFlag
	 * @return     void
	 * @throws     PropelException
	 */
	public function addmoderationFlag(moderationFlag $l)
	{
		if ($this->collmoderationFlags === null) {
			$this->initmoderationFlags();
		}
		if (!in_array($l, $this->collmoderationFlags, true)) { // only add it if the **same** object is not already associated
			array_push($this->collmoderationFlags, $l);
			$l->setentry($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this entry is new, it will return
	 * an empty collection; or if this entry has previously
	 * been saved, it will retrieve related moderationFlags from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in entry.
	 */
	public function getmoderationFlagsJoinkuserRelatedByKuserId($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(entryPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collmoderationFlags === null) {
			if ($this->isNew()) {
				$this->collmoderationFlags = array();
			} else {

				$criteria->add(moderationFlagPeer::FLAGGED_ENTRY_ID, $this->id);

				$this->collmoderationFlags = moderationFlagPeer::doSelectJoinkuserRelatedByKuserId($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(moderationFlagPeer::FLAGGED_ENTRY_ID, $this->id);

			if (!isset($this->lastmoderationFlagCriteria) || !$this->lastmoderationFlagCriteria->equals($criteria)) {
				$this->collmoderationFlags = moderationFlagPeer::doSelectJoinkuserRelatedByKuserId($criteria, $con, $join_behavior);
			}
		}
		$this->lastmoderationFlagCriteria = $criteria;

		return $this->collmoderationFlags;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this entry is new, it will return
	 * an empty collection; or if this entry has previously
	 * been saved, it will retrieve related moderationFlags from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in entry.
	 */
	public function getmoderationFlagsJoinkuserRelatedByFlaggedKuserId($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(entryPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collmoderationFlags === null) {
			if ($this->isNew()) {
				$this->collmoderationFlags = array();
			} else {

				$criteria->add(moderationFlagPeer::FLAGGED_ENTRY_ID, $this->id);

				$this->collmoderationFlags = moderationFlagPeer::doSelectJoinkuserRelatedByFlaggedKuserId($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(moderationFlagPeer::FLAGGED_ENTRY_ID, $this->id);

			if (!isset($this->lastmoderationFlagCriteria) || !$this->lastmoderationFlagCriteria->equals($criteria)) {
				$this->collmoderationFlags = moderationFlagPeer::doSelectJoinkuserRelatedByFlaggedKuserId($criteria, $con, $join_behavior);
			}
		}
		$this->lastmoderationFlagCriteria = $criteria;

		return $this->collmoderationFlags;
	}

	/**
	 * Clears out the collroughcutEntrysRelatedByRoughcutId collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addroughcutEntrysRelatedByRoughcutId()
	 */
	public function clearroughcutEntrysRelatedByRoughcutId()
	{
		$this->collroughcutEntrysRelatedByRoughcutId = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collroughcutEntrysRelatedByRoughcutId collection (array).
	 *
	 * By default this just sets the collroughcutEntrysRelatedByRoughcutId collection to an empty array (like clearcollroughcutEntrysRelatedByRoughcutId());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initroughcutEntrysRelatedByRoughcutId()
	{
		$this->collroughcutEntrysRelatedByRoughcutId = array();
	}

	/**
	 * Gets an array of roughcutEntry objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this entry has previously been saved, it will retrieve
	 * related roughcutEntrysRelatedByRoughcutId from storage. If this entry is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array roughcutEntry[]
	 * @throws     PropelException
	 */
	public function getroughcutEntrysRelatedByRoughcutId($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(entryPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collroughcutEntrysRelatedByRoughcutId === null) {
			if ($this->isNew()) {
			   $this->collroughcutEntrysRelatedByRoughcutId = array();
			} else {

				$criteria->add(roughcutEntryPeer::ROUGHCUT_ID, $this->id);

				roughcutEntryPeer::addSelectColumns($criteria);
				$this->collroughcutEntrysRelatedByRoughcutId = roughcutEntryPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(roughcutEntryPeer::ROUGHCUT_ID, $this->id);

				roughcutEntryPeer::addSelectColumns($criteria);
				if (!isset($this->lastroughcutEntryRelatedByRoughcutIdCriteria) || !$this->lastroughcutEntryRelatedByRoughcutIdCriteria->equals($criteria)) {
					$this->collroughcutEntrysRelatedByRoughcutId = roughcutEntryPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastroughcutEntryRelatedByRoughcutIdCriteria = $criteria;
		return $this->collroughcutEntrysRelatedByRoughcutId;
	}

	/**
	 * Returns the number of related roughcutEntry objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related roughcutEntry objects.
	 * @throws     PropelException
	 */
	public function countroughcutEntrysRelatedByRoughcutId(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(entryPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collroughcutEntrysRelatedByRoughcutId === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(roughcutEntryPeer::ROUGHCUT_ID, $this->id);

				$count = roughcutEntryPeer::doCount($criteria, false, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(roughcutEntryPeer::ROUGHCUT_ID, $this->id);

				if (!isset($this->lastroughcutEntryRelatedByRoughcutIdCriteria) || !$this->lastroughcutEntryRelatedByRoughcutIdCriteria->equals($criteria)) {
					$count = roughcutEntryPeer::doCount($criteria, false, $con);
				} else {
					$count = count($this->collroughcutEntrysRelatedByRoughcutId);
				}
			} else {
				$count = count($this->collroughcutEntrysRelatedByRoughcutId);
			}
		}
		return $count;
	}

	/**
	 * Method called to associate a roughcutEntry object to this object
	 * through the roughcutEntry foreign key attribute.
	 *
	 * @param      roughcutEntry $l roughcutEntry
	 * @return     void
	 * @throws     PropelException
	 */
	public function addroughcutEntryRelatedByRoughcutId(roughcutEntry $l)
	{
		if ($this->collroughcutEntrysRelatedByRoughcutId === null) {
			$this->initroughcutEntrysRelatedByRoughcutId();
		}
		if (!in_array($l, $this->collroughcutEntrysRelatedByRoughcutId, true)) { // only add it if the **same** object is not already associated
			array_push($this->collroughcutEntrysRelatedByRoughcutId, $l);
			$l->setentryRelatedByRoughcutId($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this entry is new, it will return
	 * an empty collection; or if this entry has previously
	 * been saved, it will retrieve related roughcutEntrysRelatedByRoughcutId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in entry.
	 */
	public function getroughcutEntrysRelatedByRoughcutIdJoinkshow($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(entryPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collroughcutEntrysRelatedByRoughcutId === null) {
			if ($this->isNew()) {
				$this->collroughcutEntrysRelatedByRoughcutId = array();
			} else {

				$criteria->add(roughcutEntryPeer::ROUGHCUT_ID, $this->id);

				$this->collroughcutEntrysRelatedByRoughcutId = roughcutEntryPeer::doSelectJoinkshow($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(roughcutEntryPeer::ROUGHCUT_ID, $this->id);

			if (!isset($this->lastroughcutEntryRelatedByRoughcutIdCriteria) || !$this->lastroughcutEntryRelatedByRoughcutIdCriteria->equals($criteria)) {
				$this->collroughcutEntrysRelatedByRoughcutId = roughcutEntryPeer::doSelectJoinkshow($criteria, $con, $join_behavior);
			}
		}
		$this->lastroughcutEntryRelatedByRoughcutIdCriteria = $criteria;

		return $this->collroughcutEntrysRelatedByRoughcutId;
	}

	/**
	 * Clears out the collroughcutEntrysRelatedByEntryId collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addroughcutEntrysRelatedByEntryId()
	 */
	public function clearroughcutEntrysRelatedByEntryId()
	{
		$this->collroughcutEntrysRelatedByEntryId = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collroughcutEntrysRelatedByEntryId collection (array).
	 *
	 * By default this just sets the collroughcutEntrysRelatedByEntryId collection to an empty array (like clearcollroughcutEntrysRelatedByEntryId());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initroughcutEntrysRelatedByEntryId()
	{
		$this->collroughcutEntrysRelatedByEntryId = array();
	}

	/**
	 * Gets an array of roughcutEntry objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this entry has previously been saved, it will retrieve
	 * related roughcutEntrysRelatedByEntryId from storage. If this entry is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array roughcutEntry[]
	 * @throws     PropelException
	 */
	public function getroughcutEntrysRelatedByEntryId($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(entryPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collroughcutEntrysRelatedByEntryId === null) {
			if ($this->isNew()) {
			   $this->collroughcutEntrysRelatedByEntryId = array();
			} else {

				$criteria->add(roughcutEntryPeer::ENTRY_ID, $this->id);

				roughcutEntryPeer::addSelectColumns($criteria);
				$this->collroughcutEntrysRelatedByEntryId = roughcutEntryPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(roughcutEntryPeer::ENTRY_ID, $this->id);

				roughcutEntryPeer::addSelectColumns($criteria);
				if (!isset($this->lastroughcutEntryRelatedByEntryIdCriteria) || !$this->lastroughcutEntryRelatedByEntryIdCriteria->equals($criteria)) {
					$this->collroughcutEntrysRelatedByEntryId = roughcutEntryPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastroughcutEntryRelatedByEntryIdCriteria = $criteria;
		return $this->collroughcutEntrysRelatedByEntryId;
	}

	/**
	 * Returns the number of related roughcutEntry objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related roughcutEntry objects.
	 * @throws     PropelException
	 */
	public function countroughcutEntrysRelatedByEntryId(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(entryPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collroughcutEntrysRelatedByEntryId === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(roughcutEntryPeer::ENTRY_ID, $this->id);

				$count = roughcutEntryPeer::doCount($criteria, false, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(roughcutEntryPeer::ENTRY_ID, $this->id);

				if (!isset($this->lastroughcutEntryRelatedByEntryIdCriteria) || !$this->lastroughcutEntryRelatedByEntryIdCriteria->equals($criteria)) {
					$count = roughcutEntryPeer::doCount($criteria, false, $con);
				} else {
					$count = count($this->collroughcutEntrysRelatedByEntryId);
				}
			} else {
				$count = count($this->collroughcutEntrysRelatedByEntryId);
			}
		}
		return $count;
	}

	/**
	 * Method called to associate a roughcutEntry object to this object
	 * through the roughcutEntry foreign key attribute.
	 *
	 * @param      roughcutEntry $l roughcutEntry
	 * @return     void
	 * @throws     PropelException
	 */
	public function addroughcutEntryRelatedByEntryId(roughcutEntry $l)
	{
		if ($this->collroughcutEntrysRelatedByEntryId === null) {
			$this->initroughcutEntrysRelatedByEntryId();
		}
		if (!in_array($l, $this->collroughcutEntrysRelatedByEntryId, true)) { // only add it if the **same** object is not already associated
			array_push($this->collroughcutEntrysRelatedByEntryId, $l);
			$l->setentryRelatedByEntryId($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this entry is new, it will return
	 * an empty collection; or if this entry has previously
	 * been saved, it will retrieve related roughcutEntrysRelatedByEntryId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in entry.
	 */
	public function getroughcutEntrysRelatedByEntryIdJoinkshow($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(entryPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collroughcutEntrysRelatedByEntryId === null) {
			if ($this->isNew()) {
				$this->collroughcutEntrysRelatedByEntryId = array();
			} else {

				$criteria->add(roughcutEntryPeer::ENTRY_ID, $this->id);

				$this->collroughcutEntrysRelatedByEntryId = roughcutEntryPeer::doSelectJoinkshow($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(roughcutEntryPeer::ENTRY_ID, $this->id);

			if (!isset($this->lastroughcutEntryRelatedByEntryIdCriteria) || !$this->lastroughcutEntryRelatedByEntryIdCriteria->equals($criteria)) {
				$this->collroughcutEntrysRelatedByEntryId = roughcutEntryPeer::doSelectJoinkshow($criteria, $con, $join_behavior);
			}
		}
		$this->lastroughcutEntryRelatedByEntryIdCriteria = $criteria;

		return $this->collroughcutEntrysRelatedByEntryId;
	}

	/**
	 * Clears out the collwidgets collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addwidgets()
	 */
	public function clearwidgets()
	{
		$this->collwidgets = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collwidgets collection (array).
	 *
	 * By default this just sets the collwidgets collection to an empty array (like clearcollwidgets());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initwidgets()
	{
		$this->collwidgets = array();
	}

	/**
	 * Gets an array of widget objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this entry has previously been saved, it will retrieve
	 * related widgets from storage. If this entry is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array widget[]
	 * @throws     PropelException
	 */
	public function getwidgets($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(entryPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collwidgets === null) {
			if ($this->isNew()) {
			   $this->collwidgets = array();
			} else {

				$criteria->add(widgetPeer::ENTRY_ID, $this->id);

				widgetPeer::addSelectColumns($criteria);
				$this->collwidgets = widgetPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(widgetPeer::ENTRY_ID, $this->id);

				widgetPeer::addSelectColumns($criteria);
				if (!isset($this->lastwidgetCriteria) || !$this->lastwidgetCriteria->equals($criteria)) {
					$this->collwidgets = widgetPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastwidgetCriteria = $criteria;
		return $this->collwidgets;
	}

	/**
	 * Returns the number of related widget objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related widget objects.
	 * @throws     PropelException
	 */
	public function countwidgets(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(entryPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collwidgets === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(widgetPeer::ENTRY_ID, $this->id);

				$count = widgetPeer::doCount($criteria, false, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(widgetPeer::ENTRY_ID, $this->id);

				if (!isset($this->lastwidgetCriteria) || !$this->lastwidgetCriteria->equals($criteria)) {
					$count = widgetPeer::doCount($criteria, false, $con);
				} else {
					$count = count($this->collwidgets);
				}
			} else {
				$count = count($this->collwidgets);
			}
		}
		return $count;
	}

	/**
	 * Method called to associate a widget object to this object
	 * through the widget foreign key attribute.
	 *
	 * @param      widget $l widget
	 * @return     void
	 * @throws     PropelException
	 */
	public function addwidget(widget $l)
	{
		if ($this->collwidgets === null) {
			$this->initwidgets();
		}
		if (!in_array($l, $this->collwidgets, true)) { // only add it if the **same** object is not already associated
			array_push($this->collwidgets, $l);
			$l->setentry($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this entry is new, it will return
	 * an empty collection; or if this entry has previously
	 * been saved, it will retrieve related widgets from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in entry.
	 */
	public function getwidgetsJoinkshow($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(entryPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collwidgets === null) {
			if ($this->isNew()) {
				$this->collwidgets = array();
			} else {

				$criteria->add(widgetPeer::ENTRY_ID, $this->id);

				$this->collwidgets = widgetPeer::doSelectJoinkshow($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(widgetPeer::ENTRY_ID, $this->id);

			if (!isset($this->lastwidgetCriteria) || !$this->lastwidgetCriteria->equals($criteria)) {
				$this->collwidgets = widgetPeer::doSelectJoinkshow($criteria, $con, $join_behavior);
			}
		}
		$this->lastwidgetCriteria = $criteria;

		return $this->collwidgets;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this entry is new, it will return
	 * an empty collection; or if this entry has previously
	 * been saved, it will retrieve related widgets from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in entry.
	 */
	public function getwidgetsJoinuiConf($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(entryPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collwidgets === null) {
			if ($this->isNew()) {
				$this->collwidgets = array();
			} else {

				$criteria->add(widgetPeer::ENTRY_ID, $this->id);

				$this->collwidgets = widgetPeer::doSelectJoinuiConf($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(widgetPeer::ENTRY_ID, $this->id);

			if (!isset($this->lastwidgetCriteria) || !$this->lastwidgetCriteria->equals($criteria)) {
				$this->collwidgets = widgetPeer::doSelectJoinuiConf($criteria, $con, $join_behavior);
			}
		}
		$this->lastwidgetCriteria = $criteria;

		return $this->collwidgets;
	}

	/**
	 * Clears out the collflavorParamsOutputs collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addflavorParamsOutputs()
	 */
	public function clearflavorParamsOutputs()
	{
		$this->collflavorParamsOutputs = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collflavorParamsOutputs collection (array).
	 *
	 * By default this just sets the collflavorParamsOutputs collection to an empty array (like clearcollflavorParamsOutputs());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initflavorParamsOutputs()
	{
		$this->collflavorParamsOutputs = array();
	}

	/**
	 * Gets an array of flavorParamsOutput objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this entry has previously been saved, it will retrieve
	 * related flavorParamsOutputs from storage. If this entry is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array flavorParamsOutput[]
	 * @throws     PropelException
	 */
	public function getflavorParamsOutputs($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(entryPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collflavorParamsOutputs === null) {
			if ($this->isNew()) {
			   $this->collflavorParamsOutputs = array();
			} else {

				$criteria->add(flavorParamsOutputPeer::ENTRY_ID, $this->id);

				flavorParamsOutputPeer::addSelectColumns($criteria);
				$this->collflavorParamsOutputs = flavorParamsOutputPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(flavorParamsOutputPeer::ENTRY_ID, $this->id);

				flavorParamsOutputPeer::addSelectColumns($criteria);
				if (!isset($this->lastflavorParamsOutputCriteria) || !$this->lastflavorParamsOutputCriteria->equals($criteria)) {
					$this->collflavorParamsOutputs = flavorParamsOutputPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastflavorParamsOutputCriteria = $criteria;
		return $this->collflavorParamsOutputs;
	}

	/**
	 * Returns the number of related flavorParamsOutput objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related flavorParamsOutput objects.
	 * @throws     PropelException
	 */
	public function countflavorParamsOutputs(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(entryPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collflavorParamsOutputs === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(flavorParamsOutputPeer::ENTRY_ID, $this->id);

				$count = flavorParamsOutputPeer::doCount($criteria, false, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(flavorParamsOutputPeer::ENTRY_ID, $this->id);

				if (!isset($this->lastflavorParamsOutputCriteria) || !$this->lastflavorParamsOutputCriteria->equals($criteria)) {
					$count = flavorParamsOutputPeer::doCount($criteria, false, $con);
				} else {
					$count = count($this->collflavorParamsOutputs);
				}
			} else {
				$count = count($this->collflavorParamsOutputs);
			}
		}
		return $count;
	}

	/**
	 * Method called to associate a flavorParamsOutput object to this object
	 * through the flavorParamsOutput foreign key attribute.
	 *
	 * @param      flavorParamsOutput $l flavorParamsOutput
	 * @return     void
	 * @throws     PropelException
	 */
	public function addflavorParamsOutput(flavorParamsOutput $l)
	{
		if ($this->collflavorParamsOutputs === null) {
			$this->initflavorParamsOutputs();
		}
		if (!in_array($l, $this->collflavorParamsOutputs, true)) { // only add it if the **same** object is not already associated
			array_push($this->collflavorParamsOutputs, $l);
			$l->setentry($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this entry is new, it will return
	 * an empty collection; or if this entry has previously
	 * been saved, it will retrieve related flavorParamsOutputs from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in entry.
	 */
	public function getflavorParamsOutputsJoinflavorParams($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(entryPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collflavorParamsOutputs === null) {
			if ($this->isNew()) {
				$this->collflavorParamsOutputs = array();
			} else {

				$criteria->add(flavorParamsOutputPeer::ENTRY_ID, $this->id);

				$this->collflavorParamsOutputs = flavorParamsOutputPeer::doSelectJoinflavorParams($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(flavorParamsOutputPeer::ENTRY_ID, $this->id);

			if (!isset($this->lastflavorParamsOutputCriteria) || !$this->lastflavorParamsOutputCriteria->equals($criteria)) {
				$this->collflavorParamsOutputs = flavorParamsOutputPeer::doSelectJoinflavorParams($criteria, $con, $join_behavior);
			}
		}
		$this->lastflavorParamsOutputCriteria = $criteria;

		return $this->collflavorParamsOutputs;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this entry is new, it will return
	 * an empty collection; or if this entry has previously
	 * been saved, it will retrieve related flavorParamsOutputs from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in entry.
	 */
	public function getflavorParamsOutputsJoinflavorAsset($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(entryPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collflavorParamsOutputs === null) {
			if ($this->isNew()) {
				$this->collflavorParamsOutputs = array();
			} else {

				$criteria->add(flavorParamsOutputPeer::ENTRY_ID, $this->id);

				$this->collflavorParamsOutputs = flavorParamsOutputPeer::doSelectJoinflavorAsset($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(flavorParamsOutputPeer::ENTRY_ID, $this->id);

			if (!isset($this->lastflavorParamsOutputCriteria) || !$this->lastflavorParamsOutputCriteria->equals($criteria)) {
				$this->collflavorParamsOutputs = flavorParamsOutputPeer::doSelectJoinflavorAsset($criteria, $con, $join_behavior);
			}
		}
		$this->lastflavorParamsOutputCriteria = $criteria;

		return $this->collflavorParamsOutputs;
	}

	/**
	 * Clears out the collflavorAssets collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addflavorAssets()
	 */
	public function clearflavorAssets()
	{
		$this->collflavorAssets = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collflavorAssets collection (array).
	 *
	 * By default this just sets the collflavorAssets collection to an empty array (like clearcollflavorAssets());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initflavorAssets()
	{
		$this->collflavorAssets = array();
	}

	/**
	 * Gets an array of flavorAsset objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this entry has previously been saved, it will retrieve
	 * related flavorAssets from storage. If this entry is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array flavorAsset[]
	 * @throws     PropelException
	 */
	public function getflavorAssets($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(entryPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collflavorAssets === null) {
			if ($this->isNew()) {
			   $this->collflavorAssets = array();
			} else {

				$criteria->add(flavorAssetPeer::ENTRY_ID, $this->id);

				flavorAssetPeer::addSelectColumns($criteria);
				$this->collflavorAssets = flavorAssetPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(flavorAssetPeer::ENTRY_ID, $this->id);

				flavorAssetPeer::addSelectColumns($criteria);
				if (!isset($this->lastflavorAssetCriteria) || !$this->lastflavorAssetCriteria->equals($criteria)) {
					$this->collflavorAssets = flavorAssetPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastflavorAssetCriteria = $criteria;
		return $this->collflavorAssets;
	}

	/**
	 * Returns the number of related flavorAsset objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related flavorAsset objects.
	 * @throws     PropelException
	 */
	public function countflavorAssets(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(entryPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collflavorAssets === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(flavorAssetPeer::ENTRY_ID, $this->id);

				$count = flavorAssetPeer::doCount($criteria, false, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(flavorAssetPeer::ENTRY_ID, $this->id);

				if (!isset($this->lastflavorAssetCriteria) || !$this->lastflavorAssetCriteria->equals($criteria)) {
					$count = flavorAssetPeer::doCount($criteria, false, $con);
				} else {
					$count = count($this->collflavorAssets);
				}
			} else {
				$count = count($this->collflavorAssets);
			}
		}
		return $count;
	}

	/**
	 * Method called to associate a flavorAsset object to this object
	 * through the flavorAsset foreign key attribute.
	 *
	 * @param      flavorAsset $l flavorAsset
	 * @return     void
	 * @throws     PropelException
	 */
	public function addflavorAsset(flavorAsset $l)
	{
		if ($this->collflavorAssets === null) {
			$this->initflavorAssets();
		}
		if (!in_array($l, $this->collflavorAssets, true)) { // only add it if the **same** object is not already associated
			array_push($this->collflavorAssets, $l);
			$l->setentry($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this entry is new, it will return
	 * an empty collection; or if this entry has previously
	 * been saved, it will retrieve related flavorAssets from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in entry.
	 */
	public function getflavorAssetsJoinflavorParams($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(entryPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collflavorAssets === null) {
			if ($this->isNew()) {
				$this->collflavorAssets = array();
			} else {

				$criteria->add(flavorAssetPeer::ENTRY_ID, $this->id);

				$this->collflavorAssets = flavorAssetPeer::doSelectJoinflavorParams($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(flavorAssetPeer::ENTRY_ID, $this->id);

			if (!isset($this->lastflavorAssetCriteria) || !$this->lastflavorAssetCriteria->equals($criteria)) {
				$this->collflavorAssets = flavorAssetPeer::doSelectJoinflavorParams($criteria, $con, $join_behavior);
			}
		}
		$this->lastflavorAssetCriteria = $criteria;

		return $this->collflavorAssets;
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
			if ($this->collkvotes) {
				foreach ((array) $this->collkvotes as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collconversions) {
				foreach ((array) $this->collconversions as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collWidgetLogs) {
				foreach ((array) $this->collWidgetLogs as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collmoderationFlags) {
				foreach ((array) $this->collmoderationFlags as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collroughcutEntrysRelatedByRoughcutId) {
				foreach ((array) $this->collroughcutEntrysRelatedByRoughcutId as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collroughcutEntrysRelatedByEntryId) {
				foreach ((array) $this->collroughcutEntrysRelatedByEntryId as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collwidgets) {
				foreach ((array) $this->collwidgets as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collflavorParamsOutputs) {
				foreach ((array) $this->collflavorParamsOutputs as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collflavorAssets) {
				foreach ((array) $this->collflavorAssets as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		$this->collkvotes = null;
		$this->collconversions = null;
		$this->collWidgetLogs = null;
		$this->collmoderationFlags = null;
		$this->collroughcutEntrysRelatedByRoughcutId = null;
		$this->collroughcutEntrysRelatedByEntryId = null;
		$this->collwidgets = null;
		$this->collflavorParamsOutputs = null;
		$this->collflavorAssets = null;
			$this->akshow = null;
			$this->akuser = null;
			$this->aaccessControl = null;
			$this->aconversionProfile2 = null;
	}

	/* ---------------------- CustomData functions ------------------------- */

	/**
	 * @var myCustomData
	 */
	protected $m_custom_data = null;

	/**
	 * Store custom data old values before the changes
	 * @var        array
	 */
	protected $oldCustomDataValues = array();
	
	/**
	 * @return array
	 */
	public function getCustomDataOldValues()
	{
		return $this->oldCustomDataValues;
	}
	
	/**
	 * @param string $name
	 * @param string $value
	 * @param string $namespace
	 * @return string
	 */
	public function putInCustomData ( $name , $value , $namespace = null )
	{
		$customData = $this->getCustomDataObj( );
		
		$currentNamespace = '';
		if($namespace)
			$currentNamespace = $namespace;
			
		if(!isset($this->oldCustomDataValues[$currentNamespace]))
			$this->oldCustomDataValues[$currentNamespace] = array();
		if(!isset($this->oldCustomDataValues[$currentNamespace][$name]))
			$this->oldCustomDataValues[$currentNamespace][$name] = $customData->get($name, $namespace);
		
		$customData->put ( $name , $value , $namespace );
	}

	/**
	 * @param string $name
	 * @param string $namespace
	 * @param string $defaultValue
	 * @return string
	 */
	public function getFromCustomData ( $name , $namespace = null , $defaultValue = null )
	{
		$customData = $this->getCustomDataObj( );
		$res = $customData->get ( $name , $namespace );
		if ( $res === null ) return $defaultValue;
		return $res;
	}

	/**
	 * @param string $name
	 * @param string $namespace
	 */
	public function removeFromCustomData ( $name , $namespace = null)
	{

		$customData = $this->getCustomDataObj( );
		return $customData->remove ( $name , $namespace );
	}

	/**
	 * @param string $name
	 * @param int $delta
	 * @param string $namespace
	 * @return string
	 */
	public function incInCustomData ( $name , $delta = 1, $namespace = null)
	{
		$customData = $this->getCustomDataObj( );
		return $customData->inc ( $name , $delta , $namespace  );
	}

	/**
	 * @param string $name
	 * @param int $delta
	 * @param string $namespace
	 * @return string
	 */
	public function decInCustomData ( $name , $delta = 1, $namespace = null)
	{
		$customData = $this->getCustomDataObj(  );
		return $customData->dec ( $name , $delta , $namespace );
	}

	/**
	 * @return myCustomData
	 */
	public function getCustomDataObj( )
	{
		if ( ! $this->m_custom_data )
		{
			$this->m_custom_data = myCustomData::fromString ( $this->getCustomData() );
		}
		return $this->m_custom_data;
	}
	
	/**
	 * Must be called before saving the object
	 */
	public function setCustomDataObj()
	{
		if ( $this->m_custom_data != null )
		{
			$this->setCustomData( $this->m_custom_data->toString() );
		}
	}
	
	/* ---------------------- CustomData functions ------------------------- */
	
} // Baseentry
