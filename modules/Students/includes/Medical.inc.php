<?php
#**************************************************************************
#  openSIS is a free student information system for public and non-public 
#  schools from Open Solutions for Education, Inc. web: www.os4ed.com
#
#  openSIS is  web-based, open source, and comes packed with features that 
#  include student demographic info, scheduling, grade book, attendance, 
#  report cards, eligibility, transcripts, parent portal, 
#  student portal and more.   
#
#  Visit the openSIS web site at http://www.opensis.com to learn more.
#  If you have question regarding this system or the license, please send 
#  an email to info@os4ed.com.
#
#  This program is released under the terms of the GNU General Public License as  
#  published by the Free Software Foundation, version 2 of the License. 
#  See license.txt.
#
#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  You should have received a copy of the GNU General Public License
#  along with this program.  If not, see <http://www.gnu.org/licenses/>.
#
#***************************************************************************************
include('../../../Redirect_includes.php');

include_once('modules/Students/includes/functions.php');

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='delete' && User('PROFILE')=='admin')
{
	if(!$_REQUEST['delete_ok'] && !$_REQUEST['delete_cancel'])
		echo '</FORM>';
	if(DeletePrompt($_REQUEST['title']))
	{
		DBQuery("DELETE FROM $_REQUEST[table] WHERE ID='$_REQUEST[id]'");
		unset($_REQUEST['modfunc']);
	}
}

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='update')
	unset($_REQUEST['modfunc']);

