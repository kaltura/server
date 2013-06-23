package com.kaltura.player;

import java.util.ArrayList;
import com.kaltura.client.types.KalturaWidevineFlavorAsset;
import java.util.List;
import java.util.Observable;
import java.util.Observer;

import org.apache.commons.codec.binary.Base64;

import android.app.Activity;
import android.graphics.Color;
import android.graphics.Rect;
import android.graphics.Typeface;
import android.os.AsyncTask;
import android.os.Build.VERSION;
import android.util.Log;
import android.view.SurfaceHolder;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.AbsoluteLayout;
import android.widget.AdapterView;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.ProgressBar;
import android.widget.RelativeLayout;
import android.widget.SeekBar;
import android.widget.TextView;

import com.kaltura.activity.R;
import com.kaltura.boxAdapter.BoxAdapterRate;
import com.kaltura.client.types.KalturaFlavorAsset;
import com.kaltura.services.AdminUser;
import com.kaltura.utils.Utils;
import com.kaltura.widevine.WidevineHandler;
import com.nostra13.universalimageloader.core.DisplayImageOptions;
import com.nostra13.universalimageloader.core.ImageLoader;
import com.nostra13.universalimageloader.core.ImageLoaderConfiguration;
import com.nostra13.universalimageloader.core.ImageLoadingListener;

/**
 * Performs the mapping of elements of the player, handling control events
 */
public class ViewPlayer implements Observer, OnClickListener, SeekBar.OnSeekBarChangeListener, AdapterView.OnItemClickListener {

    private String TAG;
    private Activity activity;
    private View panel_player;
    private TextView tv_player_duration;
    private SeekBar sb_player_duration;
    private ImageView iv_palyer_play;
    private ImageView iv_palyer_sound;
    private Typeface typeFont;
    private int progress_duration;
    public boolean isRelease;
    private ListView list_rates;
    private TextView tv_rate;
    private int mediaSizeKb;
    private ProgressBar pb_loading;
    private View rates;
    private RelativeLayout rate;
    private BoxAdapterRate boxAdapterRate;
    private String flavorId;
    private int duration;
    private TextView tv_player_position;
    private float levelVolume;
    private Player player;
    private String entryId;
    private int partnerId;
    private SeekBar vsb_volume;
    private TextView tv_done;
    private ImageView iv_thumb;
    protected ImageLoader imageLoader = ImageLoader.getInstance();
    private LinearLayout ll_palyer_sound;
    private boolean isRun = false;

