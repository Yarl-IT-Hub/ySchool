package org.yarlithub.yschool.web.analytics;

import org.primefaces.model.chart.PieChartModel;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.analytics.core.YAnalyzer;
import org.yarlithub.yschool.repository.model.obj.yschool.Student;
import org.yarlithub.yschool.service.AnalyticsService;
import org.yarlithub.yschool.service.StudentService;

import javax.faces.bean.ManagedBean;
import javax.faces.model.DataModel;
import javax.faces.model.ListDataModel;
import java.io.Serializable;
import java.util.List;

/**
 * Created with IntelliJ IDEA.
 * User: Jay Krish
 * Date: 11/2/13
 * Time: 10:25 PM
 * To change this template use File | Settings | File Templates.
 */

@ManagedBean
@Scope(value = "session")
@Controller
public class AnalyticsStudentRecommenderBean implements Serializable {
    @Autowired
    private StudentService studentService;
    @Autowired
    private AnalyticsService analyticsService;
    @Autowired
    private AnalyticsController analyticsController;
    private Student student;
    private DataModel<Student> matchingStudentProfiles;
    private PieChartModel pieModelSubject;
    private PieChartModel pieModelSuccess;

    public Student getStudent() {
        return student;
    }

    public void setStudent(Student student) {
        this.student = student;
    }

    public DataModel<Student> getMatchingStudentProfiles() {
        return matchingStudentProfiles;
    }

    public void setMatchingStudentProfiles(DataModel<Student> matchingStudentProfiles) {
        this.matchingStudentProfiles = matchingStudentProfiles;
    }

    public PieChartModel getPieModelSubject() {
        this.createPieModelSubject();
        return pieModelSubject;
    }

    public void setPieModelSubject(PieChartModel pieModelSubject) {
        this.pieModelSubject = pieModelSubject;
    }

    private void createPieModelSubject() {
        pieModelSubject = new PieChartModel();

        pieModelSubject.set("Arts", 9);
        pieModelSubject.set("Biology", 2);
        pieModelSubject.set("Maths", 1);
        pieModelSubject.set("Commerce", 5);
    }

    public PieChartModel getPieModelSuccess() {
        this.createPieModelSuccess();
        return pieModelSuccess;
    }

    public void setPieModelSuccess(PieChartModel pieModelSuccess) {
        this.pieModelSuccess = pieModelSuccess;
    }

    private void createPieModelSuccess() {
        pieModelSuccess = new PieChartModel();

        pieModelSuccess.set("Arts_Success", 2);
        pieModelSuccess.set("Biology__Success", 0);
        pieModelSuccess.set("Maths__Success", 1);
        pieModelSuccess.set("Commerce__Success", 1);
    }

    public boolean preloadProfiles() {
        YAnalyzer yAnalyzer = new YAnalyzer();
        List<Integer> admissionNoList = null;
        admissionNoList = yAnalyzer.getNeighbours();

        this.matchingStudentProfiles = new ListDataModel(analyticsService.getStudentByAdmissionNumber(admissionNoList));
        //  this.student=analyticsController.getStudent();

        return true;
    }

}
