// ===================================================================================================
//                           _  __     _ _
//                          | |/ /__ _| | |_ _  _ _ _ __ _
//                          | ' </ _` | |  _| || | '_/ _` |
//                          |_|\_\__,_|_|\__|\_,_|_| \__,_|
//
// This file is part of the Kaltura Collaborative Media Suite which allows users
// to do with audio, video, and animation what Wiki platfroms allow them to do with
// text.
//
// Copyright (C) 2006-2011  Kaltura Inc.
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Affero General Public License for more details.
//
// You should have received a copy of the GNU Affero General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
// @ignore
// ===================================================================================================
#import <objc/runtime.h>
#import "KalturaMetadataClientPlugin.h"
#import "KalturaClientTester.h"
#import "KalturaClient.h"
#import "ASIHTTPRequest.h"

// Account specific constants
// TODO: update this
#define ADMIN_SECRET (@"YOUR_ADMIN_SECRET")
#define PARTNER_ID (54321)
#define USER_ID (@"testUser")

// Fixed constants
#define UPLOAD_FILENAME (@"DemoVideo.flv")
#define ENTRY_NAME (@"Media entry uploaded from ObjC client")
#define DEFAULT_SERVICE_URL (@"http://www.kaltura.com")
#define KALTURA_CLIENT_TEST_URL (@"http://www.kaltura.com/clientTest")

/*
 KalturaTestDetails
 */
@interface KalturaTestDetails : NSObject

@property (nonatomic, retain) NSString* name;
@property (nonatomic, assign) SEL sel;
@property (nonatomic, assign) BOOL isSync;

@end

@implementation KalturaTestDetails

@synthesize name = _name;
@synthesize sel = _sel;
@synthesize isSync = _isSync;

- (void) dealloc
{
    [self->_name release];
    [super dealloc];
}

@end

/*
 KalturaCallbackDelegate
 */
@implementation KalturaCallbackDelegate

@synthesize target = _target;
@synthesize failedSel = _failedSel;
@synthesize finishedSel = _finishedSel;

- (void)requestFailed:(KalturaClientBase *)aClient
{
    [self.target performSelector:self.failedSel withObject:aClient];
}

- (void)requestFinished:(KalturaClientBase *)aClient withResult:(id)result
{
    [self.target performSelector:self.finishedSel withObject:aClient withObject:result];
}

@end

/*
 KalturaDownloadDelegate
 */
@interface KalturaProgressDelegate : NSObject <ASIProgressDelegate> 

@property (nonatomic, assign) BOOL receiveBytesCalled;
@property (nonatomic, assign) BOOL sendBytesCalled;

@end

@implementation KalturaProgressDelegate

@synthesize receiveBytesCalled = _receiveBytesCalled;
@synthesize sendBytesCalled = _sendBytesCalled;

- (void)request:(ASIHTTPRequest *)request didReceiveBytes:(long long)bytes
{
    self.receiveBytesCalled = TRUE;
}

- (void)request:(ASIHTTPRequest *)request didSendBytes:(long long)bytes
{
    self.sendBytesCalled = TRUE;
}

@end


/*
 KalturaClientTester
 */
@interface KalturaClientTester()

- (void)setUp;
- (void)tearDown;

@end

@implementation KalturaClientTester

@synthesize delegate = _delegate;

- (void)initTestsArray
{
    unsigned int methodCount = 0;
    
    self->_tests = [[NSMutableArray alloc] init];
    
    Method* methodList = class_copyMethodList(object_getClass(self), &methodCount);
    for (int i = 0; i < methodCount; i++)
    {
        SEL curSel = method_getName(methodList[i]);
        const char* curName = sel_getName(curSel);
        
        if (0 != strncmp(curName, "test", 4))
            continue;
        
        KalturaTestDetails* details = [[KalturaTestDetails alloc] init];
        details.name = [NSString stringWithUTF8String:curName];
        details.sel = curSel;
        details.isSync = (0 != strncmp(curName, "testAsync", 9));
        [self->_tests addObject:details];
        [details release];
    }
    
    free(methodList);
}

- (id)initWithDelegate:(id <KalturaClientTesterDelegate>)aDelegate;
{
    self = [super init];
    if (self == nil)
        return nil;
    
    self->_delegate = aDelegate;
    
    KalturaClientConfiguration* config = [[KalturaClientConfiguration alloc] init];
    KalturaNSLogger* logger = [[KalturaNSLogger alloc] init];
    config.logger = logger;
    config.serviceUrl = DEFAULT_SERVICE_URL;
    [logger release];           // retained on config
    config.partnerId = PARTNER_ID;
    self->_client = [[KalturaClient alloc] initWithConfig:config];
    [config release];           // retained on the client
    
    self->_client.ks = [KalturaClient generateSessionWithSecret:ADMIN_SECRET withUserId:USER_ID withType:[KalturaSessionType ADMIN] withPartnerId:PARTNER_ID withExpiry:86400 withPrivileges:@""];
    
    self->_clientDelegate = [[KalturaCallbackDelegate alloc] init];
    self->_clientDelegate.target = self;

    [self initTestsArray];

    return self;
}

- (void)dealloc
{
    [self->_tests release];
    [self->_clientDelegate release];
    [self->_client release];
    [super dealloc];
}

