package org.yarlithub.yschool.analytics.datasync;

import org.hibernate.Criteria;
import org.hibernate.Hibernate;
import org.hibernate.criterion.Restrictions;
import org.yarlithub.yschool.repository.factories.yschool.YschoolDataPoolFactory;
import org.yarlithub.yschool.repository.model.obj.yschool.Exam;
import org.yarlithub.yschool.repository.model.obj.yschool.ExamSync;
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

    public boolean addNewSyncExam(int examid){
        Exam exam = dataLayerYschool.getExam(examid);

        /*passing null while creating examsync to make sure no exams are associated with it in the beginning
        * to avoid notnull constrain error of examsync table*/
        ExamSync examSync = YschoolDataPoolFactory.getExamSync(null);
        examSync.setExamIdexam(exam);
        examSync.setSyncStatus(SyncStatus.NOT_SYNCED);
        examSync.setClassIdexam(0);
        dataLayerYschool.save(examSync);
        dataLayerYschool.flushSession();
        if(examSync.getId()>0){
            return true;
        }
        return false;
    }

    public List<Exam> getNotSyncedExams(){

         List<Exam> examList= new ArrayList<Exam>();
        Criteria c = dataLayerYschool.createCriteria(ExamSync.class);
        c.add(Restrictions.eq("syncStatus",0));
        List<ExamSync> examSyncList=c.list();
        Iterator<ExamSync> examSyncIterator = examSyncList.iterator();
        while (examSyncIterator.hasNext()){
            ExamSync examSync = examSyncIterator.next();
            Hibernate.initialize(examSync.getExamIdexam());
               examList.add(examSync.getExamIdexam());
        }
        return examList;
    }
}
