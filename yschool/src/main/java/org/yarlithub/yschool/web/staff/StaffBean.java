package org.yarlithub.yschool.web.staff;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.repository.model.obj.yschool.Staff;
import org.yarlithub.yschool.service.StaffService;

import javax.faces.bean.ManagedBean;
import javax.faces.bean.ManagedProperty;
import javax.faces.model.DataModel;
import javax.faces.model.ListDataModel;
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
    private String searchKey = null;
    private DataModel<Staff>staffsSearchResultAjax;
    private Staff staff;
    //private UploadedFile photo;


    @Autowired
    private StaffService staffService;
    @Autowired
    private StaffController staffController;

    public Staff getStaff() {
        return staff;
    }

    public void setStaff(Staff staff) {
        this.staff = staff;
    }

    public StaffBean() {
        super();
        staffsSearchResultAjax = new ListDataModel<Staff>();
    }
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

    public String getSearchKey() {
        return searchKey;
    }

    public void setSearchKey(String searchKey) {
        this.searchKey = searchKey;
    }

    public DataModel<Staff> getStaffsSearchResultAjax() {
        return staffsSearchResultAjax;
    }

    public void setStaffsSearchResultAjax(DataModel<Staff> staffsSearchResultAjax) {
        this.staffsSearchResultAjax = staffsSearchResultAjax;
    }
    public String viewStaffAjax(){
        staffsSearchResultAjax = new ListDataModel<Staff>(staffService.getStaffsNameLike(searchKey, 10));
        setStaff(staffsSearchResultAjax.getRowData());
        staffController.setStaff(staff);
        return "viewStaffAjax";
    }
    public boolean preloadStaff()
    {
        staffs=new ListDataModel(staffService.getStaff());
        this.setStaffs(staffs);
        return  true;
    }

}
