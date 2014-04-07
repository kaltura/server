<?php

class AggregatorConfiguration
{
	private $partnerId;
	private $adminSecret;
	private $serviceUrl;
	private $numPlaysScanPeriodInSeconds;
	private $entryContentTypeMetadataProfileId;
	private $entryContentTypeMetadataFieldName;
	private $entryContentTypeValueMovie;
	private $entryContentTypeValueSeries;
	private $entryContentTypeValueEpisode;
	private $statisticsMetadataProfileId;
	private $totalNumPlaysMetadataFieldName;
	private $numPlaysInScanPeriodMetadataFieldName;
	private $availableFromDateMetadataFieldName;
	private $seriesRootCategoryId;

	private $filterStartTime;
	private $filterEndTime;

	public function __construct(
							$partnerId,
							$userId,
							$adminSecret,
							$serviceUrl,
							$numPlaysScanPeriodInSeconds,
							$entryContentTypeMetadataProfileId,
							$entryContentTypeMetadataFieldName,
							$entryContentTypeValueMovie,
							$entryContentTypeValueSeries,
							$entryContentTypeValueEpisode,
							$statisticsMetadataProfileId,
							$totalNumPlaysMetadataFieldName,
							$numPlaysInScanPeriodMetadataFieldName,
							$availableFromDateMetadataFieldName,
							$seriesRootCategoryId
						)
	{
		$this->partnerId = $partnerId;
		$this->userId = $userId;
		$this->adminSecret = $adminSecret;
		$this->serviceUrl = $serviceUrl;

		$this->numPlaysScanPeriodInSeconds = $numPlaysScanPeriodInSeconds;

		$this->entryContentTypeMetadataProfileId = $entryContentTypeMetadataProfileId;
		$this->entryContentTypeMetadataFieldName = $entryContentTypeMetadataFieldName;
		$this->entryContentTypeValueMovie = $entryContentTypeValueMovie;
		$this->entryContentTypeValueSeries = $entryContentTypeValueSeries;
		$this->entryContentTypeValueEpisode = $entryContentTypeValueEpisode;

		$this->statisticsMetadataProfileId = $statisticsMetadataProfileId;
		$this->totalNumPlaysMetadataFieldName = $totalNumPlaysMetadataFieldName;
		$this->numPlaysInScanPeriodMetadataFieldName = $numPlaysInScanPeriodMetadataFieldName;
		$this->availableFromDateMetadataFieldName = $availableFromDateMetadataFieldName;

		$this->seriesRootCategoryId = $seriesRootCategoryId;

		$this->filterEndTime = time();
		$this->filterStartTime = $this->filterEndTime - $this->getNumPlaysScanPeriodInSeconds();
	}

	public function getPartnerId() { return $this->partnerId; }
	public function getUserId() { return $this->userId; }
	public function getAdminSecret() { return $this->adminSecret; }
	public function getServiceUrl() { return $this->serviceUrl; }

	public function getNumPlaysScanPeriodInSeconds() { return $this->numPlaysScanPeriodInSeconds; }

	public function getEntryContentTypeMetadataProfileId() { return $this->entryContentTypeMetadataProfileId; }
	public function getEntryContentTypeMetadataFieldName() { return $this->entryContentTypeMetadataFieldName; }
	public function getEntryContentTypeValueMovie() { return $this->entryContentTypeValueMovie; }
	public function getEntryContentTypeValueSeries() { return $this->entryContentTypeValueSeries; }
	public function getEntryContentTypeValueEpisode() { return $this->entryContentTypeValueEpisode; }

	public function getStatisticsMetadataProfileId() { return $this->statisticsMetadataProfileId; }
	public function getTotalNumPlaysMetadataFieldName() { return $this->totalNumPlaysMetadataFieldName; }
	public function getNumPlaysInScanPeriodMetadataFieldName() { return $this->numPlaysInScanPeriodMetadataFieldName; }
	public function getAvailableFromDateMetadataFieldName() { return $this->availableFromDateMetadataFieldName; }

	public function getSeriesRootCategoryId() { return $this->seriesRootCategoryId; }

	public function getFilterStartTime() { return $this->filterStartTime; }
	public function getFilterEndTime() { return $this->filterEndTime; }
}
