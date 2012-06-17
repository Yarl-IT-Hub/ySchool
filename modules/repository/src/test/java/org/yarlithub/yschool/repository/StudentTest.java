package org.yarlithub.yschool.repository;

import org.hibernate.Transaction;
import org.junit.Before;
import org.junit.Test;
import org.yarlithub.yschool.repository.util.HibernateUtil;

import java.util.List;

public class StudentTest {

    @Before
    public void setUp() throws Exception {

        final Student student = new Student();
        student.setFname("Fname");
        student.setLname("Test");

        final Transaction transaction = HibernateUtil.getCurrentSession().beginTransaction();
        student.save();
        transaction.commit();
    }


    @Test
    public void testStudentSearch() {
        final Transaction transaction = HibernateUtil.getCurrentSession().beginTransaction();
        final List<Student> test = new Student().searchStudentByLastName("Test");
        for (Student student : test) {
            System.out.println(student);
        }
        transaction.commit();
    }
}
