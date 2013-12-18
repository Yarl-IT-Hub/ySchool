package org.yarlithub.yschool.staff.core;

import org.hibernate.Criteria;
import org.yarlithub.yschool.repository.model.obj.yschool.Staff;
import org.yarlithub.yschool.repository.services.data.DataLayerYschool;
import org.yarlithub.yschool.repository.services.data.DataLayerYschoolImpl;

import java.util.List;

/**
 * Created with IntelliJ IDEA.
 * User: Amaaniy
 * Date: 12/16/13
 * Time: 12:48 PM
 * To change this template use File | Settings | File Templates.
 */
public class StaffHelper {

    DataLayerYschool dataLayerYschool = DataLayerYschoolImpl.getInstance();

    /**
     *
     * @return
     */
    public List<Staff> listAllStaffs() {
        Criteria staffCriteria = dataLayerYschool.createCriteria(Staff.class);
        return staffCriteria.list();

    }
    public Staff saveOrUpdate(Staff staff) {
        dataLayerYschool.saveOrUpdate(staff);
        return staff;
    }

}
