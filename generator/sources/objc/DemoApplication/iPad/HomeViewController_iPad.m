//
//  HomeViewController_iPad.m
//  Kaltura
//
//  Created by Pavel on 01.03.12.
//  Copyright (c) 2012 Kaltura. All rights reserved.
//

#import "HomeViewController_iPad.h"
#import "MoviesViewController_iPad.h"
#import "SettingsViewController_iPad.h"

#import "AppDelegate_iPad.h"

@implementation HomeViewController_iPad

@synthesize popoverController;
@synthesize cancelAlert;
@synthesize uploadFilePath;

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        
        self.navigationItem.title = @"Menu";

    }
    return self;
}

- (BOOL)shouldAutorotate {
    return NO;
}

#pragma Upload

- (IBAction)buttonUploadSelectClose {
    
    [viewUploadSelect removeFromSuperview];
    
    [viewShadow removeFromSuperview];
        
}

- (IBAction)buttonUploadInfoClose {
    
    if (buttonUpload.hidden && [[Client instance] uploadingInProgress]) {
        
        self.cancelAlert = [[UIAlertView alloc] initWithTitle:@"Uploading is in progress!\nDo you want to cancel?" message:nil delegate:self cancelButtonTitle:@"No" otherButtonTitles:@"Yes", nil];
        [self.cancelAlert show];
        
    } else {
        
        [viewUploadInfo removeFromSuperview];
        
        [viewShadow removeFromSuperview];
        
    }
    
}

- (IBAction)buttonUploadSuccessClose {
    
    [viewUploadSuccess removeFromSuperview];
    
    [viewShadow removeFromSuperview];

}


- (void)alertView:(UIAlertView *)alertView didDismissWithButtonIndex:(NSInteger)buttonIndex {
    
    if (buttonIndex == 1) {
        
        [[Client instance] cancelUploading];
        
        [viewUploadInfo removeFromSuperview];
        
        [viewShadow removeFromSuperview];

    }
    
    [self.cancelAlert release];
    self.cancelAlert = nil;
}

- (void)updateProgress:(NSNumber *)value {
    
    progressUploadingProcess.progress = [value floatValue];
    
}

- (void)uploadFinished {

    if (self.cancelAlert) {
        
        [self.cancelAlert dismissWithClickedButtonIndex:0 animated:YES];
        
    }
    
    
    imageViewThumbSuccess.image = imageViewThumb.image;
    [viewUploadInfo removeFromSuperview];
    
    [self.view addSubview:viewUploadSuccess];
    
}

- (void)uploadFailed {

    UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Uploading error\nPlease try again" message:nil delegate:nil cancelButtonTitle:@"Close" otherButtonTitles:nil];
    [alert show];
    [alert release];
    
    buttonCategory.enabled = YES;
    textVTitle.enabled = YES;
    textDescription.editable = YES;
    textTags.enabled = YES;
    
    
    buttonUpload.hidden = NO;
    viewUploadingProcess.hidden = YES;
    
}

- (void)uploadProcess {
    
    NSDictionary *data = [NSDictionary dictionaryWithObjects:[NSArray arrayWithObjects:labelCategoryName.text, textVTitle.text, textDescription.text, textTags.text, self.uploadFilePath, nil] 
                                                     forKeys:[NSArray arrayWithObjects:@"category", @"title", @"description", @"tags", @"path", nil]];
    
    [[Client instance] uploadProcess:data withDelegate:self];
    
}

