package org.yarlithub.yschool.analytics.datasync;

import com.arima.classanalyzer.datasync.CExam;
import com.arima.classanalyzer.datasync.CMarks;
import com.arima.classanalyzer.datasync.CResults;
import com.arima.classanalyzer.datasync.Synchronizer;
import org.hibernate.Criteria;
import org.hibernate.Hibernate;
import org.hibernate.criterion.Restrictions;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import org.yarlithub.yschool.repository.factories.yschool.YschoolDataPoolFactory;
import org.yarlithub.yschool.repository.model.obj.yschool.*;
import org.yarlithub.yschool.repository.services.data.DataLayerYschool;
import org.yarlithub.yschool.repository.services.data.DataLayerYschoolImpl;

import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;

/**
 * Created with IntelliJ IDEA.
 * User: Jay Krish
 * Date: 11/25/13
 * Time: 5:01 AM
 * To change this template use File | Settings | File Templates.
 */
//TODO: redo due to database change to subject module
public class SyncExamination {
    DataLayerYschool dataLayerYschool = DataLayerYschoolImpl.getInstance();

    public boolean addNewSyncExam(int examid) {
        Exam exam = dataLayerYschool.getExam(examid);

        /*passing null while creating examsync to make sure no exams are associated with it in the beginning
        * to avoid notnull constrain error of examsync table*/
        ExamSync examSync = YschoolDataPoolFactory.getExamSync(null);
        examSync.setExamIdexam(exam);
        examSync.setSyncStatus(SyncStatus.NOT_SYNCED);
        examSync.setClassIdexam(0);
        dataLayerYschool.save(examSync);
        dataLayerYschool.flushSession();
        if (examSync.getId() > 0) {
            return true;
        }
        return false;
    }

    public List<Exam> getNotSyncedExams() {

        List<Exam> examList = new ArrayList<Exam>();
        Criteria c = dataLayerYschool.createCriteria(ExamSync.class);
        c.add(Restrictions.eq("syncStatus", 0));
        List<ExamSync> examSyncList = c.list();
        Iterator<ExamSync> examSyncIterator = examSyncList.iterator();
        while (examSyncIterator.hasNext()) {
            ExamSync examSync = examSyncIterator.next();
            Hibernate.initialize(examSync.getExamIdexam());
            examList.add(examSync.getExamIdexam());
        }
        return examList;
    }

    public String PushNewExam(Exam exam) {

        Synchronizer synchronizer = new Synchronizer();

        CExam cExam = new CExam();
        cExam.setSchoolNo(11086);
        cExam.setDate(exam.getDate());
        cExam.setGrade(exam.getClassroomModuleIdclassroomModule().getClassroomIdclassroom().getGradeIdgrade().getGrade());
        cExam.setDivision(exam.getClassroomModuleIdclassroomModule().getClassroomIdclassroom().getDivisionIddivision().getDivision());
        cExam.setTerm(exam.getTerm());
        cExam.setSubjectId(exam.getClassroomModuleIdclassroomModule().getModuleIdmodule().getId());
        cExam.setExamType(exam.getExamTypeIdexamType().getId());

        if(exam.getExamTypeIdexamType().getId()==Constants.GENERAL_EXAM){
           Iterator<Results> resultsIterator = exam.getResultss().iterator();
            List<CResults> cResultsList=new ArrayList<CResults>();
            while(resultsIterator.hasNext()){
                Results results =resultsIterator.next();
                CResults cResults = new CResults();
                cResults.setAdmissionNo(results.getStudentIdstudent().getAdmissionNo());
                cResults.setResult(results.getResults());
                  cResultsList.add(cResults);
            }
           cExam.setcResultsList(cResultsList);
        }

        else{
            /*term or CA exam */
            Iterator<Marks> marksIterator = exam.getMarkss().iterator();
            List<CMarks> cMarksList=new ArrayList<CMarks>();
            while(marksIterator.hasNext()){
                Marks marks =marksIterator.next();
                CMarks cMarks = new CMarks();
                cMarks.setAdmissionNo(marks.getStudentIdstudent().getAdmissionNo());
                cMarks.setMarks(marks.getMarks());
                cMarksList.add(cMarks);
            }
            cExam.setcMarksList(cMarksList);
        }

        String results = synchronizer.pushExamPerformance(cExam);
         if(results.startsWith(Constants.SUCCESS_MSG)){
             /*take the returned class-examid and insert it into yschool sync table*/
             String[] re = results.split(":");
             int classExamId = Integer.valueOf(re[1]);
             updateSyncExam(SyncStatus.SYNCED,classExamId,exam);
         }

        return results;
    }

    private void updateSyncExam(int status, int classExamId, Exam exam) {
        List<ExamSync> examSyncList = new ArrayList<>();
        Criteria examSyncCR = dataLayerYschool.createCriteria(ExamSync.class);
        examSyncCR.add(Restrictions.eq("examIdexam", exam));                        //String.valueOf(admissionNo)
        examSyncList= examSyncCR.list();
        ExamSync examSync =examSyncList.get(0);


        examSync.setClassIdexam(classExamId);
        examSync.setSyncStatus(status);
        dataLayerYschool.save(examSync);
        dataLayerYschool.flushSession();
    }
}
