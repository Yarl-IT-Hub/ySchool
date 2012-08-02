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

import java.util.List;
import org.yarlithub.yschool.repository.util.HibernateUtil;

import javax.persistence.Entity;
import javax.persistence.Table;

/**
 * $LastChangedDate$
 * $LastChangedBy$
 * $LastChangedRevision$
 */
@Entity
@Table(name = "subject")
public class Subject extends PersistentObject {
    
    private String name;
    private String year;
    private String medium;


    public void save() {
        HibernateUtil.getSessionFactory().getCurrentSession().save(this);
    }

    public void update() {
        HibernateUtil.getSessionFactory().getCurrentSession().update(this);
    }

    public void delete() {
        HibernateUtil.getSessionFactory().getCurrentSession().delete(this);
    }
 public List<Subject> searchSubjectByname(String subjectname) {
        
if("".equals(subjectname) || subjectname ==null){
			return "";
FacesContext.getCurrentInstance().addMessage(null, 
                new FacesMessage(FacesMessage.SEVERITY_ERROR, "Please type the subject name!", null));
		}
else{
				
return HibernateUtil.getCurrentSession().createQuery("from Subject  where name = ?").setString(0, subjectname).list();
    }
}
public List<Subject> searchSubjectBystartname(String subjectname) {

        return HibernateUtil.getCurrentSession().createQuery("from Subject  where name = ?").setString(0, subjectname).list();
    }
	    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

  public String getYear() {
        return year;
    }

    public void setYear(String year) {
        this.year = year;
    }

    public String getMedium() {
        return medium;
    }

    public void setMedium(String medium) {
        this.medium = medium;
    }
}
