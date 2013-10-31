package org.yarlithub.yschool.analytics.reporting;

/**
 * Created with IntelliJ IDEA.
 * User: kana
 * Date: 10/29/13
 * Time: 3:10 PM
 * To change this template use File | Settings | File Templates.
 */

import java.util.ArrayList;
import java.util.List;

public class DataBeanMaker {


    public List<StudentDataBean> getStudentDataBeanList() {

        List<StudentDataBean> dataBeanList = new ArrayList<StudentDataBean>();


        dataBeanList.add(produce("Kana", "9", "A", "Term-2", getSubjectBeanList()));
        //   dataBeanList.add(produce("Kana", "Sri", "bad", "Trust me"));


        return dataBeanList;


    }

    private StudentDataBean produce(String studentName, String grade, String division,
                                    String termId) {
        StudentDataBean dataBean = new StudentDataBean();
        dataBean.setStudentName(studentName);
        dataBean.setGrade(grade);
        dataBean.setDivision(division);
        dataBean.setTermId(termId);

        return dataBean;

    }

    private StudentDataBean produce(String studentName, String grade, String division,
                                    String termId, List<SubjectDataBean> subjectDataBeanArrayList) {
        StudentDataBean dataBean = new StudentDataBean();

        dataBean.setStudentName(studentName);
        dataBean.setGrade(grade);
        dataBean.setDivision(division);
        dataBean.setTermId(termId);
        dataBean.setSubjectDataBeanList(subjectDataBeanArrayList);

        return dataBean;

    }



    public List<SubjectDataBean> getSubjectBeanList() {
        List<SubjectDataBean> subjectBeanList = new ArrayList<SubjectDataBean>();
        subjectBeanList.add(getSubject("Tamil", 98.1));
        subjectBeanList.add(getSubject("English", 47.7));
        subjectBeanList.add(getSubject("Mathematics", 57));
        subjectBeanList.add(getSubject("Religion", 78));
        subjectBeanList.add(getSubject("Science and Technology", 67.4));
        subjectBeanList.add(getSubject("SocialStudies", 97.4));

        return subjectBeanList;
    }


    private SubjectDataBean getSubject(String subject, double marks) {
        SubjectDataBean subjectDataBean = new SubjectDataBean();

        subjectDataBean.setSubject(subject);
        subjectDataBean.setMarks(marks);
        return subjectDataBean;
    }
}