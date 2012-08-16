package com.kaltura.activity;

import java.util.ArrayList;
import java.util.Collections;
import java.util.List;

import android.app.Activity;
import android.content.pm.ActivityInfo;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.MotionEvent;
import android.view.SurfaceHolder;
import android.view.SurfaceView;
import android.view.View;
import android.view.WindowManager;
import android.widget.Toast;

import com.kaltura.client.KalturaApiException;
import com.kaltura.client.types.KalturaFlavorAsset;
import com.kaltura.enums.States;
import com.kaltura.mediatorActivity.TemplateActivity;
import com.kaltura.services.FlavorAsset;
import com.kaltura.utils.Sort;
import com.kaltura.utils.Utils;

public class Player extends TemplateActivity implements SurfaceHolder.Callback {

    private com.kaltura.player.ViewPlayer viewPlayer;
    private String dataUrl;
    private String entryId;
    private SurfaceView surface;
    private SurfaceHolder holder;
    private int duration;
    private Activity activity;
    private List<KalturaFlavorAsset> listFlavorAssets = new ArrayList<KalturaFlavorAsset>();
    private List<KalturaFlavorAsset> copyListFlavorAssets = new ArrayList<KalturaFlavorAsset>();
    private String url;

    @Override
    public void onCreate(Bundle savedInstanceState) {

        super.onCreate(savedInstanceState);
        init();
        /**
         * Screen orientation is portrait
         */
        setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_LANDSCAPE);
        /**
         * Hide notification bar
         */
        getWindow().setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN, WindowManager.LayoutParams.FLAG_FULLSCREEN);
        setContentView(R.layout.player);

        extractBundle();

        activity = this;

        surface = (SurfaceView) findViewById(R.id.surfaceView1);
    }

    @Override
    public void onResume() {
        super.onResume();
        holder = surface.getHolder();
        holder.addCallback(this);
        try {
            holder.setType(SurfaceHolder.SURFACE_TYPE_PUSH_BUFFERS);
            holder.setFixedSize(400, 300);
            viewPlayer = new com.kaltura.player.ViewPlayer(TAG, activity, holder, duration, entryId);
            viewPlayer.setThumb(url);
            new DownloadTask().execute();
        } catch (IllegalArgumentException e) {
            e.printStackTrace();
            Log.w(TAG, "err: " + e);
        } catch (SecurityException e) {
            e.printStackTrace();
            Log.w(TAG, "err: " + e);
        } catch (IllegalStateException e) {
            e.printStackTrace();
            Log.w(TAG, "err: " + e);
        }
    }

    @Override
    protected void onPause() {
        super.onPause();
        if (viewPlayer != null) {
            viewPlayer.setRelease();
        }
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
        if (viewPlayer != null) {
            viewPlayer.setRelease();
            viewPlayer = null;
        }
    }

    public void surfaceCreated(SurfaceHolder arg0) {
        Log.w(TAG, "surfaceCreated called");
    }

    public void surfaceChanged(SurfaceHolder arg0, int arg1, int arg2, int arg3) {
        Log.w(TAG, "surfaceChanged called");
    }

    public void surfaceDestroyed(SurfaceHolder arg0) {
        Log.w(TAG, "surfaceDestroyed called");
    }

    private void extractBundle() {
        try {
            Bundle extras = getIntent().getExtras();
            entryId = extras.getString("entryId");
            dataUrl = extras.getString("dataUrl");
            url = extras.getString("url");
            duration = extras.getInt("duration");
            Log.w(TAG, "dataUrl: " + dataUrl + " duration: " + duration);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    /**
     * Called to process touch screen events.
     */
    @Override
    public boolean dispatchTouchEvent(MotionEvent ev) {

        switch (ev.getAction()) {
            case MotionEvent.ACTION_UP:
                if (viewPlayer != null && viewPlayer.getStatePanel() == View.VISIBLE) {// && !viewPlayer.isRun()){
                    //Panel is visible
                }
                break;
            case MotionEvent.ACTION_DOWN:
                break;

            case MotionEvent.ACTION_MOVE:
                if (viewPlayer != null) {
                    viewPlayer.hidePanel();
                    viewPlayer.setStatePanel(View.VISIBLE);
                } else {
                }
                break;
        }
        return super.dispatchTouchEvent(ev);
    }

    private class DownloadTask extends AsyncTask<Void, States, Void> {

        private String message;

        @Override
        protected Void doInBackground(Void... params) {
            // Test for connection
            try {
                if (Utils.checkInternetConnection(getApplicationContext())) {
                    //Getting list of all entries category
                    publishProgress(States.LOADING_DATA);
                    try {
                        listFlavorAssets = FlavorAsset.listAllFlavorAssets(TAG, entryId, 1, 500);
                        Collections.sort(listFlavorAssets, new Sort<KalturaFlavorAsset>("bitrate", "reverse"));
                        for (KalturaFlavorAsset f : listFlavorAssets) {
                            Log.w(TAG, "FLAVORS:  containerFormat: " + f.containerFormat + " description: " + f.description + " bitrate: " + f.bitrate + " frameRate: " + f.frameRate
                                    + " size: " + f.size + " height: " + f.height + " width: " + f.width + " fileExt " + f.fileExt + " partnerDescription " + f.partnerDescription
                                    + " tags: " + f.tags + " videoCodecId " + f.videoCodecId);
                        }
                        for (KalturaFlavorAsset kalturaFlavorAsset : listFlavorAssets) {
                            if (!new Integer(Math.round(kalturaFlavorAsset.bitrate / 100) * 100).equals(0) && (kalturaFlavorAsset.tags.indexOf("iphonenew")) != -1) {
                                copyListFlavorAssets.add(kalturaFlavorAsset);
                            }
                        }
                    } catch (KalturaApiException e) {
                        e.printStackTrace();
                        Log.w(TAG, "err: " + e.getMessage(), e);
                    }

                }
                Log.w(TAG, "Thread is end!");
            } catch (Exception e) {
                e.printStackTrace();
                message = e.getMessage();
                Log.w(TAG, "" + message);
                publishProgress(States.NO_CONNECTION);
            }
            return null;
        }

        @Override
        protected void onPostExecute(Void param) {
            progressDialog.hide();

            Log.w(TAG, "----------------");
            for (KalturaFlavorAsset f : copyListFlavorAssets) {
                Log.w(TAG, "FLAVORS:  containerFormat: " + f.containerFormat + " description: " + f.description + " bitrate: " + f.bitrate + " frameRate: " + f.frameRate
                        + " size: " + f.size + " height: " + f.height + " width: " + f.width + " fileExt " + f.fileExt + " partnerDescription " + f.partnerDescription
                        + " tags " + f.tags + " videoCodecId " + f.videoCodecId);

            }
            if (viewPlayer != null) {
                viewPlayer.addListRates(copyListFlavorAssets);
                viewPlayer.autoStart();
            }
        }

        @Override
        protected void onProgressUpdate(States... progress) {
            for (States state : progress) {
                if (state == States.LOADING_DATA) {
                    progressDialog.hide();
                    if (viewPlayer != null) {
                        viewPlayer.setVisibleLoading();
                    }

                }
                if (state == States.NO_CONNECTION) {
                    progressDialog.hide();
                    Toast.makeText(context, message, Toast.LENGTH_SHORT).show();
                }
            }
        }
    }
}