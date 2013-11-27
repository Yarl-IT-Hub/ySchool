package org.yarlithub.yschool.student.core;

import org.hibernate.Criteria;
import org.hibernate.criterion.Restrictions;
import org.yarlithub.yschool.repository.model.obj.yschool.Student;
import org.yarlithub.yschool.repository.model.obj.yschool.StudentGeneralexamProfile;
import org.yarlithub.yschool.repository.services.data.DataLayerYschool;
import org.yarlithub.yschool.repository.services.data.DataLayerYschoolImpl;

import java.util.ArrayList;
import java.util.List;

/**
 * Created with IntelliJ IDEA.
 * User: kana
 * Date: 11/17/13
 * Time: 11:56 AM
 * To change this template use File | Settings | File Templates.
 */
public class StudentHelper {

    DataLayerYschool dataLayerYschool = DataLayerYschoolImpl.getInstance();

    /**
     * @param id
     * @return
     */
    public Student getStudentByID(int id) {

        Student student = dataLayerYschool.getStudent(id);
        return student;
    }

    /**
     * Returns the YSchool Repository Student Object, given the admission number
     * (should be unique), if duplicates exist then NULL is returned
     *
     * @param admissionNo of Student
     * @return org.yarlithub.yschool.repository.model.obj.yschool.Student
     */
    public Student getStudentByAdmissionNo(int admissionNo) {
        Student student;
        List<Student> studentList = new ArrayList<>();
        Criteria studentCR = dataLayerYschool.createCriteria(Student.class);
        studentCR.add(Restrictions.eq("admissionNo", String.valueOf(admissionNo)));                        //String.valueOf(admissionNo)
        studentList = studentCR.list();
        /*The admission is unique thus the number of students retured should be one */
        if (studentList.size() == 1) {
            student = studentList.get(0);
            return student;
        }
        return null;
    }

    public StudentGeneralexamProfile getStudentProfileViaStudentID(Student student) {
        Criteria studentGeneralExamProfilesCR = dataLayerYschool.createCriteria(StudentGeneralexamProfile.class);

        studentGeneralExamProfilesCR.add(Restrictions.eq("studentIdstudent", student));

        List<StudentGeneralexamProfile> lt =  studentGeneralExamProfilesCR.list();
        return lt.get(0);

    }
}


