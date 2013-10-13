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
// @package External
// @subpackage Kaltura
#import "../KalturaClient.h"

///////////////////////// enums /////////////////////////
// @package External
// @subpackage Kaltura
@interface KalturaEventNotificationTemplateStatus : NSObject
+ (int)DISABLED;
+ (int)ACTIVE;
+ (int)DELETED;
@end

// @package External
// @subpackage Kaltura
@interface KalturaEventNotificationEventObjectType : NSObject
+ (NSString*)ENTRY;
+ (NSString*)CATEGORY;
+ (NSString*)ASSET;
+ (NSString*)FLAVORASSET;
+ (NSString*)THUMBASSET;
+ (NSString*)KUSER;
+ (NSString*)ACCESSCONTROL;
+ (NSString*)BATCHJOB;
+ (NSString*)BULKUPLOADRESULT;
+ (NSString*)CATEGORYKUSER;
+ (NSString*)CONVERSIONPROFILE2;
+ (NSString*)FLAVORPARAMS;
+ (NSString*)FLAVORPARAMSCONVERSIONPROFILE;
+ (NSString*)FLAVORPARAMSOUTPUT;
+ (NSString*)GENERICSYNDICATIONFEED;
+ (NSString*)KUSERTOUSERROLE;
+ (NSString*)PARTNER;
+ (NSString*)PERMISSION;
+ (NSString*)PERMISSIONITEM;
+ (NSString*)PERMISSIONTOPERMISSIONITEM;
+ (NSString*)SCHEDULER;
+ (NSString*)SCHEDULERCONFIG;
+ (NSString*)SCHEDULERSTATUS;
+ (NSString*)SCHEDULERWORKER;
+ (NSString*)STORAGEPROFILE;
+ (NSString*)SYNDICATIONFEED;
+ (NSString*)THUMBPARAMS;
+ (NSString*)THUMBPARAMSOUTPUT;
+ (NSString*)UPLOADTOKEN;
+ (NSString*)USERLOGINDATA;
+ (NSString*)USERROLE;
+ (NSString*)WIDGET;
+ (NSString*)CATEGORYENTRY;
@end

// @package External
// @subpackage Kaltura
@interface KalturaEventNotificationEventType : NSObject
+ (NSString*)BATCH_JOB_STATUS;
+ (NSString*)OBJECT_ADDED;
+ (NSString*)OBJECT_CHANGED;
+ (NSString*)OBJECT_COPIED;
+ (NSString*)OBJECT_CREATED;
+ (NSString*)OBJECT_DATA_CHANGED;
+ (NSString*)OBJECT_DELETED;
+ (NSString*)OBJECT_ERASED;
+ (NSString*)OBJECT_READY_FOR_REPLACMENT;
+ (NSString*)OBJECT_SAVED;
+ (NSString*)OBJECT_UPDATED;
@end

// @package External
// @subpackage Kaltura
@interface KalturaEventNotificationTemplateOrderBy : NSObject
+ (NSString*)CREATED_AT_ASC;
+ (NSString*)ID_ASC;
+ (NSString*)UPDATED_AT_ASC;
+ (NSString*)CREATED_AT_DESC;
+ (NSString*)ID_DESC;
+ (NSString*)UPDATED_AT_DESC;
@end

// @package External
// @subpackage Kaltura
@interface KalturaEventNotificationTemplateType : NSObject
+ (NSString*)EMAIL;
@end

///////////////////////// classes /////////////////////////
// @package External
// @subpackage Kaltura
@interface KalturaEventCondition : KalturaObjectBase
@end

// @package External
// @subpackage Kaltura
@interface KalturaEventNotificationParameter : KalturaObjectBase
// The key in the subject and body to be replaced with the dynamic value
@property (nonatomic,copy) NSString* key;
// The dynamic value to be placed in the final output
@property (nonatomic,retain) KalturaStringValue* value;
- (KalturaFieldType)getTypeOfKey;
- (KalturaFieldType)getTypeOfValue;
- (NSString*)getObjectTypeOfValue;
@end

