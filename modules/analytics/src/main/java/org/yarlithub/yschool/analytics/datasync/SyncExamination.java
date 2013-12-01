package org.yarlithub.yschool.analytics.datasync;

import com.arima.classanalyzer.datasync.Synchronizer;
import org.hibernate.Criteria;
import org.hibernate.Hibernate;
import org.hibernate.criterion.Restrictions;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;
import org.yarlithub.yschool.repository.factories.yschool.YschoolDataPoolFactory;
import org.yarlithub.yschool.repository.model.obj.yschool.Exam;
import org.yarlithub.yschool.repository.model.obj.yschool.ExamSync;
import org.yarlithub.yschool.repository.model.obj.yschool.Marks;
import org.yarlithub.yschool.repository.model.obj.yschool.Results;
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

    public String PushNewExam(Exam exam) throws JSONException {

        Synchronizer synchronizer = new Synchronizer();

        JSONObject jsonCExam = new JSONObject();
        jsonCExam.put(Constants.SCHOOL_NO, 11086);
        jsonCExam.put(Constants.EXAM_DATE, "2012-1-1");
        jsonCExam.put(Constants.EXAM_GRADE, exam.getClassroomSubjectIdclassroomSubject().getClassroomIdclass().getGrade());
        jsonCExam.put(Constants.EXAM_DIVISION, exam.getClassroomSubjectIdclassroomSubject().getClassroomIdclass().getDivision());
        jsonCExam.put(Constants.EXAM_TERM, exam.getTerm());
        jsonCExam.put(Constants.EXAM_SUBJECT_ID, exam.getClassroomSubjectIdclassroomSubject().getSubjectIdsubject().getId());
        jsonCExam.put(Constants.EXAM_TYPE, exam.getExamTypeIdexamType().getId());

        JSONArray jsonArray = new JSONArray();
        if(exam.getExamTypeIdexamType().getId()==Constants.GENERAL_EXAM){
           Iterator<Results> resultsIterator = exam.getResultss().iterator();
            while(resultsIterator.hasNext()){
                JSONObject jsonObject1 = new JSONObject();
                Results results =resultsIterator.next();
                jsonObject1.put(Constants.ADDMISSION_NO, results.getStudentIdstudent().getAdmissionNo());
                jsonObject1.put(Constants.EXAM_RESULTS, results.getResults());
                jsonArray.put(jsonObject1);
            }
        }

        if(exam.getExamTypeIdexamType().getId()==Constants.TERM_EXAM){
            Iterator<Marks> marksIterator = exam.getMarkss().iterator();
            while(marksIterator.hasNext()){
                JSONObject jsonObject1 = new JSONObject();
                Marks marks =marksIterator.next();
                jsonObject1.put(Constants.ADDMISSION_NO, marks.getStudentIdstudent().getAdmissionNo());
                jsonObject1.put(Constants.EXAM_RESULTS, marks.getMarks());
                jsonArray.put(jsonObject1);
            }
        }


        jsonCExam.put(Constants.PERFORMANCE_LIST, jsonArray);

      //  return synchronizer.pushExamPerformance(jsonCExam);
        return null;
    }
}
