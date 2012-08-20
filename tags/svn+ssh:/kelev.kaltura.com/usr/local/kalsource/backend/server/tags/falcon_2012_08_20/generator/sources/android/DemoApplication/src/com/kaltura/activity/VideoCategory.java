package com.kaltura.activity;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Collections;
import java.util.Date;
import java.util.HashMap;
import java.util.List;
import java.util.Observable;
import java.util.Observer;
import java.util.logging.Level;
import java.util.logging.Logger;

import android.app.Activity;
import android.content.pm.ActivityInfo;
import android.content.res.Configuration;
import android.graphics.Bitmap;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.KeyEvent;
import android.view.MotionEvent;
import android.view.View;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.kaltura.bar.ActionBar;
import com.kaltura.boxAdapter.BoxAdapterAllEntries;
import com.kaltura.client.enums.KalturaMediaType;
import com.kaltura.client.types.KalturaMediaEntry;
import com.kaltura.client.types.KalturaMediaEntryFilter;
import com.kaltura.components.GridForLand;
import com.kaltura.components.GridForPort;
import com.kaltura.enums.States;
import com.kaltura.mediatorActivity.TemplateActivity;
import com.kaltura.services.Media;
import com.kaltura.utils.SearchTextEntry;
import com.kaltura.utils.Sort;
import com.kaltura.utils.Utils;
import com.nostra13.universalimageloader.core.DisplayImageOptions;
import com.nostra13.universalimageloader.core.ImageLoader;
import com.nostra13.universalimageloader.core.ImageLoaderConfiguration;
import com.nostra13.universalimageloader.core.ImageLoadingListener;

public class VideoCategory extends TemplateActivity implements Observer {

    private List<KalturaMediaEntry> listEntries;
    private List<KalturaMediaEntry> copyEntries;
    private BoxAdapterAllEntries gridAllEntries;
    private int categoryId;
    private String categoryName;
    private EditText etSearch;
    private SearchTextEntry searchText;
    private HashMap<KalturaMediaEntry, Bitmap> listBitmap;
    private KalturaMediaEntry lastCreatedEntry;
    private RelativeLayout rl_category;
    private DownloadListCatigoriesTask downloadTask;
    private View search;
    private int width;
    private int height;
    private int sizeListentry;
    private Activity activity;
    private LinearLayout ll_base;
    private List<GridForPort> contentPort;
    private List<GridForLand> contentLand;
    private ProgressBar pb_loading;
    private boolean isFinish = true;
    private int orientation;
    private View itemTopRight;
    private KalturaMediaEntry rightTopEntry;
    private Bitmap rightTopBimap;
    private boolean listCategoriesIsLoaded = false;
    private List<ImageView> view;
    private List<ProgressBar> progressBar;
    private int count = 0;
    int k = 0;

    public VideoCategory() {
        listEntries = new ArrayList<KalturaMediaEntry>();
        copyEntries = new ArrayList<KalturaMediaEntry>();
        downloadTask = new DownloadListCatigoriesTask();
        listBitmap = new HashMap<KalturaMediaEntry, Bitmap>();
        lastCreatedEntry = new KalturaMediaEntry();
        searchText = new SearchTextEntry();
        searchText.addObserver(this);
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        init();
        setContentView(R.layout.category);

        Configuration c = getResources().getConfiguration();
        orientation = c.orientation;

        setView();
        activity = this;
        extractBundle();
 
        rl_category.setVisibility(View.INVISIBLE);
        if (bar != null) {
            bar.setTitle(categoryName);
            bar.setVisibleSearchButon(View.GONE);
            bar.setVisibleBackButton(View.VISIBLE);
            bar.setVisibleImageMovie(View.VISIBLE);
            bar.setVisibleNameCategory(View.GONE);
        }
        search.setVisibility(View.VISIBLE);
        //setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_LANDSCAPE);
        switch (orientation) {
            case Configuration.ORIENTATION_PORTRAIT:
                // setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_PORTRAIT);
                search.setVisibility(View.VISIBLE);
                if (bar != null) {
                    bar.setVisibleSearchButon(View.GONE);
                    bar.setVisibleBackButton(View.VISIBLE);
                    bar.setVisibleImageMovie(View.VISIBLE);
                    bar.setVisibleNameCategory(View.GONE);
                }
                width = display.getWidth() / 2;
                height = display.getWidth() / 2;
                downloadTask.execute();


                break;
            case Configuration.ORIENTATION_LANDSCAPE:
                // setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_LANDSCAPE);
                search.setVisibility(View.GONE);
                if (bar != null) {
                    bar.setVisibleSearchButon(View.VISIBLE);
                    bar.setVisibleBackButton(View.VISIBLE);
                    bar.setVisibleImageMovie(View.VISIBLE);
                    bar.setVisibleNameCategory(View.GONE);
                }
                width = display.getHeight() / 2;
                height = display.getHeight() / 2;
                downloadTask.execute();

                break;
            default:
                break;
        }

        Log.w(TAG, "create orien3: " + getScreenOrientation() + " " + orientation);
    }

