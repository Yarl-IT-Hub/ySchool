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
import org.yarlithub.yschool.repository.House;
import org.yarlithub.yschool.repository.Status;
import org.yarlithub.yschool.repository.Student;
import org.yarlithub.yschool.util.InitialDateLoaderUtil;

import javax.annotation.PostConstruct;
import javax.faces.application.FacesMessage;
import javax.faces.bean.ManagedBean;
import javax.faces.bean.ManagedProperty;
import javax.faces.bean.SessionScoped;
import javax.faces.context.FacesContext;
import java.io.Serializable;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;


/**
 * $LastChangedDate$
 * $LastChangedBy$
 * $LastChangedRevision$
 */
@ManagedBean
@SessionScoped
public class StudentBean implements Serializable {

    private static final Logger logger = Logger.getLogger(StudentBean.class);

    @ManagedProperty(value = "#{initialDateLoaderUtil}")
    private InitialDateLoaderUtil initialDateLoaderUtil;

    public Student student;
    private List<Student> studentList;
    private List<String> genderList = Arrays.asList("Male", "Female");
    private List<String> houseList;
    private List<Status> studentStatusList = Arrays.asList(Status.COMPLETED, Status.PENDING, Status.POSTPONED);
    private List<String> calenderYearList,gradeList,divisionList,mediumList,subjectList;

    public StudentBean() {
        logger.info("initiation a new student bean");
        student = new Student();
    }

    @PostConstruct
    public void init() {
        logger.info("loading initial data [" + initialDateLoaderUtil + "]");
        loadInitData();
    }

    public void submit() {
        logger.info("saving student information [" + student + "]");
          FacesContext.getCurrentInstance().addMessage(null, 
                new FacesMessage(FacesMessage.SEVERITY_INFO, "New Student successfully inserted.", null));
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
    public List<String> getGenderList() {
        return genderList;
    }

    /**
     * @param genderList the genderList to set
     */
    public void setGenderList(List<String> genderList) {
        this.genderList = genderList;
    }

    /**
     * @return the houseList
     */
    public List<String> getHouseList() {
        if(houseList == null){
           houseList = new ArrayList<>();
           houseList.add(House.RED.name());
           houseList.add(House.YELLOW.name());
           houseList.add(House.BLUE.name());
        }
        return houseList;
    }

    /**
     * @param houseList the houseList to set
     */
    public void setHouseList(List<String> houseList) {
        this.houseList = houseList;
    }

    /**
     * @return the studentStatusList
     */
    public List<Status> getStudentStatusList() {
        return studentStatusList;
    }

    /**
     * @param studentStatusList the studentStatusList to set
     */
    public void setStudentStatusList(List<Status> studentStatusList) {
        this.studentStatusList = studentStatusList;
    }

    /**
     * @return the calenderYearList
     */
    public List<String> getCalenderYearList() {
        if(calenderYearList == null){
           calenderYearList = new ArrayList<>();
           calenderYearList.add("2012");
           calenderYearList.add("2011");
           calenderYearList.add("2010");
        }       
        
        return calenderYearList;
    }

    /**
     * @param calenderYearList the calenderYearList to set
     */
    public void setCalenderYearList(List<String> calenderYearList) {
        this.calenderYearList = calenderYearList;
    } 
    
    public void loadInitData(){
        System.out.println(initialDateLoaderUtil);
        setGradeList(initialDateLoaderUtil.loadData("PRE_GRADE"));
        setMediumList(initialDateLoaderUtil.loadData("PRE_MEDIUM"));
        setSubjectList(initialDateLoaderUtil.loadData("PRE_SUBJECT"));
        setDivisionList(initialDateLoaderUtil.loadData("PRE_DIVISION"));
    }

    /**
     * @return the gradeList
     */
    public List<String> getGradeList() {
        return gradeList;
    }

    /**
     * @param gradeList the gradeList to set
     */
    public void setGradeList(List<String> gradeList) {
        this.gradeList = gradeList;
    }

    /**
     * @return the divisionList
     */
    public List<String> getDivisionList() {
        return divisionList;
    }

    /**
     * @param divisionList the divisionList to set
     */
    public void setDivisionList(List<String> divisionList) {
        this.divisionList = divisionList;
    }

    /**
     * @return the mediumList
     */
    public List<String> getMediumList() {
        return mediumList;
    }

    /**
     * @param mediumList the mediumList to set
     */
    public void setMediumList(List<String> mediumList) {
        this.mediumList = mediumList;
    }

    /**
     * @return the subjectList
     */
    public List<String> getSubjectList() {
        return subjectList;
    }

    /**
     * @param subjectList the subjectList to set
     */
    public void setSubjectList(List<String> subjectList) {
        this.subjectList = subjectList;
    }

    public void setInitialDateLoaderUtil(InitialDateLoaderUtil initialDateLoaderUtil) {
        this.initialDateLoaderUtil = initialDateLoaderUtil;
    }
}
