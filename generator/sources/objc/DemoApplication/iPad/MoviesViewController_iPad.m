//
//  MoviesViewController_iPad.m
//  Kaltura
//
//  Created by Pavel on 14.03.12.
//  Copyright (c) 2012 Kaltura. All rights reserved.
//

#import "MoviesViewController_iPad.h"
#import "MovieTableViewCellHeader_iPad.h"
#import "MovieTableViewCell_iPad.h"
#import "MovieCategoryTableViewCell_iPad.h"
#import "PlayerViewController_iPad.h"

@implementation MoviesViewController_iPad

@synthesize media;
@synthesize category;
@synthesize mostPopular;

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
        self.media = [[NSMutableArray alloc] init];
    }
    return self;
}

- (BOOL)shouldAutorotate {
    return NO;
}

- (void)didReceiveMemoryWarning
{
    // Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
    
    // Release any cached data, images, etc that aren't in use.
}

- (IBAction)menuBarButtonPressed:(UIButton *)button {
    
    [self.navigationController popToRootViewControllerAnimated:YES];
    
}

- (IBAction)categoriesButtonPressed:(UIButton *)button {
    
    [UIView beginAnimations:nil context:nil];
    [UIView setAnimationDuration:0.5];
    
    if (categoriesView.frame.origin.x < 0) {
        
        [buttonCategories setImage:[UIImage imageNamed:@"button_categories.png"] forState:UIControlStateNormal];
        buttonCategories.frame = CGRectMake(258, 2, buttonCategories.frame.size.width, buttonCategories.frame.size.height);
        
        categoriesView.frame = CGRectMake(0, 0, categoriesView.frame.size.width, categoriesView.frame.size.height);

        //labelTitle.frame = CGRectMake(80 + 256, 0, 608 - 256, 44);
        
    } else {
        
        [buttonCategories setImage:[UIImage imageNamed:@"button_categories_open.png"] forState:UIControlStateNormal];
        buttonCategories.frame = CGRectMake(2, 2, buttonCategories.frame.size.width, buttonCategories.frame.size.height);
        
        categoriesView.frame = CGRectMake(-256, 0, categoriesView.frame.size.width, categoriesView.frame.size.height);

        //labelTitle.frame = CGRectMake(80, 0, 608, 44);
        
    }
    
    [UIView commitAnimations];
}

- (IBAction)closeInfoButtonPressed:(UIButton *)button {

    [viewInfo removeFromSuperview];
    
}

#pragma -
- (void)textFieldDidBeginEditing:(UITextField *)textField {
    
    searchLabel.hidden = YES;
    
}

- (void)textFieldDidEndEditing:(UITextField *)textField {
    
    searchLabel.hidden = ([textField.text length] > 0);
    
}

- (BOOL)textFieldShouldReturn:(UITextField *)textField {
    
    [textField resignFirstResponder];
    
    return YES;
    
}

- (BOOL)textFieldShouldClear:(UITextField *)textField {

    [self updateMedia:@""];
    
    return YES;
}

- (BOOL)textField:(UITextField *)textField shouldChangeCharactersInRange:(NSRange)range replacementString:(NSString *)string
{
    //set color for text input
    NSString *searchStr = [textField.text stringByReplacingCharactersInRange:range withString:string];
    [self updateMedia:searchStr];
    
    return YES;
}

#pragma -;

- (void)playButtonPressed {
    
    if ([self.media count] > 0) {
        
        KalturaMediaEntry *mediaEntry = [self.media objectAtIndex:0];
        NSLog(@"@", mediaEntry);
        PlayerViewController_iPad *controller = [[PlayerViewController_iPad alloc] initWithNibName:@"PlayerViewController_iPad" bundle:nil];
        controller.mediaEntry = mediaEntry;
        [self.navigationController pushViewController:controller animated:YES];
        [controller release];
        
    }
    
}

NSInteger playsPadSort(id media1, id media2, void *reverse)
{
	KalturaMediaEntry *mediaEntry1 = (KalturaMediaEntry *)media1;
    KalturaMediaEntry *mediaEntry2 = (KalturaMediaEntry *)media2;
    
	if (mediaEntry1.plays > mediaEntry2.plays) {
		return NSOrderedAscending;
	} else {
		return NSOrderedDescending;
	}
}


