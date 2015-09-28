package com.kaltura.client;

import javax.json.JsonValue;

public interface IKalturaParam {

	Object toQueryString(String key) throws KalturaApiException;

	JsonValue toJsonObject();

}
