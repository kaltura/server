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
#import "KalturaAuditClientPlugin.h"

///////////////////////// enums /////////////////////////
@implementation KalturaAuditTrailChangeXmlNodeType
+ (int)CHANGED
{
    return 1;
}
+ (int)ADDED
{
    return 2;
}
+ (int)REMOVED
{
    return 3;
}
@end

@implementation KalturaAuditTrailContext
+ (int)CLIENT
{
    return -1;
}
+ (int)SCRIPT
{
    return 0;
}
+ (int)PS2
{
    return 1;
}
+ (int)API_V3
{
    return 2;
}
@end

@implementation KalturaAuditTrailFileSyncType
+ (int)FILE
{
    return 1;
}
+ (int)LINK
{
    return 2;
}
+ (int)URL
{
    return 3;
}
@end

@implementation KalturaAuditTrailStatus
+ (int)PENDING
{
    return 1;
}
+ (int)READY
{
    return 2;
}
+ (int)FAILED
{
    return 3;
}
@end

@implementation KalturaAuditTrailAction
+ (NSString*)CHANGED
{
    return @"CHANGED";
}
+ (NSString*)CONTENT_VIEWED
{
    return @"CONTENT_VIEWED";
}
+ (NSString*)COPIED
{
    return @"COPIED";
}
+ (NSString*)CREATED
{
    return @"CREATED";
}
+ (NSString*)DELETED
{
    return @"DELETED";
}
+ (NSString*)FILE_SYNC_CREATED
{
    return @"FILE_SYNC_CREATED";
}
+ (NSString*)RELATION_ADDED
{
    return @"RELATION_ADDED";
}
+ (NSString*)RELATION_REMOVED
{
    return @"RELATION_REMOVED";
}
+ (NSString*)VIEWED
{
    return @"VIEWED";
}
@end

@implementation KalturaAuditTrailObjectType
+ (NSString*)BATCH_JOB
{
    return @"BatchJob";
}
+ (NSString*)EMAIL_INGESTION_PROFILE
{
    return @"EmailIngestionProfile";
}
+ (NSString*)FILE_SYNC
{
    return @"FileSync";
}
+ (NSString*)KSHOW_KUSER
{
    return @"KshowKuser";
}
+ (NSString*)METADATA
{
    return @"Metadata";
}
+ (NSString*)METADATA_PROFILE
{
    return @"MetadataProfile";
}
+ (NSString*)PARTNER
{
    return @"Partner";
}
+ (NSString*)PERMISSION
{
    return @"Permission";
}
+ (NSString*)UPLOAD_TOKEN
{
    return @"UploadToken";
}
+ (NSString*)USER_LOGIN_DATA
{
    return @"UserLoginData";
}
+ (NSString*)USER_ROLE
{
    return @"UserRole";
}
+ (NSString*)ACCESS_CONTROL
{
    return @"accessControl";
}
+ (NSString*)CATEGORY
{
    return @"category";
}
+ (NSString*)CONVERSION_PROFILE_2
{
    return @"conversionProfile2";
}
+ (NSString*)ENTRY
{
    return @"entry";
}
+ (NSString*)FLAVOR_ASSET
{
    return @"flavorAsset";
}
+ (NSString*)FLAVOR_PARAMS
{
    return @"flavorParams";
}
+ (NSString*)FLAVOR_PARAMS_CONVERSION_PROFILE
{
    return @"flavorParamsConversionProfile";
}
+ (NSString*)FLAVOR_PARAMS_OUTPUT
{
    return @"flavorParamsOutput";
}
+ (NSString*)KSHOW
{
    return @"kshow";
}
+ (NSString*)KUSER
{
    return @"kuser";
}
+ (NSString*)MEDIA_INFO
{
    return @"mediaInfo";
}
+ (NSString*)MODERATION
{
    return @"moderation";
}
+ (NSString*)ROUGHCUT
{
    return @"roughcutEntry";
}
+ (NSString*)SYNDICATION
{
    return @"syndicationFeed";
}
+ (NSString*)THUMBNAIL_ASSET
{
    return @"thumbAsset";
}
+ (NSString*)THUMBNAIL_PARAMS
{
    return @"thumbParams";
}
+ (NSString*)THUMBNAIL_PARAMS_OUTPUT
{
    return @"thumbParamsOutput";
}
+ (NSString*)UI_CONF
{
    return @"uiConf";
}
+ (NSString*)WIDGET
{
    return @"widget";
}
@end

