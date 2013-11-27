<?php

/**
 * @package plugins.scheduledTask
 * @subpackage model.enum
  */
interface DeleteFlavorsLogicType extends BaseEnum
{
	/**
	 * Keep flavors in list and delete others
	 */
	const KEEP_LIST_DELETE_OTHERS = 1;

	/**
	 * Delete only flavors in list
	 */
	const DELETE_LIST = 2;

	/**
	 * Delete all apart from the smallest-file-sized flavor
	 */
	const DELETE_KEEP_SMALLEST = 3;

}