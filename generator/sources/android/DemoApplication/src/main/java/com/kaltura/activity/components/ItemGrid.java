/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package com.kaltura.activity.components;

import java.util.HashMap;
import java.util.Observable;
import java.util.Observer;

import android.graphics.Bitmap;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.kaltura.activity.R;
import com.kaltura.client.types.KalturaMediaEntry;

/**
 *
 * @author sda
 */
public class ItemGrid implements Observer{
    
    private String TAG;
    private LayoutInflater  inflater;
    private LinearLayout row_grid;
    private View item;
    private ImageView iv_thumbnail;
    private TextView tv_name;
    private TextView tv_episode;
    private ProgressBar pb_loading;
    private KalturaMediaEntry key;
    private boolean isSetBitmap = false;
    private Bitmap bitmap;
    private RelativeLayout rl_item_entry;
    
    public ItemGrid(String TAG, View view, int res){
        this.TAG = TAG;

        item = (View) view.findViewById(res);//R.id.left_item);        
        
        rl_item_entry = (RelativeLayout) item.findViewById(R.id.rl_item_entry);
        
        iv_thumbnail = (ImageView) item.findViewById(R.id.iv_thumbnail);
        tv_name = (TextView) item.findViewById(R.id.tv_name);
        tv_episode = (TextView) item.findViewById(R.id.tv_episode);
        pb_loading = (ProgressBar) item.findViewById(R.id.pb_loading);
        
    }
    
    public RelativeLayout getItemEntry(){
        return rl_item_entry;
    }
    
    public ImageView getThumb(){
        return iv_thumbnail;
    }

    public TextView getName(){
        return tv_name;
    }

    public TextView getEpisode(){
        return tv_episode;
    }

    public ProgressBar getProgressBar(){
        return pb_loading;
    }
    
     public void setKey(KalturaMediaEntry key){
        if(key != null){
            this.key = key;
        }else{
            this.key = new KalturaMediaEntry();
        }
    }

    @Override
    public void update(Observable arg0, Object param) {
            if(((HashMap<String, Bitmap>)param).get(key.name) != null){
                if(!isSetBitmap){
                    getThumb().setImageBitmap(((HashMap<String, Bitmap>)param).get(key.name));
                    getProgressBar().setVisibility(View.GONE);
                    isSetBitmap = true;
                    Log.w(TAG, "update data!");
                }
            }
    }
    
}
