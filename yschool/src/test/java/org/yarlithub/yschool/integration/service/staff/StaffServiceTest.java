package org.yarlithub.yschool.integration.service.staff;

import org.junit.*;
import org.junit.runner.RunWith;
import org.springframework.test.context.ContextConfiguration;
import org.springframework.test.context.junit4.SpringJUnit4ClassRunner;
import org.springframework.test.context.transaction.TransactionConfiguration;
import org.springframework.transaction.annotation.Transactional;
import org.yarlithub.yschool.integration.service.testdata.StaffIntegrationData;
import org.yarlithub.yschool.repository.model.obj.yschool.Staff;
import org.yarlithub.yschool.service.StaffService;

import java.util.Iterator;
import java.util.List;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;

/**
 * Created with IntelliJ IDEA.
 * User: JayKrish
 * Date: 1/20/14
 * Time: 11:00 AM
 * To change this template use File | Settings | File Templates.
 */

/**
 * These tests connects with ySchool database and transfer data.
 * Before Running tests make sure to import schema and initial data into the database.
 */

@ContextConfiguration(locations = {"/applicationContext.xml"})
@RunWith(SpringJUnit4ClassRunner.class)
@TransactionConfiguration(transactionManager = "transactionManager", defaultRollback = true)
@Transactional
public class StaffServiceTest {

    private static StaffService staffService;

    @Before

    public  void setup(){
        staffService=new StaffService();
        Iterator iterator= StaffIntegrationData.staffData1.iterator();
        while (iterator.hasNext()){
            Object[] studentData = (Object[]) iterator.next();
            Staff result=staffService.addStaff((String)studentData[0],(String)studentData[1],(String)studentData[2]);
            assertTrue("Error !", result.getId()>0);
        }
    }

    @After
    public  void tearDown(){
        staffService=null;
    }


    @Test
    @Transactional
    public void addStaffTest(){
        Iterator iterator= StaffIntegrationData.staffData2.iterator();
        while (iterator.hasNext()){
            Object[] studentData = (Object[]) iterator.next();
            Staff result=staffService.addStaff((String)studentData[0],(String)studentData[1],(String)studentData[2]);
            assertTrue("Error !", result.getId()>0);
        }
    }



    @Test
    @Transactional
    public void getStaffTest(){
        Iterator iterator= StaffIntegrationData.staffData1.iterator();
        List<Staff> staffList=staffService.getStaff();
        for(int i=0;i<staffList.size();i++){
            Staff tempStaff=staffList.get(i);
           // assertEquals(iterator.next(),tempStaff);
        }
    }

    @Test
    @Transactional
    public void saveOrUpdateTest(){

        Iterator iterator= StaffIntegrationData.staffDataUpdate.iterator();
        Object[] staffdata=(Object[])iterator.next();
        Staff result=staffService.addStaff((String)staffdata[0],(String)staffdata[1],(String)staffdata[2]);
        while (iterator.hasNext()){
            staffdata = (Object[]) iterator.next();
            result.setFullName((String)staffdata[2]);
            result.setFullName((String)staffdata[1]);
            result.setStaffid((String)staffdata[0]);
            result=staffService.saveOrUpdate(result);
            assertTrue("Error !", result.getStaffid()==staffdata[0]);
        }
    }

    @Test
    @Transactional
    public void getStaffNameLikeTest(){
        List<Staff> staffs=staffService.getStaffsNameLike("alk",100);
        assertEquals(StaffIntegrationData.staffData1.size(),staffs.size());
    }



}
