//package org.yarlithub.yschool.integration.olds;
//
//import org.junit.After;
//import org.junit.Before;
//import org.junit.Test;
//import org.junit.runner.RunWith;
//import org.springframework.test.context.ContextConfiguration;
//import org.springframework.test.context.TestContextManager;
//import org.springframework.test.context.junit4.SpringJUnit4ClassRunner;
//import org.springframework.test.context.transaction.TransactionConfiguration;
//import org.springframework.transaction.annotation.Transactional;
//import org.yarlithub.yschool.web.student.StudentNewBean;
//
//import java.text.DateFormat;
//import java.text.ParseException;
//import java.text.SimpleDateFormat;
//import java.util.Date;
//
//import static org.junit.Assert.assertEquals;
//
////import org.yarlithub.yschool.integration.utils.SpringJUnit4ParameterizedClassRunner;
////import org.yarlithub.yschool.integration.utils.SpringParameterizedRunner;
//
///**
// * Created with IntelliJ IDEA.
// * User: JayKrish
// * Date: 1/20/14
// * Time: 11:50 AM
// * To change this template use File | Settings | File Templates.
// */
//
///**
// * These tests connects with ySchool database  and transfer data.
// * Before Running tests make sure to import schema and initial data into the database.
// */
//
//@ContextConfiguration(locations = {"/applicationContext.xml"})
//@RunWith(SpringJUnit4ClassRunner.class)
//@TransactionConfiguration(transactionManager = "transactionManager", defaultRollback = true)
//@Transactional
//public class StudentNewTestParams {
//
//   // private StudentService studentService;
//  //@Autowired( required = true )
//    //private StudentNewBean studentNewBean;
//
//   private StudentNewBean studentNewBean;
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
////    public  StudentServiceTest(String addmision_No, String name, String fullname, String name_wt_initial, Date dob, String gender, String address, String expected) {
////        this.addmision_No = addmision_No;
////        this.name = name;
////        this.fullname = fullname;
////        this.name_wt_initial = name_wt_initial;
////        this.dob = dob;
////        this.gender = gender;
////        this.address = address;
////        this.expected=expected;
////    }
////
////    @Parameterized.Parameters
////    public static Collection<Object[]> parameters() throws ParseException {
////        String testDate = "29-Apr-2010,13:00:14 PM";
////        DateFormat formatter = new SimpleDateFormat("d-MMM-yyyy,HH:mm:ss aaa");
////        Date date = formatter.parse(testDate);
////        return Arrays.asList(new Object[][]{{"090200u","alkdlaksjf","dlsakjfdlkaj alkdjfa", "alkfdalkdsjf",date,"male","Jaffna","AddStudentSuccess"}});
////    }
//
//    @Before
//    public void setUp() throws Exception {
//        this.testContextManager = new TestContextManager(getClass());
//        this.testContextManager.prepareTestInstance(this.getClass());
//        String testDate = "29-Apr-2010,13:00:14 PM";
//        DateFormat formatter = new SimpleDateFormat("d-MMM-yyyy,HH:mm:ss aaa");
//        Date date = formatter.parse(testDate);
//        studentNewBean = new StudentNewBean();
//    //       studentService =new StudentService();
//        addmision_No = "okjsdfa";
//        this.name = "aksjdf";
//        this.fullname = "lkjasfda";
//        this.name_wt_initial = "alksjdf";
//        this.dob = date;
//        this.gender = "female";
//        this.address = "Jaffna";
//        this.expected="AddStudentSuccess";
//    }
//
//    @After
//    public void tearDown() throws Exception {
//       // studentNewBean = null;
//    }
//
//    @Test
//    @Transactional
//    public void addNewStudentTest() throws ParseException {
//
////        String testDate = "29-Apr-2010,13:00:14 PM";
////        DateFormat formatter = new SimpleDateFormat("d-MMM-yyyy,HH:mm:ss aaa");
////        Date date = formatter.parse(testDate);
//        studentNewBean.setAdmission_No(addmision_No);
//        studentNewBean.setAdmission_No(name);
//        studentNewBean.setFullname(fullname);
//        studentNewBean.setName_wt_initial(name_wt_initial);
//        studentNewBean.setDob(dob);
//        studentNewBean.setGender(gender);
//        studentNewBean.setAddress(address);
// //       Student actual = studentService.addNewStudent("090200u","alkdlaksjf","dlsakjfdlkaj alkdjfa", "alkfdalkdsjf",dob,"male","Jaffna");
//        String actual = studentNewBean.addStudent();
//        System.out.println(actual);
//       assertEquals("error!", "Jaffna", actual);
//       // assertEquals("error",studentNewBean.getAddress(),"Jaffna");
//      // Student student= studentService.addNewStudent("090200u","alkdlaksjf","dlsakjfdlkaj alkdjfa", "alkfdalkdsjf",date,"male","Jaffna");
//
//      //  assertEquals("error",student.getAddress(),"Jaffna");
//      //  System.out.println(student.getName());
//    }
//}
