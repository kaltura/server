//
//  MovieTableViewCell_iPad.m
//  Kaltura
//
//  Created by Pavel on 14.03.12.
//  Copyright (c) 2012 Kaltura. All rights reserved.
//

#import "MovieTableViewCell_iPad.h"

@implementation MovieTableViewCell_iPad

@synthesize cell1View;
@synthesize cell2View;
@synthesize cell3View;
@synthesize cell4View;
@synthesize index;
@synthesize parentController;

- (id)initWithStyle:(UITableViewCellStyle)style reuseIdentifier:(NSString *)reuseIdentifier
{
    self = [super initWithStyle:style reuseIdentifier:reuseIdentifier];
    if (self) {
        // Initialization code
    }
    return self;
}

- (void)setSelected:(BOOL)selected animated:(BOOL)animated
{
    [super setSelected:selected animated:animated];

    // Configure the view for the selected state
}

- (IBAction)selectCellView:(UIButton *)button {
    
    NSNumber *num = [NSNumber numberWithInt:(index + button.tag)];
    
    [parentController performSelector:@selector(openMediaInfoByIndex:) withObject:num];
    
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

- (void)updateCell4:(KalturaMediaEntry *)mediaEntry {
    
    [self updateData:mediaEntry label1:cell4Label1 label2:cell4Label2 image:cell4Image view:cell4View];
    
}

@end
