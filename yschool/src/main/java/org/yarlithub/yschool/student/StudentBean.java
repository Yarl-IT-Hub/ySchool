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
package org.yarlithub.yschool.student;

import org.apache.log4j.Logger;
import org.yarlithub.yschool.repository.Student;

import javax.faces.bean.ManagedBean;
import javax.faces.bean.SessionScoped;
import java.io.Serializable;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;
import javax.faces.model.SelectItem;
import org.yarlithub.yschool.repository.House;
import org.yarlithub.yschool.repository.Status;


/**
 * $LastChangedDate$
 * $LastChangedBy$
 * $LastChangedRevision$
 */
@ManagedBean
@SessionScoped
public class StudentBean implements Serializable {

    private static final Logger logger = Logger.getLogger(StudentBean.class);

    public Student student;
    private Student selectedStudent;
    private List<Student> studentList;
    private List<SelectItem> genderList = Arrays.asList(new SelectItem("MALE"), new SelectItem("FEMALE"));
    private List<SelectItem> houseList = Arrays.asList();
    private List<SelectItem> studentStatusList = Arrays.asList(new SelectItem(Status.COMPLETED), new SelectItem(Status.PENDING), new SelectItem(Status.POSTPONED));
    private List<SelectItem> calenderYearList;
    

    public StudentBean() {
        logger.info("initiation a new student bean");
        student = new Student();
        selectedStudent = new Student();        
    }

    public void submit() {
        logger.info("saving student information [" + student + "]");
        student.save();
    }

    public Student getStudent() {
        return student;
    }

    public void setStudent(Student student) {
        this.student = student;
    }
    
    public String search(){
        logger.info("search for student by full name[" + student.getFullName() + "]");
        setStudentList(student.searchStudentByfullName(student.getFullName()));      
        return "searchStudentList";
    }

    /**
     * @return the searchStudent
     */
    public Student getSelectedStudent() {
        return selectedStudent;
    }

    /**
     * @param searchStudent the searchStudent to set
     */
    public void setSelectedStudent(Student selectedStudent) {
        this.selectedStudent = selectedStudent;
    }

    /**
     * @return the studentList
     */
    public List<Student> getStudentList() {
        return studentList;
    }

    /**
     * @param studentList the studentList to set
     */
    public void setStudentList(List<Student> studentList) {
        this.studentList = studentList;
    }

    /**
     * @return the genderList
     */
    public List<SelectItem> getGenderList() {
        return genderList;
    }

    /**
     * @param genderList the genderList to set
     */
    public void setGenderList(List<SelectItem> genderList) {
        this.genderList = genderList;
    }

    /**
     * @return the houseList
     */
    public List<SelectItem> getHouseList() {
        if(houseList == null){
           houseList = new ArrayList<>();
           houseList.add(new SelectItem(House.RED));
           houseList.add(new SelectItem(House.YELLOW));
           houseList.add(new SelectItem(House.BLUE));
        }
        return houseList;
    }

    /**
     * @param houseList the houseList to set
     */
    public void setHouseList(List<SelectItem> houseList) {
        this.houseList = houseList;
    }

    /**
     * @return the studentStatusList
     */
    public List<SelectItem> getStudentStatusList() {
        return studentStatusList;
    }

    /**
     * @param studentStatusList the studentStatusList to set
     */
    public void setStudentStatusList(List<SelectItem> studentStatusList) {
        this.studentStatusList = studentStatusList;
    }

    /**
     * @return the calenderYearList
     */
    public List<SelectItem> getCalenderYearList() {
        if(calenderYearList == null){
           calenderYearList = new ArrayList<>();
           calenderYearList.add(new SelectItem(2012));
           calenderYearList.add(new SelectItem(2011));
           calenderYearList.add(new SelectItem(2010));
        }       
        
        return calenderYearList;
    }

    /**
     * @param calenderYearList the calenderYearList to set
     */
    public void setCalenderYearList(List<SelectItem> calenderYearList) {
        this.calenderYearList = calenderYearList;
    }
}
