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

    static {
        try {
            newStudentData = Arrays.asList(new Object[][]{
                    {"090200u", "alkdlaksjf", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "Jaffna"},
                    {"sdfs", "alkdlaksjf", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "Jaffna"},
                    {"dfs00u", "alkdlaksjf", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "Jaffna"}
            });
        } catch (ParseException e) {
            e.printStackTrace();
        }

    }

}
