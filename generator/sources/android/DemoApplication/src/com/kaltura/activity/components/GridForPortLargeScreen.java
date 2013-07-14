/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package com.kaltura.activity.components;

import java.util.HashMap;
import java.util.List;

import android.app.Activity;
import android.content.Context;
import android.graphics.Bitmap;
import android.view.LayoutInflater;
import android.widget.LinearLayout;

import com.kaltura.activity.R;
import com.kaltura.client.types.KalturaMediaEntry;

/**
 *
 * @author sda
 */
public class GridForPortLargeScreen{
    
    private String TAG;
    private Activity activity;
    private LayoutInflater  inflater;
    private LinearLayout row_grid;
    private ItemGrid itemFirst;
    private ItemGrid itemSecond;
    private ItemGrid itemThird;
    private ItemGrid itemFourth;
    private int offset;
    private HashMap<KalturaMediaEntry, Bitmap> listBitmap;
    private List<KalturaMediaEntry> listKeys;
    
    
    public GridForPortLargeScreen(String TAG, Activity activity, int offset){
        this.TAG = TAG;
        this.activity = activity;
        
        inflater = (LayoutInflater)activity.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        row_grid = (LinearLayout)inflater.inflate(R.layout.template_entry_for_large_screen, null);    
        
        itemFirst = new ItemGrid(TAG, row_grid, R.id.item_entry_for_large_screen_first);
        itemSecond = new ItemGrid(TAG, row_grid, R.id.item_entry_for_large_screen_second);
        itemThird = new ItemGrid(TAG, row_grid, R.id.item_entry_for_large_screen_third);
        itemFourth = new ItemGrid(TAG, row_grid, R.id.item_entry_for_large_screen_fourth);
        this.offset = offset;
        
    }
    
    public LinearLayout getRowGrid(){
        return row_grid;
    }

    public ItemGrid getFirstItemGrid(){
        return itemFirst;
    }

    public ItemGrid getSecondItemGrid(){
        return itemSecond;
    }
    
    public ItemGrid getThirdItemGrid(){
        return itemThird;
    }

    public ItemGrid getFourthItemGrid(){
        return itemFourth;
    }
    
    private void addContent() {
    }
    
    public int getOffset(){
        return offset;
    }
    
}
