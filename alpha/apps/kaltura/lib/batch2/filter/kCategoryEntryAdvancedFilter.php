<?php
/**
 * @package api
 * @subpackage filters
 */
class kCategoryEntryAdvancedFilter extends AdvancedSearchFilterItem
{
	const CREATED_AT = 'createdAt';

	/**
	 * @var string
	 */
	protected $categoriesMatchOr = null;
	
	/**
	 * @var string
	 */
	protected $categoryEntryStatusIn = null;

	/**
	 * @var string
	 */
	protected $orderBy = null;

	/**
	 * @var int
	 */
	protected $categoryIdEqual = null;

	/**
	 * Compose a dynamic attribute field name
	 *
	 * @param int $categoryId
	 * @return string cat_{cat id}_createdAt (e.g. cat_32_createdAt)
	 */
	public static function getCategoryCreatedAtDynamicAttributeName( $categoryId )
	{
		return "cat_{$categoryId}_createdAt";
	}

	/* (non-PHPdoc)
	 * @see AdvancedSearchFilterItem::applyCondition()
	 */
	public function applyCondition(IKalturaDbQuery $query)
	{
		if ( is_null( $this->categoriesMatchOr ) && is_null( $this->categoryIdEqual ) )
		{
			return;
		}

		// Fetch the list of categories
		$categoryEntries = null;
		if ( $this->categoriesMatchOr )
		{
			$categoryEntries = entryFilter::categoryFullNamesToIdsParsed ($this->categoriesMatchOr, $this->categoryEntryStatusIn );
		}
		else
		{
			$categoryEntries = entryFilter::categoryIdsToSphinxIds( $this->categoryIdEqual, $this->categoryEntryStatusIn );
		}

		if ( $categoryEntries == '' )
		{
			// Set a non-exiting cat. id. in order to return empty results (instead of throwing an exception)
			$categoryEntries = category::CATEGORY_ID_THAT_DOES_NOT_EXIST;
		}

		$categoryEntries = explode( ',', $categoryEntries );

		if($query instanceof IKalturaIndexQuery)
		{
			$categoriesStrs = array();
			foreach($categoryEntries as $categoryId)
			{
				$categoriesStrs[] = '"'.$categoryId.'"';
			}
			$query->addMatch('@' . entryIndex::getIndexFieldName(entryPeer::CATEGORIES_IDS) . ' (' . implode(' | ', $categoriesStrs) . ')');
		}
		else
		{
			$query->addColumnWhere(entryPeer::CATEGORIES_IDS, $categoryEntries, KalturaCriteria::IN_LIKE);
		}

		if ( $this->orderBy )
		{
			$orderByField = substr($this->orderBy, 1);
			$orderBy = $this->orderBy[0] == '+' ? Criteria::ASC : Criteria::DESC;

			if ( $orderByField != self::CREATED_AT )
			{
				throw new kCoreException( "Unsupported orderBy criteria [$orderByField]" );
			}

			$dynAttribCriteriaFieldName = entryIndex::DYNAMIC_ATTRIBUTES . '.' . self::getCategoryCreatedAtDynamicAttributeName( $this->categoryIdEqual );
			$query->addNumericOrderBy( $dynAttribCriteriaFieldName, $orderBy);
		}
	}
	
	/**
	 * @return string $categoriesMatchOr
	 */
	public function getCategoriesMatchOr()
	{
		return $this->categoriesMatchOr;
	}

	/**
	 * @param string $categoriesMatchOr
	 */
	public function setCategoriesMatchOr($categoriesMatchOr)
	{
		$this->categoriesMatchOr = $categoriesMatchOr;
	}
	
	/**
	 * @return string $categoryEntryStatusIn
	 */
	public function getCategoryEntryStatusIn()
	{
		return $this->categoryEntryStatusIn;
	}

	/**
	 * @param string $categoryEntryStatusIn
	 */
	public function setCategoryEntryStatusIn($categoryEntryStatusIn)
	{
		$this->categoryEntryStatusIn = $categoryEntryStatusIn;
	}

	/**
	 * @param string $orderBy
	 */
	public function setOrderBy($orderBy)
	{
		$this->orderBy = $orderBy;
	}
	
	/**
	 * @return string $orderBy
	 */
	public function getOrderBy()
	{
		return $this->orderBy;
	}

	/**
	 * @param int $categoryIdEqual
	 */
	public function setCategoryIdEqual($categoryIdEqual)
	{
		$this->categoryIdEqual = $categoryIdEqual;
	}
	
	/**
	 * @return int $categoryIdEqual
	 */
	public function getCategoryIdEqual()
	{
		return $this->categoryIdEqual;
	}

	public function addToXml(SimpleXMLElement &$xmlElement)
	{
		parent::addToXml($xmlElement);

		if (!is_null($this->categoriesMatchOr))
			$xmlElement->addAttribute('categoriesMatchOr', $this->categoriesMatchOr);
		if (!is_null($this->categoryEntryStatusIn))
			$xmlElement->addAttribute('categoryEntryStatusIn', $this->categoryEntryStatusIn);
		if (!is_null($this->orderBy))
			$xmlElement->addAttribute('orderBy', $this->orderBy);
		if (!is_null($this->categoryIdEqual))
			$xmlElement->addAttribute('categoryIdEqual', $this->categoryIdEqual);
	}

	public function fillObjectFromXml(SimpleXMLElement $xmlElement)
	{
		parent::fillObjectFromXml($xmlElement);

		$attr = $xmlElement->attributes();
		if(isset($attr['categoriesMatchOr']))
			$this->categoriesMatchOr = (string)$attr['categoriesMatchOr'];
		if(isset($attr['categoryEntryStatusIn']))
			$this->categoryEntryStatusIn = (string)$attr['categoryEntryStatusIn'];
		if(isset($attr['orderBy']))
			$this->orderBy = (string)$attr['orderBy'];
		if(isset($attr['categoryIdEqual']))
			$this->categoryIdEqual = (int)$attr['categoryIdEqual'];
	}
}
