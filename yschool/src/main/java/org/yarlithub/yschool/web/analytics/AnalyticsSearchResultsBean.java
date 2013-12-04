package org.yarlithub.yschool.web.analytics;

import net.sf.jasperreports.engine.JRException;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.repository.model.obj.yschool.Student;
import org.yarlithub.yschool.service.AnalyticsService;

import javax.faces.bean.ManagedBean;
import javax.faces.context.FacesContext;
import javax.faces.model.DataModel;
import javax.faces.model.ListDataModel;
import javax.servlet.ServletOutputStream;
import javax.servlet.http.HttpServletResponse;
import java.io.IOException;
import java.io.Serializable;
import java.util.List;


/**
 * $LastChangedDate$
 * $LastChangedBy$
 * $LastChangedRevision$
 */

@ManagedBean
@Scope(value = "session")
@Controller
public class AnalyticsSearchResultsBean implements Serializable {


    @Autowired
    private AnalyticsService analyticsService;
    @Autowired
    private AnalyticsController analyticsController;

    private Student student;

    private String searchKey = null;

    private DataModel<Student> studentsSearchResultAjax;
    private DataModel<Student> studentsSearchResult;
    private DataModel<Student> searchResults;



    public AnalyticsSearchResultsBean() {
        super();
        studentsSearchResultAjax = new ListDataModel<Student>();
        studentsSearchResult = new ListDataModel<Student>();

    }

    public DataModel<Student> getSearchResults() {
        return searchResults;
    }

    public void setSearchResults(DataModel<Student> searchResults) {
        this.searchResults = searchResults;
    }

    public DataModel<Student> getStudentsSearchResultAjax() {
        return studentsSearchResultAjax;
    }

    public void setStudentsSearchResultAjax(DataModel<Student> studentsSearchResultAjax) {
        this.studentsSearchResultAjax = studentsSearchResultAjax;
    }

    public DataModel<Student> getStudentsSearchResult() {
        return studentsSearchResult;
    }

    public void setStudentsSearchResult(DataModel<Student> studentsSearchResult) {
        this.studentsSearchResult = studentsSearchResult;
    }

    public String getSearchKey() {
        return searchKey;
    }

    public void setSearchKey(String searchKey) {
        this.searchKey = searchKey;
    }

    public void searchAjax() {

        studentsSearchResultAjax = new ListDataModel<Student>(analyticsService.getStudentsNameLike(searchKey, 5));

    }
    public String search() {
        studentsSearchResult = new ListDataModel<Student>(analyticsService.getStudentsNameLike(searchKey,10));
         analyticsController.setSearchResults(analyticsService.getStudentsNameLike(searchKey,10));
        return "viewAnalyticsSearchResults";
    }

    public String viewAnalyticsStudentAjax(){
        setStudent(studentsSearchResultAjax.getRowData());
        analyticsController.setStudent(student);
        return "viewAnalyticsStudent";
    }

    public String viewAnalyticsStudent(){
        setStudent(searchResults.getRowData());
        analyticsController.setStudent(student);
        return "viewAnalyticsStudent";
    }

    public Student getStudent() {
        return student;
    }

    public void setStudent(Student student) {
        this.student = student;
    }

    public boolean preloadStudent() {
        this.setStudent(analyticsService.getStudent());
        this.setSearchResults(new ListDataModel<Student>(analyticsController.getSearchResults()));
        return true;
    }


}
