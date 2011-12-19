#import "KalturaClientTesterViewController.h"
#import "KalturaClientTester.h"

@implementation KalturaClientTesterViewController

@synthesize consoleLabel = _consoleLabel;

- (void)dealloc
{
    [self->_tester release];
    [self->_consoleLabel release];
    [super dealloc];
}

- (void)didReceiveMemoryWarning
{
    // Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
    
    // Release any cached data, images, etc that aren't in use.
}

#pragma mark - View lifecycle


// Implement viewDidLoad to do additional setup after loading the view, typically from a nib.
- (void)viewDidLoad
{
    [super viewDidLoad];
    self->_tester = [[KalturaClientTester alloc] initWithDelegate:self];
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
    return (interfaceOrientation == UIInterfaceOrientationPortrait);
}

- (IBAction)run
{    
    [self->_tester run];
}

- (void)updateProgressWithMessage:(NSString*)aMessage
{
    [self->_consoleLabel setText:aMessage];
}

@end
