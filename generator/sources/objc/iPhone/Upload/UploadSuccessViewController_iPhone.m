//
//  UploadSuccessViewController_iPhone.m
//  Kaltura
//
//  Created by Pavel on 06.03.12.
//  Copyright (c) 2012 Kaltura. All rights reserved.
//

#import "UploadSuccessViewController_iPhone.h"
#import "AppDelegate_iPhone.h"

@implementation UploadSuccessViewController_iPhone

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
        
        app = (AppDelegate_iPhone *)[[UIApplication sharedApplication] delegate];
        
    }
    return self;
}

- (void)didReceiveMemoryWarning
{
    // Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
    
    // Release any cached data, images, etc that aren't in use.
}

- (IBAction)menuBarButtonPressed:(UIButton *)button {
    
    [app.navigation popToRootViewControllerAnimated:YES];
    
}

#pragma mark - View lifecycle

- (void)viewDidLoad
{
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
    
    labelTitle.font = [UIFont fontWithName:@"Maven Pro" size:19];
    viewMain.backgroundColor = [UIColor colorWithPatternImage:[UIImage imageNamed:@"bg_table.png"]];
    
    labelThankYou.font = [UIFont fontWithName:@"Maven Pro" size:36];
    labelText.font = [UIFont fontWithName:@"Maven Pro" size:18];
   
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
