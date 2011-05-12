<?php

class kMemoryManager
{
	public static function clearMemory()
	{
		accessControlPeer::clearInstancePool();
	    kuserPeer::clearInstancePool();
	    kshowPeer::clearInstancePool();
	    entryPeer::clearInstancePool();
//	    kvotePeer::clearInstancePool();
//	    commentPeer::clearInstancePool();
//	    flagPeer::clearInstancePool();
//	    favoritePeer::clearInstancePool();
//	    KshowKuserPeer::clearInstancePool();
//	    MailJobPeer::clearInstancePool();
	    SchedulerPeer::clearInstancePool();
	    SchedulerWorkerPeer::clearInstancePool();
	    SchedulerStatusPeer::clearInstancePool();
	    SchedulerConfigPeer::clearInstancePool();
	    ControlPanelCommandPeer::clearInstancePool();
	    BatchJobPeer::clearInstancePool();
//	    PriorityGroupPeer::clearInstancePool();
	    BulkUploadResultPeer::clearInstancePool();
//	    blockedEmailPeer::clearInstancePool();
//	    conversionPeer::clearInstancePool();
//	    flickrTokenPeer::clearInstancePool();
	    PuserKuserPeer::clearInstancePool();
//	    PuserRolePeer::clearInstancePool();
	    PartnerPeer::clearInstancePool();
//	    WidgetLogPeer::clearInstancePool();
//	    adminKuserPeer::clearInstancePool();
//	    notificationPeer::clearInstancePool();
	    moderationPeer::clearInstancePool();
	    moderationFlagPeer::clearInstancePool();
	    roughcutEntryPeer::clearInstancePool();
//	    widgetPeer::clearInstancePool();
	    uiConfPeer::clearInstancePool();
//	    PartnerStatsPeer::clearInstancePool();
//	    PartnerActivityPeer::clearInstancePool();
	    ConversionProfilePeer::clearInstancePool();
//	    ConversionParamsPeer::clearInstancePool();
//	    KceInstallationErrorPeer::clearInstancePool();
	    FileSyncPeer::clearInstancePool();
	    accessControlPeer::clearInstancePool();
	    mediaInfoPeer::clearInstancePool();
	    assetParamsPeer::clearInstancePool();
	    assetParamsOutputPeer::clearInstancePool();
	    assetPeer::clearInstancePool();
	    conversionProfile2Peer::clearInstancePool();
	    flavorParamsConversionProfilePeer::clearInstancePool();
	    categoryPeer::clearInstancePool();
	    syndicationFeedPeer::clearInstancePool();
	    TrackEntryPeer::clearInstancePool();
//	    SystemUserPeer::clearInstancePool();
	    StorageProfilePeer::clearInstancePool();
//	    EmailIngestionProfilePeer::clearInstancePool();
	    UploadTokenPeer::clearInstancePool();
//	    invalidSessionPeer::clearInstancePool();
	    DynamicEnumPeer::clearInstancePool();
	    UserLoginDataPeer::clearInstancePool();
	    PermissionPeer::clearInstancePool();
	    UserRolePeer::clearInstancePool();
	    PermissionItemPeer::clearInstancePool();
	    PermissionToPermissionItemPeer::clearInstancePool();
	    KuserToUserRolePeer::clearInstancePool();

		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaMemoryCleaner');
		foreach($pluginInstances as $pluginInstance)
			$pluginInstance->cleanMemory();
					
		if(function_exists('gc_collect_cycles')) // php 5.3 and above
			gc_collect_cycles();
	}
}