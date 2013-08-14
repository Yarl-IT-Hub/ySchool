package org.yarlithub.yschool.ySchoolSetUp.Reader;

import org.apache.poi.ss.usermodel.Row;

/**
 * Created with IntelliJ IDEA.
 * User: jayrksih
 * Date: 8/13/13
 * Time: 11:12 PM
 * To change this template use File | Settings | File Templates.
 */
public interface Reader {

    boolean setSheet(int sheetNo);

    Row getRow(int rowNo);

    Integer getLastRowNumber();

}
