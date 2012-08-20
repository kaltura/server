//
//  Client.h
//  Kaltura
//
//  Created by Pavel on 28.02.12.
//  Copyright (c) 2012 Kaltura. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "KalturaClient.h"

#define DEFAULT_SERVICE_URL     @"http://www.kaltura.com"

@class KalturaClient;

@interface Client : NSObject <KalturaClientDelegate, ASIProgressDelegate> {
    
    KalturaClient *client;
    NSMutableArray *categories;
    NSMutableArray *media;
    
    int partnerId;
    
    
    KalturaUploadToken* token;
    
    long long fileSize;
    long long uploadedSize;
    int currentChunk;
    int uploadTryCount;
    
    NSString *uploadFileTokenId;
    NSString *uploadFilePath;
    
    UIViewController *uploadDelegateController;
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
- (NSString *)getShareURL:(KalturaMediaEntry *)mediaEntry;
- (void)shareFacebook:(KalturaMediaEntry *)mediaEntry;
- (void)shareTwitter:(KalturaMediaEntry *)mediaEntry;

- (void)cancelUploading;
- (BOOL)uploadingInProgress;
- (void)uploadProcess:(NSDictionary *)data withDelegate:(UIViewController *)delegateController;
- (NSArray *)getBitratesList:(KalturaMediaEntry *)mediaEntry withFilter:(NSString *)filter;
- (NSString *)getVideoURL:(KalturaMediaEntry *)mediaEntry forFlavor:(NSString *)flavorId;

@property (nonatomic, retain) KalturaClient *client;
@property (nonatomic, retain) NSMutableArray *categories;
@property (nonatomic, retain) NSMutableArray *media;
@property int partnerId;

@property (nonatomic, retain) NSString *uploadFileTokenId;
@property (nonatomic, retain) NSString *uploadFilePath;

@end
