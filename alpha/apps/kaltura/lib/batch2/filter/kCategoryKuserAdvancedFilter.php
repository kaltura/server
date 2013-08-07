<?php
/**
 * @package api
 * @subpackage filters
 */
class kCategoryKuserAdvancedFilter extends AdvancedSearchFilterItem
{
	/**
	 * @var string
	 */
	protected $memberIdEq;
	
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
		if ($this->memberIdEq)
		{
			if ($this->memberPermissionsMatchAnd)
			{
				$permissionsMultiLikeAndArr = explode(',', $this->memberPermissionsMatchAnd);
				foreach($permissionsMultiLikeAndArr as &$permissionName)
				{
					$permissionName = $this->memberIdEq.str_replace('_', '', $permissionName);
				}
				$query->addColumnWhere('category.MEMBERS', $permissionsMultiLikeAndArr, baseObjectFilter::MATCH_AND);
			}
			elseif ($this->memberPermissionsMatchOr)
			{
				$permissionsMultiLikeOrArr = explode(',', $this->memberPermissionsMatchOr);
				foreach($permissionsMultiLikeOrArr as &$permissionName)
				{
					$permissionName = $this->memberIdEq.str_replace('_', '', $permissionName);
				}
				$query->addColumnWhere('category.MEMBERS', $permissionsMultiLikeOrArr, baseObjectFilter::MATCH_OR);
			}
		}
		elseif ($this->memberIdIn)
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
				
				$criterion = $query->getNewCriterion('category.MEMBERS', $memberPermissionsArr, $this->memberPermissionsMatchAnd ? baseObjectFilter::MATCH_OR : baseObjectFilter::MATCH_AND);
				$query->addOr($criterion);
			}
			
		} 
		
	}
}