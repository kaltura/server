//
//  CategoryViewController_iPhone.m
//  Kaltura
//
//  Created by Pavel on 28.02.12.
//  Copyright (c) 2012 Kaltura. All rights reserved.
//

#import "CategoryViewController_iPhone.h"
#import "AppDelegate_iPhone.h"
#import "MediaInfoViewController_iPhone.h"
#import "CategoryPTableViewCell_iPhone.h"
#import "CategoryLTableViewCell_iPhone.h"
#import "PlayerViewController_iPhone.h"

@implementation CategoryViewController_iPhone

@synthesize category;
@synthesize media;
@synthesize mostPopular;

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        app = (AppDelegate_iPhone *)[[UIApplication sharedApplication] delegate];
        
        self.media = [[NSMutableArray alloc] init];

    }
    return self;
}

- (BOOL)shouldAutorotate {
    return NO;
}

- (IBAction)menuBarButtonPressed:(UIButton *)button {
    
    [app.navigation popToRootViewControllerAnimated:YES];
    
}

- (IBAction)categoriesBarButtonPressed:(UIButton *)button {
    
    [app.navigation popViewControllerAnimated:YES];
    
}

- (void)playButtonPressed {

    if ([self.media count] > 0) {
        
        KalturaMediaEntry *mediaEntry = [self.media objectAtIndex:0];
        
        PlayerViewController_iPhone *controller = [[PlayerViewController_iPhone alloc] initWithNibName:@"PlayerViewController_iPhone" bundle:nil];
        controller.mediaEntry = mediaEntry;
        [app.navigation pushViewController:controller animated:YES];
        [controller release];

    }

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
    
    if (isLandscape) {
    //    [self searchButtonPressed:buttonSearch];
    }
    
    return YES;
    
}

- (BOOL)textField:(UITextField *)textField shouldChangeCharactersInRange:(NSRange)range replacementString:(NSString *)string
{
    //set color for text input
    NSString *searchStr = [textField.text stringByReplacingCharactersInRange:range withString:string];
    [self updateMedia:searchStr];
    
    return YES;
}

- (BOOL)textFieldShouldClear:(UITextField *)textField {
    
    [self updateMedia:@""];
    
    return YES;
}

#pragma -

NSInteger playsPhoneSort(id media1, id media2, void *reverse)
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
    
    [activity startAnimating];
    
    [self.media removeAllObjects];
    
    NSArray *array = [[Client instance] getMedia:self.category];
    
    for (KalturaMediaEntry *mediaEntry in array) {
        
        BOOL canAdd = NO;
        //NSLog(@"%@", mediaEntry.id);
         //NSLog(@"\n\n%@", category.fullName);
        if (mediaEntry.categories && self.category) {
            
            
            NSArray *ids = [mediaEntry.categories componentsSeparatedByString:@","];
        
            for (NSString *str in ids) {
                //NSLog(@"%@", str);
                
                if ([str rangeOfString:self.category.name].location != NSNotFound) {
                    
                    canAdd = YES;
                    
                }
            }
            
        } else {
            
            canAdd = YES;
            
        }
        
        //NSLog(@"\n\n");
        
        if (canAdd && [searchStr length] > 0) {
            
            NSString *str = mediaEntry.name;
            
            if ([str rangeOfString:searchStr options:NSCaseInsensitiveSearch].location == NSNotFound) {
                
                canAdd = NO;
                
            }
            
            
        }
        
        if (canAdd) {
            
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
            
            //NSLog(@"%d %d", maxPlaysInd, maxPlaysCount);
        }
        
    }
    
    if (maxPlaysInd > 0) {
        
        [self.media exchangeObjectAtIndex:maxPlaysInd withObjectAtIndex:0];
        
    }
    
    if (mostPopular) {
        [self.media sortUsingFunction:playsPhoneSort context:nil];

    }
    /*
    for (int i = 0; i < [self.media count]; i++) {
        
        KalturaMediaEntry *mediaEntry = [self.media objectAtIndex:i];
        
        NSLog(@"%d", mediaEntry.plays);
        
        
    }
    */
    [activity stopAnimating];
    
    [mediaTableView reloadData];

    
}

- (void)viewDidAppear:(BOOL)animated {
    
    [super viewDidAppear:YES];
    
    [self updateMedia:searchText.text];
}

- (void)updateInterfaceOrientation:(BOOL)_isLandscape {
	
    isLandscape = _isLandscape;
    
    
    [searchText resignFirstResponder];
    
    if (isLandscape) {
        
        mediaTableView.frame = CGRectMake(0, 44, 480, 254);
        
    } else {
        
        mediaTableView.frame = CGRectMake(0, 92, 320, 368);
        
    }
    
    buttonSearch.hidden = !isLandscape;
    
    [mediaTableView reloadData];
    
}

- (IBAction)searchButtonPressed:(UIButton *)button {
    
    if (mediaTableView.frame.origin.y == 44) {
        
        [searchText becomeFirstResponder];
        
    } else {
        
        [searchText resignFirstResponder];
        
    }
    
    [UIView beginAnimations:nil context:nil];
    [UIView setAnimationDuration:0.5];
    
    if (mediaTableView.frame.origin.y == 44) {
        
        mediaTableView.frame = CGRectMake(0, 92, 480, 206);
        
    } else {
        
        mediaTableView.frame = CGRectMake(0, 44, 480, 254);
        
    }
    
    [UIView commitAnimations];
}


