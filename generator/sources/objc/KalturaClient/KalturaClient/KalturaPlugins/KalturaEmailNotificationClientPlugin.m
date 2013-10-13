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
#import "KalturaEmailNotificationClientPlugin.h"

///////////////////////// enums /////////////////////////
@implementation KalturaEmailNotificationTemplatePriority
+ (int)HIGH
{
    return 1;
}
+ (int)NORMAL
{
    return 3;
}
+ (int)LOW
{
    return 5;
}
@end

@implementation KalturaEmailNotificationFormat
+ (NSString*)HTML
{
    return @"1";
}
+ (NSString*)TEXT
{
    return @"2";
}
@end

@implementation KalturaEmailNotificationRecipientProviderType
+ (NSString*)STATIC_LIST
{
    return @"1";
}
+ (NSString*)CATEGORY
{
    return @"2";
}
+ (NSString*)USER
{
    return @"3";
}
@end

@implementation KalturaEmailNotificationTemplateOrderBy
+ (NSString*)CREATED_AT_ASC
{
    return @"+createdAt";
}
+ (NSString*)ID_ASC
{
    return @"+id";
}
+ (NSString*)UPDATED_AT_ASC
{
    return @"+updatedAt";
}
+ (NSString*)CREATED_AT_DESC
{
    return @"-createdAt";
}
+ (NSString*)ID_DESC
{
    return @"-id";
}
+ (NSString*)UPDATED_AT_DESC
{
    return @"-updatedAt";
}
@end

///////////////////////// classes /////////////////////////
@implementation KalturaEmailNotificationRecipient
@synthesize email = _email;
@synthesize name = _name;

- (KalturaFieldType)getTypeOfEmail
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfEmail
{
    return @"KalturaStringValue";
}

- (KalturaFieldType)getTypeOfName
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfName
{
    return @"KalturaStringValue";
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaEmailNotificationRecipient"];
    [aParams addIfDefinedKey:@"email" withObject:self.email];
    [aParams addIfDefinedKey:@"name" withObject:self.name];
}

- (void)dealloc
{
    [self->_email release];
    [self->_name release];
    [super dealloc];
}

@end

@interface KalturaEmailNotificationRecipientJobData()
@property (nonatomic,copy) NSString* providerType;
@end

@implementation KalturaEmailNotificationRecipientJobData
@synthesize providerType = _providerType;

- (KalturaFieldType)getTypeOfProviderType
{
    return KFT_String;
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaEmailNotificationRecipientJobData"];
}

- (void)dealloc
{
    [self->_providerType release];
    [super dealloc];
}

@end

@implementation KalturaEmailNotificationRecipientProvider
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaEmailNotificationRecipientProvider"];
}

@end

@implementation KalturaCategoryUserProviderFilter
@synthesize userIdEqual = _userIdEqual;
@synthesize userIdIn = _userIdIn;
@synthesize statusEqual = _statusEqual;
@synthesize statusIn = _statusIn;
@synthesize createdAtGreaterThanOrEqual = _createdAtGreaterThanOrEqual;
@synthesize createdAtLessThanOrEqual = _createdAtLessThanOrEqual;
@synthesize updatedAtGreaterThanOrEqual = _updatedAtGreaterThanOrEqual;
@synthesize updatedAtLessThanOrEqual = _updatedAtLessThanOrEqual;
@synthesize updateMethodEqual = _updateMethodEqual;
@synthesize updateMethodIn = _updateMethodIn;
@synthesize permissionNamesMatchAnd = _permissionNamesMatchAnd;
@synthesize permissionNamesMatchOr = _permissionNamesMatchOr;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_statusEqual = KALTURA_UNDEF_INT;
    self->_createdAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_createdAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtGreaterThanOrEqual = KALTURA_UNDEF_INT;
    self->_updatedAtLessThanOrEqual = KALTURA_UNDEF_INT;
    self->_updateMethodEqual = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfUserIdEqual
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfUserIdIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfStatusEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfStatusIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCreatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfCreatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtGreaterThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdatedAtLessThanOrEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdateMethodEqual
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfUpdateMethodIn
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPermissionNamesMatchAnd
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfPermissionNamesMatchOr
{
    return KFT_String;
}

