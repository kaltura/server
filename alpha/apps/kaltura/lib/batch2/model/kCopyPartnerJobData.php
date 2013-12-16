<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kCopyPartnerJobData extends kJobData
{
	/**
	 * The PartnerId to copy from
	 * @var int
	 */
	private $fromPartnerId;
	
	/**
	 * The PartnerId to copy to
	 * @var int
	 */
	private $toPartnerId;

	
	/**
	 * @return int $fromPartnerId
	 */
	public function getFromPartnerId()
	{
		return $this->fromPartnerId;
	}
	
	/**
	 * @param int $fromPartnerId
	 */
	public function setFromPartnerId($fromPartnerId)
	{
		$this->fromPartnerId = $fromPartnerId;
	}

	/**
	 * @return int $toPartnerId
	 */
	public function getToPartnerId()
	{
		return $this->toPartnerId;
	}
	
	/**
	 * @param int $toPartnerId
	 */
	public function setToPartnerId($toPartnerId)
	{
		$this->toPartnerId = $toPartnerId;
	}
}
