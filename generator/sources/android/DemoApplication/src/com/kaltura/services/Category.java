package com.kaltura.services;

import java.util.List;

import android.util.Log;

import com.kaltura.client.KalturaApiException;
import com.kaltura.client.KalturaClient;
import com.kaltura.client.services.KalturaCategoryService;
import com.kaltura.client.types.KalturaCategory;
import com.kaltura.client.types.KalturaCategoryFilter;
import com.kaltura.client.types.KalturaCategoryListResponse;
import com.kaltura.client.types.KalturaFilterPager;

/**
 * Add & Manage Categories *
 */
public class Category {

    /**
     * Get a list of all categories on the kaltura server
     *
     * @param TAG constant in your class
     * @param pageindex The page number for which {pageSize} of objects should
     * be retrieved (Default is 1)
     * @param pageSize The number of objects to retrieve. (Default is 30,
     * maximum page size is 500)
     *
     * @return The list of all categories
     *
     * @throws KalturaApiException
     */
    public static List<KalturaCategory> listAllCategories(String TAG, int pageIndex, int pageSize) throws KalturaApiException {
        // create a new ADMIN-session client
        KalturaClient client = AdminUser.getClient();//RequestsKaltura.getKalturaClient();

        // create a new mediaService object for our client
        KalturaCategoryService categoryService = client.getCategoryService();

        // create a new filter to filter entries - not mandatory
        KalturaCategoryFilter filter = new KalturaCategoryFilter();
        //filter.mediaTypeEqual = mediaType;

        // create a new pager to choose how many and which entries should be recieved
        // out of the filtered entries - not mandatory
        KalturaFilterPager pager = new KalturaFilterPager();
        pager.pageIndex = pageIndex;
        pager.pageSize = pageSize;

        // execute the list action of the mediaService object to recieve the list of entries
        KalturaCategoryListResponse listResponse = categoryService.list(filter);

        // loop through all entries in the reponse list and print their id.
        Log.w(TAG, "Entries list :");
        int i = 0;
        for (KalturaCategory entry : listResponse.objects) {
            Log.w(TAG, ++i + " id:" + entry.id + " name:" + entry.name + " depth: " + entry.depth + " fullName: " + entry.fullName);
        }
        return listResponse.objects;
    }
}
