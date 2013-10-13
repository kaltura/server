// ===================================================================================================
//                           _  __     _ _
//                          | |/ /__ _| | |_ _  _ _ _ __ _
//                          | ' </ _` | |  _| || | '_/ _` |
//                          |_|\_\__,_|_|\__|\_,_|_| \__,_|
//
// This file is part of the Kaltura Collaborative Media Suite which allows users
// to do with audio, video, and animation what Wiki platfroms allow them to do with
// text.
//
// Copyright (C) 2006-2011  Kaltura Inc.
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Affero General Public License for more details.
//
// You should have received a copy of the GNU Affero General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// @ignore
// ===================================================================================================
#import "KalturaContentDistributionClientPlugin.h"

///////////////////////// enums /////////////////////////
@implementation KalturaDistributionAction
+ (int)SUBMIT
{
    return 1;
}
+ (int)UPDATE
{
    return 2;
}
+ (int)DELETE
{
    return 3;
}
+ (int)FETCH_REPORT
{
    return 4;
}
@end

@implementation KalturaDistributionErrorType
+ (int)MISSING_FLAVOR
{
    return 1;
}
+ (int)MISSING_THUMBNAIL
{
    return 2;
}
+ (int)MISSING_METADATA
{
    return 3;
}
+ (int)INVALID_DATA
{
    return 4;
}
+ (int)MISSING_ASSET
{
    return 5;
}
@end

@implementation KalturaDistributionFieldRequiredStatus
+ (int)NOT_REQUIRED
{
    return 0;
}
+ (int)REQUIRED_BY_PROVIDER
{
    return 1;
}
+ (int)REQUIRED_BY_PARTNER
{
    return 2;
}
@end

@implementation KalturaDistributionProfileActionStatus
+ (int)DISABLED
{
    return 1;
}
+ (int)AUTOMATIC
{
    return 2;
}
+ (int)MANUAL
{
    return 3;
}
@end

@implementation KalturaDistributionProfileStatus
+ (int)DISABLED
{
    return 1;
}
+ (int)ENABLED
{
    return 2;
}
+ (int)DELETED
{
    return 3;
}
@end

@implementation KalturaDistributionProtocol
+ (int)FTP
{
    return 1;
}
+ (int)SCP
{
    return 2;
}
+ (int)SFTP
{
    return 3;
}
+ (int)HTTP
{
    return 4;
}
+ (int)HTTPS
{
    return 5;
}
+ (int)ASPERA
{
    return 10;
}
@end

@implementation KalturaDistributionValidationErrorType
+ (int)CUSTOM_ERROR
{
    return 0;
}
+ (int)STRING_EMPTY
{
    return 1;
}
+ (int)STRING_TOO_LONG
{
    return 2;
}
+ (int)STRING_TOO_SHORT
{
    return 3;
}
+ (int)INVALID_FORMAT
{
    return 4;
}
@end

@implementation KalturaEntryDistributionFlag
+ (int)NONE
{
    return 0;
}
+ (int)SUBMIT_REQUIRED
{
    return 1;
}
+ (int)DELETE_REQUIRED
{
    return 2;
}
+ (int)UPDATE_REQUIRED
{
    return 3;
}
+ (int)ENABLE_REQUIRED
{
    return 4;
}
+ (int)DISABLE_REQUIRED
{
    return 5;
}
@end

@implementation KalturaEntryDistributionStatus
+ (int)PENDING
{
    return 0;
}
+ (int)QUEUED
{
    return 1;
}
+ (int)READY
{
    return 2;
}
+ (int)DELETED
{
    return 3;
}
+ (int)SUBMITTING
{
    return 4;
}
+ (int)UPDATING
{
    return 5;
}
+ (int)DELETING
{
    return 6;
}
+ (int)ERROR_SUBMITTING
{
    return 7;
}
+ (int)ERROR_UPDATING
{
    return 8;
}
+ (int)ERROR_DELETING
{
    return 9;
}
+ (int)REMOVED
{
    return 10;
}
+ (int)IMPORT_SUBMITTING
{
    return 11;
}
+ (int)IMPORT_UPDATING
{
    return 12;
}
@end

@implementation KalturaEntryDistributionSunStatus
+ (int)BEFORE_SUNRISE
{
    return 1;
}
+ (int)AFTER_SUNRISE
{
    return 2;
}
+ (int)AFTER_SUNSET
{
    return 3;
}
@end

@implementation KalturaGenericDistributionProviderParser
+ (int)XSL
{
    return 1;
}
+ (int)XPATH
{
    return 2;
}
+ (int)REGEX
{
    return 3;
}
@end

@implementation KalturaGenericDistributionProviderStatus
+ (int)ACTIVE
{
    return 2;
}
+ (int)DELETED
{
    return 3;
}
@end

@implementation KalturaConfigurableDistributionProfileOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
@end

@implementation KalturaDistributionProfileOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
@end

@implementation KalturaDistributionProviderOrderBy
@end

@implementation KalturaDistributionProviderType
+ (NSString*)ATT_UVERSE
{
    return @"attUverseDistribution.ATT_UVERSE";
}
+ (NSString*)AVN
{
    return @"avnDistribution.AVN";
}
+ (NSString*)COMCAST_MRSS
{
    return @"comcastMrssDistribution.COMCAST_MRSS";
}
+ (NSString*)CROSS_KALTURA
{
    return @"crossKalturaDistribution.CROSS_KALTURA";
}
+ (NSString*)DAILYMOTION
{
    return @"dailymotionDistribution.DAILYMOTION";
}
+ (NSString*)DOUBLECLICK
{
    return @"doubleClickDistribution.DOUBLECLICK";
}
+ (NSString*)FREEWHEEL
{
    return @"freewheelDistribution.FREEWHEEL";
}
+ (NSString*)FREEWHEEL_GENERIC
{
    return @"freewheelGenericDistribution.FREEWHEEL_GENERIC";
}
+ (NSString*)FTP
{
    return @"ftpDistribution.FTP";
}
+ (NSString*)FTP_SCHEDULED
{
    return @"ftpDistribution.FTP_SCHEDULED";
}
+ (NSString*)HULU
{
    return @"huluDistribution.HULU";
}
+ (NSString*)IDETIC
{
    return @"ideticDistribution.IDETIC";
}
+ (NSString*)METRO_PCS
{
    return @"metroPcsDistribution.METRO_PCS";
}
+ (NSString*)MSN
{
    return @"msnDistribution.MSN";
}
+ (NSString*)NDN
{
    return @"ndnDistribution.NDN";
}
+ (NSString*)PODCAST
{
    return @"podcastDistribution.PODCAST";
}
+ (NSString*)QUICKPLAY
{
    return @"quickPlayDistribution.QUICKPLAY";
}
+ (NSString*)SYNACOR_HBO
{
    return @"synacorHboDistribution.SYNACOR_HBO";
}
+ (NSString*)TIME_WARNER
{
    return @"timeWarnerDistribution.TIME_WARNER";
}
+ (NSString*)TVCOM
{
    return @"tvComDistribution.TVCOM";
}
+ (NSString*)UVERSE_CLICK_TO_ORDER
{
    return @"uverseClickToOrderDistribution.UVERSE_CLICK_TO_ORDER";
}
+ (NSString*)UVERSE
{
    return @"uverseDistribution.UVERSE";
}
+ (NSString*)VERIZON_VCAST
{
    return @"verizonVcastDistribution.VERIZON_VCAST";
}
+ (NSString*)YAHOO
{
    return @"yahooDistribution.YAHOO";
}
+ (NSString*)YOUTUBE
{
    return @"youTubeDistribution.YOUTUBE";
}
+ (NSString*)YOUTUBE_API
{
    return @"youtubeApiDistribution.YOUTUBE_API";
}
+ (NSString*)GENERIC
{
    return @"1";
}
+ (NSString*)SYNDICATION
{
    return @"2";
}
@end

@implementation KalturaEntryDistributionOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)SUBMITTED_AT_ASC
{
    return @"+submittedAt";
}
+ (NSString*)SUNRISE_ASC
{
    return @"+sunrise";
}
+ (NSString*)SUNSET_ASC
{
    return @"+sunset";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)SUBMITTED_AT_DESC
{
    return @"-submittedAt";
}
+ (NSString*)SUNRISE_DESC
{
    return @"-sunrise";
}
+ (NSString*)SUNSET_DESC
{
    return @"-sunset";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
@end

@implementation KalturaGenericDistributionProfileOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
@end

@implementation KalturaGenericDistributionProviderActionOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
@end

@implementation KalturaGenericDistributionProviderOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
@end

@implementation KalturaSyndicationDistributionProfileOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
@end

@implementation KalturaSyndicationDistributionProviderOrderBy
@end

///////////////////////// classes /////////////////////////
@implementation KalturaAssetDistributionCondition
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAssetDistributionCondition"];
}

@end

@implementation KalturaAssetDistributionRule
@synthesize validationError = _validationError;
@synthesize assetDistributionConditions = _assetDistributionConditions;

- (KalturaFieldType)getTypeOfValidationError
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAssetDistributionConditions
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfAssetDistributionConditions
{
    return @"KalturaAssetDistributionCondition";
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAssetDistributionRule"];
    [aParams addIfDefinedKey:@"validationError" withString:self.validationError];
    [aParams addIfDefinedKey:@"assetDistributionConditions" withArray:self.assetDistributionConditions];
}

- (void)dealloc
{
    [self->_validationError release];
    [self->_assetDistributionConditions release];
    [super dealloc];
}

@end

@interface KalturaDistributionFieldConfig()
@property (nonatomic,assign) BOOL isDefault;
@end

@implementation KalturaDistributionFieldConfig
@synthesize fieldName = _fieldName;
@synthesize userFriendlyFieldName = _userFriendlyFieldName;
@synthesize entryMrssXslt = _entryMrssXslt;
@synthesize isRequired = _isRequired;
@synthesize updateOnChange = _updateOnChange;
@synthesize updateParams = _updateParams;
@synthesize isDefault = _isDefault;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_isRequired = KALTURA_UNDEF_INT;
    self->_updateOnChange = KALTURA_UNDEF_BOOL;
    self->_isDefault = KALTURA_UNDEF_BOOL;
    return self;
}

- (KalturaFieldType)getTypeOfFieldName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfUserFriendlyFieldName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEntryMrssXslt
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfIsRequired
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdateOnChange
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfUpdateParams
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfUpdateParams
{
    return @"KalturaString";
}

- (KalturaFieldType)getTypeOfIsDefault
{
    return KFT_Bool;
}

