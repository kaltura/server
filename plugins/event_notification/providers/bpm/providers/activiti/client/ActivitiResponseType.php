<?php

class ActivitiResponseType
{
	/**
	 * The operation was successful and a response has been returned (GET and PUT requests).
	 */
	const OK = 200;
	
	/**
	 * The operation was successful and the entity has been created and is returned in the response-body (POST request).
	 */
	const CREATED = 201;
	
	/**
	 * The operation was successful and entity has been deleted and therefore there is no response-body returned (DELETE request).
	 */
	const NO_CONTENT = 204;
	
	/**
	 * The operation failed.
	 * The operation requires an Authentication header to be set.
	 * If this was present in the request, the supplied credentials are not valid or the user is not authorized to perform this operation.
	 */
	const UNAUTHORIZED = 401;
	
	/**
	 * The operation is forbidden and should not be re-attempted.
	 * This does not imply an issue with authentication not authorization, it's an operation that is not allowed.
	 * Example: deleting a task that is part of a running process is not allowed and will never be allowed, regardless of the user or process/task state.
	 */
	const FORBIDDEN = 403;
	
	/**
	 * The operation failed.
	 * The requested resource was not found.
	 */
	const NOT_FOUND = 404;
	
	/**
	 * The operation failed.
	 * The used method is not allowed for this resource.
	 * Eg. trying to update (PUT) a deployment-resource will result in a 405 status.
	 */
	const METHOD_NOT_ALLOWED = '';
	
	/**
	 * The operation failed.
	 * The operation causes an update of a resource that has been updated by another operation, which makes the update no longer valid.
	 * Can also indicate a resource that is being created in a collection where a resource with that identifier already exists.
	 */
	const CONFLICT = 409;
	
	/**
	 * The operation failed.
	 * The request body contains an unsupported media type.
	 * Also occurs when the request-body JSON contains an unknown attribute or value that doesn't have the right format/type to be accepted.
	 */
	const UNSUPPORTED_MEDIA_TYPE = 415;
	
	/**
	 * The operation failed.
	 * An unexpected exception occurred while executing the operation.
	 * The response-body contains details about the error.
	 */
	const INTERNAL_SERVER_ERROR = 500;
	
	private static $descriptions = array(
		self::NO_CONTENT => 'The operation was successful and entity has been deleted and therefore there is no response-body returned',
		self::UNAUTHORIZED => 'The operation requires an Authentication header to be set. If this was present in the request, the supplied credentials are not valid or the user is not authorized to perform this operation',
		self::FORBIDDEN => 'The operation is forbidden and should not be re-attempted. This does not imply an issue with authentication not authorization, it\'s an operation that is not allowed. Example: deleting a task that is part of a running process is not allowed and will never be allowed, regardless of the user or process/task state',
		self::NOT_FOUND => 'The requested resource was not found',
		self::METHOD_NOT_ALLOWED => 'The used method is not allowed for this resource. Eg. trying to update (PUT) a deployment-resource will result in a 405 status',
		self::CONFLICT => 'The operation causes an update of a resource that has been updated by another operation, which makes the update no longer valid. Can also indicate a resource that is being created in a collection where a resource with that identifier already exists',
		self::UNSUPPORTED_MEDIA_TYPE => 'The request body contains an unsupported media type. Also occurs when the request-body JSON contains an unknown attribute or value that doesn\'t have the right format/type to be accepted',
		self::INTERNAL_SERVER_ERROR => 'An unexpected exception occurred while executing the operation. The response-body contains details about the error',
	);
	
	public static function isExpectedCode($status)
	{
		return isset(self::$descriptions[$status]);
	}
	
	public static function getCodeDescription($status)
	{
		if(isset(self::$descriptions[$status]))
			return self::$descriptions[$status];
			
		return null;
	}
}
