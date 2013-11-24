package org.yarlithub.yschool.web.analytics;

import org.primefaces.model.chart.CartesianChartModel;
import org.primefaces.model.chart.LineChartSeries;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.repository.model.obj.yschool.ClassroomSubject;
import org.yarlithub.yschool.repository.model.obj.yschool.Student;
import org.yarlithub.yschool.service.AnalyticsService;
import org.yarlithub.yschool.service.StudentService;

import javax.faces.bean.ManagedBean;
import javax.faces.model.DataModel;
import javax.faces.model.ListDataModel;
import java.io.Serializable;

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
public class AnalyticsStudentHomeBean implements Serializable {
    @Autowired
    private StudentService studentService;
    @Autowired
    private AnalyticsService analyticsService;
    @Autowired
    private AnalyticsController analyticsController;
    private Student student;
    private DataModel secondarySubjects;
    private DataModel<ClassroomSubject> oLSubjects;
    private DataModel aLSubjects;
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

    public boolean preloadStudent() {

        this.student = analyticsService.getStudenById(1);
        //  this.student=analyticsController.getStudent();
        this.oLSubjects = new ListDataModel(analyticsService.getOLSubjects(student));
        createLinearModel();

        return true;
    }

}