- (void)startNextTest
{
    if (self->_curTestIndex >= self->_tests.count)
    {
        self->_curTestIndex = 0;
        self->_client.config.serviceUrl = DEFAULT_SERVICE_URL;   
        self->_client.delegate = nil;
        [self tearDown];
        
        NSString* message = [NSString stringWithFormat:@"Done - %d tests !", self->_tests.count];
        [self->_delegate updateProgressWithMessage:message];
        return;
    }
    
    self->_curTestDetails = [self->_tests objectAtIndex:self->_curTestIndex];
    self->_curTestIndex++;
    
    if (self->_curTestDetails.isSync)
    {
        self->_client.delegate = nil;
    }
    else
    {
        self->_client.delegate = self->_clientDelegate;
        NSString* failedSelName = [NSString stringWithFormat:@"callback_%@_RequestFailed:", self->_curTestDetails.name];
        NSString* finishedSelName = [NSString stringWithFormat:@"callback_%@_RequestFinished:withResult:", self->_curTestDetails.name];
        SEL failedSel = NSSelectorFromString(failedSelName);
        SEL finishedSel = NSSelectorFromString(finishedSelName);
        
        if (failedSel != nil && [self respondsToSelector:failedSel])
        {
            self->_clientDelegate.failedSel = failedSel;
        }
        else
        {
            self->_clientDelegate.failedSel = @selector(unexpRequestFailed:);
        }

        if (finishedSel != nil && [self respondsToSelector:finishedSel])
        {
            self->_clientDelegate.finishedSel = finishedSel;
        }
        else
        {
            self->_clientDelegate.finishedSel = @selector(unexpRequestFinished:withResult:);
        }
    }
    
    NSString* message = [NSString stringWithFormat:@"Running %@ (%d/%d)...", self->_curTestDetails.name, self->_curTestIndex, self->_tests.count];
    NSLog(@"%@", message);
    [self->_delegate updateProgressWithMessage:message];
        
    [NSTimer scheduledTimerWithTimeInterval:0.1 target:self selector:@selector(dispatchTest) userInfo:nil repeats:NO];
}

- (void)dispatchTest
{
    self->_client.config.serviceUrl = DEFAULT_SERVICE_URL;   

    BOOL shouldRunNextTest = self->_curTestDetails.isSync;
    
    if (![self respondsToSelector:self->_curTestDetails.sel])
        assert(NO);
    
    [self performSelector:self->_curTestDetails.sel];
    
    if (shouldRunNextTest)
        [self startNextTest];
}

- (void)run
{
    if (self->_curTestIndex != 0)
        return;
    
    self->_client.config.serviceUrl = DEFAULT_SERVICE_URL;   
    self->_client.delegate = nil;
    [self setUp];
    [self startNextTest];
}

- (KalturaBaseEntry*)uploadEntryWithFileName:(NSString*)fileBase withFileExt:(NSString*)fileExt withMediaType:(int)mediaType
{
    NSString* fileName = [NSString stringWithFormat:@"%@.%@", fileBase, fileExt];
    
    // return: object, params: object
    KalturaUploadToken* token = [[[KalturaUploadToken alloc] init] autorelease];
    token.fileName = fileName;
    token = [self->_client.uploadToken addWithUploadToken:token];
    assert(self->_client.error == nil);
    
    // return: object, params: object
    KalturaMediaEntry* entry = [[[KalturaMediaEntry alloc] init] autorelease];
    entry.name = fileName;
    entry.mediaType = mediaType;
    entry = [self->_client.media addWithEntry:entry];
    assert(self->_client.error == nil);
   
    // return: object, params: string, object
    KalturaUploadedFileTokenResource* resource = [[[KalturaUploadedFileTokenResource alloc] init] autorelease];
    resource.token = token.id;
    entry = [self->_client.media addContentWithEntryId:entry.id withResource:resource];
    assert(self->_client.error == nil);
    
    // return: object, params: string, file
    NSString* uploadFilePath = [[NSBundle mainBundle] pathForResource:fileBase ofType:fileExt];
    [self->_client.uploadToken uploadWithUploadTokenId:token.id withFileData:uploadFilePath];
    assert(self->_client.error == nil);
    
    // approve the entry, required when the account has content moderation enabled
    [self->_client.media approveWithEntryId:entry.id];
    assert(self->_client.error == nil);
    
    return entry;
}

- (void)unexpRequestFailed:(KalturaClientBase *)aClient
{
    assert(NO);
}

- (void)unexpRequestFinished:(KalturaClientBase *)aClient withResult:(id)result
{
    assert(NO);
}

- (void)setUp
{
    // -- create an image entry since it's immediately ready
    self->_imageEntry = [[self uploadEntryWithFileName:@"DemoImage" withFileExt:@"jpg" withMediaType:[KalturaMediaType IMAGE]] retain];

    // -- create a video entry
    self->_videoEntry = [[self uploadEntryWithFileName:@"DemoVideo" withFileExt:@"flv" withMediaType:[KalturaMediaType VIDEO]] retain];
    
    KalturaFlavorAsset* firstFlavor = nil;
    for(;;)
    {
        NSArray* flavorArray = [self->_client.flavorAsset getByEntryIdWithEntryId:self->_videoEntry.id];
        assert(self->_client.error == nil);
        assert(flavorArray.count > 0);
        firstFlavor = [flavorArray objectAtIndex:0];
        if (firstFlavor.status == [KalturaFlavorAssetStatus READY])
            break;
        
        [NSThread sleepForTimeInterval:10];     
    }
}

- (void)tearDown
{
    // -- delete the video entry
    [self->_client.media deleteWithEntryId:self->_videoEntry.id];
    assert(self->_client.error == nil);
    [self->_videoEntry release];
    self->_videoEntry = nil;

    // -- delete the image entry
    [self->_client.media deleteWithEntryId:self->_imageEntry.id];
    assert(self->_client.error == nil);
    [self->_imageEntry release];
    self->_imageEntry = nil;
}

///////////////// Sync tests /////////////////

