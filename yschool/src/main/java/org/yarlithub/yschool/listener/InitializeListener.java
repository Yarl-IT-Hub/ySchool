package org.yarlithub.yschool.listener; /**
 * $LastChangedDate$
 * $LastChangedBy$
 * $LastChangedRevision$
 */

import org.hibernate.Session;
import org.yarlithub.yschool.repository.util.HibernateUtil;

import javax.servlet.ServletContextEvent;
import javax.servlet.ServletContextListener;
import javax.servlet.http.HttpSessionAttributeListener;
import javax.servlet.http.HttpSessionEvent;
import javax.servlet.http.HttpSessionListener;
import javax.servlet.http.HttpSessionBindingEvent;

public class InitializeListener implements HttpSessionListener {


    public static final String CURRENT_SESSION = "current-hibernate-session";

    @Override
    public void sessionCreated(HttpSessionEvent httpSessionEvent) {
        Session currentSession = HibernateUtil.getSessionFactory().openSession();
        httpSessionEvent.getSession().setAttribute(CURRENT_SESSION, currentSession);
    }

    @Override
    public void sessionDestroyed(HttpSessionEvent httpSessionEvent) {
        ((Session) httpSessionEvent.getSession().getAttribute(CURRENT_SESSION)).close();
    }
}
