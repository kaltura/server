//
//  UploadInfoViewController_iPhone.m
//  Kaltura
//
//  Created by Pavel on 05.03.12.
//  Copyright (c) 2012 Kaltura. All rights reserved.
//

#import "UploadInfoViewController_iPhone.h"
#import "AppDelegate_iPhone.h"
#import "UploadProcessViewController_iPhone.h"

@implementation UploadInfoViewController_iPhone

@synthesize path;

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

- (IBAction)uploadButtonPressed:(UIButton *)button {
    
    if ([textVTitle.text length] > 0 && [textDescription.text length] > 0) {

        UploadProcessViewController_iPhone *controller = [[UploadProcessViewController_iPhone alloc] initWithNibName:@"UploadProcessViewController_iPhone" bundle:nil];
        
        controller.data = [NSDictionary dictionaryWithObjects:[NSArray arrayWithObjects:labelCategoryName.text, textVTitle.text, textDescription.text, textTags.text, path, nil] 
                                                      forKeys:[NSArray arrayWithObjects:@"category", @"title", @"description", @"tags", @"path", nil]];
        
        [app.navigation pushViewController:controller animated:YES];
        [controller release];

        
    } else {
        
        UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Please fill 'Title' and 'Description' fields" message:nil delegate:nil cancelButtonTitle:@"Close" otherButtonTitles:nil];
        [alert show];
        [alert release];
        
    }
    
}

#pragma -

- (void)updateInterfaceOrientation:(BOOL)_isLandscape {
    
    if (selectedItem > 1) {
        
        [self buttonDonePressed];
        
    }
    
    isLandscape = _isLandscape;
    
    if (!isLandscape) {
        
        labelCategory.textAlignment = UITextAlignmentLeft;
        labelCategory.frame = CGRectMake(20, 10, labelCategory.frame.size.width, labelCategory.frame.size.height);
        viewCategory.frame = CGRectMake(15, 35, viewCategory.frame.size.width, viewCategory.frame.size.height);
        
        labelVTitle.textAlignment = UITextAlignmentLeft;
        labelVTitle.frame = CGRectMake(20, 75, labelVTitle.frame.size.width, labelVTitle.frame.size.height);
        viewVTitle.frame = CGRectMake(15, 100, viewVTitle.frame.size.width, viewVTitle.frame.size.height);
        
        labelDescription.textAlignment = UITextAlignmentLeft;
        labelDescription.frame = CGRectMake(20, 140, labelDescription.frame.size.width, labelDescription.frame.size.height);
        viewDescription.frame = CGRectMake(15, 165, viewDescription.frame.size.width, viewDescription.frame.size.height);
        
        labelTags.textAlignment = UITextAlignmentLeft;
        labelTags.frame = CGRectMake(20, 260, labelTags.frame.size.width, labelTags.frame.size.height);
        viewTags.frame = CGRectMake(15, 285, viewTags.frame.size.width, viewTags.frame.size.height);
        
        buttonUpload.frame = CGRectMake(15, 335, buttonUpload.frame.size.width, 60);
        
        scrollMain.contentSize = CGSizeMake(320, 416);
        
    } else {
        
        labelCategory.textAlignment = UITextAlignmentRight;
        labelCategory.frame = CGRectMake(20, 10, labelCategory.frame.size.width, labelCategory.frame.size.height);
        viewCategory.frame = CGRectMake(140, 5, viewCategory.frame.size.width, viewCategory.frame.size.height);
        
        labelVTitle.textAlignment = UITextAlignmentRight;
        labelVTitle.frame = CGRectMake(20, 45, labelVTitle.frame.size.width, labelVTitle.frame.size.height);
        viewVTitle.frame = CGRectMake(140, 40, viewVTitle.frame.size.width, viewVTitle.frame.size.height);
        
        labelDescription.textAlignment = UITextAlignmentRight;
        labelDescription.frame = CGRectMake(20, 80, labelDescription.frame.size.width, labelDescription.frame.size.height);
        viewDescription.frame = CGRectMake(140, 75, viewDescription.frame.size.width, viewDescription.frame.size.height);
        
        labelTags.textAlignment = UITextAlignmentRight;
        labelTags.frame = CGRectMake(20, 170, labelTags.frame.size.width, labelTags.frame.size.height);
        viewTags.frame = CGRectMake(140, 165, viewTags.frame.size.width, viewTags.frame.size.height);
        
        buttonUpload.frame = CGRectMake(95, 200, buttonUpload.frame.size.width, 54);

        scrollMain.contentSize = CGSizeMake(480, 256);
        
    }
    
    
}

