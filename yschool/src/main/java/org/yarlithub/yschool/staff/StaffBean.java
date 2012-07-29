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
package org.yarlithub.yschool.staff;

import org.apache.log4j.Logger;
import java.io.Serializable;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;
import org.yarlithub.yschool.repository.Staff;

import javax.annotation.PostConstruct;
import javax.faces.application.FacesMessage;
import javax.faces.bean.ManagedBean;
import javax.faces.bean.ManagedProperty;
import javax.faces.bean.SessionScoped;
import javax.faces.context.FacesContext;
import org.yarlithub.yschool.util.InitialDateLoaderUtil;

/**
 *
 * @author Vanaja
 */
@ManagedBean
@SessionScoped
public class StaffBean implements Serializable {
private static final Logger logger = Logger.getLogger(StaffBean.class);

    @ManagedProperty(value = "#{initialDateLoaderUtil}")
    private InitialDateLoaderUtil initialDateLoaderUtil;

    public Staff staff;
    private List<Staff> staffList;
    private List<String> genderList = Arrays.asList("Male", "Female");
    private List<String> typeList;
    
    private List<String> calenderYearList;

    public StaffBean() {
        logger.info("initiation a new staff bean");
        staff = new Staff();
    }

    @PostConstruct
    public void init() {
        logger.info("loading initial data [" + initialDateLoaderUtil + "]");
        loadInitData();
    }

    public void submit() {
        logger.info("saving staff information [" + staff + "]");
          FacesContext.getCurrentInstance().addMessage(null, 
                new FacesMessage(FacesMessage.SEVERITY_INFO, "New Staff successfully inserted.", null));
        staff.save();
      
    }
    
    public String search(){
        logger.info("search for staff by full name[" + staff.getFullName() + "]");
        setStaffList(staff.searchStaffByfullName(staff.getFullName()));
              
        return "searchStaffList";
    }
    
    public Staff getStaff() {
        return staff;
    }

    public void setStaff(Staff staff) {
        this.staff = staff;
    }
     
    public List<Staff> getStaffList() {
        return staffList;
    }

    public void setStaffList(List<Staff> staffList) {
        this.staffList = staffList;
    }

    public List<String> getGenderList() {
        return genderList;
    }

    public void setGenderList(List<String> genderList) {
        this.genderList = genderList;
    }

    public List<String> getTypeList() {
        if(typeList == null){
           typeList = new ArrayList<>();
           typeList.add("Principal");
           typeList.add("Teachers");
           typeList.add("Non accademy");
        }
        return typeList;
    }

    public void setTypeList(List<String> typeList) {
        this.typeList = typeList;
    }

    public List<String> getCalenderYearList() {
        if(calenderYearList == null){
           calenderYearList = new ArrayList<>();
           calenderYearList.add("2012");
           calenderYearList.add("2013");
           calenderYearList.add("2014");
        }       
        
        return calenderYearList;
    }

    public void setCalenderYearList(List<String> calenderYearList) {
        this.calenderYearList = calenderYearList;
    } 
    
    public void loadInitData(){
        System.out.println(initialDateLoaderUtil);
    }

    public void setInitialDateLoaderUtil(InitialDateLoaderUtil initialDateLoaderUtil) {
        this.initialDateLoaderUtil = initialDateLoaderUtil;
    }
}
