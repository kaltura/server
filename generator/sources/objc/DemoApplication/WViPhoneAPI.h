#ifndef IPHONSESSION_H_
#define IPHONSESSION_H_


#ifdef __cplusplus
extern "C" {
#endif
#import <Foundation/NSString.h>
#import <Foundation/NSDictionary.h>
    
    typedef enum WViOsApiStatus {
	WViOsApiStatus_OK = 0,
	WViOsApiStatus_NotInitialized,
	WViOsApiStatus_AlreadyInitialized,
	WViOsApiStatus_CantConnectToMediaServer,
	WViOsApiStatus_BadMedia,
	WViOsApiStatus_CantConnectToDrmServer,
	WViOsApiStatus_NotEntitled,
	WViOsApiStatus_EntitlementDenied,
	WViOsApiStatus_LostConnection,
	WViOsApiStatus_EntitlementExpired,
	WViOsApiStatus_NotEntitledByRegion,
	WViOsApiStatus_BadUrl,
	WViOsApiStatus_FileNotPresent,
	WViOsApiStatus_NotRegistered,
	WViOsApiStatus_AlreadyRegistered,
	WViOsApiStatus_NotPlaying,
	WViOsApiStatus_AlreadyPlaying,
	WViOsApiStatus_FileSystemError,
	WViOsApiStatus_AssetDBWasCorrupted,
        WViOsApiStatus_JailBreakDetected,
	WViOsApiStatus_UnknownError,

    } WViOsApiStatus;

    typedef enum WViOsApiEvent {
	WViOsApiEvent_NullEvent = 0,
	WViOsApiEvent_EMMReceived,
	WViOsApiEvent_EMMFailed,
	WViOsApiEvent_Playing,
	WViOsApiEvent_PlayFailed,
	WViOsApiEvent_Stopped,
	WViOsApiEvent_QueryStatus,
	WViOsApiEvent_EndOfList,
	WViOsApiEvent_Initialized,
	WViOsApiEvent_InitializeFailed,
	WViOsApiEvent_Terminated,
	WViOsApiEvent_EMMRemoved,
	WViOsApiEvent_Registered,
	WViOsApiEvent_Unregistered,
        WViOsApiEvent_SetCurrentBitrate,
        WViOsApiEvent_Bitrates,
        WViOsApiEvent_ChapterTitle,
        WViOsApiEvent_ChapterImage,
        WViOsApiEvent_ChapterSetup,
        WViOsApiEvent_StoppingOnError,
        WViOsApiEvent_VideoParams,
        WViOsApiEvent_AudioParams
    } WViOsApiEvent;

    enum {
	WVAssetType_Unknown = 0,
	WVAssetType_File = 1,
	WVAssetType_HTTPStream = 2
    };


// Event/callback Attributes
#define WVVersionKey @"WVVersionKey"
#define WVAssetPathKey @"WVAssetPathKey"
#define WViOsApiStatusKey @"WVStatusKey"
#define WVEMMTimeRemainingKey @"WVEMMTimeRemainingKey"
#define WVDistributionTimeRemainingKey @"WVDistributionTimeRemainingKey"
#define WVPurchaseTimeRemainingKey @"WVPurchaseTimeRemainingKey"
#define WVTimeSinceLastPlaybackKey @"WVTimeSinceLastPlaybackKey"
#define WVIsEncryptedKey @"WVIsEncryptedKey"
#define WVSystemIDKey @"WVSystemIDKey"
#define WVAssetIDKey @"WVAssetIDKey"
#define WVKeyIDKey @"WVKeyIDKey"
#define WVAssetTypeKey @"WVAssetTypeKey"
#define WVErrorKey @"WVErrorKey"
#define WVBitratesKey @"WVBitratesKey"
#define WVCurrentBitrateKey @"WVCurrentBitrate"
#define WVChapterIndexKey @"WVChapterIndexKey"
#define WVChapterTitleKey @"WVChapterTitleKey"
#define WVChapterTimeIndexKey @"WVChapterTimeIndexKey"
#define WVChapterImageKey @"WVChapterImageKey"
#define WVNumChaptersKey @"WVNumChaptersKey"
#define WVDescriptionKey @"WVDescriptionKey"
#define WVVideoType @"WVVideoType"
#define WVProfile @"WVProfile"
#define WVLevel @"WVLevel"
#define WVWidth @"WVWidth"
#define WVHeight @"WVHeight"
#define WVPixelAspectRatio @"WVPixelAspectRatio"
#define WVFrameRate @"WVFrameRate"
#define WVBitrate @"WVBitrate"
#define WVAudioType @"WVAudioType"
#define WVNumChannels @"WVNumChannels"
#define WVSampleFrequency @"WVSampleFrequency"
#define WVBitrate @"WVBitrate"    
#define WVCopyControlInformation_EMIKey @"WVCopyControlInformation_EMIKey"
#define WVCopyControlInformation_APSKey @"WVCopyControlInformation_APSKey"
#define WVCopyControlInformation_CITKey @"WVCopyControlInformation_CITKey"
#define WVCopyControlInformation_HDCPKey @"WVCopyControlInformation_HDCPKey"

   
//Setup values
#define WVDRMServerKey @"WVDRMServerKey"
#define WVDRMAckUrlKey @"WVDRMAckUrlKey"
#define WVHeartbeatUrlKey @"WVHeartbeatUrlKey"
#define WVHeartbeatPeriodKey @"WVHeartbeatPeriodKey"
#define WVAssetDBPathKey @"WVAssetDBPathKey"
#define WVAssetRootKey @"WVAssetRootKey"
#define WVSessionIdKey @"WVSessionIDKey"
#define WVStreamIdKey @"WVStreamIdKey"
#define WVDeviceIdKey @"WVDeviceIdKey"
#define WVClientIPKey @"WVClientIPKey"
#define WVPlayerDrivenAdaptationKey @"WVPlayerDrivenAdaptationKey"
#define WVCAUserDataKey @"WVCAUserDataKey"
#define WVPreloadTimeoutKey @"WVPreloadTimeoutKey"
#define WVClientIdKey @"WVClientIDKey"
#define WVPortalKey @"WVPortalKey"
#define WVStorefrontKey @"WVStorefrontKey"

typedef WViOsApiStatus (*WViOsApiStatusCallback)( WViOsApiEvent event, NSDictionary *attributes );

WViOsApiStatus WV_Initialize(const WViOsApiStatusCallback callback, NSDictionary *settings );
WViOsApiStatus WV_Terminate();
WViOsApiStatus WV_SetUserData( NSString *userData );
WViOsApiStatus WV_SetCredentials( NSDictionary *settings );
WViOsApiStatus WV_RegisterAsset (NSString *asset);
WViOsApiStatus WV_UnregisterAsset (NSString *asset);
WViOsApiStatus WV_QueryAssetsStatus ();
WViOsApiStatus WV_QueryAssetStatus (NSString *asset );
WViOsApiStatus WV_NowOnline ();
WViOsApiStatus WV_Play (NSString *asset, NSMutableString *url, NSData *authentication );
WViOsApiStatus WV_Stop ();
WViOsApiStatus WV_SelectBitrateTrack( int trackNumber );
NSString *WV_GetDeviceId();
    
NSString *NSStringFromWViOsApiEvent( WViOsApiEvent );


#ifdef __cplusplus
};
#endif
        
#endif