- (void)viewWillAppear:(BOOL)animated {
	[super viewWillAppear:animated];
    
	[self updateInterfaceOrientation:UIInterfaceOrientationIsLandscape([[UIApplication sharedApplication] statusBarOrientation])];
    
}


- (void)willRotateToInterfaceOrientation:(UIInterfaceOrientation)toInterfaceOrientation duration:(NSTimeInterval)duration {
	
	[self updateInterfaceOrientation:UIInterfaceOrientationIsLandscape(toInterfaceOrientation)];
}

#pragma -

- (IBAction)buttonDonePressed {
    
    if (selectedItem == 0) {
        
        [UIView beginAnimations:nil context:nil];
        
         
        if (isLandscape) {
            
            pickerCategories.frame = CGRectMake(0, 300, 480, pickerCategories.frame.size.height);
            
        } else {
            
            pickerCategories.frame = CGRectMake(0, 460, 320, pickerCategories.frame.size.height);
            
        }
        
        toolbar.frame = CGRectMake(0, -toolbar.frame.size.height, toolbar.frame.size.width, toolbar.frame.size.height);
        
        [UIView commitAnimations];
        
        
    } if (selectedItem == 1) {
        [textVTitle resignFirstResponder];
        
        [UIView beginAnimations:nil context:nil];
        
        toolbar.frame = CGRectMake(0, -toolbar.frame.size.height, toolbar.frame.size.width, toolbar.frame.size.height);
        
        [UIView commitAnimations];
        
    } else if (selectedItem == 2) {
        [textDescription resignFirstResponder];
        
        [UIView beginAnimations:nil context:nil];
        
        toolbar.frame = CGRectMake(0, -toolbar.frame.size.height, toolbar.frame.size.width, toolbar.frame.size.height);
        scrollMain.contentOffset = CGPointMake(0, 0);
        
        [UIView commitAnimations];
        
    } else if (selectedItem == 3) {
        [textTags resignFirstResponder];
        
        [UIView beginAnimations:nil context:nil];
        
        toolbar.frame = CGRectMake(0, -toolbar.frame.size.height, toolbar.frame.size.width, toolbar.frame.size.height);
        scrollMain.contentOffset = CGPointMake(0, 0);
        
        [UIView commitAnimations];
        
    }
    
    if (!isLandscape) {
        
        scrollMain.contentSize = CGSizeMake(320, 416);
        
    } else {
        
        scrollMain.contentSize = CGSizeMake(480, 256);
        
    }
    selectedItem = -1;
}

- (void)textFieldDidBeginEditing:(UITextField *)textField {
    
    if (selectedItem == 0) {
        
        [UIView beginAnimations:nil context:nil];
        
        
        if (isLandscape) {
            
            pickerCategories.frame = CGRectMake(0, 300, 480, pickerCategories.frame.size.height);
            
        } else {
            
            pickerCategories.frame = CGRectMake(0, 460, 320, pickerCategories.frame.size.height);
            
        }
        
        [UIView commitAnimations];
        
        
    } else if (selectedItem == -1) {
        
        [UIView beginAnimations:nil context:nil];
        
        toolbar.frame = CGRectMake(0, 0, toolbar.frame.size.width, toolbar.frame.size.height);
        
        [UIView commitAnimations];
        
    }
    
    if (!isLandscape) {
        
        scrollMain.contentSize = CGSizeMake(320, 416 + 160);
        
    } else {
        
        scrollMain.contentSize = CGSizeMake(480, 256 + 120);
        
    }
    
    selectedItem = textField.tag;
    
    if (selectedItem == 3) {
        
        [UIView beginAnimations:nil context:nil];
        
        //viewMain.frame = CGRectMake(0, -90, viewMain.frame.size.width, viewMain.frame.size.height);
        scrollMain.contentOffset = CGPointMake(0, 120);
        
        [UIView commitAnimations];
    }

}

