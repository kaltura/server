<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model
 */
class ESearchSearchHistoryInfo
{

	/**
	 * @var string
	 */
	protected $searchTerms;

	/**
	 * @var string
	 */
	protected $searchedObject;

	/**
	 * @var string
	 */
	protected $partnerId;

	/**
	 * @var string
	 */
	protected $kuserId;

	/**
	 * @var int
	 */
	protected $timestamp;

	/**
	 * @var string
	 */
	protected $searchContext;

	/**
	 * @return string
	 */
	public function getSearchTerms()
	{
		return $this->searchTerms;
	}

	/**
	 * @param string $searchTerms
	 */
	public function setSearchTerms($searchTerms)
	{
		$this->searchTerms = $searchTerms;
	}

	/**
	 * @return string
	 */
	public function getSearchedObject()
	{
		return $this->searchedObject;
	}

	/**
	 * @param string $searchedObject
	 */
	public function setSearchedObject($searchedObject)
	{
		$this->searchedObject = $searchedObject;
	}

	/**
	 * @return string
	 */
	public function getPartnerId()
	{
		return $this->partnerId;
	}

	/**
	 * @param string $partnerId
	 */
	public function setPartnerId($partnerId)
	{
		$this->partnerId = $partnerId;
	}

	/**
	 * @return string
	 */
	public function getKUserId()
	{
		return $this->kuserId;
	}

	/**
	 * @param string $userId
	 */
	public function setKUserId($userId)
	{
		$this->kuserId = $userId;
	}

	/**
	 * @return int
	 */
	public function getTimestamp()
	{
		return $this->timestamp;
	}

	/**
	 * @param int $timestamp
	 */
	public function setTimestamp($timestamp)
	{
		$this->timestamp = $timestamp;
	}

	/**
	 * @return string
	 */
	public function getSearchContext()
	{
		return $this->searchContext;
	}

	/**
	 * @param string $searchContext
	 */
	public function setSearchContext($searchContext)
	{
		$this->searchContext = $searchContext;
	}

	public function getPidUidContext()
	{
		$pidUidContextObject = array(
			searchHistoryUtils::formatPartnerIdUserIdContext($this->getPartnerId(), $this->getKUserId(), searchHistoryUtils::DEFAULT_SEARCH_CONTEXT),
		);

		if ($this->getSearchContext() != searchHistoryUtils::DEFAULT_SEARCH_CONTEXT)
		{
			$pidUidContextObject[] = searchHistoryUtils::formatPartnerIdUserIdContext($this->getPartnerId(), $this->getKUserId(), $this->getSearchContext());
		}

		return $pidUidContextObject;
	}

	public function getSearchContextArray()
	{
		$searchContext = $this->getSearchContext();
		return $searchContext == searchHistoryUtils::DEFAULT_SEARCH_CONTEXT ? array($searchContext) : array($searchContext, searchHistoryUtils::DEFAULT_SEARCH_CONTEXT);
	}

}