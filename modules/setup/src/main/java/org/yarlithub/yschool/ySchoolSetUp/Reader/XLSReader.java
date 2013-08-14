package org.yarlithub.yschool.ySchoolSetUp.Reader;

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
    public Row getRow(int rowNo) {
        return sheet.getRow(rowNo);
    }

    @Override
    public Integer getLastRowNumber() {
        return sheet.getLastRowNum();
    }
}