if(!$_REQUEST['modfunc'])
{
	echo '<div style="position: absolute; z-index:1000; width: 495px; height: 300px; visibility:hidden; background-image:url(\'assets/comment_background.gif\');" id="dc"></div>';
	
	echo '<TABLE width=100% border=0 cellpadding=0 cellspacing=0>';
	echo '<TR><TD valign=top>';
	$_REQUEST['category_id'] = 2;
	echo '<div class=hseparator><b>Medical Information</b></div><div class=clear></div>';
       
        echo '<TABLE cellpadding=5>';
                        echo '<TR>';
			echo '<td>Primary Care Physician</td><td>:</td><td>';
			echo TextInput($student['PHYSICIAN'],'students[PHYSICIAN]','','class=cell_medium');
			echo '</TD>';
			echo '</TR>';

                        echo '<TR>';
			echo "<td>Physician's Phone</td><td>:</td><td>";
			echo TextInput($student['PHYSICIAN_PHONE'],'students[PHYSICIAN_PHONE]','','class=cell_medium');
			echo '</TD>';
			echo '</TR>';

                        echo '<TR>';
			echo '<td>Preferred Medical Facility</td><td>:</td><td>';
			echo TextInput($student['PREFERRED_HOSPITAL'],'students[PREFERRED_HOSPITAL]','','class=cell_medium');
			echo '</TD>';
			echo '</TR>';

                      /* echo '<TR>';
		       echo '<td valign=top>Comments</td><td valign=top>:</td><td>';
		       echo TextAreaInput($student['COMMENTS'],'students[COMMENTS]','', '', 'true');
		       echo '</TD>';
		       echo '</TR>';

                       echo '<TR>';
		       echo "<td valign=top>Doctor's Note Comments</td><td valign=top>:</td><td>";
		       echo TextAreaInput($student['DOCTORS_NOTE_COMMENTS'],'students[DOCTORS_NOTE_COMMENTS]','', '', 'true');
		       echo '</TD>';
		       echo '</TR>';*/
         echo '</TABLE>';
         	
			echo '<div class=clear></div>';
			include('modules/Students/includes/Other_Info.inc.php');

        $table = 'STUDENT_MEDICAL_NOTES';
	$functions = array('DOCTORS_NOTE_DATE'=>'_makeDate','DOCTORS_NOTE_COMMENTS'=>'_makeAlertComments');
	$med_RET = DBGet(DBQuery("SELECT ID,STUDENT_ID,DOCTORS_NOTE_DATE,DOCTORS_NOTE_COMMENTS
                    FROM STUDENT_MEDICAL_NOTES
                    WHERE STUDENT_ID='".UserStudentID()."'"),$functions);
	$columns = array('DOCTORS_NOTE_DATE'=>'Date','DOCTORS_NOTE_COMMENTS'=>'Doctor\'s Note');
	$link['add']['html'] = array('DOCTORS_NOTE_DATE'=>_makeDate('','DOCTORS_NOTE_DATE'),'DOCTORS_NOTE_COMMENTS'=>_makeAlertComments('','DOCTORS_NOTE_COMMENTS'));
	$link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&include=$_REQUEST[include]&modfunc=delete&table=STUDENT_MEDICAL_NOTES&title=".urlencode('Medical Note');
	$link['remove']['variables'] = array('id'=>'ID');
        ListOutput($med_RET,$columns,'Medical Note','Medical Notes',$link,array(),array('search'=>false));
        
       echo '<div class=clear></div>';
        #############################################CUSTOM FIELDS###############################
	// include('modules/Students/includes/Other_Info.inc.php');
        #############################################CUSTOM FIELDS###############################
	echo '</TD></TR><TR><TD valign=top>';
	echo '<TABLE width=100%><TR><TD valign=top>';
	$table = 'STUDENT_MEDICAL';
	$functions = array('TYPE'=>'_makeType','MEDICAL_DATE'=>'_makeDate','COMMENTS'=>'_makeAlertComments');
	$med_RET = DBGet(DBQuery("SELECT ID,TYPE,MEDICAL_DATE,COMMENTS FROM STUDENT_MEDICAL WHERE STUDENT_ID='".UserStudentID()."' ORDER BY MEDICAL_DATE,TYPE"),$functions);
	$columns = array('TYPE'=>'Type','MEDICAL_DATE'=>'Date','COMMENTS'=>'Comments');
	$link['add']['html'] = array('TYPE'=>_makeType('',''),'MEDICAL_DATE'=>_makeDate('','MEDICAL_DATE'),'COMMENTS'=>_makeAlertComments('','COMMENTS'));
	$link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&include=$_REQUEST[include]&modfunc=delete&table=STUDENT_MEDICAL&title=".urlencode('immunization or physical');
	$link['remove']['variables'] = array('id'=>'ID');

	if(count($med_RET)==0)
		$plural = 'Immunizations or Physicals';
	else
		$plural = 'Immunizations and Physicals';

	echo '<div class=hseparator><b>Immunization/Physical Record</b></div>';
	ListOutput($med_RET,$columns,'Immunization or Physical',$plural,$link,array(),array('search'=>false));
	echo '</TD></tr><TD valign=top>';
	$table = 'STUDENT_MEDICAL_ALERTS';
	$functions = array('ALERT_DATE'=>'_makeDate','TITLE'=>'_makeAlertComments');
	$med_RET = DBGet(DBQuery("SELECT ID,TITLE,ALERT_DATE FROM STUDENT_MEDICAL_ALERTS WHERE STUDENT_ID='".UserStudentID()."' ORDER BY ID"),$functions);
	$columns = array('ALERT_DATE'=>'Alert Date','TITLE'=>'Medical Alert');
	$link['add']['html'] = array('ALERT_DATE'=>_makeDate('','ALERT_DATE'),'TITLE'=>_makeAlertComments('','TITLE'));
	$link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&include=$_REQUEST[include]&modfunc=delete&table=STUDENT_MEDICAL_ALERTS&title=".urlencode('medical alert');
	$link['remove']['variables'] = array('id'=>'ID');
	echo '<div class=clear></div><div class=hseparator><b>Medical Alert</b></div>';
	ListOutput($med_RET,$columns,'Medical Alert','Medical Alerts',$link,array(),array('search'=>false));
	
	echo '</TD></TR></TABLE>';
	echo '</TD>';
	// IMAGE
	echo '</TR>';

	echo '<TR><TD valign=top>';
	$table = 'STUDENT_MEDICAL_VISITS';
	$functions = array('SCHOOL_DATE'=>'_makeDate','TIME_IN'=>'_makeComments','TIME_OUT'=>'_makeComments','REASON'=>'_makeComments','RESULT'=>'_makeComments','COMMENTS'=>'_makeLongComments');
	$med_RET = DBGet(DBQuery("SELECT ID,SCHOOL_DATE,TIME_IN,TIME_OUT,REASON,RESULT,COMMENTS FROM STUDENT_MEDICAL_VISITS WHERE STUDENT_ID='".UserStudentID()."' ORDER BY SCHOOL_DATE"),$functions);
	$columns = array('SCHOOL_DATE'=>'Date','TIME_IN'=>'Time In','TIME_OUT'=>'Time Out','REASON'=>'Reason','RESULT'=>'Result','COMMENTS'=>'Comments');
	$link['add']['html'] = array('SCHOOL_DATE'=>_makeDate('','SCHOOL_DATE'),'TIME_IN'=>_makeComments('','TIME_IN'),'TIME_OUT'=>_makeComments('','TIME_OUT'),'REASON'=>_makeComments('','REASON'),'RESULT'=>_makeComments('','RESULT'),'COMMENTS'=>_makeLongComments('','COMMENTS'));
	$link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&include=$_REQUEST[include]&modfunc=delete&table=STUDENT_MEDICAL_VISITS&title=".urlencode('visit');
	$link['remove']['variables'] = array('id'=>'ID');
	echo '<div class=clear></div><div class=hseparator><b>Nurse Visit Record</b></div>';
	ListOutput($med_RET,$columns,'Nurse Visit','Nurse Visits',$link,array(),array('search'=>false));
	echo '</TD></TR></TABLE>';
}

?>