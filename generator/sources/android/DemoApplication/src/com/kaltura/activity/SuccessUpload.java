package com.kaltura.activity;

import android.content.pm.ActivityInfo;
import android.os.Bundle;
import android.view.View;
import android.widget.TextView;

import com.kaltura.bar.ActionBar;
import com.kaltura.mediatorActivity.TemplateActivity;

public class SuccessUpload extends TemplateActivity {

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        init();
        setContentView(R.layout.succsess_upload);
        setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_PORTRAIT);
        bar = new ActionBar(this, TAG);
        bar.setTitle(getText(R.string.upload));
        bar.setVisibleBackButton(View.INVISIBLE);
        bar.setVisibleSearchButon(View.INVISIBLE);

        setFont();
    }

    private void setFont() {
        ((TextView) findViewById(R.id.tv_thank_you)).setTypeface(typeFont);
        ((TextView) findViewById(R.id.tv_text)).setTypeface(typeFont);
    }

    public void onClick(View v) {
        switch (v.getId()) {
            case R.id.btn_upload_successfully:
            case R.id.iv_bar_menu:
                getActivityMediator().showMain();
                break;
            default:
                break;
        }
    }
}
