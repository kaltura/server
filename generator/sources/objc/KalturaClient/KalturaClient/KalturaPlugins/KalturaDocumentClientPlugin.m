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
#import "KalturaDocumentClientPlugin.h"

///////////////////////// enums /////////////////////////
@implementation KalturaDocumentType
+ (int)DOCUMENT
{
    return 11;
}
+ (int)SWF
{
    return 12;
}
+ (int)PDF
{
    return 13;
}
@end

@implementation KalturaDocumentEntryOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)END_DATE_ASC
{
    return @"+endDate";
}
+ (NSString*)MODERATION_COUNT_ASC
{
    return @"+moderationCount";
}
+ (NSString*)NAME_ASC
{
    return @"+name";
}
+ (NSString*)PARTNER_SORT_VALUE_ASC
{
    return @"+partnerSortValue";
}
+ (NSString*)RANK_ASC
{
    return @"+rank";
}
+ (NSString*)RECENT_ASC
{
    return @"+recent";
}
+ (NSString*)START_DATE_ASC
{
    return @"+startDate";
}
+ (NSString*)TOTAL_RANK_ASC
{
    return @"+totalRank";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)WEIGHT_ASC
{
    return @"+weight";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)END_DATE_DESC
{
    return @"-endDate";
}
+ (NSString*)MODERATION_COUNT_DESC
{
    return @"-moderationCount";
}
+ (NSString*)NAME_DESC
{
    return @"-name";
}
+ (NSString*)PARTNER_SORT_VALUE_DESC
{
    return @"-partnerSortValue";
}
+ (NSString*)RANK_DESC
{
    return @"-rank";
}
+ (NSString*)RECENT_DESC
{
    return @"-recent";
}
+ (NSString*)START_DATE_DESC
{
    return @"-startDate";
}
+ (NSString*)TOTAL_RANK_DESC
{
    return @"-totalRank";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
+ (NSString*)WEIGHT_DESC
{
    return @"-weight";
}
@end

@implementation KalturaDocumentFlavorParamsOrderBy
@end

@implementation KalturaDocumentFlavorParamsOutputOrderBy
@end

@implementation KalturaImageFlavorParamsOrderBy
@end

@implementation KalturaImageFlavorParamsOutputOrderBy
@end

@implementation KalturaPdfFlavorParamsOrderBy
@end

@implementation KalturaPdfFlavorParamsOutputOrderBy
@end

@implementation KalturaSwfFlavorParamsOrderBy
@end

@implementation KalturaSwfFlavorParamsOutputOrderBy
@end

///////////////////////// classes /////////////////////////
@interface KalturaDocumentEntry()
@property (nonatomic,copy) NSString* assetParamsIds;
@end

@implementation KalturaDocumentEntry
@synthesize documentType = _documentType;
@synthesize assetParamsIds = _assetParamsIds;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_documentType = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfDocumentType
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfAssetParamsIds
{
    return KFT_String;
}

- (void)setDocumentTypeFromString:(NSString*)aPropVal
{
    self.documentType = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDocumentEntry"];
    [aParams addIfDefinedKey:@"documentType" withInt:self.documentType];
}

- (void)dealloc
{
    [self->_assetParamsIds release];
    [super dealloc];
}

@end

@interface KalturaDocumentListResponse()
@property (nonatomic,retain) NSMutableArray* objects;
@property (nonatomic,assign) int totalCount;
@end

@implementation KalturaDocumentListResponse
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
    return @"KalturaDocumentEntry";
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
        [aParams putKey:@"objectType" withString:@"KalturaDocumentListResponse"];
}

- (void)dealloc
{
    [self->_objects release];
    [super dealloc];
}

@end

@implementation KalturaDocumentFlavorParams
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDocumentFlavorParams"];
}

@end

@implementation KalturaImageFlavorParams
@synthesize densityWidth = _densityWidth;
@synthesize densityHeight = _densityHeight;
@synthesize sizeWidth = _sizeWidth;
@synthesize sizeHeight = _sizeHeight;
@synthesize depth = _depth;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_densityWidth = KALTURA_UNDEF_INT;
    self->_densityHeight = KALTURA_UNDEF_INT;
    self->_sizeWidth = KALTURA_UNDEF_INT;
    self->_sizeHeight = KALTURA_UNDEF_INT;
    self->_depth = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfDensityWidth
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDensityHeight
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfSizeWidth
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfSizeHeight
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDepth
{
    return KFT_Int;
}

