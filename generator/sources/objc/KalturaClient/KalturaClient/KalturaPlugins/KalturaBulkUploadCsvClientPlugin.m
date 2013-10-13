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
#import "KalturaBulkUploadCsvClientPlugin.h"

///////////////////////// enums /////////////////////////
@implementation KalturaBulkUploadCsvVersion
+ (int)V1
{
    return 1;
}
+ (int)V2
{
    return 2;
}
+ (int)V3
{
    return 3;
}
@end

///////////////////////// classes /////////////////////////
@interface KalturaBulkUploadCsvJobData()
@property (nonatomic,assign) int csvVersion;
@end

@implementation KalturaBulkUploadCsvJobData
@synthesize csvVersion = _csvVersion;
@synthesize columns = _columns;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_csvVersion = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfCsvVersion
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfColumns
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfColumns
{
    return @"KalturaString";
}

- (void)setCsvVersionFromString:(NSString*)aPropVal
{
    self.csvVersion = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaBulkUploadCsvJobData"];
    [aParams addIfDefinedKey:@"columns" withArray:self.columns];
}

- (void)dealloc
{
    [self->_columns release];
    [super dealloc];
}

@end

///////////////////////// services /////////////////////////
