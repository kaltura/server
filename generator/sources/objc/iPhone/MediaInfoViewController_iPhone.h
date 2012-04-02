//
//  MediaInfoViewController_iPhone.h
//  Kaltura
//
//  Created by Pavel on 29.02.12.
//  Copyright (c) 2012 Kaltura. All rights reserved.
//

#import <UIKit/UIKit.h>
#import <MediaPlayer/MediaPlayer.h>
#import <MessageUI/MessageUI.h>
#import <MessageUI/MFMailComposeViewController.h>

@class AppDelegate_iPhone;

@interface MediaInfoViewController_iPhone : UIViewController <MFMailComposeViewControllerDelegate> {
    
    AppDelegate_iPhone *app;
    
    KalturaMediaEntry *mediaEntry;
    
    IBOutlet UIScrollView *scrollMain;
    
    IBOutlet UIView *viewIntro;
    IBOutlet UIView *viewDescription;

    IBOutlet KalturaThumbView *imgThumb;
    IBOutlet UILabel *labelTitle;

    IBOutlet UILabel *labelVTitle;
    IBOutlet UILabel *labelVDuration;

    IBOutlet UILabel *textDescription;
    
    IBOutlet UIButton *buttonPlay;
    IBOutlet UIButton *buttonCategory;
    
    IBOutlet UIView *viewShare;
    
    NSString *categoryName;
        
}

- (IBAction)menuBarButtonPressed:(UIButton *)button;
- (IBAction)categoryBarButtonPressed:(UIButton *)button;
- (IBAction)playButtonPressed;
- (IBAction)shareButtonPressed:(UIButton *)button;

@property (nonatomic, retain) KalturaMediaEntry *mediaEntry;
@property (nonatomic, retain) NSString *categoryName;

@end
