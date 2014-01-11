package org.yarlithub.yschool.web.analytics;

import org.primefaces.model.chart.CartesianChartModel;
import org.primefaces.model.chart.LineChartSeries;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.analytics.core.OLSubjectPrediction;
import org.yarlithub.yschool.repository.model.obj.yschool.ClassroomModule;
import org.yarlithub.yschool.repository.model.obj.yschool.Student;
import org.yarlithub.yschool.service.AnalyticsService;
import org.yarlithub.yschool.service.StudentService;

import javax.faces.bean.ManagedBean;
import javax.faces.model.DataModel;
import javax.faces.model.ListDataModel;
import java.io.Serializable;
import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;

import static com.arima.classanalyzer.core.CFinal.predictNextTerm;

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
public class AnalyticsStudentHomeBean implements Serializable {
    @Autowired
    private StudentService studentService;
    @Autowired
    private AnalyticsService analyticsService;
    @Autowired
    private AnalyticsController analyticsController;
    private Student student;
    private DataModel secondarySubjects;
    private DataModel<ClassroomModule> oLSubjects;
    private DataModel<ClassroomModule> oLSubjectsEleven;
    private DataModel<OLSubjectPrediction> olSubjectPredictions;
    private DataModel aLSubjects;
    private CartesianChartModel linearModel;
    private CartesianChartModel linearModelTermMarks;

    public Student getStudent() {
        return student;
    }

    public void setStudent(Student student) {
        this.student = student;
    }

    public DataModel<OLSubjectPrediction> getOlSubjectPredictions() {
        return olSubjectPredictions;
    }

    public void setOlSubjectPredictions(DataModel<OLSubjectPrediction> olSubjectPredictions) {
        this.olSubjectPredictions = olSubjectPredictions;
    }

    public DataModel getoLSubjects() {
        return oLSubjects;
    }

    public void setoLSubjects(DataModel oLSubjects) {
        this.oLSubjects = oLSubjects;
    }

    public CartesianChartModel getLinearModel() {
        return linearModel;
    }

    public void setLinearModel(CartesianChartModel linearModel) {
        this.linearModel = linearModel;
    }

//    private void createLinearModelTermMarks(OLSubjectPrediction olSubjectPrediction) {
//        linearModelTermMarks = new CartesianChartModel();
//
//        LineChartSeries termMarks = new LineChartSeries();
//
//
//        termMarks.setLabel("Term Marks");
//
//        if (olSubjectPrediction.getTermMarks().get(0) != -.1) {
//            termMarks.set(1, olSubjectPrediction.getTermMarks().get(0));
//        }
//        if (olSubjectPrediction.getTermMarks().get(1) != -.1) {
//            termMarks.set(2, olSubjectPrediction.getTermMarks().get(1));
//        }
//        if (olSubjectPrediction.getTermMarks().get(2) != -.1) {
//            termMarks.set(3, olSubjectPrediction.getTermMarks().get(2));
//        }
//        if (olSubjectPrediction.getTermMarks().get(3) != -.1) {
//            termMarks.set(4, olSubjectPrediction.getTermMarks().get(3));
//        }
//        if (olSubjectPrediction.getTermMarks().get(4) != -.1) {
//            termMarks.set(5, olSubjectPrediction.getTermMarks().get(4));
//        }
//
////        if (olSubjectPrediction.getTermMarks().get(5) != -.1) {
////            termMarks.set(6, olSubjectPrediction.getTermMarks().get(5));
////        }
//
//
//        linearModelTermMarks.addSeries(termMarks);
//        // linearModel.addSeries(series2);
//        setLinearModelTermMarks(linearModelTermMarks);
//
//    }

