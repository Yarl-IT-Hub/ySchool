package org.yarlithub.yschool.analytics.datasync;

import org.yarlithub.yschool.repository.factories.yschool.YschoolDataPoolFactory;
import org.yarlithub.yschool.repository.model.obj.yschool.Exam;
import org.yarlithub.yschool.repository.model.obj.yschool.ExamSync;
import org.yarlithub.yschool.repository.services.data.DataLayerYschool;
import org.yarlithub.yschool.repository.services.data.DataLayerYschoolImpl;

/**
 * Created with IntelliJ IDEA.
 * User: Jay Krish
 * Date: 11/25/13
 * Time: 5:01 AM
 * To change this template use File | Settings | File Templates.
 */
public class SyncExamination {
    DataLayerYschool dataLayerYschool = DataLayerYschoolImpl.getInstance();

    public boolean addNewSyncExam(Exam exam){

        ExamSync examSync = YschoolDataPoolFactory.getExamSync(null);
        examSync.setExamIdexam(exam);
        examSync.setSyncStatus(0);
        examSync.setClassIdexam(0);
        dataLayerYschool.save(examSync);
        dataLayerYschool.flushSession();
        if(examSync.getId()>0){
            return true;
        }
        return false;
    }
}
