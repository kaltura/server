<?php 

abstract class LiveReportQueryHelper {
	
	/**
	 * Executes a simple report query and returns the result as array of <key, value> according to the specified fields. 
	 * @param KalturaLiveReportType $reportType The type of the report
	 * @param KalturaLiveReportInputFilter $filter The input filter for the report
	 * @param KalturaFilterPager $pager The pager of the report
	 * @param string $keyField The name of the field that will be used as key
	 * @param string $valueField The name of the field that will be used as value
	 */
	public static function retrieveFromReport($reportType,
			KalturaLiveReportInputFilter $filter = null,
			KalturaFilterPager $pager = null,
			$keyField = null,
			$valueField) {
		
		$result = KBatchBase::$kClient->liveReports->getReport($reportType, $filter, $pager);
		if($result->totalCount == 0)
			return array();
		
		$res = array();
		foreach($result->objects as $object) {
			if($keyField)
				$res[$object->$keyField] = $object->$valueField;
			else
				$res[] = $object->$valueField;
		}
		return $res;
	}

	/**
	 * Executes a simple report query and returns the result as array of <key, value> according to the specified fields.
	 * @param KalturaLiveReportType $reportType The type of the report
	 * @param KalturaLiveReportInputFilter $filter The input filter for the report
	 * @param KalturaFilterPager $pager The pager of the report
	 * @param string $keyField The name of the field that will be used as key
	 * @param string $valueFields The name of the field that will be used as value
	 */
	public static function retrieveMultipleValuesFromReport($reportType,
	                                          KalturaLiveReportInputFilter $filter = null,
	                                          KalturaFilterPager $pager = null,
	                                          $keyField = null,
	                                          $valueFields) {

		$result = KBatchBase::$kClient->liveReports->getReport($reportType, $filter, $pager);
		if($result->totalCount == 0)
			return array();

		$res = array();
		foreach($result->objects as $object) {
			foreach($valueFields as $valueField) {
				if($keyField) {
					if (empty($res[$object->$keyField])) {
						$res[$object->$keyField] = array();
					}
					$res[$object->$keyField][$valueField] = $object->$valueField;
				}
				else {
					if (empty($res[$valueField])) {
						$res[$valueField] = array();
					}
					$res[$valueField][] = $object->$valueField;
				}
			}
		}
		return $res;
	}

	/**
	 * Executes a simple events query and returns the result as string according to the specified key.
	 * @param KalturaLiveReportType $reportType The type of the report
	 * @param KalturaLiveReportInputFilter $filter The input filter for the report
	 * @param KalturaFilterPager $pager The pager of the report
	 * @param string $keyField The name of the field that will be used as key
	 */
	public static function getEvents($reportType,
			KalturaLiveReportInputFilter $filter = null,
			KalturaFilterPager $pager = null,
			$keyField) {
		
		$results = KBatchBase::$kClient->liveReports->getEvents($reportType, $filter, $pager);
		foreach($results as $result) {
			if($result->id == $keyField) {
				return $result->data;
			}
		}
		return null;
	}
}