// @package External
// @subpackage Kaltura
@interface KalturaEventNotificationTemplate : KalturaObjectBase
@property (nonatomic,assign,readonly) int id;
@property (nonatomic,assign,readonly) int partnerId;
@property (nonatomic,copy) NSString* name;
@property (nonatomic,copy) NSString* systemName;
@property (nonatomic,copy) NSString* description;
@property (nonatomic,copy) NSString* type;	// enum KalturaEventNotificationTemplateType, insertonly
@property (nonatomic,assign,readonly) int status;	// enum KalturaEventNotificationTemplateStatus
@property (nonatomic,assign,readonly) int createdAt;
@property (nonatomic,assign,readonly) int updatedAt;
// Define that the template could be dispatched manually from the API
@property (nonatomic,assign) BOOL manualDispatchEnabled;
// Define that the template could be dispatched automatically by the system
@property (nonatomic,assign) BOOL automaticDispatchEnabled;
// Define the event that should trigger this notification
@property (nonatomic,copy) NSString* eventType;	// enum KalturaEventNotificationEventType
// Define the object that raied the event that should trigger this notification
@property (nonatomic,copy) NSString* eventObjectType;	// enum KalturaEventNotificationEventObjectType
// Define the conditions that cause this notification to be triggered
@property (nonatomic,retain) NSMutableArray* eventConditions;	// of KalturaEventCondition elements
- (KalturaFieldType)getTypeOfId;
- (KalturaFieldType)getTypeOfPartnerId;
- (KalturaFieldType)getTypeOfName;
- (KalturaFieldType)getTypeOfSystemName;
- (KalturaFieldType)getTypeOfDescription;
- (KalturaFieldType)getTypeOfType;
- (KalturaFieldType)getTypeOfStatus;
- (KalturaFieldType)getTypeOfCreatedAt;
- (KalturaFieldType)getTypeOfUpdatedAt;
- (KalturaFieldType)getTypeOfManualDispatchEnabled;
- (KalturaFieldType)getTypeOfAutomaticDispatchEnabled;
- (KalturaFieldType)getTypeOfEventType;
- (KalturaFieldType)getTypeOfEventObjectType;
- (KalturaFieldType)getTypeOfEventConditions;
- (NSString*)getObjectTypeOfEventConditions;
- (void)setIdFromString:(NSString*)aPropVal;
- (void)setPartnerIdFromString:(NSString*)aPropVal;
- (void)setStatusFromString:(NSString*)aPropVal;
- (void)setCreatedAtFromString:(NSString*)aPropVal;
- (void)setUpdatedAtFromString:(NSString*)aPropVal;
- (void)setManualDispatchEnabledFromString:(NSString*)aPropVal;
- (void)setAutomaticDispatchEnabledFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
@interface KalturaEventNotificationTemplateListResponse : KalturaObjectBase
@property (nonatomic,retain,readonly) NSMutableArray* objects;	// of KalturaEventNotificationTemplate elements
@property (nonatomic,assign,readonly) int totalCount;
- (KalturaFieldType)getTypeOfObjects;
- (NSString*)getObjectTypeOfObjects;
- (KalturaFieldType)getTypeOfTotalCount;
- (void)setTotalCountFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
@interface KalturaEventFieldCondition : KalturaEventCondition
// The field to be evaluated at runtime
@property (nonatomic,retain) KalturaBooleanField* field;
- (KalturaFieldType)getTypeOfField;
- (NSString*)getObjectTypeOfField;
@end

