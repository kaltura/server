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

