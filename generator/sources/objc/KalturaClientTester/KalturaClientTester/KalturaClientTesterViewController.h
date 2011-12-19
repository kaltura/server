#import <UIKit/UIKit.h>
#import "KalturaClientTester.h"

@interface KalturaClientTesterViewController : UIViewController <KalturaClientTesterDelegate>
{
    KalturaClientTester* _tester;
}

@property (nonatomic, retain) IBOutlet UITextView* consoleLabel;

- (IBAction)run;

@end