- (void)setStatusEqualFromString:(NSString*)aPropVal
{
    self.statusEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setCreatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.createdAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtGreaterThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdatedAtLessThanOrEqualFromString:(NSString*)aPropVal
{
    self.updatedAtLessThanOrEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)setUpdateMethodEqualFromString:(NSString*)aPropVal
{
    self.updateMethodEqual = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaCategoryUserProviderFilter"];
    [aParams addIfDefinedKey:@"userIdEqual" withString:self.userIdEqual];
    [aParams addIfDefinedKey:@"userIdIn" withString:self.userIdIn];
    [aParams addIfDefinedKey:@"statusEqual" withInt:self.statusEqual];
    [aParams addIfDefinedKey:@"statusIn" withString:self.statusIn];
    [aParams addIfDefinedKey:@"createdAtGreaterThanOrEqual" withInt:self.createdAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"createdAtLessThanOrEqual" withInt:self.createdAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtGreaterThanOrEqual" withInt:self.updatedAtGreaterThanOrEqual];
    [aParams addIfDefinedKey:@"updatedAtLessThanOrEqual" withInt:self.updatedAtLessThanOrEqual];
    [aParams addIfDefinedKey:@"updateMethodEqual" withInt:self.updateMethodEqual];
    [aParams addIfDefinedKey:@"updateMethodIn" withString:self.updateMethodIn];
    [aParams addIfDefinedKey:@"permissionNamesMatchAnd" withString:self.permissionNamesMatchAnd];
    [aParams addIfDefinedKey:@"permissionNamesMatchOr" withString:self.permissionNamesMatchOr];
}

- (void)dealloc
{
    [self->_userIdEqual release];
    [self->_userIdIn release];
    [self->_statusIn release];
    [self->_updateMethodIn release];
    [self->_permissionNamesMatchAnd release];
    [self->_permissionNamesMatchOr release];
    [super dealloc];
}

@end

@implementation KalturaEmailNotificationCategoryRecipientJobData
@synthesize categoryUserFilter = _categoryUserFilter;

- (KalturaFieldType)getTypeOfCategoryUserFilter
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfCategoryUserFilter
{
    return @"KalturaCategoryUserFilter";
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaEmailNotificationCategoryRecipientJobData"];
    [aParams addIfDefinedKey:@"categoryUserFilter" withObject:self.categoryUserFilter];
}

- (void)dealloc
{
    [self->_categoryUserFilter release];
    [super dealloc];
}

@end

@implementation KalturaEmailNotificationCategoryRecipientProvider
@synthesize categoryId = _categoryId;
@synthesize categoryUserFilter = _categoryUserFilter;

- (KalturaFieldType)getTypeOfCategoryId
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfCategoryId
{
    return @"KalturaStringValue";
}

- (KalturaFieldType)getTypeOfCategoryUserFilter
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfCategoryUserFilter
{
    return @"KalturaCategoryUserProviderFilter";
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaEmailNotificationCategoryRecipientProvider"];
    [aParams addIfDefinedKey:@"categoryId" withObject:self.categoryId];
    [aParams addIfDefinedKey:@"categoryUserFilter" withObject:self.categoryUserFilter];
}

- (void)dealloc
{
    [self->_categoryId release];
    [self->_categoryUserFilter release];
    [super dealloc];
}

@end

@implementation KalturaEmailNotificationStaticRecipientJobData
@synthesize emailRecipients = _emailRecipients;

- (KalturaFieldType)getTypeOfEmailRecipients
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfEmailRecipients
{
    return @"KalturaKeyValue";
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaEmailNotificationStaticRecipientJobData"];
    [aParams addIfDefinedKey:@"emailRecipients" withArray:self.emailRecipients];
}

- (void)dealloc
{
    [self->_emailRecipients release];
    [super dealloc];
}

@end

@implementation KalturaEmailNotificationStaticRecipientProvider
@synthesize emailRecipients = _emailRecipients;

- (KalturaFieldType)getTypeOfEmailRecipients
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfEmailRecipients
{
    return @"KalturaEmailNotificationRecipient";
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaEmailNotificationStaticRecipientProvider"];
    [aParams addIfDefinedKey:@"emailRecipients" withArray:self.emailRecipients];
}

- (void)dealloc
{
    [self->_emailRecipients release];
    [super dealloc];
}

@end

@implementation KalturaEmailNotificationTemplate
@synthesize format = _format;
@synthesize subject = _subject;
@synthesize body = _body;
@synthesize fromEmail = _fromEmail;
@synthesize fromName = _fromName;
@synthesize to = _to;
@synthesize cc = _cc;
@synthesize bcc = _bcc;
@synthesize replyTo = _replyTo;
@synthesize priority = _priority;
@synthesize confirmReadingTo = _confirmReadingTo;
@synthesize hostname = _hostname;
@synthesize messageID = _messageID;
@synthesize customHeaders = _customHeaders;
@synthesize contentParameters = _contentParameters;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_priority = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfFormat
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfSubject
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfBody
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFromEmail
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFromName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTo
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfTo
{
    return @"KalturaEmailNotificationRecipientProvider";
}

- (KalturaFieldType)getTypeOfCc
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfCc
{
    return @"KalturaEmailNotificationRecipientProvider";
}

- (KalturaFieldType)getTypeOfBcc
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfBcc
{
    return @"KalturaEmailNotificationRecipientProvider";
}

- (KalturaFieldType)getTypeOfReplyTo
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfReplyTo
{
    return @"KalturaEmailNotificationRecipientProvider";
}

- (KalturaFieldType)getTypeOfPriority
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfConfirmReadingTo
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfHostname
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfMessageID
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCustomHeaders
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfCustomHeaders
{
    return @"KalturaKeyValue";
}

- (KalturaFieldType)getTypeOfContentParameters
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfContentParameters
{
    return @"KalturaEventNotificationParameter";
}

- (void)setPriorityFromString:(NSString*)aPropVal
{
    self.priority = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaEmailNotificationTemplate"];
    [aParams addIfDefinedKey:@"format" withString:self.format];
    [aParams addIfDefinedKey:@"subject" withString:self.subject];
    [aParams addIfDefinedKey:@"body" withString:self.body];
    [aParams addIfDefinedKey:@"fromEmail" withString:self.fromEmail];
    [aParams addIfDefinedKey:@"fromName" withString:self.fromName];
    [aParams addIfDefinedKey:@"to" withObject:self.to];
    [aParams addIfDefinedKey:@"cc" withObject:self.cc];
    [aParams addIfDefinedKey:@"bcc" withObject:self.bcc];
    [aParams addIfDefinedKey:@"replyTo" withObject:self.replyTo];
    [aParams addIfDefinedKey:@"priority" withInt:self.priority];
    [aParams addIfDefinedKey:@"confirmReadingTo" withString:self.confirmReadingTo];
    [aParams addIfDefinedKey:@"hostname" withString:self.hostname];
    [aParams addIfDefinedKey:@"messageID" withString:self.messageID];
    [aParams addIfDefinedKey:@"customHeaders" withArray:self.customHeaders];
    [aParams addIfDefinedKey:@"contentParameters" withArray:self.contentParameters];
}

- (void)dealloc
{
    [self->_format release];
    [self->_subject release];
    [self->_body release];
    [self->_fromEmail release];
    [self->_fromName release];
    [self->_to release];
    [self->_cc release];
    [self->_bcc release];
    [self->_replyTo release];
    [self->_confirmReadingTo release];
    [self->_hostname release];
    [self->_messageID release];
    [self->_customHeaders release];
    [self->_contentParameters release];
    [super dealloc];
}

@end

@implementation KalturaEmailNotificationUserRecipientJobData
@synthesize filter = _filter;

- (KalturaFieldType)getTypeOfFilter
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfFilter
{
    return @"KalturaUserFilter";
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaEmailNotificationUserRecipientJobData"];
    [aParams addIfDefinedKey:@"filter" withObject:self.filter];
}

- (void)dealloc
{
    [self->_filter release];
    [super dealloc];
}

@end

@implementation KalturaEmailNotificationUserRecipientProvider
@synthesize filter = _filter;

- (KalturaFieldType)getTypeOfFilter
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfFilter
{
    return @"KalturaUserFilter";
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaEmailNotificationUserRecipientProvider"];
    [aParams addIfDefinedKey:@"filter" withObject:self.filter];
}

- (void)dealloc
{
    [self->_filter release];
    [super dealloc];
}

@end

@implementation KalturaEmailNotificationDispatchJobData
@synthesize fromEmail = _fromEmail;
@synthesize fromName = _fromName;
@synthesize to = _to;
@synthesize cc = _cc;
@synthesize bcc = _bcc;
@synthesize replyTo = _replyTo;
@synthesize priority = _priority;
@synthesize confirmReadingTo = _confirmReadingTo;
@synthesize hostname = _hostname;
@synthesize messageID = _messageID;
@synthesize customHeaders = _customHeaders;
@synthesize contentParameters = _contentParameters;

- (id)init
{
    self = [super init];
    if (self == nil)
        return nil;
    self->_priority = KALTURA_UNDEF_INT;
    return self;
}

- (KalturaFieldType)getTypeOfFromEmail
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfFromName
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfTo
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfTo
{
    return @"KalturaEmailNotificationRecipientJobData";
}

- (KalturaFieldType)getTypeOfCc
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfCc
{
    return @"KalturaEmailNotificationRecipientJobData";
}

- (KalturaFieldType)getTypeOfBcc
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfBcc
{
    return @"KalturaEmailNotificationRecipientJobData";
}

- (KalturaFieldType)getTypeOfReplyTo
{
    return KFT_Object;
}

- (NSString*)getObjectTypeOfReplyTo
{
    return @"KalturaEmailNotificationRecipientJobData";
}

- (KalturaFieldType)getTypeOfPriority
{
    return KFT_Int;
}

- (KalturaFieldType)getTypeOfConfirmReadingTo
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfHostname
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfMessageID
{
    return KFT_String;
}

- (KalturaFieldType)getTypeOfCustomHeaders
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfCustomHeaders
{
    return @"KalturaKeyValue";
}

- (KalturaFieldType)getTypeOfContentParameters
{
    return KFT_Array;
}

- (NSString*)getObjectTypeOfContentParameters
{
    return @"KalturaKeyValue";
}

- (void)setPriorityFromString:(NSString*)aPropVal
{
    self.priority = [KalturaSimpleTypeParser parseInt:aPropVal];
}

- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaEmailNotificationDispatchJobData"];
    [aParams addIfDefinedKey:@"fromEmail" withString:self.fromEmail];
    [aParams addIfDefinedKey:@"fromName" withString:self.fromName];
    [aParams addIfDefinedKey:@"to" withObject:self.to];
    [aParams addIfDefinedKey:@"cc" withObject:self.cc];
    [aParams addIfDefinedKey:@"bcc" withObject:self.bcc];
    [aParams addIfDefinedKey:@"replyTo" withObject:self.replyTo];
    [aParams addIfDefinedKey:@"priority" withInt:self.priority];
    [aParams addIfDefinedKey:@"confirmReadingTo" withString:self.confirmReadingTo];
    [aParams addIfDefinedKey:@"hostname" withString:self.hostname];
    [aParams addIfDefinedKey:@"messageID" withString:self.messageID];
    [aParams addIfDefinedKey:@"customHeaders" withArray:self.customHeaders];
    [aParams addIfDefinedKey:@"contentParameters" withArray:self.contentParameters];
}

- (void)dealloc
{
    [self->_fromEmail release];
    [self->_fromName release];
    [self->_to release];
    [self->_cc release];
    [self->_bcc release];
    [self->_replyTo release];
    [self->_confirmReadingTo release];
    [self->_hostname release];
    [self->_messageID release];
    [self->_customHeaders release];
    [self->_contentParameters release];
    [super dealloc];
}

@end

@implementation KalturaEmailNotificationTemplateBaseFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaEmailNotificationTemplateBaseFilter"];
}

@end

@implementation KalturaEmailNotificationTemplateFilter
- (void)toParams:(KalturaParams*)aParams isSuper:(BOOL)aIsSuper
{
    [super toParams:aParams isSuper:YES];
    if (!aIsSuper)
        [aParams putKey:@"objectType" withString:@"KalturaEmailNotificationTemplateFilter"];
}

@end

///////////////////////// services /////////////////////////
