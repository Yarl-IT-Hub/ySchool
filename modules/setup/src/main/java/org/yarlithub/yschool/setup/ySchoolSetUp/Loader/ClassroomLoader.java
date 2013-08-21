package org.yarlithub.yschool.setup.ySchoolSetUp.Loader;

import org.hibernate.SQLQuery;
import org.yarlithub.yschool.Reader.Reader;
import org.yarlithub.yschool.services.data.DataLayerYschool;
import org.yarlithub.yschool.services.data.DataLayerYschoolImpl;
import org.yarlithub.yschool.setup.dataAccess.SetUpDBQueries;


/**
 * Created with IntelliJ IDEA.
 * User: jayrksih
 * Date: 8/13/13
 * Time: 11:16 PM
 * To change this template use File | Settings | File Templates.
 */
public class ClassroomLoader {

    public boolean load(Reader reader) {

        /**
         * In initialization document 4th scheet is class information.
         */
        reader.setSheet(3);
        DataLayerYschool DataLayerYschool = DataLayerYschoolImpl.getInstance();

        for (int i = 1; i <= reader.getLastRowNumber(); i++) {
            reader.setRow(i);

            int grade = reader.getNumericCellValue(0);
            String division = reader.getStringCellValue(1);
            int year = reader.getNumericCellValue(2);

            SQLQuery insertQuery = DataLayerYschool.createSQLQuery(SetUpDBQueries.CLASS_INIT_SQL);
            insertQuery.setParameter("year", String.valueOf(year));
            insertQuery.setParameter("grade", grade);
            insertQuery.setParameter("division", division);
            insertQuery.setParameter("Section_idSection", null);
            int result = insertQuery.executeUpdate();


        }
        return true;
    }
}
