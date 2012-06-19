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
package org.yarlithub.yschool.repository;

import org.joda.time.DateTime;
import org.yarlithub.yschool.repository.util.HibernateUtil;

import javax.persistence.Entity;
import javax.persistence.ManyToMany;
import javax.persistence.Table;
import java.util.List;


/**
 * $LastChangedDate$
 * $LastChangedBy$
 * $LastChangedRevision$
 */
@Entity
@Table(name = "student")
public class Student extends PersistentObject {

//    private String fname;
//    private String lname;
    private String admissionNumber;
    private String fullName;
    private String nameWithInitials;
    private DateTime dob;
    private String gender;
    private String house;
    private String studentStatus;
    private String userName;
    private String division;
    private DateTime registrationDate;
    private int calenderYear;

    @ManyToMany
    private List<Grade> grades;
    
    @ManyToMany
    private List<Subject> subject;


    public void save() {
        HibernateUtil.getCurrentSession().save(this);
    }

    public void update() {
        HibernateUtil.getCurrentSession().update(this);
    }

    public void delete() {
        HibernateUtil.getCurrentSession().delete(this);
    }
//    public List<Student> search() {    
//        return (List) HibernateUtil.getCurrentSession()
//                .createQuery("from Student  where lname = ?").setString(0, this.getLname().trim()).list();
//    }

    public List<Student> searchStudentByLastName(String studentsLastName) {
        return HibernateUtil.getCurrentSession().createQuery("from Student  where fullName = ?").setString(0, studentsLastName).list();
    }
    
//    public String getFname() {
//        return fname;
//    }
//
//    public void setFname(String fname) {
//        this.fname = fname;
//    }
//
//    public String getLname() {
//        return lname;
//    }
//
//    public void setLname(String lname) {
//        this.lname = lname;
//    }

    public DateTime getDob() {
        return dob;
    }

    public void setDob(DateTime dob) {
        this.dob = dob;
    }

    public DateTime getRegistrationDate() {
        return registrationDate;
    }

    public void setRegistrationDate(DateTime registrationDate) {
        this.registrationDate = registrationDate;
    }

    public List<Grade> getGrades() {
        return grades;
    }

    public void setGrades(List<Grade> grades) {
        this.setGrades(grades);
    }

    /**
     * @return the admissionNumber
     */
    public String getAdmissionNumber() {
        return admissionNumber;
    }

    /**
     * @param admissionNumber the admissionNumber to set
     */
    public void setAdmissionNumber(String admissionNumber) {
        this.admissionNumber = admissionNumber;
    }

    /**
     * @return the fullName
     */
    public String getFullName() {
        return fullName;
    }

    /**
     * @param fullName the fullName to set
     */
    public void setFullName(String fullName) {
        this.fullName = fullName;
    }

    /**
     * @return the nameWithInitials
     */
    public String getNameWithInitials() {
        return nameWithInitials;
    }

    /**
     * @param nameWithInitials the nameWithInitials to set
     */
    public void setNameWithInitials(String nameWithInitials) {
        this.nameWithInitials = nameWithInitials;
    }

    /**
     * @return the gender
     */
    public String getGender() {
        return gender;
    }

    /**
     * @param gender the gender to set
     */
    public void setGender(String gender) {
        this.gender = gender;
    }

    /**
     * @return the house
     */
    public String getHouse() {
        return house;
    }

    /**
     * @param house the house to set
     */
    public void setHouse(String house) {
        this.house = house;
    }

    /**
     * @return the studentStatus
     */
    public String getStudentStatus() {
        return studentStatus;
    }

    /**
     * @param studentStatus the studentStatus to set
     */
    public void setStudentStatus(String studentStatus) {
        this.studentStatus = studentStatus;
    }

    /**
     * @return the userName
     */
    public String getUserName() {
        return userName;
    }

    /**
     * @param userName the userName to set
     */
    public void setUserName(String userName) {
        this.userName = userName;
    }

    /**
     * @return the division
     */
    public String getDivision() {
        return division;
    }

    /**
     * @param division the division to set
     */
    public void setDivision(String division) {
        this.division = division;
    }

    /**
     * @return the subject
     */
    public List<Subject> getSubject() {
        return subject;
    }

    /**
     * @param subject the subject to set
     */
    public void setSubject(List<Subject> subject) {
        this.subject = subject;
    }

    /**
     * @return the calenderYear
     */
    public int getCalenderYear() {
        return calenderYear;
    }

    /**
     * @param calenderYear the calenderYear to set
     */
    public void setCalenderYear(int calenderYear) {
        this.calenderYear = calenderYear;
    }


}
