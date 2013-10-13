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
@interface KalturaWidevineFlavorAssetOrderBy : NSObject
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
@interface KalturaWidevineFlavorParamsOrderBy : NSObject
@end

// @package External
// @subpackage Kaltura
@interface KalturaWidevineFlavorParamsOutputOrderBy : NSObject
@end

///////////////////////// classes /////////////////////////
// @package External
// @subpackage Kaltura
@interface KalturaWidevineFlavorAsset : KalturaFlavorAsset
// License distribution window start date
@property (nonatomic,assign) int widevineDistributionStartDate;
// License distribution window end date
@property (nonatomic,assign) int widevineDistributionEndDate;
// Widevine unique asset id
@property (nonatomic,assign) int widevineAssetId;
- (KalturaFieldType)getTypeOfWidevineDistributionStartDate;
- (KalturaFieldType)getTypeOfWidevineDistributionEndDate;
- (KalturaFieldType)getTypeOfWidevineAssetId;
- (void)setWidevineDistributionStartDateFromString:(NSString*)aPropVal;
- (void)setWidevineDistributionEndDateFromString:(NSString*)aPropVal;
- (void)setWidevineAssetIdFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
@interface KalturaWidevineFlavorParams : KalturaFlavorParams
@end

// @package External
// @subpackage Kaltura
@interface KalturaWidevineFlavorParamsOutput : KalturaFlavorParamsOutput
@end

// @package External
// @subpackage Kaltura
@interface KalturaWidevineFlavorAssetBaseFilter : KalturaFlavorAssetFilter
@end

// @package External
// @subpackage Kaltura
@interface KalturaWidevineFlavorParamsBaseFilter : KalturaFlavorParamsFilter
@end

// @package External
// @subpackage Kaltura
@interface KalturaWidevineFlavorAssetFilter : KalturaWidevineFlavorAssetBaseFilter
@end

// @package External
// @subpackage Kaltura
@interface KalturaWidevineFlavorParamsFilter : KalturaWidevineFlavorParamsBaseFilter
@end

// @package External
// @subpackage Kaltura
@interface KalturaWidevineFlavorParamsOutputBaseFilter : KalturaFlavorParamsOutputFilter
@end

// @package External
// @subpackage Kaltura
@interface KalturaWidevineFlavorParamsOutputFilter : KalturaWidevineFlavorParamsOutputBaseFilter
@end

///////////////////////// services /////////////////////////
// @package External
// @subpackage Kaltura
// WidevineDrmService serves as a license proxy to a Widevine license server
@interface KalturaWidevineDrmService : KalturaServiceBase
// Get license for encrypted content playback
- (NSString*)getLicenseWithFlavorAssetId:(NSString*)aFlavorAssetId;
@end

@interface KalturaWidevineClientPlugin : KalturaClientPlugin
{
	KalturaWidevineDrmService* _widevineDrm;
}

@property (nonatomic, assign) KalturaClientBase* client;
@property (nonatomic, readonly) KalturaWidevineDrmService* widevineDrm;
@end