    /**
     * Constructor Description of ViewPlayer
     *
     * @param TAG constant in your class
     * @param activity Activities that shows the player
     * @param holder Abstract interface to someone holding a display surface.
     * Allows you to control the surface size and format, edit the pixels in the
     * surface, and monitor changes to the surface. This interface is typically
     * available through the SurfaceView class.
     * @param duration The duration of the video. Used to display the duration
     * of the video player on the control panel
     * @param entryId Used to generate links to the video
     * @param partnerId used to build the playback URL
     */
    public ViewPlayer(String TAG, Activity activity, SurfaceHolder holder, int duration, String entryId, int partnerId) {
        this.TAG = TAG;
        this.activity = activity;
        this.duration = duration;
        this.entryId = entryId;
        this.partnerId = partnerId;
        /**
         * Set type font
         */
        typeFont = Typeface.createFromAsset(activity.getAssets(), "fonts/Maven_Pro_Medium.ttf");
        player = new Player(TAG, holder, activity);
        player.addObserver(this);
        new PlayProgressUpdater().execute();

        panel_player = activity.findViewById(R.id.panel_player);
        panel_player.setVisibility(View.VISIBLE);
        sb_player_duration = (SeekBar) activity.findViewById(R.id.sb_player_duration);
        sb_player_duration.setOnSeekBarChangeListener(this);
        iv_palyer_play = (ImageView) activity.findViewById(R.id.iv_palyer_play);
        iv_palyer_play.setOnClickListener(this);
        iv_palyer_sound = (ImageView) activity.findViewById(R.id.iv_palyer_sound);
        iv_palyer_sound.setOnClickListener(this);

        pb_loading = (ProgressBar) activity.findViewById(R.id.pb_loading);
        pb_loading.setVisibility(View.GONE);

        rate = ((RelativeLayout) activity.findViewById(R.id.rate));
        rate.setOnClickListener(this);

        list_rates = (ListView) activity.findViewById(R.id.list_rates);
        list_rates.setOnItemClickListener(this);
        rates = activity.findViewById(R.id.rates);
        rates.setVisibility(View.GONE);

        tv_player_duration = (TextView) activity.findViewById(R.id.tv_player_duration);
        tv_player_duration.setTypeface(typeFont);
        tv_player_duration.setText(Utils.durationInSecondsToString(duration));
        tv_player_position = (TextView) activity.findViewById(R.id.tv_player_position);
        tv_player_position.setTypeface(typeFont);
        tv_player_position.setText(Utils.durationInSecondsToString(0));

        tv_rate = (TextView) activity.findViewById(R.id.tv_rate);

        vsb_volume = (SeekBar) activity.findViewById(R.id.vsb_volume);
        vsb_volume.setOnSeekBarChangeListener(this);

        tv_done = (TextView) activity.findViewById(R.id.tv_done);
        tv_done.setOnClickListener(this);

        iv_thumb = (ImageView) activity.findViewById(R.id.iv_thumb);
        ll_palyer_sound = (LinearLayout) activity.findViewById(R.id.ll_palyer_sound);
        setStatePanel(View.GONE);
    }

    /**
     * Starts the boot thumbnail
     *
     * @param Link to the thumbnail
     */
    public void setThumb(String url) {
        ImageLoader(url);
    }

