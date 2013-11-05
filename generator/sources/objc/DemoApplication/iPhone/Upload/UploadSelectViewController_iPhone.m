//
//  UploadSelectViewController_iPhone.m
//  Kaltura
//
//  Created by Pavel on 06.03.12.
//  Copyright (c) 2012 Kaltura. All rights reserved.
//

#import "UploadSelectViewController_iPhone.h"
#import "AppDelegate_iPhone.h"
#import "UploadInfoViewController_iPhone.h"

@implementation UploadSelectViewController_iPhone

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

- (IBAction)actionRecord:(UIButton *)button {
    
    if ([UIImagePickerController isSourceTypeAvailable:UIImagePickerControllerSourceTypeCamera])
	{
        UIImagePickerController *videoRecorder = [[UIImagePickerController alloc] init];
        videoRecorder.sourceType = UIImagePickerControllerSourceTypeCamera;
        videoRecorder.delegate = self;
        
        NSArray *mediaTypes = [UIImagePickerController availableMediaTypesForSourceType:UIImagePickerControllerSourceTypeCamera];
        NSArray *videoMediaTypesOnly = [mediaTypes filteredArrayUsingPredicate:[NSPredicate predicateWithFormat:@"(SELF contains %@)", @"movie"]];
        
        if ([videoMediaTypesOnly count] == 0)		//Is movie output possible?
        {
            UIActionSheet *actionSheet = [[UIActionSheet alloc] initWithTitle:@"Sorry but your device does not support video recording"
                                                                     delegate:nil
                                                            cancelButtonTitle:@"OK"
                                                       destructiveButtonTitle:nil
                                                            otherButtonTitles:nil];
            [actionSheet showInView:[[self view] window]];
            [actionSheet autorelease];
        }
        else
        {
            //Select front facing camera if possible
            if ([UIImagePickerController isCameraDeviceAvailable:UIImagePickerControllerCameraDeviceFront])
                videoRecorder.cameraDevice = UIImagePickerControllerCameraDeviceFront;
            
            videoRecorder.mediaTypes = videoMediaTypesOnly;
            videoRecorder.videoQuality = UIImagePickerControllerQualityTypeMedium;
            videoRecorder.videoMaximumDuration = 180;			//Specify in seconds (600 is default)
            
            [self presentModalViewController:videoRecorder animated:YES];
        }
        [videoRecorder release];
    } else {
        
    }
    
}

- (IBAction)actionPick:(UIButton *)button {
    
    UIImagePickerController *imagePicker = [[UIImagePickerController alloc] init];
    imagePicker.sourceType = UIImagePickerControllerSourceTypeSavedPhotosAlbum;
    imagePicker.mediaTypes = [[NSArray alloc] initWithObjects: (NSString *) kUTTypeMovie, nil]; 
    imagePicker.allowsEditing = NO;
    imagePicker.videoQuality = UIImagePickerControllerQualityTypeHigh;
    imagePicker.delegate = self;
    
    [self presentModalViewController:imagePicker animated:YES];
      
    [imagePicker release];
}

- (void)imagePickerController:(UIImagePickerController *)picker didFinishPickingMediaWithInfo:(NSDictionary *)info
{
    [self dismissModalViewControllerAnimated:YES];
    
    NSURL *url =  [info objectForKey:UIImagePickerControllerMediaURL];
    
    [self performSelector:@selector(operateVideo:) withObject:url afterDelay:0.5];
}

- (void)operateVideo:(NSURL *)url {
    
    NSString* uploadFilePath = [url path];
    
    UploadInfoViewController_iPhone *controller = [[UploadInfoViewController_iPhone alloc] initWithNibName:@"UploadInfoViewController_iPhone" bundle:nil];
    controller.path = uploadFilePath;
    [app.navigation pushViewController:controller animated:YES];
    [controller release];
}  

- (void)imagePickerControllerDidCancel:(UIImagePickerController *)picker {
    
    [self dismissModalViewControllerAnimated:YES];
    
}

#pragma mark - View lifecycle

- (void)viewDidLoad
{
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
    
    labelTitle.font = [UIFont fontWithName:@"Maven Pro" size:19];
    viewMain.backgroundColor = [UIColor colorWithPatternImage:[UIImage imageNamed:@"bg_table.png"]];
    
    buttonRecord.titleLabel.lineBreakMode = UILineBreakModeWordWrap;
	buttonRecord.titleLabel.numberOfLines = 0;
    
    buttonPick.titleLabel.lineBreakMode = UILineBreakModeWordWrap;
	buttonPick.titleLabel.numberOfLines = 0;
    
    [buttonRecord.titleLabel setFont:[UIFont fontWithName:@"Maven Pro" size:20]];
    [buttonPick.titleLabel setFont:[UIFont fontWithName:@"Maven Pro" size:20]];
    
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