- (void)operateVideo:(NSURL *)url {
    
    self.uploadFilePath = [url path];
    
    AVURLAsset *asset = [[AVURLAsset alloc] initWithURL:url options:nil];
    AVAssetImageGenerator *gen = [[AVAssetImageGenerator alloc] initWithAsset:asset];
    gen.appliesPreferredTrackTransform = YES;
    CMTime time = CMTimeMakeWithSeconds(0.0, 600);
    NSError *error = nil;
    CMTime actualTime;
    
    CGImageRef image = [gen copyCGImageAtTime:time actualTime:&actualTime error:&error];
    imageViewThumb.image = [[UIImage alloc] initWithCGImage:image];
    CGImageRelease(image);
    [gen release];

    selectedItem = -1;
    
    NSArray *array = [[Client instance] getCategories];
    
    if ([array count] > 0) {
        
        KalturaCategory *category = [array objectAtIndex:0];
        
        labelCategoryName.text = category.fullName;
        
        [pickerCategories selectRow:0 inComponent:0 animated:NO];
    }
    
    buttonCategory.enabled = YES;
    textVTitle.enabled = YES;
    textVTitle.text = @"";
    textDescription.editable = YES;
    textDescription.text = @"";
    textTags.enabled = YES;
    textTags.text = @"";
    
    buttonUpload.hidden = NO;
    viewUploadingProcess.hidden = YES;
    progressUploadingProcess.progress = 0.0;
    
    [viewUploadSelect removeFromSuperview];
    [self.view addSubview:viewUploadInfo];
    

}  

- (IBAction)buttonUploadPressed {
    
    if ([textVTitle.text length] > 0 && [textDescription.text length] > 0) {
        
        buttonCategory.enabled = NO;
        textVTitle.enabled = NO;
        textDescription.editable = NO;
        textTags.enabled = NO;
        
        
        buttonUpload.hidden = YES;
        viewUploadingProcess.hidden = NO;
        
        [self performSelector:@selector(uploadProcess) withObject:nil afterDelay:0.1];
        
    } else {
        
        UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Please fill 'Title' and 'Description' fields" message:nil delegate:nil cancelButtonTitle:@"Close" otherButtonTitles:nil];
        [alert show];
        [alert release];
        
    }
    
}

- (IBAction)actionRecord:(UIButton *)button {
    
    type = 0;
    
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
    
    type = 1;
    
    if ([self.popoverController isPopoverVisible]) {
        [self.popoverController dismissPopoverAnimated:YES];
        [popoverController release];
    } else {
        
        
        UIImagePickerController *imagePicker = [[UIImagePickerController alloc] init];
        imagePicker.sourceType = UIImagePickerControllerSourceTypeSavedPhotosAlbum;
        imagePicker.mediaTypes = [[NSArray alloc] initWithObjects: (NSString *) kUTTypeMovie, nil]; 
        imagePicker.allowsEditing = NO;
        imagePicker.videoQuality = UIImagePickerControllerQualityTypeHigh;
        imagePicker.delegate = self;
        
        
        self.popoverController = [[UIPopoverController alloc]
                                  initWithContentViewController:imagePicker];
        
        [self.popoverController presentPopoverFromRect:CGRectMake(button.frame.size.width / 2, button.frame.size.height / 2, 1, 1) inView:button permittedArrowDirections:UIPopoverArrowDirectionDown animated:YES];
        
        [imagePicker release];
        
    }
    
}

- (void)imagePickerControllerDidCancel:(UIImagePickerController *)picker {
    
    if (type == 0) {
        [self dismissModalViewControllerAnimated:YES];
    } else {
        [self.popoverController dismissPopoverAnimated:YES];
    }
    
}

- (void)imagePickerController:(UIImagePickerController *)picker didFinishPickingMediaWithInfo:(NSDictionary *)info
{
    
    if (type == 0) {
        [self dismissModalViewControllerAnimated:YES];
    } else {
        [self.popoverController dismissPopoverAnimated:YES];
        [self.popoverController release];
    }
    
    
    NSURL *url =  [info objectForKey:UIImagePickerControllerMediaURL];
    
    
    [self performSelector:@selector(operateVideo:) withObject:url afterDelay:0.5];
    
}

- (IBAction)categoriesPressed {
    
    if (selectedItem != 0) {
        
        if (selectedItem == 1) {
            [textVTitle resignFirstResponder];
        } else if (selectedItem == 2) {
            [textDescription resignFirstResponder];
        } else if (selectedItem == 3) {
            [textTags resignFirstResponder];
        }
        
        [UIView beginAnimations:nil context:nil];
        
        pickerCategories.alpha = 1.0;
        
        toolbar.alpha = 1.0;
        
        [UIView commitAnimations];
        
        selectedItem = 0;
        
        
    }
    
}

