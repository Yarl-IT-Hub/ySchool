package org.yarlithub.yschool.generals;

import org.apache.log4j.Logger;
import org.yarlithub.yschool.repository.model.obj.yschool.Staff;
import org.yarlithub.yschool.repository.services.data.DataLayerYschool;
import org.yarlithub.yschool.repository.services.data.DataLayerYschoolImpl;

/**
 * Created with IntelliJ IDEA.
 * User: admin
 * Date: 2014-04-05
 * Time: 11:38 AM
 * To change this template use File | Settings | File Templates.
 */
public class SchoolHandler {
    static Logger log = Logger.getLogger(
            SchoolHandler.class);
    DataLayerYschool dataLayerYschool = DataLayerYschoolImpl.getInstance();

    public Staff getStaff(Class cls){
               return null;
    }
}
