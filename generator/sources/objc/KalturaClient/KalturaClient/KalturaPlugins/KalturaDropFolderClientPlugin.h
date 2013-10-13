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
@interface KalturaDropFolderContentFileHandlerMatchPolicy : NSObject
+ (int)ADD_AS_NEW;
+ (int)MATCH_EXISTING_OR_ADD_AS_NEW;
+ (int)MATCH_EXISTING_OR_KEEP_IN_FOLDER;
@end

// @package External
// @subpackage Kaltura
@interface KalturaDropFolderFileDeletePolicy : NSObject
+ (int)MANUAL_DELETE;
+ (int)AUTO_DELETE;
@end

// @package External
// @subpackage Kaltura
@interface KalturaDropFolderFileStatus : NSObject
+ (int)UPLOADING;
+ (int)PENDING;
+ (int)WAITING;
+ (int)HANDLED;
+ (int)IGNORE;
+ (int)DELETED;
+ (int)PURGED;
+ (int)NO_MATCH;
+ (int)ERROR_HANDLING;
+ (int)ERROR_DELETING;
+ (int)DOWNLOADING;
+ (int)ERROR_DOWNLOADING;
+ (int)PROCESSING;
+ (int)PARSED;
+ (int)DETECTED;
@end

// @package External
// @subpackage Kaltura
@interface KalturaDropFolderStatus : NSObject
+ (int)DISABLED;
+ (int)ENABLED;
+ (int)DELETED;
+ (int)ERROR;
@end

// @package External
// @subpackage Kaltura
@interface KalturaDropFolderErrorCode : NSObject
+ (NSString*)ERROR_CONNECT;
+ (NSString*)ERROR_AUTENTICATE;
+ (NSString*)ERROR_GET_PHISICAL_FILE_LIST;
+ (NSString*)ERROR_GET_DB_FILE_LIST;
+ (NSString*)DROP_FOLDER_APP_ERROR;
+ (NSString*)CONTENT_MATCH_POLICY_UNDEFINED;
@end

// @package External
// @subpackage Kaltura
@interface KalturaDropFolderFileErrorCode : NSObject
+ (NSString*)ERROR_ADDING_BULK_UPLOAD;
+ (NSString*)ERROR_ADD_CONTENT_RESOURCE;
+ (NSString*)ERROR_IN_BULK_UPLOAD;
+ (NSString*)ERROR_WRITING_TEMP_FILE;
+ (NSString*)LOCAL_FILE_WRONG_CHECKSUM;
+ (NSString*)LOCAL_FILE_WRONG_SIZE;
+ (NSString*)MALFORMED_XML_FILE;
+ (NSString*)XML_FILE_SIZE_EXCEED_LIMIT;
+ (NSString*)ERROR_UPDATE_ENTRY;
+ (NSString*)ERROR_ADD_ENTRY;
+ (NSString*)FLAVOR_NOT_FOUND;
+ (NSString*)FLAVOR_MISSING_IN_FILE_NAME;
+ (NSString*)SLUG_REGEX_NO_MATCH;
+ (NSString*)ERROR_READING_FILE;
+ (NSString*)ERROR_DOWNLOADING_FILE;
+ (NSString*)ERROR_UPDATE_FILE;
+ (NSString*)ERROR_ADDING_CONTENT_PROCESSOR;
+ (NSString*)ERROR_IN_CONTENT_PROCESSOR;
+ (NSString*)ERROR_DELETING_FILE;
+ (NSString*)FILE_NO_MATCH;
@end

// @package External
// @subpackage Kaltura
@interface KalturaDropFolderFileHandlerType : NSObject
+ (NSString*)XML;
+ (NSString*)CONTENT;
@end

// @package External
// @subpackage Kaltura
@interface KalturaDropFolderFileOrderBy : NSObject
+ (NSString*)CREATED_AT_ASC;
+ (NSString*)FILE_NAME_ASC;
+ (NSString*)FILE_SIZE_ASC;
+ (NSString*)FILE_SIZE_LAST_SET_AT_ASC;
+ (NSString*)ID_ASC;
+ (NSString*)PARSED_FLAVOR_ASC;
+ (NSString*)PARSED_SLUG_ASC;
+ (NSString*)UPDATED_AT_ASC;
+ (NSString*)CREATED_AT_DESC;
+ (NSString*)FILE_NAME_DESC;
+ (NSString*)FILE_SIZE_DESC;
+ (NSString*)FILE_SIZE_LAST_SET_AT_DESC;
+ (NSString*)ID_DESC;
+ (NSString*)PARSED_FLAVOR_DESC;
+ (NSString*)PARSED_SLUG_DESC;
+ (NSString*)UPDATED_AT_DESC;
@end

