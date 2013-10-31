package org.yarlithub.yschool.analytics.reporting;


import java.util.List;

public class StudentDataBean {


    private String studentName;
    private String division;
    private String termId;
    private List<SubjectDataBean> subjectDataBeanList;
    private String grade;



    private int term;
    private int year;

    private int noOfDays;
    private int attendance;

    private String classTeacherName;
    private List<Double> marks;
    private List<Double> caMarks ;
    private List<String> subjects ;
    private int classRank;
    private int batchRank;
    private float totalMarks;
    private float average;


    public List<SubjectDataBean> getSubjectDataBeanList() {
        return subjectDataBeanList;
    }

    public void setSubjectDataBeanList(List<SubjectDataBean> subjectDataBeanList) {
        this.subjectDataBeanList = subjectDataBeanList;
    }

    public String getStudentName() {
        return studentName;
    }

    public void setStudentName(String studentName) {
        this.studentName = studentName;
    }

    public String getTermId() {
        return termId;
    }

    public void setTermId(String termId) {
        this.termId = termId;
    }

    public int getYear() {
        return year;
    }

    public void setYear(int year) {
        this.year = year;
    }

    public String getGrade() {
        return grade;
    }

    public void setGrade(String grade) {
        this.grade = grade;
    }

    public int getTerm() {
        return term;
    }

    public void setTerm(int term) {
        this.term = term;
    }

    public int getNoOfDays() {
        return noOfDays;
    }

    public void setNoOfDays(int noOfDays) {
        this.noOfDays = noOfDays;
    }

    public int getAttendance() {
        return attendance;
    }

    public void setAttendance(int attendance) {
        this.attendance = attendance;
    }

    public String getDivision() {
        return division;
    }

    public void setDivision(String division) {
        this.division = division;
    }

    public String getClassTeacherName() {
        return classTeacherName;
    }

    public void setClassTeacherName(String classTeacherName) {
        this.classTeacherName = classTeacherName;
    }

    public List<Double> getMarks() {
        return marks;
    }

    public void setMarks(List<Double> marks) {
        this.marks = marks;
    }

    public List<Double> getCaMarks() {
        return caMarks;
    }

    public void setCaMarks(List<Double> caMarks) {
        this.caMarks = caMarks;
    }

    public List<String> getSubjects() {
        return subjects;
    }

    public void setSubjects(List<String> subjects) {
        this.subjects = subjects;
    }

    public int getClassRank() {
        return classRank;
    }

    public void setClassRank(int classRank) {
        this.classRank = classRank;
    }

    public int getBatchRank() {
        return batchRank;
    }

    public void setBatchRank(int batchRank) {
        this.batchRank = batchRank;
    }

    public float getTotalMarks() {
        return totalMarks;
    }

    public void setTotalMarks(float totalMarks) {
        this.totalMarks = totalMarks;
    }

    public float getAverage() {
        return average;
    }

    public void setAverage(float average) {
        this.average = average;
    }















}