- (void)testSyncFlow
{
    // return: bool, params: N/A
    assert([self->_client.system ping]);
    assert(self->_client.error == nil);
    
    // return: object, params: object
    KalturaUploadToken* token = [[[KalturaUploadToken alloc] init] autorelease];
    token.fileName = UPLOAD_FILENAME;
    token = [self->_client.uploadToken addWithUploadToken:token];
    assert(self->_client.error == nil);
    assert(token.id.length > 0);
    assert([token.fileName compare:UPLOAD_FILENAME] == NSOrderedSame);
    assert(token.status == [KalturaUploadTokenStatus PENDING]);
    assert(token.partnerId == PARTNER_ID);
    assert([token.userId compare:USER_ID] == NSOrderedSame);
    assert(isnan(token.fileSize));
    
    // return: object, params: object
    KalturaMediaEntry* entry = [[[KalturaMediaEntry alloc] init] autorelease];
    entry.name = ENTRY_NAME;
    entry.mediaType = [KalturaMediaType VIDEO];
    entry = [self->_client.media addWithEntry:entry];
    assert(self->_client.error == nil);
    assert(entry.id.length > 0);
    assert([[KalturaEntryStatus NO_CONTENT] compare:entry.status] == NSOrderedSame);
    assert([entry.name compare:ENTRY_NAME] == NSOrderedSame);
    assert(entry.partnerId == PARTNER_ID);
    assert([entry.userId compare:USER_ID] == NSOrderedSame);
    
    // return: object, params: string, object
    KalturaUploadedFileTokenResource* resource = [[[KalturaUploadedFileTokenResource alloc] init] autorelease];
    resource.token = token.id;
    entry = [self->_client.media addContentWithEntryId:entry.id withResource:resource];
    assert(self->_client.error == nil);
    assert([[KalturaEntryStatus IMPORT] compare:entry.status] == NSOrderedSame);
    
    // approve the entry, required when the account has content moderation enabled
    [self->_client.media approveWithEntryId:entry.id];
    assert(self->_client.error == nil);
    
    // return: object, params: string, file
    NSString* uploadFilePath = [[NSBundle mainBundle] pathForResource:@"DemoVideo" ofType:@"flv"];
    token = [self->_client.uploadToken uploadWithUploadTokenId:token.id withFileData:uploadFilePath];
    assert(self->_client.error == nil);
    assert(token.status == [KalturaUploadTokenStatus CLOSED]);
    
    // return: array, params: string
    NSArray* flavorArray = [self->_client.flavorAsset getByEntryIdWithEntryId:entry.id];
    assert(self->_client.error == nil);
    assert(flavorArray.count > 0);
    BOOL foundSource = NO;
    for (KalturaFlavorAsset* asset in flavorArray)
    {
        if (asset.flavorParamsId != 0)
            continue;
        
        assert(asset.isOriginal);
        assert([asset.entryId compare:entry.id] == NSOrderedSame);
        foundSource = YES;
        break;
    }
    assert(foundSource);
    
    // return: int, params: object
    KalturaMediaEntryFilter* mediaFilter = [[[KalturaMediaEntryFilter alloc] init] autorelease];
    mediaFilter.idEqual = entry.id;
    mediaFilter.statusNotEqual = [KalturaEntryStatus DELETED];
    int entryCount = [self->_client.media countWithFilter:mediaFilter];
    assert(self->_client.error == nil);
    assert(entryCount == 1);
    
    // return: void
    [self->_client.media deleteWithEntryId:entry.id];
    assert(self->_client.error == nil);

    [NSThread sleepForTimeInterval:5];          // wait for the status to update
    entryCount = [self->_client.media countWithFilter:mediaFilter];
    assert(self->_client.error == nil);
    assert(entryCount == 0);
    
    // return: object, params: array, int
    KalturaMediaEntryFilterForPlaylist* playlistFilter = [[[KalturaMediaEntryFilterForPlaylist alloc] init] autorelease];
    playlistFilter.idEqual = self->_imageEntry.id;
    NSArray* filterArray = [NSArray arrayWithObject:playlistFilter];
    NSArray* playlistExecute = [self->_client.playlist executeFromFiltersWithFilters:filterArray withTotalResults:10];
    assert(self->_client.error == nil);
    assert(playlistExecute.count == 1);
    KalturaBaseEntry* firstPlaylistEntry = [playlistExecute objectAtIndex:0];
    assert([firstPlaylistEntry.id compare:self->_imageEntry.id] == NSOrderedSame);
    
    // return: file, params: string, int, bool
    NSString *serveUrl = [self->_client.data serveWithEntryId:@"12345" withVersion:5 withForceProxy:YES];
    NSString *encodedKs = (NSString*)CFURLCreateStringByAddingPercentEscapes(
        NULL, 
        (CFStringRef)self->_client.ks, 
        NULL, 
        (CFStringRef)@"!*'();:@&=+$,/?%#[] \"\\<>{}|^~`", 
        kCFStringEncodingUTF8);
    NSString *encodedClientTag = (NSString*)CFURLCreateStringByAddingPercentEscapes(
        NULL, 
        (CFStringRef)self->_client.config.clientTag, 
        NULL, 
        (CFStringRef)@"!*'();:@&=+$,/?%#[] \"\\<>{}|^~`", 
        kCFStringEncodingUTF8);
    NSString* expectedPrefix = [NSString stringWithFormat:@"%@/api_v3/index.php?kalsig=", self->_client.config.serviceUrl];
    NSString* expectedPostfix = [NSString stringWithFormat:@"&version=5&service=data&partnerId=%d&ks=%@&ignoreNull=1&format=2&forceProxy=1&entryId=12345&clientTag=%@&apiVersion=%@&action=serve&", PARTNER_ID, encodedKs, encodedClientTag, self->_client.apiVersion];
    [encodedKs release];
    [encodedClientTag release];
    assert([serveUrl hasPrefix:expectedPrefix]);
    assert([serveUrl hasSuffix:expectedPostfix]);
}

- (NSArray*)buildSyncMultiReqFlow
{
    // start the multi request
    [self->_client startMultiRequest];
    
    // return: bool
    [self->_client.system ping];
    
    // return: object, params: object
    KalturaUploadToken* token = [[[KalturaUploadToken alloc] init] autorelease];
    token.fileName = UPLOAD_FILENAME;
    [self->_client.uploadToken addWithUploadToken:token];
    NSString* tokenId = @"{2:result:id}";
    
    // return: object, params: object
    KalturaMediaEntry* entry = [[[KalturaMediaEntry alloc] init] autorelease];
    entry.name = ENTRY_NAME;
    entry.mediaType = [KalturaMediaType VIDEO];
    [self->_client.media addWithEntry:entry];
    NSString* entryId = @"{3:result:id}";
    
    // return: object, params: string, object
    KalturaUploadedFileTokenResource* resource = [[[KalturaUploadedFileTokenResource alloc] init] autorelease];
    resource.token = tokenId;
    [self->_client.media addContentWithEntryId:entryId withResource:resource];
    
    // return: object, params: string, file
    NSString* uploadFilePath = [[NSBundle mainBundle] pathForResource:@"DemoVideo" ofType:@"flv"];
    [self->_client.uploadToken uploadWithUploadTokenId:tokenId withFileData:uploadFilePath];
    
    // return: array, params: string
    [self->_client.flavorAsset getByEntryIdWithEntryId:entryId];
    
    // return: int, params: object
    KalturaMediaEntryFilter* mediaFilter = [[[KalturaMediaEntryFilter alloc] init] autorelease];
    mediaFilter.idEqual = entryId;
    mediaFilter.statusNotEqual = [KalturaEntryStatus DELETED];
    [self->_client.media countWithFilter:mediaFilter];
    
    // return: void
    [self->_client.media deleteWithEntryId:entryId];
    
    // return: object, params: array, int
    KalturaMediaEntryFilterForPlaylist* playlistFilter = [[[KalturaMediaEntryFilterForPlaylist alloc] init] autorelease];
    playlistFilter.idEqual = self->_imageEntry.id;
    NSArray* filterArray = [NSArray arrayWithObject:playlistFilter];
    [self->_client.playlist executeFromFiltersWithFilters:filterArray withTotalResults:10];
    
    // validate the results
    return [self->_client doMultiRequest];
}

