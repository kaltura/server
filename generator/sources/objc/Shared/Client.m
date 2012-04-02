//
//  Client.m
//  Kaltura
//
//  Created by Pavel on 28.02.12.
//  Copyright (c) 2012 Kaltura. All rights reserved.
//

#import "Client.h"

@implementation Client

@synthesize client;
@synthesize categories;
@synthesize media;
@synthesize partnerId;

+ (Client *)instance {
	static Client *sharedSingleton = nil;
	
	@synchronized(self) {
		if (!sharedSingleton) {
			sharedSingleton = [[Client alloc] initClient];
		}
	}
	return sharedSingleton;
}

- (id)initClient {
    
    KalturaClientConfiguration* config = [[KalturaClientConfiguration alloc] init];
    KalturaNSLogger* logger = [[KalturaNSLogger alloc] init];
    config.logger = logger;
    config.serviceUrl = DEFAULT_SERVICE_URL;
    [logger release];           // retained on config
    
    self.client = [[KalturaClient alloc] initWithConfig:config];
    [config release];           // retained on the client
    
    KalturaUserService *service = [[KalturaUserService alloc] init];
    service.client = self.client;
    
    NSString *userEmail = [[NSUserDefaults standardUserDefaults] objectForKey:@"userEmail"];
    NSString *userPassword = [[NSUserDefaults standardUserDefaults] objectForKey:@"userPassword"];

    
    self.client.ks = [service loginByLoginIdWithLoginId:userEmail withPassword:userPassword];
    
    [service release];
    
    KalturaUserListResponse *response = [self.client.user list];
    
    for (KalturaUser *user in [response objects]) {
        self.partnerId = user.partnerId;
    }
    
    self.categories = [[NSMutableArray alloc] init];
    self.media = [[NSMutableArray alloc] init];
    
    return self;
}

- (BOOL)login {
    
    [self.client release];
    
    KalturaClientConfiguration* config = [[KalturaClientConfiguration alloc] init];
    KalturaNSLogger* logger = [[KalturaNSLogger alloc] init];
    config.logger = logger;
    config.serviceUrl = DEFAULT_SERVICE_URL;
    [logger release];           // retained on config
    
    self.client = [[KalturaClient alloc] initWithConfig:config];
    [config release];           // retained on the client
    
    KalturaUserService *service = [[KalturaUserService alloc] init];
    service.client = self.client;
    
    
    NSString *userEmail = [[NSUserDefaults standardUserDefaults] objectForKey:@"userEmail"];
    NSString *userPassword = [[NSUserDefaults standardUserDefaults] objectForKey:@"userPassword"];
    
    self.client.ks = [service loginByLoginIdWithLoginId:userEmail withPassword:userPassword];
    
    [service release];
    
    KalturaUserListResponse *response = [self.client.user list];
    
    for (KalturaUser *user in [response objects]) {
        self.partnerId = user.partnerId;
    }

    [self.categories removeAllObjects];
    [self.media removeAllObjects];
    
    return ([self.client.ks length] > 0);
    
}

- (NSArray *)getCategories {
    
    if ([self.categories count] == 0) {
        
        KalturaCategoryListResponse *response = [self.client.category list];
        
        for (KalturaCategory *category in response.objects) {
            
            [self.categories addObject:category];
            
        }
        
    }
    
    
    return self.categories;
    
}

- (NSArray *)getMedia:(KalturaCategory *)category {

    if ([self.media count] == 0) {
        
        KalturaMediaEntryFilter *filter = [[KalturaMediaEntryFilter alloc] init];
        
        KalturaFilterPager *pager = [[KalturaFilterPager alloc] init];
        pager.pageSize = 0;
        
        KalturaMediaListResponse *response  = [self.client.media listWithFilter:filter withPager:pager];
        
        for (KalturaMediaEntry *mediaEntry in response.objects) {
            
            [self.media addObject:mediaEntry];
           
        }
        
        [filter release];
        [pager release];
        
    }
    
    return self.media;
    
}


- (NSString *)getDocPath:(NSString *)fileName {
	NSArray *paths = NSSearchPathForDirectoriesInDomains(NSDocumentDirectory , NSUserDomainMask, YES);
	NSString *docsDir = [paths objectAtIndex:0];
	
	return [docsDir stringByAppendingPathComponent:fileName];
}

- (NSString *)getThumbPath:(NSString *)fileName {
    
    NSError *error;
	
	NSString *thumbPath = [self getDocPath:@"Thumbs"];
	if (![[NSFileManager defaultManager] fileExistsAtPath:thumbPath])
		[[NSFileManager defaultManager] createDirectoryAtPath:thumbPath withIntermediateDirectories:NO attributes:nil error:&error]; //
    
	NSArray *paths = NSSearchPathForDirectoriesInDomains(NSDocumentDirectory , NSUserDomainMask, YES);
	NSString *docsDir = [paths objectAtIndex:0];
	docsDir = [docsDir stringByAppendingFormat:@"/Thumbs"];
	
	return [docsDir stringByAppendingPathComponent:fileName];
}

- (BOOL)isThumbExist:(KalturaMediaEntry *)mediaEntry {
    
    NSString *thumbPath = [self getThumbPath:mediaEntry.id];
    if ([[NSFileManager defaultManager] fileExistsAtPath:thumbPath]) {
        
        return YES;
    }
    
    return NO;
    
}

- (BOOL)isThumbNameExist:(NSString *)fileName {
    
    NSString *thumbPath = [self getThumbPath:fileName];
    if ([[NSFileManager defaultManager] fileExistsAtPath:thumbPath]) {
        
        return YES;
    }
    
    return NO;
    
}

- (BOOL)isThumbExist:(KalturaMediaEntry *)mediaEntry width:(int)width height:(int)height {

    NSString *thumbPath = [NSString stringWithFormat:@"%@_%d_%d", mediaEntry.id, width, height];
    thumbPath = [self getThumbPath:thumbPath];
    if ([[NSFileManager defaultManager] fileExistsAtPath:thumbPath]) {
        
        return YES;
    }
    
    return NO;
    
}

- (NSData *)getThumb:(KalturaMediaEntry *)mediaEntry {
    
    NSString *thumbPath = [self getThumbPath:mediaEntry.id];
    if (![[NSFileManager defaultManager] fileExistsAtPath:thumbPath]) {
    
        NSData *data = [NSData dataWithContentsOfURL:[NSURL URLWithString:mediaEntry.thumbnailUrl]];
        [data writeToFile:thumbPath atomically:NO];
        
        return data;
    }
    
    return [NSData dataWithContentsOfFile:thumbPath];

}

- (NSString *)getThumbURL:(NSString *)fileName width:(int)width height:(int)height {
    
    return [NSString stringWithFormat:@"http://cdn.kaltura.com/p/%d/thumbnail/entry_id/%@/width/%d/height/%d", self.partnerId, fileName, width, height];
    
}

- (void)requestFinished:(KalturaClientBase*)aClient withResult:(id)result {
    
    NSLog(@"requestFinished");
    
}

- (void)requestFailed:(KalturaClientBase*)aClient {
    
    NSLog(@"requestFailed");
    
}

@end
