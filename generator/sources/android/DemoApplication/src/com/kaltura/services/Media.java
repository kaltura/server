package com.kaltura.services;

import java.io.File;
import java.util.List;

import android.util.Log;

import com.kaltura.client.KalturaApiException;
import com.kaltura.client.KalturaClient;
import com.kaltura.client.enums.KalturaEntryType;
import com.kaltura.client.enums.KalturaMediaType;
import com.kaltura.client.services.KalturaMediaService;
import com.kaltura.client.types.KalturaBaseEntry;
import com.kaltura.client.types.KalturaFilterPager;
import com.kaltura.client.types.KalturaMediaEntry;
import com.kaltura.client.types.KalturaMediaEntryFilter;
import com.kaltura.client.types.KalturaMediaListResponse;

/**
 * Media service lets you upload and manage media files (images / videos &
 * audio)
 */
public class Media {

    /**
     * Get a list of all media data from the kaltura server
     *
     * @param TAG constant in your class
     * @param mediaType Type of entries
     * @param pageSize The number of objects to retrieve. (Default is 30,
     * maximum page size is 500)
     *
     * @throws KalturaApiException
     */
    public static List<KalturaMediaEntry> listAllEntriesByIdCategories(String TAG, KalturaMediaEntryFilter filter, int pageIndex, int pageSize) throws KalturaApiException {
        // create a new ADMIN-session client
        KalturaClient client = AdminUser.getClient();//RequestsKaltura.getKalturaClient();

        // create a new mediaService object for our client
        KalturaMediaService mediaService = client.getMediaService();

        // create a new pager to choose how many and which entries should be recieved
        // out of the filtered entries - not mandatory
        KalturaFilterPager pager = new KalturaFilterPager();
        pager.pageIndex = pageIndex;
        pager.pageSize = pageSize;

        // execute the list action of the mediaService object to recieve the list of entries
        KalturaMediaListResponse listResponse = mediaService.list(filter, pager);

        // loop through all entries in the reponse list and print their id.
        Log.w(TAG, "Entries list :");
        int i = 0;
        for (KalturaMediaEntry entry : listResponse.objects) {
            Log.w(TAG, ++i + " id:" + entry.id + " name:" + entry.name + " type:" + entry.type + " dataURL: " + entry.dataUrl);
        }
        return listResponse.objects;
    }

    /**
     * Get media entry by ID
     *
     * @param TAG constant in your class
     * @param entryId Media entry id
     *
     * @return Information about the entry
     *
     * @throws KalturaApiException
     */
    public static KalturaMediaEntry getEntrybyId(String TAG, String entryId) throws KalturaApiException {
        // create a new ADMIN-session client
        KalturaClient client = AdminUser.getClient();//RequestsKaltura.getKalturaClient();

        // create a new mediaService object for our client
        KalturaMediaService mediaService = client.getMediaService();
        KalturaMediaEntry entry = mediaService.get(entryId);
        Log.w(TAG, "Entry:");
        Log.w(TAG, " id:" + entry.id + " name:" + entry.name + " type:" + entry.type + " categories: " + entry.categories);
        return entry;
    }

    /**
     * Creates an empty media entry and assigns basic metadata to it.
     *
     * @param TAG constant in your class
     * @param category Category name which belongs to an entry
     * @param name Name of an entry
     * @param description Description of an entry
     * @param tag Tag of an entry
     *
     * @return Information about created the entry
     *
     *
     */
    public static KalturaMediaEntry addEmptyEntry(String TAG, String category, String name, String description, String tag) {

        try {
            KalturaClient client = AdminUser.getClient();

            Log.w(TAG, "\nCreating an empty Kaltura Entry (without actual media binary attached)...");

            KalturaMediaEntry entry = new KalturaMediaEntry();
            entry.mediaType = KalturaMediaType.VIDEO;
            entry.categories = category;
            entry.name = name;
            entry.description = description;
            entry.tags = tag;

            KalturaMediaEntry newEntry = client.getMediaService().add(entry);
            Log.w(TAG, "\nThe id of our new Video Entry is: " + newEntry.id);
            return newEntry;
        } catch (KalturaApiException e) {
            e.printStackTrace();
            Log.w(TAG, "err: " + e.getMessage());
            return null;
        }
    }

    /**
     * Create an entry
     *
     * @param TAG constant in your class
     * @param String fileName File to upload.
     * @param String entryName Name for the new entry.
     *
     * @throws KalturaApiException
     */
    public static void addEntry(String TAG, String fileName, String entryName) throws KalturaApiException {
        // create a new USER-session client
        KalturaClient client = AdminUser.getClient();

        // upload the new file and recieve the token that identifies it on the kaltura server
        File up = new File(fileName);
        String token = client.getBaseEntryService().upload(up);

        // create a new entry object with the required meta-data
        KalturaBaseEntry entry = new KalturaBaseEntry();
        entry.name = entryName;
        entry.categories = "Comedy";
        entry.type = KalturaEntryType.MEDIA_CLIP;

        // add the entry you created to the kaltura server, by attaching it with the uploaded file
        KalturaBaseEntry newEntry = client.getBaseEntryService().addFromUploadedFile(entry, token);

        // newEntry now contains the information of the new entry that was just created on the server
        Log.w(TAG, "New entry created successfuly with ID " + newEntry.id);
    }
}
