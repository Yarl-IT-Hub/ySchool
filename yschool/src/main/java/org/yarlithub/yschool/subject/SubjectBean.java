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
package org.yarlithub.yschool.subject;

import javax.faces.bean.ManagedBean;
import javax.faces.bean.SessionScoped;
import java.io.Serializable;
import java.util.Arrays;
import java.util.List;
import javax.faces.application.FacesMessage;
import javax.faces.context.FacesContext;
import org.yarlithub.yschool.repository.Status;
import org.yarlithub.yschool.repository.Student;
import org.yarlithub.yschool.repository.Subject;

/**
 * $LastChangedDate$
 * $LastChangedBy$
 * $LastChangedRevision$
 */
@ManagedBean
@SessionScoped
public class SubjectBean implements Serializable {
    private Subject subject;
    private List<Subject> subjectList;
    public SubjectBean() {
        //logger.info("initiation a new subject bean");
        subject = new Subject();
    }
        
      public void submit() {
     //  logger.info("saving subject information [" + student + "]");
	 subject.save();
          FacesContext.getCurrentInstance().addMessage(null, 
                new FacesMessage(FacesMessage.SEVERITY_INFO, "New Subject is successfully inserted.", null));
                }
        public List<Subject> getSubjectList()
        {
        return subjectList;
    }
      public void setSubjectList(List<Subject> subjectList) {
        this.subjectList = subjectList;
    }
  public void quickSearch(){
         setSubjectList(subject.searchSubjectByname(subject.getName()));     
        }
    public String search(){
        //logger.info("search for subject by subject name[" + subject.getName() + "]");
        setSubjectList(subject.searchSubjectByname(subject.getName()));      
       return "searchSubjectList";
    }
    public Subject getSubject() {
        return subject;
    }

    public void setSubject(Subject subject) {
        this.subject = subject;
    }
	
}
