 
# IX-live-1.1 #
Kaltura live stream recording and clipping.

## API: ##

 - New `liveStream.appendRecording` action

## Configuration: ##

**File Sync**

Add new file sync exclusions to dc_config.ini based on dc_config.template.ini.  

- FILE_SYNC_ENTRY_SUB_TYPE_LIVE_PRIMARY = 1:10 
- FILE_SYNC_ENTRY_SUB_TYPE_LIVE_SECONDARY = 1:11 


**Batch**

Add new workers to batch.ini based on batch.ini.template.

- DirectoryCleanupRecordedMedia
- KAsyncConvertLiveSegment
- KAsyncConcat
- KAsyncValidateLiveMediaServers

## Deployment: ##

**Permissions**

- deployment/updates/scripts/add_permissions/2013_11_13_liveStream_appendRecording.php
- deployment/updates/scripts/add_permissions/2013_11_14_media_update_content.php
- deployment/updates/scripts/add_permissions/2013_11_28_liveStream_validateRegisteredMediaServers.php
- deployment/updates/scripts/add_permissions/2013_12_08_media_approve_replace.php

**DB**

- Create media server partner permissions - deployment/updates/sql/2013_11_18_create_media_partner_permissions.sql

**Default Content**

- Create live conversion profiles to existing partners -  deployment/updates/scripts/2013_11_20_create_live_profiles.php


----------

# IX-live-1.0 #
Kaltura live stream, including DVR configuration and live transcoding.

## API: ##

- New `mediaServer` service.
- New enum `KalturaConversionProfileType` for `KalturaConversionProfile.type`


## Media server: ##
- [Media server configuration guide](doc/media-server-config.md "Media server configuration guide")

## Deployment: ##
- Reinstall plugins using deployment/base/scripts/installPlugins.php.
- Regenerate clients.


**DB**

- Create media_server table using deployment/updates/sql/2013_10_17_create_media_server_table.sql
- Add conversion_profile_2.type column using deployment/updates/sql/2013_10_29_add_type_column_to_conversion_profile_table.sql
- Add media partner using deployment/updates/sql/2013_11_13_create_media_partner.sql

**Shared Content**

- Install LiveParams using deployment/updates/scripts/2013_10_27_create_live_params.php

**Permissions**

- deployment/updates/scripts/add_permissions/2013_09_29_make_isLive_allways_allowed.php
- deployment/updates/scripts/add_permissions/2013_10_17_wowza_live_conversion_profile.php
- deployment/updates/scripts/add_permissions/2013_10_20_media_server.php
- deployment/updates/scripts/add_permissions/2013_10_23_liveStream_mediaServer.php
