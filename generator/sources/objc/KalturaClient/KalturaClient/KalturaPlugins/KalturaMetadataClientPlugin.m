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
#import "KalturaMetadataClientPlugin.h"

///////////////////////// enums /////////////////////////
@implementation KalturaMetadataProfileCreateMode
+ (int)API
{
    return 1;
}
+ (int)KMC
{
    return 2;
}
+ (int)APP
{
    return 3;
}
@end

@implementation KalturaMetadataProfileStatus
+ (int)ACTIVE
{
    return 1;
}
+ (int)DEPRECATED
{
    return 2;
}
+ (int)TRANSFORMING
{
    return 3;
}
@end

@implementation KalturaMetadataStatus
+ (int)VALID
{
    return 1;
}
+ (int)INVALID
{
    return 2;
}
+ (int)DELETED
{
    return 3;
}
@end

@implementation KalturaMetadataObjectType
+ (NSString*)AD_CUE_POINT
{
    return @"adCuePoint.AdCuePoint";
}
+ (NSString*)ANNOTATION
{
    return @"annotation.Annotation";
}
+ (NSString*)CODE_CUE_POINT
{
    return @"codeCuePoint.CodeCuePoint";
}
+ (NSString*)ENTRY
{
    return @"1";
}
+ (NSString*)CATEGORY
{
    return @"2";
}
+ (NSString*)USER
{
    return @"3";
}
+ (NSString*)PARTNER
{
    return @"4";
}
@end

