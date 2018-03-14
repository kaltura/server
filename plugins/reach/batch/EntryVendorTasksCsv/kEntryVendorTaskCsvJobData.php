<?php
/**
 * @package plugins.reach
 * @subpackage batch
 */
class kEntryVendorTaskCsvJobData extends kJobData
{

	/**
	 * The filter should return the list of users that need to be specified in the csv.
	 * @var baseObjectFilter
	 */
	private $filter;


	/**
	 * The users name
	 * @var string
	 */
	private $userName;


	/**
	 * The users email
	 * @var string
	 */
	private $userMail;


	/**
	 * The file location
	 * @var string
	 */
	private $outputPath;


	/**
	 *
	 * @return baseObjectFilter $filter
	 */
	public function getFilter()
	{
		return $this->filter;
	}

	/**
	 * @param baseObjectFilter $filter
	 */
	public function setFilter($filter)
	{
		$this->filter = $filter;
	}


	/**
	 * @return string
	 */
	public function getUserMail()
	{
		return $this->userMail;
	}

	/**
	 * @param string $userMail
	 */
	public function setUserMail($userMail)
	{
		$this->userMail = $userMail;
	}

	/**
	 * @return string
	 */
	public function getUserName()
	{
		return $this->userName;
	}

	/**
	 * @param string $userName
	 */
	public function setUserName($userName)
	{
		$this->userName = $userName;
	}

	/**
	 * @return string
	 */
	public function getOutputPath()
	{
		return $this->outputPath;
	}

	/**
	 * @param string $outputPath
	 */
	public function setOutputPath($outputPath)
	{
		$this->outputPath = $outputPath;
	}
}