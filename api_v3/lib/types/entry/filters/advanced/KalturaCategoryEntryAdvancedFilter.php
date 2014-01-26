<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaCategoryEntryAdvancedFilter extends KalturaSearchItem
{
	/**
	 * @var string
	 */
	public $categoriesMatchOr;
	
	/**
	 * @var string
	 */
	public $categoryEntryStatusIn;
	
	/**
	 * @var KalturaCategoryEntryAdvancedOrderBy
	 */
	public $orderBy;
	
	/**
	 * @var int
	 */
	public $categoryIdEqual;
	
	private static $map_between_objects = array
	(
		"categoriesMatchOr",
		"categoryEntryStatusIn",
		"orderBy",
		"categoryIdEqual",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(!$object_to_fill)
			$object_to_fill = new kCategoryEntryAdvancedFilter();
			
		return parent::toObject($object_to_fill, $props_to_skip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUsage($sourceObject, $propertiesToSkip)
	 */
	public function validateForUsage($sourceObject, $propertiesToSkip = array())
	{
		parent::validateForUsage($sourceObject, $propertiesToSkip);

		if ( $this->orderBy )
		{
			$orderByField = substr($this->orderBy, 1);
			if ( $orderByField !== kCategoryEntryAdvancedFilter::CREATED_AT )
			{
				throw new KalturaAPIException( APIErrors::INVALID_FIELD_VALUE, "orderBy" );
			}
			
			if ( $this->categoryEntryStatusIn )
			{
				if ( trim( $this->categoryEntryStatusIn ) != CategoryEntryStatus::ACTIVE )
				{
					throw new KalturaAPIException( APIErrors::INVALID_FIELD_VALUE, "status" );
				}
			}
			else
			{
				throw new KalturaAPIException( KalturaErrors::MANDATORY_PARAMETER_MISSING, "categoryEntryStatusIn" );
			}

			if ( ! $this->categoryIdEqual )
			{
				throw new KalturaAPIException( KalturaErrors::MANDATORY_PARAMETER_MISSING, "categoryIdEqual" );
			}
		}
		else if ( !is_null($this->categoriesMatchOr) && !is_null($this->categoryEntryStatusIn))
		{
			if ( $categoryIdEqual )
			{
				throw new KalturaAPIException( APIErrors::INVALID_FIELD_VALUE, "categoryIdEqual" ); // Must be null in this scenario
			}
		}
		else
		{
			throw new KalturaAPIException( KalturaErrors::MANDATORY_PARAMETER_MISSING, "categoriesMatchOr / orderBy" );
		}
	}
}
