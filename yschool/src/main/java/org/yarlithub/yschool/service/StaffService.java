package org.yarlithub.yschool.service;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import org.yarlithub.yschool.repository.model.obj.yschool.Staff;
import org.yarlithub.yschool.staff.core.StaffCreator;
import org.yarlithub.yschool.staff.core.StaffHelper;

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
        StaffCreator staffCreator = new StaffCreator();
        boolean success = staffCreator.addNewStaff(staffID, name, fullname);
        return success;
        //StaffCreator staffCreator=new StaffCreator();

    }

    @Transactional
    public List<Staff> getStaff() {
        StaffHelper staffHelper = new StaffHelper();

        return staffHelper.listAllStaffs();

    }

    @Transactional
    public Staff saveOrUpdate(Staff staff) {
        StaffHelper staffHelper = new StaffHelper();
        staffHelper.saveOrUpdate(staff);
        return staff;
    }

    @Transactional
    public List<Staff> getStaffsNameLike(String regx, int maxNo) {
        StaffHelper staffHelper = new StaffHelper();
        return staffHelper.getStaffsNameLike(regx, maxNo);
    }

    }
