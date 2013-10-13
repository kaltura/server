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
///////////////////////// classes /////////////////////////
///////////////////////// services /////////////////////////
// @package External
// @subpackage Kaltura
// Bulk upload service is used to upload & manage bulk uploads using CSV files
@interface KalturaBulkService : KalturaServiceBase
// Get bulk upload batch job by id
- (KalturaBulkUpload*)getWithId:(int)aId;
// List bulk upload batch jobs
- (KalturaBulkUploadListResponse*)listWithBulkUploadFilter:(KalturaBulkUploadFilter*)aBulkUploadFilter withPager:(KalturaFilterPager*)aPager;
- (KalturaBulkUploadListResponse*)listWithBulkUploadFilter:(KalturaBulkUploadFilter*)aBulkUploadFilter;
- (KalturaBulkUploadListResponse*)list;
// serve action returns the original file.
- (NSString*)serveWithId:(int)aId;
// serveLog action returns the log file for the bulk-upload job.
- (NSString*)serveLogWithId:(int)aId;
// Aborts the bulk upload and all its child jobs
- (KalturaBulkUpload*)abortWithId:(int)aId;
@end

@interface KalturaBulkUploadClientPlugin : KalturaClientPlugin
{
	KalturaBulkService* _bulk;
}

@property (nonatomic, assign) KalturaClientBase* client;
@property (nonatomic, readonly) KalturaBulkService* bulk;
@end

