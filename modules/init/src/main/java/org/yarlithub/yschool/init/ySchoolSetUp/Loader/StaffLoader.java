package org.yarlithub.yschool.init.ySchoolSetUp.Loader;

import org.hibernate.SQLQuery;
import org.yarlithub.yschool.init.dataAccess.InitDBQueries;
import org.yarlithub.yschool.repository.services.data.DataLayerYschool;
import org.yarlithub.yschool.repository.services.data.DataLayerYschoolImpl;
import org.yarlithub.yschool.spreadSheetHandler.Reader;

/**
 * Created with IntelliJ IDEA.
 * User: Jay Krish
 * Date: 8/13/13
 * Time: 11:16 PM
 * To change this template use File | Settings | File Templates.
 */
public class StaffLoader {

    public boolean load(Reader reader) {

        /**
         * In initialization document 2th sheet is staff information.
         */
        reader.setSheet(1);
        DataLayerYschool DataLayerYschool = DataLayerYschoolImpl.getInstance();

        for (int i = 1; i <= reader.getLastRowNumber(); i++) {
            reader.setRow(i);
            int id = reader.getNumericCellValue(0);
            String name = reader.getStringCellValue(1);
            String fullName = reader.getStringCellValue(2);

            SQLQuery insertQuery = DataLayerYschool.createSQLQuery(InitDBQueries.STAFF_INIT_SQL);
            insertQuery.setParameter("id", id);
            insertQuery.setParameter("name", name);
            insertQuery.setParameter("full_name", fullName);
            insertQuery.setParameter("photo", null);
            int result = insertQuery.executeUpdate();

        }
        return true;
    }
}
