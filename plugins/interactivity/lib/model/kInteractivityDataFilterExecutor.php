<?php
/**
 * @package plugins.interactivity
 * @subpackage model
 */

class kInteractivityDataFilterExecutor extends BaseObject
{
	/**
	 * @param string $data
	 * @param array $filtersArray
	 * @return string
	 */
	public function filterData($data, $filtersArray)
	{
		$json = json_decode($data, true);

		return json_encode($json);
	}
}
