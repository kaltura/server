<?php

class SyndicationLiveExporter extends LiveReportEntryExporter {

	public function __construct(KalturaLiveReportExportJobData $data) {
		parent::__construct($data, "referrers-live-now-@ENTRY_ID@-%s-%s.csv", LiveReportConstants::SECONDS_36_HOURS);
		$this->params[LiveReportConstants::IS_LIVE] = true;
	}

	protected function getEngines() {
		return array_merge(
			$this->getHeadersEngines("Referrers"),
			$this->liveEntriesEngines,
			array(new LiveReportReferrerEngine())
		);
	}
}