@implementation KalturaAuditTrailOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)PARSED_AT_ASC
{
    return @"+parsedAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)PARSED_AT_DESC
{
    return @"-parsedAt";
}
@end

///////////////////////// classes /////////////////////////
@implementation KalturaAuditTrailInfo
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAuditTrailInfo"];
}

@end

@interface KalturaAuditTrail()
@property (nonatomic,assign) int id;
@property (nonatomic,assign) int createdAt;
@property (nonatomic,assign) int parsedAt;
@property (nonatomic,assign) int status;
@property (nonatomic,assign) int masterPartnerId;
@property (nonatomic,assign) int partnerId;
@property (nonatomic,copy) NSString* requestId;
@property (nonatomic,copy) NSString* ks;
@property (nonatomic,assign) int context;
@property (nonatomic,copy) NSString* entryPoint;
@property (nonatomic,copy) NSString* serverName;
@property (nonatomic,copy) NSString* ipAddress;
@property (nonatomic,copy) NSString* userAgent;
@property (nonatomic,copy) NSString* errorDescription;
@end

@implementation KalturaAuditTrail
@synthesize id = _id;
@synthesize createdAt = _createdAt;
@synthesize parsedAt = _parsedAt;
@synthesize status = _status;
@synthesize auditObjectType = _auditObjectType;
@synthesize objectId = _objectId;
@synthesize relatedObjectId = _relatedObjectId;
@synthesize relatedObjectType = _relatedObjectType;
@synthesize entryId = _entryId;
@synthesize masterPartnerId = _masterPartnerId;
@synthesize partnerId = _partnerId;
@synthesize requestId = _requestId;
@synthesize userId = _userId;
@synthesize action = _action;
@synthesize data = _data;
@synthesize ks = _ks;
@synthesize context = _context;
@synthesize entryPoint = _entryPoint;
@synthesize serverName = _serverName;
@synthesize ipAddress = _ipAddress;
@synthesize userAgent = _userAgent;
@synthesize clientTag = _clientTag;
@synthesize description = _description;
@synthesize errorDescription = _errorDescription;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_id = KALTURA_UNDEF_INT;
    self->_createdAt = KALTURA_UNDEF_INT;
    self->_parsedAt = KALTURA_UNDEF_INT;
    self->_status = KALTURA_UNDEF_INT;
    self->_masterPartnerId = KALTURA_UNDEF_INT;
    self->_partnerId = KALTURA_UNDEF_INT;
    self->_context = KALTURA_UNDEF_INT;
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

- (KalturaFieldType)getTypeOfParsedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStatus
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfAuditObjectType
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfObjectId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfRelatedObjectId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfRelatedObjectType
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEntryId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfMasterPartnerId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfRequestId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfUserId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAction
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfData
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfData
{
    return @"KalturaAuditTrailInfo";
}

- (KalturaFieldType)getTypeOfKs
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfContext
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfEntryPoint
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfServerName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfIpAddress
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfUserAgent
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfClientTag
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDescription
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfErrorDescription
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

