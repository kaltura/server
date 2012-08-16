package com.kaltura.activity;

import java.util.ArrayList;
import java.util.List;
import java.util.Observable;
import java.util.Observer;

import android.app.Dialog;
import android.content.Intent;
import android.content.pm.ActivityInfo;
import android.content.res.Configuration;
import android.database.Cursor;
import android.graphics.Bitmap;
import android.media.ThumbnailUtils;
import android.net.Uri;
import android.os.AsyncTask;
import android.os.Bundle;
import android.provider.MediaStore;
import android.util.Log;
import android.view.KeyEvent;
import android.view.View;
import android.view.Window;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.RelativeLayout;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

import com.kaltura.client.KalturaApiException;
import com.kaltura.client.types.KalturaCategory;
import com.kaltura.client.types.KalturaMediaEntry;
import com.kaltura.enums.States;
import com.kaltura.mediatorActivity.TemplateActivity;
import com.kaltura.services.AdminUser;
import com.kaltura.services.Category;
import com.kaltura.services.Media;
import com.kaltura.services.UploadToken;
import com.kaltura.utils.Utils;

public class Main extends TemplateActivity {

    public static int state = 0;
    private int orientation;
    private static final int GALLERY = 1;
    private static final int RECORD_VIDEO = 2;
    private Dialog dialogVideoInfo;
    private Dialog dialogUpload;
    private Dialog dialogUploadSuccess;
    private List<String> ls;
    private ArrayAdapter<String> adapter;
    private Spinner spinner;
    private String pathfromURI;
    private String category;
    private String title;
    private String description;
    private String tags;
    private ProgressBar pb_uploading;
    private RelativeLayout rl_upload;
    private TextView tv_uploading;
    private boolean startUpload;
    private UploadToken uploadToken;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        init();
        //setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_UNSPECIFIED);
        setContentView(R.layout.main);
        //determineScreenSize();
        setFont();
        extractBundle();
        Configuration c = getResources().getConfiguration();


