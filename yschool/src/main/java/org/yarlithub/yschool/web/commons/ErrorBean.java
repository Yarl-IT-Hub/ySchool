package org.yarlithub.yschool.web.commons;

import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;

import javax.faces.bean.ManagedBean;

/**
 * Created with IntelliJ IDEA.
 * User: JayKrish
 * Date: 1/29/14
 * Time: 12:17 AM
 * To change this template use File | Settings | File Templates.
 */
@ManagedBean
@Scope(value = "session")
@Controller
public class ErrorBean {
    String errorMessage;

    public ErrorBean(){
        errorMessage="Error not recorded!";
    }

    public String getErrorMessage() {
        return errorMessage;
    }

    public void setErrorMessage(String errorMessage) {
        this.errorMessage = errorMessage;
    }
}
