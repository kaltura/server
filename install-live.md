API:
======

 - New MediaServer service.
 - New enum KalturaConversionProfileType for KalturaConversionProfile.type


Configuration:
======

 - Add Wowza wo plugins.ini and reinstall plugins.
 - Add admin.ini new permissions:
   - FEATURE_LIVE_STREAM_RECORD
   - FEATURE_KALTURA_LIVE_STREAM

Deployment:
======

 - Install Wowza according to technical design configuration instructions.
 - Install LiveParams using deployment/updates/scripts/2013_10_27_create_live_params.php
 - Create media_server table using deployment/updates/sql/2013_10_17_create_media_server_table.sql
 - Add conversion_profile_2.type column using deployment/updates/sql/2013_10_29_add_type_column_to_conversion_profile_table.sql
 - Add permissions:
   - deployment/updates/scripts/add_permissions/2013_09_29_make_isLive_allways_allowed.php
   - deployment/updates/scripts/add_permissions/2013_10_17_wowza_live_conversion_profile.php
   - deployment/updates/scripts/add_permissions/2013_10_20_media_server.php
   - deployment/updates/scripts/add_permissions/2013_10_23_liveStream_mediaServer.php
 - Reinstall plugins using deployment/base/scripts/installPlugins.php.
 - Regenerate clients.
