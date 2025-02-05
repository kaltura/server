<?php
/**
 * @package plugins.dropFolder
 * @subpackage model
 */
class S3DropFolder extends RemoteDropFolder
{
	const S3_HOST = 's3Host';

	const S3_REGION = 's3Region';

	const S3_USER_ID = 's3UserId';

	const S3_PASSWORD = 's3Password';
	
	const S3_ARN = 's3Arn';

	/**
	 * @var string
	 */
	protected $s3Host;

	/**
	 * @var string
	 */
	protected $s3Region;

	/**
	 * @var string
	 */
	protected $s3UserId;

	/**
	 * @var string
	 */
	protected $s3Password;
	
	/**
	 * @var bool
	 */
	protected $s3Arn;

	/**
	 * return string
	 */
	public function getS3Host (){ return $this->getFromCustomData(self::S3_HOST);}

	/**
	 * return string
	 */
	public function getS3Region (){ return $this->getFromCustomData(self::S3_REGION);}

	/**
	 * return string
	 */
	public function getS3UserId (){ return $this->getFromCustomData(self::S3_USER_ID);}

	/**
	 * return string
	 */
	public function getS3Password (){ return $this->getFromCustomData(self::S3_PASSWORD);}
	
	/**
	 * @return bool
	 */
	public function getS3Arn (){ return (bool) $this->getFromCustomData(self::S3_ARN);}

	/**
	 * @param string $v
	 */
	public function setS3Host ($v){ $this->putInCustomData(self::S3_HOST, $v);}

	/**
	 * @param string $v
	 */
	public function setS3Region ($v){ $this->putInCustomData(self::S3_REGION, $v);}

	/**
	 * @param string $v
	 */
	public function setS3UserId ($v){ $this->putInCustomData(self::S3_USER_ID, $v);}

	/**
	 * @param string $v
	 */
	public function setS3Password ($v){ $this->putInCustomData(self::S3_PASSWORD, $v);}
	
	/**
	 * @param bool $v
	 */
	public function setS3Arn ($v){ $this->putInCustomData(self::S3_ARN, (bool) $v);}


	public function getImportJobData()
	{
		return new kDropFolderImportJobData();
	}

	public function getFolderUrl()
	{
		return '';
	}

	protected function getRemoteFileTransferMgrType()
	{
		return kFileTransferMgrType::S3;
	}

	public function loginByCredentialsType(kFileTransferMgr $fileTransferMgr)
	{
		return $fileTransferMgr->login($this->getS3Host(), $this->getS3UserId(), $this->getS3Password());
	}

	public function getDropFolderParams()
	{
		return array(
			's3Host' => $this->getS3Host(),
			's3UserId' => $this->getS3UserId(),
			's3Password' => $this->getS3Password(),
			's3Region' => $this->getS3Region(),
			's3Arn' => (boolean) $this->getS3Arn(),
		);
	}
}