    private void setView() {
        rl_category = (RelativeLayout) findViewById(R.id.rl_category);
        etSearch = (EditText) findViewById(R.id.et_search);
        bar = new ActionBar(this, TAG);
        search = findViewById(R.id.search);
        pb_loading = (ProgressBar) findViewById(R.id.pb_loading);
        ll_base = (LinearLayout) findViewById(R.id.ll_base);
        itemTopRight = (View) findViewById(R.id.right_top_item);
    }

    private void extractBundle() {
        try {
            Bundle extras = getIntent().getExtras();
            categoryId = extras.getInt("categoryId");
            categoryName = extras.getString("categoryName");
        } catch (Exception e) {
            e.printStackTrace();
            categoryId = 0;
            categoryName = "";
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
                if (orientation != Configuration.ORIENTATION_LANDSCAPE) {
                    //Hide the keyboard on the screen of a finger        	
                    //imm.hideSoftInputFromWindow(getWindow().getCurrentFocus().getWindowToken(), 0);
                }
                break;
        }
        return super.dispatchTouchEvent(ev);
    }

    @Override
    public void onConfigurationChanged(Configuration newConfig) {
        //setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_UNSPECIFIED);
        super.onConfigurationChanged(newConfig);
        setContentView(R.layout.category);
        this.orientation = newConfig.orientation;


        setView();
        bar.setTitle(categoryName);
        rl_category.setVisibility(View.INVISIBLE);
        if (listCategoriesIsLoaded) {
            searchText.init(TAG, etSearch, listEntries);
            etSearch.addTextChangedListener(searchText);
            updateData(listEntries);
        }

        switch (orientation) {
            case Configuration.ORIENTATION_PORTRAIT:
                search.setVisibility(View.VISIBLE);
                if (bar != null) {
                    bar.setVisibleSearchButon(View.GONE);
                    bar.setVisibleBackButton(View.VISIBLE);
                    bar.setVisibleImageMovie(View.VISIBLE);
                    bar.setVisibleNameCategory(View.GONE);
                }
                width = display.getWidth() / 2;
                height = display.getWidth() / 2;

                break;
            case Configuration.ORIENTATION_LANDSCAPE:
                search.setVisibility(View.GONE);
                if (bar != null) {
                    bar.setVisibleSearchButon(View.VISIBLE);
                    bar.setVisibleBackButton(View.VISIBLE);
                    bar.setVisibleImageMovie(View.VISIBLE);
                    bar.setVisibleNameCategory(View.GONE);
                }
                width = display.getHeight() / 2;
                height = display.getHeight() / 2;
                break;
            default:
                break;
        }
        Log.w(TAG, "oon change rien: " + getScreenOrientation() + " " + newConfig.orientation);
    }