- (IBAction)buttonDonePressed {
    
    if (selectedItem == 0) {
        
        [UIView beginAnimations:nil context:nil];
                
        pickerCategories.alpha = 0;
        toolbar.alpha = 0;
        
        [UIView commitAnimations];
        
        
    } if (selectedItem == 1) {
        [textVTitle resignFirstResponder];
        
    } else if (selectedItem == 2) {
        [textDescription resignFirstResponder];
        
    } else if (selectedItem == 3) {
        [textTags resignFirstResponder];
        
        
    }
    
    selectedItem = -1;
}

- (void)textFieldDidBeginEditing:(UITextField *)textField {
    
    if (selectedItem == 0) {
        
        [UIView beginAnimations:nil context:nil];
        
        pickerCategories.alpha = 0;
        toolbar.alpha = 0.0;
        
        [UIView commitAnimations];
        
        
    }
        
    selectedItem = textField.tag;
    
    
}

- (void)textViewDidBeginEditing:(UITextView *)textView {
    
    if (selectedItem == 0) {
        
        [UIView beginAnimations:nil context:nil];
        
        
        pickerCategories.alpha = 0;
        toolbar.alpha = 0.0;    
        
        [UIView commitAnimations];
        
        
    }
    
    selectedItem = textView.tag;
}

#pragma PickerViewDelegate

- (NSInteger)numberOfComponentsInPickerView:(UIPickerView *)pickerView {
    
    return 1;
    
}

- (NSInteger)pickerView:(UIPickerView *)pickerView numberOfRowsInComponent:(NSInteger)component {
    
    NSArray *array = [[Client instance] getCategories];
    
    return [array count];
    
}

- (NSString *)pickerView:(UIPickerView *)pickerView titleForRow:(NSInteger)row forComponent:(NSInteger)component {
    
    NSArray *array = [[Client instance] getCategories];
    
    KalturaCategory *category = [array objectAtIndex:row];
    
    return category.fullName;
}

- (void)pickerView:(UIPickerView *)pickerView didSelectRow:(NSInteger)row inComponent:(NSInteger)component {
    
    NSArray *array = [[Client instance] getCategories];
    
    KalturaCategory *category = [array objectAtIndex:row];
    
    labelCategoryName.text = category.fullName;
}

#pragma Main Menu

- (void)menuProcess {
    
    [[Client instance] getCategories];
    
    [activity stopAnimating];
    
    if (selectedMenu == 0) {
        
        MoviesViewController_iPad *controller = [[MoviesViewController_iPad alloc] initWithNibName:@"MoviesViewController_iPad" bundle:nil];
        controller.mostPopular = YES;
        [app.navigation pushViewController:controller animated:YES];
        [controller release];
        
    } else if (selectedMenu == 1) {
        
        MoviesViewController_iPad *controller = [[MoviesViewController_iPad alloc] initWithNibName:@"MoviesViewController_iPad" bundle:nil];
        [app.navigation pushViewController:controller animated:YES];
        [controller release];
        
    } else if (selectedMenu == 2) {
        
        [self.view addSubview:viewShadow];
        
        [self.view addSubview:viewUploadSelect];
        
    }
}

- (IBAction)menuButtonPressed:(UIButton *)button {
  
    selectedMenu = button.tag;
    
    if (button.tag < 3) {
        
        [activity startAnimating];
        [self performSelector:@selector(menuProcess) withObject:nil afterDelay:0.1];
        
    } else if (button.tag == 3) {
        
        SettingsViewController_iPad *controller = [[SettingsViewController_iPad alloc] initWithNibName:@"SettingsViewController_iPad" bundle:nil];
        [app.navigation pushViewController:controller animated:YES];
        [controller release];
        
    }
        
}

#pragma -

- (void)didReceiveMemoryWarning
{
    // Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
    
    // Release any cached data, images, etc that aren't in use.
}

#pragma mark - View lifecycle

