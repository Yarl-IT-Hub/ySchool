package org.yarlithub.yschool.integration.testdata;

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Arrays;
import java.util.List;

/**
 * Created with IntelliJ IDEA.
 * User: jayrksih
 * Date: 1/22/14
 * Time: 9:15 AM
 * To change this template use File | Settings | File Templates.
 */
public class ExaminationIntegrationData {

    public static List newCAExamData;
    public static List newTermExamData;
    public static List studentSaveOrUpdateData;

    static {
        try {
            newCAExamData = Arrays.asList(new Object[][]{
                    {(new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2014"), 1, 1, 5, 1, 1},
                    {(new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2014"), 2, 1, 5, 2, 1},
                    {(new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2014"), 3, 1, 5, 3, 1}}
            );
        } catch (ParseException e) {
            e.printStackTrace();
        }

    }

    static {
        try {
            newTermExamData = Arrays.asList(new Object[][]{
                    {(new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2014"), 1, 1, 5, 1},
                    {(new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2014"), 2, 1, 5, 1},
                    {(new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2014"), 3, 1, 5, 1}});
        } catch (ParseException e) {
            e.printStackTrace();
        }

    }


}
