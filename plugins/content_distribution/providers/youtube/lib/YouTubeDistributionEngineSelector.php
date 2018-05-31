<?php
/**
 * @package plugins.youTubeDistribution
 * @subpackage lib
 */
class YouTubeDistributionEngineSelector extends DistributionEngine implements
	IDistributionEngineUpdate,
	IDistributionEngineSubmit,
	IDistributionEngineReport,
	IDistributionEngineDelete,
	IDistributionEngineCloseUpdate,
	IDistributionEngineCloseSubmit,
	IDistributionEngineCloseDelete
{
	/* (non-PHPdoc)
	 * @see IDistributionEngineSubmit::submit()
	 */
	public function submit(KalturaDistributionSubmitJobData $data)
	{
		$engine = $this->getEngineByProfile($data);
		return $engine->submit($data);
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseSubmit::closeSubmit()
	 */
	public function closeSubmit(KalturaDistributionSubmitJobData $data)
	{
		$engine = $this->getEngineByProfile($data);
		return $engine->closeSubmit($data);
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineDelete::delete()
	 */
	public function delete(KalturaDistributionDeleteJobData $data)
	{
		$engine = $this->getEngineByProfile($data);
		return $engine->delete($data);
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseDelete::closeDelete()
	 */
	public function closeDelete(KalturaDistributionDeleteJobData $data)
	{
		$engine = $this->getEngineByProfile($data);
		return $engine->closeDelete($data);
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineUpdate::update()
	 */
	public function update(KalturaDistributionUpdateJobData $data)
	{
		$engine = $this->getEngineByProfile($data);
		return $engine->update($data);
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineCloseUpdate::closeUpdate()
	 */
	public function closeUpdate(KalturaDistributionUpdateJobData $data)
	{
		$engine = $this->getEngineByProfile($data);
		return $engine->closeUpdate($data);
	}

	/* (non-PHPdoc)
	 * @see IDistributionEngineReport::fetchReport()
	 */
	public function fetchReport(KalturaDistributionFetchReportJobData $data)
	{
		$engine = $this->getEngineByProfile($data);
		return $engine->fetchReport($data);
	}

	protected function getEngineByProfile(KalturaDistributionJobData $data)
	{
		if (!$data->distributionProfile instanceof KalturaYouTubeDistributionProfile)
			throw new Exception('Distribution profile is not of type KalturaYouTubeDistributionProfile for entry distribution #'.$data->entryDistributionId);

		switch ( $data->distributionProfile->feedSpecVersion )
		{
			case KalturaYouTubeDistributionFeedSpecVersion::VERSION_1:
			{
				$engine = new YouTubeDistributionLegacyEngine();
				break;
			}
			case KalturaYouTubeDistributionFeedSpecVersion::VERSION_2:
			{
				$engine = new YouTubeDistributionRightsFeedEngine();
				break;
			}
			case KalturaYouTubeDistributionFeedSpecVersion::VERSION_3:
			{
				$engine = new YouTubeDistributionCsvEngine();
				break;
			}
			default:
				throw new Exception('Distribution profile feedSpecVersion does not match existing versions');
		}

		if (KBatchBase::$taskConfig)
			$engine->configure();
		$engine->setClient();

		return $engine;
	}
}