- (void)updateInterfaceOrientation:(BOOL)_isLandscape {

    isLandscape = _isLandscape;
    
    int width = isLandscape ? 1024 : 768;
    int height = isLandscape ? 768 : 1024;
    
    viewShadow.frame = CGRectMake(0, 0, width, height);
    
    
    if (isLandscape) {
        
        menuButton0.center = CGPointMake(width / 2 - 330, 450);
        menuButton1.center = CGPointMake(width / 2 - 110, 450);
        menuButton2.center = CGPointMake(width / 2 + 110, 450);
        menuButton3.center = CGPointMake(width / 2 + 330, 450);
        
        viewUploadSelect.center = CGPointMake(width / 2, height / 2 - 100);

        
        viewUploadInfo.frame = CGRectMake(0, 0, 700, 420);

        viewUploadInfo.center = CGPointMake(width / 2, height / 2 - 100);
        
        
        imageViewThumb.frame = CGRectMake(50, 100, 300, 200);
        viewUploadInfoEdit.frame = CGRectMake(350, 100, 300, 200);

        labelUploadingProcess.frame = CGRectMake(50, 10, 170, 30);
        labelUploadingProcess.textAlignment = UITextAlignmentLeft;
        progressUploadingProcess.frame = CGRectMake(220, 20, 420, progressUploadingProcess.frame.size.height);
        
        viewUploadSuccess.frame = CGRectMake(0, 0, 700, 420);
        
        viewUploadSuccess.center = CGPointMake(width / 2, height / 2 - 100);
        
        labelUploadSuccess1.frame = CGRectMake(300, 320, 170, 40);
        labelUploadSuccess1.textAlignment = UITextAlignmentLeft;
        labelUploadSuccess2.frame = CGRectMake(160, 360, 380, 30);
        
        imageViewUploadSuccess.frame = CGRectMake(230, 310, 60, 50);
        
    } else {
        
        menuButton0.center = CGPointMake(224, 490);
        menuButton1.center = CGPointMake(544, 490);
        menuButton2.center = CGPointMake(224, 770);
        menuButton3.center = CGPointMake(544, 770);
        
        viewUploadSelect.center = CGPointMake(width / 2, height / 2);
        
        viewUploadInfo.frame = CGRectMake(0, 0, 440, 680);
        
        viewUploadInfo.center = CGPointMake(width / 2, height / 2);
        
        imageViewThumb.frame = CGRectMake(70, 100, 300, 200);
        viewUploadInfoEdit.frame = CGRectMake(70, 340, 300, 200);

        labelUploadingProcess.frame = CGRectMake(70, 10, 300, 30);
        labelUploadingProcess.textAlignment = UITextAlignmentCenter;
        progressUploadingProcess.frame = CGRectMake(70, 50, 300, progressUploadingProcess.frame.size.height);
        
        viewUploadSuccess.frame = CGRectMake(0, 0, 440, 540);
        
        viewUploadSuccess.center = CGPointMake(width / 2, height / 2);
        
        labelUploadSuccess1.frame = CGRectMake(100, 340, 240, 40);
        labelUploadSuccess1.textAlignment = UITextAlignmentCenter;
        labelUploadSuccess2.frame = CGRectMake(100, 380, 240, 50);
        
        imageViewUploadSuccess.frame = CGRectMake(190, 450, 60, 50);
        
    }
    
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
    
    app = (AppDelegate_iPad *)[[UIApplication sharedApplication] delegate];
    
    menuButton0.titleLabel.lineBreakMode = UILineBreakModeWordWrap;
	menuButton0.titleLabel.numberOfLines = 0;
	menuButton0.titleLabel.textAlignment = UITextAlignmentCenter;
	[menuButton0.titleLabel setFont:[UIFont fontWithName:@"Maven Pro" size:24]];
	
    menuButton1.titleLabel.lineBreakMode = UILineBreakModeWordWrap;
	menuButton1.titleLabel.numberOfLines = 0;
	menuButton1.titleLabel.textAlignment = UITextAlignmentCenter;
	[menuButton1.titleLabel setFont:[UIFont fontWithName:@"Maven Pro" size:24]];
    
    menuButton2.titleLabel.lineBreakMode = UILineBreakModeWordWrap;
	menuButton2.titleLabel.numberOfLines = 0;
	menuButton2.titleLabel.textAlignment = UITextAlignmentCenter;
	[menuButton2.titleLabel setFont:[UIFont fontWithName:@"Maven Pro" size:24]];
	
    menuButton3.titleLabel.lineBreakMode = UILineBreakModeWordWrap;
	menuButton3.titleLabel.numberOfLines = 0;
	menuButton3.titleLabel.textAlignment = UITextAlignmentCenter;
	[menuButton3.titleLabel setFont:[UIFont fontWithName:@"Maven Pro" size:24]];
    
    // Upload Select
    
    labelUploadSelectTitle.font = [UIFont fontWithName:@"Maven Pro" size:38];
    viewUploadSelect.backgroundColor = [UIColor colorWithPatternImage:[UIImage imageNamed:@"bg_table.png"]];
    
    buttonUploadSelectRecord.titleLabel.lineBreakMode = UILineBreakModeWordWrap;
	buttonUploadSelectRecord.titleLabel.numberOfLines = 0;
    buttonUploadSelectRecord.titleLabel.textAlignment = UITextAlignmentCenter;
    [buttonUploadSelectRecord setTitle:@"Record \na Video" forState:UIControlStateNormal];
    
    buttonUploadSelectPick.titleLabel.lineBreakMode = UILineBreakModeWordWrap;
	buttonUploadSelectPick.titleLabel.numberOfLines = 0;
    buttonUploadSelectPick.titleLabel.textAlignment = UITextAlignmentCenter;
    [buttonUploadSelectPick setTitle:@"Pick from\nGallery" forState:UIControlStateNormal];
    
    [buttonUploadSelectRecord.titleLabel setFont:[UIFont fontWithName:@"Maven Pro" size:30]];
    [buttonUploadSelectPick.titleLabel setFont:[UIFont fontWithName:@"Maven Pro" size:30]];
    
    
    // Upload Info and Progress
    
    labelUploadInfoTitle.font = [UIFont fontWithName:@"Maven Pro" size:38];
    viewUploadInfo.backgroundColor = [UIColor colorWithPatternImage:[UIImage imageNamed:@"bg_table.png"]];
    
    labelCategory.font = [UIFont fontWithName:@"Maven Pro" size:17];
    labelCategoryName.font = [UIFont fontWithName:@"Maven Pro" size:16];
    
    labelVTitle.font = [UIFont fontWithName:@"Maven Pro" size:17];
    textVTitle.font = [UIFont fontWithName:@"Maven Pro" size:16];
    
    labelDescription.font = [UIFont fontWithName:@"Maven Pro" size:17];
    textDescription.font = [UIFont fontWithName:@"Maven Pro" size:16];
    
    labelTags.font = [UIFont fontWithName:@"Maven Pro" size:17];
    textTags.font = [UIFont fontWithName:@"Maven Pro" size:16];
    
    [buttonUpload.titleLabel setFont:[UIFont fontWithName:@"Maven Pro" size:22]];
    
    labelUploadingProcess.font = [UIFont fontWithName:@"Maven Pro" size:22];
    
    // Upload Success
    
    
    viewUploadSuccess.backgroundColor = [UIColor colorWithPatternImage:[UIImage imageNamed:@"bg_table.png"]];
    
    labelUploadSuccessTitle.font = [UIFont fontWithName:@"Maven Pro" size:38];
    
    labelUploadSuccess1.font = [UIFont fontWithName:@"Maven Pro" size:32];
    labelUploadSuccess2.font = [UIFont fontWithName:@"Maven Pro" size:20];
    
}



- (void)viewWillAppear:(BOOL)animated {
	[super viewWillAppear:animated];
    
	[self updateInterfaceOrientation:UIInterfaceOrientationIsLandscape([[UIApplication sharedApplication] statusBarOrientation])];
    
}

- (void)viewDidAppear:(BOOL)animated {
    
    [super viewDidAppear:YES];
    
    selectedMenu = -1;
    [[Client instance].media removeAllObjects];
    
    NSString *userEmail = [[NSUserDefaults standardUserDefaults] objectForKey:@"userEmail"];
    
    if (!userEmail || [userEmail length] == 0) {

        [self menuButtonPressed:menuButton3];
        
    }
    
}

- (void)willRotateToInterfaceOrientation:(UIInterfaceOrientation)toInterfaceOrientation duration:(NSTimeInterval)duration {
	
	[self updateInterfaceOrientation:UIInterfaceOrientationIsLandscape(toInterfaceOrientation)];
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