- (void)setParsedAtFromString:(NSString*)aPropVal
{
    self.parsedAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusFromString:(NSString*)aPropVal
{
    self.status = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setMasterPartnerIdFromString:(NSString*)aPropVal
{
    self.masterPartnerId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerIdFromString:(NSString*)aPropVal
{
    self.partnerId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setContextFromString:(NSString*)aPropVal
{
    self.context = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAuditTrail"];
    [aParams addIfDefinedKey:@"auditObjectType" withString:self.auditObjectType];
    [aParams addIfDefinedKey:@"objectId" withString:self.objectId];
    [aParams addIfDefinedKey:@"relatedObjectId" withString:self.relatedObjectId];
    [aParams addIfDefinedKey:@"relatedObjectType" withString:self.relatedObjectType];
    [aParams addIfDefinedKey:@"entryId" withString:self.entryId];
    [aParams addIfDefinedKey:@"userId" withString:self.userId];
    [aParams addIfDefinedKey:@"action" withString:self.action];
    [aParams addIfDefinedKey:@"data" withObject:self.data];
    [aParams addIfDefinedKey:@"clientTag" withString:self.clientTag];
    [aParams addIfDefinedKey:@"description" withString:self.description];
}

- (void)dealloc
{
    [self->_auditObjectType release];
    [self->_objectId release];
    [self->_relatedObjectId release];
    [self->_relatedObjectType release];
    [self->_entryId release];
    [self->_requestId release];
    [self->_userId release];
    [self->_action release];
    [self->_data release];
    [self->_ks release];
    [self->_entryPoint release];
    [self->_serverName release];
    [self->_ipAddress release];
    [self->_userAgent release];
    [self->_clientTag release];
    [self->_description release];
    [self->_errorDescription release];
    [super dealloc];
}

@end

@implementation KalturaAuditTrailChangeItem
@synthesize descriptor = _descriptor;
@synthesize oldValue = _oldValue;
@synthesize newValue = _newValue;

- (KalturaFieldType)getTypeOfDescriptor
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfOldValue
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfNewValue
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAuditTrailChangeItem"];
    [aParams addIfDefinedKey:@"descriptor" withString:self.descriptor];
    [aParams addIfDefinedKey:@"oldValue" withString:self.oldValue];
    [aParams addIfDefinedKey:@"newValue" withString:self.newValue];
}

- (void)dealloc
{
    [self->_descriptor release];
    [self->_oldValue release];
    [self->_newValue release];
    [super dealloc];
}

@end

@interface KalturaAuditTrailListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaAuditTrailListResponse
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
    return @"KalturaAuditTrail";
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
        [aParams putKey:@"objectType" withString:@"KalturaAuditTrailListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@implementation KalturaAuditTrailBaseFilter
@synthesize idEqual = _idEqual;
@synthesize createdAtGreaterThanOrEqual = _createdAtGreaterThanOrEqual;
@synthesize createdAtLessThanOrEqual = _createdAtLessThanOrEqual;
@synthesize parsedAtGreaterThanOrEqual = _parsedAtGreaterThanOrEqual;
@synthesize parsedAtLessThanOrEqual = _parsedAtLessThanOrEqual;
@synthesize statusEqual = _statusEqual;
@synthesize statusIn = _statusIn;
@synthesize auditObjectTypeEqual = _auditObjectTypeEqual;
@synthesize auditObjectTypeIn = _auditObjectTypeIn;
@synthesize objectIdEqual = _objectIdEqual;
@synthesize objectIdIn = _objectIdIn;
@synthesize relatedObjectIdEqual = _relatedObjectIdEqual;
@synthesize relatedObjectIdIn = _relatedObjectIdIn;
@synthesize relatedObjectTypeEqual = _relatedObjectTypeEqual;
@synthesize relatedObjectTypeIn = _relatedObjectTypeIn;
@synthesize entryIdEqual = _entryIdEqual;
@synthesize entryIdIn = _entryIdIn;
@synthesize masterPartnerIdEqual = _masterPartnerIdEqual;
@synthesize masterPartnerIdIn = _masterPartnerIdIn;
@synthesize partnerIdEqual = _partnerIdEqual;
@synthesize partnerIdIn = _partnerIdIn;
@synthesize requestIdEqual = _requestIdEqual;
@synthesize requestIdIn = _requestIdIn;
@synthesize userIdEqual = _userIdEqual;
@synthesize userIdIn = _userIdIn;
@synthesize actionEqual = _actionEqual;
@synthesize actionIn = _actionIn;
@synthesize ksEqual = _ksEqual;
@synthesize contextEqual = _contextEqual;
@synthesize contextIn = _contextIn;
@synthesize entryPointEqual = _entryPointEqual;
@synthesize entryPointIn = _entryPointIn;
@synthesize serverNameEqual = _serverNameEqual;
@synthesize serverNameIn = _serverNameIn;
@synthesize ipAddressEqual = _ipAddressEqual;
@synthesize ipAddressIn = _ipAddressIn;
@synthesize clientTagEqual = _clientTagEqual;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_idEqual = KALTURA_UNDEF_INT;
    self->_createdAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_createdAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_parsedAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_parsedAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_statusEqual = KALTURA_UNDEF_INT;
    self->_masterPartnerIdEqual = KALTURA_UNDEF_INT;
    self->_partnerIdEqual = KALTURA_UNDEF_INT;
    self->_contextEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfParsedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfParsedAtLessThanOrEqual
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

- (KalturaFieldType)getTypeOfAuditObjectTypeEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAuditObjectTypeIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfObjectIdEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfObjectIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfRelatedObjectIdEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfRelatedObjectIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfRelatedObjectTypeEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfRelatedObjectTypeIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEntryIdEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEntryIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfMasterPartnerIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfMasterPartnerIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPartnerIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfRequestIdEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfRequestIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfUserIdEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfUserIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfActionEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfActionIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfKsEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfContextEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfContextIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEntryPointEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfEntryPointIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfServerNameEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfServerNameIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfIpAddressEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfIpAddressIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfClientTagEqual
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

- (void)setParsedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.parsedAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setParsedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.parsedAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusEqualFromString:(NSString*)aPropVal
{
    self.statusEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setMasterPartnerIdEqualFromString:(NSString*)aPropVal
{
    self.masterPartnerIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerIdEqualFromString:(NSString*)aPropVal
{
    self.partnerIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setContextEqualFromString:(NSString*)aPropVal
{
    self.contextEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAuditTrailBaseFilter"];
    [aParams addIfDefinedKey:@"idEqual" withInt:self.idEqual];
    [aParams addIfDefinedKey:@"createdAtGreaterThanOrEqual" withInt:self.createdAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"createdAtLessThanOrEqual" withInt:self.createdAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"parsedAtGreaterThanOrEqual" withInt:self.parsedAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"parsedAtLessThanOrEqual" withInt:self.parsedAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"statusEqual" withInt:self.statusEqual];
    [aParams addIfDefinedKey:@"statusIn" withString:self.statusIn];
    [aParams addIfDefinedKey:@"auditObjectTypeEqual" withString:self.auditObjectTypeEqual];
    [aParams addIfDefinedKey:@"auditObjectTypeIn" withString:self.auditObjectTypeIn];
    [aParams addIfDefinedKey:@"objectIdEqual" withString:self.objectIdEqual];
    [aParams addIfDefinedKey:@"objectIdIn" withString:self.objectIdIn];
    [aParams addIfDefinedKey:@"relatedObjectIdEqual" withString:self.relatedObjectIdEqual];
    [aParams addIfDefinedKey:@"relatedObjectIdIn" withString:self.relatedObjectIdIn];
    [aParams addIfDefinedKey:@"relatedObjectTypeEqual" withString:self.relatedObjectTypeEqual];
    [aParams addIfDefinedKey:@"relatedObjectTypeIn" withString:self.relatedObjectTypeIn];
    [aParams addIfDefinedKey:@"entryIdEqual" withString:self.entryIdEqual];
    [aParams addIfDefinedKey:@"entryIdIn" withString:self.entryIdIn];
    [aParams addIfDefinedKey:@"masterPartnerIdEqual" withInt:self.masterPartnerIdEqual];
    [aParams addIfDefinedKey:@"masterPartnerIdIn" withString:self.masterPartnerIdIn];
    [aParams addIfDefinedKey:@"partnerIdEqual" withInt:self.partnerIdEqual];
    [aParams addIfDefinedKey:@"partnerIdIn" withString:self.partnerIdIn];
    [aParams addIfDefinedKey:@"requestIdEqual" withString:self.requestIdEqual];
    [aParams addIfDefinedKey:@"requestIdIn" withString:self.requestIdIn];
    [aParams addIfDefinedKey:@"userIdEqual" withString:self.userIdEqual];
    [aParams addIfDefinedKey:@"userIdIn" withString:self.userIdIn];
    [aParams addIfDefinedKey:@"actionEqual" withString:self.actionEqual];
    [aParams addIfDefinedKey:@"actionIn" withString:self.actionIn];
    [aParams addIfDefinedKey:@"ksEqual" withString:self.ksEqual];
    [aParams addIfDefinedKey:@"contextEqual" withInt:self.contextEqual];
    [aParams addIfDefinedKey:@"contextIn" withString:self.contextIn];
    [aParams addIfDefinedKey:@"entryPointEqual" withString:self.entryPointEqual];
    [aParams addIfDefinedKey:@"entryPointIn" withString:self.entryPointIn];
    [aParams addIfDefinedKey:@"serverNameEqual" withString:self.serverNameEqual];
    [aParams addIfDefinedKey:@"serverNameIn" withString:self.serverNameIn];
    [aParams addIfDefinedKey:@"ipAddressEqual" withString:self.ipAddressEqual];
    [aParams addIfDefinedKey:@"ipAddressIn" withString:self.ipAddressIn];
    [aParams addIfDefinedKey:@"clientTagEqual" withString:self.clientTagEqual];
}

- (void)dealloc
{
    [self->_statusIn release];
    [self->_auditObjectTypeEqual release];
    [self->_auditObjectTypeIn release];
    [self->_objectIdEqual release];
    [self->_objectIdIn release];
    [self->_relatedObjectIdEqual release];
    [self->_relatedObjectIdIn release];
    [self->_relatedObjectTypeEqual release];
    [self->_relatedObjectTypeIn release];
    [self->_entryIdEqual release];
    [self->_entryIdIn release];
    [self->_masterPartnerIdIn release];
    [self->_partnerIdIn release];
    [self->_requestIdEqual release];
    [self->_requestIdIn release];
    [self->_userIdEqual release];
    [self->_userIdIn release];
    [self->_actionEqual release];
    [self->_actionIn release];
    [self->_ksEqual release];
    [self->_contextIn release];
    [self->_entryPointEqual release];
    [self->_entryPointIn release];
    [self->_serverNameEqual release];
    [self->_serverNameIn release];
    [self->_ipAddressEqual release];
    [self->_ipAddressIn release];
    [self->_clientTagEqual release];
    [super dealloc];
}

@end

@implementation KalturaAuditTrailChangeInfo
@synthesize changedItems = _changedItems;

- (KalturaFieldType)getTypeOfChangedItems
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfChangedItems
{
    return @"KalturaAuditTrailChangeItem";
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAuditTrailChangeInfo"];
    [aParams addIfDefinedKey:@"changedItems" withArray:self.changedItems];
}

- (void)dealloc
{
    [self->_changedItems release];
    [super dealloc];
}

@end

@implementation KalturaAuditTrailChangeXmlNode
@synthesize type = _type;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_type = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfType
{
    return KFT_Int;
}

- (void)setTypeFromString:(NSString*)aPropVal
{
    self.type = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAuditTrailChangeXmlNode"];
    [aParams addIfDefinedKey:@"type" withInt:self.type];
}

@end

@implementation KalturaAuditTrailFileSyncCreateInfo
@synthesize version = _version;
@synthesize objectSubType = _objectSubType;
@synthesize dc = _dc;
@synthesize original = _original;
@synthesize fileType = _fileType;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_objectSubType = KALTURA_UNDEF_INT;
    self->_dc = KALTURA_UNDEF_INT;
    self->_original = KALTURA_UNDEF_BOOL;
    self->_fileType = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfVersion
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfObjectSubType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDc
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfOriginal
{
    return KFT_Bool;
}

- (KalturaFieldType)getTypeOfFileType
{
    return KFT_Int;
}

- (void)setObjectSubTypeFromString:(NSString*)aPropVal
{
    self.objectSubType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDcFromString:(NSString*)aPropVal
{
    self.dc = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setOriginalFromString:(NSString*)aPropVal
{
    self.original = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)setFileTypeFromString:(NSString*)aPropVal
{
    self.fileType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAuditTrailFileSyncCreateInfo"];
    [aParams addIfDefinedKey:@"version" withString:self.version];
    [aParams addIfDefinedKey:@"objectSubType" withInt:self.objectSubType];
    [aParams addIfDefinedKey:@"dc" withInt:self.dc];
    [aParams addIfDefinedKey:@"original" withBool:self.original];
    [aParams addIfDefinedKey:@"fileType" withInt:self.fileType];
}

- (void)dealloc
{
    [self->_version release];
    [super dealloc];
}

@end

@implementation KalturaAuditTrailTextInfo
@synthesize info = _info;

- (KalturaFieldType)getTypeOfInfo
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAuditTrailTextInfo"];
    [aParams addIfDefinedKey:@"info" withString:self.info];
}

- (void)dealloc
{
    [self->_info release];
    [super dealloc];
}

@end

@implementation KalturaAuditTrailFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaAuditTrailFilter"];
}

@end

///////////////////////// services /////////////////////////
@implementation KalturaAuditTrailService
- (KalturaAuditTrail*)addWithAuditTrail:(KalturaAuditTrail*)aAuditTrail
{
    [self.client.params addIfDefinedKey:@"auditTrail" withObject:aAuditTrail];
    return [self.client queueObjectService:@"audit_audittrail" withAction:@"add" withExpectedType:@"KalturaAuditTrail"];
}

- (KalturaAuditTrail*)getWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    return [self.client queueObjectService:@"audit_audittrail" withAction:@"get" withExpectedType:@"KalturaAuditTrail"];
}

- (KalturaAuditTrailListResponse*)listWithFilter:(KalturaAuditTrailFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"audit_audittrail" withAction:@"list" withExpectedType:@"KalturaAuditTrailListResponse"];
}

- (KalturaAuditTrailListResponse*)listWithFilter:(KalturaAuditTrailFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaAuditTrailListResponse*)list
{
    return [self listWithFilter:nil];
}

@end

@implementation KalturaAuditClientPlugin
@synthesize client = _client;

- (id)initWithClient:(KalturaClient*)aClient
{
    self = [super init];
    if (self == nil)
        return nil;
    self.client = aClient;
    return self;
}

- (KalturaAuditTrailService*)auditTrail
{
    if (self->_auditTrail == nil)
    	self->_auditTrail = [[KalturaAuditTrailService alloc] initWithClient:self.client];
    return self->_auditTrail;
}

- (void)dealloc
{
    [self->_auditTrail release];
	[super dealloc];
}

@end

