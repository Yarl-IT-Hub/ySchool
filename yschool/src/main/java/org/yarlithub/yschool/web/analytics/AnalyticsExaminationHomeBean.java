package org.yarlithub.yschool.web.analytics;

import com.arima.classanalyzer.analyzer.ExamComparator;
import com.arima.classanalyzer.core.ExamStandard;
import org.primefaces.model.chart.CartesianChartModel;
import org.primefaces.model.chart.ChartSeries;
import org.primefaces.model.chart.LineChartSeries;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.repository.model.obj.yschool.Exam;
import org.yarlithub.yschool.service.AnalyticsService;
import org.yarlithub.yschool.service.ExaminationService;
import org.yarlithub.yschool.service.StudentService;
import org.yarlithub.yschool.web.examination.ExaminationController;

import javax.faces.bean.ManagedBean;
import java.io.Serializable;
import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;

/**
 * Created with IntelliJ IDEA.
 * User: Jay Krish
 * Date: 11/2/13
 * Time: 10:25 PM
 * To change this template use File | Settings | File Templates.
 */

@ManagedBean
@Scope(value = "session")
@Controller
public class AnalyticsExaminationHomeBean implements Serializable {
    @Autowired
    private StudentService studentService;
    @Autowired
    private ExaminationService examinationService;
    @Autowired
    private AnalyticsService analyticsService;
    @Autowired
    private ExaminationController examinationController;
    private Exam exam;
    private CartesianChartModel overallComp;
    private CartesianChartModel individualComp;
    private String generalString;
    private String termString;
    private int[] generalIntArray;
    private int[] termIntArray;
    private double seqAlignScore;
    private double jacScore;
    private List<Integer> termIntegerArrayList;
    private List<Integer> generalIntegerArrayList;
    private CartesianChartModel indivSim;

    public List<Integer> getTermIntegerArrayList() {
        return termIntegerArrayList;
    }

    public void setTermIntegerArrayList(List<Integer> termIntegerArrayList) {
        this.termIntegerArrayList = termIntegerArrayList;
    }

    public List<Integer> getGeneralIntegerArrayList() {
        return generalIntegerArrayList;
    }

    public void setGeneralIntegerArrayList(List<Integer> generalIntegerArrayList) {
        this.generalIntegerArrayList = generalIntegerArrayList;
    }

    public CartesianChartModel getIndivSim() {
        return indivSim;
    }

    public void setIndivSim(CartesianChartModel indivSim) {
        this.indivSim = indivSim;
    }

    public CartesianChartModel getIndividualComp() {

        return individualComp;
    }

    public void setIndividualComp(CartesianChartModel individualComp) {
        this.individualComp = individualComp;

    }

    public CartesianChartModel getOverallComp() {
        //createOverComp();
        return overallComp;
    }

    public void setOverallComp(CartesianChartModel overallComp) {
        this.overallComp = overallComp;
    }

    public ExaminationService getExaminationService() {
        return examinationService;
    }

    public void setExaminationService(ExaminationService examinationService) {
        this.examinationService = examinationService;
    }

    public String getGeneralString() {
        return generalString;
    }

    public void setGeneralString(String generalString) {
        this.generalString = generalString;
    }

    public String getTermString() {
        return termString;
    }

    public void setTermString(String termString) {
        this.termString = termString;
    }

    public int[] getGeneralIntArray() {
        return generalIntArray;
    }

    public void setGeneralIntArray(int[] generalIntArray) {
        this.generalIntArray = generalIntArray;
    }

    public int[] getTermIntArray() {
        return termIntArray;
    }

    public void setTermIntArray(int[] termIntArray) {
        this.termIntArray = termIntArray;
    }

    public double getSeqAlignScore() {
        return seqAlignScore;
    }

    public void setSeqAlignScore(double seqAlignScore) {
        this.seqAlignScore = seqAlignScore;
    }

    public double getJacScore() {
        return jacScore;
    }

    public void setJacScore(double jacScore) {
        this.jacScore = jacScore;
    }

    public Exam getExam() {
        return exam;
    }

    public void setExam(Exam exam) {
        this.exam = exam;
    }

    private CartesianChartModel createOverComp() {
        CartesianChartModel model = new CartesianChartModel();

        LineChartSeries termGrade = new LineChartSeries();
        LineChartSeries generalGrade = new LineChartSeries();

        termGrade.setLabel("Term Grades");
        generalGrade.setLabel("General Grades");

        termGrade.set("A", termIntArray[4]);
        termGrade.set("B", termIntArray[3]);
        termGrade.set("C", termIntArray[2]);
        termGrade.set("S", termIntArray[1]);
        termGrade.set("F", termIntArray[0]);
//
//
        generalGrade.set("A", generalIntArray[4]);
        generalGrade.set("B", generalIntArray[3]);
        generalGrade.set("C", generalIntArray[2]);
        generalGrade.set("S", generalIntArray[1]);
        generalGrade.set("F", generalIntArray[0]);


//        termGrade.set(1, termIntArray[4]);
//        termGrade.set(2, termIntArray[3]);
//        termGrade.set(3, termIntArray[2]);
//        termGrade.set(4, termIntArray[1]);
//        termGrade.set(5, termIntArray[0]);


//        generalGrade.set(1, generalIntArray[4]);
//        generalGrade.set(2, generalIntArray[3]);
//        generalGrade.set(3, generalIntArray[2]);
//        generalGrade.set(4, generalIntArray[1]);
//        generalGrade.set(5, generalIntArray[0]);

        model.addSeries(termGrade);
        model.addSeries(generalGrade);

        return model;
    }

