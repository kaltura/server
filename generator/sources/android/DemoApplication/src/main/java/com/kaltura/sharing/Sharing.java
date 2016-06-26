/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package com.kaltura.sharing;

import android.app.Activity;
import android.content.Intent;
import android.widget.Toast;

import com.kaltura.activity.R;
import com.kaltura.client.types.KalturaMediaEntry;
import com.nostra13.socialsharing.common.AuthListener;
import com.nostra13.socialsharing.common.LogoutListener;
import com.nostra13.socialsharing.common.PostListener;
import com.nostra13.socialsharing.facebook.FacebookEvents;
import com.nostra13.socialsharing.facebook.FacebookFacade;
import com.nostra13.socialsharing.twitter.TwitterEvents;
import com.nostra13.socialsharing.twitter.TwitterFacade;

/**
 *
 */
public class Sharing {

    public static final String FACEBOOK_APP_ID = "211619498871712";
    public static final String TWITTER_CONSUMER_KEY = "8soLHVfwGLUDn43caYkNEg";
    public static final String TWITTER_CONSUMER_SECRET = "VuoSyQ35b6RYDGadSl8elwyN3bflkwcfWezaqCHSw";
    private FacebookFacade facebook;
    private TwitterFacade twitter;
    private Activity activity;

    public Sharing(Activity activity) {

        this.activity = activity;

        facebook = new FacebookFacade(activity, FACEBOOK_APP_ID);
        twitter = new TwitterFacade(activity, TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET);

    }

    public void addListener() {
        FacebookEvents.addPostListener(facebookPostListener);
        TwitterEvents.addAuthListener(authListener);
        TwitterEvents.addPostListener(postListener);
        TwitterEvents.addLogoutListener(logoutListener);

    }

    public void removeListener() {
        FacebookEvents.removePostListener(facebookPostListener);
        TwitterEvents.removeAuthListener(authListener);
        TwitterEvents.removePostListener(postListener);
        TwitterEvents.removeLogoutListener(logoutListener);
    }

    public void sendToFacebook(final KalturaMediaEntry entry) {
        if (facebook.isAuthorized()) {
            facebook.publishMessage(entry.name, "", entry.dataUrl, entry.description, entry.dataUrl);

        } else {
            // Start authentication dialog and publish message after successful authentication
            facebook.authorize(new AuthListener() {

                @Override
                public void onAuthSucceed() {
                    facebook.publishMessage(entry.name, "", entry.dataUrl, entry.description, entry.dataUrl);

                }

                @Override
                public void onAuthFail(String error) { // Do noting
                }
            });

        }
    }

    public void sendToTwitter(final KalturaMediaEntry entry) {
        if (twitter.isAuthorized()) {
            twitter.publishMessage(entry.name + " " + entry.dataUrl);
        } else {
            // Start authentication dialog and publish message after successful authentication
            twitter.authorize(new AuthListener() {

                @Override
                public void onAuthSucceed() {
                    twitter.publishMessage(entry.name + " " + entry.dataUrl);
                }

                @Override
                public void onAuthFail(String error) { // Do nothing
                }
            });
        }
    }
    private PostListener facebookPostListener = new PostListener() {

        @Override
        public void onPostPublishingFailed() {
            activity.runOnUiThread(new Runnable() {

                @Override
                public void run() {
                    Toast.makeText(activity, R.string.facebook_post_publishing_failed, Toast.LENGTH_SHORT).show();
                }
            });
        }

        @Override
        public void onPostPublished() {
            activity.runOnUiThread(new Runnable() {

                @Override
                public void run() {
                    Toast.makeText(activity, R.string.facebook_post_published, Toast.LENGTH_SHORT).show();
                }
            });
        }
    };
    private AuthListener authListener = new AuthListener() {

        @Override
        public void onAuthSucceed() {
            showToastOnUIThread(R.string.toast_twitter_auth_success);
        }

        @Override
        public void onAuthFail(String error) {
            showToastOnUIThread(R.string.toast_twitter_auth_fail);
        }
    };
    private PostListener postListener = new PostListener() {

        @Override
        public void onPostPublishingFailed() {
            showToastOnUIThread(R.string.twitter_post_publishing_failed);
        }

        @Override
        public void onPostPublished() {
            showToastOnUIThread(R.string.twitter_post_published);
        }
    };
    private LogoutListener logoutListener = new LogoutListener() {

        @Override
        public void onLogoutComplete() {
            showToastOnUIThread(R.string.twitter_logged_out);
        }
    };

    private void showToastOnUIThread(final int textRes) {
        activity.runOnUiThread(new Runnable() {

            @Override
            public void run() {
                Toast.makeText(activity, textRes, Toast.LENGTH_SHORT).show();
            }
        });
    }

    public void sendToMail(KalturaMediaEntry entry) {
        Intent i = new Intent(Intent.ACTION_SEND);
        i.setType("text/plain");
        i.putExtra(Intent.EXTRA_SUBJECT, entry.name);
        i.putExtra(Intent.EXTRA_TEXT, entry.description + " " + entry.dataUrl);
        try {
            activity.startActivity(Intent.createChooser(i, "Send mail..."));
        } catch (android.content.ActivityNotFoundException ex) {
            Toast.makeText(activity, "There are no email clients installed.", Toast.LENGTH_SHORT).show();
        }
    }
}
