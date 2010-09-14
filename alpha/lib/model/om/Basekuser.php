<?php

/**
 * Base class that represents a row from the 'kuser' table.
 *
 * 
 *
 * @package    lib.model.om
 */
abstract class Basekuser extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        kuserPeer
	 */
	protected static $peer;

	/**
	 * The value for the id field.
	 * @var        int
	 */
	protected $id;

	/**
	 * The value for the screen_name field.
	 * @var        string
	 */
	protected $screen_name;

	/**
	 * The value for the full_name field.
	 * @var        string
	 */
	protected $full_name;

	/**
	 * The value for the email field.
	 * @var        string
	 */
	protected $email;

	/**
	 * The value for the sha1_password field.
	 * @var        string
	 */
	protected $sha1_password;

	/**
	 * The value for the salt field.
	 * @var        string
	 */
	protected $salt;

	/**
	 * The value for the date_of_birth field.
	 * @var        string
	 */
	protected $date_of_birth;

	/**
	 * The value for the country field.
	 * @var        string
	 */
	protected $country;

	/**
	 * The value for the state field.
	 * @var        string
	 */
	protected $state;

	/**
	 * The value for the city field.
	 * @var        string
	 */
	protected $city;

	/**
	 * The value for the zip field.
	 * @var        string
	 */
	protected $zip;

	/**
	 * The value for the url_list field.
	 * @var        string
	 */
	protected $url_list;

	/**
	 * The value for the picture field.
	 * @var        string
	 */
	protected $picture;

	/**
	 * The value for the icon field.
	 * @var        int
	 */
	protected $icon;

	/**
	 * The value for the about_me field.
	 * @var        string
	 */
	protected $about_me;

	/**
	 * The value for the tags field.
	 * @var        string
	 */
	protected $tags;

	/**
	 * The value for the tagline field.
	 * @var        string
	 */
	protected $tagline;

	/**
	 * The value for the network_highschool field.
	 * @var        string
	 */
	protected $network_highschool;

	/**
	 * The value for the network_college field.
	 * @var        string
	 */
	protected $network_college;

	/**
	 * The value for the network_other field.
	 * @var        string
	 */
	protected $network_other;

	/**
	 * The value for the mobile_num field.
	 * @var        string
	 */
	protected $mobile_num;

	/**
	 * The value for the mature_content field.
	 * @var        int
	 */
	protected $mature_content;

	/**
	 * The value for the gender field.
	 * @var        int
	 */
	protected $gender;

	/**
	 * The value for the registration_ip field.
	 * @var        int
	 */
	protected $registration_ip;

	/**
	 * The value for the registration_cookie field.
	 * @var        string
	 */
	protected $registration_cookie;

	/**
	 * The value for the im_list field.
	 * @var        string
	 */
	protected $im_list;

	/**
	 * The value for the views field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $views;

	/**
	 * The value for the fans field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $fans;

	/**
	 * The value for the entries field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $entries;

	/**
	 * The value for the storage_size field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $storage_size;

	/**
	 * The value for the produced_kshows field.
	 * Note: this column has a database default value of: 0
	 * @var        int
	 */
	protected $produced_kshows;

	/**
	 * The value for the status field.
	 * @var        int
	 */
	protected $status;

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
	 * The value for the search_text field.
	 * @var        string
	 */
	protected $search_text;

	/**
	 * The value for the partner_data field.
	 * @var        string
	 */
	protected $partner_data;

	/**
	 * The value for the puser_id field.
	 * @var        string
	 */
	protected $puser_id;

	/**
	 * The value for the admin_tags field.
	 * @var        string
	 */
	protected $admin_tags;

	/**
	 * The value for the indexed_partner_data_int field.
	 * @var        int
	 */
	protected $indexed_partner_data_int;

	/**
	 * The value for the indexed_partner_data_string field.
	 * @var        string
	 */
	protected $indexed_partner_data_string;

	/**
	 * @var        array kshow[] Collection to store aggregation of kshow objects.
	 */
	protected $collkshows;

	/**
	 * @var        Criteria The criteria used to select the current contents of collkshows.
	 */
	private $lastkshowCriteria = null;

	/**
	 * @var        array entry[] Collection to store aggregation of entry objects.
	 */
	protected $collentrys;

	/**
	 * @var        Criteria The criteria used to select the current contents of collentrys.
	 */
	private $lastentryCriteria = null;

	/**
	 * @var        array comment[] Collection to store aggregation of comment objects.
	 */
	protected $collcomments;

	/**
	 * @var        Criteria The criteria used to select the current contents of collcomments.
	 */
	private $lastcommentCriteria = null;

	/**
	 * @var        array flag[] Collection to store aggregation of flag objects.
	 */
	protected $collflags;

	/**
	 * @var        Criteria The criteria used to select the current contents of collflags.
	 */
	private $lastflagCriteria = null;

	/**
	 * @var        array favorite[] Collection to store aggregation of favorite objects.
	 */
	protected $collfavorites;

	/**
	 * @var        Criteria The criteria used to select the current contents of collfavorites.
	 */
	private $lastfavoriteCriteria = null;

	/**
	 * @var        array KshowKuser[] Collection to store aggregation of KshowKuser objects.
	 */
	protected $collKshowKusers;

	/**
	 * @var        Criteria The criteria used to select the current contents of collKshowKusers.
	 */
	private $lastKshowKuserCriteria = null;

	/**
	 * @var        array MailJob[] Collection to store aggregation of MailJob objects.
	 */
	protected $collMailJobs;

	/**
	 * @var        Criteria The criteria used to select the current contents of collMailJobs.
	 */
	private $lastMailJobCriteria = null;

	/**
	 * @var        array PuserKuser[] Collection to store aggregation of PuserKuser objects.
	 */
	protected $collPuserKusers;

	/**
	 * @var        Criteria The criteria used to select the current contents of collPuserKusers.
	 */
	private $lastPuserKuserCriteria = null;

	/**
	 * @var        array Partner[] Collection to store aggregation of Partner objects.
	 */
	protected $collPartners;

	/**
	 * @var        Criteria The criteria used to select the current contents of collPartners.
	 */
	private $lastPartnerCriteria = null;

	/**
	 * @var        array moderation[] Collection to store aggregation of moderation objects.
	 */
	protected $collmoderations;

	/**
	 * @var        Criteria The criteria used to select the current contents of collmoderations.
	 */
	private $lastmoderationCriteria = null;

	/**
	 * @var        array moderationFlag[] Collection to store aggregation of moderationFlag objects.
	 */
	protected $collmoderationFlagsRelatedByKuserId;

	/**
	 * @var        Criteria The criteria used to select the current contents of collmoderationFlagsRelatedByKuserId.
	 */
	private $lastmoderationFlagRelatedByKuserIdCriteria = null;

	/**
	 * @var        array moderationFlag[] Collection to store aggregation of moderationFlag objects.
	 */
	protected $collmoderationFlagsRelatedByFlaggedKuserId;

	/**
	 * @var        Criteria The criteria used to select the current contents of collmoderationFlagsRelatedByFlaggedKuserId.
	 */
	private $lastmoderationFlagRelatedByFlaggedKuserIdCriteria = null;

	/**
	 * @var        array UploadToken[] Collection to store aggregation of UploadToken objects.
	 */
	protected $collUploadTokens;

	/**
	 * @var        Criteria The criteria used to select the current contents of collUploadTokens.
	 */
	private $lastUploadTokenCriteria = null;

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
		$this->fans = 0;
		$this->entries = 0;
		$this->storage_size = 0;
		$this->produced_kshows = 0;
		$this->partner_id = 0;
	}

	/**
	 * Initializes internal state of Basekuser object.
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
	 * @return     int
	 */
	public function getId()
	{
		return $this->id;
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
	 * Get the [full_name] column value.
	 * 
	 * @return     string
	 */
	public function getFullName()
	{
		return $this->full_name;
	}

	/**
	 * Get the [email] column value.
	 * 
	 * @return     string
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * Get the [sha1_password] column value.
	 * 
	 * @return     string
	 */
	public function getSha1Password()
	{
		return $this->sha1_password;
	}

	/**
	 * Get the [salt] column value.
	 * 
	 * @return     string
	 */
	public function getSalt()
	{
		return $this->salt;
	}

	/**
	 * Get the [optionally formatted] temporal [date_of_birth] column value.
	 * 
	 * This accessor only only work with unix epoch dates.  Consider enabling the propel.useDateTimeClass
	 * option in order to avoid converstions to integers (which are limited in the dates they can express).
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getDateOfBirth($format = '%x')
	{
		if ($this->date_of_birth === null) {
			return null;
		}


		if ($this->date_of_birth === '0000-00-00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->date_of_birth);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->date_of_birth, true), $x);
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
	 * Get the [country] column value.
	 * 
	 * @return     string
	 */
	public function getCountry()
	{
		return $this->country;
	}

	/**
	 * Get the [state] column value.
	 * 
	 * @return     string
	 */
	public function getState()
	{
		return $this->state;
	}

	/**
	 * Get the [city] column value.
	 * 
	 * @return     string
	 */
	public function getCity()
	{
		return $this->city;
	}

	/**
	 * Get the [zip] column value.
	 * 
	 * @return     string
	 */
	public function getZip()
	{
		return $this->zip;
	}

	/**
	 * Get the [url_list] column value.
	 * 
	 * @return     string
	 */
	public function getUrlList()
	{
		return $this->url_list;
	}

	/**
	 * Get the [picture] column value.
	 * 
	 * @return     string
	 */
	public function getPicture()
	{
		return $this->picture;
	}

	/**
	 * Get the [icon] column value.
	 * 
	 * @return     int
	 */
	public function getIcon()
	{
		return $this->icon;
	}

	/**
	 * Get the [about_me] column value.
	 * 
	 * @return     string
	 */
	public function getAboutMe()
	{
		return $this->about_me;
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
	 * Get the [tagline] column value.
	 * 
	 * @return     string
	 */
	public function getTagline()
	{
		return $this->tagline;
	}

	/**
	 * Get the [network_highschool] column value.
	 * 
	 * @return     string
	 */
	public function getNetworkHighschool()
	{
		return $this->network_highschool;
	}

	/**
	 * Get the [network_college] column value.
	 * 
	 * @return     string
	 */
	public function getNetworkCollege()
	{
		return $this->network_college;
	}

	/**
	 * Get the [network_other] column value.
	 * 
	 * @return     string
	 */
	public function getNetworkOther()
	{
		return $this->network_other;
	}

	/**
	 * Get the [mobile_num] column value.
	 * 
	 * @return     string
	 */
	public function getMobileNum()
	{
		return $this->mobile_num;
	}

	/**
	 * Get the [mature_content] column value.
	 * 
	 * @return     int
	 */
	public function getMatureContent()
	{
		return $this->mature_content;
	}

	/**
	 * Get the [gender] column value.
	 * 
	 * @return     int
	 */
	public function getGender()
	{
		return $this->gender;
	}

	/**
	 * Get the [registration_ip] column value.
	 * 
	 * @return     int
	 */
	public function getRegistrationIp()
	{
		return $this->registration_ip;
	}

	/**
	 * Get the [registration_cookie] column value.
	 * 
	 * @return     string
	 */
	public function getRegistrationCookie()
	{
		return $this->registration_cookie;
	}

	/**
	 * Get the [im_list] column value.
	 * 
	 * @return     string
	 */
	public function getImList()
	{
		return $this->im_list;
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
	 * Get the [fans] column value.
	 * 
	 * @return     int
	 */
	public function getFans()
	{
		return $this->fans;
	}

	/**
	 * Get the [entries] column value.
	 * 
	 * @return     int
	 */
	public function getEntries()
	{
		return $this->entries;
	}

	/**
	 * Get the [storage_size] column value.
	 * 
	 * @return     int
	 */
	public function getStorageSize()
	{
		return $this->storage_size;
	}

	/**
	 * Get the [produced_kshows] column value.
	 * 
	 * @return     int
	 */
	public function getProducedKshows()
	{
		return $this->produced_kshows;
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
	 * Get the [search_text] column value.
	 * 
	 * @return     string
	 */
	public function getSearchText()
	{
		return $this->search_text;
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
	 * Get the [puser_id] column value.
	 * 
	 * @return     string
	 */
	public function getPuserId()
	{
		return $this->puser_id;
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
	 * Get the [indexed_partner_data_int] column value.
	 * 
	 * @return     int
	 */
	public function getIndexedPartnerDataInt()
	{
		return $this->indexed_partner_data_int;
	}

	/**
	 * Get the [indexed_partner_data_string] column value.
	 * 
	 * @return     string
	 */
	public function getIndexedPartnerDataString()
	{
		return $this->indexed_partner_data_string;
	}

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::ID]))
			$this->oldColumnsValues[kuserPeer::ID] = $this->getId();

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = kuserPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Set the value of [screen_name] column.
	 * 
	 * @param      string $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setScreenName($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::SCREEN_NAME]))
			$this->oldColumnsValues[kuserPeer::SCREEN_NAME] = $this->getScreenName();

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->screen_name !== $v) {
			$this->screen_name = $v;
			$this->modifiedColumns[] = kuserPeer::SCREEN_NAME;
		}

		return $this;
	} // setScreenName()

	/**
	 * Set the value of [full_name] column.
	 * 
	 * @param      string $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setFullName($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::FULL_NAME]))
			$this->oldColumnsValues[kuserPeer::FULL_NAME] = $this->getFullName();

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->full_name !== $v) {
			$this->full_name = $v;
			$this->modifiedColumns[] = kuserPeer::FULL_NAME;
		}

		return $this;
	} // setFullName()

	/**
	 * Set the value of [email] column.
	 * 
	 * @param      string $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setEmail($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::EMAIL]))
			$this->oldColumnsValues[kuserPeer::EMAIL] = $this->getEmail();

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->email !== $v) {
			$this->email = $v;
			$this->modifiedColumns[] = kuserPeer::EMAIL;
		}

		return $this;
	} // setEmail()

	/**
	 * Set the value of [sha1_password] column.
	 * 
	 * @param      string $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setSha1Password($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::SHA1_PASSWORD]))
			$this->oldColumnsValues[kuserPeer::SHA1_PASSWORD] = $this->getSha1Password();

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->sha1_password !== $v) {
			$this->sha1_password = $v;
			$this->modifiedColumns[] = kuserPeer::SHA1_PASSWORD;
		}

		return $this;
	} // setSha1Password()

	/**
	 * Set the value of [salt] column.
	 * 
	 * @param      string $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setSalt($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::SALT]))
			$this->oldColumnsValues[kuserPeer::SALT] = $this->getSalt();

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->salt !== $v) {
			$this->salt = $v;
			$this->modifiedColumns[] = kuserPeer::SALT;
		}

		return $this;
	} // setSalt()

	/**
	 * Sets the value of [date_of_birth] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setDateOfBirth($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::DATE_OF_BIRTH]))
			$this->oldColumnsValues[kuserPeer::DATE_OF_BIRTH] = $this->getDateOfBirth();

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

		if ( $this->date_of_birth !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->date_of_birth !== null && $tmpDt = new DateTime($this->date_of_birth)) ? $tmpDt->format('Y-m-d') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->date_of_birth = ($dt ? $dt->format('Y-m-d') : null);
				$this->modifiedColumns[] = kuserPeer::DATE_OF_BIRTH;
			}
		} // if either are not null

		return $this;
	} // setDateOfBirth()

	/**
	 * Set the value of [country] column.
	 * 
	 * @param      string $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setCountry($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::COUNTRY]))
			$this->oldColumnsValues[kuserPeer::COUNTRY] = $this->getCountry();

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->country !== $v) {
			$this->country = $v;
			$this->modifiedColumns[] = kuserPeer::COUNTRY;
		}

		return $this;
	} // setCountry()

	/**
	 * Set the value of [state] column.
	 * 
	 * @param      string $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setState($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::STATE]))
			$this->oldColumnsValues[kuserPeer::STATE] = $this->getState();

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->state !== $v) {
			$this->state = $v;
			$this->modifiedColumns[] = kuserPeer::STATE;
		}

		return $this;
	} // setState()

	/**
	 * Set the value of [city] column.
	 * 
	 * @param      string $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setCity($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::CITY]))
			$this->oldColumnsValues[kuserPeer::CITY] = $this->getCity();

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->city !== $v) {
			$this->city = $v;
			$this->modifiedColumns[] = kuserPeer::CITY;
		}

		return $this;
	} // setCity()

	/**
	 * Set the value of [zip] column.
	 * 
	 * @param      string $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setZip($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::ZIP]))
			$this->oldColumnsValues[kuserPeer::ZIP] = $this->getZip();

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->zip !== $v) {
			$this->zip = $v;
			$this->modifiedColumns[] = kuserPeer::ZIP;
		}

		return $this;
	} // setZip()

	/**
	 * Set the value of [url_list] column.
	 * 
	 * @param      string $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setUrlList($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::URL_LIST]))
			$this->oldColumnsValues[kuserPeer::URL_LIST] = $this->getUrlList();

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->url_list !== $v) {
			$this->url_list = $v;
			$this->modifiedColumns[] = kuserPeer::URL_LIST;
		}

		return $this;
	} // setUrlList()

	/**
	 * Set the value of [picture] column.
	 * 
	 * @param      string $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setPicture($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::PICTURE]))
			$this->oldColumnsValues[kuserPeer::PICTURE] = $this->getPicture();

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->picture !== $v) {
			$this->picture = $v;
			$this->modifiedColumns[] = kuserPeer::PICTURE;
		}

		return $this;
	} // setPicture()

	/**
	 * Set the value of [icon] column.
	 * 
	 * @param      int $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setIcon($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::ICON]))
			$this->oldColumnsValues[kuserPeer::ICON] = $this->getIcon();

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->icon !== $v) {
			$this->icon = $v;
			$this->modifiedColumns[] = kuserPeer::ICON;
		}

		return $this;
	} // setIcon()

	/**
	 * Set the value of [about_me] column.
	 * 
	 * @param      string $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setAboutMe($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::ABOUT_ME]))
			$this->oldColumnsValues[kuserPeer::ABOUT_ME] = $this->getAboutMe();

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->about_me !== $v) {
			$this->about_me = $v;
			$this->modifiedColumns[] = kuserPeer::ABOUT_ME;
		}

		return $this;
	} // setAboutMe()

	/**
	 * Set the value of [tags] column.
	 * 
	 * @param      string $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setTags($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::TAGS]))
			$this->oldColumnsValues[kuserPeer::TAGS] = $this->getTags();

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->tags !== $v) {
			$this->tags = $v;
			$this->modifiedColumns[] = kuserPeer::TAGS;
		}

		return $this;
	} // setTags()

	/**
	 * Set the value of [tagline] column.
	 * 
	 * @param      string $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setTagline($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::TAGLINE]))
			$this->oldColumnsValues[kuserPeer::TAGLINE] = $this->getTagline();

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->tagline !== $v) {
			$this->tagline = $v;
			$this->modifiedColumns[] = kuserPeer::TAGLINE;
		}

		return $this;
	} // setTagline()

	/**
	 * Set the value of [network_highschool] column.
	 * 
	 * @param      string $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setNetworkHighschool($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::NETWORK_HIGHSCHOOL]))
			$this->oldColumnsValues[kuserPeer::NETWORK_HIGHSCHOOL] = $this->getNetworkHighschool();

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->network_highschool !== $v) {
			$this->network_highschool = $v;
			$this->modifiedColumns[] = kuserPeer::NETWORK_HIGHSCHOOL;
		}

		return $this;
	} // setNetworkHighschool()

	/**
	 * Set the value of [network_college] column.
	 * 
	 * @param      string $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setNetworkCollege($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::NETWORK_COLLEGE]))
			$this->oldColumnsValues[kuserPeer::NETWORK_COLLEGE] = $this->getNetworkCollege();

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->network_college !== $v) {
			$this->network_college = $v;
			$this->modifiedColumns[] = kuserPeer::NETWORK_COLLEGE;
		}

		return $this;
	} // setNetworkCollege()

	/**
	 * Set the value of [network_other] column.
	 * 
	 * @param      string $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setNetworkOther($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::NETWORK_OTHER]))
			$this->oldColumnsValues[kuserPeer::NETWORK_OTHER] = $this->getNetworkOther();

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->network_other !== $v) {
			$this->network_other = $v;
			$this->modifiedColumns[] = kuserPeer::NETWORK_OTHER;
		}

		return $this;
	} // setNetworkOther()

	/**
	 * Set the value of [mobile_num] column.
	 * 
	 * @param      string $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setMobileNum($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::MOBILE_NUM]))
			$this->oldColumnsValues[kuserPeer::MOBILE_NUM] = $this->getMobileNum();

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->mobile_num !== $v) {
			$this->mobile_num = $v;
			$this->modifiedColumns[] = kuserPeer::MOBILE_NUM;
		}

		return $this;
	} // setMobileNum()

	/**
	 * Set the value of [mature_content] column.
	 * 
	 * @param      int $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setMatureContent($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::MATURE_CONTENT]))
			$this->oldColumnsValues[kuserPeer::MATURE_CONTENT] = $this->getMatureContent();

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->mature_content !== $v) {
			$this->mature_content = $v;
			$this->modifiedColumns[] = kuserPeer::MATURE_CONTENT;
		}

		return $this;
	} // setMatureContent()

	/**
	 * Set the value of [gender] column.
	 * 
	 * @param      int $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setGender($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::GENDER]))
			$this->oldColumnsValues[kuserPeer::GENDER] = $this->getGender();

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->gender !== $v) {
			$this->gender = $v;
			$this->modifiedColumns[] = kuserPeer::GENDER;
		}

		return $this;
	} // setGender()

	/**
	 * Set the value of [registration_ip] column.
	 * 
	 * @param      int $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setRegistrationIp($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::REGISTRATION_IP]))
			$this->oldColumnsValues[kuserPeer::REGISTRATION_IP] = $this->getRegistrationIp();

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->registration_ip !== $v) {
			$this->registration_ip = $v;
			$this->modifiedColumns[] = kuserPeer::REGISTRATION_IP;
		}

		return $this;
	} // setRegistrationIp()

	/**
	 * Set the value of [registration_cookie] column.
	 * 
	 * @param      string $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setRegistrationCookie($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::REGISTRATION_COOKIE]))
			$this->oldColumnsValues[kuserPeer::REGISTRATION_COOKIE] = $this->getRegistrationCookie();

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->registration_cookie !== $v) {
			$this->registration_cookie = $v;
			$this->modifiedColumns[] = kuserPeer::REGISTRATION_COOKIE;
		}

		return $this;
	} // setRegistrationCookie()

	/**
	 * Set the value of [im_list] column.
	 * 
	 * @param      string $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setImList($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::IM_LIST]))
			$this->oldColumnsValues[kuserPeer::IM_LIST] = $this->getImList();

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->im_list !== $v) {
			$this->im_list = $v;
			$this->modifiedColumns[] = kuserPeer::IM_LIST;
		}

		return $this;
	} // setImList()

	/**
	 * Set the value of [views] column.
	 * 
	 * @param      int $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setViews($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::VIEWS]))
			$this->oldColumnsValues[kuserPeer::VIEWS] = $this->getViews();

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->views !== $v || $this->isNew()) {
			$this->views = $v;
			$this->modifiedColumns[] = kuserPeer::VIEWS;
		}

		return $this;
	} // setViews()

	/**
	 * Set the value of [fans] column.
	 * 
	 * @param      int $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setFans($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::FANS]))
			$this->oldColumnsValues[kuserPeer::FANS] = $this->getFans();

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->fans !== $v || $this->isNew()) {
			$this->fans = $v;
			$this->modifiedColumns[] = kuserPeer::FANS;
		}

		return $this;
	} // setFans()

	/**
	 * Set the value of [entries] column.
	 * 
	 * @param      int $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setEntries($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::ENTRIES]))
			$this->oldColumnsValues[kuserPeer::ENTRIES] = $this->getEntries();

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->entries !== $v || $this->isNew()) {
			$this->entries = $v;
			$this->modifiedColumns[] = kuserPeer::ENTRIES;
		}

		return $this;
	} // setEntries()

	/**
	 * Set the value of [storage_size] column.
	 * 
	 * @param      int $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setStorageSize($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::STORAGE_SIZE]))
			$this->oldColumnsValues[kuserPeer::STORAGE_SIZE] = $this->getStorageSize();

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->storage_size !== $v || $this->isNew()) {
			$this->storage_size = $v;
			$this->modifiedColumns[] = kuserPeer::STORAGE_SIZE;
		}

		return $this;
	} // setStorageSize()

	/**
	 * Set the value of [produced_kshows] column.
	 * 
	 * @param      int $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setProducedKshows($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::PRODUCED_KSHOWS]))
			$this->oldColumnsValues[kuserPeer::PRODUCED_KSHOWS] = $this->getProducedKshows();

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->produced_kshows !== $v || $this->isNew()) {
			$this->produced_kshows = $v;
			$this->modifiedColumns[] = kuserPeer::PRODUCED_KSHOWS;
		}

		return $this;
	} // setProducedKshows()

	/**
	 * Set the value of [status] column.
	 * 
	 * @param      int $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setStatus($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::STATUS]))
			$this->oldColumnsValues[kuserPeer::STATUS] = $this->getStatus();

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->status !== $v) {
			$this->status = $v;
			$this->modifiedColumns[] = kuserPeer::STATUS;
		}

		return $this;
	} // setStatus()

	/**
	 * Sets the value of [created_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     kuser The current object (for fluent API support)
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
				$this->modifiedColumns[] = kuserPeer::CREATED_AT;
			}
		} // if either are not null

		return $this;
	} // setCreatedAt()

	/**
	 * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     kuser The current object (for fluent API support)
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
				$this->modifiedColumns[] = kuserPeer::UPDATED_AT;
			}
		} // if either are not null

		return $this;
	} // setUpdatedAt()

	/**
	 * Set the value of [partner_id] column.
	 * 
	 * @param      int $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setPartnerId($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::PARTNER_ID]))
			$this->oldColumnsValues[kuserPeer::PARTNER_ID] = $this->getPartnerId();

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->partner_id !== $v || $this->isNew()) {
			$this->partner_id = $v;
			$this->modifiedColumns[] = kuserPeer::PARTNER_ID;
		}

		return $this;
	} // setPartnerId()

	/**
	 * Set the value of [display_in_search] column.
	 * 
	 * @param      int $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setDisplayInSearch($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::DISPLAY_IN_SEARCH]))
			$this->oldColumnsValues[kuserPeer::DISPLAY_IN_SEARCH] = $this->getDisplayInSearch();

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->display_in_search !== $v) {
			$this->display_in_search = $v;
			$this->modifiedColumns[] = kuserPeer::DISPLAY_IN_SEARCH;
		}

		return $this;
	} // setDisplayInSearch()

	/**
	 * Set the value of [search_text] column.
	 * 
	 * @param      string $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setSearchText($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::SEARCH_TEXT]))
			$this->oldColumnsValues[kuserPeer::SEARCH_TEXT] = $this->getSearchText();

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->search_text !== $v) {
			$this->search_text = $v;
			$this->modifiedColumns[] = kuserPeer::SEARCH_TEXT;
		}

		return $this;
	} // setSearchText()

	/**
	 * Set the value of [partner_data] column.
	 * 
	 * @param      string $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setPartnerData($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::PARTNER_DATA]))
			$this->oldColumnsValues[kuserPeer::PARTNER_DATA] = $this->getPartnerData();

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->partner_data !== $v) {
			$this->partner_data = $v;
			$this->modifiedColumns[] = kuserPeer::PARTNER_DATA;
		}

		return $this;
	} // setPartnerData()

	/**
	 * Set the value of [puser_id] column.
	 * 
	 * @param      string $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setPuserId($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::PUSER_ID]))
			$this->oldColumnsValues[kuserPeer::PUSER_ID] = $this->getPuserId();

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->puser_id !== $v) {
			$this->puser_id = $v;
			$this->modifiedColumns[] = kuserPeer::PUSER_ID;
		}

		return $this;
	} // setPuserId()

	/**
	 * Set the value of [admin_tags] column.
	 * 
	 * @param      string $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setAdminTags($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::ADMIN_TAGS]))
			$this->oldColumnsValues[kuserPeer::ADMIN_TAGS] = $this->getAdminTags();

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->admin_tags !== $v) {
			$this->admin_tags = $v;
			$this->modifiedColumns[] = kuserPeer::ADMIN_TAGS;
		}

		return $this;
	} // setAdminTags()

	/**
	 * Set the value of [indexed_partner_data_int] column.
	 * 
	 * @param      int $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setIndexedPartnerDataInt($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::INDEXED_PARTNER_DATA_INT]))
			$this->oldColumnsValues[kuserPeer::INDEXED_PARTNER_DATA_INT] = $this->getIndexedPartnerDataInt();

		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->indexed_partner_data_int !== $v) {
			$this->indexed_partner_data_int = $v;
			$this->modifiedColumns[] = kuserPeer::INDEXED_PARTNER_DATA_INT;
		}

		return $this;
	} // setIndexedPartnerDataInt()

	/**
	 * Set the value of [indexed_partner_data_string] column.
	 * 
	 * @param      string $v new value
	 * @return     kuser The current object (for fluent API support)
	 */
	public function setIndexedPartnerDataString($v)
	{
		if(!isset($this->oldColumnsValues[kuserPeer::INDEXED_PARTNER_DATA_STRING]))
			$this->oldColumnsValues[kuserPeer::INDEXED_PARTNER_DATA_STRING] = $this->getIndexedPartnerDataString();

		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->indexed_partner_data_string !== $v) {
			$this->indexed_partner_data_string = $v;
			$this->modifiedColumns[] = kuserPeer::INDEXED_PARTNER_DATA_STRING;
		}

		return $this;
	} // setIndexedPartnerDataString()

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

			if ($this->fans !== 0) {
				return false;
			}

			if ($this->entries !== 0) {
				return false;
			}

			if ($this->storage_size !== 0) {
				return false;
			}

			if ($this->produced_kshows !== 0) {
				return false;
			}

			if ($this->partner_id !== 0) {
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

			$this->id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
			$this->screen_name = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
			$this->full_name = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->email = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->sha1_password = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->salt = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->date_of_birth = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->country = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->state = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->city = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
			$this->zip = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
			$this->url_list = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
			$this->picture = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
			$this->icon = ($row[$startcol + 13] !== null) ? (int) $row[$startcol + 13] : null;
			$this->about_me = ($row[$startcol + 14] !== null) ? (string) $row[$startcol + 14] : null;
			$this->tags = ($row[$startcol + 15] !== null) ? (string) $row[$startcol + 15] : null;
			$this->tagline = ($row[$startcol + 16] !== null) ? (string) $row[$startcol + 16] : null;
			$this->network_highschool = ($row[$startcol + 17] !== null) ? (string) $row[$startcol + 17] : null;
			$this->network_college = ($row[$startcol + 18] !== null) ? (string) $row[$startcol + 18] : null;
			$this->network_other = ($row[$startcol + 19] !== null) ? (string) $row[$startcol + 19] : null;
			$this->mobile_num = ($row[$startcol + 20] !== null) ? (string) $row[$startcol + 20] : null;
			$this->mature_content = ($row[$startcol + 21] !== null) ? (int) $row[$startcol + 21] : null;
			$this->gender = ($row[$startcol + 22] !== null) ? (int) $row[$startcol + 22] : null;
			$this->registration_ip = ($row[$startcol + 23] !== null) ? (int) $row[$startcol + 23] : null;
			$this->registration_cookie = ($row[$startcol + 24] !== null) ? (string) $row[$startcol + 24] : null;
			$this->im_list = ($row[$startcol + 25] !== null) ? (string) $row[$startcol + 25] : null;
			$this->views = ($row[$startcol + 26] !== null) ? (int) $row[$startcol + 26] : null;
			$this->fans = ($row[$startcol + 27] !== null) ? (int) $row[$startcol + 27] : null;
			$this->entries = ($row[$startcol + 28] !== null) ? (int) $row[$startcol + 28] : null;
			$this->storage_size = ($row[$startcol + 29] !== null) ? (int) $row[$startcol + 29] : null;
			$this->produced_kshows = ($row[$startcol + 30] !== null) ? (int) $row[$startcol + 30] : null;
			$this->status = ($row[$startcol + 31] !== null) ? (int) $row[$startcol + 31] : null;
			$this->created_at = ($row[$startcol + 32] !== null) ? (string) $row[$startcol + 32] : null;
			$this->updated_at = ($row[$startcol + 33] !== null) ? (string) $row[$startcol + 33] : null;
			$this->partner_id = ($row[$startcol + 34] !== null) ? (int) $row[$startcol + 34] : null;
			$this->display_in_search = ($row[$startcol + 35] !== null) ? (int) $row[$startcol + 35] : null;
			$this->search_text = ($row[$startcol + 36] !== null) ? (string) $row[$startcol + 36] : null;
			$this->partner_data = ($row[$startcol + 37] !== null) ? (string) $row[$startcol + 37] : null;
			$this->puser_id = ($row[$startcol + 38] !== null) ? (string) $row[$startcol + 38] : null;
			$this->admin_tags = ($row[$startcol + 39] !== null) ? (string) $row[$startcol + 39] : null;
			$this->indexed_partner_data_int = ($row[$startcol + 40] !== null) ? (int) $row[$startcol + 40] : null;
			$this->indexed_partner_data_string = ($row[$startcol + 41] !== null) ? (string) $row[$startcol + 41] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 42; // 42 = kuserPeer::NUM_COLUMNS - kuserPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating kuser object", $e);
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
			$con = Propel::getConnection(kuserPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = kuserPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->collkshows = null;
			$this->lastkshowCriteria = null;

			$this->collentrys = null;
			$this->lastentryCriteria = null;

			$this->collcomments = null;
			$this->lastcommentCriteria = null;

			$this->collflags = null;
			$this->lastflagCriteria = null;

			$this->collfavorites = null;
			$this->lastfavoriteCriteria = null;

			$this->collKshowKusers = null;
			$this->lastKshowKuserCriteria = null;

			$this->collMailJobs = null;
			$this->lastMailJobCriteria = null;

			$this->collPuserKusers = null;
			$this->lastPuserKuserCriteria = null;

			$this->collPartners = null;
			$this->lastPartnerCriteria = null;

			$this->collmoderations = null;
			$this->lastmoderationCriteria = null;

			$this->collmoderationFlagsRelatedByKuserId = null;
			$this->lastmoderationFlagRelatedByKuserIdCriteria = null;

			$this->collmoderationFlagsRelatedByFlaggedKuserId = null;
			$this->lastmoderationFlagRelatedByFlaggedKuserIdCriteria = null;

			$this->collUploadTokens = null;
			$this->lastUploadTokenCriteria = null;

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
			$con = Propel::getConnection(kuserPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			if ($ret) {
				kuserPeer::doDelete($this, $con);
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
			$con = Propel::getConnection(kuserPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
				kuserPeer::addInstanceToPool($this);
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

			if ($this->isNew() ) {
				$this->modifiedColumns[] = kuserPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = kuserPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += kuserPeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			if ($this->collkshows !== null) {
				foreach ($this->collkshows as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collentrys !== null) {
				foreach ($this->collentrys as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collcomments !== null) {
				foreach ($this->collcomments as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collflags !== null) {
				foreach ($this->collflags as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collfavorites !== null) {
				foreach ($this->collfavorites as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collKshowKusers !== null) {
				foreach ($this->collKshowKusers as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collMailJobs !== null) {
				foreach ($this->collMailJobs as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collPuserKusers !== null) {
				foreach ($this->collPuserKusers as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collPartners !== null) {
				foreach ($this->collPartners as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collmoderations !== null) {
				foreach ($this->collmoderations as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collmoderationFlagsRelatedByKuserId !== null) {
				foreach ($this->collmoderationFlagsRelatedByKuserId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collmoderationFlagsRelatedByFlaggedKuserId !== null) {
				foreach ($this->collmoderationFlagsRelatedByFlaggedKuserId as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			if ($this->collUploadTokens !== null) {
				foreach ($this->collUploadTokens as $referrerFK) {
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
		return parent::preSave($con);
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
		kuserPeer::setUseCriteriaFilter(false);
		$this->reload();
		kuserPeer::setUseCriteriaFilter(true);
		
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


			if (($retval = kuserPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collkshows !== null) {
					foreach ($this->collkshows as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collentrys !== null) {
					foreach ($this->collentrys as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collcomments !== null) {
					foreach ($this->collcomments as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collflags !== null) {
					foreach ($this->collflags as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collfavorites !== null) {
					foreach ($this->collfavorites as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collKshowKusers !== null) {
					foreach ($this->collKshowKusers as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collMailJobs !== null) {
					foreach ($this->collMailJobs as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collPuserKusers !== null) {
					foreach ($this->collPuserKusers as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collPartners !== null) {
					foreach ($this->collPartners as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collmoderations !== null) {
					foreach ($this->collmoderations as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collmoderationFlagsRelatedByKuserId !== null) {
					foreach ($this->collmoderationFlagsRelatedByKuserId as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collmoderationFlagsRelatedByFlaggedKuserId !== null) {
					foreach ($this->collmoderationFlagsRelatedByFlaggedKuserId as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}

				if ($this->collUploadTokens !== null) {
					foreach ($this->collUploadTokens as $referrerFK) {
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
		$pos = kuserPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				return $this->getScreenName();
				break;
			case 2:
				return $this->getFullName();
				break;
			case 3:
				return $this->getEmail();
				break;
			case 4:
				return $this->getSha1Password();
				break;
			case 5:
				return $this->getSalt();
				break;
			case 6:
				return $this->getDateOfBirth();
				break;
			case 7:
				return $this->getCountry();
				break;
			case 8:
				return $this->getState();
				break;
			case 9:
				return $this->getCity();
				break;
			case 10:
				return $this->getZip();
				break;
			case 11:
				return $this->getUrlList();
				break;
			case 12:
				return $this->getPicture();
				break;
			case 13:
				return $this->getIcon();
				break;
			case 14:
				return $this->getAboutMe();
				break;
			case 15:
				return $this->getTags();
				break;
			case 16:
				return $this->getTagline();
				break;
			case 17:
				return $this->getNetworkHighschool();
				break;
			case 18:
				return $this->getNetworkCollege();
				break;
			case 19:
				return $this->getNetworkOther();
				break;
			case 20:
				return $this->getMobileNum();
				break;
			case 21:
				return $this->getMatureContent();
				break;
			case 22:
				return $this->getGender();
				break;
			case 23:
				return $this->getRegistrationIp();
				break;
			case 24:
				return $this->getRegistrationCookie();
				break;
			case 25:
				return $this->getImList();
				break;
			case 26:
				return $this->getViews();
				break;
			case 27:
				return $this->getFans();
				break;
			case 28:
				return $this->getEntries();
				break;
			case 29:
				return $this->getStorageSize();
				break;
			case 30:
				return $this->getProducedKshows();
				break;
			case 31:
				return $this->getStatus();
				break;
			case 32:
				return $this->getCreatedAt();
				break;
			case 33:
				return $this->getUpdatedAt();
				break;
			case 34:
				return $this->getPartnerId();
				break;
			case 35:
				return $this->getDisplayInSearch();
				break;
			case 36:
				return $this->getSearchText();
				break;
			case 37:
				return $this->getPartnerData();
				break;
			case 38:
				return $this->getPuserId();
				break;
			case 39:
				return $this->getAdminTags();
				break;
			case 40:
				return $this->getIndexedPartnerDataInt();
				break;
			case 41:
				return $this->getIndexedPartnerDataString();
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
		$keys = kuserPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getScreenName(),
			$keys[2] => $this->getFullName(),
			$keys[3] => $this->getEmail(),
			$keys[4] => $this->getSha1Password(),
			$keys[5] => $this->getSalt(),
			$keys[6] => $this->getDateOfBirth(),
			$keys[7] => $this->getCountry(),
			$keys[8] => $this->getState(),
			$keys[9] => $this->getCity(),
			$keys[10] => $this->getZip(),
			$keys[11] => $this->getUrlList(),
			$keys[12] => $this->getPicture(),
			$keys[13] => $this->getIcon(),
			$keys[14] => $this->getAboutMe(),
			$keys[15] => $this->getTags(),
			$keys[16] => $this->getTagline(),
			$keys[17] => $this->getNetworkHighschool(),
			$keys[18] => $this->getNetworkCollege(),
			$keys[19] => $this->getNetworkOther(),
			$keys[20] => $this->getMobileNum(),
			$keys[21] => $this->getMatureContent(),
			$keys[22] => $this->getGender(),
			$keys[23] => $this->getRegistrationIp(),
			$keys[24] => $this->getRegistrationCookie(),
			$keys[25] => $this->getImList(),
			$keys[26] => $this->getViews(),
			$keys[27] => $this->getFans(),
			$keys[28] => $this->getEntries(),
			$keys[29] => $this->getStorageSize(),
			$keys[30] => $this->getProducedKshows(),
			$keys[31] => $this->getStatus(),
			$keys[32] => $this->getCreatedAt(),
			$keys[33] => $this->getUpdatedAt(),
			$keys[34] => $this->getPartnerId(),
			$keys[35] => $this->getDisplayInSearch(),
			$keys[36] => $this->getSearchText(),
			$keys[37] => $this->getPartnerData(),
			$keys[38] => $this->getPuserId(),
			$keys[39] => $this->getAdminTags(),
			$keys[40] => $this->getIndexedPartnerDataInt(),
			$keys[41] => $this->getIndexedPartnerDataString(),
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
		$pos = kuserPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
				$this->setScreenName($value);
				break;
			case 2:
				$this->setFullName($value);
				break;
			case 3:
				$this->setEmail($value);
				break;
			case 4:
				$this->setSha1Password($value);
				break;
			case 5:
				$this->setSalt($value);
				break;
			case 6:
				$this->setDateOfBirth($value);
				break;
			case 7:
				$this->setCountry($value);
				break;
			case 8:
				$this->setState($value);
				break;
			case 9:
				$this->setCity($value);
				break;
			case 10:
				$this->setZip($value);
				break;
			case 11:
				$this->setUrlList($value);
				break;
			case 12:
				$this->setPicture($value);
				break;
			case 13:
				$this->setIcon($value);
				break;
			case 14:
				$this->setAboutMe($value);
				break;
			case 15:
				$this->setTags($value);
				break;
			case 16:
				$this->setTagline($value);
				break;
			case 17:
				$this->setNetworkHighschool($value);
				break;
			case 18:
				$this->setNetworkCollege($value);
				break;
			case 19:
				$this->setNetworkOther($value);
				break;
			case 20:
				$this->setMobileNum($value);
				break;
			case 21:
				$this->setMatureContent($value);
				break;
			case 22:
				$this->setGender($value);
				break;
			case 23:
				$this->setRegistrationIp($value);
				break;
			case 24:
				$this->setRegistrationCookie($value);
				break;
			case 25:
				$this->setImList($value);
				break;
			case 26:
				$this->setViews($value);
				break;
			case 27:
				$this->setFans($value);
				break;
			case 28:
				$this->setEntries($value);
				break;
			case 29:
				$this->setStorageSize($value);
				break;
			case 30:
				$this->setProducedKshows($value);
				break;
			case 31:
				$this->setStatus($value);
				break;
			case 32:
				$this->setCreatedAt($value);
				break;
			case 33:
				$this->setUpdatedAt($value);
				break;
			case 34:
				$this->setPartnerId($value);
				break;
			case 35:
				$this->setDisplayInSearch($value);
				break;
			case 36:
				$this->setSearchText($value);
				break;
			case 37:
				$this->setPartnerData($value);
				break;
			case 38:
				$this->setPuserId($value);
				break;
			case 39:
				$this->setAdminTags($value);
				break;
			case 40:
				$this->setIndexedPartnerDataInt($value);
				break;
			case 41:
				$this->setIndexedPartnerDataString($value);
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
		$keys = kuserPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setScreenName($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setFullName($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setEmail($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setSha1Password($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setSalt($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setDateOfBirth($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setCountry($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setState($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setCity($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setZip($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setUrlList($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setPicture($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setIcon($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setAboutMe($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setTags($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setTagline($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setNetworkHighschool($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setNetworkCollege($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setNetworkOther($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setMobileNum($arr[$keys[20]]);
		if (array_key_exists($keys[21], $arr)) $this->setMatureContent($arr[$keys[21]]);
		if (array_key_exists($keys[22], $arr)) $this->setGender($arr[$keys[22]]);
		if (array_key_exists($keys[23], $arr)) $this->setRegistrationIp($arr[$keys[23]]);
		if (array_key_exists($keys[24], $arr)) $this->setRegistrationCookie($arr[$keys[24]]);
		if (array_key_exists($keys[25], $arr)) $this->setImList($arr[$keys[25]]);
		if (array_key_exists($keys[26], $arr)) $this->setViews($arr[$keys[26]]);
		if (array_key_exists($keys[27], $arr)) $this->setFans($arr[$keys[27]]);
		if (array_key_exists($keys[28], $arr)) $this->setEntries($arr[$keys[28]]);
		if (array_key_exists($keys[29], $arr)) $this->setStorageSize($arr[$keys[29]]);
		if (array_key_exists($keys[30], $arr)) $this->setProducedKshows($arr[$keys[30]]);
		if (array_key_exists($keys[31], $arr)) $this->setStatus($arr[$keys[31]]);
		if (array_key_exists($keys[32], $arr)) $this->setCreatedAt($arr[$keys[32]]);
		if (array_key_exists($keys[33], $arr)) $this->setUpdatedAt($arr[$keys[33]]);
		if (array_key_exists($keys[34], $arr)) $this->setPartnerId($arr[$keys[34]]);
		if (array_key_exists($keys[35], $arr)) $this->setDisplayInSearch($arr[$keys[35]]);
		if (array_key_exists($keys[36], $arr)) $this->setSearchText($arr[$keys[36]]);
		if (array_key_exists($keys[37], $arr)) $this->setPartnerData($arr[$keys[37]]);
		if (array_key_exists($keys[38], $arr)) $this->setPuserId($arr[$keys[38]]);
		if (array_key_exists($keys[39], $arr)) $this->setAdminTags($arr[$keys[39]]);
		if (array_key_exists($keys[40], $arr)) $this->setIndexedPartnerDataInt($arr[$keys[40]]);
		if (array_key_exists($keys[41], $arr)) $this->setIndexedPartnerDataString($arr[$keys[41]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(kuserPeer::DATABASE_NAME);

		if ($this->isColumnModified(kuserPeer::ID)) $criteria->add(kuserPeer::ID, $this->id);
		if ($this->isColumnModified(kuserPeer::SCREEN_NAME)) $criteria->add(kuserPeer::SCREEN_NAME, $this->screen_name);
		if ($this->isColumnModified(kuserPeer::FULL_NAME)) $criteria->add(kuserPeer::FULL_NAME, $this->full_name);
		if ($this->isColumnModified(kuserPeer::EMAIL)) $criteria->add(kuserPeer::EMAIL, $this->email);
		if ($this->isColumnModified(kuserPeer::SHA1_PASSWORD)) $criteria->add(kuserPeer::SHA1_PASSWORD, $this->sha1_password);
		if ($this->isColumnModified(kuserPeer::SALT)) $criteria->add(kuserPeer::SALT, $this->salt);
		if ($this->isColumnModified(kuserPeer::DATE_OF_BIRTH)) $criteria->add(kuserPeer::DATE_OF_BIRTH, $this->date_of_birth);
		if ($this->isColumnModified(kuserPeer::COUNTRY)) $criteria->add(kuserPeer::COUNTRY, $this->country);
		if ($this->isColumnModified(kuserPeer::STATE)) $criteria->add(kuserPeer::STATE, $this->state);
		if ($this->isColumnModified(kuserPeer::CITY)) $criteria->add(kuserPeer::CITY, $this->city);
		if ($this->isColumnModified(kuserPeer::ZIP)) $criteria->add(kuserPeer::ZIP, $this->zip);
		if ($this->isColumnModified(kuserPeer::URL_LIST)) $criteria->add(kuserPeer::URL_LIST, $this->url_list);
		if ($this->isColumnModified(kuserPeer::PICTURE)) $criteria->add(kuserPeer::PICTURE, $this->picture);
		if ($this->isColumnModified(kuserPeer::ICON)) $criteria->add(kuserPeer::ICON, $this->icon);
		if ($this->isColumnModified(kuserPeer::ABOUT_ME)) $criteria->add(kuserPeer::ABOUT_ME, $this->about_me);
		if ($this->isColumnModified(kuserPeer::TAGS)) $criteria->add(kuserPeer::TAGS, $this->tags);
		if ($this->isColumnModified(kuserPeer::TAGLINE)) $criteria->add(kuserPeer::TAGLINE, $this->tagline);
		if ($this->isColumnModified(kuserPeer::NETWORK_HIGHSCHOOL)) $criteria->add(kuserPeer::NETWORK_HIGHSCHOOL, $this->network_highschool);
		if ($this->isColumnModified(kuserPeer::NETWORK_COLLEGE)) $criteria->add(kuserPeer::NETWORK_COLLEGE, $this->network_college);
		if ($this->isColumnModified(kuserPeer::NETWORK_OTHER)) $criteria->add(kuserPeer::NETWORK_OTHER, $this->network_other);
		if ($this->isColumnModified(kuserPeer::MOBILE_NUM)) $criteria->add(kuserPeer::MOBILE_NUM, $this->mobile_num);
		if ($this->isColumnModified(kuserPeer::MATURE_CONTENT)) $criteria->add(kuserPeer::MATURE_CONTENT, $this->mature_content);
		if ($this->isColumnModified(kuserPeer::GENDER)) $criteria->add(kuserPeer::GENDER, $this->gender);
		if ($this->isColumnModified(kuserPeer::REGISTRATION_IP)) $criteria->add(kuserPeer::REGISTRATION_IP, $this->registration_ip);
		if ($this->isColumnModified(kuserPeer::REGISTRATION_COOKIE)) $criteria->add(kuserPeer::REGISTRATION_COOKIE, $this->registration_cookie);
		if ($this->isColumnModified(kuserPeer::IM_LIST)) $criteria->add(kuserPeer::IM_LIST, $this->im_list);
		if ($this->isColumnModified(kuserPeer::VIEWS)) $criteria->add(kuserPeer::VIEWS, $this->views);
		if ($this->isColumnModified(kuserPeer::FANS)) $criteria->add(kuserPeer::FANS, $this->fans);
		if ($this->isColumnModified(kuserPeer::ENTRIES)) $criteria->add(kuserPeer::ENTRIES, $this->entries);
		if ($this->isColumnModified(kuserPeer::STORAGE_SIZE)) $criteria->add(kuserPeer::STORAGE_SIZE, $this->storage_size);
		if ($this->isColumnModified(kuserPeer::PRODUCED_KSHOWS)) $criteria->add(kuserPeer::PRODUCED_KSHOWS, $this->produced_kshows);
		if ($this->isColumnModified(kuserPeer::STATUS)) $criteria->add(kuserPeer::STATUS, $this->status);
		if ($this->isColumnModified(kuserPeer::CREATED_AT)) $criteria->add(kuserPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(kuserPeer::UPDATED_AT)) $criteria->add(kuserPeer::UPDATED_AT, $this->updated_at);
		if ($this->isColumnModified(kuserPeer::PARTNER_ID)) $criteria->add(kuserPeer::PARTNER_ID, $this->partner_id);
		if ($this->isColumnModified(kuserPeer::DISPLAY_IN_SEARCH)) $criteria->add(kuserPeer::DISPLAY_IN_SEARCH, $this->display_in_search);
		if ($this->isColumnModified(kuserPeer::SEARCH_TEXT)) $criteria->add(kuserPeer::SEARCH_TEXT, $this->search_text);
		if ($this->isColumnModified(kuserPeer::PARTNER_DATA)) $criteria->add(kuserPeer::PARTNER_DATA, $this->partner_data);
		if ($this->isColumnModified(kuserPeer::PUSER_ID)) $criteria->add(kuserPeer::PUSER_ID, $this->puser_id);
		if ($this->isColumnModified(kuserPeer::ADMIN_TAGS)) $criteria->add(kuserPeer::ADMIN_TAGS, $this->admin_tags);
		if ($this->isColumnModified(kuserPeer::INDEXED_PARTNER_DATA_INT)) $criteria->add(kuserPeer::INDEXED_PARTNER_DATA_INT, $this->indexed_partner_data_int);
		if ($this->isColumnModified(kuserPeer::INDEXED_PARTNER_DATA_STRING)) $criteria->add(kuserPeer::INDEXED_PARTNER_DATA_STRING, $this->indexed_partner_data_string);

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
		$criteria = new Criteria(kuserPeer::DATABASE_NAME);

		$criteria->add(kuserPeer::ID, $this->id);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     int
	 */
	public function getPrimaryKey()
	{
		return $this->getId();
	}

	/**
	 * Generic method to set the primary key (id column).
	 *
	 * @param      int $key Primary key.
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
	 * @param      object $copyObj An object of kuser (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setScreenName($this->screen_name);

		$copyObj->setFullName($this->full_name);

		$copyObj->setEmail($this->email);

		$copyObj->setSha1Password($this->sha1_password);

		$copyObj->setSalt($this->salt);

		$copyObj->setDateOfBirth($this->date_of_birth);

		$copyObj->setCountry($this->country);

		$copyObj->setState($this->state);

		$copyObj->setCity($this->city);

		$copyObj->setZip($this->zip);

		$copyObj->setUrlList($this->url_list);

		$copyObj->setPicture($this->picture);

		$copyObj->setIcon($this->icon);

		$copyObj->setAboutMe($this->about_me);

		$copyObj->setTags($this->tags);

		$copyObj->setTagline($this->tagline);

		$copyObj->setNetworkHighschool($this->network_highschool);

		$copyObj->setNetworkCollege($this->network_college);

		$copyObj->setNetworkOther($this->network_other);

		$copyObj->setMobileNum($this->mobile_num);

		$copyObj->setMatureContent($this->mature_content);

		$copyObj->setGender($this->gender);

		$copyObj->setRegistrationIp($this->registration_ip);

		$copyObj->setRegistrationCookie($this->registration_cookie);

		$copyObj->setImList($this->im_list);

		$copyObj->setViews($this->views);

		$copyObj->setFans($this->fans);

		$copyObj->setEntries($this->entries);

		$copyObj->setStorageSize($this->storage_size);

		$copyObj->setProducedKshows($this->produced_kshows);

		$copyObj->setStatus($this->status);

		$copyObj->setCreatedAt($this->created_at);

		$copyObj->setUpdatedAt($this->updated_at);

		$copyObj->setPartnerId($this->partner_id);

		$copyObj->setDisplayInSearch($this->display_in_search);

		$copyObj->setSearchText($this->search_text);

		$copyObj->setPartnerData($this->partner_data);

		$copyObj->setPuserId($this->puser_id);

		$copyObj->setAdminTags($this->admin_tags);

		$copyObj->setIndexedPartnerDataInt($this->indexed_partner_data_int);

		$copyObj->setIndexedPartnerDataString($this->indexed_partner_data_string);


		if ($deepCopy) {
			// important: temporarily setNew(false) because this affects the behavior of
			// the getter/setter methods for fkey referrer objects.
			$copyObj->setNew(false);

			foreach ($this->getkshows() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addkshow($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getentrys() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addentry($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getcomments() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addcomment($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getflags() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addflag($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getfavorites() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addfavorite($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getKshowKusers() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addKshowKuser($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getMailJobs() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addMailJob($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getPuserKusers() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addPuserKuser($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getPartners() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addPartner($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getmoderations() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addmoderation($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getmoderationFlagsRelatedByKuserId() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addmoderationFlagRelatedByKuserId($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getmoderationFlagsRelatedByFlaggedKuserId() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addmoderationFlagRelatedByFlaggedKuserId($relObj->copy($deepCopy));
				}
			}

			foreach ($this->getUploadTokens() as $relObj) {
				if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
					$copyObj->addUploadToken($relObj->copy($deepCopy));
				}
			}

		} // if ($deepCopy)


		$copyObj->setNew(true);

		$copyObj->setId(NULL); // this is a auto-increment column, so set to default value

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
	 * @return     kuser Clone of current object.
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
	 * @var     kuser Clone of current object.
	 */
	protected $copiedFrom = null;
	
	/**
	 * Stores the source object that this object copied from 
	 *
	 * @param      kuser $copiedFrom Clone of current object.
	 */
	public function setCopiedFrom(kuser $copiedFrom)
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
	 * @return     kuserPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new kuserPeer();
		}
		return self::$peer;
	}

	/**
	 * Clears out the collkshows collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addkshows()
	 */
	public function clearkshows()
	{
		$this->collkshows = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collkshows collection (array).
	 *
	 * By default this just sets the collkshows collection to an empty array (like clearcollkshows());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initkshows()
	{
		$this->collkshows = array();
	}

	/**
	 * Gets an array of kshow objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this kuser has previously been saved, it will retrieve
	 * related kshows from storage. If this kuser is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array kshow[]
	 * @throws     PropelException
	 */
	public function getkshows($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(kuserPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collkshows === null) {
			if ($this->isNew()) {
			   $this->collkshows = array();
			} else {

				$criteria->add(kshowPeer::PRODUCER_ID, $this->id);

				kshowPeer::addSelectColumns($criteria);
				$this->collkshows = kshowPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(kshowPeer::PRODUCER_ID, $this->id);

				kshowPeer::addSelectColumns($criteria);
				if (!isset($this->lastkshowCriteria) || !$this->lastkshowCriteria->equals($criteria)) {
					$this->collkshows = kshowPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastkshowCriteria = $criteria;
		return $this->collkshows;
	}

	/**
	 * Returns the number of related kshow objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related kshow objects.
	 * @throws     PropelException
	 */
	public function countkshows(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(kuserPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collkshows === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(kshowPeer::PRODUCER_ID, $this->id);

				$count = kshowPeer::doCount($criteria, false, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(kshowPeer::PRODUCER_ID, $this->id);

				if (!isset($this->lastkshowCriteria) || !$this->lastkshowCriteria->equals($criteria)) {
					$count = kshowPeer::doCount($criteria, false, $con);
				} else {
					$count = count($this->collkshows);
				}
			} else {
				$count = count($this->collkshows);
			}
		}
		return $count;
	}

	/**
	 * Method called to associate a kshow object to this object
	 * through the kshow foreign key attribute.
	 *
	 * @param      kshow $l kshow
	 * @return     void
	 * @throws     PropelException
	 */
	public function addkshow(kshow $l)
	{
		if ($this->collkshows === null) {
			$this->initkshows();
		}
		if (!in_array($l, $this->collkshows, true)) { // only add it if the **same** object is not already associated
			array_push($this->collkshows, $l);
			$l->setkuser($this);
		}
	}

	/**
	 * Clears out the collentrys collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addentrys()
	 */
	public function clearentrys()
	{
		$this->collentrys = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collentrys collection (array).
	 *
	 * By default this just sets the collentrys collection to an empty array (like clearcollentrys());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initentrys()
	{
		$this->collentrys = array();
	}

	/**
	 * Gets an array of entry objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this kuser has previously been saved, it will retrieve
	 * related entrys from storage. If this kuser is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array entry[]
	 * @throws     PropelException
	 */
	public function getentrys($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(kuserPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collentrys === null) {
			if ($this->isNew()) {
			   $this->collentrys = array();
			} else {

				$criteria->add(entryPeer::KUSER_ID, $this->id);

				entryPeer::addSelectColumns($criteria);
				$this->collentrys = entryPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(entryPeer::KUSER_ID, $this->id);

				entryPeer::addSelectColumns($criteria);
				if (!isset($this->lastentryCriteria) || !$this->lastentryCriteria->equals($criteria)) {
					$this->collentrys = entryPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastentryCriteria = $criteria;
		return $this->collentrys;
	}

	/**
	 * Returns the number of related entry objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related entry objects.
	 * @throws     PropelException
	 */
	public function countentrys(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(kuserPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collentrys === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(entryPeer::KUSER_ID, $this->id);

				$count = entryPeer::doCount($criteria, false, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(entryPeer::KUSER_ID, $this->id);

				if (!isset($this->lastentryCriteria) || !$this->lastentryCriteria->equals($criteria)) {
					$count = entryPeer::doCount($criteria, false, $con);
				} else {
					$count = count($this->collentrys);
				}
			} else {
				$count = count($this->collentrys);
			}
		}
		return $count;
	}

	/**
	 * Method called to associate a entry object to this object
	 * through the entry foreign key attribute.
	 *
	 * @param      entry $l entry
	 * @return     void
	 * @throws     PropelException
	 */
	public function addentry(entry $l)
	{
		if ($this->collentrys === null) {
			$this->initentrys();
		}
		if (!in_array($l, $this->collentrys, true)) { // only add it if the **same** object is not already associated
			array_push($this->collentrys, $l);
			$l->setkuser($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this kuser is new, it will return
	 * an empty collection; or if this kuser has previously
	 * been saved, it will retrieve related entrys from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in kuser.
	 */
	public function getentrysJoinkshow($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(kuserPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collentrys === null) {
			if ($this->isNew()) {
				$this->collentrys = array();
			} else {

				$criteria->add(entryPeer::KUSER_ID, $this->id);

				$this->collentrys = entryPeer::doSelectJoinkshow($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(entryPeer::KUSER_ID, $this->id);

			if (!isset($this->lastentryCriteria) || !$this->lastentryCriteria->equals($criteria)) {
				$this->collentrys = entryPeer::doSelectJoinkshow($criteria, $con, $join_behavior);
			}
		}
		$this->lastentryCriteria = $criteria;

		return $this->collentrys;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this kuser is new, it will return
	 * an empty collection; or if this kuser has previously
	 * been saved, it will retrieve related entrys from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in kuser.
	 */
	public function getentrysJoinaccessControl($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(kuserPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collentrys === null) {
			if ($this->isNew()) {
				$this->collentrys = array();
			} else {

				$criteria->add(entryPeer::KUSER_ID, $this->id);

				$this->collentrys = entryPeer::doSelectJoinaccessControl($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(entryPeer::KUSER_ID, $this->id);

			if (!isset($this->lastentryCriteria) || !$this->lastentryCriteria->equals($criteria)) {
				$this->collentrys = entryPeer::doSelectJoinaccessControl($criteria, $con, $join_behavior);
			}
		}
		$this->lastentryCriteria = $criteria;

		return $this->collentrys;
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this kuser is new, it will return
	 * an empty collection; or if this kuser has previously
	 * been saved, it will retrieve related entrys from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in kuser.
	 */
	public function getentrysJoinconversionProfile2($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(kuserPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collentrys === null) {
			if ($this->isNew()) {
				$this->collentrys = array();
			} else {

				$criteria->add(entryPeer::KUSER_ID, $this->id);

				$this->collentrys = entryPeer::doSelectJoinconversionProfile2($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(entryPeer::KUSER_ID, $this->id);

			if (!isset($this->lastentryCriteria) || !$this->lastentryCriteria->equals($criteria)) {
				$this->collentrys = entryPeer::doSelectJoinconversionProfile2($criteria, $con, $join_behavior);
			}
		}
		$this->lastentryCriteria = $criteria;

		return $this->collentrys;
	}

	/**
	 * Clears out the collcomments collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addcomments()
	 */
	public function clearcomments()
	{
		$this->collcomments = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collcomments collection (array).
	 *
	 * By default this just sets the collcomments collection to an empty array (like clearcollcomments());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initcomments()
	{
		$this->collcomments = array();
	}

	/**
	 * Gets an array of comment objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this kuser has previously been saved, it will retrieve
	 * related comments from storage. If this kuser is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array comment[]
	 * @throws     PropelException
	 */
	public function getcomments($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(kuserPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collcomments === null) {
			if ($this->isNew()) {
			   $this->collcomments = array();
			} else {

				$criteria->add(commentPeer::KUSER_ID, $this->id);

				commentPeer::addSelectColumns($criteria);
				$this->collcomments = commentPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(commentPeer::KUSER_ID, $this->id);

				commentPeer::addSelectColumns($criteria);
				if (!isset($this->lastcommentCriteria) || !$this->lastcommentCriteria->equals($criteria)) {
					$this->collcomments = commentPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastcommentCriteria = $criteria;
		return $this->collcomments;
	}

	/**
	 * Returns the number of related comment objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related comment objects.
	 * @throws     PropelException
	 */
	public function countcomments(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(kuserPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collcomments === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(commentPeer::KUSER_ID, $this->id);

				$count = commentPeer::doCount($criteria, false, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(commentPeer::KUSER_ID, $this->id);

				if (!isset($this->lastcommentCriteria) || !$this->lastcommentCriteria->equals($criteria)) {
					$count = commentPeer::doCount($criteria, false, $con);
				} else {
					$count = count($this->collcomments);
				}
			} else {
				$count = count($this->collcomments);
			}
		}
		return $count;
	}

	/**
	 * Method called to associate a comment object to this object
	 * through the comment foreign key attribute.
	 *
	 * @param      comment $l comment
	 * @return     void
	 * @throws     PropelException
	 */
	public function addcomment(comment $l)
	{
		if ($this->collcomments === null) {
			$this->initcomments();
		}
		if (!in_array($l, $this->collcomments, true)) { // only add it if the **same** object is not already associated
			array_push($this->collcomments, $l);
			$l->setkuser($this);
		}
	}

	/**
	 * Clears out the collflags collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addflags()
	 */
	public function clearflags()
	{
		$this->collflags = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collflags collection (array).
	 *
	 * By default this just sets the collflags collection to an empty array (like clearcollflags());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initflags()
	{
		$this->collflags = array();
	}

	/**
	 * Gets an array of flag objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this kuser has previously been saved, it will retrieve
	 * related flags from storage. If this kuser is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array flag[]
	 * @throws     PropelException
	 */
	public function getflags($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(kuserPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collflags === null) {
			if ($this->isNew()) {
			   $this->collflags = array();
			} else {

				$criteria->add(flagPeer::KUSER_ID, $this->id);

				flagPeer::addSelectColumns($criteria);
				$this->collflags = flagPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(flagPeer::KUSER_ID, $this->id);

				flagPeer::addSelectColumns($criteria);
				if (!isset($this->lastflagCriteria) || !$this->lastflagCriteria->equals($criteria)) {
					$this->collflags = flagPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastflagCriteria = $criteria;
		return $this->collflags;
	}

	/**
	 * Returns the number of related flag objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related flag objects.
	 * @throws     PropelException
	 */
	public function countflags(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(kuserPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collflags === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(flagPeer::KUSER_ID, $this->id);

				$count = flagPeer::doCount($criteria, false, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(flagPeer::KUSER_ID, $this->id);

				if (!isset($this->lastflagCriteria) || !$this->lastflagCriteria->equals($criteria)) {
					$count = flagPeer::doCount($criteria, false, $con);
				} else {
					$count = count($this->collflags);
				}
			} else {
				$count = count($this->collflags);
			}
		}
		return $count;
	}

	/**
	 * Method called to associate a flag object to this object
	 * through the flag foreign key attribute.
	 *
	 * @param      flag $l flag
	 * @return     void
	 * @throws     PropelException
	 */
	public function addflag(flag $l)
	{
		if ($this->collflags === null) {
			$this->initflags();
		}
		if (!in_array($l, $this->collflags, true)) { // only add it if the **same** object is not already associated
			array_push($this->collflags, $l);
			$l->setkuser($this);
		}
	}

	/**
	 * Clears out the collfavorites collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addfavorites()
	 */
	public function clearfavorites()
	{
		$this->collfavorites = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collfavorites collection (array).
	 *
	 * By default this just sets the collfavorites collection to an empty array (like clearcollfavorites());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initfavorites()
	{
		$this->collfavorites = array();
	}

	/**
	 * Gets an array of favorite objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this kuser has previously been saved, it will retrieve
	 * related favorites from storage. If this kuser is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array favorite[]
	 * @throws     PropelException
	 */
	public function getfavorites($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(kuserPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collfavorites === null) {
			if ($this->isNew()) {
			   $this->collfavorites = array();
			} else {

				$criteria->add(favoritePeer::KUSER_ID, $this->id);

				favoritePeer::addSelectColumns($criteria);
				$this->collfavorites = favoritePeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(favoritePeer::KUSER_ID, $this->id);

				favoritePeer::addSelectColumns($criteria);
				if (!isset($this->lastfavoriteCriteria) || !$this->lastfavoriteCriteria->equals($criteria)) {
					$this->collfavorites = favoritePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastfavoriteCriteria = $criteria;
		return $this->collfavorites;
	}

	/**
	 * Returns the number of related favorite objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related favorite objects.
	 * @throws     PropelException
	 */
	public function countfavorites(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(kuserPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collfavorites === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(favoritePeer::KUSER_ID, $this->id);

				$count = favoritePeer::doCount($criteria, false, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(favoritePeer::KUSER_ID, $this->id);

				if (!isset($this->lastfavoriteCriteria) || !$this->lastfavoriteCriteria->equals($criteria)) {
					$count = favoritePeer::doCount($criteria, false, $con);
				} else {
					$count = count($this->collfavorites);
				}
			} else {
				$count = count($this->collfavorites);
			}
		}
		return $count;
	}

	/**
	 * Method called to associate a favorite object to this object
	 * through the favorite foreign key attribute.
	 *
	 * @param      favorite $l favorite
	 * @return     void
	 * @throws     PropelException
	 */
	public function addfavorite(favorite $l)
	{
		if ($this->collfavorites === null) {
			$this->initfavorites();
		}
		if (!in_array($l, $this->collfavorites, true)) { // only add it if the **same** object is not already associated
			array_push($this->collfavorites, $l);
			$l->setkuser($this);
		}
	}

	/**
	 * Clears out the collKshowKusers collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addKshowKusers()
	 */
	public function clearKshowKusers()
	{
		$this->collKshowKusers = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collKshowKusers collection (array).
	 *
	 * By default this just sets the collKshowKusers collection to an empty array (like clearcollKshowKusers());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initKshowKusers()
	{
		$this->collKshowKusers = array();
	}

	/**
	 * Gets an array of KshowKuser objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this kuser has previously been saved, it will retrieve
	 * related KshowKusers from storage. If this kuser is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array KshowKuser[]
	 * @throws     PropelException
	 */
	public function getKshowKusers($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(kuserPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collKshowKusers === null) {
			if ($this->isNew()) {
			   $this->collKshowKusers = array();
			} else {

				$criteria->add(KshowKuserPeer::KUSER_ID, $this->id);

				KshowKuserPeer::addSelectColumns($criteria);
				$this->collKshowKusers = KshowKuserPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(KshowKuserPeer::KUSER_ID, $this->id);

				KshowKuserPeer::addSelectColumns($criteria);
				if (!isset($this->lastKshowKuserCriteria) || !$this->lastKshowKuserCriteria->equals($criteria)) {
					$this->collKshowKusers = KshowKuserPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastKshowKuserCriteria = $criteria;
		return $this->collKshowKusers;
	}

	/**
	 * Returns the number of related KshowKuser objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related KshowKuser objects.
	 * @throws     PropelException
	 */
	public function countKshowKusers(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(kuserPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collKshowKusers === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(KshowKuserPeer::KUSER_ID, $this->id);

				$count = KshowKuserPeer::doCount($criteria, false, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(KshowKuserPeer::KUSER_ID, $this->id);

				if (!isset($this->lastKshowKuserCriteria) || !$this->lastKshowKuserCriteria->equals($criteria)) {
					$count = KshowKuserPeer::doCount($criteria, false, $con);
				} else {
					$count = count($this->collKshowKusers);
				}
			} else {
				$count = count($this->collKshowKusers);
			}
		}
		return $count;
	}

	/**
	 * Method called to associate a KshowKuser object to this object
	 * through the KshowKuser foreign key attribute.
	 *
	 * @param      KshowKuser $l KshowKuser
	 * @return     void
	 * @throws     PropelException
	 */
	public function addKshowKuser(KshowKuser $l)
	{
		if ($this->collKshowKusers === null) {
			$this->initKshowKusers();
		}
		if (!in_array($l, $this->collKshowKusers, true)) { // only add it if the **same** object is not already associated
			array_push($this->collKshowKusers, $l);
			$l->setkuser($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this kuser is new, it will return
	 * an empty collection; or if this kuser has previously
	 * been saved, it will retrieve related KshowKusers from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in kuser.
	 */
	public function getKshowKusersJoinkshow($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(kuserPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collKshowKusers === null) {
			if ($this->isNew()) {
				$this->collKshowKusers = array();
			} else {

				$criteria->add(KshowKuserPeer::KUSER_ID, $this->id);

				$this->collKshowKusers = KshowKuserPeer::doSelectJoinkshow($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(KshowKuserPeer::KUSER_ID, $this->id);

			if (!isset($this->lastKshowKuserCriteria) || !$this->lastKshowKuserCriteria->equals($criteria)) {
				$this->collKshowKusers = KshowKuserPeer::doSelectJoinkshow($criteria, $con, $join_behavior);
			}
		}
		$this->lastKshowKuserCriteria = $criteria;

		return $this->collKshowKusers;
	}

	/**
	 * Clears out the collMailJobs collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addMailJobs()
	 */
	public function clearMailJobs()
	{
		$this->collMailJobs = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collMailJobs collection (array).
	 *
	 * By default this just sets the collMailJobs collection to an empty array (like clearcollMailJobs());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initMailJobs()
	{
		$this->collMailJobs = array();
	}

	/**
	 * Gets an array of MailJob objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this kuser has previously been saved, it will retrieve
	 * related MailJobs from storage. If this kuser is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array MailJob[]
	 * @throws     PropelException
	 */
	public function getMailJobs($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(kuserPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collMailJobs === null) {
			if ($this->isNew()) {
			   $this->collMailJobs = array();
			} else {

				$criteria->add(MailJobPeer::RECIPIENT_ID, $this->id);

				MailJobPeer::addSelectColumns($criteria);
				$this->collMailJobs = MailJobPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(MailJobPeer::RECIPIENT_ID, $this->id);

				MailJobPeer::addSelectColumns($criteria);
				if (!isset($this->lastMailJobCriteria) || !$this->lastMailJobCriteria->equals($criteria)) {
					$this->collMailJobs = MailJobPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastMailJobCriteria = $criteria;
		return $this->collMailJobs;
	}

	/**
	 * Returns the number of related MailJob objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related MailJob objects.
	 * @throws     PropelException
	 */
	public function countMailJobs(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(kuserPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collMailJobs === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(MailJobPeer::RECIPIENT_ID, $this->id);

				$count = MailJobPeer::doCount($criteria, false, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(MailJobPeer::RECIPIENT_ID, $this->id);

				if (!isset($this->lastMailJobCriteria) || !$this->lastMailJobCriteria->equals($criteria)) {
					$count = MailJobPeer::doCount($criteria, false, $con);
				} else {
					$count = count($this->collMailJobs);
				}
			} else {
				$count = count($this->collMailJobs);
			}
		}
		return $count;
	}

	/**
	 * Method called to associate a MailJob object to this object
	 * through the MailJob foreign key attribute.
	 *
	 * @param      MailJob $l MailJob
	 * @return     void
	 * @throws     PropelException
	 */
	public function addMailJob(MailJob $l)
	{
		if ($this->collMailJobs === null) {
			$this->initMailJobs();
		}
		if (!in_array($l, $this->collMailJobs, true)) { // only add it if the **same** object is not already associated
			array_push($this->collMailJobs, $l);
			$l->setkuser($this);
		}
	}

	/**
	 * Clears out the collPuserKusers collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addPuserKusers()
	 */
	public function clearPuserKusers()
	{
		$this->collPuserKusers = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collPuserKusers collection (array).
	 *
	 * By default this just sets the collPuserKusers collection to an empty array (like clearcollPuserKusers());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initPuserKusers()
	{
		$this->collPuserKusers = array();
	}

	/**
	 * Gets an array of PuserKuser objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this kuser has previously been saved, it will retrieve
	 * related PuserKusers from storage. If this kuser is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array PuserKuser[]
	 * @throws     PropelException
	 */
	public function getPuserKusers($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(kuserPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collPuserKusers === null) {
			if ($this->isNew()) {
			   $this->collPuserKusers = array();
			} else {

				$criteria->add(PuserKuserPeer::KUSER_ID, $this->id);

				PuserKuserPeer::addSelectColumns($criteria);
				$this->collPuserKusers = PuserKuserPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(PuserKuserPeer::KUSER_ID, $this->id);

				PuserKuserPeer::addSelectColumns($criteria);
				if (!isset($this->lastPuserKuserCriteria) || !$this->lastPuserKuserCriteria->equals($criteria)) {
					$this->collPuserKusers = PuserKuserPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastPuserKuserCriteria = $criteria;
		return $this->collPuserKusers;
	}

	/**
	 * Returns the number of related PuserKuser objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related PuserKuser objects.
	 * @throws     PropelException
	 */
	public function countPuserKusers(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(kuserPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collPuserKusers === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(PuserKuserPeer::KUSER_ID, $this->id);

				$count = PuserKuserPeer::doCount($criteria, false, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(PuserKuserPeer::KUSER_ID, $this->id);

				if (!isset($this->lastPuserKuserCriteria) || !$this->lastPuserKuserCriteria->equals($criteria)) {
					$count = PuserKuserPeer::doCount($criteria, false, $con);
				} else {
					$count = count($this->collPuserKusers);
				}
			} else {
				$count = count($this->collPuserKusers);
			}
		}
		return $count;
	}

	/**
	 * Method called to associate a PuserKuser object to this object
	 * through the PuserKuser foreign key attribute.
	 *
	 * @param      PuserKuser $l PuserKuser
	 * @return     void
	 * @throws     PropelException
	 */
	public function addPuserKuser(PuserKuser $l)
	{
		if ($this->collPuserKusers === null) {
			$this->initPuserKusers();
		}
		if (!in_array($l, $this->collPuserKusers, true)) { // only add it if the **same** object is not already associated
			array_push($this->collPuserKusers, $l);
			$l->setkuser($this);
		}
	}

	/**
	 * Clears out the collPartners collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addPartners()
	 */
	public function clearPartners()
	{
		$this->collPartners = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collPartners collection (array).
	 *
	 * By default this just sets the collPartners collection to an empty array (like clearcollPartners());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initPartners()
	{
		$this->collPartners = array();
	}

	/**
	 * Gets an array of Partner objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this kuser has previously been saved, it will retrieve
	 * related Partners from storage. If this kuser is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array Partner[]
	 * @throws     PropelException
	 */
	public function getPartners($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(kuserPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collPartners === null) {
			if ($this->isNew()) {
			   $this->collPartners = array();
			} else {

				$criteria->add(PartnerPeer::ANONYMOUS_KUSER_ID, $this->id);

				PartnerPeer::addSelectColumns($criteria);
				$this->collPartners = PartnerPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(PartnerPeer::ANONYMOUS_KUSER_ID, $this->id);

				PartnerPeer::addSelectColumns($criteria);
				if (!isset($this->lastPartnerCriteria) || !$this->lastPartnerCriteria->equals($criteria)) {
					$this->collPartners = PartnerPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastPartnerCriteria = $criteria;
		return $this->collPartners;
	}

	/**
	 * Returns the number of related Partner objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related Partner objects.
	 * @throws     PropelException
	 */
	public function countPartners(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(kuserPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collPartners === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(PartnerPeer::ANONYMOUS_KUSER_ID, $this->id);

				$count = PartnerPeer::doCount($criteria, false, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(PartnerPeer::ANONYMOUS_KUSER_ID, $this->id);

				if (!isset($this->lastPartnerCriteria) || !$this->lastPartnerCriteria->equals($criteria)) {
					$count = PartnerPeer::doCount($criteria, false, $con);
				} else {
					$count = count($this->collPartners);
				}
			} else {
				$count = count($this->collPartners);
			}
		}
		return $count;
	}

	/**
	 * Method called to associate a Partner object to this object
	 * through the Partner foreign key attribute.
	 *
	 * @param      Partner $l Partner
	 * @return     void
	 * @throws     PropelException
	 */
	public function addPartner(Partner $l)
	{
		if ($this->collPartners === null) {
			$this->initPartners();
		}
		if (!in_array($l, $this->collPartners, true)) { // only add it if the **same** object is not already associated
			array_push($this->collPartners, $l);
			$l->setkuser($this);
		}
	}

	/**
	 * Clears out the collmoderations collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addmoderations()
	 */
	public function clearmoderations()
	{
		$this->collmoderations = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collmoderations collection (array).
	 *
	 * By default this just sets the collmoderations collection to an empty array (like clearcollmoderations());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initmoderations()
	{
		$this->collmoderations = array();
	}

	/**
	 * Gets an array of moderation objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this kuser has previously been saved, it will retrieve
	 * related moderations from storage. If this kuser is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array moderation[]
	 * @throws     PropelException
	 */
	public function getmoderations($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(kuserPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collmoderations === null) {
			if ($this->isNew()) {
			   $this->collmoderations = array();
			} else {

				$criteria->add(moderationPeer::KUSER_ID, $this->id);

				moderationPeer::addSelectColumns($criteria);
				$this->collmoderations = moderationPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(moderationPeer::KUSER_ID, $this->id);

				moderationPeer::addSelectColumns($criteria);
				if (!isset($this->lastmoderationCriteria) || !$this->lastmoderationCriteria->equals($criteria)) {
					$this->collmoderations = moderationPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastmoderationCriteria = $criteria;
		return $this->collmoderations;
	}

	/**
	 * Returns the number of related moderation objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related moderation objects.
	 * @throws     PropelException
	 */
	public function countmoderations(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(kuserPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collmoderations === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(moderationPeer::KUSER_ID, $this->id);

				$count = moderationPeer::doCount($criteria, false, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(moderationPeer::KUSER_ID, $this->id);

				if (!isset($this->lastmoderationCriteria) || !$this->lastmoderationCriteria->equals($criteria)) {
					$count = moderationPeer::doCount($criteria, false, $con);
				} else {
					$count = count($this->collmoderations);
				}
			} else {
				$count = count($this->collmoderations);
			}
		}
		return $count;
	}

	/**
	 * Method called to associate a moderation object to this object
	 * through the moderation foreign key attribute.
	 *
	 * @param      moderation $l moderation
	 * @return     void
	 * @throws     PropelException
	 */
	public function addmoderation(moderation $l)
	{
		if ($this->collmoderations === null) {
			$this->initmoderations();
		}
		if (!in_array($l, $this->collmoderations, true)) { // only add it if the **same** object is not already associated
			array_push($this->collmoderations, $l);
			$l->setkuser($this);
		}
	}

	/**
	 * Clears out the collmoderationFlagsRelatedByKuserId collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addmoderationFlagsRelatedByKuserId()
	 */
	public function clearmoderationFlagsRelatedByKuserId()
	{
		$this->collmoderationFlagsRelatedByKuserId = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collmoderationFlagsRelatedByKuserId collection (array).
	 *
	 * By default this just sets the collmoderationFlagsRelatedByKuserId collection to an empty array (like clearcollmoderationFlagsRelatedByKuserId());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initmoderationFlagsRelatedByKuserId()
	{
		$this->collmoderationFlagsRelatedByKuserId = array();
	}

	/**
	 * Gets an array of moderationFlag objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this kuser has previously been saved, it will retrieve
	 * related moderationFlagsRelatedByKuserId from storage. If this kuser is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array moderationFlag[]
	 * @throws     PropelException
	 */
	public function getmoderationFlagsRelatedByKuserId($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(kuserPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collmoderationFlagsRelatedByKuserId === null) {
			if ($this->isNew()) {
			   $this->collmoderationFlagsRelatedByKuserId = array();
			} else {

				$criteria->add(moderationFlagPeer::KUSER_ID, $this->id);

				moderationFlagPeer::addSelectColumns($criteria);
				$this->collmoderationFlagsRelatedByKuserId = moderationFlagPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(moderationFlagPeer::KUSER_ID, $this->id);

				moderationFlagPeer::addSelectColumns($criteria);
				if (!isset($this->lastmoderationFlagRelatedByKuserIdCriteria) || !$this->lastmoderationFlagRelatedByKuserIdCriteria->equals($criteria)) {
					$this->collmoderationFlagsRelatedByKuserId = moderationFlagPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastmoderationFlagRelatedByKuserIdCriteria = $criteria;
		return $this->collmoderationFlagsRelatedByKuserId;
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
	public function countmoderationFlagsRelatedByKuserId(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(kuserPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collmoderationFlagsRelatedByKuserId === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(moderationFlagPeer::KUSER_ID, $this->id);

				$count = moderationFlagPeer::doCount($criteria, false, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(moderationFlagPeer::KUSER_ID, $this->id);

				if (!isset($this->lastmoderationFlagRelatedByKuserIdCriteria) || !$this->lastmoderationFlagRelatedByKuserIdCriteria->equals($criteria)) {
					$count = moderationFlagPeer::doCount($criteria, false, $con);
				} else {
					$count = count($this->collmoderationFlagsRelatedByKuserId);
				}
			} else {
				$count = count($this->collmoderationFlagsRelatedByKuserId);
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
	public function addmoderationFlagRelatedByKuserId(moderationFlag $l)
	{
		if ($this->collmoderationFlagsRelatedByKuserId === null) {
			$this->initmoderationFlagsRelatedByKuserId();
		}
		if (!in_array($l, $this->collmoderationFlagsRelatedByKuserId, true)) { // only add it if the **same** object is not already associated
			array_push($this->collmoderationFlagsRelatedByKuserId, $l);
			$l->setkuserRelatedByKuserId($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this kuser is new, it will return
	 * an empty collection; or if this kuser has previously
	 * been saved, it will retrieve related moderationFlagsRelatedByKuserId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in kuser.
	 */
	public function getmoderationFlagsRelatedByKuserIdJoinentry($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(kuserPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collmoderationFlagsRelatedByKuserId === null) {
			if ($this->isNew()) {
				$this->collmoderationFlagsRelatedByKuserId = array();
			} else {

				$criteria->add(moderationFlagPeer::KUSER_ID, $this->id);

				$this->collmoderationFlagsRelatedByKuserId = moderationFlagPeer::doSelectJoinentry($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(moderationFlagPeer::KUSER_ID, $this->id);

			if (!isset($this->lastmoderationFlagRelatedByKuserIdCriteria) || !$this->lastmoderationFlagRelatedByKuserIdCriteria->equals($criteria)) {
				$this->collmoderationFlagsRelatedByKuserId = moderationFlagPeer::doSelectJoinentry($criteria, $con, $join_behavior);
			}
		}
		$this->lastmoderationFlagRelatedByKuserIdCriteria = $criteria;

		return $this->collmoderationFlagsRelatedByKuserId;
	}

	/**
	 * Clears out the collmoderationFlagsRelatedByFlaggedKuserId collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addmoderationFlagsRelatedByFlaggedKuserId()
	 */
	public function clearmoderationFlagsRelatedByFlaggedKuserId()
	{
		$this->collmoderationFlagsRelatedByFlaggedKuserId = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collmoderationFlagsRelatedByFlaggedKuserId collection (array).
	 *
	 * By default this just sets the collmoderationFlagsRelatedByFlaggedKuserId collection to an empty array (like clearcollmoderationFlagsRelatedByFlaggedKuserId());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initmoderationFlagsRelatedByFlaggedKuserId()
	{
		$this->collmoderationFlagsRelatedByFlaggedKuserId = array();
	}

	/**
	 * Gets an array of moderationFlag objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this kuser has previously been saved, it will retrieve
	 * related moderationFlagsRelatedByFlaggedKuserId from storage. If this kuser is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array moderationFlag[]
	 * @throws     PropelException
	 */
	public function getmoderationFlagsRelatedByFlaggedKuserId($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(kuserPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collmoderationFlagsRelatedByFlaggedKuserId === null) {
			if ($this->isNew()) {
			   $this->collmoderationFlagsRelatedByFlaggedKuserId = array();
			} else {

				$criteria->add(moderationFlagPeer::FLAGGED_KUSER_ID, $this->id);

				moderationFlagPeer::addSelectColumns($criteria);
				$this->collmoderationFlagsRelatedByFlaggedKuserId = moderationFlagPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(moderationFlagPeer::FLAGGED_KUSER_ID, $this->id);

				moderationFlagPeer::addSelectColumns($criteria);
				if (!isset($this->lastmoderationFlagRelatedByFlaggedKuserIdCriteria) || !$this->lastmoderationFlagRelatedByFlaggedKuserIdCriteria->equals($criteria)) {
					$this->collmoderationFlagsRelatedByFlaggedKuserId = moderationFlagPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastmoderationFlagRelatedByFlaggedKuserIdCriteria = $criteria;
		return $this->collmoderationFlagsRelatedByFlaggedKuserId;
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
	public function countmoderationFlagsRelatedByFlaggedKuserId(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(kuserPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collmoderationFlagsRelatedByFlaggedKuserId === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(moderationFlagPeer::FLAGGED_KUSER_ID, $this->id);

				$count = moderationFlagPeer::doCount($criteria, false, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(moderationFlagPeer::FLAGGED_KUSER_ID, $this->id);

				if (!isset($this->lastmoderationFlagRelatedByFlaggedKuserIdCriteria) || !$this->lastmoderationFlagRelatedByFlaggedKuserIdCriteria->equals($criteria)) {
					$count = moderationFlagPeer::doCount($criteria, false, $con);
				} else {
					$count = count($this->collmoderationFlagsRelatedByFlaggedKuserId);
				}
			} else {
				$count = count($this->collmoderationFlagsRelatedByFlaggedKuserId);
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
	public function addmoderationFlagRelatedByFlaggedKuserId(moderationFlag $l)
	{
		if ($this->collmoderationFlagsRelatedByFlaggedKuserId === null) {
			$this->initmoderationFlagsRelatedByFlaggedKuserId();
		}
		if (!in_array($l, $this->collmoderationFlagsRelatedByFlaggedKuserId, true)) { // only add it if the **same** object is not already associated
			array_push($this->collmoderationFlagsRelatedByFlaggedKuserId, $l);
			$l->setkuserRelatedByFlaggedKuserId($this);
		}
	}


	/**
	 * If this collection has already been initialized with
	 * an identical criteria, it returns the collection.
	 * Otherwise if this kuser is new, it will return
	 * an empty collection; or if this kuser has previously
	 * been saved, it will retrieve related moderationFlagsRelatedByFlaggedKuserId from storage.
	 *
	 * This method is protected by default in order to keep the public
	 * api reasonable.  You can provide public methods for those you
	 * actually need in kuser.
	 */
	public function getmoderationFlagsRelatedByFlaggedKuserIdJoinentry($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
	{
		if ($criteria === null) {
			$criteria = new Criteria(kuserPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collmoderationFlagsRelatedByFlaggedKuserId === null) {
			if ($this->isNew()) {
				$this->collmoderationFlagsRelatedByFlaggedKuserId = array();
			} else {

				$criteria->add(moderationFlagPeer::FLAGGED_KUSER_ID, $this->id);

				$this->collmoderationFlagsRelatedByFlaggedKuserId = moderationFlagPeer::doSelectJoinentry($criteria, $con, $join_behavior);
			}
		} else {
			// the following code is to determine if a new query is
			// called for.  If the criteria is the same as the last
			// one, just return the collection.

			$criteria->add(moderationFlagPeer::FLAGGED_KUSER_ID, $this->id);

			if (!isset($this->lastmoderationFlagRelatedByFlaggedKuserIdCriteria) || !$this->lastmoderationFlagRelatedByFlaggedKuserIdCriteria->equals($criteria)) {
				$this->collmoderationFlagsRelatedByFlaggedKuserId = moderationFlagPeer::doSelectJoinentry($criteria, $con, $join_behavior);
			}
		}
		$this->lastmoderationFlagRelatedByFlaggedKuserIdCriteria = $criteria;

		return $this->collmoderationFlagsRelatedByFlaggedKuserId;
	}

	/**
	 * Clears out the collUploadTokens collection (array).
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 * @see        addUploadTokens()
	 */
	public function clearUploadTokens()
	{
		$this->collUploadTokens = null; // important to set this to NULL since that means it is uninitialized
	}

	/**
	 * Initializes the collUploadTokens collection (array).
	 *
	 * By default this just sets the collUploadTokens collection to an empty array (like clearcollUploadTokens());
	 * however, you may wish to override this method in your stub class to provide setting appropriate
	 * to your application -- for example, setting the initial array to the values stored in database.
	 *
	 * @return     void
	 */
	public function initUploadTokens()
	{
		$this->collUploadTokens = array();
	}

	/**
	 * Gets an array of UploadToken objects which contain a foreign key that references this object.
	 *
	 * If this collection has already been initialized with an identical Criteria, it returns the collection.
	 * Otherwise if this kuser has previously been saved, it will retrieve
	 * related UploadTokens from storage. If this kuser is new, it will return
	 * an empty collection or the current collection, the criteria is ignored on a new object.
	 *
	 * @param      PropelPDO $con
	 * @param      Criteria $criteria
	 * @return     array UploadToken[]
	 * @throws     PropelException
	 */
	public function getUploadTokens($criteria = null, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(kuserPeer::DATABASE_NAME);
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collUploadTokens === null) {
			if ($this->isNew()) {
			   $this->collUploadTokens = array();
			} else {

				$criteria->add(UploadTokenPeer::KUSER_ID, $this->id);

				UploadTokenPeer::addSelectColumns($criteria);
				$this->collUploadTokens = UploadTokenPeer::doSelect($criteria, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return the collection.


				$criteria->add(UploadTokenPeer::KUSER_ID, $this->id);

				UploadTokenPeer::addSelectColumns($criteria);
				if (!isset($this->lastUploadTokenCriteria) || !$this->lastUploadTokenCriteria->equals($criteria)) {
					$this->collUploadTokens = UploadTokenPeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastUploadTokenCriteria = $criteria;
		return $this->collUploadTokens;
	}

	/**
	 * Returns the number of related UploadToken objects.
	 *
	 * @param      Criteria $criteria
	 * @param      boolean $distinct
	 * @param      PropelPDO $con
	 * @return     int Count of related UploadToken objects.
	 * @throws     PropelException
	 */
	public function countUploadTokens(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
	{
		if ($criteria === null) {
			$criteria = new Criteria(kuserPeer::DATABASE_NAME);
		} else {
			$criteria = clone $criteria;
		}

		if ($distinct) {
			$criteria->setDistinct();
		}

		$count = null;

		if ($this->collUploadTokens === null) {
			if ($this->isNew()) {
				$count = 0;
			} else {

				$criteria->add(UploadTokenPeer::KUSER_ID, $this->id);

				$count = UploadTokenPeer::doCount($criteria, false, $con);
			}
		} else {
			// criteria has no effect for a new object
			if (!$this->isNew()) {
				// the following code is to determine if a new query is
				// called for.  If the criteria is the same as the last
				// one, just return count of the collection.


				$criteria->add(UploadTokenPeer::KUSER_ID, $this->id);

				if (!isset($this->lastUploadTokenCriteria) || !$this->lastUploadTokenCriteria->equals($criteria)) {
					$count = UploadTokenPeer::doCount($criteria, false, $con);
				} else {
					$count = count($this->collUploadTokens);
				}
			} else {
				$count = count($this->collUploadTokens);
			}
		}
		return $count;
	}

	/**
	 * Method called to associate a UploadToken object to this object
	 * through the UploadToken foreign key attribute.
	 *
	 * @param      UploadToken $l UploadToken
	 * @return     void
	 * @throws     PropelException
	 */
	public function addUploadToken(UploadToken $l)
	{
		if ($this->collUploadTokens === null) {
			$this->initUploadTokens();
		}
		if (!in_array($l, $this->collUploadTokens, true)) { // only add it if the **same** object is not already associated
			array_push($this->collUploadTokens, $l);
			$l->setkuser($this);
		}
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
			if ($this->collkshows) {
				foreach ((array) $this->collkshows as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collentrys) {
				foreach ((array) $this->collentrys as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collcomments) {
				foreach ((array) $this->collcomments as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collflags) {
				foreach ((array) $this->collflags as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collfavorites) {
				foreach ((array) $this->collfavorites as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collKshowKusers) {
				foreach ((array) $this->collKshowKusers as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collMailJobs) {
				foreach ((array) $this->collMailJobs as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collPuserKusers) {
				foreach ((array) $this->collPuserKusers as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collPartners) {
				foreach ((array) $this->collPartners as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collmoderations) {
				foreach ((array) $this->collmoderations as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collmoderationFlagsRelatedByKuserId) {
				foreach ((array) $this->collmoderationFlagsRelatedByKuserId as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collmoderationFlagsRelatedByFlaggedKuserId) {
				foreach ((array) $this->collmoderationFlagsRelatedByFlaggedKuserId as $o) {
					$o->clearAllReferences($deep);
				}
			}
			if ($this->collUploadTokens) {
				foreach ((array) $this->collUploadTokens as $o) {
					$o->clearAllReferences($deep);
				}
			}
		} // if ($deep)

		$this->collkshows = null;
		$this->collentrys = null;
		$this->collcomments = null;
		$this->collflags = null;
		$this->collfavorites = null;
		$this->collKshowKusers = null;
		$this->collMailJobs = null;
		$this->collPuserKusers = null;
		$this->collPartners = null;
		$this->collmoderations = null;
		$this->collmoderationFlagsRelatedByKuserId = null;
		$this->collmoderationFlagsRelatedByFlaggedKuserId = null;
		$this->collUploadTokens = null;
	}

} // Basekuser
