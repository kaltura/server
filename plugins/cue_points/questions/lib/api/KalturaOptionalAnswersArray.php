<?php
/**
 * @package api
 * @subpackage objects
 *
 */

class KalturaOptionalAnswersArray extends KalturaAssociativeArray {

	public function __construct()
	{
		return parent::__construct("KalturaOptionalAnswer");
	}
}