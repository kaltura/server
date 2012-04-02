//
//  MoviesViewController_iPad.h
//  Kaltura
//
//  Created by Pavel on 14.03.12.
//  Copyright (c) 2012 Kaltura. All rights reserved.
//

#import <UIKit/UIKit.h>
#import <MessageUI/MessageUI.h>
#import <MessageUI/MFMailComposeViewController.h>

@interface MoviesViewController_iPad : UIViewController <UINavigationControllerDelegate, MFMailComposeViewControllerDelegate> {
    
    NSMutableArray *media;
    
    IBOutlet UITableView *mediaTableView;
    
    IBOutlet UILabel *labelTitle;
    KalturaCategory *category;
    
    IBOutlet UITableView *categoriesTableView;
    IBOutlet UIView *categoriesView;
    
    IBOutlet UIActivityIndicatorView *activity;
   
    IBOutlet UITextField *searchText;
    IBOutlet UILabel *searchLabel;
    
    IBOutlet UILabel *labelCategories;
    IBOutlet UIButton *buttonCategories;
    
    BOOL mostPopular;
    int currentCategoryInd;
    
    int currentMovieInd;
    
    IBOutlet UIView *viewInfo;
    
    BOOL isLandscape;
    
    IBOutlet KalturaThumbView *imgInfoThumb;
    
    IBOutlet UILabel *labelInfoTitle;
    IBOutlet UILabel *labelInfoDuration;
    
    IBOutlet UITextView *textInfoDescription;
    
}

- (IBAction)menuBarButtonPressed:(UIButton *)button;
- (void)updateMedia:(NSString *)searchStr;
- (IBAction)categoriesButtonPressed:(UIButton *)button;
- (IBAction)closeInfoButtonPressed:(UIButton *)button;
- (IBAction)playInfoButtonPressed:(UIButton *)button;
- (IBAction)shareButtonPressed:(UIButton *)button;

@property (nonatomic, retain) NSMutableArray *media;
@property (nonatomic, retain) KalturaCategory *category;
@property BOOL mostPopular;

@end
