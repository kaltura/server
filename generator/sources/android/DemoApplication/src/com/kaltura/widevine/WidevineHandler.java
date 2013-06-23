package com.kaltura.widevine;

import android.app.Activity;
import android.drm.DrmErrorEvent;
import android.drm.DrmEvent;
import android.drm.DrmInfoEvent;
import android.drm.DrmInfoRequest;
import android.drm.DrmManagerClient;
import android.widget.Toast;

import com.kaltura.services.AdminUser;

public class WidevineHandler {
	
	public static String WIDEVINE_MIME_TYPE = "video/wvm";
	public static String DRM_SERVER_URI = "/api_v3/index.php?service=widevine_widevinedrm&action=getLicense&format=widevine&flavorAssetId=";
	
	//widevine wvm URL	
	public String url;
	private Activity context;
	
	public WidevineHandler (Activity activity, int partnerId, String entryId, String flavorId) {
		String host = (AdminUser.cdnHost != null) ? AdminUser.cdnHost : AdminUser.host;
		url = host + "/p/" + partnerId + "/sp/" + partnerId + "00/playManifest/entryId/" + entryId + "/flavorId/" + flavorId + "/format/url/protocol/http/a.wvm?ks=" + AdminUser.ks;
		context = activity;
		
		DrmManagerClient mDrmManager = new DrmManagerClient(context);
		DrmInfoRequest drmInfoRequest = new DrmInfoRequest(DrmInfoRequest.TYPE_RIGHTS_ACQUISITION_INFO, WIDEVINE_MIME_TYPE); 
		drmInfoRequest.put("WVAssetURIKey", url);
		drmInfoRequest.put("WVDRMServerKey", AdminUser.host + DRM_SERVER_URI + flavorId + "&ks=" + AdminUser.ks);
		drmInfoRequest.put("WVDeviceIDKey", "device1234");
		drmInfoRequest.put("WVPortalKey", "kaltura");

		mDrmManager.setOnEventListener(new DrmManagerClient.OnEventListener() {
			public void onEvent(DrmManagerClient client, DrmEvent event) {
		                switch (event.getType()) {
		                case DrmEvent.TYPE_DRM_INFO_PROCESSED:
						  //INFO PROCESSED
						break;
		                }      }      });

		mDrmManager.setOnErrorListener(new DrmManagerClient.OnErrorListener() {
            public void onError(DrmManagerClient client, DrmErrorEvent event) {
                switch (event.getType()) {
				 case DrmErrorEvent.TYPE_RIGHTS_NOT_INSTALLED:
					 Toast.makeText(context , "We're sorry, you don’t have a valid license for this video.", Toast.LENGTH_SHORT).show();
			 	//RIGHTA NOT INSTALLED
				break;
                }      }      });

		mDrmManager.setOnInfoListener(new DrmManagerClient.OnInfoListener() {
			public void onInfo(DrmManagerClient client, DrmInfoEvent event) {
               if (event.getType() == DrmInfoEvent.TYPE_RIGHTS_INSTALLED) {
                   //RIGHTS INSTALLED
               }     }      });
		
		//get license
		mDrmManager.acquireRights(drmInfoRequest);
	}
}
