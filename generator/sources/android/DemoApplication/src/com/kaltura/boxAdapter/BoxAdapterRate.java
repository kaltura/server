package com.kaltura.boxAdapter;

import java.util.ArrayList;
import java.util.List;

import android.content.Context;
import android.graphics.Color;
import android.graphics.Typeface;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.TextView;

import com.kaltura.activity.R;
import com.kaltura.client.types.KalturaFlavorAsset;
import com.kaltura.utils.Utils;

public class BoxAdapterRate extends BaseAdapter {

    private List<KalturaFlavorAsset> list;
    private LayoutInflater lInflater;
    private Typeface typeFont;
    private boolean setHighlight = false;
    private int highlightIndex = 0;
    private int backgroundColor;

    public BoxAdapterRate(Context context, List<KalturaFlavorAsset> list, int backgroundColor) {
        this.backgroundColor = backgroundColor;
        /**
         * Set type font
         */
        typeFont = Typeface.createFromAsset(context.getAssets(), "fonts/Maven_Pro_Medium.ttf");
        if (list != null) {
            this.list = list;
        } else {
            this.list = new ArrayList<KalturaFlavorAsset>();
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
        protected ImageView iv_rate_select;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {
        ViewHolder holder;
        if (convertView == null) {
            convertView = lInflater.inflate(R.layout.item_list_rate, null);
            holder = new ViewHolder();

            holder.text = (TextView) convertView.findViewById(R.id.tv_name_rate);
            holder.text.setTypeface(typeFont);
            holder.iv_rate_select = (ImageView) convertView.findViewById(R.id.iv_rate_select);
            holder.iv_rate_select.setVisibility(View.INVISIBLE);
            convertView.setTag(holder);
        } else {
            convertView = convertView;
            holder = (ViewHolder) convertView.getTag();
        }
        if (setHighlight && highlightIndex == position) {
            convertView.setBackgroundResource(backgroundColor);
            holder.iv_rate_select.setVisibility(View.VISIBLE);
        } else {
            convertView.setBackgroundColor(Color.BLACK);
            holder.iv_rate_select.setVisibility(View.INVISIBLE);
        }
   
        holder.text.setText(getFlavorBitrate(position));
        return convertView;
    }

    @Override
    public int getCount() {
        return list.size();
    }

    @Override
    public Object getItem(int arg0) {
        return arg0;
    }

    @Override
    public long getItemId(int arg0) {
        return arg0;
    }

    public String getFlavorId(int position) {
        return list.get(position).id;
    }

    public int getFlavorSizeKb(int position) {
        return list.get(position).size;
    }

    public int getFlavorBitrateInt(int position) {
        return list.get(position).bitrate;
    }

    public StringBuffer getFlavorBitrate(int position) {
    	KalturaFlavorAsset flavor = getFlavor(position);
    	if (flavor.tags!=null && flavor.tags.contains("widevine_mbr"))
    		return new StringBuffer("auto");
    	else 
    		return Utils.roundBitrate(flavor.bitrate);
    }
    
    public KalturaFlavorAsset getFlavor(int position) {
    	return list.get(position);
    }

    public String getListFlavors() {
        String str = "";
        for (int i = 0; i < list.size(); i++) {
            str = str + list.get(i).id;
            if(i != list.size() - 1){
                str = str + ",";
            }
        }
        return str;
    }
}