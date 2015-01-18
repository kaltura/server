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
package com.kaltura.client.test;

import java.util.ArrayList;

import com.kaltura.client.KalturaApiException;
import com.kaltura.client.enums.KalturaContainerFormat;
import com.kaltura.client.enums.KalturaNullableBoolean;
import com.kaltura.client.enums.KalturaSiteRestrictionType;
import com.kaltura.client.types.KalturaAccessControl;
import com.kaltura.client.types.KalturaBaseRestriction;
import com.kaltura.client.types.KalturaConversionProfile;
import com.kaltura.client.types.KalturaCountryRestriction;
import com.kaltura.client.types.KalturaSiteRestriction;
import com.kaltura.client.types.KalturaThumbParams;

public class MediaServiceFieldsTest extends BaseTest {

	/**
	 * Tests that when we set values to their matching "NULL" their value isn't passed to the server.
	 * The parameter types that are tested : 
	 * String, int, EnumAsInt, EnumAsString.
	 */
	public void testSetFieldValueShouldNotPass() throws KalturaApiException {

		BaseTest.startAdminSession(client, kalturaConfig);

		final String testString = "Kaltura test string";
		final int testInt = 42;
		final KalturaNullableBoolean testEnumAsInt = KalturaNullableBoolean.FALSE_VALUE;
		final KalturaContainerFormat testEnumAsString = KalturaContainerFormat.ISMV;

		KalturaThumbParams params = new KalturaThumbParams();
		params.setName(testString);
		params.setDescription(testString);
		params.setDensity(testInt);
		params.setIsSystemDefault(testEnumAsInt);
		params.setFormat(testEnumAsString);

		// Regular update works
		params = client.getThumbParamsService().add(params);

		assertEquals(testString, params.getDescription());
		assertEquals(testInt, params.getDensity());
		assertEquals(testEnumAsInt, params.getIsSystemDefault());
		assertEquals(testEnumAsString, params.getFormat());

		// Null value not passed
		KalturaThumbParams params2 = new KalturaThumbParams();
		params2.setDescription(null);
		params2.setDensity(Integer.MIN_VALUE);
		params2.setIsSystemDefault(null);
		params2.setFormat(null);

		params2 = client.getThumbParamsService().update(params.getId(), params2);
		assertEquals(testString, params2.getDescription());
		assertEquals(testInt, params2.getDensity());
		assertEquals(testEnumAsInt, params2.getIsSystemDefault());
		assertEquals(testEnumAsString, params2.getFormat());

		client.getThumbParamsService().delete(params.getId());
	}

	
	/**
	 * Tests that when we ask to set parameters to null, we indeed set them to null
	 * The parameter types that are tested : String
	 */
	public void testSetFieldsToNullString() throws KalturaApiException {

		BaseTest.startAdminSession(client, kalturaConfig);

		final String testString = "Kaltura test string";

		KalturaThumbParams params = new KalturaThumbParams();
		params.setName(testString);
		params.setDescription(testString);

		// Regular update works
		params = client.getThumbParamsService().add(params);

		assertEquals(testString, params.getDescription());

		// Set to null
		KalturaThumbParams params2 = new KalturaThumbParams();
		params2.setDescription("__null_string__");

		params2 = client.getThumbParamsService().update(params.getId(), params2);
		assertNull(params2.getDescription());

		client.getThumbParamsService().delete(params.getId());
		
	}
	
	/**
	 * Tests that when we ask to set parameters to null, we indeed set them to null
	 * The parameter types that are tested : int
	 */
	public void testSetFieldsToNullInt() throws KalturaApiException {

		BaseTest.startAdminSession(client, kalturaConfig);
		final int testInt = 42;

		KalturaConversionProfile profile = new KalturaConversionProfile();
		profile.setName("Kaltura test string");
		profile.setFlavorParamsIds("0");
		profile.setStorageProfileId(testInt);

		// Regular update works
		profile = client.getConversionProfileService().add(profile);

		assertEquals(testInt, profile.getStorageProfileId());

		// Set to null
		KalturaConversionProfile profile2 = new KalturaConversionProfile();
		profile2.setStorageProfileId(Integer.MAX_VALUE);

		profile2 = client.getConversionProfileService().update(profile.getId(), profile2);
		assertEquals(Integer.MIN_VALUE, profile2.getStorageProfileId());

		client.getConversionProfileService().delete(profile.getId());
		
		
	}
	
	/**
	 * Tests that array update is working - 
	 * tests empty array, Null array & full array.
	 */
	public void testArrayConversion() throws KalturaApiException {
		
		KalturaSiteRestriction resA = new KalturaSiteRestriction();
		resA.setSiteRestrictionType(KalturaSiteRestrictionType.RESTRICT_SITE_LIST);
		resA.setSiteList("ResA");
		KalturaCountryRestriction resB = new KalturaCountryRestriction();
		resB.setCountryList("IllegalCountry");
		
		ArrayList<KalturaBaseRestriction> restrictions = new ArrayList<KalturaBaseRestriction>();
		restrictions.add(resA);
		restrictions.add(resB);
		
		KalturaAccessControl accessControl = new KalturaAccessControl();
		accessControl.setName("test access control");
		accessControl.setRestrictions(restrictions);
		
		BaseTest.startAdminSession(client, kalturaConfig);
		accessControl = client.getAccessControlService().add(accessControl);
		
		assertNotNull(accessControl.getRestrictions());
		assertEquals(2, accessControl.getRestrictions().size());
		
		// Test null update - shouldn't update
		KalturaAccessControl accessControl2 = new KalturaAccessControl();
		accessControl2.setRestrictions(null); 
		accessControl2 = client.getAccessControlService().update(accessControl.getId(), accessControl2);
		
		assertEquals(2, accessControl2.getRestrictions().size());
		
		// Test update Empty array - should update
		KalturaAccessControl accessControl3 = new KalturaAccessControl();
		accessControl3.setRestrictions(new ArrayList<KalturaBaseRestriction>()); 
		accessControl3 = client.getAccessControlService().update(accessControl.getId(), accessControl3);
		
		assertEquals(0, accessControl3.getRestrictions().size());

		// Delete entry
		client.getAccessControlService().delete(accessControl.getId());
	}

}
