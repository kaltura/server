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
#import "KalturaMultiCentersClientPlugin.h"

///////////////////////// enums /////////////////////////
///////////////////////// classes /////////////////////////
@implementation KalturaFileSyncImportJobData
@synthesize sourceUrl = _sourceUrl;
@synthesize filesyncId = _filesyncId;
@synthesize tmpFilePath = _tmpFilePath;
@synthesize destFilePath = _destFilePath;

- (KalturaFieldType)getTypeOfSourceUrl
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFilesyncId
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTmpFilePath
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfDestFilePath
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaFileSyncImportJobData"];
    [aParams addIfDefinedKey:@"sourceUrl" withString:self.sourceUrl];
    [aParams addIfDefinedKey:@"filesyncId" withString:self.filesyncId];
    [aParams addIfDefinedKey:@"tmpFilePath" withString:self.tmpFilePath];
    [aParams addIfDefinedKey:@"destFilePath" withString:self.destFilePath];
}

- (void)dealloc
{
    [self->_sourceUrl release];
    [self->_filesyncId release];
    [self->_tmpFilePath release];
    [self->_destFilePath release];
    [super dealloc];
}

@end

///////////////////////// services /////////////////////////
