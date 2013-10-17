//
//  SettingsViewController_iPhone.m
//  Kaltura
//
//  Created by Pavel on 05.03.12.
//  Copyright (c) 2012 Kaltura. All rights reserved.
//

#import "SettingsViewController_iPhone.h"
#import "AppDelegate_iPhone.h"
#import "HomeViewController_iPhone.h"

@implementation SettingsViewController_iPhone

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
        
        app = (AppDelegate_iPhone *)[[UIApplication sharedApplication] delegate];
        
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

- (BOOL)textFieldShouldReturn:(UITextField *)textField {
    
    [textField resignFirstResponder];
    
    return YES;
    
}

- (IBAction)menuBarButtonPressed:(UIButton *)button {
    
    [app.navigation popToRootViewControllerAnimated:YES];
    
}

- (IBAction)loginButtonPressed:(UIButton *)button {
    
    if ([textUser.text length] > 0 && [textPassword.text length] > 0) {
        
        [[NSUserDefaults standardUserDefaults] setObject:textUser.text forKey:@"userEmail"];
        [[NSUserDefaults standardUserDefaults] setObject:textPassword.text forKey:@"userPassword"];
        [[NSUserDefaults standardUserDefaults] synchronize];

        if ([[Client instance] login]) {
            
            id rootController = [[self.navigationController viewControllers] objectAtIndex:0];
          
            if(![rootController isKindOfClass:[HomeViewController_iPhone class]]){
                //after login switch the rootviewcontroller to be HomeViewController_iPhone and not SettingsViewController_iPad
                NSMutableArray *viewControllers = [NSMutableArray arrayWithArray:[self.navigationController viewControllers]];
                HomeViewController_iPhone *homeController = [[HomeViewController_iPhone alloc] initWithNibName:@"HomeViewController_iPhone" bundle:nil];
                [viewControllers replaceObjectAtIndex:0 withObject:homeController];
                [app.navigation setViewControllers:viewControllers];
                [homeController release];

            }
        
            [app.navigation popToRootViewControllerAnimated:YES];
            
        } else {
            
            [[NSUserDefaults standardUserDefaults] removeObjectForKey:@"userEmail"];
            [[NSUserDefaults standardUserDefaults] removeObjectForKey:@"userPassword"];
            [[NSUserDefaults standardUserDefaults] synchronize];

            buttonMenu.enabled = NO;
            
            UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Incorrect user name or password" message:nil delegate:nil cancelButtonTitle:@"Ok" otherButtonTitles:nil];
            [alert show];
            [alert release];
            
            
        }
    }
    
}

#pragma -

- (void)updateInterfaceOrientation:(BOOL)isLandscape {
	    
    if (!isLandscape) {
        
        labelUser.textAlignment = UITextAlignmentLeft;
        labelUser.frame = CGRectMake(20, 10, labelUser.frame.size.width, labelUser.frame.size.height);
        viewUser.frame = CGRectMake(15, 45, viewUser.frame.size.width, viewUser.frame.size.height);

        labelPassword.textAlignment = UITextAlignmentLeft;
        labelPassword.frame = CGRectMake(20, 90, labelPassword.frame.size.width, labelPassword.frame.size.height);
        viewPassword.frame = CGRectMake(15, 125, viewPassword.frame.size.width, viewPassword.frame.size.height);

        
        
    } else {
        
        labelUser.textAlignment = UITextAlignmentRight;
        labelUser.frame = CGRectMake(20, 10, labelUser.frame.size.width, labelUser.frame.size.height);
        viewUser.frame = CGRectMake(140, 10, viewUser.frame.size.width, viewUser.frame.size.height);
        
        labelPassword.textAlignment = UITextAlignmentRight;
        labelPassword.frame = CGRectMake(20, 50, labelPassword.frame.size.width, labelPassword.frame.size.height);
        viewPassword.frame = CGRectMake(140, 50, viewPassword.frame.size.width, viewPassword.frame.size.height);
        
    }
    
     
}

- (void)viewWillAppear:(BOOL)animated {
	[super viewWillAppear:animated];
    
	[self updateInterfaceOrientation:UIInterfaceOrientationIsLandscape([[UIApplication sharedApplication] statusBarOrientation])];
    
    NSString *userEmail = [[NSUserDefaults standardUserDefaults] objectForKey:@"userEmail"];
    NSString *userPassword = [[NSUserDefaults standardUserDefaults] objectForKey:@"userPassword"];
    
    textUser.text = userEmail;
    textPassword.text = userPassword;
    
}


- (void)willRotateToInterfaceOrientation:(UIInterfaceOrientation)toInterfaceOrientation duration:(NSTimeInterval)duration {
	
	[self updateInterfaceOrientation:UIInterfaceOrientationIsLandscape(toInterfaceOrientation)];
}


#pragma mark - View lifecycle

- (void)viewDidLoad
{
    [super viewDidLoad];
    
    
    labelTitle.font = [UIFont fontWithName:@"Maven Pro" size:19];
    viewMain.backgroundColor = [UIColor colorWithPatternImage:[UIImage imageNamed:@"bg_table.png"]];

    labelUser.font = [UIFont fontWithName:@"Maven Pro" size:17];
    textUser.font = [UIFont fontWithName:@"Maven Pro" size:16];
    
    labelPassword.font = [UIFont fontWithName:@"Maven Pro" size:17];
    textPassword.font = [UIFont fontWithName:@"Maven Pro" size:16];
    
    [buttonLogin.titleLabel setFont:[UIFont fontWithName:@"Maven Pro" size:18]];
    
    NSString *userEmail = [[NSUserDefaults standardUserDefaults] objectForKey:@"userEmail"];
    
    if (!userEmail || [userEmail length] == 0) {
        buttonMenu.enabled = NO;
    }
    
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
