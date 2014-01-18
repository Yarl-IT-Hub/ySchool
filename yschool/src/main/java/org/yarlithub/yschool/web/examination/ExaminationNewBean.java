package org.yarlithub.yschool.web.examination;


import org.apache.log4j.Logger;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.repository.model.obj.yschool.Division;
import org.yarlithub.yschool.repository.model.obj.yschool.Exam;
import org.yarlithub.yschool.repository.model.obj.yschool.Grade;
import org.yarlithub.yschool.repository.model.obj.yschool.Module;
import org.yarlithub.yschool.service.ExaminationService;

import javax.annotation.PostConstruct;
import javax.faces.bean.ManagedBean;
import javax.faces.event.ValueChangeEvent;
import java.io.Serializable;
import java.util.Date;
import java.util.List;


/**
 * $LastChangedDate$
 * $LastChangedBy$
 * $LastChangedRevision$
 */

@ManagedBean
@Scope(value = "view")
@Controller
public class ExaminationNewBean implements Serializable {
    public static final Logger logger = Logger.getLogger(ExaminationNewBean.class);
    @Autowired
    private ExaminationService examinationService;
    private String page = "_caExamNew";
    private Date date;
    private int term;
    private int examType;
    private int gradeid;
    private int divisionid;
    private int moduleid;
    private List<Grade> availableGrades;
    private List<Division> availableDivisions;
    private List<Module> availableModules;

    public ExaminationNewBean() {
        setExamType(ExamType.CA_EXAM);
    }

    @PostConstruct
    public void init() {
        logger.info("[ySchool]: Initiating ExaminationNewBean");
        setAvailableGrades(this.examinationService.getAvailableGrades());
        setAvailableDivisions(this.examinationService.getAvailableDivisions());
        setAvailableModules(this.examinationService.getAvailableModules());
    }

    public void retrieveModules(ValueChangeEvent event) {
        setAvailableModules(this.examinationService.getAvailableModules(gradeid));
    }

    public int getExamType() {
        return examType;
    }

    public void setExamType(int examType) {
        this.examType = examType;
    }

    public int getTerm() {
        return term;
    }

    public void setTerm(int term) {
        this.term = term;
    }

    public Date getDate() {
        return date;
    }

    public void setDate(Date date) {
        this.date = date;
    }

    public int getGradeid() {
        return gradeid;
    }

    public void setGradeid(int gradeid) {
        this.gradeid = gradeid;
    }

    public int getDivisionid() {
        return divisionid;
    }

    public void setDivisionid(int divisionid) {
        this.divisionid = divisionid;
    }

    public int getModuleid() {
        return moduleid;
    }

    public void setModuleid(int moduleid) {
        this.moduleid = moduleid;
    }

    public List<Grade> getAvailableGrades() {
        return availableGrades;
    }

    public void setAvailableGrades(List<Grade> availableGrades) {
        this.availableGrades = availableGrades;
    }

    public List<Division> getAvailableDivisions() {
        return availableDivisions;
    }

    public void setAvailableDivisions(List<Division> availableDivisions) {
        this.availableDivisions = availableDivisions;
    }

    public List<Module> getAvailableModules() {
        return availableModules;
    }

    public void setAvailableModules(List<Module> availableModules) {
        this.availableModules = availableModules;
    }

    /**
     * Ajax Dynamic loading.
     */

    public String getPage() {
        return page;
    }

    public void setPage(String page) {
        this.page = page;
    }

    public void setCAExamPage() {
        setExamType(ExamType.CA_EXAM);
        this.page = "_caExamNew";
    }

    public void setTermExamPage() {
        setExamType(ExamType.TERM_EXAM);
        this.page = "_termExamNew";
    }

    public String addCAExam() {
        logger.info("[ySchool]: Adding new CA Exam ");
        Exam insertedExam = examinationService.addCAExam(date, term, examType, gradeid, divisionid, moduleid);
        if (insertedExam != null) {
            logger.info("[ySchool]: Adding new CA Exam Success");
            //navigates to home page.(see faces-config.xml)
            return "success";
        }
        logger.info("[ySchool]: Adding new CA Exam Failed!");
        //shows error page.
        return "failure";
    }

    public String addTermExam() {
        logger.info("[ySchool]: Adding new Term Exam ");
        List<Exam> insertedExamList = examinationService.addTermExam(date, term, examType, gradeid, moduleid);
        if (insertedExamList != null) {
            logger.info("[ySchool]: Adding new Term Exam Success");
            //navigates to home page.(see faces-config.xml)
            return "NewTermExamSuccess";
        }
        logger.info("[ySchool]: Adding new Term Exam Failure");
        //shows error page.
        return "NewTermExamFailure";
    }

}
