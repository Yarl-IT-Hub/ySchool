package org.yarlithub.yschool.web.analytics;

import org.primefaces.model.chart.CartesianChartModel;
import org.primefaces.model.chart.ChartSeries;
import org.primefaces.model.chart.PieChartModel;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.repository.model.obj.yschool.Student;
import org.yarlithub.yschool.service.AnalyticsService;
import org.yarlithub.yschool.service.StudentService;

import javax.faces.bean.ManagedBean;
import javax.faces.model.DataModel;
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
public class AnalyticsStudentRecommenderHomeBean implements Serializable {
    @Autowired
    private StudentService studentService;
    @Autowired
    private AnalyticsService analyticsService;
    @Autowired
    private AnalyticsController analyticsController;
    private Student student;
    private PieChartModel pieModelSubject;
    private PieChartModel pieModelSuccess;
    private PieChartModel pieModelStreamSuccess;
    private CartesianChartModel categoryModelStreamSuccessFailure;

    public Student getStudent() {
        return student;
    }

    public void setStudent(Student student) {
        this.student = student;
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

    public CartesianChartModel getCategoryModelStreamSuccessFailure() {
        createCategoryModel();
        return categoryModelStreamSuccessFailure;
    }

    public void setCategoryModelStreamSuccessFailure(CartesianChartModel categoryModelStreamSuccessFailure) {
        this.categoryModelStreamSuccessFailure = categoryModelStreamSuccessFailure;
    }

    private void createCategoryModel() {
        categoryModelStreamSuccessFailure = new CartesianChartModel();

        ChartSeries boys = new ChartSeries();
        boys.setLabel("Successful");

        boys.set("2004", 120);
        boys.set("2005", 100);
        boys.set("2006", 44);
        boys.set("2007", 150);
        boys.set("2008", 25);

        ChartSeries girls = new ChartSeries();
        girls.setLabel("Failure");

        girls.set("2004", 52);
        girls.set("2005", 60);
        girls.set("2006", 110);
        girls.set("2007", 135);
        girls.set("2008", 120);

        categoryModelStreamSuccessFailure.addSeries(boys);
        categoryModelStreamSuccessFailure.addSeries(girls);
    }

    public PieChartModel getPieModelStreamSuccess() {
        createPieModelStreamSuccess();
        return pieModelStreamSuccess;
    }

    public void setPieModelStreamSuccess(PieChartModel pieModelStreamSuccess) {
        this.pieModelStreamSuccess = pieModelStreamSuccess;
    }

    private void createPieModelStreamSuccess() {
        pieModelStreamSuccess = new PieChartModel();

        pieModelStreamSuccess.set("Arts_Success", 2);
        pieModelStreamSuccess.set("Arts__Failure", 6);
//        pieModelSuccess.set("Maths__Success", 1);
//        pieModelSuccess.set("Commerce__Success", 1);
    }

    public String navigateToStreamProfiles_Arts() {



        return "StreamProfiles";
    }

    public String navigateToStreamProfiles_Commerce() {

        return "StreamProfiles";
    }

    public String navigateToStreamProfiles_Maths() {
        return "StreamProfiles";
    }

    public String navigateToStreamProfiles_Science() {
        return "StreamProfiles";
    }


}



