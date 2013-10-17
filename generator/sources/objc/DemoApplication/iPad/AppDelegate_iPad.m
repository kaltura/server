//
//  AppDelegate_iPad.m
//  Kaltura
//
//  Created by Pavel on 28.02.12.
//  Copyright 2012 Kaltura. All rights reserved.
//

#import "AppDelegate_iPad.h"
#import "HomeViewController_iPad.h"
#import "SettingsViewController_iPad.h"
#import <dlfcn.h>
#import <AudioToolbox/AudioToolbox.h>

@implementation AppDelegate_iPad

@synthesize window;
@synthesize navigation;
@synthesize volumeLevel;

#pragma mark -
#pragma mark Application lifecycle

- (BOOL)application:(UIApplication *)application didFinishLaunchingWithOptions:(NSDictionary *)launchOptions {    
    
    // Override point for customization after application launch.
    int newFontCount = 0;
	NSBundle *frameworkBundle = [NSBundle bundleWithIdentifier:@"com.apple.GraphicsServices"];
	const char *frameworkPath = [[frameworkBundle executablePath] UTF8String];
    
	if (frameworkPath) {
		void *graphicsServices = dlopen(frameworkPath, RTLD_NOLOAD | RTLD_LAZY);
		if (graphicsServices) {
			BOOL (*GSFontAddFromFile)(const char*) = dlsym(graphicsServices, "GSFontAddFromFile");
			//			(void*)GSFontAddFromFile = dlsym(graphicsServices, "GSFontAddFromFile");
			if (GSFontAddFromFile)
				for (NSString *fontFile in [[NSBundle mainBundle] pathsForResourcesOfType:@"ttf" inDirectory:nil]) {
                   	//				NSLog(@"%@",fontFile);
					newFontCount += GSFontAddFromFile([fontFile UTF8String]);
				}
		}
	}

    NSString *userEmail = [[NSUserDefaults standardUserDefaults] objectForKey:@"userEmail"];
    
    if (!userEmail || [userEmail length] == 0) {

        NSString *userEmail = [[NSBundle mainBundle] objectForInfoDictionaryKey:@"UserEmail"];
        NSString *userPassword = [[NSBundle mainBundle] objectForInfoDictionaryKey:@"UserPassword"];
        
        if (userEmail && [userEmail length] > 0 && userPassword && [userPassword length] > 0) {
            
            [[NSUserDefaults standardUserDefaults] setObject:userEmail forKey:@"userEmail"];
            [[NSUserDefaults standardUserDefaults] setObject:userPassword forKey:@"userPassword"];
            [[NSUserDefaults standardUserDefaults] synchronize];
            

        }
        
    }
    
    HomeViewController_iPad *homeController = [[HomeViewController_iPad alloc] initWithNibName:@"HomeViewController_iPad" bundle:nil];
    SettingsViewController_iPad *settingsController = [[SettingsViewController_iPad alloc] initWithNibName:@"SettingsViewController_iPad" bundle:nil];
    
    if (![[Client instance] login]){
        
        self.navigation = [[UINavigationController alloc] initWithRootViewController:settingsController];
    }
    else{
        
        self.navigation = [[UINavigationController alloc] initWithRootViewController:homeController];
    }
    [self.navigation setNavigationBarHidden:YES];
    
    [homeController release];
    [settingsController release];
    
    [self.window addSubview:self.navigation.view];
    
    UInt32 category = kAudioSessionCategory_MediaPlayback;
	AudioSessionSetProperty(kAudioSessionProperty_AudioCategory, sizeof(category), &category);
	
	AudioSessionSetActive(true);
	
	[[NSNotificationCenter defaultCenter]
     addObserver:self
     selector:@selector(volumeChanged:)
     name:@"AVSystemController_SystemVolumeDidChangeNotification"
     object:nil];
	
	MPMusicPlayerController *iPod = [MPMusicPlayerController iPodMusicPlayer];
	volumeLevel = iPod.volume;
   
    [self.window makeKeyAndVisible];
    
    return YES;
}

- (void)volumeChanged:(NSNotification *)notification
{
    float _volumeLevel =
    [[[notification userInfo]
      objectForKey:@"AVSystemController_AudioVolumeNotificationParameter"]
     floatValue];
	
	volumeLevel = _volumeLevel;
	
}

- (void)applicationWillResignActive:(UIApplication *)application {
    /*
     Sent when the application is about to move from active to inactive state. This can occur for certain types of temporary interruptions (such as an incoming phone call or SMS message) or when the user quits the application and it begins the transition to the background state.
     Use this method to pause ongoing tasks, disable timers, and throttle down OpenGL ES frame rates. Games should use this method to pause the game.
     */
}


- (void)applicationDidBecomeActive:(UIApplication *)application {
    /*
     Restart any tasks that were paused (or not yet started) while the application was inactive.
     */
}


- (void)applicationWillTerminate:(UIApplication *)application {
    /*
     Called when the application is about to terminate.
     */
}


#pragma mark -
#pragma mark Memory management

- (void)applicationDidReceiveMemoryWarning:(UIApplication *)application {
    /*
     Free up as much memory as possible by purging cached data objects that can be recreated (or reloaded from disk) later.
     */
}


- (void)dealloc {
    [window release];
    [super dealloc];
}


@end
