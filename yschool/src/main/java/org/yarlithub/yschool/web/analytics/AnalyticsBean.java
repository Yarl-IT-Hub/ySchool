package org.yarlithub.yschool.web.analytics;

import net.sf.jasperreports.engine.JRException;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.repository.model.obj.yschool.Student;
import org.yarlithub.yschool.service.AnalyticsService;

import javax.faces.bean.ManagedBean;
import javax.faces.context.FacesContext;
import javax.servlet.ServletOutputStream;
import javax.servlet.http.HttpServletResponse;
import java.io.IOException;
import java.io.Serializable;
import java.util.*;


/**
 * $LastChangedDate$
 * $LastChangedBy$
 * $LastChangedRevision$
 */

@ManagedBean
@Scope(value = "request")
@Controller
public class AnalyticsBean implements Serializable {


    @Autowired
    private AnalyticsService analyticsService;
    private Student student;
    private int searchKeyVal;
    private String searchKey =null;

    private static Map<String,Integer> studentsSearchResultMap;

    public static Map<String, Integer> getStudentsSearchResultMap() {
        return studentsSearchResultMap;
    }

    public String getSearchKey() {
        return searchKey;
    }

    public void setSearchKey(String searchKey) {
        this.searchKey = searchKey;
    }

    public int getSearchKeyVal() {
        return searchKeyVal;
    }

    public void setSearchKeyVal(int searchKeyVal) {
        this.searchKeyVal = searchKeyVal;
    }

    public void search() {
        studentsSearchResultMap = new LinkedHashMap<String,Integer>();
        List<Student> studentList=analyticsService.getStudentsNameLike(searchKey,5);
        Iterator<Student> studentIterator = studentList.iterator();
        while (studentIterator.hasNext()){
            Student student1 =studentIterator.next();
            studentsSearchResultMap.put(student1.getName(), student1.getId());
        }
    }

    public void showSearchResults(){
              setStudent(analyticsService.getStudenById(searchKeyVal));
    }

    public Student getStudent() {
        return student;
    }

    public void setStudent(Student student) {
        this.student = student;
    }

    public boolean preloadStudent() {
        this.setStudent(analyticsService.getStudent());
        return true;
    }

    public void printReport() throws IOException, JRException {
        HttpServletResponse httpServletResponse = (HttpServletResponse) FacesContext.getCurrentInstance().getExternalContext().getResponse();
        httpServletResponse.addHeader("Content-disposition", "attachment; filename=report.pdf");
        ServletOutputStream servletOutputStream = httpServletResponse.getOutputStream();

        analyticsService.printReport(servletOutputStream);
        FacesContext.getCurrentInstance().responseComplete();
    }

}
