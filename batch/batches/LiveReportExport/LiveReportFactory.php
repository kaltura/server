<?php

class LiveReportFactory {
	
	public function getExporter($type, KalturaLiveReportExportJobData $jobData) {
		
		$exporter = null;
		switch ($type) {
			case KalturaLiveReportExportType::PARTNER_TOTAL_ALL :
				$exporter = new PartnerTotalAllExporter($jobData);
				break;
			case KalturaLiveReportExportType::PARTNER_TOTAL_LIVE :
				$exporter = new PartnerTotalLiveExporter($jobData);
				break;
			case KalturaLiveReportExportType::ENTRY_TIME_LINE_ALL :
				$exporter = new EntryTimeLineAllExporter($jobData);
				break;
			case KalturaLiveReportExportType::ENTRY_TIME_LINE_LIVE :
				$exporter = new EntryTimeLineLiveExporter ($jobData);
				break;
			case KalturaLiveReportExportType::LOCATION_ALL :
				$exporter = new LocationAllExporter($jobData);
				break;
			case KalturaLiveReportExportType::LOCATION_LIVE :
				$exporter = new LocationLiveExporter($jobData);
				break;
			case KalturaLiveReportExportType::SYNDICATION_ALL :
				$exporter = new SyndicationAllExporter($jobData);
				break;
			case KalturaLiveReportExportType::SYNDICATION_LIVE :
				$exporter = new SyndicationLiveExporter($jobData);
				break;
			default:
				throw new KOperationEngineException("Unknown Exporter type : " . $type);
		}
		
		$exporter->init($jobData);
	}
}
