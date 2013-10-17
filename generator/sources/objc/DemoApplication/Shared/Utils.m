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
//    NSString *sBitrate = [[NSString alloc]];
    
    NSString *sBitrate = bitrate;//[[NSString alloc]initWithString:bitrate];
//bitrate;//[bitrate stringValue];
    
    if ([sBitrate intValue] == 0) {
        
        sBitrate = @"adaptive";
        
    } else {
        
        float b1 = (float)[bitrate intValue];
        
        int b2 = roundf(b1 / 10.0) * 10;
        
        sBitrate = [NSString stringWithFormat:@"%dKbps", b2];
        
    }
    
    return sBitrate;
    
}

+ (NSString *)getDocPath:(NSString *)fileName {
	NSArray *paths = NSSearchPathForDirectoriesInDomains(NSDocumentDirectory , NSUserDomainMask, YES);
	NSString *docsDir = [paths objectAtIndex:0];
	
	return [docsDir stringByAppendingPathComponent:fileName];
}

+ (void)deleteBufferFile {
    
    NSString *bufferPath = [self getDocPath:@"buffer.tmp"];
    
    NSFileManager *fManager = [NSFileManager defaultManager];
    if ([fManager fileExistsAtPath:bufferPath]) {
        NSError *error;
        [fManager removeItemAtPath:bufferPath error:&error];
    }
}

+ (void)createBuffer:(NSString *)path offset:(long long)offset {
    
    [self deleteBufferFile];
    
    NSString *bufferPath = [self getDocPath:@"buffer.tmp"];
    
    NSFileHandle *fileHandleIn = [NSFileHandle fileHandleForReadingAtPath:path];
    [fileHandleIn seekToFileOffset:offset];
    
    NSData *data = [fileHandleIn readDataOfLength:CHUNK_SIZE];
    
    [data writeToFile:bufferPath atomically:NO];
    [fileHandleIn closeFile];
}

@end
