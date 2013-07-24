<?php
/**
 * @package plugins.httpNotification
 * @subpackage model
 */
class HttpNotificationTemplate extends EventNotificationTemplate implements ISyncableFile
{
	const CUSTOM_DATA_URL = 'url';
	const CUSTOM_DATA_DATA = 'data';
	const CUSTOM_DATA_METHOD = 'method';
	const CUSTOM_DATA_TIMEOUT = 'timeout';
	const CUSTOM_DATA_CONNECT_TIMEOUT = 'connectTimeout';
	const CUSTOM_DATA_USERNAME = 'username';
	const CUSTOM_DATA_PASSWORD = 'password';
	const CUSTOM_DATA_AUTHENTICATION_METHOD = 'authenticationMethod';
	const CUSTOM_DATA_SSL_VERSION = 'sslVersion';
	const CUSTOM_DATA_SSL_CERTIFICATE = 'sslCertificate';
	const CUSTOM_DATA_SSL_CERTIFICATE_TYPE = 'sslCertificateType';
	const CUSTOM_DATA_SSL_CERTIFICATE_PASSWORD = 'sslCertificatePassword';
	const CUSTOM_DATA_SSL_ENGINE = 'sslEngine';
	const CUSTOM_DATA_SSL_ENGINE_DEFAULT = 'sslEngineDefault';
	const CUSTOM_DATA_SSL_KEY_TYPE = 'sslKeyType';
	const CUSTOM_DATA_SSL_KEY = 'sslKey';
	const CUSTOM_DATA_SSL_KEY_PASSWORD = 'sslKeyPassword';
	const CUSTOM_DATA_CUSTOM_HEADERS = 'customHeaders';
	const CUSTOM_DATA_POST_FILE_VERSION = 'postFileVersion';
	
	const FILE_SYNC_POST = 1;
	
	public function __construct()
	{
		$this->setType(HttpNotificationPlugin::getHttpNotificationTemplateTypeCoreValue(HttpNotificationTemplateType::HTTP));
		parent::__construct();
	}

	/* (non-PHPdoc)
	 * @see EventNotificationTemplate::getJobData()
	 */
	public function getJobData(kScope $scope = null)
	{
		$contentParametersValues = array();
		
		$userParameters = $this->getUserParameters();
		foreach($userParameters as $userParameter)
		{
			/* @var $userParameter kEventNotificationParameter */
			$value = $userParameter->getValue();
			if($scope && $value instanceof kStringField)
				$value->setScope($scope);
				
			$key = $userParameter->getKey();
			$contentParametersValues[$key] = $value->getValue();
			$scope->addDynamicValue($key, $value);
		}
		
		$contentParameters = $this->getContentParameters();
		foreach($contentParameters as $contentParameter)
		{
			/* @var $contentParameter kEventNotificationParameter */
			$value = $contentParameter->getValue();
			if($scope && $value instanceof kStringField)
				$value->setScope($scope);
				
			$key = $contentParameter->getKey();
			$contentParametersValues[$key] = $value->getValue();
			$scope->addDynamicValue($key, $value);
		}
	
		$data = $this->getData();
		if($data)
			$data->setScope($scope);
		
		$jobData = new kHttpNotificationDispatchJobData();
		$jobData->setTemplateId($this->getId());
		$jobData->setUrl($this->getUrl());
		$jobData->setDataObject($data);
		$jobData->setMethod($this->getMethod());
		$jobData->setTimeout($this->getTimeout());
		$jobData->setConnectTimeout($this->getConnectTimeout());
		$jobData->setUsername($this->getUsername());
		$jobData->setPassword($this->getPassword());
		$jobData->setAuthenticationMethod($this->getAuthenticationMethod());
		$jobData->setSslVersion($this->getSslVersion());
		$jobData->setSslCertificate($this->getSslCertificate());
		$jobData->setSslCertificateType($this->getsslCertificateType());
		$jobData->setSslCertificatePassword($this->getsslCertificatePassword());
		$jobData->setSslEngine($this->getsslEngine());
		$jobData->setSslEngineDefault($this->getsslEngineDefault());
		$jobData->setSslKeyType($this->getsslKeyType());
		$jobData->setSslKey($this->getsslKey());
		$jobData->setSslKeyPassword($this->getsslKeyPassword());
		$jobData->setCustomHeaders($this->getCustomHeaders());
		$jobData->setContentParameters($contentParametersValues);
		
		return $jobData;
	}
	
	/**
	 * @param int $sub_type
	 * @throws string
	 */
	private function getFileSyncVersion($sub_type)
	{
		switch($sub_type)
		{
			case self::FILE_SYNC_POST:
				return $this->getPostFileVersion();
		}
		return null;
	}
	
