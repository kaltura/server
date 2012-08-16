package com.kaltura.utils;

import android.content.Context;
import android.net.ConnectivityManager;

public class Utils {

    /**
     * Check the current status of the network
     *
     * @param context
     *
     * @return the current network state. true - network is active, false -
     * network is'nt active
     *
     * @throws Exception
    *
     */
    public static boolean checkInternetConnection(Context context) throws Exception {
        ConnectivityManager cm = (ConnectivityManager) context.getSystemService(Context.CONNECTIVITY_SERVICE);
        // Test for connection
        if (cm.getActiveNetworkInfo() != null && cm.getActiveNetworkInfo().isAvailable() && cm.getActiveNetworkInfo().isConnected()) {
            return true;
        } else {
            //Log.v(TAG, "Internet Connection Not Present");
            //Toast.makeText(context, "Internet Connection Not Present" , Toast.LENGTH_LONG).show();
            throw new Exception("Internet Connection not present!");
        }
    }

    /**
     * Converts a time given in seconds in the format hh:mm:ss
     *
     * @param Time given in seconds
     *
     * @return Time received in the format hh:mm:ss
     */
    public static String durationInSecondsToString(int sec) {
        int hours = sec / 3600;
        int minutes = (sec / 60) - (hours * 60);
        int seconds = sec - (hours * 3600) - (minutes * 60);
        if (hours < 0) {
            hours = 0;
        }
        if (minutes < 0) {
            minutes = 0;
        }
        if (seconds < 0) {
            seconds = 0;
        }
        String formatted = String.format("%d:%02d:%02d", hours, minutes, seconds);
        return formatted;
    }

    /**
     * Rounds the specified value
     *
     * @param A given bitrate
     *
     * @return Rounded in a given bit rate defined format: if bitrate > 1000 =>
     * bitrate(mb) else bitrate(kb)
     */
    public static StringBuffer roundBitrate(int bitrate) {
        int roundBitrate = Math.round(bitrate / 100) * 100;
        StringBuffer formatted = new StringBuffer();
        if (roundBitrate / 1000 == 0) {
            //Kb
            formatted.append(roundBitrate);
            formatted.append("kb");
        } else {
            //Mb
            formatted.append(roundBitrate / 1000.0);
            formatted.append("mb");
        }
        return formatted;
    }
}
