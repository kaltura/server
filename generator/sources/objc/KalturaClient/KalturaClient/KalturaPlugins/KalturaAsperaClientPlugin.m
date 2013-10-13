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
#import "KalturaAsperaClientPlugin.h"

///////////////////////// enums /////////////////////////
///////////////////////// classes /////////////////////////
///////////////////////// services /////////////////////////
@implementation KalturaAsperaService
- (NSString*)getFaspUrlWithFlavorAssetId:(NSString*)aFlavorAssetId
{
    [self.client.params addIfDefinedKey:@"flavorAssetId" withString:aFlavorAssetId];
    return [self.client queueStringService:@"aspera_aspera" withAction:@"getFaspUrl"];
}

@end

@implementation KalturaAsperaClientPlugin
@synthesize client = _client;

- (id)initWithClient:(KalturaClient*)aClient
{
    self = [super init];
    if (self == nil)
        return nil;
    self.client = aClient;
    return self;
}

- (KalturaAsperaService*)aspera
{
    if (self->_aspera == nil)
    	self->_aspera = [[KalturaAsperaService alloc] initWithClient:self.client];
    return self->_aspera;
}

- (void)dealloc
{
    [self->_aspera release];
	[super dealloc];
}

@end

