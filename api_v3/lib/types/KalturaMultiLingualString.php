<?php
/**
 * A Multi Lingual String
 *
 * @package api
 * @subpackage objects
 */
class KalturaMultiLingualString extends KalturaObject
{
	/**
	 * The language of the value
	 *
	 * @var string
	 */
	public $language;
	
	/**
	 * Value
	 *
	 * @var string
	 */
	public $value;
}