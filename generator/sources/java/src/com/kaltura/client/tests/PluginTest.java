package com.kaltura.client.tests;

import com.kaltura.client.KalturaApiException;
import com.kaltura.client.types.KalturaMetadataProfile;



public class PluginTest extends BaseTest {

	public void testPlugin() throws KalturaApiException {
		final String testString = "TEST PROFILE";
		BaseTest.startAdminSession(client, kalturaConfig);

		KalturaMetadataProfile profile = new KalturaMetadataProfile();
		profile = client.getMetadataProfileService().add(profile, "");
		assertNotNull(profile.id);
		
		KalturaMetadataProfile updateProfile = new KalturaMetadataProfile();
		updateProfile.name = testString;
		updateProfile = client.getMetadataProfileService().update(profile.id, updateProfile);
		assertEquals(testString, updateProfile.name);
		
		client.getMetadataProfileService().delete(profile.id);
	}

}
