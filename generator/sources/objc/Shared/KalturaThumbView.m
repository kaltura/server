//
//  KalturaThumbView.m
//  Kaltura
//
//  Created by Pavel on 02.04.12.
//  Copyright (c) 2012 Kaltura. All rights reserved.
//

#import "KalturaThumbView.h"

@implementation KalturaThumbView

@synthesize mediaEntry;

- (void)updateWithMediaEntry:(KalturaMediaEntry *)_mediaEntry {
    
    [self updateWithMediaEntry:_mediaEntry withSize:CGSizeMake(self.frame.size.width, self.frame.size.height)];
    
}

- (void)updateWithMediaEntry:(KalturaMediaEntry *)_mediaEntry withSize:(CGSize)size {

    if (isLoading && request) {
        
        [request cancel];
        request = nil;
        
    }
    
    self.mediaEntry = _mediaEntry;
    
    self.image = nil;
    
    width = size.width;
    height = size.height;
    
    NSString *thumbName1 = [NSString stringWithFormat:@"%@_%d_%d", mediaEntry.id, width, height];
    
    if ([[Client instance] isThumbNameExist:thumbName1]) {
        
        NSString *path = [[Client instance] getThumbPath:thumbName1];
        
        NSData *data = [NSData dataWithContentsOfFile:path];
        
        self.image = [UIImage imageWithData:data];
        
    } else {
        
        isLoading = YES;
        
        NSString *thumbURL1 = [[Client instance] getThumbURL:mediaEntry.id width:width height:height];
        
        NSURL *url = [NSURL URLWithString:thumbURL1];
		request = [ASIHTTPRequest requestWithURL:url];
		
		[request setDelegate:self];
		[request startAsynchronous];
        
    }
    
}

- (void)requestFinished:(ASIHTTPRequest *)_request {
	
    isLoading = NO;
    
	@try {
		NSData *imgData = [_request responseData];
		
        NSString *thumbName1 = [NSString stringWithFormat:@"%@_%d_%d", self.mediaEntry.id, width, height];
        
		NSString *fileName = [[Client instance] getThumbPath:thumbName1];
        
        [imgData writeToFile:fileName atomically:NO];
        
        self.image = [UIImage imageWithData:imgData];        
		
	}
	@catch (NSException * e) {
		
	}
	@finally {
		
	}
	
}

- (void)requestFailed:(ASIHTTPRequest *)_request {
    
    isLoading = NO;
    
}

@end
