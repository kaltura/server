<?php
/**
 * @package plugins.thumbnail
 * @subpackage model
 */

class kThumbAdapterParameters
{
	const UNSET_PARAMETER_ZERO_BASED = 0;
	const UNSET_PARAMETER = -1;

	protected $params = array();

	/**
	 * @param string $fieldName kThumbFactoryFieldName
	 * @return mixed
	 */
	function get($fieldName)
	{
		return isset($this->params[$fieldName]) ? $this->params[$fieldName] : null;
	}

	/**
	 * @param string $fieldName kThumbFactoryFieldName
	 * @param mixed $value
	 */
	function set($fieldName, $value)
	{
		$this->params[$fieldName] = $value;
	}
}