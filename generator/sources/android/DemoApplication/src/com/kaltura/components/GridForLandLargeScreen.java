/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package com.kaltura.components;

import android.app.Activity;
import android.content.Context;
import android.view.LayoutInflater;
import android.widget.LinearLayout;

import com.kaltura.activity.R;

/**
 *
 * @author sda
 */
public class GridForLandLargeScreen {

    private String TAG;
    private LayoutInflater inflater;
    private LinearLayout row_grid;
    private ItemGrid itemFirst;
    private ItemGrid itemSecond;
    private ItemGrid itemThird;
    private ItemGrid itemFourth;
    private ItemGrid itemFifth;
    private ItemGrid itemSixth;
    private int offset;

    public GridForLandLargeScreen(String TAG, Activity activity, int offset) {
        this.TAG = TAG;

        inflater = (LayoutInflater) activity.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        row_grid = (LinearLayout) inflater.inflate(R.layout.template_entry_for_large_screen, null);

        itemFirst = new ItemGrid(TAG, row_grid, R.id.item_entry_for_large_screen_first);
        itemSecond = new ItemGrid(TAG, row_grid, R.id.item_entry_for_large_screen_second);
        itemThird = new ItemGrid(TAG, row_grid, R.id.item_entry_for_large_screen_third);
        itemFourth = new ItemGrid(TAG, row_grid, R.id.item_entry_for_large_screen_fourth);
        itemFifth = new ItemGrid(TAG, row_grid, R.id.item_entry_for_large_screen_fifth);
        itemSixth = new ItemGrid(TAG, row_grid, R.id.item_entry_for_large_screen_sixth);

        this.offset = offset;
    }

    public LinearLayout getRowGrid() {
        return row_grid;
    }

    public ItemGrid getFirstItemGrid() {
        return itemFirst;
    }

    public ItemGrid getSecondItemGrid() {
        return itemSecond;
    }

    public ItemGrid getThirdItemGrid() {
        return itemThird;
    }

    public ItemGrid getFourthItemGrid() {
        return itemFourth;
    }

    public ItemGrid getFifthItemGrid() {
        return itemFifth;
    }

    public ItemGrid getSixthItemGrid() {
        return itemSixth;
    }

    public int getOffset() {
        return offset;
    }
}
