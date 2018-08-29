<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
class kESearchSuggestContext
{

	const CONTEXT_KEY = 'context';
	const BOOST_KEY = 'boost';

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $value;

	/**
	 * @var int
	 */
	protected $boost;

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @return int
	 */
	public function getBoost()
	{
		return $this->boost;
	}

	/**
	 * @param int $boost
	 */
	public function setBoost($boost)
	{
		$this->boost = $boost;
	}

	/**
	 * @return string
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @param string $value
	 */
	public function setValue($value)
	{
		$this->value = $value;
	}

	public function getContext()
	{
		$context = array();
		$context[self::CONTEXT_KEY] = $this->getValue();
		if ($this->getBoost())
		{
			$context[self::BOOST_KEY] = $this->getBoost();
		}
		return $context;
	}

}