// @package External
// @subpackage Kaltura
@interface KalturaDropFolderOrderBy : NSObject
+ (NSString*)CREATED_AT_ASC;
+ (NSString*)ID_ASC;
+ (NSString*)NAME_ASC;
+ (NSString*)UPDATED_AT_ASC;
+ (NSString*)CREATED_AT_DESC;
+ (NSString*)ID_DESC;
+ (NSString*)NAME_DESC;
+ (NSString*)UPDATED_AT_DESC;
@end

// @package External
// @subpackage Kaltura
@interface KalturaDropFolderType : NSObject
+ (NSString*)LOCAL;
+ (NSString*)FTP;
+ (NSString*)SCP;
+ (NSString*)SFTP;
+ (NSString*)S3;
@end

// @package External
// @subpackage Kaltura
@interface KalturaFtpDropFolderOrderBy : NSObject
+ (NSString*)CREATED_AT_ASC;
+ (NSString*)ID_ASC;
+ (NSString*)NAME_ASC;
+ (NSString*)UPDATED_AT_ASC;
+ (NSString*)CREATED_AT_DESC;
+ (NSString*)ID_DESC;
+ (NSString*)NAME_DESC;
+ (NSString*)UPDATED_AT_DESC;
@end

// @package External
// @subpackage Kaltura
@interface KalturaRemoteDropFolderOrderBy : NSObject
+ (NSString*)CREATED_AT_ASC;
+ (NSString*)ID_ASC;
+ (NSString*)NAME_ASC;
+ (NSString*)UPDATED_AT_ASC;
+ (NSString*)CREATED_AT_DESC;
+ (NSString*)ID_DESC;
+ (NSString*)NAME_DESC;
+ (NSString*)UPDATED_AT_DESC;
@end

// @package External
// @subpackage Kaltura
@interface KalturaScpDropFolderOrderBy : NSObject
+ (NSString*)CREATED_AT_ASC;
+ (NSString*)ID_ASC;
+ (NSString*)NAME_ASC;
+ (NSString*)UPDATED_AT_ASC;
+ (NSString*)CREATED_AT_DESC;
+ (NSString*)ID_DESC;
+ (NSString*)NAME_DESC;
+ (NSString*)UPDATED_AT_DESC;
@end

// @package External
// @subpackage Kaltura
@interface KalturaSftpDropFolderOrderBy : NSObject
+ (NSString*)CREATED_AT_ASC;
+ (NSString*)ID_ASC;
+ (NSString*)NAME_ASC;
+ (NSString*)UPDATED_AT_ASC;
+ (NSString*)CREATED_AT_DESC;
+ (NSString*)ID_DESC;
+ (NSString*)NAME_DESC;
+ (NSString*)UPDATED_AT_DESC;
@end

// @package External
// @subpackage Kaltura
@interface KalturaSshDropFolderOrderBy : NSObject
+ (NSString*)CREATED_AT_ASC;
+ (NSString*)ID_ASC;
+ (NSString*)NAME_ASC;
+ (NSString*)UPDATED_AT_ASC;
+ (NSString*)CREATED_AT_DESC;
+ (NSString*)ID_DESC;
+ (NSString*)NAME_DESC;
+ (NSString*)UPDATED_AT_DESC;
@end

///////////////////////// classes /////////////////////////
// @package External
// @subpackage Kaltura
@interface KalturaDropFolderFileHandlerConfig : KalturaObjectBase
@property (nonatomic,copy,readonly) NSString* handlerType;	// enum KalturaDropFolderFileHandlerType
- (KalturaFieldType)getTypeOfHandlerType;
@end

