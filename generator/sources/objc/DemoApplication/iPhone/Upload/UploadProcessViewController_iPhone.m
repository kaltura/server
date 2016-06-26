//
//  UploadProcessViewController_iPhone.m
//  Kaltura
//
//  Created by Pavel on 06.03.12.
//  Copyright (c) 2012 Kaltura. All rights reserved.
//

#import "UploadProcessViewController_iPhone.h"
#import "AppDelegate_iPhone.h"
#import "UploadSuccessViewController_iPhone.h"


@implementation UploadProcessViewController_iPhone

@synthesize data;
@synthesize cancelAlert;

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


- (void)alertView:(UIAlertView *)alertView didDismissWithButtonIndex:(NSInteger)buttonIndex {
    
    if (buttonIndex == 1) {
        
        [[Client instance] cancelUploading];
        
        [app.navigation popToRootViewControllerAnimated:YES];
    }
    
    [self.cancelAlert release];
    self.cancelAlert = nil;
}

- (IBAction)menuBarButtonPressed:(UIButton *)button {
    
    if (![[Client instance] uploadingInProgress]) {
        [app.navigation popToRootViewControllerAnimated:YES];
    } else {
        
        self.cancelAlert = [[UIAlertView alloc] initWithTitle:@"Uploading is in progress!\nDo you want to cancel?" message:nil delegate:self cancelButtonTitle:@"No" otherButtonTitles:@"Yes", nil];
        [self.cancelAlert show];
        
    }
}

- (void)updateProgress:(NSNumber *)value {

    progressView.progress = [value floatValue];

}

- (void)uploadFinished {
    
    if (self.cancelAlert) {
        
        [self.cancelAlert dismissWithClickedButtonIndex:0 animated:YES];
        
    }
    
    UploadSuccessViewController_iPhone *controller = [[UploadSuccessViewController_iPhone alloc] initWithNibName:@"UploadSuccessViewController_iPhone" bundle:nil];
    [app.navigation pushViewController:controller animated:YES];
    [controller release];
    
}

- (void)uploadFailed {

    buttonMenu.enabled = YES;

    [app.navigation popViewControllerAnimated:YES];

    UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Uploading error\nPlease try again" message:nil delegate:nil cancelButtonTitle:@"Close" otherButtonTitles:nil];
    [alert show];
    [alert release];
    

    
}

- (void)uploadProcess {
    
    [[Client instance] uploadProcess:data withDelegate:self];
    
        
}

#pragma -
#pragma mark - View lifecycle

- (void)viewDidAppear:(BOOL)animated {

    [super viewDidAppear:YES];
    
     [self performSelector:@selector(uploadProcess) withObject:nil afterDelay:0.1];
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
    
    labelTitle.font = [UIFont fontWithName:@"Maven Pro" size:19];
    viewMain.backgroundColor = [UIColor colorWithPatternImage:[UIImage imageNamed:@"bg_table.png"]];

    labelUploading.font = [UIFont fontWithName:@"Maven Pro" size:17];
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