- (void)validateSyncMultiReqFlow:(NSArray*)results
{
    // system.ping
    NSString* pingResult = [results objectAtIndex:0];
    assert([pingResult compare:@"1"] == NSOrderedSame);
    
    // uploadToken.add
    KalturaUploadToken* token = [results objectAtIndex:1];
    assert(token.id.length > 0);
    assert([token.fileName compare:UPLOAD_FILENAME] == NSOrderedSame);
    assert(token.status == [KalturaUploadTokenStatus PENDING]);
    assert(token.partnerId == PARTNER_ID);
    assert([token.userId compare:USER_ID] == NSOrderedSame);
    assert(isnan(token.fileSize));
    
    // media.add
    KalturaMediaEntry* entry = [results objectAtIndex:2];
    assert(entry.id.length > 0);
    assert([[KalturaEntryStatus NO_CONTENT] compare:entry.status] == NSOrderedSame);
    assert([entry.name compare:ENTRY_NAME] == NSOrderedSame);
    assert(entry.partnerId == PARTNER_ID);
    assert([entry.userId compare:USER_ID] == NSOrderedSame);
    
    // media.addContent
    entry = [results objectAtIndex:3];
    assert([[KalturaEntryStatus IMPORT] compare:entry.status] == NSOrderedSame);
    
    // uploadToken.upload
    token = [results objectAtIndex:4];
    assert(token.status == [KalturaUploadTokenStatus CLOSED]);
    
    // flavorAsset.getByEntryId
    NSArray* flavorArray = [results objectAtIndex:5];
    assert(flavorArray.count > 0);
    BOOL foundSource = NO;
    for (KalturaFlavorAsset* asset in flavorArray)
    {
        if (asset.flavorParamsId != 0)
            continue;
        
        assert(asset.isOriginal);
        assert([asset.entryId compare:entry.id] == NSOrderedSame);
        foundSource = YES;
        break;
    }
    assert(foundSource);
    
    // media.count
    NSString* entryCount = [results objectAtIndex:6];
    assert([entryCount compare:@"0"] == NSOrderedSame || 
           [entryCount compare:@"1"] == NSOrderedSame);
    
    // playlist.executeWithFilters
    NSArray* playlistExecute = [results objectAtIndex:8];
    assert(playlistExecute.count == 1);
    KalturaBaseEntry* firstPlaylistEntry = [playlistExecute objectAtIndex:0];
    assert([firstPlaylistEntry.id compare:self->_imageEntry.id] == NSOrderedSame);
}

- (void)testSyncMultiReqFlow
{
    NSArray* results = [self buildSyncMultiReqFlow];
    
    assert(self->_client.error == nil);

    [self validateSyncMultiReqFlow:results];
}

- (void)testEmptyMultirequest
{
    [self->_client startMultiRequest];
    NSArray* result = [self->_client doMultiRequest];
    assert(result.count == 0);
}

// validates: 
//      kalsig is generated
//      objectType is included for objects
//      serialization of all types
//      serialization of empty/null variables
- (void)testPremadeRequest
{
    // init a client with fixed values
    KalturaClientConfiguration* config = [[KalturaClientConfiguration alloc] init];
    KalturaNSLogger* logger = [[KalturaNSLogger alloc] init];
    config.logger = logger;
    config.serviceUrl = DEFAULT_SERVICE_URL;
    config.clientTag = @"testTag";
    [logger release];           // retained on config
    config.partnerId = 56789;
    KalturaClient* client = [[KalturaClient alloc] initWithConfig:config];
    [config release];           // retained on the client
    client.apiVersion = @"9.8.7";
    client.ks = @"abcdef";
    
    // add all basic types
    // Note: not testing float since its formatting may change between platforms
    [client.params addIfDefinedKey:@"bool" withBool:NO];
    [client.params addIfDefinedKey:@"int" withInt:1234];
    [client.params addIfDefinedKey:@"string" withString:@"strVal"];
    
    // object
    KalturaMediaEntry* entry = [[[KalturaMediaEntry alloc] init] autorelease];
    entry.name = @"abcd";
    [client.params addIfDefinedKey:@"object" withObject:entry];
    
    // array
    KalturaString* string = [[[KalturaString alloc] init] autorelease];
    string.value = @"dummy";
    NSArray* array = [NSArray arrayWithObject:string];
    [client.params addIfDefinedKey:@"array" withArray:array];
    
    // null / empty items
    [client.params addIfDefinedKey:@"emptyBool" withBool:KALTURA_NULL_BOOL];
    [client.params addIfDefinedKey:@"emptyInt" withInt:KALTURA_NULL_INT];
    [client.params addIfDefinedKey:@"emptyFloat" withFloat:KALTURA_NULL_FLOAT];
    [client.params addIfDefinedKey:@"emptyString" withString:KALTURA_NULL_STRING];
    [client.params addIfDefinedKey:@"emptyObject" withObject:KALTURA_NULL_OBJECT];
    [client.params addIfDefinedKey:@"emptyArray" withArray:[NSArray array]];
    
    // verify
    NSString* result = [client queueServeService:@"test" withAction:@"testAct"];
    NSString* expectedResult = [NSString stringWithFormat:@"%@/api_v3/index.php?kalsig=b2e9bd151b7edf43c2e210e45ffb15fd&string=strVal&service=test&partnerId=56789&object%%3AobjectType=KalturaMediaEntry&object%%3Aname=abcd&ks=abcdef&int=1234&ignoreNull=1&format=2&emptyString__null=&emptyObject__null=&emptyInt__null=&emptyFloat__null=&emptyBool__null=&emptyArray%%3A-=&clientTag=testTag&bool=0&array%%3A0%%3Avalue=dummy&array%%3A0%%3AobjectType=KalturaString&apiVersion=9.8.7&action=testAct&", DEFAULT_SERVICE_URL];
    assert([result compare:expectedResult] == NSOrderedSame);
    
    // cleanup
    [client release];
}

