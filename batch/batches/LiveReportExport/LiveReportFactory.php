<?php

class LiveReportFactory {
	
	public function getExporter($partnerId, $type, KalturaLiveReportExportJobData $jobData) {
		
		$exporter = null;
		switch ($type) {
			case KalturaLiveReportExportType::PARTNER_TOTAL_ALL :
				$exporter = new PartnerTotalAllExporter($partnerId, $jobData);
				break;
			case KalturaLiveReportExportType::PARTNER_TOTAL_LIVE :
				$exporter = new PartnerTotalLiveExporter($partnerId, $jobData);
				break;
			case KalturaLiveReportExportType::ENTRY_TIME_LINE_ALL :
				$exporter = new EntryTimeLineAllExporter($partnerId, $jobData);
				break;
			case KalturaLiveReportExportType::ENTRY_TIME_LINE_LIVE :
				$exporter = new EntryTimeLineLiveExporter ($partnerId, $jobData);
				break;
			case KalturaLiveReportExportType::LOCATION_ALL :
				$exporter = new LocationAllExporter($partnerId, $jobData);
				break;
			case KalturaLiveReportExportType::LOCATION_LIVE :
				$exporter = new LocationLiveExporter($partnerId, $jobData);
				break;
			case KalturaLiveReportExportType::SYNDICATION_ALL :
				$exporter = new SyndicationAllExporter($partnerId, $jobData);
				break;
			case KalturaLiveReportExportType::SYNDICATION_LIVE :
				$exporter = new SyndicationLiveExporter($partnerId, $jobData);
				break;
			default:
				throw new KOperationEngineException("Unknown Exporter type : " . $type);
		}
		
		$exporter->init($jobData);
		
		return $exporter;
	}
}
