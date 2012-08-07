//
//  CategoryViewController_iPhone.h
//  Kaltura
//
//  Created by Pavel on 28.02.12.
//  Copyright (c) 2012 Kaltura. All rights reserved.
//

#import <UIKit/UIKit.h>

@class AppDelegate_iPhone;

@interface CategoryViewController_iPhone : UIViewController {
    
    AppDelegate_iPhone *app;
    
    IBOutlet UIActivityIndicatorView *activity;
    IBOutlet UITableView *mediaTableView;
    IBOutlet UITextField *searchText;
    IBOutlet UILabel *searchLabel;
    
    IBOutlet UILabel *labelTitle;

    KalturaCategory *category;
    
    NSMutableArray *media;
    
    BOOL isLandscape;
    
    BOOL mostPopular;
    
    IBOutlet UIButton *buttonBack;
    
    IBOutlet UIButton *buttonSearch;

}

- (void)updateMedia:(NSString *)searchStr;

- (IBAction)menuBarButtonPressed:(UIButton *)button;
- (IBAction)categoriesBarButtonPressed:(UIButton *)button;
- (IBAction)searchButtonPressed:(UIButton *)button;

- (void)openMediaInfoByIndex:(int)index;
- (void)playButtonPressed;

@property (nonatomic, retain) KalturaCategory *category;
@property (nonatomic, retain) NSMutableArray *media;
@property BOOL mostPopular;

@end
