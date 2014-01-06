# Kaltura Server #



## Plugins: ##
- Add Wowza to plugins.ini.



## Admin Console: ##
- Add admin.ini new permissions, see admin.template.ini:
 - FEATURE_LIVE_STREAM_RECORD
 - FEATURE_KALTURA_LIVE_STREAM



## Origin Servers: ##
-  broadcast.ini according to broadcast.template.ini



## Edge Servers: ##
media_servers.ini is optional and needed only for custom configurations.

- application - defaults to kLive
- search_regex_pattern, replacement - the regular expression to be replaced in the machine name in order to get the external host name.
- domain - overwrites the machine name and the regular expression replacement with a full domain name.
- port - defaults to 1935.
- port-https - no default defined.





# Wowza #



## Prerequisites: ##
- Wowza media server 3.6.2.16 or above.
- Java jre 1.7.
- kaltura group (gid = 613) or any other group that apache user is associated with.



## Additional libraries: ##
- commons-codec-1.4.jar
- commons-httpclient-3.1.jar
- commons-logging-1.1.1.jar
- commons-lang-2.6.jar




## For all wowza machine (origin and edge): ##
- Copy [KalturaWowzaServer.jar](https://github.com/kaltura/server-bin-linux-64bit/raw/master/wowza/KalturaWowzaServer-2.0.1.jar "KalturaWowzaServer.jar") to @WOWZA_DIR@/lib/
- Copy additional jar files (available in Kaltura Java client library) to @WOWZA_DIR@/lib/
 - [commons-codec-1.4.jar](https://github.com/kaltura/server-bin-linux-64bit/raw/master/wowza/commons-codec-1.4.jar "commons-codec-1.4.jar")
 - [commons-httpclient-3.1.jar](https://github.com/kaltura/server-bin-linux-64bit/raw/master/wowza/commons-httpclient-3.1.jar "commons-httpclient-3.1.jar")
 - [commons-logging-1.1.1.jar](https://github.com/kaltura/server-bin-linux-64bit/raw/master/wowza/commons-logging-1.1.1.jar "commons-logging-1.1.1.jar") 
 - [commons-lang-2.6.jar](https://github.com/kaltura/server-bin-linux-64bit/raw/master/wowza/commons-lang-2.6.jar "commons-lang-2.6.jar")
- Delete all directories under @WOWZA_DIR@/applications, but not the applications directory itself.
- Create @WOWZA_DIR@/applications/kLive directory.
- Delete all directories under @WOWZA_DIR@/conf, but not the conf directory itself.
- Create @WOWZA_DIR@/conf/kLive directory.
- Copy @WOWZA_DIR@/conf/Application.xml to @WOWZA_DIR@/conf/kLive/Application.xml

**Edit @WOWZA_DIR@/conf/kLive/Application.xml:**

 - /Root/Application/Streams/StorageDir - @WEB_DIR@/content/recorded
 - /Root/Application/DVR/Properties, add new properties:
     - HTTP random media name
         - Name - httpRandomizeMediaName
         - Value - true
         - Type - Boolean
 - /Root/Application/LiveStreamPacketizer/Properties, add new properties:
     - HTTP random media name
         - Name - httpRandomizeMediaName
         - Value - true
         - Type - Boolean
 - /Root/Application/HTTPStreamer/Properties, add new properties:
     - HTTP origin mode
         - Name - httpOriginMode
         - Value - on
     - Apple HLS cache control playlist
         - Name - cupertinoCacheControlPlaylist
         - Value - max-age=3
     - Apple HLS cache control media chunk
         - Name - cupertinoCacheControlMediaChunk
         - Value - max-age=86400
     - Flash HDS cache control reset counter
         - Name - cupertinoOnChunkStartResetCounter
         - Value - true
         - Type - Boolean
     - Smooth Streaming cache control playlist
         - Name - smoothCacheControlPlaylist
         - Value - max-age=3
     - Smooth Streaming cache control media chunk
         - Name - smoothCacheControlMediaChunk
         - Value - max-age=86400
     - Smooth Streaming cache control data chunk
         - Name - smoothCacheControlDataChunk
         - Value - max-age=86400
     - Flash HDS cache control playlist
         - Name - sanjoseCacheControlPlaylist
         - Value - max-age=3
     - Flash HDS cache control media chunk
         - Name - sanjoseCacheControlMediaChunk
         - Value - max-age=86400

**Edit @WOWZA_DIR@/conf/Server.xml:**

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
     - Kaltura web services http host name to be used for address binding, external IP could be used as well.
         - Name - KalturaServerWebServicesHost
         - Value - Machine external host name or IP.
     - Kaltura recorded files group name
         - Name - KalturaRecordedFileGroup
         - Value - kaltura (or apache if kaltura doesn't exist)
     - Kaltura web services binding host anme
         - Name - KalturaServerWebServicesHost
         - Value - external IP as will be accessed from the API machines.
     - Kaltura recorded file group
         - Name - KalturaRecordedFileGroup
         - Value - kaltura (gid = 613) or any other group that apache user is associated with.

**Edit @WOWZA_DIR@/conf/log4j.properties:**

 - Add `log4j.category.KalturaClientBase.class` = DEBUG
 - Change `log4j.appender.serverAccess.File` = @LOG_DIR@/kaltura\_mediaserver\_access.log
 - Change `log4j.appender.serverError.File` = @LOG_DIR@/kaltura\_mediaserver\_error.log
 - Change `log4j.appender.serverStats.File` = @LOG_DIR@/kaltura\_mediaserver\_stats.log
 - Comment out `log4j.appender.serverError.layout` and its sub values `log4j.appender.serverError.layout.*` 
 - Add `log4j.appender.serverError.layout` = `org.apache.log4j.PatternLayout`
 - Add `log4j.appender.serverError.layout.ConversionPattern` = `[%d{yyyy-MM-dd HH:mm:ss}] %p - "%m" - (%F:%L) %n` 
 - Comment out `log4j.appender.serverAccess.layout` and its sub values `log4j.appender.serverAccess.layout.*` 
 - Add `log4j.appender.serverAccess.layout` = `org.apache.log4j.PatternLayout`
 - Add `log4j.appender.serverAccess.layout.ConversionPattern` = `[%d{yyyy-MM-dd HH:mm:ss}] %p - "%m" - (%F:%L) %n`





## For origin servers: ##
**Edit @WOWZA_DIR@/conf/kLive/Application.xml:**

 - /Root/Application/Streams/StreamType - liverepeater-origin
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
 - /Root/Application/Properties, add new Property:
     - Name - streamTimeout
     - Value - 200 (the value is in milliseconds)
     - Type - Integer




## For edge servers: ##
**Edit @WOWZA_DIR@/conf/kLive/Application.xml:**

 - /Root/Application/Streams/StreamType - liverepeater-edge
 - /Root/Application/Streams/LiveStreamPacketizers:
     - cupertinostreamingrepeater
     - smoothstreamingrepeater
     - sanjosestreamingrepeater
     - dvrstreamingrepeater
 - /Root/Application/HTTPStreamers:
     - cupertinostreaming
     - smoothstreaming
     - sanjosestreaming
     - mpegdashstreaming
     - dvrchunkstreaming
 - /Root/Application/Repeater - list all origin servers URLs separated with `|`, for example, `wowz://wowza-origin-server1:1935/kLive|wowz://wowza-origin-server2:1935/kLive`.


**Setting keystore.jks:**

- [Create a self-signed SSL certificate](http://www.wowza.com/forums/content.php?435 "Create a self-signed SSL certificate") or use existing one.
- Copy the certificate file to @WOWZA_DIR@/conf/keystore.jks


**Edit @WOWZA_DIR@/conf/VHost.xml:**

- Uncomment /Root/VHost/HostPortList/HostPort with port 443 for SSL.
- /Root/VHost/HostPortList/HostPort/SSLConfig/KeyStorePassword - set the password for your certificate file.




## For webcam recording servers: ##

**Create oflaDemo application**

 - Create oflaDemo application in your Wowza server.
  - Create @WOWZA_DIR@/applications/oflaDemo directory
  - Create @WOWZA_DIR@/conf/oflaDemo directory
  - Copy @WOWZA_DIR@/conf/Application.xml to @WOWZA_DIR@/conf/oflaDemo/Application.xml.
 - Configure @WOWZA_DIR@/conf/oflaDemo/Application.xml
  - /Root/Streams/StreamType - live-record
  - /Root/Streams/StorageDir - @WEB_DIR@/content/webcam
  - /Root/Transcoder/LiveStreamTranscoder - transcoder
  - /Root/Transcoder/Templates - hdfvr.xml

**Create transcoding template**

 - Create @WOWZA_DIR@/transcoder/templates/hdfvr.xml template:

>     <Root>
>     	<Transcode>
>     		<Encodes>
>     			<!-- Example Encode block for source, not required unless Member of StreamNameGroup. -->
>     			<Encode>
>     				<Enable>true</Enable>
>     				<Name>aac</Name>
>     				<StreamName>mp4:${SourceStreamName}</StreamName>
>     				<Video>
>     					<!-- H.264, PassThru, Disable -->
>     					<Codec>PassThru</Codec>
>     					<Bitrate>${SourceVideoBitrate}</Bitrate>
>     					<Parameters>
>     					</Parameters>
>     				</Video>
>     				<Audio>
>     					<!-- AAC, PassThru, Disable -->
>     					<Codec>AAC</Codec>
>     					<Bitrate>48000</Bitrate>
>     				</Audio>
>     				<Properties>
>     				</Properties>
>     			</Encode>
>     		</Encodes>
>     		<Decode>
>     		</Decode>
>     		<StreamNameGroups>
>     		</StreamNameGroups>
>     		<Properties>
>     		</Properties>
>     	</Transcode>
>     </Root>

**Configure file system**

 - Make sure that @WEB_DIR@/content/webcam group is kaltura or apache
 - Define permissions stickiness on the group:
  - chmod +t @WEB_DIR@/content/webcam
  - chmod g+s @WEB_DIR@/content/webcam