- (void)textViewDidBeginEditing:(UITextView *)textView {
    
    if (selectedItem == 0) {
        
        [UIView beginAnimations:nil context:nil];
        
        
        if (isLandscape) {
            
            pickerCategories.frame = CGRectMake(0, 300, 480, pickerCategories.frame.size.height);
            
        } else {
            
            pickerCategories.frame = CGRectMake(0, 460, 320, pickerCategories.frame.size.height);
            
        }
        
        [UIView commitAnimations];
        
        
    } else if (selectedItem == -1) {
        
        [UIView beginAnimations:nil context:nil];
        
        toolbar.frame = CGRectMake(0, 0, toolbar.frame.size.width, toolbar.frame.size.height);
        
        [UIView commitAnimations];
        
    }
    
    if (!isLandscape) {
        
        scrollMain.contentSize = CGSizeMake(320, 416 + 160);
        
    } else {
        
        scrollMain.contentSize = CGSizeMake(480, 256 + 120);
        
    }
    
    [UIView beginAnimations:nil context:nil];
    
    //viewMain.frame = CGRectMake(0, -30, viewMain.frame.size.width, viewMain.frame.size.height);
    scrollMain.contentOffset = CGPointMake(0, 30);
    
    [UIView commitAnimations];
    
    selectedItem = textView.tag;
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
        
        if (isLandscape) {
            
            pickerCategories.frame = CGRectMake(0, 300, 480, pickerCategories.frame.size.height);
            
        } else {
            
            pickerCategories.frame = CGRectMake(0, 400, 320, pickerCategories.frame.size.height);
            
        }
        
        pickerCategories.alpha = 1.0;
    
        [UIView beginAnimations:nil context:nil];
        
        //pickerCategories.frame = CGRectMake(pickerCategories.frame.origin.x, pickerCategories.frame.origin.y - pickerCategories.frame.size.height, pickerCategories.frame.size.width, pickerCategories.frame.size.height);
        
        if (isLandscape) {
            
            pickerCategories.frame = CGRectMake(0, 300 - pickerCategories.frame.size.height, 480, pickerCategories.frame.size.height);
            
        } else {
            
            pickerCategories.frame = CGRectMake(0, 460 - pickerCategories.frame.size.height, 320, pickerCategories.frame.size.height);
            
        }
        
        toolbar.frame = CGRectMake(0, 0, toolbar.frame.size.width, toolbar.frame.size.height);
        
        [UIView commitAnimations];
        
        selectedItem = 0;
        
        
    }
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

#pragma mark - View lifecycle

- (void)viewDidLoad
{
    [super viewDidLoad];
  
    labelTitle.font = [UIFont fontWithName:@"Maven Pro" size:19];
    viewMain.backgroundColor = [UIColor colorWithPatternImage:[UIImage imageNamed:@"bg_table.png"]];
    
    
    labelCategory.font = [UIFont fontWithName:@"Maven Pro" size:17];
    labelCategoryName.font = [UIFont fontWithName:@"Maven Pro" size:16];
    
    labelVTitle.font = [UIFont fontWithName:@"Maven Pro" size:17];
    textVTitle.font = [UIFont fontWithName:@"Maven Pro" size:16];
    
    labelDescription.font = [UIFont fontWithName:@"Maven Pro" size:17];
    textDescription.font = [UIFont fontWithName:@"Maven Pro" size:16];
    
    labelTags.font = [UIFont fontWithName:@"Maven Pro" size:17];
    textTags.font = [UIFont fontWithName:@"Maven Pro" size:16];
    
    [buttonUpload.titleLabel setFont:[UIFont fontWithName:@"Maven Pro" size:18]];

    selectedItem = -1;
    
    NSArray *array = [[Client instance] getCategories];
    
    if ([array count] > 0) {
        
        KalturaCategory *category = [array objectAtIndex:0];
        
        labelCategoryName.text = category.fullName;
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
