package org.yarlithub.yschool.integration.service.student;

import org.yarlithub.yschool.repository.model.obj.yschool.Student;
import org.yarlithub.yschool.service.StudentService;

import java.util.Date;

/**
 * Created with IntelliJ IDEA.
 * User: jayrksih
 * Date: 1/22/14
 * Time: 11:04 PM
 * To change this template use File | Settings | File Templates.
 */
public class StudentServiceTestUtils {

    public static Student addNewStudent(StudentService studentService, String addmision_No, String name, String fullname, String name_wt_initial,
                                        Date dob, String gender, String address) {
        Student student = studentService.addNewStudent(addmision_No, name, fullname, name_wt_initial, dob, gender, address);
        return student;
    }

    public static Student saveOrUpdate(StudentService studentService, Student student, String addmision_No, String name, String fullname, String name_wt_initial,
                                       Date dob, String gender, String address) {

        student.setAdmissionNo(addmision_No);
        student.setName(name);
        student.setFullName(fullname);
        student.setNameWtInitial(name_wt_initial);
        student.setDob(dob);
        student.setGender(gender);
        student.setAddress(address);
        student = studentService.saveOrUpdate(student);
        return student;
    }
}
