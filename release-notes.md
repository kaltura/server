# Kajam-11.0.0 #

## New scheduled task profile ##

 - Issue Type: New Feature
 - Issue ID: PS-2330  
  
### Deployment scripts ###

	php /opt/kaltura/app/tests/standAloneClient/exec.php /opt/kaltura/app/tests/standAloneClient/scheduledTaskProfiles/30DayDeleteAfterScheduleEnd.xml  
	Input: 
	- partner ID - 1956791
	- Max total count allowed per execution: 500
	- Host name: www.kaltura.com
	- Partner email address: admin console admin user
	- Partner password: user's password
	- Partner ID: -2
	
## Like->list API call ##

- Issue Type: New Feature
- Issue ID: PLAT-3920

#### Configuration ####
 
None.

#### Deployment Scripts ####
	php /opt/kaltura/app/deployment/updates/scripts/add_permissions/2015_10_25_add_like_list_permission.php
	php /opt/kaltura/app/alpha/scripts/utils/permissions/addPermissionToRole.php 0 Basic\ User\ Session\ Role LIKE_LIST_USER realrun (please use copy-paste carefully here)

	(only for on-prem/CE environments)
	mysql -h@db_host@ -u@db_user@ -p@db_pass@ -P3306 kaltura < deployment/updates/sql/2015_10_25_alter_kvote_table_puser_id_table.sql
	php deployment/updates/scripts/2015_10_25_populate_like_table_puser_id_field.php
	
#### Known Issues & Limitations ####

None.

# Jupiter-10.21.0 #

## Cielo24 plugin ##

- Issue Type: New Feature
- Issue ID: PLAT-2598

#### Configuration ####
 
- Added 'Cielo24' in plugins.ini.base, plugins.ini.admin.
- Added 'Cielo24' module to the admin-console in admin.ini.

