<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kBulkUploadJobData extends kJobData
{
	protected static $privilegesToPass = array(kSessionBase::PRIVILEGE_ENABLE_CATEGORY_MODERATION, kSessionBase::PRIVILEGE_ENABLE_CAPTION_MODERATION);

	/**
	 * @var int
	 */
	private $userId;
	
	/**
	 * The screen name of the user
	 * @var string
	 */
	private $uploadedBy;

	/**
	 * Selected profile id for all bulk entries
	 * @deprecated
	 * @var int
	 */
	private $conversionProfileId;
		
	/**
	 * Number of created entries
	 * @deprecated Use numOfObjects instead
	 * @var int
	 */
	private $numOfEntries;
	
	/**
	 * Created by the API
	 * @var string
	 */
	private $resultsFileLocalPath;
	
	/**
	 * Created by the API
	 * @var string
	 */
	private $resultsFileUrl;

	/**
	 * The bulk upload job file path
	 * @var string
	 */
	private $filePath;
	
	/**
	 * The bulk upload job file name
	 * @var string
	 */
	private $fileName;

	/**
	 * Type of object for bulk upload
	 * @var int
	 */
	protected $bulkUploadObjectType;

	/**
	 * Number of created objects
	 * @var int
	 */
	protected $numOfObjects;
	
	/**
	 * Data pertaining to the objects being uploaded
	 * @var kBulkUploadObjectData
	 */
	protected $objectData;
	
	/**
	 * Number of bulk upload results is status ERROR 
	 * @var int
	 */
	protected $numOfErrorObjects;

	/**
	 * Recipients of the email for bulk upload success/failure
	 * @var string
	 */
	 protected $emailRecipients;

	/**
	 * The bulk upload job ks privileges
	 * @var string
	 */
	protected $privileges;

	/**
	 * @return int $userId
	 */
	public function getUserId()
	{
		return $this->userId;
	}

	/**
	 * @return string $uploadedBy
	 */
	public function getUploadedBy()
	{
		return $this->uploadedBy;
	}

	/**
	 * @return int $conversionProfileId
	 */
	public function getConversionProfileId()
	{
		return $this->conversionProfileId;
	}

	/**
	 * @return int $numOfEntries
	 */
	public function getNumOfEntries()
	{
		return $this->numOfEntries;
	}

	/**
	 * @return string $privileges
	 */
	public function getPrivileges()
	{
		return $this->privileges;
	}

	/**
	 * @param int $userId
	 */
	public function setUserId($userId)
	{
		$this->userId = $userId;
	}

	/**
	 * @param string $uploadedBy
	 */
	public function setUploadedBy($uploadedBy)
	{
		$this->uploadedBy = $uploadedBy;
	}

	/**
	 * @param int $conversionProfileId
	 */
	public function setConversionProfileId($conversionProfileId)
	{
		$this->conversionProfileId = $conversionProfileId;
	}

	/**
	 * @param int $numOfEntries
	 */
	public function setNumOfEntries($numOfEntries)
	{
		$this->numOfEntries = $numOfEntries;
	}
	
	/**
	 * @return string $filePath
	 */
	public function getFilePath()
	{
		return $this->filePath;
	}

	/**
	 * @param string $filePath
	 */
	public function setFilePath($filePath)
	{
		$this->filePath = $filePath;
	}
	
	/**
	 * @return string $fileName
	 */
	public function getFileName()
	{
		return $this->fileName;
	}

	/**
	 * @param string $fileName
	 */
	public function setFileName($fileName)
	{
		$this->fileName = $fileName;
	}

	/**
	 * @return string $resultsFileLocalPath
	 */
	public function getResultsFileLocalPath()
	{
		return $this->resultsFileLocalPath;
	}

	/**
	 * @return string $resultsFileUrl
	 */
	public function getResultsFileUrl()
	{
		return $this->resultsFileUrl;
	}

	/**
	 * @param string $resultsFileLocalPath
	 */
	public function setResultsFileLocalPath($resultsFileLocalPath)
	{
		$this->resultsFileLocalPath = $resultsFileLocalPath;
	}

	/**
	 * @param string $resultsFileUrl
	 */
	public function setResultsFileUrl($resultsFileUrl)
	{
		$this->resultsFileUrl = $resultsFileUrl;
	}

	/**
	 * @return int $numOfObjects
	 */
	public function getNumOfObjects ()
	{
		return $this->numOfObjects;
	}

	/**
	 * @param int $numOfObjects
	 */
	public function setNumOfObjects ($numOfObjects)
	{
		$this->numOfObjects = $numOfObjects;
	}

	/**
	 * @return int $bulkUploadObjectType
	 */
	public function getBulkUploadObjectType ()
	{
		return $this->bulkUploadObjectType;
	}

	/**
	 * @param int $bulkUploadObjectType
	 */
	public function setBulkUploadObjectType ($bulkUploadObjectType)
	{
		$this->bulkUploadObjectType = $bulkUploadObjectType;
	}

	/**
	 * @return kBulkUploadObjectData $objectData
	 */
	public function getObjectData ()
	{
		return $this->objectData;
	}

	/**
	 * @param kBulkUploadObjectData $objectData
	 */
	public function setObjectData ($objectData)
	{
		$this->objectData = $objectData;
	}

	/**
	 * @return int $numOfErrorObjects
	 */
	public function getNumOfErrorObjects ()
	{
		return $this->numOfErrorObjects;
	}

	/**
	 * @param int $numOfErrorObjects
	 */
	public function setNumOfErrorObjects ($numOfErrorObjects)
	{
		$this->numOfErrorObjects = $numOfErrorObjects;
	}

	/**
	 * @param string $emailRecipients
	 */
	public function setEmailRecipients ($emailRecipients)
	{
		$this->emailRecipients = $emailRecipients;
	}

	/**
	 * @param string $privileges
	 */
	public function setPrivileges($privileges)
	{
		$this->privileges = $privileges;
	}

	/**
	 * @return string $emailRecipients
	 */
	public function getEmailRecipients ()
	{
		return $this->emailRecipients;
	}

	public function handleKsPrivileges()
	{
		if (!empty(kCurrentContext::$ks))
		{
			$ks = ks::fromSecureString(kCurrentContext::$ks);
			$extraPrivileges = array();
			foreach (self::$privilegesToPass as $privilege)
			{
				if($ks->hasPrivilege($privilege))
				{
					$extraPrivileges[] = $privilege;
				}
			}

			if(!empty($extraPrivileges))
			{
				$this->setPrivileges(implode(',', $extraPrivileges));
			}
		}
	}
}
