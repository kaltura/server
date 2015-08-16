<?php
/**
 * @package Core
 * @subpackage model.filters.advanced
 */ 
class AdvancedSearchFilterMatchAttributeCondition extends AdvancedSearchFilterMatchCondition
{
	/**
	 * Adds conditions, matches and where clauses to the query
	 * @param IKalturaDbQuery $query
	 */
	public function applyCondition(IKalturaDbQuery $query)
	{
		if (!$query instanceof IKalturaIndexQuery)
			return;

		$matchText = '"'.SphinxUtils::escapeString($this->value).'"';
		if ($this->not)
			$matchText = '!'.$matchText;
		$query->addMatch("@$this->field (".$matchText.")");
	}
}
