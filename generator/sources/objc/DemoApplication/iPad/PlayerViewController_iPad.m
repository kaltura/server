//
//  PlayerViewController_iPad.m
//  Kaltura
//
//  Created by Pavel on 22.03.12.
//  Copyright (c) 2012 Kaltura. All rights reserved.
//

#import "PlayerViewController_iPad.h"
#import "AppDelegate_iPad.h"

@implementation PlayerViewController_iPad

@synthesize mediaEntry;
@synthesize bitrates;
@synthesize moviePlayerViewController;
@synthesize flavorType;

static NSString* flavorID = @"";
+ (NSString*) getFlavorID { return flavorID; }

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

- (void)MPMoviePlayerLoadStateDidChange:(NSNotification *)notification {
    
    if (self.moviePlayerViewController.moviePlayer.loadState == 3) {
        
        [activity stopAnimating];
    }
}

- (void)MPMoviePlayerPlaybackStateDidChange:(NSNotification *)notification {
    
    if (self.moviePlayerViewController.moviePlayer.playbackState == MPMoviePlaybackStatePlaying) {
        
        activeTime = CFAbsoluteTimeGetCurrent();
        //[activity stopAnimating];
        [buttonPlay setImage:[UIImage imageNamed:@"button_player_pause.png"] forState:UIControlStateNormal];
        
    } else {
        
        [buttonPlay setImage:[UIImage imageNamed:@"button_player_play.png"] forState:UIControlStateNormal];
        
    }
}

- (void)MPMoviePlayerPlaybackDidFinish:(NSNotification *)notification {
    
    if (toolsView.alpha < 1.0) {
        
        [UIView beginAnimations:nil context:nil];
        [UIView setAnimationDuration:1.0];
        
        toolsView.alpha = 1.0;
        
        [UIView commitAnimations];
        
    }
    
}

- (void)deregisterFromNotifications {
    [[NSNotificationCenter defaultCenter] removeObserver:self
                                                    name:MPMoviePlayerLoadStateDidChangeNotification
                                                  object:nil];
    
    [[NSNotificationCenter defaultCenter] removeObserver:self
                                                    name:MPMoviePlayerPlaybackStateDidChangeNotification
                                                  object:nil];
    
    [[NSNotificationCenter defaultCenter] removeObserver:self
                                                    name:MPMoviePlayerPlaybackDidFinishNotification
                                                  object:nil];
    
}


- (void)registerForNotifications {
    [[NSNotificationCenter defaultCenter] addObserver:self
                                             selector:@selector(MPMoviePlayerLoadStateDidChange:)
                                                 name:MPMoviePlayerLoadStateDidChangeNotification
                                               object:nil];
    
    [[NSNotificationCenter defaultCenter] addObserver:self
                                             selector:@selector(MPMoviePlayerPlaybackStateDidChange:)
                                                 name:MPMoviePlayerPlaybackStateDidChangeNotification
                                               object:nil];
    
    [[NSNotificationCenter defaultCenter] addObserver:self
                                             selector:@selector(MPMoviePlayerPlaybackDidFinish:)
                                                 name:MPMoviePlayerPlaybackDidFinishNotification
                                               object:nil];
    
    
//    [[NSNotificationCenter defaultCenter] addObserver:self
//                                             selector:@selector(callbackWV:)
//                                                 name:@"callbackWV"
//                                               object:nil];
}

- (void)runWithBitrate:(int)ind {
    
    for (int i = 0; i < [self.bitrates count]; i++) {
        UIButton *button = (UIButton *)[viewBitrates viewWithTag:i + 100];
        button.enabled = (i != ind);
        
        if (i == ind) {
            imageBitrateCheck.center = CGPointMake(imageBitrateCheck.center.x, button.center.y);
        }
    }
    
    [activity startAnimating];
    
    NSDictionary *dic = [self.bitrates objectAtIndex:ind];
    
    [buttonBitrate setTitle:[Utils getStrBitrate:[dic objectForKey:@"bitrate"]] forState:UIControlStateNormal];
    
    if ([flavorType isEqual:@"wv"])
    {
        //There will be initialize of WV only if the flavor id will be chenged
        if([dic objectForKey:@"id"] != flavorID){
            flavorID = [dic objectForKey:@"id"];
            [[Client instance] initializeWVDictionary:[dic objectForKey:@"id"]];
            [Client instance].delegate = self;
        }
        else{
            [[Client instance] selectBitrate:ind];
        }
        
        NSString *strURL = [[Client instance] getVideoURL:self.mediaEntry forMediaEntryDuration: self.mediaEntry.duration forFlavor:[dic objectForKey:@"id"] forFlavorType: flavorType];
        [[Client instance] playMovieFromUrl:strURL];
    }
    else
    {
        [self playVideo:dic];
    }
}

