//package org.yarlithub.examination.test;
//
//import org.hibernate.Criteria;
//import org.hibernate.Query;
//import org.hibernate.SQLQuery;
//import org.hibernate.criterion.Restrictions;
//import org.junit.Test;
//import org.junit.runner.RunWith;
//import org.springframework.test.context.ContextConfiguration;
//import org.springframework.test.context.junit4.SpringJUnit4ClassRunner;
//import org.springframework.test.context.transaction.TransactionConfiguration;
//import org.springframework.transaction.annotation.Transactional;
//import org.yarlithub.yschool.examination.core.NewCAExamination;
//import org.yarlithub.yschool.examination.dataAccess.ExaminationDBQueries;
//import org.yarlithub.yschool.repository.factories.yschool.YschoolDataPoolFactory;
//import org.yarlithub.yschool.repository.model.obj.yschool.ClassSubject;
//import org.yarlithub.yschool.repository.model.obj.yschool.Classroom;
//import org.yarlithub.yschool.repository.model.obj.yschool.Exam;
//import org.yarlithub.yschool.repository.model.obj.yschool.*;
//
//import org.yarlithub.yschool.repository.services.data.DataLayerYschool;
//import org.yarlithub.yschool.repository.services.data.DataLayerYschoolImpl;
//
//
//
//import java.util.Calendar;
//import java.util.Date;
//import java.util.List;
//
//import static org.junit.Assert.assertEquals;
//import static org.junit.Assert.assertTrue;
//
//
///**
//* Created with IntelliJ IDEA.
//* User: jayrksih
//* Date: 9/17/13
//* Time: 10:17 AM
//* To change this template use File | Settings | File Templates.
//*/
//
//@ContextConfiguration(locations = { "/applicationContext.xml" } )
//@RunWith(SpringJUnit4ClassRunner.class)
//@TransactionConfiguration(transactionManager = "transactionManager", defaultRollback = false)
//public class NewCAExaminationTest {
//
//        @Test
//        @Transactional
//        public void testGetClassid() {
//
////            DataLayerYschool dataLayerYschool = DataLayerYschoolImpl.getInstance();
////            Criteria getclassCriteria = dataLayerYschool.createCriteria(Classroom.class);
////              Date date = new Date();
////            date.setTime(2013);
////            getclassCriteria.add(Restrictions.eq("year", date));
////            getclassCriteria.add(Restrictions.eq("grade", 10));
////            getclassCriteria.add(Restrictions.eq("division", "D"));
////            List<Classroom> list = getclassCriteria.list();
////            int size = list.get(0).getId();
////            System.out.print(size);
//            //assertEquals(size,1);
//    }
//
//    @Test
//    @Transactional
//    public void testInsertExam(){
////        DataLayerYschool DataLayerYschool = DataLayerYschoolImpl.getInstance();
////
//      //  Date date = new Date();
//       // date.setYear(2013);
////        java.sql.Date sqlDate = new java.sql.Date(date.getTime());
////       java.sql.Date sqlYear =  new java.sql.Date(date.getYear());
////        SQLQuery insertExamQuery = DataLayerYschool.createSQLQuery(ExaminationDBQueries.INSERT_EXAM);
////          insertExamQuery.setParameter("date", sqlDate);
////        insertExamQuery.setParameter("term", 1);
////        insertExamQuery.setParameter("year", sqlDate.getYear());
////        insertExamQuery.setParameter("idClass_Subject", 2);
////        insertExamQuery.setParameter("idExam_Type", 2);
////        int success=insertExamQuery.executeUpdate();
////        NewCAExamination newCAExamination = new NewCAExamination();
////        int a= newCAExamination.getClassid(6,"A");
////        System.out.print(a);
//         Calendar cal = Calendar.getInstance();
//        cal.set(2013,1,1);
//        Date date=cal.getTime();
//        DataLayerYschool dataLayerYschool = DataLayerYschoolImpl.getInstance();
//        ClassSubject classsubject = dataLayerYschool.getClassSubject(2);
//        ExamType examType =dataLayerYschool.getExamType(2);
//       // examType.setId(2);
//        Exam exam = YschoolDataPoolFactory.getExam();
//        exam.setDate(date);
//        exam.setTerm(1);
//        exam.setYear(date);
//        exam.setClassSubjectIdclassSubject(classsubject);
//        exam.setExamTypeIdexamType(examType);
//        dataLayerYschool.save(exam);
//       // dataLayerYschool.flushSession();
//
//
//
//    }
//
//}