- (void)testHttps
{
    self->_client.config.serviceUrl = @"https://www.kaltura.com";
    [self testSyncFlow];
}

- (void)testInvalidServerIp
{
    self->_client.config.serviceUrl = @"http://1.1.1.1";
    [self->_client.system ping];
    NSError* error = self->_client.error;
    
    assert(error != nil);
    assert([error.domain compare:NetworkRequestErrorDomain] == NSOrderedSame);
}

- (void)testInvalidServerDnsName
{
    self->_client.config.serviceUrl = @"http://www.nonexistingkaltura.com";
    [self->_client.system ping];
    NSError* error = self->_client.error;
    
    assert(error != nil);
    assert([error.domain compare:NetworkRequestErrorDomain] == NSOrderedSame);
}

- (void)testSendNonExistingFile
{
    [self->_client.uploadToken uploadWithUploadTokenId:@"12345" withFileData:@"NonExistingFile.dat"];
    NSError* error = self->_client.error;

    assert(error != nil);
    assert([error.domain compare:NetworkRequestErrorDomain] == NSOrderedSame);
    assert(error.code == ASIInternalErrorWhileBuildingRequestType);
}

- (void)assertKalturaError:(NSError*)error withCode:(int)code
{
    assert(error != nil);
    assert([error.domain compare:KalturaClientErrorDomain] == NSOrderedSame);
    assert(error.code == code);
}

- (void)testSyncApiError
{
    KalturaBaseEntry* entry = [self->_client.baseEntry getWithEntryId:@"NonExistingEntry"];
    assert(entry == nil);
    [self assertKalturaError:self->_client.error withCode:KalturaClientErrorAPIException];
}

- (void)testSyncMultiReqApiError
{
    [self->_client startMultiRequest];
    [self->_client.system ping];
    [self->_client.baseEntry getWithEntryId:@"NonExistingEntry"];
    [self->_client.system ping];
    NSArray* results = [self->_client doMultiRequest];
    assert(self->_client.error == nil);
    assert(results.count == 3);
    
    NSString* res1 = [results objectAtIndex:0];
    assert([res1 compare:@"1"] == NSOrderedSame);
    
    NSError* res2 = [results objectAtIndex:1];
    [self assertKalturaError:res2 withCode:KalturaClientErrorAPIException];
    
    NSString* res3 = [results objectAtIndex:2];
    assert([res3 compare:@"1"] == NSOrderedSame);
}

- (void)testXmlParsingError
{
    self->_client.config.serviceUrl = KALTURA_CLIENT_TEST_URL;
    [self->_client.params addIfDefinedKey:@"responseBuffer" withString:@"<xml>"];
    [self->_client.system ping];
    
    [self assertKalturaError:self->_client.error withCode:KalturaClientErrorXmlParsing];
}

- (void)testTagInSimpleType
{
    self->_client.config.serviceUrl = KALTURA_CLIENT_TEST_URL;
    [self->_client.params addIfDefinedKey:@"responseBuffer" withString:@"<xml><result><sometag></sometag></result></xml>"];
    [self->_client.system ping];
    
    [self assertKalturaError:self->_client.error withCode:KalturaClientErrorStartTagInSimpleType];
}

- (void)testEmptyObjectOrException
{
    self->_client.config.serviceUrl = KALTURA_CLIENT_TEST_URL;
    [self->_client.params addIfDefinedKey:@"responseBuffer" withString:@"<xml><result></result></xml>"];
    [self->_client.baseEntry getWithEntryId:@"1234"];
    
    [self assertKalturaError:self->_client.error withCode:KalturaClientErrorEmptyObject];
}

- (void)testEmptyObject
{
    self->_client.config.serviceUrl = KALTURA_CLIENT_TEST_URL;
    [self->_client.params addIfDefinedKey:@"responseBuffer" withString:@"<xml><result><objectType>KalturaPlaylist</objectType><filters><item/></filters></result></xml>"];
    [self->_client.playlist getWithId:@"1234"];
    
    [self assertKalturaError:self->_client.error withCode:KalturaClientErrorMissingObjectTypeTag];
}

- (void)testTagInSimpleObjectProperty
{
    self->_client.config.serviceUrl = KALTURA_CLIENT_TEST_URL;
    [self->_client.params addIfDefinedKey:@"responseBuffer" withString:@"<xml><result><objectType>KalturaPlaylist</objectType><id><sometag/></id></result></xml>"];
    [self->_client.playlist getWithId:@"1234"];
    
    [self assertKalturaError:self->_client.error withCode:KalturaClientErrorUnexpectedTagInSimpleType];
}

- (void)testTagInObjectDoesntStartWithType
{
    self->_client.config.serviceUrl = KALTURA_CLIENT_TEST_URL;
    [self->_client.params addIfDefinedKey:@"responseBuffer" withString:@"<xml><result><id>1234</id></result></xml>"];
    [self->_client.playlist getWithId:@"1234"];
    
    [self assertKalturaError:self->_client.error withCode:KalturaClientErrorExpectedObjectTypeTag];
}

- (void)testCharsInsteadOfObject
{
    self->_client.config.serviceUrl = KALTURA_CLIENT_TEST_URL;
    [self->_client.params addIfDefinedKey:@"responseBuffer" withString:@"<xml><result>1234</result></xml>"];
    [self->_client.playlist getWithId:@"1234"];
    
    [self assertKalturaError:self->_client.error withCode:KalturaClientErrorExpectedPropertyTag];
}

