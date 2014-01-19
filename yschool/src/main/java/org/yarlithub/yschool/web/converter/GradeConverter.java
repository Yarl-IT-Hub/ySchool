package org.yarlithub.yschool.web.converter;

import org.yarlithub.yschool.repository.model.obj.yschool.Grade;
import org.yarlithub.yschool.repository.services.data.DataLayerYschool;
import org.yarlithub.yschool.repository.services.data.DataLayerYschoolImpl;

import javax.faces.component.UIComponent;
import javax.faces.context.FacesContext;
import javax.faces.convert.Converter;
import javax.faces.convert.FacesConverter;

/**
 * Created with IntelliJ IDEA.
 * User: JayKrish
 * Date: 1/18/14
 * Time: 9:10 PM
 * To change this template use File | Settings | File Templates.
 */
@FacesConverter("org.yarlithub.yschool.web.converter.GradeConverter")
public class GradeConverter implements Converter {
    DataLayerYschool dataLayerYschool = DataLayerYschoolImpl.getInstance();
    @Override
    public Object getAsObject(FacesContext facesContext, UIComponent uiComponent, String gradeid) {
        Grade grade=dataLayerYschool.getGrade(Integer.valueOf(gradeid));
        return grade;
    }

    @Override
    public String getAsString(FacesContext facesContext, UIComponent uiComponent, Object o) {
        return o.toString();  //To change body of implemented methods use File | Settings | File Templates.
    }
}
