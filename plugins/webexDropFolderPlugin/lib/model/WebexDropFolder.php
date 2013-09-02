<?php
/**
 * @package plugins.dropFolder
 * @subpackage model
 */
class WebexDropFolder extends DropFolder
{
	const WEBEX_USER_ID = 'webex_user_id';
	
	const WEBEX_PASSWORD = 'webex_password';
	
	const WEBEX_SITE_ID = 'webex_site_id';
	
	const WEBEX_PARTNER_ID = 'webex_partner_id';
	
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
	
}