- (void)setIsRequiredFromString:(NSString*)aPropVal
{
    self.isRequired = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdateOnChangeFromString:(NSString*)aPropVal
{
    self.updateOnChange = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)setIsDefaultFromString:(NSString*)aPropVal
{
    self.isDefault = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDistributionFieldConfig"];
    [aParams addIfDefinedKey:@"fieldName" withString:self.fieldName];
    [aParams addIfDefinedKey:@"userFriendlyFieldName" withString:self.userFriendlyFieldName];
    [aParams addIfDefinedKey:@"entryMrssXslt" withString:self.entryMrssXslt];
    [aParams addIfDefinedKey:@"isRequired" withInt:self.isRequired];
    [aParams addIfDefinedKey:@"updateOnChange" withBool:self.updateOnChange];
    [aParams addIfDefinedKey:@"updateParams" withArray:self.updateParams];
}

- (void)dealloc
{
    [self->_fieldName release];
    [self->_userFriendlyFieldName release];
    [self->_entryMrssXslt release];
    [self->_updateParams release];
    [super dealloc];
}

@end

@implementation KalturaDistributionJobProviderData
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDistributionJobProviderData"];
}

@end

@implementation KalturaDistributionThumbDimensions
@synthesize width = _width;
@synthesize height = _height;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_width = KALTURA_UNDEF_INT;
    self->_height = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfWidth
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfHeight
{
    return KFT_Int;
}

