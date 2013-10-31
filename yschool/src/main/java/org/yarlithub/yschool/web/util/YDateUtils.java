package org.yarlithub.yschool.web.util;

import java.text.DateFormatSymbols;

/**
 * Created with IntelliJ IDEA.
 * User: jayrksih
 * Date: 10/30/13
 * Time: 9:48 PM
 * To change this template use File | Settings | File Templates.
 */
public class YDateUtils {

    public static String getMonthForInt(int num) {
        String month = "wrong";
        DateFormatSymbols dfs = new DateFormatSymbols();
        String[] months = dfs.getMonths();
        if (num >= 0 && num <= 11 ) {
            month = months[num];
        }
        return month;
    }
}
