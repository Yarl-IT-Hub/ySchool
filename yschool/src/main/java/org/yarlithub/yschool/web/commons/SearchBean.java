package org.yarlithub.yschool.web.commons;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.repository.model.obj.yschool.Staff;
import org.yarlithub.yschool.repository.model.obj.yschool.Student;
import org.yarlithub.yschool.service.CommonService;
import org.yarlithub.yschool.web.converter.SearchConverter;

import javax.faces.bean.ManagedBean;
import java.util.ArrayList;
import java.util.List;

/**
 * Created with IntelliJ IDEA.
 * User: JayKrish
 * Date: 3/23/14
 * Time: 11:30 PM
 * To change this template use File | Settings | File Templates.
 */
@ManagedBean
@Scope(value = "session")
@Controller
public class SearchBean {

    @Autowired
    private CommonService commonService;
    private SearchResult selectedResult;

    public SearchResult getSelectedResult() {
        return selectedResult;
    }

    public void setSelectedResult(SearchResult selectedResult) {
        this.selectedResult = selectedResult;
    }

    public List<SearchResult> completeSearch(String query) {

        List<SearchResult> suggestions = new ArrayList<SearchResult>();
        List<SearchResult> resultList = getSuggestionList(query, 5);
        SearchConverter.resultList = resultList;

        for(SearchResult sr : resultList) {
            if(sr.getName().toLowerCase().startsWith(query))
                suggestions.add(sr);
        }

        return suggestions;
    }


    private ArrayList<SearchResult> getSuggestionList(String name, int maxNo){

        ArrayList<SearchResult> resultList = new ArrayList<>();

        List<Student> studentList = commonService.getStudentsNameLike(name, maxNo);
        for(Student student : studentList){
            resultList.add(new SearchResult(student.getName(), "Student", student.getId()));
        }

        List<Staff> staffList = commonService.getStaffsNameLike(name, maxNo);
        for(Staff staff : staffList){
            resultList.add(new SearchResult(staff.getName(), "Staff", staff.getId()));
        }

        return resultList;
    }
}