    private void ImageLoader(String url) {
        Log.w(TAG, "Start image loader");

        DisplayImageOptions options = new DisplayImageOptions.Builder() //.showStubImage(R.drawable.arrow)
                //.showImageForEmptyUrl(R.drawable.arrow)
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
        iv_thumb.setScaleType(ImageView.ScaleType.CENTER_CROP);

        imageLoader.displayImage(url, iv_thumb, options, new ImageLoadingListener() {

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

    /**
     * Returns the status of the control panel
     *
     * @return true - VISIBLE, false - GONE
     */
    public boolean getVisiblePanel() {
        return tv_done.getVisibility() == View.VISIBLE;
    }

    /**
     * Sets the visible progress bar
     */
    public void setVisibleLoading() {
        pb_loading.setVisibility(View.VISIBLE);
    }

    /**
     * Notification that the progress level has changed.
     */
    @Override
    public void onProgressChanged(SeekBar seekBar, int progress, boolean fromTouch) {
        switch (seekBar.getId()) {
            case R.id.sb_player_duration:
                this.progress_duration = progress;
                break;
            case R.id.vsb_volume:
                levelVolume = (float) (progress / 100.0);
                if (player != null) {
                    player.setVolume(levelVolume, levelVolume);
                } else {
                    Log.w(TAG, "player null");
                }
                break;
        }
    }

    @Override
    public void onStartTrackingTouch(SeekBar seekBar) {
    }

    /**
     * Notification that the user has finished a touch gesture.
     */
    @Override
    public void onStopTrackingTouch(SeekBar seekBar) {
        switch (seekBar.getId()) {
            case R.id.sb_player_duration:
                player.seekTo(player.getDuration() * progress_duration / 100);
                break;

            case R.id.vsb_volume:
                if (levelVolume > 0) {
                    iv_palyer_sound.setBackgroundResource(R.drawable.volume_ico);
                } else {
                    iv_palyer_sound.setBackgroundResource(R.drawable.no_volume_ico);
                }
                break;
        }
    }

    //----------------------------------------LIST VIEW BITRATE-----------------
    /**
     * A value of the selected bit rate from the list of bitrates. Provided the
     * selected specified color, marked with the selected check.
     */
    @Override
    public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
        selectBitrate(position, 1);

        boxAdapterRate.setHighlightIndex(position);
        boxAdapterRate.setVisiblityHighlight(true);
        boxAdapterRate.notifyDataSetChanged();
    }

    //----------------------------------------UPDATE DATA-----------------------
    @Override
    public void update(Observable paramObservable, Object paramObject) {
        if (((STATES) paramObject).equals(STATES.COMPLETE)) {
            sb_player_duration.setProgress(0);
            iv_palyer_play.setBackgroundResource(R.drawable.button_player_play_selector);
        }
        if (((STATES) paramObject).equals(STATES.SEEK_COMPLETE)) {
        }
        if (((STATES) paramObject).equals(STATES.PREAPERED)) {
        }
        if (((STATES) paramObject).equals(STATES.BUFFERING_UPDATE)) {
        }
        if (((STATES) paramObject).equals(STATES.ERROR)) {
            Log.w(TAG, "FAILED PLAYLIST");
        }
        if (((STATES) paramObject).equals(STATES.PROGRESS_LOADING1)) {
            pb_loading.setVisibility(View.VISIBLE);
        }
        if (((STATES) paramObject).equals(STATES.PROGRESS_LOADING)) {
            if (!player.isStarted()) {
                pb_loading.setVisibility(View.VISIBLE);
            }
            sb_player_duration.setSecondaryProgress(player.getDownloadingPercent());
        }
        if (((STATES) paramObject).equals(STATES.LOADING_COMPLETE)) {
            pb_loading.setVisibility(View.GONE);
        }
        if (((STATES) paramObject).equals(STATES.PAUSED)) {
            iv_palyer_play.setBackgroundResource(R.drawable.button_player_play_selector);
        }
        if (((STATES) paramObject).equals(STATES.STARTED)) {
            iv_palyer_play.setBackgroundResource(R.drawable.button_player_pause_selector);
            pb_loading.setVisibility(View.GONE);
            iv_thumb.setVisibility(View.GONE);
        }
    }

    /**
     * Passed a list to display the bitrates
     */
    public void addListRates(List<KalturaFlavorAsset> listRates) {
        if (listRates != null) {
            boxAdapterRate = new BoxAdapterRate(activity, listRates, R.drawable.background_selected_rate);
        } else {
            listRates = new ArrayList<KalturaFlavorAsset>();
            boxAdapterRate = new BoxAdapterRate(activity, listRates, R.drawable.background_selected_rate);
        }
        boxAdapterRate.setVisiblityHighlight(true);
        boxAdapterRate.notifyDataSetChanged();
        boxAdapterRate.setHighlightIndex(1);

        list_rates.setAdapter(boxAdapterRate);

        if (listRates.size() != 0) {
            selectBitrate(0, -1);
        }
    }

    /**
     * Click handler controls (button start, sound control, the control list of
     * bitrates, button done)
     *
     * By clicking the Start button, start playback and changes the icon on the
     * Start icon pause. If there is video and this time pressed a pause, the
     * video stops, the icon changes to a Pause icon launch.
     *
     * By clicking on the element of sound control, showing the progress bar to
     * adjust the sound. Pressing the control sound - the progress bar
     * disappears.
     *
     * By clicking on the control bitrate, displays a list of bitrates. Pressing
     * the control bitrate - with a list of bitrates hiding.
     *
     */
    public void onClick(View view) {
        Rect rect = new Rect();
        AbsoluteLayout.LayoutParams absParams;

        switch (view.getId()) {
            case R.id.iv_palyer_play:
                Log.w(TAG, "Start play" + player.isStarted() + " " + player.isPaused() + " " + player.isStopped());
                if (player.isPaused() || player.isStopped()) {
                    player.setStart();

                } else {
                    if (player.isStarted()) {
                        player.setPause();
                    }
                }
                break;
            case R.id.iv_palyer_sound:
                Log.w(TAG, "test button sound");

                rect = setCoordinatesView(ll_palyer_sound);
                absParams = (AbsoluteLayout.LayoutParams) vsb_volume.getLayoutParams();
                absParams.x = rect.right - vsb_volume.getWidth();
                absParams.y = rect.top - vsb_volume.getHeight();
                Log.w(TAG, "Width: " + vsb_volume.getWidth() + " Height: " + vsb_volume.getHeight());
                vsb_volume.setLayoutParams(absParams);
                if (vsb_volume.getVisibility() == View.VISIBLE) {
                    vsb_volume.setVisibility(View.GONE);
                    ll_palyer_sound.setBackgroundColor(Color.argb(0, 06, 06, 06));
                } else {
                    vsb_volume.setVisibility(View.VISIBLE);
                    ll_palyer_sound.setBackgroundResource(R.drawable.volume_background);
                }
                break;

            case R.id.rate:
                Log.w(TAG, "test button rate");

                rect = setCoordinatesView(rate);
                absParams = (AbsoluteLayout.LayoutParams) rates.getLayoutParams();
                absParams.x = rect.right - rates.getWidth();
                absParams.y = rect.top - rates.getHeight();
                Log.w(TAG, "Width: " + rates.getWidth() + " Height: " + rates.getHeight());
                rates.setLayoutParams(absParams);
                if (rates.getVisibility() == View.VISIBLE) {
                    rates.setVisibility(View.GONE);
                } else {
                    rates.setVisibility(View.VISIBLE);
                }
                break;
            case R.id.tv_done:
                activity.finish();
                break;
            default:
                break;
        }
    }

    public void autoStart() {
        player.setStart();
    }

    private Rect setCoordinatesView(View view) {
        int[] loc = new int[2];
        Rect rect = new Rect();

        view.getLocationInWindow(loc);
        rect.left = loc[0];
        rect.top = loc[1];
        rect.right = loc[0] + view.getWidth();
        rect.bottom = loc[1] + view.getHeight();

        Log.w(TAG, "Left: " + rect.left + " Top: " + rect.top + " Right: " + rect.right + " Bottom: " + rect.bottom);
        return rect;
    }

    /**
     * Releases resources associated with this MediaPlayer object. It is
     * considered good practice to call this method when you're done using the
     * MediaPlayer. In particular, whenever an Activity of an application is
     * paused (its onPause() method is called), or stopped (its onStop() method
     * is called), this method should be invoked to release the MediaPlayer
     * object, unless the application has a special need to keep the object
     * around. In addition to unnecessary resources (such as memory and
     * instances of codecs) being held, failure to call this method immediately
     * if a MediaPlayer object is no longer needed may also lead to continuous
     * battery consumption for mobile devices, and playback failure for other
     * applications if no multiple instances of the same codec are supported on
     * a device. Even if multiple instances of the same codec are supported,
     * some performance degradation may be expected when unnecessary multiple
     * instances are used at the same time.
     */
    public void setRelease() {
        player.setRelease();
    }

    public void selectBitrate(int position, int state) {
    	KalturaFlavorAsset flavor = boxAdapterRate.getFlavor(position);
        flavorId = flavor.id;
        Log.w(TAG, flavorId);
        tv_rate.setText(boxAdapterRate.getFlavorBitrate(position));
        rates.setVisibility(View.INVISIBLE);
        vsb_volume.setVisibility(View.INVISIBLE);
        this.mediaSizeKb = boxAdapterRate.getFlavorSizeKb(position);
        Log.w(TAG, "size(KB): " + this.mediaSizeKb);

        String url;
        String host = (AdminUser.cdnHost != null ) ? AdminUser.cdnHost : AdminUser.host;
        String appName64 = new String(Base64.encodeBase64(activity.getString(R.string.app_name).getBytes()));
        Log.w(TAG, "versionName: " + VERSION.SDK_INT);
        if (flavor instanceof KalturaWidevineFlavorAsset) {
        	WidevineHandler wvHandler = new WidevineHandler(activity, partnerId, entryId, flavorId);
        	url = wvHandler.url;
        }
        else {
        	if (VERSION.SDK_INT < 13) {
        		 url = "/p/" + partnerId + "/sp/" + partnerId + "00/playManifest/entryId/" + entryId + "/flavorId/" + flavorId + "/format/url/protocol/http/a.mp4";
            } else {
                url = createLinkForM3u8(partnerId , entryId, boxAdapterRate.getListFlavors());
            }
            url = host + url + "?ks=" + AdminUser.ks + "&referrer=" + appName64;
        }
           
        player.setUrl(url);
        if (state != -1) {
            player.selectBitrate();
        }
    }

    private String createLinkForM3u8(int pid, String entryId, String flavorIds) {
        String url = null;
        url = "/p/" + pid + "/sp/" + pid + "00/playManifest/entryId/" + entryId + "/flavorIds/" + flavorIds + "/format/applehttp/protocol/http/a.m3u8";
        Log.w(TAG, "url: " + url);
        return url;
    }

    private class PlayProgressUpdater extends AsyncTask<Void, Void, Void> {

        @Override
        protected Void doInBackground(Void... params) {

            while (!player.isReleased()) {
                publishProgress();
                try {
                    Thread.sleep(1000);
                } catch (InterruptedException e) {
                    e.printStackTrace();
                }
            }
            Log.w(TAG, "Thread is end");
            return null;
        }

        @Override
        protected void onPostExecute(Void param) {
        }

        @Override
        protected void onProgressUpdate(Void... params) {
            try {
                sb_player_duration.setProgress(player.getCurrentPosition() / 1000 * 100 / duration);
                tv_player_position.setText(Utils.durationInSecondsToString(player.getCurrentPosition() / 1000));
            } catch (ArithmeticException e) {
                e.printStackTrace();
                Log.w(TAG, "err : arithmetic exception", e);
            } catch (IllegalStateException e1) {
                e1.printStackTrace();
                Log.w(TAG, "err: illegalState exception", e1);
            }
        }
    }

    /**
     * Set the status control panel
     *
     * @param VISIBLE, INVISIBLE
     */
    public void setStatePanel(int state) {
        Log.w(TAG, "set state: " + state);
        tv_done.setVisibility(state);
        if (state == View.GONE) {
            rates.setVisibility(View.GONE);
            vsb_volume.setVisibility(View.GONE);
            ll_palyer_sound.setBackgroundColor(Color.argb(0, 06, 06, 06));
        }
        panel_player.setVisibility(state);
    }

    /**
     * Get state control panel
     *
     * @return VISIBLE, GONE
     */
    public int getStatePanel() {
        return panel_player.getVisibility();
    }

    /**
     * Hides the panel after 7 seconds
     */
    public void hidePanel() {
        if (!isRun) {
            new ProcessTask().execute();
        } else {
            Log.w(TAG, "Thread already run!");
        }
    }

    private class ProcessTask extends AsyncTask<Void, Void, Void> {

        private String message;

        @Override
        protected Void doInBackground(Void... params) {
            try {
                Log.w(TAG, "begin: ");
                isRun = true;
                Thread.sleep(7000);
                Log.w(TAG, "end: ");
            } catch (InterruptedException e) {
                e.printStackTrace();
            }
            Log.w(TAG, "end thread!");
            return null;
        }

        @Override
        protected void onPostExecute(Void param) {
            isRun = false;
            setStatePanel(View.GONE);
        }

        @Override
        protected void onProgressUpdate(Void... params) {
        }
    }
}