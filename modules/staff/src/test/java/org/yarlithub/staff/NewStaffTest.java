//package org.yarlithub.staff;//package org.yarlithub.examination.test;
//
//import org.junit.Test;
//import org.junit.runner.RunWith;
//import org.springframework.test.context.ContextConfiguration;
//import org.springframework.test.context.junit4.SpringJUnit4ClassRunner;
//import org.springframework.test.context.transaction.TransactionConfiguration;
//import org.springframework.transaction.annotation.Transactional;
//import org.yarlithub.yschool.repository.factories.yschool.YschoolDataPoolFactory;
//import org.yarlithub.yschool.repository.model.obj.yschool.Staff;
//import org.yarlithub.yschool.repository.services.data.DataLayerYschool;
//import org.yarlithub.yschool.repository.services.data.DataLayerYschoolImpl;
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
//@ContextConfiguration(locations = {"/applicationContext.xml"})
//@RunWith(SpringJUnit4ClassRunner.class)
//@TransactionConfiguration(transactionManager = "transactionManager", defaultRollback = false)
//public class NewStaffTest {
//
//    @Test
//    @Transactional
//    public void tesNewStaff() {
//
//        DataLayerYschool dataLayerYschool = DataLayerYschoolImpl.getInstance();
//
//        //create object and save into RDBMS
//        Staff staff = YschoolDataPoolFactory.getStaff();
//        staff.setStaffid("newtest");
//        staff.setFullName("asdfasdfadsf");
//        staff.setName("asdfasdf");
//        dataLayerYschool.save(staff);
//        dataLayerYschool.flushSession();
//    }
//
//}
