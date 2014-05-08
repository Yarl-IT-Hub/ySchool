package org.yarlithub.yschool.web.examination;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.repository.model.obj.yschool.Exam;
import org.yarlithub.yschool.service.ExaminationService;
import org.yarlithub.yschool.web.util.PageName;

import javax.annotation.PostConstruct;
import javax.faces.bean.ManagedBean;
import javax.faces.model.DataModel;
import javax.faces.model.ListDataModel;
import java.io.Serializable;


@ManagedBean
@Scope(value = "view")
@Controller
public class ExaminationHomeBean implements Serializable {

    @Autowired
    private ExaminationService examinationService;
    private DataModel<Exam> exams;

    @PostConstruct
    public void init() {
        this.exams = new ListDataModel(examinationService.getLatestExams(0, 100));
    }

    public DataModel getExams() {
        return exams;
    }

    public void setExams(DataModel exams) {
        this.exams = exams;
    }

    public String getExaminationViewPage() {
        return PageName.EXAMINATION_VIEW;
    }
}
