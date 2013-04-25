package org.yarlithub.yschool.repository;

import org.hibernate.Transaction;
import org.junit.Before;
import org.junit.Test;
import org.yarlithub.yschool.repository.util.HibernateUtil;

import java.util.List;

/**
 * Created with IntelliJ IDEA.
 * User: jaykrish
 * Date: 4/25/13
 * Time: 3:01 PM
 * To change this template use File | Settings | File Templates.
 */
public class ParentTest {

    @Before
    public void setUp() throws Exception {

        final Parent parent = new Parent();
  //      parent.setFullName("TestParent");


        final Transaction transaction = HibernateUtil.getCurrentSession().beginTransaction();
        parent.save();
        transaction.commit();
    }


    @Test
    public void testParentSearch() {
        final Transaction transaction = HibernateUtil.getCurrentSession().beginTransaction();
        final List<Parent> test = new Parent().searchParentByfullName("TestParent");
        for (Parent parent : test) {
            System.out.println(parent);
        }
        transaction.commit();
    }

}
