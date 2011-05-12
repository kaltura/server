<?php


class ComcastAccountField extends SoapObject
{				
	const _ID = 'ID';
					
	const _ACTIONLIMIT = 'actionLimit';
					
	const _ADDED = 'added';
					
	const _ADDEDBYUSER = 'addedByUser';
					
	const _ADDEDBYUSEREMAILADDRESS = 'addedByUserEmailAddress';
					
	const _ADDEDBYUSERID = 'addedByUserID';
					
	const _ADDEDBYUSERNAME = 'addedByUserName';
					
	const _ALLOWCONSOLEACCESS = 'allowConsoleAccess';
					
	const _ALLOWOPENDELIVERY = 'allowOpenDelivery';
					
	const _ALLOWEDDELIVERYSITES = 'allowedDeliverySites';
					
	const _ALLOWEDUSERAGENTS = 'allowedUserAgents';
					
	const _AUTOMATICALLYCOLLECTPAYMENTSBYDEFAULT = 'automaticallyCollectPaymentsByDefault';
					
	const _AUTOMATICALLYDELETEEMPTYRELEASES = 'automaticallyDeleteEmptyReleases';
					
	const _AUTOMATICALLYDELETEEXPIREDCONTENT = 'automaticallyDeleteExpiredContent';
					
	const _AUTOMATICALLYGENERATETHUMBNAILS = 'automaticallyGenerateThumbnails';
					
	const _BANNERHTML = 'bannerHTML';
					
	const _CHILDACCOUNTIDS = 'childAccountIDs';
					
	const _CONTACTINFO = 'contactInfo';
					
	const _CONTENTPUBLISHINGISPUBLIC = 'contentPublishingIsPublic';
					
	const _CONTENTPUBLISHINGNETWORKS = 'contentPublishingNetworks';
					
	const _CONTENTPUBLISHINGPASSWORD = 'contentPublishingPassword';
					
	const _CONTENTPUBLISHINGURL = 'contentPublishingURL';
					
	const _CONTENTPUBLISHINGUSEMESSAGING = 'contentPublishingUseMessaging';
					
	const _CONTENTPUBLISHINGUSERNAME = 'contentPublishingUserName';
					
	const _DEFAULTAPPROVED = 'defaultApproved';
					
	const _DEFAULTCONTAINERPLAYLISTIDS = 'defaultContainerPlaylistIDs';
					
	const _DEFAULTCONTAINERPLAYLISTS = 'defaultContainerPlaylists';
					
	const _DEFAULTCONTENTAPPROVED = 'defaultContentApproved';
					
	const _DEFAULTCOPYRIGHT = 'defaultCopyright';
					
	const _DEFAULTFLVDOWNLOADSERVERID = 'defaultFLVDownloadServerID';
					
	const _DEFAULTFLVPUSHSERVERID = 'defaultFLVPushServerID';
					
	const _DEFAULTFLVSTORAGESERVERID = 'defaultFLVStorageServerID';
					
	const _DEFAULTFLVSTREAMINGSERVERID = 'defaultFLVStreamingServerID';
					
	const _DEFAULTINHERITEDSERVERIDS = 'defaultInheritedServerIDs';
					
	const _DEFAULTLANGUAGE = 'defaultLanguage';
					
	const _DEFAULTLICENSEIDS = 'defaultLicenseIDs';
					
	const _DEFAULTLICENSES = 'defaultLicenses';
					
	const _DEFAULTOTHERDOWNLOADSERVERID = 'defaultOtherDownloadServerID';
					
	const _DEFAULTOTHERPUSHSERVERID = 'defaultOtherPushServerID';
					
	const _DEFAULTOTHERSTORAGESERVERID = 'defaultOtherStorageServerID';
					
	const _DEFAULTOTHERSTREAMINGSERVERID = 'defaultOtherStreamingServerID';
					
	const _DEFAULTQTDOWNLOADSERVERID = 'defaultQTDownloadServerID';
					
	const _DEFAULTQTPUSHSERVERID = 'defaultQTPushServerID';
					
	const _DEFAULTQTSTORAGESERVERID = 'defaultQTStorageServerID';
					
	const _DEFAULTQTSTREAMINGSERVERID = 'defaultQTStreamingServerID';
					
	const _DEFAULTRATING = 'defaultRating';
					
	const _DEFAULTREALDOWNLOADSERVERID = 'defaultRealDownloadServerID';
					
	const _DEFAULTREALPUSHSERVERID = 'defaultRealPushServerID';
					
	const _DEFAULTREALSTORAGESERVERID = 'defaultRealStorageServerID';
					
	const _DEFAULTREALSTREAMINGSERVERID = 'defaultRealStreamingServerID';
					
	const _DEFAULTRESTRICTIONIDS = 'defaultRestrictionIDs';
					
	const _DEFAULTRESTRICTIONS = 'defaultRestrictions';
					
	const _DEFAULTTHUMBNAILSERVERID = 'defaultThumbnailServerID';
					
	const _DEFAULTTIMEZONE = 'defaultTimeZone';
					
