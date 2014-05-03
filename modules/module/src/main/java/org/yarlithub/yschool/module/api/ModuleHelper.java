package org.yarlithub.yschool.module.api;

import org.hibernate.Criteria;
import org.hibernate.criterion.Restrictions;
import org.yarlithub.yschool.repository.model.obj.yschool.Grade;
import org.yarlithub.yschool.repository.model.obj.yschool.Module;
import org.yarlithub.yschool.repository.model.obj.yschool.Subject;
import org.yarlithub.yschool.repository.services.data.DataLayerYschool;
import org.yarlithub.yschool.repository.services.data.DataLayerYschoolImpl;

import java.util.List;

/**
 * Created with IntelliJ IDEA.
 * User: Jay Krish
 * Date: 10/25/13
 * Time: 12:20 AM
 * To change this template use File | Settings | File Templates.
 */


public class ModuleHelper {

    static DataLayerYschool dataLayerYschool = DataLayerYschoolImpl.getInstance();

    /**
     * Returns all Modules entries.
     *
     * @return List of org.yarlithub.yschool.repository.model.obj.yschool.Module;
     */
    public static List<Module> getAllModules() {
        Criteria moduleCriteria = dataLayerYschool.createCriteria(Module.class);
        return moduleCriteria.list();
    }

    /**
     * Returns all Modules entries for the specific grade.
     *
     * @return List of org.yarlithub.yschool.repository.model.obj.yschool.Module;
     */
    public static List<Module> getModules(int gradeId) {
        Grade grade = dataLayerYschool.getGrade(gradeId);
        Criteria moduleCriteria = dataLayerYschool.createCriteria(Module.class);
        moduleCriteria.add(Restrictions.eq("gradeIdgrade", grade));
        return moduleCriteria.list();
    }

    /**
     * Add new module
     *
     * @param subject
     * @param grade
     * @param isOptional
     * @return
     */
    public static Module addModule(Subject subject, Grade grade, boolean isOptional) {
        Module module = new Module();
        module.setGradeIdgrade(grade);
        module.setSubjectIdsubject(subject);
        module.setIsOptional(isOptional);
        dataLayerYschool.save(module);
        return module;

    }


}
