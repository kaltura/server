<?php

class KalturaAuditTrailObjectType extends KalturaStringEnum
{
	const ACCESS_CONTROL = accessControlPeer::OM_CLASS;
	const ADMIN_KUSER = adminKuserPeer::OM_CLASS; //deprecated
	const BATCH_JOB = BatchJobPeer::OM_CLASS;
	const CATEGORY = categoryPeer::OM_CLASS;
	const CONVERSION_PROFILE_2 = conversionProfile2Peer::OM_CLASS;
	const EMAIL_INGESTION_PROFILE = EmailIngestionProfilePeer::OM_CLASS;
	const ENTRY = entryPeer::OM_CLASS;
	const FILE_SYNC = FileSyncPeer::OM_CLASS;
	const FLAVOR_ASSET = flavorAssetPeer::OM_CLASS;
	const FLAVOR_PARAMS = flavorParamsPeer::OM_CLASS;
	const FLAVOR_PARAMS_CONVERSION_PROFILE = flavorParamsConversionProfilePeer::OM_CLASS;
	const FLAVOR_PARAMS_OUTPUT = flavorParamsOutputPeer::OM_CLASS;
	const KSHOW = kshowPeer::OM_CLASS;
	const KSHOW_KUSER = KshowKuserPeer::OM_CLASS;
	const KUSER = kuserPeer::OM_CLASS;
	const MEDIA_INFO = mediaInfoPeer::OM_CLASS;
	const MODERATION = moderationPeer::OM_CLASS;
	const PARTNER = PartnerPeer::OM_CLASS;
	const PUSER_KUSER = PuserKuserPeer::OM_CLASS;
	const ROUGHCUT = roughcutEntryPeer::OM_CLASS;
	const SYNDICATION = syndicationFeedPeer::OM_CLASS;
	const UI_CONF = uiConfPeer::OM_CLASS;
	const UPLOAD_TOKEN = UploadTokenPeer::OM_CLASS;
	const WIDGET = widgetPeer::OM_CLASS;
	const METADATA = MetadataPeer::OM_CLASS;
	const METADATA_PROFILE = MetadataProfilePeer::OM_CLASS;
	const USER_LOGIN_DATA = UserLoginDataPeer::OM_CLASS;
	const USER_ROLE = UserRolePeer::OM_CLASS;
	const PERMISSION = PermissionPeer::OM_CLASS;
}
