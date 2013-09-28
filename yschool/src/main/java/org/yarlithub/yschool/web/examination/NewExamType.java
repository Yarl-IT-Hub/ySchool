package org.yarlithub.yschool.web.examination;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.service.SetupService;

import javax.faces.bean.ManagedBean;
import javax.faces.bean.ManagedProperty;
import java.io.Serializable;


/**
 * $LastChangedDate$    25/09/2013
 * $LastChangedBy$      JayKrish
 * $LastChangedRevision$
 */

@ManagedBean
@Scope(value = "request")
@Controller
public class NewExamType implements Serializable {
    @ManagedProperty(value = "_caExamNew")

    @Autowired
    private SetupService setupService;
    private String page = "_caExamNew";

    public String getPage() {
        return page;
    }

    public void setPage(String page) {
        this.page = page;
    }

    public void setCAExamPage() {
        this.page = "_caExamNew";
    }

    public void setTermExamPage() {
        this.page = "_termExamNew";
    }
}
