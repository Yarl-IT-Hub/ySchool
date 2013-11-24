package org.yarlithub.yschool.staff.core;

import org.yarlithub.yschool.repository.factories.yschool.YschoolDataPoolFactory;
import org.yarlithub.yschool.repository.model.obj.yschool.Staff;
import org.yarlithub.yschool.repository.services.data.DataLayerYschool;
import org.yarlithub.yschool.repository.services.data.DataLayerYschoolImpl;
/**
 * Created with IntelliJ IDEA.
 * User: Jay Krish
 * Date: 9/22/13
 * Time: 9:05 AM
 * To change this template use File | Settings | File Templates.
 */
public class NewStaff {

    DataLayerYschool dataLayerYschool = DataLayerYschoolImpl.getInstance();

    public boolean addNewStaff(String staffID, String name, String fullname) {

        Staff staff = YschoolDataPoolFactory.getStaff();
        staff.setStaffid(staffID);
        staff.setName(name);
        staff.setFullName(fullname);

        //TODO: hage to get bytestream to send database.
        //student.setPhoto(photo);



        dataLayerYschool.save(staff);
        dataLayerYschool.flushSession();
        //TODO: save method does not indicates/returns success/failure
        return true;
    }

}
