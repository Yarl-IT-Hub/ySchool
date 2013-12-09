package org.yarlithub.yschool.service;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.hibernate.Hibernate;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import org.yarlithub.yschool.student.core.NewStudent;
import java.util.Date;
import org.yarlithub.yschool.repository.model.obj.yschool.Student;
import org.yarlithub.yschool.repository.model.obj.yschool.Classroom;
import org.yarlithub.yschool.student.core.StudentHelper;

import java.util.List;


/**
 * TODO description
 */
@Service(value = "studentService")
public class StudentService {
    private static final Logger logger = LoggerFactory.getLogger(StudentService.class);
    @Transactional
    public boolean addStudent(String addmision_No, String name, String fullname, String name_wt_initial, Date dob, String gender, String address) {
        NewStudent newStudent= new NewStudent();
        boolean success = newStudent.addNewStudent(addmision_No, name, fullname, name_wt_initial, dob, gender, address);
        return success;
    }



    @Transactional
    public List<Classroom> getCurrentClasses(int grade){
        StudentHelper student=new StudentHelper();
        return student.getCurrentClasses(grade);
    }

}

