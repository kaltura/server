<?php


class MicrosoftGraphFieldNames
{
	//Common field names
	const ID_FIELD = 'id';
	const USER = 'user';

	// DriveItem Field Names
	const FOLDER_FACET = 'folder';
	const DELETED_FACET = 'deleted';
	const PARENT_REFERENCE = 'parentReference';
	const DRIVE_ID = 'driveId';
	const NAME = 'name';
	const SIZE = 'size';
	const DESCRIPTION = 'description';
	const DOWNLOAD_URL = '@microsoft.graph.downloadUrl';
	const CREATED_BY = 'createdBy';
	const EMAIL = 'email';
	const SOURCE = 'source';
	const EXTERNAL_ID = 'externalId';
	const APPLICATION = 'application';
	const MEDIA = 'media';
	const MEDIA_SOURCE = 'mediaSource';
	const CONTENT_CATEGORY = 'contentCategory';

	//CallRecord field names
	const PARTICIPANTS = 'participants';

	//User field names
	const MAIL = 'mail';

	//Bearer token field names
	const ACCESS_TOKEN = 'access_token';
	const EXPIRES_ON = 'expires_on';

	//List response field names
	const VALUE = 'value';
	const TOKEN_QUERY_PARAM = 'token';
}