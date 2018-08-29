<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.filters
 */
abstract class ESearchBaseFilter extends BaseObject
{

	protected $query;

	abstract protected function applyFilter();

}
