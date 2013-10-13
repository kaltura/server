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
@interface KalturaCaptionAssetStatus : NSObject
+ (int)ERROR;
+ (int)QUEUED;
+ (int)READY;
+ (int)DELETED;
+ (int)IMPORTING;
+ (int)EXPORTING;
@end

// @package External
// @subpackage Kaltura
@interface KalturaCaptionAssetOrderBy : NSObject
+ (NSString*)CREATED_AT_ASC;
+ (NSString*)DELETED_AT_ASC;
+ (NSString*)SIZE_ASC;
+ (NSString*)UPDATED_AT_ASC;
+ (NSString*)CREATED_AT_DESC;
+ (NSString*)DELETED_AT_DESC;
+ (NSString*)SIZE_DESC;
+ (NSString*)UPDATED_AT_DESC;
@end

// @package External
// @subpackage Kaltura
@interface KalturaCaptionParamsOrderBy : NSObject
@end

// @package External
// @subpackage Kaltura
@interface KalturaCaptionType : NSObject
+ (NSString*)SRT;
+ (NSString*)DFXP;
+ (NSString*)WEBVTT;
@end

///////////////////////// classes /////////////////////////
// @package External
// @subpackage Kaltura
@interface KalturaCaptionAsset : KalturaAsset
// The Caption Params used to create this Caption Asset
@property (nonatomic,assign) int captionParamsId;	// insertonly
// The language of the caption asset content
@property (nonatomic,copy) NSString* language;	// enum KalturaLanguage
// The language of the caption asset content
@property (nonatomic,copy,readonly) NSString* languageCode;	// enum KalturaLanguageCode
// Is default caption asset of the entry
@property (nonatomic,assign) int isDefault;	// enum KalturaNullableBoolean
// Friendly label
@property (nonatomic,copy) NSString* label;
// The caption format
@property (nonatomic,copy) NSString* format;	// enum KalturaCaptionType, insertonly
// The status of the asset
@property (nonatomic,assign,readonly) int status;	// enum KalturaCaptionAssetStatus
- (KalturaFieldType)getTypeOfCaptionParamsId;
- (KalturaFieldType)getTypeOfLanguage;
- (KalturaFieldType)getTypeOfLanguageCode;
- (KalturaFieldType)getTypeOfIsDefault;
- (KalturaFieldType)getTypeOfLabel;
- (KalturaFieldType)getTypeOfFormat;
- (KalturaFieldType)getTypeOfStatus;
- (void)setCaptionParamsIdFromString:(NSString*)aPropVal;
- (void)setIsDefaultFromString:(NSString*)aPropVal;
- (void)setStatusFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
@interface KalturaCaptionAssetListResponse : KalturaObjectBase
@property (nonatomic,retain,readonly) NSMutableArray* objects;	// of KalturaCaptionAsset elements
@property (nonatomic,assign,readonly) int totalCount;
- (KalturaFieldType)getTypeOfObjects;
- (NSString*)getObjectTypeOfObjects;
- (KalturaFieldType)getTypeOfTotalCount;
- (void)setTotalCountFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
@interface KalturaCaptionParams : KalturaAssetParams
// The language of the caption content
@property (nonatomic,copy) NSString* language;	// enum KalturaLanguage, insertonly
// Is default caption asset of the entry
@property (nonatomic,assign) int isDefault;	// enum KalturaNullableBoolean
// Friendly label
@property (nonatomic,copy) NSString* label;
// The caption format
@property (nonatomic,copy) NSString* format;	// enum KalturaCaptionType, insertonly
// Id of the caption params or the flavor params to be used as source for the caption creation
@property (nonatomic,assign) int sourceParamsId;
- (KalturaFieldType)getTypeOfLanguage;
- (KalturaFieldType)getTypeOfIsDefault;
- (KalturaFieldType)getTypeOfLabel;
- (KalturaFieldType)getTypeOfFormat;
- (KalturaFieldType)getTypeOfSourceParamsId;
- (void)setIsDefaultFromString:(NSString*)aPropVal;
- (void)setSourceParamsIdFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
@interface KalturaCaptionParamsListResponse : KalturaObjectBase
@property (nonatomic,retain,readonly) NSMutableArray* objects;	// of KalturaCaptionParams elements
@property (nonatomic,assign,readonly) int totalCount;
- (KalturaFieldType)getTypeOfObjects;
- (NSString*)getObjectTypeOfObjects;
- (KalturaFieldType)getTypeOfTotalCount;
- (void)setTotalCountFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
@interface KalturaCaptionAssetBaseFilter : KalturaAssetFilter
@property (nonatomic,assign) int captionParamsIdEqual;
@property (nonatomic,copy) NSString* captionParamsIdIn;
@property (nonatomic,copy) NSString* formatEqual;	// enum KalturaCaptionType
@property (nonatomic,copy) NSString* formatIn;
@property (nonatomic,assign) int statusEqual;	// enum KalturaCaptionAssetStatus
@property (nonatomic,copy) NSString* statusIn;
@property (nonatomic,copy) NSString* statusNotIn;
- (KalturaFieldType)getTypeOfCaptionParamsIdEqual;
- (KalturaFieldType)getTypeOfCaptionParamsIdIn;
- (KalturaFieldType)getTypeOfFormatEqual;
- (KalturaFieldType)getTypeOfFormatIn;
- (KalturaFieldType)getTypeOfStatusEqual;
- (KalturaFieldType)getTypeOfStatusIn;
- (KalturaFieldType)getTypeOfStatusNotIn;
- (void)setCaptionParamsIdEqualFromString:(NSString*)aPropVal;
- (void)setStatusEqualFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
@interface KalturaCaptionParamsBaseFilter : KalturaAssetParamsFilter
@property (nonatomic,copy) NSString* formatEqual;	// enum KalturaCaptionType
@property (nonatomic,copy) NSString* formatIn;
- (KalturaFieldType)getTypeOfFormatEqual;
- (KalturaFieldType)getTypeOfFormatIn;
@end

