package com.kaltura.activity;

import android.content.Intent;
import android.content.pm.ActivityInfo;
import android.database.Cursor;
import android.net.Uri;
import android.os.Bundle;
import android.provider.MediaStore;
import android.util.Log;
import android.view.View;

import com.kaltura.bar.ActionBar;
import com.kaltura.mediatorActivity.TemplateActivity;

public class Upload extends TemplateActivity {

    private static final int GALLERY = 1;
    private static final int RECORD_VIDEO = 2;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_PORTRAIT);
        init();
        setContentView(R.layout.upload);

    }

    public void onClick(View v) {
        Intent intent;

        switch (v.getId()) {
            case R.id.rl_recod_video:
                //create new Intent
                intent = new Intent(MediaStore.ACTION_VIDEO_CAPTURE);
                intent.putExtra(MediaStore.EXTRA_VIDEO_QUALITY, 1); // set the video image quality to high
                intent.putExtra(MediaStore.EXTRA_DURATION_LIMIT, 0);
                // start the Video Capture Intent
                startActivityForResult(intent, RECORD_VIDEO);
                break;
            case R.id.rl_pick_from_gallery:
                intent = new Intent(Intent.ACTION_PICK, null);
                intent.setType("video/*");
                startActivityForResult(intent, 1);
                break;
            case R.id.iv_bar_menu:
                getActivityMediator().showMain();
                break;
            default:
                break;
        }
    }

    @Override
    public void onStart() {
        super.onStart();
        bar = new ActionBar(this, TAG);
        bar.setTitle(getText(R.string.upload));
        bar.setVisibleBackButton(View.INVISIBLE);
        bar.setVisibleSearchButon(View.INVISIBLE);

    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        if ((resultCode == RESULT_OK) && (data != null)) {
            switch (requestCode) {
                case RECORD_VIDEO:
                    Log.w(TAG, "real path: " + getRealPathFromURI(data.getData()));
                    getActivityMediator().showVideoInfo(getRealPathFromURI(data.getData()));
                    break;
                case GALLERY:
                    Log.w(TAG, "real path: " + getRealPathFromURI(data.getData()));
                    getActivityMediator().showVideoInfo(getRealPathFromURI(data.getData()));
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
}
