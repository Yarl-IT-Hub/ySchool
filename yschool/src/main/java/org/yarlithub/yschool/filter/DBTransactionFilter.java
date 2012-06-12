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
package org.yarlithub.yschool.filter;

import org.apache.log4j.Logger;
import org.hibernate.Session;
import org.hibernate.Transaction;
import org.yarlithub.yschool.repository.util.HibernateUtil;

import javax.servlet.*;
import java.io.IOException;

/**
 * $LastChangedDate$
 * $LastChangedBy$
 * $LastChangedRevision$
 */
public class DBTransactionFilter implements Filter {

    private static final Logger logger = Logger.getLogger(DBTransactionFilter.class);
    public static final String CURRENT_SESSION = "current-hibernate-session";

    private Transaction transaction;

    @Override
    public void init(FilterConfig filterConfig) throws ServletException {
        //do nothing
    }

    @Override
    public void doFilter(ServletRequest servletRequest, ServletResponse servletResponse, FilterChain filterChain) throws IOException, ServletException {
        Session session = HibernateUtil.getSessionFactory().openSession();
        logger.info("Creating a new session and saving to session [" + session + "]");
        servletRequest.setAttribute(CURRENT_SESSION, session);
        transaction = session.getTransaction();
        transaction.begin();
        filterChain.doFilter(servletRequest, servletResponse);
    }

    @Override
    public void destroy() {
        if (!transaction.wasRolledBack()) {
            transaction.commit();
        } else {
            transaction.rollback();
        }
    }
}
