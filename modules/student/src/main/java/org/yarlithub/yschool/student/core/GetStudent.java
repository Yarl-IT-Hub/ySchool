package org.yarlithub.yschool.student.core;

import org.yarlithub.yschool.repository.factories.yschool.YschoolDataPoolFactory;
import org.yarlithub.yschool.repository.model.obj.yschool.Student;
import org.yarlithub.yschool.repository.services.data.DataLayerYschool;
import org.yarlithub.yschool.repository.services.data.DataLayerYschoolImpl;

import java.util.Date;


public class GetStudent {


    public Student getStudentByID(int id){
        DataLayerYschool dataLayerYschool = DataLayerYschoolImpl.getInstance();





               Student student= dataLayerYschool.getStudent(id);
        return   student;
    }


}

