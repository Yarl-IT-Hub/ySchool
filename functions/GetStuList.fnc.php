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
function GetStuList(& $extra)
{	global $contacts_RET,$view_other_RET,$_openSIS;
	$offset='GRADE_ID';
	
	if((!$extra['SELECT_ONLY'] || strpos($extra['SELECT_ONLY'],$offset)!==false) && !$extra['functions']['GRADE_ID'])
		$functions = array('GRADE_ID'=>'GetGrade');
	else
		$functions = array();

	if($extra['functions'])
		$functions +=$extra['functions'];

	if(!$extra['DATE'])
	{
		$queryMP = UserMP();
		$extra['DATE'] = DBDate();
	}
	else
		$queryMP = GetCurrentMP('QTR',$extra['DATE'],false);

	if($_REQUEST['expanded_view']=='true')
	{
		if(!$extra['columns_after'])
			$extra['columns_after'] = array();
#############################################################################################
//Commented as it crashing for Linux due to  Blank Database tables

		$view_fields_RET = DBGet(DBQuery("SELECT cf.ID,cf.TYPE,cf.TITLE FROM PROGRAM_USER_CONFIG puc,CUSTOM_FIELDS cf WHERE puc.TITLE=cf.ID AND puc.PROGRAM='StudentFieldsView' AND puc.USER_ID='".User('STAFF_ID')."' AND puc.VALUE='Y'"));
#############################################################################################
		$view_address_RET = DBGet(DBQuery("SELECT VALUE FROM PROGRAM_USER_CONFIG WHERE PROGRAM='StudentFieldsView' AND TITLE='ADDRESS' AND USER_ID='".User('STAFF_ID')."'"));
		$view_address_RET = $view_address_RET[1]['VALUE'];
		$view_other_RET = DBGet(DBQuery("SELECT TITLE,VALUE FROM PROGRAM_USER_CONFIG WHERE PROGRAM='StudentFieldsView' AND TITLE IN ('PHONE','HOME_PHONE','GUARDIANS','ALL_CONTACTS') AND USER_ID='".User('STAFF_ID')."'"),array(),array('TITLE'));

		if(!count($view_fields_RET) && !isset($view_address_RET) && !isset($view_other_RET['CONTACT_INFO']))
		{
			$extra['columns_after'] = array('PHONE'=>'Phone','GENDER'=>'Gender','ETHNICITY'=>'Ethnicity','ADDRESS'=>'Mailing Address','CITY'=>'City','STATE'=>'State','ZIPCODE'=>'Zipcode') + $extra['columns_after'];
			$select = ',s.PHONE,s.GENDER,s.ETHNICITY,COALESCE(a.MAIL_ADDRESS,a.ADDRESS) AS ADDRESS,COALESCE(a.MAIL_CITY,a.CITY) AS CITY,COALESCE(a.MAIL_STATE,a.STATE) AS STATE,COALESCE(a.MAIL_ZIPCODE,a.ZIPCODE) AS ZIPCODE ';
			#$extra['FROM'] = " LEFT OUTER JOIN STUDENTS_JOIN_ADDRESS sam ON (ssm.STUDENT_ID=sam.STUDENT_ID AND sam.MAILING='Y') LEFT OUTER JOIN ADDRESS a ON (sam.ADDRESS_ID=a.ADDRESS_ID) ".$extra['FROM'];
			$extra['FROM'] = " LEFT OUTER JOIN STUDENTS_JOIN_ADDRESS sam ON (ssm.STUDENT_ID=sam.STUDENT_ID ) LEFT OUTER JOIN ADDRESS a ON (sam.ADDRESS_ID=a.ADDRESS_ID) ".$extra['FROM'];
                        $functions['CONTACT_INFO'] = 'makeContactInfo';
			// if gender is converted to codeds type
			//$functions['CUSTOM_200000000'] = 'DeCodeds';
			$extra['singular'] = 'Student Address';
			$extra['plural'] = 'Student Addresses';

			$extra2['NoSearchTerms'] = true;
			$extra2['SELECT_ONLY'] = 'ssm.STUDENT_ID,p.PERSON_ID,p.FIRST_NAME,p.LAST_NAME,sjp.STUDENT_RELATION,pjc.TITLE,pjc.VALUE,a.PHONE,sjp.ADDRESS_ID ';
			$extra2['FROM'] .= ',ADDRESS a,STUDENTS_JOIN_ADDRESS sja LEFT OUTER JOIN STUDENTS_JOIN_PEOPLE sjp ON (sja.STUDENT_ID=sjp.STUDENT_ID AND sja.ADDRESS_ID=sjp.ADDRESS_ID AND (sjp.CUSTODY=\'Y\' OR sjp.EMERGENCY=\'Y\')) LEFT OUTER JOIN PEOPLE p ON (p.PERSON_ID=sjp.PERSON_ID) LEFT OUTER JOIN PEOPLE_JOIN_CONTACTS pjc ON (pjc.PERSON_ID=p.PERSON_ID) ';
			$extra2['WHERE'] .= ' AND a.ADDRESS_ID=sja.ADDRESS_ID AND sja.STUDENT_ID=ssm.STUDENT_ID ';
			$extra2['ORDER_BY'] .= 'COALESCE(sjp.CUSTODY,\'N\') DESC';
			$extra2['group'] = array('STUDENT_ID','PERSON_ID');

			// EXPANDED VIEW AND ADDR BREAKS THIS QUERY ... SO, TURN 'EM OFF
			if(!$_REQUEST['_openSIS_PDF'])
			{
				$expanded_view = $_REQUEST['expanded_view'];
				$_REQUEST['expanded_view'] = false;
				$addr = $_REQUEST['addr'];
				unset($_REQUEST['addr']);
				$contacts_RET = GetStuList($extra2);
				$_REQUEST['expanded_view'] = $expanded_view;
				$_REQUEST['addr'] = $addr;
			}
			else
				unset($extra2['columns_after']['CONTACT_INFO']);
		}
		else
		{
			if($view_other_RET['CONTACT_INFO'][1]['VALUE']=='Y' && !$_REQUEST['_openSIS_PDF'])
			{
				$select .= ',NULL AS CONTACT_INFO ';
				$extra['columns_after']['CONTACT_INFO'] = '<IMG SRC=assets/down_phone_button.gif border=0>';
				$functions['CONTACT_INFO'] = 'makeContactInfo';

				$extra2 = $extra;
				$extra2['NoSearchTerms'] = true;
				$extra2['SELECT'] = '';
				$extra2['SELECT_ONLY'] = 'ssm.STUDENT_ID,p.PERSON_ID,p.FIRST_NAME,p.LAST_NAME,sjp.STUDENT_RELATION,pjc.TITLE,pjc.VALUE,a.PHONE,sjp.ADDRESS_ID,COALESCE(sjp.CUSTODY,\'N\') ';
				$extra2['FROM'] .= ',ADDRESS a,STUDENTS_JOIN_ADDRESS sja LEFT OUTER JOIN STUDENTS_JOIN_PEOPLE sjp ON (sja.STUDENT_ID=sjp.STUDENT_ID AND sja.ADDRESS_ID=sjp.ADDRESS_ID AND (sjp.CUSTODY=\'Y\' OR sjp.EMERGENCY=\'Y\')) LEFT OUTER JOIN PEOPLE p ON (p.PERSON_ID=sjp.PERSON_ID) LEFT OUTER JOIN PEOPLE_JOIN_CONTACTS pjc ON (pjc.PERSON_ID=p.PERSON_ID) ';
				$extra2['WHERE'] .= ' AND a.ADDRESS_ID=sja.ADDRESS_ID AND sja.STUDENT_ID=ssm.STUDENT_ID ';
				$extra2['ORDER_BY'] .= 'COALESCE(sjp.CUSTODY,\'N\') DESC';
				$extra2['group'] = array('STUDENT_ID','PERSON_ID');
				$extra2['functions'] = array();
				$extra2['link'] = array();

				// EXPANDED VIEW AND ADDR BREAKS THIS QUERY ... SO, TURN 'EM OFF
				$expanded_view = $_REQUEST['expanded_view'];
				$_REQUEST['expanded_view'] = false;
				$addr = $_REQUEST['addr'];
				unset($_REQUEST['addr']);
				$contacts_RET = GetStuList($extra2);
				$_REQUEST['expanded_view'] = $expanded_view;
				$_REQUEST['addr'] = $addr;
			}
			foreach($view_fields_RET as $field)
			{
                           $custom=DBGet(DBQuery("SHOW COLUMNS FROM STUDENTS WHERE FIELD='CUSTOM_".$field['ID']."'"));
                           $custom=$custom[1];
                           if($custom)
                           {
				$extra['columns_after']['CUSTOM_'.$field['ID']] = $field['TITLE'];
				if($field['TYPE']=='date')
					$functions['CUSTOM_'.$field['ID']] = 'ProperDate';
				elseif($field['TYPE']=='numeric')
					$functions['CUSTOM_'.$field['ID']] = 'removeDot00';
				elseif($field['TYPE']=='codeds')
					$functions['CUSTOM_'.$field['ID']] = 'DeCodeds';
				$select .= ',s.CUSTOM_'.$field['ID'];
                           }
                           else
                           {
                               $custom_stu=DBGet(DBQuery("SELECT TYPE,TITLE FROM CUSTOM_FIELDS WHERE ID='".$field['ID']."'"));
                               $custom_stu=$custom_stu[1];
                               if($custom_stu['TYPE']=='date')
					$functions[strtolower(str_replace (" ", "_", $custom_stu['TITLE']))] = 'ProperDate';
				elseif($custom_stu['TYPE']=='numeric')
					$functions[strtolower(str_replace (" ", "_", $custom_stu['TITLE']))] = 'removeDot00';
				elseif($custom_stu['TYPE']=='codeds')
					$functions[strtolower(str_replace (" ", "_", $custom_stu['TITLE']))] = 'DeCodeds';
				$select .= ',s.'.strtoupper(str_replace (" ", "_", $custom_stu['TITLE']));
                               
                                $extra['columns_after'] += array(strtoupper (str_replace (" ", "_", $custom_stu['TITLE']))=>$custom_stu['TITLE']);
                           }
			}
			if($view_address_RET)
			{
				$extra['FROM'] = " LEFT OUTER JOIN STUDENTS_JOIN_ADDRESS sam ON (ssm.STUDENT_ID=sam.STUDENT_ID AND sam.".$view_address_RET."='Y') LEFT OUTER JOIN ADDRESS a ON (sam.ADDRESS_ID=a.ADDRESS_ID) ".$extra['FROM'];
				$extra['columns_after'] += array('ADDRESS'=>ucwords(strtolower(str_replace('_',' ',$view_address_RET))).' Address','CITY'=>'City','STATE'=>'State','ZIPCODE'=>'Zipcode');
				if($view_address_RET!='MAILING')
					$select .= ",a.ADDRESS_ID,a.ADDRESS,a.CITY,a.STATE,a.ZIPCODE,a.PHONE,ssm.STUDENT_ID AS PARENTS";
				else
					$select .= ",a.ADDRESS_ID,COALESCE(a.MAIL_ADDRESS,a.ADDRESS) AS ADDRESS,COALESCE(a.MAIL_CITY,a.CITY) AS CITY,COALESCE(a.MAIL_STATE,a.STATE) AS STATE,COALESCE(a.MAIL_ZIPCODE,a.ZIPCODE) AS ZIPCODE,a.PHONE,ssm.STUDENT_ID AS PARENTS ";
				$extra['singular'] = 'Student Address';
				$extra['plural'] = 'Student Addresses';

				if($view_other_RET['HOME_PHONE'][1]['VALUE']=='Y')
				{
					$functions['PHONE'] = 'makePhone';
					$extra['columns_after']['PHONE'] = 'Home Phone';
				}
				if($view_other_RET['GUARDIANS'][1]['VALUE']=='Y' || $view_other_RET['ALL_CONTACTS'][1]['VALUE']=='Y')
				{
					$functions['PARENTS'] = 'makeParents';
					if($view_other_RET['ALL_CONTACTS'][1]['VALUE']=='Y')
						$extra['columns_after']['PARENTS'] = 'Contacts';
					else
						$extra['columns_after']['PARENTS'] = 'Guardians';
				}
			}
			elseif($_REQUEST['addr'] || $extra['addr'])
			{
				$extra['FROM'] = " LEFT OUTER JOIN STUDENTS_JOIN_ADDRESS sam ON (ssm.STUDENT_ID=sam.STUDENT_ID ".$extra['STUDENTS_JOIN_ADDRESS'].") LEFT OUTER JOIN ADDRESS a ON (sam.ADDRESS_ID=a.ADDRESS_ID) ".$extra['FROM'];
				$distinct = 'DISTINCT ';
			}
		}
		$extra['SELECT'] .= $select;
	}
	elseif($_REQUEST['addr'] || $extra['addr'])
	{
		$extra['FROM'] = " LEFT OUTER JOIN STUDENTS_JOIN_ADDRESS sam ON (ssm.STUDENT_ID=sam.STUDENT_ID ".$extra['STUDENTS_JOIN_ADDRESS'].") LEFT OUTER JOIN ADDRESS a ON (sam.ADDRESS_ID=a.ADDRESS_ID) ".$extra['FROM'];
		$distinct = 'DISTINCT ';
	}
	switch(User('PROFILE'))
	{
		case 'admin':
			$sql = 'SELECT ';
			if($extra['SELECT_ONLY'])
				$sql .= $extra['SELECT_ONLY'];
			else
			{
				if(Preferences('NAME')=='Common')
					$sql .= "CONCAT(s.LAST_NAME,', ',coalesce(s.COMMON_NAME,s.FIRST_NAME)) AS FULL_NAME,";
				else
					$sql .= "CONCAT(s.LAST_NAME,', ',s.FIRST_NAME,' ',COALESCE(s.MIDDLE_NAME,' ')) AS FULL_NAME,";
				$sql .='s.LAST_NAME,s.FIRST_NAME,s.MIDDLE_NAME,s.STUDENT_ID,s.PHONE,ssm.SCHOOL_ID,s.ALT_ID,ssm.SCHOOL_ID AS LIST_SCHOOL_ID,ssm.GRADE_ID'.$extra['SELECT'];
				
				if($_REQUEST['include_inactive']=='Y')
				$sql .= ','.db_case(array("(ssm.SYEAR='".UserSyear()."' AND ( (ssm.START_DATE IS NOT NULL AND '".date('Y-m-d',strtotime($extra['DATE']))."'>=ssm.START_DATE) AND('".date('Y-m-d',strtotime($extra['DATE']))."'<=ssm.END_DATE OR ssm.END_DATE IS NULL)))",'true',"'<FONT color=green>Active</FONT>'","'<FONT color=red>Inactive</FONT>'")).' AS ACTIVE ';
				
			}
			
			$sql .= " FROM STUDENTS s ";
			if($_REQUEST['mp_comment']){
			$sql .= ",STUDENT_MP_COMMENTS smc ";
			}
			if($_REQUEST['goal_title'] || $_REQUEST['goal_description']){
			$sql .= ",GOAL g ";
			}
			if($_REQUEST['progress_name'] || $_REQUEST['progress_description']){
			$sql .= ",PROGRESS p ";
			}
			if($_REQUEST['doctors_note_comments'] || $_REQUEST['med_day'] || $_REQUEST['med_month'] || $_REQUEST['med_year']){
			$sql .= ",STUDENT_MEDICAL_NOTES smn ";
			}
			if($_REQUEST['type']||$_REQUEST['imm_comments'] || $_REQUEST['imm_day']|| $_REQUEST['imm_month'] || $_REQUEST['imm_year']){
			$sql .= ",STUDENT_MEDICAL sm ";
			}
			if($_REQUEST['med_alrt_title'] || $_REQUEST['ma_day'] || $_REQUEST['ma_month'] || $_REQUEST['ma_year']){
			$sql .= ",STUDENT_MEDICAL_ALERTS sma ";
			}
if($_REQUEST['reason'] || $_REQUEST['result'] || $_REQUEST['med_vist_comments']||  $_REQUEST['nv_day'] || $_REQUEST['nv_month'] || $_REQUEST['nv_year']){
			$sql .= ",STUDENT_MEDICAL_VISITS smv ";
			}
			$sql .=",STUDENT_ENROLLMENT ssm ";
		$sql.=$extra['FROM']." WHERE ssm.STUDENT_ID=s.STUDENT_ID ";
			if($_REQUEST['include_inactive']=='Y')
				$sql .= " AND ssm.ID=(SELECT ID FROM STUDENT_ENROLLMENT WHERE STUDENT_ID=ssm.STUDENT_ID AND SYEAR ='".UserSyear()."' ORDER BY START_DATE DESC LIMIT 1)";
			else
				 $sql .= $_SESSION['inactive_stu_filter'] =" AND ssm.SYEAR='".UserSyear()."' AND ((ssm.START_DATE IS NOT NULL AND '".date('Y-m-d',strtotime($extra['DATE']))."'>=ssm.START_DATE) AND ('".date('Y-m-d',strtotime($extra['DATE']))."'<=ssm.END_DATE OR ssm.END_DATE IS NULL)) ";
                 //$sql .= " AND ssm.SYEAR='".UserSyear()."' AND ('".date('Y-m-d',strtotime($extra['DATE']))."'>=ssm.START_DATE AND ('".date('Y-m-d',strtotime($extra['DATE']))."'<=ssm.END_DATE OR ssm.END_DATE IS NULL)) ";

			if(UserSchool() && $_REQUEST['_search_all_schools']!='Y')
				$sql .= " AND ssm.SYEAR='".UserSyear()."' AND ssm.SCHOOL_ID='".UserSchool()."'";
			else
			{
				if(User('SCHOOLS'))
					$sql .= " AND ssm.SCHOOL_ID IN (".substr(str_replace(',',"','",User('SCHOOLS')),2,-2).") ";
				$extra['columns_after']['LIST_SCHOOL_ID'] = 'School';
				$functions['LIST_SCHOOL_ID'] = 'GetSchool';
			}

			if(!$extra['SELECT_ONLY'] && $_REQUEST['include_inactive']=='Y')
				$extra['columns_after']['ACTIVE'] = 'Status';
				
		break;

		case 'teacher':
			$sql = 'SELECT ';
			if($extra['SELECT_ONLY'])
				$sql .= $extra['SELECT_ONLY'];
			else
			{
				if(Preferences('NAME')=='Common')
					$sql .= "CONCAT(s.LAST_NAME,', ',coalesce(s.COMMON_NAME,s.FIRST_NAME)) AS FULL_NAME,";
				else
					$sql .= "CONCAT(s.LAST_NAME,', ',s.FIRST_NAME,' ',COALESCE(s.MIDDLE_NAME,' ')) AS FULL_NAME,";
				$sql .='s.LAST_NAME,s.FIRST_NAME,s.MIDDLE_NAME,s.STUDENT_ID,s.PHONE,s.ALT_ID,ssm.SCHOOL_ID,ssm.GRADE_ID '.$extra['SELECT'];
				if($_REQUEST['include_inactive']=='Y')
				{
					$sql .= ','.db_case(array("(ssm.START_DATE IS NOT NULL AND ('".$extra['DATE']."'<=ssm.END_DATE OR ssm.END_DATE IS NULL))",'true',"'<FONT color=green>Active</FONT>'","'<FONT color=red>Inactive</FONT>'")).' AS ACTIVE';
					$sql .= ','.db_case(array("(ssm.START_DATE IS NOT NULL AND ('".$extra['DATE']."'<=ss.END_DATE OR ss.END_DATE IS NULL))",'true',"'<FONT color=green>Active</FONT>'","'<FONT color=red>Inactive</FONT>'")).' AS ACTIVE_SCHEDULE';
				}
			}

			$sql .= " FROM STUDENTS s,COURSE_PERIODS cp,SCHEDULE ss ";
			if($_REQUEST['mp_comment']){
			$sql .= ",STUDENT_MP_COMMENTS smc ";
			}
			if($_REQUEST['goal_title'] || $_REQUEST['goal_description']){
			$sql .= ",GOAL g ";
			}
			if($_REQUEST['progress_name'] || $_REQUEST['progress_description']){
			$sql .= ",PROGRESS p ";
			}
			if($_REQUEST['doctors_note_comments'] || $_REQUEST['med_day'] || $_REQUEST['med_month'] || $_REQUEST['med_year']){
			$sql .= ",STUDENT_MEDICAL_NOTES smn ";
			}
			if($_REQUEST['type']||$_REQUEST['imm_comments'] || $_REQUEST['imm_day']|| $_REQUEST['imm_month'] || $_REQUEST['imm_year']){
			$sql .= ",STUDENT_MEDICAL sm ";
			}
			if($_REQUEST['med_alrt_title'] || $_REQUEST['ma_day'] || $_REQUEST['ma_month'] || $_REQUEST['ma_year']){
			$sql .= ",STUDENT_MEDICAL_ALERTS sma ";
			}
if($_REQUEST['reason'] || $_REQUEST['result'] || $_REQUEST['med_vist_comments']||  $_REQUEST['nv_day'] || $_REQUEST['nv_month'] || $_REQUEST['nv_year']){
			$sql .= ",STUDENT_MEDICAL_VISITS smv ";
			}
			$sql.=" ,STUDENT_ENROLLMENT ssm ";
			$sql.=$extra['FROM']." WHERE ssm.STUDENT_ID=s.STUDENT_ID AND ssm.STUDENT_ID=ss.STUDENT_ID
					AND ssm.SCHOOL_ID='".UserSchool()."' AND ssm.SYEAR='".UserSyear()."' AND ssm.SYEAR=cp.SYEAR AND ssm.SYEAR=ss.SYEAR
					AND ss.MARKING_PERIOD_ID IN (".GetAllMP('',$queryMP).")
					AND (cp.TEACHER_ID='".User('STAFF_ID')."' OR cp.SECONDARY_TEACHER_ID='".User('STAFF_ID')."') AND cp.COURSE_PERIOD_ID='".UserCoursePeriod()."'
					AND cp.COURSE_ID=ss.COURSE_ID AND cp.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID";

			if($_REQUEST['include_inactive']=='Y')
			{
				$sql .= " AND ssm.ID=(SELECT ID FROM STUDENT_ENROLLMENT WHERE STUDENT_ID=ssm.STUDENT_ID AND SYEAR=ssm.SYEAR ORDER BY START_DATE DESC LIMIT 1)";
				$sql .= " AND ss.START_DATE=(SELECT START_DATE FROM SCHEDULE WHERE STUDENT_ID=ssm.STUDENT_ID AND SYEAR=ssm.SYEAR AND MARKING_PERIOD_ID IN (".GetAllMP('',$queryMP).") AND COURSE_ID=cp.COURSE_ID AND COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID ORDER BY START_DATE DESC LIMIT 1)";
			}
			else
			{
				$sql .= $_SESSION['inactive_stu_filter'] = " AND (ssm.START_DATE IS NOT NULL AND ('".$extra['DATE']."'<=ssm.END_DATE OR ssm.END_DATE IS NULL))";
				$sql .= $_SESSION['inactive_stu_filter'] =" AND (ssm.START_DATE IS NOT NULL AND ('".$extra['DATE']."'<=ss.END_DATE OR ss.END_DATE IS NULL))";
                                // $sql .= " AND ('".$extra['DATE']."'>=ssm.START_DATE AND ('".$extra['DATE']."'<=ssm.END_DATE OR ssm.END_DATE IS NULL))";
				//$sql .= " AND ('".$extra['DATE']."'>=ss.START_DATE AND ('".$extra['DATE']."'<=ss.END_DATE OR ss.END_DATE IS NULL))";
			}

			if(!$extra['SELECT_ONLY'] && $_REQUEST['include_inactive']=='Y')
			{
				$extra['columns_after']['ACTIVE'] = 'School Status';
				$extra['columns_after']['ACTIVE_SCHEDULE'] = 'Course Status';
			}
		break;

		case 'parent':
		case 'student':
			$sql = 'SELECT ';
			if($extra['SELECT_ONLY'])
				$sql .= $extra['SELECT_ONLY'];
			else
			{
				if(Preferences('NAME')=='Common')
					$sql .= "CONCAT(s.LAST_NAME,', ',coalesce(s.COMMON_NAME,s.FIRST_NAME)) AS FULL_NAME,";
				else
					$sql .= "CONCAT(s.LAST_NAME,', ',s.FIRST_NAME,' ',COALESCE(s.MIDDLE_NAME,' ')) AS FULL_NAME,";
				$sql .='s.LAST_NAME,s.FIRST_NAME,s.MIDDLE_NAME,s.STUDENT_ID,s.ALT_ID,ssm.SCHOOL_ID,ssm.GRADE_ID '.$extra['SELECT'];
			}
			$sql .= " FROM STUDENTS s,STUDENT_ENROLLMENT ssm ".$extra['FROM']."
					WHERE ssm.STUDENT_ID=s.STUDENT_ID AND ssm.SYEAR='".UserSyear()."' AND ssm.SCHOOL_ID='".UserSchool()."' AND ('".DBDate()."' BETWEEN ssm.START_DATE AND ssm.END_DATE OR (ssm.END_DATE IS NULL AND '".DBDate()."'>ssm.START_DATE)) AND ssm.STUDENT_ID".($extra['ASSOCIATED']?" IN (SELECT STUDENT_ID FROM STUDENTS_JOIN_USERS WHERE STAFF_ID='".$extra['ASSOCIATED']."')":"='".UserStudentID()."'");
		break;
		default:
			exit('Error');
	}
        if($expanded_view==true)
        {
            $custom_str=CustomFields('where','',1);
            if($custom_str!='')
                $_SESSION['custom_count_sql']=$custom_str;
            
            $sql .= $custom_str;
        }
        elseif($expanded_view==false)
        {
            $custom_str=CustomFields('where','',2);
            if($custom_str!='')
                $_SESSION['custom_count_sql']=$custom_str;

            $sql .= $custom_str;
        }
        else {
            $custom_str = CustomFields('where');
            if($custom_str!='')
                $_SESSION['custom_count_sql']=$custom_str;
            
             $sql .= $custom_str;
        }

        $sql .= $extra['WHERE'].' ';
	$sql = appendSQL($sql,$extra);

//        TODO               Modification Required
//        if($_SESSION['stu_search']['sql'] && $_REQUEST['return_session'] && $extra['SELECT']!='' && strpos($sql,'ADDRESS a')==0)
//        {
//            $sql = str_replace("FROM", $extra['SELECT']." FROM",$sql);
//        }
//
//        if($_SESSION['stu_search']['sql'] && $_REQUEST['return_session'] && $extra['FROM']!='' && strpos($sql,'ADDRESS a')==0)
//        {
//            $sql = str_replace("WHERE",$extra['FROM']." WHERE",$sql);
//	
//        }
//        --------------------------------------------------

	if($extra['GROUP'])
		$sql .= ' GROUP BY '.$extra['GROUP'];

	if(!$extra['ORDER_BY'] && !$extra['SELECT_ONLY'])
	{
		if(Preferences('SORT')=='Grade')
			$sql .= " ORDER BY (SELECT SORT_ORDER FROM SCHOOL_GRADELEVELS WHERE ID=ssm.GRADE_ID),FULL_NAME";
		else
			$sql .= " ORDER BY FULL_NAME";
		$sql .= $extra['ORDER'];
	}
	elseif($extra['ORDER_BY'] && !($_SESSION['stu_search']['sql'] && $_REQUEST['return_session']))
		$sql .= ' ORDER BY '.$extra['ORDER_BY'];

	if($extra['DEBUG']===true)
		echo '<!--'.$sql.'-->';
	$return = DBGet(DBQuery($sql),$functions,$extra['group']);
                  $_SESSION['count_stu'] =  count($return);
                  return $return;
}

