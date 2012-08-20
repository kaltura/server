package com.kaltura.activity;

import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.ArrayList;
import java.util.Collections;
import java.util.HashMap;
import java.util.List;
import java.util.Observable;
import java.util.Observer;

import android.app.Activity;
import android.app.Dialog;
import android.content.pm.ActivityInfo;
import android.content.res.Configuration;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Color;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.KeyEvent;
import android.view.MotionEvent;
import android.view.View;
import android.view.Window;
import android.widget.AdapterView;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import com.kaltura.bar.ActionBar;
import com.kaltura.boxAdapter.BoxAdapterAllCategories;
import com.kaltura.client.enums.KalturaMediaType;
import com.kaltura.client.types.KalturaCategory;
import com.kaltura.client.types.KalturaMediaEntry;
import com.kaltura.client.types.KalturaMediaEntryFilter;
import com.kaltura.components.GridForLandLargeScreen;
import com.kaltura.components.GridForPortLargeScreen;
import com.kaltura.enums.States;
import com.kaltura.mediatorActivity.TemplateActivity;
import com.kaltura.services.Category;
import com.kaltura.services.Media;
import com.kaltura.sharing.Sharing;
import com.kaltura.utils.SearchTextCategory;
import com.kaltura.utils.Sort;
import com.kaltura.utils.Utils;
import com.nostra13.universalimageloader.core.DisplayImageOptions;
import com.nostra13.universalimageloader.core.ImageLoader;
import com.nostra13.universalimageloader.core.ImageLoaderConfiguration;
import com.nostra13.universalimageloader.core.ImageLoadingListener;

public class VideoCategories extends TemplateActivity implements Observer, ListView.OnItemClickListener {

    private ListView lvAllCategories;
    private List<KalturaCategory> listCategory;
    private BoxAdapterAllCategories listAllCategories;
    private EditText etSearch;
    private SearchTextCategory searchText;
    private View search;
    private TextView tv_bar;
    private LinearLayout ll_categories;
    private int categoryId;
    private List<KalturaMediaEntry> listEntries;
    private HashMap<KalturaMediaEntry, Bitmap> listBitmap;
    private int textColor;
    private int arrow;
    private List<GridForPortLargeScreen> contentPort;
    private List<GridForLandLargeScreen> contentLand;
    private LinearLayout ll_conent;
    private String categoryName = "";
    private int orientation;
    private boolean largeScreen;
    private static int save = -1;
    private boolean isSet = false;
    private List<KalturaMediaEntry> listMostPopular;
    private boolean listCategoriesIsLoaded = false;
    private boolean isMostPopular = false;
    private Activity activity;
    private List<ImageView> view;
    private List<ProgressBar> progressBar;
    private int count = 0;
    int k = 0;
    private int width;
    private int height;
    private static int position = 0;
    private int transparentColor = 255;
    private boolean visibleHightLight;
    private Sharing sharing;
    private Dialog dialogUpload;
    private String url;
    private static int countShowDialog = 0;

    public VideoCategories() {
        listMostPopular = new ArrayList<KalturaMediaEntry>();
        searchText = new SearchTextCategory();
        searchText.addObserver(this);
        listCategory = new ArrayList<KalturaCategory>();
        listEntries = new ArrayList<KalturaMediaEntry>();
        listBitmap = new HashMap<KalturaMediaEntry, Bitmap>();
        contentPort = new ArrayList<GridForPortLargeScreen>();
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        init();
        // setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_UNSPECIFIED);
        setContentView(R.layout.categories);

        this.activity = this;
        //this.position = 0;
        Configuration c = getResources().getConfiguration();
        orientation = c.orientation;
        extractBundle();
        countShowDialog = 0;
        Log.w(TAG, "Position: " + position);

        switch (determineScreenSize()) {
            case Configuration.SCREENLAYOUT_SIZE_NORMAL:
            case Configuration.SCREENLAYOUT_SIZE_SMALL:
                Log.w(TAG, "not large");
                textColor = Color.BLACK;
                arrow = R.drawable.arrow;
                transparentColor = 0;
                visibleHightLight = false;
                setViewOtherScreens();
                if (bar != null) {
                    bar.setTitle(getText(R.string.action_bar_catigories));
                    bar.setVisibleBackButton(View.INVISIBLE);
                }
                sharing = new Sharing(this);
                switch (orientation) {
                    case Configuration.ORIENTATION_PORTRAIT:
                    case Configuration.ORIENTATION_UNDEFINED:
                    case Configuration.ORIENTATION_SQUARE:
                        //setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_PORTRAIT);
                        if (bar != null) {
                            bar.setVisibleSearchButon(View.INVISIBLE);
                        }
                        new DownloadListCatigoriesTask().execute();
                        break;
                    case Configuration.ORIENTATION_LANDSCAPE:
                        search.setVisibility(View.GONE);
                        if (bar != null) {
                            bar.setVisibleSearchButon(View.VISIBLE);
                        }
                        // setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_LANDSCAPE);
                        new DownloadListCatigoriesTask().execute();
                        break;
                    default:
                        break;
                }
                break;
            case Configuration.SCREENLAYOUT_SIZE_LARGE:
                Log.w(TAG, "large");
                textColor = Color.WHITE;
                arrow = R.drawable.arrow_white;
                transparentColor = 0;
                visibleHightLight = true;
                //Large Screen
                setViewLargeScreen();
                width = display.getHeight() / 2;
                height = display.getHeight() / 2;
                sharing = new Sharing(this);
                switch (orientation) {
                    case Configuration.ORIENTATION_PORTRAIT:
                    case Configuration.ORIENTATION_UNDEFINED:
                    case Configuration.ORIENTATION_SQUARE:
                        setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_PORTRAIT);
                        if (tv_bar != null) {
                            tv_bar.setText(getText(R.string.action_bar_catigories));
                        }
                        if (bar != null) {
                            bar.setTitle("");
                            bar.setVisibleBackButton(View.VISIBLE);
                            bar.setBackgroundBackButton(R.drawable.button_back_large_screen_selector);
                        }
                        new DownloadListCatigoriesTask().execute();
                        break;
                    case Configuration.ORIENTATION_LANDSCAPE:
                        setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_LANDSCAPE);

                        if (bar != null) {
                            bar.setTitle(getText(R.string.action_bar_catigories));
                            bar.setVisibleBackButton(View.INVISIBLE);
                            bar.setBackgroundBackButton(R.drawable.button_back_large_screen_selector);
                            bar.setVisibleSearchButon(View.GONE);
                        }
                        new DownloadListCatigoriesTask().execute();
                        break;
                    default:
                        break;
                }
                break;

