package com.kaltura.utils;

import java.util.Comparator;

import com.kaltura.client.types.KalturaCategory;
import com.kaltura.client.types.KalturaFlavorAsset;
import com.kaltura.client.types.KalturaMediaEntry;

/**
 * The class performs a sort
 */
public class Sort<T> implements Comparator<T> {

    private String filter = "name";
    private String direction = "compareTo";

    /**
     * Constructor Description of Sort<T>
     *
     * @param filter Specify which field to sort
     * @param direction Specifies the sort direction
     */
    public Sort(String filter, String direction) {
        this.filter = filter;
        this.direction = direction;
    }

    /**
     * Compares its two arguments for order. Returns a negative integer, zero,
     * or a positive integer as the first argument is less than, equal to, or
     * greater than the second.
     *
     * @param paramT1 the first object to be compared.
     * @param paramT2 the second object to be compared.
     *
     * @return a negative integer, zero, or a positive integer as the first
     * argument is less than, equal to, or greater than the second.
     *
     * @throws ClassCastException - if the arguments' types prevent them from
     * being compared by this Comparator.
     */
    @Override
    public int compare(T paramT1, T paramT2) {

        int res = 0;
        if (paramT1 instanceof KalturaMediaEntry && paramT2 instanceof KalturaMediaEntry) {
            if (this.filter.equals("name")) {
                res = ((KalturaMediaEntry) paramT1).name.compareTo(((KalturaMediaEntry) paramT2).name);
            }
            if (this.filter.equals("plays") && this.direction.equals("compareTo")) {
                res = new Integer(((KalturaMediaEntry) paramT1).plays).compareTo(new Integer(((KalturaMediaEntry) paramT2).plays));
            } else {
                res = ((KalturaMediaEntry) paramT2).plays - ((KalturaMediaEntry) paramT1).plays;
            }
            if (this.filter.equals("createdAt")) {
                res = new Integer(((KalturaMediaEntry) paramT1).createdAt).compareTo(new Integer(((KalturaMediaEntry) paramT2).createdAt));
            }
        }
        if (paramT1 instanceof KalturaCategory && paramT2 instanceof KalturaCategory) {
            res = ((KalturaCategory) paramT1).name.compareTo(((KalturaCategory) paramT2).name);
        }
        if (paramT1 instanceof KalturaFlavorAsset && paramT2 instanceof KalturaFlavorAsset) {
            res = ((KalturaFlavorAsset) paramT2).bitrate - ((KalturaFlavorAsset) paramT1).bitrate;
        }
        return res;
    }
}
