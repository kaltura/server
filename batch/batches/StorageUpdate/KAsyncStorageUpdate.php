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

	private $localMap = null;

	private $debugMode = null;

	/**
	 * @param KSchedularTaskConfig $taskConfig
	 */
	public function __construct($taskConfig = null)
	{
		parent::__construct($taskConfig);
		$this->systemPartnerClientPlugin = KalturaSystemPartnerClientPlugin::get(self::$kClient);

		$this->packages = array();
		$packageConfig = simplexml_load_string(file_get_contents(dirname(__FILE__).'/../../../alpha/apps/kaltura/config/partnerPackages.xml'));
		$packageNodes = $packageConfig->xpath('/packages/package');
		foreach ($packageNodes as $package)
		{
			$arrPackage = KAsyncStorageUpdateUtils::flatXml2arr($package);
			$this->packages[$arrPackage['id']] = $arrPackage;
		}

		$this->debugMode = $this->getAdditionalParams('debugMode');
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
		$this->initMap();
		$maxPartner = $this->getAdditionalParams('maxPartner');
		$minPartner = $this->getAdditionalParams('minPartner');
		$lowPartnerWaterMark = $minPartner ? $minPartner : KAsyncStorageUpdateUtils::LOWEST_PARTNER;
		$maxPartnerReached = false;
		do
		{
			$partners = $this->getSystemPartnerList($lowPartnerWaterMark, KAsyncStorageUpdateUtils::PAGE_INDEX, KAsyncStorageUpdateUtils::PAGE_SIZE);
			$countPartners = isset($partners->objects) ? count($partners->objects) : 0;
			if ($countPartners)
			{
				KalturaLog::debug( 'Looping '. count($partners->objects) .' partners' );
				foreach($partners->objects as $partner)
				{
					if (!$maxPartner || ($partner->id <= $maxPartner))
					{
						$this->handlePartner($partner, KAsyncStorageUpdateUtils::PARTNER_PACKAGE_FREE);
						unset($partner);
					}
					else
					{
						$maxPartnerReached = true;
						KalturaLog::debug( 'Finished handling partners: ' .$lowPartnerWaterMark .' < partner_id <= ' . $maxPartner);
						break;
					}
				}

				$partner = end($partners->objects);
				if($partner)
				{
					$lowPartnerWaterMark = $partner->id;
				}
			}
			unset($partners);
			if(function_exists('gc_collect_cycles'))
			{
				gc_collect_cycles();
			}
		} while ($countPartners && !$maxPartnerReached);

		KalturaLog::debug('Done.');
	}

	protected function initMap()
	{
		$configurationPluginClient = KalturaConfMapsClientPlugin::get(self::$kClient);
		$configurationMapFilter = new KalturaConfMapsFilter();
		$configurationMapFilter->nameEqual = KAsyncStorageUpdateUtils::LOCAL;
		$configurationMapFilter->relatedHostEqual = self::$taskConfig->getSchedulerName();
		$configurationMap = $configurationPluginClient->confMaps->get($configurationMapFilter);
		if ($configurationMap)
		{
			$this->localMap = json_decode($configurationMap->content, true);
		}
	}

	public function handlePartner($partner, $partnerPackage)
	{
		if (self::isPartnerCreatedAsMonitoredFreeTrial($partner))
		{
			$this->doPartnerUsage($partner);
			$this->handleDayInFreeTrial($partner);
		}
		else if ($partnerPackage == KAsyncStorageUpdateUtils::PARTNER_PACKAGE_FREE)
		{
			$this->doPartnerUsage($partner);
		}
	}

	public function getSystemPartnerList($lowPartnerWaterMark, $pageIndex, $bulkSize)
	{
		$filter = new KalturaPartnerFilter();
		$filter->partnerPackageIn = KAsyncStorageUpdateUtils::PARTNER_PACKAGE_FREE .','. KAsyncStorageUpdateUtils::PARTNER_PACKAGE_INTERNAL_TRIAL;
		$filter->statusIn = KalturaPartnerStatus::ACTIVE .','. KalturaPartnerStatus::BLOCKED .','. KalturaPartnerStatus::FULL_BLOCK;
		$filter->idGreaterThan = $lowPartnerWaterMark;
		$filter->monitorUsageEqual = 1;
		$filter->orderBy = '+id';

		$pager = new KalturaFilterPager();
		$pager->pageIndex = $pageIndex;
		$pager->pageSize = $bulkSize;

		try
		{
			return $this->systemPartnerClientPlugin->systemPartner->listAction($filter, $pager);
		}
		catch(Exception $e)
		{
			KalturaLog::err("Could not get partner higher than : " . $lowPartnerWaterMark.' '. $e->getMessage());
			return null;
		}
	}

	public function doPartnerUsage($partner)
	{
		KalturaLog::debug('Validating partner [' . $partner->id . ']');
		$systemPartnerConfiguration = new KalturaSystemPartnerConfiguration();
		if($partner->extendedFreeTrail)
		{
			$systemPartnerConfiguration = $this->handleExtendedFreeTrail($partner, $systemPartnerConfiguration);
			if (!$systemPartnerConfiguration)
			{
				return;
			}
		}
		list($blockNotificationGrace, $deleteGrace, $partnerPackage, $divisionFactor) = $this->getRelevantFields($partner);

		$monitoredFreeTrial = false;
		if(self::isPartnerCreatedAsMonitoredFreeTrial($partner))
		{
			$monitoredFreeTrial = true;
			if ($partner->partnerPackage == KAsyncStorageUpdateUtils::PARTNER_PACKAGE_DEVELOPER_TRIAL)
			{
				$divisionFactor = $partnerPackage['cycle_bw_for_monitored_trial'];
			}
		}

		list($percent, $totalUsageGB) = self::getPercentFromStatistics($partner, $divisionFactor);
		if($percent == null && $totalUsageGB == null)
		{
			return;
		}
		$systemPartnerConfiguration->usagePercent = $percent;

		KalturaLog::debug('percent ('.$partner->id.') is: '.$percent);
		$emailLinkHash = 'pid='.$partner->id.'&h='.(self::getEmailLinkHash($partner->id, $partner->secret));

		$mindtouchNotice = ' ';
		if($partner->type == KAsyncStorageUpdateUtils::DEKIWIKI) // dekiwiki-mindtouch partner
		{
			$mindtouchNotice = '<BR><BR>Note: You must be a MindTouch paying customer to upgrade your video service. If you are not a paying MindTouch customer, contact MindTouch: http://www.mindtouch.com/about_mindtouch/contact_mindtouch to get a quote.<BR><BR>';
		}

		$this->handleUsage($percent, $partner, $systemPartnerConfiguration, $monitoredFreeTrial, $partnerPackage, $mindtouchNotice, $totalUsageGB, $emailLinkHash, $blockNotificationGrace, $deleteGrace);
	}

	public function getRelevantFields($partner)
	{
		$blockNotificationGrace = time() - (KAsyncStorageUpdateUtils::DAY * KAsyncStorageUpdateUtils::BLOCKING_DAYS_GRACE);
		$deleteGrace = time() -  (KAsyncStorageUpdateUtils::DAY * 30);
		$partnerPackage = $this->packages[$partner->partnerPackage];
		$divisionFactor = $partnerPackage['cycle_bw'];
		return array($blockNotificationGrace, $deleteGrace, $partnerPackage, $divisionFactor);
	}

	public function getPercentFromStatistics($partner, $divisionFactor)
	{
		$divisionFactor = ($divisionFactor != 0 ? $divisionFactor : 1);
		try
		{
			KBatchBase::impersonate($partner->id);
			$partnerStatistics = self::$kClient->partner->getStatistics();
			KBatchBase::unimpersonate();
		}
		catch(KalturaException $kex)
		{
			KBatchBase::unimpersonate();
			KalturaLog::debug('Moving to next partner. Failed to get partner statistics on pid: ' . $partner->id . 'Error: '. $kex->getMessage());
			return array(null, null);
		}
		catch(KalturaClientException $kcex)
		{
			KBatchBase::unimpersonate();
			KalturaLog::debug('Moving to next partner. Failed to get partner statistics on pid: ' . $partner->id . 'Error: '. $kcex->getMessage());
			return array(null, null);
		}

		$totalUsageGB = $partnerStatistics->usage;
		$percent = round( ($totalUsageGB / $divisionFactor)*100, 2);
		return array($percent, $totalUsageGB);
	}

	public function handleUsage($percent, $partner, $systemPartnerConfiguration, $monitoredFreeTrial, $partnerPackage, $mindtouchNotice, $totalUsageGB, $emailLinkHash, $blockNotificationGrace, $deleteGrace)
	{
		if ($percent < KAsyncStorageUpdateUtils::WATERMARK_LOW)
		{
			if ($partner->eightyPercentWarning || $partner->usageLimitWarning)
			{
				KalturaLog::debug('partner '. $partner->id .' was above ' .KAsyncStorageUpdateUtils::WATERMARK_LOW. '%, now it is below. clearing warnings');
				$systemPartnerConfiguration->eightyPercentWarning = 0;
				$systemPartnerConfiguration->usageLimitWarning = 0;
			}
		}
		elseif ($percent >= KAsyncStorageUpdateUtils::WATERMARK_LOW && $percent < KAsyncStorageUpdateUtils::WATERMARK_HIGH)
		{
			if (!$partner->eightyPercentWarning)
			{
				KalturaLog::debug('partner '. $partner->id .' reached ' .KAsyncStorageUpdateUtils::WATERMARK_LOW. '% - setting first warning');

				/* prepare mail job, and set EightyPercentWarning() to true/date */
				$systemPartnerConfiguration->eightyPercentWarning = time();
				$systemPartnerConfiguration->usageLimitWarning = 0;
				if(!$monitoredFreeTrial)
				{
					$bodyParams = array($partner->adminName, $partnerPackage['cycle_bw'], $mindtouchNotice, round($totalUsageGB, 2), $emailLinkHash);
					$this->notifyPartner(KalturaMailType::MAIL_TYPE_VIDEO_SERVICE_NOTICE, $partner, $bodyParams);
				}
			}
			elseif ($partner->eightyPercentWarning && !$partner->usageLimitWarning)
			{
				KalturaLog::debug('passed the ' .KAsyncStorageUpdateUtils::WATERMARK_LOW. '%, assume notification sent, nothing to do.');
			}
		}
		elseif($percent >= KAsyncStorageUpdateUtils::WATERMARK_HIGH &&
			!$partner->usageLimitWarning)
		{
			$systemPartnerConfiguration->usageLimitWarning = time();
			if (!$monitoredFreeTrial)
			{
				KalturaLog::debug('partner ' . $partner->id . ' reached ' .KAsyncStorageUpdateUtils::WATERMARK_HIGH. '% - setting second warning');
				$bodyParams = array($partner->adminName, $mindtouchNotice, round($totalUsageGB, 2), $emailLinkHash);
				$this->notifyPartner(KalturaMailType::MAIL_TYPE_VIDEO_SERVICE_NOTICE_LIMIT_REACHED, $partner, $bodyParams);
			}
			else
			{
				$reason = 'partner ' . $partner->id . ' reached ' .KAsyncStorageUpdateUtils::WATERMARK_HIGH. '% - blocking partner';
				KalturaLog::debug($reason);
				if ($this->debugMode)
				{
					KalturaLog::debug('Debug Mode: suppose to change status to blocked on partner id: ' . $partner->id );
				}
				else
				{
					$this->partnerUpdateStatus($partner->id, KAsyncStorageUpdateUtils::PARTNER_STATUS_CONTENT_BLOCK, $reason);
				}

			}
		}
		elseif($percent >= KAsyncStorageUpdateUtils::WATERMARK_HIGH &&
			$partnerPackage['cycle_fee'] == 0 &&
			$partner->usageLimitWarning > 0 &&
			$partner->usageLimitWarning <= $blockNotificationGrace &&
			$partner->usageLimitWarning > $deleteGrace &&
			$partner->status != KAsyncStorageUpdateUtils::PARTNER_STATUS_CONTENT_BLOCK)
		{
			$reason = 'partner '. $partner->id .' reached ' .KAsyncStorageUpdateUtils::WATERMARK_HIGH. '% '.KAsyncStorageUpdateUtils::BLOCKING_DAYS_GRACE .' days ago - sending block email and blocking partner';
			KalturaLog::debug($reason);

			// send block email and block partner
			$bodyParams = array ( $partner->adminName, $mindtouchNotice, round($totalUsageGB, 2), $emailLinkHash );
			$this->notifyPartner(KalturaMailType::MAIL_TYPE_VIDEO_SERVICE_NOTICE_ACCOUNT_LOCKED, $partner, $bodyParams);

			if ($this->debugMode)
			{
				KalturaLog::debug('Debug Mode: suppose to change status to blocked on partner id: ' . $partner->id );
			}
			else
			{
				$this->partnerUpdateStatus($partner->id, KAsyncStorageUpdateUtils::PARTNER_STATUS_CONTENT_BLOCK, $reason);
			}
		}

		elseif($percent >= KAsyncStorageUpdateUtils::WATERMARK_HIGH &&
			$partnerPackage['cycle_fee'] == 0 &&
			$partner->usageLimitWarning > 0 &&
			$partner->usageLimitWarning <= $deleteGrace &&
			$partner->status == KAsyncStorageUpdateUtils::PARTNER_STATUS_CONTENT_BLOCK &&
			!$monitoredFreeTrial)
		{
			$reason = 'partner '. $partner->id .' reached ' .KAsyncStorageUpdateUtils::WATERMARK_HIGH. '% a month ago - deleting partner';
			KalturaLog::debug($reason);

			//delete partner
			$bodyParams = array ( $partner->adminName );
			$this->notifyPartner(KalturaMailType::MAIL_TYPE_VIDEO_SERVICE_NOTICE_ACCOUNT_DELETED, $partner, $bodyParams);

			if ($this->debugMode)
			{
				KalturaLog::debug('Debug Mode: suppose to change status to deleted on partner id: ' . $partner->id );
			}
			else
			{
				$this->partnerUpdateStatus($partner->id, KAsyncStorageUpdateUtils::PARTNER_STATUS_DELETED, $reason);
			}

		}

		elseif ($percent >= KAsyncStorageUpdateUtils::WATERMARK_UPGRADE)
		{
			if ($partnerPackage['cycle_fee'] != 0 &&
				$partner->usageLimitWarning <= $blockNotificationGrace &&
				!$monitoredFreeTrial)
			{
				$bodyParams = array ( $partner->adminName, round($totalUsageGB, 2) );
				$this->notifyPartner(KalturaMailType::MAIL_TYPE_VIDEO_SERVICE_NOTICE_UPGRADE_OFFER, $partner, $bodyParams);
			}
		}
		if ($this->debugMode)
		{
			KalturaLog::debug('Debug Mode: suppose to update on partner id: ' . $partner->id . ' the partner configuration ' . print_r($systemPartnerConfiguration, true));
		}
		else
		{
			$this->partnerUpdateConfiguration($partner->id, $systemPartnerConfiguration);
		}
	}

	public function handleExtendedFreeTrail($partner, $systemPartnerConfiguration)
	{
		KalturaLog::debug('Partner [' . $partner->id . '] trial account has extension');
		if($partner->extendedFreeTrailExpiryDate < time())
		{
			//ExtendedFreeTrail ended
			$systemPartnerConfiguration->extendedFreeTrail = null;
			$systemPartnerConfiguration->extendedFreeTrailExpiryDate = null;
			$systemPartnerConfiguration->extendedFreeTrailExpiryReason = '';
			return $systemPartnerConfiguration;
		}
		elseif (self::isPartnerCreatedAsMonitoredFreeTrial($partner))
		{
			KalturaLog::debug('Partner [' . $partner->id . '] trial account extended - monitored trial');
		}
		else
		{
			//ExtendedFreeTrail
			if ( ($partner->extendedFreeTrailExpiryDate < (time() + (KAsyncStorageUpdateUtils::DAY * 7))) &&
				!$partner->extendedFreeTrailEndsWarning)
			{
				$systemPartnerConfiguration->extendedFreeTrailEndsWarning = true;
				if ($this->debugMode)
				{
					KalturaLog::debug('Debug Mode: suppose to update on partner id: ' . $partner->id . ' the partner configuration ' . print_r($systemPartnerConfiguration, true));
				}
				else
				{
					$this->partnerUpdateConfiguration($partner->id, $systemPartnerConfiguration);
				}

				$emailLinkHash = 'pid='.$partner->id.'&h='.(self::getEmailLinkHash($partner->id, $partner->secret));
				$mailParmas = array($partner->adminName ,$emailLinkHash);
				$this->notifyPartner(KalturaMailType::MAIL_TYPE_EXTENDED_FREE_TRIAL_ENDS_WARNING, $partner, $mailParmas);
			}
			KalturaLog::debug('Partner [' . $partner->id . '] trial account extended');
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
		$formattedCreatedAt = date('Y-m-d H:i:s', $partner->createdAt);

		if($partner->extendedFreeTrailExpiryDate)
		{
			$formattedExtensionDate = date('Y-m-d H:i:s', $partner->extendedFreeTrailExpiryDate);
			$endDay = KAsyncStorageUpdateUtils::diffInDays($formattedCreatedAt, $formattedExtensionDate);
			KalturaLog::debug("After trial extension the End day is: [$endDay]");
		}

		$freeTrialUpdatesDays = explode(',', $partnerPackageInfo['notification_days']);
		$dayInFreeTrial = KAsyncStorageUpdateUtils::diffInDays($formattedCreatedAt, KAsyncStorageUpdateUtils::today());
		KalturaLog::debug("partner [{$partner->id}] is currently at the [$dayInFreeTrial] day of free trial");

		if ($freeTrialUpdatesDays && self::checkForNotificationDay($partner, $dayInFreeTrial, $freeTrialUpdatesDays))
		{
			KalturaLog::debug('Partner [' . $partner->id . '] reached to one of the Marketo lead sync days.');
			$systemPartnerConfiguration = new KalturaSystemPartnerConfiguration();
			$systemPartnerConfiguration->lastFreeTrialNotificationDay = $dayInFreeTrial;
			if ($this->debugMode)
			{
				KalturaLog::debug('Debug Mode: suppose to update on partner id: ' . $partner->id . ' the partner configuration ' . print_r($systemPartnerConfiguration, true));
			}
			else
			{
				$this->partnerUpdateConfiguration($partner->id, $systemPartnerConfiguration);
			}
		}
	}

	public static function checkForNotificationDay($partner, $dayInFreeTrial, $freeTrialUpdatesDays)
	{
		$closestUpdatesDay = KAsyncStorageUpdateUtils::getClosestDay($dayInFreeTrial, $freeTrialUpdatesDays);
		KalturaLog::debug('closest notification day comparing today [' . $closestUpdatesDay . ']');
		if ($closestUpdatesDay > $partner->lastFreeTrialNotificationDay)
		{
			return true;
		}
		return false;
	}

	public function notifyPartner($mailType, $partner, $bodyParams = array() )
	{
		$mailJobData = $this->createMailJobData($mailType, $partner->adminEmail);
		$bodyParams[0] = $bodyParams[0].' (PartnerID: '. $partner->id .')';
		$paramsString = implode($mailJobData->separator, $bodyParams);
		$mailJobData->bodyParams = $paramsString;
		if ($this->debugMode)
		{
			KalturaLog::debug('Debug Mode: suppose to send mail. job data: '. print_r($mailJobData, true));
		}
		else
		{
			self::$kClient->jobs->addMailJob($mailJobData);
		}

		$mailJobData = $this->createMailJobData($mailType, KAsyncStorageUpdateUtils::KALTURA_ACCOUNT_UPGRADES_NOTIFICATION_EMAIL);
		$bodyParams[0] = $bodyParams[0].' ('. $partner->id .')'." type:[{$partner->type}] partnerName:[{$partner->name}]";
		$paramsString = implode($mailJobData->separator, $bodyParams);
		$mailJobData->bodyParams = $paramsString;
		if ($this->debugMode)
		{
			KalturaLog::debug('Debug Mode: suppose to send mail. job data: '. print_r($mailJobData, true));
		}
		else
		{
			self::$kClient->jobs->addMailJob($mailJobData);
		}
	}

	protected function partnerUpdateStatus($pid, $status, $reason)
	{
		try
		{
			$this->systemPartnerClientPlugin->systemPartner->updateStatus($pid, $status, $reason);
		}
		catch(KalturaException $kex)
		{
			KalturaLog::debug('Failed to update status on pid: ' . $pid . ' Error is: '. $kex->getMessage());
		}
		catch(KalturaClientException $kcex)
		{
			KalturaLog::debug('Failed to update status on pid: ' . $pid . ' Error is: '. $kcex->getMessage());
		}
	}

	protected function partnerUpdateConfiguration($pid, $systemPartnerConfiguration)
	{
		try
		{
			$this->systemPartnerClientPlugin->systemPartner->updateConfiguration($pid, $systemPartnerConfiguration);
		}
		catch(KalturaException $kex)
		{
			KalturaLog::debug('Failed to update configuration on pid: ' . $pid . ' Error is: '. $kex->getMessage());
		}
		catch(KalturaClientException $kcex)
		{
			KalturaLog::debug('Failed to update configuration on pid: ' . $pid . ' Error is: '. $kcex->getMessage());
		}
	}

	public function createMailJobData($mail_type, $recipientEmail)
	{
		$mailJobData = new KalturaMailJobData();
		$mailJobData->separator = '|';
		$mailJobData->mailType = $mail_type;
		$mailJobData->mailPriority = KAsyncStorageUpdateUtils::MAIL_PRIORITY_NORMAL;
		$mailJobData->fromEmail =  self::getKeyFromMap($this->localMap, KAsyncStorageUpdateUtils::PARTNER_NOTIFICATION_EMAIL, false);
		$mailJobData->fromName = self::getKeyFromMap($this->localMap, KAsyncStorageUpdateUtils::PARTNER_NOTIFICATION_NAME, false);
		$mailJobData->recipientEmail = $recipientEmail;
		return $mailJobData;
	}

	public static function getKeyFromMap($map, $key, $defaultValue)
	{
		if ($map && isset($map[$key]))
		{
			$value =  $map[$key];
		}
		else
		{
			$value = $defaultValue;
		}
		return $value;
	}

	public function getEmailLinkHash($partner_id, $partner_secret)
	{
		$value = self::getKeyFromMap($this->localMap, KAsyncStorageUpdateUtils::KALTURA_EMAIL_HASH, false);
		return md5($partner_secret.$partner_id.$value);
	}

	public function isPartnerCreatedAsMonitoredFreeTrial($partner)
	{
		if ($partner->partnerPackage == KAsyncStorageUpdateUtils::PARTNER_PACKAGE_INTERNAL_TRIAL)
		{
			return true;
		}
		if ($partner->partnerPackage == KAsyncStorageUpdateUtils::PARTNER_PACKAGE_DEVELOPER_TRIAL)
		{
			$freeTrialStartDate = self::getKeyFromMap($this->localMap, KAsyncStorageUpdateUtils::NEW_DEVELOPER_FREE_TRIAL_START_DATE, null);
		}
		else
		{
			$freeTrialStartDate = self::getKeyFromMap($this->localMap, KAsyncStorageUpdateUtils::NEW_FREE_TRIAL_START_DATE, null);
		}
		if(!$freeTrialStartDate)
		{
			return false;
		}
		$createTime = $partner->createdAt;
		if($createTime >= $freeTrialStartDate)
		{
			return true;
		}
		return false;
	}
}
