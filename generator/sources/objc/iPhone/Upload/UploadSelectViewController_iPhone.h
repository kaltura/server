//
//  UploadSelectViewController_iPhone.h
//  Kaltura
//
//  Created by Pavel on 06.03.12.
//  Copyright (c) 2012 Kaltura. All rights reserved.
//

#import <UIKit/UIKit.h>

@class AppDelegate_iPhone;

@interface UploadSelectViewController_iPhone : UIViewController <UIImagePickerControllerDelegate, UINavigationControllerDelegate> {
    
    AppDelegate_iPhone *app;
    
    
    IBOutlet UILabel *labelTitle;
    IBOutlet UIView *viewMain;
    
    IBOutlet UIButton *buttonRecord;
    IBOutlet UIButton *buttonPick;
    
}

- (IBAction)actionRecord:(UIButton *)button;
- (IBAction)actionPick:(UIButton *)button;

@end