- (void)setDensityWidthFromString:(NSString*)aPropVal
{
    self.densityWidth = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDensityHeightFromString:(NSString*)aPropVal
{
    self.densityHeight = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setSizeWidthFromString:(NSString*)aPropVal
{
    self.sizeWidth = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setSizeHeightFromString:(NSString*)aPropVal
{
    self.sizeHeight = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDepthFromString:(NSString*)aPropVal
{
    self.depth = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaImageFlavorParams"];
    [aParams addIfDefinedKey:@"densityWidth" withInt:self.densityWidth];
    [aParams addIfDefinedKey:@"densityHeight" withInt:self.densityHeight];
    [aParams addIfDefinedKey:@"sizeWidth" withInt:self.sizeWidth];
    [aParams addIfDefinedKey:@"sizeHeight" withInt:self.sizeHeight];
    [aParams addIfDefinedKey:@"depth" withInt:self.depth];
}

@end

@implementation KalturaPdfFlavorParams
@synthesize readonly = _readonly;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_readonly = KALTURA_UNDEF_BOOL;
    return self;
}

- (KalturaFieldType)getTypeOfReadonly
{
    return KFT_Bool;
}

- (void)setReadonlyFromString:(NSString*)aPropVal
{
    self.readonly = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaPdfFlavorParams"];
    [aParams addIfDefinedKey:@"readonly" withBool:self.readonly];
}

@end

@implementation KalturaSwfFlavorParams
@synthesize flashVersion = _flashVersion;
@synthesize poly2Bitmap = _poly2Bitmap;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_flashVersion = KALTURA_UNDEF_INT;
    self->_poly2Bitmap = KALTURA_UNDEF_BOOL;
    return self;
}

- (KalturaFieldType)getTypeOfFlashVersion
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPoly2Bitmap
{
    return KFT_Bool;
}

- (void)setFlashVersionFromString:(NSString*)aPropVal
{
    self.flashVersion = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPoly2BitmapFromString:(NSString*)aPropVal
{
    self.poly2Bitmap = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaSwfFlavorParams"];
    [aParams addIfDefinedKey:@"flashVersion" withInt:self.flashVersion];
    [aParams addIfDefinedKey:@"poly2Bitmap" withBool:self.poly2Bitmap];
}

@end

@implementation KalturaDocumentEntryBaseFilter
@synthesize documentTypeEqual = _documentTypeEqual;
@synthesize documentTypeIn = _documentTypeIn;
@synthesize assetParamsIdsMatchOr = _assetParamsIdsMatchOr;
@synthesize assetParamsIdsMatchAnd = _assetParamsIdsMatchAnd;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_documentTypeEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfDocumentTypeEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDocumentTypeIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAssetParamsIdsMatchOr
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfAssetParamsIdsMatchAnd
{
    return KFT_String;
}

- (void)setDocumentTypeEqualFromString:(NSString*)aPropVal
{
    self.documentTypeEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDocumentEntryBaseFilter"];
    [aParams addIfDefinedKey:@"documentTypeEqual" withInt:self.documentTypeEqual];
    [aParams addIfDefinedKey:@"documentTypeIn" withString:self.documentTypeIn];
    [aParams addIfDefinedKey:@"assetParamsIdsMatchOr" withString:self.assetParamsIdsMatchOr];
    [aParams addIfDefinedKey:@"assetParamsIdsMatchAnd" withString:self.assetParamsIdsMatchAnd];
}

- (void)dealloc
{
    [self->_documentTypeIn release];
    [self->_assetParamsIdsMatchOr release];
    [self->_assetParamsIdsMatchAnd release];
    [super dealloc];
}

@end

@implementation KalturaDocumentFlavorParamsOutput
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDocumentFlavorParamsOutput"];
}

@end

@implementation KalturaImageFlavorParamsOutput
@synthesize densityWidth = _densityWidth;
@synthesize densityHeight = _densityHeight;
@synthesize sizeWidth = _sizeWidth;
@synthesize sizeHeight = _sizeHeight;
@synthesize depth = _depth;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_densityWidth = KALTURA_UNDEF_INT;
    self->_densityHeight = KALTURA_UNDEF_INT;
    self->_sizeWidth = KALTURA_UNDEF_INT;
    self->_sizeHeight = KALTURA_UNDEF_INT;
    self->_depth = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfDensityWidth
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDensityHeight
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfSizeWidth
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfSizeHeight
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfDepth
{
    return KFT_Int;
}

- (void)setDensityWidthFromString:(NSString*)aPropVal
{
    self.densityWidth = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDensityHeightFromString:(NSString*)aPropVal
{
    self.densityHeight = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setSizeWidthFromString:(NSString*)aPropVal
{
    self.sizeWidth = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setSizeHeightFromString:(NSString*)aPropVal
{
    self.sizeHeight = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setDepthFromString:(NSString*)aPropVal
{
    self.depth = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaImageFlavorParamsOutput"];
    [aParams addIfDefinedKey:@"densityWidth" withInt:self.densityWidth];
    [aParams addIfDefinedKey:@"densityHeight" withInt:self.densityHeight];
    [aParams addIfDefinedKey:@"sizeWidth" withInt:self.sizeWidth];
    [aParams addIfDefinedKey:@"sizeHeight" withInt:self.sizeHeight];
    [aParams addIfDefinedKey:@"depth" withInt:self.depth];
}

@end

@implementation KalturaPdfFlavorParamsOutput
@synthesize readonly = _readonly;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_readonly = KALTURA_UNDEF_BOOL;
    return self;
}

- (KalturaFieldType)getTypeOfReadonly
{
    return KFT_Bool;
}

- (void)setReadonlyFromString:(NSString*)aPropVal
{
    self.readonly = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaPdfFlavorParamsOutput"];
    [aParams addIfDefinedKey:@"readonly" withBool:self.readonly];
}

@end

@implementation KalturaSwfFlavorParamsOutput
@synthesize flashVersion = _flashVersion;
@synthesize poly2Bitmap = _poly2Bitmap;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_flashVersion = KALTURA_UNDEF_INT;
    self->_poly2Bitmap = KALTURA_UNDEF_BOOL;
    return self;
}

- (KalturaFieldType)getTypeOfFlashVersion
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfPoly2Bitmap
{
    return KFT_Bool;
}

- (void)setFlashVersionFromString:(NSString*)aPropVal
{
    self.flashVersion = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setPoly2BitmapFromString:(NSString*)aPropVal
{
    self.poly2Bitmap = [KalturaSimpleTypeParser parseBool:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaSwfFlavorParamsOutput"];
    [aParams addIfDefinedKey:@"flashVersion" withInt:self.flashVersion];
    [aParams addIfDefinedKey:@"poly2Bitmap" withBool:self.poly2Bitmap];
}

@end

@implementation KalturaDocumentEntryFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDocumentEntryFilter"];
}

@end

@implementation KalturaDocumentFlavorParamsBaseFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDocumentFlavorParamsBaseFilter"];
}

@end

@implementation KalturaImageFlavorParamsBaseFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaImageFlavorParamsBaseFilter"];
}

@end

@implementation KalturaPdfFlavorParamsBaseFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaPdfFlavorParamsBaseFilter"];
}

@end

@implementation KalturaSwfFlavorParamsBaseFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaSwfFlavorParamsBaseFilter"];
}

@end

@implementation KalturaDocumentFlavorParamsFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDocumentFlavorParamsFilter"];
}