    private CartesianChartModel createLinearModelTermMarksForOlSub(OLSubjectPrediction olSubjectPrediction) {
        CartesianChartModel model = new CartesianChartModel();

        LineChartSeries termMarks = new LineChartSeries();
        LineChartSeries lowerBound = null;

        termMarks.setLabel("Term Marks");

        if (olSubjectPrediction.getTermMarks().get(0) >= 0) {
            termMarks.set(1, olSubjectPrediction.getTermMarks().get(0));
        }
        if (olSubjectPrediction.getTermMarks().get(1) >= 0) {
            termMarks.set(2, olSubjectPrediction.getTermMarks().get(1));
        }
        if (olSubjectPrediction.getTermMarks().get(2) >= 0) {
            termMarks.set(3, olSubjectPrediction.getTermMarks().get(2));
        }
        if (olSubjectPrediction.getTermMarks().get(3) >= 0) {
            termMarks.set(4, olSubjectPrediction.getTermMarks().get(3));
        }
        if (olSubjectPrediction.getTermMarks().get(4) >= 0) {
            termMarks.set(5, olSubjectPrediction.getTermMarks().get(4));
        }

//        if (olSubjectPrediction.getTermMarks().get(5) >= 0) {
//            termMarks.set(6, olSubjectPrediction.getTermMarks().get(5));
//        }


        if (!olSubjectPrediction.isCheck()) {
            LineChartSeries upperBound = new LineChartSeries();


            upperBound.setLabel("Upper Bound");


            upperBound.set(2, olSubjectPrediction.getTermMarksUpper().get(0));
            upperBound.set(3, olSubjectPrediction.getTermMarksUpper().get(1));
            upperBound.set(4, olSubjectPrediction.getTermMarksUpper().get(2));
            upperBound.set(5, olSubjectPrediction.getTermMarksUpper().get(3));
            upperBound.set(6, olSubjectPrediction.getTermMarksUpper().get(4));


            lowerBound = new LineChartSeries();


            lowerBound.setLabel("Lower Bound");


            lowerBound.set(2, olSubjectPrediction.getTermMarksLower().get(0));
            lowerBound.set(3, olSubjectPrediction.getTermMarksLower().get(1));
            lowerBound.set(4, olSubjectPrediction.getTermMarksLower().get(2));
            lowerBound.set(5, olSubjectPrediction.getTermMarksLower().get(3));
            lowerBound.set(6, olSubjectPrediction.getTermMarksLower().get(4));


            model.addSeries(upperBound);
        }
        //upperBound.setMarkerStyle();
        model.addSeries(termMarks);
        if (!olSubjectPrediction.isCheck()) {
            model.addSeries(lowerBound);
        }
        return model;
    }

    public CartesianChartModel getLinearModelTermMarks() {
        return linearModelTermMarks;
    }

    public void setLinearModelTermMarks(CartesianChartModel linearModelTermMarks) {
        this.linearModelTermMarks = linearModelTermMarks;
    }

