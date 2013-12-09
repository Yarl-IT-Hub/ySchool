package org.yarlithub.yschool.web.staff;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.service.StaffService;
import org.yarlithub.yschool.web.util.InitialDateLoaderUtil;
import javax.faces.model.DataModel;
import javax.faces.bean.ManagedBean;
import javax.faces.bean.ManagedProperty;
import javax.faces.model.ListDataModel;
import javax.swing.*;
import java.io.Serializable;

/**
 * $LastChangedDate$
 * $LastChangedBy$
 * $LastChangedRevision$
 */

@ManagedBean
@Scope(value = "session")
@Controller
public class StaffBean implements Serializable {

    private String staffID;
    private String name;
    private String fullname;
    private DataModel staffs;
    //private UploadedFile photo;

    @Autowired
    private StaffService staffService;
    @ManagedProperty(value = "#{initialDateLoaderUtil}")
    private InitialDateLoaderUtil initialDateLoaderUtil;



    public String getStaffID() {
        return staffID;
    }

    public void setStaffID(String staffID) {
        this.staffID = staffID;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getFullname() {
        return fullname;
    }

    public void setFullname(String fullname) {
        this.fullname = fullname;
    }

    public DataModel getStaffs() {
        return staffs;
    }

    public void setStaffs(DataModel staffs) {
        this.staffs = staffs;
    }

    public String addStaff()  {

        boolean setupResult = staffService.addStaff(staffID, name, fullname);
        if (setupResult) {
            return "AddStaffSuccess";
        }
        return "AddStaffFailed";
    }
    public boolean preloadStaff()
    {
        staffs=new ListDataModel(staffService.getStaff());
        this.setStaffs(staffs);
        return  true;
    }
}
