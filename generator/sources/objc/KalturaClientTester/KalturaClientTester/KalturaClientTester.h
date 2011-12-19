#import <Foundation/Foundation.h>
#import "KalturaClient.h"


@class KalturaTestDetails;

/*
 KalturaCallbackDelegate
 */
@interface KalturaCallbackDelegate : NSObject <KalturaClientDelegate>

@property (nonatomic, assign) id target;
@property (nonatomic, assign) SEL failedSel;
@property (nonatomic, assign) SEL finishedSel;

@end

/*
 KalturaClientTesterDelegate
 */
@protocol KalturaClientTesterDelegate <NSObject>

- (void)updateProgressWithMessage:(NSString*)aMessage;

@end

/*
 KalturaClientTester
 */
@interface KalturaClientTester : NSObject
{
    KalturaCallbackDelegate* _clientDelegate;
    KalturaClient* _client;
    NSMutableArray* _tests;
    int _curTestIndex;
    KalturaTestDetails* _curTestDetails;
    
    KalturaBaseEntry* _imageEntry;
    KalturaBaseEntry* _videoEntry;
}

@property (nonatomic, assign) id <KalturaClientTesterDelegate> delegate;

- (id)initWithDelegate:(id <KalturaClientTesterDelegate>)aDelegate;
- (void)run;

@end
