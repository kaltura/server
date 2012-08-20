package com.kaltura.utils;

import java.util.ArrayList;
import java.util.List;
import java.util.Observable;

import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.widget.EditText;

import com.kaltura.client.types.KalturaCategory;

/**
 * Seeking a string entered in the search for the category
 */
public class SearchTextCategory extends Observable implements TextWatcher {

    private int textlength;
    private List<KalturaCategory> copyList;
    private List<KalturaCategory> listCategory;
    private List<String> copyListInLowerCase;
    private EditText editText;
    private String TAG;

    public SearchTextCategory() {
    }

    public void init(String TAG, EditText editText, List<KalturaCategory> listCategory) {
        this.TAG = TAG;
        this.editText = editText;
        this.listCategory = listCategory;
        textlength = 0;
        copyList = new ArrayList<KalturaCategory>();
        copyListInLowerCase = new ArrayList<String>();
        try {
            copyList.addAll(listCategory);

            for (KalturaCategory kalturaCategory : listCategory) {
                copyListInLowerCase.add(kalturaCategory.name.toLowerCase());
            }

        } catch (Exception e) {
            e.printStackTrace();
            Log.w(TAG, "err: " + e.getMessage());
        }
    }

    @Override
    public void beforeTextChanged(CharSequence s, int start, int count, int after) {
    }

    @Override
    public void onTextChanged(CharSequence s, int start, int before, int count) {
        textlength = editText.getText().length();

        listCategory.clear();
        for (int i = 0; i < copyList.size(); i++) {
            Log.w(TAG, "search - str: " + copyList.get(i).name);

            if (copyListInLowerCase.get(i).indexOf(editText.getText().toString().toLowerCase()) != -1) {
                listCategory.add(copyList.get(i));
            } else {
                Log.w(TAG, "search - not found");
            }
        }

        setChanged();
        notifyObservers(listCategory);
    }

    @Override
    public void afterTextChanged(Editable s) {
    }
}
