<?php
/**
 * @package api
 * @subpackage filters
 */
class kCategoryKuserAdvancedFilter extends AdvancedSearchFilterItem
{
	/**
	 * @var array
	 */
	protected $memberIdIn;
	
	/**
	 * @var string
	 */
	protected $memberPermissionsMatchOr;
	
	/**
	 * @var string
	 */
	protected $memberPermissionsMatchAnd;
	
	public function getMemberIdIn ()
	{
		return $this->memberIdIn;
	}
	
	public function setMemberIdIn (array $v)
	{
		$this->memberIdIn = $v;
	}
	
	public function getMemberIdEq ()
	{
		return $this->memberIdEq;
	}
	
	public function setMemberIdEq ($v)
	{
		$this->memberIdEq = $v;
	}
	
	public function getMemberPermissionsMatchOr ()
	{
		return $this->memberPermissionsMatchOr;
	}
	
	public function setMemberPermissionsMatchOr ($v)
	{
		$this->memberPermissionsMatchOr = $v;
	}
	
	public function getMemberPermissionsMatchAnd ()
	{
		return $this->memberPermissionsMatchAnd;
	}
	
	public function setMemberPermissionsMatchAnd ($v)
	{
		$this->memberPermissionsMatchAnd = $v;
	}
	
	/* (non-PHPdoc)
	 * @see AdvancedSearchFilterItem::apply()
	 */
	public function apply(baseObjectFilter $filter, IKalturaDbQuery $query)
	{
		if ($this->memberIdIn)
		{
			foreach($this->memberIdIn as $memberId)
			{
				$memberPermissionsArr = array();
				if ($this->memberPermissionsMatchAnd)
				{
					$permissionsMultiLikeAndArr = explode(',', $this->memberPermissionsMatchAnd);
					foreach($permissionsMultiLikeAndArr as &$permissionName)
					{
						$memberPermissionsArr[] = $memberId.str_replace('_', '', $permissionName);
					}
				}
				elseif ($this->memberPermissionsMatchOr)
				{
					$permissionsMultiLikeOrArr = explode(',', $this->memberPermissionsMatchOr);
					foreach($permissionsMultiLikeOrArr as &$permissionName)
					{
						$memberPermissionsArr[] = $memberId.str_replace('_', '', $permissionName);
					}
				}
				
				$criterion = $query->getNewCriterion('category.MEMBERS', $memberPermissionsArr, $this->memberPermissionsMatchAnd ? baseObjectFilter::MATCH_AND : baseObjectFilter::MATCH_OR);
				$query->addOr($criterion);
			}
			
		} 
		
	}
}