// @package External
// @subpackage Kaltura
@interface KalturaEventNotificationDispatchJobData : KalturaJobData
@property (nonatomic,assign) int templateId;
- (KalturaFieldType)getTypeOfTemplateId;
- (void)setTemplateIdFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
@interface KalturaEventNotificationTemplateBaseFilter : KalturaFilter
@property (nonatomic,assign) int idEqual;
@property (nonatomic,copy) NSString* idIn;
@property (nonatomic,assign) int partnerIdEqual;
@property (nonatomic,copy) NSString* partnerIdIn;
@property (nonatomic,copy) NSString* typeEqual;	// enum KalturaEventNotificationTemplateType
@property (nonatomic,copy) NSString* typeIn;
@property (nonatomic,assign) int statusEqual;	// enum KalturaEventNotificationTemplateStatus
@property (nonatomic,copy) NSString* statusIn;
@property (nonatomic,assign) int createdAtGreaterThanOrEqual;
@property (nonatomic,assign) int createdAtLessThanOrEqual;
@property (nonatomic,assign) int updatedAtGreaterThanOrEqual;
@property (nonatomic,assign) int updatedAtLessThanOrEqual;
- (KalturaFieldType)getTypeOfIdEqual;
- (KalturaFieldType)getTypeOfIdIn;
- (KalturaFieldType)getTypeOfPartnerIdEqual;
- (KalturaFieldType)getTypeOfPartnerIdIn;
- (KalturaFieldType)getTypeOfTypeEqual;
- (KalturaFieldType)getTypeOfTypeIn;
- (KalturaFieldType)getTypeOfStatusEqual;
- (KalturaFieldType)getTypeOfStatusIn;
- (KalturaFieldType)getTypeOfCreatedAtGreaterThanOrEqual;
- (KalturaFieldType)getTypeOfCreatedAtLessThanOrEqual;
- (KalturaFieldType)getTypeOfUpdatedAtGreaterThanOrEqual;
- (KalturaFieldType)getTypeOfUpdatedAtLessThanOrEqual;
- (void)setIdEqualFromString:(NSString*)aPropVal;
- (void)setPartnerIdEqualFromString:(NSString*)aPropVal;
- (void)setStatusEqualFromString:(NSString*)aPropVal;
- (void)setCreatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal;
- (void)setCreatedAtLessThanOrEqualFromString:(NSString*)aPropVal;
- (void)setUpdatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal;
- (void)setUpdatedAtLessThanOrEqualFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
@interface KalturaEventNotificationTemplateFilter : KalturaEventNotificationTemplateBaseFilter
@end

///////////////////////// services /////////////////////////
// @package External
// @subpackage Kaltura
// Event notification template service lets you create and manage event notification templates
@interface KalturaEventNotificationTemplateService : KalturaServiceBase
// Allows you to add a new event notification template object
- (KalturaEventNotificationTemplate*)addWithEventNotificationTemplate:(KalturaEventNotificationTemplate*)aEventNotificationTemplate;
// Allows you to clone exiting event notification template object and create a new one with similar configuration
- (KalturaEventNotificationTemplate*)cloneWithId:(int)aId withEventNotificationTemplate:(KalturaEventNotificationTemplate*)aEventNotificationTemplate;
- (KalturaEventNotificationTemplate*)cloneWithId:(int)aId;
// Retrieve an event notification template object by id
- (KalturaEventNotificationTemplate*)getWithId:(int)aId;
// Update an existing event notification template object
- (KalturaEventNotificationTemplate*)updateWithId:(int)aId withEventNotificationTemplate:(KalturaEventNotificationTemplate*)aEventNotificationTemplate;
// Update event notification template status by id
- (KalturaEventNotificationTemplate*)updateStatusWithId:(int)aId withStatus:(int)aStatus;
// Delete an event notification template object
- (void)deleteWithId:(int)aId;
// list event notification template objects
- (KalturaEventNotificationTemplateListResponse*)listWithFilter:(KalturaEventNotificationTemplateFilter*)aFilter withPager:(KalturaFilterPager*)aPager;
- (KalturaEventNotificationTemplateListResponse*)listWithFilter:(KalturaEventNotificationTemplateFilter*)aFilter;
- (KalturaEventNotificationTemplateListResponse*)list;
- (KalturaEventNotificationTemplateListResponse*)listByPartnerWithFilter:(KalturaPartnerFilter*)aFilter withPager:(KalturaFilterPager*)aPager;
- (KalturaEventNotificationTemplateListResponse*)listByPartnerWithFilter:(KalturaPartnerFilter*)aFilter;
- (KalturaEventNotificationTemplateListResponse*)listByPartner;
// Dispatch event notification object by id
- (int)dispatchWithId:(int)aId withData:(KalturaEventNotificationDispatchJobData*)aData;
// Action lists the template partner event notification templates.
- (KalturaEventNotificationTemplateListResponse*)listTemplatesWithFilter:(KalturaEventNotificationTemplateFilter*)aFilter withPager:(KalturaFilterPager*)aPager;
- (KalturaEventNotificationTemplateListResponse*)listTemplatesWithFilter:(KalturaEventNotificationTemplateFilter*)aFilter;
- (KalturaEventNotificationTemplateListResponse*)listTemplates;
@end

@interface KalturaEventNotificationClientPlugin : KalturaClientPlugin
{
	KalturaEventNotificationTemplateService* _eventNotificationTemplate;
}

@property (nonatomic, assign) KalturaClientBase* client;
@property (nonatomic, readonly) KalturaEventNotificationTemplateService* eventNotificationTemplate;
@end

