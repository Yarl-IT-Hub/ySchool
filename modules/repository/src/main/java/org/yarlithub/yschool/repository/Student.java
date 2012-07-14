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

import java.util.Date;
import java.util.List;
import javax.persistence.Entity;
import javax.persistence.ManyToMany;
import javax.persistence.Table;
import org.joda.time.DateTime;
import org.yarlithub.yschool.repository.util.HibernateUtil;


/**
 * $LastChangedDate$
 * $LastChangedBy$
 * $LastChangedRevision$
 */
@Entity
@Table(name = "student")
public class Student extends PersistentObject {


    private String admissionNumber;
    private String fullName;
    private String nameWithInitials;
    private Date dob;
    private String addressLine1; // new
    private String addressLine2; // new
    private String city;
    private String country;
    private String tempaddressLine1;
    private String tempaddressLine2;
    private String tempcountry;
    
    private String gender;
    private String house;
    private String studentStatus;
    private String userName;
    private String password;
    private String division;//TODO
    private DateTime registrationDate;
    private int calenderYear;
    //private int grade;
    

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


    public List<Student> searchStudentByfullName(String studentsfullName) {
        return HibernateUtil.getCurrentSession().createQuery("from Student  where fullName = ?").setString(0, studentsfullName).list();
    }

    public Date getDob() {
        return dob;
    }

    public void setDob(Date dob) {
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

    
    public String getAddressLine1() {
        return addressLine1;
    }

    public void setAddressLine1(String addressLine1) {
        this.addressLine1 = addressLine1;
    }

    public String getAddressLine2() {
        return addressLine2;
    }

    public void setAddressLine2(String addressLine2) {
        this.addressLine2 = addressLine2;
    }

    public String getCity() {
        return city;
    }

    public void setCity(String city) {
        this.city = city;
    }

    public String getCountry() {
        return country;
    }

    public void setCountry(String country) {
        this.country = country;
    }

    public String getTempaddressLine1() {
        return tempaddressLine1;
    }

    public void setTempaddressLine1(String tempaddressLine1) {
        this.tempaddressLine1 = tempaddressLine1;
    }

    public String getTempaddressLine2() {
        return tempaddressLine2;
    }

    public void setTempaddressLine2(String tempaddressLine2) {
        this.tempaddressLine2 = tempaddressLine2;
    }

    public String getTempcountry() {
        return tempcountry;
    }

    public void setTempcountry(String tempcountry) {
        this.tempcountry = tempcountry;
    }

    public String getPassword() {
        return password;
    }

    public void setPassword(String password) {
        this.password = password;
    }

   
    

    @Override
    public String toString() {
        final StringBuilder sb = new StringBuilder();
        sb.append("Student");
        sb.append("{admissionNumber='").append(admissionNumber).append('\'');
        sb.append(", fullName='").append(fullName).append('\'');
        sb.append(", nameWithInitials='").append(nameWithInitials).append('\'');
        sb.append(", dob=").append(dob);
        sb.append(", gender='").append(gender).append('\'');
        sb.append(", house='").append(house).append('\'');
        sb.append(", studentStatus='").append(studentStatus).append('\'');
        sb.append(", userName='").append(userName).append('\'');
        sb.append(", division='").append(division).append('\'');
        sb.append(", registrationDate=").append(registrationDate);
        sb.append(", calenderYear=").append(calenderYear);
        sb.append(", grades=").append(grades);
        sb.append(", subject=").append(subject);
        sb.append('}');
        return sb.toString();
    }


}
