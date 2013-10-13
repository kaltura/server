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
// @package External
// @subpackage Kaltura
#import "../KalturaClient.h"

///////////////////////// enums /////////////////////////
// @package External
// @subpackage Kaltura
@interface KalturaFileSyncStatus : NSObject
+ (int)ERROR;
+ (int)PENDING;
+ (int)READY;
+ (int)DELETED;
+ (int)PURGED;
@end

// @package External
// @subpackage Kaltura
@interface KalturaFileSyncType : NSObject
+ (int)FILE;
+ (int)LINK;
+ (int)URL;
@end

// @package External
// @subpackage Kaltura
@interface KalturaFileSyncOrderBy : NSObject
+ (NSString*)CREATED_AT_ASC;
+ (NSString*)FILE_SIZE_ASC;
+ (NSString*)READY_AT_ASC;
+ (NSString*)SYNC_TIME_ASC;
+ (NSString*)UPDATED_AT_ASC;
+ (NSString*)CREATED_AT_DESC;
+ (NSString*)FILE_SIZE_DESC;
+ (NSString*)READY_AT_DESC;
+ (NSString*)SYNC_TIME_DESC;
+ (NSString*)UPDATED_AT_DESC;
@end

///////////////////////// classes /////////////////////////
// @package External
// @subpackage Kaltura
@interface KalturaFileSyncBaseFilter : KalturaFilter
@property (nonatomic,assign) int partnerIdEqual;
@property (nonatomic,copy) NSString* fileObjectTypeEqual;	// enum KalturaFileSyncObjectType
@property (nonatomic,copy) NSString* fileObjectTypeIn;
@property (nonatomic,copy) NSString* objectIdEqual;
@property (nonatomic,copy) NSString* objectIdIn;
@property (nonatomic,copy) NSString* versionEqual;
@property (nonatomic,copy) NSString* versionIn;
@property (nonatomic,assign) int objectSubTypeEqual;
@property (nonatomic,copy) NSString* objectSubTypeIn;
@property (nonatomic,copy) NSString* dcEqual;
@property (nonatomic,copy) NSString* dcIn;
@property (nonatomic,assign) int originalEqual;
@property (nonatomic,assign) int createdAtGreaterThanOrEqual;
@property (nonatomic,assign) int createdAtLessThanOrEqual;
@property (nonatomic,assign) int updatedAtGreaterThanOrEqual;
@property (nonatomic,assign) int updatedAtLessThanOrEqual;
@property (nonatomic,assign) int readyAtGreaterThanOrEqual;
@property (nonatomic,assign) int readyAtLessThanOrEqual;
@property (nonatomic,assign) int syncTimeGreaterThanOrEqual;
@property (nonatomic,assign) int syncTimeLessThanOrEqual;
@property (nonatomic,assign) int statusEqual;	// enum KalturaFileSyncStatus
@property (nonatomic,copy) NSString* statusIn;
@property (nonatomic,assign) int fileTypeEqual;	// enum KalturaFileSyncType
@property (nonatomic,copy) NSString* fileTypeIn;
@property (nonatomic,assign) int linkedIdEqual;
@property (nonatomic,assign) int linkCountGreaterThanOrEqual;
@property (nonatomic,assign) int linkCountLessThanOrEqual;
@property (nonatomic,assign) double fileSizeGreaterThanOrEqual;
@property (nonatomic,assign) double fileSizeLessThanOrEqual;
- (KalturaFieldType)getTypeOfPartnerIdEqual;
- (KalturaFieldType)getTypeOfFileObjectTypeEqual;
- (KalturaFieldType)getTypeOfFileObjectTypeIn;
- (KalturaFieldType)getTypeOfObjectIdEqual;
- (KalturaFieldType)getTypeOfObjectIdIn;
- (KalturaFieldType)getTypeOfVersionEqual;
- (KalturaFieldType)getTypeOfVersionIn;
- (KalturaFieldType)getTypeOfObjectSubTypeEqual;
- (KalturaFieldType)getTypeOfObjectSubTypeIn;
- (KalturaFieldType)getTypeOfDcEqual;
- (KalturaFieldType)getTypeOfDcIn;
- (KalturaFieldType)getTypeOfOriginalEqual;
- (KalturaFieldType)getTypeOfCreatedAtGreaterThanOrEqual;
- (KalturaFieldType)getTypeOfCreatedAtLessThanOrEqual;
- (KalturaFieldType)getTypeOfUpdatedAtGreaterThanOrEqual;
- (KalturaFieldType)getTypeOfUpdatedAtLessThanOrEqual;
- (KalturaFieldType)getTypeOfReadyAtGreaterThanOrEqual;
- (KalturaFieldType)getTypeOfReadyAtLessThanOrEqual;
- (KalturaFieldType)getTypeOfSyncTimeGreaterThanOrEqual;
- (KalturaFieldType)getTypeOfSyncTimeLessThanOrEqual;
- (KalturaFieldType)getTypeOfStatusEqual;
- (KalturaFieldType)getTypeOfStatusIn;
- (KalturaFieldType)getTypeOfFileTypeEqual;
- (KalturaFieldType)getTypeOfFileTypeIn;
- (KalturaFieldType)getTypeOfLinkedIdEqual;
- (KalturaFieldType)getTypeOfLinkCountGreaterThanOrEqual;
- (KalturaFieldType)getTypeOfLinkCountLessThanOrEqual;
- (KalturaFieldType)getTypeOfFileSizeGreaterThanOrEqual;
- (KalturaFieldType)getTypeOfFileSizeLessThanOrEqual;
- (void)setPartnerIdEqualFromString:(NSString*)aPropVal;
- (void)setObjectSubTypeEqualFromString:(NSString*)aPropVal;
- (void)setOriginalEqualFromString:(NSString*)aPropVal;
- (void)setCreatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal;
- (void)setCreatedAtLessThanOrEqualFromString:(NSString*)aPropVal;
- (void)setUpdatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal;
- (void)setUpdatedAtLessThanOrEqualFromString:(NSString*)aPropVal;
- (void)setReadyAtGreaterThanOrEqualFromString:(NSString*)aPropVal;
- (void)setReadyAtLessThanOrEqualFromString:(NSString*)aPropVal;
- (void)setSyncTimeGreaterThanOrEqualFromString:(NSString*)aPropVal;
- (void)setSyncTimeLessThanOrEqualFromString:(NSString*)aPropVal;
- (void)setStatusEqualFromString:(NSString*)aPropVal;
- (void)setFileTypeEqualFromString:(NSString*)aPropVal;
- (void)setLinkedIdEqualFromString:(NSString*)aPropVal;
- (void)setLinkCountGreaterThanOrEqualFromString:(NSString*)aPropVal;
- (void)setLinkCountLessThanOrEqualFromString:(NSString*)aPropVal;
- (void)setFileSizeGreaterThanOrEqualFromString:(NSString*)aPropVal;
- (void)setFileSizeLessThanOrEqualFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
@interface KalturaFileSyncFilter : KalturaFileSyncBaseFilter
@end

///////////////////////// services /////////////////////////
