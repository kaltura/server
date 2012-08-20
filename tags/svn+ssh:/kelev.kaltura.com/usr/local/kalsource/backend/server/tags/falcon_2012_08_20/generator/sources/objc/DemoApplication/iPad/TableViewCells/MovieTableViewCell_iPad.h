//
//  MovieTableViewCell_iPad.h
//  Kaltura
//
//  Created by Pavel on 14.03.12.
//  Copyright (c) 2012 Kaltura. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface MovieTableViewCell_iPad : UITableViewCell {

    
    IBOutlet UIView *cell1View;
    IBOutlet KalturaThumbView *cell1Image;
    IBOutlet UILabel *cell1Label1;
    IBOutlet UILabel *cell1Label2;
    
    IBOutlet UIView *cell2View;
    IBOutlet KalturaThumbView *cell2Image;
    IBOutlet UILabel *cell2Label1;
    IBOutlet UILabel *cell2Label2;
    
    IBOutlet UIView *cell3View;
    IBOutlet KalturaThumbView *cell3Image;
    IBOutlet UILabel *cell3Label1;
    IBOutlet UILabel *cell3Label2;
    
    IBOutlet UIView *cell4View;
    IBOutlet KalturaThumbView *cell4Image;
    IBOutlet UILabel *cell4Label1;
    IBOutlet UILabel *cell4Label2;
    
    int index;
    
    UIViewController *parentController;
    
}

- (void)updateCell1:(KalturaMediaEntry *)mediaEntry;
- (void)updateCell2:(KalturaMediaEntry *)mediaEntry;
- (void)updateCell3:(KalturaMediaEntry *)mediaEntry;
- (void)updateCell4:(KalturaMediaEntry *)mediaEntry;

- (IBAction)selectCellView:(UIButton *)button;

@property (nonatomic, retain) IBOutlet UIView *cell1View;
@property (nonatomic, retain) IBOutlet UIView *cell2View;
@property (nonatomic, retain) IBOutlet UIView *cell3View;
@property (nonatomic, retain) IBOutlet UIView *cell4View;
@property int index;
@property (nonatomic, retain) UIViewController *parentController;

@end
