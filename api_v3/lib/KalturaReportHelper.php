<?php
/**
 * @package api
 * @subpackage v3
 */
class KalturaReportHelper
{
	public static function getValidateExecutionParameters(Report $report, KalturaKeyValueArray $params = null)
	{
		if (is_null($params))
			$params = new KalturaKeyValueArray();
			
		$execParams = array();
		$currentParams = $report->getParameters();
		foreach($currentParams as $currentParam)
		{
			$found = false;
			foreach($params as $param)
			{
				/* @var $param KalturaKeyValue */
				if ((strtolower($param->key) == strtolower($currentParam)))
				{
					$execParams[':'.$currentParam] = $param->value;
					$found = true;
				}
			}
			
			if (!$found)
				throw new KalturaAPIException(KalturaErrors::REPORT_PARAMETER_MISSING, $currentParam);
		}
		return $execParams;
	}
}