- (void)setWidthFromString:(NSString*)aPropVal
{
    self.width = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setHeightFromString:(NSString*)aPropVal
{
    self.height = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDistributionThumbDimensions"];
    [aParams addIfDefinedKey:@"width" withInt:self.width];
    [aParams addIfDefinedKey:@"height" withInt:self.height];
}

@end

@interface KalturaDistributionProfile()
@property (nonatomic,assign) int id;
@property (nonatomic,assign) int createdAt;
@property (nonatomic,assign) int updatedAt;
@property (nonatomic,assign) int partnerId;
@end

@implementation KalturaDistributionProfile
@synthesize id = _id;
@synthesize createdAt = _createdAt;
@synthesize updatedAt = _updatedAt;
@synthesize partnerId = _partnerId;
@synthesize providerType = _providerType;
@synthesize name = _name;
@synthesize status = _status;
@synthesize submitEnabled = _submitEnabled;
@synthesize updateEnabled = _updateEnabled;
@synthesize deleteEnabled = _deleteEnabled;
@synthesize reportEnabled = _reportEnabled;
@synthesize autoCreateFlavors = _autoCreateFlavors;
@synthesize autoCreateThumb = _autoCreateThumb;
@synthesize optionalFlavorParamsIds = _optionalFlavorParamsIds;
@synthesize requiredFlavorParamsIds = _requiredFlavorParamsIds;
@synthesize optionalThumbDimensions = _optionalThumbDimensions;
@synthesize requiredThumbDimensions = _requiredThumbDimensions;
@synthesize optionalAssetDistributionRules = _optionalAssetDistributionRules;
@synthesize requiredAssetDistributionRules = _requiredAssetDistributionRules;
@synthesize sunriseDefaultOffset = _sunriseDefaultOffset;
@synthesize sunsetDefaultOffset = _sunsetDefaultOffset;
@synthesize recommendedStorageProfileForDownload = _recommendedStorageProfileForDownload;
@synthesize recommendedDcForDownload = _recommendedDcForDownload;
@synthesize recommendedDcForExecute = _recommendedDcForExecute;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_id = KALTURA_UNDEF_INT;
    self->_createdAt = KALTURA_UNDEF_INT;
    self->_updatedAt = KALTURA_UNDEF_INT;
    self->_partnerId = KALTURA_UNDEF_INT;
    self->_status = KALTURA_UNDEF_INT;
    self->_submitEnabled = KALTURA_UNDEF_INT;
    self->_updateEnabled = KALTURA_UNDEF_INT;
    self->_deleteEnabled = KALTURA_UNDEF_INT;
    self->_reportEnabled = KALTURA_UNDEF_INT;
    self->_sunriseDefaultOffset = KALTURA_UNDEF_INT;
    self->_sunsetDefaultOffset = KALTURA_UNDEF_INT;
    self->_recommendedStorageProfileForDownload = KALTURA_UNDEF_INT;
    self->_recommendedDcForDownload = KALTURA_UNDEF_INT;
    self->_recommendedDcForExecute = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfProviderType
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatus
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfSubmitEnabled
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdateEnabled
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDeleteEnabled
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfReportEnabled
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfAutoCreateFlavors
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAutoCreateThumb
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfOptionalFlavorParamsIds
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfRequiredFlavorParamsIds
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfOptionalThumbDimensions
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfOptionalThumbDimensions
{
    return @"KalturaDistributionThumbDimensions";
}

- (KalturaFieldType)getTypeOfRequiredThumbDimensions
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfRequiredThumbDimensions
{
    return @"KalturaDistributionThumbDimensions";
}

- (KalturaFieldType)getTypeOfOptionalAssetDistributionRules
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfOptionalAssetDistributionRules
{
    return @"KalturaAssetDistributionRule";
}

- (KalturaFieldType)getTypeOfRequiredAssetDistributionRules
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfRequiredAssetDistributionRules
{
    return @"KalturaAssetDistributionRule";
}

- (KalturaFieldType)getTypeOfSunriseDefaultOffset
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfSunsetDefaultOffset
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfRecommendedStorageProfileForDownload
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfRecommendedDcForDownload
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfRecommendedDcForExecute
{
    return KFT_Int;
}

- (void)setIdFromString:(NSString*)aPropVal
{
    self.id = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtFromString:(NSString*)aPropVal
{
    self.createdAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtFromString:(NSString*)aPropVal
{
    self.updatedAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerIdFromString:(NSString*)aPropVal
{
    self.partnerId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusFromString:(NSString*)aPropVal
{
    self.status = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setSubmitEnabledFromString:(NSString*)aPropVal
{
    self.submitEnabled = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdateEnabledFromString:(NSString*)aPropVal
{
    self.updateEnabled = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDeleteEnabledFromString:(NSString*)aPropVal
{
    self.deleteEnabled = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setReportEnabledFromString:(NSString*)aPropVal
{
    self.reportEnabled = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setSunriseDefaultOffsetFromString:(NSString*)aPropVal
{
    self.sunriseDefaultOffset = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setSunsetDefaultOffsetFromString:(NSString*)aPropVal
{
    self.sunsetDefaultOffset = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setRecommendedStorageProfileForDownloadFromString:(NSString*)aPropVal
{
    self.recommendedStorageProfileForDownload = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setRecommendedDcForDownloadFromString:(NSString*)aPropVal
{
    self.recommendedDcForDownload = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setRecommendedDcForExecuteFromString:(NSString*)aPropVal
{
    self.recommendedDcForExecute = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDistributionProfile"];
    [aParams addIfDefinedKey:@"providerType" withString:self.providerType];
    [aParams addIfDefinedKey:@"name" withString:self.name];
    [aParams addIfDefinedKey:@"status" withInt:self.status];
    [aParams addIfDefinedKey:@"submitEnabled" withInt:self.submitEnabled];
    [aParams addIfDefinedKey:@"updateEnabled" withInt:self.updateEnabled];
    [aParams addIfDefinedKey:@"deleteEnabled" withInt:self.deleteEnabled];
    [aParams addIfDefinedKey:@"reportEnabled" withInt:self.reportEnabled];
    [aParams addIfDefinedKey:@"autoCreateFlavors" withString:self.autoCreateFlavors];
    [aParams addIfDefinedKey:@"autoCreateThumb" withString:self.autoCreateThumb];
    [aParams addIfDefinedKey:@"optionalFlavorParamsIds" withString:self.optionalFlavorParamsIds];
    [aParams addIfDefinedKey:@"requiredFlavorParamsIds" withString:self.requiredFlavorParamsIds];
    [aParams addIfDefinedKey:@"optionalThumbDimensions" withArray:self.optionalThumbDimensions];
    [aParams addIfDefinedKey:@"requiredThumbDimensions" withArray:self.requiredThumbDimensions];
    [aParams addIfDefinedKey:@"optionalAssetDistributionRules" withArray:self.optionalAssetDistributionRules];
    [aParams addIfDefinedKey:@"requiredAssetDistributionRules" withArray:self.requiredAssetDistributionRules];
    [aParams addIfDefinedKey:@"sunriseDefaultOffset" withInt:self.sunriseDefaultOffset];
    [aParams addIfDefinedKey:@"sunsetDefaultOffset" withInt:self.sunsetDefaultOffset];
    [aParams addIfDefinedKey:@"recommendedStorageProfileForDownload" withInt:self.recommendedStorageProfileForDownload];
    [aParams addIfDefinedKey:@"recommendedDcForDownload" withInt:self.recommendedDcForDownload];
    [aParams addIfDefinedKey:@"recommendedDcForExecute" withInt:self.recommendedDcForExecute];
}

- (void)dealloc
{
    [self->_providerType release];
    [self->_name release];
    [self->_autoCreateFlavors release];
    [self->_autoCreateThumb release];
    [self->_optionalFlavorParamsIds release];
    [self->_requiredFlavorParamsIds release];
    [self->_optionalThumbDimensions release];
    [self->_requiredThumbDimensions release];
    [self->_optionalAssetDistributionRules release];
    [self->_requiredAssetDistributionRules release];
    [super dealloc];
}

@end

@interface KalturaDistributionProfileListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaDistributionProfileListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaDistributionProfile";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDistributionProfileListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@interface KalturaDistributionProvider()
@property (nonatomic,copy) NSString* type;
@end

@implementation KalturaDistributionProvider
@synthesize type = _type;
@synthesize name = _name;
@synthesize scheduleUpdateEnabled = _scheduleUpdateEnabled;
@synthesize availabilityUpdateEnabled = _availabilityUpdateEnabled;
@synthesize deleteInsteadUpdate = _deleteInsteadUpdate;
@synthesize intervalBeforeSunrise = _intervalBeforeSunrise;
@synthesize intervalBeforeSunset = _intervalBeforeSunset;
@synthesize updateRequiredEntryFields = _updateRequiredEntryFields;
@synthesize updateRequiredMetadataXPaths = _updateRequiredMetadataXPaths;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_scheduleUpdateEnabled = KALTURA_UNDEF_BOOL;
    self->_availabilityUpdateEnabled = KALTURA_UNDEF_BOOL;
    self->_deleteInsteadUpdate = KALTURA_UNDEF_BOOL;
    self->_intervalBeforeSunrise = KALTURA_UNDEF_INT;
    self->_intervalBeforeSunset = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfType
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfScheduleUpdateEnabled
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfAvailabilityUpdateEnabled
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfDeleteInsteadUpdate
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfIntervalBeforeSunrise
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfIntervalBeforeSunset
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdateRequiredEntryFields
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfUpdateRequiredMetadataXPaths
{
    return KFT_String;
}

- (void)setScheduleUpdateEnabledFromString:(NSString*)aPropVal
{
    self.scheduleUpdateEnabled = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)setAvailabilityUpdateEnabledFromString:(NSString*)aPropVal
{
    self.availabilityUpdateEnabled = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)setDeleteInsteadUpdateFromString:(NSString*)aPropVal
{
    self.deleteInsteadUpdate = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)setIntervalBeforeSunriseFromString:(NSString*)aPropVal
{
    self.intervalBeforeSunrise = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setIntervalBeforeSunsetFromString:(NSString*)aPropVal
{
    self.intervalBeforeSunset = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDistributionProvider"];
    [aParams addIfDefinedKey:@"name" withString:self.name];
    [aParams addIfDefinedKey:@"scheduleUpdateEnabled" withBool:self.scheduleUpdateEnabled];
    [aParams addIfDefinedKey:@"availabilityUpdateEnabled" withBool:self.availabilityUpdateEnabled];
    [aParams addIfDefinedKey:@"deleteInsteadUpdate" withBool:self.deleteInsteadUpdate];
    [aParams addIfDefinedKey:@"intervalBeforeSunrise" withInt:self.intervalBeforeSunrise];
    [aParams addIfDefinedKey:@"intervalBeforeSunset" withInt:self.intervalBeforeSunset];
    [aParams addIfDefinedKey:@"updateRequiredEntryFields" withString:self.updateRequiredEntryFields];
    [aParams addIfDefinedKey:@"updateRequiredMetadataXPaths" withString:self.updateRequiredMetadataXPaths];
}

- (void)dealloc
{
    [self->_type release];
    [self->_name release];
    [self->_updateRequiredEntryFields release];
    [self->_updateRequiredMetadataXPaths release];
    [super dealloc];
}

@end

@interface KalturaDistributionProviderListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaDistributionProviderListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaDistributionProvider";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDistributionProviderListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@implementation KalturaDistributionRemoteMediaFile
@synthesize version = _version;
@synthesize assetId = _assetId;
@synthesize remoteId = _remoteId;

- (KalturaFieldType)getTypeOfVersion
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAssetId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfRemoteId
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDistributionRemoteMediaFile"];
    [aParams addIfDefinedKey:@"version" withString:self.version];
    [aParams addIfDefinedKey:@"assetId" withString:self.assetId];
    [aParams addIfDefinedKey:@"remoteId" withString:self.remoteId];
}

- (void)dealloc
{
    [self->_version release];
    [self->_assetId release];
    [self->_remoteId release];
    [super dealloc];
}

@end

@implementation KalturaDistributionValidationError
@synthesize action = _action;
@synthesize errorType = _errorType;
@synthesize description = _description;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_action = KALTURA_UNDEF_INT;
    self->_errorType = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfAction
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfErrorType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDescription
{
    return KFT_String;
}

- (void)setActionFromString:(NSString*)aPropVal
{
    self.action = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setErrorTypeFromString:(NSString*)aPropVal
{
    self.errorType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDistributionValidationError"];
    [aParams addIfDefinedKey:@"action" withInt:self.action];
    [aParams addIfDefinedKey:@"errorType" withInt:self.errorType];
    [aParams addIfDefinedKey:@"description" withString:self.description];
}

- (void)dealloc
{
    [self->_description release];
    [super dealloc];
}

@end

@interface KalturaEntryDistribution()
@property (nonatomic,assign) int id;
@property (nonatomic,assign) int createdAt;
@property (nonatomic,assign) int updatedAt;
@property (nonatomic,assign) int submittedAt;
@property (nonatomic,assign) int partnerId;
@property (nonatomic,assign) int status;
@property (nonatomic,assign) int sunStatus;
@property (nonatomic,assign) int dirtyStatus;
@property (nonatomic,copy) NSString* remoteId;
@property (nonatomic,assign) int plays;
@property (nonatomic,assign) int views;
@property (nonatomic,assign) int errorType;
@property (nonatomic,assign) int errorNumber;
@property (nonatomic,copy) NSString* errorDescription;
@property (nonatomic,assign) int hasSubmitResultsLog;
@property (nonatomic,assign) int hasSubmitSentDataLog;
@property (nonatomic,assign) int hasUpdateResultsLog;
@property (nonatomic,assign) int hasUpdateSentDataLog;
@property (nonatomic,assign) int hasDeleteResultsLog;
@property (nonatomic,assign) int hasDeleteSentDataLog;
@end

@implementation KalturaEntryDistribution
@synthesize id = _id;
@synthesize createdAt = _createdAt;
@synthesize updatedAt = _updatedAt;
@synthesize submittedAt = _submittedAt;
@synthesize entryId = _entryId;
@synthesize partnerId = _partnerId;
@synthesize distributionProfileId = _distributionProfileId;
@synthesize status = _status;
@synthesize sunStatus = _sunStatus;
@synthesize dirtyStatus = _dirtyStatus;
@synthesize thumbAssetIds = _thumbAssetIds;
@synthesize flavorAssetIds = _flavorAssetIds;
@synthesize assetIds = _assetIds;
@synthesize sunrise = _sunrise;
@synthesize sunset = _sunset;
@synthesize remoteId = _remoteId;
@synthesize plays = _plays;
@synthesize views = _views;
@synthesize validationErrors = _validationErrors;
@synthesize errorType = _errorType;
@synthesize errorNumber = _errorNumber;
@synthesize errorDescription = _errorDescription;
@synthesize hasSubmitResultsLog = _hasSubmitResultsLog;
@synthesize hasSubmitSentDataLog = _hasSubmitSentDataLog;
@synthesize hasUpdateResultsLog = _hasUpdateResultsLog;
@synthesize hasUpdateSentDataLog = _hasUpdateSentDataLog;
@synthesize hasDeleteResultsLog = _hasDeleteResultsLog;
@synthesize hasDeleteSentDataLog = _hasDeleteSentDataLog;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_id = KALTURA_UNDEF_INT;
    self->_createdAt = KALTURA_UNDEF_INT;
    self->_updatedAt = KALTURA_UNDEF_INT;
    self->_submittedAt = KALTURA_UNDEF_INT;
    self->_partnerId = KALTURA_UNDEF_INT;
    self->_distributionProfileId = KALTURA_UNDEF_INT;
    self->_status = KALTURA_UNDEF_INT;
    self->_sunStatus = KALTURA_UNDEF_INT;
    self->_dirtyStatus = KALTURA_UNDEF_INT;
    self->_sunrise = KALTURA_UNDEF_INT;
    self->_sunset = KALTURA_UNDEF_INT;
    self->_plays = KALTURA_UNDEF_INT;
    self->_views = KALTURA_UNDEF_INT;
    self->_errorType = KALTURA_UNDEF_INT;
    self->_errorNumber = KALTURA_UNDEF_INT;
    self->_hasSubmitResultsLog = KALTURA_UNDEF_INT;
    self->_hasSubmitSentDataLog = KALTURA_UNDEF_INT;
    self->_hasUpdateResultsLog = KALTURA_UNDEF_INT;
    self->_hasUpdateSentDataLog = KALTURA_UNDEF_INT;
    self->_hasDeleteResultsLog = KALTURA_UNDEF_INT;
    self->_hasDeleteSentDataLog = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfSubmittedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfEntryId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDistributionProfileId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStatus
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfSunStatus
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDirtyStatus
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfThumbAssetIds
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFlavorAssetIds
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAssetIds
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSunrise
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfSunset
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfRemoteId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPlays
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfViews
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfValidationErrors
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfValidationErrors
{
    return @"KalturaDistributionValidationError";
}

- (KalturaFieldType)getTypeOfErrorType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfErrorNumber
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfErrorDescription
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfHasSubmitResultsLog
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfHasSubmitSentDataLog
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfHasUpdateResultsLog
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfHasUpdateSentDataLog
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfHasDeleteResultsLog
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfHasDeleteSentDataLog
{
    return KFT_Int;
}

- (void)setIdFromString:(NSString*)aPropVal
{
    self.id = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtFromString:(NSString*)aPropVal
{
    self.createdAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtFromString:(NSString*)aPropVal
{
    self.updatedAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setSubmittedAtFromString:(NSString*)aPropVal
{
    self.submittedAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerIdFromString:(NSString*)aPropVal
{
    self.partnerId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDistributionProfileIdFromString:(NSString*)aPropVal
{
    self.distributionProfileId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusFromString:(NSString*)aPropVal
{
    self.status = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setSunStatusFromString:(NSString*)aPropVal
{
    self.sunStatus = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDirtyStatusFromString:(NSString*)aPropVal
{
    self.dirtyStatus = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setSunriseFromString:(NSString*)aPropVal
{
    self.sunrise = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setSunsetFromString:(NSString*)aPropVal
{
    self.sunset = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPlaysFromString:(NSString*)aPropVal
{
    self.plays = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setViewsFromString:(NSString*)aPropVal
{
    self.views = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setErrorTypeFromString:(NSString*)aPropVal
{
    self.errorType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setErrorNumberFromString:(NSString*)aPropVal
{
    self.errorNumber = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setHasSubmitResultsLogFromString:(NSString*)aPropVal
{
    self.hasSubmitResultsLog = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setHasSubmitSentDataLogFromString:(NSString*)aPropVal
{
    self.hasSubmitSentDataLog = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setHasUpdateResultsLogFromString:(NSString*)aPropVal
{
    self.hasUpdateResultsLog = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setHasUpdateSentDataLogFromString:(NSString*)aPropVal
{
    self.hasUpdateSentDataLog = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setHasDeleteResultsLogFromString:(NSString*)aPropVal
{
    self.hasDeleteResultsLog = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setHasDeleteSentDataLogFromString:(NSString*)aPropVal
{
    self.hasDeleteSentDataLog = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaEntryDistribution"];
    [aParams addIfDefinedKey:@"entryId" withString:self.entryId];
    [aParams addIfDefinedKey:@"distributionProfileId" withInt:self.distributionProfileId];
    [aParams addIfDefinedKey:@"thumbAssetIds" withString:self.thumbAssetIds];
    [aParams addIfDefinedKey:@"flavorAssetIds" withString:self.flavorAssetIds];
    [aParams addIfDefinedKey:@"assetIds" withString:self.assetIds];
    [aParams addIfDefinedKey:@"sunrise" withInt:self.sunrise];
    [aParams addIfDefinedKey:@"sunset" withInt:self.sunset];
    [aParams addIfDefinedKey:@"validationErrors" withArray:self.validationErrors];
}

- (void)dealloc
{
    [self->_entryId release];
    [self->_thumbAssetIds release];
    [self->_flavorAssetIds release];
    [self->_assetIds release];
    [self->_remoteId release];
    [self->_validationErrors release];
    [self->_errorDescription release];
    [super dealloc];
}

@end

@interface KalturaEntryDistributionListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaEntryDistributionListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaEntryDistribution";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaEntryDistributionListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@implementation KalturaGenericDistributionProfileAction
@synthesize protocol = _protocol;
@synthesize serverUrl = _serverUrl;
@synthesize serverPath = _serverPath;
@synthesize username = _username;
@synthesize password = _password;
@synthesize ftpPassiveMode = _ftpPassiveMode;
@synthesize httpFieldName = _httpFieldName;
@synthesize httpFileName = _httpFileName;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_protocol = KALTURA_UNDEF_INT;
    self->_ftpPassiveMode = KALTURA_UNDEF_BOOL;
    return self;
}

- (KalturaFieldType)getTypeOfProtocol
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfServerUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfServerPath
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfUsername
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPassword
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFtpPassiveMode
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfHttpFieldName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfHttpFileName
{
    return KFT_String;
}

- (void)setProtocolFromString:(NSString*)aPropVal
{
    self.protocol = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setFtpPassiveModeFromString:(NSString*)aPropVal
{
    self.ftpPassiveMode = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaGenericDistributionProfileAction"];
    [aParams addIfDefinedKey:@"protocol" withInt:self.protocol];
    [aParams addIfDefinedKey:@"serverUrl" withString:self.serverUrl];
    [aParams addIfDefinedKey:@"serverPath" withString:self.serverPath];
    [aParams addIfDefinedKey:@"username" withString:self.username];
    [aParams addIfDefinedKey:@"password" withString:self.password];
    [aParams addIfDefinedKey:@"ftpPassiveMode" withBool:self.ftpPassiveMode];
    [aParams addIfDefinedKey:@"httpFieldName" withString:self.httpFieldName];
    [aParams addIfDefinedKey:@"httpFileName" withString:self.httpFileName];
}

- (void)dealloc
{
    [self->_serverUrl release];
    [self->_serverPath release];
    [self->_username release];
    [self->_password release];
    [self->_httpFieldName release];
    [self->_httpFileName release];
    [super dealloc];
}

@end

@interface KalturaGenericDistributionProviderAction()
@property (nonatomic,assign) int id;
@property (nonatomic,assign) int createdAt;
@property (nonatomic,assign) int updatedAt;
@property (nonatomic,assign) int status;
@property (nonatomic,copy) NSString* mrssTransformer;
@property (nonatomic,copy) NSString* mrssValidator;
@property (nonatomic,copy) NSString* resultsTransformer;
@end

@implementation KalturaGenericDistributionProviderAction
@synthesize id = _id;
@synthesize createdAt = _createdAt;
@synthesize updatedAt = _updatedAt;
@synthesize genericDistributionProviderId = _genericDistributionProviderId;
@synthesize action = _action;
@synthesize status = _status;
@synthesize resultsParser = _resultsParser;
@synthesize protocol = _protocol;
@synthesize serverAddress = _serverAddress;
@synthesize remotePath = _remotePath;
@synthesize remoteUsername = _remoteUsername;
@synthesize remotePassword = _remotePassword;
@synthesize editableFields = _editableFields;
@synthesize mandatoryFields = _mandatoryFields;
@synthesize mrssTransformer = _mrssTransformer;
@synthesize mrssValidator = _mrssValidator;
@synthesize resultsTransformer = _resultsTransformer;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_id = KALTURA_UNDEF_INT;
    self->_createdAt = KALTURA_UNDEF_INT;
    self->_updatedAt = KALTURA_UNDEF_INT;
    self->_genericDistributionProviderId = KALTURA_UNDEF_INT;
    self->_action = KALTURA_UNDEF_INT;
    self->_status = KALTURA_UNDEF_INT;
    self->_resultsParser = KALTURA_UNDEF_INT;
    self->_protocol = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfGenericDistributionProviderId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfAction
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStatus
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfResultsParser
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfProtocol
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfServerAddress
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfRemotePath
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfRemoteUsername
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfRemotePassword
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEditableFields
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfMandatoryFields
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfMrssTransformer
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfMrssValidator
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfResultsTransformer
{
    return KFT_String;
}

- (void)setIdFromString:(NSString*)aPropVal
{
    self.id = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtFromString:(NSString*)aPropVal
{
    self.createdAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtFromString:(NSString*)aPropVal
{
    self.updatedAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setGenericDistributionProviderIdFromString:(NSString*)aPropVal
{
    self.genericDistributionProviderId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setActionFromString:(NSString*)aPropVal
{
    self.action = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusFromString:(NSString*)aPropVal
{
    self.status = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setResultsParserFromString:(NSString*)aPropVal
{
    self.resultsParser = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setProtocolFromString:(NSString*)aPropVal
{
    self.protocol = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaGenericDistributionProviderAction"];
    [aParams addIfDefinedKey:@"genericDistributionProviderId" withInt:self.genericDistributionProviderId];
    [aParams addIfDefinedKey:@"action" withInt:self.action];
    [aParams addIfDefinedKey:@"resultsParser" withInt:self.resultsParser];
    [aParams addIfDefinedKey:@"protocol" withInt:self.protocol];
    [aParams addIfDefinedKey:@"serverAddress" withString:self.serverAddress];
    [aParams addIfDefinedKey:@"remotePath" withString:self.remotePath];
    [aParams addIfDefinedKey:@"remoteUsername" withString:self.remoteUsername];
    [aParams addIfDefinedKey:@"remotePassword" withString:self.remotePassword];
    [aParams addIfDefinedKey:@"editableFields" withString:self.editableFields];
    [aParams addIfDefinedKey:@"mandatoryFields" withString:self.mandatoryFields];
}

- (void)dealloc
{
    [self->_serverAddress release];
    [self->_remotePath release];
    [self->_remoteUsername release];
    [self->_remotePassword release];
    [self->_editableFields release];
    [self->_mandatoryFields release];
    [self->_mrssTransformer release];
    [self->_mrssValidator release];
    [self->_resultsTransformer release];
    [super dealloc];
}

@end

@interface KalturaGenericDistributionProviderActionListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaGenericDistributionProviderActionListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaGenericDistributionProviderAction";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaGenericDistributionProviderActionListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@interface KalturaGenericDistributionProvider()
@property (nonatomic,assign) int id;
@property (nonatomic,assign) int createdAt;
@property (nonatomic,assign) int updatedAt;
@property (nonatomic,assign) int partnerId;
@property (nonatomic,assign) int status;
@end

@implementation KalturaGenericDistributionProvider
@synthesize id = _id;
@synthesize createdAt = _createdAt;
@synthesize updatedAt = _updatedAt;
@synthesize partnerId = _partnerId;
@synthesize isDefault = _isDefault;
@synthesize status = _status;
@synthesize optionalFlavorParamsIds = _optionalFlavorParamsIds;
@synthesize requiredFlavorParamsIds = _requiredFlavorParamsIds;
@synthesize optionalThumbDimensions = _optionalThumbDimensions;
@synthesize requiredThumbDimensions = _requiredThumbDimensions;
@synthesize editableFields = _editableFields;
@synthesize mandatoryFields = _mandatoryFields;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_id = KALTURA_UNDEF_INT;
    self->_createdAt = KALTURA_UNDEF_INT;
    self->_updatedAt = KALTURA_UNDEF_INT;
    self->_partnerId = KALTURA_UNDEF_INT;
    self->_isDefault = KALTURA_UNDEF_BOOL;
    self->_status = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfIsDefault
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfStatus
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfOptionalFlavorParamsIds
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfRequiredFlavorParamsIds
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfOptionalThumbDimensions
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfOptionalThumbDimensions
{
    return @"KalturaDistributionThumbDimensions";
}

- (KalturaFieldType)getTypeOfRequiredThumbDimensions
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfRequiredThumbDimensions
{
    return @"KalturaDistributionThumbDimensions";
}

- (KalturaFieldType)getTypeOfEditableFields
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfMandatoryFields
{
    return KFT_String;
}

- (void)setIdFromString:(NSString*)aPropVal
{
    self.id = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtFromString:(NSString*)aPropVal
{
    self.createdAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtFromString:(NSString*)aPropVal
{
    self.updatedAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerIdFromString:(NSString*)aPropVal
{
    self.partnerId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setIsDefaultFromString:(NSString*)aPropVal
{
    self.isDefault = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)setStatusFromString:(NSString*)aPropVal
{
    self.status = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaGenericDistributionProvider"];
    [aParams addIfDefinedKey:@"isDefault" withBool:self.isDefault];
    [aParams addIfDefinedKey:@"optionalFlavorParamsIds" withString:self.optionalFlavorParamsIds];
    [aParams addIfDefinedKey:@"requiredFlavorParamsIds" withString:self.requiredFlavorParamsIds];
    [aParams addIfDefinedKey:@"optionalThumbDimensions" withArray:self.optionalThumbDimensions];
    [aParams addIfDefinedKey:@"requiredThumbDimensions" withArray:self.requiredThumbDimensions];
    [aParams addIfDefinedKey:@"editableFields" withString:self.editableFields];
    [aParams addIfDefinedKey:@"mandatoryFields" withString:self.mandatoryFields];
}

- (void)dealloc
{
    [self->_optionalFlavorParamsIds release];
    [self->_requiredFlavorParamsIds release];
    [self->_optionalThumbDimensions release];
    [self->_requiredThumbDimensions release];
    [self->_editableFields release];
    [self->_mandatoryFields release];
    [super dealloc];
}

@end

@interface KalturaGenericDistributionProviderListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaGenericDistributionProviderListResponse
@synthesize objects = _objects;
@synthesize totalCount = _totalCount;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_totalCount = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfObjects
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfObjects
{
    return @"KalturaGenericDistributionProvider";
}

- (KalturaFieldType)getTypeOfTotalCount
{
    return KFT_Int;
}

- (void)setTotalCountFromString:(NSString*)aPropVal
{
    self.totalCount = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaGenericDistributionProviderListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@implementation KalturaAssetDistributionPropertyCondition
@synthesize propertyName = _propertyName;
@synthesize propertyValue = _propertyValue;

- (KalturaFieldType)getTypeOfPropertyName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPropertyValue
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAssetDistributionPropertyCondition"];
    [aParams addIfDefinedKey:@"propertyName" withString:self.propertyName];
    [aParams addIfDefinedKey:@"propertyValue" withString:self.propertyValue];
}

- (void)dealloc
{
    [self->_propertyName release];
    [self->_propertyValue release];
    [super dealloc];
}

@end

@implementation KalturaConfigurableDistributionJobProviderData
@synthesize fieldValues = _fieldValues;

- (KalturaFieldType)getTypeOfFieldValues
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaConfigurableDistributionJobProviderData"];
    [aParams addIfDefinedKey:@"fieldValues" withString:self.fieldValues];
}

- (void)dealloc
{
    [self->_fieldValues release];
    [super dealloc];
}

@end

@implementation KalturaConfigurableDistributionProfile
@synthesize fieldConfigArray = _fieldConfigArray;
@synthesize itemXpathsToExtend = _itemXpathsToExtend;

- (KalturaFieldType)getTypeOfFieldConfigArray
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfFieldConfigArray
{
    return @"KalturaDistributionFieldConfig";
}

- (KalturaFieldType)getTypeOfItemXpathsToExtend
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfItemXpathsToExtend
{
    return @"KalturaExtendingItemMrssParameter";
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaConfigurableDistributionProfile"];
    [aParams addIfDefinedKey:@"fieldConfigArray" withArray:self.fieldConfigArray];
    [aParams addIfDefinedKey:@"itemXpathsToExtend" withArray:self.itemXpathsToExtend];
}

- (void)dealloc
{
    [self->_fieldConfigArray release];
    [self->_itemXpathsToExtend release];
    [super dealloc];
}

@end

@implementation KalturaContentDistributionSearchItem
@synthesize noDistributionProfiles = _noDistributionProfiles;
@synthesize distributionProfileId = _distributionProfileId;
@synthesize distributionSunStatus = _distributionSunStatus;
@synthesize entryDistributionFlag = _entryDistributionFlag;
@synthesize entryDistributionStatus = _entryDistributionStatus;
@synthesize hasEntryDistributionValidationErrors = _hasEntryDistributionValidationErrors;
@synthesize entryDistributionValidationErrors = _entryDistributionValidationErrors;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_noDistributionProfiles = KALTURA_UNDEF_BOOL;
    self->_distributionProfileId = KALTURA_UNDEF_INT;
    self->_distributionSunStatus = KALTURA_UNDEF_INT;
    self->_entryDistributionFlag = KALTURA_UNDEF_INT;
    self->_entryDistributionStatus = KALTURA_UNDEF_INT;
    self->_hasEntryDistributionValidationErrors = KALTURA_UNDEF_BOOL;
    return self;
}

- (KalturaFieldType)getTypeOfNoDistributionProfiles
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfDistributionProfileId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDistributionSunStatus
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfEntryDistributionFlag
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfEntryDistributionStatus
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfHasEntryDistributionValidationErrors
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfEntryDistributionValidationErrors
{
    return KFT_String;
}

- (void)setNoDistributionProfilesFromString:(NSString*)aPropVal
{
    self.noDistributionProfiles = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)setDistributionProfileIdFromString:(NSString*)aPropVal
{
    self.distributionProfileId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDistributionSunStatusFromString:(NSString*)aPropVal
{
    self.distributionSunStatus = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setEntryDistributionFlagFromString:(NSString*)aPropVal
{
    self.entryDistributionFlag = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setEntryDistributionStatusFromString:(NSString*)aPropVal
{
    self.entryDistributionStatus = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setHasEntryDistributionValidationErrorsFromString:(NSString*)aPropVal
{
    self.hasEntryDistributionValidationErrors = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaContentDistributionSearchItem"];
    [aParams addIfDefinedKey:@"noDistributionProfiles" withBool:self.noDistributionProfiles];
    [aParams addIfDefinedKey:@"distributionProfileId" withInt:self.distributionProfileId];
    [aParams addIfDefinedKey:@"distributionSunStatus" withInt:self.distributionSunStatus];
    [aParams addIfDefinedKey:@"entryDistributionFlag" withInt:self.entryDistributionFlag];
    [aParams addIfDefinedKey:@"entryDistributionStatus" withInt:self.entryDistributionStatus];
    [aParams addIfDefinedKey:@"hasEntryDistributionValidationErrors" withBool:self.hasEntryDistributionValidationErrors];
    [aParams addIfDefinedKey:@"entryDistributionValidationErrors" withString:self.entryDistributionValidationErrors];
}

- (void)dealloc
{
    [self->_entryDistributionValidationErrors release];
    [super dealloc];
}

@end

@implementation KalturaDistributionJobData
@synthesize distributionProfileId = _distributionProfileId;
@synthesize distributionProfile = _distributionProfile;
@synthesize entryDistributionId = _entryDistributionId;
@synthesize entryDistribution = _entryDistribution;
@synthesize remoteId = _remoteId;
@synthesize providerType = _providerType;
@synthesize providerData = _providerData;
@synthesize results = _results;
@synthesize sentData = _sentData;
@synthesize mediaFiles = _mediaFiles;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_distributionProfileId = KALTURA_UNDEF_INT;
    self->_entryDistributionId = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfDistributionProfileId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDistributionProfile
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfDistributionProfile
{
    return @"KalturaDistributionProfile";
}

- (KalturaFieldType)getTypeOfEntryDistributionId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfEntryDistribution
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfEntryDistribution
{
    return @"KalturaEntryDistribution";
}

- (KalturaFieldType)getTypeOfRemoteId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfProviderType
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfProviderData
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfProviderData
{
    return @"KalturaDistributionJobProviderData";
}

- (KalturaFieldType)getTypeOfResults
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSentData
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfMediaFiles
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfMediaFiles
{
    return @"KalturaDistributionRemoteMediaFile";
}

- (void)setDistributionProfileIdFromString:(NSString*)aPropVal
{
    self.distributionProfileId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setEntryDistributionIdFromString:(NSString*)aPropVal
{
    self.entryDistributionId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDistributionJobData"];
    [aParams addIfDefinedKey:@"distributionProfileId" withInt:self.distributionProfileId];
    [aParams addIfDefinedKey:@"distributionProfile" withObject:self.distributionProfile];
    [aParams addIfDefinedKey:@"entryDistributionId" withInt:self.entryDistributionId];
    [aParams addIfDefinedKey:@"entryDistribution" withObject:self.entryDistribution];
    [aParams addIfDefinedKey:@"remoteId" withString:self.remoteId];
    [aParams addIfDefinedKey:@"providerType" withString:self.providerType];
    [aParams addIfDefinedKey:@"providerData" withObject:self.providerData];
    [aParams addIfDefinedKey:@"results" withString:self.results];
    [aParams addIfDefinedKey:@"sentData" withString:self.sentData];
    [aParams addIfDefinedKey:@"mediaFiles" withArray:self.mediaFiles];
}

- (void)dealloc
{
    [self->_distributionProfile release];
    [self->_entryDistribution release];
    [self->_remoteId release];
    [self->_providerType release];
    [self->_providerData release];
    [self->_results release];
    [self->_sentData release];
    [self->_mediaFiles release];
    [super dealloc];
}

@end

@implementation KalturaDistributionProfileBaseFilter
@synthesize idEqual = _idEqual;
@synthesize idIn = _idIn;
@synthesize createdAtGreaterThanOrEqual = _createdAtGreaterThanOrEqual;
@synthesize createdAtLessThanOrEqual = _createdAtLessThanOrEqual;
@synthesize updatedAtGreaterThanOrEqual = _updatedAtGreaterThanOrEqual;
@synthesize updatedAtLessThanOrEqual = _updatedAtLessThanOrEqual;
@synthesize statusEqual = _statusEqual;
@synthesize statusIn = _statusIn;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_idEqual = KALTURA_UNDEF_INT;
    self->_createdAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_createdAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_statusEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStatusEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStatusIn
{
    return KFT_String;
}

- (void)setIdEqualFromString:(NSString*)aPropVal
{
    self.idEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusEqualFromString:(NSString*)aPropVal
{
    self.statusEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDistributionProfileBaseFilter"];
    [aParams addIfDefinedKey:@"idEqual" withInt:self.idEqual];
    [aParams addIfDefinedKey:@"idIn" withString:self.idIn];
    [aParams addIfDefinedKey:@"createdAtGreaterThanOrEqual" withInt:self.createdAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"createdAtLessThanOrEqual" withInt:self.createdAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtGreaterThanOrEqual" withInt:self.updatedAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtLessThanOrEqual" withInt:self.updatedAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"statusEqual" withInt:self.statusEqual];
    [aParams addIfDefinedKey:@"statusIn" withString:self.statusIn];
}

- (void)dealloc
{
    [self->_idIn release];
    [self->_statusIn release];
    [super dealloc];
}

@end

@implementation KalturaDistributionProviderBaseFilter
@synthesize typeEqual = _typeEqual;
@synthesize typeIn = _typeIn;

- (KalturaFieldType)getTypeOfTypeEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTypeIn
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDistributionProviderBaseFilter"];
    [aParams addIfDefinedKey:@"typeEqual" withString:self.typeEqual];
    [aParams addIfDefinedKey:@"typeIn" withString:self.typeIn];
}

- (void)dealloc
{
    [self->_typeEqual release];
    [self->_typeIn release];
    [super dealloc];
}

@end

@implementation KalturaDistributionValidationErrorInvalidData
@synthesize fieldName = _fieldName;
@synthesize validationErrorType = _validationErrorType;
@synthesize validationErrorParam = _validationErrorParam;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_validationErrorType = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfFieldName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfValidationErrorType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfValidationErrorParam
{
    return KFT_String;
}

- (void)setValidationErrorTypeFromString:(NSString*)aPropVal
{
    self.validationErrorType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDistributionValidationErrorInvalidData"];
    [aParams addIfDefinedKey:@"fieldName" withString:self.fieldName];
    [aParams addIfDefinedKey:@"validationErrorType" withInt:self.validationErrorType];
    [aParams addIfDefinedKey:@"validationErrorParam" withString:self.validationErrorParam];
}

- (void)dealloc
{
    [self->_fieldName release];
    [self->_validationErrorParam release];
    [super dealloc];
}

@end

@implementation KalturaDistributionValidationErrorMissingAsset
@synthesize data = _data;

- (KalturaFieldType)getTypeOfData
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDistributionValidationErrorMissingAsset"];
    [aParams addIfDefinedKey:@"data" withString:self.data];
}

- (void)dealloc
{
    [self->_data release];
    [super dealloc];
}

@end

@implementation KalturaDistributionValidationErrorMissingFlavor
@synthesize flavorParamsId = _flavorParamsId;

- (KalturaFieldType)getTypeOfFlavorParamsId
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDistributionValidationErrorMissingFlavor"];
    [aParams addIfDefinedKey:@"flavorParamsId" withString:self.flavorParamsId];
}

- (void)dealloc
{
    [self->_flavorParamsId release];
    [super dealloc];
}

@end

@implementation KalturaDistributionValidationErrorMissingMetadata
@synthesize fieldName = _fieldName;

- (KalturaFieldType)getTypeOfFieldName
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDistributionValidationErrorMissingMetadata"];
    [aParams addIfDefinedKey:@"fieldName" withString:self.fieldName];
}

- (void)dealloc
{
    [self->_fieldName release];
    [super dealloc];
}

@end

@implementation KalturaDistributionValidationErrorMissingThumbnail
@synthesize dimensions = _dimensions;

- (KalturaFieldType)getTypeOfDimensions
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfDimensions
{
    return @"KalturaDistributionThumbDimensions";
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDistributionValidationErrorMissingThumbnail"];
    [aParams addIfDefinedKey:@"dimensions" withObject:self.dimensions];
}

- (void)dealloc
{
    [self->_dimensions release];
    [super dealloc];
}

@end

@implementation KalturaEntryDistributionBaseFilter
@synthesize idEqual = _idEqual;
@synthesize idIn = _idIn;
@synthesize createdAtGreaterThanOrEqual = _createdAtGreaterThanOrEqual;
@synthesize createdAtLessThanOrEqual = _createdAtLessThanOrEqual;
@synthesize updatedAtGreaterThanOrEqual = _updatedAtGreaterThanOrEqual;
@synthesize updatedAtLessThanOrEqual = _updatedAtLessThanOrEqual;
@synthesize submittedAtGreaterThanOrEqual = _submittedAtGreaterThanOrEqual;
@synthesize submittedAtLessThanOrEqual = _submittedAtLessThanOrEqual;
@synthesize entryIdEqual = _entryIdEqual;
@synthesize entryIdIn = _entryIdIn;
@synthesize distributionProfileIdEqual = _distributionProfileIdEqual;
@synthesize distributionProfileIdIn = _distributionProfileIdIn;
@synthesize statusEqual = _statusEqual;
@synthesize statusIn = _statusIn;
@synthesize dirtyStatusEqual = _dirtyStatusEqual;
@synthesize dirtyStatusIn = _dirtyStatusIn;
@synthesize sunriseGreaterThanOrEqual = _sunriseGreaterThanOrEqual;
@synthesize sunriseLessThanOrEqual = _sunriseLessThanOrEqual;
@synthesize sunsetGreaterThanOrEqual = _sunsetGreaterThanOrEqual;
@synthesize sunsetLessThanOrEqual = _sunsetLessThanOrEqual;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_idEqual = KALTURA_UNDEF_INT;
    self->_createdAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_createdAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_submittedAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_submittedAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_distributionProfileIdEqual = KALTURA_UNDEF_INT;
    self->_statusEqual = KALTURA_UNDEF_INT;
    self->_dirtyStatusEqual = KALTURA_UNDEF_INT;
    self->_sunriseGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_sunriseLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_sunsetGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_sunsetLessThanOrEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfSubmittedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfSubmittedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfEntryIdEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEntryIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDistributionProfileIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDistributionProfileIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatusEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStatusIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDirtyStatusEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDirtyStatusIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSunriseGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfSunriseLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfSunsetGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfSunsetLessThanOrEqual
{
    return KFT_Int;
}

- (void)setIdEqualFromString:(NSString*)aPropVal
{
    self.idEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setSubmittedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.submittedAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setSubmittedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.submittedAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDistributionProfileIdEqualFromString:(NSString*)aPropVal
{
    self.distributionProfileIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusEqualFromString:(NSString*)aPropVal
{
    self.statusEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDirtyStatusEqualFromString:(NSString*)aPropVal
{
    self.dirtyStatusEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setSunriseGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.sunriseGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setSunriseLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.sunriseLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setSunsetGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.sunsetGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setSunsetLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.sunsetLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaEntryDistributionBaseFilter"];
    [aParams addIfDefinedKey:@"idEqual" withInt:self.idEqual];
    [aParams addIfDefinedKey:@"idIn" withString:self.idIn];
    [aParams addIfDefinedKey:@"createdAtGreaterThanOrEqual" withInt:self.createdAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"createdAtLessThanOrEqual" withInt:self.createdAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtGreaterThanOrEqual" withInt:self.updatedAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtLessThanOrEqual" withInt:self.updatedAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"submittedAtGreaterThanOrEqual" withInt:self.submittedAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"submittedAtLessThanOrEqual" withInt:self.submittedAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"entryIdEqual" withString:self.entryIdEqual];
    [aParams addIfDefinedKey:@"entryIdIn" withString:self.entryIdIn];
    [aParams addIfDefinedKey:@"distributionProfileIdEqual" withInt:self.distributionProfileIdEqual];
    [aParams addIfDefinedKey:@"distributionProfileIdIn" withString:self.distributionProfileIdIn];
    [aParams addIfDefinedKey:@"statusEqual" withInt:self.statusEqual];
    [aParams addIfDefinedKey:@"statusIn" withString:self.statusIn];
    [aParams addIfDefinedKey:@"dirtyStatusEqual" withInt:self.dirtyStatusEqual];
    [aParams addIfDefinedKey:@"dirtyStatusIn" withString:self.dirtyStatusIn];
    [aParams addIfDefinedKey:@"sunriseGreaterThanOrEqual" withInt:self.sunriseGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"sunriseLessThanOrEqual" withInt:self.sunriseLessThanOrEqual];
    [aParams addIfDefinedKey:@"sunsetGreaterThanOrEqual" withInt:self.sunsetGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"sunsetLessThanOrEqual" withInt:self.sunsetLessThanOrEqual];
}

- (void)dealloc
{
    [self->_idIn release];
    [self->_entryIdEqual release];
    [self->_entryIdIn release];
    [self->_distributionProfileIdIn release];
    [self->_statusIn release];
    [self->_dirtyStatusIn release];
    [super dealloc];
}

@end

@implementation KalturaGenericDistributionJobProviderData
@synthesize xml = _xml;
@synthesize resultParseData = _resultParseData;
@synthesize resultParserType = _resultParserType;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_resultParserType = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfXml
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfResultParseData
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfResultParserType
{
    return KFT_Int;
}

- (void)setResultParserTypeFromString:(NSString*)aPropVal
{
    self.resultParserType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaGenericDistributionJobProviderData"];
    [aParams addIfDefinedKey:@"xml" withString:self.xml];
    [aParams addIfDefinedKey:@"resultParseData" withString:self.resultParseData];
    [aParams addIfDefinedKey:@"resultParserType" withInt:self.resultParserType];
}

- (void)dealloc
{
    [self->_xml release];
    [self->_resultParseData release];
    [super dealloc];
}

@end

@implementation KalturaGenericDistributionProfile
@synthesize genericProviderId = _genericProviderId;
@synthesize submitAction = _submitAction;
@synthesize updateAction = _updateAction;
@synthesize deleteAction = _deleteAction;
@synthesize fetchReportAction = _fetchReportAction;
@synthesize updateRequiredEntryFields = _updateRequiredEntryFields;
@synthesize updateRequiredMetadataXPaths = _updateRequiredMetadataXPaths;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_genericProviderId = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfGenericProviderId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfSubmitAction
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfSubmitAction
{
    return @"KalturaGenericDistributionProfileAction";
}

- (KalturaFieldType)getTypeOfUpdateAction
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfUpdateAction
{
    return @"KalturaGenericDistributionProfileAction";
}

- (KalturaFieldType)getTypeOfDeleteAction
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfDeleteAction
{
    return @"KalturaGenericDistributionProfileAction";
}

- (KalturaFieldType)getTypeOfFetchReportAction
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfFetchReportAction
{
    return @"KalturaGenericDistributionProfileAction";
}

- (KalturaFieldType)getTypeOfUpdateRequiredEntryFields
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfUpdateRequiredMetadataXPaths
{
    return KFT_String;
}

- (void)setGenericProviderIdFromString:(NSString*)aPropVal
{
    self.genericProviderId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaGenericDistributionProfile"];
    [aParams addIfDefinedKey:@"genericProviderId" withInt:self.genericProviderId];
    [aParams addIfDefinedKey:@"submitAction" withObject:self.submitAction];
    [aParams addIfDefinedKey:@"updateAction" withObject:self.updateAction];
    [aParams addIfDefinedKey:@"deleteAction" withObject:self.deleteAction];
    [aParams addIfDefinedKey:@"fetchReportAction" withObject:self.fetchReportAction];
    [aParams addIfDefinedKey:@"updateRequiredEntryFields" withString:self.updateRequiredEntryFields];
    [aParams addIfDefinedKey:@"updateRequiredMetadataXPaths" withString:self.updateRequiredMetadataXPaths];
}

- (void)dealloc
{
    [self->_submitAction release];
    [self->_updateAction release];
    [self->_deleteAction release];
    [self->_fetchReportAction release];
    [self->_updateRequiredEntryFields release];
    [self->_updateRequiredMetadataXPaths release];
    [super dealloc];
}

@end

@implementation KalturaGenericDistributionProviderActionBaseFilter
@synthesize idEqual = _idEqual;
@synthesize idIn = _idIn;
@synthesize createdAtGreaterThanOrEqual = _createdAtGreaterThanOrEqual;
@synthesize createdAtLessThanOrEqual = _createdAtLessThanOrEqual;
@synthesize updatedAtGreaterThanOrEqual = _updatedAtGreaterThanOrEqual;
@synthesize updatedAtLessThanOrEqual = _updatedAtLessThanOrEqual;
@synthesize genericDistributionProviderIdEqual = _genericDistributionProviderIdEqual;
@synthesize genericDistributionProviderIdIn = _genericDistributionProviderIdIn;
@synthesize actionEqual = _actionEqual;
@synthesize actionIn = _actionIn;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_idEqual = KALTURA_UNDEF_INT;
    self->_createdAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_createdAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_genericDistributionProviderIdEqual = KALTURA_UNDEF_INT;
    self->_actionEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfGenericDistributionProviderIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfGenericDistributionProviderIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfActionEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfActionIn
{
    return KFT_String;
}

- (void)setIdEqualFromString:(NSString*)aPropVal
{
    self.idEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setGenericDistributionProviderIdEqualFromString:(NSString*)aPropVal
{
    self.genericDistributionProviderIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setActionEqualFromString:(NSString*)aPropVal
{
    self.actionEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaGenericDistributionProviderActionBaseFilter"];
    [aParams addIfDefinedKey:@"idEqual" withInt:self.idEqual];
    [aParams addIfDefinedKey:@"idIn" withString:self.idIn];
    [aParams addIfDefinedKey:@"createdAtGreaterThanOrEqual" withInt:self.createdAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"createdAtLessThanOrEqual" withInt:self.createdAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtGreaterThanOrEqual" withInt:self.updatedAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtLessThanOrEqual" withInt:self.updatedAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"genericDistributionProviderIdEqual" withInt:self.genericDistributionProviderIdEqual];
    [aParams addIfDefinedKey:@"genericDistributionProviderIdIn" withString:self.genericDistributionProviderIdIn];
    [aParams addIfDefinedKey:@"actionEqual" withInt:self.actionEqual];
    [aParams addIfDefinedKey:@"actionIn" withString:self.actionIn];
}

- (void)dealloc
{
    [self->_idIn release];
    [self->_genericDistributionProviderIdIn release];
    [self->_actionIn release];
    [super dealloc];
}

@end

@interface KalturaSyndicationDistributionProfile()
@property (nonatomic,copy) NSString* feedId;
@end

@implementation KalturaSyndicationDistributionProfile
@synthesize xsl = _xsl;
@synthesize feedId = _feedId;

- (KalturaFieldType)getTypeOfXsl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFeedId
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaSyndicationDistributionProfile"];
    [aParams addIfDefinedKey:@"xsl" withString:self.xsl];
}

- (void)dealloc
{
    [self->_xsl release];
    [self->_feedId release];
    [super dealloc];
}

@end

@implementation KalturaSyndicationDistributionProvider
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaSyndicationDistributionProvider"];
}

@end

@implementation KalturaDistributionDeleteJobData
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDistributionDeleteJobData"];
}

@end

@implementation KalturaDistributionFetchReportJobData
@synthesize plays = _plays;
@synthesize views = _views;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_plays = KALTURA_UNDEF_INT;
    self->_views = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfPlays
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfViews
{
    return KFT_Int;
}

- (void)setPlaysFromString:(NSString*)aPropVal
{
    self.plays = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setViewsFromString:(NSString*)aPropVal
{
    self.views = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDistributionFetchReportJobData"];
    [aParams addIfDefinedKey:@"plays" withInt:self.plays];
    [aParams addIfDefinedKey:@"views" withInt:self.views];
}

@end

@implementation KalturaDistributionProfileFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDistributionProfileFilter"];
}

@end

@implementation KalturaDistributionProviderFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDistributionProviderFilter"];
}

@end

@implementation KalturaDistributionSubmitJobData
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDistributionSubmitJobData"];
}

@end

@implementation KalturaDistributionUpdateJobData
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDistributionUpdateJobData"];
}

@end

@implementation KalturaDistributionValidationErrorInvalidMetadata
@synthesize metadataProfileId = _metadataProfileId;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_metadataProfileId = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfMetadataProfileId
{
    return KFT_Int;
}

- (void)setMetadataProfileIdFromString:(NSString*)aPropVal
{
    self.metadataProfileId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDistributionValidationErrorInvalidMetadata"];
    [aParams addIfDefinedKey:@"metadataProfileId" withInt:self.metadataProfileId];
}

@end

@implementation KalturaEntryDistributionFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaEntryDistributionFilter"];
}

@end

@implementation KalturaGenericDistributionProviderActionFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaGenericDistributionProviderActionFilter"];
}

@end

@implementation KalturaConfigurableDistributionProfileBaseFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaConfigurableDistributionProfileBaseFilter"];
}

@end

@implementation KalturaDistributionDisableJobData
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDistributionDisableJobData"];
}

@end

@implementation KalturaDistributionEnableJobData
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDistributionEnableJobData"];
}

@end

@implementation KalturaGenericDistributionProfileBaseFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaGenericDistributionProfileBaseFilter"];
}

@end

@implementation KalturaGenericDistributionProviderBaseFilter
@synthesize idEqual = _idEqual;
@synthesize idIn = _idIn;
@synthesize createdAtGreaterThanOrEqual = _createdAtGreaterThanOrEqual;
@synthesize createdAtLessThanOrEqual = _createdAtLessThanOrEqual;
@synthesize updatedAtGreaterThanOrEqual = _updatedAtGreaterThanOrEqual;
@synthesize updatedAtLessThanOrEqual = _updatedAtLessThanOrEqual;
@synthesize partnerIdEqual = _partnerIdEqual;
@synthesize partnerIdIn = _partnerIdIn;
@synthesize isDefaultEqual = _isDefaultEqual;
@synthesize isDefaultIn = _isDefaultIn;
@synthesize statusEqual = _statusEqual;
@synthesize statusIn = _statusIn;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_idEqual = KALTURA_UNDEF_INT;
    self->_createdAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_createdAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_partnerIdEqual = KALTURA_UNDEF_INT;
    self->_isDefaultEqual = KALTURA_UNDEF_INT;
    self->_statusEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfIsDefaultEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfIsDefaultIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatusEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStatusIn
{
    return KFT_String;
}

- (void)setIdEqualFromString:(NSString*)aPropVal
{
    self.idEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerIdEqualFromString:(NSString*)aPropVal
{
    self.partnerIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setIsDefaultEqualFromString:(NSString*)aPropVal
{
    self.isDefaultEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusEqualFromString:(NSString*)aPropVal
{
    self.statusEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaGenericDistributionProviderBaseFilter"];
    [aParams addIfDefinedKey:@"idEqual" withInt:self.idEqual];
    [aParams addIfDefinedKey:@"idIn" withString:self.idIn];
    [aParams addIfDefinedKey:@"createdAtGreaterThanOrEqual" withInt:self.createdAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"createdAtLessThanOrEqual" withInt:self.createdAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtGreaterThanOrEqual" withInt:self.updatedAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtLessThanOrEqual" withInt:self.updatedAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"partnerIdEqual" withInt:self.partnerIdEqual];
    [aParams addIfDefinedKey:@"partnerIdIn" withString:self.partnerIdIn];
    [aParams addIfDefinedKey:@"isDefaultEqual" withInt:self.isDefaultEqual];
    [aParams addIfDefinedKey:@"isDefaultIn" withString:self.isDefaultIn];
    [aParams addIfDefinedKey:@"statusEqual" withInt:self.statusEqual];
    [aParams addIfDefinedKey:@"statusIn" withString:self.statusIn];
}

- (void)dealloc
{
    [self->_idIn release];
    [self->_partnerIdIn release];
    [self->_isDefaultIn release];
    [self->_statusIn release];
    [super dealloc];
}

@end

@implementation KalturaSyndicationDistributionProfileBaseFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaSyndicationDistributionProfileBaseFilter"];
}

@end

@implementation KalturaSyndicationDistributionProviderBaseFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaSyndicationDistributionProviderBaseFilter"];
}

@end

@implementation KalturaConfigurableDistributionProfileFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaConfigurableDistributionProfileFilter"];
}

@end

@implementation KalturaGenericDistributionProfileFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaGenericDistributionProfileFilter"];
}

@end

@implementation KalturaGenericDistributionProviderFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaGenericDistributionProviderFilter"];
}

@end

@implementation KalturaSyndicationDistributionProfileFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaSyndicationDistributionProfileFilter"];
}

@end

@implementation KalturaSyndicationDistributionProviderFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaSyndicationDistributionProviderFilter"];
}

@end

///////////////////////// services /////////////////////////
@implementation KalturaDistributionProfileService
- (KalturaDistributionProfile*)addWithDistributionProfile:(KalturaDistributionProfile*)aDistributionProfile
{
    [self.client.params addIfDefinedKey:@"distributionProfile" withObject:aDistributionProfile];
    return [self.client queueObjectService:@"contentdistribution_distributionprofile" withAction:@"add" withExpectedType:@"KalturaDistributionProfile"];
}

- (KalturaDistributionProfile*)getWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    return [self.client queueObjectService:@"contentdistribution_distributionprofile" withAction:@"get" withExpectedType:@"KalturaDistributionProfile"];
}

- (KalturaDistributionProfile*)updateWithId:(int)aId withDistributionProfile:(KalturaDistributionProfile*)aDistributionProfile
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"distributionProfile" withObject:aDistributionProfile];
    return [self.client queueObjectService:@"contentdistribution_distributionprofile" withAction:@"update" withExpectedType:@"KalturaDistributionProfile"];
}

- (KalturaDistributionProfile*)updateStatusWithId:(int)aId withStatus:(int)aStatus
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"status" withInt:aStatus];
    return [self.client queueObjectService:@"contentdistribution_distributionprofile" withAction:@"updateStatus" withExpectedType:@"KalturaDistributionProfile"];
}

- (void)deleteWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client queueVoidService:@"contentdistribution_distributionprofile" withAction:@"delete"];
}

- (KalturaDistributionProfileListResponse*)listWithFilter:(KalturaDistributionProfileFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"contentdistribution_distributionprofile" withAction:@"list" withExpectedType:@"KalturaDistributionProfileListResponse"];
}

- (KalturaDistributionProfileListResponse*)listWithFilter:(KalturaDistributionProfileFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaDistributionProfileListResponse*)list
{
    return [self listWithFilter:nil];
}

- (KalturaDistributionProfileListResponse*)listByPartnerWithFilter:(KalturaPartnerFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"contentdistribution_distributionprofile" withAction:@"listByPartner" withExpectedType:@"KalturaDistributionProfileListResponse"];
}

- (KalturaDistributionProfileListResponse*)listByPartnerWithFilter:(KalturaPartnerFilter*)aFilter
{
    return [self listByPartnerWithFilter:aFilter withPager:nil];
}

- (KalturaDistributionProfileListResponse*)listByPartner
{
    return [self listByPartnerWithFilter:nil];
}

@end

@implementation KalturaEntryDistributionService
- (KalturaEntryDistribution*)addWithEntryDistribution:(KalturaEntryDistribution*)aEntryDistribution
{
    [self.client.params addIfDefinedKey:@"entryDistribution" withObject:aEntryDistribution];
    return [self.client queueObjectService:@"contentdistribution_entrydistribution" withAction:@"add" withExpectedType:@"KalturaEntryDistribution"];
}

- (KalturaEntryDistribution*)getWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    return [self.client queueObjectService:@"contentdistribution_entrydistribution" withAction:@"get" withExpectedType:@"KalturaEntryDistribution"];
}

- (KalturaEntryDistribution*)validateWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    return [self.client queueObjectService:@"contentdistribution_entrydistribution" withAction:@"validate" withExpectedType:@"KalturaEntryDistribution"];
}

- (KalturaEntryDistribution*)updateWithId:(int)aId withEntryDistribution:(KalturaEntryDistribution*)aEntryDistribution
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"entryDistribution" withObject:aEntryDistribution];
    return [self.client queueObjectService:@"contentdistribution_entrydistribution" withAction:@"update" withExpectedType:@"KalturaEntryDistribution"];
}

- (void)deleteWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client queueVoidService:@"contentdistribution_entrydistribution" withAction:@"delete"];
}

- (KalturaEntryDistributionListResponse*)listWithFilter:(KalturaEntryDistributionFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"contentdistribution_entrydistribution" withAction:@"list" withExpectedType:@"KalturaEntryDistributionListResponse"];
}

- (KalturaEntryDistributionListResponse*)listWithFilter:(KalturaEntryDistributionFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaEntryDistributionListResponse*)list
{
    return [self listWithFilter:nil];
}

- (KalturaEntryDistribution*)submitAddWithId:(int)aId withSubmitWhenReady:(BOOL)aSubmitWhenReady
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"submitWhenReady" withBool:aSubmitWhenReady];
    return [self.client queueObjectService:@"contentdistribution_entrydistribution" withAction:@"submitAdd" withExpectedType:@"KalturaEntryDistribution"];
}

- (KalturaEntryDistribution*)submitAddWithId:(int)aId
{
    return [self submitAddWithId:aId withSubmitWhenReady:KALTURA_UNDEF_BOOL];
}

- (KalturaEntryDistribution*)submitUpdateWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    return [self.client queueObjectService:@"contentdistribution_entrydistribution" withAction:@"submitUpdate" withExpectedType:@"KalturaEntryDistribution"];
}

- (KalturaEntryDistribution*)submitFetchReportWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    return [self.client queueObjectService:@"contentdistribution_entrydistribution" withAction:@"submitFetchReport" withExpectedType:@"KalturaEntryDistribution"];
}

- (KalturaEntryDistribution*)submitDeleteWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    return [self.client queueObjectService:@"contentdistribution_entrydistribution" withAction:@"submitDelete" withExpectedType:@"KalturaEntryDistribution"];
}

- (KalturaEntryDistribution*)retrySubmitWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    return [self.client queueObjectService:@"contentdistribution_entrydistribution" withAction:@"retrySubmit" withExpectedType:@"KalturaEntryDistribution"];
}

- (NSString*)serveSentDataWithId:(int)aId withActionType:(int)aActionType
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"actionType" withInt:aActionType];
    return [self.client queueServeService:@"contentdistribution_entrydistribution" withAction:@"serveSentData"];
}

- (NSString*)serveReturnedDataWithId:(int)aId withActionType:(int)aActionType
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"actionType" withInt:aActionType];
    return [self.client queueServeService:@"contentdistribution_entrydistribution" withAction:@"serveReturnedData"];
}

@end

@implementation KalturaDistributionProviderService
- (KalturaDistributionProviderListResponse*)listWithFilter:(KalturaDistributionProviderFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"contentdistribution_distributionprovider" withAction:@"list" withExpectedType:@"KalturaDistributionProviderListResponse"];
}

- (KalturaDistributionProviderListResponse*)listWithFilter:(KalturaDistributionProviderFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaDistributionProviderListResponse*)list
{
    return [self listWithFilter:nil];
}

@end

@implementation KalturaGenericDistributionProviderService
- (KalturaGenericDistributionProvider*)addWithGenericDistributionProvider:(KalturaGenericDistributionProvider*)aGenericDistributionProvider
{
    [self.client.params addIfDefinedKey:@"genericDistributionProvider" withObject:aGenericDistributionProvider];
    return [self.client queueObjectService:@"contentdistribution_genericdistributionprovider" withAction:@"add" withExpectedType:@"KalturaGenericDistributionProvider"];
}

- (KalturaGenericDistributionProvider*)getWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    return [self.client queueObjectService:@"contentdistribution_genericdistributionprovider" withAction:@"get" withExpectedType:@"KalturaGenericDistributionProvider"];
}

- (KalturaGenericDistributionProvider*)updateWithId:(int)aId withGenericDistributionProvider:(KalturaGenericDistributionProvider*)aGenericDistributionProvider
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"genericDistributionProvider" withObject:aGenericDistributionProvider];
    return [self.client queueObjectService:@"contentdistribution_genericdistributionprovider" withAction:@"update" withExpectedType:@"KalturaGenericDistributionProvider"];
}

- (void)deleteWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client queueVoidService:@"contentdistribution_genericdistributionprovider" withAction:@"delete"];
}

- (KalturaGenericDistributionProviderListResponse*)listWithFilter:(KalturaGenericDistributionProviderFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"contentdistribution_genericdistributionprovider" withAction:@"list" withExpectedType:@"KalturaGenericDistributionProviderListResponse"];
}

- (KalturaGenericDistributionProviderListResponse*)listWithFilter:(KalturaGenericDistributionProviderFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaGenericDistributionProviderListResponse*)list
{
    return [self listWithFilter:nil];
}

@end

@implementation KalturaGenericDistributionProviderActionService
- (KalturaGenericDistributionProviderAction*)addWithGenericDistributionProviderAction:(KalturaGenericDistributionProviderAction*)aGenericDistributionProviderAction
{
    [self.client.params addIfDefinedKey:@"genericDistributionProviderAction" withObject:aGenericDistributionProviderAction];
    return [self.client queueObjectService:@"contentdistribution_genericdistributionprovideraction" withAction:@"add" withExpectedType:@"KalturaGenericDistributionProviderAction"];
}

- (KalturaGenericDistributionProviderAction*)addMrssTransformWithId:(int)aId withXslData:(NSString*)aXslData
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"xslData" withString:aXslData];
    return [self.client queueObjectService:@"contentdistribution_genericdistributionprovideraction" withAction:@"addMrssTransform" withExpectedType:@"KalturaGenericDistributionProviderAction"];
}

