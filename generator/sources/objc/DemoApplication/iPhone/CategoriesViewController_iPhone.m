//
//  CategoriesViewController_iPhone.m
//  Kaltura
//
//  Created by Pavel on 28.02.12.
//  Copyright (c) 2012 Kaltura. All rights reserved.
//

#import "CategoriesViewController_iPhone.h"
#import "CategoryViewController_iPhone.h"
#import "AppDelegate_iPhone.h"

@implementation CategoriesViewController_iPhone

@synthesize categories;

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
        
        app = (AppDelegate_iPhone *)[[UIApplication sharedApplication] delegate];
        
        self.navigationItem.title = @"Categories";
        self.categories = [[NSMutableArray alloc] init];
    }
    return self;
}

- (BOOL)shouldAutorotate {
    return NO;
}

- (IBAction)menuBarButtonPressed:(UIButton *)button {
    
    [app.navigation popToRootViewControllerAnimated:YES];
 
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
    [self updateCategories:searchStr];
    
    return YES;
}

- (BOOL)textFieldShouldClear:(UITextField *)textField {
    
    [self updateCategories:@""];
    
    return YES;
}

- (IBAction)searchButtonPressed:(UIButton *)button {
    
    if (categoriesTableView.frame.origin.y == 44) {
        
        [searchText becomeFirstResponder];
        
    } else {
        
        [searchText resignFirstResponder];
        
    }
    
    [UIView beginAnimations:nil context:nil];
    [UIView setAnimationDuration:0.5];
    
    if (categoriesTableView.frame.origin.y == 44) {
        
        categoriesTableView.frame = CGRectMake(0, 92, 480, 206);
        
    } else {
        
        categoriesTableView.frame = CGRectMake(0, 44, 480, 254);
        
    }
    
    [UIView commitAnimations];
}

- (void)updateCategories:(NSString *)searchStr {
    
    [activity startAnimating];
    
    [self.categories removeAllObjects];
    
    NSArray *array = [[Client instance] getCategories];
    
    for (KalturaCategory *category in array) {
        
        BOOL canAdd = YES;
        
        if ([searchStr length] > 0) {
            
            NSString *str = category.fullName;
            
            if ([str rangeOfString:searchStr options:NSCaseInsensitiveSearch].location == NSNotFound) {
                
                canAdd = NO;
                
            }
            
            
        }
        
        if (canAdd) {
        
            [self.categories addObject:category];
        }
        
    }
    
    [activity stopAnimating];
    
    [categoriesTableView reloadData];
    
}



- (void)updateInterfaceOrientation:(BOOL)_isLandscape {
	
    isLandscape = _isLandscape;
    
    [searchText resignFirstResponder];
    
    if (isLandscape) {
        
        categoriesTableView.frame = CGRectMake(0, 44, 480, 254);
        
    } else {
        
        categoriesTableView.frame = CGRectMake(0, 92, 320, 368);
        
    }
    
    buttonSearch.hidden = !isLandscape;
}

- (void)willRotateToInterfaceOrientation:(UIInterfaceOrientation)toInterfaceOrientation duration:(NSTimeInterval)duration {
	
	[self updateInterfaceOrientation:UIInterfaceOrientationIsLandscape(toInterfaceOrientation)];
}

- (void)viewWillAppear:(BOOL)animated {
    
    [super viewWillAppear:YES];

    [self updateInterfaceOrientation:UIInterfaceOrientationIsLandscape([[UIApplication sharedApplication] statusBarOrientation])];
    
}

- (void)viewDidAppear:(BOOL)animated {
    
    [super viewDidAppear:YES];
    
    [self updateCategories:@""];
}

#pragma -
#pragma Table View

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView {
    // Return the number of sections.
	return 1;
}


- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
    // Return the number of rows in the section.
    
    return [self.categories count];
    
}



// Customize the appearance of table view cells.
- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath {
    
    NSString *CellIdentifier = @"Cell";
	
	UITableViewCell *cell  = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
    if (cell == nil) {
		cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CellIdentifier] autorelease];
    }
    
    
    KalturaCategory *category = [self.categories objectAtIndex:indexPath.row];
    
    cell.textLabel.text = category.fullName;
    cell.textLabel.font = [UIFont fontWithName:@"Maven Pro" size:16];
    cell.accessoryType = UITableViewCellAccessoryDisclosureIndicator;
    /*
    if (category.entriesCount == 0) {
    
        cell.accessoryType = UITableViewCellAccessoryNone;
        cell.selectionStyle = UITableViewCellSelectionStyleNone;
       
    } else {
        
        cell.accessoryType = UITableViewCellAccessoryDisclosureIndicator;
        cell.selectionStyle = UITableViewCellSelectionStyleBlue;
       
    }*/
    
    return cell;
}

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath {

    CategoryViewController_iPhone *controller = [[CategoryViewController_iPhone alloc] initWithNibName:@"CategoryViewController_iPhone" bundle:nil];
    
    controller.category = [self.categories objectAtIndex:indexPath.row];
    
    [app.navigation pushViewController:controller animated:YES];
    
    [controller release];
    
}

#pragma mark -

- (void)dealloc {
    
    [self.categories removeAllObjects];
    [self.categories release];

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
    
    categoriesTableView.backgroundColor = [UIColor colorWithPatternImage:[UIImage imageNamed:@"bg_table.png"]];
    labelTitle.font = [UIFont fontWithName:@"Maven Pro" size:19];
    searchLabel.font = [UIFont fontWithName:@"Maven Pro" size:17];
    searchText.font = [UIFont fontWithName:@"Maven Pro" size:17];
    
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