- (void)testUnknownObjectType
{
    self->_client.config.serviceUrl = KALTURA_CLIENT_TEST_URL;
    [self->_client.params addIfDefinedKey:@"responseBuffer" withString:@"<xml><result><objectType>UnknownObjectType</objectType></result></xml>"];
    [self->_client queueObjectService:@"playlist" withAction:@"get" withExpectedType:@"AnotherUnknownObject"];
    
    [self assertKalturaError:self->_client.error withCode:KalturaClientErrorUnknownObjectType];
}

- (void)testNonKalturaObjectType
{
    self->_client.config.serviceUrl = KALTURA_CLIENT_TEST_URL;
    [self->_client.params addIfDefinedKey:@"responseBuffer" withString:@"<xml><result><objectType>NSString</objectType></result></xml>"];
    [self->_client.playlist getWithId:@"1234"];
    
    [self assertKalturaError:self->_client.error withCode:KalturaClientErrorUnknownObjectType];
}

- (void)testArrayTagIsNotItem
{
    self->_client.config.serviceUrl = KALTURA_CLIENT_TEST_URL;
    [self->_client.params addIfDefinedKey:@"responseBuffer" withString:@"<xml><result><sometag/></result></xml>"];
    [self->_client.flavorAsset getByEntryIdWithEntryId:@"1234"];
    
    [self assertKalturaError:self->_client.error withCode:KalturaClientErrorUnexpectedArrayTag];
}

- (void)testMultiReqTagNotItem
{
    self->_client.config.serviceUrl = KALTURA_CLIENT_TEST_URL;
    [self->_client.params addIfDefinedKey:@"responseBuffer" withString:@"<xml><result><sometag/></result></xml>"];
    [self->_client startMultiRequest];
    [self->_client.system ping];
    [self->_client doMultiRequest];
        
    [self assertKalturaError:self->_client.error withCode:KalturaClientErrorUnexpectedMultiReqTag];
}

- (void)testMultiReqTooManyItems
{
    self->_client.config.serviceUrl = KALTURA_CLIENT_TEST_URL;
    [self->_client.params addIfDefinedKey:@"responseBuffer" withString:@"<xml><result><item>1</item><item>1</item></result></xml>"];
    [self->_client startMultiRequest];
    [self->_client.system ping];
    [self->_client doMultiRequest];
    
    [self assertKalturaError:self->_client.error withCode:KalturaClientErrorUnexpectedMultiReqTag];
}

- (void)testMultiReqNotEnoughItems
{
    self->_client.config.serviceUrl = KALTURA_CLIENT_TEST_URL;
    [self->_client.params addIfDefinedKey:@"responseBuffer" withString:@"<xml><result></result></xml>"];
    [self->_client startMultiRequest];
    [self->_client.system ping];
    [self->_client doMultiRequest];
    
    [self assertKalturaError:self->_client.error withCode:KalturaClientErrorMissingMultiReqItems];
}

- (void)testInvalidHttpStatus
{
    self->_client.config.serviceUrl = @"http://www.kaltura.com/nonExistingFolder";
    [self->_client.system ping];
    
    [self assertKalturaError:self->_client.error withCode:KalturaClientErrorInvalidHttpCode];
}

- (void)testDoubleMultiReqStart
{
    [self->_client startMultiRequest];
    @try 
    {
        [self->_client startMultiRequest];
    }
    @catch (KalturaClientException *exception) 
    {
        assert([exception.name compare:@"DoubleStartMultiReq"] == NSOrderedSame);
        [self->_client cancelRequest];
        return;
    }
    
    assert(NO);
}

- (void)testDoMultiReqWithoutStart
{
    @try 
    {
        [self->_client doMultiRequest];
    }
    @catch (KalturaClientException *exception) 
    {
        assert([exception.name compare:@"EndWithoutMultiReq"] == NSOrderedSame);
        [self->_client cancelRequest];
        return;
    }
    
    assert(NO);
}

- (void)testApiTimeout
{
    self->_client.config.requestTimeout = 1;
    self->_client.config.serviceUrl = KALTURA_CLIENT_TEST_URL;
    [self->_client.params addIfDefinedKey:@"sleepTime" withString:@"10"];
    [self->_client.system ping];    
    self->_client.config.requestTimeout = 120;
    
    NSError* error = self->_client.error;
    assert(error != nil);
    assert([error.domain compare:NetworkRequestErrorDomain] == NSOrderedSame);
    assert(error.code == ASIRequestTimedOutErrorType);
}

#define METADATA_XML (@"<metadata><List1>val1</List1><Text1>some text</Text1></metadata>")

- (void)testUsePlugin
{
    KalturaMetadataClientPlugin* metadata = [[KalturaMetadataClientPlugin alloc] initWithClient:self->_client];
    KalturaMetadataProfile* profile = [[[KalturaMetadataProfile alloc] init] autorelease];
    profile.metadataObjectType = [KalturaMetadataObjectType ENTRY];
    profile.name = @"Test metadata profile";
    NSString* xsdFilePath = [[NSBundle mainBundle] pathForResource:@"MetadataSchema" ofType:@"xsd"];
    NSString* xsdFileData = [[[NSString alloc] initWithContentsOfFile:xsdFilePath encoding:NSUTF8StringEncoding error:nil] autorelease];    
    profile = [metadata.metadataProfile addWithMetadataProfile:profile withXsdData:xsdFileData];
    assert(self->_client.error == nil);

    KalturaMetadata* metadataObj = [metadata.metadata addWithMetadataProfileId:profile.id withObjectType:[KalturaMetadataObjectType ENTRY] withObjectId:self->_imageEntry.id withXmlData:METADATA_XML];
    assert(self->_client.error == nil);
    
    metadataObj = [metadata.metadata getWithId:metadataObj.id];
    assert(self->_client.error == nil);
    assert([metadataObj.xml compare:METADATA_XML] == NSOrderedSame);
    
    [metadata.metadata deleteWithId:metadataObj.id];
    assert(self->_client.error == nil);
    
    [metadata.metadataProfile deleteWithId:profile.id];
    assert(self->_client.error == nil);

    [metadata release];                                         
}

