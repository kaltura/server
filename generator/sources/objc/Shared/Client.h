//
//  Client.h
//  Kaltura
//
//  Created by Pavel on 28.02.12.
//  Copyright (c) 2012 Kaltura. All rights reserved.
//

#import <Foundation/Foundation.h>

#define DEFAULT_SERVICE_URL     @"http://www.kaltura.com"

@class KalturaClient;

@interface Client : NSObject {
    
    KalturaClient *client;
    NSMutableArray *categories;
    NSMutableArray *media;
    
    int partnerId;
}

+ (Client *)instance;

- (id)initClient;
- (NSArray *)getCategories;
- (NSArray *)getMedia:(KalturaCategory *)category;
- (BOOL)login;
- (NSString *)getThumbPath:(NSString *)fileName;
- (BOOL)isThumbExist:(KalturaMediaEntry *)mediaEntry;
- (BOOL)isThumbExist:(KalturaMediaEntry *)mediaEntry width:(int)width height:(int)height;
- (BOOL)isThumbNameExist:(NSString *)fileName;
- (NSString *)getThumbURL:(NSString *)fileName width:(int)width height:(int)height;
- (NSData *)getThumb:(KalturaMediaEntry *)mediaEntry;
                      
@property (nonatomic, retain) KalturaClient *client;
@property (nonatomic, retain) NSMutableArray *categories;
@property (nonatomic, retain) NSMutableArray *media;
@property int partnerId;

@end
