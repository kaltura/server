package com.kaltura.mediatorActivity;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.res.Configuration;
import android.graphics.Typeface;
import android.view.Display;
import android.view.KeyEvent;
import android.view.Window;
import android.view.WindowManager;
import android.view.inputmethod.InputMethodManager;

import com.kaltura.bar.ActionBar;
import com.nostra13.universalimageloader.core.ImageLoader;

/**
 * Concrete colleague
 */
public abstract class TemplateActivity extends Activity {

    protected static String TAG;
    private TemplateActivityMediator tempAM = new TemplateActivityMediator(this);
    protected InputMethodManager imm;
    protected Typeface typeFont;
    protected static ProgressDialog progressDialog;
    protected static Context context;
    protected ActionBar bar;
    protected Display display;
    protected ImageLoader imageLoader = ImageLoader.getInstance();

    public TemplateActivityMediator getActivityMediator() {
        return tempAM;
    }

    /**
     * Setting the default activity
     *
     */
    protected void init() {
        /**
         *          */
        TAG = getClass().getName();
        /**
         *          */
        context = getApplicationContext();
        /**
         *          */
        display = getWindowManager().getDefaultDisplay();
        /**
         * Set type font
         */
        typeFont = Typeface.createFromAsset(getAssets(), "fonts/Maven_Pro_Medium.ttf");
        /**
         * Hide title
         */
        requestWindowFeature(Window.FEATURE_NO_TITLE);
        /**
         * Screen orientation is portrait
         */
        //setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_PORTRAIT);
        /**
         * Hide soft keyboard
         */
        imm = (InputMethodManager) getSystemService(this.INPUT_METHOD_SERVICE);
        getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
        /**
         *          */
        progressDialog = new ProgressDialog(this);
        progressDialog.setProgressStyle(ProgressDialog.STYLE_SPINNER);
    }

    protected static void showProgressDialog(String nameProcess) {
        progressDialog.setMessage(nameProcess);
        progressDialog.show();
    }

    @Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
        switch (keyCode) {
            case KeyEvent.KEYCODE_MENU:
                getActivityMediator().showMain();
                break;
            case KeyEvent.KEYCODE_BACK:
                break;
            default:
                break;
        }
        return super.onKeyDown(keyCode, event);
    }

    protected int getScreenOrientation() {

        Display getOrient = getWindowManager().getDefaultDisplay();
        int orientation = getOrient.getOrientation();

        // Sometimes you may get undefined orientation Value is 0
        // simple logic solves the problem compare the screen
        // X,Y Co-ordinates and determine the Orientation in such cases
        if (orientation == Configuration.ORIENTATION_UNDEFINED) {
            Configuration config = getResources().getConfiguration();
            orientation = config.orientation;
            if (orientation == Configuration.ORIENTATION_UNDEFINED) {
                //if height and widht of screen are equal then
                // it is square orientation
                if (getOrient.getWidth() == getOrient.getHeight()) {
                    orientation = Configuration.ORIENTATION_SQUARE;
                } else {
                    //if widht is less than height than it is portrait
                    if (getOrient.getWidth() < getOrient.getHeight()) {
                        orientation = Configuration.ORIENTATION_PORTRAIT;
                    } else {
                        // if it is not any of the above it will defineitly be landscape
                        orientation = Configuration.ORIENTATION_LANDSCAPE;
                    }
                }
            }
        }
        return orientation; // return value 1 is portrait and 2 is Landscape Mode
    }

    //Determine screen size
    protected int determineScreenSize() {

        int res = Configuration.SCREENLAYOUT_SIZE_UNDEFINED;
        if ((getResources().getConfiguration().screenLayout & Configuration.SCREENLAYOUT_SIZE_MASK) == Configuration.SCREENLAYOUT_SIZE_LARGE) {
            res = Configuration.SCREENLAYOUT_SIZE_LARGE;
        } else if ((getResources().getConfiguration().screenLayout & Configuration.SCREENLAYOUT_SIZE_MASK) == Configuration.SCREENLAYOUT_SIZE_NORMAL) {
            res = Configuration.SCREENLAYOUT_SIZE_NORMAL;
        } else if ((getResources().getConfiguration().screenLayout & Configuration.SCREENLAYOUT_SIZE_MASK) == Configuration.SCREENLAYOUT_SIZE_SMALL) {
            res = Configuration.SCREENLAYOUT_SIZE_SMALL;
        } else {
        }
        return res;
    }
}
