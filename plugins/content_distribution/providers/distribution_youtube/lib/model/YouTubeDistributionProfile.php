<?php
class YouTubeDistributionProfile extends DistributionProfile
{
	const CUSTOM_DATA_USERNAME = 'username';
	const CUSTOM_DATA_NOTIFICATION_EMAIL = 'notificationEmail';
	const CUSTOM_DATA_SFTP_HOST = 'sftpHost';
	const CUSTOM_DATA_SFTP_LOGIN = 'sftpLogin';
	const CUSTOM_DATA_SFTP_PUBLIC_KEY = 'sftpPublicKey';
	const CUSTOM_DATA_SFTP_PRIVATE_KEY = 'sftpPrivateKey';

	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return YouTubeDistributionPlugin::getProvider();
	}

	public function getUsername()				{return $this->getFromCustomData(self::CUSTOM_DATA_USERNAME);}
	public function getNotificationEmail()		{return $this->getFromCustomData(self::CUSTOM_DATA_NOTIFICATION_EMAIL);}
	public function getSftpHost()				{return $this->getFromCustomData(self::CUSTOM_DATA_SFTP_HOST);}
	public function getSftpLogin()				{return $this->getFromCustomData(self::CUSTOM_DATA_SFTP_LOGIN);}
	public function getSftpPublicKey()			{return $this->getFromCustomData(self::CUSTOM_DATA_SFTP_PUBLIC_KEY);}
	public function getSftpPrivateKey()			{return $this->getFromCustomData(self::CUSTOM_DATA_SFTP_PRIVATE_KEY);}
	
	public function setUsername($v)				{$this->putInCustomData(self::CUSTOM_DATA_USERNAME, $v);}
	public function setNotificationEmail($v)	{$this->putInCustomData(self::CUSTOM_DATA_NOTIFICATION_EMAIL, $v);}
	public function setSftpHost($v)				{$this->putInCustomData(self::CUSTOM_DATA_SFTP_HOST, $v);}
	public function setSftpLogin($v)			{$this->putInCustomData(self::CUSTOM_DATA_SFTP_LOGIN, $v);}
	public function setSftpPublicKey($v)		{$this->putInCustomData(self::CUSTOM_DATA_SFTP_PUBLIC_KEY, $v);}
	public function setSftpPrivateKey($v)		{$this->putInCustomData(self::CUSTOM_DATA_SFTP_PRIVATE_KEY, $v);}
}