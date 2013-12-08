package org.yarlithub.yschool.service;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import org.yarlithub.yschool.staff.core.NewStaff;
import org.yarlithub.yschool.repository.model.obj.yschool.Staff;

import java.util.List;
/**
 * Created with IntelliJ IDEA.
 * User: Jay Krish
 * Date: 9/22/13
 * Time: 9:05 AM
 * To change this template use File | Settings | File Templates.
 */


@Service(value = "staffService")
public class StaffService {
    private static final Logger logger = LoggerFactory.getLogger(StaffService.class);

    @Transactional
    public boolean addStaff(String staffID, String name, String fullname) {
        NewStaff newStaff= new NewStaff();
        boolean success = newStaff.addNewStaff(staffID, name, fullname);
        return success;
    }

    @Transactional
    public List<Staff> getStaff(){
        NewStaff newStaff= new NewStaff();
        return newStaff.getAllStaff();

    }

}