	/**
	 * @param int $sub_type
	 * @throws FileSyncException
	 */
	private static function validateFileSyncSubType($sub_type)
	{
		$valid_sub_types = array(
			self::FILE_SYNC_POST,
		);
		
		if(! in_array($sub_type, $valid_sub_types))
			throw new FileSyncException(HttpNotificationFileSyncObjectType::HTTP_NOTIFICATION_TEMPLATE, $sub_type, $valid_sub_types);
	}
	
	/* (non-PHPdoc)
	 * @see ISyncableFile::getSyncKey()
	 */
	public function getSyncKey( $sub_type , $version=null)
	{
		self::validateFileSyncSubType($sub_type);
		
		if(!$version)
			$version = $this->getFileSyncVersion($sub_type);
		
		$key = new FileSyncKey();
		$key->object_type = HttpNotificationPlugin::getHttpNotificationFileSyncObjectTypeCoreValue(HttpNotificationFileSyncObjectType::HTTP_NOTIFICATION_TEMPLATE);
		$key->object_sub_type = $sub_type;
		$key->object_id = $this->getId();
		$key->version = $version;
		$key->partner_id = $this->getPartnerId();
		
		return $key;
	}
	
	/* (non-PHPdoc)
	 * @see ISyncableFile::generateFilePathArr()
	 */
	public function generateFilePathArr ( $sub_type , $version=null )
	{
		self::validateFileSyncSubType ( $sub_type );
		
		if(!$version)
			$version = $this->getFileSyncVersion($sub_type);
		
		$dir = (intval($this->getId() / 1000000)) . '/' . (intval($this->getId() / 1000) % 1000);
		$path =  "/content/notifications/$dir/" . $this->generateFileName($sub_type, $version);

		return array(myContentStorage::getFSContentRootPath(), $path); 
	} 

	/* (non-PHPdoc)
	 * @see ISyncableFile::generateFileName()
	 */
	public function generateFileName( $sub_type, $version = null)
	{
		self::validateFileSyncSubType($sub_type);
		
		if(!$version)
			$version = $this->getFileSyncVersion($sub_type);
	
		return $this->getId() . "_{$sub_type}_{$version}.txt";	
	}
	
	/**
	 * @var FileSync
	 */
	private $fileSync;

	/* (non-PHPdoc)
	 * @see ISyncableFile::getFileSync()
	 */
	public function getFileSync()
	{
		return $this->fileSync; 
	}

	/* (non-PHPdoc)
	 * @see ISyncableFile::setFileSync()
	 */
	public function setFileSync(FileSync $fileSync)
	{
		 $this->fileSync = $fileSync;
	}
	
	/**
	 * @var string
	 */
	private $setPost = null;
	
	/**
	 * @var string
	 */
	private $cachedPost = null;
	
	/**
	 * @var int
	 */
	private $postPreviousVersion = null;

	public function getPost()
	{
		if($this->cachedPost)
			return $this->cachedPost;
			
		$key = $this->getSyncKey(self::FILE_SYNC_POST);
		$this->cachedPost = kFileSyncUtils::file_get_contents($key, true, false);
		return $this->cachedPost;
	}

	public function setPost($post)
	{
		$this->getPost();
		if($post != $this->cachedPost)
			$this->setPost = $post;
	}

	/* (non-PHPdoc)
	 * @see BaseEventNotificationTemplate::preSave()
	 */
	public function preSave(PropelPDO $con = null)
	{
		if($this->setPost)
		{
			$this->postPreviousVersion = $this->getPostFileVersion();
			if($this->postPreviousVersion)
				$this->incrementPostFileVersion();
			else 
				$this->resetPostFileVersion();
		}
			
		return parent::preSave($con);
	}

	/* (non-PHPdoc)
	 * @see BaseEventNotificationTemplate::postSave()
	 */
	public function postSave(PropelPDO $con = null)
	{
		if($this->wasObjectSaved() && $this->setPost)
		{
			$key = $this->getSyncKey(self::FILE_SYNC_POST);
			kFileSyncUtils::file_put_contents($key, $this->setPost);
			$this->cachedPost = $this->setPost;
			$this->setPost = null;
			
			kEventsManager::raiseEvent(new kObjectDataChangedEvent($this, $this->postPreviousVersion));	
		}
		
		return parent::postSave($con);
	}

	/**
	 * @return kHttpNotificationData
	 */
	public function getData()									{return $this->getFromCustomData(self::CUSTOM_DATA_DATA);}
	