@end

@implementation KalturaImageFlavorParamsFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaImageFlavorParamsFilter"];
}

@end

@implementation KalturaPdfFlavorParamsFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaPdfFlavorParamsFilter"];
}

@end

@implementation KalturaSwfFlavorParamsFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaSwfFlavorParamsFilter"];
}

@end

@implementation KalturaDocumentFlavorParamsOutputBaseFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDocumentFlavorParamsOutputBaseFilter"];
}

@end

@implementation KalturaImageFlavorParamsOutputBaseFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaImageFlavorParamsOutputBaseFilter"];
}

@end

@implementation KalturaPdfFlavorParamsOutputBaseFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaPdfFlavorParamsOutputBaseFilter"];
}

@end

@implementation KalturaSwfFlavorParamsOutputBaseFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaSwfFlavorParamsOutputBaseFilter"];
}

@end

@implementation KalturaDocumentFlavorParamsOutputFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaDocumentFlavorParamsOutputFilter"];
}

@end

@implementation KalturaImageFlavorParamsOutputFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaImageFlavorParamsOutputFilter"];
}

@end

@implementation KalturaPdfFlavorParamsOutputFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaPdfFlavorParamsOutputFilter"];
}

@end

@implementation KalturaSwfFlavorParamsOutputFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaSwfFlavorParamsOutputFilter"];
}

@end

///////////////////////// services /////////////////////////
@implementation KalturaDocumentsService
- (KalturaDocumentEntry*)addFromUploadedFileWithDocumentEntry:(KalturaDocumentEntry*)aDocumentEntry withUploadTokenId:(NSString*)aUploadTokenId
{
    [self.client.params addIfDefinedKey:@"documentEntry" withObject:aDocumentEntry];
    [self.client.params addIfDefinedKey:@"uploadTokenId" withString:aUploadTokenId];
    return [self.client queueObjectService:@"document_documents" withAction:@"addFromUploadedFile" withExpectedType:@"KalturaDocumentEntry"];
}