        orientation = c.orientation;
        switch (determineScreenSize()) {
            case Configuration.SCREENLAYOUT_SIZE_NORMAL:
            case Configuration.SCREENLAYOUT_SIZE_SMALL:
                break;
            case Configuration.SCREENLAYOUT_SIZE_LARGE:
                setDialog();
                break;

            default:
                setDialog();
                break;
        }

    }

    @Override
    public void onConfigurationChanged(Configuration newConfig) {
        super.onConfigurationChanged(newConfig);
        setContentView(R.layout.main);

        //setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_UNSPECIFIED);
        Log.w(TAG, "STATE :" + state);
        orientation = newConfig.orientation;

        switch (determineScreenSize()) {
            case Configuration.SCREENLAYOUT_SIZE_NORMAL:
            case Configuration.SCREENLAYOUT_SIZE_SMALL:
                break;
            case Configuration.SCREENLAYOUT_SIZE_LARGE:
                setDialog();
                break;

            default:
                setDialog();
                break;
        }
    }

    private void setDialog() {
        dialogUpload = new Dialog(Main.this);
        dialogUpload.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialogUpload.setContentView(R.layout.upload);

        dialogVideoInfo = new Dialog(Main.this);
        dialogVideoInfo.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialogVideoInfo.setContentView(R.layout.video_info);

        dialogUploadSuccess = new Dialog(Main.this);
        dialogUploadSuccess.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialogUploadSuccess.setContentView(R.layout.succsess_upload);
    }

    @Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
        switch (keyCode) {
            case KeyEvent.KEYCODE_MENU:
                return true;
            case KeyEvent.KEYCODE_BACK:
                setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_UNSPECIFIED);
                moveTaskToBack(true);
                break;
            default:
                break;
        }
        return super.onKeyDown(keyCode, event);
    }

    private void setFont() {
        ((TextView) findViewById(R.id.tv_most_popular)).setTypeface(typeFont);
        ((TextView) findViewById(R.id.tv_video_catigories)).setTypeface(typeFont);
        ((TextView) findViewById(R.id.tv_upload)).setTypeface(typeFont);
        ((TextView) findViewById(R.id.tv_settings)).setTypeface(typeFont);
    }

    public void onClick(View v) {
        switch (v.getId()) {
            case R.id.ibtn_most_popular:
                if (AdminUser.userIsLogin()) {
                    switch (determineScreenSize()) {
                        case Configuration.SCREENLAYOUT_SIZE_NORMAL:
                        case Configuration.SCREENLAYOUT_SIZE_SMALL:
                            getActivityMediator().showMostPopular();
                            break;
                        case Configuration.SCREENLAYOUT_SIZE_LARGE:
                            getActivityMediator().showVideoCategories(true);
                            break;
                        default:
                            getActivityMediator().showVideoCategories(true);
                            break;
                    }
                } else {
                    getActivityMediator().showSettings();
                    state = 1;
                }
                break;
            case R.id.ibtn_video_categories:
                if (AdminUser.userIsLogin()) {
                    getActivityMediator().showVideoCategories();
                } else {
                    getActivityMediator().showSettings();
                    state = 2;
                }
                break;
            case R.id.ibtn_upload:
                switch (determineScreenSize()) {
                    case Configuration.SCREENLAYOUT_SIZE_NORMAL:
                    case Configuration.SCREENLAYOUT_SIZE_SMALL:
                        if (AdminUser.userIsLogin()) {
                            getActivityMediator().showUpload();
                        } else {
                            getActivityMediator().showSettings();
                            state = 3;
                        }

                        break;
                    case Configuration.SCREENLAYOUT_SIZE_LARGE:
                        if (AdminUser.userIsLogin()) {
                            showUploadDialog();
                        } else {
                            getActivityMediator().showSettings();
                            state = 3;
                        }
                        break;

                    default:
                        if (AdminUser.userIsLogin()) {
                            showUploadDialog();
                        } else {
                            getActivityMediator().showSettings();
                            state = 3;
                        }
                        break;
                }

                break;
            case R.id.ibtn_settings:
                getActivityMediator().showSettings();
                state = 4;
                break;
            default:
                break;
        }
    }

    private void showUploadDialog() {
        //set up dialogVideoInfo

        dialogUpload.setCancelable(true);

        LinearLayout ll_upload = (LinearLayout) dialogUpload.findViewById(R.id.ll_upload);

        if (display.getWidth() > display.getHeight()) {
            ll_upload.getLayoutParams().width = display.getHeight();
            ll_upload.getLayoutParams().height = display.getHeight() / 2;
        } else {
            ll_upload.getLayoutParams().width = display.getWidth();
            ll_upload.getLayoutParams().height = display.getWidth() / 2;
        }

        //set up button
        RelativeLayout rl_recod_video = (RelativeLayout) dialogUpload.findViewById(R.id.rl_recod_video);
        rl_recod_video.setOnClickListener(new View.OnClickListener() {

            Intent intent;

            @Override
            public void onClick(View v) {
                intent = new Intent(MediaStore.ACTION_VIDEO_CAPTURE);
                intent.putExtra(MediaStore.EXTRA_VIDEO_QUALITY, 1); // set the video image quality to high
                intent.putExtra(MediaStore.EXTRA_DURATION_LIMIT, 0);
                // start the Video Capture Intent
                startActivityForResult(intent, RECORD_VIDEO);
            }
        });
        RelativeLayout rl_pick_from_gallery = (RelativeLayout) dialogUpload.findViewById(R.id.rl_pick_from_gallery);
        rl_pick_from_gallery.setOnClickListener(new View.OnClickListener() {

            Intent intent;

            @Override
            public void onClick(View v) {
                intent = new Intent(Intent.ACTION_PICK, null);
                intent.setType("video/*");
                startActivityForResult(intent, 1);
            }
        });
        ImageView iv_close = (ImageView) dialogUpload.findViewById(R.id.iv_close);
        iv_close.setOnClickListener(new View.OnClickListener() {

            @Override
            public void onClick(View v) {
                finish();
                getActivityMediator().showMain();
            }
        });

        //now that the dialogVideoInfo is set up, it's time to show it    
        dialogUpload.show();
    }

    private void showVideoInfoDialog(final String pathFromUri) {
        dialogUpload.cancel();
        //set up dialogVideoInfo

        switch (orientation) {
            case Configuration.ORIENTATION_PORTRAIT:
            case Configuration.ORIENTATION_UNDEFINED:
            case Configuration.ORIENTATION_SQUARE:
                setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_PORTRAIT);
                break;
            case Configuration.ORIENTATION_LANDSCAPE:
                setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_LANDSCAPE);
                break;
            default:
                break;
        }

        new DownloadListCatigoriesTask().execute();
        dialogVideoInfo.setCancelable(true);

        ImageView iv_thumb = (ImageView) dialogVideoInfo.findViewById(R.id.iv_thumb);
        LinearLayout ll_video_info = (LinearLayout) dialogVideoInfo.findViewById(R.id.ll_video_info);
        if (display.getWidth() > display.getHeight()) {
            ll_video_info.getLayoutParams().width = display.getHeight();
            ll_video_info.getLayoutParams().height = (int) Math.round((float) display.getHeight() * 0.75);
        } else {
            ll_video_info.getLayoutParams().width = (int) Math.round((float) display.getWidth() * 0.7);
            ll_video_info.getLayoutParams().height = (int) Math.round((float) display.getHeight() * 0.73);
            iv_thumb.getLayoutParams().height = (int) Math.round((float) display.getHeight() * 0.5) / 2;
        }

        Bitmap bMap = ThumbnailUtils.createVideoThumbnail(pathFromUri, MediaStore.Video.Thumbnails.MICRO_KIND);
        iv_thumb.setScaleType(ImageView.ScaleType.CENTER_CROP);
        iv_thumb.setImageBitmap(bMap);

        pb_uploading = (ProgressBar) dialogVideoInfo.findViewById(R.id.pb_uploading);
        tv_uploading = (TextView) dialogVideoInfo.findViewById(R.id.tv_uploading);
        final EditText et_title = (EditText) dialogVideoInfo.findViewById(R.id.et_title);
        final EditText et_description = (EditText) dialogVideoInfo.findViewById(R.id.et_description);
        final EditText et_tags = (EditText) dialogVideoInfo.findViewById(R.id.et_tags);

        uploadToken = new UploadToken(TAG, 5);
        //set up button
        ImageView iv_close = (ImageView) dialogVideoInfo.findViewById(R.id.iv_close);
        rl_upload = (RelativeLayout) dialogVideoInfo.findViewById(R.id.rl_upload);
        rl_upload.setVisibility(View.VISIBLE);
        pb_uploading.setVisibility(View.GONE);
        tv_uploading.setVisibility(View.GONE);

        iv_close.setOnClickListener(new View.OnClickListener() {

            @Override
            public void onClick(View v) {
                setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_UNSPECIFIED);
                rl_upload.setVisibility(View.VISIBLE);
                pb_uploading.setVisibility(View.GONE);
                tv_uploading.setVisibility(View.GONE);
                dialogVideoInfo.cancel();
                uploadToken.setStartUpload(false);
                Log.w(TAG, "close video info dialog");
            }
        });
        rl_upload.setOnClickListener(new View.OnClickListener() {

            @Override
            public void onClick(View v) {
                rl_upload.setVisibility(View.GONE);
                pb_uploading.setVisibility(View.VISIBLE);
                tv_uploading.setVisibility(View.VISIBLE);

                if (spinner != null) {
                    Main.this.pathfromURI = pathFromUri;
                    Main.this.category = spinner.getSelectedItem().toString();
                    Main.this.title = et_title.getText().toString();
                    Main.this.description = et_description.getText().toString();
                    Main.this.tags = et_tags.getText().toString();
                    uploadToken.setStartUpload(true);
                    new UploadDataTask().execute();
                }
            }
        });
        dialogVideoInfo.show();
    }

    private void showUploadSuccessDialog() {
        //set up dialogVideoInfo
        dialogUploadSuccess.setCancelable(true);
        state = 0;
        ImageView iv_thumb = (ImageView) dialogUploadSuccess.findViewById(R.id.iv_thumb);

        LinearLayout ll_video_info = (LinearLayout) dialogUploadSuccess.findViewById(R.id.ll_upload_success);
        if (display.getWidth() > display.getHeight()) {
            ll_video_info.getLayoutParams().width = display.getHeight();
            ll_video_info.getLayoutParams().height = (int) Math.round((float) display.getHeight() * 0.7);
        } else {
            ll_video_info.getLayoutParams().width = (int) Math.round((float) display.getWidth() * 0.7);
            ll_video_info.getLayoutParams().height = (int) Math.round((float) display.getHeight() * 0.6);
            iv_thumb.getLayoutParams().height = (int) Math.round((float) display.getHeight() * 0.6) / 2;
        }


        Bitmap bMap = ThumbnailUtils.createVideoThumbnail(Main.this.pathfromURI, MediaStore.Video.Thumbnails.MICRO_KIND);
        iv_thumb.setScaleType(ImageView.ScaleType.CENTER_CROP);
        iv_thumb.setImageBitmap(bMap);

        //set up button
        ImageView iv_close = (ImageView) dialogUploadSuccess.findViewById(R.id.iv_close);
        iv_close.setOnClickListener(new View.OnClickListener() {

            @Override
            public void onClick(View v) {
                setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_UNSPECIFIED);
                finish();
                getActivityMediator().showMain();
            }
        });

        Button btn_upload_successfully = (Button) dialogUploadSuccess.findViewById(R.id.btn_upload_successfully);
        btn_upload_successfully.setOnClickListener(new View.OnClickListener() {

            @Override
            public void onClick(View v) {
                setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_UNSPECIFIED);
                finish();
                getActivityMediator().showMain();
            }
        });

        //now that the dialogVideoInfo is set up, it's time to show it    
        dialogUploadSuccess.show();
    }

    private void extractBundle() {
        try {
            Bundle extras = getIntent().getExtras();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        if ((resultCode == RESULT_OK) && (data != null)) {
            switch (requestCode) {
                case RECORD_VIDEO:
                    Log.w(TAG, "real path: " + getRealPathFromURI(data.getData()));
                    showVideoInfoDialog(getRealPathFromURI(data.getData()));
                    break;
                case GALLERY:
                    Log.w(TAG, "real path: " + getRealPathFromURI(data.getData()));
                    showVideoInfoDialog(getRealPathFromURI(data.getData()));
                    break;
                default:
                    break;
            }
        }
    }

    public String getRealPathFromURI(Uri currImageURI) {
        // can post image
        String[] proj = {MediaStore.Video.Media.DATA};
        Cursor cursor = managedQuery(currImageURI,
                proj, // Which columns to return
                null, // WHERE clause; which rows to return (all rows)
                null, // WHERE clause selection arguments (none)
                null); // Order-by clause (ascending by name)
        int column_index = cursor.getColumnIndexOrThrow(MediaStore.Video.Media.DATA);
        cursor.moveToFirst();
        return cursor.getString(column_index);
    }

    private class UploadDataTask extends AsyncTask<Void, States, Void> implements Observer {

        private String message;
        private boolean isUploaded = false;
        private int progress = 0;

        public UploadDataTask() {
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
                dialogVideoInfo.cancel();
                showUploadSuccessDialog();
            } else {
                Toast.makeText(context, "Upload is error. Repeat please!", Toast.LENGTH_SHORT).show();
                Main.this.rl_upload.setVisibility(View.VISIBLE);
                pb_uploading.setVisibility(View.GONE);
                Main.this.tv_uploading.setVisibility(View.GONE);
                dialogVideoInfo.cancel();
                showUploadDialog();
            }
        }

        @Override
        protected void onProgressUpdate(States... progress) {
            for (States state : progress) {
                if (state == States.LOADING_DATA) {
                    progressDialog.hide();
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

    private class DownloadListCatigoriesTask extends AsyncTask<Void, States, Void> {

        private String message;
        private List<KalturaCategory> listCategory;

        @Override
        protected Void doInBackground(Void... params) {
            // Test for connection
            try {
                if (Utils.checkInternetConnection(getApplicationContext())) {
                    /**
                     * Getting list of all categories
                     */
                    publishProgress(States.LOADING_DATA);
                    listCategory = Category.listAllCategories(TAG, 1, 500);
                }
            } catch (KalturaApiException e) {
                e.printStackTrace();
                message = e.getMessage();
                Log.w(TAG, message);
                publishProgress(States.ERR);
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

            if (listCategory != null) {
                ls = new ArrayList<String>();
                for (KalturaCategory list : listCategory) {
                    ls.add(list.name);
                }
                spinner = (Spinner) dialogVideoInfo.findViewById(R.id.sp_category);
                adapter = new ArrayAdapter<String>(Main.this, android.R.layout.simple_spinner_item, ls);
                adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
                spinner.setAdapter(adapter);
            } else {
                Log.w(TAG, "Data weren't load!");
                dialogVideoInfo.cancel();
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
            }
        }
    }
}
