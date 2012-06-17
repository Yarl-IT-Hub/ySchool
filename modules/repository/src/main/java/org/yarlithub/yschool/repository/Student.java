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

import org.hibernate.Session;
import org.joda.time.DateTime;
import org.yarlithub.yschool.repository.util.HibernateUtil;

import javax.persistence.Entity;
import javax.persistence.ManyToMany;
import javax.persistence.Table;
import java.util.List;
import javax.persistence.GeneratedValue;
import javax.persistence.Id;
import org.hibernate.criterion.Restrictions;

/**
 * $LastChangedDate$
 * $LastChangedBy$
 * $LastChangedRevision$
 */
@Entity
@Table(name = "student")
public class Student extends PersistentObject {

    private String fname;
    private String lname;
    private DateTime dob;
    private DateTime registrationDate;

    @ManyToMany
    private List<Grade> grades;


    public void save() {
        HibernateUtil.getCurrentSession().save(this);
    }

    public void update() {
        HibernateUtil.getCurrentSession().update(this);
    }

    public void delete() {
        HibernateUtil.getCurrentSession().delete(this);
    }
    public List<Student> search() {    
        return (List) HibernateUtil.getCurrentSession()
                .createCriteria(Student.class)
                //.add(Restrictions.like(fname, this.getFname() +"%"))
                //.add(Restrictions.like(lname, this.getLname() +"%"))
                .list();
    }
    
    public String getFname() {
        return fname;
    }

    public void setFname(String fname) {
        this.fname = fname;
    }

    public String getLname() {
        return lname;
    }

    public void setLname(String lname) {
        this.lname = lname;
    }

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
        this.grades = grades;
    }


}
