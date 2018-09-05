<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.search
 */
abstract class kESearchBaseSuggestQuery
{
	const SUGGEST_KEY = 'suggest';

	abstract public function getFinalQuery();

}
