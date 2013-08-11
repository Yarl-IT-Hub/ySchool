package org.yarlithub.yschool.web.setup;

import org.apache.log4j.Logger;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.service.SetupService;
import org.yarlithub.yschool.web.util.InitialDateLoaderUtil;

import javax.faces.application.FacesMessage;
import javax.faces.bean.ManagedBean;
import javax.faces.bean.ManagedProperty;
import javax.faces.context.FacesContext;
import java.io.IOException;
import java.io.Serializable;

import org.apache.myfaces.custom.fileupload.UploadedFile;
/**
 * Created with IntelliJ IDEA.
 * User: jaykrish
 * Date: 4/25/13
 * Time: 2:55 PM
 * To change this template use File | Settings | File Templates.
 */


@ManagedBean
@Scope(value = "session")
@Controller
public class SetupBean implements Serializable {

    private static final Logger logger = Logger.getLogger(SetupBean.class);
    //Strings related to user information
    private String userName;
    private String usereMail;
    private String userRole;
    private String password;
    private String confirmPassword;
    //have to get in from userRole string later
    private Integer adminUser;
    //Strings related to school information
    private String schoolName;
    private String schoolAddress;
    private String schoolZone;
    private String schoolDistrict;
    private String schoolProvience;
    /**
     * The path of the ySchool initiation document in the user's machine.
     */
    private String initDocPath;
    private UploadedFile initFile;
    @Autowired
    private SetupService setupService;
    @ManagedProperty(value = "#{initialDateLoaderUtil}")
    private InitialDateLoaderUtil initialDateLoaderUtil;

    public SetupBean() {
        logger.info("initiating a new setup bean");

    }

    public UploadedFile getInitFile() {
        return initFile;
    }

    public void setInitFile(UploadedFile initFile) {
        this.initFile = initFile;
    }

    public String getUserName() {
        return userName;
    }

    public void setUserName(String userName) {
        this.userName = userName;
    }

    public String getUsereMail() {
        return usereMail;
    }

    public void setUsereMail(String usereMail) {
        this.usereMail = usereMail;
    }

    public String getUserRole() {
        return userRole;
    }

    public void setUserRole(String userRole) {
        this.userRole = userRole;
        //TODO: various user levels.
        adminUser = 1;
    }

    public String getPassword() {
        return password;
    }

    public void setPassword(String password) {
        this.password = password;
    }

    public String getConfirmPassword() {
        return confirmPassword;
    }

    public void setConfirmPassword(String confirmPassword) {
        this.confirmPassword = confirmPassword;
    }

    public String getSchoolName() {
        return schoolName;
    }

    public void setSchoolName(String schoolName) {
        this.schoolName = schoolName;
    }

    public String getSchoolAddress() {
        return schoolAddress;
    }

    public void setSchoolAddress(String schoolAddress) {
        this.schoolAddress = schoolAddress;
    }

    public String getSchoolZone() {
        return schoolZone;
    }

    public void setSchoolZone(String schoolZone) {
        this.schoolZone = schoolZone;
    }

    public String getSchoolDistrict() {
        return schoolDistrict;
    }

    public void setSchoolDistrict(String schoolDistrict) {
        this.schoolDistrict = schoolDistrict;
    }

    public String getSchoolProvience() {
        return schoolProvience;
    }

    public void setSchoolProvience(String schoolProvience) {
        this.schoolProvience = schoolProvience;
    }

    public String getInitDocPath() {
        return initDocPath;
    }

    public void setInitDocPath(String initDocPath) {
        this.initDocPath = initDocPath;
    }

    public String enterSetup() throws IOException {
        logger.info("Entering into first time ySchool setup");
        FacesContext.getCurrentInstance().addMessage(null,
                new FacesMessage(FacesMessage.SEVERITY_INFO, "setting up now.", null));

        boolean setupResult = setupService.ySchoolSetUP(userName, usereMail, password, schoolName, schoolAddress, schoolZone, schoolDistrict,
                schoolProvience, initFile);
        if (setupResult) {
            //navigates to home page.(see faces-config.xml)
            return "success";
        }
        //shows error page.
        return "failure";
    }

    public void setInitialDateLoaderUtil(InitialDateLoaderUtil initialDateLoaderUtil) {
        this.initialDateLoaderUtil = initialDateLoaderUtil;
    }
}
