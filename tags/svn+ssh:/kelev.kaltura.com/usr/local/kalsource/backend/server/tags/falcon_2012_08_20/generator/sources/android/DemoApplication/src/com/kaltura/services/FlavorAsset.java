/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package com.kaltura.services;

import java.util.List;

import android.util.Log;

import com.kaltura.client.KalturaApiException;
import com.kaltura.client.KalturaClient;
import com.kaltura.client.services.KalturaFlavorAssetService;
import com.kaltura.client.types.KalturaFilterPager;
import com.kaltura.client.types.KalturaFlavorAsset;
import com.kaltura.client.types.KalturaFlavorAssetFilter;
import com.kaltura.client.types.KalturaFlavorAssetListResponse;

/**
 * Retrieve information and invoke actions on Flavor Asset
 */
public class FlavorAsset {

    /**
     * List Flavor Assets by filter and pager
     *
     * @param TAG constant in your class
     * @param entryId Entry id
     * @param pageindex The page number for which {pageSize} of objects should
     * be retrieved (Default is 1)
     * @param pageSize The number of objects to retrieve. (Default is 30,
     * maximum page size is 500)
     *
     * @return The list of all categories
     *
     * @throws KalturaApiException
     */
    public static List<KalturaFlavorAsset> listAllFlavorAssets(String TAG, String entryId, int pageIndex, int pageSize) throws KalturaApiException {
        // create a new ADMIN-session client
        KalturaClient client = AdminUser.getClient();//RequestsKaltura.getKalturaClient();

        KalturaFlavorAssetService flavorAssetService = client.getFlavorAssetService();

        // create a new filter to filter entries - not mandatory
        KalturaFlavorAssetFilter filter = new KalturaFlavorAssetFilter();
        filter.entryIdEqual = entryId;
        // create a new pager to choose how many and which entries should be recieved
        // out of the filtered entries - not mandatory
        KalturaFilterPager pager = new KalturaFilterPager();
        pager.pageIndex = pageIndex;
        pager.pageSize = pageSize;

        // execute the list action of the mediaService object to recieve the list of entries
        KalturaFlavorAssetListResponse listResponseFlavorAsset = flavorAssetService.list(filter);

        return listResponseFlavorAsset.objects;
    }

    /**
     * Get download URL for the asset
     *
     * @param TAG constant in your class
     * @param id asset id
     *
     * @return The asset url
     */
    public static String getUrl(String TAG, String id) throws KalturaApiException {
        // create a new ADMIN-session client
        KalturaClient client = AdminUser.getClient();//RequestsKaltura.getKalturaClient();

        // create a new mediaService object for our client
        KalturaFlavorAssetService mediaService = client.getFlavorAssetService();
        String url = mediaService.getUrl(id);
        Log.w(TAG, "URL for the asset: " + url);
        return url;
    }
}
