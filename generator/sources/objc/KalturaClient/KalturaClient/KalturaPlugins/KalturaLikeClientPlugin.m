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
#import "KalturaLikeClientPlugin.h"

///////////////////////// enums /////////////////////////
///////////////////////// classes /////////////////////////
///////////////////////// services /////////////////////////
@implementation KalturaLikeService
- (BOOL)likeWithEntryId:(NSString*)aEntryId
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    return [self.client queueBoolService:@"like_like" withAction:@"like"];
}

- (BOOL)unlikeWithEntryId:(NSString*)aEntryId
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    return [self.client queueBoolService:@"like_like" withAction:@"unlike"];
}

- (BOOL)checkLikeExistsWithEntryId:(NSString*)aEntryId withUserId:(NSString*)aUserId
{
    [self.client.params addIfDefinedKey:@"entryId" withString:aEntryId];
    [self.client.params addIfDefinedKey:@"userId" withString:aUserId];
    return [self.client queueBoolService:@"like_like" withAction:@"checkLikeExists"];
}

- (BOOL)checkLikeExistsWithEntryId:(NSString*)aEntryId
{
    return [self checkLikeExistsWithEntryId:aEntryId withUserId:nil];
}

@end

@implementation KalturaLikeClientPlugin
@synthesize client = _client;

- (id)initWithClient:(KalturaClient*)aClient
{
    self = [super init];
    if (self == nil)
        return nil;
    self.client = aClient;
    return self;
}

- (KalturaLikeService*)like
{
    if (self->_like == nil)
    	self->_like = [[KalturaLikeService alloc] initWithClient:self.client];
    return self->_like;
}

- (void)dealloc
{
    [self->_like release];
	[super dealloc];
}

@end

