package org.yarlithub.yschool.web.student;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.service.StudentService;
import org.yarlithub.yschool.repository.model.obj.yschool.Student;


import javax.faces.bean.ManagedBean;
import java.io.Serializable;
import java.lang.String;
import java.util.Date;
import java.util.List;
import javax.faces.model.DataModel;
import javax.faces.model.ListDataModel;


/**
 * $LastChangedDate$
 * $LastChangedBy$
 * $LastChangedRevision$
 */
@ManagedBean
@Scope(value = "session")
@Controller
public class StudentBean implements Serializable {

    private String addmision_No;
    private String name;
    private String fullname;
    private String name_wt_initial;
    private Date dob;
    private String gender;
    private String address;
    private DataModel students;

    @Autowired
    private StudentService studentService;

    public String getAddmision_No() {
        return addmision_No;
    }

    public void setAddmision_No(String addmision_No) {
        this.addmision_No = addmision_No;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getFullname() {
        return fullname;
    }

    public void setFullname(String fullname) {
        this.fullname = fullname;
    }

    public String getName_wt_initial() {
        return name_wt_initial;
    }

    public void setName_wt_initial(String name_wt_initial) {
        this.name_wt_initial = name_wt_initial;
    }

    public Date getDob() {
        return dob;
    }

    public void setDob(Date dob) {
        this.dob = dob;
    }

    public String getGender() {
        return gender;
    }

    public void setGender(String gender) {
        this.gender = gender;
    }

    public String getAddress() {
        return address;
    }

    public void setAddress(String address) {
        this.address = address;
    }

    public DataModel getStudents() {
        return students;
    }

    public void setStudents(DataModel students) {
        this.students = students;
    }

    public boolean preloadstudents() {
        students = new ListDataModel(studentService.getStudents());
        this.setStudents(students);
        return true;
    }

    public String preloaddelete(Integer studentId)
    {
        studentService.deleteStudent(studentId);
        return "delete_this_student";
    }

    public String addStudent() {

        boolean setupResult = studentService.addStudent(addmision_No, name, fullname, name_wt_initial, dob, gender, address);
        if (setupResult) {
            return "AddStudentSuccess";
        }
           return "AddStudentFailed";
    }
}
