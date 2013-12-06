package org.yarlithub.yschool.web.analytics;

import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.repository.model.obj.yschool.Exam;
import org.yarlithub.yschool.repository.model.obj.yschool.Student;

import javax.faces.bean.ManagedBean;
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
public class AnalyticsController implements Serializable {

    private Student student;
    private String stream;
    private String profileStream;
    private String analyticsErrorMessage;

    private Exam exam;

    private List<Student> searchResults;


    public List<Student> getSearchResults() {
        return searchResults;
    }

    public void setSearchResults(List<Student> searchResults) {
        this.searchResults = searchResults;
    }

    public Student getStudent() {
        return student;
    }

    public void setStudent(Student student) {
        this.student = student;
    }

    public String getStream() {
        return stream;
    }

    public void setStream(String stream) {
        this.stream = stream;
    }

    public String getProfileStream() {
        return profileStream;
    }

    public void setProfileStream(String profileStream) {
        this.profileStream = profileStream;
    }

    public String getAnalyticsErrorMessage() {
        return analyticsErrorMessage;
    }

    public void setAnalyticsErrorMessage(String analyticsErrorMessage) {
        this.analyticsErrorMessage = analyticsErrorMessage;
    }

    public Exam getExam() {
        return exam;
    }

    public void setExam(Exam exam) {
        this.exam = exam;
    }
}
