<?php
/**
 * @package Core
 * @subpackage model.filters
 */
abstract class KalturaCriterion extends Criterion implements IKalturaDbQuery
{
	const TAG_ENTITLEMENT_ENTRY = 'TAG_ENTITLEMENT_ENTRY';
	const TAG_ENTITLEMENT_CATEGORY = 'TAG_ENTITLEMENT_CATEGORY';
	const TAG_WIDGET_SESSION = 'TAG_WIDGET_SESSION';
	const TAG_PARTNER_SESSION = 'TAG_PARTNER_SESSION';
	const TAG_USER_SESSION = 'TAG_USER_SESSION';
	
	/**
	 * @var KalturaCriterion
	 */
	protected $parentCriterion = null;
	
	/**
	 * @var KalturaCriteria
	 */
	protected $criteria = false;
	
	protected static $enabledTags = array('TAG_PARTNER_SESSION' => 0,);
	
	protected $tags = array();
	
	public static function clearTags()
	{
		self::$enabledTags = array('TAG_PARTNER_SESSION' => 0,);
	}
	
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
		if (!isset(self::$enabledTags[$tag]))
			self::$enabledTags[$tag] = 0;
	}
	
	public static function restoreTag($tag)
	{
		if(!isset(self::$enabledTags[$tag]))
			return;
			
		self::$enabledTags[$tag]++;
			
		if(self::$enabledTags[$tag] > 0)
			throw new Exception("Enable called more times than disable");
	}
	
	public static function restoreTags(array $tags)
	{
		foreach($tags as $tag)
			self::restoreTag($tag);
	}
	
	public static function disableTag($tag)
	{
		if(isset(self::$enabledTags[$tag]))
			self::$enabledTags[$tag]--;
	}
	
	public static function disableTags($tags)
	{
		foreach($tags as $tag)
			self::disableTag($tag);
	}
	
	public static function isTagEnable($tag)
	{
		return (isset(self::$enabledTags[$tag]) && self::$enabledTags[$tag] === 0);
	}
	
	public function isEnabled()
	{
		if(!count($this->getTags()))
			return true;
			
		foreach ($this->getTags() as $tag)
		{
			if(self::isTagEnable($tag))
				return true;
		}
		
		return false;
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
	public function getConjunction()
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
	
	/* (non-PHPdoc)
	 * @see IKalturaDbQuery::addColumnWhere()
	 */
	public function addColumnWhere($column, $value, $comparison)
	{
		$criterion = $this->criteria->getNewCriterion($column, $value, $comparison);
		
		if($this->getConjunction() == self::ODER)
			$this->addOr($criterion);
		else
			$this->addAnd($criterion);
	}

	public function handleConditionClause()
	{

	}
}