function makeContactInfo($student_id,$column)
{	global $THIS_RET,$contacts_RET;

	if(count($contacts_RET[$THIS_RET['STUDENT_ID']]))
	{
		foreach($contacts_RET[$THIS_RET['STUDENT_ID']] as $person)
		{
			if($person[1]['FIRST_NAME'] || $person[1]['LAST_NAME'])
				$tipmessage .= ''.$person[1]['STUDENT_RELATION'].': '.$person[1]['FIRST_NAME'].' '.$person[1]['LAST_NAME'].' | ';
			$tipmessage .= '';
			if($person[1]['PHONE'])
				$tipmessage .= ' '.$person[1]['PHONE'].'';
			foreach($person as $info)
			{
				if($info['TITLE'] || $info['VALUE'])
					$tipmessage .= ''.$info['TITLE'].''.$info['VALUE'].'';
			}
			$tipmessage .= '';
		}
	}
	else
		$tipmessage = 'This student has no contact information.';
	return button('phone','','# alt="'.$tipmessage.'" title="'.$tipmessage.'"');
}

function removeDot00($value,$column)
{
	return str_replace('.00','',$value);
}

function makePhone($phone,$column='')
{	global $THIS_RET;

	if(strlen($phone)==10)
		$return .= '('.substr($phone,0,3).')'.substr($phone,3,7).'-'.substr($phone,7);
	if(strlen($phone)=='7')
		$return .= substr($phone,0,3).'-'.substr($phone,3);
	else
		$return .= $phone;

	return $return;
}

