<?php
/**
 * @package api
 * @subpackage filters
 */
class kCategoryEntryAdvancedFilter extends AdvancedSearchFilterItem
{
	/**
	 * @var string
	 */
	protected $categoriesMatchOr = null;
	
	/**
	 * @var string
	 */
	protected $categoryEntryStatusIn = null;

	/* (non-PHPdoc)
	 * @see AdvancedSearchFilterItem::apply()
	 */
	public function apply(baseObjectFilter $filter, IKalturaDbQuery $query)
	{
		if (is_null($this->categoriesMatchOr) || is_null($this->categoryEntryStatusIn))
			return;
			
		$categoriesTocategoryEntryStatus = entryFilter::categoryFullNamesToIdsParsed ($this->categoriesMatchOr, $this->categoryEntryStatusIn );
		
		if($categoriesTocategoryEntryStatus == '')
			$categoriesTocategoryEntryStatus = category::CATEGORY_ID_THAT_DOES_NOT_EXIST;
		
		$query->addColumnWhere(entryPeer::CATEGORIES_IDS, explode(',', $categoriesTocategoryEntryStatus), kalturaCriteria::IN_LIKE);
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

	
}
