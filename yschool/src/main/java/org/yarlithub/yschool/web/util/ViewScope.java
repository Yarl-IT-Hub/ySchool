package org.yarlithub.yschool.web.util;

import java.util.Map;

import javax.faces.context.FacesContext;

import org.springframework.beans.factory.ObjectFactory;
import org.springframework.beans.factory.config.Scope;
/**
 * Created with IntelliJ IDEA.
 * User: jayrksih
 * Date: 12/6/13
 * Time: 10:23 PM
 * To change this template use File | Settings | File Templates.
 */
public class ViewScope implements Scope {
    public Object get(String name, ObjectFactory objectFactory) {
        Map<String,Object> viewMap = FacesContext.getCurrentInstance().getViewRoot().getViewMap();

        if(viewMap.containsKey(name)) {
            return viewMap.get(name);
        } else {
            Object object = objectFactory.getObject();
            viewMap.put(name, object);

            return object;
        }
    }

    public Object remove(String name) {
        return FacesContext.getCurrentInstance().getViewRoot().getViewMap().remove(name);
    }

    public String getConversationId() {
        return null;
    }

    public void registerDestructionCallback(String name, Runnable callback) {
        //Not supported
    }

    public Object resolveContextualObject(String key) {
        return null;
    }
}