function makeParents($student_id,$column='')
{	global $THIS_RET,$view_other_RET,$_openSIS;

	if($THIS_RET['PARENTS']==$student_id)
	{
		if(!$THIS_RET['ADDRESS_ID'])
			$THIS_RET['ADDRESS_ID'] = 0;

		$THIS_RET['PARENTS'] = '';

		if($_openSIS['makeParents'])
			$constraint = 'AND (LOWER(sjp.STUDENT_RELATION) LIKE \''.strtolower($_openSIS['makeParents']).'%\')';
		elseif($view_other_RET['ALL_CONTACTS'][1]['VALUE']=='Y')
			$constraint = "AND (sjp.CUSTODY='Y' OR sjp.EMERGENCY='Y')";
		else
			$constraint = "AND sjp.CUSTODY='Y'";

		$people_RET = DBGet(DBQuery("SELECT p.PERSON_ID,p.FIRST_NAME,p.LAST_NAME,sjp.ADDRESS_ID,sjp.CUSTODY,sjp.EMERGENCY FROM STUDENTS_JOIN_PEOPLE sjp,PEOPLE p WHERE sjp.PERSON_ID=p.PERSON_ID AND sjp.STUDENT_ID='$student_id' ".$constraint." ORDER BY p.LAST_NAME,p.FIRST_NAME"));
		if(count($people_RET))
		{
			foreach($people_RET as $person)
			{
				if($person['ADDRESS_ID']==$THIS_RET['ADDRESS_ID'])
				{
					if($person['CUSTODY']=='Y')
						$color = '0000FF';
					elseif($person['EMERGENCY']=='Y')
						$color = 'FFFF00';

					if($_REQUEST['_openSIS_PDF'])
						$THIS_RET['PARENTS'] .= '<TR><TD>'.button('dot',$color,'',6).'</TD><TD>'.$person['FIRST_NAME'].' '.$person['LAST_NAME'].'</TD></TR>, ';
					else
						$THIS_RET['PARENTS'] .= '<TR><TD>'.button('dot',$color,'',6).'</TD><TD><A HREF=# onclick=\'window.open("Modules.php?modname=misc/ViewContact.php?person_id='.$person['PERSON_ID'].'","","scrollbars=yes,resizable=yes,width=400,height=200");\'>'.$person['FIRST_NAME'].' '.$person['LAST_NAME'].'</A></TD></TR>';
				}
			}
			if($_REQUEST['_openSIS_PDF'])
				$THIS_RET['PARENTS'] = substr($THIS_RET['PARENTS'],0,-2);
		}
	}

	if($THIS_RET['PARENTS'])
		return '<TABLE border=0 cellpadding=0 cellspacing=0 class=LO_field>'.$THIS_RET['PARENTS'].'</TABLE>';
}

function appendSQL($sql,& $extra)
{	global $_openSIS;

	if($_REQUEST['stuid'])
	{
		$sql .= " AND ssm.STUDENT_ID = '".str_replace("'","\'",$_REQUEST[stuid])."' ";
		if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Student ID: </b></font>'.$_REQUEST['stuid'].'<BR>';
	}
         if($_REQUEST['altid'])
	{
		//$sql .= " AND s.ALT_ID = '$_REQUEST[altid]' ";
		$sql .= " AND LOWER(s.ALT_ID) LIKE '".str_replace("'","\'",strtolower(trim($_REQUEST['altid'])))."%' ";
		if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Student ID: </b></font>'.$_REQUEST['stuid'].'<BR>';
	}
	if($_REQUEST['last'])
	{
		$sql .= " AND LOWER(s.LAST_NAME) LIKE '".str_replace("'","\'",strtolower(trim($_REQUEST['last'])))."%' ";
		if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Last Name starts with: </b></font>'.trim($_REQUEST['last']).'<BR>';
	}
	if($_REQUEST['first'])
	{
		$sql .= " AND LOWER(s.FIRST_NAME) LIKE '".str_replace("'","\'",strtolower(trim($_REQUEST['first'])))."%' ";
		if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>First Name starts with: </b></font>'.trim($_REQUEST['first']).'<BR>';
	}
	if($_REQUEST['grade'])
	{
		$sql .= " AND ssm.GRADE_ID = '".str_replace("'","\'",$_REQUEST[grade])."' ";
		if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Grade: </b></font>'.GetGrade($_REQUEST['grade']).'<BR>';
	}
	if($_REQUEST['addr'])
	{
		$sql .= " AND (LOWER(a.ADDRESS) LIKE '%".str_replace("'","\'",strtolower(trim($_REQUEST['addr'])))."%' OR LOWER(a.CITY) LIKE '".str_replace("'","\'",strtolower(trim($_REQUEST['addr'])))."%' OR LOWER(a.STATE)='".str_replace("'","\'",strtolower(trim($_REQUEST['addr'])))."' OR ZIPCODE LIKE '".trim(str_replace("'","\'",$_REQUEST['addr']))."%')";
		if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Address contains: </b></font>'.trim($_REQUEST['addr']).'<BR>';
	}
	if($_REQUEST['preferred_hospital'])
	{
		$sql .= " AND LOWER(s.PREFERRED_HOSPITAL) LIKE '".str_replace("'","\'",strtolower($_REQUEST['preferred_hospital']))."%' ";
		if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Preferred Medical Facility starts with: </b></font>'.$_REQUEST['preferred_hospital'].'<BR>';
	}
	if($_REQUEST['mp_comment'])
	{
		$sql .= " AND LOWER(smc.COMMENT) LIKE '".str_replace("'","\'",strtolower($_REQUEST['mp_comment']))."%' AND s.STUDENT_ID=smc.STUDENT_ID ";
		if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Comments starts with: </b></font>'.$_REQUEST['mp_comment'].'<BR>';
	}
	if($_REQUEST['goal_title'])
	{
		$sql .= " AND LOWER(g.GOAL_TITLE) LIKE '".str_replace("'","\'",strtolower($_REQUEST['goal_title']))."%' AND s.STUDENT_ID=g.STUDENT_ID ";
		if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Goal Title starts with: </b></font>'.$_REQUEST['goal_title'].'<BR>';
	}
		if($_REQUEST['goal_description'])
	{
		$sql .= " AND LOWER(g.GOAL_DESCRIPTION) LIKE '".str_replace("'","\'",strtolower($_REQUEST['goal_description']))."%' AND s.STUDENT_ID=g.STUDENT_ID ";
		if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Goal Description starts with: </b></font>'.$_REQUEST['goal_description'].'<BR>';
	}
		if($_REQUEST['progress_name'])
	{
		$sql .= " AND LOWER(p.PROGRESS_NAME) LIKE '".str_replace("'","\'",strtolower($_REQUEST['progress_name']))."%' AND s.STUDENT_ID=p.STUDENT_ID ";
		if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Progress Period Name starts with: </b></font>'.$_REQUEST['progress_name'].'<BR>';
	}
	if($_REQUEST['progress_description'])
	{
		$sql .= " AND LOWER(p.PROGRESS_DESCRIPTION) LIKE '".str_replace("'","\'",strtolower($_REQUEST['progress_description']))."%' AND s.STUDENT_ID=p.STUDENT_ID ";
		if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Progress Assessment starts with: </b></font>'.$_REQUEST['progress_description'].'<BR>';
	}
	if($_REQUEST['doctors_note_comments'])
	{
		$sql .= " AND LOWER(smn.DOCTORS_NOTE_COMMENTS) LIKE '".str_replace("'","\'",strtolower($_REQUEST['doctors_note_comments']))."%' AND s.STUDENT_ID=smn.STUDENT_ID ";
		if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Doctor\'s Note starts with: </b></font>'.$_REQUEST['doctors_note_comments'].'<BR>';
	}
	if($_REQUEST['type'])
	{
		$sql .= " AND LOWER(sm.TYPE) LIKE '".str_replace("'","\'",strtolower($_REQUEST['type']))."%' AND s.STUDENT_ID=sm.STUDENT_ID ";
		if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Type starts with: </b></font>'.$_REQUEST['type'].'<BR>';
	}
	if($_REQUEST['imm_comments'])
	{
		$sql .= " AND LOWER(sm.COMMENTS) LIKE '".str_replace("'","\'",strtolower($_REQUEST['imm_comments']))."%' AND s.STUDENT_ID=sm.STUDENT_ID ";
		if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Comments starts with: </b></font>'.$_REQUEST['imm_comments'].'<BR>';
	}
	if($_REQUEST['imm_day']&& $_REQUEST['imm_month']&& $_REQUEST['imm_year'])
	{
$imm_date=$_REQUEST['imm_year'].'-'.$_REQUEST['imm_month'].'-'.$_REQUEST['imm_day'];
		$sql .= " AND sm.MEDICAL_DATE ='".date('Y-m-d',strtotime($imm_date))."' AND s.STUDENT_ID=sm.STUDENT_ID ";
		if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Immunization Date: </b></font>'.$imm_date.'<BR>';
	}elseif($_REQUEST['imm_day'] || $_REQUEST['imm_month'] || $_REQUEST['imm_year']){
	if($_REQUEST['imm_day']){
	$sql .= " AND SUBSTR(sm.MEDICAL_DATE,9,2) ='".$_REQUEST['imm_day']."' AND s.STUDENT_ID=sm.STUDENT_ID ";
	$imm_date.=" Day :".$_REQUEST['imm_day'];
	}
	if($_REQUEST['imm_month']){
	$sql .= " AND SUBSTR(sm.MEDICAL_DATE,6,2) ='".$_REQUEST['imm_month']."' AND s.STUDENT_ID=sm.STUDENT_ID ";
	$imm_date.=" Month :".$_REQUEST['imm_month'];
	}
	if($_REQUEST['imm_year']){
	$sql .= " AND SUBSTR(sm.MEDICAL_DATE,1,4) ='".$_REQUEST['imm_year']."' AND s.STUDENT_ID=sm.STUDENT_ID ";
	$imm_date.=" Year :".$_REQUEST['imm_year'];
	}
	if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Immunization Date: </b></font>'.$imm_date.'<BR>';
	}
	if($_REQUEST['med_day']&&$_REQUEST['med_month']&&$_REQUEST['med_year'])
	{
$med_date=$_REQUEST['med_year'].'-'.$_REQUEST['med_month'].'-'.$_REQUEST['med_day'];
		$sql .= " AND smn.DOCTORS_NOTE_DATE ='".date('Y-m-d',strtotime($med_date))."' AND s.STUDENT_ID=smn.STUDENT_ID ";
		if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Medical Date: </b></font>'.$med_date.'<BR>';
	}elseif($_REQUEST['med_day'] || $_REQUEST['med_month'] || $_REQUEST['med_year']){
	if($_REQUEST['med_day']){
	$sql .= " AND SUBSTR(smn.DOCTORS_NOTE_DATE,9,2) ='".$_REQUEST['med_day']."' AND s.STUDENT_ID=smn.STUDENT_ID ";
	$med_date.=" Day :".$_REQUEST['med_day'];
	}
	if($_REQUEST['med_month']){
	$sql .= " AND SUBSTR(smn.DOCTORS_NOTE_DATE,6,2) ='".$_REQUEST['med_month']."' AND s.STUDENT_ID=smn.STUDENT_ID ";
	$med_date.=" Month :".$_REQUEST['med_month'];
	}
	if($_REQUEST['med_year']){
	$sql .= " AND SUBSTR(smn.DOCTORS_NOTE_DATE,1,4) ='".$_REQUEST['med_year']."' AND s.STUDENT_ID=smn.STUDENT_ID ";
	$med_date.=" Year :".$_REQUEST['med_year'];
	}
	if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Medical Date: </b></font>'.$med_date.'<BR>';
	}
	if($_REQUEST['ma_day']&&$_REQUEST['ma_month']&&$_REQUEST['ma_year'])
	{
$ma_date=$_REQUEST['ma_year'].'-'.$_REQUEST['ma_month'].'-'.$_REQUEST['ma_day'];
		$sql .= " AND sma.ALERT_DATE ='".date('Y-m-d',strtotime($ma_date))."' AND s.STUDENT_ID=sma.STUDENT_ID ";
		if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Medical Alert Date: </b></font>'.$ma_date.'<BR>';
	}elseif($_REQUEST['ma_day'] || $_REQUEST['ma_month'] || $_REQUEST['ma_year']){
	if($_REQUEST['ma_day']){
	$sql .= " AND SUBSTR(sma.ALERT_DATE,9,2) ='".$_REQUEST['ma_day']."' AND s.STUDENT_ID=sma.STUDENT_ID ";
	$ma_date.=" Day :".$_REQUEST['ma_day'];
	}
	if($_REQUEST['ma_month']){
	$sql .= " AND SUBSTR(sma.ALERT_DATE,6,2) ='".$_REQUEST['ma_month']."' AND s.STUDENT_ID=sma.STUDENT_ID ";
	$ma_date.=" Month :".$_REQUEST['ma_month'];
	}
	if($_REQUEST['ma_year']){
	$sql .= " AND SUBSTR(sma.ALERT_DATE,1,4) ='".$_REQUEST['ma_year']."' AND s.STUDENT_ID=sma.STUDENT_ID ";
	$ma_date.=" Year :".$_REQUEST['ma_year'];
	}
	if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Medical Alert Date: </b></font>'.$ma_date.'<BR>';
	}
	if($_REQUEST['nv_day']&&$_REQUEST['nv_month']&&$_REQUEST['nv_year'])
	{
$nv_date=$_REQUEST['nv_year'].'-'.$_REQUEST['nv_month'].'-'.$_REQUEST['nv_day'];
		$sql .= " AND smv.SCHOOL_DATE ='".date('Y-m-d',strtotime($nv_date))."' AND s.STUDENT_ID=smv.STUDENT_ID ";
		if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Nurse Visit Date: </b></font>'.$nv_date.'<BR>';
	}elseif($_REQUEST['nv_day'] || $_REQUEST['nv_month'] || $_REQUEST['nv_year']){
	if($_REQUEST['nv_day']){
	$sql .= " AND SUBSTR(smv.SCHOOL_DATE,9,2) ='".$_REQUEST['nv_day']."' AND s.STUDENT_ID=smv.STUDENT_ID ";
	$nv_date.=" Day :".$_REQUEST['nv_day'];
	}
	if($_REQUEST['nv_month']){
	$sql .= " AND SUBSTR(smv.SCHOOL_DATE,6,2) ='".$_REQUEST['nv_month']."' AND s.STUDENT_ID=smv.STUDENT_ID ";
	$nv_date.=" Month :".$_REQUEST['nv_month'];
	}
	if($_REQUEST['nv_year']){
	$sql .= " AND SUBSTR(smv.SCHOOL_DATE,1,4) ='".$_REQUEST['nv_year']."' AND s.STUDENT_ID=smv.STUDENT_ID ";
	$nv_date.=" Year :".$_REQUEST['nv_year'];
	}
	if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Nurse Visit Date: </b></font>'.$nv_date.'<BR>';
	}
	
	
	if($_REQUEST['med_alrt_title'])
	{
		$sql .= " AND LOWER(sma.TITLE) LIKE '".str_replace("'","\'",strtolower($_REQUEST['med_alrt_title']))."%' AND s.STUDENT_ID=sma.STUDENT_ID ";
		if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Alert starts with: </b></font>'.$_REQUEST['med_alrt_title'].'<BR>';
	}
	if($_REQUEST['reason'])
	{
		$sql .= " AND LOWER(smv.REASON) LIKE '".str_replace("'","\'",strtolower($_REQUEST['reason']))."%' AND s.STUDENT_ID=smv.STUDENT_ID ";
		if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Reason starts with: </b></font>'.$_REQUEST['reason'].'<BR>';
	}
	if($_REQUEST['result'])
	{
		$sql .= " AND LOWER(smv.RESULT) LIKE '".str_replace("'","\'",strtolower($_REQUEST['result']))."%' AND s.STUDENT_ID=smv.STUDENT_ID ";
		if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Result starts with: </b></font>'.$_REQUEST['result'].'<BR>';
	}
	if($_REQUEST['med_vist_comments'])
	{
		$sql .= " AND LOWER(smv.COMMENTS) LIKE '".str_replace("'","\'",strtolower($_REQUEST['med_vist_comments']))."%' AND s.STUDENT_ID=smv.STUDENT_ID ";
		if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Nurse Visit Comments starts with: </b></font>'.$_REQUEST['med_vist_comments'].'<BR>';
	}
	if($_REQUEST['day_to_birthdate']&&$_REQUEST['month_to_birthdate']&&$_REQUEST['day_from_birthdate']&&$_REQUEST['month_from_birthdate'])
	{
	$date_to=$_REQUEST['month_to_birthdate'].'-'.$_REQUEST['day_to_birthdate'];
	$date_from=$_REQUEST['month_from_birthdate'].'-'.$_REQUEST['day_from_birthdate'];
		$sql .= " AND (SUBSTR(s.BIRTHDATE,6,2) BETWEEN ".$_REQUEST['month_from_birthdate']." AND ".$_REQUEST['month_to_birthdate'].") ";
		$sql .= " AND (SUBSTR(s.BIRTHDATE,9,2) BETWEEN ".$_REQUEST['day_from_birthdate']." AND ".$_REQUEST['day_to_birthdate'].") ";
		if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Birthday Starts from '.$date_from.' to '.$date_to.'</b></font>';
	}	
	// test cases start
	
	
	
	// test cases end
	if($_SESSION['stu_search']['sql'] && $_REQUEST['return_session']){
            unset($_SESSION['inactive_stu_filter']);
            return $_SESSION['stu_search']['sql'];
	}else{
            if($_REQUEST['sql_save_session'] && !$_SESSION['stu_search']['search_from_grade']){
                $_SESSION['stu_search']['sql']=$sql;
            }else if($_SESSION['stu_search']['search_from_grade']){
                unset($_SESSION['stu_search']['search_from_grade']);
            }
	return $sql;
	}
}

