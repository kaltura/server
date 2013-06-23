//
//  PlayerViewController_iPad.h
//  Kaltura
//
//  Created by Pavel on 22.03.12.
//  Copyright (c) 2012 Kaltura. All rights reserved.
//

#import <UIKit/UIKit.h>

@class AppDelegate_iPad;

@interface PlayerViewController_iPad : UIViewController<ClientDelegate> {
    
    AppDelegate_iPad *app;
    
    IBOutlet UIActivityIndicatorView *activity;
    
    KalturaMediaEntry *mediaEntry;
    NSArray *bitrates;
    
    IBOutlet UIView *toolsView;

    MPMoviePlayerViewController *moviePlayerViewController;
    
    IBOutlet UIView *toolbarView;
    
    IBOutlet UIButton *buttonPlay;
    IBOutlet UILabel *currentTimeLabel;
    IBOutlet UILabel *totalTimeLabel;
    IBOutlet UISlider *timeSlider;
    
    NSTimer *timer;
    
    CFAbsoluteTime activeTime;
    
    IBOutlet UIButton *buttonBitrate;
    
    IBOutlet UIView *viewBitrates;
    IBOutlet UIView *viewBitratesMiddle;
    IBOutlet UIView *viewBitratesBottom;

    IBOutlet UIImageView *imageBitrateCheck;

    IBOutlet UIButton *buttonVolume;
    
    IBOutlet UIView *viewVolume;

    BOOL noVolume;
    NSString *flavorType;
    
}

+(NSString*) getFlavorID;

- (IBAction)donePressed;
- (IBAction)playPressed;
- (IBAction)bitratesPressed:(UIButton *)button;
- (IBAction)sliderChanged:(UISlider *)slider;
- (IBAction)volumePressed:(UIButton *)button;

@property (nonatomic, retain) KalturaMediaEntry *mediaEntry;
@property (nonatomic, retain) NSArray *bitrates;
@property (nonatomic, retain) MPMoviePlayerViewController *moviePlayerViewController;
@property (nonatomic, retain) NSString *flavorType;

@end
