# IX-9.20.0 #

## Live recording optimization ##
Record all live assets and manage the recording on the API server side.

- Issue Type: Change Request 
- Issue ID: PLAT-1367
- Issue ID: PLAT-1274
- Issue ID: PLAT-1476
- Issue ID: SUP-2202

#### Configuration ####
- `base.ini` already changed to support `max_live_recording_duration_hours` of 24 hours.
- `[api_strict_error_map]` section was added to `base.ini` to define strict error results for login services 

#### Media-Server version ####
- New media-server version [3.0.9](https://github.com/kaltura/media-server/releases/download/rel-3.0.9/KalturaWowzaServer-3.0.9.jar "3.0.9") required. 

#### Deployment Scripts ####
None

#### DB Changes ####

		deployment/updates/sql/2014_14_07_permission_getcurrentpermissions_remove_widget_permission.sql

#### Known Issues & Limitations ####
- The recording duration is limited to 24 hours.


# IX-9.19.0 #

## Add ENTRY_CHANGED email notification template ##
- Issue Type: Customer Request
- Issue ID: PLAT-1442

#### Scripts ####

		php /deployment/updates/scripts/2014_06_15_add_entry_changed_email_notification.php
#### Configurations ####

**Local.ini**

api_cache_warmup_host = 127.0.0.1  
html5lib_host = 127.0.0.1


## Watermark support ##
- Issue Type: PLAT-1510

#### Objective:
To provide static watermark support. The watermark definitions will be defined on a specific flavor params, and will be applied to all assets generated with this flavor.

#### Description:
Added 'watermarkData' field to flavor params object (stored in a customData). This field will store following structure as a JSON string:
- imageEntry - (optional),an image entry that will be used as a watermark image. Supported - PNG and JPG. Transparent alpha layer (PNG only) is supported.
- url - (optional), external url for the watermark image file. Formats same as above. Either 'imageEntry' or 'url' must be provided
- margins - (optional), 'WxH', distance from the video frame borders. Positive numbers refer to LeftUp corner, negative to RightDown corner of the video frame. If omitted - LeftUp is assumed. (Example - '-100x10'- 100pix from right side, 10 pixs from the upper side)
- opacity -  (optional) - 0-1.0 range. Defines the blending level between the watermark image and the video frame. if omitted teh watermark is presented un-blended.
- scale - (optional), 'WxH' - scale the water mark image to the given size. If one of the dimensions is omitted, it is calculated to preserve the watermark image aspect ratio.
​

#### Limitations:
The combination of transparent waternark with opacity does not work properly.

#### Sample watermark setup:
{"imageEntry":"0_yn0vivhl","margins":"-100x10","opacity":"0.5","scale":"0x250"}

## Mutli-audio stream support ##
- Issue Type: PLAT-1510

#### Objective:
To support input multi-stream detection and mapping.

#### Description:
Using an existing (but unused) flavorParams::multiStream field to store an optional configuration structure as a JSON string.
If omitted ‘layout detection logic’ is used to detect whether it has a ‘known layout’.
Currently supported - ‘audio surround layout’. If detected, the FR and FL streams are merged into a stereo source stream.

#### Configuration structure
- detect - (optional),

-   -- ‘auto’ - use internal logic to detect the source stream layout. All other fields are ignored.
-   --  TBD - hinting the detection logic of the source stream layout (for example - ‘languages’,’surround’)
- audio (optional) - description of either a single target audio stream or an array of target audio streams -

-   -- mapping - array of input streams to be mapped in. ffmpeg style multi file source mapping notation might be used (aka. lecture-captured files, not-implemented)

-   -- action - (optional) required processing action
-   --- ‘merge’ (default)
-   --- ‘concat’ (optional,not-implemented)
-   -- output (optional,not-implemented) - output stream mapping
- video (optional, not-implemented)

#### Sample multi-stream configuration stream:
{"audio":{"mapping":[1,2]}}


## Live Cue-Point support ##
Support cue-point on absolute server time stamp.


# IX-9.18.0 #

## Event Cue point support ##
- Issue ID: PLAT-1136

#### Configuration Files ####

The following rewrite rules should be added to kaltura's apache configuration file:

In `^p/[-0-9]+/sp/[-0-9]+/` section:

	RewriteRule ^p/[-0-9]+/sp/[-0-9]+/serveManifest/(.*)$ /index.php/extwidget/serveManifest/$1 [L]

In `^s/p/[-0-9]+/sp/[-0-9]+/` section:

	RewriteRule ^s/p/[-0-9]+/sp/[-0-9]+/serveManifest/(.*)$ /index.php/extwidget/serveManifest/$1 [L]

In `^p/[-0-9]+/` section:

	RewriteRule ^p/[-0-9]+/serveManifest/(.*)$ /index.php/extwidget/serveManifest/$1 [L]

In `^s/p/[-0-9]+/serveManifest/` section:

	RewriteRule ^s/p/[-0-9]+/serveManifest/(.*)$ /index.php/extwidget/serveManifest/$1 [L]

##### Enable the plugin #####

1. Add the following line to plugins.ini:

		EventCuePoint

2. Install plugins:

		php deployment/base/scripts/installPlugins.php

# IX-9.17.0 #

## 'Content Moderator' user-role permissions fix ##
- Issue Type: Bug fix
- Issue ID: SUP-2000

#### Permissions ####

		php /deployment/updates/scripts/add_permissions/2014_05_11_add_permissions_to_CONTENT_MODERATE_BASE.php

## Add Tvinci Distribution Profile ##
- Issue Type: New Feature
- Issue ID: PLAT-1352

#### Configuration Files ####

##### Enable the plugin #####
1. Add the following line to plugins.ini:

		TvinciDistribution

2. Install plugins:

		php deployment/base/scripts/installPlugins.php

##### Add custom metadata schema #####
1. Add the following lines from admin.template.ini to admin.ini,
   right after the "moduls.pushPublish.permissionName = FEATURE_PUSH_PUBLISH" block:

		moduls.tvinciIngestV1.enabled = true
		moduls.tvinciIngestV1.permissionType = 2
		moduls.tvinciIngestV1.label = Enable Tvinci Ingest v1
		moduls.tvinciIngestV1.permissionName = FEATURE_TVINCI_INGEST_V1
		moduls.tvinciIngestV1.basePermissionType =
		moduls.tvinciIngestV1.basePermissionName =
		moduls.tvinciIngestV1.group = GROUP_ENABLE_DISABLE_FEATURES

		moduls.tvinciIngestV2.enabled = true
		moduls.tvinciIngestV2.permissionType = 2
		moduls.tvinciIngestV2.label = Enable Tvinci Ingest v2
		moduls.tvinciIngestV2.permissionName = FEATURE_TVINCI_INGEST_V2
		moduls.tvinciIngestV2.basePermissionType =
		moduls.tvinciIngestV2.basePermissionName =
		moduls.tvinciIngestV2.group = GROUP_ENABLE_DISABLE_FEATURES

2. Run the following script, which will add the custom metadata schema to partner 99:

		php alpha/scripts/utils/addTvinciIngestSchemasToPartner99.php realrun

   * note the 'realrun' parameter

# IX-9.16.0 #

## Multicast eCDN ##

## Job suspension ##


# IX-9.15.0 #

## Disable email notification for new KMC user creation ##

## Live streaming provision should exclude cloud transcode where not available ##

# IX-9.14.0 #

## Image magick ##
* In case of GS failure that indicates a corrupted file, update the flavor accordingly. 

## Admin Console security tightening ##

*Configuartion Changes*
Update admin.ini:

- Remove the line (will be readded in the next item)

        settings.cookieNameSpace = Zend_Auth_AdminConsole"

- Add the following block right under the "settings.enableKCWVisualEditor" line:

        ; cookie options
        settings.cookieNameSpace = Zend_Auth_AdminConsole
        settings.secure_cookie_upon_https = true
        settings.sessionOptions.cookie_httponly = true
		
## TagIndex job ##
* Enable TagIndex job

*Configuartion Changes*
- Update batch.ini and workers.ini, done on saas tag (added KAsyncTagIndex)

*Data update*
- Before applying the batch configuration delete all pending jobs from batch_job_lock, will be done by Eran K. 

## Admin Console "View History" Permission Fix ##

*Permissions*

- deployment/updates/scripts/add_permissions/2014_03_09_add_system_admin_publisher_config_to_audittrail.php

---

# IX-9.13.0 #

## Live sync points ##
Enable sending periodic live sync points on Kaltura live stream.

*Permissions*

- deployment/updates/scripts/add_permissions/2014_03_09_live_stream_create_sync_points.php

*Media Server*

- Version 3.0.3 [KalturaWowzaServer.jar](https://github.com/kaltura/media-server/releases/download/rel-3.0.3/KalturaWowzaServer-3.0.3.jar "KalturaWowzaServer.jar")
 
## Play Ready ##
- 1. upgrade PR license server to v2.9
- 2. device registration flow

*DB Changes*
- /deployment/updates/sql/2014_03_04_update_drm_device_table.sql

*PR license server update*
- 1. clone git repository: https://github.com/kaltura/playready-server
- 2. copy dll's from PlayReadyLicenseServer/vdir/bin to the license server under: C:\Program Files\PlayReady Server SDK 2.9\vdir\bin
- 3.  update web.xml - add <add key="RemoteAddrHeaderSalt" value="@REMOTE_ADDR_HEADER_SALT_LOCAL_INI@" /> under appSettings. Change @REMOTE_ADDR_HEADER_SALT_LOCAL_INI@ to the value of remote_addr_header_salt in local.ini
- 4. restart IIS

*Configuartion Changes*
- update batch.ini/worker.ini
	- add under KAsyncConvertWorker params.ismIndexCmd = @BIN_DIR@/ismindex
	- update under KAsyncConvert filter.jobSubTypeIn = 1,2,99,3,fastStart.FastStart,segmenter.Segmenter,mp4box.Mp4box,vlc.Vlc,document.ImageMagick,201,202,quickTimeTools.QuickTimeTools,ismIndex.IsmIndex,ismIndex.IsmManifest
	- Add KAsyncConvertSmoothProtect worker section, place it following other Windows transcoding workers.
		- [KAsyncConvertSmoothProtect: KAsyncDistributedConvert] 
		- id = $WORKER_ID 
		- baseLocalPath = $BASE_LOACL_PATH 
		- params.sharedTempPath = $SHARED_TEMP_PATH 
		- filter.jobSubTypeIn = smoothProtect.SmoothProtect 
		- params.smoothProtectCmd = $SMOOTHPROTECT_BIN 
		- params.isRemoteOutput = $IS_REMOTE_OUTPUT 
		- params.isRemoteInput = $IS_REMOTE_INPUT 
	- $WORKER_ID – set to match existing Testing QA settings 
	- $BASE_LOACL_PATH – follow other windows workers (aka Webex worker) 
	- $SHARED_TEMP_PATH – follow other windows workers (aka Webex worker) 
	- $SMOOTHPROTECT_BIN – full path to the 'smoothprotect.exe', typically '/opt/kaltura/bin/smoothprotect' 
	- $IS_REMOTE_OUTPUT – should match other Windows workers (aka Webex worker) 
	- $IS_REMOTE_INPUT – should match other Windows workers (aka Webex worker)

*Binaries*
- Linux
	- Install ffmpeg binary and ismindex binary from - http://ny-www.kaltura.com/content/shared/bin/ffmpeg-2.1.3-bin.tar.gz
	- Switch the ffmpeg allias to work with the new ffmpeg-2.1.3
	- The ffmpeg-aux remains unchanged. 
- Windows
	- Install 'SmoothProtect.exe' binary

## H265/FFmpeg 2.2 ##

*Binaries*
- Install ffmpeg-2.2 from http://ny-www.kaltura.com/content/shared/bin/ffmpeg-2.2-bin.tar.gz
- Don't assign ffmpeg-2.2 to neither 'ffmpeg' nor to 'ffmpeg-aux'

## Multicast ##

*Permissions*

- deployment/updates/scripts/2014_03_10_addpushpublishconfigurationaction_added_to_livestreamservice.php


## YouTube Captions Upload via SFTP ##

*Permissions*

* deployment/updates/scripts/add_permissions/2014_03_11_add_filesync_list_to_batch_partner.php

----

## Allow "View History" for any Admin Console users (revisited) ##
The monitor's View History permission is lowered from System Admin user to any Admin Console user.

- update admin.ini:
access.partner.configure-account-options-monitor-view = SYSTEM_ADMIN_PUBLISHER_CONFIG
access.partner.exteneded-free-trail-history = SYSTEM_ADMIN_PUBLISHER_CONFIG

*Permissions*

- deployment/updates/scripts/add_permissions/2014_03_09_add_system_admin_publisher_config_to_audittrail.php

## Fix duplicate permission names in Admin Console ##
Many Admin Console permissions carry the same name.
Run the following SQL script in order to make them unique:

*DB Changes*

- deployment/updates/sql/2014_03_19_fix_admin_console_permission_names.sql


# IX-9.12.0 #

## Remove limitation of 32 categories per entry##
The limitation will be removed for partners that have a Disable Category Limit feature enabled.

*Configuration Changes*

- update admin.ini:
add
moduls.categoryLimit.enabled = true
moduls.categoryLimit.permissionType = 2
moduls.categoryLimit.label = Disble Category Limit
moduls.categoryLimit.permissionName = FEATURE_DISABLE_CATEGORY_LIMIT
moduls.categoryLimit.basePermissionType =
moduls.categoryLimit.basePermissionName =
moduls.categoryLimit.group = GROUP_ENABLE_DISABLE_FEATURES
 
- update batch.ini
add 
enabledWorkers.KAsyncSyncCategoryPrivacyContext		= 1
enabledWorkers.KAsyncTagIndex						= 1
  
[KAsyncSyncCategoryPrivacyContext : JobHandlerWorker]
id													= 530
friendlyName										= Sync Category Privacy Context
type												= KAsyncSyncCategoryPrivacyContext
maximumExecutionTime								= 12000
scriptPath											= batches/SyncCategoryPrivacyContext/KAsyncSyncCategoryPrivacyContextExe.php

[KAsyncTagIndex : JobHandlerWorker]
id													= 500
friendlyName										= Re-index tags
type												= KAsyncTagIndex
maximumExecutionTime								= 12000
scriptPath											= ../plugins/tag_search/lib/batch/tag_index/KAsyncTagIndexExe.php

*Permissions*

- /deployment/updates/scripts/add_permissions/2014_01_20_categoryentry_syncprivacycontext_action.php

*Migration*
- /alpha/scripts/utils/setCategoryEntriesPrivacyContext.php realrun

## New FFMpeg 2.1.3##
 *Binaries*
  - Linux
 -- -Install the new ffmpeg 2.1.3 as a 'main' ffmpeg - http://ny-www.kaltura.com/content/shared/bin/ffmpeg-2.1.3-bin.tar.gz
 -- -The ffmpeg-aux remains unchanged.

## Create Draft Entries as Ready ##
Assign a Ready status to draft entries that were created using a conversion profile which contains no flavor params.

- update admin.ini

	moduls.draftEntryConversionProfileSelection.enabled = true
	moduls.draftEntryConversionProfileSelection.permissionType = 2
	moduls.draftEntryConversionProfileSelection.label = Enable KMC transcoding profile selection for draft entries
	moduls.draftEntryConversionProfileSelection.permissionName = FEATURE_DRAFT_ENTRY_CONV_PROF_SELECTION
	moduls.draftEntryConversionProfileSelection.basePermissionType =
	moduls.draftEntryConversionProfileSelection.basePermissionName =
	moduls.draftEntryConversionProfileSelection.group = GROUP_ENABLE_DISABLE_FEATURES

## Allow "View History" for any Admin Console users ##
The monitor's View History permission is lowered from System Admin user to any Admin Console user.      

- update admin.ini:
<br>access.partner.configure-account-options-monitor-view = SYSTEM_ADMIN_BASE 

## Support hybrid eCDN architecture

- Update scripts
	
	/opt/kaltura/app/deployment/updates/scripts/add_permissions/2014_01_26_add_media_server_partner_level_permission.php
	/opt/kaltura/app/deployment/updates/scripts/add_permissions/2014_02_25_add_push_publish_permission_to_partner_0.php
	/opt/kaltura/app/deployment/updates/scripts/add_permissions/2014_01_26_update_live_stream_service_permissions.php
	/opt/kaltura/app/deployment/updates/scripts/add_permissions/2014_02_25_add_push_publish_permission_to_live_asset_parameters.php
	/opt/kaltura/app/deployment/updates/scripts/add_permissions/2014_02_25_add_push_publish_permission_to_live_entry_parameters.php

- Update admin.ini

	moduls.hybridCdn.enabled = true
	moduls.hybridCdn.permissionType = 2
	moduls.hybridCdn.label = Hybrid CDN
	moduls.hybridCdn.permissionName = FEATURE_HYBRID_ECDN
	moduls.hybridCdn.basePermissionType = 2
	moduls.hybridCdn.basePermissionName = FEATURE_KALTURA_LIVE_STREAM
	moduls.hybridCdn.group = GROUP_ENABLE_DISABLE_FEATURES
	
	moduls.pushPublish.enabled = true
	moduls.pushPublish.permissionType = 2
	moduls.pushPublish.label = Push Publish Feature
	moduls.pushPublish.permissionName = FEATURE_PUSH_PUBLISH
	moduls.pushPublish.basePermissionType = 2
	moduls.pushPublish.basePermissionName = FEATURE_HYBRID_ECDN
	moduls.pushPublish.group = GROUP_ENABLE_DISABLE_FEATURES

- Update local.ini

	uploaded_segment_destination = @WEB_DIR@/tmp/convert/

----------

# IX-9.11.0 #

## Remove limitation of 32 categories per entry - db changes only##

*DB Changes*

- /deployment/updates/sql/2014_01_19_category_entry_add_privacy_context.sql


## PlayReady, ISM Index, Smooth Protect##

*DB Changes*

- /deployment/updates/sql/2014_02_09_change_drm_key_key_column_name.sql

*Configuration Changes*
- update plugins.ini
  add plugins: PlayReady, SmoothProtect
  
- update admin.ini:
add
moduls.drmPlayReady.enabled = true
moduls.drmPlayReady.permissionType = 3
moduls.drmPlayReady.label = DRM - PlayReady
moduls.drmPlayReady.permissionName = PLAYREADY_PLUGIN_PERMISSION
moduls.drmPlayReady.basePermissionType = 3
moduls.drmPlayReady.basePermissionName = DRM_PLUGIN_PERMISSION
moduls.drmPlayReady.group = GROUP_ENABLE_DISABLE_FEATURES

- update batch.ini
1. add under KAsyncConvertWorker 
params.ismIndexCmd									= @BIN_DIR@/ismindex
2. update under KAsyncConvert
filter.jobSubTypeIn	= 1,2,99,3,fastStart.FastStart,segmenter.Segmenter,mp4box.Mp4box,vlc.Vlc,document.ImageMagick,201,202,quickTimeTools.QuickTimeTools,ismIndex.IsmIndex,ismIndex.IsmManifest
3. Add KAsyncConvertSmoothProtect  worker section, place it following other Windows  transcoding workers.
	[KAsyncConvertSmoothProtect: KAsyncDistributedConvert]
	id                       = $WORKER_ID
	baseLocalPath            = $BASE_LOACL_PATH
	params.sharedTempPath    = $SHARED_TEMP_PATH
	filter.jobSubTypeIn	 = smoothProtect.SmoothProtect
	params.smoothProtectCmd  = $SMOOTHPROTECT_BIN
	params.isRemoteOutput    = $IS_REMOTE_OUTPUT
	params.isRemoteInput     = $IS_REMOTE_INPUT
	• $WORKER_ID – set to match existing Testing QA settings
	• $BASE_LOACL_PATH – follow other windows workers (aka Webex worker)
	• $SHARED_TEMP_PATH – follow other windows workers (aka Webex worker)
	• $SMOOTHPROTECT_BIN – full path to the 'smoothprotect.exe', typically '/opt/kaltura/bin/smoothprotect'
	• $IS_REMOTE_OUTPUT – should match other Windows workers (aka Webex worker)
	• $IS_REMOTE_INPUT – should match other Windows workers (aka Webex worker)
4. Add 'worker enabler' to template section of your Windows server:  
	• enabledWorkers.KAsyncConvertSmoothProtect  = 1

- create playReady.ini from playReady.template.ini
change @PLAYREADY_LICENSE_SERVER_HOST@ to the relevant host 

*Scripts*
- run installPlugins

*Permissions*
- deployment/updates/scripts/add_permissions/2013_10_22_add_drm_policy_permissions.php

*Binaries*
- Linux
- -Install ismindex  from - http://ny-www.kaltura.com/content/shared/bin/ffmpeg-2.1.3-bin.tar.gz
- -The ffmpeg and ffmpeg-aux remains unchanged. The ffmpeg will be switched to the new version on the next deployment.
- Windows
- -Install 'SmoothProtect.exe' binary



----------

# IX-9.10.0 #


## Enhanced media server logging level ##

**Configuration**

*Edit @WOWZA_DIR@/conf/log4j.properties:*

 - Change `log4j.rootCategory` = `INFO, stdout, serverAccess, serverError` 
 - Remove `log4j.category.KalturaServer.class`
 - Add `log4j.logger.com.kaltura` = `DEBUG`
 - Change `log4j.appender.serverAccess.layout.ConversionPattern` = `[%d{yyyy-MM-dd HH:mm:ss}][%t][%C:%M] %p - %m - (%F:%L) %n` 
 - Change `log4j.appender.serverError.layout.ConversionPattern` = `[%d{yyyy-MM-dd HH:mm:ss}][%t][%C:%M] %p - %m - (%F:%L) %n` 


## Live stream multiple flavors ingestion ##

Enable streaming more than one source.

**Deployment:**

*Shared Content*

- Add source LiveParams using deployment/updates/scripts/2014_01_14_add_ingest_live_params.php

*Media Server*

- Change transcoding template to `http://@WWW_HOST@/api_v3/index.php/service/wowza_liveConversionProfile/action/serve/streamName/${SourceStreamName}/f/transcode.xml`



## Entry redirect moderation ##
The moderation status is copied to the redirected entry from the original entry when the redirect defined.

## Media Server - support multiple sources ingestion ##

*Permissions*

- deployment/updates/scripts/2014_01_14_add_ingest_live_params.php
- deployment/updates/scripts/add_permissions/2014_01_14_conversion_profile_asset_params_media_server.php
- deployment/updates/scripts/add_permissions/2014_01_21_media_server_partner_live.php


## Media Server - DVR with edge-origin ##
Fixed broadcast path to use query string instead of slashed parameters.

*Data Migration*

- deployment/updates/scripts/2014_01_22_fix_broadcast_urls.php

*Permissions*

- deployment/updates/scripts/add_permissions/2014_01_22_live_stream_entry_broadcast_url.php

## PlayReady, ISM Index, Smooth Protect - regression only ##
Initial version of PlayReady, Ism Index and Smooth Protect. PlayReady and SmoothProtect plugins will not be activated. 
This version deployed for regression purposes only.

*DB Changes*

- deployment/updates/sql/2013_10_22_add_drm_policy_table.sql
- deployment/updates/sql/2013_12_10_add_drm_device_table.sql
- deployment/updates/sql/2013_12_31_add_drm_key_table.sql
- deployment/updates/sql/2014_01_14_audit_trail_config_admin_console_partner_updates.sql

*Configuration Changes*
- update plugins.ini
  add IsmIndex plugin

*Scripts*
- run installPlugins

*Permissions*

- deployment/updates/scripts/add_permissions/2013_10_22_add_drm_policy_permissions.php
- deployment/updates/scripts/add_permissions/2013_12_10_add_drm_device_permissions.php 

----------

# IX-9.9.0 #

## Media Server - live stream recording ##

*Permissions*

- deployment/updates/scripts/add_permissions/2014_01_15_conversionprofileassetparams_permission_media_partner.php

----------
 
# IX-9.8.0 #

## Update live-params permissions ##

Enable only to partners with live-stream permission to list live-params as part of flavor-params lists.

**Deployment:**

*Permissions*

- deployment/updates/scripts/2014_01_12_update_live_params_permissions.php

## VOD to Live ##
Demo version only, enables broadcasting a live-channel base on playlist.

**Deployment:**

*Permissions*

- deployment/updates/scripts/add_permissions/2014_01_01_live_channel_services.php

*DB*

- Add live_channel_segment table - deployment/updates/sql/2014_01_01_create_live_channel_segment_table.sql


*Media Server*
- Update  [KalturaWowzaServer.jar](https://github.com/kaltura/server-bin-linux-64bit/raw/master/wowza/KalturaWowzaServer-2.0.1.jar "KalturaWowzaServer.jar")


*Configuration*

- Add FEATURE_LIVE_CHANNEL permission according to admin.template.ini.
- Update Bulkupload worker configuration. Added parameters sharedTempPath and fileOwner. The value for sharedTempPath is /web/tmp/bulkupload and needs to be created on the machine.
 

*File System*

- Create a symbolic link of @WEB_DIR@/content under @WEB_DIR@/content/recorded:
  ln –s @WEB_DIR@/content @WEB_DIR@/content/recorded/content 
 



## Enforce max concurrent streams ##
- New partner configuration fields in admin console.
- New API action liveStream.authenticate.
- New media server version - 1.1.0

**Deployment:**

*Permissions*

- deployment/updates/scripts/add_permissions/2013_12_30_liveStream_authenticate.php

*Media Server*

- Redeploy [KalturaWowzaServer.jar](https://github.com/kaltura/server-bin-linux-64bit/raw/master/wowza/KalturaWowzaServer.jar "KalturaWowzaServer.jar") to @WOWZA_DIR@/lib/






## Admin console boost entry jobs ##
A new button was added to the Admin page which allows you to boost the jobs of the entry.

**Deployment:**

*Permissions*

- deployment/updates/scripts/add_permissions/2013_12_03_jobs_service.php




## KAsyncFileSyncImport - use HTTP keep-alive ##
By adding this optimization we now can use the same curl handle to import multiple files.
There is no creation of new handle per file as before.




## FilesyncImport - increase priority for source flavors ##
From now on source asset file sync import jobs will have higher urgency and priority than others. This was added so we could start the convert process quicker.




## Limit amount of assets alowed per entry ##
A limit was added to the amount of assets that each entry can contain.
By default the limitation is set to 500 but this could be configured per partner based if needed by calling "setAssetsPerEntryLimitation" on the partner.




## Add flavor required for intermediate flow to flavorParams.ini ##
Add support for intermediate flow to on-prem installations as well.



## Entry - last played at ##

*Core:*
 
- Added `last_played_at` to entry table.

*API:*

- New field `lastPlayedAt` for `KalturaPlayableEntry`

*Sphinx:*

- New date attribute `last_played_at` for `kaltura_entry`


**Deployment:**

*DB*

- Add last_played_at to entry table - deployment/updates/sql/2013_12_19_entry_add_last_played_at.sql

*Sphinx*

- Update configurations/sphinx/kaltura.conf according to template.
- Repopulate sphinx entries


## Bulk Upload from Filter – infrastructure ##

Deployment instructions:

1. Update the code and clients
2. Update plugins.ini – add BulkUploadFilter plugin
3. Run installPlugins.php





## HTML5 Studio Deployment ##
* Located the studio directory: @BASE_DIR@/apps/studio/ (create it if it doesn't exist)
	* The directory owner should be apache and its group should be kaltura.
* Create a sub directory within the studio folder. Name it by the version of the studio (for example: v0.1)
* Fetch latest studio project files into apps/studio/v0.1 from https://github.com/kaltura/player-studio/releases.
* Open the file studio.ini (within the studio project files) and update "html5_version" to include the rc version.
* Execute deployment script on studio.ini file (located in studio project root):
From studio root, run: php /opt/kaltura/app/deployment/uiconf/deploy_v2.php --ini=studio.ini

## Fixed a security hole in media.addFromUploaded file ##
Restricting webcam and uploaded to their designated directories and blocking attempts to access outer directories, with ../../some_sensitive_data_file for example.

## Fixed Animated GIF thumbnail cropping ##
Bug fix: When cropping a .gif thumbnail, black margins appear around the crop are not removed.
Bug fix: File extension of downloaded thumbnails is hardcoded to .jpg instead of the original file's ext.

##  Client libraries update
Part of PLAT-528.
The updated client libraries are - 

- java
- php53
- phpzend
- python
- ruby

The change included the following - 

1. Changed client libraries to have a fallback class in case of object de-serialization. supported both for regular request and multi request. 
2.  Check the http return code and throw an exception in case it isn't 200

##  Batch changes
Contains the following improvements:

1. Don't create lock object if not needed (#plat-718)
2. Use less save commands when creating a new batch (#PLAT-661)


## Sphinx
Merged into the code changes that were hot-fixed at the beginning of the sprint. Including :

- Addition of 'getObjectName' and use it in fixing field name
- Numerical ordering of Json attributes. 

## Minor issues

- #PLAT-526: Sort the event consumers alphabetically if not requested otherwise.
- #PLAT-681: In case an empty ui-conf filter is used, filter at least by the partner
- #PLAT-489: Extract delayed job types to kconf. <b><u> requires updateding base.ini </u></b>

---------
 
# IX-9.7.0 #

## Kaltura live platform ##
- Kaltura live stream
- Live Transcoding
- DVR
- Recording
- Clipping

**API:**

- New `liveStream.appendRecording` action
- New `mediaServer` service.
- New enum `KalturaConversionProfileType` for `KalturaConversionProfile.type`



**Configuration:**

*File Sync*

Add new file sync exclusions to dc_config.ini based on dc_config.template.ini.  

- FILE_SYNC_ENTRY_SUB_TYPE_LIVE_PRIMARY = 1:10 
- FILE_SYNC_ENTRY_SUB_TYPE_LIVE_SECONDARY = 1:11 


*Batch*

Add new workers to batch.ini based on batch.ini.template.

- DirectoryCleanupRecordedMedia
- KAsyncConvertLiveSegment
- KAsyncConcat
- KAsyncValidateLiveMediaServers


**Deployment:**

*Media server*

- [Media server configuration guide](doc/media-server-config.md "Media server configuration guide")

*Permissions*

- deployment/updates/scripts/add_permissions/2013_09_29_make_isLive_allways_allowed.php
- deployment/updates/scripts/add_permissions/2013_10_17_wowza_live_conversion_profile.php
- deployment/updates/scripts/add_permissions/2013_10_20_media_server.php
- deployment/updates/scripts/add_permissions/2013_10_23_liveStream_mediaServer.php
- deployment/updates/scripts/add_permissions/2013_11_13_liveStream_appendRecording.php
- deployment/updates/scripts/add_permissions/2013_11_14_media_update_content.php
- deployment/updates/scripts/add_permissions/2013_11_28_liveStream_validateRegisteredMediaServers.php
- deployment/updates/scripts/add_permissions/2013_12_08_media_approve_replace.php

*DB*

- Create media_server table using deployment/updates/sql/2013_10_17_create_media_server_table.sql
- Add conversion_profile_2.type column using deployment/updates/sql/2013_10_29_add_type_column_to_conversion_profile_table.sql
- Add media partner using deployment/updates/sql/2013_11_13_create_media_partner.sql
- Create media server partner permissions - deployment/updates/sql/2013_11_18_create_media_partner_permissions.sql

*Shared Content*

- Install LiveParams using deployment/updates/scripts/2013_10_27_create_live_params.php


*Default Content*

- Create live conversion profiles to existing partners -  deployment/updates/scripts/2013_11_20_create_live_profiles.php
- Create source-only live conversion profile to existing partners -  deployment/updates/scripts/2013_12_16_create_live_passthru_profile.php


*Plugins and Client libraries*

- Reinstall plugins using deployment/base/scripts/installPlugins.php.
- Regenerate clients.






## File assets (for ui-confs) ##
New file assets core object and API service

**Deployment:**

*DB*

- Create file_asset table using deployment/updates/sql/2013_11_07_file_asset_table.sql

*Permissions*

- deployment/updates/scripts/add_permissions/2013_11_07_file_asset_service.php







## API - Relative time ##
Internal indication for api time properties and support for times that are relative to "now()"


**Configuration**
- default "max_relative_time" is set to 315360000 (10 years), times under 10 years would be converted as relative to now.
- Relative time conversion can be disabled for certain partners by modifying local.ini and adding

`
[disable_relative_time_partners]
0 = PID1
1 = PID2
`