- (KalturaDocumentEntry*)addFromEntryWithSourceEntryId:(NSString*)aSourceEntryId withDocumentEntry:(KalturaDocumentEntry*)aDocumentEntry withSourceFlavorParamsId:(int)aSourceFlavorParamsId
{
    [self.client.params addIfDefinedKey:@"sourceEntryId" withString:aSourceEntryId];
    [self.client.params addIfDefinedKey:@"documentEntry" withObject:aDocumentEntry];
    [self.client.params addIfDefinedKey:@"sourceFlavorParamsId" withInt:aSourceFlavorParamsId];
    return [self.client queueObjectService:@"document_documents" withAction:@"addFromEntry" withExpectedType:@"KalturaDocumentEntry"];
}

- (KalturaDocumentEntry*)addFromEntryWithSourceEntryId:(NSString*)aSourceEntryId withDocumentEntry:(KalturaDocumentEntry*)aDocumentEntry
{
    return [self addFromEntryWithSourceEntryId:aSourceEntryId withDocumentEntry:aDocumentEntry withSourceFlavorParamsId:KALTURA_UNDEF_INT];
}

- (KalturaDocumentEntry*)addFromEntryWithSourceEntryId:(NSString*)aSourceEntryId
{
    return [self addFromEntryWithSourceEntryId:aSourceEntryId withDocumentEntry:nil];
}

- (KalturaDocumentEntry*)addFromFlavorAssetWithSourceFlavorAssetId:(NSString*)aSourceFlavorAssetId withDocumentEntry:(KalturaDocumentEntry*)aDocumentEntry
{
    [self.client.params addIfDefinedKey:@"sourceFlavorAssetId" withString:aSourceFlavorAssetId];
    [self.client.params addIfDefinedKey:@"documentEntry" withObject:aDocumentEntry];
    return [self.client queueObjectService:@"document_documents" withAction:@"addFromFlavorAsset" withExpectedType:@"KalturaDocumentEntry"];
}

- (KalturaDocumentEntry*)addFromFlavorAssetWithSourceFlavorAssetId:(NSString*)aSourceFlavorAssetId
{
    return [self addFromFlavorAssetWithSourceFlavorAssetId:aSourceFlavorAssetId withDocumentEntry:nil];
}

- (int)convertWithEntryId:(NSString*)aEntryId withConversionProfileId:(int)aConversionProfileId withDynamicConversionAttributes:(NSArray*)aDynamicConversionAttributes
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"conversionProfileId" withInt:aConversionProfileId];
    [self.client.params addIfDefinedKey:@"dynamicConversionAttributes" withArray:aDynamicConversionAttributes];
    return [self.client queueIntService:@"document_documents" withAction:@"convert"];
}

- (int)convertWithEntryId:(NSString*)aEntryId withConversionProfileId:(int)aConversionProfileId
{
    return [self convertWithEntryId:aEntryId withConversionProfileId:aConversionProfileId withDynamicConversionAttributes:nil];
}

- (int)convertWithEntryId:(NSString*)aEntryId
{
    return [self convertWithEntryId:aEntryId withConversionProfileId:KALTURA_UNDEF_INT];
}

- (KalturaDocumentEntry*)getWithEntryId:(NSString*)aEntryId withVersion:(int)aVersion
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"version" withInt:aVersion];
    return [self.client queueObjectService:@"document_documents" withAction:@"get" withExpectedType:@"KalturaDocumentEntry"];
}

- (KalturaDocumentEntry*)getWithEntryId:(NSString*)aEntryId
{
    return [self getWithEntryId:aEntryId withVersion:KALTURA_UNDEF_INT];
}

- (KalturaDocumentEntry*)updateWithEntryId:(NSString*)aEntryId withDocumentEntry:(KalturaDocumentEntry*)aDocumentEntry
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"documentEntry" withObject:aDocumentEntry];
    return [self.client queueObjectService:@"document_documents" withAction:@"update" withExpectedType:@"KalturaDocumentEntry"];
}

- (void)deleteWithEntryId:(NSString*)aEntryId
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client queueVoidService:@"document_documents" withAction:@"delete"];
}

- (KalturaDocumentListResponse*)listWithFilter:(KalturaDocumentEntryFilter*)aFilter withPager:(KalturaFilterPager*)aPager
{
    [self.client.params addIfDefinedKey:@"filter" withObject:aFilter];
    [self.client.params addIfDefinedKey:@"pager" withObject:aPager];
    return [self.client queueObjectService:@"document_documents" withAction:@"list" withExpectedType:@"KalturaDocumentListResponse"];
}

