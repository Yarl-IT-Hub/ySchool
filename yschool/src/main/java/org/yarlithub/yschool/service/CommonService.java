package org.yarlithub.yschool.service;

import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import org.yarlithub.yschool.repository.model.obj.yschool.Staff;
import org.yarlithub.yschool.repository.model.obj.yschool.Student;
import org.yarlithub.yschool.staff.core.StaffHelper;
import org.yarlithub.yschool.student.core.StudentHelper;

import java.util.List;

/**
 * Created with IntelliJ IDEA.
 * User: Pirinthapan
 * Date: 4/5/14
 * Time: 8:28 PM
 * To change this template use File | Settings | File Templates.
 */

@Service(value = "commonService")
public class CommonService {

    @Transactional
    public List<Student> getStudentsNameLike(String name, int maxNo){
        StudentHelper studentHelper = new StudentHelper();
        return studentHelper.getStudentsNameLike(name, maxNo);
    }

    @Transactional
    public List<Staff> getStaffsNameLike(String name, int maxNo){
        StaffHelper staffHelper = new StaffHelper();
        return staffHelper.getStaffsNameLike(name, maxNo);
    }
}
