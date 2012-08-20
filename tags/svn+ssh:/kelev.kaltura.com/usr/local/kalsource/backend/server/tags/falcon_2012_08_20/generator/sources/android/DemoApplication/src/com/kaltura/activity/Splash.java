package com.kaltura.activity;

//<editor-fold defaultstate="collapsed" desc="comment">
import android.app.ProgressDialog;
import android.content.res.Configuration;
import android.os.Bundle;
import android.util.Log;
import android.widget.Toast;

import com.kaltura.mediatorActivity.TemplateActivity;
import com.kaltura.services.AdminUser;
import com.kaltura.utils.Utils;
//</editor-fold>

public class Splash extends TemplateActivity {

    private ProgressDialog progressDialog;

    @Override
    public void onCreate(Bundle savedInstanceState) {

        super.onCreate(savedInstanceState);
        init();
        setContentView(R.layout.splash);

        try {
            if (Utils.checkInternetConnection(context)) {
                getActivityMediator().showSettings();
            }
        } catch (Exception e) {
            e.printStackTrace();
            Toast.makeText(context, "Internet Connection not present!", Toast.LENGTH_LONG).show();
        }

    }

    @Override
    public void onDestroy() {
        super.onDestroy();
    }

    @Override
    public void onConfigurationChanged(Configuration newConfig) {
        super.onConfigurationChanged(newConfig);
        setContentView(R.layout.main);
    }

    public void authorization(String email, String password) {
        AdminUser.login(TAG, email, password, new AdminUser.LoginTaskListener() {

            @Override
            public void onLoginSuccess() {
                Toast.makeText(context, "Authorization is success!", Toast.LENGTH_LONG).show();
                getActivityMediator().showSettings();
            }

            @Override
            public void onLoginError(String errorMessage) {
                progressDialog.cancel();
            }
        });
    }

    private void updateData() {
        Log.w(TAG, new Integer(Main.state).toString());
        switch (Main.state) {
            case 1:
                if (AdminUser.userIsLogin()) {
                    getActivityMediator().showMostPopular();
                } else {
                    getActivityMediator().showSettings();
                }
                break;
            case 2:
                if (AdminUser.userIsLogin()) {
                    getActivityMediator().showVideoCategories();
                } else {
                    getActivityMediator().showSettings();
                }
                break;
            case 3:
                if (AdminUser.userIsLogin()) {
                    getActivityMediator().showUpload();
                } else {
                    getActivityMediator().showSettings();
                }
                break;
            case 4:
                if (AdminUser.userIsLogin()) {
                    getActivityMediator().showMain();
                } else {
                    getActivityMediator().showSettings();
                }
                break;
            default:
                getActivityMediator().showMain();
                break;
        }
    }
}
