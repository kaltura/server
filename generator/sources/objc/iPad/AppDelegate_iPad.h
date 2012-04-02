//
//  AppDelegate_iPad.h
//  Kaltura
//
//  Created by Pavel on 28.02.12.
//  Copyright 2012 Kaltura. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface AppDelegate_iPad : NSObject <UIApplicationDelegate> {
    UIWindow *window;
    
    UINavigationController *navigation;
    
    float volumeLevel;
}

@property (nonatomic, retain) IBOutlet UIWindow *window;
@property (nonatomic, retain) UINavigationController *navigation;
@property float volumeLevel;

@end

