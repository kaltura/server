<?php
class criteriaFilter
{
	private $criteria;
	private $enable = true;
	
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

			// copy all constraints from the criteria to $criteria_to_filter			
			$columns = $this->criteria->keys();
			
			foreach ( $columns as $column )
			{
				$filter_criterion = $this->criteria->getCriterion ( $column );
				$new_crit = $criteria_to_filter->getNewCriterion ( $filter_criterion->getTable() . "." . $filter_criterion->getColumn() ,  $filter_criterion->getValue() , $filter_criterion->getComparison() );
				$existing_criterion = $criteria_to_filter->getCriterion ( $column );

				// don't add duplicates !!
				if ( $existing_criterion && ( $existing_criterion->getValue() == $filter_criterion->getValue() &&  $existing_criterion->getComparison() == $filter_criterion->getComparison() ) ) 
					continue;
				
					// go one step deeper to copy the inner clauses
				$this->addClauses( $this->criteria , $filter_criterion , $new_crit );
				$criteria_to_filter->addAnd ( $new_crit );
			}
			

			// TODO - adda more robust way to copy the orderBy from this->criteria
			$orderBy = $this->criteria->getOrderByColumns();
			if ( $orderBy ) 
			{
				foreach ( $orderBy as $orderByColumn ) 
				{
					@list ( $name , $order ) = explode ( " " , $orderByColumn );
					if ( $order == Criteria::ASC )
						$criteria_to_filter->addAscendingOrderByColumn ( $name );
					else
						$criteria_to_filter->addDescendingOrderByColumn ( $name );
				}
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