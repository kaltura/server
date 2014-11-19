<?php

class LiveReportFactory {
	
	public function getExporter($type, $jobData) {
		
		$exporter = null;
		switch ($type) {
			case KalturaLiveReportExportType::PARTNER_TOTAL_ALL :
				$exporter = new PartnerTotalAllExporter();
				break;
			case KalturaLiveReportExportType::PARTNER_TOTAL_LIVE :
				$exporter = new PartnerTotalLiveExporter();
				break;
			case KalturaLiveReportExportType::ENTRY_TIME_LINE_ALL :
				$exporter = new EntryTimeLineAllExporter();
				break;
			case KalturaLiveReportExportType::ENTRY_TIME_LINE_LIVE :
				$exporter = new EntryTimeLineLiveExporter ();
				break;
			case KalturaLiveReportExportType::LOCATION_ALL :
				$exporter = new LocationAllExporter();
				break;
			case KalturaLiveReportExportType::LOCATION_LIVE :
				$exporter = new LocationLiveExporter();
				break;
			case KalturaLiveReportExportType::SYNDICATION_ALL :
				$exporter = new SyndicationAllExporter();
				break;
			case KalturaLiveReportExportType::SYNDICATION_LIVE :
				$exporter = new SyndicationLiveExporter();
				break;
			default:
				throw new KOperationEngineException("Unknown Exporter type : " . $type);
		}
		
		$exporter->init($jobData);
	}
}
