package com.kaltura.activity;

import java.util.Observable;
import java.util.Observer;

import android.content.pm.ActivityInfo;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import com.kaltura.bar.ActionBar;
import com.kaltura.client.types.KalturaMediaEntry;
import com.kaltura.enums.States;
import com.kaltura.mediatorActivity.TemplateActivity;
import com.kaltura.services.Media;
import com.kaltura.services.UploadToken;
import com.kaltura.utils.Utils;

public class Uploading extends TemplateActivity {

    private String pathfromURI;
    private String category;
    private String title;
    private String description;
    private String tags;
    private ProgressBar pb_uploading;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        init();
        setContentView(R.layout.uploading);
        setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_PORTRAIT);

        pb_uploading = (ProgressBar) findViewById(R.id.pb_uploading);

        bar = new ActionBar(this, TAG);
        bar.setTitle(getText(R.string.upload));
        bar.setVisibleBackButton(View.INVISIBLE);
        bar.setVisibleSearchButon(View.INVISIBLE);

        extractBundle();
        setFont();
        new UploadDataTask().execute();
    }

    private void extractBundle() {
        try {
            Bundle extras = getIntent().getExtras();
            pathfromURI = extras.getString("pathfromURI");
            category = extras.getString("category");
            title = extras.getString("title");
            description = extras.getString("description");
            tags = extras.getString("tags");
            Log.w(TAG, "extracted data: ");
            Log.w(TAG, "pathfromURI: " + pathfromURI);
            Log.w(TAG, "category: " + category);
            Log.w(TAG, "title: " + title);
            Log.w(TAG, "description: " + description);
            Log.w(TAG, "tags: " + tags);
        } catch (Exception e) {
            e.printStackTrace();
            Log.w(TAG, "err: " + e.getMessage());
            pathfromURI = "";
            category = "";
            title = "";
            description = "";
            tags = "";
        }

    }

    private void setFont() {
        ((TextView) findViewById(R.id.tv_uploading)).setTypeface(typeFont);
    }

    public void onClick(View v) {
        switch (v.getId()) {
            case R.id.iv_bar_menu:
                getActivityMediator().showMain();
                break;
            default:
                break;
        }

    }

    private class UploadDataTask extends AsyncTask<Void, States, Void> implements Observer {

        private String message;
        private boolean isUploaded = false;
        private UploadToken uploadToken;
        private int progress = 0;

        public UploadDataTask() {
            uploadToken = new UploadToken(TAG, 5);
            uploadToken.setStartUpload(true);
            uploadToken.addObserver(this);
        }

        @Override
        protected Void doInBackground(Void... params) {
            // Test for connection
            try {
                if (Utils.checkInternetConnection(getApplicationContext())) {
                    /**
                     * Getting list of all categories
                     */
                    publishProgress(States.LOADING_DATA);

                    if (pathfromURI != null && category != null && title != null && description != null && tags != null) {
                        message = "Create new entry";
                        KalturaMediaEntry newEntry = Media.addEmptyEntry(TAG, category, title, description, tags);
                        message = "Uploading data";
                        isUploaded = uploadToken.uploadMediaFileAndAttachToEmptyEntry(TAG, newEntry, pathfromURI);
                    } else {
                        message = "data null";
                        publishProgress(States.ERR);
                    }
                }
            } catch (Exception e) {
                e.printStackTrace();
                message = e.getMessage();
                Log.w(TAG, message);
                publishProgress(States.NO_CONNECTION);
            }
            return null;
        }

        @Override
        protected void onPostExecute(Void param) {
            progressDialog.hide();
            if (isUploaded) {
                getActivityMediator().showSuccessUpload();
            } else {
                Toast.makeText(context, "Upload is error. Repeat please!", Toast.LENGTH_SHORT).show();
                getActivityMediator().showUpload();
            }
        }

        @Override
        protected void onProgressUpdate(States... progress) {
            for (States state : progress) {
                if (state == States.LOADING_DATA) {
                    progressDialog.hide();
                    showProgressDialog("Loading data...");
                }
                if (state == States.NO_CONNECTION) {
                    progressDialog.hide();
                    Toast.makeText(context, message, Toast.LENGTH_SHORT).show();
                }
                if (state == States.ERR) {
                    progressDialog.hide();
                    Toast.makeText(context, message, Toast.LENGTH_SHORT).show();
                }
                if (state == States.UPLOADING_DATA) {
                    progressDialog.hide();
                    pb_uploading.setProgress(this.progress);
                }
            }
        }

        public void update(Observable paramObservable, Object paramObject) {
            progress = (Integer) paramObject;
            Log.w(TAG, "%: " + progress);
            publishProgress(States.UPLOADING_DATA);
        }
    }
}
