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
#import "KalturaEventNotificationClientPlugin.h"

///////////////////////// enums /////////////////////////
// @package External
// @subpackage Kaltura
@interface KalturaEmailNotificationTemplatePriority : NSObject
+ (int)HIGH;
+ (int)NORMAL;
+ (int)LOW;
@end

// @package External
// @subpackage Kaltura
@interface KalturaEmailNotificationFormat : NSObject
+ (NSString*)HTML;
+ (NSString*)TEXT;
@end

// @package External
// @subpackage Kaltura
@interface KalturaEmailNotificationRecipientProviderType : NSObject
+ (NSString*)STATIC_LIST;
+ (NSString*)CATEGORY;
+ (NSString*)USER;
@end

// @package External
// @subpackage Kaltura
@interface KalturaEmailNotificationTemplateOrderBy : NSObject
+ (NSString*)CREATED_AT_ASC;
+ (NSString*)ID_ASC;
+ (NSString*)UPDATED_AT_ASC;
+ (NSString*)CREATED_AT_DESC;
+ (NSString*)ID_DESC;
+ (NSString*)UPDATED_AT_DESC;
@end

///////////////////////// classes /////////////////////////
// @package External
// @subpackage Kaltura
@interface KalturaEmailNotificationRecipient : KalturaObjectBase
// Recipient e-mail address
@property (nonatomic,retain) KalturaStringValue* email;
// Recipient name
@property (nonatomic,retain) KalturaStringValue* name;
- (KalturaFieldType)getTypeOfEmail;
- (NSString*)getObjectTypeOfEmail;
- (KalturaFieldType)getTypeOfName;
- (NSString*)getObjectTypeOfName;
@end

// @package External
// @subpackage Kaltura
// Abstract class representing the final output recipients going into the batch mechanism
@interface KalturaEmailNotificationRecipientJobData : KalturaObjectBase
// Provider type of the job data.
@property (nonatomic,copy,readonly) NSString* providerType;	// enum KalturaEmailNotificationRecipientProviderType
- (KalturaFieldType)getTypeOfProviderType;
@end

// @package External
// @subpackage Kaltura
// Abstract core class  which provides the recipients (to, CC, BCC) for an email notification
@interface KalturaEmailNotificationRecipientProvider : KalturaObjectBase
@end

// @package External
// @subpackage Kaltura
@interface KalturaCategoryUserProviderFilter : KalturaFilter
@property (nonatomic,copy) NSString* userIdEqual;
@property (nonatomic,copy) NSString* userIdIn;
@property (nonatomic,assign) int statusEqual;	// enum KalturaCategoryUserStatus
@property (nonatomic,copy) NSString* statusIn;
@property (nonatomic,assign) int createdAtGreaterThanOrEqual;
@property (nonatomic,assign) int createdAtLessThanOrEqual;
@property (nonatomic,assign) int updatedAtGreaterThanOrEqual;
@property (nonatomic,assign) int updatedAtLessThanOrEqual;
@property (nonatomic,assign) int updateMethodEqual;	// enum KalturaUpdateMethodType
@property (nonatomic,copy) NSString* updateMethodIn;
@property (nonatomic,copy) NSString* permissionNamesMatchAnd;
@property (nonatomic,copy) NSString* permissionNamesMatchOr;
- (KalturaFieldType)getTypeOfUserIdEqual;
- (KalturaFieldType)getTypeOfUserIdIn;
- (KalturaFieldType)getTypeOfStatusEqual;
- (KalturaFieldType)getTypeOfStatusIn;
- (KalturaFieldType)getTypeOfCreatedAtGreaterThanOrEqual;
- (KalturaFieldType)getTypeOfCreatedAtLessThanOrEqual;
- (KalturaFieldType)getTypeOfUpdatedAtGreaterThanOrEqual;
- (KalturaFieldType)getTypeOfUpdatedAtLessThanOrEqual;
- (KalturaFieldType)getTypeOfUpdateMethodEqual;
- (KalturaFieldType)getTypeOfUpdateMethodIn;
- (KalturaFieldType)getTypeOfPermissionNamesMatchAnd;
- (KalturaFieldType)getTypeOfPermissionNamesMatchOr;
- (void)setStatusEqualFromString:(NSString*)aPropVal;
- (void)setCreatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal;
- (void)setCreatedAtLessThanOrEqualFromString:(NSString*)aPropVal;
- (void)setUpdatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal;
- (void)setUpdatedAtLessThanOrEqualFromString:(NSString*)aPropVal;
- (void)setUpdateMethodEqualFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
// Job Data representing the provider of recipients for a single categoryId
@interface KalturaEmailNotificationCategoryRecipientJobData : KalturaEmailNotificationRecipientJobData
@property (nonatomic,retain) KalturaCategoryUserFilter* categoryUserFilter;
- (KalturaFieldType)getTypeOfCategoryUserFilter;
- (NSString*)getObjectTypeOfCategoryUserFilter;
@end

