//
//  HomeViewController_iPhone.m
//  Kaltura
//
//  Created by Pavel on 28.02.12.
//  Copyright (c) 2012 Kaltura. All rights reserved.
//

#import "HomeViewController_iPhone.h"
#import "CategoriesViewController_iPhone.h"
#import "UploadSelectViewController_iPhone.h"
#import "SettingsViewController_iPhone.h"
#import "AppDelegate_iPhone.h"
#import "CategoryViewController_iPhone.h"

@implementation HomeViewController_iPhone

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        
        app = (AppDelegate_iPhone *)[[UIApplication sharedApplication] delegate];
        
        self.navigationItem.title = @"Menu";
                
    }
    return self;
}

- (BOOL)shouldAutorotate {
    return NO;
}

- (void)menuProcess {
    
    [[Client instance] getCategories];
    
    [activity stopAnimating];
    
    if (selectedMenu == 0) {
        
        CategoryViewController_iPhone *controller = [[CategoryViewController_iPhone alloc] initWithNibName:@"CategoryViewController_iPhone" bundle:nil];
        controller.mostPopular = YES;
        [app.navigation pushViewController:controller animated:YES];
        [controller release];
        
    } else if (selectedMenu == 1) {
        
        CategoriesViewController_iPhone *controller = [[CategoriesViewController_iPhone alloc] initWithNibName:@"CategoriesViewController_iPhone" bundle:nil];
        [app.navigation pushViewController:controller animated:YES];
        [controller release];
        
    } else if (selectedMenu == 2) {
        
        UploadSelectViewController_iPhone *controller = [[UploadSelectViewController_iPhone alloc] initWithNibName:@"UploadSelectViewController_iPhone" bundle:nil];
        [app.navigation pushViewController:controller animated:YES];
        [controller release];
        
    }
}

- (IBAction)menuButtonPressed:(UIButton *)button {

    
    selectedMenu = button.tag;
    
    if (button.tag < 3) {
        
        [activity startAnimating];
        [self performSelector:@selector(menuProcess) withObject:nil afterDelay:0.1];
        
    } else if (button.tag == 3) {
        
        SettingsViewController_iPhone *controller = [[SettingsViewController_iPhone alloc] initWithNibName:@"SettingsViewController_iPhone" bundle:nil];
        [app.navigation pushViewController:controller animated:YES];
        [controller release];
        
    }
    
}

- (void)didReceiveMemoryWarning
{
    // Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
    
    // Release any cached data, images, etc that aren't in use.
}

#pragma mark - View lifecycle

- (void)viewDidAppear:(BOOL)animated {
    
    [super viewDidAppear:YES];

    [[Client instance].media removeAllObjects];
    
    NSString *userEmail = [[NSUserDefaults standardUserDefaults] objectForKey:@"userEmail"];
    
    if (!userEmail || [userEmail length] == 0) {
        
        [self menuButtonPressed:menuButton3];
        
    }
}

- (void)viewDidLoad
{
    [super viewDidLoad];

    menuButton0.titleLabel.lineBreakMode = UILineBreakModeWordWrap;
	menuButton0.titleLabel.numberOfLines = 0;
	menuButton0.titleLabel.textAlignment = UITextAlignmentCenter;
	[menuButton0.titleLabel setFont:[UIFont fontWithName:@"Maven Pro" size:16]];
	
    menuButton1.titleLabel.lineBreakMode = UILineBreakModeWordWrap;
	menuButton1.titleLabel.numberOfLines = 0;
	menuButton1.titleLabel.textAlignment = UITextAlignmentCenter;
	[menuButton1.titleLabel setFont:[UIFont fontWithName:@"Maven Pro" size:16]];

    menuButton2.titleLabel.lineBreakMode = UILineBreakModeWordWrap;
	menuButton2.titleLabel.numberOfLines = 0;
	menuButton2.titleLabel.textAlignment = UITextAlignmentCenter;
	[menuButton2.titleLabel setFont:[UIFont fontWithName:@"Maven Pro" size:16]];
	
    menuButton3.titleLabel.lineBreakMode = UILineBreakModeWordWrap;
	menuButton3.titleLabel.numberOfLines = 0;
	menuButton3.titleLabel.textAlignment = UITextAlignmentCenter;
	[menuButton3.titleLabel setFont:[UIFont fontWithName:@"Maven Pro" size:16]];
    
     
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
