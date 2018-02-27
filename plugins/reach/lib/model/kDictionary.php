
<?php

/**
 * Define language Dictionary profile
 *
 * @package plugins.reach
 * @subpackage model
 *
 */
class kDictionary
{
	/**
	 * @var KalturaCatalogItemLanguage
	 */
	protected $language;

	/**
	 * @var string
	 */
	protected $data;

	/**
	 * @return the KalturaCatalogItemLanguage
	 */
	public function getLanguage()
	{
		return $this->language;
	}

	/**
	 * @return the data
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * @param KalturaCatalogItemLanguage $language
	 */
	public function setLanguage($language)
	{
		$this->language = $language;
	}

	/**
	 * @param string $data
	 */
	public function setData($data)
	{
		$this->data = $data;
	}
}