// @package External
// @subpackage Kaltura
@interface KalturaDropFolder : KalturaObjectBase
@property (nonatomic,assign,readonly) int id;
@property (nonatomic,assign) int partnerId;	// insertonly
@property (nonatomic,copy) NSString* name;
@property (nonatomic,copy) NSString* description;
@property (nonatomic,copy) NSString* type;	// enum KalturaDropFolderType
@property (nonatomic,assign) int status;	// enum KalturaDropFolderStatus
@property (nonatomic,assign) int conversionProfileId;
@property (nonatomic,assign) int dc;
@property (nonatomic,copy) NSString* path;
// The ammount of time, in seconds, that should pass so that a file with no change in size we'll be treated as "finished uploading to folder"
@property (nonatomic,assign) int fileSizeCheckInterval;
@property (nonatomic,assign) int fileDeletePolicy;	// enum KalturaDropFolderFileDeletePolicy
@property (nonatomic,assign) int autoFileDeleteDays;
@property (nonatomic,copy) NSString* fileHandlerType;	// enum KalturaDropFolderFileHandlerType
@property (nonatomic,copy) NSString* fileNamePatterns;
@property (nonatomic,retain) KalturaDropFolderFileHandlerConfig* fileHandlerConfig;
@property (nonatomic,copy) NSString* tags;
@property (nonatomic,copy) NSString* errorCode;	// enum KalturaDropFolderErrorCode
@property (nonatomic,copy) NSString* errorDescription;
@property (nonatomic,copy) NSString* ignoreFileNamePatterns;
@property (nonatomic,assign,readonly) int createdAt;
@property (nonatomic,assign,readonly) int updatedAt;
@property (nonatomic,assign) int lastAccessedAt;
- (KalturaFieldType)getTypeOfId;
- (KalturaFieldType)getTypeOfPartnerId;
- (KalturaFieldType)getTypeOfName;
- (KalturaFieldType)getTypeOfDescription;
- (KalturaFieldType)getTypeOfType;
- (KalturaFieldType)getTypeOfStatus;
- (KalturaFieldType)getTypeOfConversionProfileId;
- (KalturaFieldType)getTypeOfDc;
- (KalturaFieldType)getTypeOfPath;
- (KalturaFieldType)getTypeOfFileSizeCheckInterval;
- (KalturaFieldType)getTypeOfFileDeletePolicy;
- (KalturaFieldType)getTypeOfAutoFileDeleteDays;
- (KalturaFieldType)getTypeOfFileHandlerType;
- (KalturaFieldType)getTypeOfFileNamePatterns;
- (KalturaFieldType)getTypeOfFileHandlerConfig;
- (NSString*)getObjectTypeOfFileHandlerConfig;
- (KalturaFieldType)getTypeOfTags;
- (KalturaFieldType)getTypeOfErrorCode;
- (KalturaFieldType)getTypeOfErrorDescription;
- (KalturaFieldType)getTypeOfIgnoreFileNamePatterns;
- (KalturaFieldType)getTypeOfCreatedAt;
- (KalturaFieldType)getTypeOfUpdatedAt;
- (KalturaFieldType)getTypeOfLastAccessedAt;
- (void)setIdFromString:(NSString*)aPropVal;
- (void)setPartnerIdFromString:(NSString*)aPropVal;
- (void)setStatusFromString:(NSString*)aPropVal;
- (void)setConversionProfileIdFromString:(NSString*)aPropVal;
- (void)setDcFromString:(NSString*)aPropVal;
- (void)setFileSizeCheckIntervalFromString:(NSString*)aPropVal;
- (void)setFileDeletePolicyFromString:(NSString*)aPropVal;
- (void)setAutoFileDeleteDaysFromString:(NSString*)aPropVal;
- (void)setCreatedAtFromString:(NSString*)aPropVal;
- (void)setUpdatedAtFromString:(NSString*)aPropVal;
- (void)setLastAccessedAtFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
@interface KalturaDropFolderFile : KalturaObjectBase
@property (nonatomic,assign,readonly) int id;
@property (nonatomic,assign,readonly) int partnerId;
@property (nonatomic,assign) int dropFolderId;	// insertonly
@property (nonatomic,copy) NSString* fileName;	// insertonly
@property (nonatomic,assign) double fileSize;
@property (nonatomic,assign,readonly) int fileSizeLastSetAt;
@property (nonatomic,assign,readonly) int status;	// enum KalturaDropFolderFileStatus
@property (nonatomic,copy) NSString* parsedSlug;
@property (nonatomic,copy) NSString* parsedFlavor;
@property (nonatomic,assign) int leadDropFolderFileId;
@property (nonatomic,assign) int deletedDropFolderFileId;
@property (nonatomic,copy) NSString* entryId;
@property (nonatomic,copy) NSString* errorCode;	// enum KalturaDropFolderFileErrorCode
@property (nonatomic,copy) NSString* errorDescription;
@property (nonatomic,copy) NSString* lastModificationTime;
@property (nonatomic,assign,readonly) int createdAt;
@property (nonatomic,assign,readonly) int updatedAt;
@property (nonatomic,assign) int uploadStartDetectedAt;
@property (nonatomic,assign) int uploadEndDetectedAt;
@property (nonatomic,assign) int importStartedAt;
@property (nonatomic,assign) int importEndedAt;
@property (nonatomic,assign,readonly) int batchJobId;
- (KalturaFieldType)getTypeOfId;
- (KalturaFieldType)getTypeOfPartnerId;
- (KalturaFieldType)getTypeOfDropFolderId;
- (KalturaFieldType)getTypeOfFileName;
- (KalturaFieldType)getTypeOfFileSize;
- (KalturaFieldType)getTypeOfFileSizeLastSetAt;
- (KalturaFieldType)getTypeOfStatus;
- (KalturaFieldType)getTypeOfParsedSlug;
- (KalturaFieldType)getTypeOfParsedFlavor;
- (KalturaFieldType)getTypeOfLeadDropFolderFileId;
- (KalturaFieldType)getTypeOfDeletedDropFolderFileId;
- (KalturaFieldType)getTypeOfEntryId;
- (KalturaFieldType)getTypeOfErrorCode;
- (KalturaFieldType)getTypeOfErrorDescription;
- (KalturaFieldType)getTypeOfLastModificationTime;
- (KalturaFieldType)getTypeOfCreatedAt;
- (KalturaFieldType)getTypeOfUpdatedAt;
- (KalturaFieldType)getTypeOfUploadStartDetectedAt;
- (KalturaFieldType)getTypeOfUploadEndDetectedAt;
- (KalturaFieldType)getTypeOfImportStartedAt;
- (KalturaFieldType)getTypeOfImportEndedAt;
- (KalturaFieldType)getTypeOfBatchJobId;
- (void)setIdFromString:(NSString*)aPropVal;
- (void)setPartnerIdFromString:(NSString*)aPropVal;
- (void)setDropFolderIdFromString:(NSString*)aPropVal;
- (void)setFileSizeFromString:(NSString*)aPropVal;
- (void)setFileSizeLastSetAtFromString:(NSString*)aPropVal;
- (void)setStatusFromString:(NSString*)aPropVal;
- (void)setLeadDropFolderFileIdFromString:(NSString*)aPropVal;
- (void)setDeletedDropFolderFileIdFromString:(NSString*)aPropVal;
- (void)setCreatedAtFromString:(NSString*)aPropVal;
- (void)setUpdatedAtFromString:(NSString*)aPropVal;
- (void)setUploadStartDetectedAtFromString:(NSString*)aPropVal;
- (void)setUploadEndDetectedAtFromString:(NSString*)aPropVal;
- (void)setImportStartedAtFromString:(NSString*)aPropVal;
- (void)setImportEndedAtFromString:(NSString*)aPropVal;
- (void)setBatchJobIdFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
@interface KalturaDropFolderFileListResponse : KalturaObjectBase
@property (nonatomic,retain,readonly) NSMutableArray* objects;	// of KalturaDropFolderFile elements
@property (nonatomic,assign,readonly) int totalCount;
- (KalturaFieldType)getTypeOfObjects;
- (NSString*)getObjectTypeOfObjects;
- (KalturaFieldType)getTypeOfTotalCount;
- (void)setTotalCountFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
@interface KalturaDropFolderListResponse : KalturaObjectBase
@property (nonatomic,retain,readonly) NSMutableArray* objects;	// of KalturaDropFolder elements
@property (nonatomic,assign,readonly) int totalCount;
- (KalturaFieldType)getTypeOfObjects;
- (NSString*)getObjectTypeOfObjects;
- (KalturaFieldType)getTypeOfTotalCount;
- (void)setTotalCountFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
@interface KalturaDropFolderBaseFilter : KalturaFilter
@property (nonatomic,assign) int idEqual;
@property (nonatomic,copy) NSString* idIn;
@property (nonatomic,assign) int partnerIdEqual;
@property (nonatomic,copy) NSString* partnerIdIn;
@property (nonatomic,copy) NSString* nameLike;
@property (nonatomic,copy) NSString* typeEqual;	// enum KalturaDropFolderType
@property (nonatomic,copy) NSString* typeIn;
@property (nonatomic,assign) int statusEqual;	// enum KalturaDropFolderStatus
@property (nonatomic,copy) NSString* statusIn;
@property (nonatomic,assign) int conversionProfileIdEqual;
@property (nonatomic,copy) NSString* conversionProfileIdIn;
@property (nonatomic,assign) int dcEqual;
@property (nonatomic,copy) NSString* dcIn;
@property (nonatomic,copy) NSString* pathEqual;
@property (nonatomic,copy) NSString* pathLike;
@property (nonatomic,copy) NSString* fileHandlerTypeEqual;	// enum KalturaDropFolderFileHandlerType
@property (nonatomic,copy) NSString* fileHandlerTypeIn;
@property (nonatomic,copy) NSString* fileNamePatternsLike;
@property (nonatomic,copy) NSString* fileNamePatternsMultiLikeOr;
@property (nonatomic,copy) NSString* fileNamePatternsMultiLikeAnd;
@property (nonatomic,copy) NSString* tagsLike;
@property (nonatomic,copy) NSString* tagsMultiLikeOr;
@property (nonatomic,copy) NSString* tagsMultiLikeAnd;
@property (nonatomic,copy) NSString* errorCodeEqual;	// enum KalturaDropFolderErrorCode
@property (nonatomic,copy) NSString* errorCodeIn;
@property (nonatomic,assign) int createdAtGreaterThanOrEqual;
@property (nonatomic,assign) int createdAtLessThanOrEqual;
@property (nonatomic,assign) int updatedAtGreaterThanOrEqual;
@property (nonatomic,assign) int updatedAtLessThanOrEqual;
- (KalturaFieldType)getTypeOfIdEqual;
- (KalturaFieldType)getTypeOfIdIn;
- (KalturaFieldType)getTypeOfPartnerIdEqual;
- (KalturaFieldType)getTypeOfPartnerIdIn;
- (KalturaFieldType)getTypeOfNameLike;
- (KalturaFieldType)getTypeOfTypeEqual;
- (KalturaFieldType)getTypeOfTypeIn;
- (KalturaFieldType)getTypeOfStatusEqual;
- (KalturaFieldType)getTypeOfStatusIn;
- (KalturaFieldType)getTypeOfConversionProfileIdEqual;
- (KalturaFieldType)getTypeOfConversionProfileIdIn;
- (KalturaFieldType)getTypeOfDcEqual;
- (KalturaFieldType)getTypeOfDcIn;
- (KalturaFieldType)getTypeOfPathEqual;
- (KalturaFieldType)getTypeOfPathLike;
- (KalturaFieldType)getTypeOfFileHandlerTypeEqual;
- (KalturaFieldType)getTypeOfFileHandlerTypeIn;
- (KalturaFieldType)getTypeOfFileNamePatternsLike;
- (KalturaFieldType)getTypeOfFileNamePatternsMultiLikeOr;
- (KalturaFieldType)getTypeOfFileNamePatternsMultiLikeAnd;
- (KalturaFieldType)getTypeOfTagsLike;
- (KalturaFieldType)getTypeOfTagsMultiLikeOr;
- (KalturaFieldType)getTypeOfTagsMultiLikeAnd;
- (KalturaFieldType)getTypeOfErrorCodeEqual;
- (KalturaFieldType)getTypeOfErrorCodeIn;
- (KalturaFieldType)getTypeOfCreatedAtGreaterThanOrEqual;
- (KalturaFieldType)getTypeOfCreatedAtLessThanOrEqual;
- (KalturaFieldType)getTypeOfUpdatedAtGreaterThanOrEqual;
- (KalturaFieldType)getTypeOfUpdatedAtLessThanOrEqual;
- (void)setIdEqualFromString:(NSString*)aPropVal;
- (void)setPartnerIdEqualFromString:(NSString*)aPropVal;
- (void)setStatusEqualFromString:(NSString*)aPropVal;
- (void)setConversionProfileIdEqualFromString:(NSString*)aPropVal;
- (void)setDcEqualFromString:(NSString*)aPropVal;
- (void)setCreatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal;
- (void)setCreatedAtLessThanOrEqualFromString:(NSString*)aPropVal;
- (void)setUpdatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal;
- (void)setUpdatedAtLessThanOrEqualFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
@interface KalturaDropFolderContentFileHandlerConfig : KalturaDropFolderFileHandlerConfig
@property (nonatomic,assign) int contentMatchPolicy;	// enum KalturaDropFolderContentFileHandlerMatchPolicy
// Regular expression that defines valid file names to be handled.
// 	 The following might be extracted from the file name and used if defined:
// 	 - (?P<referenceId>\w+) - will be used as the drop folder file's parsed slug.
// 	 - (?P<flavorName>\w+)  - will be used as the drop folder file's parsed flavor.
@property (nonatomic,copy) NSString* slugRegex;
- (KalturaFieldType)getTypeOfContentMatchPolicy;
- (KalturaFieldType)getTypeOfSlugRegex;
- (void)setContentMatchPolicyFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
@interface KalturaDropFolderContentProcessorJobData : KalturaJobData
@property (nonatomic,copy) NSString* dropFolderFileIds;
@property (nonatomic,copy) NSString* parsedSlug;
@property (nonatomic,assign) int contentMatchPolicy;	// enum KalturaDropFolderContentFileHandlerMatchPolicy
@property (nonatomic,assign) int conversionProfileId;
- (KalturaFieldType)getTypeOfDropFolderFileIds;
- (KalturaFieldType)getTypeOfParsedSlug;
- (KalturaFieldType)getTypeOfContentMatchPolicy;
- (KalturaFieldType)getTypeOfConversionProfileId;
- (void)setContentMatchPolicyFromString:(NSString*)aPropVal;
- (void)setConversionProfileIdFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
@interface KalturaDropFolderFileBaseFilter : KalturaFilter
@property (nonatomic,assign) int idEqual;
@property (nonatomic,copy) NSString* idIn;
@property (nonatomic,assign) int partnerIdEqual;
@property (nonatomic,copy) NSString* partnerIdIn;
@property (nonatomic,assign) int dropFolderIdEqual;
@property (nonatomic,copy) NSString* dropFolderIdIn;
@property (nonatomic,copy) NSString* fileNameEqual;
@property (nonatomic,copy) NSString* fileNameIn;
@property (nonatomic,copy) NSString* fileNameLike;
@property (nonatomic,assign) int statusEqual;	// enum KalturaDropFolderFileStatus
@property (nonatomic,copy) NSString* statusIn;
@property (nonatomic,copy) NSString* parsedSlugEqual;
@property (nonatomic,copy) NSString* parsedSlugIn;
@property (nonatomic,copy) NSString* parsedSlugLike;
@property (nonatomic,copy) NSString* parsedFlavorEqual;
@property (nonatomic,copy) NSString* parsedFlavorIn;
@property (nonatomic,copy) NSString* parsedFlavorLike;
@property (nonatomic,assign) int leadDropFolderFileIdEqual;
@property (nonatomic,assign) int deletedDropFolderFileIdEqual;
@property (nonatomic,copy) NSString* entryIdEqual;
@property (nonatomic,copy) NSString* errorCodeEqual;	// enum KalturaDropFolderFileErrorCode
@property (nonatomic,copy) NSString* errorCodeIn;
@property (nonatomic,assign) int createdAtGreaterThanOrEqual;
@property (nonatomic,assign) int createdAtLessThanOrEqual;
@property (nonatomic,assign) int updatedAtGreaterThanOrEqual;
@property (nonatomic,assign) int updatedAtLessThanOrEqual;
- (KalturaFieldType)getTypeOfIdEqual;
- (KalturaFieldType)getTypeOfIdIn;
- (KalturaFieldType)getTypeOfPartnerIdEqual;
- (KalturaFieldType)getTypeOfPartnerIdIn;
- (KalturaFieldType)getTypeOfDropFolderIdEqual;
- (KalturaFieldType)getTypeOfDropFolderIdIn;
- (KalturaFieldType)getTypeOfFileNameEqual;
- (KalturaFieldType)getTypeOfFileNameIn;
- (KalturaFieldType)getTypeOfFileNameLike;
- (KalturaFieldType)getTypeOfStatusEqual;
- (KalturaFieldType)getTypeOfStatusIn;
- (KalturaFieldType)getTypeOfParsedSlugEqual;
- (KalturaFieldType)getTypeOfParsedSlugIn;
- (KalturaFieldType)getTypeOfParsedSlugLike;
- (KalturaFieldType)getTypeOfParsedFlavorEqual;
- (KalturaFieldType)getTypeOfParsedFlavorIn;
- (KalturaFieldType)getTypeOfParsedFlavorLike;
- (KalturaFieldType)getTypeOfLeadDropFolderFileIdEqual;
- (KalturaFieldType)getTypeOfDeletedDropFolderFileIdEqual;
- (KalturaFieldType)getTypeOfEntryIdEqual;
- (KalturaFieldType)getTypeOfErrorCodeEqual;
- (KalturaFieldType)getTypeOfErrorCodeIn;
- (KalturaFieldType)getTypeOfCreatedAtGreaterThanOrEqual;
- (KalturaFieldType)getTypeOfCreatedAtLessThanOrEqual;
- (KalturaFieldType)getTypeOfUpdatedAtGreaterThanOrEqual;
- (KalturaFieldType)getTypeOfUpdatedAtLessThanOrEqual;
- (void)setIdEqualFromString:(NSString*)aPropVal;
- (void)setPartnerIdEqualFromString:(NSString*)aPropVal;
- (void)setDropFolderIdEqualFromString:(NSString*)aPropVal;
- (void)setStatusEqualFromString:(NSString*)aPropVal;
- (void)setLeadDropFolderFileIdEqualFromString:(NSString*)aPropVal;
- (void)setDeletedDropFolderFileIdEqualFromString:(NSString*)aPropVal;
- (void)setCreatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal;
- (void)setCreatedAtLessThanOrEqualFromString:(NSString*)aPropVal;
- (void)setUpdatedAtGreaterThanOrEqualFromString:(NSString*)aPropVal;
- (void)setUpdatedAtLessThanOrEqualFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
@interface KalturaRemoteDropFolder : KalturaDropFolder
@end

