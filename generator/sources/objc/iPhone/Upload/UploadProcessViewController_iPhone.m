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

- (void)endUploading {
    
    if (token) {
        
        [token release];
        
    }
    
    KalturaClient *client = [Client instance].client;
    client.delegate = nil;
    client.uploadProgressDelegate = nil;
    
    
}
- (void)alertView:(UIAlertView *)alertView didDismissWithButtonIndex:(NSInteger)buttonIndex {
    
    if (buttonIndex == 1) {
        
        KalturaClient *client = [Client instance].client;
        
        [client cancelRequest];
        
        [self endUploading];
        
        [app.navigation popToRootViewControllerAnimated:YES];
    }
    
    [self.cancelAlert release];
    self.cancelAlert = nil;
}

- (IBAction)menuBarButtonPressed:(UIButton *)button {
    
    if (uploadedSize == 0) {
        [app.navigation popToRootViewControllerAnimated:YES];
    } else {
        
        self.cancelAlert = [[UIAlertView alloc] initWithTitle:@"Uploading is in progress!\nDo you want to cancel?" message:nil delegate:self cancelButtonTitle:@"No" otherButtonTitles:@"Yes", nil];
        [self.cancelAlert show];
        
    }
}

- (void)request:(ASIHTTPRequest *)request didSendBytes:(long long)bytes {
    
    uploadedSize += bytes;
    
    progressView.progress = (float)(uploadedSize * 300 / fileSize) / 300.0;
    
}

- (void)requestFinished:(KalturaClientBase*)aClient withResult:(id)result {
    
    NSLog(@"requestFinished");
    
    if (self.cancelAlert) {
        
        [self.cancelAlert dismissWithClickedButtonIndex:0 animated:YES];
        
    }
    
    [self endUploading];
    
    
    UploadSuccessViewController_iPhone *controller = [[UploadSuccessViewController_iPhone alloc] initWithNibName:@"UploadSuccessViewController_iPhone" bundle:nil];
    [app.navigation pushViewController:controller animated:YES];
    [controller release];
    
}

- (void)requestFailed:(KalturaClientBase*)aClient {
    
    NSLog(@"requestFailed");
    
    [self endUploading];
    
    
    buttonMenu.enabled = YES;
    
}

- (void)uploadProcess {
    
    KalturaClient *client = [Client instance].client;
    client.delegate = nil;
    
    token = [[KalturaUploadToken alloc] init];
    token.fileName = @"video.m4v";
    token = [client.uploadToken addWithUploadToken:token];
    
    // return: object, params: object
    KalturaMediaEntry* entry = [[[KalturaMediaEntry alloc] init] autorelease];
    entry.name = [self.data objectForKey:@"title"];
    entry.mediaType = [KalturaMediaType VIDEO];
    entry.categories = [self.data objectForKey:@"category"];
    entry.description = [self.data objectForKey:@"description"];
    entry.tags = [self.data objectForKey:@"tags"];
    
    entry = [client.media addWithEntry:entry];
    
    // return: object, params: string, object
    KalturaUploadedFileTokenResource* resource = [[[KalturaUploadedFileTokenResource alloc] init] autorelease];
    resource.token = token.id;
    entry = [client.media addContentWithEntryId:entry.id withResource:resource];
   
    client.delegate = self;
    client.uploadProgressDelegate = self;
    
    NSDictionary *fileAttributes = [[NSFileManager defaultManager] attributesOfItemAtPath:[self.data objectForKey:@"path"] error:nil];
    
    NSNumber *fileSizeNumber = [fileAttributes objectForKey:NSFileSize];
    fileSize = [fileSizeNumber longLongValue];
    uploadedSize = 0;
    
    token = [client.uploadToken uploadWithUploadTokenId:token.id withFileData:[self.data objectForKey:@"path"]];

    
}

#pragma -
#pragma mark - View lifecycle

- (void)viewDidAppear:(BOOL)animated {

    [super viewDidAppear:YES];
    
    
    //NSLog([self.data description]);
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