- (KalturaDocumentListResponse*)listWithFilter:(KalturaDocumentEntryFilter*)aFilter
{
    return [self listWithFilter:aFilter withPager:nil];
}

- (KalturaDocumentListResponse*)list
{
    return [self listWithFilter:nil];
}

- (NSString*)uploadWithFileData:(NSString*)aFileData
{
    [self.client.params addIfDefinedKey:@"fileData" withFileName:aFileData];
    return [self.client queueStringService:@"document_documents" withAction:@"upload"];
}

- (NSString*)convertPptToSwfWithEntryId:(NSString*)aEntryId
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    return [self.client queueStringService:@"document_documents" withAction:@"convertPptToSwf"];
}

- (NSString*)serveWithEntryId:(NSString*)aEntryId withFlavorAssetId:(NSString*)aFlavorAssetId withForceProxy:(BOOL)aForceProxy
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"flavorAssetId" withString:aFlavorAssetId];
    [self.client.params addIfDefinedKey:@"forceProxy" withBool:aForceProxy];
    return [self.client queueServeService:@"document_documents" withAction:@"serve"];
}

- (NSString*)serveWithEntryId:(NSString*)aEntryId withFlavorAssetId:(NSString*)aFlavorAssetId
{
    return [self serveWithEntryId:aEntryId withFlavorAssetId:aFlavorAssetId withForceProxy:KALTURA_UNDEF_BOOL];
}

- (NSString*)serveWithEntryId:(NSString*)aEntryId
{
    return [self serveWithEntryId:aEntryId withFlavorAssetId:nil];
}

- (NSString*)serveByFlavorParamsIdWithEntryId:(NSString*)aEntryId withFlavorParamsId:(NSString*)aFlavorParamsId withForceProxy:(BOOL)aForceProxy
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"flavorParamsId" withString:aFlavorParamsId];
    [self.client.params addIfDefinedKey:@"forceProxy" withBool:aForceProxy];
    return [self.client queueServeService:@"document_documents" withAction:@"serveByFlavorParamsId"];
}

- (NSString*)serveByFlavorParamsIdWithEntryId:(NSString*)aEntryId withFlavorParamsId:(NSString*)aFlavorParamsId
{
    return [self serveByFlavorParamsIdWithEntryId:aEntryId withFlavorParamsId:aFlavorParamsId withForceProxy:KALTURA_UNDEF_BOOL];
}

- (NSString*)serveByFlavorParamsIdWithEntryId:(NSString*)aEntryId
{
    return [self serveByFlavorParamsIdWithEntryId:aEntryId withFlavorParamsId:nil];
}

- (KalturaDocumentEntry*)updateContentWithEntryId:(NSString*)aEntryId withResource:(KalturaResource*)aResource withConversionProfileId:(int)aConversionProfileId
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"resource" withObject:aResource];
    [self.client.params addIfDefinedKey:@"conversionProfileId" withInt:aConversionProfileId];
    return [self.client queueObjectService:@"document_documents" withAction:@"updateContent" withExpectedType:@"KalturaDocumentEntry"];
}

- (KalturaDocumentEntry*)updateContentWithEntryId:(NSString*)aEntryId withResource:(KalturaResource*)aResource
{
    return [self updateContentWithEntryId:aEntryId withResource:aResource withConversionProfileId:KALTURA_UNDEF_INT];
}

- (KalturaDocumentEntry*)approveReplaceWithEntryId:(NSString*)aEntryId
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    return [self.client queueObjectService:@"document_documents" withAction:@"approveReplace" withExpectedType:@"KalturaDocumentEntry"];
}

- (KalturaDocumentEntry*)cancelReplaceWithEntryId:(NSString*)aEntryId
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    return [self.client queueObjectService:@"document_documents" withAction:@"cancelReplace" withExpectedType:@"KalturaDocumentEntry"];
}

@end

@implementation KalturaDocumentClientPlugin
@synthesize client = _client;

- (id)initWithClient:(KalturaClient*)aClient
{
    self = [super init];
    if (self == nil)
        return nil;
    self.client = aClient;
    return self;
}

- (KalturaDocumentsService*)documents
{
    if (self->_documents == nil)
    	self->_documents = [[KalturaDocumentsService alloc] initWithClient:self.client];
    return self->_documents;
}

- (void)dealloc
{
    [self->_documents release];
	[super dealloc];
}

@end

