<?php
/**
 * Will update storage script for free trial
 *
 * @package Scheduler
 * @subpackage StorageUpdate
 */
class KAsyncStorageUpdate extends KPeriodicWorker
{
	/*
	* @var KalturaSystemPartnerClientPlugin
	*/
	private $systemPartnerClientPlugin = null;

	private $packages = null;

	/**
	 * @param KSchedularTaskConfig $taskConfig
	 */
	public function __construct($taskConfig = null)
	{
		parent::__construct($taskConfig);
		$this->systemPartnerClientPlugin = KalturaSystemPartnerClientPlugin::get(self::$kClient);

		$packageConfig = simplexml_load_string(file_get_contents(dirname(__FILE__).'/../../../alpha/apps/kaltura/config/partnerPackages.xml'));
		$packageNodes = $packageConfig->xpath('/packages/package');
		foreach ($packageNodes as $package)
		{
			$arrPackage = KAsyncStorageUpdateUtils::flatXml2arr($package);
			$this->packages[$arrPackage['id']] = $arrPackage;
		}
	}

	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::STORAGE_UPDATE;
	}

	/* (non-PHPdoc)
	 * @see KBatchBase::run()
	*/
	public function run($jobs = null)
	{
		$partnersExists = true;
		$pageIndex = 1;
		$bulkSize = 500;
		$highestPartnerId = 103;
		while($partnersExists)
		{
			$partners = $this->getSystemPartnerList($highestPartnerId, $pageIndex, $bulkSize);
			if ($partners->objects)
			{
				KalturaLog::debug( 'Looping '. count($partners->objects) .' partners' );
				foreach($partners->objects as $partner)
				{
					$this->handlePartner($partner, KAsyncStorageUpdateUtils::PARTNER_PACKAGE_FREE);
				}

				$partner = end($partners->objects);
				if($partner)
				{
					$highestPartnerId = $partner->id;
				}
			}
			else
			{
				KalturaLog::debug( 'No more partners.' );
				$partnersExists = false;
			}
			unset($partners);
		}
		KalturaLog::debug('Done.');
	}

	public function handlePartner($partner, $partnerPackage)
	{
		if (KAsyncStorageUpdateUtils::isPartnerCreatedAsMonitoredFreeTrial($partner))
		{
			$this->doPartnerUsage($partner);
			$this->handleDayInFreeTrial($partner);
		}
		else if ($partnerPackage == KAsyncStorageUpdateUtils::PARTNER_PACKAGE_FREE)
		{
			$this->doPartnerUsage($partner);
		}
	}

	public function getSystemPartnerList($highestPartnerId, $pageIndex, $bulkSize)
	{
		$filter = new KalturaPartnerFilter();
		$filter->partnerPackageIn = KAsyncStorageUpdateUtils::PARTNER_PACKAGE_FREE .','. KAsyncStorageUpdateUtils::PARTNER_PACKAGE_INTERNAL_TRIAL;
		$filter->statusIn = KalturaPartnerStatus::ACTIVE .','. KalturaPartnerStatus::BLOCKED .','. KalturaPartnerStatus::FULL_BLOCK;
		$filter->idGreaterThan = $highestPartnerId;
		$filter->orderBy = '+id';

		$pager = new KalturaFilterPager();
		$pager->pageIndex = $pageIndex;
		$pager->pageSize = $bulkSize;

		return $this->systemPartnerClientPlugin->systemPartner->listAction($filter, $pager);
	}

	public function doPartnerUsage($partner)
	{
		KalturaLog::debug("Validating partner [" . $partner->id . "]");
		$systemPartnerConfiguration = new KalturaSystemPartnerConfiguration();
		if(isset($partner->extendedFreeTrail))
		{
			$systemPartnerConfiguration = $this->handleExtendedFreeTrail($partner, $systemPartnerConfiguration);
			if (!$systemPartnerConfiguration)
			{
				return;
			}
		}
		list($shouldBlockDeletePartner, $block_notification_grace, $delete_grace, $partnerPackage, $divisionFactor) = $this->getRelevantFields($partner);

		$monitoredFreeTrial = false;
		if(KAsyncStorageUpdateUtils::isPartnerCreatedAsMonitoredFreeTrial($partner))
		{
			$monitoredFreeTrial = true;
			if ($partner->partnerPackage == KAsyncStorageUpdateUtils::PARTNER_PACKAGE_DEVELOPER_TRIAL)
			{
				$divisionFactor = $partnerPackage['cycle_bw_for_monitored_trial'];
			}
		}
		$divisionFactor = ($divisionFactor != 0 ? $divisionFactor : 1);

		list($percent, $totalUsageGB) = self::getPercentFromStatistics($partner,$partnerPackage, $divisionFactor);
		$systemPartnerConfiguration->usagePercent = $percent;

		KalturaLog::debug("percent (".$partner->id.") is: $percent");
		$emailLinkHash = 'pid='.$partner->id.'&h='.(KAsyncStorageUpdateUtils::getEmailLinkHash($partner->id, $partner->secret));

		$mindtouchNotice = ' ';
		if($partner->type == KAsyncStorageUpdateUtils::DEKIWIKI) // dekiwiki-mindtouch partner
		{
			$mindtouchNotice = '<BR><BR>Note: You must be a MindTouch paying customer to upgrade your video service. If you are not a paying MindTouch customer, contact MindTouch: http://www.mindtouch.com/about_mindtouch/contact_mindtouch to get a quote.<BR><BR>';
		}

		$this->handleUsage($percent, $partner, $systemPartnerConfiguration, $monitoredFreeTrial, $partnerPackage, $mindtouchNotice, $totalUsageGB, $emailLinkHash, $shouldBlockDeletePartner, $block_notification_grace, $delete_grace);
	}

	public function getRelevantFields($partner)
	{
		$shouldBlockDeletePartner = true;
		$block_notification_grace = time() - (KAsyncStorageUpdateUtils::DAY * KAsyncStorageUpdateUtils::BLOCKING_DAYS_GRACE);
		$delete_grace = time() -  (KAsyncStorageUpdateUtils::DAY * 30);
		$partnerPackage = $this->packages[$partner->partnerPackage];
		$divisionFactor = $partnerPackage['cycle_bw'];
		return array($shouldBlockDeletePartner, $block_notification_grace, $delete_grace, $partnerPackage, $divisionFactor);
	}

	public static function getPercentFromStatistics($partner,$partnerPackage, $divisionFactor)
	{
		// We are now working with the DWH and a stored-procedure, and not with record type 6 on partner_activity.
		$reportDate = KAsyncStorageUpdateUtils::todayOffset(-1);
		list ( $totalStorage , $totalUsage , $totalTraffic ) = self::collectPartnerStatisticsFromDWH($partner, $partnerPackage, $reportDate);
		$totalUsageGB = $totalUsage/1024/1024; // from KB to GB
		$percent = round( ($totalUsageGB / $divisionFactor)*100, 2);
		return array($percent, $totalUsageGB);
	}

	public function handleUsage($percent, $partner, $systemPartnerConfiguration, $monitoredFreeTrial, $partnerPackage, $mindtouchNotice, $totalUsageGB, $emailLinkHash, $shouldBlockDeletePartner, $block_notification_grace, $delete_grace)
	{
		if ($percent >= 80 &&
			$percent < 100 &&
			(!isset($partner->eightyPercentWarning) || !$partner->eightyPercentWarning))
		{
			KalturaLog::debug("partner ". $partner->id ." reached 80% - setting first warning");

			/* prepare mail job, and set EightyPercentWarning() to true/date */
			$systemPartnerConfiguration->eightyPercentWarning = time();
			$systemPartnerConfiguration->usageLimitWarning = 0;
			if(!$monitoredFreeTrial)
			{
				$bodyParams = array($partner->adminName, $partnerPackage['cycle_bw'], $mindtouchNotice, round($totalUsageGB, 2), $emailLinkHash);
				$kMailType = new KalturaMailType();
				self::notifyPartner($kMailType::KALTURA_PACKAGE_EIGHTY_PERCENT_WARNING, $partner, $bodyParams);
			}
		}
		elseif ($percent >= 80 &&
			$percent < 100 &&
			isset($partner->eightyPercentWarning) && $partner->eightyPercentWarning &&
			(!isset($partner->usageLimitWarning) || !$partner->usageLimitWarning))
		{
			KalturaLog::log("passed the 80%, assume notification sent, nothing to do.");
		}
		elseif ($percent < 80 &&
			isset($partner->eightyPercentWarning) && $partner->eightyPercentWarning)
		{
			KalturaLog::debug("partner ". $partner->id ." was 80%, now not. clearing warnings");

			/* clear getEightyPercentWarning */
			$systemPartnerConfiguration->eightyPercentWarning = 0;
			$systemPartnerConfiguration->usageLimitWarning = 0;
		}
		elseif ($percent >= 100 &&
			(!isset($partner->usageLimitWarning) || !$partner->usageLimitWarning))
		{
			$systemPartnerConfiguration->usageLimitWarning = time();
			if(!$monitoredFreeTrial)
			{
				KalturaLog::debug("partner ". $partner->id ." reached 100% - setting second warning");
				$bodyParams = array ( $partner->adminName, $mindtouchNotice, round($totalUsageGB, 2), $emailLinkHash );
				$kMailType = new KalturaMailType();
				self::notifyPartner($kMailType::KALTURA_PACKAGE_LIMIT_WARNING_1, $partner, $bodyParams);
			}
			else
			{
				$reason = "partner ". $partner->id ." reached 100% - blocking partner";
				KalturaLog::debug($reason);
				if($shouldBlockDeletePartner)
				{
					$this->systemPartnerClientPlugin->systemPartner->updateStatus($partner->id, KAsyncStorageUpdateUtils::PARTNER_STATUS_CONTENT_BLOCK, $reason);
				}
			}
		}
		elseif ($percent >= 100 &&
			$partnerPackage['cycle_fee'] == 0 &&
			isset($partner->usageLimitWarning) && $partner->usageLimitWarning > 0 &&
			$partner->usageLimitWarning <= $block_notification_grace &&
			$partner->usageLimitWarning > $delete_grace &&
			$partner->status != KAsyncStorageUpdateUtils::PARTNER_STATUS_CONTENT_BLOCK)
		{
			$reason = "partner ". $partner->id ." reached 100% ".KAsyncStorageUpdateUtils::BLOCKING_DAYS_GRACE ." days ago - sending block email and blocking partner";
			KalturaLog::debug($reason);

			/* send block email and block partner */
			$bodyParams = array ( $partner->adminName, $mindtouchNotice, round($totalUsageGB, 2), $emailLinkHash );
			$kMailType = new KalturaMailType();
			self::notifyPartner($kMailType::KALTURA_PACKAGE_LIMIT_WARNING_2, $partner, $bodyParams);

			if($shouldBlockDeletePartner)
			{
				$this->systemPartnerClientPlugin->systemPartner->updateStatus($partner->id, KAsyncStorageUpdateUtils::PARTNER_STATUS_CONTENT_BLOCK, $reason);
			}
		}
		elseif ($percent >= 120 &&
			$partnerPackage['cycle_fee'] != 0 &&
			isset($partner->usageLimitWarning) && $partner->usageLimitWarning <= $block_notification_grace &&
			!$monitoredFreeTrial)
		{
			$bodyParams = array ( $partner->adminName, round($totalUsageGB, 2) );
			$kMailType = new KalturaMailType();
			self::notifyPartner($kMailType::KALTURA_PAID_PACKAGE_SUGGEST_UPGRADE, $partner, $bodyParams);
		}
		elseif ($percent >= 100 &&
			$partnerPackage['cycle_fee'] == 0 &&
			isset($partner->usageLimitWarning) && $partner->usageLimitWarning > 0 &&
			isset($partner->usageLimitWarning) && $partner->usageLimitWarning <= $delete_grace &&
			$partner->status == KAsyncStorageUpdateUtils::PARTNER_STATUS_CONTENT_BLOCK &&
			!$monitoredFreeTrial)
		{
			$reason = "partner ". $partner->id ." reached 100% a month ago - deleting partner";
			KalturaLog::debug($reason);

			/* delete partner */
			$bodyParams = array ( $partner->adminName );
			$kMailType = new KalturaMailType();
			self::notifyPartner($kMailType::KALTURA_DELETE_ACCOUNT, $partner, $bodyParams);

			if($shouldBlockDeletePartner)
			{
				$this->systemPartnerClientPlugin->systemPartner->updateStatus($partner->id, KAsyncStorageUpdateUtils::PARTNER_STATUS_DELETED, $reason);
			}
		}
		elseif($percent < 80 && ( (isset($partner->usageLimitWarning) && $partner->usageLimitWarning) || (isset($partner->eightyPercentWarning) && $partner->eightyPercentWarning)))
		{
			KalturaLog::debug("partner ". $partner->id ." OK");
			// PARTNER OK - resetting warnings
			$systemPartnerConfiguration->eightyPercentWarning =0;
			$systemPartnerConfiguration->usageLimitWarning = 0;
		}
		$this->systemPartnerClientPlugin->systemPartner->updateConfiguration($partner->id, $systemPartnerConfiguration);  //TODO - update without default criteria

	}

	public function handleExtendedFreeTrail($partner, $systemPartnerConfiguration)
	{
		KalturaLog::debug("Partner [" . $partner->id . "] trial account has extension");
		if(isset($partner->extendedFreeTrailExpiryDate) && $partner->extendedFreeTrailExpiryDate < time())
		{
			//ExtendedFreeTrail ended
			$systemPartnerConfiguration->extendedFreeTrail = null;
			$systemPartnerConfiguration->extendedFreeTrailExpiryDate = null;
			$systemPartnerConfiguration->extendedFreeTrailExpiryReason = '';
			return $systemPartnerConfiguration;
		}
		elseif (KAsyncStorageUpdateUtils::isPartnerCreatedAsMonitoredFreeTrial($partner))
		{
			KalturaLog::debug("Partner [" . $partner->id . "] trial account extended - monitored trial");
		}
		else
		{
			//ExtendedFreeTrail
			if ( isset($partner->extendedFreeTrailExpiryDate) && ($partner->extendedFreeTrailExpiryDate < (time() + (KAsyncStorageUpdateUtils::DAY * 7))) &&
				(!isset($partner->extendedFreeTrailEndsWarning) || !$partner->extendedFreeTrailEndsWarning))
			{
				$systemPartnerConfiguration->extendedFreeTrailEndsWarning = true;
				$this->systemPartnerClientPlugin->systemPartner->updateConfiguration($partner->id, $systemPartnerConfiguration);

				$emailLinkHash = 'pid='.$partner->id.'&h='.(KAsyncStorageUpdateUtils::getEmailLinkHash($partner->id, $partner->secret));
				$mailParmas = array($partner->adminName ,$emailLinkHash);
				$kMailType = new KalturaMailType();
				self::notifyPartner($kMailType::KALTURA_EXTENED_FREE_TRAIL_ENDS_WARNING, $partner, $mailParmas);
			}
			KalturaLog::debug("Partner [" . $partner->id . "] trial account extended");
		}
		return null;
	}


	/**
	 * The function checks for new free trial partners if its time to block/delete them and whether
	 * we need to sync their lead in Marketo
	 *
	 * @param $partner
	 */
	public function handleDayInFreeTrial($partner)
	{
		$partnerPackageInfo = $this->packages[$partner->partnerPackage];

		$endDay = $partnerPackageInfo['trial_num_days'];
		$deletionDay = $partnerPackageInfo['trial_num_days_until_deletion'];
		$formattedCreatedAt = date('Y-m-d H:i:s', $partner->createdAt);

		if(isset($partner->extendedFreeTrailExpiryDate) && $partner->extendedFreeTrailExpiryDate)
		{
			$formattedExtensionDate = date('Y-m-d H:i:s', $partner->extendedFreeTrailExpiryDate);
			$endDay = KAsyncStorageUpdateUtils::diffInDays($formattedCreatedAt, $formattedExtensionDate);
			$deletionDay = $endDay + 30;
			KalturaLog::debug("After trial extension the End day is: [$endDay]");
		}

		$freeTrialUpdatesDays = explode(',', $partnerPackageInfo['notification_days']);
		$dayInFreeTrial = KAsyncStorageUpdateUtils::diffInDays($formattedCreatedAt, KAsyncStorageUpdateUtils::today());
		KalturaLog::debug("partner [{$partner->id}] is currently at the [$dayInFreeTrial] day of free trial");

		// in case we want to delete/block partner that reached to specific day we wil disable this line
		//$partner = self::checkIfPartnerStatusChangeRequired($partner, $dayInFreeTrial, $endDay, $deletionDay);
		if($freeTrialUpdatesDays)
		{
			$systemPartnerConfiguration = self::checkForNotificationDay($partner, $dayInFreeTrial, $freeTrialUpdatesDays);
		}
		if ($systemPartnerConfiguration)
		{
			$this->systemPartnerClientPlugin->systemPartner->updateConfiguration($partner->id, $systemPartnerConfiguration);
		}
	}

	public static function checkForNotificationDay($partner, $dayInFreeTrial, $freeTrialUpdatesDays)
	{
		$closestUpdatesDay = KAsyncStorageUpdateUtils::getClosestDay($dayInFreeTrial, $freeTrialUpdatesDays);
		KalturaLog::debug('closest notification day comparing today [' . $closestUpdatesDay . ']');
		if (isset($partner->lastFreeTrialNotificationDay) && $closestUpdatesDay > $partner->lastFreeTrialNotificationDay)
		{
			KalturaLog::debug('Partner [' . $partner->id . '] reached to one of the Marketo lead sync days.');
			$systemPartnerConfiguration = new KalturaSystemPartnerConfiguration();
			$systemPartnerConfiguration->lastFreeTrialNotificationDay = $dayInFreeTrial;
			return $systemPartnerConfiguration;
		}
		return null;
	}

	public static function collectPartnerStatisticsFromDWH($partner, $partnerPackage, $reportDate)
	{
		// reset values:
		$totalStorage = 0;
		$totalTraffic = 0;
		$totalUsage = 0;

		$reportFilter = new reportsInputFilter();
		$reportFilter->from_day = str_replace('-','',$reportDate);

		$reportFilter->extra_map[KAsyncStorageUpdateUtils::IS_FREE_PACKAGE_PLACE_HOLDER] = "FALSE"; //TODO - change it, no extra map right now.
		if ($partnerPackage['id'] == 1) // free package
			$reportFilter->extra_map[KAsyncStorageUpdateUtils::IS_FREE_PACKAGE_PLACE_HOLDER] = "TRUE";

		$reportFilter = new KalturaReportInputFilter();
		$reportFilter->fromDay = str_replace('-','',$reportDate);

		$pager = new KalturaFilterPager();
		$pager->pageSize = 10000;
		$pager->pageIndex = 1;

		//TODO - change REPORT_TYPE_PARTNER_USAGE_DASHBOARD, to KalturaReportType
		$kalturaReportTable = self::$kClient->report->getTable(KAsyncStorageUpdateUtils::REPORT_TYPE_PARTNER_USAGE_DASHBOARD, $reportFilter, $pager, "");

		$header = $kalturaReportTable->header;
		$data = $kalturaReportTable->data;

		$avg_continuous_aggr_storage_mb_key = array_search('avg_continuous_aggr_storage_mb', $header);
		$sum_partner_bandwidth_kb_key = array_search('sum_partner_bandwidth_kb', $header);

		$relevant_row = count($data)-1;

		$totalStorage = $data[$relevant_row][$avg_continuous_aggr_storage_mb_key]; // MB
		$totalTraffic = $data[$relevant_row][$sum_partner_bandwidth_kb_key]; // KB
		$totalUsage = ($totalStorage*1024) + $totalTraffic; // (MB*1024 => KB) + KB

		return array( $totalStorage , $totalUsage , $totalTraffic );
	}

	public function notifyPartner($mailType, $partner, $bodyParams = array() )
	{
		$mailJobData = self::createMailJobData($mailType, $partner->adminEmail);
		$bodyParams[0] = $bodyParams[0].' (PartnerID: '. $partner->id .')';
		$paramsString = self::getParamsString($bodyParams, $mailJobData->separator);
		$mailJobData->bodyParams = $paramsString;
		self::$kClient->jobs->addMailJob($mailJobData);

		$mailJobData = self::createMailJobData($mailType, KAsyncStorageUpdateUtils::KALTURA_ACCOUNT_UPGRADES_NOTIFICATION_EMAIL);
		$bodyParams[0] = $bodyParams[0].' ('. $partner->id .')'." type:[{$partner->type}] partnerName:[{$partner->name}]";
		$paramsString = self::getParamsString($bodyParams, $mailJobData->separator);
		$mailJobData->bodyParams = $paramsString;
		//self::$kClient->jobs->addMailJob($mailJobData);
	}

	public function createMailJobData($mail_type, $recipientEmail)
	{
		$mailJobData = new KalturaMailJobData();
		$mailJobData->separator = '|';
		$mailJobData->mailType = $mail_type;
		$mailJobData->mailPriority = KAsyncStorageUpdateUtils::MAIL_PRIORITY_NORMAL;
		$mailJobData->fromEmail = kConf::get ("partner_notification_email" );
		$mailJobData->fromName = kConf::get ("partner_notification_name" );
		$mailJobData->recipientEmail = $recipientEmail;
		return $mailJobData;
	}

	public function getParamsString($bodyParams, $separator)
	{
		$paramsString = '';
		if ( is_array($bodyParams) )
		{
			foreach($bodyParams as $param )
			{
				$paramsString =  ( $paramsString ? $paramsString.$separator : '' ).$param;
			}
		}
		return $paramsString;
	}
}
