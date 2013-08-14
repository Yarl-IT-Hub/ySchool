package org.yarlithub.yschool.ySchoolSetUp.Reader;

import java.io.FileInputStream;
import java.io.IOException;

/**
 * Created with IntelliJ IDEA.
 * User: jayrksih
 * Date: 8/13/13
 * Time: 11:13 PM
 * To change this template use File | Settings | File Templates.
 */
public class ReaderFactory {


    public Reader getspreadSheetReader(FileInputStream fileInputStream, String fileName) throws IOException {

        Reader spreadSheetReader = null;
        String spreadSheetExtension = this.getExtension(fileName);

        if (spreadSheetExtension.contentEquals("xls")) {
            spreadSheetReader = new XLSReader(fileInputStream);

        } else if (spreadSheetExtension.contentEquals("xlsx")) {
              spreadSheetReader = new XLSXReader(fileInputStream);
        }
        return spreadSheetReader;
    }

    private String getExtension(String fileName) {
        String extension = null;
        try {
            int i = fileName.lastIndexOf('.');
            if (i > 0 && i < fileName.length() - 1) {
                extension = fileName.substring(i + 1).toLowerCase();
            }

        } catch (NullPointerException ex) {
            System.out.println("The file name is null! " + ex);
        } finally {
            return extension;
        }
    }
}
