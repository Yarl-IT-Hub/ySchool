package org.yarlithub.yschool.ySchoolSetUp.Reader;

import org.apache.poi.ss.usermodel.Row;
import org.apache.poi.xssf.usermodel.XSSFSheet;
import org.apache.poi.xssf.usermodel.XSSFWorkbook;

import java.io.FileInputStream;
import java.io.IOException;

/**
 * Created with IntelliJ IDEA.
 * User: jayrksih
 * Date: 8/13/13
 * Time: 11:13 PM
 * To change this template use File | Settings | File Templates.
 */
public class XLSXReader implements Reader {

    XSSFWorkbook wb = null;
    XSSFSheet sheet = null;

    public XLSXReader(FileInputStream fileInputStream) throws IOException {
        wb = new XSSFWorkbook(fileInputStream);
    }

    @Override
    public boolean setSheet(int sheetNo) {
        sheet = wb.getSheetAt(sheetNo);
        if (sheet != null) {
            return true;
        }
        return false;
    }

    @Override
    public Row getRow(int rowNo) {
        return sheet.getRow(rowNo);
    }

    @Override
    public Integer getLastRowNumber() {
        return sheet.getLastRowNum();
    }
}
