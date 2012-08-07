//
//  HomeViewController_iPad.h
//  Kaltura
//
//  Created by Pavel on 01.03.12.
//  Copyright (c) 2012 Kaltura. All rights reserved.
//

#import <UIKit/UIKit.h>

@class AppDelegate_iPad;

@interface HomeViewController_iPad : UIViewController <UIImagePickerControllerDelegate, UINavigationControllerDelegate> {
    
    AppDelegate_iPad *app;

    IBOutlet UIButton *menuButton0;
    IBOutlet UIButton *menuButton1;
    IBOutlet UIButton *menuButton2;
    IBOutlet UIButton *menuButton3;
    
    int selectedMenu;
    
    IBOutlet UIActivityIndicatorView *activity;
    
    BOOL isLandscape;
    
    IBOutlet UIView *viewShadow;
    
    
    IBOutlet UIView *viewUploadSelect;
    
    IBOutlet UILabel *labelUploadSelectTitle;
    
    IBOutlet UIButton *buttonUploadSelectRecord;
	IBOutlet UIButton *buttonUploadSelectPick;

    
    IBOutlet UIView *viewUploadInfo;

    IBOutlet UIImageView *imageViewThumb;
    IBOutlet UIView *viewUploadInfoEdit;

    IBOutlet UILabel *labelCategory;
    IBOutlet UILabel *labelCategoryName;
    IBOutlet UIButton *buttonCategory;
    
    IBOutlet UILabel *labelUploadInfoTitle;

    IBOutlet UILabel *labelVTitle;
    IBOutlet UITextField *textVTitle;
    
    IBOutlet UILabel *labelDescription;
    IBOutlet UITextView *textDescription;
    
    IBOutlet UILabel *labelTags;
    IBOutlet UITextField *textTags;
    
    IBOutlet UIButton *buttonUpload;
    
    IBOutlet UIView *viewUploadingProcess;
    IBOutlet UILabel *labelUploadingProcess;
    IBOutlet UIProgressView *progressUploadingProcess;
    
    
    
    IBOutlet UIPickerView *pickerCategories;
    IBOutlet UIToolbar *toolbar;
    
    UIPopoverController *popoverController;
    
    int type;

    int selectedItem;
    
    KalturaUploadToken* token;
    
    NSString *uploadFilePath;
    
    UIAlertView *cancelAlert;
    
    
    IBOutlet UIView *viewUploadSuccess;
    IBOutlet UILabel *labelUploadSuccessTitle;

    IBOutlet UIImageView *imageViewThumbSuccess;
    
    IBOutlet UIImageView *imageViewUploadSuccess;
    IBOutlet UILabel *labelUploadSuccess1;
    IBOutlet UILabel *labelUploadSuccess2;
    

    
}

- (IBAction)menuButtonPressed:(UIButton *)button;

- (IBAction)buttonUploadSelectClose;
- (IBAction)buttonUploadInfoClose;
- (IBAction)buttonUploadSuccessClose;

- (IBAction)categoriesPressed;
- (IBAction)buttonDonePressed;

- (IBAction)buttonUploadPressed;

@property (nonatomic, retain) UIPopoverController *popoverController;
@property (nonatomic, retain) UIAlertView *cancelAlert;
@property (nonatomic, retain) NSString *uploadFilePath;

@end