- (KalturaGenericDistributionProviderAction*)addMrssTransformFromFileWithId:(int)aId withXslFile:(NSString*)aXslFile
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"xslFile" withFileName:aXslFile];
    return [self.client queueObjectService:@"contentdistribution_genericdistributionprovideraction" withAction:@"addMrssTransformFromFile" withExpectedType:@"KalturaGenericDistributionProviderAction"];
}

- (KalturaGenericDistributionProviderAction*)addMrssValidateWithId:(int)aId withXsdData:(NSString*)aXsdData
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"xsdData" withString:aXsdData];
    return [self.client queueObjectService:@"contentdistribution_genericdistributionprovideraction" withAction:@"addMrssValidate" withExpectedType:@"KalturaGenericDistributionProviderAction"];
}

- (KalturaGenericDistributionProviderAction*)addMrssValidateFromFileWithId:(int)aId withXsdFile:(NSString*)aXsdFile
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"xsdFile" withFileName:aXsdFile];
    return [self.client queueObjectService:@"contentdistribution_genericdistributionprovideraction" withAction:@"addMrssValidateFromFile" withExpectedType:@"KalturaGenericDistributionProviderAction"];
}

- (KalturaGenericDistributionProviderAction*)addResultsTransformWithId:(int)aId withTransformData:(NSString*)aTransformData
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"transformData" withString:aTransformData];
    return [self.client queueObjectService:@"contentdistribution_genericdistributionprovideraction" withAction:@"addResultsTransform" withExpectedType:@"KalturaGenericDistributionProviderAction"];
}

