<?php

require_once __DIR__ . '/AggregatorConfiguration.php';

class AggregatorConfigurationEnumerator
{

    /*
     * array with the following keys:
     * host, user, pass, db
     */
    protected $_params;

	public function __construct($params)
	{
		$this->_params = $params;
	}

    protected function _getPartnerIds()
    {
        $partner_ids = array();
        $conn = mysqli_connect($this->_params['host'], $this->_params['user'], $this->_params['pass'], $this->_params['db']);
        $res = mysqli_query($conn, "select partner_id from site, config where site.config_id=config.id and config.profile='mediago'");
        while ($row = mysqli_fetch_row($res)) {
            $partner_ids[$row[0]] = true;
        }
        mysqli_close($conn);
        return array_keys($partner_ids);
    }

    protected function _getPartnerDatas($partnerId)
    {
        $ans = array();
        $conn = mysqli_connect($this->_params['host'], $this->_params['user'], $this->_params['pass'], $this->_params['db']);
        $res = mysqli_query($conn, "
            select config.config, config.modules from site, config
            where site.config_id=config.id
            and config.profile='mediago' and site.partner_id='{$partnerId}'
            group by config.config, config.modules
        ");
        while ($row = mysqli_fetch_row($res)) {
            $ans[] = array($row[0], $row[1]);
        }
        mysqli_close($conn);
        return $ans;
    }

    protected function _getConfiguration($partnerId, $config, $modules)
    {
        $userId = "AggregatorBatchUser";
        $adminSecret = $config->client->adminSecret;
        $serviceUrl = $config->client->serviceUrl;

        //                            seconds * minutes * hours * days
        $numPlaysScanPeriodInSeconds =   60   *    60   *   24  *  7;

        $entryContentTypeMetadataProfileId = $modules->mediago->Common_Entry_Metadata_Id;
        $entryContentTypeMetadataFieldName = 'EntryContentType';

        $entryContentTypeValueMovie = 'Movie';
        $entryContentTypeValueSeries = 'Series';
        $entryContentTypeValueEpisode = 'Episode';

        $statisticsMetadataProfileId = $config->application->mediagoEntryAnalyticsProfileId;
        $totalNumPlaysMetadataFieldName = 'ViewsEver';
        $numPlaysInScanPeriodMetadataFieldName = 'ViewsLastSevenDays';
        $availableFromDateMetadataFieldName = 'SeriesLatestEpisodeDate';

        $tmp = $config->categories->ContentTreeSeriesTopCategory;
        $tmp = explode('::', $tmp);
        $tmp = explode('>', $tmp[0]);
        $seriesRootCategoryId = $tmp[count($tmp)-1];

        return new AggregatorConfiguration(
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
        );
    }

    public function getConfigurations()
    {
        $configs = array();
        foreach ($this->_getPartnerIds() as $partnerId) {
            foreach ($this->_getPartnerDatas($partnerId) as $partnerData) {
                list($configBlob, $modulesBlob) = $partnerData;
                $config = unserialize($configBlob);
                $modules = unserialize($modulesBlob);
                $configs[] = $this->_getConfiguration($partnerId, $config, $modules);
            }
        }
        return $configs;
    }
}