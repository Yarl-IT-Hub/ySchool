package org.yarlithub.yschool.web.student;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.repository.model.obj.yschool.Student;
import org.yarlithub.yschool.service.StudentService;

import javax.faces.bean.ManagedBean;
import javax.faces.model.DataModel;
import javax.faces.model.ListDataModel;
import java.io.Serializable;
import java.util.Date;


/**
 * $LastChangedDate$
 * $LastChangedBy$
 * $LastChangedRevision$
 */
@ManagedBean
@Scope(value = "session")
@Controller
public class StudentBean implements Serializable {

    private String admission_No;
    private String name;
    private String fullname;
    private String name_wt_initial;
    private Date dob;
    private String gender;
    private String address;
    private Student student;
    private String searchKey = null;
    private DataModel<Student>studentsSearchResultAjax;
    private String page = "studentSearch";

    @Autowired
    private StudentService studentService;

    @Autowired
    private StudentController studentController;

    public StudentBean() {
        super();
        studentsSearchResultAjax = new ListDataModel<Student>();
    }

    public String getAdmission_No() {
        return admission_No;
    }

    public void setAdmission_No(String admission_No) {
        this.admission_No = admission_No;
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

    public Student getStudent() {
        return student;
    }

    public void setStudent(Student student) {
        this.student = student;
    }

    public String getSearchKey() {
        return searchKey;
    }

    public void setSearchKey(String searchKey) {
        this.searchKey = searchKey;
    }

    public DataModel<Student> getStudentsSearchResultAjax() {
        return studentsSearchResultAjax;
    }

    public void setStudentsSearchResultAjax(DataModel<Student> studentsSearchResultAjax) {
        this.studentsSearchResultAjax = studentsSearchResultAjax;
    }


    public String viewStudentAjax(){
        studentsSearchResultAjax = new ListDataModel<Student>(studentService.getStudentsNameLike(searchKey,10));
        setStudent(studentsSearchResultAjax.getRowData());
        studentController.setStudent(student);
        return "viewStudentAjax";
    }

    public String getPage() {
        return page;
    }

    public void setPage(String page) {
        this.page = page;
    }

    public String addStudent() {

        boolean setupResult = studentService.addStudent(admission_No, name, fullname, name_wt_initial, dob, gender, address);
        if (setupResult) {
            return "AddStudentSuccess";
        }
           return "AddStudentFailed";
    }
}
