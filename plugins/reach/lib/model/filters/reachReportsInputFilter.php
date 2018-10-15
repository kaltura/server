<?php

class reachReportsInputFilter extends reportsInputFilter
{
	public $serviceType;
	
	public $serviceFeature;

	public $turnAroundTime;
	
	protected static $serviceTypeMapping = array(
		1 => 'Human',
		2 => 'Machine'
	);
	
	protected static $serviceFeatureMapping = array(
		1 => 'Captions',
		2 => 'Translation',
	);
	
	protected static $turnaroundTimeMapping = array(
		-1     => 'Best effort',
		0      => 'Immediate',
		1800   => '30 minutes',
		7200   => '2 hours',
		10800  => '3 hours',
		21600  => '6 hours',
		28800  => '8 hours',
		43200  => '12 hours',
		86400  => '24 hours',
		172800 => '48 hours',
		864000 => '10 days',
	);
	
	public function addReportsDruidFilters($partner_id, $report_def, &$druid_filter)
	{
		$serviceType = $this->serviceType;
		if($serviceType && isset(self::$serviceTypeMapping[$serviceType]))
		{
			$druid_filter[] = array(
				kKavaReportsMgr::DRUID_DIMENSION => kKavaReportsMgr::DIMENSION_SERVICE_TYPE,
				kKavaReportsMgr::DRUID_VALUES => explode(',', $serviceType)
			);
		}
		
		$serviceFeature = $this->serviceFeature;
		if ($serviceFeature && isset(self::$serviceFeatureMapping[$serviceFeature]))
		{
			$druid_filter[] = array(
				kKavaReportsMgr::DRUID_DIMENSION => kKavaReportsMgr::DIMENSION_SERVICE_FEATURE,
				kKavaReportsMgr::DRUID_VALUES => explode(',', $serviceFeature)
			);
		}
		
		$turnAroundTime = $this->turnAroundTime;
		if ($turnAroundTime && isset(self::$turnaroundTimeMapping[$turnAroundTime]))
		{
			$druid_filter[] = array(
				kKavaReportsMgr::DRUID_DIMENSION => kKavaReportsMgr::DIMENSION_TURNAROUND_TIME,
				kKavaReportsMgr::DRUID_VALUES => explode(',', $turnAroundTime)
			);
		}
	}
	
	public function getCacheKey($object_ids)
	{
		$cacheKey = parent::getCacheKey($object_ids);
		$cacheKey .= $this->serviceType . $this->serviceFeature . $this->turnAroundTime;
		
		return $cacheKey;
	}
}
