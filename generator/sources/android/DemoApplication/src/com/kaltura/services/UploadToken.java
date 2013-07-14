/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package com.kaltura.services;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.util.Observable;
import java.util.logging.Level;
import java.util.logging.Logger;

import android.os.Environment;
import android.util.Log;

import com.kaltura.client.KalturaApiException;
import com.kaltura.client.KalturaClient;
import com.kaltura.client.types.KalturaMediaEntry;
import com.kaltura.client.types.KalturaUploadToken;
import com.kaltura.client.types.KalturaUploadedFileTokenResource;

/**
 * Upload files to the server
 */
public class UploadToken extends Observable {

    private String TAG;
    private File fileData;
    private KalturaUploadToken kalturaUploadToken;
    private KalturaClient client;
    private int setAttemptUpload;
    private KalturaMediaEntry newEntry;
    private float uploadedFileSize;
    private boolean startUpload;

    /**
     * Constructor Description of UploadToken
     *
     * @param TAG constant in your class
     * @param setAttemptUpload quantity attempts upload to the server
     */
    public UploadToken(String TAG, int setAttemptUpload) {
        this.TAG = TAG;
        fileData = new File(TAG);
        this.setAttemptUpload = setAttemptUpload;
        kalturaUploadToken = new KalturaUploadToken();
        uploadedFileSize = kalturaUploadToken.uploadedFileSize;
        client = AdminUser.getClient();
    }

    /**
     * Get size uploading file in percent
     *
     * @return size uploading file in percent
     */
    public int getUploadedFileSize() {
        int res = 0;
        try {
            res = (int) (kalturaUploadToken.uploadedFileSize / fileData.length() * 100.0);
        } catch (ArithmeticException e) {
            e.printStackTrace();
            Log.w(TAG, e);
            res = 0;
        }
        return res;
    }

    public void setStartUpload(boolean startUpload) {
        this.startUpload = startUpload;
    }

    /**
     * uploads a video file to Kaltura and assigns it to a given Media Entry
     * object
     *
     * @param TAG constant in your class
     * @param entry Entry to which is attached to the file
     * @param pathfromURI File path on local storage
     *
     * @return true - file uploaded; false - file not uploaded
     *
     */
    public boolean uploadMediaFileAndAttachToEmptyEntry(String TAG, KalturaMediaEntry entry, String pathfromURI) {
        Log.w(TAG, "\nUploading a video file...");

        fileData = new File(pathfromURI);

        KalturaUploadToken upToken = null;
        try {
            upToken = client.getUploadTokenService().add();
        } catch (KalturaApiException ex) {
            Logger.getLogger(UploadToken.class.getName()).log(Level.SEVERE, null, ex);
        }

        int sizeBuf = 1024 * 1000;
        byte buf[] = new byte[sizeBuf];
        int numRead = -1;
        String PATH = Environment.getExternalStorageDirectory() + "/download/";
        File outFile = null;
        FileOutputStream fos = null;
        FileInputStream fis = null;
        boolean uploaded = false;
        try {
            fis = new FileInputStream(pathfromURI);
        } catch (FileNotFoundException ex) {
            Log.w(TAG, "err: ", ex);
        }
        int i = 0;
        boolean errUpload = false;
        Log.w(TAG, "HASH:" + new Float(fileData.length()).hashCode());
        int attemptUpload = 0;

        boolean wasFirst = false;
        do {
            if (!errUpload) {
                try {
                    Log.w(TAG, "Available bytes: " + fis.available());
                    buf = new byte[sizeBuf];
                    numRead = fis.read(buf);
                    Log.w(TAG, "Readed bytes: " + numRead);
                    outFile = new File(PATH, "upload.dat");
                    fos = new FileOutputStream(outFile);
                    fos.write(buf, 0, numRead);
                    fos.close();
                } catch (IOException ex) {
                    Log.w(TAG, "err: ", ex);
                }
            } else {
                attemptUpload++;
                Log.w(TAG, "upload error chunk: attemptUpload - " + attemptUpload);
                if (attemptUpload >= setAttemptUpload) {
                    Log.w(TAG, "---");
                }

            }
            if (fileData.length() - kalturaUploadToken.uploadedFileSize > numRead) {
                if (kalturaUploadToken.uploadedFileSize <= uploadedFileSize) {
                    if (addChunk(client, upToken.id, outFile, false, false, -1)) {
                        Log.w(TAG, "1 chunk[" + ++i + "] - uploaddFileSize: " + kalturaUploadToken.uploadedFileSize);
                        wasFirst = true;
                    } else {
                        Log.w(TAG, "error loading chunk!");
                        errUpload = true;
                    }
                } else {
                    if (addChunk(client, upToken.id, outFile, true, false, -1)) {
                        Log.w(TAG, "n chunk[" + ++i + "] - uploaddFileSize: " + kalturaUploadToken.uploadedFileSize);
                    } else {
                        Log.w(TAG, "error loading chunk!");
                        errUpload = true;
                    }
                }
            } else {
                Log.w(TAG, "was:" + wasFirst);
                if (wasFirst) {
                    if (addChunk(client, upToken.id, outFile, true, true, -1)) {
                        Log.w(TAG, "l chunk[" + ++i + "] - uploaddFileSize: " + kalturaUploadToken.uploadedFileSize);
                        uploaded = true;
                        wasFirst = false;
                    } else {
                        Log.w(TAG, "error loading chunk!");
                        errUpload = true;
                    }
                } else {
                    if (addChunk(client, upToken.id, outFile, false, true, -1)) {
                        Log.w(TAG, "n chunk[" + ++i + "] - uploaddFileSize: " + kalturaUploadToken.uploadedFileSize);
                        uploaded = true;
                        wasFirst = false;
                    } else {
                        Log.w(TAG, "error loading chunk!");
                        errUpload = true;
                    }
                }
            }
            setChanged();
            notifyObservers(getUploadedFileSize());
        } while (!uploaded && !(attemptUpload >= setAttemptUpload) && startUpload);

        Log.w(TAG, "HASH:" + new Float(kalturaUploadToken.uploadedFileSize).hashCode());
        if (new Float(kalturaUploadToken.uploadedFileSize).hashCode() == new Float(fileData.length()).hashCode()) {
            uploaded = true;
            startUpload = false;
            try {
                KalturaUploadedFileTokenResource fileTokenResource = new KalturaUploadedFileTokenResource();
                fileTokenResource.token = upToken.id;
                newEntry = client.getMediaService().addContent(entry.id, fileTokenResource);

                Log.w(TAG, "\nUploaded a new Video file to entry: " + newEntry.id);
            } catch (KalturaApiException e) {
                e.printStackTrace();
                Log.w(TAG, "err: " + e.getMessage());
            }
        } else {
            uploaded = false;
        }
        try {
            fis.close();
            fos.close();
        } catch (IOException ex) {
            Log.w(TAG, "err: ", ex);
        }
        return uploaded;
    }

    /**
     * @param KalturaClient client
     * @param String uploadTokenId
     * @param File outFile
     * @param int i
     *
     * @return
     */
    private boolean addChunk(KalturaClient client, String uploadTokenId, File outFile, boolean resume, boolean finalChunk, int resumeAt) {
        boolean isUploaded = false;
        try {
            kalturaUploadToken = client.getUploadTokenService().upload(uploadTokenId, outFile, resume, finalChunk, resumeAt);
            outFile.delete();
            isUploaded = true;
        } catch (KalturaApiException e) {
            e.printStackTrace();
            isUploaded = false;
            Log.w(TAG, "err: " + e.getMessage());
        }
        return isUploaded;
    }
}
