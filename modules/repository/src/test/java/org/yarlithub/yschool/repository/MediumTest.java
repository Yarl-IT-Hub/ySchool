package org.yarlithub.yschool.repository;


import org.hibernate.Transaction;
import org.junit.Before;
import org.junit.Test;
import org.yarlithub.yschool.repository.util.HibernateUtil;

import java.util.List;

import static org.junit.Assert.*;

public class MediumTest {

    @Before
    public void setUp() throws Exception {
        final Transaction transaction = HibernateUtil.getCurrentSession().beginTransaction();

        Medium medium = new Medium(Medium.Language.ENGLISH);
        medium.save();

        transaction.commit();
    }

    @Test
    public void testFindAll() throws Exception {
        final Transaction transaction = HibernateUtil.getCurrentSession().beginTransaction();

        List<Medium> mediums = Medium.findAll();
        assertEquals(1, mediums.size());
        assertEquals(Medium.Language.ENGLISH, mediums.get(0).getLanguage());

        transaction.commit();
    }

}
