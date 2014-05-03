package org.yarlithub.yschool.commons.api;

import org.hibernate.Criteria;
import org.yarlithub.yschool.repository.model.obj.yschool.Division;
import org.yarlithub.yschool.repository.model.obj.yschool.Grade;
import org.yarlithub.yschool.repository.services.data.DataLayerYschool;
import org.yarlithub.yschool.repository.services.data.DataLayerYschoolImpl;

import java.util.List;

/**
 * Created with IntelliJ IDEA.
 * User: Jay Krish
 * Date: 10/25/13
 * Time: 12:20 AM
 * To change this template use File | Settings | File Templates.
 */


public class CommonsHelper {

    static DataLayerYschool dataLayerYschool = DataLayerYschoolImpl.getInstance();

    /**
     * Returns all grade entries.
     *
     * @return List of org.yarlithub.yschool.repository.model.obj.yschool.Grade;
     */
    public static List<Grade> getAllGrades() {
        Criteria gradeCriteria = dataLayerYschool.createCriteria(Grade.class);
        return gradeCriteria.list();
    }

    /**
     * Returns all division entries.
     *
     * @return List of org.yarlithub.yschool.repository.model.obj.yschool.Division;
     */
    public static List<Division> getAllDivisions() {
        Criteria divisionCriteria = dataLayerYschool.createCriteria(Division.class);
        return divisionCriteria.list();
    }

}
