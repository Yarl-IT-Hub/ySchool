package org.yarlithub.yschool.service;

import org.apache.myfaces.custom.fileupload.UploadedFile;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import org.yarlithub.yschool.examination.core.NewCAExamination;


import java.io.IOException;
import java.util.Date;

/**
 * TODO description
 */
@Service(value = "examinationService")
public class ExaminationService {
    private static final Logger logger = LoggerFactory.getLogger(ExaminationService.class);

    @Transactional
    public boolean addCAExam(Date date, int term, int examType, int grade, String division, int subjectid) {
              NewCAExamination newCAExamination= new NewCAExamination();
        boolean success = newCAExamination.addCA(date,term,examType,grade,division,subjectid);
        return success;
    }



}
