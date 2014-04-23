package org.yarlithub.yschool.generals;

import org.hibernate.Criteria;
import org.hibernate.criterion.Restrictions;
import org.yarlithub.yschool.repository.DBConstants;
import org.yarlithub.yschool.repository.model.obj.yschool.*;
import org.yarlithub.yschool.repository.model.obj.yschool.Module;
import org.yarlithub.yschool.repository.services.data.DataLayerYschool;
import org.yarlithub.yschool.repository.services.data.DataLayerYschoolImpl;

import java.util.ArrayList;
import java.util.List;

/**
 * Created with IntelliJ IDEA.
 * User: admin
 * Date: 2014-04-05
 * Time: 12:03 PM
 * To change this template use File | Settings | File Templates.
 */
public class ModuleHandler {
    DataLayerYschool dataLayerYschool = DataLayerYschoolImpl.getInstance();

    /**
     * Add new module
     * @param subject
     * @param grade
     * @param isOptional
     * @return
     */
    public Module addModule(Subject subject, Grade grade, boolean isOptional){
         Module module=new Module();
        module.setGradeIdgrade(grade);
        module.setSubjectIdsubject(subject);
        module.setIsOptional(isOptional);
        dataLayerYschool.save(module) ;
        return module;

    }

    /**
     * Get list of modules connected with given clssroom
     * @param classRoomId
     * @return
     */
    public List<Module> getModules(int classRoomId){

        List<Module> modules=new ArrayList<>();
        Criteria classRoomModuleCriteria =dataLayerYschool.createCriteria(ClassroomModule.class);
        classRoomModuleCriteria.add(Restrictions.eq(DBConstants.classroom_module.classroom_idclassroom,classRoomId));
        List<ClassroomModule> classroomModules=classRoomModuleCriteria.list();
        for(ClassroomModule classroomModule:classroomModules){
            Criteria moduleCriteria=dataLayerYschool.createCriteria(Module.class);
            moduleCriteria.add(Restrictions.eq(DBConstants.module.idmodule,classroomModule.getId()));
            if(!moduleCriteria.list().isEmpty()){
                modules.add((Module) moduleCriteria.list().get(0));
            }
        }
        return modules;
    }

    /**
     * Update module
     * @param m
     * @return
     */
    public Module updateModule(Module m){
        dataLayerYschool.saveOrUpdate(m);
        return m;
    }
}
