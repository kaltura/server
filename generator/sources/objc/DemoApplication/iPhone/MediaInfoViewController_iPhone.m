//
//  MediaInfoViewController_iPhone.m
//  Kaltura
//
//  Created by Pavel on 29.02.12.
//  Copyright (c) 2012 Kaltura. All rights reserved.
//

#import "MediaInfoViewController_iPhone.h"
#import "AppDelegate_iPhone.h"
#import "PlayerViewController_iPhone.h"

@implementation MediaInfoViewController_iPhone

@synthesize mediaEntry;
@synthesize categoryName;

const CGRect PlayerCGRect = { { 0.0f, 0.0f }, { 320.0f, 180.0f } };

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        app = (AppDelegate_iPhone *)[[UIApplication sharedApplication] delegate];
        
    }
    return self;
}

- (BOOL)shouldAutorotate {
    return NO;
}

- (IBAction)menuBarButtonPressed:(UIButton *)button {
    
    [app.navigation popToRootViewControllerAnimated:YES];
    
}

- (IBAction)categoryBarButtonPressed:(UIButton *)button {
    
    [app.navigation popViewControllerAnimated:YES];
    
}

- (void)mailComposeController:(MFMailComposeViewController*)_controller didFinishWithResult:(MFMailComposeResult)result error:(NSError*)error {
	
    [self dismissViewControllerAnimated:YES completion:nil];
    
}

