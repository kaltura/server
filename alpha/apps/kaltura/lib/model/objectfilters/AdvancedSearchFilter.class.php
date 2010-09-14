<?php

abstract class AdvancedSearchFilter extends AdvancedSearchFilterOperator
{
	abstract public function apply(baseObjectFilter $filter, Criteria &$criteria, array &$matchClause, array &$whereClause);
}
