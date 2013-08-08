
var kObjects = {
	coreObjectType: {
		label: 			'Event',
		subSelections:	{
			baseEntry:						{label: 'Base Entry', coreType: 'entry', apiType: 'KalturaBaseEntry'},
			dataEntry:						{label: 'Data Entry', coreType: 'entry', apiType: 'KalturaDataEntry'},
			documentEntry:					{label: 'Document Entry', coreType: 'entry', apiType: 'KalturaDocumentEntry'},
			mediaEntry:						{label: 'Media Entry', coreType: 'entry', apiType: 'KalturaMediaEntry'},
			externalMediaEntry:				{label: 'External Media Entry', coreType: 'entry', apiType: 'KalturaExternalMediaEntry'},
			liveStreamEntry:				{label: 'Live Stream Entry', coreType: 'entry', apiType: 'KalturaLiveStreamEntry'},
			playlist:						{label: 'Playlist', coreType: 'entry', apiType: 'KalturaPlaylist'},
			category:						{label:	'Category', apiType: 'KalturaCategory'},
			kuser:							{label:	'User', apiType: 'KalturaUser'},
	        CuePoint:						{label:	'CuePoint', apiType: 'KalturaCuePoint'},
	        AdCuePoint:						{label:	'Ad Cue-Point', apiType: 'KalturaAdCuePoint'},
	        Annotation:						{label:	'Annotation', apiType: 'KalturaAnnotation'},
	        CodeCuePoint:					{label:	'Code Cue-Point', apiType: 'KalturaCodeCuePoint'},
	        DistributionProfile:			{label:	'Distribution Profile', apiType: 'KalturaDistributionProfile'},
	        EntryDistribution:				{label:	'Entry Distribution', apiType: 'KalturaEntryDistribution'},
	        Metadata:						{label:	'Metadata', apiType: 'KalturaMetadata'},
	        asset:							{label:	'Asset', apiType: 'KalturaAsset'},
	        flavorAsset:					{label:	'Flavor Asset', apiType: 'KalturaFlavorAsset'},
	        thumbAsset:						{label:	'Thumbnail Asset', apiType: 'KalturaThumbAsset'},
	        accessControl:					{label:	'Access Control', apiType: 'KalturaAccessControlProfile'},
	        BatchJob:						{label:	'BatchJob', apiType: 'KalturaBatchJob'},
	        BulkUploadResultEntry:			{label:	'Bulk-Upload Entry Result', apiType: 'KalturaBulkUploadResultEntry'},
	        BulkUploadResultCategory:		{label:	'Bulk-Upload Category Result', apiType: 'KalturaBulkUploadResultCategory'},
	        BulkUploadResultKuser:			{label:	'Bulk-Upload User Result', apiType: 'KalturaBulkUploadResultUser'},
	        BulkUploadResultCategoryKuser:	{label:	'Bulk-Upload Category - User Result', apiType: 'KalturaBulkUploadResultCategoryUser'},
	        categoryKuser:					{label:	'Category - User', apiType: 'KalturaCategoryUser'},
	        conversionProfile2:				{label:	'Conversion Profile', apiType: 'KalturaConversionProfile'},
	        flavorParams:					{label:	'Flavor Params', apiType: 'KalturaFlavorParams'},
	        flavorParamsConversionProfile:	{label:	'Asset Params - Conversion Profile', apiType: 'KalturaConversionProfileAssetParams'},
	        flavorParamsOutput:				{label:	'Flavor Params Output', apiType: 'KalturaFlavorParamsOutput'},
	        genericsynDicationFeed:			{label:	'Genericsyn Dication Feed', apiType: 'KalturaGenericsynDicationFeed'},
	        Partner:						{label:	'Partner', apiType: 'KalturaPartner'},
	        Permission:						{label:	'Permission', apiType: 'KalturaPermission'},
	        PermissionItem:					{label:	'Permission Item', apiType: 'KalturaPermissionItem'},
	        Scheduler:						{label:	'Scheduler', apiType: 'KalturaScheduler'},
	        SchedulerConfig:				{label:	'Scheduler Config', apiType: 'KalturaSchedulerConfig'},
	        SchedulerStatus:				{label:	'Scheduler Status', apiType: 'KalturaSchedulerStatus'},
	        SchedulerWorker:				{label:	'Scheduler Worker', apiType: 'KalturaSchedulerWorker'},
	        StorageProfile:					{label:	'Storage Profile', apiType: 'KalturaStorageProfile'},
	        thumbParams:					{label:	'Thumbnail Params', apiType: 'KalturaThumbParams'},
	        thumbParamsOutput:				{label:	'Thumbnail Params Output', apiType: 'KalturaThumbParamsOutput'},
	        UploadToken:					{label:	'Upload Token', apiType: 'KalturaUploadToken'},
	        UserLoginData:					{label:	'User Login Data', apiType: 'KalturaUserLoginData'},
	        UserRole:						{label:	'User Role', apiType: 'KalturaUserRole'},
	        widget:							{label:	'Widget', apiType: 'KalturaWidget'},
	        categoryEntry:					{label:	'Category - Entry', apiType: 'KalturaCategoryEntry'}
		},
		subLabel:		'Select Object Type',
		getData:		function(subCode, variables){
							var coreType = variables.value;
							if(variables.coreType != null)
								coreType = variables.coreType;
								
							var ret = {
								code: '(($scope->getEvent()->getObject() instanceof ' + coreType + ') ? $scope->getEvent()->getObject() : null)',
								coreType: coreType
							};
							
							if(variables.apiType != null)
								ret.apiName = variables.apiType;
								
							return ret;
		}
	}
};
