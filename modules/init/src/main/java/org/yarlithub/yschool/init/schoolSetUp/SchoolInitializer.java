package org.yarlithub.yschool.init.schoolSetUp;

import org.hibernate.SQLQuery;
import org.yarlithub.yschool.init.dataAccess.InitDBQueries;
import org.yarlithub.yschool.repository.services.data.DataLayerYschool;
import org.yarlithub.yschool.repository.services.data.DataLayerYschoolImpl;

/**
 * Created with IntelliJ IDEA.
 * User: Jay Krish
 * Date: 8/8/13
 * Time: 4:49 PM
 * To change this template use File | Settings | File Templates.
 */
public class SchoolInitializer {
    public SchoolInitializer() {
    }

    /**
     * TODO jaykrish
     *
     * @param schoolName
     * @param schoolAddress
     * @param schoolZone
     * @param schoolDistrict
     * @param schoolProvience
     * @return
     */
    public boolean initializeSchool(String schoolName, String schoolAddress, String schoolZone, String schoolDistrict, String schoolProvience) {

        DataLayerYschool DataLayerYschool = DataLayerYschoolImpl.getInstance();

        SQLQuery insertQuery = DataLayerYschool.createSQLQuery(InitDBQueries.INSERT_SCHOOL);
        insertQuery.setParameter("name", schoolName);
        insertQuery.setParameter("address", schoolAddress);
        insertQuery.setParameter("zone", schoolZone);
        insertQuery.setParameter("district", schoolDistrict);
        insertQuery.setParameter("province", schoolProvience);
        int result = insertQuery.executeUpdate();

        return true;  //To change body of created methods use File | Settings | File Templates.
    }
}
