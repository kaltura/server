package com.kaltura.services;

//<editor-fold defaultstate="collapsed" desc="comment">
import android.os.Handler;
import android.util.Log;

import com.kaltura.client.KalturaApiException;
import com.kaltura.client.KalturaClient;
import com.kaltura.client.KalturaConfiguration;
import com.kaltura.client.services.KalturaAdminUserService;
//</editor-fold>

/**
 * Manage details for the administrative user
 *
 */
public class AdminUser {

    private static KalturaClient client;
    private static boolean userIsLogin;
    /**
     * Contains the session if the user has successfully logged
     */
    public static String ks;

    /**
     *
     */
    public static KalturaClient getClient() {
        return client;
    }

    /**
     */
    public static boolean userIsLogin() {
        return userIsLogin;
    }

    /**
     * Get an admin session using admin email and password (Used for login to
     * the KMC application)
     *
     * @param TAG constant in your class
     * @param email
     * @param password
     *
     * @throws KalturaApiException
     */
    public static void login(final String TAG, final String email, final String password, final LoginTaskListener loginTaskListener) {
        final Handler handler = new Handler();
        Runnable runnable = new Runnable() {

            @Override
            public void run() {
                try {
                    // set a new configuration object
                    KalturaConfiguration config = new KalturaConfiguration();
                    config.setTimeout(10000);
                    config.setEndpoint("http://www.kaltura.com");

                    client = new KalturaClient(config);

                    KalturaAdminUserService userService = new KalturaAdminUserService(client);
                    ks = userService.login(email, password);
                    Log.w(TAG, ks);
                    // set the kaltura client to use the recieved ks as default for all future operations
                    client.setSessionId(ks);
                    userIsLogin = true;
                    handler.post(new Runnable() {

                        @Override
                        public void run() {
                            loginTaskListener.onLoginSuccess();
                        }
                    });
                } catch (final KalturaApiException e) {
                    e.printStackTrace();
                    Log.w(TAG, "Login error: " + e.getMessage() + " error code: " + e.code);
                    userIsLogin = false;
                    handler.post(new Runnable() {

                        @Override
                        public void run() {
                            loginTaskListener.onLoginError(e.getMessage());
                        }
                    });
                }
            }
        };
        new Thread(runnable).start();
    }

    public interface LoginTaskListener {

        void onLoginSuccess();

        void onLoginError(String errorMessage);
    }
}
