/*
 *   (C) Copyright 2012-2013 hSenid Software International (Pvt) Limited.
 *   All Rights Reserved.
 *
 *   These materials are unpublished, proprietary, confidential source code of
 *   hSenid Software International (Pvt) Limited and constitute a TRADE SECRET
 *   of hSenid Software International (Pvt) Limited.
 *
 *   hSenid Software International (Pvt) Limited retains all title to and intellectual
 *   property rights in these materials.
 *
 */
package org.yarlithub.yschool.repository;

import org.hibernate.Session;
import org.hibernate.Transaction;
import org.junit.Before;
import org.junit.Test;
import org.yarlithub.yschool.repository.util.HibernateUtil;

import java.util.List;

/**
 * $LastChangedDate$
 * $LastChangedBy$
 * $LastChangedRevision$
 */
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