- (void)testOptionalParameters
{
    // int, string
    NSString* ks = [self->_client.session startWithSecret:ADMIN_SECRET withUserId:USER_ID withType:[KalturaSessionType ADMIN] withPartnerId:PARTNER_ID];
    assert(self->_client.error == nil);
    assert(ks.length > 40);     // 40 is the signature length
    
    // bool
    KalturaFlavorAsset* firstFlavor = nil;
    NSArray* flavorArray = [self->_client.flavorAsset getByEntryIdWithEntryId:self->_videoEntry.id];
    firstFlavor = [flavorArray objectAtIndex:0];
    NSString* downloadUrl = [self->_client.flavorAsset getDownloadUrlWithId:firstFlavor.id];
    assert(self->_client.error == nil);
    assert(downloadUrl.length > 0);

    // object
    KalturaMediaEntryFilter* mediaFilter = [[[KalturaMediaEntryFilter alloc] init] autorelease];
    mediaFilter.statusNotEqual = [KalturaEntryStatus DELETED];
    KalturaMediaListResponse* listResult = [self->_client.media listWithFilter:mediaFilter];
    assert(self->_client.error == nil);
    assert(listResult.totalCount > 0);
    
    // array
    int convertJobId = [self->_client.media convertWithEntryId:self->_videoEntry.id];
    assert(self->_client.error == nil);
    assert(convertJobId != 0);
}

///////////////// Async tests /////////////////

- (void)testAsyncCancel
{
    for (int i = 0; i < 3; i++)
    {
        [self->_client startMultiRequest];
        [self->_client cancelRequest];
        
        [self->_client.system ping];
        [self->_client cancelRequest];
    }
    
    [self startNextTest];
}

- (void)callback_testAsyncApiError_RequestFailed:(KalturaClientBase *)aClient
{
    [self assertKalturaError:aClient.error withCode:KalturaClientErrorAPIException];
    
    [self startNextTest];
}

- (void)testAsyncApiError
{
    [self->_client.media getWithEntryId:@"NonExistingEntry"];
}

- (void)callback_testAsyncInvalidServerDnsName_RequestFailed:(KalturaClientBase *)aClient
{
    assert([aClient.error.domain compare:NetworkRequestErrorDomain] == NSOrderedSame);
    
    [self startNextTest];
}

- (void)testAsyncInvalidServerDnsName
{
    self->_client.config.serviceUrl = @"http://www.nonexistingkaltura.com";
    [self->_client.system ping];
}

- (void)callback_testAsyncXmlParsingError_RequestFailed:(KalturaClientBase *)aClient
{
    [self assertKalturaError:self->_client.error withCode:KalturaClientErrorXmlParsing];
    
    [self startNextTest];
}

- (void)testAsyncXmlParsingError
{
    self->_client.config.serviceUrl = KALTURA_CLIENT_TEST_URL;
    [self->_client.params addIfDefinedKey:@"responseBuffer" withString:@"<xml>"];
    [self->_client.system ping];
}

- (void)callback_testAsyncMultiReqFlow_RequestFinished:(KalturaClientBase *)aClient withResult:(id)aResult
{
    assert(self->_client.error == nil);
    [self validateSyncMultiReqFlow:aResult];
    
    [self startNextTest];
}

- (void)testAsyncMultiReqFlow
{
    [self buildSyncMultiReqFlow];
}

- (void)callback_testAsyncInvalidHttpStatus_RequestFailed:(KalturaClientBase *)aClient
{
    [self assertKalturaError:self->_client.error withCode:KalturaClientErrorInvalidHttpCode];

    [self startNextTest];
}

- (void)testAsyncInvalidHttpStatus
{
    self->_client.config.serviceUrl = @"http://www.kaltura.com/nonExistingFolder";
    [self->_client.system ping];
}

- (void)callback_testAsyncMultiReqXmlParsingError_RequestFailed:(KalturaClientBase *)aClient
{
    [self assertKalturaError:self->_client.error withCode:KalturaClientErrorXmlParsing];
    
    [self startNextTest];
}

- (void)testAsyncMultiReqXmlParsingError
{
    self->_client.config.serviceUrl = KALTURA_CLIENT_TEST_URL;
    [self->_client.params addIfDefinedKey:@"responseBuffer" withString:@"<xml>"];
    [self->_client startMultiRequest];
    [self->_client.system ping];
    [self->_client doMultiRequest];
}

- (void)callback_testAsyncMultiReqApiError_RequestFinished:(KalturaClientBase *)aClient withResult:(id)aResults
{
    NSArray* results = aResults;
    assert(self->_client.error == nil);
    assert(results.count == 3);
    
    NSString* res1 = [results objectAtIndex:0];
    assert([res1 compare:@"1"] == NSOrderedSame);
    
    NSError* res2 = [results objectAtIndex:1];
    [self assertKalturaError:res2 withCode:KalturaClientErrorAPIException];
    
    NSString* res3 = [results objectAtIndex:2];
    assert([res3 compare:@"1"] == NSOrderedSame);
    
    [self startNextTest];
}

- (void)testAsyncMultiReqApiError
{
    [self->_client startMultiRequest];
    [self->_client.system ping];
    [self->_client.baseEntry getWithEntryId:@"NonExistingEntry"];
    [self->_client.system ping];
    [self->_client doMultiRequest];
}

- (void)callback_testAsyncEmptyMultirequest_RequestFinished:(KalturaClientBase *)aClient withResult:(id)aResults
{
    NSArray* results = aResults;
    assert(self->_client.error == nil);
    assert(results.count == 0);
    
    [self startNextTest];
}

- (void)testAsyncEmptyMultirequest
{
    [self->_client startMultiRequest];
    [self->_client doMultiRequest];
}

- (void)callback_testAsyncBoolType_RequestFinished:(KalturaClientBase *)aClient withResult:(id)aResults
{
    NSString* result = aResults;
    assert([result compare:@"1"] == NSOrderedSame);    
    
    [self startNextTest];
}

- (void)testAsyncBoolType
{
    [self->_client.system ping];
}

- (void)callback_testAsyncIntType_RequestFinished:(KalturaClientBase *)aClient withResult:(id)aResults
{
    NSString* result = aResults;
    assert(self->_client.error == nil);
    assert([result compare:@"1"] == NSOrderedSame);    
    
    [self startNextTest];
}

