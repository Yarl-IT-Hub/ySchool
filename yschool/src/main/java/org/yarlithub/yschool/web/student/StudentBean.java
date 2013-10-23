package org.yarlithub.yschool.web.student;


import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import javax.annotation.PostConstruct;
import javax.faces.application.FacesMessage;
import javax.faces.bean.ManagedBean;
import javax.faces.bean.ManagedProperty;
import javax.faces.bean.SessionScoped;
import javax.faces.context.FacesContext;
import java.io.Serializable;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Date;
import java.util.List;
import org.apache.myfaces.custom.fileupload.UploadedFile;
import org.yarlithub.yschool.service.SetupService;
import org.yarlithub.yschool.service.StudentService;
import org.springframework.beans.factory.annotation.Autowired;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 *
 * @author HP
 */
@ManagedBean
@SessionScoped
@Controller
public class StudentBean {

    private String addmision_No;
    private String name;
    private String fullname;
    private String name_wt_initial;
    private Date dob;
    private String gender;
    private String address;
    private UploadedFile photo;

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
    public UploadedFile getphoto() {
        return photo;
    }

    public void setPhoto(UploadedFile photo) {
        this.photo = photo;
    }

    public String addStudent()  {

        boolean setupResult = studentService.addStudent(addmision_No, name, fullname, name_wt_initial, dob, gender, address);
        if (setupResult) {
            return "AddStudentSuccess";
        }
        return "AddStudentFailed";
    }

}
