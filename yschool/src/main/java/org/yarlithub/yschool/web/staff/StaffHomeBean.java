package org.yarlithub.yschool.web.staff;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.repository.model.obj.yschool.Staff;
import org.yarlithub.yschool.service.StaffService;

import javax.annotation.PostConstruct;
import javax.faces.bean.ManagedBean;
import javax.faces.model.DataModel;
import javax.faces.model.ListDataModel;
import java.io.Serializable;

/**
 * $LastChangedDate$
 * $LastChangedBy$
 * $LastChangedRevision$
 */

@ManagedBean
@Scope(value = "view")
@Controller
public class StaffHomeBean implements Serializable {

    private DataModel<Staff> staffDataModel;
    //private UploadedFile photo;
    @Autowired
    private StaffService staffService;
    @Autowired
    private StaffController staffController;

    @PostConstruct
    public boolean init() {
        staffDataModel = new ListDataModel(staffService.getStaff());
        this.setStaffDataModel(staffDataModel);
        return true;
    }

    public DataModel getStaffDataModel() {
        return staffDataModel;
    }

    public void setStaffDataModel(DataModel staffDataModel) {
        this.staffDataModel = staffDataModel;
    }

    public String viewStaff() {
        Staff staff = staffDataModel.getRowData();
        staffController.setStaff(staff);
        return "ViewStaff";
    }
}
