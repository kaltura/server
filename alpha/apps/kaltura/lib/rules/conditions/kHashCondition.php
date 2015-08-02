<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kHashCondition extends kCondition
{
	/* (non-PHPdoc)
	 * @see kCondition::__construct()
	 */
	public function __construct($not = false)
	{
		$this->setType(ConditionType::HASH);
		parent::__construct($not);
	}
	
	/**
	 * @var string
	 */
	protected $hashName;

	/**
	 * @var string
	 */
	protected $hashSecret;

	/**
	 * @param string $hashName
	 */
	public function setHashName($hashName)
	{
		$this->hashName = $hashName;
	}

	/**
	 * @return string
	 */
	public function getHashName()
	{
		return $this->hashName;
	}

	/**
	 * @param string $hashSecret
	 */
	public function setHashSecret($hashSecret)
	{
		$this->hashSecret = $hashSecret;
	}

	/**
	 * @return string
	 */
	public function gethashSecret()
	{
		return $this->hashSecret;
	}

	/* (non-PHPdoc)
	 * @see kCondition::internalFulfilled()
	 */
	protected function internalFulfilled(kScope $scope)
	{
		$hashes = $scope->getHashes();
		if (is_array($hashes) && isset($hashes[$this->hashName]))
		{
			$sentHash = $hashes[$this->hashName];
			$compareHash = md5($this->hashSecret. kCurrentContext::$ks);
			if ($sentHash === $compareHash)
			{
				KalturaLog::info("Correct hash sent");
				return false;
			}
			
		}
		
		KalturaLog::info("Incorrect hash sent");
		return true;
    }

	/* (non-PHPdoc)
	 * @see kCondition::shouldDisableCache()
	 */
	public function shouldDisableCache($scope)
	{
		return false;
	}
}