#### Deployment Scripts ####
	php /opt/kaltura/app/deployment/base/scripts/installPlugins.php
	php /opt/kaltura/app/deployment/updates/scripts/add_permissions/2015_09_20_update_integration_notify_permission_name.php
	(developer's responsibility) php /opt/kaltura/app/alpha/scripts/utils/addPartnerToCielo24.php /opt/kaltura/app {PID} {cielo24-api-username} {cielo24-api-password} 
	
#### Known Issues & Limitations ####

None.

## In Video Quiz - Permissions Update ##

- Issue Type: New Feature  
- Issue ID: PLAT-3864

#### Installation ####

None.

#### Configuration ####

- Run the following permission script:
	php deployment\updates\scripts\add_permissions\2015_09_17_update_quiz_permissions.php

#### Known Issues & Limitations ####

None.

## In Video Quiz - Sphinx Implementation for Answer Cue-Point ##

- Issue Type: New Feature  
- Issue ID: PLAT-3836

#### Installation ####

- Add QuizSphinx to plugins.ini (as demonstrated in plugins.ini.template)
- Run the installPlugins.php script: deployment/base/scripts/installPlugins.php
- Run the cue-points population sphinx script: deployment/base/scripts/populateSphinxCuePoints.php

#### Configuration ####

None.

#### Known Issues & Limitations ####

None.

## In Video Quiz - new action servePdf under quiz service##

- Issue Type: New Feature  
- Issue ID: PLAT-2784 Add Quiz PDF support

#### Installation ####

- run deployment script - 2015_10_1_update_quiz_permissions.php
- clear cache

#### Configuration ####

None.
		
# Jupiter-10.20.0 #

### Configuration ###

## Changed kaltura.scheduler_status.id from int(11) to bigint(20) ##

- Run the following permission script:

  mysql -h@db_host@ -u@db_user@ -p@db_pass@ -P3306 kaltura <  deployment/updates/sql/2015_09_06_alter_scheduler_status_bigint.sql

# Jupiter-10.18.0 #

## Allow answer cue points to be added by player and anonymous users ##

- Issue Type: New Feature
- Issue ID: KMS-8423

#### Configuration ####

None.

#### Deployment Scripts ####

	php /opt/kaltura/app/deployment/updates/scripts/add_permissions/2015_08_16_allow_adding_answer_cue_points_with_widget_ks.php
	 
#### Known Issues & Limitations ####

None.

## Dexter/JCare Integration Plugin ##

- Issue Type: New Feature  
- Issue ID: PLAT-2595  

### Installation ###

- Run the installPlugins.php script:
	php /opt/kaltura/app/deployment/base/scripts/installPlugins.php

### Configuration ###

- Run the following permission script:
	php /opt/kaltura/app/deployment/updates/scripts/add_permissions/2015_08_12_add_metadata_profile_get_action_for_partner_-1.php

## Voicebase plugin ##

- Issue Type: New Feature
- Issue ID: PLAT-2599

#### Configuration ####
 
- Added 'Voicebase' in plugins.ini.base, plugins.ini.admin.
- Added 'Voicebase' module to the admin-console in admin.ini.

#### Deployment Scripts ####
	php /opt/kaltura/app/deployment/base/scripts/installPlugins.php
	php /opt/kaltura/app/deployment/updates/scripts/add_permissions/2015_08_23_add_integration_notify_permission.php
	(developer's responsibility) php /opt/kaltura/app/alpha/scripts/utils/addPartnerToVoicebase.php /opt/kaltura/app {PID} {voicebase-api-key} {voicebase-api-password} 
	
#### Known Issues & Limitations ####

None.

## Allow users to use attachmentAsset->add ##

- Issue Type: Permission to an existing API 
- Issue ID: PLAT-3652

#### Configuration ####
 
 	None.

#### Deployment Scripts ####

 	- php deployment/updates/scripts/add_permissions/2015_08_16_add_attachment_asset_permission.php

#### Known Issues & Limitations ####

None.

# Jupiter-10.17.0 #

## Allow users to use uiconf-->listTemplates ##

- Issue Type: Permission to existing API 
- Issue ID: PLAT-3541

#### Configuration ####
 
 	- In the file "deployment/permissions/service.uiconf.ini" in the line "permissionItem7.permissions" add `,BASE_USER_SESSION_PERMISSION` at the end

#### Deployment Scripts ####

 	- php deployment/updates/scripts/add_permissions/2015_07_29_allow_user_session_uiconf_listTemplates.php

#### Known Issues & Limitations ####

None.

## new Transcript asset ##

-- Issue Type: New Feature
-- Issue ID: PLAT-2622

#### Configuration ####
 
- Added 'Transcript' in configurations/plugins.ini.base and configurations/plugins.ini.admin

#### Deployment Scripts ####

Run:
 	- php /opt/kaltura/app/deployment/base/scripts/installPlugins.php

#### Known Issues & Limitations ####

None.

## Allow PLAYBACK_BASE_ROLE to user user_entry and quiz ##

- Issue Type: Permission to existing API 
- Issue ID:

#### Configuration ####
 
 	None.

#### Deployment Scripts ####

 	- php deployment/updates/scripts/add_permissions/2015_07_29_update_quiz_and_userentry_permissions.php

#### Known Issues & Limitations ####

None.

--
# Jupiter-10.16.0 #

## New applehttp to multicast delivery profile ##

-- Issue Type: New Feature
-- Issue ID: PLAT-3510

#### Configuration ####
 
None.

#### Deployment Scripts ####

 	- (Already executed on production) php /opt/kaltura/app/deployment/updates/scripts/2015_07_20_create_applehttp_to_multicast_delivery_profile.php

#### Known Issues & Limitations ####

None.

## Edge Server - Rename column name ##

-- Issue Type: Bug report
-- Issue ID: PLAT-3441

#### Configuration ####
 
None.

#### Deployment Scripts ####

 - mysql -h@db_host@ -u@db_user@ -p@db_pass@ -P3306 kaltura < deployment/updates/sql/2015_07_26_alter_edge_server_column_name.sql

#### Known Issues & Limitations ####

None.

## Cache response-profile results ##

- Issue Type: optimization

### Couchbase Deployment Instructions ###
Download Couchbase server and install according to [official instructions](http://www.couchbase.com/nosql-databases/downloads#Couchbase_Server "http://www.couchbase.com/").

#### Server Setup ####

 - Install Couchbase PHP extension: `pecl install couchbase`
     - Required `php-devel` and `libcouchbase-devel`.
 - Add couchbase extension in your php.ini file.
 - Setup Couchbase server [http://@WWW_HOST@:8091](http://@WWW_HOST@:8091 "").
 - Define username and password to be used later in cache.ini configuration.
 - Create new data bucket named `ResponseProfile`.

#### Views Setup ####

 - Create design-document: `_design/dev_deploy1`.
 - Create View: `objectSpecific`:
```javascript
	function (doc, meta) {
    	if (meta.type == "json") {
    		if(doc.type == "primaryObject"){
    			emit(doc.objectKey, null);
    		}
    	}
}
```

 - Create View: `relatedObjectSessions`:
```javascript
	function (doc, meta) {
    	if (meta.type == "json") {
    		if(doc.type == "relatedObject"){
    	 			emit([doc.triggerKey, doc.objectType, doc.sessionKey], null);
    		}
    	}
}
```
	
 - Create View: `objectSessions`:
```javascript
	function (doc, meta) {
	 	if (meta.type == "json") {
	 		if(doc.type == "primaryObject"){
	 			emit([doc.objectKey, doc.sessionKey], null);
	 		}
	 	}
}
```

 - Create View: `objectTypeSessions`:
```javascript
	function (doc, meta) {
	 	if (meta.type == "json") {
	 		if(doc.type == "primaryObject"){
	 			emit([doc.objectType, doc.sessionKey], null);
	 		}
	 	}
}
```
	
 - Create View: `sessionType`:
```javascript
	function (doc, meta) {
    	if (meta.type == "json") {
    		if(doc.type == "primaryObject"){
    			emit([doc.sessionKey, doc.objectKey], null);
    		}
    	}
}
```
	
 - Create View: `relatedObjectsTypes`:
```javascript
	function (doc, meta) {
		if (meta.type == "json") {
			if(doc.type == "relatedObject"){
	 			emit([doc.triggerKey, doc.objectType], null);
			}
		}
}
```
 - Publish the design-document.

### Configuration ###
 - Update configurations/cache.ini under couchbase section to use the username and password you configured for couchbase server.
 - Add new worker into configurations/batch/batch.ini:

```ini
[KAsyncRecalculateCache : JobHandlerWorker]
id													= 590
friendlyName										= Recalculate Cache
type												= KAsyncRecalculateCache
scriptPath											= batch/batches/Cache/KAsyncRecalculateCacheExe.php
```
 - Add new module to the admin-console in admin.ini:

```ini
moduls.recalculateResponseProfile.enabled = true
moduls.recalculateResponseProfile.permissionType = 2
moduls.recalculateResponseProfile.label = "Recalculate response-profile cache"
moduls.recalculateResponseProfile.permissionName = FEATURE_RECALCULATE_RESPONSE_PROFILE_CACHE
moduls.recalculateResponseProfile.basePermissionType = 2
moduls.recalculateResponseProfile.basePermissionType =
moduls.recalculateResponseProfile.basePermissionName =
moduls.recalculateResponseProfile.group = GROUP_ENABLE_DISABLE_FEATURES
```

### Deployment Scripts ###
 - php deployment/updates/scripts/add_permissions/2015_06_09_response_profile.php

### Known Issues & Limitations ###
None.

---
# Jupiter-10.15.0 #

## Add Developer Partner ##

- Issue Type: New Feature  
- Issue ID: PLAT-3326  

### Configuration ###

 - Added new e-mail configuration in /batch/batches/Mailer/emails_en.ini
 - Remark for production configuration: add /alpha/crond/kaltura/monthly_quota_storage_update.sh script to kaltura.daily cron jobs 
 
### Deployment Scripts ###

- None.  
		
## File sync pull without jobs ##

- Issue Type: optimization
- Issue ID: N/A  

### Configuration ###

 - Update the file sync import worker configuration, sample config:
 
[KAsyncFileSyncImport : PeriodicWorker]
type                            = KAsyncFileSyncImport
scriptPath                      = ../plugins/multi_centers/batch/FileSyncImport/KAsyncFileSyncImportExe.php
params.curlTimeout              = 180
params.fileChmod                = 755
params.fileOwner                = apache

[KAsyncFileSyncImportSmall : KAsyncFileSyncImport]
id                      = 27020
friendlyName            = FileSyncImportSmall
filter.estimatedEffortLessThan = 5000000
params.maxCount         = 100
params.maxSize          = 10000000

[KAsyncFileSyncImportBig : KAsyncFileSyncImport]
id                      = 27030
friendlyName            = FileSyncImportBig
filter.estimatedEffortGreaterThan = 4999999
params.maxCount         = 1

[KAsyncFileSyncImportDelayed : KAsyncFileSyncImport]
id                      = 27040
friendlyName            = FileSyncImportDelayed
params.maxCount         = 1
filter.createdAtLessThanOrEqual = -39600	; now() - 11 hours
 
### Deployment Scripts ###

 - php deployment/updates/scripts/add_permissions/2015_07_06_file_sync_service.php
 - php deployment/base/scripts/createQueryCacheTriggers.php create @db_host@ @db_user@ @db_pass@ realrun

## Metadata Change HTTP Notification ##

- Issue Type: bug  
- Issue ID: PS-2287  

### Configuration ###

 - None.
 
### Deployment Scripts ###

- run the following deployment script:  
		php exec.php /opt/kaltura/app/tests/standAloneClient/entryCustomMetadataChangedHttpNotification.xml  


## Application authentication token ##

-- Issue Type: New feature
-- Issue ID: PLAT-3095

#### Configuration ####
 
None.

#### Deployment Scripts ####

 - php deployment/updates/scripts/add_permissions/2015_06_22_app_token_service.php
 - mysql -h@db_host@ -u@db_user@ -p@db_pass@ -P3306 kaltura < deployment/updates/sql/2015_06_22_create_app_token_table.sql

#### Known Issues & Limitations ####

None.


## Update KMC docs  ##

-- Issue Type: Doc change
-- Issue ID: SUP-3117

#### Configuration ####

Need to update the following doc on the SAAS server under location /web/content/docs/kaltura_batch_upload_falcon.zip
from repository kmc-docs.

#### Deployment Scripts ####

None.

#### Known Issues & Limitations ####

None.



---
# Jupiter-10.14.0 #

## Email Notifications ##

- Issue Type: Email notifications send all addresses in the "To" field
- Issue ID: SUP-4339 

#### Configuration ####
 
- None.

#### Deployment Scripts ####

/deployment/updates/scripts/2015_06_18_update_mediaspace_email_notification_templates.php

#### Known Issues & Limitations ####

None.

## On the fly encryption ##

- Issue Type: Configuration for existing feature
- Issue ID: PLAT-2675 

#### Configuration ####
 
- Add relevant value of "license_server_url" in "drm.ini", see saas-config

#### Deployment Scripts ####

None.

#### Known Issues & Limitations ####

None.


## in video quiz ##

- Issue Type: new feature  
- Issue ID: PLAT-2795 and PLAT-2792 and PLAT-2791 and PLAT-2790 and PLAT-2786 and PLAT-2857

#### Configuration ####

- Add the following line to the plugins.ini file:  
        Quiz 

- Add the following lines from admin.template.ini to admin.ini:

		moduls.quizCuePoint.enabled = true
		moduls.quizCuePoint.permissionType = 3
		moduls.quizCuePoint.label = Quiz - Cue Points
		moduls.quizCuePoint.permissionName = QUIZ_PLUGIN_PERMISSION
		moduls.quizCuePoint.basePermissionType = 3
		moduls.quizCuePoint.basePermissionName = CUEPOINT_PLUGIN_PERMISSION
		moduls.quizCuePoint.group = GROUP_ENABLE_DISABLE_FEATURES
		
#### Deployment Scripts ####

- run the Following deployemnt scripts:
        
		Create new user_entry table:
        mysql -h@db_host@ -u@db_user@ -p@db_pass@ -P3306 kaltura < deployment/updates/sql/2015_15_06_create_user_entry_table.sql
        

		Update new services permissions:
		php /opt/kaltura/app/deployment/updates/scripts/add_permissions/2015_04_11_update_quiz_permissions.php
		php /opt/kaltura/app/deployment/updates/scripts/add_permissions/2015_05_07_update_userentry_permissions.php
		php /opt/kaltura/app/deployment/updates/scripts/add_permissions/2015_06_07_update_quiz_analytics_permissions.php

		Install Plugins:
		php /opt/kaltura/app/deployment/base/scripts/installPlugins.php

		
		

#### Known Issues & Limitations ####

None.


# Jupiter-10.13.0 #

## New edgeServer service - drop one of the dynamic eCDN ##

- Issue Type: new feature  
- Issue ID: PLAT-3007 

### Configuration ###
- Add the following to the admin.ini under "AVAILABLE MODULES (permissionType)":

		moduls.EdgeServer.enabled = true
		moduls.EdgeServer.permissionType = 2
		moduls.EdgeServer.label = "Edge server usage"
		moduls.EdgeServer.permissionName = FEATURE_EDGE_SERVER
		moduls.EdgeServer.basePermissionType =
		moduls.EdgeServer.basePermissionName =
		moduls.EdgeServer.group = GROUP_ENABLE_DISABLE_FEATURES

#### Deployment Scripts ####
- run the Following deployemnt scripts:

		Update new servcie permissions: 
		php /opt/kaltura/app/deployment/updates/scripts/add_permissions/2015_05_28_edge_server_service.php

		create new edge_Server table:
		mysql -h@db_host@ -u@db_user@ -p@db_pass@ -P3306 kaltura < deployment/updates/sql/2015_27_05_create_edge_server_table.sql

#### Known Issues & Limitations ####

* To enable this feature on your account you will need you will need to flip on the feature in the partner configuration section.

##multi-language caption ingestion##
- Issue Type: feature request
- Issue ID: PLAT-2500

#### Configuration ####

- allocate worker/s for KAsyncParseMultiLanguageCaptionAsset.

#### Deployment Scripts ####

	php /opt/kaltura/app/deployment/base/scripts/installPlugins.php

- deploy server-saas-config to update batch client.

#### Known Issues & Limitations ####

Players will allow choosing 'multi-language' captions ,by default.


## Search for tags with spaces and words with less than 3 characters ##

- Issue Type: bug fix
- Issue ID: SUP-4362

#### Configuration ####

None.

#### Deployment Scripts ####

    - Need to re-build so that spaces in tags will be replaced by '=' & re-index the tag sphinx table.

#### Known Issues & Limitations ####

None.


# Jupiter-10.12.0 #

## Set new permission to flavorasset geturl ##

- Issue Type: Permission change
- Issue ID : SUP-4739

### Configuration ###
 
None.

#### Deployment Script ####

- Run php deployment/updates/scripts/add_permissions/2015_05_18_update_flavorasset_permissions.php

## New event notification template- drop folder error description changed ##

- Issue Type: new feature  
- Issue ID: PS-2251  

#### Deployment Script ####

- Run php /opt/kaltura/app/tests/standAloneClient/exec.php /opt/kaltura/app/tests/standAloneClient/emailDropFolderFailedStatusMessage.xml  

## Server ingestion of chapter cue points without slides ##

- Issue Type: bug fix
- Issue ID: PLAT-2204

### Configuration ###
- **workers.ini**

under 'KAsyncBulkUpload'

		params.xmlSchemaVersion		= 7

#### Deployment Scripts ####

None.

#### Known Issues & Limitations ####

None.

## New event notification template- entry custom data changed ##

- Issue Type: new feature  
- Issue ID: PS-2253  

#### Deployment Script ####

- Run php /opt/kaltura/app/tests/standAloneClient/exec.php /opt/kaltura/app/tests/standAloneClient/metadataObjectChanged.xml  

## "Entry flagged for review" Email Notification missing on production ##

- Issue Type: bug  
- Issue ID: PS-2252  

#### Deployment Script ####

- Run php /opt/kaltura/app/tests/standAloneClient/exec.php /opt/kaltura/app/tests/standAloneClient/kmcModerationNotificationsTemplates.xml  

## uDRM on the fly encryption ##

- Issue Type: new feature
- Issue ID: PLAT-2675

#### Configuration ####

- Clone @APP_DIR/configurations/drm.template.ini to @APP_DIR/configurations/drm.ini
- In @APP_DIR/configurations/drm.ini replace @UDRM_SIGNING_KEY@ with key given from me.
- Add the following permission block to @APP_DIR@/configurations/admin.ini:

		moduls.drmBase.enabled = true
		moduls.drmBase.permissionType = 3
		moduls.drmBase.label = DRM - Base
		moduls.drmBase.permissionName = DRM_PLUGIN_PERMISSION
		moduls.drmBase.basePermissionType =
		moduls.drmBase.basePermissionName =
		moduls.drmBase.group = GROUP_ENABLE_DISABLE_FEATURES
		
		moduls.drmCencFlavors.enabled = false
		moduls.drmCencFlavors.permissionType = 2
		moduls.drmCencFlavors.label = DRM – Enable CENC Flavors
		moduls.drmCencFlavors.permissionName = DRM_CENC_FLAVORS
		moduls.drmCencFlavors.basePermissionType = 3
		moduls.drmCencFlavors.basePermissionName = DRM_PLUGIN_PERMISSION
		moduls.drmCencFlavors.group = GROUP_ENABLE_DISABLE_FEATURES


#### Deployment Scripts ####

		- run php /opt/kaltura/app/deployment/updates/scripts/2015_05_17_update_DRM_access_control.php
		- run php deployment/updates/scripts/add_permissions/2015_05_17_update_drm_license_access_permissions.php
        - run php /opt/kaltura/app/deployment/base/scripts/installPlugins.php

#### Known Issues & Limitations ####

None.


# Jupiter-10.11.0 #

## Server support for Q&A feature ##

- Issue Type: new feature
- Issue ID: PLAT-2850

### Configuration ###
- update sphinx kaltura.conf:
	
		Add the following to kaltura_cue_point index:
		- rt_attr_uint = is_public
		- rt_field = plugins_data

#### Deployment Scripts ####

		- Need to re-build & re-index the cue point sphinx table.
		- run php /opt/kaltura/app/deployment/updates/scripts/2015_05_11_create_qAndA_default_schema.php

#### Known Issues & Limitations ####

None.


## New feature- hide template partner uiconfs ##

- Issue Type: bug fix  
- Issue ID: PLAT-2946

### Configuration ###
- Add the following permission block to @APP_DIR@/configurations/admin.ini:
		moduls.hideTemplatePartnerUiConfs.enabled = true  
        moduls.hideTemplatePartnerUiConfs.permissionType = 2  
        moduls.hideTemplatePartnerUiConfs.label = "Hide template partner ui-confs from preview&embed menu"  
        moduls.hideTemplatePartnerUiConfs.permissionName = FEATURE_HIDE_TEMPLATE_PARTNER_UICONFS  
        moduls.hideTemplatePartnerUiConfs.basePermissionType = 2  
        moduls.hideTemplatePartnerUiConfs.basePermissionType =  
        moduls.hideTemplatePartnerUiConfs.basePermissionName =  
        moduls.hideTemplatePartnerUiConfs.group = GROUP_ENABLE_DISABLE_FEATURES  

## Error when manually dispatching notification template ##

- Issue Type: bug fix
- Issue ID: PLAT-2387
### Deployment ###

- Run the following script:  
			cd /opt/kaltura/app/tests/standAloneClient  
			php exec.php commentAddedEnabledForManualDispatch.xml    
- Delete older email notification from partner 0.

## Too many logs are written to file on batch ##

- Issue Type: bug fix
- Issue ID: PLAT-2914

### Configuration ###
- update batch.ini
Add the following parameters to the batch.ini [template] configuration
logWorkerInterval										= 60


#### Deployment Scripts ####

None.

#### Known Issues & Limitations ####

None.

## Avoid the need to update many metadata objects ##

- Issue Type: new feature
- Issue ID: PLAT-1998

### Configuration ###

None.

#### Deployment Scripts ####

- Run mysql -h@db_host@ -u@db_user@ -p@db_pass@ -P3306 kaltura < deployment/updates/sql/2014_11_06_metadata_profile_file_sync_version.sql

#### Known Issues & Limitations ####

None.


# Jupiter-10.10.0 #
## Support marking file_sync's as directories ##

- Issue type - new feature
- Issue ID - WEBC-467

### Configuration ###

None.
   
  
### Deployment ###
 
 - Run mysql -h@db_host@ -u@db_user@ -p@db_pass@ -P3306 kaltura < deployment/updates/sql/2015_04_28_alter_file_sync_table_custom_data_field.sql
	
		Please verify this column does not exist propir to running.

#### Known Issues & Limitations ####

None.


 
## Feed Drop Folder Feature ##

- Issue type - new feature
- Issue ID - PLAT-2042

### Configuration ###

Add the following line to the plugins.ini file:  
        FeedDropFolder 
   
Add the following parameters to the batch.ini DropFolderWatcher worker configuration:  
        params.mrss.xmlPath									= @WEB_DIR@/tmp/dropFolderFiles  
        params.mrss.limitProcessEachRun						= 20
   
  
### Deployment ###
 
 - clear the cache
 - run php /opt/kaltura/app/deployment/base/scripts/installPlugins.php
 - Create new folder : @WEB_DIR@/tmp/dropFolderFiles

## Time Based Playlist Filters ##

Allows adding timebased filters to playlists that support expiry of a filter on a certain time.

- Issue Type: New Feature
- Issue ID: PLAT-2817

#### Configuration ####

None.

#### Deployment Scripts ####

None.

#### Known Issues & Limitations ####

None.


## Live to VOD entry should support copying all metadata ##

- Issue Type: Story
- Issue ID: PLAT-2744

#### Configuration ####

Add the following lines from admin.template.ini to admin.ini:

    moduls.liveStreamRecordShouldCopyEntitelment.enabled = true
    moduls.liveStreamRecordShouldCopyEntitelment.permissionType = 2
    moduls.liveStreamRecordShouldCopyEntitelment.label = Kaltura Live Streams - Copy entitelment
    moduls.liveStreamRecordShouldCopyEntitelment.permissionName = FEATURE_LIVE_STREAM_COPY_ENTITELMENTS
    moduls.liveStreamRecordShouldCopyEntitelment.basePermissionType = 2
    moduls.liveStreamRecordShouldCopyEntitelment.basePermissionName = FEATURE_LIVE_STREAM
    moduls.liveStreamRecordShouldCopyEntitelment.group = GROUP_ENABLE_DISABLE_FEATURES

#### Deployment Scripts ####

None.

#### Known Issues & Limitations ####

None.

## Delivery Profile selection logic for playback ##

Added logic to the selection of deliveryProfiles for playback.
A priority attributes orders available deliveryProfile.
Each deliveryProfile may override the base class implementation of supportsDeliveryDynamicAttributes which returns 
whether the deliveryProfile supports the required playback constraints (progressive media seek, flv support etc), doesn't support or partially support it. 
Partial support means the playback will work but a feature (e.g. seek within flash progressive download) won't.
These enhancements allow for multiple deliveryProfiles to be configured as default and provide fall back in case of delivery constraints.
Delivered by - Eran Itam.

- Issue Type:Enhancement
- Issue ID: No ID

#### Configuration ####

None.

#### Deployment Scripts ####

deployment/updates/sql/2015_04_25_alter_delivery_profile_add_priority.sql

#### Known Issues & Limitations ####

None.

----------
# Jupiter-10.9.0 #

## Copy cue points to clips and trimmed entries ##

- Issue Type: bug fix
- Issue ID: PLAT-1118

#### Configuration ####

Add the following lines from admin.template.ini to admin.ini:

	moduls.annotationCopyToClip.enabled = true
	moduls.annotationCopyToClip.permissionType = 2
	moduls.annotationCopyToClip.label = Time Based - Copy annotation cue points when user clips entries
	moduls.annotationCopyToClip.permissionName = COPY_ANNOTATIONS_TO_CLIP
	moduls.annotationCopyToClip.basePermissionType = 3
	moduls.annotationCopyToClip.basePermissionName = ANNOTATION_PLUGIN_PERMISSION
	moduls.annotationCopyToClip.group = GROUP_ENABLE_DISABLE_FEATURES

	moduls.annotationCopyToTrim.enabled = true
	moduls.annotationCopyToTrim.permissionType = 2
	moduls.annotationCopyToTrim.label = Time Based - Do not keep annotation cue points when user trims entries
	moduls.annotationCopyToTrim.permissionName = DO_NOT_COPY_ANNOTATIONS_TO_TRIMMED_ENTRY
	moduls.annotationCopyToTrim.basePermissionType = 3
	moduls.annotationCopyToTrim.basePermissionName = ANNOTATION_PLUGIN_PERMISSION
	moduls.annotationCopyToTrim.group = GROUP_ENABLE_DISABLE_FEATURES

	moduls.cuePointCopyToClip.enabled = true
	moduls.cuePointCopyToClip.permissionType = 2
	moduls.cuePointCopyToClip.label = Time Based - Do not copy code, thumb and ad cue points when user clips entries
	moduls.cuePointCopyToClip.permissionName = DO_NOT_COPY_CUE_POINTS_TO_CLIP
	moduls.cuePointCopyToClip.basePermissionType = 3
	moduls.cuePointCopyToClip.basePermissionName = CUEPOINT_PLUGIN_PERMISSION
	moduls.cuePointCopyToClip.group = GROUP_ENABLE_DISABLE_FEATURES

	moduls.cuePointCopyToTrim.enabled = true
	moduls.cuePointCopyToTrim.permissionType = 2
	moduls.cuePointCopyToTrim.label = Time Based - Do not keep code, thumb, and ad cue points when user trims entries
	moduls.cuePointCopyToTrim.permissionName = DO_NOT_COPY_CUE_POINTS_TO_TRIMMED_ENTRY
	moduls.cuePointCopyToTrim.basePermissionType = 3
	moduls.cuePointCopyToTrim.basePermissionName = CUEPOINT_PLUGIN_PERMISSION
	moduls.cuePointCopyToTrim.group = GROUP_ENABLE_DISABLE_FEATURES

	moduls.keepCuePointsOnMediaReplacement.enabled = true
	moduls.keepCuePointsOnMediaReplacement.permissionType = 2
	moduls.keepCuePointsOnMediaReplacement.label = Time Based - Remove original cue points when user replaces media in existing entry
	moduls.keepCuePointsOnMediaReplacement.permissionName = REMOVE_CUE_POINTS_WHEN_REPLACING_MEDIA
	moduls.keepCuePointsOnMediaReplacement.basePermissionType = 3
	moduls.keepCuePointsOnMediaReplacement.basePermissionName = CUEPOINT_PLUGIN_PERMISSION
	moduls.keepCuePointsOnMediaReplacement.group = GROUP_ENABLE_DISABLE_FEATURES

#### Deployment Scripts ####

None.

#### Known Issues & Limitations ####

None.

## YouTube API connector V3 ##

***Note:*** Manual migration required to all existing accounts. 

- Issue Type: bug fix
- Issue ID: PLAT-2776

#### Configuration ####

**google_auth.ini**

Added `youtubeapi` section.

#### Deployment Scripts ####

		deployment/updates/scripts/2015_04_12_migrate_youtube_api_category.php

#### Known Issues & Limitations ####

The new API, currently, doesn't support existing features:

- Disallow comments
- Disallow ratings
- Disallow responses
- Set raw file name
- Set start and end dates

## Redirect live entry updates via its original DC ##

- Issue Type: bug fix
- Issue ID: PLAT-2762

#### Configuration ####

** local.ini **

Added the following configuration.

	;set to true when one of the DC's is down
	disable_dump_api_request = false

#### Deployment Scripts ####

None.

#### Known Issues & Limitations ####

None.

----------
# Jupiter-10.8.0 #

## Tag-search - return all objects when no entitlement ##

- Issue Type: bug fix
- Issue ID: PLAT-2646

#### Configuration ####

**sphinx/kaltura.conf**

Added the following attribute to the kaltura_tag sphinx table. please re-index.

	rt_attr_string = tag

#### Deployment Scripts ####

None.

#### Known Issues & Limitations ####

None.

## Real-time dashboard permission in now based on the general live-stream permission ##

- Issue Type: Change Request
- Issue ID: PLAT-2705

#### Configuration ####

Remove moduls.realTimeReports config from configurations/admin.ini

#### Deployment Scripts ####

None.

#### Known Issues & Limitations ####

None.


## Dynamic Objects ##

- Issue Type: New Feature
- Issue ID: PLAT-2466

#### Configuration ####

**plugins.ini**

Add `MetadataSphinx` to the end of `Mandatory plugins` section (after `SphinxSearch`)

**sphinx**

Update `configurations/sphinx/kaltura.conf` according to template (a new index `kaltura_metadata` was added).


#### Deployment Scripts ####

		mysql -uroot -p kaltura < deployment/updates/sql/2015_03_18_alter_metadata_profile_field_with_custom_data_field.sql
		php deployment/updates/scripts/add_permissions/2015_03_18_update_metadata_permissions.php
		php deployment/base/scripts/installPlugins.php
		php deployment/base/scripts/populateSphinxMetadata.php

#### Known Issues & Limitations ####

None.

##New file formats MXF and M2TS##
- Issue Type: new feature
- Issue ID: PLAT-2742 and SUP-4124



----------
# Jupiter-10.7.0 #

##API Response Profiles##
- Issue Type: new feature

#### Configuration ####
None

#### Deployment Scripts ####

	mysql -uroot -p kaltura < deployment/updates/sql/2015_02_23_response_profile_table.sql
	php deployment/updates/scripts/add_permissions/2015_02_23_response_profile.php  

#### Known Issues & Limitations ####

None.

##Live Analytics - Show DVR audience metrics on Live Analytics##
- Issue Type: new feature
- Issue ID: PLAT-2413

#### Configuration ####

Deploy an up-to-date version of batch/batches/Mailer/emails_en.ini

#### Deployment Scripts ####

Run on the Cassandra cluster: **live_analytics**/KalturaLiveModel/conf/migrations/2015-03-01-000000-update_dvr_kaltura_live_keyspace.cql
Deploy KalturaLiveAnalyics.war

#### Known Issues & Limitations ####

None.

----------
# Jupiter-10.6.0 #

##Live - A/V out of sync in second part of recorded entry after restart streaming (regression)##
- Issue ID: PLAT-2540

### Configuration ###
- Add "params.ffprobeCmd = ffprobe" to configurations/batch/live.workers.ini - KAsyncConvertLiveSegment

----------
# Jupiter-10.5.0 #

##Flavor-asset status HTTP Notifications##
- Issue Type: new feature
- Issue ID: PS-2065

### Configuration ###
None

###Installation  
- Run:  
php /opt/kaltura/app/tests/standAloneClient/exec.php /opt/kaltura/app/tests/standAloneClient/flavorAssetHttpNotifications.xml  

#### Known Issues & Limitations ####

None.

##Support MPEG-DASH Delivery Profile##
- Issue Type: New Feature
- Issue ID: PLAT-2064

#### Configuration ####

None.

#### Deployment Scripts ####

		php deployment/updates/scripts/2014_12_08_create_dash_delivery_profile.php

#### Known Issues & Limitations ####

No client side (player) failover support.  

##Live Audio/Video async fix##
- Issue ID: SUP-2942

### Configuration ###
- Add "params.ffprobeCmd = ffprobe" to 
- - configurations/batch/workers.ini - KAsyncExtractMedia
- - configurations/batch/live.workers.ini - KAsyncConcat


## Business Process Management Integration ##
Integration with Activiti BPM engine

- Issue Type: New Feature

#### Configuration ####

*plugins.ini*

Add the following line:

		Integration		
		IntegrationEventNotifications
		BpmEventNotificationIntegration
		BusinessProcessNotification
		ActivitiBusinessProcessNotification

*batch.ini*

Add the following lines under `[template]` section:

		enabledWorkers.KAsyncIntegrate						= 1
		enabledWorkers.KAsyncIntegrateCloser				= 1

Add the following lines as new sections:

		[KAsyncIntegrate : JobHandlerWorker]
		id													= 570
		friendlyName										= Integrate
		type												= KAsyncIntegrate
		maximumExecutionTime								= 12000
		scriptPath											= ../plugins/integration/batch/Integrate/KAsyncIntegrateExe.php
		
		[KAsyncIntegrateCloser : JobHandlerWorker]
		id													= 580
		friendlyName										= Integrate Closer
		type												= KAsyncIntegrateCloser
		maximumExecutionTime								= 12000
		scriptPath											= ../plugins/integration/batch/Integrate/KAsyncIntegrateCloserExe.php
		params.maxTimeBeforeFail							= 1000000


#### Deployment Preparations ####

 - Reload configuration: `touch cache/base.reload`.
 - Clear cache: `rm -rf cache/*`.
 - Install plugins: `php deployment/base/scripts/installPlugins.php`.
 - Generate clients: `php generator/generate.php`.
 - Restart batch: `/etc/init.d/kaltura-batch restart`.

#### Deployment Scripts ####

		mysql -uroot -p kaltura < deployment/updates/sql/2014_11_20_business_process_server.sql
		php deployment/updates/scripts/add_permissions/2014_11_20_business_process_server_permissions.php
		php deployment/updates/scripts/add_permissions/2015_01_20_dispatch_integration_job.php
		php tests/standAloneClient/exec.php tests/standAloneClient/bpmNotificationsTemplates.xml

#### Activiti Deployment Instructions ####

 - Install [Apache Tomcat 7](http://tomcat.apache.org/tomcat-7.0-doc/setup.html#Unix_daemon "Apache Tomcat 7")
 - Make sure $CATALINA_BASE is defined.
 - Install [Apache Ant](http://ant.apache.org/manual/installlist.html "Apache Ant")
 - Download [Activiti 5.17.0](https://github.com/Activiti/Activiti/releases/download/activiti-5.17.0/activiti-5.17.0.zip "Activiti 5.17.0")
 - Open zip: `unzip activiti-5.17.0.zip`
 - Copy WAR files: 
  - `cp activiti-5.17.0/wars/activiti-explorer.war $CATALINA_BASE/webapps/activiti-explorer##5.17.0.war`
  - `cp activiti-5.17.0/wars/activiti-rest.war $CATALINA_BASE/webapps/activiti-rest##5.17.0.war`
 - Restart Apache Tomcat.
 - Create DB **(replace tokens)**: `mysql -uroot -p`

		CREATE DATABASE activiti;
		GRANT INSERT,UPDATE,DELETE,SELECT,ALTER,CREATE,INDEX ON activiti.* TO '@DB1_USER@'@'%';
		FLUSH PRIVILEGES;

 - Edit **(replace tokens)** $CATALINA_BASE/webapps/**activiti-explorer**/WEB-INF/classes/db.properties

		jdbc.driver=com.mysql.jdbc.Driver
		jdbc.url=jdbc:mysql://@DB1_HOST@:@DB1_PORT@/activiti
		jdbc.username=@DB1_USER@
		jdbc.password=@DB1_PASS@

 - Edit **(replace tokens)** $CATALINA_BASE/webapps/**activiti-rest**/WEB-INF/classes/db.properties

		jdbc.driver=com.mysql.jdbc.Driver
		jdbc.url=jdbc:mysql://@DB1_HOST@:@DB1_PORT@/activiti
		jdbc.username=@DB1_USER@
		jdbc.password=@DB1_PASS@

 - Download [mysql jdbc connector 5.0.8](http://cdn.mysql.com/Downloads/Connector-J/mysql-connector-java-5.0.8.zip "mysql jdbc connector 5.0.8")
 - Open zip: `unzip mysql-connector-java-5.0.8.zip`
 - Copy the mysql jdbc connector: `cp mysql-connector-java-5.0.8/mysql-connector-java-5.0.8-bin.jar $CATALINA_BASE/lib/`
 - Restart Apache Tomcat.
 - Open your browser to validate installation **(replace tokens)**: http://@WWW_HOST@:8080/activiti-explorer/
	 - Username: kermit
	 - Password: kermit
 - Generate java pojo and bpmn clients **(replace tokens)**: `php @APP_DIR@/generator/generate.php pojo,bpmn`
 - Edit deployment configuration file **(replace tokens)**: `cp @WEB_DIR@/content/clientlibs/bpmn/deploy/src/activiti.cfg.template.xml @WEB_DIR@/content/clientlibs/bpmn/deploy/src/activiti.cfg.xml`
 - Deploy processes **(replace tokens)**:
	 - `cd @WEB_DIR@/content/clientlibs/bpmn`
	 - `ant`
 - Add Activiti server to Kaltura server using the API **(replace tokens)**: `php @APP_DIR@/tests/standAloneClient/exec.php @APP_DIR@/tests/standAloneClient/activitiServer.xml`

##Caption added HTTP Notifications##
- Issue Type: new feature
- Issue ID: PLAT-2412

### Configuration ###
None

###Installation  
- Run:  
php /opt/kaltura/app/tests/standAloneClient/exec.php /opt/kaltura/app/tests/standAloneClient/captionAssetHttpNotifications.xml  

#### Known Issues & Limitations ####

None.  

----------
# Jupiter-10.4.0 #

##Drop Folder Email Notifications##
- Issue Type - new feature 

### Configuration ###
*plugins.ini*  
Add new line:  
DropFolderEventNotifications

###Installation  

- Run:  
php /opt/kaltura/app/deployment/base/scripts/installPlugins.php  
- Run:  
php /opt/kaltura/app/tests/standAloneClient/exec.php /opt/kaltura/app/tests/standAloneClient/emailDropFolderFileFailedStatus.xml  


----------
# Jupiter-10.2.0 #

## Webex Fix ## 
- Issue Type: bug fix

#### Configuration ####

*batch.ini* 

Add the following to the KAsyncImport worker configuartion:

params.webex.iterations                                                                 = 30  
params.webex.sleep 

## Unicorn Connector ##
- Issue Type: New Feature

#### Configuration ####

*plugins.ini*

Add the following line:

		UnicornDistribution

#### Deployment Scripts ####

		php deployment/updates/scripts/add_permissions/2014_12_30_unicorn_callback_service.php

##link externalmedia->add permission to basic permission objects##
- Issue Type: Back-End Request
- Issue ID: SUP-2708

#### Configuration ####

None.

#### Deployment Scripts ####

		php /opt/kaltura/app/deployment/updates/scripts/add_permissions/2015_01_11_add_externalmedia_add_permissions.php

#### Known Issues & Limitations ####

None.

##add flavorasset->getwebplayablebyentryid permission to basic playback role##
- Issue Type: Back-End Request
- Issue ID: KMS-5334

#### Configuration ####

None.

#### Deployment Scripts ####

		php /opt/kaltura/app/deployment/updates/scripts/add_permissions/2015_01_11_add_base_playback_role_flavorasset_getwebplayablebyentryid_permission.php

#### Known Issues & Limitations ####

None.

## Copy entitlement info from live entry to vod entry ##
- Issue Type: New Feature
- Issue ID: PLAT-2313

#### Configuration ####

*base.ini*

Add the following line to the the event_consumers[] list

		event_consumers[] = kObjectCreatedHandler

#### Deployment Scripts ####

None.

#### Known Issues & Limitations ####

None.

## Scheduled Tasks Enhancements 2 ##
- Issue Type: New Feature
- Issue ID: PLAT-1631

#### Configuration ####

*plugins.ini*

Add the following plugin to the list of plugins

		ScheduledTaskContentDistribution

#### Deployment Scripts ####

		php /opt/kaltura/app/deployment/updates/scripts/add_permissions/2014_11_25_scheduled_task_update.php

#### Known Issues & Limitations ####

None.

# Jupiter-10.1.0 #

## New UI Conf Type ##
- Issue Type: New Feature
- Issue ID: PLAT-2245

#### Configuration ####

*admin.ini*

Add the following line to the end of the settings.uiConfTypes[] list

		settings.uiConfTypes[] = Kaltura_Client_Enum_UiConfObjType::WEBCASTING

#### Deployment Scripts ####

None.

#### Known Issues & Limitations ####

None.

## Allow cue point search combined with entry filter ##
- Issue Type: New Feature
- Issue ID: PLAT-2208

#### Configuration ####

None.

#### Deployment Scripts ####

Need to re-index the entry in order for the cue points to get indexed on it.

#### Known Issues & Limitations ####

None.

# Jupiter-10.0.0 #

## Change emails_en to templace ##
- Issue Type: Back-End Request
- Issue ID: PLAT-2244

#### Configuration ####

** emails_en **

Requires cloning batch/batches/Mailer/emails_en.template.ini to batch/batches/Mailer/emails_en.ini and replace all place holders in it.


#### Deployment Scripts ####

None.

#### Known Issues & Limitations ####

None.


## sourceType filter ##
- Issue Type: Change Request
- Issue ID: PLAT-2148

#### Configuration ####

** sphinx/kaltura.conf **

Add the following line to the kaltura_entry class in configurations/sphinx/kaltura.conf (or merged from configurations/sphinx/kaltura.conf.template)

	rt_attr_uint = source


#### Deployment Scripts ####

None.

#### Known Issues & Limitations ####

None.

##add user->get permission to basic user role##
- Issue Type: Customer request
- Issue ID: SUP-2899

#### Configuration ####

None.

#### Deployment Scripts ####

		php deployment/updates/scripts/add_permissions/2014_11_30_BASE_USER_SESSION_PERMISSION_add_USER_GET_permissions.php

#### Known Issues & Limitations ####

None.

##Live reports - Export to CSV ##
- Issue Type: Customer request
- Issue ID: PLAT-2020

#### Configuration ####

Create the following directory for the generated reports - 
@WEB_DIR@/content/reports/live/

**local.ini**
requires the configuration of 'live_report_sender_email' and 'live_report_sender_name'.

**workers.ini**
Requires the addition of 'KAsyncLiveReportExport' worker definition and enabling.
a template can be found at batch.ini.template

#### Deployment Scripts ####

*Permissions*

- execute: php deployment/updates/scripts/add_permissions/2014_11_20_export_live_reports_to_csv.php

#### Known Issues & Limitations ####

None.

# IX-9.19.8 #

##Add support for multiple video/audio substreams##
- Issue Type: Back-End Request
- Issue ID: PLAT-1112

#### Configuration ####

**workers.ini**

under 'KAsyncBulkUpload'

		params.xmlSchemaVersion		= 3

#### Deployment Scripts ####

None.

#### Known Issues & Limitations ####

None.

##Scheduled Tasks Enhancements##
- Issue Type: Customer request
- Issue ID: P-282702

#### Configuration ####

**workers.ini**

under 'KScheduledTaskRunner' add

		maxProfiles = 50
		maxTotalCountAllowed = 10

#### Deployment Scripts ####

*DB*

- Run deployment/updates/sql/2014_11_02_add_max_total_count_allowed_column_to_scheduled_task_table.sql

#### Known Issues & Limitations ####

None.

# IX-9.19.7 #

##remove partner from 'exclude' list##
- Issue Type: Customer request
- Issue ID: SUP-2935

#### Configuration ####

**local.ini**

under 'global_whitelisted_domains_exclude'

-		12 = 520641

#### Deployment Scripts ####

None.

#### Known Issues & Limitations ####

None.

##added read permissions to delivery profiles##
- Issue Type: Customer request
- Issue ID: PLAT-2021

#### Configuration ####

requires adding the permission to the required user roles.

#### Deployment Scripts ####

execute:
	php deployment/updates/scripts/add_permissions/2014_10_29_read_permissions_delivery_profiles.php

#### Known Issues & Limitations ####

None.


##add sub type to thumb cue point##
- Issue Type: Application's request
- Issue ID: PLAT-2069

#### Configuration ####

**workers.ini**

under 'KAsyncBulkUpload'

		params.xmlSchemaVersion		= 3

#### Deployment Scripts ####

Need to run an update SQL statment:

		deployment/updates/sql/2014_11_11_set_thumb_cue_point_default_sub_type.sql

#### Known Issues & Limitations ####

None.

# IX-9.19.6 #

##Added PPT to image conversion##
- Issue Type: Back-End Request
- Issue ID: PLAT-1750

#### Configuration ####
In order to use, requires adding a new flavor_params such as: [Assuming 10025 == document / assetType / Image]

	INSERT INTO flavor_params VALUES (581230,0,0,'PPT 2 Image','',NULL,'PPT 2 Image',0,'0000-00-00 00:00:00','0000-00-00 00:00:00',NULL,0,'jpg','',1,'',0,0,0,0,0,0,0,0,0,NULL,NULL,'a:3:{s:18:\"FlavorVideoBitrate\";i:1;s:19:\"requiredPermissions\";a:0:{}s:9:"sizeWidth";i:940;}',0,NULL,1,0,0,'[[{\"id\":\"document.ppt2Img\",\"extra\":null,\"command\":null}]]',NULL,10025);

Place PowerPointConvertor.exe and PowerPointConvertor.exe.config in the same directory on your windows machine.
f.i. /opt/kaltura/exe

Requires adding a new windows worker. Sample configuration - 

	[KAsyncConvertPpt : KAsyncConvert]
	id = XXXXX
	friendlyName = Convert ppt
	params.isRemoteInput = 1
	params.isRemoteOutput = 0
	maximumExecutionTime = 36000
	maxJobsEachRun = 1
	filter.jobSubTypeIn = document.ppt2Img
	params.ppt2ImgCmd = C:\opt\kaltura\exe\PowerPointConvertor.exe
	baseLocalPath = C:\web\
	baseTempSharedPath = /opt/kaltura/web/tmp/convert/
	baseTempLocalPath = W:\tmp\convert\
	params.localFileRoot = C:/output
	params.remoteUrlDirectory = /output
	params.fileCacheExpire = 36000
	params.localTempPath = C:\opt\kaltura\tmp\convert
	params.sharedTempPath = W:\tmp\convert\ 

#### Deployment Scripts ####

	php deployment/base/scripts/installPlugins.php

#### Known Issues & Limitations ####
None.

##add partner to 'exclude' list##
- Issue Type: Customer request
- Issue ID: SUP-2935

#### Configuration ####

**local.ini**

under 'global_whitelisted_domains_exclude'

		12 = 520641

#### Deployment Scripts ####

None.

#### Known Issues & Limitations ####

None.

##add new XML drop folder configuration - KS validation##
- Issue Type: Back-End Request
- Issue ID: PLAT-1978

#### Configuration ####

**workers.ini**

under 'KAsyncBulkUpload'

		params.xmlSchemaVersion		= 2

#### Deployment Scripts ####

	php deployment/updates/scripts/add_permissions/2014_10_20_update_session_service_permissions.php to update batch permissions.

#### Known Issues & Limitations ####

None.

##Added user names column to Kaltura_entry table on sphinx##
- Issue Type: Customer request
- Issue ID: PLAT-1973

#### Configuration ####

Make sure configurations\sphinx\kaltura.conf is updated and the line - 
rt_field = user_names
is added under kaltura_entry part

#### Deployment Scripts ####

None.
As it requires adding a sphinx column, kaltura_entry must be re-populated.

#### Known Issues & Limitations ####

won't be updated when a user changes his first name / last name or screen name.

# IX-9.19.5 #

##add attachment service permissions to base-playback##
- Issue Type: Customer request
- Issue ID: PLAT-1830

#### Configuration ####

None.

#### Deployment Scripts ####

	php /opt/kaltura/app/deployment/updates/scripts/add_permissions/2014_10_19_base_playback_role_add_widevine_Attachment_permissions.php

#### Known Issues & Limitations ####

##'remove user from channel' notification template fix##
- Issue Type: Bug fix
- Issue ID: SUP-2132

#### Configuration ####

None.

#### Deployment Scripts ####

	php /opt/kaltura/app/deployment/updates/scripts/2014_06_10_update_remove_user_from_category_notification_template.php

#### Known Issues & Limitations ####

None.

# IX-9.19.4 #

## KS invalidation : PLAT-1556 ##

- Issue Type: Bug fix
- Issue ID: PLAT-1556

#### Configuration ####

None.


#### Deployment Scripts ####
	deployment/updates/sql/2014_09_02_add_session_type_invalid_session.sql


#### Known Issues & Limitations ####
None

# IX-9.19.3 #


## Get version action on system services - PLAT-1663 ##
- Issue Type: Back-End Request
- Issue ID: PLAT-1663


## Live params tags ##
Added web and mobile tags to live params

- Issue Type: Bug fix
- Issue ID: PLAT-1624

#### Configuration ####
None

#### Deployment Scripts ####
- deployment/updates/scripts/2014_01_12_update_live_params_permissions.php

#### Known Issues & Limitations ####
None

## Live recording optimization ##
Record all live assets and manage the recording on the API server side.

Add server get version action on system service

#### Configuration ####

None.

#### Deployment Scripts ####

	php deployment/updates/scripts/add_permissions/2014_09_04_add_system_get_version_permission.php	

#### Known Issues & Limitations ####

- For each deployment we need to make sure that the 'VERSION.txt' file is updated with the current server version.

## Live Analytics - PLAT-1862 ##

- Issue Type: Bug fix
- Issue ID: PLAT-1862

#### Configuration ####

**admin.ini**

- requires merge of the section - realTimeReports


#### Deployment Scripts ####
None.


#### Known Issues & Limitations ####
None

## BOA - PLAT-1554 ##

- Issue Type: Bug fix
- Issue ID: PLAT-1554

#### Configuration ####

None.


#### Deployment Scripts ####
None.


#### Known Issues & Limitations ####
Requires validating all the partners with ks_max_expiry_in_seconds different than 86400 and either nullify them or set them to 86400.

	mysql> select id, partner_name, status, ks_max_expiry_in_seconds from partner where ks_max_expiry_in_seconds != 86400 and status = 1;
	mysql> select id, partner_name, status, ks_max_expiry_in_seconds from partner where ks_max_expiry_in_seconds is null and status = 1;

## BOA - PLAT-1649 ##

- Issue Type: Bug fix
- Issue ID: PLAT-1649

#### Configuration ####

**admin.ini**

- requires merge of the section - disableResetPassword


#### Deployment Scripts ####
None.


#### Known Issues & Limitations ####
None

## BOA - PLAT-1558 ##

- Issue Type: Bug fix
- Issue ID: PLAT-1558

#### Configuration ####

**admin.ini**

- requires merge of the section - disableRememberMe


#### Deployment Scripts ####
None.


#### Known Issues & Limitations ####
None

## BOA - PLAT-1555 ##

- Issue Type: Bug fix
- Issue ID: PLAT-1555

#### Configuration ####
None


#### Deployment Scripts ####
None.


#### Known Issues & Limitations ####
See limitations within the JIRA ticket.


## BOA - PLAT-1548 ##

- Issue Type: Bug fix
- Issue ID: PLAT-1548

#### Configuration ####
None


#### Deployment Scripts ####

	php deployment/updates/scripts/add_permissions/2014_09_09_serve_report.php


#### Known Issues & Limitations ####
None.

# IX-9.19.2 #

## Delivery profiles UI ##
- Issue Type: Customer Request
- Issue ID: PLAT-1482

Adding a UI for delivery profiles

#### Configuration ####

**admin.ini**

	access.delivery.all = SYSTEM_ADMIN_PUBLISHER_USAGE

#### Deployment Scripts ####

Execute: 

	php deployment/updates/scripts/add_permissions/2014_09_07_delivery_profile_ui.php

#### Known Issues & Limitations ####

None

## Live analytics integration ##
- Issue Type: Customer Request
- Issue ID: PLAT-870

Added php support for live analytics

#### Prerequisites ####

- Player version: v2.17.rc7 or higher. (http://kgit.html5video.org/tags/v2.17.rc7/mwEmbedLoader.php)
- KMC version: V5.38

#### Configuration ####

**base.ini**

Should verify the following:

- live analytics version v0.1
- kmc version v5.38 

**local.ini**

Should fill with the WS path:
 
	live_analytics_web_service_url = @LIVE_ANALYTICS_WS@

Should set the live stats host:

	live_stats_host =  <LIVE_STATS_HOST_NAME>
	live_stats_host_https = <LIVE_STATS_HOST_NAME_HTTPS>


#### Deployment Scripts ####

Permission script execution:
	php deployment\updates\scripts\add_permissions\2014_07_17_live_reports_service.php

#### Apps installation ####
Install live analytics app by downloading _dist.zip from

	https://github.com/kaltura/LiveAnalytics/releases/tag/0.1
and unzipping it into 

	/opt/Kaltura/apps/liveanalytics/v0.1/

(discard "_dist" folder)
Deploy uiconf: 

	<liveanalytics_version>/deploy/config.ini


#### Known Issues & Limitations ####

Integration in process.

## Thumbnail encoder ##
reverting the current encoder to the old one

- Issue Type: 
- 
- 
- 
- 
- 
- Bug fix
- Issue ID: SUP-2581

#### Configuration ####

**Local.ini**

- bin_path_ffmpeg = ffmpeg
- ;bin_path_ffmpeg = /opt/kaltura/bin/x64/run/run-ffmpeg-0.10.sh


#### Deployment Scripts ####
None.


#### Known Issues & Limitations ####
None

# IX-9.19.1 #

## add widevine permission to base-playback ##
- Issue Type: Customer Request
- Issue ID: PLAT-1741

#### Configuration ####
None

#### Deployment Scripts ####

		php /opt/kaltura/app/deployment/updates/scripts/add_permissions/2014_08_21_base_playback_role_add_widevine_permission.php

#### Known Issues & Limitations ####
None

## add base-playback user role ##
- Issue Type: Customer Request
- Issue ID: PLAT-1565

Adding a user-role with playback capabilities only

#### Configuration ####
None

#### Deployment Scripts ####

		php /opt/kaltura/app/deployment/updates/scripts/add_permissions/2014_08_03_add_base_playback_role_permissions.php

#### Known Issues & Limitations ####
None

## Image entry plays/views ##
- Issue Type: Change Request
- Issue ID: KMS-3488

Match the number of plays to the number of views in image entries.

#### Configuration ####
None

#### Deployment Scripts ####

deployment/updates/scripts/2014_07_31_match_plays_to_views_for_image_entries.php realrun

* Note the **realrun** argument after the script name

#### Known Issues & Limitations ####
None


## Delivery profile ##
set is default to be false in default.

#### Configuration ####
None

#### Deployment Scripts ####
	/deployment/updates/sql/2014_07_27_delivery_profile_default_false.sql

#### Known Issues & Limitations ####
None

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


## Live recording optimization ##
Record all live assets and manage the recording on the API server side.

- Issue Type: Change Request 
- Issue ID: PLAT-1367
- Issue ID: PLAT-1274
- Issue ID: PLAT-1476
- Issue ID: SUP-2202

#### Configuration ####
- `base.ini` already changed to support `max_live_recording_duration_hours` of 24 hours.

#### Media-Server version ####
- New media-server version [3.0.9](https://github.com/kaltura/media-server/releases/download/rel-3.0.9/KalturaWowzaServer-3.0.9.jar "3.0.9") required. 

#### Deployment Scripts ####
None

#### Known Issues & Limitations ####
- The recording duration is limited to 24 hours.

# IX-9.18.0 #

+## Add base-playback user role ##
+- Issue Type: Customer Request
+- Issue ID: PLAT-1565
+
+Adding a user-role with playback capabilities only
+
+#### Configuration ####
+None
+
+#### Deployment Scripts ####
+
+		php /opt/kaltura/app/deployment/updates/scripts/add_permissions/2014_08_03_add_base_playback_role_permissions.php
+
+#### Known Issues & Limitations ####
+None
+

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

- deployment/updates/scripts/2014_03_10_addpushpublishconfigurationaction_added_to_livestream
- php


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