-(void) videoStop{
    
    [self.moviePlayerViewController.moviePlayer stop];
}

-(void) videoPlay:(NSURL*) url{
    
    NSTimeInterval interval = self.moviePlayerViewController.moviePlayer.currentPlaybackTime;
    [self.moviePlayerViewController.moviePlayer stop];
    [self.moviePlayerViewController.moviePlayer setContentURL:url];
    [self.moviePlayerViewController.moviePlayer prepareToPlay];
    self.moviePlayerViewController.moviePlayer.initialPlaybackTime = interval;
    [self.moviePlayerViewController.moviePlayer play];
}

-(void) callbackWV:(NSNotification *)notification
{
    NSDictionary* userInfo = [notification userInfo];
    [self performSelector:@selector(playVideo:) withObject:userInfo afterDelay:5];
}

-(void) playVideo:(NSDictionary*)dic{
    
    NSString *strURL = [[Client instance] getVideoURL:self.mediaEntry forMediaEntryDuration: self.mediaEntry.duration forFlavor:[dic objectForKey:@"id"] forFlavorType: flavorType];

    NSTimeInterval interval = self.moviePlayerViewController.moviePlayer.currentPlaybackTime;
    [self.moviePlayerViewController.moviePlayer stop];
    [self.moviePlayerViewController.moviePlayer setContentURL:[NSURL URLWithString:strURL]];
    [self.moviePlayerViewController.moviePlayer prepareToPlay];
    self.moviePlayerViewController.moviePlayer.initialPlaybackTime = interval;
    [self.moviePlayerViewController.moviePlayer play];
}

- (void)bitrateButtonPressed:(UIButton *)button {

    [self runWithBitrate:button.tag - 100];
    
    viewBitrates.alpha = 0.0;
}

- (IBAction)sliderChanged:(UISlider *)slider {

    activeTime = CFAbsoluteTimeGetCurrent();
    
    float time = self.mediaEntry.duration * slider.value;
    if (time < 0) {
        
        time = 0;
    }
    if (time > self.mediaEntry.duration){
        
        time = self.mediaEntry.duration;
    }
    
    self.moviePlayerViewController.moviePlayer.currentPlaybackTime = time;
    currentTimeLabel.text = [Utils getTimeStr:self.moviePlayerViewController.moviePlayer.currentPlaybackTime];
    
    [activity startAnimating];
}

- (IBAction)volumePressed:(UIButton *)button {
    
    if (viewVolume.alpha == 0.0) {
        
        viewBitrates.alpha = 0.0;
        
        viewVolume.frame = CGRectMake(button.frame.origin.x - 110,
                                      button.frame.origin.y - viewVolume.frame.size.height + 55,
                                      viewVolume.frame.size.width,
                                      viewVolume.frame.size.height);
        
        viewVolume.alpha = 1.0;
        
    }
    else {
        
        viewVolume.alpha = 0.0;
        
    }
}

- (IBAction)bitratesPressed:(UIButton *)button {
    
    if (viewBitrates.alpha == 0.0) {
        
        viewVolume.alpha = 0.0;
        
        viewBitrates.frame = CGRectMake(button.frame.origin.x - 74,
                                        button.frame.origin.y - viewBitrates.frame.size.height + 55,
                                        viewBitrates.frame.size.width,
                                        viewBitrates.frame.size.height);
        
        viewBitrates.alpha = 1.0;
        
    }
    else {
        
        viewBitrates.alpha = 0.0;
        
    }
}

- (void)willRotateToInterfaceOrientation:(UIInterfaceOrientation)toInterfaceOrientation duration:(NSTimeInterval)duration {
    
    if (viewBitrates.alpha > 0.0) {
        viewBitrates.alpha = 0.0;
    }
    if (viewVolume.alpha > 0.0) {
        viewVolume.alpha = 0.0;
    }
}

-(void) loadWVBitratesList:(NSArray*)wvBitrates{
    self.bitrates = wvBitrates;
    NSLog(@"%@", self.bitrates);
    [self crerateBitratesList];
}

