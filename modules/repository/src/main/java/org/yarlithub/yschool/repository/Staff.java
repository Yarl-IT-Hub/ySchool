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
import javax.persistence.Table;
import org.yarlithub.yschool.repository.util.HibernateUtil;

/**
 *
 * @author Vanaja
 */
@Entity
@Table(name = "staff")
public class Staff extends PersistentObject {
    
    private String staffId;
    private String fullName;
    private Date dob;
    private String addressLine1; 
    private String addressLine2;
    private String city;
    private String country;
    private String tempaddressLine1;
    private String tempaddressLine2;
    private String tempcountry;
    private String type;
    private String qualification;
    private String email;
    private String tp;
    private String gender;
    private String userName;
    private String password;
    
    
    public void save() {
        HibernateUtil.getCurrentSession().save(this);
    }

    public void update() {
        HibernateUtil.getCurrentSession().update(this);
    }

    public void delete() {
        HibernateUtil.getCurrentSession().delete(this);
    }

     public List<Staff> searchStaffByfullName(String staffsfullName) {
        return HibernateUtil.getCurrentSession().createQuery("from Staff where fullName = ?").setString(0, staffsfullName).list();
    }
     
    public String getFullName() {
        return fullName;
    }

    public void setFullName(String fullName) {
        this.fullName = fullName;
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

    public String getPassword() {
        return password;
    }

    public void setPassword(String password) {
        this.password = password;
    }

    public String getQualification() {
        return qualification;
    }

    public void setQualification(String qualification) {
        this.qualification = qualification;
    }

    public String getStaffId() {
        return staffId;
    }

    public void setStaffId(String staffId) {
        this.staffId = staffId;
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

    public String getType() {
        return type;
    }

    public void setType(String type) {
        this.type = type;
    }

    public String getUserName() {
        return userName;
    }

    public void setUserName(String userName) {
        this.userName = userName;
    }

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }

    public String getTp() {
        return tp;
    }

    public void setTp(String tp) {
        this.tp = tp;
    }
    
    
}