// @package External
// @subpackage Kaltura
@interface KalturaDropFolderFileFilter : KalturaDropFolderFileBaseFilter
@end

// @package External
// @subpackage Kaltura
@interface KalturaDropFolderFilter : KalturaDropFolderBaseFilter
@property (nonatomic,assign) int currentDc;	// enum KalturaNullableBoolean
- (KalturaFieldType)getTypeOfCurrentDc;
- (void)setCurrentDcFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
@interface KalturaFtpDropFolder : KalturaRemoteDropFolder
@property (nonatomic,copy) NSString* host;
@property (nonatomic,assign) int port;
@property (nonatomic,copy) NSString* username;
@property (nonatomic,copy) NSString* password;
- (KalturaFieldType)getTypeOfHost;
- (KalturaFieldType)getTypeOfPort;
- (KalturaFieldType)getTypeOfUsername;
- (KalturaFieldType)getTypeOfPassword;
- (void)setPortFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
@interface KalturaSshDropFolder : KalturaRemoteDropFolder
@property (nonatomic,copy) NSString* host;
@property (nonatomic,assign) int port;
@property (nonatomic,copy) NSString* username;
@property (nonatomic,copy) NSString* password;
@property (nonatomic,copy) NSString* privateKey;
@property (nonatomic,copy) NSString* publicKey;
@property (nonatomic,copy) NSString* passPhrase;
- (KalturaFieldType)getTypeOfHost;
- (KalturaFieldType)getTypeOfPort;
- (KalturaFieldType)getTypeOfUsername;
- (KalturaFieldType)getTypeOfPassword;
- (KalturaFieldType)getTypeOfPrivateKey;
- (KalturaFieldType)getTypeOfPublicKey;
- (KalturaFieldType)getTypeOfPassPhrase;
- (void)setPortFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
// Used to ingest media that dropped through drop folder
@interface KalturaDropFolderFileResource : KalturaDataCenterContentResource
// Id of the drop folder file object
@property (nonatomic,assign) int dropFolderFileId;
- (KalturaFieldType)getTypeOfDropFolderFileId;
- (void)setDropFolderFileIdFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
@interface KalturaDropFolderImportJobData : KalturaSshImportJobData
@property (nonatomic,assign) int dropFolderFileId;
- (KalturaFieldType)getTypeOfDropFolderFileId;
- (void)setDropFolderFileIdFromString:(NSString*)aPropVal;
@end

