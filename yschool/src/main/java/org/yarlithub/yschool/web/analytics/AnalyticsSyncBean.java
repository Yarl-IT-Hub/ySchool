package org.yarlithub.yschool.web.analytics;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.repository.model.obj.yschool.Exam;
import org.yarlithub.yschool.service.AnalyticsService;

import javax.faces.bean.ManagedBean;
import javax.faces.model.DataModel;
import javax.faces.model.ListDataModel;
import java.io.Serializable;
import java.util.Iterator;
import java.util.List;


/**
 * $LastChangedDate$
 * $LastChangedBy$
 * $LastChangedRevision$
 */

@ManagedBean
@Scope(value = "session")
@Controller
public class AnalyticsSyncBean implements Serializable {

    private DataModel<Exam> newExams;
    private DataModel<Exam> modifiedExams;
    @Autowired
    private AnalyticsService analyticsService;
    @Autowired
    private AnalyticsController analyticsController;
    private boolean allSynced;

    public boolean isAllSynced() {
        return allSynced;
    }

    public void setAllSynced(boolean allSynced) {
        this.allSynced = allSynced;
    }

    public DataModel<Exam> getNewExams() {
        return newExams;
    }

    public void setNewExams(DataModel<Exam> newExams) {
        this.newExams = newExams;
    }

    public DataModel<Exam> getModifiedExams() {
        return modifiedExams;
    }

    public void setModifiedExams(DataModel<Exam> modifiedExams) {
        this.modifiedExams = modifiedExams;
    }

    public void preload() {
        setAllSynced(true);
        this.newExams = new ListDataModel(analyticsService.getNotSyncedExams());
        modifiedExams = new ListDataModel();
        if (newExams.isRowAvailable() || modifiedExams.isRowAvailable()) {
            setAllSynced(false);
        }
    }

    public String startCLASSSync(){
        String returncode="Nothing here";

                     Iterator<Exam> newExamIterator  = newExams.iterator();
                     while(newExamIterator.hasNext()){
                         Exam exam=newExamIterator.next();
                         returncode= analyticsService.PushNewExam(exam);
                         if(!returncode.startsWith(Constants.SUCCESS_MSG)){
                             break;
                         }
                     }

        if(returncode.startsWith(Constants.SUCCESS_MSG)) {
            return "AnalyticsSync";
        }

        analyticsController.setAnalyticsErrorMessage(returncode);
        return "AnalyticsError";

    }

}
