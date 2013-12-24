
----------
 
# IX-9.7.0 #

## new features: ##
- Kaltura live platform
 - Kaltura live stream
 - Live Transcoding
 - DVR
 - Recording
 - Clipping
- File assets (for ui-confs)
- Internal indication for api time properties and support for times that are relative to "now()"


## API: ##

- New `liveStream.appendRecording` action
- New `mediaServer` service.
- New enum `KalturaConversionProfileType` for `KalturaConversionProfile.type`


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


**Relative Time Properties**
- default "max_relative_time" is set to 315360000 (10 years), times under 10 years would be converted as relative to now.
- Relative time conversion can be disabled for certain partners by modifying local.ini and adding

```
[disable_relative_time_partners]
0 = PID1
1 = PID2
```



## Deployment: ##

**Media server**

- [Media server configuration guide](doc/media-server-config.md "Media server configuration guide")

**Permissions**

- deployment/updates/scripts/add_permissions/2013_09_29_make_isLive_allways_allowed.php
- deployment/updates/scripts/add_permissions/2013_10_17_wowza_live_conversion_profile.php
- deployment/updates/scripts/add_permissions/2013_10_20_media_server.php
- deployment/updates/scripts/add_permissions/2013_10_23_liveStream_mediaServer.php
- deployment/updates/scripts/add_permissions/2013_11_13_liveStream_appendRecording.php
- deployment/updates/scripts/add_permissions/2013_11_14_media_update_content.php
- deployment/updates/scripts/add_permissions/2013_11_28_liveStream_validateRegisteredMediaServers.php
- deployment/updates/scripts/add_permissions/2013_12_08_media_approve_replace.php

**DB**

- Create media_server table using deployment/updates/sql/2013_10_17_create_media_server_table.sql
- Add conversion_profile_2.type column using deployment/updates/sql/2013_10_29_add_type_column_to_conversion_profile_table.sql
- Add media partner using deployment/updates/sql/2013_11_13_create_media_partner.sql
- Create media server partner permissions - deployment/updates/sql/2013_11_18_create_media_partner_permissions.sql


**Shared Content**

- Install LiveParams using deployment/updates/scripts/2013_10_27_create_live_params.php


**Default Content**

- Create live conversion profiles to existing partners -  deployment/updates/scripts/2013_11_20_create_live_profiles.php
- Create source-only live conversion profile to existing partners -  deployment/updates/scripts/2013_12_16_create_live_passthru_profile.php


**Plugins and Client libraries**

- Reinstall plugins using deployment/base/scripts/installPlugins.php.
- Regenerate clients.

----------
