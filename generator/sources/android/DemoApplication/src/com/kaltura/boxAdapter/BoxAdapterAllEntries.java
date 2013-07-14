package com.kaltura.boxAdapter;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;

import android.content.Context;
import android.graphics.Bitmap;
import android.graphics.Typeface;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.TextView;

import com.kaltura.activity.R;
import com.kaltura.client.types.KalturaMediaEntry;

public class BoxAdapterAllEntries extends BaseAdapter {

    private List<KalturaMediaEntry> list;
    private HashMap<KalturaMediaEntry, Bitmap> listThumbanil;
    private LayoutInflater lInflater;
    private Typeface typeFont;
    private int width;
    private int height;

    public BoxAdapterAllEntries(Context context, int width, int height, List<KalturaMediaEntry> list, HashMap<KalturaMediaEntry, Bitmap> listThumbanil) {
        this.width = width;
        this.height = height;
        /**
         * Set type font
         */
        typeFont = Typeface.createFromAsset(context.getAssets(), "fonts/Maven_Pro_Medium.ttf");
        if (list != null) {
            this.list = list;
            this.listThumbanil = listThumbanil;
        } else {
            this.list = new ArrayList<KalturaMediaEntry>();
            this.listThumbanil = new HashMap<KalturaMediaEntry, Bitmap>();
        }
        lInflater = (LayoutInflater) context.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
    }

    static class ViewHolder {

        protected ImageView thumbnail;
        protected TextView name;
        protected TextView episode;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {
        View view = null;
        if (convertView == null) {
            view = lInflater.inflate(R.layout.item_entry, null);
            final ViewHolder viewHolder = new ViewHolder();

            viewHolder.thumbnail = ((ImageView) view.findViewById(R.id.iv_thumbnail));
            viewHolder.name = (TextView) view.findViewById(R.id.tv_name);
            viewHolder.name.setTypeface(typeFont);
            view.setTag(viewHolder);
            viewHolder.episode = (TextView) view.findViewById(R.id.tv_episode);
            viewHolder.episode.setTypeface(typeFont);
            viewHolder.episode.setVisibility(View.VISIBLE);
            view.setTag(viewHolder);
        } else {
            view = convertView;
        }
        ViewHolder holder = (ViewHolder) view.getTag();
        try {
            holder.thumbnail.getLayoutParams().width = this.width;
            holder.thumbnail.getLayoutParams().height = this.height;
            holder.thumbnail.setScaleType(ImageView.ScaleType.CENTER_CROP);
            holder.thumbnail.setImageBitmap(this.listThumbanil.get(list.get(position)));
        } catch (IndexOutOfBoundsException e) {
            e.printStackTrace();
        }
        holder.name.setText(list.get(position).name);
        holder.episode.setText("sdgdfgd");
        return view;
    }

    @Override
    public int getCount() {
        return list.size();
    }

    @Override
    public Object getItem(int arg0) {
        return null;
    }

    @Override
    public long getItemId(int arg0) {
        return 0;
    }

    public String getEntryId(int position) {
        return list.get(position).id;
    }

    public String getNameCategory(int position) {
        return list.get(position).categories;
    }
}