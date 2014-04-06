package org.yarlithub.yschool.web.converter;

import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.web.commons.SearchResult;

import javax.faces.application.FacesMessage;
import javax.faces.bean.ManagedBean;
import javax.faces.component.UIComponent;
import javax.faces.context.FacesContext;
import javax.faces.convert.Converter;
import javax.faces.convert.ConverterException;
import java.util.List;

/**
 * Created with IntelliJ IDEA.
 * User: Pirinthapan
 * Date: 4/5/14
 * Time: 3:45 PM
 * To change this template use File | Settings | File Templates.
 */

@ManagedBean
@Scope(value = "session")
@Controller
public class SearchConverter implements Converter {
    public static List<SearchResult> resultList;

    public Object getAsObject(FacesContext facesContext, UIComponent component, String submittedValue) {
        if (submittedValue.trim().equals("")) {
            return null;
        } else {
            try {
                int number = Integer.parseInt(submittedValue);

                for (SearchResult sr : resultList) {
                    if (sr.getId() == number) {
                        return sr;
                    }
                }

            } catch(NumberFormatException exception) {
                throw new ConverterException(new FacesMessage(FacesMessage.SEVERITY_ERROR, "Conversion Error", "Not a valid player"));
            }
        }

        return null;
    }

    public String getAsString(FacesContext facesContext, UIComponent component, Object value) {
        if (value == null || value.equals("")) {
            return "";
        } else {
            return String.valueOf(((SearchResult) value).getId());
        }
    }
}
