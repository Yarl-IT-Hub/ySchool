package org.yarlithub.yschool.integration.service.testdata;

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

    public static List newstudentData;
    public static List studentSaveOrUpdateData;

    static {
        try {
            newstudentData = Arrays.asList(new Object[][]{
                    {"090200u", "student1", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "student1"},
                    {"sdfs", "student2", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "student2"},
                    {"dfs00u", "student3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "student3"}
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

    public static List studenttestData;

    static {
        try {
            studenttestData = Arrays.asList(new Object[][]{
                    {"090200u", "student1", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "student1"},
                    {"sdfs", "student2", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "student2"},
                    {"dfs00u", "student3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "student3"}
            });
        } catch (ParseException e) {
            e.printStackTrace();
        }

    }

    public static List newStudentData;

    static {
        try {
            newStudentData = Arrays.asList(new Object[][]{
                    {"090200u", "student1", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "student1"},
                    {"sdfs", "student2", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "student2"},
                    {"dfs00u", "student3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "student3"},
                    {"dfs00u1", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u2", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u3", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u4", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u5", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u6", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u7", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u8", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u9", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u10", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u11", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u12", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u13", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u14", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u15", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u16", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u17", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u18", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u19", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u20", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u21", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u22", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u23", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u24", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u25", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u26", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u27", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u28", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u29", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u30", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u31", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u32", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u33", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u34", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u35", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u36", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u37", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u38", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u39", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"},
                    {"dfs00u40", "samplestuden3", "dlsakjfdlkaj alkdjfa", "alkfdalkdsjf", (new SimpleDateFormat("d-MMM-yyyy")).parse("29-Apr-2010"), "male", "Jaffna", "samplestuden3"}

            });
        } catch (ParseException e) {
            e.printStackTrace();
        }

    }
}