// @package External
// @subpackage Kaltura
// API object which provides the recipients of category related notifications.
@interface KalturaEmailNotificationCategoryRecipientProvider : KalturaEmailNotificationRecipientProvider
// The ID of the category whose subscribers should receive the email notification.
@property (nonatomic,retain) KalturaStringValue* categoryId;
@property (nonatomic,retain) KalturaCategoryUserProviderFilter* categoryUserFilter;
- (KalturaFieldType)getTypeOfCategoryId;
- (NSString*)getObjectTypeOfCategoryId;
- (KalturaFieldType)getTypeOfCategoryUserFilter;
- (NSString*)getObjectTypeOfCategoryUserFilter;
@end

// @package External
// @subpackage Kaltura
// JobData representing the static receipient array
@interface KalturaEmailNotificationStaticRecipientJobData : KalturaEmailNotificationRecipientJobData
// Email to emails and names
@property (nonatomic,retain) NSMutableArray* emailRecipients;	// of KalturaKeyValue elements
- (KalturaFieldType)getTypeOfEmailRecipients;
- (NSString*)getObjectTypeOfEmailRecipients;
@end

// @package External
// @subpackage Kaltura
// API class for recipient provider containing a static list of email recipients.
@interface KalturaEmailNotificationStaticRecipientProvider : KalturaEmailNotificationRecipientProvider
// Email to emails and names
@property (nonatomic,retain) NSMutableArray* emailRecipients;	// of KalturaEmailNotificationRecipient elements
- (KalturaFieldType)getTypeOfEmailRecipients;
- (NSString*)getObjectTypeOfEmailRecipients;
@end

// @package External
// @subpackage Kaltura
@interface KalturaEmailNotificationTemplate : KalturaEventNotificationTemplate
// Define the email body format
@property (nonatomic,copy) NSString* format;	// enum KalturaEmailNotificationFormat
// Define the email subject
@property (nonatomic,copy) NSString* subject;
// Define the email body content
@property (nonatomic,copy) NSString* body;
// Define the email sender email
@property (nonatomic,copy) NSString* fromEmail;
// Define the email sender name
@property (nonatomic,copy) NSString* fromName;
// Email recipient emails and names
@property (nonatomic,retain) KalturaEmailNotificationRecipientProvider* to;
// Email recipient emails and names
@property (nonatomic,retain) KalturaEmailNotificationRecipientProvider* cc;
// Email recipient emails and names
@property (nonatomic,retain) KalturaEmailNotificationRecipientProvider* bcc;
// Default email addresses to whom the reply should be sent.
@property (nonatomic,retain) KalturaEmailNotificationRecipientProvider* replyTo;
// Define the email priority
@property (nonatomic,assign) int priority;	// enum KalturaEmailNotificationTemplatePriority
// Email address that a reading confirmation will be sent
@property (nonatomic,copy) NSString* confirmReadingTo;
// Hostname to use in Message-Id and Received headers and as default HELLO string. 
// 	 If empty, the value returned by SERVER_NAME is used or 'localhost.localdomain'.
@property (nonatomic,copy) NSString* hostname;
// Sets the message ID to be used in the Message-Id header.
// 	 If empty, a unique id will be generated.
@property (nonatomic,copy) NSString* messageID;
// Adds a e-mail custom header
@property (nonatomic,retain) NSMutableArray* customHeaders;	// of KalturaKeyValue elements
// Define the content dynamic parameters
@property (nonatomic,retain) NSMutableArray* contentParameters;	// of KalturaEventNotificationParameter elements
- (KalturaFieldType)getTypeOfFormat;
- (KalturaFieldType)getTypeOfSubject;
- (KalturaFieldType)getTypeOfBody;
- (KalturaFieldType)getTypeOfFromEmail;
- (KalturaFieldType)getTypeOfFromName;
- (KalturaFieldType)getTypeOfTo;
- (NSString*)getObjectTypeOfTo;
- (KalturaFieldType)getTypeOfCc;
- (NSString*)getObjectTypeOfCc;
- (KalturaFieldType)getTypeOfBcc;
- (NSString*)getObjectTypeOfBcc;
- (KalturaFieldType)getTypeOfReplyTo;
- (NSString*)getObjectTypeOfReplyTo;
- (KalturaFieldType)getTypeOfPriority;
- (KalturaFieldType)getTypeOfConfirmReadingTo;
- (KalturaFieldType)getTypeOfHostname;
- (KalturaFieldType)getTypeOfMessageID;
- (KalturaFieldType)getTypeOfCustomHeaders;
- (NSString*)getObjectTypeOfCustomHeaders;
- (KalturaFieldType)getTypeOfContentParameters;
- (NSString*)getObjectTypeOfContentParameters;
- (void)setPriorityFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
// JobData representing the dynamic user receipient array
@interface KalturaEmailNotificationUserRecipientJobData : KalturaEmailNotificationRecipientJobData
@property (nonatomic,retain) KalturaUserFilter* filter;
- (KalturaFieldType)getTypeOfFilter;
- (NSString*)getObjectTypeOfFilter;
@end

