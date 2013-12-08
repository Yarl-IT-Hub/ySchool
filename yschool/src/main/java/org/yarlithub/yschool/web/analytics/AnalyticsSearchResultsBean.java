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
    private DataModel<Student> searchResults;


    public DataModel<Student> getSearchResults() {
        return searchResults;
    }

    public void setSearchResults(DataModel<Student> searchResults) {
        this.searchResults = searchResults;
    }


    public String viewAnalyticsStudent() {
        setStudent(searchResults.getRowData());
        analyticsController.setStudent(getStudent());
        return "viewAnalyticsStudent";
    }

    public void setStudent(Student student) {
        this.student = student;
    }

    public Student getStudent() {
        return this.student;
    }

    public boolean preloadStudent() {
        this.setSearchResults(new ListDataModel<Student>(analyticsController.getSearchResults()));
        return true;
    }


}
