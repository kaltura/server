//
//  KalturaThumbView.h
//  Kaltura
//
//  Created by Pavel on 02.04.12.
//  Copyright (c) 2012 Kaltura. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface KalturaThumbView : UIImageView {
    
    KalturaMediaEntry *mediaEntry;
    
    int width;
    int height;
    
    BOOL isLoading;
    ASIHTTPRequest *request;
}

- (void)updateWithMediaEntry:(KalturaMediaEntry *)_mediaEntry;
- (void)updateWithMediaEntry:(KalturaMediaEntry *)_mediaEntry withSize:(CGSize)size;

@property (nonatomic, assign) KalturaMediaEntry *mediaEntry;

@end