// @package External
// @subpackage Kaltura
@interface KalturaRemoteDropFolderBaseFilter : KalturaDropFolderFilter
@end

// @package External
// @subpackage Kaltura
@interface KalturaScpDropFolder : KalturaSshDropFolder
@end

// @package External
// @subpackage Kaltura
@interface KalturaSftpDropFolder : KalturaSshDropFolder
@end

// @package External
// @subpackage Kaltura
@interface KalturaRemoteDropFolderFilter : KalturaRemoteDropFolderBaseFilter
@end

// @package External
// @subpackage Kaltura
@interface KalturaFtpDropFolderBaseFilter : KalturaRemoteDropFolderFilter
@end

// @package External
// @subpackage Kaltura
@interface KalturaSshDropFolderBaseFilter : KalturaRemoteDropFolderFilter
@end

// @package External
// @subpackage Kaltura
@interface KalturaFtpDropFolderFilter : KalturaFtpDropFolderBaseFilter
@end

// @package External
// @subpackage Kaltura
@interface KalturaSshDropFolderFilter : KalturaSshDropFolderBaseFilter
@end

// @package External
// @subpackage Kaltura
@interface KalturaScpDropFolderBaseFilter : KalturaSshDropFolderFilter
@end

// @package External
// @subpackage Kaltura
@interface KalturaSftpDropFolderBaseFilter : KalturaSshDropFolderFilter
@end

