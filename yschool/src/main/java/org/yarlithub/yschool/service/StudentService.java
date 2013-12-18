package org.yarlithub.yschool.service;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.hibernate.Hibernate;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.Date;
import org.yarlithub.yschool.repository.model.obj.yschool.Student;
import org.yarlithub.yschool.repository.model.obj.yschool.Classroom;
import org.yarlithub.yschool.student.core.StudentCreator;
import org.yarlithub.yschool.student.core.StudentHelper;

import java.util.Iterator;
import java.util.List;


/**
 * TODO description
 */
@Service(value = "studentService")
public class StudentService {
    private static final Logger logger = LoggerFactory.getLogger(StudentService.class);
    @Transactional
    public boolean addStudent(String addmision_No, String name, String fullname, String name_wt_initial, Date dob, String gender, String address) {
        StudentCreator studentCreator=new StudentCreator();
        boolean success = studentCreator.addNewStudent(addmision_No, name, fullname, name_wt_initial, dob, gender, address);
        return success;
    }

    @Transactional
    public List<Classroom> getCurrentClasses(int grade){
        StudentHelper student=new StudentHelper();
        List<Classroom> classroomList = student.getCurrentClasses(grade);
        Iterator<Classroom> classroomIterator = classroomList.iterator();
        while (classroomIterator.hasNext()){
            Classroom classroom = classroomIterator.next();
            Hibernate.initialize(classroom.getGradeIdgrade());
            Hibernate.initialize(classroom.getDivisionIddivision());
        }
        return classroomList;
    }

    @Transactional
    public List<Student> getStudent(){
        StudentHelper studentHelper=new StudentHelper();
         return studentHelper.listAllStudent();
    }

    @Transactional
    public List<Student> getClassroomStudent(Classroom classroom) {
        StudentHelper studentHelper=new StudentHelper();
        List<Student> classroomStudentList = studentHelper.getClassroomStudent(classroom);
        return classroomStudentList;
    }

    @Transactional
    public Student saveOrUpdate(Student student){
        StudentHelper studentHelper=new StudentHelper();
        studentHelper.saveOrUpdate(student);
        return student;
    }

    @Transactional
    public Student studentDelete(Student student){
        StudentHelper studentHelper=new StudentHelper();
        studentHelper.studentDelete(student);
        return student;
    }
}

