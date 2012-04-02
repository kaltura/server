//
//  Utils.m
//  Kaltura
//
//  Created by Pavel on 22.03.12.
//  Copyright (c) 2012 Kaltura. All rights reserved.
//

#import "Utils.h"

@implementation Utils

+ (NSString *)getTimeStr:(int)time {

    return [NSString stringWithFormat:@"%.2d:%.2d", time / 60, time % 60];
    
}

+ (NSString *)getStrBitrate:(id)bitrate {
    
    NSString *sBitrate = [bitrate stringValue];
    
    if ([sBitrate intValue] == 0) {
        
        sBitrate = @"adaptive";
        
    } else {
        
        float b1 = (float)[bitrate intValue];
        
        int b2 = roundf(b1 / 10.0) * 10;
        
        sBitrate = [NSString stringWithFormat:@"%dKbps", b2];
        
    }
    
    return sBitrate;
    
}

@end
