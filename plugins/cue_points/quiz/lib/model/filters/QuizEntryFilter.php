<?php
/**
 * @package plugins.quiz
 * @subpackage model.filters
 */
class QuizEntryFilter extends entryFilter {

	public function attachToFinalCriteria(Criteria $c)
	{
		$c->addCondition(entryIndex::DYNAMIC_ATTRIBUTES . '.' .  QuizPlugin::getDynamicAttributeName() . ' = 1' );
		return parent::attachToFinalCriteria($c);
	}

}