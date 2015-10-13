package com.kaltura.boxAdapter;

import java.util.ArrayList;
import java.util.List;

import android.content.Context;
import android.graphics.Typeface;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.TextView;

import com.kaltura.activity.R;
import com.kaltura.client.types.KalturaCategory;

/**
 *
 */
public class BoxAdapterAllCategories extends BaseAdapter {

    private List<KalturaCategory> list;
    private LayoutInflater lInflater;
    private Typeface typeFont;
    private int textColor;
    private int arrow;
    private boolean setHighlight = false;
    private int highlightIndex = 0;
    private int backgroundColor;

    public BoxAdapterAllCategories(Context context, List<KalturaCategory> list, int textColor, int arrow, int backgroundColor) {
        this.textColor = textColor;
        this.arrow = arrow;
        this.backgroundColor = backgroundColor;
        /**
         * Set type font
         */
        typeFont = Typeface.createFromAsset(context.getAssets(), "fonts/Maven_Pro_Medium.ttf");
        if (list != null) {
            this.list = list;
        } else {
            this.list = new ArrayList<KalturaCategory>();
        }

        lInflater = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
    }

    public void setVisiblityHighlight(boolean setHighlight) {
        this.setHighlight = setHighlight;
    }

    public void setHighlightIndex(int highlightIndex) {
        this.highlightIndex = highlightIndex;
    }

    static class ViewHolder {

        protected TextView text;
        protected ImageView imageView;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {
        ViewHolder holder;

        if (convertView == null) {
            convertView = lInflater.inflate(R.layout.item_list_category, parent, false);
            holder = new ViewHolder();
            holder.text = (TextView) convertView.findViewById(R.id.tv_item_category);
            holder.text.setTypeface(typeFont);
            holder.imageView = (ImageView) convertView.findViewById(R.id.iv_arrow);
            holder.imageView.setBackgroundResource(arrow);
            convertView.setTag(holder);
        } else {
            holder = (ViewHolder) convertView.getTag();
        }
        if (setHighlight && highlightIndex == position) {
            convertView.setBackgroundResource(R.drawable.background_item_list_category); // put your highlight color here
        } else {
            convertView.setBackgroundColor(backgroundColor);//Color.argb(0, 1, 18, 35));  // transparent
        }

        holder.text.setText(list.get(position).name);
        holder.text.setTextColor(this.textColor);
        return convertView;
    }

    @Override
    public int getCount() {
        return list.size();
    }

    @Override
    public Object getItem(int position) {
        return position;
    }

    @Override
    public long getItemId(int position) {
        return position;
    }

    public int getCategoryId(int position) {
        return list.get(position).id;
    }

    public String getCategoryName(int position) {
        return list.get(position).name;
    }
}