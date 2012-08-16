package com.kaltura.bar;

import android.app.Activity;
import android.view.View;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.kaltura.activity.R;

public class ActionBar {

    private String TAG;
    private ViewHolder viewHolder;
    private Activity activity;

    public ActionBar(Activity activity, String TAG) {

        this.activity = activity;
        this.TAG = TAG;
        viewHolder = new ViewHolder();

        viewHolder.bar = activity.findViewById(R.id.bar);
        viewHolder.rl_bar = (RelativeLayout) viewHolder.bar.findViewById(R.id.rl_bar);
        viewHolder.tv_bar_title = (TextView) viewHolder.bar.findViewById(R.id.tv_bar_title);
        viewHolder.tv_bar_title.setText("");
        viewHolder.iv_bar_search = (ImageView) viewHolder.bar.findViewById(R.id.iv_bar_search);
        viewHolder.iv_bar_search.setVisibility(View.INVISIBLE);
        viewHolder.rl_back_button = (RelativeLayout) viewHolder.bar.findViewById(R.id.rl_button_back);
        viewHolder.iv_movie = (ImageView) viewHolder.bar.findViewById(R.id.iv_movie);
        viewHolder.tv_name_category = (TextView) viewHolder.bar.findViewById(R.id.tv_name_category);
        viewHolder.iv_bar_menu = (ImageView) viewHolder.bar.findViewById(R.id.iv_bar_menu);
    }

    public class ViewHolder {

        public View bar;
        public RelativeLayout rl_bar;
        public TextView tv_bar_title;
        public ImageView iv_bar_search;
        public RelativeLayout rl_back_button;
        public ImageView iv_movie;
        public TextView tv_name_category;
        public ImageView iv_bar_menu;
    }

    public ViewHolder getViewHolder() {
        return viewHolder;
    }

    public int getBarWidth() {
        return viewHolder.rl_bar.getWidth();
    }

    public int getBarHeight() {
        return viewHolder.rl_bar.getHeight();
    }

    public void setTitle(CharSequence title) {
        viewHolder.tv_bar_title.setText(title);
    }

    public void setVisibleSearchButon(int visibility) {
        viewHolder.iv_bar_search.setVisibility(visibility);
    }

    public void setVisibleBackButton(int visibility) {
        viewHolder.rl_back_button.setVisibility(visibility);
    }

    public void setBackgroundBackButton(int resid) {
        viewHolder.rl_back_button.setBackgroundResource(resid);
    }

    public void setVisibleImageMovie(int visibility) {
        viewHolder.iv_movie.setVisibility(visibility);
    }

    public void setVisibleNameCategory(int visibility) {
        viewHolder.tv_name_category.setVisibility(visibility);
    }

    public void setTextNameCategory(String text) {
        viewHolder.tv_name_category.setText(text);
    }
}
