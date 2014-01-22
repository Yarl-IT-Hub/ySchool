package org.yarlithub.yschool.integration.testdata;

import java.util.Arrays;
import java.util.List;

/**
 * Created with IntelliJ IDEA.
 * User: admin
 * Date: 1/22/2014
 * Time: 8:59 PM
 * To change this template use File | Settings | File Templates.
 */
public class StaffIntegrationData {

    public static List staffData1,staffData2,staffDataUpdate;

    static {
            staffData1 = Arrays.asList(new Object[][]{
                    {"090200u", "alkdlaksjf", "dlsakjfdlkaj alkdjfa"},
                    {"sdfs", "alkdlaksjf", "dlsakjfdlkaj alkdjfa"},
                    {"dfs00u", "alkdlaksjf", "dlsakjfdlkaj alkdjfa"}
            });

        staffData2 = Arrays.asList(new Object[][]{
                {"090100u", "al11kdlaksjf", "dlsakjfdlkaj alkdjfa"},
                {"sd11fs", "alkdl11aksjf", "dlsakjfdlkaj alkdjfa"},
                {"dfs1100u", "alkd11laksjf", "dlsakjfdlkaj alkdjfa"}
        });


        staffDataUpdate = Arrays.asList(new Object[][]{
                {"090200u", "al11kdlaksjf", "dlsakjfdlkaj alkdjfa"},
                {"sdfs", "alkdl11aksjf", "dlsakjfdlkaj alkdjfa"},
                {"dfs00u", "alkd11laksjf", "dlsakjfdlkaj alkdjfa"}
        });
    }
}
