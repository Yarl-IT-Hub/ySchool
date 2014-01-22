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
public class StudentIntegrationData {

    public static List newStudentData;
    public static List studentSaveOrUpdateData;

    static {
        try {
            newStudentData = Arrays.asList(new Object[][]{
                    {"090200u", "student1", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "student1"},
                    {"sdfs", "alkdlaksjf", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "alkdlaksjf"},
                    {"dfs00u", "alkdlaksjf", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "alkdlaksjf"}
            });
        } catch (ParseException e) {
            e.printStackTrace();
        }

    }


    static {
        try {
            studentSaveOrUpdateData = Arrays.asList(new Object[][]{
                    {"090200u", "studentSaved", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "studentSaved"},
                    {"sdfs", "studentUpdated", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "studentUpdated"},
                    {"dfs00u", "studentUpdated1", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "studentUpdated1"}
                    });
        } catch (ParseException e) {
            e.printStackTrace();
        }

    }

}
