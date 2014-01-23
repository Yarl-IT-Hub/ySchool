package org.yarlithub.yschool.service;

import org.hibernate.Hibernate;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import org.yarlithub.yschool.repository.model.obj.yschool.Classroom;
import org.yarlithub.yschool.repository.model.obj.yschool.Student;
import org.yarlithub.yschool.student.core.StudentCreator;
import org.yarlithub.yschool.student.core.StudentHelper;

import java.util.Date;
import java.util.Iterator;
import java.util.List;


/**
 * TODO description
 */
@Service(value = "studentService")
public class StudentService {
    private static final Logger logger = LoggerFactory.getLogger(StudentService.class);

    /**
     * Add new student to yschool database.
     * @param addmision_No String
     * @param name String
     * @param fullname String
     * @param name_wt_initial  String
     * @param dob  java.util.date
     * @param gender String
     * @param address String
     * @return org.yarlithub.yschool.repository.model.obj.yschool.Student object with positive student id.
     */
    @Transactional
    public Student addNewStudent(String addmision_No, String name, String fullname, String name_wt_initial, Date dob, String gender, String address) {
        StudentCreator studentCreator=new StudentCreator();
        Student student = studentCreator.addNewStudent(addmision_No, name, fullname, name_wt_initial, dob, gender, address);
        return student;
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
         List<Student> studentList= studentHelper.listAllStudent();
         Iterator<Student> studentIterator = studentList.iterator();
        while (studentIterator.hasNext()){
            Student student=studentIterator.next();
            Hibernate.initialize(student.getStudentGeneralexamProfiles());
        }
        return studentList;
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
    public Student deleteStudent(Student student){
        StudentHelper studentHelper=new StudentHelper();
        student=studentHelper.studentDelete(student);
        return student;
    }
    @Transactional
    public List<Student> getStudentsNameLike(String regx, int maxNo) {
        StudentHelper studentHelper = new StudentHelper();
        return studentHelper.getStudentsNameLike(regx,maxNo);

    }
}

