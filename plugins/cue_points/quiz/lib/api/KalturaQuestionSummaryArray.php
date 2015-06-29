<?php
/**
 * @package plugins.quiz
 * @subpackage api.objects
 */
class KalturaQuestionSummaryArray extends KalturaAssociativeArray
{
	public function __construct()
	{
		parent::__construct("float");
	}
}