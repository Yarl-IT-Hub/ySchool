package org.yarlithub.yschool.spreadSheetReader;

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
    Row row = null;

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
    public boolean setRow(int rowNo) {
        row = sheet.getRow(rowNo);
        if (row != null) {
            return true;
        }
        return false;
    }

    @Override
    public Integer getLastRowNumber() {
        return sheet.getLastRowNum();
    }


    @Override
    public String getStringCellValue(int columnNo) {
        return row.getCell(columnNo).getStringCellValue();
    }


    @Override
    public int getNumericCellValue(int columnNo) {
        return (int)row.getCell(columnNo).getNumericCellValue();
    }
}