            default:
                Log.w(TAG, "Undefined screen: ");
                Log.w(TAG, "width: " + display.getWidth() + " height: " + display.getHeight());
                textColor = Color.WHITE;
                arrow = R.drawable.arrow_white;
                transparentColor = 0;
                visibleHightLight = true;
                //Large Screen
                setViewLargeScreen();
                width = display.getHeight() / 2;
                height = display.getHeight() / 2;
                sharing = new Sharing(this);
                switch (orientation) {
                    case Configuration.ORIENTATION_PORTRAIT:
                    case Configuration.ORIENTATION_UNDEFINED:
                    case Configuration.ORIENTATION_SQUARE:
                        // setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_PORTRAIT);
                        if (tv_bar != null) {
                            tv_bar.setText(getText(R.string.action_bar_catigories));
                        }
                        if (bar != null) {
                            bar.setTitle("");
                        }
                        bar.setVisibleSearchButon(View.INVISIBLE);
                        bar.setBackgroundBackButton(R.drawable.button_back_large_screen_selector);
                        new DownloadListCatigoriesTask().execute();
                        break;
                    case Configuration.ORIENTATION_LANDSCAPE:
                        // setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_LANDSCAPE);
                        if (bar != null) {
                            bar.setTitle(getText(R.string.action_bar_catigories));
                            bar.setVisibleSearchButon(View.VISIBLE);
                            bar.setBackgroundBackButton(R.drawable.button_back_large_screen_selector);
                            bar.setVisibleBackButton(View.INVISIBLE);
                            bar.setVisibleSearchButon(View.GONE);
                        }
                        //GridViewForLand();
                        new DownloadListCatigoriesTask().execute();
                        break;
                    default:
                        break;
                }
                break;
        }
    }

    private void extractBundle() {
        try {
            Bundle extras = getIntent().getExtras();
            largeScreen = extras.getBoolean("largeScreen");
            Log.w(TAG, "extracted data: ");
            Log.w(TAG, "largeScreen: " + largeScreen);
        } catch (Exception e) {
            e.printStackTrace();
            Log.w(TAG, "err: " + e.getMessage());
            largeScreen = false;
        }
    }

    @Override
    public void onRestoreInstanceState(Bundle savedInstanceState) {
        super.onRestoreInstanceState(savedInstanceState);



    }

    @Override
    public void onStart() {
        super.onStart();
        switch (determineScreenSize()) {
            case Configuration.SCREENLAYOUT_SIZE_NORMAL:
            case Configuration.SCREENLAYOUT_SIZE_SMALL:
                break;
            case Configuration.SCREENLAYOUT_SIZE_LARGE:
                sharing.addListener();
                switch (orientation) {
                    case Configuration.ORIENTATION_PORTRAIT:
                    case Configuration.ORIENTATION_UNDEFINED:
                    case Configuration.ORIENTATION_SQUARE:
                        ll_categories.getLayoutParams().width = display.getWidth();
                        break;
                }
                break;
            default:
                sharing.addListener();
                switch (orientation) {
                    case Configuration.ORIENTATION_PORTRAIT:
                    case Configuration.ORIENTATION_UNDEFINED:
                    case Configuration.ORIENTATION_SQUARE:
                        ll_categories.getLayoutParams().width = display.getWidth() / 2;
                        break;
                }
                break;
        }

    }

    @Override
    protected void onStop() {
        super.onStop();
    }

    @Override
    public void onConfigurationChanged(Configuration newConfig) {
        super.onConfigurationChanged(newConfig);
        setContentView(R.layout.categories);
        this.orientation = newConfig.orientation;

        Log.w(TAG, "Position: " + position);

        Log.w(TAG, "get orientation: " + orientation);
        Log.w(TAG, "screen size: " + determineScreenSize());
        switch (determineScreenSize()) {
            case Configuration.SCREENLAYOUT_SIZE_NORMAL:
            case Configuration.SCREENLAYOUT_SIZE_SMALL:
                textColor = Color.BLACK;
                arrow = R.drawable.arrow;
                setViewOtherScreens();
                addListCategoriesOnScreen();
                if (bar != null) {
                    bar.setTitle(getText(R.string.action_bar_catigories));
                    bar.setVisibleBackButton(View.INVISIBLE);
                }
                switch (orientation) {
                    case Configuration.ORIENTATION_PORTRAIT:
                    case Configuration.ORIENTATION_UNDEFINED:
                    case Configuration.ORIENTATION_SQUARE:
                        search.setVisibility(View.VISIBLE);
                        bar.setVisibleSearchButon(View.INVISIBLE);
                        break;
                    case Configuration.ORIENTATION_LANDSCAPE:
                        search.setVisibility(View.GONE);
                        bar.setVisibleSearchButon(View.VISIBLE);
                        bar.setVisibleBackButton(View.INVISIBLE);
                        break;
                    default:
                        break;
                }
                break;
            case Configuration.SCREENLAYOUT_SIZE_LARGE:
                Log.w(TAG, "width: " + display.getWidth() + " height: " + display.getHeight());
                textColor = Color.WHITE;
                arrow = R.drawable.arrow_white;
                //Large Screen
                addListCategoriesOnScreen();

                save = -1;
                switch (orientation) {
                    case Configuration.ORIENTATION_PORTRAIT:
                    case Configuration.ORIENTATION_UNDEFINED:
                    case Configuration.ORIENTATION_SQUARE:
                        if (tv_bar != null) {
                            tv_bar.setText(getText(R.string.action_bar_catigories));
                        }
                        bar.setTitle("");
                        bar.setBackgroundBackButton(R.drawable.button_back_large_screen_selector);
                        bar.setVisibleBackButton(View.VISIBLE);
                        GridViewForPort();
                        new DownloadListCatigoriesTask().execute();
                        break;
                    case Configuration.ORIENTATION_LANDSCAPE:
                        bar.setTitle(getText(R.string.action_bar_catigories));
                        bar.setVisibleBackButton(View.INVISIBLE);
                        bar.setBackgroundBackButton(R.drawable.button_back_large_screen_selector);
                        bar.setVisibleBackButton(View.INVISIBLE);
                        bar.setVisibleSearchButon(View.GONE);
                        GridViewForLand();
                        new DownloadListCatigoriesTask().execute();
                        break;
                    default:

                        break;
                }
                break;
            default:
                Log.w(TAG, "Undefined screen: ");
                Log.w(TAG, "width: " + display.getWidth() + " height: " + display.getHeight());
                textColor = Color.WHITE;
                arrow = R.drawable.arrow_white;
                //Large Screen
                addListCategoriesOnScreen();

                save = -1;
                switch (orientation) {
                    case Configuration.ORIENTATION_PORTRAIT:
                    case Configuration.ORIENTATION_UNDEFINED:
                    case Configuration.ORIENTATION_SQUARE:
                        if (tv_bar != null) {
                            tv_bar.setText(getText(R.string.action_bar_catigories));
                        }
                        bar.setTitle("");
                        bar.setBackgroundBackButton(R.drawable.button_back_large_screen_selector);
                        bar.setVisibleBackButton(View.VISIBLE);
                        GridViewForPort();
                        new DownloadListCatigoriesTask().execute();
                        break;
                    case Configuration.ORIENTATION_LANDSCAPE:
                        bar.setTitle(getText(R.string.action_bar_catigories));
                        bar.setVisibleBackButton(View.INVISIBLE);
                        bar.setBackgroundBackButton(R.drawable.button_back_large_screen_selector);
                        bar.setVisibleBackButton(View.INVISIBLE);
                        bar.setVisibleSearchButon(View.GONE);
                        GridViewForLand();
                        new DownloadListCatigoriesTask().execute();

                        break;
                    default:
                        break;
                }
                break;
        }
    }

    private void GridViewForPort() {

        if (listEntries.size() > 0) {
            contentPort = new ArrayList<GridForPortLargeScreen>();
            Log.w(TAG, "size: " + listEntries.size());
            int countConent = listEntries.size() / 4;
            Log.w(TAG, "countConent: " + countConent);
            int rest = listEntries.size() % 4;
            Log.w(TAG, "rest: " + rest);

            if (rest != 0) {
                countConent = countConent + 1;
                Log.w(TAG, "countConent: " + countConent);
            }

            //Create countContent contents
            int offset = 0;
            int size = listEntries.size();

            if (size > 4) {
                Log.w(TAG, "1 offset: " + offset);
                contentPort.add(new GridForPortLargeScreen(TAG, this, offset));
                size = size - 4;
                for (int i = 0; i < countConent - 1; i++) {
                    offset = offset + 4;
                    size = size - 4;
                    Log.w(TAG, "3 offset: " + offset);
                    contentPort.add(new GridForPortLargeScreen(TAG, this, offset));
                }
            } else {
                Log.w(TAG, "2 offset: " + 0);
                contentPort.add(new GridForPortLargeScreen(TAG, this, 0));
            }

            ll_conent.removeAllViewsInLayout();
            //set params
            for (final GridForPortLargeScreen templateContent : contentPort) {
                ll_conent.addView(templateContent.getRowGrid());
                templateContent.getRowGrid().getLayoutParams().width = (int) (float) (1.5 * display.getWidth());
                templateContent.getRowGrid().getLayoutParams().height = display.getHeight() / 3;

                if (templateContent.getOffset() + 0 < listEntries.size()) {
                    templateContent.getFirstItemGrid().getName().setText(listEntries.get(templateContent.getOffset() + 0).name);
                    templateContent.getFirstItemGrid().getEpisode().setText(Utils.durationInSecondsToString(listEntries.get(templateContent.getOffset() + 0).duration));
                    templateContent.getFirstItemGrid().getThumb().setScaleType(ImageView.ScaleType.CENTER_CROP);
                    templateContent.getFirstItemGrid().getName().setVisibility(View.VISIBLE);
                    templateContent.getFirstItemGrid().getEpisode().setVisibility(View.VISIBLE);
                    templateContent.getFirstItemGrid().getThumb().setVisibility(View.VISIBLE);
                    templateContent.getFirstItemGrid().getProgressBar().setVisibility(View.VISIBLE);
                } else {
                    Log.w(TAG, "first iteam grid not created");
                }
                if (templateContent.getOffset() + 1 < listEntries.size()) {
                    templateContent.getSecondItemGrid().getName().setText(listEntries.get(templateContent.getOffset() + 1).name);
                    templateContent.getSecondItemGrid().getEpisode().setText(Utils.durationInSecondsToString(listEntries.get(templateContent.getOffset() + 1).duration));
                    templateContent.getSecondItemGrid().getThumb().setScaleType(ImageView.ScaleType.CENTER_CROP);
                    templateContent.getSecondItemGrid().getName().setVisibility(View.VISIBLE);
                    templateContent.getSecondItemGrid().getEpisode().setVisibility(View.VISIBLE);
                    templateContent.getSecondItemGrid().getThumb().setVisibility(View.VISIBLE);
                    templateContent.getSecondItemGrid().getProgressBar().setVisibility(View.VISIBLE);
                } else {
                    Log.w(TAG, "second iteam grid not created");
                }
                if (templateContent.getOffset() + 2 < listEntries.size()) {
                    templateContent.getThirdItemGrid().getName().setText(listEntries.get(templateContent.getOffset() + 2).name);
                    templateContent.getThirdItemGrid().getEpisode().setText(Utils.durationInSecondsToString(listEntries.get(templateContent.getOffset() + 2).duration));
                    templateContent.getThirdItemGrid().getThumb().setScaleType(ImageView.ScaleType.CENTER_CROP);
                    templateContent.getThirdItemGrid().getName().setVisibility(View.VISIBLE);
                    templateContent.getThirdItemGrid().getEpisode().setVisibility(View.VISIBLE);
                    templateContent.getThirdItemGrid().getThumb().setVisibility(View.VISIBLE);
                    templateContent.getThirdItemGrid().getProgressBar().setVisibility(View.VISIBLE);
                } else {
                    Log.w(TAG, "third iteam grid not created");
                }
                if (templateContent.getOffset() + 3 < listEntries.size()) {
                    templateContent.getFourthItemGrid().getName().setText(listEntries.get(templateContent.getOffset() + 3).name);
                    templateContent.getFourthItemGrid().getEpisode().setText(Utils.durationInSecondsToString(listEntries.get(templateContent.getOffset() + 3).duration));
                    templateContent.getFourthItemGrid().getThumb().setScaleType(ImageView.ScaleType.CENTER_CROP);
                    templateContent.getFourthItemGrid().getName().setVisibility(View.VISIBLE);
                    templateContent.getFourthItemGrid().getEpisode().setVisibility(View.VISIBLE);
                    templateContent.getFourthItemGrid().getThumb().setVisibility(View.VISIBLE);
                    templateContent.getFourthItemGrid().getProgressBar().setVisibility(View.VISIBLE);
                } else {
                    Log.w(TAG, "fourth iteam grid not created");
                }

                templateContent.getFirstItemGrid().getThumb().setOnClickListener(new View.OnClickListener() {

                    public void onClick(View view) {

                        if (templateContent.getOffset() + 0 < listEntries.size()) {
                            showUploadDialog(listEntries.get(templateContent.getOffset() + 0));
                            Log.w(TAG, "click first" + templateContent.getOffset());
                        }
                    }
                });
                templateContent.getSecondItemGrid().getThumb().setOnClickListener(new View.OnClickListener() {

                    public void onClick(View view) {
                        Log.w(TAG, "click second");
                        if (templateContent.getOffset() + 1 < listEntries.size()) {
                            showUploadDialog(listEntries.get(templateContent.getOffset() + 1));
                            Log.w(TAG, "click first" + templateContent.getOffset() + 1);
                        }
                    }
                });
                templateContent.getThirdItemGrid().getThumb().setOnClickListener(new View.OnClickListener() {

                    public void onClick(View view) {
                        Log.w(TAG, "click third");
                        if (templateContent.getOffset() + 2 < listEntries.size()) {
                            showUploadDialog(listEntries.get(templateContent.getOffset() + 2));
                            Log.w(TAG, "click first" + templateContent.getOffset() + 2);
                        }
                    }
                });
                templateContent.getFourthItemGrid().getThumb().setOnClickListener(new View.OnClickListener() {

                    public void onClick(View view) {
                        Log.w(TAG, "click forth");
                        if (templateContent.getOffset() + 3 < listEntries.size()) {
                            showUploadDialog(listEntries.get(templateContent.getOffset() + 3));
                            Log.w(TAG, "click first" + templateContent.getOffset() + 3);
                        }
                    }
                });
            }
        } else {
            Log.w(TAG, "list size is 0");
        }
    }

    private void GridViewForLand() {

        if (listEntries.size() > 0) {
            contentLand = new ArrayList<GridForLandLargeScreen>();
            Log.w(TAG, "size: " + listEntries.size());
            int countConent = listEntries.size() / 6;
            Log.w(TAG, "countConent: " + countConent);
            int rest = listEntries.size() % 6;
            Log.w(TAG, "rest: " + rest);

            if (rest != 0) {
                countConent = countConent + 1;
                Log.w(TAG, "countConent: " + countConent);
            }

            //Create countContent contents
            int offset = 0;
            int size = listEntries.size();

            if (size > 6) {
                Log.w(TAG, "1 offset: " + offset);
                contentLand.add(new GridForLandLargeScreen(TAG, this, offset));
                size = size - 6;
                for (int i = 0; i < countConent - 1; i++) {
                    offset = offset + 6;
                    size = size - 6;
                    Log.w(TAG, "3 offset: " + offset);
                    contentLand.add(new GridForLandLargeScreen(TAG, this, offset));
                }
            } else {
                Log.w(TAG, "2 offset: " + 0);
                contentLand.add(new GridForLandLargeScreen(TAG, this, 0));
            }

            ll_conent.removeAllViewsInLayout();
            //set params
            for (final GridForLandLargeScreen templateContent : contentLand) {
                ll_conent.addView(templateContent.getRowGrid());
                templateContent.getRowGrid().getLayoutParams().width = display.getWidth() - ll_categories.getWidth();//display.getWidth();
                templateContent.getRowGrid().getLayoutParams().height = display.getHeight() / 2;

                if (templateContent.getOffset() + 0 < listEntries.size()) {
                    templateContent.getFirstItemGrid().getName().setText(listEntries.get(templateContent.getOffset() + 0).name);
                    templateContent.getFirstItemGrid().getEpisode().setText(Utils.durationInSecondsToString(listEntries.get(templateContent.getOffset() + 0).duration));
                    templateContent.getFirstItemGrid().getThumb().setScaleType(ImageView.ScaleType.CENTER_CROP);
                    templateContent.getFirstItemGrid().getName().setVisibility(View.VISIBLE);
                    templateContent.getFirstItemGrid().getEpisode().setVisibility(View.VISIBLE);
                    templateContent.getFirstItemGrid().getThumb().setVisibility(View.VISIBLE);
                    templateContent.getFirstItemGrid().getProgressBar().setVisibility(View.VISIBLE);
                } else {
                    Log.w(TAG, "first iteam grid not created");
                }
                if (templateContent.getOffset() + 1 < listEntries.size()) {
                    templateContent.getSecondItemGrid().getName().setText(listEntries.get(templateContent.getOffset() + 1).name);
                    templateContent.getSecondItemGrid().getEpisode().setText(Utils.durationInSecondsToString(listEntries.get(templateContent.getOffset() + 1).duration));
                    templateContent.getSecondItemGrid().getThumb().setScaleType(ImageView.ScaleType.CENTER_CROP);
                    templateContent.getSecondItemGrid().getName().setVisibility(View.VISIBLE);
                    templateContent.getSecondItemGrid().getEpisode().setVisibility(View.VISIBLE);
                    templateContent.getSecondItemGrid().getThumb().setVisibility(View.VISIBLE);
                    templateContent.getSecondItemGrid().getProgressBar().setVisibility(View.VISIBLE);
                } else {
                    Log.w(TAG, "second iteam grid not created");
                }
                if (templateContent.getOffset() + 2 < listEntries.size()) {
                    templateContent.getThirdItemGrid().getName().setText(listEntries.get(templateContent.getOffset() + 2).name);
                    templateContent.getThirdItemGrid().getEpisode().setText(Utils.durationInSecondsToString(listEntries.get(templateContent.getOffset() + 2).duration));
                    templateContent.getThirdItemGrid().getThumb().setScaleType(ImageView.ScaleType.CENTER_CROP);
                    templateContent.getThirdItemGrid().getName().setVisibility(View.VISIBLE);
                    templateContent.getThirdItemGrid().getEpisode().setVisibility(View.VISIBLE);
                    templateContent.getThirdItemGrid().getThumb().setVisibility(View.VISIBLE);
                    templateContent.getThirdItemGrid().getProgressBar().setVisibility(View.VISIBLE);
                } else {
                    Log.w(TAG, "third iteam grid not created");
                }
                if (templateContent.getOffset() + 3 < listEntries.size()) {
                    templateContent.getFourthItemGrid().getName().setText(listEntries.get(templateContent.getOffset() + 3).name);
                    templateContent.getFourthItemGrid().getEpisode().setText(Utils.durationInSecondsToString(listEntries.get(templateContent.getOffset() + 3).duration));
                    templateContent.getFourthItemGrid().getThumb().setScaleType(ImageView.ScaleType.CENTER_CROP);
                    templateContent.getFourthItemGrid().getName().setVisibility(View.VISIBLE);
                    templateContent.getFourthItemGrid().getEpisode().setVisibility(View.VISIBLE);
                    templateContent.getFourthItemGrid().getThumb().setVisibility(View.VISIBLE);
                    templateContent.getFourthItemGrid().getProgressBar().setVisibility(View.VISIBLE);
                } else {
                    Log.w(TAG, "fourth iteam grid not created");
                }
                if (templateContent.getOffset() + 4 < listEntries.size()) {
                    templateContent.getFifthItemGrid().getName().setText(listEntries.get(templateContent.getOffset() + 4).name);
                    templateContent.getFifthItemGrid().getEpisode().setText(Utils.durationInSecondsToString(listEntries.get(templateContent.getOffset() + 4).duration));
                    templateContent.getFifthItemGrid().getThumb().setScaleType(ImageView.ScaleType.CENTER_CROP);
                    templateContent.getFifthItemGrid().getName().setVisibility(View.VISIBLE);
                    templateContent.getFifthItemGrid().getEpisode().setVisibility(View.VISIBLE);
                    templateContent.getFifthItemGrid().getThumb().setVisibility(View.VISIBLE);
                    templateContent.getFifthItemGrid().getProgressBar().setVisibility(View.VISIBLE);
                } else {
                    Log.w(TAG, "fifth iteam grid not created");
                }
                if (templateContent.getOffset() + 5 < listEntries.size()) {
                    templateContent.getSixthItemGrid().getName().setText(listEntries.get(templateContent.getOffset() + 5).name);
                    templateContent.getSixthItemGrid().getEpisode().setText(Utils.durationInSecondsToString(listEntries.get(templateContent.getOffset() + 5).duration));
                    templateContent.getSixthItemGrid().getThumb().setScaleType(ImageView.ScaleType.CENTER_CROP);
                    templateContent.getSixthItemGrid().getName().setVisibility(View.VISIBLE);
                    templateContent.getSixthItemGrid().getEpisode().setVisibility(View.VISIBLE);
                    templateContent.getSixthItemGrid().getThumb().setVisibility(View.VISIBLE);
                    templateContent.getSixthItemGrid().getProgressBar().setVisibility(View.VISIBLE);
                } else {
                    Log.w(TAG, "sixth iteam grid not created");
                }


                templateContent.getFirstItemGrid().getThumb().setOnClickListener(new View.OnClickListener() {

                    public void onClick(View view) {

                        if (templateContent.getOffset() + 0 < listEntries.size()) {
                            showUploadDialog(listEntries.get(templateContent.getOffset() + 0));
                            Log.w(TAG, "click first" + templateContent.getOffset());
                        }
                    }
                });
                templateContent.getSecondItemGrid().getThumb().setOnClickListener(new View.OnClickListener() {

                    public void onClick(View view) {
                        Log.w(TAG, "click second");
                        if (templateContent.getOffset() + 1 < listEntries.size()) {
                            showUploadDialog(listEntries.get(templateContent.getOffset() + 1));
                            Log.w(TAG, "click first" + templateContent.getOffset() + 1);
                        }
                    }
                });
                templateContent.getThirdItemGrid().getThumb().setOnClickListener(new View.OnClickListener() {

                    public void onClick(View view) {
                        Log.w(TAG, "click third");
                        if (templateContent.getOffset() + 2 < listEntries.size()) {
                            showUploadDialog(listEntries.get(templateContent.getOffset() + 2));
                            Log.w(TAG, "click first" + templateContent.getOffset() + 2);
                        }
                    }
                });
                templateContent.getFourthItemGrid().getThumb().setOnClickListener(new View.OnClickListener() {

                    public void onClick(View view) {
                        Log.w(TAG, "click forth");
                        if (templateContent.getOffset() + 3 < listEntries.size()) {
                            showUploadDialog(listEntries.get(templateContent.getOffset() + 3));
                            Log.w(TAG, "click first" + templateContent.getOffset() + 3);
                        }
                    }
                });
                templateContent.getFifthItemGrid().getThumb().setOnClickListener(new View.OnClickListener() {

                    public void onClick(View view) {
                        Log.w(TAG, "click fifth");
                        if (templateContent.getOffset() + 4 < listEntries.size()) {
                            showUploadDialog(listEntries.get(templateContent.getOffset() + 4));
                            Log.w(TAG, "click first" + templateContent.getOffset() + 4);
                        }
                    }
                });
                templateContent.getSixthItemGrid().getThumb().setOnClickListener(new View.OnClickListener() {

                    public void onClick(View view) {
                        Log.w(TAG, "click sixth");
                        if (templateContent.getOffset() + 5 < listEntries.size()) {
                            showUploadDialog(listEntries.get(templateContent.getOffset() + 5));
                            Log.w(TAG, "click first" + templateContent.getOffset() + 5);
                        }
                    }
                });

            }
        } else {
            Log.w(TAG, "list size is 0");
        }
    }

    private void showUploadDialog(final KalturaMediaEntry entry) {
        //set up dialogVideoInfo
        if (countShowDialog < 1) {
            countShowDialog++;

            dialogUpload = new Dialog(VideoCategories.this);
            dialogUpload.requestWindowFeature(Window.FEATURE_NO_TITLE);
            dialogUpload.setContentView(R.layout.info);
            dialogUpload.setCancelable(true);

            LinearLayout ll_info = (LinearLayout) dialogUpload.findViewById(R.id.ll_info);
            ll_info.getLayoutParams().height = (int) Math.round((float) display.getHeight() * 0.6);
            if (display.getHeight() > display.getWidth()) {
                ll_info.getLayoutParams().width = display.getWidth() / 2;
                url = entry.thumbnailUrl + "/width/" + new Integer(display.getWidth() / 2).toString();
            } else {
                ll_info.getLayoutParams().width = display.getHeight() / 2;
                url = entry.thumbnailUrl + "/width/" + new Integer(display.getHeight() / 2).toString();
            }
            new DownloadThumb().execute();

            TextView tv_name = (TextView) dialogUpload.findViewById(R.id.tv_name);
            tv_name.setText(entry.name);

            TextView tv_duration = (TextView) dialogUpload.findViewById(R.id.tv_duration);
            tv_duration.setText(Utils.durationInSecondsToString(entry.duration));

            TextView tv_description = (TextView) dialogUpload.findViewById(R.id.tv_description);
            tv_description.setText(entry.description);

            ImageView iv_button_facebook = (ImageView) dialogUpload.findViewById(R.id.iv_button_facebook);
            ImageView iv_button_twitter = (ImageView) dialogUpload.findViewById(R.id.iv_button_twitter);
            ImageView iv_button_mail = (ImageView) dialogUpload.findViewById(R.id.iv_button_mail);
            ImageView iv_close = (ImageView) dialogUpload.findViewById(R.id.iv_close);
            ImageView iv_button_play = (ImageView) dialogUpload.findViewById(R.id.iv_button_play);
            dialogUpload.show();

            //set up button
            iv_close.setOnClickListener(new View.OnClickListener() {

                @Override
                public void onClick(View v) {
                    dialogUpload.cancel();
                    countShowDialog = 0;
                }
            });
            iv_button_facebook.setOnClickListener(new View.OnClickListener() {

                @Override
                public void onClick(View v) {
                    sharing.sendToFacebook(entry);
                }
            });
            iv_button_twitter.setOnClickListener(new View.OnClickListener() {

                @Override
                public void onClick(View v) {
                    sharing.sendToTwitter(entry);
                }
            });
            iv_button_mail.setOnClickListener(new View.OnClickListener() {

                @Override
                public void onClick(View v) {
                    sharing.sendToMail(entry);
                }
            });
            iv_button_play.setOnClickListener(new View.OnClickListener() {

                @Override
                public void onClick(View v) {
                    String url = entry.thumbnailUrl + "/width/" + new Integer(display.getWidth()).toString() + "/height/" + new Integer(display.getHeight() / 2).toString();
                    countShowDialog = 0;
                    getActivityMediator().showPlayer(entry.id, entry.downloadUrl, entry.duration, url);
                    dialogUpload.cancel();
                }
            });
        }
    }

    private Bitmap downloadFile(String fileUrl) {
        URL myFileUrl = null;
        Bitmap bmImg = null;
        HttpURLConnection conn = null;
        InputStream is = null;
        try {
            myFileUrl = new URL(fileUrl);
        } catch (MalformedURLException e) {
            // TODO Auto-generated catch block
            e.printStackTrace();
        }
        try {
            conn = (HttpURLConnection) myFileUrl.openConnection();
            conn.setDoInput(true);
            conn.connect();
            is = conn.getInputStream();

            bmImg = BitmapFactory.decodeStream(is);
            //imView.setImageBitmap(bmImg);
        } catch (IOException e) {
            // TODO Auto-generated catch block
            e.printStackTrace();
        }
        try {
            is.close();
            conn.disconnect();
        } catch (IOException e) {
            e.printStackTrace();
        }
        return bmImg;
    }

    private void ImageLoaderForPort() {
        Log.w(TAG, "Start image loader");

        DisplayImageOptions options = new DisplayImageOptions.Builder() //.showStubImage(R.drawable.arrow)
                //.showImageForEmptyUrl(R.drawable.arrow)
                .cacheInMemory() //.cacheOnDisc()
                .build();

        // This configuration tuning is custom. You can tune every option, you may tune some of them, 
        // or you can create default configuration by
        //  ImageLoaderConfiguration.createDefault(this);
        // method.
        ImageLoaderConfiguration config = new ImageLoaderConfiguration.Builder(activity).threadPoolSize(3).threadPriority(Thread.NORM_PRIORITY - 2).memoryCacheSize(1500000000) // 150 Mb
                .httpReadTimeout(10000) // 10 s
                .denyCacheImageMultipleSizesInMemory().build();
        // Initialize ImageLoaderForPort with configuration.
        ImageLoader.getInstance().init(config);
        ImageLoader.getInstance().enableLogging(); // Not necessary in common
        imageLoader.init(config);

        final List<String> url = new ArrayList<String>();
        view = new ArrayList<ImageView>();
        progressBar = new ArrayList<ProgressBar>();

        for (KalturaMediaEntry entry : listEntries) {
            url.add(entry.thumbnailUrl + "/width/" + new Integer(/*
                     * display.getWidth()
                     */300).toString() + "/height/" + new Integer(/*
                     * display.getHeight()/2
                     */300).toString());
        }
        count = 0;
        for (String string : url) {
            Log.w(TAG, "url: " + count++ + " " + string);
        }

        int state = 0;
        count = 0;
        for (int j = 0; j < listEntries.size(); j++) {

            switch (state) {
                case 0:
                    //left
                    Log.w(TAG, "xl: " + count);
                    view.add(contentPort.get(count).getFirstItemGrid().getThumb());
                    progressBar.add(contentPort.get(count).getFirstItemGrid().getProgressBar());
                    state = 1;
                    break;
                case 1:
                    //center
                    Log.w(TAG, "xc: " + count);
                    view.add(contentPort.get(count).getSecondItemGrid().getThumb());
                    progressBar.add(contentPort.get(count).getSecondItemGrid().getProgressBar());
                    state = 2;
                    break;
                case 2:
                    //right
                    Log.w(TAG, "xr: " + count);
                    view.add(contentPort.get(count).getThirdItemGrid().getThumb());
                    progressBar.add(contentPort.get(count).getThirdItemGrid().getProgressBar());
                    state = 3;
                    break;
                case 3:
                    //right
                    Log.w(TAG, "xr: " + count);
                    view.add(contentPort.get(count).getFourthItemGrid().getThumb());
                    progressBar.add(contentPort.get(count).getFourthItemGrid().getProgressBar());
                    count++;
                    state = 0;
                    break;
            }
        }

        count = 0;
        Log.w(TAG, "size: " + progressBar.size());
        k = 0;
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
                        progressBar.get(k++).setVisibility(View.GONE);
                    }
                    Log.w(TAG, "onLoadingComplete: " + k);
                    if (k >= url.size() && determineScreenSize() != Configuration.SCREENLAYOUT_SIZE_LARGE) {
                        setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_UNSPECIFIED);
                    }
                }
            });
            count++;
        }

    }

    private void ImageLoaderForLand() {
        Log.w(TAG, "Start image loader");

        DisplayImageOptions options = new DisplayImageOptions.Builder() 
                .cacheInMemory() //.cacheOnDisc()
                .build();

        // This configuration tuning is custom. You can tune every option, you may tune some of them, 
        // or you can create default configuration by
        //  ImageLoaderConfiguration.createDefault(this);
        // method.
        ImageLoaderConfiguration config = new ImageLoaderConfiguration.Builder(activity).threadPoolSize(3).threadPriority(Thread.NORM_PRIORITY - 2).memoryCacheSize(1500000000) // 150 Mb
                .httpReadTimeout(10000) // 10 s
                .denyCacheImageMultipleSizesInMemory().build();
        // Initialize ImageLoaderForPort with configuration.
        ImageLoader.getInstance().init(config);
        ImageLoader.getInstance().enableLogging(); // Not necessary in common
        imageLoader.init(config);

        final List<String> url = new ArrayList<String>();
        view = new ArrayList<ImageView>();
        progressBar = new ArrayList<ProgressBar>();

        for (KalturaMediaEntry entry : listEntries) {
            url.add(entry.thumbnailUrl + "/width/" + new Integer(/*
                     * display.getWidth()
                     */300).toString() + "/height/" + new Integer(/*
                     * display.getHeight()/2
                     */300).toString());
        }
        count = 0;
        for (String string : url) {
            Log.w(TAG, "url: " + count++ + " " + string);
        }

        int state = 0;
        count = 0;
        for (int j = 0; j < listEntries.size(); j++) {

            switch (state) {
                case 0:
                    //left
                    Log.w(TAG, "xl: " + count);
                    view.add(contentLand.get(count).getFirstItemGrid().getThumb());
                    progressBar.add(contentLand.get(count).getFirstItemGrid().getProgressBar());
                    state = 1;
                    break;
                case 1:
                    //center
                    Log.w(TAG, "xc: " + count);
                    view.add(contentLand.get(count).getSecondItemGrid().getThumb());
                    progressBar.add(contentLand.get(count).getSecondItemGrid().getProgressBar());
                    state = 2;
                    break;
                case 2:
                    //right
                    Log.w(TAG, "xr: " + count);
                    view.add(contentLand.get(count).getThirdItemGrid().getThumb());
                    progressBar.add(contentLand.get(count).getThirdItemGrid().getProgressBar());
                    state = 3;
                    break;
                case 3:
                    //right
                    Log.w(TAG, "xr: " + count);
                    view.add(contentLand.get(count).getFourthItemGrid().getThumb());
                    progressBar.add(contentLand.get(count).getFourthItemGrid().getProgressBar());
                    state = 4;
                    break;
                case 4:
                    //right
                    Log.w(TAG, "xr: " + count);
                    view.add(contentLand.get(count).getFifthItemGrid().getThumb());
                    progressBar.add(contentLand.get(count).getFifthItemGrid().getProgressBar());
                    state = 5;
                    break;
                case 5:
                    //right
                    Log.w(TAG, "xr: " + count);
                    view.add(contentLand.get(count).getSixthItemGrid().getThumb());
                    progressBar.add(contentLand.get(count).getSixthItemGrid().getProgressBar());
                    count++;
                    state = 0;
                    break;
            }
        }

        count = 0;
        Log.w(TAG, "size: " + progressBar.size());
        k = 0;
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
                        progressBar.get(k++).setVisibility(View.GONE);
                    }
                    Log.w(TAG, "onLoadingComplete: " + k);
                    if (k >= url.size() && determineScreenSize() != Configuration.SCREENLAYOUT_SIZE_LARGE) {
                        setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_UNSPECIFIED);
                    }

                }
            });
            count++;
        }

    }

    private void setViewOtherScreens() {
        ll_conent = ((LinearLayout) findViewById(R.id.ll_conent));
        etSearch = (EditText) findViewById(R.id.et_search);
        bar = new ActionBar(this, TAG);
        lvAllCategories = (ListView) findViewById(R.id.lv_category);
        lvAllCategories.setOnItemClickListener(this);
        search = findViewById(R.id.search);
    }

    private void setViewLargeScreen() {
        ll_conent = ((LinearLayout) findViewById(R.id.ll_conent));
        tv_bar = (TextView) findViewById(R.id.tv_bar);
        bar = new ActionBar(this, TAG);
        ll_categories = (LinearLayout) findViewById(R.id.ll_categories);
        etSearch = (EditText) findViewById(R.id.et_search);
        lvAllCategories = (ListView) findViewById(R.id.lv_category);
        lvAllCategories.setOnItemClickListener(this);
        lvAllCategories.setSelection(0);

        //Bar
        search = findViewById(R.id.search);

    }

    public void addListCategoriesOnScreen() {
        Log.w(TAG, "update data");

        /**
         * Sort by name
         */
        Collections.sort(listCategory, new Sort<KalturaCategory>("name", "compareTo"));
        listAllCategories = new BoxAdapterAllCategories(context, listCategory, textColor, arrow, Color.argb(transparentColor, 1, 18, 35));
        if (largeScreen) {
            KalturaCategory mp = new KalturaCategory();
            mp.name = "Most Popular";
            mp.id = 1;
            listCategory.add(0, mp);
        }

        listAllCategories.setVisiblityHighlight(this.visibleHightLight);
        listAllCategories.notifyDataSetChanged();
        listAllCategories.setHighlightIndex(this.position);
        lvAllCategories.setAdapter(listAllCategories);
        searchText.init(TAG, etSearch, listCategory);
        etSearch.addTextChangedListener(searchText);
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

    @Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
        switch (keyCode) {
            case KeyEvent.KEYCODE_MENU:
                countShowDialog = 0;
                return true;
            case KeyEvent.KEYCODE_BACK:
                countShowDialog = 0;
                moveTaskToBack(true);
                break;
            default:
                break;
        }
        return super.onKeyDown(keyCode, event);
    }

    public void onClick(View v) {
        switch (v.getId()) {
            case R.id.iv_bar_menu:
                getActivityMediator().showMain();
                this.position = 0;
                finish();
                break;
            case R.id.iv_bar_search:
                if (search.getVisibility() == View.GONE) {
                    search.setVisibility(View.VISIBLE);
                } else {
                    search.setVisibility(View.GONE);
                }
                break;
            case R.id.rl_button_back:

                switch (determineScreenSize()) {
                    case Configuration.SCREENLAYOUT_SIZE_NORMAL:
                    case Configuration.SCREENLAYOUT_SIZE_SMALL:
                        //finish();
                        break;
                    case Configuration.SCREENLAYOUT_SIZE_LARGE:
                    case Configuration.SCREENLAYOUT_SIZE_UNDEFINED:
                        if (ll_categories.getVisibility() == View.VISIBLE) {
                            ll_categories.setVisibility(View.GONE);
                            tv_bar.setVisibility(View.GONE);
                        } else {
                            ll_categories.setVisibility(View.VISIBLE);
                            tv_bar.setVisibility(View.VISIBLE);
                        }

                        break;
                    default:
                        break;
                }
                break;
            default:
                break;
        }
    }

    @Override
    public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
        Log.w(TAG, "size" + determineScreenSize());
        switch (determineScreenSize()) {
            case Configuration.SCREENLAYOUT_SIZE_NORMAL:
            case Configuration.SCREENLAYOUT_SIZE_SMALL:
                Log.w(TAG, "itemClick: position = " + position + ", id = " + id);
                getActivityMediator().showVideoCategory(listAllCategories.getCategoryId(position), listAllCategories.getCategoryName(position));
                break;
            case Configuration.SCREENLAYOUT_SIZE_LARGE:
            case Configuration.SCREENLAYOUT_SIZE_UNDEFINED:
                Log.w(TAG, "itemClick: position = " + position + ", id = " + id + "selectedId" + parent.getSelectedItemId());
                this.position = position;

                listAllCategories.setHighlightIndex(position);
                listAllCategories.setVisiblityHighlight(true);
                listAllCategories.notifyDataSetChanged();

                categoryId = listAllCategories.getCategoryId(position);
                categoryName = listAllCategories.getCategoryName(position);
                if ((orientation == Configuration.ORIENTATION_PORTRAIT) || (orientation == Configuration.ORIENTATION_UNDEFINED) || (orientation == Configuration.ORIENTATION_SQUARE)) {
                    bar.setTitle(categoryName);
                } else {
                    bar.setTitle("Categories");
                }
                if (categoryName.equals("Most Popular")) {
                    //most popular
                    new DownloadListEntriesTask().execute();
                    isMostPopular = true;
                } else {
                    new DownloadListEntriesTask().execute();
                    isMostPopular = false;
                }
                break;
        }
    }

    @Override
    public void update(Observable paramObservable, Object paramObject) {
        /**
         * Sort by name
         */
        Collections.sort((List<KalturaCategory>) paramObject, new Sort<KalturaCategory>("name", "compareTo"));
        listAllCategories = new BoxAdapterAllCategories(context, (List<KalturaCategory>) paramObject, textColor, arrow, Color.argb(0, 1, 18, 35));
        lvAllCategories.setAdapter(listAllCategories);
    }

    private class DownloadListCatigoriesTask extends AsyncTask<Void, States, List<KalturaCategory>> {

        private String message;

        @Override
        protected List<KalturaCategory> doInBackground(Void... params) {
            // Test for connection
            Log.w(TAG, "Thread is start.");
            try {
                if (Utils.checkInternetConnection(getApplicationContext())) {
                    /**
                     * Getting list of all categories
                     */
                    publishProgress(States.LOADING_DATA);
                    listCategory = Category.listAllCategories(TAG, 1, 500);
                    Log.w(TAG, "Thread is end.");
                }
            } catch (Exception e) {
                e.printStackTrace();
                message = e.getMessage();
                if (message != null) {
                    Log.w(TAG, message);
                }
                publishProgress(States.NO_CONNECTION);
            }
            return listCategory;
        }

        @Override
        protected void onPostExecute(final List<KalturaCategory> listCategory) {
            progressDialog.hide();
            // setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_UNSPECIFIED);

            try {
                if (Utils.checkInternetConnection(getApplicationContext())) {
                    addListCategoriesOnScreen();
                    if (determineScreenSize() == Configuration.SCREENLAYOUT_SIZE_LARGE
                            || determineScreenSize() == Configuration.SCREENLAYOUT_SIZE_UNDEFINED) {

                        if (largeScreen) {
                            //most popular
                            Log.w(TAG, "pos: " + position + "name " + categoryName);
                            if (position == 0 || listAllCategories.getCategoryId(position) == 1) {
                                Log.w(TAG, "is true");
                                categoryName = "Most Popular";
                                isMostPopular = true;
                            } else {
                                categoryId = listAllCategories.getCategoryId(position);
                                categoryName = listAllCategories.getCategoryName(position);
                                isMostPopular = false;
                            }
                            new DownloadListEntriesTask().execute();

                        } else {
                            categoryId = listAllCategories.getCategoryId(position);
                            categoryName = listAllCategories.getCategoryName(position);
                            new DownloadListEntriesTask().execute();
                            isMostPopular = false;
                        }
                    }
                } else {
                    Log.w(TAG, "Data weren't load!");
                    VideoCategories.this.finish();
                }
            } catch (Exception e) {
                e.printStackTrace();
                message = e.getMessage();
                Log.w(TAG, message);
                publishProgress(States.NO_CONNECTION);
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

    private class DownloadThumb extends AsyncTask<Void, States, Void> {

        private String message;
        private Bitmap bmp;

        @Override
        protected Void doInBackground(Void... params) {
            // Test for connection
            Log.w(TAG, "Thread is start. Download thumb");
            try {
                if (Utils.checkInternetConnection(getApplicationContext())) {
                    /**
                     * Getting list of all categories
                     */
                    //publishProgress(States.LOADING_DATA);
                    bmp = downloadFile(url);
                    Log.w(TAG, "Thread is end.");
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
        protected void onPostExecute(final Void param) {
            progressDialog.hide();
            // setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_UNSPECIFIED);

            ImageView iv_thumbnail = (ImageView) dialogUpload.findViewById(R.id.iv_thumbnail);
            //iv_thumbnail.setImageResource(R.drawable.ic_launcher);
            iv_thumbnail.setImageBitmap(bmp);

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

    private class DownloadListEntriesTask extends AsyncTask<Void, States, Void> {

        private String message;

        @Override
        protected Void doInBackground(Void... params) {
            // Test for connection
            try {
                if (Utils.checkInternetConnection(getApplicationContext())) {
                    //Getting list of all entries category

                    publishProgress(States.LOADING_DATA);
                    //Getting list of all entries category
                    KalturaMediaEntryFilter filter = new KalturaMediaEntryFilter();
                    filter.mediaTypeEqual = KalturaMediaType.VIDEO;
                    if (!isMostPopular) {
                        filter.categoriesIdsMatchAnd = new Integer(categoryId).toString();
                    }
                    listEntries = Media.listAllEntriesByIdCategories(TAG, filter, 1, 500);
                }
                Log.w(TAG, "Thread is end!");
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

            switch (determineScreenSize()) {
                case Configuration.SCREENLAYOUT_SIZE_NORMAL:
                case Configuration.SCREENLAYOUT_SIZE_SMALL:
                    switch (orientation) {
                        case Configuration.ORIENTATION_PORTRAIT:
                        case Configuration.ORIENTATION_UNDEFINED:
                        case Configuration.ORIENTATION_SQUARE:
                            break;
                        case Configuration.ORIENTATION_LANDSCAPE:
                            break;
                        default:
                            break;
                    }
                    break;
                case Configuration.SCREENLAYOUT_SIZE_LARGE:
                    switch (orientation) {
                        case Configuration.ORIENTATION_PORTRAIT:
                        case Configuration.ORIENTATION_UNDEFINED:
                        case Configuration.ORIENTATION_SQUARE:
                            GridViewForPort();
                            ImageLoaderForPort();
                            break;
                        case Configuration.ORIENTATION_LANDSCAPE:
                            GridViewForLand();
                            ImageLoaderForLand();
                            break;
                        default:
                            break;
                    }
                    break;
                default:
                    switch (orientation) {
                        case Configuration.ORIENTATION_PORTRAIT:
                        case Configuration.ORIENTATION_UNDEFINED:
                        case Configuration.ORIENTATION_SQUARE:
                            GridViewForPort();
                            ImageLoaderForPort();
                            break;
                        case Configuration.ORIENTATION_LANDSCAPE:
                            GridViewForLand();
                            ImageLoaderForLand();
                            break;
                        default:
                            break;
                    }
                    break;
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
            }
        }
    }
}
