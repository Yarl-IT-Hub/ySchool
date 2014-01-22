//package org.yarlithub.yschool.integration.olds;
//
//import org.junit.After;
//import org.junit.Before;
//import org.junit.Test;
//import org.junit.runner.RunWith;
//import org.junit.runners.Parameterized;
//import org.springframework.test.context.ContextConfiguration;
//import org.springframework.test.context.TestContextManager;
//import org.springframework.test.context.junit4.SpringJUnit4ClassRunner;
//import org.springframework.test.context.transaction.TransactionConfiguration;
//import org.springframework.transaction.annotation.Transactional;
////import org.yarlithub.yschool.integration.utils.SpringJUnit4ParameterizedClassRunner;
//import org.yarlithub.yschool.repository.model.obj.yschool.Student;
//import org.yarlithub.yschool.service.StudentService;
//
//import java.text.DateFormat;
//import java.text.ParseException;
//import java.text.SimpleDateFormat;
//import java.util.Arrays;
//import java.util.Collection;
//import java.util.Date;
//
//import static org.junit.Assert.assertEquals;
//
///**
//* Created with IntelliJ IDEA.
//* User: JayKrish
//* Date: 1/20/14
//* Time: 11:50 AM
//* To change this template use File | Settings | File Templates.
//*/
//
///**
//* These tests connects with ySchool database  and transfer data.
//* Before Running tests make sure to import schema and initial data into the database.
//*/
//
//@ContextConfiguration(locations = {"/applicationContext.xml"})
//@TransactionConfiguration(transactionManager = "transactionManager", defaultRollback = true)
//@RunWith(SpringJUnit4ClassRunner.class)
//public class StudentNew01Test {
//
//    private StudentService studentService;
//    private String addmision_No;
//    private String name;
//    private String fullname;
//    private String name_wt_initial;
//    private Date dob;
//    private String gender;
//    private String address;
//    private String expected;
//    private TestContextManager testContextManager;
//
//    public StudentNew01Test(String addmision_No, String name, String fullname, String name_wt_initial, Date dob, String gender, String address, String expected) {
//        this.addmision_No = addmision_No;
//        this.name = name;
//        this.fullname = fullname;
//        this.name_wt_initial = name_wt_initial;
//        this.dob = dob;
//        this.gender = gender;
//        this.address = address;
//        this.expected=expected;
//    }
//
//    @Parameterized.Parameters
//    public static Collection<Object[]> parameters() throws ParseException {
//
//        String testDate = "29-Apr-2010,13:00:14 PM";
//        DateFormat formatter = new SimpleDateFormat("d-MMM-yyyy,HH:mm:ss aaa");
//        Date date = formatter.parse(testDate);
//
//        return Arrays.asList(new Object[][]{{"090200u", "alkdlaksjf","dlsakjfdlkaj alkdjfa", "alkfdalkdsjf",date,"male","Jaffna","AddStudentSuccess"}});
//    }
//
//    @Before
//    public void setUp() throws Exception {
//        studentService = new StudentService();
//
//    }
//
//    @After
//    public void tearDown() throws Exception {
//        studentService = null;
//    }
//
//    @Test
//    @Transactional
//    public void addNewStudentTest() throws ParseException {
//        Student student= studentService.addNewStudent(addmision_No, name, fullname, name_wt_initial, dob, gender, address);
//        assertEquals("error", "Jafna", student.getAddress());
//    }
//}
