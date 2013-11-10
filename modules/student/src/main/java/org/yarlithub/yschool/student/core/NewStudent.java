package org.yarlithub.yschool.student.core;

import org.hibernate.Criteria;
import org.yarlithub.yschool.repository.factories.yschool.YschoolDataPoolFactory;
import org.yarlithub.yschool.repository.model.obj.yschool.Student;
import org.yarlithub.yschool.repository.services.data.DataLayerYschool;
import org.yarlithub.yschool.repository.services.data.DataLayerYschoolImpl;

import java.util.Date;
import java.util.List;


public class NewStudent {
    DataLayerYschool dataLayerYschool = DataLayerYschoolImpl.getInstance();

    public boolean addNewStudent(String addmision_No, String name, String fullname, String name_wt_initial, Date dob, String gender, String address) {

        Student student = YschoolDataPoolFactory.getStudent();
        student.setAddmisionNo(addmision_No);
        student.setName(name);
        student.setFullName(fullname);
        student.setNameWtInitial(name_wt_initial);
        student.setDob(dob);
        student.setGender(gender);
        student.setAddress(address);
        //TODO: hage to get bytestream to send database.
        //student.setPhoto(photo);
        dataLayerYschool.save(student);
        dataLayerYschool.flushSession();
        //TODO: save method does not indicates/returns success/failure
        return true;
    }

    public List<Student> getAllStudent()
    {
         return dataLayerYschool.getCurrentSession().createCriteria(Student.class,"from student").list();
    }

    public void deleteStudent(Integer StudentId) {
        Student student = (Student) dataLayerYschool.getCurrentSession().load(
                Student.class, StudentId);
        if (null != student) {
            this.dataLayerYschool.getCurrentSession().delete(student);
        }
    }
}