@implementation KalturaMetadataOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)METADATA_PROFILE_VERSION_ASC
{
    return @"+metadataProfileVersion";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)VERSION_ASC
{
    return @"+version";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)METADATA_PROFILE_VERSION_DESC
{
    return @"-metadataProfileVersion";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
+ (NSString*)VERSION_DESC
{
    return @"-version";
}
@end

@implementation KalturaMetadataProfileOrderBy
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

///////////////////////// classes /////////////////////////
@interface KalturaMetadata()
@property (nonatomic,assign) int id;
@property (nonatomic,assign) int partnerId;
@property (nonatomic,assign) int metadataProfileId;
@property (nonatomic,assign) int metadataProfileVersion;
@property (nonatomic,copy) NSString* metadataObjectType;
@property (nonatomic,copy) NSString* objectId;
@property (nonatomic,assign) int version;
@property (nonatomic,assign) int createdAt;
@property (nonatomic,assign) int updatedAt;
@property (nonatomic,assign) int status;
@property (nonatomic,copy) NSString* xml;
@end

@implementation KalturaMetadata
@synthesize id = _id;
@synthesize partnerId = _partnerId;
@synthesize metadataProfileId = _metadataProfileId;
@synthesize metadataProfileVersion = _metadataProfileVersion;
@synthesize metadataObjectType = _metadataObjectType;
@synthesize objectId = _objectId;
@synthesize version = _version;
@synthesize createdAt = _createdAt;
@synthesize updatedAt = _updatedAt;
@synthesize status = _status;
@synthesize xml = _xml;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_id = KALTURA_UNDEF_INT;
    self->_partnerId = KALTURA_UNDEF_INT;
    self->_metadataProfileId = KALTURA_UNDEF_INT;
    self->_metadataProfileVersion = KALTURA_UNDEF_INT;
    self->_version = KALTURA_UNDEF_INT;
    self->_createdAt = KALTURA_UNDEF_INT;
    self->_updatedAt = KALTURA_UNDEF_INT;
    self->_status = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfMetadataProfileId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfMetadataProfileVersion
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfMetadataObjectType
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfObjectId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfVersion
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

- (KalturaFieldType)getTypeOfStatus
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfXml
{
    return KFT_String;
}

- (void)setIdFromString:(NSString*)aPropVal
{
    self.id = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerIdFromString:(NSString*)aPropVal
{
    self.partnerId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setMetadataProfileIdFromString:(NSString*)aPropVal
{
    self.metadataProfileId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setMetadataProfileVersionFromString:(NSString*)aPropVal
{
    self.metadataProfileVersion = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setVersionFromString:(NSString*)aPropVal
{
    self.version = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtFromString:(NSString*)aPropVal
{
    self.createdAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtFromString:(NSString*)aPropVal
{
    self.updatedAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusFromString:(NSString*)aPropVal
{
    self.status = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaMetadata"];
}

- (void)dealloc
{
    [self->_metadataObjectType release];
    [self->_objectId release];
    [self->_xml release];
    [super dealloc];
}

@end

@interface KalturaMetadataListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaMetadataListResponse
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
    return @"KalturaMetadata";
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
        [aParams putKey:@"objectType" withString:@"KalturaMetadataListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@interface KalturaMetadataProfile()
@property (nonatomic,assign) int id;
@property (nonatomic,assign) int partnerId;
@property (nonatomic,assign) int version;
@property (nonatomic,assign) int createdAt;
@property (nonatomic,assign) int updatedAt;
@property (nonatomic,assign) int status;
@property (nonatomic,copy) NSString* xsd;
@property (nonatomic,copy) NSString* views;
@property (nonatomic,copy) NSString* xslt;
@end

@implementation KalturaMetadataProfile
@synthesize id = _id;
@synthesize partnerId = _partnerId;
@synthesize metadataObjectType = _metadataObjectType;
@synthesize version = _version;
@synthesize name = _name;
@synthesize systemName = _systemName;
@synthesize description = _description;
@synthesize createdAt = _createdAt;
@synthesize updatedAt = _updatedAt;
@synthesize status = _status;
@synthesize xsd = _xsd;
@synthesize views = _views;
@synthesize xslt = _xslt;
@synthesize createMode = _createMode;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_id = KALTURA_UNDEF_INT;
    self->_partnerId = KALTURA_UNDEF_INT;
    self->_version = KALTURA_UNDEF_INT;
    self->_createdAt = KALTURA_UNDEF_INT;
    self->_updatedAt = KALTURA_UNDEF_INT;
    self->_status = KALTURA_UNDEF_INT;
    self->_createMode = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfMetadataObjectType
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfVersion
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSystemName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDescription
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAt
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStatus
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfXsd
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfViews
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfXslt
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreateMode
{
    return KFT_Int;
}

- (void)setIdFromString:(NSString*)aPropVal
{
    self.id = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerIdFromString:(NSString*)aPropVal
{
    self.partnerId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setVersionFromString:(NSString*)aPropVal
{
    self.version = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtFromString:(NSString*)aPropVal
{
    self.createdAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtFromString:(NSString*)aPropVal
{
    self.updatedAt = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setStatusFromString:(NSString*)aPropVal
{
    self.status = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreateModeFromString:(NSString*)aPropVal
{
    self.createMode = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaMetadataProfile"];
    [aParams addIfDefinedKey:@"metadataObjectType" withString:self.metadataObjectType];
    [aParams addIfDefinedKey:@"name" withString:self.name];
    [aParams addIfDefinedKey:@"systemName" withString:self.systemName];
    [aParams addIfDefinedKey:@"description" withString:self.description];
    [aParams addIfDefinedKey:@"createMode" withInt:self.createMode];
}

- (void)dealloc
{
    [self->_metadataObjectType release];
    [self->_name release];
    [self->_systemName release];
    [self->_description release];
    [self->_xsd release];
    [self->_views release];
    [self->_xslt release];
    [super dealloc];
}

@end

@interface KalturaMetadataProfileField()
@property (nonatomic,assign) int id;
@property (nonatomic,copy) NSString* xPath;
@property (nonatomic,copy) NSString* key;
@property (nonatomic,copy) NSString* label;
@end

@implementation KalturaMetadataProfileField
@synthesize id = _id;
@synthesize xPath = _xPath;
@synthesize key = _key;
@synthesize label = _label;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_id = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfId
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfXPath
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfKey
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfLabel
{
    return KFT_String;
}

- (void)setIdFromString:(NSString*)aPropVal
{
    self.id = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaMetadataProfileField"];
}

- (void)dealloc
{
    [self->_xPath release];
    [self->_key release];
    [self->_label release];
    [super dealloc];
}

@end

@interface KalturaMetadataProfileFieldListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaMetadataProfileFieldListResponse
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
    return @"KalturaMetadataProfileField";
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
        [aParams putKey:@"objectType" withString:@"KalturaMetadataProfileFieldListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@interface KalturaMetadataProfileListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaMetadataProfileListResponse
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
    return @"KalturaMetadataProfile";
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
        [aParams putKey:@"objectType" withString:@"KalturaMetadataProfileListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@implementation KalturaImportMetadataJobData
@synthesize srcFileUrl = _srcFileUrl;
@synthesize destFileLocalPath = _destFileLocalPath;
@synthesize metadataId = _metadataId;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_metadataId = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfSrcFileUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDestFileLocalPath
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfMetadataId
{
    return KFT_Int;
}

- (void)setMetadataIdFromString:(NSString*)aPropVal
{
    self.metadataId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaImportMetadataJobData"];
    [aParams addIfDefinedKey:@"srcFileUrl" withString:self.srcFileUrl];
    [aParams addIfDefinedKey:@"destFileLocalPath" withString:self.destFileLocalPath];
    [aParams addIfDefinedKey:@"metadataId" withInt:self.metadataId];
}

- (void)dealloc
{
    [self->_srcFileUrl release];
    [self->_destFileLocalPath release];
    [super dealloc];
}

@end

@implementation KalturaMetadataBaseFilter
@synthesize partnerIdEqual = _partnerIdEqual;
@synthesize metadataProfileIdEqual = _metadataProfileIdEqual;
@synthesize metadataProfileVersionEqual = _metadataProfileVersionEqual;
@synthesize metadataProfileVersionGreaterThanOrEqual = _metadataProfileVersionGreaterThanOrEqual;
@synthesize metadataProfileVersionLessThanOrEqual = _metadataProfileVersionLessThanOrEqual;
@synthesize metadataObjectTypeEqual = _metadataObjectTypeEqual;
@synthesize objectIdEqual = _objectIdEqual;
@synthesize objectIdIn = _objectIdIn;
@synthesize versionEqual = _versionEqual;
@synthesize versionGreaterThanOrEqual = _versionGreaterThanOrEqual;
@synthesize versionLessThanOrEqual = _versionLessThanOrEqual;
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
    self->_partnerIdEqual = KALTURA_UNDEF_INT;
    self->_metadataProfileIdEqual = KALTURA_UNDEF_INT;
    self->_metadataProfileVersionEqual = KALTURA_UNDEF_INT;
    self->_metadataProfileVersionGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_metadataProfileVersionLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_versionEqual = KALTURA_UNDEF_INT;
    self->_versionGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_versionLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_createdAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_createdAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_statusEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfPartnerIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfMetadataProfileIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfMetadataProfileVersionEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfMetadataProfileVersionGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfMetadataProfileVersionLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfMetadataObjectTypeEqual
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

- (KalturaFieldType)getTypeOfVersionEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfVersionGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfVersionLessThanOrEqual
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

- (void)setPartnerIdEqualFromString:(NSString*)aPropVal
{
    self.partnerIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setMetadataProfileIdEqualFromString:(NSString*)aPropVal
{
    self.metadataProfileIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setMetadataProfileVersionEqualFromString:(NSString*)aPropVal
{
    self.metadataProfileVersionEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setMetadataProfileVersionGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.metadataProfileVersionGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setMetadataProfileVersionLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.metadataProfileVersionLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setVersionEqualFromString:(NSString*)aPropVal
{
    self.versionEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setVersionGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.versionGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setVersionLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.versionLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
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
        [aParams putKey:@"objectType" withString:@"KalturaMetadataBaseFilter"];
    [aParams addIfDefinedKey:@"partnerIdEqual" withInt:self.partnerIdEqual];
    [aParams addIfDefinedKey:@"metadataProfileIdEqual" withInt:self.metadataProfileIdEqual];
    [aParams addIfDefinedKey:@"metadataProfileVersionEqual" withInt:self.metadataProfileVersionEqual];
    [aParams addIfDefinedKey:@"metadataProfileVersionGreaterThanOrEqual" withInt:self.metadataProfileVersionGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"metadataProfileVersionLessThanOrEqual" withInt:self.metadataProfileVersionLessThanOrEqual];
    [aParams addIfDefinedKey:@"metadataObjectTypeEqual" withString:self.metadataObjectTypeEqual];
    [aParams addIfDefinedKey:@"objectIdEqual" withString:self.objectIdEqual];
    [aParams addIfDefinedKey:@"objectIdIn" withString:self.objectIdIn];
    [aParams addIfDefinedKey:@"versionEqual" withInt:self.versionEqual];
    [aParams addIfDefinedKey:@"versionGreaterThanOrEqual" withInt:self.versionGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"versionLessThanOrEqual" withInt:self.versionLessThanOrEqual];
    [aParams addIfDefinedKey:@"createdAtGreaterThanOrEqual" withInt:self.createdAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"createdAtLessThanOrEqual" withInt:self.createdAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtGreaterThanOrEqual" withInt:self.updatedAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtLessThanOrEqual" withInt:self.updatedAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"statusEqual" withInt:self.statusEqual];
    [aParams addIfDefinedKey:@"statusIn" withString:self.statusIn];
}

- (void)dealloc
{
    [self->_metadataObjectTypeEqual release];
    [self->_objectIdEqual release];
    [self->_objectIdIn release];
    [self->_statusIn release];
    [super dealloc];
}

@end

@implementation KalturaMetadataProfileBaseFilter
@synthesize idEqual = _idEqual;
@synthesize partnerIdEqual = _partnerIdEqual;
@synthesize metadataObjectTypeEqual = _metadataObjectTypeEqual;
@synthesize metadataObjectTypeIn = _metadataObjectTypeIn;
@synthesize versionEqual = _versionEqual;
@synthesize nameEqual = _nameEqual;
@synthesize systemNameEqual = _systemNameEqual;
@synthesize systemNameIn = _systemNameIn;
@synthesize createdAtGreaterThanOrEqual = _createdAtGreaterThanOrEqual;
@synthesize createdAtLessThanOrEqual = _createdAtLessThanOrEqual;
@synthesize updatedAtGreaterThanOrEqual = _updatedAtGreaterThanOrEqual;
@synthesize updatedAtLessThanOrEqual = _updatedAtLessThanOrEqual;
@synthesize statusEqual = _statusEqual;
@synthesize statusIn = _statusIn;
@synthesize createModeEqual = _createModeEqual;
@synthesize createModeNotEqual = _createModeNotEqual;
@synthesize createModeIn = _createModeIn;
@synthesize createModeNotIn = _createModeNotIn;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_idEqual = KALTURA_UNDEF_INT;
    self->_partnerIdEqual = KALTURA_UNDEF_INT;
    self->_versionEqual = KALTURA_UNDEF_INT;
    self->_createdAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_createdAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_statusEqual = KALTURA_UNDEF_INT;
    self->_createModeEqual = KALTURA_UNDEF_INT;
    self->_createModeNotEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPartnerIdEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfMetadataObjectTypeEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfMetadataObjectTypeIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfVersionEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfNameEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSystemNameEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSystemNameIn
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

- (KalturaFieldType)getTypeOfCreateModeEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreateModeNotEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreateModeIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreateModeNotIn
{
    return KFT_String;
}

- (void)setIdEqualFromString:(NSString*)aPropVal
{
    self.idEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPartnerIdEqualFromString:(NSString*)aPropVal
{
    self.partnerIdEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setVersionEqualFromString:(NSString*)aPropVal
{
    self.versionEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
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

- (void)setCreateModeEqualFromString:(NSString*)aPropVal
{
    self.createModeEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreateModeNotEqualFromString:(NSString*)aPropVal
{
    self.createModeNotEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaMetadataProfileBaseFilter"];
    [aParams addIfDefinedKey:@"idEqual" withInt:self.idEqual];
    [aParams addIfDefinedKey:@"partnerIdEqual" withInt:self.partnerIdEqual];
    [aParams addIfDefinedKey:@"metadataObjectTypeEqual" withString:self.metadataObjectTypeEqual];
    [aParams addIfDefinedKey:@"metadataObjectTypeIn" withString:self.metadataObjectTypeIn];
    [aParams addIfDefinedKey:@"versionEqual" withInt:self.versionEqual];
    [aParams addIfDefinedKey:@"nameEqual" withString:self.nameEqual];
    [aParams addIfDefinedKey:@"systemNameEqual" withString:self.systemNameEqual];
    [aParams addIfDefinedKey:@"systemNameIn" withString:self.systemNameIn];
    [aParams addIfDefinedKey:@"createdAtGreaterThanOrEqual" withInt:self.createdAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"createdAtLessThanOrEqual" withInt:self.createdAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtGreaterThanOrEqual" withInt:self.updatedAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtLessThanOrEqual" withInt:self.updatedAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"statusEqual" withInt:self.statusEqual];
    [aParams addIfDefinedKey:@"statusIn" withString:self.statusIn];
    [aParams addIfDefinedKey:@"createModeEqual" withInt:self.createModeEqual];
    [aParams addIfDefinedKey:@"createModeNotEqual" withInt:self.createModeNotEqual];
    [aParams addIfDefinedKey:@"createModeIn" withString:self.createModeIn];
    [aParams addIfDefinedKey:@"createModeNotIn" withString:self.createModeNotIn];
}

- (void)dealloc
{
    [self->_metadataObjectTypeEqual release];
    [self->_metadataObjectTypeIn release];
    [self->_nameEqual release];
    [self->_systemNameEqual release];
    [self->_systemNameIn release];
    [self->_statusIn release];
    [self->_createModeIn release];
    [self->_createModeNotIn release];
    [super dealloc];
}

@end

@implementation KalturaTransformMetadataJobData
@synthesize srcXslPath = _srcXslPath;
@synthesize srcVersion = _srcVersion;
@synthesize destVersion = _destVersion;
@synthesize destXsdPath = _destXsdPath;
@synthesize metadataProfileId = _metadataProfileId;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_srcVersion = KALTURA_UNDEF_INT;
    self->_destVersion = KALTURA_UNDEF_INT;
    self->_metadataProfileId = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfSrcXslPath
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSrcVersion
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDestVersion
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDestXsdPath
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfMetadataProfileId
{
    return KFT_Int;
}

- (void)setSrcVersionFromString:(NSString*)aPropVal
{
    self.srcVersion = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDestVersionFromString:(NSString*)aPropVal
{
    self.destVersion = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setMetadataProfileIdFromString:(NSString*)aPropVal
{
    self.metadataProfileId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaTransformMetadataJobData"];
    [aParams addIfDefinedKey:@"srcXslPath" withString:self.srcXslPath];
    [aParams addIfDefinedKey:@"srcVersion" withInt:self.srcVersion];
    [aParams addIfDefinedKey:@"destVersion" withInt:self.destVersion];
    [aParams addIfDefinedKey:@"destXsdPath" withString:self.destXsdPath];
    [aParams addIfDefinedKey:@"metadataProfileId" withInt:self.metadataProfileId];
}

- (void)dealloc
{
    [self->_srcXslPath release];
    [self->_destXsdPath release];
    [super dealloc];
}

@end

@implementation KalturaCompareMetadataCondition
@synthesize xPath = _xPath;
@synthesize profileId = _profileId;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_profileId = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfXPath
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfProfileId
{
    return KFT_Int;
}

- (void)setProfileIdFromString:(NSString*)aPropVal
{
    self.profileId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCompareMetadataCondition"];
    [aParams addIfDefinedKey:@"xPath" withString:self.xPath];
    [aParams addIfDefinedKey:@"profileId" withInt:self.profileId];
}

- (void)dealloc
{
    [self->_xPath release];
    [super dealloc];
}

@end

@implementation KalturaMatchMetadataCondition
@synthesize xPath = _xPath;
@synthesize profileId = _profileId;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_profileId = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfXPath
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfProfileId
{
    return KFT_Int;
}

- (void)setProfileIdFromString:(NSString*)aPropVal
{
    self.profileId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaMatchMetadataCondition"];
    [aParams addIfDefinedKey:@"xPath" withString:self.xPath];
    [aParams addIfDefinedKey:@"profileId" withInt:self.profileId];
}

- (void)dealloc
{
    [self->_xPath release];
    [super dealloc];
}

@end

@implementation KalturaMetadataFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaMetadataFilter"];
}

@end

@implementation KalturaMetadataProfileFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaMetadataProfileFilter"];
}

@end

@implementation KalturaMetadataSearchItem
@synthesize metadataProfileId = _metadataProfileId;
@synthesize orderBy = _orderBy;

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

- (KalturaFieldType)getTypeOfOrderBy
{
    return KFT_String;
}

- (void)setMetadataProfileIdFromString:(NSString*)aPropVal
{
    self.metadataProfileId = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaMetadataSearchItem"];
    [aParams addIfDefinedKey:@"metadataProfileId" withInt:self.metadataProfileId];
    [aParams addIfDefinedKey:@"orderBy" withString:self.orderBy];
}

- (void)dealloc
{
    [self->_orderBy release];
    [super dealloc];
}

@end

///////////////////////// services /////////////////////////
@implementation KalturaMetadataService
- (KalturaMetadata*)addWithMetadataProfileId:(int)aMetadataProfileId withObjectType:(NSString*)aObjectType withObjectId:(NSString*)aObjectId withXmlData:(NSString*)aXmlData
{
    [self.client.params addIfDefinedKey:@"metadataProfileId" withInt:aMetadataProfileId];
    [self.client.params addIfDefinedKey:@"objectType" withString:aObjectType];
    [self.client.params addIfDefinedKey:@"objectId" withString:aObjectId];
    [self.client.params addIfDefinedKey:@"xmlData" withString:aXmlData];
    return [self.client queueObjectService:@"metadata_metadata" withAction:@"add" withExpectedType:@"KalturaMetadata"];
}

- (KalturaMetadata*)addFromFileWithMetadataProfileId:(int)aMetadataProfileId withObjectType:(NSString*)aObjectType withObjectId:(NSString*)aObjectId withXmlFile:(NSString*)aXmlFile
{
    [self.client.params addIfDefinedKey:@"metadataProfileId" withInt:aMetadataProfileId];
    [self.client.params addIfDefinedKey:@"objectType" withString:aObjectType];
    [self.client.params addIfDefinedKey:@"objectId" withString:aObjectId];
    [self.client.params addIfDefinedKey:@"xmlFile" withFileName:aXmlFile];
    return [self.client queueObjectService:@"metadata_metadata" withAction:@"addFromFile" withExpectedType:@"KalturaMetadata"];
}

- (KalturaMetadata*)addFromUrlWithMetadataProfileId:(int)aMetadataProfileId withObjectType:(NSString*)aObjectType withObjectId:(NSString*)aObjectId withUrl:(NSString*)aUrl
{
    [self.client.params addIfDefinedKey:@"metadataProfileId" withInt:aMetadataProfileId];
    [self.client.params addIfDefinedKey:@"objectType" withString:aObjectType];
    [self.client.params addIfDefinedKey:@"objectId" withString:aObjectId];
    [self.client.params addIfDefinedKey:@"url" withString:aUrl];
    return [self.client queueObjectService:@"metadata_metadata" withAction:@"addFromUrl" withExpectedType:@"KalturaMetadata"];
}

- (KalturaMetadata*)addFromBulkWithMetadataProfileId:(int)aMetadataProfileId withObjectType:(NSString*)aObjectType withObjectId:(NSString*)aObjectId withUrl:(NSString*)aUrl
{
    [self.client.params addIfDefinedKey:@"metadataProfileId" withInt:aMetadataProfileId];
    [self.client.params addIfDefinedKey:@"objectType" withString:aObjectType];
    [self.client.params addIfDefinedKey:@"objectId" withString:aObjectId];
    [self.client.params addIfDefinedKey:@"url" withString:aUrl];
    return [self.client queueObjectService:@"metadata_metadata" withAction:@"addFromBulk" withExpectedType:@"KalturaMetadata"];
}

- (KalturaMetadata*)getWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    return [self.client queueObjectService:@"metadata_metadata" withAction:@"get" withExpectedType:@"KalturaMetadata"];
}

- (KalturaMetadata*)updateWithId:(int)aId withXmlData:(NSString*)aXmlData withVersion:(int)aVersion
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"xmlData" withString:aXmlData];
    [self.client.params addIfDefinedKey:@"version" withInt:aVersion];
    return [self.client queueObjectService:@"metadata_metadata" withAction:@"update" withExpectedType:@"KalturaMetadata"];
}

- (KalturaMetadata*)updateWithId:(int)aId withXmlData:(NSString*)aXmlData
{
    return [self updateWithId:aId withXmlData:aXmlData withVersion:KALTURA_UNDEF_INT];
}

- (KalturaMetadata*)updateWithId:(int)aId
{
    return [self updateWithId:aId withXmlData:nil];
}

- (KalturaMetadata*)updateFromFileWithId:(int)aId withXmlFile:(NSString*)aXmlFile
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"xmlFile" withFileName:aXmlFile];
    return [self.client queueObjectService:@"metadata_metadata" withAction:@"updateFromFile" withExpectedType:@"KalturaMetadata"];
}

- (KalturaMetadata*)updateFromFileWithId:(int)aId
{
    return [self updateFromFileWithId:aId withXmlFile:nil];
}

- (KalturaMetadataListResponse*)listWithFilter:(KalturaMetadataFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"metadata_metadata" withAction:@"list" withExpectedType:@"KalturaMetadataListResponse"];
}

- (KalturaMetadataListResponse*)listWithFilter:(KalturaMetadataFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaMetadataListResponse*)list
{
    return [self listWithFilter:nil];
}

- (void)deleteWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client queueVoidService:@"metadata_metadata" withAction:@"delete"];
}

- (void)invalidateWithId:(int)aId withVersion:(int)aVersion
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"version" withInt:aVersion];
    [self.client queueVoidService:@"metadata_metadata" withAction:@"invalidate"];
}

- (void)invalidateWithId:(int)aId
{
    [self invalidateWithId:aId withVersion:KALTURA_UNDEF_INT];
}

- (NSString*)serveWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    return [self.client queueServeService:@"metadata_metadata" withAction:@"serve"];
}

- (KalturaMetadata*)updateFromXSLWithId:(int)aId withXslFile:(NSString*)aXslFile
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"xslFile" withFileName:aXslFile];
    return [self.client queueObjectService:@"metadata_metadata" withAction:@"updateFromXSL" withExpectedType:@"KalturaMetadata"];
}

@end

@implementation KalturaMetadataProfileService
- (KalturaMetadataProfile*)addWithMetadataProfile:(KalturaMetadataProfile*)aMetadataProfile withXsdData:(NSString*)aXsdData withViewsData:(NSString*)aViewsData
{
    [self.client.params addIfDefinedKey:@"metadataProfile" withObject:aMetadataProfile];
    [self.client.params addIfDefinedKey:@"xsdData" withString:aXsdData];
    [self.client.params addIfDefinedKey:@"viewsData" withString:aViewsData];
    return [self.client queueObjectService:@"metadata_metadataprofile" withAction:@"add" withExpectedType:@"KalturaMetadataProfile"];
}

- (KalturaMetadataProfile*)addWithMetadataProfile:(KalturaMetadataProfile*)aMetadataProfile withXsdData:(NSString*)aXsdData
{
    return [self addWithMetadataProfile:aMetadataProfile withXsdData:aXsdData withViewsData:nil];
}

- (KalturaMetadataProfile*)addFromFileWithMetadataProfile:(KalturaMetadataProfile*)aMetadataProfile withXsdFile:(NSString*)aXsdFile withViewsFile:(NSString*)aViewsFile
{
    [self.client.params addIfDefinedKey:@"metadataProfile" withObject:aMetadataProfile];
    [self.client.params addIfDefinedKey:@"xsdFile" withFileName:aXsdFile];
    [self.client.params addIfDefinedKey:@"viewsFile" withFileName:aViewsFile];
    return [self.client queueObjectService:@"metadata_metadataprofile" withAction:@"addFromFile" withExpectedType:@"KalturaMetadataProfile"];
}

- (KalturaMetadataProfile*)addFromFileWithMetadataProfile:(KalturaMetadataProfile*)aMetadataProfile withXsdFile:(NSString*)aXsdFile
{
    return [self addFromFileWithMetadataProfile:aMetadataProfile withXsdFile:aXsdFile withViewsFile:nil];
}

- (KalturaMetadataProfile*)getWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    return [self.client queueObjectService:@"metadata_metadataprofile" withAction:@"get" withExpectedType:@"KalturaMetadataProfile"];
}

- (KalturaMetadataProfile*)updateWithId:(int)aId withMetadataProfile:(KalturaMetadataProfile*)aMetadataProfile withXsdData:(NSString*)aXsdData withViewsData:(NSString*)aViewsData
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"metadataProfile" withObject:aMetadataProfile];
    [self.client.params addIfDefinedKey:@"xsdData" withString:aXsdData];
    [self.client.params addIfDefinedKey:@"viewsData" withString:aViewsData];
    return [self.client queueObjectService:@"metadata_metadataprofile" withAction:@"update" withExpectedType:@"KalturaMetadataProfile"];
}

- (KalturaMetadataProfile*)updateWithId:(int)aId withMetadataProfile:(KalturaMetadataProfile*)aMetadataProfile withXsdData:(NSString*)aXsdData
{
    return [self updateWithId:aId withMetadataProfile:aMetadataProfile withXsdData:aXsdData withViewsData:nil];
}

- (KalturaMetadataProfile*)updateWithId:(int)aId withMetadataProfile:(KalturaMetadataProfile*)aMetadataProfile
{
    return [self updateWithId:aId withMetadataProfile:aMetadataProfile withXsdData:nil];
}

- (KalturaMetadataProfileListResponse*)listWithFilter:(KalturaMetadataProfileFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"metadata_metadataprofile" withAction:@"list" withExpectedType:@"KalturaMetadataProfileListResponse"];
}

- (KalturaMetadataProfileListResponse*)listWithFilter:(KalturaMetadataProfileFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaMetadataProfileListResponse*)list
{
    return [self listWithFilter:nil];
}

- (KalturaMetadataProfileFieldListResponse*)listFieldsWithMetadataProfileId:(int)aMetadataProfileId
{
    [self.client.params addIfDefinedKey:@"metadataProfileId" withInt:aMetadataProfileId];
    return [self.client queueObjectService:@"metadata_metadataprofile" withAction:@"listFields" withExpectedType:@"KalturaMetadataProfileFieldListResponse"];
}

- (void)deleteWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client queueVoidService:@"metadata_metadataprofile" withAction:@"delete"];
}

- (KalturaMetadataProfile*)revertWithId:(int)aId withToVersion:(int)aToVersion
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"toVersion" withInt:aToVersion];
    return [self.client queueObjectService:@"metadata_metadataprofile" withAction:@"revert" withExpectedType:@"KalturaMetadataProfile"];
}

- (KalturaMetadataProfile*)updateDefinitionFromFileWithId:(int)aId withXsdFile:(NSString*)aXsdFile
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"xsdFile" withFileName:aXsdFile];
    return [self.client queueObjectService:@"metadata_metadataprofile" withAction:@"updateDefinitionFromFile" withExpectedType:@"KalturaMetadataProfile"];
}

- (KalturaMetadataProfile*)updateViewsFromFileWithId:(int)aId withViewsFile:(NSString*)aViewsFile
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"viewsFile" withFileName:aViewsFile];
    return [self.client queueObjectService:@"metadata_metadataprofile" withAction:@"updateViewsFromFile" withExpectedType:@"KalturaMetadataProfile"];
}

