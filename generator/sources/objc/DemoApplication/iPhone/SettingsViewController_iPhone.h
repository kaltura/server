//
//  SettingsViewController_iPhone.h
//  Kaltura
//
//  Created by Pavel on 05.03.12.
//  Copyright (c) 2012 Kaltura. All rights reserved.
//

#import <UIKit/UIKit.h>

@class AppDelegate_iPhone;

@interface SettingsViewController_iPhone : UIViewController {
    
    AppDelegate_iPhone *app;

    
    IBOutlet UILabel *labelTitle;
    IBOutlet UIView *viewMain;
    
    
    IBOutlet UILabel *labelUser;
    IBOutlet UIView *viewUser;
    IBOutlet UITextField *textUser;
    
    IBOutlet UILabel *labelPassword;
    IBOutlet UIView *viewPassword;
    IBOutlet UITextField *textPassword;
    
    IBOutlet UIButton *buttonLogin;
    IBOutlet UIButton *buttonMenu;
    
}

- (IBAction)menuBarButtonPressed:(UIButton *)button;
- (IBAction)loginButtonPressed:(UIButton *)button;


@end