- (void)updateMedia:(NSString *)searchStr {
    
    //buttonPlay.hidden = YES;
    [activity startAnimating];
    
    [self.media removeAllObjects];
    
    NSArray *array = [[Client instance] getMedia:self.category];
    
    for (KalturaMediaEntry *mediaEntry in array) {
        
        BOOL canAdd = YES;
        
        if (self.category) {
            
            canAdd = NO;
            if (mediaEntry.categories) {
                
                
                NSArray *ids = [mediaEntry.categories componentsSeparatedByString:@","];
                
                for (NSString *str in ids) {
                    
                    if ([str rangeOfString:category.name].location != NSNotFound) {
                        
                        canAdd = YES;
                        
                    }
                }
                
            }
        }
        
        if (canAdd && [searchStr length] > 0) {
            
            NSString *str = mediaEntry.name;
            
            if ([str rangeOfString:searchStr options:NSCaseInsensitiveSearch].location == NSNotFound) {
                
                canAdd = NO;
                
            }
            
            
        }
        
        if (canAdd) {
            //NSLog(@"plays %d", mediaEntry.plays);
            [self.media addObject:mediaEntry];
        }
        
    }
    
    
    int maxPlaysInd = 0;
    int maxPlaysCount = 0;
    
    for (int i = 0; i < [self.media count]; i++) {
        
        KalturaMediaEntry *mediaEntry = [self.media objectAtIndex:i];
        
        if (mediaEntry.plays > maxPlaysCount) {
            
            maxPlaysCount = mediaEntry.plays;
            maxPlaysInd = i;
            
        }
        
    }
    
    if (maxPlaysInd > 0) {
        
        [self.media exchangeObjectAtIndex:maxPlaysInd withObjectAtIndex:0];
        
    }
    
    if (mostPopular) {
        [self.media sortUsingFunction:playsPadSort context:nil];
        
    }
    
    [activity stopAnimating];
    
    [mediaTableView reloadData];
    if ([self.media count] > 0) {

        [mediaTableView scrollToRowAtIndexPath:[NSIndexPath indexPathForRow:0 inSection:0] atScrollPosition:UITableViewScrollPositionTop animated:NO];

    }

}

#pragma -

- (void)mailComposeController:(MFMailComposeViewController*)_controller didFinishWithResult:(MFMailComposeResult)result error:(NSError*)error {
	
    [self dismissModalViewControllerAnimated:YES];    
    
}

- (IBAction)shareButtonPressed:(UIButton *)button {

    KalturaMediaEntry *mediaEntry = [self.media objectAtIndex:currentMovieInd];
    
    if (button.tag == 0) {
        
        [[Client instance] shareFacebook:mediaEntry];
        
    } else if (button.tag == 1) {
        
        [[Client instance] shareTwitter:mediaEntry];
        
    } else if (button.tag == 2) {
        
        if ([MFMailComposeViewController canSendMail]) {
            
            MFMailComposeViewController *_controller = [[MFMailComposeViewController alloc] init];    
            _controller.mailComposeDelegate = self;
            
            [_controller setSubject:@"Kaltura"];
            
            NSString *str = [NSString stringWithFormat:@"I just saw this great video on Kaltura mobile app, check it out:\n%@", [[Client instance] getShareURL:mediaEntry]];
            [_controller setMessageBody:str isHTML:NO];
            
            [self presentModalViewController:_controller animated:YES];
            [_controller release];
            
        } else {
            UIAlertView *alert = [[UIAlertView alloc] initWithTitle: @"No Email Account"
                                                            message: @"You must set up an email account for your device before you can send mail."
                                                           delegate: nil
                                                  cancelButtonTitle: nil
                                                  otherButtonTitles: @"OK", nil];
            [alert show];
            [alert release];
        }
        
    }
    
}



- (IBAction)playInfoButtonPressed:(UIButton *)button {
    
    KalturaMediaEntry *mediaEntry = [self.media objectAtIndex:currentMovieInd];
    
    PlayerViewController_iPad *controller = [[PlayerViewController_iPad alloc] initWithNibName:@"PlayerViewController_iPad" bundle:nil];
    controller.mediaEntry = mediaEntry;
    [self.navigationController pushViewController:controller animated:YES];
    [controller release];

}

- (void)openMediaInfoByIndex:(NSNumber *)num {
    
    currentMovieInd = [num intValue];
    
    KalturaMediaEntry *mediaEntry = [self.media objectAtIndex:currentMovieInd];
    
    labelInfoTitle.text = mediaEntry.name;
    labelInfoDuration.text = [NSString stringWithFormat:@"%d:%.2d", mediaEntry.duration / 60, mediaEntry.duration % 60];
    textInfoDescription.text = mediaEntry.description;
    
    
    [textInfoDescription scrollRectToVisible:CGRectMake(0, 0, 1, 1) animated:NO];
    
    if (isLandscape) {
        viewInfo.frame = CGRectMake(0, 0, 1024, 748);
    } else {
        viewInfo.frame = CGRectMake(0, 0, 768, 1004);
    }
    
    [imgInfoThumb updateWithMediaEntry:mediaEntry];
    
    [self.view addSubview:viewInfo];
    

}

