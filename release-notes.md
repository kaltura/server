
# IX-live-1.1 #
Kaltura live stream recording and clipping.

## API: ##

 - New `liveStream.appendRecording` action

## Configuration: ##

**Batch**

Add new workers to batch.ini based on batch.ini.template.

- DirectoryCleanupRecordedMedia
- KAsyncConvertLiveSegment
- KAsyncConcat

## Deployment: ##

**Permissions**

- deployment/updates/scripts/add_permissions/2013_11_13_liveStream_appendRecording.php
- deployment/updates/scripts/add_permissions/2013_11_14_media_update_content.php

**DB**

- Create media server partner permissions deployment/updates/sql/2013_11_18_create_media_partner_permissions.sql

----------

# IX-live-1.0 #
Kaltura live stream, including DVR configuration and live transcoding.

## API: ##

- New `mediaServer` service.
- New enum `KalturaConversionProfileType` for `KalturaConversionProfile.type`


## Configuration: ##

- Add Wowza to plugins.ini.
- Add admin.ini new permissions, see admin.template.ini:
 - FEATURE_LIVE_STREAM_RECORD
 - FEATURE_KALTURA_LIVE_STREAM
-  broadcast.ini according to broadcast.template.ini

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

**Wowza**

- Copy [KalturaWowzaServer.jar](https://github.com/kaltura/server-bin-linux-64bit/raw/master/KalturaWowzaServer.jar "KalturaWowzaServer.jar") to @WOWZA_DIR@/lib/
- Copy additional jar files (available in Kaltura Java client library) to @WOWZA_DIR@/lib/
 - commons-codec-1.4.jar
 - commons-httpclient-3.1.jar
 - commons-logging-1.1.1.jar 
- Delete all directories under @WOWZA_DIR@/applications, but not the applications directory itself.
- Create @WOWZA_DIR@/applications/kLive directory.
- Delete all directories under @WOWZA_DIR@/conf, but not the conf directory itself.
- Create @WOWZA_DIR@/conf/kLive directory.
- Copy @WOWZA_DIR@/conf/Application.xml to @WOWZA_DIR@/conf/kLive/Application.xml
- **Edit @WOWZA_DIR@/conf/kLive/Application.xml:**
 - /Root/Application/Streams/StreamType - live
 - /Root/Application/Streams/StorageDir - @WEB_DIR@/content/recorded
 - /Root/Application/Properties, add new properties:
     - HTTP origin mode
         - Name - httpOriginMode
         - Value - on
     - HTTP random media name
         - Name - httpRandomizeMediaName
         - Value - true
         - Type - Boolean
     - Apple HLS cache control playlist
         - Name - cupertinoCacheControlPlaylist
         - Value - max-age=1
     - Apple HLS cache control media chunk
         - Name - cupertinoCacheControlMediaChunk
         - Value - max-age=3600
     - Flash HDS cache control reset counter
         - Name - cupertinoOnChunkStartResetCounter
         - Value - true
         - Type - Boolean
     - Smooth Streaming cache control playlist
         - Name - smoothCacheControlPlaylist
         - Value - max-age=1
     - Smooth Streaming cache control media chunk
         - Name - smoothCacheControlMediaChunk
         - Value - max-age=3600
     - Smooth Streaming cache control data chunk
         - Name - smoothCacheControlDataChunk
         - Value - max-age=3600
     - Flash HDS cache control playlist
         - Name - sanjoseCacheControlPlaylist
         - Value - max-age=1
     - Flash HDS cache control media chunk
         - Name - sanjoseCacheControlMediaChunk
         - Value - max-age=3600
- **Edit @WOWZA_DIR@/conf/Server.xml:**
 - /Root/Server/ServerListeners/ServerListener/BaseClass - `com.kaltura.media.server.wowza.listeners.ServerListener`
 - /Root/Server/Properties, add new properties:
     - Kaltura API server URL
         - Name - KalturaServerURL
         - Value - http://@WWW\_DIR@
     - Kaltura media server partner (-5) admin secret
         - Name - KalturaServerAdminSecret
         - Value - @MEDIA_PARTNER_ADMIN_SECRET@
     - Kaltura API http timeout
         - Name - KalturaServerTimeout
         - Value - 30
     - Kaltura server managers to be loaded
         - Name - KalturaServerManagers
         - Value: comma separated:
             - `com.kaltura.media.server.wowza.StatusManager`
             - `com.kaltura.media.server.wowza.LiveStreamManager`
     - Kaltura web services to be loaded
         - Name - KalturaServerWebServices
         - Value: comma separated:
             - `com.kaltura.media.server.api.services.KalturaLiveService`
     - Kaltura server status reporting interval, in seconds
         - Name - KalturaServerStatusInterval
         - Value - 300
     - Kaltura interval to update that live stream entry is still broadcasting, in seconds
         - Name - KalturaLiveStreamKeepAliveInterval
         - Value - 60
     - Kaltura maximum DVR window, in seconds, should be 24 hours
         - Name - KalturaLiveStreamMaxDvrWindow
         - Value - 86400
     - Kaltura maximum recorded chunk duration, in minutes, should be an hour
         - Name - KalturaRecordedChunckMaxDuration
         - Value - 60
     - Kaltura web services http port
         - Name - KalturaServerWebServicesPort
         - Value - 888
     - Kaltura recorded files group name
         - Name - KalturaRecordedFileGroup
         - Value - kaltura (or apache if kaltura doesn't exist)
- **Edit @WOWZA_DIR@/conf/log4j.properties:**
 - Add `log4j.category.KalturaClientBase.class` = DEBUG
 - Change `log4j.appender.serverAccess.File` = @LOG_DIR@/kaltura\_mediaserver\_access.log
 - Change `log4j.appender.serverError.File` = @LOG_DIR@/kaltura\_mediaserver\_error.log
 - Change `log4j.appender.serverStats.File` = @LOG_DIR@/kaltura\_mediaserver\_stats.log
 - Comment out `log4j.appender.serverError.layout`
 - Add `log4j.appender.serverError.layout` = `org.apache.log4j.PatternLayout`
 - Add `log4j.appender.serverError.layout.ConversionPattern` = `[%d{yyyy-MM-dd HH:mm:ss}] %p - "%m" - (%F:%L) %n` 
 - Comment out `log4j.appender.serverAccess.layout`
 - Add `log4j.appender.serverAccess.layout` = `org.apache.log4j.PatternLayout`
 - Add `log4j.appender.serverAccess.layout.ConversionPattern` = `[%d{yyyy-MM-dd HH:mm:ss}] %p - "%m" - (%F:%L) %n`

     
*Origin:*

- **Edit @WOWZA_DIR@/conf/kLive/Application.xml:**
 - /Root/Application/Streams/LiveStreamPacketizers:
     - cupertinostreamingpacketizer
     - smoothstreamingpacketizer
     - sanjosestreamingpacketizer
     - mpegdashstreamingpacketizer
     - dvrstreamingpacketizer 
 - /Root/Application/Transcoder/LiveStreamTranscoder - transcoder
 - /Root/Application/Transcoder/Templates - `http://@WWW\_HOST@/api_v3/index.php/service/wowza_liveConversionProfile/action/serve/entryId/${SourceStreamName}/f/transcode.xml`
 - /Root/Application/DVR/Recorders - dvrrecorder
 - /Root/Application/DVR/Store - dvrfilestorage
 - /Root/Application/HTTPStreamers:
     - cupertinostreaming
     - smoothstreaming
     - sanjosestreaming
     - mpegdashstreaming
     - dvrchunkstreaming 
 - /Root/Application/Modules, add new Module:
     - Name - LiveStreamEntry
     - Description - Live-Stream Entry Listener
     - Class - `com.kaltura.media.server.wowza.listeners.LiveStreamEntry`