- (void)viewWillAppear:(BOOL)animated {
	[super viewWillAppear:animated];

	[self updateInterfaceOrientation:UIInterfaceOrientationIsLandscape([[UIApplication sharedApplication] statusBarOrientation])];
    
    self.navigationItem.title = @"Category";
    
}

- (void)viewDidDisappear:(BOOL)animated {
	[super viewDidDisappear:animated];
    
   self.navigationItem.title = @"Back";
    
}

- (void)willRotateToInterfaceOrientation:(UIInterfaceOrientation)toInterfaceOrientation duration:(NSTimeInterval)duration {
	
	[self updateInterfaceOrientation:UIInterfaceOrientationIsLandscape(toInterfaceOrientation)];
}


#pragma -
#pragma Table View
/*
- (CGFloat)tableView:(UITableView *)tableView heightForHeaderInSection:(NSInteger)section {
    
    if (!isLandscape) {
        
        return 179;
        
    }
    
    return 0;

}

- (UIView *)tableView:(UITableView *)tableView viewForHeaderInSection:(NSInteger)section {
    
    if (!isLandscape) {
        
        return viewIntro;
        
    }
    
    return nil;
    
}*/

#pragma -

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView {
    // Return the number of sections.
	return 1;
}


- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
    // Return the number of rows in the section.
    
    int count = [self.media count];
    
    if (!isLandscape) {
        if (count > 1) {
        
            count = ([self.media count] - 1) / 2 + (([self.media count] - 1) % 2) + 1;
            
        }
        
        
    } else {
        
        count = [self.media count] / 3 + (([self.media count] % 3) > 0 ? 1 : 0);
        
    }
    
    return count;
    
}

- (CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath {
    
    if (!isLandscape) {
        return (indexPath.row == 0 ? 208 : 104);
        
    } else {
        
        return (indexPath.row == 0 ? 208 : 104);
        
    }
}

// Customize the appearance of table view cells.
- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath {
    
    if (isLandscape) {
        
        NSString *CellIdentifier = indexPath.row == 0 ? @"CellL1" : @"CellL2";
        
        CategoryLTableViewCell_iPhone *cell = (CategoryLTableViewCell_iPhone *)[tableView dequeueReusableCellWithIdentifier:CellIdentifier];    
        
        if (cell == nil) {
            NSArray *nib_objects = [[NSBundle mainBundle] loadNibNamed:@"TableViewCells_iPhone" owner:self options:nil];
            cell = [nib_objects objectAtIndex:2 + (indexPath.row == 0 ? 0 : 1)];
        }
        
        cell.selectionStyle = UITableViewCellSelectionStyleNone;
        
        int index = indexPath.row * 3;
        
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
        
        NSString *CellIdentifier = indexPath.row == 0 ? @"CellP1" : @"CellP2";
        
        CategoryPTableViewCell_iPhone *cell = (CategoryPTableViewCell_iPhone *)[tableView dequeueReusableCellWithIdentifier:CellIdentifier];    
        
        if (cell == nil) {
            NSArray *nib_objects = [[NSBundle mainBundle] loadNibNamed:@"TableViewCells_iPhone" owner:self options:nil];
            cell = [nib_objects objectAtIndex:0 + (indexPath.row == 0 ? 0 : 1)];
        }
        
        cell.selectionStyle = UITableViewCellSelectionStyleNone;
        
        int index = indexPath.row * 2 - 1;
        if (index < 0) index = 0;
        
        cell.index = index;
        cell.parentController = self;
        cell.cell1View.hidden = YES;
        cell.cell2View.hidden = YES;
        
        if (index < [self.media count]) {
            
            KalturaMediaEntry *mediaEntry = [self.media objectAtIndex:index++];
            
            [cell updateCell1:mediaEntry];
            
        }
        
        if (index < [self.media count]) {
            
            KalturaMediaEntry *mediaEntry = [self.media objectAtIndex:index++];
            
            [cell updateCell2:mediaEntry];
            
        }
        
        
        return cell;
    }

}

- (void)openMediaInfoByIndex:(int)index {
    
    MediaInfoViewController_iPhone *controller = [[MediaInfoViewController_iPhone alloc] initWithNibName:@"MediaInfoViewController_iPhone" bundle:nil];
    
    controller.mediaEntry = [self.media objectAtIndex:index];
    
    if (self.category) {
        controller.categoryName = self.category.name;
    } else if (mostPopular) {
        controller.categoryName = @"Most Popular";
    }
    [app.navigation pushViewController:controller animated:YES];
    
    [controller release];
}

#pragma mark -

- (void)dealloc {
    
    [self.media removeAllObjects];
    [self.media release];
    
    [super dealloc];
    
}


- (void)didReceiveMemoryWarning
{
    // Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
    
    // Release any cached data, images, etc that aren't in use.
}

#pragma mark - View lifecycle

- (void)viewDidLoad
{
    [super viewDidLoad];
    
    //mediaTableView.backgroundColor = [UIColor colorWithPatternImage:[UIImage imageNamed:@"bg_table.png"]];
    labelTitle.font = [UIFont fontWithName:@"Maven Pro" size:19];
    
    if (self.category) {
        labelTitle.text = self.category.name;
    } else if (mostPopular) {
        labelTitle.text = @"Most Popular";
    }
    
    buttonBack.hidden = mostPopular;
    
    // Do any additional setup after loading the view from its nib.
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

@end
