<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
abstract class ESearchItemData extends BaseObject
{
	abstract public function getType();

	abstract public function loadFromElasticHits($objectResult);

	/**
	 * @var array
	 */
	protected $highlight;

	/**
	 * @return array
	 */
	public function getHighlight()
	{
		return $this->highlight;
	}

	/**
	 * @param array $highlight
	 */
	public function setHighlight($highlight)
	{
		$this->highlight = $highlight;
	}
}