	public function getPostFileVersion()						{return $this->getFromCustomData(self::CUSTOM_DATA_POST_FILE_VERSION);}	
	public function getUrl()									{return $this->getFromCustomData(self::CUSTOM_DATA_URL);}
	public function getMethod()									{return $this->getFromCustomData(self::CUSTOM_DATA_METHOD);}
	public function getTimeout()								{return $this->getFromCustomData(self::CUSTOM_DATA_TIMEOUT);}
	public function getConnectTimeout()							{return $this->getFromCustomData(self::CUSTOM_DATA_CONNECT_TIMEOUT);}
	public function getUsername()								{return $this->getFromCustomData(self::CUSTOM_DATA_USERNAME);}
	public function getPassword()								{return $this->getFromCustomData(self::CUSTOM_DATA_PASSWORD);}
	public function getAuthenticationMethod()					{return $this->getFromCustomData(self::CUSTOM_DATA_AUTHENTICATION_METHOD);}
	public function getSslVersion()								{return $this->getFromCustomData(self::CUSTOM_DATA_SSL_VERSION);}
	public function getSslCertificate()							{return $this->getFromCustomData(self::CUSTOM_DATA_SSL_CERTIFICATE);}
	public function getSslCertificateType()						{return $this->getFromCustomData(self::CUSTOM_DATA_SSL_CERTIFICATE_TYPE);}
	public function getSslCertificatePassword()					{return $this->getFromCustomData(self::CUSTOM_DATA_SSL_CERTIFICATE_PASSWORD);}
	public function getSslEngine()								{return $this->getFromCustomData(self::CUSTOM_DATA_SSL_ENGINE);}
	public function getSslEngineDefault()						{return $this->getFromCustomData(self::CUSTOM_DATA_SSL_ENGINE_DEFAULT);}
	public function getSslKeyType()								{return $this->getFromCustomData(self::CUSTOM_DATA_SSL_KEY_TYPE);}
	public function getSslKey()									{return $this->getFromCustomData(self::CUSTOM_DATA_SSL_KEY);}
	public function getSslKeyPassword()							{return $this->getFromCustomData(self::CUSTOM_DATA_SSL_KEY_PASSWORD);}
	public function getCustomHeaders()							{return $this->getFromCustomData(self::CUSTOM_DATA_CUSTOM_HEADERS, null, array());}

	public function incrementPostFileVersion()					{return $this->incInCustomData(self::CUSTOM_DATA_POST_FILE_VERSION);}
	public function resetPostFileVersion()						{return $this->putInCustomData(self::CUSTOM_DATA_POST_FILE_VERSION, 1);}
	
	public function setData(kHttpNotificationData $v = null)	{return $this->putInCustomData(self::CUSTOM_DATA_DATA, $v);}
	public function setUrl($v)									{return $this->putInCustomData(self::CUSTOM_DATA_URL, $v);}
	public function setMethod($v)								{return $this->putInCustomData(self::CUSTOM_DATA_METHOD, $v);}
	public function setTimeout($v)								{return $this->putInCustomData(self::CUSTOM_DATA_TIMEOUT, $v);}
	public function setConnectTimeout($v)						{return $this->putInCustomData(self::CUSTOM_DATA_CONNECT_TIMEOUT, $v);}
	public function setUsername($v)								{return $this->putInCustomData(self::CUSTOM_DATA_USERNAME, $v);}
	public function setPassword($v)								{return $this->putInCustomData(self::CUSTOM_DATA_PASSWORD, $v);}
	public function setAuthenticationMethod($v)					{return $this->putInCustomData(self::CUSTOM_DATA_AUTHENTICATION_METHOD, $v);}
	public function setSslVersion($v)							{return $this->putInCustomData(self::CUSTOM_DATA_SSL_VERSION, $v);}
	public function setSslCertificate($v)						{return $this->putInCustomData(self::CUSTOM_DATA_SSL_CERTIFICATE, $v);}
	public function setSslCertificateType($v)					{return $this->putInCustomData(self::CUSTOM_DATA_SSL_CERTIFICATE_TYPE, $v);}
	public function setSslCertificatePassword($v)				{return $this->putInCustomData(self::CUSTOM_DATA_SSL_CERTIFICATE_PASSWORD, $v);}
	public function setSslEngine($v)							{return $this->putInCustomData(self::CUSTOM_DATA_SSL_ENGINE, $v);}
	public function setSslEngineDefault($v)						{return $this->putInCustomData(self::CUSTOM_DATA_SSL_ENGINE_DEFAULT, $v);}
	public function setSslKeyType($v)							{return $this->putInCustomData(self::CUSTOM_DATA_SSL_KEY_TYPE, $v);}
	public function setSslKey($v)								{return $this->putInCustomData(self::CUSTOM_DATA_SSL_KEY, $v);}
	public function setSslKeyPassword($v)						{return $this->putInCustomData(self::CUSTOM_DATA_SSL_KEY_PASSWORD, $v);}
	public function setCustomHeaders(array $v)					{return $this->putInCustomData(self::CUSTOM_DATA_CUSTOM_HEADERS, $v);}
}