- (KalturaGenericDistributionProviderAction*)addResultsTransformFromFileWithId:(int)aId withTransformFile:(NSString*)aTransformFile
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"transformFile" withFileName:aTransformFile];
    return [self.client queueObjectService:@"contentdistribution_genericdistributionprovideraction" withAction:@"addResultsTransformFromFile" withExpectedType:@"KalturaGenericDistributionProviderAction"];
}

- (KalturaGenericDistributionProviderAction*)getWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    return [self.client queueObjectService:@"contentdistribution_genericdistributionprovideraction" withAction:@"get" withExpectedType:@"KalturaGenericDistributionProviderAction"];
}

- (KalturaGenericDistributionProviderAction*)getByProviderIdWithGenericDistributionProviderId:(int)aGenericDistributionProviderId withActionType:(int)aActionType
{
    [self.client.params addIfDefinedKey:@"genericDistributionProviderId" withInt:aGenericDistributionProviderId];
    [self.client.params addIfDefinedKey:@"actionType" withInt:aActionType];
    return [self.client queueObjectService:@"contentdistribution_genericdistributionprovideraction" withAction:@"getByProviderId" withExpectedType:@"KalturaGenericDistributionProviderAction"];
}

- (KalturaGenericDistributionProviderAction*)updateByProviderIdWithGenericDistributionProviderId:(int)aGenericDistributionProviderId withActionType:(int)aActionType withGenericDistributionProviderAction:(KalturaGenericDistributionProviderAction*)aGenericDistributionProviderAction
{
    [self.client.params addIfDefinedKey:@"genericDistributionProviderId" withInt:aGenericDistributionProviderId];
    [self.client.params addIfDefinedKey:@"actionType" withInt:aActionType];
    [self.client.params addIfDefinedKey:@"genericDistributionProviderAction" withObject:aGenericDistributionProviderAction];
    return [self.client queueObjectService:@"contentdistribution_genericdistributionprovideraction" withAction:@"updateByProviderId" withExpectedType:@"KalturaGenericDistributionProviderAction"];
}