############################################################################################
function GetStuList_Absence_Summary(& $extra)
{	global $contacts_RET,$view_other_RET,$_openSIS;
	$offset='GRADE_ID';

	if((!$extra['SELECT_ONLY'] || strpos($extra['SELECT_ONLY'],$offset)!==false) && !$extra['functions']['GRADE_ID'])
		$functions = array('GRADE_ID'=>'GetGrade');
	else
		$functions = array();

	if($extra['functions'])
		$functions +=$extra['functions'];

	if(!$extra['DATE'])
	{
		$queryMP = UserMP();
		$extra['DATE'] = DBDate();
	}
	else
		$queryMP = GetCurrentMP('QTR',$extra['DATE'],false);

	if($_REQUEST['expanded_view']=='true')
	{
		if(!$extra['columns_after'])
			$extra['columns_after'] = array();
#############################################################################################
//Commented as it crashing for Linux due to  Blank Database tables

		$view_fields_RET = DBGet(DBQuery("SELECT cf.ID,cf.TYPE,cf.TITLE FROM PROGRAM_USER_CONFIG puc,CUSTOM_FIELDS cf WHERE puc.TITLE=cf.ID AND puc.PROGRAM='StudentFieldsView' AND puc.USER_ID='".User('STAFF_ID')."' AND puc.VALUE='Y'"));
#############################################################################################
		$view_address_RET = DBGet(DBQuery("SELECT VALUE FROM PROGRAM_USER_CONFIG WHERE PROGRAM='StudentFieldsView' AND TITLE='ADDRESS' AND USER_ID='".User('STAFF_ID')."'"));
		$view_address_RET = $view_address_RET[1]['VALUE'];
		$view_other_RET = DBGet(DBQuery("SELECT TITLE,VALUE FROM PROGRAM_USER_CONFIG WHERE PROGRAM='StudentFieldsView' AND TITLE IN ('PHONE','HOME_PHONE','GUARDIANS','ALL_CONTACTS') AND USER_ID='".User('STAFF_ID')."'"),array(),array('TITLE'));

		if(!count($view_fields_RET) && !isset($view_address_RET) && !isset($view_other_RET['CONTACT_INFO']))
		{
			$extra['columns_after'] = array('PHONE'=>'Phone','GENDER'=>'Gender','ETHNICITY'=>'Ethnicity','ADDRESS'=>'Mailing Address','CITY'=>'City','STATE'=>'State','ZIPCODE'=>'Zipcode') + $extra['columns_after'];
			$select = ',s.PHONE,s.GENDER,s.ETHNICITY,COALESCE(a.MAIL_ADDRESS,a.ADDRESS) AS ADDRESS,COALESCE(a.MAIL_CITY,a.CITY) AS CITY,COALESCE(a.MAIL_STATE,a.STATE) AS STATE,COALESCE(a.MAIL_ZIPCODE,a.ZIPCODE) AS ZIPCODE ';
			#$extra['FROM'] = " LEFT OUTER JOIN STUDENTS_JOIN_ADDRESS sam ON (ssm.STUDENT_ID=sam.STUDENT_ID AND sam.MAILING='Y') LEFT OUTER JOIN ADDRESS a ON (sam.ADDRESS_ID=a.ADDRESS_ID) ".$extra['FROM'];
			$extra['FROM'] = " LEFT OUTER JOIN STUDENTS_JOIN_ADDRESS sam ON (ssm.STUDENT_ID=sam.STUDENT_ID ) LEFT OUTER JOIN ADDRESS a ON (sam.ADDRESS_ID=a.ADDRESS_ID) ".$extra['FROM'];
                        $functions['CONTACT_INFO'] = 'makeContactInfo';
			// if gender is converted to codeds type
			//$functions['CUSTOM_200000000'] = 'DeCodeds';
			$extra['singular'] = 'Student Address';
			$extra['plural'] = 'Student Addresses';

			$extra2['NoSearchTerms'] = true;
			$extra2['SELECT_ONLY'] = 'ssm.STUDENT_ID,p.PERSON_ID,p.FIRST_NAME,p.LAST_NAME,sjp.STUDENT_RELATION,pjc.TITLE,pjc.VALUE,a.PHONE,sjp.ADDRESS_ID ';
			$extra2['FROM'] .= ',ADDRESS a,STUDENTS_JOIN_ADDRESS sja LEFT OUTER JOIN STUDENTS_JOIN_PEOPLE sjp ON (sja.STUDENT_ID=sjp.STUDENT_ID AND sja.ADDRESS_ID=sjp.ADDRESS_ID AND (sjp.CUSTODY=\'Y\' OR sjp.EMERGENCY=\'Y\')) LEFT OUTER JOIN PEOPLE p ON (p.PERSON_ID=sjp.PERSON_ID) LEFT OUTER JOIN PEOPLE_JOIN_CONTACTS pjc ON (pjc.PERSON_ID=p.PERSON_ID) ';
			$extra2['WHERE'] .= ' AND a.ADDRESS_ID=sja.ADDRESS_ID AND sja.STUDENT_ID=ssm.STUDENT_ID ';
			$extra2['ORDER_BY'] .= 'COALESCE(sjp.CUSTODY,\'N\') DESC';
			$extra2['group'] = array('STUDENT_ID','PERSON_ID');

			// EXPANDED VIEW AND ADDR BREAKS THIS QUERY ... SO, TURN 'EM OFF
			if(!$_REQUEST['_openSIS_PDF'])
			{
				$expanded_view = $_REQUEST['expanded_view'];
				$_REQUEST['expanded_view'] = false;
				$addr = $_REQUEST['addr'];
				unset($_REQUEST['addr']);
				$contacts_RET = GetStuList($extra2);
				$_REQUEST['expanded_view'] = $expanded_view;
				$_REQUEST['addr'] = $addr;
			}
			else
				unset($extra2['columns_after']['CONTACT_INFO']);
		}
		else
		{
			if($view_other_RET['CONTACT_INFO'][1]['VALUE']=='Y' && !$_REQUEST['_openSIS_PDF'])
			{
				$select .= ',NULL AS CONTACT_INFO ';
				$extra['columns_after']['CONTACT_INFO'] = '<IMG SRC=assets/down_phone_button.gif border=0>';
				$functions['CONTACT_INFO'] = 'makeContactInfo';

				$extra2 = $extra;
				$extra2['NoSearchTerms'] = true;
				$extra2['SELECT'] = '';
				$extra2['SELECT_ONLY'] = 'ssm.STUDENT_ID,p.PERSON_ID,p.FIRST_NAME,p.LAST_NAME,sjp.STUDENT_RELATION,pjc.TITLE,pjc.VALUE,a.PHONE,sjp.ADDRESS_ID,COALESCE(sjp.CUSTODY,\'N\') ';
				$extra2['FROM'] .= ',ADDRESS a,STUDENTS_JOIN_ADDRESS sja LEFT OUTER JOIN STUDENTS_JOIN_PEOPLE sjp ON (sja.STUDENT_ID=sjp.STUDENT_ID AND sja.ADDRESS_ID=sjp.ADDRESS_ID AND (sjp.CUSTODY=\'Y\' OR sjp.EMERGENCY=\'Y\')) LEFT OUTER JOIN PEOPLE p ON (p.PERSON_ID=sjp.PERSON_ID) LEFT OUTER JOIN PEOPLE_JOIN_CONTACTS pjc ON (pjc.PERSON_ID=p.PERSON_ID) ';
				$extra2['WHERE'] .= ' AND a.ADDRESS_ID=sja.ADDRESS_ID AND sja.STUDENT_ID=ssm.STUDENT_ID ';
				$extra2['ORDER_BY'] .= 'COALESCE(sjp.CUSTODY,\'N\') DESC';
				$extra2['group'] = array('STUDENT_ID','PERSON_ID');
				$extra2['functions'] = array();
				$extra2['link'] = array();

				// EXPANDED VIEW AND ADDR BREAKS THIS QUERY ... SO, TURN 'EM OFF
				$expanded_view = $_REQUEST['expanded_view'];
				$_REQUEST['expanded_view'] = false;
				$addr = $_REQUEST['addr'];
				unset($_REQUEST['addr']);
				$contacts_RET = GetStuList($extra2);
				$_REQUEST['expanded_view'] = $expanded_view;
				$_REQUEST['addr'] = $addr;
			}
			foreach($view_fields_RET as $field)
			{
                           $custom=DBGet(DBQuery("SHOW COLUMNS FROM STUDENTS WHERE FIELD='CUSTOM_".$field['ID']."'"));
                           $custom=$custom[1];
                           if($custom)
                           {
				$extra['columns_after']['CUSTOM_'.$field['ID']] = $field['TITLE'];
				if($field['TYPE']=='date')
					$functions['CUSTOM_'.$field['ID']] = 'ProperDate';
				elseif($field['TYPE']=='numeric')
					$functions['CUSTOM_'.$field['ID']] = 'removeDot00';
				elseif($field['TYPE']=='codeds')
					$functions['CUSTOM_'.$field['ID']] = 'DeCodeds';
				$select .= ',s.CUSTOM_'.$field['ID'];
                           }
                           else
                           {
                               $custom_stu=DBGet(DBQuery("SELECT TYPE,TITLE FROM CUSTOM_FIELDS WHERE ID='".$field['ID']."'"));
                               $custom_stu=$custom_stu[1];
                               if($custom_stu['TYPE']=='date')
					$functions[strtolower(str_replace (" ", "_", $custom_stu['TITLE']))] = 'ProperDate';
				elseif($custom_stu['TYPE']=='numeric')
					$functions[strtolower(str_replace (" ", "_", $custom_stu['TITLE']))] = 'removeDot00';
				elseif($custom_stu['TYPE']=='codeds')
					$functions[strtolower(str_replace (" ", "_", $custom_stu['TITLE']))] = 'DeCodeds';
				$select .= ',s.'.strtoupper(str_replace (" ", "_", $custom_stu['TITLE']));

                                $extra['columns_after'] += array(strtoupper (str_replace (" ", "_", $custom_stu['TITLE']))=>$custom_stu['TITLE']);
                           }
			}
			if($view_address_RET)
			{
				$extra['FROM'] = " LEFT OUTER JOIN STUDENTS_JOIN_ADDRESS sam ON (ssm.STUDENT_ID=sam.STUDENT_ID AND sam.".$view_address_RET."='Y') LEFT OUTER JOIN ADDRESS a ON (sam.ADDRESS_ID=a.ADDRESS_ID) ".$extra['FROM'];
				$extra['columns_after'] += array('ADDRESS'=>ucwords(strtolower(str_replace('_',' ',$view_address_RET))).' Address','CITY'=>'City','STATE'=>'State','ZIPCODE'=>'Zipcode');
				if($view_address_RET!='MAILING')
					$select .= ",a.ADDRESS_ID,a.ADDRESS,a.CITY,a.STATE,a.ZIPCODE,a.PHONE,ssm.STUDENT_ID AS PARENTS";
				else
					$select .= ",a.ADDRESS_ID,COALESCE(a.MAIL_ADDRESS,a.ADDRESS) AS ADDRESS,COALESCE(a.MAIL_CITY,a.CITY) AS CITY,COALESCE(a.MAIL_STATE,a.STATE) AS STATE,COALESCE(a.MAIL_ZIPCODE,a.ZIPCODE) AS ZIPCODE,a.PHONE,ssm.STUDENT_ID AS PARENTS ";
				$extra['singular'] = 'Student Address';
				$extra['plural'] = 'Student Addresses';

				if($view_other_RET['HOME_PHONE'][1]['VALUE']=='Y')
				{
					$functions['PHONE'] = 'makePhone';
					$extra['columns_after']['PHONE'] = 'Home Phone';
				}
				if($view_other_RET['GUARDIANS'][1]['VALUE']=='Y' || $view_other_RET['ALL_CONTACTS'][1]['VALUE']=='Y')
				{
					$functions['PARENTS'] = 'makeParents';
					if($view_other_RET['ALL_CONTACTS'][1]['VALUE']=='Y')
						$extra['columns_after']['PARENTS'] = 'Contacts';
					else
						$extra['columns_after']['PARENTS'] = 'Guardians';
				}
			}
			elseif($_REQUEST['addr'] || $extra['addr'])
			{
				$extra['FROM'] = " LEFT OUTER JOIN STUDENTS_JOIN_ADDRESS sam ON (ssm.STUDENT_ID=sam.STUDENT_ID ".$extra['STUDENTS_JOIN_ADDRESS'].") LEFT OUTER JOIN ADDRESS a ON (sam.ADDRESS_ID=a.ADDRESS_ID) ".$extra['FROM'];
				$distinct = 'DISTINCT ';
			}
		}
		$extra['SELECT'] .= $select;

	}
	elseif($_REQUEST['addr'] || $extra['addr'])
	{
		$extra['FROM'] = " LEFT OUTER JOIN STUDENTS_JOIN_ADDRESS sam ON (ssm.STUDENT_ID=sam.STUDENT_ID ".$extra['STUDENTS_JOIN_ADDRESS'].") LEFT OUTER JOIN ADDRESS a ON (sam.ADDRESS_ID=a.ADDRESS_ID) ".$extra['FROM'];
		$distinct = 'DISTINCT ';
	}
        $_SESSION['new_customsql']= $extra['SELECT'];
	switch(User('PROFILE'))
	{
		case 'admin':
			$sql = 'SELECT ';
			if($extra['SELECT_ONLY'])
				$sql .= $extra['SELECT_ONLY'];
			else
			{
				if(Preferences('NAME')=='Common')
					$sql .= "CONCAT(s.LAST_NAME,', ',coalesce(s.COMMON_NAME,s.FIRST_NAME)) AS FULL_NAME,";
				else
					$sql .= "CONCAT(s.LAST_NAME,', ',s.FIRST_NAME,' ',COALESCE(s.MIDDLE_NAME,' ')) AS FULL_NAME,";
				$_SESSION['new_sql']=$sql;
                                $sql .='s.LAST_NAME,s.FIRST_NAME,s.MIDDLE_NAME,s.STUDENT_ID,s.PHONE,ssm.SCHOOL_ID,s.ALT_ID,ssm.SCHOOL_ID AS LIST_SCHOOL_ID,ssm.GRADE_ID'.$extra['SELECT'];
                                $_SESSION['new_sql'].='s.LAST_NAME,s.FIRST_NAME,s.MIDDLE_NAME,s.STUDENT_ID,s.PHONE,ssm.SCHOOL_ID,s.ALT_ID,ssm.SCHOOL_ID AS LIST_SCHOOL_ID,ssm.GRADE_ID'.$_SESSION['new_customsql'];
				if($_REQUEST['include_inactive']=='Y')
				$sql .= ','.db_case(array("(ssm.SYEAR='".UserSyear()."' AND ( (ssm.START_DATE IS NOT NULL AND '".date('Y-m-d',strtotime($extra['DATE']))."'>=ssm.START_DATE) AND('".date('Y-m-d',strtotime($extra['DATE']))."'<=ssm.END_DATE OR ssm.END_DATE IS NULL)))",'true',"'<FONT color=green>Active</FONT>'","'<FONT color=red>Inactive</FONT>'")).' AS ACTIVE ';
                                $_SESSION['new_sql'] .= ','.db_case(array("(ssm.SYEAR='".UserSyear()."' AND ( (ssm.START_DATE IS NOT NULL AND '".date('Y-m-d',strtotime($extra['DATE']))."'>=ssm.START_DATE) AND('".date('Y-m-d',strtotime($extra['DATE']))."'<=ssm.END_DATE OR ssm.END_DATE IS NULL)))",'true',"'<FONT color=green>Active</FONT>'","'<FONT color=red>Inactive</FONT>'")).' AS ACTIVE ';
			}

			$sql .= " FROM STUDENTS s ";
                        $_SESSION['new_sql'] .= " FROM STUDENTS s ";
			if($_REQUEST['mp_comment']){
			$sql .= ",STUDENT_MP_COMMENTS smc ";
                        $_SESSION['newsql'] .= ",STUDENT_MP_COMMENTS smc ";
			}
			if($_REQUEST['goal_title'] || $_REQUEST['goal_description']){
			$sql .= ",GOAL g ";
                        $_SESSION['newsql'] .= ",GOAL g ";
			}
			if($_REQUEST['progress_name'] || $_REQUEST['progress_description']){
			$sql .= ",PROGRESS p ";
                        $_SESSION['newsql'] .= ",PROGRESS p ";
			}
			if($_REQUEST['doctors_note_comments'] || $_REQUEST['med_day'] || $_REQUEST['med_month'] || $_REQUEST['med_year']){
			$sql .= ",STUDENT_MEDICAL_NOTES smn ";
                        $_SESSION['newsql'] .= ",STUDENT_MEDICAL_NOTES smn ";
			}
			if($_REQUEST['type']||$_REQUEST['imm_comments'] || $_REQUEST['imm_day']|| $_REQUEST['imm_month'] || $_REQUEST['imm_year']){
			$sql .= ",STUDENT_MEDICAL sm ";
                        $_SESSION['newsql'] .= ",STUDENT_MEDICAL sm ";
			}
			if($_REQUEST['med_alrt_title'] || $_REQUEST['ma_day'] || $_REQUEST['ma_month'] || $_REQUEST['ma_year']){
			$sql .= ",STUDENT_MEDICAL_ALERTS sma ";
                        $_SESSION['newsql'] .= ",STUDENT_MEDICAL_ALERTS sma ";
			}
if($_REQUEST['reason'] || $_REQUEST['result'] || $_REQUEST['med_vist_comments']||  $_REQUEST['nv_day'] || $_REQUEST['nv_month'] || $_REQUEST['nv_year']){
			$sql .= ",STUDENT_MEDICAL_VISITS smv ";
                        $_SESSION['newsql'] .= ",STUDENT_MEDICAL_VISITS smv ";
			}
                        $_SESSION['new_sql'].= $_SESSION['newsql'];
			$sql .=",STUDENT_ENROLLMENT ssm ";
                        $_SESSION['new_sql'].=",STUDENT_ENROLLMENT ssm ";
		$sql.=$extra['FROM']." WHERE ssm.STUDENT_ID=s.STUDENT_ID ";
                $_SESSION['new_sql'].=$extra['FROM']." WHERE ssm.STUDENT_ID=s.STUDENT_ID ";
			if($_REQUEST['include_inactive']=='Y')
                        {
				$sql .= " AND ssm.ID=(SELECT ID FROM STUDENT_ENROLLMENT WHERE STUDENT_ID=ssm.STUDENT_ID AND SYEAR ='".UserSyear()."' ORDER BY START_DATE DESC LIMIT 1)";
                                $_SESSION['new_sql'].= " AND ssm.ID=(SELECT ID FROM STUDENT_ENROLLMENT WHERE STUDENT_ID=ssm.STUDENT_ID AND SYEAR ='".UserSyear()."' ORDER BY START_DATE DESC LIMIT 1)";

                        }
                        else
                            {
				 $sql .= $_SESSION['inactive_stu_filter'] =" AND ssm.SYEAR='".UserSyear()."' AND ((ssm.START_DATE IS NOT NULL AND '".date('Y-m-d',strtotime($extra['DATE']))."'>=ssm.START_DATE) AND ('".date('Y-m-d',strtotime($extra['DATE']))."'<=ssm.END_DATE OR ssm.END_DATE IS NULL)) ";
                 //$sql .= " AND ssm.SYEAR='".UserSyear()."' AND ('".date('Y-m-d',strtotime($extra['DATE']))."'>=ssm.START_DATE AND ('".date('Y-m-d',strtotime($extra['DATE']))."'<=ssm.END_DATE OR ssm.END_DATE IS NULL)) ";
                                 $_SESSION['new_sql'].=" AND ssm.SYEAR='".UserSyear()."' AND ((ssm.START_DATE IS NOT NULL AND '".date('Y-m-d',strtotime($extra['DATE']))."'>=ssm.START_DATE) AND ('".date('Y-m-d',strtotime($extra['DATE']))."'<=ssm.END_DATE OR ssm.END_DATE IS NULL)) ";
                            }
                      if(UserSchool() && $_REQUEST['_search_all_schools']!='Y')
                      {
				$sql .= " AND ssm.SYEAR='".UserSyear()."' AND ssm.SCHOOL_ID='".UserSchool()."'";
                                $_SESSION['new_sql'].= " AND ssm.SYEAR='".UserSyear()."' AND ssm.SCHOOL_ID='".UserSchool()."'";
                      }
			else
			{
				if(User('SCHOOLS'))
                                {
					$sql .= " AND ssm.SCHOOL_ID IN (".substr(str_replace(',',"','",User('SCHOOLS')),2,-2).") ";
                                        $_SESSION['new_sql'].= " AND ssm.SCHOOL_ID IN (".substr(str_replace(',',"','",User('SCHOOLS')),2,-2).") ";
                                }
				$extra['columns_after']['LIST_SCHOOL_ID'] = 'School';
				$functions['LIST_SCHOOL_ID'] = 'GetSchool';
			}

			if(!$extra['SELECT_ONLY'] && $_REQUEST['include_inactive']=='Y')
				$extra['columns_after']['ACTIVE'] = 'Status';

		break;

		case 'teacher':
			$sql = 'SELECT ';
			if($extra['SELECT_ONLY'])

				$sql .= $extra['SELECT_ONLY'];

			else
			{
				if(Preferences('NAME')=='Common')
					$sql .= "CONCAT(s.LAST_NAME,', ',coalesce(s.COMMON_NAME,s.FIRST_NAME)) AS FULL_NAME,";
				else
					$sql .= "CONCAT(s.LAST_NAME,', ',s.FIRST_NAME,' ',COALESCE(s.MIDDLE_NAME,' ')) AS FULL_NAME,";
                                $_SESSION['new_sql']=$sql;
				$sql .='s.LAST_NAME,s.FIRST_NAME,s.MIDDLE_NAME,s.STUDENT_ID,s.PHONE,s.ALT_ID,ssm.SCHOOL_ID,ssm.GRADE_ID '.$extra['SELECT'];
				$_SESSION['new_sql'].='s.LAST_NAME,s.FIRST_NAME,s.MIDDLE_NAME,s.STUDENT_ID,s.PHONE,s.ALT_ID,ssm.SCHOOL_ID,ssm.GRADE_ID '.$_SESSION['new_customsql'];
                                if($_REQUEST['include_inactive']=='Y')
				{
					$sql .= ','.db_case(array("(ssm.START_DATE IS NOT NULL AND ('".$extra['DATE']."'<=ssm.END_DATE OR ssm.END_DATE IS NULL))",'true',"'<FONT color=green>Active</FONT>'","'<FONT color=red>Inactive</FONT>'")).' AS ACTIVE';
					$sql .= ','.db_case(array("(ssm.START_DATE IS NOT NULL AND ('".$extra['DATE']."'<=ss.END_DATE OR ss.END_DATE IS NULL))",'true',"'<FONT color=green>Active</FONT>'","'<FONT color=red>Inactive</FONT>'")).' AS ACTIVE_SCHEDULE';
                                        $_SESSION['new_sql'] .= ','.db_case(array("(ssm.START_DATE IS NOT NULL AND ('".$extra['DATE']."'<=ssm.END_DATE OR ssm.END_DATE IS NULL))",'true',"'<FONT color=green>Active</FONT>'","'<FONT color=red>Inactive</FONT>'")).' AS ACTIVE';
					$_SESSION['new_sql'] .= ','.db_case(array("(ssm.START_DATE IS NOT NULL AND ('".$extra['DATE']."'<=ss.END_DATE OR ss.END_DATE IS NULL))",'true',"'<FONT color=green>Active</FONT>'","'<FONT color=red>Inactive</FONT>'")).' AS ACTIVE_SCHEDULE';

                                }
			}

			$sql .= " FROM STUDENTS s,COURSE_PERIODS cp,SCHEDULE ss ";
                        $_SESSION['new_sql'] .= " FROM STUDENTS s,COURSE_PERIODS cp,SCHEDULE ss ";
			if($_REQUEST['mp_comment']){
			$sql .= ",STUDENT_MP_COMMENTS smc ";
                        $_SESSION['newsql'] .= ",STUDENT_MP_COMMENTS smc ";
			}
			if($_REQUEST['goal_title'] || $_REQUEST['goal_description']){
			$sql .= ",GOAL g ";
                        $_SESSION['newsql'] .= ",GOAL g ";
			}
			if($_REQUEST['progress_name'] || $_REQUEST['progress_description']){
			$sql .= ",PROGRESS p ";
                        $_SESSION['newsql'].= ",PROGRESS p ";
			}
			if($_REQUEST['doctors_note_comments'] || $_REQUEST['med_day'] || $_REQUEST['med_month'] || $_REQUEST['med_year']){
			$sql .= ",STUDENT_MEDICAL_NOTES smn ";
                        $_SESSION['newsql'].= ",STUDENT_MEDICAL_NOTES smn ";
			}
			if($_REQUEST['type']||$_REQUEST['imm_comments'] || $_REQUEST['imm_day']|| $_REQUEST['imm_month'] || $_REQUEST['imm_year']){
			$sql .= ",STUDENT_MEDICAL sm ";
                        $_SESSION['newsql'].= ",STUDENT_MEDICAL sm ";
			}
			if($_REQUEST['med_alrt_title'] || $_REQUEST['ma_day'] || $_REQUEST['ma_month'] || $_REQUEST['ma_year']){
			$sql .= ",STUDENT_MEDICAL_ALERTS sma ";
                        $_SESSION['newsql'].= ",STUDENT_MEDICAL_ALERTS sma ";
			}
if($_REQUEST['reason'] || $_REQUEST['result'] || $_REQUEST['med_vist_comments']||  $_REQUEST['nv_day'] || $_REQUEST['nv_month'] || $_REQUEST['nv_year']){
			$sql .= ",STUDENT_MEDICAL_VISITS smv ";
                        $_SESSION['newsql'].= ",STUDENT_MEDICAL_VISITS smv ";
			}
                        $_SESSION['new_sql'].= $_SESSION['newsql'];
			$sql.=" ,STUDENT_ENROLLMENT ssm ";
                        $_SESSION['new_sql'].=" ,STUDENT_ENROLLMENT ssm ";
			$sql.=$extra['FROM']." WHERE ssm.STUDENT_ID=s.STUDENT_ID AND ssm.STUDENT_ID=ss.STUDENT_ID
					AND ssm.SCHOOL_ID='".UserSchool()."' AND ssm.SYEAR='".UserSyear()."' AND ssm.SYEAR=cp.SYEAR AND ssm.SYEAR=ss.SYEAR
					AND ss.MARKING_PERIOD_ID IN (".GetAllMP('',$queryMP).")
					AND (cp.TEACHER_ID='".User('STAFF_ID')."' OR cp.SECONDARY_TEACHER_ID='".User('STAFF_ID')."') AND cp.COURSE_PERIOD_ID='".UserCoursePeriod()."'
					AND cp.COURSE_ID=ss.COURSE_ID AND cp.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID";
                        $_SESSION['new_sql'].=$extra['FROM']." WHERE ssm.STUDENT_ID=s.STUDENT_ID AND ssm.STUDENT_ID=ss.STUDENT_ID
					AND ssm.SCHOOL_ID='".UserSchool()."' AND ssm.SYEAR='".UserSyear()."' AND ssm.SYEAR=cp.SYEAR AND ssm.SYEAR=ss.SYEAR
					AND ss.MARKING_PERIOD_ID IN (".GetAllMP('',$queryMP).")
					AND (cp.TEACHER_ID='".User('STAFF_ID')."' OR cp.SECONDARY_TEACHER_ID='".User('STAFF_ID')."') AND cp.COURSE_PERIOD_ID='".UserCoursePeriod()."'
					AND cp.COURSE_ID=ss.COURSE_ID AND cp.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID";
			if($_REQUEST['include_inactive']=='Y')
			{
				$sql .= " AND ssm.ID=(SELECT ID FROM STUDENT_ENROLLMENT WHERE STUDENT_ID=ssm.STUDENT_ID AND SYEAR=ssm.SYEAR ORDER BY START_DATE DESC LIMIT 1)";
				$sql .= " AND ss.START_DATE=(SELECT START_DATE FROM SCHEDULE WHERE STUDENT_ID=ssm.STUDENT_ID AND SYEAR=ssm.SYEAR AND MARKING_PERIOD_ID IN (".GetAllMP('',$queryMP).") AND COURSE_ID=cp.COURSE_ID AND COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID ORDER BY START_DATE DESC LIMIT 1)";
                                $_SESSION['new_sql'].= " AND ssm.ID=(SELECT ID FROM STUDENT_ENROLLMENT WHERE STUDENT_ID=ssm.STUDENT_ID AND SYEAR=ssm.SYEAR ORDER BY START_DATE DESC LIMIT 1)";
				$_SESSION['new_sql'].= " AND ss.START_DATE=(SELECT START_DATE FROM SCHEDULE WHERE STUDENT_ID=ssm.STUDENT_ID AND SYEAR=ssm.SYEAR AND MARKING_PERIOD_ID IN (".GetAllMP('',$queryMP).") AND COURSE_ID=cp.COURSE_ID AND COURSE_PERIOD_ID=cp.COURSE_PERIOD_ID ORDER BY START_DATE DESC LIMIT 1)";

                        }
			else
			{
				$sql .= $_SESSION['inactive_stu_filter'] = " AND (ssm.START_DATE IS NOT NULL AND ('".$extra['DATE']."'<=ssm.END_DATE OR ssm.END_DATE IS NULL))";
				$sql .= $_SESSION['inactive_stu_filter'] =" AND (ssm.START_DATE IS NOT NULL AND ('".$extra['DATE']."'<=ss.END_DATE OR ss.END_DATE IS NULL))";
                                // $sql .= " AND ('".$extra['DATE']."'>=ssm.START_DATE AND ('".$extra['DATE']."'<=ssm.END_DATE OR ssm.END_DATE IS NULL))";
				//$sql .= " AND ('".$extra['DATE']."'>=ss.START_DATE AND ('".$extra['DATE']."'<=ss.END_DATE OR ss.END_DATE IS NULL))";
                                $_SESSION['new_sql'].= " AND (ssm.START_DATE IS NOT NULL AND ('".$extra['DATE']."'<=ssm.END_DATE OR ssm.END_DATE IS NULL))";
                                $_SESSION['new_sql'].=" AND (ssm.START_DATE IS NOT NULL AND ('".$extra['DATE']."'<=ss.END_DATE OR ss.END_DATE IS NULL))";
                        }

			if(!$extra['SELECT_ONLY'] && $_REQUEST['include_inactive']=='Y')
			{
				$extra['columns_after']['ACTIVE'] = 'School Status';
				$extra['columns_after']['ACTIVE_SCHEDULE'] = 'Course Status';
			}
		break;

		case 'parent':
		case 'student':
			$sql = 'SELECT ';
			if($extra['SELECT_ONLY'])
				$sql .= $extra['SELECT_ONLY'];
			else
			{
				if(Preferences('NAME')=='Common')
					$sql .= "CONCAT(s.LAST_NAME,', ',coalesce(s.COMMON_NAME,s.FIRST_NAME)) AS FULL_NAME,";
				else
					$sql .= "CONCAT(s.LAST_NAME,', ',s.FIRST_NAME,' ',COALESCE(s.MIDDLE_NAME,' ')) AS FULL_NAME,";
				$sql .='s.LAST_NAME,s.FIRST_NAME,s.MIDDLE_NAME,s.STUDENT_ID,s.ALT_ID,ssm.SCHOOL_ID,ssm.GRADE_ID '.$extra['SELECT'];
			}
			$sql .= " FROM STUDENTS s,STUDENT_ENROLLMENT ssm ".$extra['FROM']."
					WHERE ssm.STUDENT_ID=s.STUDENT_ID AND ssm.SYEAR='".UserSyear()."' AND ssm.SCHOOL_ID='".UserSchool()."' AND ('".DBDate()."' BETWEEN ssm.START_DATE AND ssm.END_DATE OR (ssm.END_DATE IS NULL AND '".DBDate()."'>ssm.START_DATE)) AND ssm.STUDENT_ID".($extra['ASSOCIATED']?" IN (SELECT STUDENT_ID FROM STUDENTS_JOIN_USERS WHERE STAFF_ID='".$extra['ASSOCIATED']."')":"='".UserStudentID()."'");
		break;
		default:
			exit('Error');
	}
        if($expanded_view==true)
        {
            $custom_str=CustomFields('where','',1);
            if($custom_str!='')
                $_SESSION['custom_count_sql']=$custom_str;

            $sql .= $custom_str;
        }
        elseif($expanded_view==false)
        {
            $custom_str=CustomFields('where','',2);
            if($custom_str!='')
                $_SESSION['custom_count_sql']=$custom_str;

            $sql .= $custom_str;
        }
        else {
            $custom_str = CustomFields('where');
            if($custom_str!='')
                $_SESSION['custom_count_sql']=$custom_str;

             $sql .= $custom_str;
        }

        $sql .= $extra['WHERE'].' ';
	$sql = appendSQL_Absence_Summary($sql,$extra);

//        TODO               Modification Required
//        if($_SESSION['stu_search']['sql'] && $_REQUEST['return_session'] && $extra['SELECT']!='' && strpos($sql,'ADDRESS a')==0)
//        {
//            $sql = str_replace("FROM", $extra['SELECT']." FROM",$sql);
//        }
//
//        if($_SESSION['stu_search']['sql'] && $_REQUEST['return_session'] && $extra['FROM']!='' && strpos($sql,'ADDRESS a')==0)
//        {
//            $sql = str_replace("WHERE",$extra['FROM']." WHERE",$sql);
//
//        }
//        --------------------------------------------------

	if($extra['GROUP'])
		$sql .= ' GROUP BY '.$extra['GROUP'];

	if(!$extra['ORDER_BY'] && !$extra['SELECT_ONLY'])
	{
		if(Preferences('SORT')=='Grade')
			$sql .= " ORDER BY (SELECT SORT_ORDER FROM SCHOOL_GRADELEVELS WHERE ID=ssm.GRADE_ID),FULL_NAME";
		else
			$sql .= " ORDER BY FULL_NAME";
		$sql .= $extra['ORDER'];
	}
	elseif($extra['ORDER_BY'] && !($_SESSION['stu_search']['sql'] && $_REQUEST['return_session']))
		$sql .= ' ORDER BY '.$extra['ORDER_BY'];

	if($extra['DEBUG']===true)
		echo '<!--'.$sql.'-->';
	$return = DBGet(DBQuery($sql),$functions,$extra['group']);
                  $_SESSION['count_stu'] =  count($return);
                  return $return;
}
function appendSQL_Absence_Summary($sql,& $extra)
{	global $_openSIS;
	if($_REQUEST['stuid'])
	{
		$sql .= " AND ssm.STUDENT_ID = '".str_replace("'","\'",$_REQUEST[stuid])."' ";
                $_SESSION['newsql1'].= " AND ssm.STUDENT_ID = '".str_replace("'","\'",$_REQUEST[stuid])."' ";
		if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Student ID: </b></font>'.$_REQUEST['stuid'].'<BR>';
	}
         if($_REQUEST['altid'])
	{
		//$sql .= " AND s.ALT_ID = '$_REQUEST[altid]' ";
		$sql .= " AND LOWER(s.ALT_ID) LIKE '".str_replace("'","\'",strtolower(trim($_REQUEST['altid'])))."%' ";
                $_SESSION['newsql1'].= " AND LOWER(s.ALT_ID) LIKE '".str_replace("'","\'",strtolower(trim($_REQUEST['altid'])))."%' ";
		if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Student ID: </b></font>'.$_REQUEST['stuid'].'<BR>';
	}
	if($_REQUEST['last'])
	{
		$sql .= " AND LOWER(s.LAST_NAME) LIKE '".str_replace("'","\'",strtolower(trim($_REQUEST['last'])))."%' ";
                $_SESSION['newsql1'].= " AND LOWER(s.LAST_NAME) LIKE '".str_replace("'","\'",strtolower(trim($_REQUEST['last'])))."%' ";
		if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Last Name starts with: </b></font>'.trim($_REQUEST['last']).'<BR>';
	}
	if($_REQUEST['first'])
	{
		$sql .= " AND LOWER(s.FIRST_NAME) LIKE '".str_replace("'","\'",strtolower(trim($_REQUEST['first'])))."%' ";
                $_SESSION['newsql1'].= " AND LOWER(s.FIRST_NAME) LIKE '".str_replace("'","\'",strtolower(trim($_REQUEST['first'])))."%' ";
		if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>First Name starts with: </b></font>'.trim($_REQUEST['first']).'<BR>';
	}
	if($_REQUEST['grade'])
	{
		$sql .= " AND ssm.GRADE_ID = '".str_replace("'","\'",$_REQUEST[grade])."' ";
                $_SESSION['newsql1'].= " AND ssm.GRADE_ID = '".str_replace("'","\'",$_REQUEST[grade])."' ";
		if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Grade: </b></font>'.GetGrade($_REQUEST['grade']).'<BR>';
	}
	if($_REQUEST['addr'])
	{
		$sql .= " AND (LOWER(a.ADDRESS) LIKE '%".str_replace("'","\'",strtolower(trim($_REQUEST['addr'])))."%' OR LOWER(a.CITY) LIKE '".str_replace("'","\'",strtolower(trim($_REQUEST['addr'])))."%' OR LOWER(a.STATE)='".str_replace("'","\'",strtolower(trim($_REQUEST['addr'])))."' OR ZIPCODE LIKE '".trim(str_replace("'","\'",$_REQUEST['addr']))."%')";
                $_SESSION['newsql1'].= " AND (LOWER(a.ADDRESS) LIKE '%".str_replace("'","\'",strtolower(trim($_REQUEST['addr'])))."%' OR LOWER(a.CITY) LIKE '".str_replace("'","\'",strtolower(trim($_REQUEST['addr'])))."%' OR LOWER(a.STATE)='".str_replace("'","\'",strtolower(trim($_REQUEST['addr'])))."' OR ZIPCODE LIKE '".trim(str_replace("'","\'",$_REQUEST['addr']))."%')";
		if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Address contains: </b></font>'.trim($_REQUEST['addr']).'<BR>';
	}
	if($_REQUEST['preferred_hospital'])
	{
		$sql .= " AND LOWER(s.PREFERRED_HOSPITAL) LIKE '".str_replace("'","\'",strtolower($_REQUEST['preferred_hospital']))."%' ";
                $_SESSION['newsql1'].= " AND LOWER(s.PREFERRED_HOSPITAL) LIKE '".str_replace("'","\'",strtolower($_REQUEST['preferred_hospital']))."%' ";
		if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Preferred Medical Facility starts with: </b></font>'.$_REQUEST['preferred_hospital'].'<BR>';
	}
	if($_REQUEST['mp_comment'])
	{
		$sql .= " AND LOWER(smc.COMMENT) LIKE '".str_replace("'","\'",strtolower($_REQUEST['mp_comment']))."%' AND s.STUDENT_ID=smc.STUDENT_ID ";
		$_SESSION['newsql1'].= " AND LOWER(smc.COMMENT) LIKE '".str_replace("'","\'",strtolower($_REQUEST['mp_comment']))."%' AND s.STUDENT_ID=smc.STUDENT_ID ";
                if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Comments starts with: </b></font>'.$_REQUEST['mp_comment'].'<BR>';
	}
	if($_REQUEST['goal_title'])
	{
		$sql .= " AND LOWER(g.GOAL_TITLE) LIKE '".str_replace("'","\'",strtolower($_REQUEST['goal_title']))."%' AND s.STUDENT_ID=g.STUDENT_ID ";
		$_SESSION['newsql1'].= " AND LOWER(g.GOAL_TITLE) LIKE '".str_replace("'","\'",strtolower($_REQUEST['goal_title']))."%' AND s.STUDENT_ID=g.STUDENT_ID ";
                if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Goal Title starts with: </b></font>'.$_REQUEST['goal_title'].'<BR>';
	}
		if($_REQUEST['goal_description'])
	{
		$sql .= " AND LOWER(g.GOAL_DESCRIPTION) LIKE '".str_replace("'","\'",strtolower($_REQUEST['goal_description']))."%' AND s.STUDENT_ID=g.STUDENT_ID ";
                $_SESSION['newsql1'].= " AND LOWER(g.GOAL_DESCRIPTION) LIKE '".str_replace("'","\'",strtolower($_REQUEST['goal_description']))."%' AND s.STUDENT_ID=g.STUDENT_ID ";
		if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Goal Description starts with: </b></font>'.$_REQUEST['goal_description'].'<BR>';
	}
		if($_REQUEST['progress_name'])
	{
		$sql .= " AND LOWER(p.PROGRESS_NAME) LIKE '".str_replace("'","\'",strtolower($_REQUEST['progress_name']))."%' AND s.STUDENT_ID=p.STUDENT_ID ";
		$_SESSION['newsql1'].= " AND LOWER(p.PROGRESS_NAME) LIKE '".str_replace("'","\'",strtolower($_REQUEST['progress_name']))."%' AND s.STUDENT_ID=p.STUDENT_ID ";
                if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Progress Period Name starts with: </b></font>'.$_REQUEST['progress_name'].'<BR>';
	}
	if($_REQUEST['progress_description'])
	{
		$sql .= " AND LOWER(p.PROGRESS_DESCRIPTION) LIKE '".str_replace("'","\'",strtolower($_REQUEST['progress_description']))."%' AND s.STUDENT_ID=p.STUDENT_ID ";
		$_SESSION['newsql1'].= " AND LOWER(p.PROGRESS_DESCRIPTION) LIKE '".str_replace("'","\'",strtolower($_REQUEST['progress_description']))."%' AND s.STUDENT_ID=p.STUDENT_ID ";
                if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Progress Assessment starts with: </b></font>'.$_REQUEST['progress_description'].'<BR>';
	}
	if($_REQUEST['doctors_note_comments'])
	{
		$sql .= " AND LOWER(smn.DOCTORS_NOTE_COMMENTS) LIKE '".str_replace("'","\'",strtolower($_REQUEST['doctors_note_comments']))."%' AND s.STUDENT_ID=smn.STUDENT_ID ";
		$_SESSION['newsql1'].= " AND LOWER(smn.DOCTORS_NOTE_COMMENTS) LIKE '".str_replace("'","\'",strtolower($_REQUEST['doctors_note_comments']))."%' AND s.STUDENT_ID=smn.STUDENT_ID ";
                if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Doctor\'s Note starts with: </b></font>'.$_REQUEST['doctors_note_comments'].'<BR>';
	}
	if($_REQUEST['type'])
	{
		$sql .= " AND LOWER(sm.TYPE) LIKE '".str_replace("'","\'",strtolower($_REQUEST['type']))."%' AND s.STUDENT_ID=sm.STUDENT_ID ";
		$_SESSION['newsql1'].= " AND LOWER(sm.TYPE) LIKE '".str_replace("'","\'",strtolower($_REQUEST['type']))."%' AND s.STUDENT_ID=sm.STUDENT_ID ";
                if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Type starts with: </b></font>'.$_REQUEST['type'].'<BR>';
	}
	if($_REQUEST['imm_comments'])
	{
		$sql .= " AND LOWER(sm.COMMENTS) LIKE '".str_replace("'","\'",strtolower($_REQUEST['imm_comments']))."%' AND s.STUDENT_ID=sm.STUDENT_ID ";
		$_SESSION['newsql1'].= " AND LOWER(sm.COMMENTS) LIKE '".str_replace("'","\'",strtolower($_REQUEST['imm_comments']))."%' AND s.STUDENT_ID=sm.STUDENT_ID ";
                if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Comments starts with: </b></font>'.$_REQUEST['imm_comments'].'<BR>';
	}
	if($_REQUEST['imm_day']&& $_REQUEST['imm_month']&& $_REQUEST['imm_year'])
	{
                $imm_date=$_REQUEST['imm_year'].'-'.$_REQUEST['imm_month'].'-'.$_REQUEST['imm_day'];
		$sql .= " AND sm.MEDICAL_DATE ='".date('Y-m-d',strtotime($imm_date))."' AND s.STUDENT_ID=sm.STUDENT_ID ";
		$_SESSION['newsql1'].= " AND sm.MEDICAL_DATE ='".date('Y-m-d',strtotime($imm_date))."' AND s.STUDENT_ID=sm.STUDENT_ID ";
                if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Immunization Date: </b></font>'.$imm_date.'<BR>';
	}elseif($_REQUEST['imm_day'] || $_REQUEST['imm_month'] || $_REQUEST['imm_year']){
	if($_REQUEST['imm_day']){
	$sql .= " AND SUBSTR(sm.MEDICAL_DATE,9,2) ='".$_REQUEST['imm_day']."' AND s.STUDENT_ID=sm.STUDENT_ID ";
        $_SESSION['newsql1'].= " AND SUBSTR(sm.MEDICAL_DATE,9,2) ='".$_REQUEST['imm_day']."' AND s.STUDENT_ID=sm.STUDENT_ID ";
	$imm_date.=" Day :".$_REQUEST['imm_day'];
	}
	if($_REQUEST['imm_month']){
	$sql .= " AND SUBSTR(sm.MEDICAL_DATE,6,2) ='".$_REQUEST['imm_month']."' AND s.STUDENT_ID=sm.STUDENT_ID ";
        $_SESSION['newsql1'].= " AND SUBSTR(sm.MEDICAL_DATE,6,2) ='".$_REQUEST['imm_month']."' AND s.STUDENT_ID=sm.STUDENT_ID ";
	$imm_date.=" Month :".$_REQUEST['imm_month'];
	}
	if($_REQUEST['imm_year']){
	$sql .= " AND SUBSTR(sm.MEDICAL_DATE,1,4) ='".$_REQUEST['imm_year']."' AND s.STUDENT_ID=sm.STUDENT_ID ";
        $_SESSION['newsql1'].= " AND SUBSTR(sm.MEDICAL_DATE,1,4) ='".$_REQUEST['imm_year']."' AND s.STUDENT_ID=sm.STUDENT_ID ";
	$imm_date.=" Year :".$_REQUEST['imm_year'];
	}
	if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Immunization Date: </b></font>'.$imm_date.'<BR>';
	}
	if($_REQUEST['med_day']&&$_REQUEST['med_month']&&$_REQUEST['med_year'])
	{
$med_date=$_REQUEST['med_year'].'-'.$_REQUEST['med_month'].'-'.$_REQUEST['med_day'];
		$sql .= " AND smn.DOCTORS_NOTE_DATE ='".date('Y-m-d',strtotime($med_date))."' AND s.STUDENT_ID=smn.STUDENT_ID ";
		$_SESSION['newsql1'].= " AND smn.DOCTORS_NOTE_DATE ='".date('Y-m-d',strtotime($med_date))."' AND s.STUDENT_ID=smn.STUDENT_ID ";
                if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Medical Date: </b></font>'.$med_date.'<BR>';
	}elseif($_REQUEST['med_day'] || $_REQUEST['med_month'] || $_REQUEST['med_year']){
	if($_REQUEST['med_day']){
	$sql .= " AND SUBSTR(smn.DOCTORS_NOTE_DATE,9,2) ='".$_REQUEST['med_day']."' AND s.STUDENT_ID=smn.STUDENT_ID ";
	$_SESSION['newsql1'].= " AND SUBSTR(smn.DOCTORS_NOTE_DATE,9,2) ='".$_REQUEST['med_day']."' AND s.STUDENT_ID=smn.STUDENT_ID ";
        $med_date.=" Day :".$_REQUEST['med_day'];
	}
	if($_REQUEST['med_month']){
	$sql .= " AND SUBSTR(smn.DOCTORS_NOTE_DATE,6,2) ='".$_REQUEST['med_month']."' AND s.STUDENT_ID=smn.STUDENT_ID ";
	$_SESSION['newsql1'].= " AND SUBSTR(smn.DOCTORS_NOTE_DATE,6,2) ='".$_REQUEST['med_month']."' AND s.STUDENT_ID=smn.STUDENT_ID ";
        $med_date.=" Month :".$_REQUEST['med_month'];
	}
	if($_REQUEST['med_year']){
	$sql .= " AND SUBSTR(smn.DOCTORS_NOTE_DATE,1,4) ='".$_REQUEST['med_year']."' AND s.STUDENT_ID=smn.STUDENT_ID ";
	$_SESSION['newsql1'].= " AND SUBSTR(smn.DOCTORS_NOTE_DATE,1,4) ='".$_REQUEST['med_year']."' AND s.STUDENT_ID=smn.STUDENT_ID ";
        $med_date.=" Year :".$_REQUEST['med_year'];
	}
	if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Medical Date: </b></font>'.$med_date.'<BR>';
	}
	if($_REQUEST['ma_day']&&$_REQUEST['ma_month']&&$_REQUEST['ma_year'])
	{
$ma_date=$_REQUEST['ma_year'].'-'.$_REQUEST['ma_month'].'-'.$_REQUEST['ma_day'];
		$sql .= " AND sma.ALERT_DATE ='".date('Y-m-d',strtotime($ma_date))."' AND s.STUDENT_ID=sma.STUDENT_ID ";
		$_SESSION['newsql1'].= " AND sma.ALERT_DATE ='".date('Y-m-d',strtotime($ma_date))."' AND s.STUDENT_ID=sma.STUDENT_ID ";
                if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Medical Alert Date: </b></font>'.$ma_date.'<BR>';
	}elseif($_REQUEST['ma_day'] || $_REQUEST['ma_month'] || $_REQUEST['ma_year']){
	if($_REQUEST['ma_day']){
	$sql .= " AND SUBSTR(sma.ALERT_DATE,9,2) ='".$_REQUEST['ma_day']."' AND s.STUDENT_ID=sma.STUDENT_ID ";
	$_SESSION['newsql1'].= " AND SUBSTR(sma.ALERT_DATE,9,2) ='".$_REQUEST['ma_day']."' AND s.STUDENT_ID=sma.STUDENT_ID ";
        $ma_date.=" Day :".$_REQUEST['ma_day'];
	}
	if($_REQUEST['ma_month']){
	$sql .= " AND SUBSTR(sma.ALERT_DATE,6,2) ='".$_REQUEST['ma_month']."' AND s.STUDENT_ID=sma.STUDENT_ID ";
	$_SESSION['newsql1'].= " AND SUBSTR(sma.ALERT_DATE,6,2) ='".$_REQUEST['ma_month']."' AND s.STUDENT_ID=sma.STUDENT_ID ";
        $ma_date.=" Month :".$_REQUEST['ma_month'];
	}
	if($_REQUEST['ma_year']){
	$sql .= " AND SUBSTR(sma.ALERT_DATE,1,4) ='".$_REQUEST['ma_year']."' AND s.STUDENT_ID=sma.STUDENT_ID ";
	$_SESSION['newsql1'].= " AND SUBSTR(sma.ALERT_DATE,1,4) ='".$_REQUEST['ma_year']."' AND s.STUDENT_ID=sma.STUDENT_ID ";
        $ma_date.=" Year :".$_REQUEST['ma_year'];
	}
	if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Medical Alert Date: </b></font>'.$ma_date.'<BR>';
	}
	if($_REQUEST['nv_day']&&$_REQUEST['nv_month']&&$_REQUEST['nv_year'])
	{
$nv_date=$_REQUEST['nv_year'].'-'.$_REQUEST['nv_month'].'-'.$_REQUEST['nv_day'];
		$sql .= " AND smv.SCHOOL_DATE ='".date('Y-m-d',strtotime($nv_date))."' AND s.STUDENT_ID=smv.STUDENT_ID ";
		$_SESSION['newsql1'].= " AND smv.SCHOOL_DATE ='".date('Y-m-d',strtotime($nv_date))."' AND s.STUDENT_ID=smv.STUDENT_ID ";
                if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Nurse Visit Date: </b></font>'.$nv_date.'<BR>';
	}elseif($_REQUEST['nv_day'] || $_REQUEST['nv_month'] || $_REQUEST['nv_year']){
	if($_REQUEST['nv_day']){
	$sql .= " AND SUBSTR(smv.SCHOOL_DATE,9,2) ='".$_REQUEST['nv_day']."' AND s.STUDENT_ID=smv.STUDENT_ID ";
	$_SESSION['newsql1'].= " AND SUBSTR(smv.SCHOOL_DATE,9,2) ='".$_REQUEST['nv_day']."' AND s.STUDENT_ID=smv.STUDENT_ID ";
        $nv_date.=" Day :".$_REQUEST['nv_day'];
	}
	if($_REQUEST['nv_month']){
	$sql .= " AND SUBSTR(smv.SCHOOL_DATE,6,2) ='".$_REQUEST['nv_month']."' AND s.STUDENT_ID=smv.STUDENT_ID ";
	$_SESSION['newsql1'].= " AND SUBSTR(smv.SCHOOL_DATE,6,2) ='".$_REQUEST['nv_month']."' AND s.STUDENT_ID=smv.STUDENT_ID ";
        $nv_date.=" Month :".$_REQUEST['nv_month'];
	}
	if($_REQUEST['nv_year']){
	$sql .= " AND SUBSTR(smv.SCHOOL_DATE,1,4) ='".$_REQUEST['nv_year']."' AND s.STUDENT_ID=smv.STUDENT_ID ";
	$_SESSION['newsql1'].= " AND SUBSTR(smv.SCHOOL_DATE,1,4) ='".$_REQUEST['nv_year']."' AND s.STUDENT_ID=smv.STUDENT_ID ";
        $nv_date.=" Year :".$_REQUEST['nv_year'];
	}
	if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Nurse Visit Date: </b></font>'.$nv_date.'<BR>';
	}


	if($_REQUEST['med_alrt_title'])
	{
		$sql .= " AND LOWER(sma.TITLE) LIKE '".str_replace("'","\'",strtolower($_REQUEST['med_alrt_title']))."%' AND s.STUDENT_ID=sma.STUDENT_ID ";
		$_SESSION['newsql1'].= " AND LOWER(sma.TITLE) LIKE '".str_replace("'","\'",strtolower($_REQUEST['med_alrt_title']))."%' AND s.STUDENT_ID=sma.STUDENT_ID ";
                if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Alert starts with: </b></font>'.$_REQUEST['med_alrt_title'].'<BR>';
	}
	if($_REQUEST['reason'])
	{
		$sql .= " AND LOWER(smv.REASON) LIKE '".str_replace("'","\'",strtolower($_REQUEST['reason']))."%' AND s.STUDENT_ID=smv.STUDENT_ID ";
		if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Reason starts with: </b></font>'.$_REQUEST['reason'].'<BR>';
	}
	if($_REQUEST['result'])
	{
		$sql .= " AND LOWER(smv.RESULT) LIKE '".str_replace("'","\'",strtolower($_REQUEST['result']))."%' AND s.STUDENT_ID=smv.STUDENT_ID ";
		$_SESSION['newsql1'] .= " AND LOWER(smv.RESULT) LIKE '".str_replace("'","\'",strtolower($_REQUEST['result']))."%' AND s.STUDENT_ID=smv.STUDENT_ID ";
                if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Result starts with: </b></font>'.$_REQUEST['result'].'<BR>';
	}
	if($_REQUEST['med_vist_comments'])
	{
		$sql .= " AND LOWER(smv.COMMENTS) LIKE '".str_replace("'","\'",strtolower($_REQUEST['med_vist_comments']))."%' AND s.STUDENT_ID=smv.STUDENT_ID ";
		$_SESSION['newsql1'].= " AND LOWER(smv.COMMENTS) LIKE '".str_replace("'","\'",strtolower($_REQUEST['med_vist_comments']))."%' AND s.STUDENT_ID=smv.STUDENT_ID ";
                if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Nurse Visit Comments starts with: </b></font>'.$_REQUEST['med_vist_comments'].'<BR>';
	}
	if($_REQUEST['day_to_birthdate']&&$_REQUEST['month_to_birthdate']&&$_REQUEST['day_from_birthdate']&&$_REQUEST['month_from_birthdate'])
	{
	$date_to=$_REQUEST['month_to_birthdate'].'-'.$_REQUEST['day_to_birthdate'];
	$date_from=$_REQUEST['month_from_birthdate'].'-'.$_REQUEST['day_from_birthdate'];
		$sql .= " AND (SUBSTR(s.BIRTHDATE,6,2) BETWEEN ".$_REQUEST['month_from_birthdate']." AND ".$_REQUEST['month_to_birthdate'].") ";
		$sql .= " AND (SUBSTR(s.BIRTHDATE,9,2) BETWEEN ".$_REQUEST['day_from_birthdate']." AND ".$_REQUEST['day_to_birthdate'].") ";
		$_SESSION['newsql1'].= " AND (SUBSTR(s.BIRTHDATE,6,2) BETWEEN ".$_REQUEST['month_from_birthdate']." AND ".$_REQUEST['month_to_birthdate'].") ";
                $_SESSION['newsql1'].= " AND (SUBSTR(s.BIRTHDATE,9,2) BETWEEN ".$_REQUEST['day_from_birthdate']." AND ".$_REQUEST['day_to_birthdate'].") ";
                if(!$extra['NoSearchTerms'])
			$_openSIS['SearchTerms'] .= '<font color=gray><b>Birthday Starts from '.$date_from.' to '.$date_to.'</b></font>';
	}
	// test cases start



	// test cases end
	if($_SESSION['stu_search']['sql'] && $_REQUEST['return_session']){
            if(($_REQUEST['absence_go'] || $_REQUEST['chk']) && (User('PROFILE')=='teacher' || User('PROFILE')=='admin') && $_REQUEST['return_session'])
            {
             $new_sql=$_SESSION['new_sql'].$_SESSION['newsql1'];
             unset($_SESSION['inactive_stu_filter']);
             return $new_sql;
            }
            else
            {
            unset($_SESSION['inactive_stu_filter']);
            return $_SESSION['stu_search']['sql'];
            }
	}else{
            if($_REQUEST['sql_save_session'] && !$_SESSION['stu_search']['search_from_grade']){
                $_SESSION['stu_search']['sql']=$sql;
            }else if($_SESSION['stu_search']['search_from_grade']){
                unset($_SESSION['stu_search']['search_from_grade']);
            }
	return $sql;
	}
}

?>