#pragma -

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView {
    // Return the number of sections.
	return 1;
}


- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
    // Return the number of rows in the section.
    
    int count = 0;
    
    if (tableView.tag == 0) {
        
    
        if ([self.media count] > 0) {
            
            count++;
            
            if ([self.media count] > 3) {
                
                int _count = [self.media count] - 3;
                
                count += _count / 4 + (_count % 4 > 0 ? 1 : 0);
                
                
            }
            
            //NSLog(@"%d %d", count, [self.media count]);
        }
        
    } else {
        
        NSArray *array = [[Client instance] getCategories];
        
        count = [array count];
        
        if (mostPopular) count++;
    }
    
    
    
    return count;
    
}

// Customize the appearance of table view cells.
- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath {
    
    if (tableView.tag == 0) {
        
        if (indexPath.row == 0) {
            
            NSString *CellIdentifier = @"CellHeader";
            
            MovieTableViewCellHeader_iPad *cell = (MovieTableViewCellHeader_iPad *)[tableView dequeueReusableCellWithIdentifier:CellIdentifier];    
            
            if (cell == nil) {
                NSArray *nib_objects = [[NSBundle mainBundle] loadNibNamed:@"TableViewCells_iPad" owner:self options:nil];
                cell = [nib_objects objectAtIndex:0];
            }
            
            cell.selectionStyle = UITableViewCellSelectionStyleNone;
            
            int index = indexPath.row;
            
            cell.index = index;
            cell.parentController = self;
            
            cell.cell1View.hidden = YES;
            cell.cell2View.hidden = YES;
            cell.cell3View.hidden = YES;
            
            if (index < [self.media count]) {
                
                KalturaMediaEntry *mediaEntry = [self.media objectAtIndex:index++];
                
                [cell updateCell1:mediaEntry];
                
            }
            
            if (index < [self.media count]) {
                
                KalturaMediaEntry *mediaEntry = [self.media objectAtIndex:index++];
                
                [cell updateCell2:mediaEntry];
                
            }
            
            if (index < [self.media count]) {
                
                KalturaMediaEntry *mediaEntry = [self.media objectAtIndex:index++];
                
                [cell updateCell3:mediaEntry];
                
            }
            
            return cell;
            
            
        } else {
            
            
            NSString *CellIdentifier = @"CellLeft";
            
            if (indexPath.row % 2 == 0) {
                CellIdentifier = @"CellRight";
            }
            
            MovieTableViewCell_iPad *cell = (MovieTableViewCell_iPad *)[tableView dequeueReusableCellWithIdentifier:CellIdentifier];    
            
            if (cell == nil) {
                NSArray *nib_objects = [[NSBundle mainBundle] loadNibNamed:@"TableViewCells_iPad" owner:self options:nil];
                if (indexPath.row % 2 == 0) {
                    cell = [nib_objects objectAtIndex:2];
                    
                } else {
                    cell = [nib_objects objectAtIndex:1];
                    
                }
            }
            
            cell.selectionStyle = UITableViewCellSelectionStyleNone;
            
            int index = (indexPath.row - 1) * 4 + 3;
            
            cell.index = index;
            cell.parentController = self;
            
            cell.cell1View.hidden = YES;
            cell.cell2View.hidden = YES;
            cell.cell3View.hidden = YES;
            cell.cell4View.hidden = YES;
            
            if (index < [self.media count]) {
                
                KalturaMediaEntry *mediaEntry = [self.media objectAtIndex:index++];
                
                [cell updateCell1:mediaEntry];
                
            }
            
            if (index < [self.media count]) {
                
                KalturaMediaEntry *mediaEntry = [self.media objectAtIndex:index++];
                
                [cell updateCell2:mediaEntry];
                
            }
            
            if (index < [self.media count]) {
                
                KalturaMediaEntry *mediaEntry = [self.media objectAtIndex:index++];
                
                [cell updateCell3:mediaEntry];
                
            }
            
            if (index < [self.media count]) {
                
                KalturaMediaEntry *mediaEntry = [self.media objectAtIndex:index++];
                
                [cell updateCell4:mediaEntry];
                
            }
            
            return cell;
            
            
        }

    } else {
        
        NSString *CellIdentifier = @"MovieCategory";
        
        MovieCategoryTableViewCell_iPad *cell = (MovieCategoryTableViewCell_iPad *)[tableView dequeueReusableCellWithIdentifier:CellIdentifier];    
        
        if (cell == nil) {
            NSArray *nib_objects = [[NSBundle mainBundle] loadNibNamed:@"TableViewCells_iPad" owner:self options:nil];
            cell = [nib_objects objectAtIndex:3];
        }
        
        cell.selectionStyle = UITableViewCellSelectionStyleNone;
        
        cell.labelCategory.font = [UIFont fontWithName:@"Maven Pro" size:18];
        
        cell.viewSelected.hidden = YES;
        
        if (mostPopular && indexPath.row == 0) {
            
            cell.labelCategory.text = @"Most Popular";
            
        } else {
        
            NSArray *array = [[Client instance] getCategories];
            
            KalturaCategory *_category = [array objectAtIndex:indexPath.row - (mostPopular ? 1 : 0 )];
            
            cell.labelCategory.text = _category.fullName;
            
            
        }
        
        if (indexPath.row == currentCategoryInd) {
            
            cell.viewSelected.hidden = NO;
        }
        
        return cell;
    }
        
    return nil;
}

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath {

    if (tableView.tag == 1) {
        
        currentCategoryInd = indexPath.row;
        
        if (mostPopular && indexPath.row == 0) {
        
            self.category = nil;
            labelTitle.text = @"Most Popular";
            
        } else {
            NSArray *array = [[Client instance] getCategories];
            self.category = [array objectAtIndex:indexPath.row - (mostPopular ? 1 : 0 )];
            labelTitle.text = self.category.name;
            
        }
        
        
        [tableView reloadData];
        
        [self updateMedia:searchText.text];
        
    }
    
}

