package org.yarlithub.yschool.web.staff;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.repository.model.obj.yschool.Staff;
import org.yarlithub.yschool.service.StaffService;

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
@Scope(value = "session")
@Controller
public class StaffHomeBean implements Serializable {

    private DataModel<Staff> staffDataModel;
    //private UploadedFile photo;

    @Autowired
    private StaffService staffService;
    @Autowired
    private StaffController staffController;


    public DataModel getStaffDataModel() {
        return staffDataModel;
    }

    public void setStaffDataModel(DataModel staffDataModel) {
        this.staffDataModel = staffDataModel;
    }


    public boolean preLoad() {
        staffDataModel = new ListDataModel(staffService.getStaff());
        this.setStaffDataModel(staffDataModel);
        return true;
    }

    public String viewStaff() {
        Staff staff = staffDataModel.getRowData();
        staffController.setStaff(staff);
        return "ViewStaff";
    }
}
