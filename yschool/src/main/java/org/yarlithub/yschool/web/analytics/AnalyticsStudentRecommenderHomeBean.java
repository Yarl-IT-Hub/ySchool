package org.yarlithub.yschool.web.analytics;

import org.primefaces.model.chart.CartesianChartModel;
import org.primefaces.model.chart.LineChartSeries;
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
 * User: jayrksih
 * Date: 11/2/13
 * Time: 10:25 PM
 * To change this template use File | Settings | File Templates.
 */

@ManagedBean
@Scope(value = "session")
@Controller
public class AnalyticsStudentRecommenderHomeBean implements Serializable {
    @Autowired
    private StudentService studentService;
    @Autowired
    private AnalyticsService analyticsService;
    @Autowired
    private AnalyticsController analyticsController;
    private Student student;
    private DataModel<Student> oLSubjects;
    private DataModel<Student> matchingStudentProfiles;
    private CartesianChartModel linearModel;

    public Student getStudent() {
        return student;
    }

    public void setStudent(Student student) {
        this.student = student;
    }

    public DataModel getoLSubjects() {
        return oLSubjects;
    }

    public void setoLSubjects(DataModel oLSubjects) {
        this.oLSubjects = oLSubjects;
    }

    public CartesianChartModel getLinearModel() {
        return linearModel;
    }

    public void setLinearModel(CartesianChartModel linearModel) {
        this.linearModel = linearModel;
    }

    public DataModel<Student> getMatchingStudentProfiles() {
        return matchingStudentProfiles;
    }

    public void setMatchingStudentProfiles(DataModel<Student> matchingStudentProfiles) {
        this.matchingStudentProfiles = matchingStudentProfiles;
    }

    private void createLinearModel() {
        linearModel = new CartesianChartModel();


        LineChartSeries series1 = new LineChartSeries();
        series1.setLabel("Series 1");

        series1.set(1, 2);
        series1.set(2, 1);
        series1.set(3, 3);
        series1.set(4, 6);
        series1.set(5, 8);

        LineChartSeries series2 = new LineChartSeries();
        series2.setLabel("Series 2");
        series2.setMarkerStyle("diamond");

        series2.set(1, 6);
        series2.set(2, 3);
        series2.set(3, 2);
        series2.set(4, 7);
        series2.set(5, 9);

        linearModel.addSeries(series1);
        linearModel.addSeries(series2);
        setLinearModel(linearModel);

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