- (void)testAsyncIntType
{
    KalturaMediaEntryFilter* mediaFilter = [[[KalturaMediaEntryFilter alloc] init] autorelease];
    mediaFilter.idEqual = self->_imageEntry.id;
    mediaFilter.statusNotEqual = [KalturaEntryStatus DELETED];
    [self->_client.media countWithFilter:mediaFilter];
}

- (void)callback_testAsyncStringType_RequestFinished:(KalturaClientBase *)aClient withResult:(id)aResults
{
    NSString* result = aResults;
    assert(self->_client.error == nil);
    assert(result.length > 40);     // 40 is the signature length

    [self startNextTest];
}

- (void)testAsyncStringType
{
    [self->_client.session startWithSecret:ADMIN_SECRET withUserId:USER_ID withType:[KalturaSessionType ADMIN] withPartnerId:PARTNER_ID];
}

- (void)callback_testAsyncObjectType_RequestFinished:(KalturaClientBase *)aClient withResult:(id)aResults
{
    KalturaBaseEntry* result = aResults;
    assert(self->_client.error == nil);
    assert([result.id compare:self->_imageEntry.id] == NSOrderedSame);
    
    [self startNextTest];
}

- (void)testAsyncObjectType
{
    [self->_client.baseEntry getWithEntryId:self->_imageEntry.id];
}

- (void)callback_testAsyncArrayType_RequestFinished:(KalturaClientBase *)aClient withResult:(id)aResults
{
    NSArray* flavorArray = aResults;
    assert(self->_client.error == nil);
    assert(flavorArray.count > 0);
    BOOL foundSource = NO;
    for (KalturaFlavorAsset* asset in flavorArray)
    {
        if (asset.flavorParamsId != 0)
            continue;
        
        assert(asset.isOriginal);
        assert([asset.entryId compare:self->_videoEntry.id] == NSOrderedSame);
        foundSource = YES;
        break;
    }
    assert(foundSource);
    
    [self startNextTest];
}

- (void)testAsyncArrayType
{
    [self->_client.flavorAsset getByEntryIdWithEntryId:self->_videoEntry.id];
}

- (void)callback_testAsyncVoidType_RequestFinished:(KalturaClientBase *)aClient withResult:(id)aResults
{
    NSString* result = aResults;
    assert(self->_client.error == nil);
    assert(result.length == 0);

    self->_client.ks = [KalturaClient generateSessionWithSecret:ADMIN_SECRET withUserId:USER_ID withType:[KalturaSessionType ADMIN] withPartnerId:PARTNER_ID withExpiry:86400 withPrivileges:@""];

    [self startNextTest];
}

- (void)testAsyncVoidType
{
    [self->_client.session end];
}

- (void)testUnknownObjectReturned
{
    self->_client.config.serviceUrl = KALTURA_CLIENT_TEST_URL;
    [self->_client.params addIfDefinedKey:@"responseBuffer" withString:@"<xml><result><objectType>UnknownObjectType</objectType><id>abcdef</id></result></xml>"];
    KalturaBaseEntry* result = [self->_client.baseEntry getWithEntryId:self->_imageEntry.id];
    assert(self->_client.error == nil);
    assert([result isKindOfClass:[KalturaBaseEntry class]]);
    assert([result.id compare:@"abcdef"] == NSOrderedSame);
}

- (void)testUnknownArrayObjectReturned
{
    self->_client.config.serviceUrl = KALTURA_CLIENT_TEST_URL;
    [self->_client.params addIfDefinedKey:@"responseBuffer" withString:@"<xml><result><item><objectType>UnknownObjectType</objectType><id>abcdef</id></item></result></xml>"];
    NSArray* result = [self->_client.flavorAsset getByEntryIdWithEntryId:self->_videoEntry.id];
    assert(self->_client.error == nil);
    assert(result.count == 1);
    KalturaFlavorAsset* asset = [result objectAtIndex:0];
    assert([asset isKindOfClass:[KalturaFlavorAsset class]]);
    assert([asset.id compare:@"abcdef"] == NSOrderedSame);
}

- (void)testUnknownNestedObjectObjectReturned
{
    self->_client.config.serviceUrl = KALTURA_CLIENT_TEST_URL;
    [self->_client.params addIfDefinedKey:@"responseBuffer" withString:@"<xml><result><objectType>KalturaConversionProfile</objectType><cropDimensions><objectType>UnknownObjectType</objectType><left>1234</left></cropDimensions></result></xml>"];
    KalturaConversionProfile* result = [self->_client.conversionProfile getWithId:1];
    assert(self->_client.error == nil);
    KalturaCropDimensions* dimensions = result.cropDimensions;
    assert([dimensions isKindOfClass:[KalturaCropDimensions class]]);
    assert(dimensions.left == 1234);
}

- (void)testUnknownNestedArrayObjectReturned
{
    self->_client.config.serviceUrl = KALTURA_CLIENT_TEST_URL;
    [self->_client.params addIfDefinedKey:@"responseBuffer" withString:@"<xml><result><objectType>KalturaBaseEntryListResponse</objectType><objects><item><objectType>UnknownObjectType</objectType><id>abcdef</id></item></objects></result></xml>"];
    KalturaBaseEntryListResponse* result = [self->_client.baseEntry list];
    assert(self->_client.error == nil);
    assert(result.objects.count == 1);
    KalturaBaseEntry* entry = [result.objects objectAtIndex:0];
    assert([entry isKindOfClass:[KalturaBaseEntry class]]);
    assert([entry.id compare:@"abcdef"] == NSOrderedSame);
}

- (void)testDownloadDelegateSanity
{
    KalturaProgressDelegate* delegate = [[[KalturaProgressDelegate alloc] init] autorelease];
    self->_client.downloadProgressDelegate = delegate;
    [self->_client.baseEntry getWithEntryId:self->_imageEntry.id];
    self->_client.downloadProgressDelegate = nil;
    
    assert(delegate.receiveBytesCalled);
}

- (void)testUploadDelegateSanity
{
    KalturaProgressDelegate* delegate = [[[KalturaProgressDelegate alloc] init] autorelease];
    self->_client.uploadProgressDelegate = delegate;
    [self->_client.baseEntry getWithEntryId:self->_imageEntry.id];
    self->_client.uploadProgressDelegate = nil;
    
    assert(delegate.sendBytesCalled);
}

@end
