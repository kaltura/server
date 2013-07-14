<?php
/**
 * @package Core
 * @subpackage model.filters
 */
class criteriaFilter
{
	/**
	 * @var Criteria
	 */
	private $criteria;
	private $enable = true;

	public function isEnabled()
	{
		return $this->enable;
	}
	
	public function enable()
	{
		$this->enable = true;
	}
	
	public function disable ()
	{
		$this->enable = false;
	}
	
	public function setFilter ( Criteria $c )
	{
		$this->criteria = $c;
	}
	
	/**
	 * @return Criteria
	 */
	public function & getFilter ()
	{
		if(!$this->criteria)
			$this->criteria = new Criteria();
			
		return $this->criteria;
	}
	
	public function applyFilter ( Criteria $criteria_to_filter )
	{
		if ( ! $this->enable ) return;

		if ( ! isset ( $criteria_to_filter->creteria_filter_attached ) )
		{
			$criteria_to_filter->creteria_filter_attached = true;
			$this->copyCriteriaConstraints($this->criteria, $criteria_to_filter);
		}
	}
	
	/**
	 * copy all constraints from the criteria to $criteria_to_filter
	 *
	 */
	private function copyCriteriaConstraints($fromCriteria, $toCriteria)
	{
		$columns = $fromCriteria->keys();
		
		foreach ( $columns as $column )
		{
			$filterCriterion = $fromCriteria->getCriterion ( $column );
			if ($filterCriterion instanceof KalturaCriterion && !$filterCriterion->isEnabled())
			{
				KalturaLog::debug("Skip criterion[" . $filterCriterion->getColumn() . "] comparison [ " . $filterCriterion->getComparison() . " ] with disabled tag [ " . print_r($filterCriterion->getTags(), true) . " ]");
				continue;
			}
			
			$newCriterion = $toCriteria->getNewCriterion ( $filterCriterion->getTable() . "." . $filterCriterion->getColumn() ,  $filterCriterion->getValue() , $filterCriterion->getComparison() );
			$existingCriterion = $toCriteria->getCriterion ( $column );

			// don't add duplicates !!
			if ( $existingCriterion && ( $existingCriterion->getValue() == $filterCriterion->getValue() &&  $existingCriterion->getComparison() == $filterCriterion->getComparison() ) )
				continue;
			
				// go one step deeper to copy the inner clauses
			$this->addClauses( $fromCriteria , $filterCriterion , $newCriterion );
			$toCriteria->addAnd ( $newCriterion );
		}
		

		// TODO - adda more robust way to copy the orderBy from this->criteria
		$orderBy = $fromCriteria->getOrderByColumns();
		if ( $orderBy )
		{
			foreach ( $orderBy as $orderByColumn )
			{
				@list ( $name , $order ) = explode ( " " , $orderByColumn );
				if ( $order == Criteria::ASC )
					$toCriteria->addAscendingOrderByColumn ( $name );
				else
					$toCriteria->addDescendingOrderByColumn ( $name );
			}
		}
	}
	
	/**
	 * add inner criteria for criterions
	 * ----------------- IMPORTANT -----------------
	 *  for this to work - we have to change the access modifier of the Creterion::getClauses() function from private to public
	 * It's in the Criteria.php file under
	 * 	/symfony/vendor/propel/util/Criteria.php
	 */
	private function addClauses ( Criteria $criteriaToFilter , Criterion $filterCriterion , Criterion $criterion  )
	{
		$conjunctions = $filterCriterion->getConjunctions();
		if ( count ( $conjunctions ) < 1 ) return;
		
		$clauses = $filterCriterion->getClauses();
		$i=0;
		foreach ( $clauses as $clause )
		{
			if($clause instanceof KalturaCriterion && !$clause->isEnabled())
				continue;
				
			/* @var $clause Criterion */
			
			$newCriterion = $criteriaToFilter->getNewCriterion ( $clause->getTable() . "." . $clause->getColumn() ,  $clause->getValue() , $clause->getComparison() );
			
			$this->addClauses( $criteriaToFilter , $clause , $newCriterion );
			
			$conj = @$conjunctions[$i];
				
			if ( $conj == Criterion::UND ) $criterion->addAnd( $newCriterion );
			elseif ( $conj == Criterion::ODER ) $criterion->addOr ( $newCriterion );
			$i++;
		}
	}
	
}
