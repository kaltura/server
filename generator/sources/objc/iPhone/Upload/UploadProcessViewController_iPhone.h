//
//  UploadProcessViewController_iPhone.h
//  Kaltura
//
//  Created by Pavel on 06.03.12.
//  Copyright (c) 2012 Kaltura. All rights reserved.
//

#import <UIKit/UIKit.h>

@class AppDelegate_iPhone;

@interface UploadProcessViewController_iPhone : UIViewController <KalturaClientDelegate, ASIProgressDelegate> {
    
    AppDelegate_iPhone *app;
    
    
    IBOutlet UILabel *labelTitle;
    IBOutlet UIView *viewMain;
    
    IBOutlet UILabel *labelUploading;
    IBOutlet UIProgressView *progressView;
    
    NSDictionary *data;
    
    IBOutlet UIActivityIndicatorView *activityView;
    
    KalturaUploadToken* token;
    
    IBOutlet UIButton *buttonMenu;
    
    long long fileSize;
    long long uploadedSize;
    
    UIAlertView *cancelAlert;
}

@property (nonatomic, retain) NSDictionary *data;
@property (nonatomic, retain) UIAlertView *cancelAlert;

@end
