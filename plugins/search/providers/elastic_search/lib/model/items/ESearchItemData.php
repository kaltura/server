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
	 * @var string
	 */
	protected $highlight;

	/**
	 * @return string
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