package org.yarlithub.yschool.spreadSheetReader;

import org.apache.poi.hssf.usermodel.HSSFSheet;
import org.apache.poi.hssf.usermodel.HSSFWorkbook;
import org.apache.poi.poifs.filesystem.POIFSFileSystem;
import org.apache.poi.ss.usermodel.Row;

import java.io.FileInputStream;
import java.io.IOException;

/**
 * Created with IntelliJ IDEA.
 * User: jayrksih
 * Date: 8/13/13
 * Time: 11:12 PM
 * To change this template use File | Settings | File Templates.
 */
public class XLSReader implements Reader {

    HSSFWorkbook wb = null;
    HSSFSheet sheet = null;
    Row row = null;


    public XLSReader(FileInputStream fileInputStream) {

        FileInputStream excelInputStream = null;
        excelInputStream = fileInputStream;


        POIFSFileSystem fs = null;
        try {
            fs = new POIFSFileSystem(excelInputStream);
        } catch (IOException e) {
            e.printStackTrace();
        }

        try {
            wb = new HSSFWorkbook(fs);
        } catch (IOException e) {
            e.printStackTrace();
        }
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
        return (int) row.getCell(columnNo).getNumericCellValue();
    }
}
