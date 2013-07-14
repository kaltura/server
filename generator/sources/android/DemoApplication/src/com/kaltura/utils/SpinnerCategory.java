package com.kaltura.utils;

import java.util.ArrayList;
import java.util.List;

import android.app.Activity;
import android.view.View;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemSelectedListener;
import android.widget.ArrayAdapter;
import android.widget.Spinner;

import com.kaltura.activity.R;

public class SpinnerCategory implements OnItemSelectedListener {

    private String TAG;
    private Spinner spinner;
    private ArrayAdapter<String> adapter;
    private Activity acticity;

    public SpinnerCategory(String TAG, Activity acticity, String promt, List<String> list) {
        /**
         *
         */
        this.TAG = TAG;
        /**
         *
         */
        this.acticity = acticity;
        /**
         *
         */
        spinner = (Spinner) acticity.findViewById(R.id.sp_category);
        /**
         * Title
         */
        spinner.setPrompt(promt);
        List<String> listNameCategory;
        if (list != null) {
            listNameCategory = list;
        } else {
            listNameCategory = new ArrayList<String>();
        }
        adapter = new ArrayAdapter<String>(acticity, android.R.layout.simple_spinner_item, listNameCategory);
        adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        spinner.setAdapter(adapter);
        /**
         * Set selection
         */
        spinner.setSelection(0);
    }

    public void addData(List<String> list) {
        List<String> listNameCategory;
        if (list != null) {
            listNameCategory = list;
        } else {
            listNameCategory = new ArrayList<String>();
        }
        adapter = new ArrayAdapter<String>(acticity, android.R.layout.simple_spinner_item, listNameCategory);
        adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        spinner.setAdapter(adapter);
    }

    public String getSelectedItem() {
        return spinner.getSelectedItem().toString();
    }

    public void setSelection(int position) {
        spinner.setSelection(position);
    }

    @Override
    public void onItemSelected(AdapterView<?> paramAdapterView, View paramView,
            int paramInt, long paramLong) {
    }

    @Override
    public void onNothingSelected(AdapterView<?> paramAdapterView) {
    }
}