- (KalturaGenericDistributionProviderAction*)updateWithId:(int)aId withGenericDistributionProviderAction:(KalturaGenericDistributionProviderAction*)aGenericDistributionProviderAction
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"genericDistributionProviderAction" withObject:aGenericDistributionProviderAction];
    return [self.client queueObjectService:@"contentdistribution_genericdistributionprovideraction" withAction:@"update" withExpectedType:@"KalturaGenericDistributionProviderAction"];
}

- (void)deleteWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client queueVoidService:@"contentdistribution_genericdistributionprovideraction" withAction:@"delete"];
}

- (void)deleteByProviderIdWithGenericDistributionProviderId:(int)aGenericDistributionProviderId withActionType:(int)aActionType
{
    [self.client.params addIfDefinedKey:@"genericDistributionProviderId" withInt:aGenericDistributionProviderId];
    [self.client.params addIfDefinedKey:@"actionType" withInt:aActionType];
    [self.client queueVoidService:@"contentdistribution_genericdistributionprovideraction" withAction:@"deleteByProviderId"];
}

- (KalturaGenericDistributionProviderActionListResponse*)listWithFilter:(KalturaGenericDistributionProviderActionFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"contentdistribution_genericdistributionprovideraction" withAction:@"list" withExpectedType:@"KalturaGenericDistributionProviderActionListResponse"];
}

