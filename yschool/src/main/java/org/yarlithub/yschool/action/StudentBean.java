/*
 *   (C) Copyright 2012-2013 hSenid Software International (Pvt) Limited.
 *   All Rights Reserved.
 *
 *   These materials are unpublished, proprietary, confidential source code of
 *   hSenid Software International (Pvt) Limited and constitute a TRADE SECRET
 *   of hSenid Software International (Pvt) Limited.
 *
 *   hSenid Software International (Pvt) Limited retains all title to and intellectual
 *   property rights in these materials.
 *
 */
package org.yarlithub.yschool.action;

import org.apache.log4j.Logger;
import org.yarlithub.yschool.repository.Student;

import javax.annotation.PostConstruct;
import javax.faces.bean.ManagedBean;
import javax.faces.bean.ManagedProperty;
import javax.faces.bean.RequestScoped;
import javax.faces.bean.SessionScoped;
import java.io.Serializable;

/**
 * $LastChangedDate$
 * $LastChangedBy$
 * $LastChangedRevision$
 */
//@ManagedBean(name="studentBean")
@ManagedBean
@SessionScoped
public class StudentBean implements Serializable {

    private static final Logger logger = Logger.getLogger(StudentBean.class);

//    @ManagedProperty("student")
    public Student student = new Student();
    private Student searchStudent;

    public StudentBean() {
        student = new Student();
    }

    //    @PostConstruct
//    private void init() {
//        student = new Student();
//    }

    public void submit() {
        student.save();
    }

    public Student getStudent() {
        return student;
    }

    public void setStudent(Student student) {
        this.student = student;
    }
    
    public void search(){
        setSearchStudent((Student) student.search(student));        
    }

    /**
     * @return the searchStudent
     */
    public Student getSearchStudent() {
        return searchStudent;
    }

    /**
     * @param searchStudent the searchStudent to set
     */
    public void setSearchStudent(Student searchStudent) {
        this.searchStudent = searchStudent;
    }
}
