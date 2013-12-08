package org.yarlithub.yschool.analytics.core;

import org.primefaces.model.chart.CartesianChartModel;
import org.yarlithub.yschool.repository.model.obj.yschool.ClassroomSubject;

import java.util.ArrayList;
import java.util.List;

/**
 * Created with IntelliJ IDEA.
 * User: kana
 * Date: 11/30/13
 * Time: 6:33 PM
 * To change this template use File | Settings | File Templates.
 */
public class OLSubjectPrediction {
    private ClassroomSubject olSubject;
    private List<String> predictedGrade = new ArrayList<String>();
    private List<Double> termMarks = new ArrayList<Double>();
    private List<Integer> termMarksLower = new ArrayList<Integer>();
    private List<Integer> termMarksUpper = new ArrayList<Integer>();
    private CartesianChartModel linearModelTermMarks;
    private List<String> msgs = new ArrayList<String>();
    private String msg = "Current Term: No Significant Change Detected";
    private String msgWarning = "No Significant Change Detected";
    private String msgValidation = "No Significant Change Detected";
    private boolean msg_available = false;
    private boolean msgWarning_available = false;
    private boolean msgValidation_available = false;
    private boolean isPredictionNotAvailable = false;
    private boolean notAvailable = false;
    private boolean check = false;
    private boolean checkTermMarks = true;
    private String prediction_msg = "The Next Term Prediction: The Student is being consistent!";
    private String prediction_msgWarning = "The Next Term Prediction: The Student is being consistent!";
    private String prediction_msgValidation = "The Next Term Prediction: The Student is being consistent!";
    private boolean prediction_msg_available = false;
    private boolean prediction_msgWarning_available = false;
    private boolean prediction_msgValidation_available = false;
    private String idForAccordion;

    public OLSubjectPrediction() {
        this.setIdForAccordion(this.toString().substring(58, 62));
    }

    public String getIdForAccordion() {
        return idForAccordion;
    }

    public void setIdForAccordion(String idForAccordion) {
        this.idForAccordion = idForAccordion;
    }

    public boolean isNotAvailable() {
        return notAvailable;
    }

    public void setNotAvailable(boolean notAvailable) {
        this.notAvailable = notAvailable;
    }

    public String getPrediction_msg() {
        return prediction_msg;
    }

    public void setPrediction_msg(String prediction_msg) {
        this.prediction_msg = prediction_msg;
    }

    public String getPrediction_msgWarning() {
        return prediction_msgWarning;
    }

    public void setPrediction_msgWarning(String prediction_msgWarning) {
        this.prediction_msgWarning = prediction_msgWarning;
    }

    public String getPrediction_msgValidation() {
        return prediction_msgValidation;
    }

    public void setPrediction_msgValidation(String prediction_msgValidation) {
        this.prediction_msgValidation = prediction_msgValidation;
    }

    public boolean isPrediction_msg_available() {
        return prediction_msg_available;
    }

    public void setPrediction_msg_available(boolean prediction_msg_available) {
        this.prediction_msg_available = prediction_msg_available;
    }

    public boolean isPrediction_msgWarning_available() {
        return prediction_msgWarning_available;
    }

    public void setPrediction_msgWarning_available(boolean prediction_msgWarning_available) {
        this.prediction_msgWarning_available = prediction_msgWarning_available;
    }

    public boolean isPrediction_msgValidation_available() {
        return prediction_msgValidation_available;
    }

    public void setPrediction_msgValidation_available(boolean prediction_msgValidation_available) {
        this.prediction_msgValidation_available = prediction_msgValidation_available;
    }

    public boolean isCheckTermMarks() {
        return checkTermMarks;
    }

    public void setCheckTermMarks(boolean checkTermMarks) {
        this.checkTermMarks = checkTermMarks;
    }

    public boolean isCheck() {
        return check;
    }

    public void setCheck(boolean check) {
        this.check = check;
    }

    public boolean isPredictionNotAvailable() {
        return isPredictionNotAvailable;
    }

    public void setPredictionNotAvailable(boolean predictionNotAvailable) {
        isPredictionNotAvailable = predictionNotAvailable;
    }

    public boolean isMsgValidation_available() {
        return msgValidation_available;
    }

    public void setMsgValidation_available(boolean msgValidation_available) {
        this.msgValidation_available = msgValidation_available;
    }

    public boolean isMsg_available() {
        return msg_available;
    }

    public void setMsg_available(boolean msg_available) {
        this.msg_available = msg_available;
    }

    public boolean isMsgWarning_available() {
        return msgWarning_available;
    }

    public void setMsgWarning_available(boolean msgWarning_available) {
        this.msgWarning_available = msgWarning_available;
    }

    public String getMsgWarning() {
        return msgWarning;
    }

    public void setMsgWarning(String msgWarning) {

        this.msgWarning = msgWarning;
    }

    public String getMsgValidation() {
        return msgValidation;
    }

    public void setMsgValidation(String msgValidation) {
        this.msgValidation = msgValidation;
    }

    public String getMsg() {
        return msg;
    }

    public void setMsg(String msg) {
        this.msg = msg;
    }

    public CartesianChartModel getLinearModelTermMarks() {
        return linearModelTermMarks;
    }

    public void setLinearModelTermMarks(CartesianChartModel linearModelTermMarks) {
        this.linearModelTermMarks = linearModelTermMarks;
    }

    public List<String> getMsgs() {
        return msgs;
    }

    public void setMsgs(List<String> msgs) {
        this.msgs = msgs;
    }

    public List<Integer> getTermMarksLower() {
        return termMarksLower;
    }

    public void setTermMarksLower(List<Integer> termMarksLower) {
        this.termMarksLower = termMarksLower;
    }

    public List<Integer> getTermMarksUpper() {
        return termMarksUpper;
    }

    public void setTermMarksUpper(List<Integer> termMarksUpper) {
        this.termMarksUpper = termMarksUpper;
    }

    public ClassroomSubject getOlSubject() {
        return olSubject;
    }

    public void setOlSubject(ClassroomSubject olSubject) {
        this.olSubject = olSubject;
    }

    public List<String> getPredictedGrade() {
        return predictedGrade;
    }

    public void setPredictedGrade(List<String> predictedGrade) {
        this.predictedGrade = predictedGrade;
    }

    public List<Double> getTermMarks() {
        return termMarks;
    }

    public void setTermMarks(List<Double> termMarks) {
        this.termMarks = termMarks;
    }


}