package com.kaltura.activity;

//<editor-fold defaultstate="collapsed" desc="comment">
import android.content.pm.ActivityInfo;
import android.os.Bundle;
import android.view.KeyEvent;
import android.view.MotionEvent;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

import com.kaltura.bar.ActionBar;
import com.kaltura.mediatorActivity.TemplateActivity;
import com.kaltura.services.AdminUser;
import com.kaltura.utils.Utils;
//</editor-fold>

public class Settings extends TemplateActivity {

    private EditText etEmail;
    private EditText etPassword;

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        init();
        setContentView(R.layout.settings);
        setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_PORTRAIT);
        bar = new ActionBar(this, TAG);
        bar.setTitle(getText(R.string.settings));
        bar.setVisibleBackButton(View.INVISIBLE);
        bar.setVisibleBackButton(View.INVISIBLE);
        setFont();
        etEmail = (EditText) findViewById(R.id.et_login_user);
        etPassword = (EditText) findViewById(R.id.et_login_password);
        etEmail.setText("user@email.com");
        etPassword.setText("password");
        AdminUser.host = "http://www.kaltura.com";
        AdminUser.cdnHost = "http://cdnbakmi.kaltura.com";
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
                imm.hideSoftInputFromWindow(getWindow().getCurrentFocus().getWindowToken(), 0);
                break;
        }
        return super.dispatchTouchEvent(ev);
    }

    private void setFont() {
        ((TextView) findViewById(R.id.tv_categoty)).setTypeface(typeFont);
        ((TextView) findViewById(R.id.tv_password)).setTypeface(typeFont);
        ((EditText) findViewById(R.id.et_login_user)).setTypeface(typeFont);
        ((EditText) findViewById(R.id.et_login_password)).setTypeface(typeFont);
        ((Button) findViewById(R.id.btn_login_ok)).setTypeface(typeFont);
        ((Button) findViewById(R.id.btn_login_cancel)).setTypeface(typeFont);
    }

    public void onClick(View v) {
        switch (v.getId()) {
            case R.id.btn_login_ok:
                try {
                    if (Utils.checkInternetConnection(context)) {
                        authorization(etEmail.getText().toString(), etPassword.getText().toString());
                    }
                } catch (Exception e) {
                    e.printStackTrace();
                    Toast.makeText(context, "Internet Connection not present!", Toast.LENGTH_LONG).show();
                }
                break;
            case R.id.btn_login_cancel:
                finish();
                getActivityMediator().showMain();
                break;
            case R.id.iv_bar_menu:
                getActivityMediator().showMain();
                break;
            default:
                break;
        }
    }

    public void authorization(String email, String password) {
        AdminUser.login(TAG, email, password, new AdminUser.LoginTaskListener() {

            @Override
            public void onLoginSuccess() {
                Toast.makeText(context, "Authorization is success!", Toast.LENGTH_LONG).show();
                getActivityMediator().showMain();
            }

            @Override
            public void onLoginError(String errorMessage) {
                Toast.makeText(context, errorMessage, Toast.LENGTH_LONG).show();
            }
        });
    }

    @Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
        switch (keyCode) {
            case KeyEvent.KEYCODE_MENU:
                getActivityMediator().showMain();
                finish();
                break;
            case KeyEvent.KEYCODE_BACK:
                finish();
                break;
            default:
                break;
        }
        return super.onKeyDown(keyCode, event);
    }
}
