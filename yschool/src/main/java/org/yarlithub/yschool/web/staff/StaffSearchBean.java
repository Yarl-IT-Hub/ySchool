package org.yarlithub.yschool.web.staff;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.repository.model.obj.yschool.Staff;
import org.yarlithub.yschool.service.StaffService;

import javax.faces.bean.ManagedBean;
import java.io.Serializable;




/**
 * $LastChangedDate$
 * $LastChangedBy$
 * $LastChangedRevision$
 */

@ManagedBean
@Scope(value = "session")
@Controller
public class StaffSearchBean implements Serializable {


    private Staff staff;
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
    public boolean preLoad() {

        setStaff(staffController.getStaff());
        return  true;
    }
}
