<?php
/**
 * @package plugins.ask
 * @subpackage model.filters
 */
class AskEntryFilter extends entryFilter {

	public function attachToFinalCriteria(Criteria $c)
	{
		$c->addCondition(entryIndex::DYNAMIC_ATTRIBUTES . '.' .  AskPlugin::getDynamicAttributeName() . ' = 1' );
		return parent::attachToFinalCriteria($c);
	}

}