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
	 * @var string
	 */
	protected $memberIdIn;
	
	/**
	 * @var string
	 */
	protected $memberPermissionsMultiLikeOr;
	
	/**
	 * @var string
	 */
	protected $memberPermissionsMultiLikeAnd;
	
	public function getMemberIdIn ()
	{
		return $this->memberIdIn;
	}
	
	public function setMemberIdIn ($v)
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
	
	public function getMemberPermissionsMultiLikeOr ()
	{
		return $this->memberPermissionsMultiLikeOr;
	}
	
	public function setMemberPermissionsMultiLikeOr ($v)
	{
		$this->memberPermissionsMultiLikeOr = $v;
	}
	
	public function getMemberPermissionsMultiLikeAnd ()
	{
		return $this->memberPermissionsMultiLikeAnd;
	}
	
	public function setMemberPermissionsMultiLikeAnd ($v)
	{
		$this->memberPermissionsMultiLikeAnd = $v;
	}
	
	/* (non-PHPdoc)
	 * @see AdvancedSearchFilterItem::apply()
	 */
	public function apply(baseObjectFilter $filter, IKalturaDbQuery $query)
	{
		if ($this->memberIdEq)
		{
			if ($this->memberPermissionsMultiLikeAnd)
			{
				$permissionsMultiLikeAndArr = explode(',', $this->memberPermissionsMultiLikeAnd);
				$memberPermissions = $this->memberIdEq.implode(' '.$this->memberIdEq, $permissionsMultiLikeAndArr);
				$query->addColumnWhere('category.MEMBERS', $memberPermissions, baseObjectFilter::MULTI_LIKE_AND);
			}
		}
	}
}