- (KalturaMetadataProfile*)updateTransformationFromFileWithId:(int)aId withXsltFile:(NSString*)aXsltFile
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    [self.client.params addIfDefinedKey:@"xsltFile" withFileName:aXsltFile];
    return [self.client queueObjectService:@"metadata_metadataprofile" withAction:@"updateTransformationFromFile" withExpectedType:@"KalturaMetadataProfile"];
}

- (NSString*)serveWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    return [self.client queueServeService:@"metadata_metadataprofile" withAction:@"serve"];
}

- (NSString*)serveViewWithId:(int)aId
{
    [self.client.params addIfDefinedKey:@"id" withInt:aId];
    return [self.client queueServeService:@"metadata_metadataprofile" withAction:@"serveView"];
}

@end

@implementation KalturaMetadataClientPlugin
@synthesize client = _client;

- (id)initWithClient:(KalturaClient*)aClient
{
    self = [super init];
    if (self == nil)
        return nil;
    self.client = aClient;
    return self;
}

- (KalturaMetadataService*)metadata
{
    if (self->_metadata == nil)
    	self->_metadata = [[KalturaMetadataService alloc] initWithClient:self.client];
    return self->_metadata;
}

- (KalturaMetadataProfileService*)metadataProfile
{
    if (self->_metadataProfile == nil)
    	self->_metadataProfile = [[KalturaMetadataProfileService alloc] initWithClient:self.client];
    return self->_metadataProfile;
}

- (void)dealloc
{
    [self->_metadata release];
    [self->_metadataProfile release];
	[super dealloc];
}

@end

