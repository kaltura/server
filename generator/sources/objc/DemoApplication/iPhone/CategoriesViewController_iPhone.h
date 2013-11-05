//
//  CategoriesViewController_iPhone.h
//  Kaltura
//
//  Created by Pavel on 28.02.12.
//  Copyright (c) 2012 Kaltura. All rights reserved.
//

#import <UIKit/UIKit.h>

@class AppDelegate_iPhone;

@interface CategoriesViewController_iPhone : UIViewController {
    
    AppDelegate_iPhone *app;

    NSMutableArray *categories;
    
    IBOutlet UIActivityIndicatorView *activity;
    IBOutlet UITableView *categoriesTableView;
    
    IBOutlet UITextField *searchText;
    IBOutlet UILabel *searchLabel;

    IBOutlet UILabel *labelTitle;
    
    BOOL isLandscape;
    
    IBOutlet UIButton *buttonSearch;
    
}

- (void)updateCategories:(NSString *)searchStr;
- (IBAction)menuBarButtonPressed:(UIButton *)button;
- (IBAction)searchButtonPressed:(UIButton *)button;

@property (nonatomic, retain) NSMutableArray *categories;

@end
