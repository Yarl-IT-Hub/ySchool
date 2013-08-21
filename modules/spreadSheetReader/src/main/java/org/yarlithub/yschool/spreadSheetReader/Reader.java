package org.yarlithub.yschool.spreadSheetReader;

/**
 * Created with IntelliJ IDEA.
 * User: jayrksih
 * Date: 8/13/13
 * Time: 11:12 PM
 * To change this template use File | Settings | File Templates.
 */
public interface Reader {
    /**
     * @param sheetNo
     * @return
     */
    boolean setSheet(int sheetNo);

    /**
     * @param rowNo
     * @return
     */
    boolean setRow(int rowNo);

    /**
     * @return
     */
    Integer getLastRowNumber();

    /**
     * @param columnNo
     * @return
     */
    String getStringCellValue(int columnNo);

    /**
     * Returns the integer value from the specified cell.
     * The cell should be in numeric format.
     *
     * @param columnNo
     * @return
     */
    int getNumericCellValue(int columnNo);
}
