<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
abstract class ESearchBaseQueryFilterAttributes
{
	protected $ignoreDisplayInSearchValues;

	function __construct()
	{
		$this->ignoreDisplayInSearchValues = array();
	}

	public abstract function getDisplayInSearchFilter();

	public function addValueToIgnoreDisplayInSearch($key, $value)
	{
		if(!array_key_exists($key, $this->ignoreDisplayInSearchValues))
			$this->ignoreDisplayInSearchValues[$key] = array();

		$this->ignoreDisplayInSearchValues[$key][] = $value;
	}
}