	const _DEFAULTUSAGEPLANIDS = 'defaultUsagePlanIDs';
					
	const _DEFAULTUSAGEPLANS = 'defaultUsagePlans';
					
	const _DEFAULTWMDOWNLOADSERVERID = 'defaultWMDownloadServerID';
					
	const _DEFAULTWMPUSHSERVERID = 'defaultWMPushServerID';
					
	const _DEFAULTWMSTORAGESERVERID = 'defaultWMStorageServerID';
					
	const _DEFAULTWMSTREAMINGSERVERID = 'defaultWMStreamingServerID';
					
	const _DESCRIPTION = 'description';
					
	const _DISABLEACCESSTORELEASEDMEDIAFILEURLS = 'disableAccessToReleasedMediaFileURLs';
					
	const _DISABLEDROPFOLDER = 'disableDropFolder';
					
	const _DISABLEMEDIAFILEENCODING = 'disableMediaFileEncoding';
					
	const _DISABLENEWACCOUNTS = 'disableNewAccounts';
					
	const _DISABLENEWDRMLICENSES = 'disableNewDRMLicenses';
					
	const _DISABLENEWLICENSES = 'disableNewLicenses';
					
	const _DISABLENEWSHARING = 'disableNewSharing';
					
	const _DISABLEPORTALS = 'disablePortals';
					
	const _DISABLESTANDALONETRACKING = 'disableStandAloneTracking';
					
	const _DISABLESTANDALONEUPLOADS = 'disableStandAloneUploads';
					
	const _DISABLESTOREFRONTS = 'disableStorefronts';
					
	const _DISABLETHUMBNAILGENERATION = 'disableThumbnailGeneration';
					
	const _DISABLED = 'disabled';
					
	const _DOMAIN = 'domain';
					
	const _DROPFOLDERFILEPATTERNS = 'dropFolderFilePatterns';
					
	const _ERRORMESSAGEBASEURL = 'errorMessageBaseURL';
					
	const _HASCHILDACCOUNTS = 'hasChildAccounts';
					
	const _HELPURL = 'helpURL';
					
	const _HOMETABHEADERHEIGHT = 'homeTabHeaderHeight';
					
	const _HOMETABURL = 'homeTabURL';
					
	const _INHERITEDSERVERIDS = 'inheritedServerIDs';
					
	const _LASTMODIFIED = 'lastModified';
					
	const _LASTMODIFIEDBYUSER = 'lastModifiedByUser';
					
	const _LASTMODIFIEDBYUSEREMAILADDRESS = 'lastModifiedByUserEmailAddress';
					
	const _LASTMODIFIEDBYUSERID = 'lastModifiedByUserID';
					
	const _LASTMODIFIEDBYUSERNAME = 'lastModifiedByUserName';
					
	const _LIMITCONTENTBYENDUSERLOCATION = 'limitContentByEndUserLocation';
					
	const _LIMITTOACCOUNTSHARING = 'limitToAccountSharing';
					
	const _LIMITTOROLEIDS = 'limitToRoleIDs';
					
	const _LIMITTOROLES = 'limitToRoles';
					
	const _LOCKED = 'locked';
					
	const _LOGOURL = 'logoURL';
					
	const _MAINSITEURL = 'mainSiteURL';
					
	const _MAXIMUMAPIREQUESTSPERDAY = 'maximumAPIRequestsPerDay';
					
	const _MAXIMUMENCODINGPROFILETOTALBITRATE = 'maximumEncodingProfileTotalBitrate';
					
	const _MAXIMUMPAYMENTPERTRANSACTION = 'maximumPaymentPerTransaction';
					
	const _MAXIMUMRELEASEREQUESTSPERDAY = 'maximumReleaseRequestsPerDay';
					
	const _MAXIMUMUSAGEREPORTREQUESTSPERDAY = 'maximumUsageReportRequestsPerDay';
					
	const _METAFILEENCODING = 'metafileEncoding';
					
	const _NAME = 'name';
					
	const _NOTIFICATIONACTIONS = 'notificationActions';
					
	const _NOTIFICATIONISPUBLIC = 'notificationIsPublic';
					
	const _NOTIFICATIONITEMS = 'notificationItems';
					
	const _NOTIFICATIONNETWORKS = 'notificationNetworks';
					
	const _NOTIFICATIONPASSWORD = 'notificationPassword';
					
	const _NOTIFICATIONURL = 'notificationURL';
					
	const _NOTIFICATIONUSERNAME = 'notificationUserName';
					
	const _OWNER = 'owner';
					
	const _OWNERACCOUNTID = 'ownerAccountID';
					
	const _PAYPAGEURL = 'payPageURL';
					
	const _PAYMENTFAILUREEMAILADDRESSES = 'paymentFailureEmailAddresses';
					
	const _PAYMENTGATEWAY = 'paymentGateway';
					
	const _PAYMENTGATEWAYACCOUNT = 'paymentGatewayAccount';
					
	const _PAYMENTGATEWAYPASSWORD = 'paymentGatewayPassword';
					
	const _PERMISSIONIDS = 'permissionIDs';
					
