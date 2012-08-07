//
//  CategoryLTableViewCell_iPhone.m
//  Kaltura
//
//  Created by Pavel on 04.03.12.
//  Copyright (c) 2012 Kaltura. All rights reserved.
//

#import "CategoryLTableViewCell_iPhone.h"
#import "CategoryViewController_iPhone.h"

@implementation CategoryLTableViewCell_iPhone

@synthesize index;
@synthesize parentController;
@synthesize cell1View;
@synthesize cell2View;
@synthesize cell3View;

- (id)initWithStyle:(UITableViewCellStyle)style reuseIdentifier:(NSString *)reuseIdentifier
{
    self = [super initWithStyle:style reuseIdentifier:reuseIdentifier];
    if (self) {
        // Initialization code
    
        self.selectionStyle = UITableViewCellSelectionStyleNone;
        
    }
    return self;
}

- (void)setSelected:(BOOL)selected animated:(BOOL)animated
{
    [super setSelected:selected animated:animated];

    // Configure the view for the selected state
}

- (IBAction)selectCellView:(UIButton *)button {
    
    [parentController openMediaInfoByIndex:index + button.tag];
    
}

- (IBAction)playButtonPressed:(UIButton *)button {
    
    [parentController playButtonPressed];
    
}

- (void)updateData:(KalturaMediaEntry *)mediaEntry 
            label1:(UILabel *)label1
            label2:(UILabel *)label2
             image:(KalturaThumbView *)image
              view:(UIView *)view {
    
    
    label1.text = mediaEntry.name;
    label2.text = [NSString stringWithFormat:@" %d:%.2d", mediaEntry.duration / 60, mediaEntry.duration % 60];
    
    [image updateWithMediaEntry:mediaEntry];
    
    view.hidden = NO;
    
}

- (void)updateCell1:(KalturaMediaEntry *)mediaEntry {
    
    [self updateData:mediaEntry label1:cell1Label1 label2:cell1Label2 image:cell1Image view:cell1View];
    
}

- (void)updateCell2:(KalturaMediaEntry *)mediaEntry {
    
    [self updateData:mediaEntry label1:cell2Label1 label2:cell2Label2 image:cell2Image view:cell2View];
    
}

- (void)updateCell3:(KalturaMediaEntry *)mediaEntry {
    
    [self updateData:mediaEntry label1:cell3Label1 label2:cell3Label2 image:cell3Image view:cell3View];
    
}

@end