// @package External
// @subpackage Kaltura
@interface KalturaScpDropFolderFilter : KalturaScpDropFolderBaseFilter
@end

// @package External
// @subpackage Kaltura
@interface KalturaSftpDropFolderFilter : KalturaSftpDropFolderBaseFilter
@end

///////////////////////// services /////////////////////////
// @package External
// @subpackage Kaltura
// DropFolder service lets you create and manage drop folders
@interface KalturaDropFolderService : KalturaServiceBase
// Allows you to add a new KalturaDropFolder object
- (KalturaDropFolder*)addWithDropFolder:(KalturaDropFolder*)aDropFolder;
// Retrieve a KalturaDropFolder object by ID
- (KalturaDropFolder*)getWithDropFolderId:(int)aDropFolderId;
// Update an existing KalturaDropFolder object
- (KalturaDropFolder*)updateWithDropFolderId:(int)aDropFolderId withDropFolder:(KalturaDropFolder*)aDropFolder;
// Mark the KalturaDropFolder object as deleted
- (KalturaDropFolder*)deleteWithDropFolderId:(int)aDropFolderId;
// List KalturaDropFolder objects
- (KalturaDropFolderListResponse*)listWithFilter:(KalturaDropFolderFilter*)aFilter withPager:(KalturaFilterPager*)aPager;
- (KalturaDropFolderListResponse*)listWithFilter:(KalturaDropFolderFilter*)aFilter;
- (KalturaDropFolderListResponse*)list;
@end

