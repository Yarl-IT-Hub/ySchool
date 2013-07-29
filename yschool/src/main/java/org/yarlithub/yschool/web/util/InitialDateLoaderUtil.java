/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.yarlithub.yschool.web.util;

import org.apache.log4j.Logger;

import javax.faces.bean.ApplicationScoped;
import javax.faces.bean.ManagedBean;
import java.util.Arrays;
import java.util.List;

/**
 * @author dell
 */

@ManagedBean(name = "initialDateLoaderUtil", eager = true)
@ApplicationScoped
public class InitialDateLoaderUtil {

    private static final Logger logger = Logger.getLogger(InitialDateLoaderUtil.class);

    public List<String> loadData(String dataType) {
        logger.info("Loading dataList for type [" + dataType + "]");
//        return HibernateUtil.getCurrentSession().createSQLQuery("select * from PreLoadData where dataType = ?").setString(0, dataType).list();
        return Arrays.asList("1", "2", "3", "4", "5");
    }
}
