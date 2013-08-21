package org.yarlithub.yschool.setup.ySchoolSetUp.Loader;

import org.hibernate.SQLQuery;
import org.yarlithub.yschool.spreadSheetReader.Reader;
import org.yarlithub.yschool.repository.services.data.DataLayerYschool;
import org.yarlithub.yschool.repository.services.data.DataLayerYschoolImpl;
import org.yarlithub.yschool.setup.dataAccess.SetUpDBQueries;

/**
 * Created with IntelliJ IDEA.
 * User: jayrksih
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

            SQLQuery insertQuery = DataLayerYschool.createSQLQuery(SetUpDBQueries.STAFF_INIT_SQL);
            insertQuery.setParameter("id", id);
            insertQuery.setParameter("name", name);
            insertQuery.setParameter("full_name", fullName);
            insertQuery.setParameter("photo", null);
            int result = insertQuery.executeUpdate();

        }
        return true;
    }
}
