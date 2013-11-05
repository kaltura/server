//
//  SettingsViewController_iPhone.m
//  Kaltura
//
//  Created by Pavel on 05.03.12.
//  Copyright (c) 2012 Kaltura. All rights reserved.
//

#import "SettingsViewController_iPad.h"
#import "HomeViewController_iPad.h"

@implementation SettingsViewController_iPad

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
        
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
    
    [self.navigationController popToRootViewControllerAnimated:YES];
    
}

- (IBAction)loginButtonPressed:(UIButton *)button {
    
    if ([textUser.text length] > 0 && [textPassword.text length] > 0) {
        
        [[NSUserDefaults standardUserDefaults] setObject:textUser.text forKey:@"userEmail"];
        [[NSUserDefaults standardUserDefaults] setObject:textPassword.text forKey:@"userPassword"];
        [[NSUserDefaults standardUserDefaults] synchronize];

        if ([[Client instance] login]) {
            
            id rootController = [[self.navigationController viewControllers] objectAtIndex:0];
            
            if(![rootController isKindOfClass:[HomeViewController_iPad class]]){
                
                //after login switch the rootviewcontroller to be HomeViewController_iPad and not SettingsViewController_iPad
                NSMutableArray *viewControllers = [NSMutableArray arrayWithArray:[self.navigationController viewControllers]];
                HomeViewController_iPad *homeController = [[HomeViewController_iPad alloc] initWithNibName:@"HomeViewController_iPad" bundle:nil];
                [viewControllers replaceObjectAtIndex:0 withObject:homeController];
                [self.navigationController setViewControllers:viewControllers];
                [homeController release];

            }
            
            [self.navigationController popToRootViewControllerAnimated:YES];
            
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
    
	//[self updateInterfaceOrientation:UIInterfaceOrientationIsLandscape([[UIApplication sharedApplication] statusBarOrientation])];
    
    NSString *userEmail = [[NSUserDefaults standardUserDefaults] objectForKey:@"userEmail"];
    NSString *userPassword = [[NSUserDefaults standardUserDefaults] objectForKey:@"userPassword"];
    
    textUser.text = userEmail;
    textPassword.text = userPassword;
    
}


- (void)willRotateToInterfaceOrientation:(UIInterfaceOrientation)toInterfaceOrientation duration:(NSTimeInterval)duration {
	
	//[self updateInterfaceOrientation:UIInterfaceOrientationIsLandscape(toInterfaceOrientation)];
}


#pragma mark - View lifecycle

- (void)viewDidLoad
{
    [super viewDidLoad];
    
    
    labelTitle.font = [UIFont fontWithName:@"Maven Pro" size:24];
    viewMain.backgroundColor = [UIColor colorWithPatternImage:[UIImage imageNamed:@"bg_table@2x.png"]];

    labelUser.font = [UIFont fontWithName:@"Maven Pro" size:24];
    textUser.font = [UIFont fontWithName:@"Maven Pro" size:23];
    
    labelPassword.font = [UIFont fontWithName:@"Maven Pro" size:24];
    textPassword.font = [UIFont fontWithName:@"Maven Pro" size:23];
    
    [buttonLogin.titleLabel setFont:[UIFont fontWithName:@"Maven Pro" size:28]];
    
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