    public boolean preloadStudent() {

        // this.student = analyticsService.getStudenById(39);
        this.student = analyticsController.getStudent();
        this.oLSubjects = new ListDataModel(analyticsService.getOLSubjects(student));
        this.oLSubjectsEleven = new ListDataModel(analyticsService.getOLSubjectsEleven(student));


        List<OLSubjectPrediction> olSubjectPredictions = new ArrayList<>();

        double termMark = 0.0;

        Iterator<ClassroomModule> olsubjectIterator = oLSubjects.iterator();
        Iterator<ClassroomModule> olsubjectElevenIterator = oLSubjectsEleven.iterator();
        OLSubjectPrediction olSubjectPrediction = null;

        while (true) {
            List<Double> termMarks = new ArrayList<Double>();

            ClassroomModule olSubject = null;
            ClassroomModule olsubjectEleven = null;

            if (olsubjectIterator.hasNext() && olsubjectElevenIterator.hasNext()) {
                olSubject = olsubjectIterator.next();
                olsubjectEleven = olsubjectElevenIterator.next();

            } else {

                break;

            }

            ArrayList<Integer> previousTermMarks = new ArrayList<>();
            ArrayList<Integer> predictedTermMarksLower = new ArrayList<>();
            ArrayList<Integer> predictedTermMarksUpper = new ArrayList<>();
            ArrayList<Integer> range = new ArrayList<>();
            olSubjectPrediction = new OLSubjectPrediction();
            boolean check = true;

            for (int term = 1; term <= 3; term++) {
                termMark = analyticsService.getTermMarksForOLSub(this.student, olSubject, term);
                if (termMark >= 0 && termMark <= 100) {

                    termMarks.add(termMark);
                    int mark = (int) termMark;

                    if (check) {
                        previousTermMarks.add(mark);
                        try {
                            // range = predictNextTerm(null, 2008, 10, term, olSubject.getModuleIdmodule().getName(), student.getId(), previousTermMarks);


                            if (term < 3) {
                                range = predictNextTerm(null, 2008, 10, term + 1, olSubject.getModuleIdmodule().getSubjectIdsubject().getSubjectName(), student.getId(), previousTermMarks);
                            }

                            if (term == 3) {
                                range = predictNextTerm(null, 2009, 11, 1, olSubject.getModuleIdmodule().getSubjectIdsubject().getSubjectName(), student.getId(), previousTermMarks);

                            }

                        } catch (Exception e) {

                        }
                    }


                }
// else if (termMark == -1) {
//                    termMarks.add(-0.1);
//                } else if (termMark == -2) {
//                    termMarks.add(-0.2);
//                } else if (termMark == -3) {
//                    termMarks.add(-0.3);
//                }
                else {
                    check = false;
                    termMark = -1.0;
                    termMarks.add(termMark);
                    range.add(-1);
                    range.add(-1);
                    olSubjectPrediction.setCheck(true);
                    //break;

                }


                predictedTermMarksLower.add(range.get(0));
                predictedTermMarksUpper.add(range.get(1));

            }


            for (int term = 1; term <= 3; term++) {
                termMark = analyticsService.getTermMarksForOLSub(this.student, olsubjectEleven, term);
                if (termMark >= 0 && termMark <= 100) {
                    termMarks.add(termMark);
                    olSubjectPrediction.setCheckTermMarks(false);
                    int mark = (int) termMark;

                    if (check) {
                        previousTermMarks.add(mark);
                        try {
                            if (term < 3) {
                                range = predictNextTerm(null, 2008, 11, term + 1, olSubject.getModuleIdmodule().getSubjectIdsubject().getSubjectName(), student.getId(), previousTermMarks);
                            }

                            if (term == 3) {
                                //range = predictNextTerm(null, 2008, 11, 1, olSubject.getModuleIdmodule().getName(), student.getId(), previousTermMarks);

                            }

                        } catch (Exception e) {

                        }

                    }
                }

                //else if (termMark == -1) {
//                    termMarks.add(-0.1);
//                } else if (termMark == -2) {
//                    termMarks.add(-0.2);
//                } else if (termMark == -3) {
//                    termMarks.add(-0.3);
//                }

                else {
                    check = false;
                    termMark = -1.0;
                    termMarks.add(termMark);
                    range.add(-1);
                    range.add(-1);
                    olSubjectPrediction.setCheck(true);
                    // break;

                }


                if (term < 3) {
                    predictedTermMarksLower.add(range.get(0));
                    predictedTermMarksUpper.add(range.get(1));
                }

            }


            olSubjectPrediction.setOlSubject(olSubject);
            olSubjectPrediction.setTermMarks(termMarks);
            olSubjectPrediction.setTermMarksUpper(predictedTermMarksUpper);
            olSubjectPrediction.setTermMarksLower(predictedTermMarksLower);


//            createLinearModelTermMarks(olSubjectPrediction);
//            olSubjectPrediction.setLinearModelTermMarks(linearModelTermMarks);

            olSubjectPredictions.add(olSubjectPrediction);


        }

        this.olSubjectPredictions = new ListDataModel<OLSubjectPrediction>(olSubjectPredictions);

        Iterator<OLSubjectPrediction> iterator = olSubjectPredictions.iterator();
        while (iterator.hasNext()) {
            OLSubjectPrediction olSubjectPrediction_tmp = iterator.next();

            olSubjectPrediction_tmp.setLinearModelTermMarks(createLinearModelTermMarksForOlSub(olSubjectPrediction_tmp));

        }

        Iterator<OLSubjectPrediction> iterator1 = olSubjectPredictions.iterator();

        while (iterator1.hasNext()) {
            OLSubjectPrediction olSubjectPrediction_tmp = iterator1.next();
            // List<String> msgs = new ArrayList<>();
            //  String msg = olSubjectPrediction_tmp.getMsg();

            if (olSubjectPrediction_tmp.isCheck()) {
                olSubjectPrediction_tmp.setMsg(null);
                olSubjectPrediction_tmp.setMsgWarning(null);
                olSubjectPrediction_tmp.setMsgValidation(null);

                continue;
            }
//            int index = 0;
//            while (index < olSubjectPrediction_tmp.getTermMarksLower().size()-1) {
            //msgs = new ArrayList<>();

            int recentIndex = olSubjectPrediction_tmp.getTermMarks().size() - 1;
            double termMarks1;
            termMarks1 = olSubjectPrediction_tmp.getTermMarks().get(recentIndex - 1);
            double lower = olSubjectPrediction_tmp.getTermMarksLower().get(recentIndex - 2);
            double upper = olSubjectPrediction_tmp.getTermMarksUpper().get(recentIndex - 2);
            double upper_prediction = olSubjectPrediction_tmp.getTermMarksUpper().get(recentIndex - 1);
            double lower_prediction = olSubjectPrediction_tmp.getTermMarksUpper().get(recentIndex - 1);


            if (upper < upper_prediction) {
                olSubjectPrediction_tmp.setPrediction_msgValidation(MessageStudentHome.future_positive);
                olSubjectPrediction_tmp.setPrediction_msgValidation_available(true);
            } else if (lower > lower_prediction) {
                olSubjectPrediction_tmp.setPrediction_msgWarning(MessageStudentHome.future_negative
                );
                olSubjectPrediction_tmp.setPrediction_msgWarning_available(true);
            } else if (lower <= lower_prediction || upper_prediction <= upper) {
                olSubjectPrediction_tmp.setPrediction_msg(MessageStudentHome.future_information);
                olSubjectPrediction_tmp.setPrediction_msg_available(true);
            }


            if (upper >= termMarks1 && termMarks1 >= lower) {
                //index++;
                olSubjectPrediction_tmp.setMsg(MessageStudentHome.info_consis);
                olSubjectPrediction_tmp.setMsg_available(true);
                continue;


            }
            if (upper < termMarks1) {
                // msgs.add(MessageStudentHome.appreciation);
                olSubjectPrediction_tmp.setMsgValidation(MessageStudentHome.appreciation);
                olSubjectPrediction_tmp.setMsgValidation_available(true);
                olSubjectPrediction_tmp.setMsg(null);
                olSubjectPrediction_tmp.setMsgWarning(null);
                continue;
            }
            if (lower > termMarks1) {
                olSubjectPrediction_tmp.setMsgWarning(MessageStudentHome.warning);
                olSubjectPrediction_tmp.setMsgWarning_available(true);
                olSubjectPrediction_tmp.setMsg(null);
                olSubjectPrediction_tmp.setMsgValidation(null);
                continue;

            } else {
                olSubjectPrediction_tmp.setMsg_available(true);
            }


            //    index++;
        }

        // olSubjectPrediction_tmp.setMsgs(msg);

        //   }


        return true;
    }

    public String viewMatchingProfiles() {

        return "Success";

    }

}