// @package External
// @subpackage Kaltura
// API class for recipient provider which constructs a dynamic list of recipients according to a user filter
@interface KalturaEmailNotificationUserRecipientProvider : KalturaEmailNotificationRecipientProvider
@property (nonatomic,retain) KalturaUserFilter* filter;
- (KalturaFieldType)getTypeOfFilter;
- (NSString*)getObjectTypeOfFilter;
@end

// @package External
// @subpackage Kaltura
@interface KalturaEmailNotificationDispatchJobData : KalturaEventNotificationDispatchJobData
// Define the email sender email
@property (nonatomic,copy) NSString* fromEmail;
// Define the email sender name
@property (nonatomic,copy) NSString* fromName;
// Email recipient emails and names, key is mail address and value is the name
@property (nonatomic,retain) KalturaEmailNotificationRecipientJobData* to;
// Email cc emails and names, key is mail address and value is the name
@property (nonatomic,retain) KalturaEmailNotificationRecipientJobData* cc;
// Email bcc emails and names, key is mail address and value is the name
@property (nonatomic,retain) KalturaEmailNotificationRecipientJobData* bcc;
// Email addresses that a replies should be sent to, key is mail address and value is the name
@property (nonatomic,retain) KalturaEmailNotificationRecipientJobData* replyTo;
// Define the email priority
@property (nonatomic,assign) int priority;	// enum KalturaEmailNotificationTemplatePriority
// Email address that a reading confirmation will be sent to
@property (nonatomic,copy) NSString* confirmReadingTo;
// Hostname to use in Message-Id and Received headers and as default HELO string. 
// 	 If empty, the value returned by SERVER_NAME is used or 'localhost.localdomain'.
@property (nonatomic,copy) NSString* hostname;
// Sets the message ID to be used in the Message-Id header.
// 	 If empty, a unique id will be generated.
@property (nonatomic,copy) NSString* messageID;
// Adds a e-mail custom header
@property (nonatomic,retain) NSMutableArray* customHeaders;	// of KalturaKeyValue elements
// Define the content dynamic parameters
@property (nonatomic,retain) NSMutableArray* contentParameters;	// of KalturaKeyValue elements
- (KalturaFieldType)getTypeOfFromEmail;
- (KalturaFieldType)getTypeOfFromName;
- (KalturaFieldType)getTypeOfTo;
- (NSString*)getObjectTypeOfTo;
- (KalturaFieldType)getTypeOfCc;
- (NSString*)getObjectTypeOfCc;
- (KalturaFieldType)getTypeOfBcc;
- (NSString*)getObjectTypeOfBcc;
- (KalturaFieldType)getTypeOfReplyTo;
- (NSString*)getObjectTypeOfReplyTo;
- (KalturaFieldType)getTypeOfPriority;
- (KalturaFieldType)getTypeOfConfirmReadingTo;
- (KalturaFieldType)getTypeOfHostname;
- (KalturaFieldType)getTypeOfMessageID;
- (KalturaFieldType)getTypeOfCustomHeaders;
- (NSString*)getObjectTypeOfCustomHeaders;
- (KalturaFieldType)getTypeOfContentParameters;
- (NSString*)getObjectTypeOfContentParameters;
- (void)setPriorityFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
@interface KalturaEmailNotificationTemplateBaseFilter : KalturaEventNotificationTemplateFilter
@end

// @package External
// @subpackage Kaltura
@interface KalturaEmailNotificationTemplateFilter : KalturaEmailNotificationTemplateBaseFilter
@end

///////////////////////// services /////////////////////////
