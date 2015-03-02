<?php

class SyndicationAllExporter extends LiveReportEntryExporter {

	public function __construct(KalturaLiveReportExportJobData $data) {
		parent::__construct($data, "referrers-@ENTRY_ID@-%s-%s.csv", LiveReportConstants::SECONDS_36_HOURS);
	}

	protected function getEngines() {
		return array_merge(
			$this->getHeadersEngines("Referrers"),
			$this->allEntriesEngines,
			array(new LiveReportReferrerEngine())
		);
	}
}
