<?php
/**
 * @package Core
 * @subpackage model.filters
 */ 
class categoryFilter extends baseObjectFilter
{
	const FREE_TEXT_FIELDS = 'str_category_id,name,tags,description,reference_id';
	const NAME_REFERNCE_ID = 'name,reference_id';
	const MEMBERS = 'members';
	
	public function init ()
	{
		// TODO - should separate the schema of the fields from the actual values
		// or can use this to set default valuse
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
			'_eq_id',
			'_in_id',
			'_notin_id',
			'_eq_parent_id',
			'_in_parent_id',
			'_eq_full_name',
			'_likex_full_name',
			'_in_full_name',
			'_eq_depth',
			'_gte_created_at',
			'_lte_created_at',
			'_free_text',
			'_likex_name_or_reference_id',
			'_in_members',
			'_gte_updated_at',
			'_lte_updated_at',
			'_like_tags',
			'_mlikeor_tags',
			'_mlikeand_tags',
			'_eq_display_in_search',
			'_eq_privacy',
			'_in_privacy',
			'_eq_inheritance_type',
			'_in_inheritance_type',
			'_eq_status',
			'_in_status',
			'_gte_partner_sort_value',
			'_lte_partner_sort_value',
			'_eq_full_ids',
			'_likex_full_ids',
			'_eq_inherited_parent_id',
			'_in_inherited_parent_id',
			'_eq_privacy_context',
			'_eq_manager',
			'_eq_member',
			'_gte_members_count',
			'_lte_members_count',
			'_gte_pending_members_count',
			'_lte_pending_members_count',
		    '_eq_reference_id',
			'_empty_reference_id',
			'_eq_contribution_policy',		
			'_matchor_full_name',
			'_matchor_likex_full_name',
			'_matchor_full_ids',
			'_like_full_name',
			'_matchor_privacy_context',
			'_in_ancestor_id',
		    '_eq_name',
		    '_in_id-inherited_parent_id',
			'_mlikeor_aggregation_categories',
			'_mlikeand_aggregation_categories',
			) , NULL );

		$this->allowed_order_fields = array (
			'created_at', 
			'updated_at', 
			'depth', 
			'partner_sort_value', 
			'entries_count', 
			'members_count', 
			'direct_entries_count', 
			'direct_sub_categories_count', 
			'name',
			'full_name',
		);

		$this->aliases = array ( 
			'owner' => 'kuser_id'
		);
	}

	public function describe() 
	{
		return 
			array (
				'display_name' => 'CategoryFilter',
				'desc' => ''
			);
	}
	
	// TODO - move to base class, all that should stay here is the peer class, not the logic of the field translation !
	// The base class should invoke $peek_class::translateFieldName( $field_name , BasePeer::TYPE_FIELDNAME , BasePeer::TYPE_COLNAME );
	public function getFieldNameFromPeer($field_name)
	{
		try
		{
			return categoryPeer::translateFieldName($field_name, $this->field_name_translation_type , BasePeer::TYPE_COLNAME );
		}
		catch(PropelException $e)
		{
			return 'category.' . strtoupper($field_name);
		}
	}

	public function getIdFromPeer (  )
	{
		return categoryPeer::ID;
	}
	
	public function setFullIdsStartsWith($v)
	{
		$this->set('_likex_full_ids', $v);
	}
	
	public function setIdIn($v)
	{
		$this->set('_in_id', $v);
	}
	
	public function setInheritedParentId($v)
	{
		$this->set('_eq_inherited_parent_id', $v);
	}
	
	public function setInheritanceTypeEqual($v)
	{
		$this->set('_eq_inheritance_type', $v);
	}
	
	/**
	 * Convert the categories to categories ids - not includes the category itself (only sub categories)
	 * 
	 * @param string $cats Categories full names
	 * @param string $statuses comma seperated
	 * @return string Comma seperated fullIds
	 */
	public static function categoryIdsToAllSubCategoriesIdsParsed($cats)
	{
		if ($cats === "")
			$cats = array();
		else
			$cats = explode(",", $cats);
		kArray::trim($cats);
		
		$categoryFullIdsToIds = array();
		foreach($cats as $cat)
		{
			$category = categoryPeer::retrieveByPK($cat); //all sub categories and not the category itself
			if(!$category)
				continue;
			
			$categoryFullIdsToIds[] = $category->getFullIds() . '>';
		}

		return implode(",", $categoryFullIdsToIds);
	}
}