	const _PLAYERADMINSERVICEURL = 'playerAdminServiceURL';
					
	const _POSSIBLERATINGS = 'possibleRatings';
					
	const _RELEASEURL = 'releaseURL';
					
	const _STORAGEUSED = 'storageUsed';
					
	const _STYLESHEETURL = 'stylesheetURL';
					
	const _SUBDOMAIN = 'subdomain';
					
	const _TARGETCOUNTRIES = 'targetCountries';
					
	const _THUMBNAILADJUSTMENT = 'thumbnailAdjustment';
					
	const _THUMBNAILBACKGROUNDCOLOR = 'thumbnailBackgroundColor';
					
	const _THUMBNAILWIDTH = 'thumbnailWidth';
					
	const _TRACKBROWSERBYDEFAULT = 'trackBrowserByDefault';
					
	const _TRACKLOCATIONBYDEFAULT = 'trackLocationByDefault';
					
	const _TRANSCRIPTFOOTER = 'transcriptFooter';
					
	const _TRANSCRIPTHEADER = 'transcriptHeader';
					
	const _UPLOADQUOTA = 'uploadQuota';
					
	const _USEFLVSERVERSFORMPEG4 = 'useFLVServersForMPEG4';
					
	const _USEOWNERBANNERHTML = 'useOwnerBannerHTML';
					
	const _USEOWNERERRORMESSAGEBASEURL = 'useOwnerErrorMessageBaseURL';
					
	const _USEOWNERFLVDOWNLOADSERVER = 'useOwnerFLVDownloadServer';
					
	const _USEOWNERFLVPUSHSERVER = 'useOwnerFLVPushServer';
					
	const _USEOWNERFLVSTORAGESERVER = 'useOwnerFLVStorageServer';
					
	const _USEOWNERFLVSTREAMINGSERVER = 'useOwnerFLVStreamingServer';
					
	const _USEOWNERHOMETABHEADERHEIGHT = 'useOwnerHomeTabHeaderHeight';
					
	const _USEOWNERLOGOURL = 'useOwnerLogoURL';
					
	const _USEOWNERMAINSITEURL = 'useOwnerMainSiteURL';
					
	const _USEOWNEROTHERDOWNLOADSERVER = 'useOwnerOtherDownloadServer';
					
	const _USEOWNEROTHERPUSHSERVER = 'useOwnerOtherPushServer';
					
	const _USEOWNEROTHERSTORAGESERVER = 'useOwnerOtherStorageServer';
					
	const _USEOWNEROTHERSTREAMINGSERVER = 'useOwnerOtherStreamingServer';
					
	const _USEOWNERQTDOWNLOADSERVER = 'useOwnerQTDownloadServer';
					
	const _USEOWNERQTPUSHSERVER = 'useOwnerQTPushServer';
					
	const _USEOWNERQTSTORAGESERVER = 'useOwnerQTStorageServer';
					
	const _USEOWNERQTSTREAMINGSERVER = 'useOwnerQTStreamingServer';
					
	const _USEOWNERREALDOWNLOADSERVER = 'useOwnerRealDownloadServer';
					
	const _USEOWNERREALPUSHSERVER = 'useOwnerRealPushServer';
					
	const _USEOWNERREALSTORAGESERVER = 'useOwnerRealStorageServer';
					
	const _USEOWNERREALSTREAMINGSERVER = 'useOwnerRealStreamingServer';
					
	const _USEOWNERSTYLESHEETURL = 'useOwnerStylesheetURL';
					
	const _USEOWNERTHUMBNAILSERVER = 'useOwnerThumbnailServer';
					
	const _USEOWNERWMDOWNLOADSERVER = 'useOwnerWMDownloadServer';
					
	const _USEOWNERWMPUSHSERVER = 'useOwnerWMPushServer';
					
	const _USEOWNERWMSTORAGESERVER = 'useOwnerWMStorageServer';
					
	const _USEOWNERWMSTREAMINGSERVER = 'useOwnerWMStreamingServer';
					
	const _USEPAYMENTGATEWAYTESTMODE = 'usePaymentGatewayTestMode';
					
	const _VERSION = 'version';
					
	const _VISIBLETOACCOUNTIDS = 'visibleToAccountIDs';
					
	const _VISIBLETOACCOUNTS = 'visibleToAccounts';
					
	const _VISIBLETOALLACCOUNTS = 'visibleToAllAccounts';
					
	const _WMRMLICENSEACQUISITIONURL = 'wmrmLicenseAcquisitionURL';
					
	const _WMRMLICENSEKEYSEED = 'wmrmLicenseKeySeed';
					
	const _WMRMPRIVATEKEY = 'wmrmPrivateKey';
					
	const _WMRMPUBLICKEY = 'wmrmPublicKey';
					
	const _WMRMREVOCATIONPRIVATEKEY = 'wmrmRevocationPrivateKey';
					
	const _WMRMREVOCATIONPUBLICKEY = 'wmrmRevocationPublicKey';
					
	const _WRITEACTIONLIMIT = 'writeActionLimit';
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


