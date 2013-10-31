package org.yarlithub.yschool.examination.core;

import org.hibernate.Criteria;
import org.hibernate.criterion.Order;
import org.hibernate.criterion.Restrictions;
import org.yarlithub.yschool.repository.model.obj.yschool.Exam;
import org.yarlithub.yschool.repository.model.obj.yschool.Marks;
import org.yarlithub.yschool.repository.services.data.DataLayerYschool;
import org.yarlithub.yschool.repository.services.data.DataLayerYschoolImpl;

import java.util.List;

/**
 * Created with IntelliJ IDEA.
 * User: jayrksih
 * Date: 10/25/13
 * Time: 12:20 AM
 * To change this template use File | Settings | File Templates.
 */


public class ListExamination {
    DataLayerYschool dataLayerYschool = DataLayerYschoolImpl.getInstance();

    /**
     * Retrieve last inserted exam entries as Exam objects ordered by id:autoincrement .
     *
     * @param first the first entry, for pagination.
     * @param max   maximum objects to retrieve.
     * @return list of latest Exam objects.
     */
    public List<Exam> getLatestExams(int first, int max) {
        Criteria examCriteria = dataLayerYschool.createCriteria(Exam.class);
        examCriteria.addOrder(Order.desc("id"));
        examCriteria.setFirstResult(first);
        examCriteria.setMaxResults(max);
        List<Exam> examlist = examCriteria.list();
        return examlist;
    }

    /**
     * Returns Marks list of specified exam id.
     *
     * @param examid Requested exam id.
     * @return
     */

    public List<Marks> getExamMarks(Integer examid) {
        Exam exam = dataLayerYschool.getExam(examid);
        Criteria marksCriteria = dataLayerYschool.createCriteria(Marks.class);
        marksCriteria.add(Restrictions.eq("examIdexam", exam));
        return marksCriteria.list();
    }
}
