package org.yarlithub.yschool.service;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import org.yarlithub.yschool.student.core.NewStudent;
import java.util.Date;
import org.yarlithub.yschool.repository.model.obj.yschool.Student;
import java.util.List;

/**
 * Created with IntelliJ IDEA.
 * User: Jay Krish
 * Date: 9/22/13
 * Time: 9:05 AM
 * To change this template use File | Settings | File Templates.
 */

/**
 * TODO description
 */
@Service(value = "studentService")
public class StudentService {
    private static final Logger logger = LoggerFactory.getLogger(StudentService.class);
    @Transactional
    public boolean addStudent(String admission_No, String name, String fullname, String name_wt_initial, Date dob, String gender, String address) {
        NewStudent newStudent= new NewStudent();
        boolean success = newStudent.addNewStudent(admission_No, name, fullname, name_wt_initial, dob, gender, address);
        return success;
    }

    @Transactional
    public List<Student> getStudents() {
        NewStudent student=new NewStudent();
        return  student.getAllStudent();
    }

    @Transactional
    public void deleteStudent(Integer studentId)
    {
        NewStudent student=new NewStudent();
        student.deleteStudent(studentId);
    }

}