    private CartesianChartModel createIndividualComp() {
        CartesianChartModel model = new CartesianChartModel();

        LineChartSeries termGrade = new LineChartSeries();
        LineChartSeries generalGrade = new LineChartSeries();


        termGrade.setLabel("Term Grades");
        generalGrade.setLabel("General Grades");


        Iterator<Integer> termIterator = termIntegerArrayList.iterator();
        int i = 0;
        while (termIterator.hasNext()) {
            i++;
            int k = termIterator.next();
            if (i % 5 == 0) {
                termGrade.set(i, k);
            }
        }

        Iterator<Integer> generalIterator = generalIntegerArrayList.iterator();
        i = 0;
        while (generalIterator.hasNext()) {
            i++;
            int k = generalIterator.next();
            if (i % 5== 0) {
                generalGrade.set(i, k);
            }

        }
        model.addSeries(termGrade);
        model.addSeries(generalGrade);

        return model;
    }

    private CartesianChartModel createIndivSim() {
        indivSim = new CartesianChartModel();

        ChartSeries termGrade = new ChartSeries();
        termGrade.setLabel("Term Exam Grades");
//
//        termGrade.set("2004", 120);
//        termGrade.set("2005", 100);
//        termGrade.set("2006", 44);
//        termGrade.set("2007", 150);
//        termGrade.set("2008", 25);

        ChartSeries generalGrade = new ChartSeries();
        generalGrade.setLabel("GeneralExam Grades");

//        generalGrade.set("2004", 52);
//        generalGrade.set("2005", 60);
//        generalGrade.set("2006", 110);
//        generalGrade.set("2007", 135);
//        generalGrade.set("2008", 120);

        indivSim.addSeries(termGrade);
        indivSim.addSeries(generalGrade);


        Iterator<Integer> termIterator = termIntegerArrayList.iterator();
        int i = 0;
        while (termIterator.hasNext()) {

            int k = termIterator.next();
            if (i % 5 == 0) {
                termGrade.set(i, k);
            }
            i++;
        }

        Iterator<Integer> generalIterator = generalIntegerArrayList.iterator();
        i = 0;
        while (generalIterator.hasNext()) {

            int k = generalIterator.next();
            if (i % 5 == 0) {
                generalGrade.set(i, k);
            }

            i++;

        }


        indivSim.addSeries(termGrade);
        indivSim.addSeries(generalGrade);
        return indivSim;
    }

    public boolean preloadExam() {
        ExamStandard examStandard = new ExamStandard();
        exam = examinationController.getExam();
        // exam =examinationService.getExambyId(2);
        try {
            examStandard = ExamComparator.getExamStandard(11086, this.exam.getClassroomSubjectIdclassroomSubject().getClassroomIdclassroom().getYear(), this.exam.getClassroomSubjectIdclassroomSubject().getClassroomIdclassroom().getGradeIdgrade().getGrade(), this.exam.getClassroomSubjectIdclassroomSubject().getSubjectIdsubject().getName());
        } catch (Exception e) {
            //To change body of catch statement use File | Settings | File Templates.
        }

        generalString = examStandard.getGeneral();
        termString = examStandard.getTerm();
        generalIntArray = examStandard.getGeneralCount();
        termIntArray = examStandard.getTermCount();
        jacScore = examStandard.getJaccardIndex();
        seqAlignScore = examStandard.getSequenceAlignmentScore();


        char[] termCharArray = termString.toCharArray();
        char[] generalCharArray = generalString.toCharArray();

        List<Character> termCharArrayList = new ArrayList<>();
        List<Character> generalCharArrayList = new ArrayList<>();
        termIntegerArrayList = new ArrayList<>();
        generalIntegerArrayList = new ArrayList<>();
        int itr = 0;

        while (itr < termCharArray.length) {
            termCharArrayList.add(termCharArray[itr]);
            generalCharArrayList.add(generalCharArray[itr]);
            itr++;
        }

        Iterator<Character> iteratorTerm = termCharArrayList.iterator();
        Iterator<Character> iteratorGeneral = generalCharArrayList.iterator();

        while (iteratorGeneral.hasNext() && iteratorTerm.hasNext()) {
            char gradeGeneral = iteratorGeneral.next();
            char gradeTerm = iteratorTerm.next();

            if (gradeGeneral == 'A') {
                generalIntegerArrayList.add(5);
            }

            if (gradeGeneral == 'B') {
                generalIntegerArrayList.add(4);
            }
            if (gradeGeneral == 'C') {
                generalIntegerArrayList.add(3);
            }
            if (gradeGeneral == 'S') {
                generalIntegerArrayList.add(2);
            }
            if (gradeGeneral == 'F') {
                generalIntegerArrayList.add(1);
            }


            if (gradeTerm == 'A') {
                termIntegerArrayList.add(5);
            }

            if (gradeTerm == 'B') {
                termIntegerArrayList.add(4);
            }
            if (gradeTerm == 'C') {
                termIntegerArrayList.add(3);
            }
            if (gradeTerm == 'S') {
                termIntegerArrayList.add(2);
            }
            if (gradeTerm == 'F') {
                termIntegerArrayList.add(1);
            }


        }


        this.setOverallComp(createOverComp());

        this.setIndividualComp(createIndividualComp());  //createIndividualComp();

        this.setIndivSim(createIndivSim());

        return true;

    }

}

