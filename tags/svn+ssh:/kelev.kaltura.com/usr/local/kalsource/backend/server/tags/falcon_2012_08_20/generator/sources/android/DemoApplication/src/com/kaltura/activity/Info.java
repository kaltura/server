package com.kaltura.activity;

import java.text.SimpleDateFormat;
import java.util.Date;

import android.app.Activity;
import android.content.res.Configuration;
import android.graphics.Bitmap;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.MotionEvent;
import android.view.View;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.kaltura.bar.ActionBar;
import com.kaltura.client.KalturaApiException;
import com.kaltura.client.types.KalturaMediaEntry;
import com.kaltura.enums.States;
import com.kaltura.mediatorActivity.TemplateActivity;
import com.kaltura.services.Media;
import com.kaltura.sharing.Sharing;
import com.kaltura.utils.Utils;
import com.nostra13.universalimageloader.core.DisplayImageOptions;
import com.nostra13.universalimageloader.core.ImageLoader;
import com.nostra13.universalimageloader.core.ImageLoaderConfiguration;
import com.nostra13.universalimageloader.core.ImageLoadingListener;

public class Info extends TemplateActivity {

    private String entryId;
    private KalturaMediaEntry entry;
    private LinearLayout ll_info;
    private DownloadEntryTask downloadTask;
    private ImageView iv_thumbnail;
    private boolean isDownload;
    private Bitmap bitmap;
    private String nameCategory;
    private int orientation;
    private Activity activity;
    private Sharing sharing;

    public Info() {
        downloadTask = new DownloadEntryTask();
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        init();
        setContentView(R.layout.info);

        activity = this;
        Configuration c = getResources().getConfiguration();
        orientation = c.orientation;
        setView();

        extractBundle();

        if (bar != null) {
            bar.setTitle(getText(R.string.info));
            bar.setVisibleBackButton(View.VISIBLE);
            bar.setVisibleNameCategory(View.VISIBLE);
            bar.setTextNameCategory(nameCategory);
        }
        ll_info.setVisibility(View.INVISIBLE);
        sharing = new Sharing(this);

        switch (orientation) {
            case Configuration.ORIENTATION_PORTRAIT:
            case Configuration.ORIENTATION_UNDEFINED:
            case Configuration.ORIENTATION_SQUARE:
                //setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_PORTRAIT);
                iv_thumbnail.getLayoutParams().height = display.getHeight() / 2;
                iv_thumbnail.getLayoutParams().width = display.getWidth();
                downloadTask.execute();
                break;
            case Configuration.ORIENTATION_LANDSCAPE:
                //setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_LANDSCAPE);
                iv_thumbnail.getLayoutParams().height = display.getHeight() - display.getHeight() / 3;
                iv_thumbnail.getLayoutParams().width = display.getWidth();
                downloadTask.execute();
                break;
            default:
                break;
        }
    }

    @Override
    public void onConfigurationChanged(Configuration newConfig) {
        super.onConfigurationChanged(newConfig);
        setContentView(R.layout.info);
        this.orientation = newConfig.orientation;
        setView();
        if (bar != null) {
            bar.setTitle(getText(R.string.info));
            bar.setVisibleNameCategory(View.VISIBLE);
            bar.setTextNameCategory(nameCategory);
        }
        if (isDownload) {
            updateData();
        }


        Log.w(TAG, "oreientation: " + newConfig.orientation);
        switch (orientation) {
            case Configuration.ORIENTATION_PORTRAIT:
            case Configuration.ORIENTATION_UNDEFINED:
            case Configuration.ORIENTATION_SQUARE:
                iv_thumbnail.getLayoutParams().height = display.getHeight() / 2;
                iv_thumbnail.getLayoutParams().width = display.getWidth();
                break;
            case Configuration.ORIENTATION_LANDSCAPE:
                iv_thumbnail.getLayoutParams().height = display.getHeight() - display.getHeight() / 3;
                iv_thumbnail.getLayoutParams().width = display.getWidth();
                break;
            default:
                break;
        }

    }

    private void setView() {
        bar = new ActionBar(this, TAG);
        ll_info = (LinearLayout) findViewById(R.id.ll_info);
        iv_thumbnail = ((ImageView) findViewById(R.id.iv_thumbnail));
        setFont();

    }

    @Override
    public void onStart() {
        super.onStart();
        sharing.addListener();
    }

    @Override
    protected void onStop() {
        super.onStop();
        sharing.removeListener();
    }

    private void setFont() {
        ((TextView) findViewById(R.id.tv_name)).setTypeface(typeFont);
        ((TextView) findViewById(R.id.tv_episode)).setTypeface(typeFont);
        ((TextView) findViewById(R.id.tv_duration)).setTypeface(typeFont);
        ((TextView) findViewById(R.id.tv_description)).setTypeface(typeFont);
    }

