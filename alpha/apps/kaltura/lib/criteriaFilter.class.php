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
		return $this->criteria;
	}
	
	// TODO - FOR now assume the all columns of the criteria are glued using AND 
	// added a 1-level deep copy of criterions (addClauses)
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
			$filter_criterion = $fromCriteria->getCriterion ( $column );
			if ($filter_criterion instanceof KalturaCriterion && count($filter_criterion->getTags()))
			{
				$enableByTags = false;
		
				foreach ($filter_criterion->getTags() as $tag)
				{
					if(KalturaCriterion::isTagEnable($tag))
						$enableByTags = true;
				}
				
				if (!$enableByTags)
				{
					KalturaLog::debug("Skip criterion[" . $filter_criterion->getColumn() . "] comparison [ " . $filter_criterion->getComparison() . " ] with disabled tag [ " . print_r($filter_criterion->getTags(), true) . " ]");
					continue;
				}
			}
			
			$new_crit = $toCriteria->getNewCriterion ( $filter_criterion->getTable() . "." . $filter_criterion->getColumn() ,  $filter_criterion->getValue() , $filter_criterion->getComparison() );
			$existing_criterion = $toCriteria->getCriterion ( $column );

			// don't add duplicates !!
			if ( $existing_criterion && ( $existing_criterion->getValue() == $filter_criterion->getValue() &&  $existing_criterion->getComparison() == $filter_criterion->getComparison() ) ) 
				continue;
			
				// go one step deeper to copy the inner clauses
			$this->addClauses( $fromCriteria , $filter_criterion , $new_crit );
			$toCriteria->addAnd ( $new_crit );
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
	private function addClauses ( Criteria $criteria_to_filter , Criterion $filter_criterion , Criterion $crit  )
	{
		$conjunctions = $filter_criterion->getConjunctions();
		if ( count ( $conjunctions ) < 1 ) return;
		
		$clauses = $filter_criterion->getClauses();
		$i=0;
		foreach ( $clauses as $clause )
		{
			$new_crit = $criteria_to_filter->getNewCriterion ( $clause->getTable() . "." . $clause->getColumn() ,  $clause->getValue() , $clause->getComparison() );
			$conj = @$conjunctions[$i];
				
			if ( $conj == Criterion::UND ) $crit->addAnd( $new_crit );
			elseif ( $conj == Criterion::ODER ) $crit->addOr ( $new_crit );
			$i++;
		}
	}
	
}
?>