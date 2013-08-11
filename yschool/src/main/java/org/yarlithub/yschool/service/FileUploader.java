package org.yarlithub.yschool.service;

import org.apache.commons.io.FilenameUtils;
import org.apache.myfaces.custom.fileupload.UploadedFile;

import javax.faces.application.FacesMessage;
import javax.faces.context.FacesContext;
import java.io.IOException;

/**
 * Created with IntelliJ IDEA.
 * User: jayrksih
 * Date: 8/11/13
 * Time: 7:51 PM
 * To change this template use File | Settings | File Templates.
 */
public class FileUploader {
    public byte[] upload(UploadedFile uploadedFile) throws IOException {
        String fileName = FilenameUtils.getName(uploadedFile.getName());
        String contentType = uploadedFile.getContentType();
        byte[] bytes = uploadedFile.getBytes();

        // Now you can save bytes in DB (and also content type?)

        FacesContext.getCurrentInstance().addMessage(null,
                new FacesMessage(String.format("File '%s' of type '%s' successfully uploaded!", fileName, contentType)));
        return bytes;
    }
}
