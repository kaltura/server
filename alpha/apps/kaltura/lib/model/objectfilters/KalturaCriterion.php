<?php
/**
 * @package Core
 * @subpackage model.filters
 */
abstract class KalturaCriterion extends Criterion
{
	const TAG_ENTITLEMENT_ENTRY = 'TAG_ENTITLEMENT_ENTRY';
	const TAG_ENTITLEMENT_CATEGORY = 'TAG_ENTITLEMENT_CATEGORY';
	/**
	 * @var KalturaCriterion
	 */
	protected $parentCriterion = null;
	
	/**
	 * @var KalturaCriteria
	 */
	protected $criteria = false;
	
	public static $enabledTags = array();
	
	protected $tags = array();
	
	public function setTags($tags)
	{
		$this->tags = $tags;
	}
	
	public function addTag($tag)
	{
		$this->tags[] = $tag;
	}
	
	public function getTags()
	{
		return $this->tags;
	}
	
	public static function enableTag($tag)
	{
		self::$enabledTags[$tag] = $tag;
	}
	
	public static function disableTag($tag)
	{
		if(isset(self::$enabledTags[$tag]))
			unset(self::$enabledTags[$tag]);
	}
	
	public static function isTagEnable($tag)
	{
		return isset(self::$enabledTags[$tag]);
	}
	
	/**
	 * AND or OR
	 * @var string
	 */
	protected $selfConjunction = '';
	
	/**
	 * @param Criteria $criteria
	 * @param string $column
	 * @param string $value
	 * @param string $comparison
	 */

	public function __construct(Criteria $criteria, $column, $value, $comparison = null)
	{
		$this->criteria = $criteria;
		
		parent::__construct($criteria, $column, $value, $comparison);
	}

	/**
	 * @param IKalturaIndexQuery $query
	 * @param int $depth
	 * @param bool $queryHasOr
	 */
	abstract public function apply(IKalturaIndexQuery $query);
	
	/* (non-PHPdoc)
	 * @see Criterion::addAnd()
	 */
	public function addAnd(Criterion $criterion)
	{
		if($criterion instanceof KalturaCriterion)
		{
			$criterion->setParentCriterion($this);
			$criterion->setSelfConjunction(self::UND);
		}
			
		return parent::addAnd($criterion);
	}

	/* (non-PHPdoc)
	 * @see Criterion::addOr()
	 */
	public function addOr(Criterion $criterion)
	{
		if($criterion instanceof KalturaCriterion)
		{
			$criterion->setSelfConjunction(self::ODER);
			$criterion->setParentCriterion($this);
		}
			
		return parent::addOr($criterion);
	}
	
	/**
	 * @return KalturaCriterion $parentCriterion
	 */
	protected function getParentCriterion()
	{
		return $this->parentCriterion;
	}

	/**
	 * @param KalturaCriterion $parentCriterion
	 */
	protected function setParentCriterion(KalturaCriterion $parentCriterion)
	{
		$this->parentCriterion = $parentCriterion;
	}
	
	/**
	 * @return string $selfConjunction
	 */
	protected function getSelfConjunction()
	{
		return $this->selfConjunction;
	}

	/**
	 * @param string $selfConjunction
	 */
	protected function setSelfConjunction($selfConjunction)
	{
		$this->selfConjunction = $selfConjunction;
	}
}