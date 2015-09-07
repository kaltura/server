<?php

/**
 * Skeleton subclass for representing a row from the 'app_token' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package Core
 * @subpackage model
 */
class AppToken extends BaseAppToken
{
	const CUSTOM_DATA_FIELD_HASH_FUNCTION = 'hashType';

	private function calculateId()
	{
		$dc = kDataCenterMgr::getCurrentDc();
		for ($i = 0; $i < 10; $i++)
		{
			$id = $dc["id"] . '_' . kString::generateStringId();
			$existingObject = AppTokenPeer::retrieveByPkNoFilter($id);
			
			if ($existingObject)
				KalturaLog::log("ID [$id] already exists");
			else
				return $id;
		}
		
		throw new Exception("Could not find unique id for AppToken");
	}

	/* (non-PHPdoc)
	 * @see BaseAppToken::preInsert()
	 */
	public function preInsert(PropelPDO $con = null)
	{
		$this->setId($this->calculateId());
		return parent::preInsert($con);
	}
	
	/* (non-PHPdoc)
	 * @see BaseAppToken::preUpdate()
	 */
	public function preUpdate(PropelPDO $con = null)
	{
		if($this->isColumnModified(AppTokenPeer::STATUS) && $this->getStatus() == AppTokenStatus::DELETED)
		{
			$this->setDeleted(time());
		}
		
		return parent::preUpdate($con);
	}

	public function setHashType($hashFunction)
	{
		$this->putInCustomData(self::CUSTOM_DATA_FIELD_HASH_FUNCTION, $hashFunction);
	}
	
	public function getHashType()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_HASH_FUNCTION, null, "SHA1");
		
	}

	public function calcHash()
	{
		$hashFunction = $this->getHashType();
		return hash($hashFunction, kCurrentContext::$ks . $this->getToken());
	}
} // AppToken
