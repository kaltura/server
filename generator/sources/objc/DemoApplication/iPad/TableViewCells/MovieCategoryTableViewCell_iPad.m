//
//  MovieCategoryTableViewCell_iPad.m
//  Kaltura
//
//  Created by Pavel on 14.03.12.
//  Copyright (c) 2012 Kaltura. All rights reserved.
//

#import "MovieCategoryTableViewCell_iPad.h"

@implementation MovieCategoryTableViewCell_iPad

@synthesize viewSelected;
@synthesize labelCategory;

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

@end