- (KalturaGenericDistributionProviderActionListResponse*)listWithFilter:(KalturaGenericDistributionProviderActionFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaGenericDistributionProviderActionListResponse*)list
{
    return [self listWithFilter:nil];
}

@end

@implementation KalturaContentDistributionClientPlugin
@synthesize client = _client;

- (id)initWithClient:(KalturaClient*)aClient
{
    self = [super init];
    if (self == nil)
        return nil;
    self.client = aClient;
    return self;
}

- (KalturaDistributionProfileService*)distributionProfile
{
    if (self->_distributionProfile == nil)
    	self->_distributionProfile = [[KalturaDistributionProfileService alloc] initWithClient:self.client];
    return self->_distributionProfile;
}

- (KalturaEntryDistributionService*)entryDistribution
{
    if (self->_entryDistribution == nil)
    	self->_entryDistribution = [[KalturaEntryDistributionService alloc] initWithClient:self.client];
    return self->_entryDistribution;
}

- (KalturaDistributionProviderService*)distributionProvider
{
    if (self->_distributionProvider == nil)
    	self->_distributionProvider = [[KalturaDistributionProviderService alloc] initWithClient:self.client];
    return self->_distributionProvider;
}

- (KalturaGenericDistributionProviderService*)genericDistributionProvider
{
    if (self->_genericDistributionProvider == nil)
    	self->_genericDistributionProvider = [[KalturaGenericDistributionProviderService alloc] initWithClient:self.client];
    return self->_genericDistributionProvider;
}

- (KalturaGenericDistributionProviderActionService*)genericDistributionProviderAction
{
    if (self->_genericDistributionProviderAction == nil)
    	self->_genericDistributionProviderAction = [[KalturaGenericDistributionProviderActionService alloc] initWithClient:self.client];
    return self->_genericDistributionProviderAction;
}

- (void)dealloc
{
    [self->_distributionProfile release];
    [self->_entryDistribution release];
    [self->_distributionProvider release];
    [self->_genericDistributionProvider release];
    [self->_genericDistributionProviderAction release];
	[super dealloc];
}

@end

