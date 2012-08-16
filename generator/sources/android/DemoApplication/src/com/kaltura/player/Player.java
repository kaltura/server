package com.kaltura.player;

import java.io.IOException;
import java.util.Observable;

import android.app.Activity;
import android.media.MediaPlayer;
import android.util.Log;
import android.view.SurfaceHolder;
import android.widget.Toast;

/**
 * Implements MediaPlayer class action and sends the current message class
 * ViewPlayer
 */
public class Player extends Observable implements MediaPlayer.OnErrorListener,
        MediaPlayer.OnCompletionListener, MediaPlayer.OnInfoListener, MediaPlayer.OnSeekCompleteListener,
        MediaPlayer.OnBufferingUpdateListener,
        MediaPlayer.OnPreparedListener {

    private String TAG;
    private MediaPlayer player;
    private String url;
    private boolean isPrepared = false;
    private boolean setDataSource = false;
    private boolean isStarted = false;
    private boolean isPaused = false;
    private boolean isStopped = true;
    private boolean isReleased = false;
    private int percent = 0;
    private SurfaceHolder holder;
    private Activity activity;
    private int seekTo = 0;
    private int prevCurPos;
    private int prevPercent = 0;

    /**
     * Constructor Description of Player
     *
     * @param TAG constant in your class
     * @param holder Abstract interface to someone holding a display surface.
     * Allows you to control the surface size and format, edit the pixels in the
     * surface, and monitor changes to the surface. This interface is typically
     * available through the SurfaceView class.
     * @param activity Activities that shows the player
     */
    public Player(String TAG, SurfaceHolder holder, Activity activity) {

        this.TAG = TAG;
        this.holder = holder;
        this.activity = activity;
        player = new MediaPlayer();

        player.setDisplay(holder);
        player.setScreenOnWhilePlaying(true);
        player.setOnPreparedListener(this);
        player.setOnBufferingUpdateListener(this);
        player.setOnErrorListener(this);
        player.setOnCompletionListener(this);
        player.setOnInfoListener(this);
        player.setOnSeekCompleteListener(this);
        isReleased = false;

    }

    /**
     * Register a callback to be invoked when an error has happened during an
     * asynchronous operation.
     *
     * @param mp - the MediaPlayer the info pertains to.
     * @param what - the type of error
     * @param extra - an extra code, specific to the info. Typically
     * implementation dependant.
     */
    @Override
    public boolean onError(MediaPlayer mp, int what, int extra) {
        Log.w(TAG, "ERROR:          ");
        setChanged();
        notifyObservers(STATES.ERROR);        
        StringBuilder sb = new StringBuilder();
        sb.append("Media Player Error: ");
        String message = "";
        switch (what) {
            case MediaPlayer.MEDIA_ERROR_NOT_VALID_FOR_PROGRESSIVE_PLAYBACK:
                sb.append("Not Valid for Progressive Playback");
                message = "Not Valid for Progressive Playback";
                break;
            case MediaPlayer.MEDIA_ERROR_SERVER_DIED:
                sb.append("Server Died");
                message = "Server Died";
                setRelease();
                break;
            case MediaPlayer.MEDIA_ERROR_UNKNOWN:
                sb.append("Unknown");
                message = "Unknown";
                Toast.makeText(activity, "Invalid data format. Select another!", Toast.LENGTH_SHORT).show();
                break;
            default:
                sb.append(" Non standard (");
                message = "Non standard";
                sb.append(what);
                sb.append(")");
        }
        sb.append(" (" + what + ") ");
        sb.append(extra);
        Log.w(TAG, sb.toString());
        return false;
    }

    /**
     * Interface definition for a callback to be invoked when playback of a
     * media source has completed.
     *
     * @param mp - the MediaPlayer that reached the end of the file
     */
    @Override
    public void onCompletion(MediaPlayer mp) {
        Log.w(TAG, "Complete");
        setChanged();
        notifyObservers(STATES.COMPLETE);
        isStopped = true;
        setStop();
        setReset();
    }

    /**
     * Called to indicate an info or a warning.
     *
     * @param mp - the MediaPlayer the info pertains to.
     * @param what - the type of info or warning.
     * @param extra - an extra code, specific to the info. Typically
     * implementation dependant.
     *
     * @return True if the method handled the info, false if it didn't.
     * Returning false, or not having an OnErrorListener at all, will cause the
     * info to be discarded.
     */
    @Override
    public boolean onInfo(MediaPlayer mp, int what, int extra) {
        Log.w(TAG, "PlayerService onInfo : ");
        if (what == mp.MEDIA_INFO_UNKNOWN) {
            Log.w(TAG, "MEDIA_INFO_UNKNOWN");
        }
        if (what == mp.MEDIA_INFO_VIDEO_TRACK_LAGGING) {
            Log.w(TAG, "MEDIA_INFO_VIDEO_TRACK_LAGGING");
        }
        if (what == mp.MEDIA_INFO_METADATA_UPDATE) {
            Log.w(TAG, "MEDIA_INFO_METADATA_UPDATE");
        }
        if (what == mp.MEDIA_INFO_BAD_INTERLEAVING) {
            Log.w(TAG, "MEDIA_INFO_BAD_INTERLEAVING");
        }
        if (what == mp.MEDIA_INFO_NOT_SEEKABLE) {
            Log.w(TAG, "MEDIA_INFO_NOT_SEEKABLE");
        }
        if (what == mp.MEDIA_INFO_METADATA_UPDATE) {
            Log.w(TAG, "MEDIA_INFO_METADATA_UPDATE");
        }
        return false;
    }

    /**
     * Called to indicate the completion of a seek operation.
     *
     * @param mp - the MediaPlayer that issued the seek operation
     *
     */
    @Override
    public void onSeekComplete(MediaPlayer mp) {
        Log.w(TAG, "Seek complete: ");
        setChanged();
        notifyObservers(STATES.SEEK_COMPLETE);
    }

    /**
     * Called to update status in buffering a media stream received through
     * progressive HTTP download. The received buffering percentage indicates
     * how much of the content has been buffered or played. For example a
     * buffering update of 80 percent when half the content has already been
     * played indicates that the next 30 percent of the content to play has been
     * buffered.
     *
     * @param mp - the MediaPlayer the update pertains to
     * @param percent - the percentage (0-100) of the content that has been
     * buffered or played thus far
     */
    @Override
    public void onBufferingUpdate(MediaPlayer mp, int percent) {
        Log.w(TAG, "PlayerService onBufferingUpdate : " + percent + "%");
        this.percent = percent;
        if (prevPercent == percent) {
            //setChanged();
            //notifyObservers(STATES.PROGRESS_LOADING1);
        }

        prevPercent = percent;
        setChanged();
        notifyObservers(STATES.PROGRESS_LOADING);
        if (percent >= 100) {
            setChanged();
            notifyObservers(STATES.LOADING_COMPLETE);
        }
    }

    /**
     * Called when the media file is ready for playback.
     *
     * @param mp - the MediaPlayer that is ready for playback
     */
    @Override
    public void onPrepared(MediaPlayer mp) {
        Log.w(TAG, "Stream is prepared");
        isPrepared = true;
        setChanged();
        notifyObservers(STATES.PREAPERED);

        Log.w(TAG, "Player started when downloading");
        setChanged();
        notifyObservers(STATES.STARTED);
        player.start();
        player.seekTo(this.seekTo);
        isStarted = true;
        isPaused = false;
        isStopped = false;
    }

    /**
     * Implements the logic to switch between bitrates during playback of video
     *
     */
    public void selectBitrate() {
        prevCurPos = getCurrentPosition();
        Log.w(TAG, "prevCurPos: " + prevCurPos);
        setStop();
        setReset();
        setDataSource(url);
        Log.w(TAG, "prevCurPos: " + prevCurPos);
        seekTo = prevCurPos;
        prepare();
    }

    public void setUrl(String url) {
        this.url = url;
    }

    public void setDisplay() {
        player.setDisplay(holder);
    }

    public int getDownloadingPercent() {
        return this.percent;
    }

    public void setVolume(float left, float right) {
        player.setVolume(left, right);
    }

    public void setRelease() {
        Log.w(TAG, "Set release");
        isPrepared = false;
        setDataSource = false;
        isStarted = false;
        isPaused = false;
        isStopped = true;
        player.release();
        isReleased = true;
    }

    public boolean isReleased() {
        return isReleased;
    }

    /**
     * Sets the data source (FileDescriptor) to use. It is the caller's
     * responsibility to close the file descriptor. It is safe to do so as soon
     * as this call returns.
     *
     * @param path - the FileDescriptor for the file you want to play
     *
     * @throws IllegalStateException - if it is called in an invalid state
     * @throws IOException
     * @throws IllegalArgumentException
     */
    public void setDataSource(String path) {
        Log.w(TAG, "Set data source: " + path);
        setDataSource = true;
        isPrepared = false;
        try {
            player.setDataSource(path);
            Log.w(TAG, "Set data source: " + path);
        } catch (IOException ex) {
            Log.w(TAG, "error set data source", ex);
        } catch (IllegalArgumentException ex) {
            Log.w(TAG, "error set data source", ex);
        } catch (IllegalStateException ex) {
            Log.w(TAG, "error set data source", ex);
        }
    }

    public void prepare() {
        try {
            player.prepareAsync();
        } catch (IllegalStateException e) {
            e.printStackTrace();
            Log.w(TAG, "err prepare: set data source did'n set");
        }
    }

    public boolean getPrepared() {
        return isPrepared;
    }

    public boolean isPlaying() {
        return player.isPlaying();
    }

    public void setStart() {
        if (!getStateDataSource()) {
            setDataSource(url);
        } else {
            Log.w(TAG, "set data sourse set");
        }
        if (!isPrepared) {
            prepare();
            this.seekTo = 0;
        }
        if (isPrepared) {
            Log.w(TAG, "Player started");
            setChanged();
            notifyObservers(STATES.STARTED);
            player.start();
            isStarted = true;
            isPaused = false;
            isStopped = false;
        }
    }

    public boolean isStarted() {
        return isStarted;
    }

    public boolean getStateDataSource() {
        return setDataSource;
    }

    public void setStop() {
        Log.w(TAG, "set stop");
        try {
            player.stop();
        } catch (IllegalStateException e) {
            e.printStackTrace();
            Log.w(TAG, "err stop: ", e);
        }
        isStarted = false;
        isPrepared = false;
        isStopped = true;
    }

    public boolean isStopped() {
        return isStopped;
    }

    public void setReset() {
        Log.w(TAG, "set reset");
        isPrepared = false;
        setDataSource = false;
        player.reset();
    }

    public void setPause() {
        Log.w(TAG, "set pause");
        try {
            player.pause();
        } catch (IllegalStateException e) {
            e.printStackTrace();
            Log.w(TAG, "err pause: ", e);
        }
        isStarted = false;
        isPaused = true;
        setChanged();
        notifyObservers(STATES.PAUSED);
    }

    public boolean isPaused() {
        return isPaused;
    }

    public void seekTo(int seek) {
        try {
            player.seekTo(seek);
        } catch (IllegalStateException e) {
            e.printStackTrace();
            Log.w(TAG, "err seekTo: ", e);
        }
    }

    public int getDuration() {
        int res = 0;
        if (player != null) {
            res = player.getDuration();
        }
        return res;
    }

    public int getCurrentPosition() {
        int res = 0;
        if (player != null) {
            try {
                res = player.getCurrentPosition();
            } catch (IllegalStateException e) {
                e.printStackTrace();
                Log.w(TAG, "err get current position", e);
                res = 0;
            }
        }
        return res;
    }
}
