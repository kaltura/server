//
//  HomeViewController_iPhone.h
//  Kaltura
//
//  Created by Pavel on 28.02.12.
//  Copyright (c) 2012 Kaltura. All rights reserved.
//

#import <UIKit/UIKit.h>

@class AppDelegate_iPhone;

@interface HomeViewController_iPhone : UIViewController {
    
    AppDelegate_iPhone *app;
    
    IBOutlet UIButton *menuButton0;
    IBOutlet UIButton *menuButton1;
    IBOutlet UIButton *menuButton2;
    IBOutlet UIButton *menuButton3;
    
    int selectedMenu;
    
    IBOutlet UIActivityIndicatorView *activity;
    
}

- (IBAction)menuButtonPressed:(UIButton *)button;

@end
