//
//  UploadInfoViewController_iPhone.h
//  Kaltura
//
//  Created by Pavel on 05.03.12.
//  Copyright (c) 2012 Kaltura. All rights reserved.
//

#import <UIKit/UIKit.h>

@class AppDelegate_iPhone;

@interface UploadInfoViewController_iPhone : UIViewController {
    
    AppDelegate_iPhone *app;
    
    
    IBOutlet UILabel *labelTitle;
    IBOutlet UIView *viewMain;
    
    IBOutlet UILabel *labelCategory;
    IBOutlet UIView *viewCategory;
    IBOutlet UILabel *labelCategoryName;

    IBOutlet UILabel *labelVTitle;
    IBOutlet UIView *viewVTitle;
    IBOutlet UITextField *textVTitle;
    
    IBOutlet UILabel *labelDescription;
    IBOutlet UIView *viewDescription;
    IBOutlet UITextView *textDescription;

    IBOutlet UILabel *labelTags;
    IBOutlet UIView *viewTags;
    IBOutlet UITextField *textTags;
    
    IBOutlet UIButton *buttonUpload;
    
    IBOutlet UIPickerView *pickerCategories;
    IBOutlet UIToolbar *toolbar;
    
    IBOutlet UIScrollView *scrollMain;
    
    BOOL isLandscape;
    
    int selectedItem;
    
    NSString *path;
}

- (IBAction)menuBarButtonPressed:(UIButton *)button;
- (IBAction)uploadButtonPressed:(UIButton *)button;
- (IBAction)categoriesPressed;
- (IBAction)buttonDonePressed;

@property (nonatomic, retain) NSString *path;

@end
