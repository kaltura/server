<?php
/**
 * @package plugins.dropFolder
 * @subpackage model
 */
class WebexDropFolder extends RemoteDropFolder
{
	const WEBEX_USER_ID = 'webex_user_id';
	
	const WEBEX_PASSWORD = 'webex_password';
	
	const WEBEX_SITE_ID = 'webex_site_id';

	const WEBEX_SITE_NAME = 'webex_site_name';
	
	const WEBEX_PARTNER_ID = 'webex_partner_id';
	
	const WEBEX_SERVICE_URL = 'webex_service_url';

	const WEBEX_HOST_ID_METADATA_FIELD_NAME = 'webex_host_id_metadata_field_name';
	
	const WEBEX_DELETE_FROM_RECYCLE_BIN = 'deleteFromRecycleBin';
	
	const WEBEX_SERVICE_TYPE = 'webexServiceType';
	
	const WEBEX_DELETE_FROM_TIMESTAMP = 'deleteFromTimestamp';

	
	/**
	 * @var string
	 */
	protected $webexUserId;
	
	/**
	 * @var string
	 */
	protected $webexPassword;
	
	/**
	 * @var int
	 */
	protected $webexSiteId;
	
	/**
	 * @var string
	 */	
	protected $webexPartnerId;
	
	/**
	* @var string
	*/
	protected $webexServiceUrl;
	
	/**
	 * @var string
	 */
	protected $webexHostIdMetadataFieldName;

	/**
	 * @var string
	 */
	protected $webexSiteName;
	
	/**
	 * return string
	 */
	public function getWebexUserId ()
	{
		return $this->getFromCustomData(self::WEBEX_USER_ID);
	}

	/**
	 * return string
	 */
	public function getWebexSiteName ()
	{
		return $this->getFromCustomData(self::WEBEX_SITE_NAME);
	}

	/**
	 * @param string $v
	 */
	public function setWebexSiteName ($v)
	{
		$this->putInCustomData(self::WEBEX_SITE_NAME, $v);
	}


	/**
	 * @param string $v
	 */
	public function setWebexUserId ($v)
	{
		$this->putInCustomData(self::WEBEX_USER_ID, $v);
	}
	
	/**
	 * return string
	 */
	public function getWebexPassword ()
	{
		return $this->getFromCustomData(self::WEBEX_PASSWORD);
	}
	
	/**
	 * @param string $v
	 */
	public function setWebexPassword ($v)
	{
		$this->putInCustomData(self::WEBEX_PASSWORD, $v);
	}
	
	/**
	 * return int
	 */
	public function getWebexSiteId ()
	{
		return $this->getFromCustomData(self::WEBEX_SITE_ID);
	}
	
	/**
	 * @param int $v
	 */
	public function setWebexSiteId ($v)
	{
		$this->putInCustomData(self::WEBEX_SITE_ID, $v);
	}
	
	/**
	 * return string
	 */
	public function getWebexPartnerId ()
	{
		return $this->getFromCustomData(self::WEBEX_PARTNER_ID);
	}
	
	/**
	 * @param string $v
	 */
	public function setWebexPartnerId ($v)
	{
		$this->putInCustomData(self::WEBEX_PARTNER_ID, $v);
	}
	
	/**
	 * return string
	 */
	public function getWebexServiceUrl ()
	{
		return $this->getFromCustomData(self::WEBEX_SERVICE_URL);
	}
	
	/**
	 * @param string $v
	 */
	public function setWebexServiceUrl ($v)
	{
		$this->putInCustomData(self::WEBEX_SERVICE_URL, $v);
	}
	
	/**
	 * return string
	 */
	public function getWebexHostIdMetadataFieldName ()
	{
		return $this->getFromCustomData(self::WEBEX_HOST_ID_METADATA_FIELD_NAME);
	}
	
	/**
	 * @param string $v
	 */
	public function setWebexHostIdMetadataFieldName ($v)
	{
		$this->putInCustomData(self::WEBEX_HOST_ID_METADATA_FIELD_NAME, $v);
	}
	
	/**
	 * return string
	 */
	public function getDeleteFromRecycleBin ()
	{
		return $this->getFromCustomData(self::WEBEX_DELETE_FROM_RECYCLE_BIN);
	}
	
	/**
	 * @param string $v
	 */
	public function setDeleteFromRecycleBin ($v)
	{
		$this->putInCustomData(self::WEBEX_DELETE_FROM_RECYCLE_BIN, $v);
	}
	
	/**
	 * return string
	 */
	public function getWebexServiceType ()
	{
		return $this->getFromCustomData(self::WEBEX_SERVICE_TYPE);
	}
	
	/**
	 * @param string $v
	 */
	public function setWebexServiceType ($v)
	{
		$this->putInCustomData(self::WEBEX_SERVICE_TYPE, $v);
	}
	
	public function setDeleteFromTimestamp ($v)
	{
		$this->putInCustomData(self::WEBEX_DELETE_FROM_TIMESTAMP, $v);
	}
	
	public function getDeleteFromTimestamp()
	{
		return $this->getFromCustomData(self::WEBEX_DELETE_FROM_TIMESTAMP);
	}
	
	public function getImportJobData()
	{
		return new kDropFolderImportJobData();
	}
	
	public function getFolderUrl()
	{
		return $this->webexServiceUrl . "/" . $this->getPath();
	}
	
	protected function getRemoteFileTransferMgrType()
	{
		return kFileTransferMgrType::HTTP;
	}
	
}