    private void extractBundle() {
        try {
            Bundle extras = getIntent().getExtras();
            entryId = extras.getString("entryId");
            nameCategory = extras.getString("nameCategory");

        } catch (Exception e) {
            e.printStackTrace();
            entryId = "";
            nameCategory = "";
        }
    }

    /**
     * Called to process touch screen events.
     */
    @Override
    public boolean dispatchTouchEvent(MotionEvent ev) {

        switch (ev.getAction()) {
            case MotionEvent.ACTION_DOWN:
                break;
            case MotionEvent.ACTION_UP:
                break;
            case MotionEvent.ACTION_MOVE:
                //Hide the keyboard on the screen of a finger        	
                //imm.hideSoftInputFromWindow(getWindow().getCurrentFocus().getWindowToken(), 0);
                break;
        }
        return super.dispatchTouchEvent(ev);
    }

    public void onClick(View v) {
        switch (v.getId()) {
            case R.id.iv_button_play:
                Log.w(TAG, "test play button");
                String url = entry.thumbnailUrl + "/width/" + new Integer(display.getWidth()).toString() + "/height/" + new Integer(display.getHeight() / 2).toString();
                getActivityMediator().showPlayer(entry.id, entry.downloadUrl, entry.duration, url);
                break;
            case R.id.iv_button_facebook:
                Log.w(TAG, "test facebook button");
                sharing.sendToFacebook(entry);
                break;
            case R.id.iv_button_twitter:
                Log.w(TAG, "test twitter button");
                sharing.sendToTwitter(entry);
                break;
            case R.id.iv_button_mail:
                Log.w(TAG, "test mail button");
                sharing.sendToMail(entry);
                break;
            case R.id.iv_bar_menu:
                getActivityMediator().showMain();
                break;
            case R.id.rl_button_back:
                finish();
                break;
            default:
                break;
        }
    }

    private void updateData() {
        String url = entry.thumbnailUrl + "/width/" + new Integer(display.getWidth()).toString() + "/height/" + new Integer(display.getHeight() / 2).toString();
        ImageLoader(url);
    }


    private class DownloadEntryTask extends AsyncTask<Void, States, Void> {

        private String message;

        @Override
        protected Void doInBackground(Void... params) {
            // Test for connection
            Log.w(TAG, "Thread is start");
            try {
                if (Utils.checkInternetConnection(getApplicationContext())) {
                    /**
                     * Getting information about the entry
                     */
                    publishProgress(States.LOADING_DATA);
                    try {
                        entry = Media.getEntrybyId(TAG, entryId);
                    } catch (KalturaApiException e) {
                        e.printStackTrace();
                        Log.w(TAG, "error get entry by id: " + e.getMessage());
                        entry = new KalturaMediaEntry();
                    }
                }
                Log.w(TAG, "Thread is end");
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
            isDownload = true;
            updateData();
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
            }
        }
    }

    private void ImageLoader(String url) {
        Log.w(TAG, "Start image loader");

        DisplayImageOptions options = new DisplayImageOptions.Builder()
                .cacheInMemory().cacheOnDisc().build();

        // This configuration tuning is custom. You can tune every option, you may tune some of them, 
        // or you can create default configuration by
        //  ImageLoaderConfiguration.createDefault(this);
        // method.
        ImageLoaderConfiguration config = new ImageLoaderConfiguration.Builder(activity).threadPoolSize(3).threadPriority(Thread.NORM_PRIORITY - 2).memoryCacheSize(150000000) // 150 Mb
                .discCacheSize(50000000) // 50 Mb
                .httpReadTimeout(10000) // 10 s
                .denyCacheImageMultipleSizesInMemory().build();
        // Initialize ImageLoader with configuration.
        ImageLoader.getInstance().init(config);
        ImageLoader.getInstance().enableLogging(); // Not necessary in common
        imageLoader.init(config);


        iv_thumbnail.setScaleType(ImageView.ScaleType.CENTER_CROP);
        ((TextView) findViewById(R.id.tv_name)).setText(entry.name);
        ((TextView) findViewById(R.id.tv_episode)).setText("");
        SimpleDateFormat sdf = new SimpleDateFormat("mm:ss");
        ((TextView) findViewById(R.id.tv_duration)).setText(sdf.format(new Date(entry.duration * 1000)));
        ((TextView) findViewById(R.id.tv_description)).setText(entry.description);
        ll_info.setVisibility(View.VISIBLE);


        imageLoader.displayImage(url, iv_thumbnail, options, new ImageLoadingListener() {

            @Override
            public void onLoadingStarted() {
                // do nothing
                Log.w(TAG, "onLoadingStarted");
            }

            @Override
            public void onLoadingFailed() {
                Log.w(TAG, "onLoadingFailed");
                imageLoader.clearMemoryCache();
                imageLoader.clearDiscCache();
            }

            @Override
            public void onLoadingComplete() {
                // do nothing
                Log.w(TAG, "onLoadingComplete: ");
            }
        });



    }
}
