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
	
	const WEBEX_PARTNER_ID = 'webex_partner_id';
	
	const WEBEX_SERVICE_URL = 'webex_service_url';

	const WEBEX_HOST_ID_METADATA_FIELD_NAME = 'webex_host_id_metadata_field_name';

	const CATEGORIES_METADATA_FIELD_NAME = 'categories_metadata_field_name';
	
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
	protected $categoriesMetadataFieldName;
	
	/**
	 * return string
	 */
	public function getWebexUserId ()
	{
		return $this->getFromCustomData(self::WEBEX_USER_ID);
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
	public function getCategoriesMetadataFieldName ()
	{
		return $this->getFromCustomData(self::CATEGORIES_METADATA_FIELD_NAME);
	}
	
	/**
	 * @param string $v
	 */
	public function setCategoriesMetadataFieldName ($v)
	{
		$this->putInCustomData(self::CATEGORIES_METADATA_FIELD_NAME, $v);
	}
	
	public function getImportJobData()
	{
		return null;
	}
	
	public function getFolderUrl()
	{
		$this->webexServiceUrl . "/" . $this->getPath();
	}
	
	protected function getRemoteFileTransferMgrType()
	{
		return null;
	}
	
}
