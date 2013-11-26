//package org.yarlithub.yschool;
//
//import org.junit.Assert;
//import org.junit.Test;
//import org.junit.runner.RunWith;
//import org.springframework.test.context.ContextConfiguration;
//import org.springframework.test.context.junit4.SpringJUnit4ClassRunner;
//import org.springframework.test.context.transaction.TransactionConfiguration;
//import org.springframework.transaction.annotation.Transactional;
//import org.yarlithub.yschool.repository.factories.yschool.YschoolDataPoolFactory;
//import org.yarlithub.yschool.repository.model.obj.yschool.ExamSync;
//import org.yarlithub.yschool.repository.services.data.DataLayerYschool;
//import org.yarlithub.yschool.repository.services.data.DataLayerYschoolImpl;
//
///**
// * Created with IntelliJ IDEA.
// * User: Jay Krish
// * Date: 11/25/13
// * Time: 3:51 PM
// * To change this template use File | Settings | File Templates.
// */
//
//@ContextConfiguration(locations = {"/applicationContext.xml"})
//@RunWith(SpringJUnit4ClassRunner.class)
//@TransactionConfiguration(transactionManager = "transactionManager", defaultRollback = false)
//public class ExamSyncTest {
//
//    @Transactional
//    @Test
//    public void testExamSync() throws CloneNotSupportedException {
//
//        DataLayerYschool dataLayerYschool = DataLayerYschoolImpl.getInstance();
//        ExamSync examSync = YschoolDataPoolFactory.getExamSync();
//        dataLayerYschool.saveOrUpdate(examSync);
//
//        Integer examSyncId = examSync.getId();
//        ExamSync copy = examSync.clone();
//        dataLayerYschool.flushSession(); // flush+evict from cache to make sure we hit the DB next
//        dataLayerYschool.evict(examSync);
//
//        examSync = dataLayerYschool.getExamSync(examSyncId); 	// load it again
//
//
//        // Validity checks
//        Assert.assertNotNull(copy);
//        // null equals check
//        Assert.assertFalse(copy.equals(null));
//
//        Assert.assertEquals(copy.getClassIdexam(), examSync.getClassIdexam());
//        Assert.assertEquals(copy.getExamIdexam().getId(), examSync.getExamIdexam().getId());
//        Assert.assertEquals(copy.getModifiedTime().getTime() / 1000, examSync.getModifiedTime().getTime() / 1000);
//        Assert.assertEquals(copy.getSyncStatus(), examSync.getSyncStatus());
//        // tests for coverage completeness
//        Assert.assertFalse(examSync.toString().equals(""));
//        Assert.assertEquals(copy, copy.clone());
//        // symmetric equality check
//        Assert.assertEquals(copy.clone(), copy);
//        // reflexive equality check
//        Assert.assertEquals(copy, copy);
//        // hashcode on identical object should return same number
//        Assert.assertEquals(examSync.hashCode(), copy.hashCode());
//        Assert.assertNotSame(copy, 0L);
//        // End of table
//    }
//}
