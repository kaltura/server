<?php
/**
 * @package plugins.sphinxSearch
 * @subpackage model.enum
 */
interface SphinxFieldEscapeType extends BaseEnum 
{
	const DEFAULT_ESCAPE  = '1';
	const STRIP  = '2';
}