- (void)updateBitrates {
    
    totalTimeLabel.text = [NSString stringWithFormat:@"/ %@", [Utils getTimeStr:self.mediaEntry.duration]];

    self.bitrates = [[Client instance] getBitratesList:mediaEntry withFilter:@"widevine"];
    
    //Check if the video supports WV
    if (self.bitrates.count < 1){
        
        flavorType = @"ipadnew";
        buttonBitrate.enabled = YES;
        
        self.bitrates = [[Client instance] getBitratesList:mediaEntry withFilter:@"ipadnew"];
        if (self.bitrates.count < 1){
            flavorType = @"iphonenew";
            buttonBitrate.enabled = YES;
            
            self.bitrates = [[Client instance] getBitratesList:mediaEntry withFilter:@"iphonenew"];
        }
    }
    else{
        
        flavorType = @"wv";
        buttonBitrate.enabled = NO;
        [buttonBitrate setTitle:@"auto" forState:UIControlStateDisabled];
    }
    
    if ([self.bitrates count] > 0){
        
        [self crerateBitratesList];
        
        self.moviePlayerViewController = [[MPMoviePlayerViewController alloc] init];
        self.moviePlayerViewController.moviePlayer.controlStyle = MPMovieControlStyleNone;
        
        self.moviePlayerViewController.view.autoresizingMask = UIViewAutoresizingFlexibleWidth | UIViewAutoresizingFlexibleHeight |
        UIViewAutoresizingFlexibleLeftMargin | UIViewAutoresizingFlexibleRightMargin |
        UIViewAutoresizingFlexibleTopMargin | UIViewAutoresizingFlexibleBottomMargin;
        
        BOOL isLandscape = UIInterfaceOrientationIsLandscape([[UIApplication sharedApplication] statusBarOrientation]);
        
        self.moviePlayerViewController.moviePlayer.view.frame = CGRectMake(0, 0, (isLandscape ? 480 : 320), (isLandscape ? 300 : 460));
        
        [self.view insertSubview:self.moviePlayerViewController.moviePlayer.view atIndex:0];
        [self registerForNotifications];
        
        [self runWithBitrate:0];
        
        activeTime = CFAbsoluteTimeGetCurrent();
        
        timer = [NSTimer scheduledTimerWithTimeInterval:1.0 target:self selector:@selector(updateCurrentTime) userInfo:nil repeats:YES];
        
    } else {
        
        toolbarView.hidden = YES;
        [activity stopAnimating];
        
    }
    
    [UIView beginAnimations:nil context:nil];
    [UIView setAnimationDuration:1.0];
    
    toolsView.alpha = 1.0;
    
    [UIView commitAnimations];    
}

-(void) crerateBitratesList{
    
    for (int i = 0; i < [self.bitrates count]; i++) {
        
        NSArray *nib_objects = [[NSBundle mainBundle] loadNibNamed:@"Bitrates" owner:self options:nil];
        
        int index = i;
        if (i > 0) index = 1;
        if (i == [self.bitrates count] - 1) index = 2;
        
        UIButton *button = [nib_objects objectAtIndex:index];
        
        button.frame = CGRectMake(10, 6 + i * 32, button.frame.size.width, button.frame.size.height);
        button.tag = i + 100;
        NSMutableDictionary *dic = [self.bitrates objectAtIndex:i];
        
        [button setTitle:[Utils getStrBitrate:[dic objectForKey:@"bitrate"]] forState:UIControlStateNormal];
        
        [button addTarget:self action:@selector(bitrateButtonPressed:)forControlEvents:UIControlEventTouchUpInside];
        [viewBitrates insertSubview:button belowSubview:imageBitrateCheck];
    }
    
    viewBitratesMiddle.frame = CGRectMake(viewBitratesMiddle.frame.origin.x,
                                          viewBitratesMiddle.frame.origin.y,
                                          viewBitratesMiddle.frame.size.width,
                                          32 * [self.bitrates count]);
    
    viewBitratesBottom.frame = CGRectMake(viewBitratesBottom.frame.origin.x,
                                          viewBitratesMiddle.frame.origin.y + viewBitratesMiddle.frame.size.height,
                                          viewBitratesBottom.frame.size.width,
                                          viewBitratesBottom.frame.size.height);
    
    viewBitrates.frame = CGRectMake(viewBitrates.frame.origin.x,
                                    viewBitrates.frame.origin.y,
                                    viewBitrates.frame.size.width,
                                    viewBitratesBottom.frame.origin.y + viewBitratesBottom.frame.size.height);
}