    @Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
        switch (keyCode) {
            case KeyEvent.KEYCODE_MENU:
                getActivityMediator().showMain();
                isFinish = true;
                finish();
                break;
            case KeyEvent.KEYCODE_BACK:
                Log.w(TAG, "Click on Back button");
                finish();
            default:
                break;
        }
        return super.onKeyDown(keyCode, event);
    }

    public void onClick(View v) {
        switch (v.getId()) {
            case R.id.iv_bar_menu:
                getActivityMediator().showMain();
                try {
                    finalize();
                } catch (Throwable ex) {
                    Logger.getLogger(MostPopular.class.getName()).log(Level.SEVERE, null, ex);
                }
                finish();
                break;
            case R.id.iv_bar_search:
                if (search.getVisibility() == View.GONE) {
                    search.setVisibility(View.VISIBLE);
                } else {
                    search.setVisibility(View.GONE);
                }
                break;
            case R.id.iv_thumbnail:
            case R.id.right_top_item:
                getActivityMediator().showInfo(lastCreatedEntry.id, categoryName);
                break;
            case R.id.rl_button_back:
                try {
                    finalize();
                } catch (Throwable ex) {
                    Logger.getLogger(MostPopular.class.getName()).log(Level.SEVERE, null, ex);
                }
                finish();
                break;
            default:
                break;
        }
    }

    private class DownloadListCatigoriesTask extends AsyncTask<Void, States, List<KalturaMediaEntry>> {

        private String message;

        @Override
        protected List<KalturaMediaEntry> doInBackground(Void... params) {
            // Test for connection
            try {
                if (Utils.checkInternetConnection(getApplicationContext())) {
                    /**
                     * Getting list of all entries category
                     */
                    publishProgress(States.LOADING_DATA);
                    /**
                     * Getting list of all entries category
                     */
                    KalturaMediaEntryFilter filter = new KalturaMediaEntryFilter();
                    filter.mediaTypeEqual = KalturaMediaType.VIDEO;
                    filter.categoriesIdsMatchAnd = new Integer(categoryId).toString();
                    listEntries = Media.listAllEntriesByIdCategories(TAG, filter, 1, 500);
                }
                listCategoriesIsLoaded = true;
            } catch (Exception e) {
                e.printStackTrace();
                message = e.getMessage();
                Log.w(TAG, message);
                publishProgress(States.NO_CONNECTION);
            }
            return listEntries;
        }

        @Override
        protected void onPostExecute(List<KalturaMediaEntry> listCategory) {
            progressDialog.hide();
            //setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_UNSPECIFIED);
            if (listEntries.size() != 0) {
                searchText.init(TAG, etSearch, listEntries);
                etSearch.addTextChangedListener(searchText);
                updateData(listEntries);

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
            }
        }
    }

    private void ImageLoader(KalturaMediaEntry lastCreatedEntry) {
        Log.w(TAG, "Start image loader");
        float scale = (float) display.getWidth() / (float) display.getHeight();
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

        final List<String> url = new ArrayList<String>();
        view = new ArrayList<ImageView>();
        progressBar = new ArrayList<ProgressBar>();
        url.add(lastCreatedEntry.thumbnailUrl + "/width/" + new Integer(display.getWidth()).toString() + "/height/" + 
		new Integer(250).toString());
        ImageView thumb = ((ImageView) findViewById(R.id.iv_thumbnail));
        thumb.getLayoutParams().width = display.getWidth();
        if (orientation == Configuration.ORIENTATION_PORTRAIT) {
            thumb.getLayoutParams().height = (int) (display.getWidth() * scale);
        } else {
            thumb.getLayoutParams().height = display.getHeight() - 200;
        }
        thumb.setScaleType(ImageView.ScaleType.CENTER_CROP);
        view.add(thumb);
        progressBar.add(pb_loading);

        if (orientation == Configuration.ORIENTATION_LANDSCAPE && rightTopEntry != null) {
            url.add(rightTopEntry.thumbnailUrl + "/width/" + new Integer(250/*
                     * display.getWidth()
                     */).toString() + "/height/" + new Integer(250/*
                     * display.getHeight()/2
                     */).toString());
            Log.w(TAG, "set last bitmap");
            thumb = ((ImageView) itemTopRight.findViewById(R.id.iv_thumbnail));
            thumb.getLayoutParams().width = display.getWidth();
            if (orientation == Configuration.ORIENTATION_PORTRAIT) {
                thumb.getLayoutParams().height = (int) (display.getWidth() * scale);
            } else {
                thumb.getLayoutParams().height = display.getHeight() - 200;
            }
            thumb.setScaleType(ImageView.ScaleType.CENTER_CROP);
            view.add(thumb);
            progressBar.add(pb_loading);
            thumb.setOnClickListener(new View.OnClickListener() {

                public void onClick(View view) {
                    Log.w(TAG, "click on thumb");
                    getActivityMediator().showInfo(rightTopEntry.id, categoryName);
                }
            });
        }

        for (KalturaMediaEntry entry : copyEntries) {
            url.add(entry.thumbnailUrl + "/width/" + new Integer(250/*
                     * display.getWidth()
                     */).toString() + "/height/" + new Integer(250/*
                     * display.getHeight()/2
                     */).toString());
        }
        count = 0;
        for (String string : url) {
            Log.w(TAG, "url: " + count++ + " " + string);
        }

        int state = 0;
        count = 0;
        for (int j = 0; j < copyEntries.size(); j++) {

            switch (orientation) {
                case Configuration.ORIENTATION_PORTRAIT:
                    switch (state) {
                        case 0:
                            //left
                            Log.w(TAG, "xl: " + count);
                            view.add(contentPort.get(count).getLeftItemGrid().getThumb());
                            progressBar.add(contentPort.get(count).getLeftItemGrid().getProgressBar());//.setVisibility(View.GONE);
                            state = 2;
                            break;
                        case 2:
                            //right
                            Log.w(TAG, "xr: " + count);
                            view.add(contentPort.get(count).getRightItemGrid().getThumb());
                            progressBar.add(contentPort.get(count).getRightItemGrid().getProgressBar());
                            count++;
                            state = 0;
                            break;
                    }
                    break;
                case Configuration.ORIENTATION_LANDSCAPE:
                    switch (state) {
                        case 0:
                            //left
                            Log.w(TAG, "xl: " + count);
                            view.add(contentLand.get(count).getLeftItemGrid().getThumb());
                            progressBar.add(contentLand.get(count).getLeftItemGrid().getProgressBar());
                            state = 1;
                            break;
                        case 1:
                            //center
                            Log.w(TAG, "xc: " + count);
                            view.add(contentLand.get(count).getCenterItemGrid().getThumb());
                            progressBar.add(contentLand.get(count).getCenterItemGrid().getProgressBar());
                            state = 2;
                            break;
                        case 2:
                            //right
                            Log.w(TAG, "xr: " + count);
                            view.add(contentLand.get(count).getRightItemGrid().getThumb());
                            progressBar.add(contentLand.get(count).getRightItemGrid().getProgressBar());
                            count++;
                            state = 0;
                            break;
                    }
                    break;
                default:
                    break;
            }
        }

        count = 0;
        Log.w(TAG, "size: " + progressBar.size());
        k = 0;
        imageLoader.enableLogging();
        for (String string : url) {
            imageLoader.displayImage(string, view.get(count), options, new ImageLoadingListener() {

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
                    if (k < progressBar.size()) {
                        try {
                            progressBar.get(k++).setVisibility(View.GONE);
                        } catch (Exception e) {
                            e.printStackTrace();
                            Log.w(TAG, "err: ", e);
                        }
                    }
                    if (k >= url.size()) {
                        setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_UNSPECIFIED);
                    }
                    Log.w(TAG, "onLoadingComplete: " + k);

                }
            });
            count++;
        }


    }

    private void updateData(List<KalturaMediaEntry> listEntries) {
        copyEntries = new ArrayList<KalturaMediaEntry>();
        copyEntries.addAll(listEntries);


        if (copyEntries.size() > 0) {
            sizeListentry = copyEntries.size();
            Collections.sort(copyEntries, new Sort<KalturaMediaEntry>("createdAt", "compareTo"));
            lastCreatedEntry = copyEntries.get(copyEntries.size() - 1);
            copyEntries.remove(lastCreatedEntry);
            Collections.sort(copyEntries, new Sort<KalturaMediaEntry>("name", "compareTo"));
            addContentLastEntry();
        } else {
            rl_category.setVisibility(View.GONE);
        }

        switch (orientation) {
            case Configuration.ORIENTATION_PORTRAIT:
                Log.w(TAG, "start in port");
                setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_PORTRAIT);
                createGridForPort();
                if (listEntries.size() > 0) {
                    ImageLoader(lastCreatedEntry);
                }
                break;
            case Configuration.ORIENTATION_LANDSCAPE:
                setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_LANDSCAPE);
                if (listEntries.size() > 1) {
                    itemTopRight.setVisibility(View.VISIBLE);
                    rightTopEntry = copyEntries.get(0);
                    ((TextView) itemTopRight.findViewById(R.id.tv_name)).setText(rightTopEntry.name);
                    ((TextView) itemTopRight.findViewById(R.id.tv_episode)).setText(Utils.durationInSecondsToString(rightTopEntry.duration));
                    copyEntries.remove(0);
                } else {
                    itemTopRight.setVisibility(View.GONE);
                }
                createGridForLand();
                Log.w(TAG, "start in land");
                if (listEntries.size() > 0) {
                    ImageLoader(lastCreatedEntry);
                }
                break;
            default:
                break;
        }


    }

    private void createGridForPort() {
        Log.w(TAG, "grid for port");
        ll_base.removeAllViewsInLayout();
        if (copyEntries.size() > 0) {
            contentPort = new ArrayList<GridForPort>();
            Log.w(TAG, "size: " + copyEntries.size());
            int countConent = copyEntries.size() / 2;
            Log.w(TAG, "countConent: " + countConent);
            int rest = copyEntries.size() % 2;
            Log.w(TAG, "rest: " + rest);

            if (rest != 0) {
                countConent = countConent + 1;
                Log.w(TAG, "countConent: " + countConent);
            }

            //Create countContent contents
            int offset = 0;
            int size = copyEntries.size();

            if (size > 2) {
                Log.w(TAG, "1 offset: " + offset);
                contentPort.add(new GridForPort(TAG, this, offset));
                size = size - 2;
                for (int i = 0; i < countConent - 1; i++) {
                    offset = offset + 2;
                    size = size - 2;
                    Log.w(TAG, "3 offset: " + offset);
                    contentPort.add(new GridForPort(TAG, this, offset));
                }
            } else {
                Log.w(TAG, "2 offset: " + 0);
                contentPort.add(new GridForPort(TAG, this, 0));
            }

            float scale = (float) display.getWidth() / (float) display.getHeight();
            //set params
            for (final GridForPort templateContent : contentPort) {
                ll_base.addView(templateContent.getRowGrid());

                if (templateContent.getOffset() + 0 < copyEntries.size()) {
                    templateContent.getLeftItemGrid().getName().setText(copyEntries.get(templateContent.getOffset() + 0).name);
                    templateContent.getLeftItemGrid().getEpisode().setText(Utils.durationInSecondsToString(copyEntries.get(templateContent.getOffset() + 0).duration));
                    templateContent.getLeftItemGrid().getThumb().getLayoutParams().width = display.getWidth() / 2;
                    templateContent.getLeftItemGrid().getThumb().getLayoutParams().height = (int) (display.getWidth() / 2 * scale);
                    templateContent.getLeftItemGrid().getThumb().setScaleType(ImageView.ScaleType.CENTER_CROP);
                    templateContent.getLeftItemGrid().setKey(copyEntries.get(templateContent.getOffset() + 0));
                } else {
                    Log.w(TAG, "no right element");
                }
                if (templateContent.getOffset() + 1 < copyEntries.size()) {
                    templateContent.getRightItemGrid().getName().setText(copyEntries.get(templateContent.getOffset() + 1).name);
                    templateContent.getRightItemGrid().getEpisode().setText(Utils.durationInSecondsToString(copyEntries.get(templateContent.getOffset() + 0).duration));
                    templateContent.getRightItemGrid().getThumb().getLayoutParams().width = display.getWidth() / 2;
                    templateContent.getRightItemGrid().getThumb().getLayoutParams().height = (int) (display.getWidth() / 2 * scale);
                    templateContent.getRightItemGrid().getThumb().setScaleType(ImageView.ScaleType.CENTER_CROP);
                    templateContent.getRightItemGrid().setKey(copyEntries.get(templateContent.getOffset() + 1));
                } else {
                    Log.w(TAG, "no right element");
                    templateContent.getRightItemGrid().getThumb().setVisibility(View.INVISIBLE);
                    templateContent.getRightItemGrid().getProgressBar().setVisibility(View.INVISIBLE);
                    templateContent.getRightItemGrid().getName().setVisibility(View.INVISIBLE);
                    templateContent.getRightItemGrid().getEpisode().setVisibility(View.INVISIBLE);
                }

                templateContent.getLeftItemGrid().getThumb().setOnClickListener(new View.OnClickListener() {

                    public void onClick(View view) {

                        if (templateContent.getOffset() + 0 < copyEntries.size()) {
                            getActivityMediator().showInfo(copyEntries.get(templateContent.getOffset() + 0).id, categoryName);
                            Log.w(TAG, "click first" + templateContent.getOffset() + " category name: " + categoryName);
                        }
                    }
                });
                templateContent.getRightItemGrid().getThumb().setOnClickListener(new View.OnClickListener() {

                    public void onClick(View view) {
                        Log.w(TAG, "click second");
                        if (templateContent.getOffset() + 1 < copyEntries.size()) {
                            getActivityMediator().showInfo(copyEntries.get(templateContent.getOffset() + 1).id, categoryName);
                            Log.w(TAG, "click first" + templateContent.getOffset() + 1);
                        }
                    }
                });
            }
        } else {
            Log.w(TAG, "list size is 0");
        }
    }

    private void createGridForLand() {
        Log.w(TAG, "grid for land");
        ll_base.removeAllViewsInLayout();
        if (copyEntries.size() > 0) {
            contentLand = new ArrayList<GridForLand>();
            Log.w(TAG, "size: " + copyEntries.size());
            int countConent = copyEntries.size() / 3;
            Log.w(TAG, "countConent: " + countConent);
            int rest = copyEntries.size() % 3;
            Log.w(TAG, "rest: " + rest);

            if (rest != 0) {
                countConent = countConent + 1;
                Log.w(TAG, "countConent: " + countConent);
            }

            //Create countContent contents
            int offset = 0;
            int size = copyEntries.size();

            if (size > 3) {
                Log.w(TAG, "1 offset: " + offset);
                contentLand.add(new GridForLand(TAG, this, offset));
                size = size - 3;
                for (int i = 0; i < countConent - 1; i++) {
                    offset = offset + 3;
                    size = size - 3;
                    Log.w(TAG, "3 offset: " + offset);
                    contentLand.add(new GridForLand(TAG, this, offset));
                }
            } else {
                Log.w(TAG, "2 offset: " + 0);
                contentLand.add(new GridForLand(TAG, this, 0));
            }

            float scale = (float) display.getHeight() / (float) display.getWidth();
            //set params
            for (final GridForLand templateContent : contentLand) {
                ll_base.addView(templateContent.getRowGrid());

                if (templateContent.getOffset() + 0 < copyEntries.size()) {
                    templateContent.getLeftItemGrid().getName().setText(copyEntries.get(templateContent.getOffset() + 0).name);
                    templateContent.getLeftItemGrid().getEpisode().setText(Utils.durationInSecondsToString(copyEntries.get(templateContent.getOffset() + 0).duration));
                    templateContent.getLeftItemGrid().getThumb().getLayoutParams().width = display.getWidth() / 3;
                    templateContent.getLeftItemGrid().getThumb().getLayoutParams().height = (int) (display.getWidth() / 3 * scale);
                    templateContent.getLeftItemGrid().getThumb().setScaleType(ImageView.ScaleType.CENTER_CROP);
                    templateContent.getLeftItemGrid().setKey(copyEntries.get(templateContent.getOffset() + 0));
                } else {
                    Log.w(TAG, "no left element");
                }

                if (templateContent.getOffset() + 1 < copyEntries.size()) {
                    templateContent.getCenterItemGrid().getName().setText(copyEntries.get(templateContent.getOffset() + 1).name);
                    templateContent.getCenterItemGrid().getEpisode().setText(Utils.durationInSecondsToString(copyEntries.get(templateContent.getOffset() + 0).duration));
                    templateContent.getCenterItemGrid().getThumb().getLayoutParams().width = display.getWidth() / 3;
                    templateContent.getCenterItemGrid().getThumb().getLayoutParams().height = (int) (display.getWidth() / 3 * scale);
                    templateContent.getCenterItemGrid().getThumb().setScaleType(ImageView.ScaleType.CENTER_CROP);
                    templateContent.getCenterItemGrid().setKey(copyEntries.get(templateContent.getOffset() + 1));
                } else {
                    Log.w(TAG, "no center element");
                    templateContent.getCenterItemGrid().getThumb().setVisibility(View.INVISIBLE);
                    templateContent.getCenterItemGrid().getProgressBar().setVisibility(View.INVISIBLE);
                    templateContent.getCenterItemGrid().getName().setVisibility(View.INVISIBLE);
                    templateContent.getCenterItemGrid().getEpisode().setVisibility(View.INVISIBLE);
                }

                if (templateContent.getOffset() + 2 < copyEntries.size()) {
                    templateContent.getRightItemGrid().getName().setText(copyEntries.get(templateContent.getOffset() + 2).name);
                    templateContent.getRightItemGrid().getEpisode().setText(Utils.durationInSecondsToString(copyEntries.get(templateContent.getOffset() + 0).duration));
                    templateContent.getRightItemGrid().getThumb().getLayoutParams().width = display.getWidth() / 3;
                    templateContent.getRightItemGrid().getThumb().getLayoutParams().height = (int) (display.getWidth() / 3 * scale);
                    templateContent.getRightItemGrid().getThumb().setScaleType(ImageView.ScaleType.CENTER_CROP);
                    templateContent.getRightItemGrid().setKey(copyEntries.get(templateContent.getOffset() + 2));
                } else {
                    Log.w(TAG, "no right element");
                    templateContent.getRightItemGrid().getThumb().setVisibility(View.INVISIBLE);
                    templateContent.getRightItemGrid().getProgressBar().setVisibility(View.INVISIBLE);
                    templateContent.getRightItemGrid().getName().setVisibility(View.INVISIBLE);
                    templateContent.getRightItemGrid().getEpisode().setVisibility(View.INVISIBLE);
                }


                templateContent.getLeftItemGrid().getThumb().setOnClickListener(new View.OnClickListener() {

                    public void onClick(View view) {

                        if (templateContent.getOffset() + 0 < copyEntries.size()) {
                            getActivityMediator().showInfo(copyEntries.get(templateContent.getOffset() + 0).id, categoryName);
                            Log.w(TAG, "click first" + templateContent.getOffset());
                        }
                    }
                });
                templateContent.getCenterItemGrid().getThumb().setOnClickListener(new View.OnClickListener() {

                    public void onClick(View view) {

                        if (templateContent.getOffset() + 0 < copyEntries.size()) {
                            getActivityMediator().showInfo(copyEntries.get(templateContent.getOffset() + 0).id, categoryName);
                            Log.w(TAG, "click first" + templateContent.getOffset());
                        }
                    }
                });
                templateContent.getRightItemGrid().getThumb().setOnClickListener(new View.OnClickListener() {

                    public void onClick(View view) {
                        Log.w(TAG, "click second");
                        if (templateContent.getOffset() + 1 < copyEntries.size()) {
                            getActivityMediator().showInfo(copyEntries.get(templateContent.getOffset() + 1).id, categoryName);
                            Log.w(TAG, "click first" + templateContent.getOffset() + 1);
                        }
                    }
                });
            }
        } else {
            Log.w(TAG, "list size is 0");
        }
    }

    private void addContentLastEntry() {

        Log.w(TAG, "sizeListentry: " + sizeListentry);
        if (sizeListentry != 0) {
            rl_category.setVisibility(View.VISIBLE);
            try {
                ((TextView) findViewById(R.id.tv_name)).setText(lastCreatedEntry.name);
                ((TextView) findViewById(R.id.tv_episode)).setText("");
                SimpleDateFormat sdf = new SimpleDateFormat("mm:ss");
                ((TextView) findViewById(R.id.tv_duration)).setText(sdf.format(new Date(lastCreatedEntry.duration * 1000)));
            } catch (Exception e) {
                e.printStackTrace();
                Log.w(TAG, "err: " + e.getMessage());
            }
        }
    }

    @Override
    public void update(Observable paramObservable, Object paramObject) {
        updateData((List<KalturaMediaEntry>) paramObject);
    }
}
