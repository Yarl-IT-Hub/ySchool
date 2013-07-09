package org.yarlithub.yschool.parent;

import org.apache.log4j.Logger;
import org.yarlithub.yschool.util.InitialDateLoaderUtil;

import javax.faces.bean.ManagedBean;
import javax.faces.bean.ManagedProperty;
import javax.faces.bean.SessionScoped;
import java.io.Serializable;

//import org.yarlithub.yschool.repository_lite.Parent;

/**
 * Created with IntelliJ IDEA.
 * User: jaykrish
 * Date: 4/25/13
 * Time: 2:55 PM
 * To change this template use File | Settings | File Templates.
 */

@ManagedBean
@SessionScoped
public class UserBean implements Serializable {

    private static final Logger logger = Logger.getLogger(ParentBean.class);

    @ManagedProperty(value = "#{initialDateLoaderUtil}")
    private InitialDateLoaderUtil initialDateLoaderUtil;

    // public User user;


    public UserBean() {
        logger.info("initiating a new user bean");
        //  user = DataServices.getSomeTable(100);
        //  someTable.setFoo(..);
        //  DataServices.save(someTable);
    }

    public void submit() {

        //user.save();

    }

    public void setInitialDateLoaderUtil(InitialDateLoaderUtil initialDateLoaderUtil) {
        this.initialDateLoaderUtil = initialDateLoaderUtil;
    }
}
