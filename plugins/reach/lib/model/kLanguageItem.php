
<?php

/**
 * Catalog Item pricing calac definition
 *
 * @package plugins.reach
 * @subpackage model
 *
 */
class kLanguageItem
{
	/**
	 * @var string
	 */
	protected $language;
	
	/**
	 * @return the $language
	 */
	public function getLanguage()
	{
		return $this->language;
	}
	
	/**
	 * @param string $language
	 */
	public function setLanguage($language)
	{
		$this->language = $language;
	}
}