package com.kaltura.client.tests;

import java.util.ArrayList;

import com.kaltura.client.KalturaApiException;
import com.kaltura.client.enums.KalturaContainerFormat;
import com.kaltura.client.enums.KalturaNullableBoolean;
import com.kaltura.client.enums.KalturaSiteRestrictionType;
import com.kaltura.client.types.KalturaAccessControl;
import com.kaltura.client.types.KalturaBaseRestriction;
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
		params.name = testString;
		params.description = testString;
		params.density = testInt;
		params.isSystemDefault = testEnumAsInt;
		params.format = testEnumAsString;

		// Regular update works
		params = client.getThumbParamsService().add(params);

		assertEquals(testString, params.description);
		assertEquals(testInt, params.density);
		assertEquals(testEnumAsInt, params.isSystemDefault);
		assertEquals(testEnumAsString, params.format);

		// Null value not passed
		KalturaThumbParams params2 = new KalturaThumbParams();
		params2.description = null;
		params2.density = Integer.MIN_VALUE;
		params2.isSystemDefault = null;
		params2.format = null;

		params2 = client.getThumbParamsService().update(params.id, params2);
		assertEquals(testString, params2.description);
		assertEquals(testInt, params2.density);
		assertEquals(testEnumAsInt, params2.isSystemDefault);
		assertEquals(testEnumAsString, params2.format);

		client.getThumbParamsService().delete(params.id);
	}

	
	/**
	 * Tests that when we ask to set parameters to null, we indeed set them to null
	 * The parameter types that are tested : 
	 * String, int.
	 */
	public void testSetFieldsToNull() throws KalturaApiException {

		BaseTest.startAdminSession(client, kalturaConfig);

		final String testString = "Kaltura test string";
		final int testInt = 42;

		KalturaThumbParams params = new KalturaThumbParams();
		params.name = testString;
		params.description = testString;
		params.density = testInt;

		// Regular update works
		params = client.getThumbParamsService().add(params);

		assertEquals(testString, params.description);
		assertEquals(testInt, params.density);

		// Set to null
		KalturaThumbParams params2 = new KalturaThumbParams();
		params2.description = "__null_string__";
		params2.density = Integer.MAX_VALUE;

		params2 = client.getThumbParamsService().update(params.id, params2);
		assertNull(params2.description);
		assertEquals(Integer.MIN_VALUE, params2.density);

		client.getThumbParamsService().delete(params.id);
	}
	
	/**
	 * Tests that array update is working - 
	 * tests empty array, Null array & full array.
	 */
	public void testArrayConversion() throws KalturaApiException {
		
		KalturaSiteRestriction resA = new KalturaSiteRestriction();
		resA.siteRestrictionType = KalturaSiteRestrictionType.RESTRICT_SITE_LIST;
		resA.siteList = "ResA";
		KalturaCountryRestriction resB = new KalturaCountryRestriction();
		resB.countryList = "IllegalCountry";
		
		ArrayList<KalturaBaseRestriction> restrictions = new ArrayList<KalturaBaseRestriction>();
		restrictions.add(resA);
		restrictions.add(resB);
		
		KalturaAccessControl accessControl = new KalturaAccessControl();
		accessControl.name = "test access control";
		accessControl.restrictions = restrictions;
		
		BaseTest.startAdminSession(client, kalturaConfig);
		accessControl = client.getAccessControlService().add(accessControl);
		
		assertNotNull(accessControl.restrictions);
		assertEquals(2, accessControl.restrictions.size());
		
		// Test null update - shouldn't update
		KalturaAccessControl accessControl2 = new KalturaAccessControl();
		accessControl2.restrictions = null; 
		accessControl2 = client.getAccessControlService().update(accessControl.id, accessControl2);
		
		assertEquals(2, accessControl2.restrictions.size());
		
		// Test update Empty array - should update
		KalturaAccessControl accessControl3 = new KalturaAccessControl();
		accessControl3.restrictions = new ArrayList<KalturaBaseRestriction>(); 
		accessControl3 = client.getAccessControlService().update(accessControl.id, accessControl3);
		
		assertEquals(0, accessControl3.restrictions.size());

		// Delete entry
		client.getAccessControlService().delete(accessControl.id);
	}

}