// @package External
// @subpackage Kaltura
@interface KalturaCaptionAssetFilter : KalturaCaptionAssetBaseFilter
@end

// @package External
// @subpackage Kaltura
@interface KalturaCaptionParamsFilter : KalturaCaptionParamsBaseFilter
@end

///////////////////////// services /////////////////////////
// @package External
// @subpackage Kaltura
// Retrieve information and invoke actions on caption Asset
@interface KalturaCaptionAssetService : KalturaServiceBase
// Add caption asset
- (KalturaCaptionAsset*)addWithEntryId:(NSString*)aEntryId withCaptionAsset:(KalturaCaptionAsset*)aCaptionAsset;
// Update content of caption asset
- (KalturaCaptionAsset*)setContentWithId:(NSString*)aId withContentResource:(KalturaContentResource*)aContentResource;
// Update caption asset
- (KalturaCaptionAsset*)updateWithId:(NSString*)aId withCaptionAsset:(KalturaCaptionAsset*)aCaptionAsset;
// Serves caption by entry id and thumnail params id
- (NSString*)serveByEntryIdWithEntryId:(NSString*)aEntryId withCaptionParamId:(int)aCaptionParamId;
- (NSString*)serveByEntryIdWithEntryId:(NSString*)aEntryId;
// Get download URL for the asset
- (NSString*)getUrlWithId:(NSString*)aId withStorageId:(int)aStorageId;
- (NSString*)getUrlWithId:(NSString*)aId;
// Get remote storage existing paths for the asset
- (KalturaRemotePathListResponse*)getRemotePathsWithId:(NSString*)aId;
// Serves caption by its id
- (NSString*)serveWithCaptionAssetId:(NSString*)aCaptionAssetId;
// Markss the caption as default and removes that mark from all other caption assets of the entry.
- (void)setAsDefaultWithCaptionAssetId:(NSString*)aCaptionAssetId;
- (KalturaCaptionAsset*)getWithCaptionAssetId:(NSString*)aCaptionAssetId;
// List caption Assets by filter and pager
- (KalturaCaptionAssetListResponse*)listWithFilter:(KalturaAssetFilter*)aFilter withPager:(KalturaFilterPager*)aPager;
- (KalturaCaptionAssetListResponse*)listWithFilter:(KalturaAssetFilter*)aFilter;
- (KalturaCaptionAssetListResponse*)list;
- (void)deleteWithCaptionAssetId:(NSString*)aCaptionAssetId;
@end

// @package External
// @subpackage Kaltura
// Add & Manage Caption Params
@interface KalturaCaptionParamsService : KalturaServiceBase
// Add new Caption Params
- (KalturaCaptionParams*)addWithCaptionParams:(KalturaCaptionParams*)aCaptionParams;
// Get Caption Params by ID
- (KalturaCaptionParams*)getWithId:(int)aId;
// Update Caption Params by ID
- (KalturaCaptionParams*)updateWithId:(int)aId withCaptionParams:(KalturaCaptionParams*)aCaptionParams;
// Delete Caption Params by ID
- (void)deleteWithId:(int)aId;
// List Caption Params by filter with paging support (By default - all system default params will be listed too)
- (KalturaCaptionParamsListResponse*)listWithFilter:(KalturaCaptionParamsFilter*)aFilter withPager:(KalturaFilterPager*)aPager;
- (KalturaCaptionParamsListResponse*)listWithFilter:(KalturaCaptionParamsFilter*)aFilter;
- (KalturaCaptionParamsListResponse*)list;
@end

@interface KalturaCaptionClientPlugin : KalturaClientPlugin
{
	KalturaCaptionAssetService* _captionAsset;
	KalturaCaptionParamsService* _captionParams;
}

@property (nonatomic, assign) KalturaClientBase* client;
@property (nonatomic, readonly) KalturaCaptionAssetService* captionAsset;
@property (nonatomic, readonly) KalturaCaptionParamsService* captionParams;
@end