- (IBAction)shareButtonPressed:(UIButton *)button {
    
    
    if (button.tag == 0) {
        
        [[Client instance] shareFacebook:self.mediaEntry];
        
    } else if (button.tag == 1) {
        
        [[Client instance] shareTwitter:self.mediaEntry];
        
        
    } else if (button.tag == 2) {
        
        if ([MFMailComposeViewController canSendMail]) {
            
            MFMailComposeViewController *_controller = [[MFMailComposeViewController alloc] init];    
            _controller.mailComposeDelegate = self;
            
            [_controller setSubject:@"Kaltura"];
            
            NSString *str = [NSString stringWithFormat:@"I just saw this great video on Kaltura mobile app, check it out:\n%@", [[Client instance] getShareURL:self.mediaEntry]];
            [_controller setMessageBody:str isHTML:NO];
            
            [self presentViewController:_controller animated:YES completion:nil];
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

- (IBAction)playButtonPressed {

    PlayerViewController_iPhone *controller = [[PlayerViewController_iPhone alloc] initWithNibName:@"PlayerViewController_iPhone" bundle:nil];
    controller.mediaEntry = self.mediaEntry;
    [app.navigation pushViewController:controller animated:YES];
    [controller release];
    
}

#pragma -

- (void)updateInterfaceOrientation:(BOOL)isLandscape {
	
    int descriptionWidth = isLandscape ? 460 : 300;
    
    CGSize labelSize = CGSizeMake(descriptionWidth, 500);
	CGSize newSize = [textDescription.text sizeWithFont:textDescription.font constrainedToSize:labelSize lineBreakMode:textDescription.lineBreakMode];
	textDescription.frame = CGRectMake(textDescription.frame.origin.x, textDescription.frame.origin.y, descriptionWidth, newSize.height);
    
    float descriptionHeight = textDescription.frame.origin.y + textDescription.frame.size.height + viewShare.frame.size.height + 5;
    
    if (!isLandscape) {
        
        viewIntro.frame = PlayerCGRect;
        
        viewDescription.frame = CGRectMake(PlayerCGRect.origin.x, PlayerCGRect.size.height, PlayerCGRect.size.width, descriptionHeight);
        
        
    } else {
        
        viewIntro.frame = CGRectMake(0, 0, 480, 190);
        
        viewDescription.frame = CGRectMake(0, 190 - 44, 480, descriptionHeight);
       
    }
    
    scrollMain.contentSize = CGSizeMake(viewIntro.frame.size.width, viewDescription.frame.origin.y + viewDescription.frame.size.height);
    
}

- (void)viewWillAppear:(BOOL)animated {
	[super viewWillAppear:animated];
    
	[self updateInterfaceOrientation:UIInterfaceOrientationIsLandscape([[UIApplication sharedApplication] statusBarOrientation])];
    
}

- (void)viewDidDisappear:(BOOL)animated {
	[super viewDidDisappear:animated];
}

- (void)viewWillDisappear:(BOOL)animated {
    [self stopAndRemovePlayer];
	[super viewWillDisappear:animated];
}

- (void)willRotateToInterfaceOrientation:(UIInterfaceOrientation)toInterfaceOrientation duration:(NSTimeInterval)duration {
	
	[self updateInterfaceOrientation:UIInterfaceOrientationIsLandscape(toInterfaceOrientation)];
}

#pragma -

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
    
    [self drawPlayer];
    
    labelTitle.font = [UIFont fontWithName:@"Maven Pro" size:19];
    
    labelVTitle.font = [UIFont fontWithName:@"Maven Pro" size:16];
    labelVDuration.font = [UIFont fontWithName:@"Maven Pro" size:14];
    textDescription.font = [UIFont fontWithName:@"Maven Pro" size:14];
    
    labelVTitle.text = self.mediaEntry.name;
    labelVDuration.text = [NSString stringWithFormat:@"%d:%.2d", self.mediaEntry.duration / 60, self.mediaEntry.duration % 60];
    textDescription.text = self.mediaEntry.description;
    
    [imgThumb updateWithMediaEntry:self.mediaEntry withSize:CGSizeMake(480, 320)];
    
    [buttonCategory.titleLabel setFont:[UIFont fontWithName:@"Maven Pro" size:13]];
    [buttonCategory setTitle:self.categoryName forState:UIControlStateNormal];
    
    UIImage *stretchImage = [[UIImage imageNamed:@"button_category.png"] stretchableImageWithLeftCapWidth:30 topCapHeight:0];
    [buttonCategory setBackgroundImage:stretchImage forState:UIControlStateNormal];
    
    float width = [self.categoryName sizeWithFont:[UIFont fontWithName:@"Maven Pro" size:13]].width + 40;
    
    [buttonCategory setFrame:CGRectMake(buttonCategory.frame.origin.x, buttonCategory.frame.origin.y, width, buttonCategory.frame.size.height)];
    
    labelTitle.frame = CGRectMake(width + 2, 0, self.view.frame.size.width - width - 46, 44);
    
}

- (void)drawPlayer{
    
    [[UIDevice currentDevice] beginGeneratingDeviceOrientationNotifications];
    
    [[NSNotificationCenter defaultCenter] addObserver:self
                                             selector:@selector( orientationDidChange: )
                                                 name:UIDeviceOrientationDidChangeNotification
                                               object:nil];
    
    [[NSNotificationCenter defaultCenter] addObserver:self
                                             selector:@selector( toggleFullscreen: )
                                                 name:@"toggleFullscreenNotification"
                                               object:nil];
    
    [self.navigationController setDelegate:self];
    
    if ( !playerViewController ) {
        playerViewController = [[ KPViewController alloc] init];
        
        playerViewController.view.frame = PlayerCGRect;
    }
    
    [scrollMain addSubview:playerViewController.view];
    [playerViewController viewWillAppear:NO];
    [self reDrawPlayer: PlayerCGRect.origin.x right: PlayerCGRect.origin.y width: PlayerCGRect.size.width height: PlayerCGRect.size.height];
    
    NSString *iframeUrl = [[Client instance] getIframeURL:mediaEntry];
    [playerViewController setWebViewURL: iframeUrl];
}

- (void)reDrawPlayer: (CGFloat )top right: (CGFloat )right width: (CGFloat )width height: (CGFloat )height
{
    NSLog(@"reDrawPlayer Enter");
    
    [playerViewController resizePlayerView:(CGRect){right, top, width,height}];
    
    NSLog(@"reDrawPlayer Exit");
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

- (void)stopAndRemovePlayer{
    NSLog(@"stopAndRemovePlayer Enter");
    
    [playerViewController stopAndRemovePlayer];
    [playerViewController.view removeFromSuperview];
    [playerViewController removeFromParentViewController];
    playerViewController = nil;
    
    NSLog(@"stopAndRemovePlayer Exit");
}

-(void) orientationDidChange:(NSNotification *)notification {
    NSLog(@"orientationDidChange Enter");
    
    [ playerViewController checkOrientationStatus ];
    
    NSLog(@"orientationDidChange Exit");
}

- (void)toggleFullscreen:(NSNotification *)note {
    NSLog(@"toggleFullscreen Enter");
    
    NSDictionary *theData = [note userInfo];
    if (theData != nil) {
        NSNumber *n = [theData objectForKey:@"isFullScreen"];
        BOOL isFullScreen = [n boolValue];
        
        if ( isFullScreen ) {
            [[[UIApplication sharedApplication] delegate].window addSubview:playerViewController.view];
        }
        else if( !isFullScreen ){
            [scrollMain addSubview:playerViewController.view];
        }
    }
    
    NSLog(@"toggleFullscreen Exit");
}

- (void)dealloc {
    [super dealloc];
}
@end