// @package External
// @subpackage Kaltura
// DropFolderFile service lets you create and manage drop folder files
@interface KalturaDropFolderFileService : KalturaServiceBase
// Allows you to add a new KalturaDropFolderFile object
- (KalturaDropFolderFile*)addWithDropFolderFile:(KalturaDropFolderFile*)aDropFolderFile;
// Retrieve a KalturaDropFolderFile object by ID
- (KalturaDropFolderFile*)getWithDropFolderFileId:(int)aDropFolderFileId;
// Update an existing KalturaDropFolderFile object
- (KalturaDropFolderFile*)updateWithDropFolderFileId:(int)aDropFolderFileId withDropFolderFile:(KalturaDropFolderFile*)aDropFolderFile;
// Update status of KalturaDropFolderFile
- (KalturaDropFolderFile*)updateStatusWithDropFolderFileId:(int)aDropFolderFileId withStatus:(int)aStatus;
// Mark the KalturaDropFolderFile object as deleted
- (KalturaDropFolderFile*)deleteWithDropFolderFileId:(int)aDropFolderFileId;
// List KalturaDropFolderFile objects
- (KalturaDropFolderFileListResponse*)listWithFilter:(KalturaDropFolderFileFilter*)aFilter withPager:(KalturaFilterPager*)aPager;
- (KalturaDropFolderFileListResponse*)listWithFilter:(KalturaDropFolderFileFilter*)aFilter;
- (KalturaDropFolderFileListResponse*)list;
// Set the KalturaDropFolderFile status to ignore (KalturaDropFolderFileStatus::IGNORE)
- (KalturaDropFolderFile*)ignoreWithDropFolderFileId:(int)aDropFolderFileId;
@end

@interface KalturaDropFolderClientPlugin : KalturaClientPlugin
{
	KalturaDropFolderService* _dropFolder;
	KalturaDropFolderFileService* _dropFolderFile;
}

@property (nonatomic, assign) KalturaClientBase* client;
@property (nonatomic, readonly) KalturaDropFolderService* dropFolder;
@property (nonatomic, readonly) KalturaDropFolderFileService* dropFolderFile;
@end