#pragma mark - View lifecycle

- (void)updateInterfaceOrientation:(BOOL)_isLandscape {
    
    isLandscape = _isLandscape;
    
    //[categoriesView removeFromSuperview];
    
    if (isLandscape) {
        
        labelTitle.frame = CGRectMake(80, 0, 1024 - 160, 44);
        
        buttonCategories.hidden = YES;
        
        //[self.view addSubview:categoriesView];
        categoriesView.frame = CGRectMake(0, 0, categoriesView.frame.size.width, 748);
        
    } else {
        
        //80 608
        
        labelTitle.frame = CGRectMake(80, 0, 608, 44);
        
        buttonCategories.hidden = NO;
        
        [buttonCategories setImage:[UIImage imageNamed:@"button_categories_open.png"] forState:UIControlStateNormal];
        buttonCategories.frame = CGRectMake(2, 2, buttonCategories.frame.size.width, buttonCategories.frame.size.height);
        
        //[self.view addSubview:categoriesView];
        categoriesView.frame = CGRectMake(-256, 0, categoriesView.frame.size.width, 1004);
        
    }
    
}

- (void)viewWillAppear:(BOOL)animated {
	[super viewWillAppear:animated];
    
	[self updateInterfaceOrientation:UIInterfaceOrientationIsLandscape([[UIApplication sharedApplication] statusBarOrientation])];
    
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    
    labelTitle.font = [UIFont fontWithName:@"Maven Pro" size:24];
    labelCategories.font = [UIFont fontWithName:@"Maven Pro" size:24];
    
    currentCategoryInd = -1;
    
    if (mostPopular) {
    
        currentCategoryInd = 0;
        labelTitle.text = @"Most Popular";
        
    }
    
    labelInfoTitle.font = [UIFont fontWithName:@"Maven Pro" size:20];
    labelInfoDuration.font = [UIFont fontWithName:@"Maven Pro" size:18];
    textInfoDescription.font = [UIFont fontWithName:@"Maven Pro" size:15];
    
    [self.view addSubview:categoriesView];
    
    [self updateMedia:@""];
    
    
    // Do any additional setup after loading the view from its nib.
}

- (void)willRotateToInterfaceOrientation:(UIInterfaceOrientation)toInterfaceOrientation duration:(NSTimeInterval)duration {
	
	[self updateInterfaceOrientation:UIInterfaceOrientationIsLandscape(toInterfaceOrientation)];
}

- (void)viewDidUnload
{
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    // Return YES for supported orientations
	return YES;
}

#pragma mark -

- (void)dealloc {
    
    [self.media removeAllObjects];
    [self.media release];
    
    [super dealloc];
    
}

@end