- (void)updateCurrentTime {
    
    if (self.moviePlayerViewController.moviePlayer.playbackState == MPMoviePlaybackStatePlaying) {
        
        if ([activity isAnimating] && self.moviePlayerViewController.moviePlayer.loadState == 3) {
            
            [activity stopAnimating];
        }
        
        currentTimeLabel.text = [Utils getTimeStr:self.moviePlayerViewController.moviePlayer.currentPlaybackTime];
        
        if (self.mediaEntry.duration > 0) {
            
            timeSlider.value = self.moviePlayerViewController.moviePlayer.currentPlaybackTime / self.mediaEntry.duration;
        }
        
        if (CFAbsoluteTimeGetCurrent() - activeTime > 5.0 && toolsView.alpha == 1.0 &&
            viewBitrates.alpha == 0 && viewVolume.alpha == 0.0) {
            
            [UIView beginAnimations:nil context:nil];
            [UIView setAnimationDuration:1.0];
            
            toolsView.alpha = 0.0;
            
            [UIView commitAnimations];
            
        } else {
            
            if (viewBitrates.alpha == 1.0) {
                activeTime = CFAbsoluteTimeGetCurrent();
            }
            
        }
        
    }    
    
    BOOL _noVolume = (app.volumeLevel == 0);
    if (_noVolume != noVolume) {
        noVolume = _noVolume;
        [buttonVolume setImage:[UIImage imageNamed:(noVolume ? @"no_volume_ico.png" : @"volume_ico.png")] forState:UIControlStateNormal];
        
    }
    
}

- (void)touchesBegan:(NSSet *)touches withEvent:(UIEvent *)event {
    
    activeTime = CFAbsoluteTimeGetCurrent();
    
    if (viewBitrates.alpha > 0.0 || viewVolume.alpha > 0.0) {
        viewBitrates.alpha = 0.0;
        viewVolume.alpha = 0.0;
        
        return;
    }
    
    //The animation for toolsView that fades it out or in 
    [UIView beginAnimations:nil context:nil];
    [UIView setAnimationDuration:1.0];
    
    if (toolsView.alpha == 0.0) {
        
        toolsView.alpha = 1.0;        
    }
    else{
        
        toolsView.alpha = 0.0;
    }
    
    [UIView commitAnimations];
    
}

- (IBAction)playPressed {
    
    activeTime = CFAbsoluteTimeGetCurrent();
    
    if (self.moviePlayerViewController.moviePlayer.playbackState == MPMoviePlaybackStatePlaying) {
        
        [self.moviePlayerViewController.moviePlayer pause];
        
    } else {
        
        [self.moviePlayerViewController.moviePlayer play];
        
    }
    
}

- (IBAction)donePressed {
    
    if (self.moviePlayerViewController && [flavorType isEqualToString:@"wv"])
    {   
        [[Client instance] donePlayingMovieWithWV];
    }
    else
    {
        [self.moviePlayerViewController.moviePlayer stop];
    }
    
    [self.navigationController popViewControllerAnimated:YES];
}

#pragma mark - View lifecycle

- (void)dealloc {
    
    [self.bitrates release];
    
    if (self.moviePlayerViewController) {
        
        [self.moviePlayerViewController release];
        
    }
    
    [super dealloc];
    
}

- (void)viewWillDisappear:(BOOL)animated
{
    [super viewWillDisappear:YES];
    
    [self deregisterFromNotifications];
    
    if (timer) {
        
        [timer invalidate];
        timer = nil;
        
    }
    // Do any additional setup after loading the view from its nib.
}

- (void)viewDidAppear:(BOOL)animated
{
    [super viewDidAppear:YES];
    
    self.bitrates = [[NSMutableArray alloc] init];
    [self updateBitrates];
    
    // Do any additional setup after loading the view from its nib.
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    
    app = (AppDelegate_iPad *)[[UIApplication sharedApplication] delegate];
    
    MPVolumeView *volumeView = [[MPVolumeView alloc] initWithFrame:CGRectMake(0, 8, viewVolume.frame.size.width, 30)];
	volumeView.showsRouteButton = NO;
	[viewVolume addSubview:volumeView];
	[volumeView release];
    
    noVolume = (app.volumeLevel == 0);
    
    if (noVolume) {
        
        [buttonVolume setImage:[UIImage imageNamed:@"no_volume_ico.png"] forState:UIControlStateNormal];
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
