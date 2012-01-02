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
function Widgets($item,$allow_widget=false)
{	global $extra,$_openSIS;

	if(!is_array($extra['functions']))
		$extra['functions'] = array();

	if(User('PROFILE')=='admin' || User('PROFILE')=='teacher' || $allow_widget)
	{
		switch($item)
		{
			case 'course':
				if(User('PROFILE')=='admin' || $allow_widget)
				{
				if($_REQUEST['w_course_period_id'])
				{
					if($_REQUEST['w_course_period_id_which']=='course')
					{
						$course = DBGet(DBQuery("SELECT c.TITLE AS COURSE_TITLE,cp.TITLE,cp.COURSE_ID FROM COURSE_PERIODS cp,COURSES c WHERE c.COURSE_ID=cp.COURSE_ID AND cp.COURSE_PERIOD_ID='".$_REQUEST['w_course_period_id']."'"));
						$extra['FROM'] .= ",SCHEDULE w_ss";
						$extra['WHERE'] .= " AND w_ss.STUDENT_ID=s.STUDENT_ID AND w_ss.SYEAR=ssm.SYEAR AND w_ss.SCHOOL_ID=ssm.SCHOOL_ID AND w_ss.COURSE_ID='".$course[1]['COURSE_ID']."' AND ('".DBDate()."' BETWEEN w_ss.START_DATE AND w_ss.END_DATE OR w_ss.END_DATE IS NULL)";
						$_openSIS['SearchTerms'] .= '<font color=gray><b>Course: </b></font>'.$course[1]['COURSE_TITLE'].'<BR>';
					}
					else
					{
						$extra['FROM'] .= ",SCHEDULE w_ss";
						$extra['WHERE'] .= " AND w_ss.STUDENT_ID=s.STUDENT_ID AND w_ss.SYEAR=ssm.SYEAR AND w_ss.SCHOOL_ID=ssm.SCHOOL_ID AND w_ss.COURSE_PERIOD_ID='".$_REQUEST['w_course_period_id']."' AND ('".DBDate()."' BETWEEN w_ss.START_DATE AND w_ss.END_DATE OR w_ss.END_DATE IS NULL)";
						$course = DBGet(DBQuery("SELECT c.TITLE AS COURSE_TITLE,cp.TITLE,cp.COURSE_ID FROM COURSE_PERIODS cp,COURSES c WHERE c.COURSE_ID=cp.COURSE_ID AND cp.COURSE_PERIOD_ID='".$_REQUEST['w_course_period_id']."'"));
						$_openSIS['SearchTerms'] .= '<font color=gray><b>Course Period: </b></font>'.$course[1]['COURSE_TITLE'].': '.$course[1]['TITLE'].'<BR>';
					}
				}
			#	$extra['search'] .= "<TR><TD align=right width=120>Course</TD><TD><DIV id=course_div></DIV> <A HREF=# onclick='window.open(\"Modules.php?modname=misc/ChooseCourse.php\",\"\",\"scrollbars=yes,resizable=yes,width=800,height=400\");'><SMALL>Choose</SMALL></A></TD></TR>";
				$extra['search'] .= "<TR><TD align=right width=120>Course</TD><TD><DIV id=course_div></DIV> <A HREF=# onclick='window.open(\"for_window.php?modname=misc/ChooseCourse.php\",\"\",\"scrollbars=yes,resizable=yes,width=800,height=400\");'><SMALL>Choose</SMALL></A></TD></TR>";
				}
			break;

			case 'request':
				if(User('PROFILE')=='admin' || $allow_widget)
				{
				// PART OF THIS IS DUPLICATED IN PrintRequests.php
				if($_REQUEST['request_course_id'])// && $_REQUEST['request_course_weight'])
				{
					$course = DBGet(DBQuery("SELECT c.TITLE FROM COURSES c WHERE c.COURSE_ID='".$_REQUEST['request_course_id']."'"));
					if(!$_REQUEST['not_request_course'])
					{
						$extra['FROM'] .= ",SCHEDULE_REQUESTS sr";
						$extra['WHERE'] .= " AND sr.STUDENT_ID=s.STUDENT_ID AND sr.SYEAR=ssm.SYEAR AND sr.SCHOOL_ID=ssm.SCHOOL_ID AND sr.COURSE_ID='".$_REQUEST['request_course_id']."'";
                        //"' AND sr.COURSE_WEIGHT='".$_REQUEST['request_course_weight']."'";
						$_openSIS['SearchTerms'] .= '<font color=gray><b>Request: </b></font>'.$course[1]['TITLE'].'<BR>';
                        //.' - '.$_REQUEST['request_course_weight'].'<BR>';
					}
					else
					{
						$extra['WHERE'] .= " AND NOT EXISTS (SELECT '' FROM SCHEDULE_REQUESTS sr WHERE sr.STUDENT_ID=ssm.STUDENT_ID AND sr.SYEAR=ssm.SYEAR AND sr.COURSE_ID='".$_REQUEST['request_course_id']."') ";
                        //."' AND sr.COURSE_WEIGHT='".$_REQUEST['request_course_weight']."') ";
						$_openSIS['SearchTerms'] .= '<font color=gray><b>Missing Request: </b></font>'.$course[1]['TITLE'].'<BR>';
                        //.' - '.$_REQUEST['request_course_weight'].'<BR>';
					}
				}
		#		$extra['search'] .= "<TR><TD align=right width=120>Request</TD><TD><DIV id=request_div></DIV> <A HREF=# onclick='window.open(\"Modules.php?modname=misc/ChooseRequest.php\",\"\",\"scrollbars=yes,resizable=yes,width=800,height=400\");'><SMALL>Choose</SMALL></A></TD></TR>";
				$extra['search'] .= "<TR><TD align=right width=120>Request</TD><TD><DIV id=request_div></DIV> <A HREF=# onclick='window.open(\"for_window.php?modname=misc/ChooseRequest.php\",\"\",\"scrollbars=yes,resizable=yes,width=800,height=400\");'><SMALL>Choose</SMALL></A></TD></TR>";
				}
			break;

			case 'absences':
				if(is_numeric($_REQUEST['absences_low']) && is_numeric($_REQUEST['absences_high']))
				{
					if($_REQUEST['absences_low'] > $_REQUEST['absences_high'])
					{
						$temp = $_REQUEST['absences_high'];
						$_REQUEST['absences_high'] = $_REQUEST['absences_low'];
						$_REQUEST['absences_low'] = $temp;
					}

					if($_REQUEST['absences_low']==$_REQUEST['absences_high'])
						$extra['WHERE'] .= " AND (SELECT sum(1-STATE_VALUE) AS STATE_VALUE FROM ATTENDANCE_DAY ad WHERE ssm.STUDENT_ID=ad.STUDENT_ID AND ad.SYEAR=ssm.SYEAR AND ad.MARKING_PERIOD_ID IN (".GetChildrenMP($_REQUEST['absences_term'],UserMP()).")) = '$_REQUEST[absences_low]'";
					else
						$extra['WHERE'] .= " AND (SELECT sum(1-STATE_VALUE) AS STATE_VALUE FROM ATTENDANCE_DAY ad WHERE ssm.STUDENT_ID=ad.STUDENT_ID AND ad.SYEAR=ssm.SYEAR AND ad.MARKING_PERIOD_ID IN (".GetChildrenMP($_REQUEST['absences_term'],UserMP()).")) BETWEEN '$_REQUEST[absences_low]' AND '$_REQUEST[absences_high]'";
					switch($_REQUEST['absences_term'])
					{
						case 'FY':
							$term = 'this school year to date';
						break;
						case 'SEM':
							$term = 'this semester to date';
						break;
						case 'QTR':
							$term = 'this marking period to date';
						break;
					}
					$_openSIS['SearchTerms'] .= '<font color=gray><b>Days Absent '.$term.' between: </b></font>'.$_REQUEST['absences_low'].' &amp; '.$_REQUEST['absences_high'].'<BR>';
				}
				$extra['search'] .= "<TR><TD align=right width=120>Days Absent<BR><INPUT type=radio name=absences_term value=FY checked>YTD<INPUT type=radio name=absences_term value=SEM>".GetMP(GetParentMP('SEM',UserMP()),'SHORT_NAME')."<INPUT type=radio name=absences_term value=QTR>".GetMP(UserMP(),'SHORT_NAME')."</TD><TD>Between <INPUT type=text name=absences_low size=3 class=cell_small maxlength=5> &amp; <INPUT type=text name=absences_high size=3 maxlength=5 class=cell_small></TD></TR>";
			break;

			case 'gpa':
				if(is_numeric($_REQUEST['gpa_low']) && is_numeric($_REQUEST['gpa_high']))
				{
					if($_REQUEST['gpa_low'] > $_REQUEST['gpa_high'])
					{
						$temp = $_REQUEST['gpa_high'];
						$_REQUEST['gpa_high'] = $_REQUEST['gpa_low'];
						$_REQUEST['gpa_low'] = $temp;
					}
					if($_REQUEST['list_gpa'])
					{
						$extra['SELECT'] .= ',sgc.WEIGHTED_GPA,sgc.UNWEIGHTED_GPA';
						$extra['columns_after']['WEIGHTED_GPA'] = 'Weighted GPA';
						$extra['columns_after']['UNWEIGHTED_GPA'] = 'Unweighted GPA';
					}
					if(strpos($extra['FROM'],'STUDENT_GPA_CALCULATED sgc')===false)
					{
						$extra['FROM'] .= ",STUDENT_GPA_CALCULATED sgc";
						$extra['WHERE'] .= " AND sgc.STUDENT_ID=s.STUDENT_ID AND sgc.MARKING_PERIOD_ID='".$_REQUEST['gpa_term']."'";
					}
					$extra['WHERE'] .= " AND sgc.".(($_REQUEST['weighted']=='Y')?'WEIGHTED_':'')."GPA BETWEEN '$_REQUEST[gpa_low]' AND '$_REQUEST[gpa_high]' AND sgc.MARKING_PERIOD_ID='".$_REQUEST['gpa_term']."'";
					$_openSIS['SearchTerms'] .= '<font color=gray><b>'.(($_REQUEST['gpa_weighted']=='Y')?'Weighted ':'').'GPA between: </b></font>'.$_REQUEST['gpa_low'].' &amp; '.$_REQUEST['gpa_high'].'<BR>';
				}
				$extra['search'] .= "<TR><TD align=right width=120>GPA<BR><INPUT type=checkbox name=gpa_weighted value=Y>Weighted<BR><INPUT type=radio name=gpa_term value=CUM checked>Cumulative<INPUT type=radio name=gpa_term value=".GetParentMP('SEM',UserMP()).">".GetMP(GetParentMP('SEM',UserMP()),'SHORT_NAME')."<INPUT type=radio name=gpa_term value=".UserMP().">".GetMP(UserMP(),'SHORT_NAME')."</TD><TD>Between<INPUT type=text name=gpa_low class=cell_small size=3 maxlength=5> &amp; <INPUT type=text name=gpa_high size=3 maxlength=5 class=cell_small></TD></TR>";
			break;

			case 'class_rank':
				if(is_numeric($_REQUEST['class_rank_low']) && is_numeric($_REQUEST['class_rank_high']))
				{
					if($_REQUEST['class_rank_low'] > $_REQUEST['class_rank_high'])
					{
						$temp = $_REQUEST['class_rank_high'];
						$_REQUEST['class_rank_high'] = $_REQUEST['class_rank_low'];
						$_REQUEST['class_rank_low'] = $temp;
					}
					if(strpos($extra['FROM'],'STUDENT_GPA_CALCULATED sgc')===false)
					{
						$extra['FROM'] .= ",STUDENT_GPA_CALCULATED sgc";
						$extra['WHERE'] .= " AND sgc.STUDENT_ID=s.STUDENT_ID AND sgc.MARKING_PERIOD_ID='".$_REQUEST['class_rank_term']."'";
					}
					$extra['WHERE'] .= " AND sgc.CLASS_RANK BETWEEN '$_REQUEST[class_rank_low]' AND '$_REQUEST[class_rank_high]'";
					$_openSIS['SearchTerms'] .= '<font color=gray><b>Class Rank between: </b></font>'.$_REQUEST['class_rank_low'].' &amp; '.$_REQUEST['class_rank_high'].'<BR>';
				}
				$extra['search'] .= "<TR><TD align=right width=120>Class Rank<BR><INPUT type=radio name=class_rank_term value=CUM checked>Cumulative<INPUT type=radio name=class_rank_term value=".GetParentMP('SEM',UserMP()).">".GetMP(GetParentMP('SEM',UserMP()),'SHORT_NAME')."<INPUT type=radio name=class_rank_term value=".UserMP().">".GetMP(UserMP(),'SHORT_NAME')."";
				if(strlen($pros = GetChildrenMP('PRO',UserMP())))
				{
					$pros = explode(',',str_replace("'",'',$pros));
					foreach($pros as $pro)
						$extra['search'] .= "<INPUT type=radio name=class_rank_term value=".$pro.">".GetMP($pro,'SHORT_NAME')."";
				}
				$extra['search'] .= "</TD><TD>Between<INPUT type=text name=class_rank_low size=3 maxlength=5 class=cell_small> &amp; <INPUT type=text name=class_rank_high size=3 maxlength=5 class=cell_small></TD></TR>";
			break;

			case 'letter_grade':
				if(count($_REQUEST['letter_grade']))
				{
					$_openSIS['SearchTerms'] .= '<font color=gray><b>With'.($_REQUEST['letter_grade_exclude']=='Y'?'out':'').' Report Card Grade: </b></font>';
					$letter_grades_RET = DBGet(DBQuery("SELECT ID,TITLE FROM REPORT_CARD_GRADES WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."'"),array(),array('ID'));
					foreach($_REQUEST['letter_grade'] as $grade=>$Y)
					{
						$letter_grades .= ",'$grade'";
						$_openSIS['SearchTerms'] .= $letter_grades_RET[$grade][1]['TITLE'].', ';
					}
					$_openSIS['SearchTerms'] = substr($_openSIS['SearchTerms'],0,-2);
					$extra['WHERE'] .= " AND ".($_REQUEST['letter_grade_exclude']=='Y'?'NOT ':'')."EXISTS (SELECT '' FROM STUDENT_REPORT_CARD_GRADES sg3 WHERE sg3.STUDENT_ID=ssm.STUDENT_ID AND sg3.SYEAR=ssm.SYEAR AND sg3.REPORT_CARD_GRADE_ID IN (".substr($letter_grades,1).") AND sg3.MARKING_PERIOD_ID='".$_REQUEST['letter_grade_term']."' )";
					$_openSIS['SearchTerms'] .= '<BR>';
				}

				$extra['search'] .= "<TR><TD align=right width=120>Letter Grade<BR><INPUT type=checkbox name=letter_grade_exclude value=Y>Did not receive<BR><INPUT type=radio name=letter_grade_term value=".GetParentMP('SEM',UserMP()).">".GetMP(GetParentMP('SEM',UserMP()),'SHORT_NAME')."<INPUT type=radio name=letter_grade_term value=".UserMP().">".GetMP(UserMP(),'SHORT_NAME')."";
				if(strlen($pros = GetChildrenMP('PRO',UserMP())))
				{
					$pros = explode(',',str_replace("'",'',$pros));
					foreach($pros as $pro)
						$extra['search'] .= "<INPUT type=radio name=letter_grade_term value=".$pro.">".GetMP($pro,'SHORT_NAME')."";
				}
				$extra['search'] .= "</TD><TD>";
				if($_REQUEST['search_modfunc']=='search_fnc' || !$_REQUEST['search_modfunc'])
					$letter_grades_RET = DBGet(DBQuery("SELECT rg.ID,rg.TITLE,rg.GRADE_SCALE_ID FROM REPORT_CARD_GRADES rg,REPORT_CARD_GRADE_SCALES rs WHERE rg.SCHOOL_ID='".UserSchool()."' AND rg.SYEAR='".UserSyear()."' AND rs.ID=rg.GRADE_SCALE_ID".(User('PROFILE')=='teacher'?' AND rg.GRADE_SCALE_ID=(SELECT GRADE_SCALE_ID FROM COURSE_PERIODS WHERE COURSE_PERIOD_ID=\''.UserCoursePeriod().'\')':'')." ORDER BY rs.SORT_ORDER,rs.ID,rg.BREAK_OFF IS NOT NULL DESC,rg.BREAK_OFF DESC,rg.SORT_ORDER"),array(),array('GRADE_SCALE_ID'));
				foreach($letter_grades_RET as $grades)
				{
					$i = 0;
					if(count($grades))
					{
						foreach($grades as $grade)
						{
							if($i%9==0)
								$extra['search'] .= '<BR>';

							$extra['search'] .= '<INPUT type=checkbox value=Y name=letter_grade['.$grade['ID'].']>'.$grade['TITLE'];
							$i++;
						}
					}
				}
				$extra['search'] .= '</TD></TR>';
			break;

			case 'eligibility':
				if($_REQUEST['ineligible']=='Y')
				{
					$start_end_RET = DBGet(DBQuery("SELECT TITLE,VALUE FROM PROGRAM_CONFIG WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND PROGRAM='eligibility' AND TITLE IN ('START_DAY','END_DAY')"));
					if(count($start_end_RET))
					{
						foreach($start_end_RET as $value)
							$$value['TITLE'] = $value['VALUE'];
					}

					switch(date('D'))
					{
						case 'Mon':
						$today = 1;
						break;
						case 'Tue':
						$today = 2;
						break;
						case 'Wed':
						$today = 3;
						break;
						case 'Thu':
						$today = 4;
						break;
						case 'Fri':
						$today = 5;
						break;
						case 'Sat':
						$today = 6;
						break;
						case 'Sun':
						$today = 7;
						break;
					}

					$start_date = strtoupper(date('d-M-y',time() - ($today-$START_DAY)*60*60*24));
					$end_date = strtoupper(date('d-M-y',time()));
					$extra['WHERE'] .= " AND (SELECT count(*) FROM ELIGIBILITY e WHERE ssm.STUDENT_ID=e.STUDENT_ID AND e.SYEAR=ssm.SYEAR AND e.SCHOOL_DATE BETWEEN '$start_date' AND '$end_date' AND e.ELIGIBILITY_CODE='FAILING') > '0'";
					$_openSIS['SearchTerms'] .= '<font color=gray><b>Eligibility: </b></font>Ineligible<BR>';
				}
				$extra['search'] .= "<TR><TD align=right width=120>Ineligible</TD><TD><INPUT type=checkbox name=ineligible value='Y'></TD></TR>";
			break;

			case 'activity':
				if($_REQUEST['activity_id'])
				{
					$extra['FROM'] .= ",STUDENT_ELIGIBILITY_ACTIVITIES sea";
					$extra['WHERE'] .= " AND sea.STUDENT_ID=s.STUDENT_ID AND sea.SYEAR=ssm.SYEAR AND sea.ACTIVITY_ID='".$_REQUEST['activity_id']."'";
					$activity = DBGet(DBQuery("SELECT TITLE FROM ELIGIBILITY_ACTIVITIES WHERE ID='".$_REQUEST['activity_id']."'"));
					$_openSIS['SearchTerms'] .= '<font color=gray><b>Activity: </b></font>'.$activity[1]['TITLE'].'<BR>';
				}
				if($_REQUEST['search_modfunc']=='search_fnc' || !$_REQUEST['search_modfunc'])
					$activities_RET = DBGet(DBQuery("SELECT ID,TITLE FROM ELIGIBILITY_ACTIVITIES WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."'"));
				$select = "<SELECT name=activity_id><OPTION value=''>Not Specified</OPTION>";
				if(count($activities_RET))
				{
					foreach($activities_RET as $activity)
						$select .= "<OPTION value=$activity[ID]>$activity[TITLE]</OPTION>";
				}
				$select .= '</SELECT>';
				$extra['search'] .= "<TR><TD align=right width=120>Activity</TD><TD>".$select."</TD></TR>";
			break;

			case 'mailing_labels':
				if($_REQUEST['mailing_labels']=='Y')
				{
					$extra['SELECT'] .= ',sam.ADDRESS_ID AS MAILING_LABEL';
					$extra['FROM'] = " LEFT OUTER JOIN STUDENTS_JOIN_ADDRESS sam ON (sam.STUDENT_ID=ssm.STUDENT_ID AND sam.MAILING='Y')".$extra['FROM'];
					$extra['functions'] += array('MAILING_LABEL'=>'MailingLabel');
				}

				$extra['search'] .= '<TR><TD align=right width=120>Mailing Labels</TD><TD><INPUT type=checkbox name=mailing_labels value=Y></TD>';
			break;

			case 'balance':
				if(is_numeric($_REQUEST['balance_low']) && is_numeric($_REQUEST['balance_high']))
				{
					if($_REQUEST['balance_low'] > $_REQUEST['balance_high'])
					{
						$temp = $_REQUEST['balance_high'];
						$_REQUEST['balance_high'] = $_REQUEST['balance_low'];
						$_REQUEST['balance_low'] = $temp;
					}
					$extra['WHERE'] .= " AND (COALESCE((SELECT SUM(f.AMOUNT) FROM BILLING_FEES f,STUDENTS_JOIN_FEES sjf WHERE sjf.FEE_ID=f.ID AND sjf.STUDENT_ID=ssm.STUDENT_ID AND f.SYEAR=ssm.SYEAR),0)+(SELECT COALESCE(SUM(f.AMOUNT),0)-COALESCE(SUM(f.CASH),0) FROM LUNCH_TRANSACTIONS f WHERE f.STUDENT_ID=ssm.STUDENT_ID AND f.SYEAR=ssm.SYEAR)-COALESCE((SELECT SUM(p.AMOUNT) FROM BILLING_PAYMENTS p WHERE p.STUDENT_ID=ssm.STUDENT_ID AND p.SYEAR=ssm.SYEAR),0)) BETWEEN '$_REQUEST[balance_low]' AND '$_REQUEST[balance_high]' ";
				}
				$extra['search'] .= "<TR><TD align=right width=120>Student Billing Balance<BR></TD><TD>Between<INPUT type=text name=balance_low size=5 maxlength=10 class=cell_small> &amp; <INPUT type=text name=balance_high size=5 maxlength=10 class=cell_small></TD></TR>";
			break;
####################################################################################################################
			/*case 'discipline':
				if(is_array($_REQUEST['discipline']))
				{
					foreach($_REQUEST['discipline'] as $key=>$value)
					{
						if(!$value)
							unset($_REQUEST['discipline'][$key]);
					}
				}
				if($_REQUEST['month_discipline_entry_begin'] && $_REQUEST['day_discipline_entry_begin'] && $_REQUEST['year_discipline_entry_begin'])
				{
					$_REQUEST['discipline_entry_begin'] = $_REQUEST['day_discipline_entry_begin'].'-'.$_REQUEST['month_discipline_entry_begin'].'-'.$_REQUEST['year_discipline_entry_begin'];
					if(!VerifyDate($_REQUEST['discipline_entry_begin']))
						unset($_REQUEST['discipline_entry_begin']);
					$_REQUEST['discipline_entry_end'] = $_REQUEST['day_discipline_entry_end'].'-'.$_REQUEST['month_discipline_entry_end'].'-'.$_REQUEST['year_discipline_entry_end'];
					if(!VerifyDate($_REQUEST['discipline_entry_end']))
						unset($_REQUEST['discipline_entry_end']);
				}
				if($_REQUEST['discipline_begin'] && $_REQUEST['discipline_end'])
				{
					foreach($_REQUEST['discipline_begin'] as $key=>$begin)
					{
						if($begin > $_REQUEST['discipline_end'][$key])
						{
							$temp = $_REQUEST['discipline_begin'][$key];
							$_REQUEST['discipline_begin'][$key] = $_REQUEST['discipline_end'][$key];
							$_REQUEST['discipline_end'][$key] = $temp;
						}
					}
				}
				if($_REQUEST['discipline_reporter'] || ($_REQUEST['discipline_entry_begin'] && $_REQUEST['discipline_entry_end']) || count($_REQUEST['discipline']))
				{
					$extra['WHERE'] .= ' AND dr.STUDENT_ID=ssm.STUDENT_ID AND dr.SYEAR=ssm.SYEAR AND dr.SCHOOL_ID=ssm.SCHOOL_ID ';
					$extra['FROM'] .= ',DISCIPLINE_REFERRALS dr ';
					if($_REQUEST['discipline_reporter'])
						$extra['WHERE'] .= " AND dr.STAFF_ID='$_REQUEST[discipline_reporter]' ";
					if($_REQUEST['discipline_entry_begin'] && $_REQUEST['discipline_entry_end'])
						$extra['WHERE'] .= " AND dr.ENTRY_DATE BETWEEN '$_REQUEST[discipline_entry_begin]' AND '$_REQUEST[discipline_entry_end]' ";
				}
				$extra['search'] .= '<TR><TD align=right width=120>Reporter</TD><TD>';
				$users_RET = DBGet(DBQuery("SELECT STAFF_ID,FIRST_NAME,LAST_NAME,MIDDLE_NAME FROM STAFF WHERE SYEAR='".UserSyear()."' AND SCHOOLS LIKE '%,".UserSchool().",%' AND PROFILE IN ('admin','teacher') ORDER BY LAST_NAME,FIRST_NAME,MIDDLE_NAME"));
				$extra['search'] .= '<SELECT name=discipline_reporter><OPTION value="">Not Specified</OPTION>';
				foreach($users_RET as $user)
					$extra['search'] .= '<OPTION value='.$user['STAFF_ID'].'>'.$user['LAST_NAME'].', '.$user['FIRST_NAME'].' '.$user['MIDDLE_NAME'].'</OPTION>';
				$extra['search'] .= '</SELECT>';
				$extra['search'] .= '</TD></TR>';

				$extra['search'] .= '<TR><TD colspan=2>Incident Date<BR> &nbsp; &nbsp; Between '.PrepareDate('','_discipline_entry_begin',true,array('short'=>true)).' & '.PrepareDate('','_discipline_entry_end',true,array('short'=>true))."</font></TD></TR>";
				$categories_RET = DBGet(DBQuery("SELECT ID,TITLE,TYPE,OPTIONS FROM DISCIPLINE_CATEGORIES WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND TYPE!='textarea'"));
				foreach($categories_RET as $category)
				{
					if($category['TYPE']!='date')
					{
						$extra['search'] .= '<TR><TD align=right width=120>'.$category['TITLE'].'</TD><TD>';
						switch($category['TYPE'])
						{
							case 'text':
								$extra['search'] .= '<INPUT type=text name=discipline['.$category['ID'].']>';
								if($_REQUEST['discipline'][$cateogory['ID']])
									$extra['WHERE'] .= " AND dr.CATEGORY_".$category['ID']." LIKE CONCAT('%',".$_REQUEST['discipline'][$cateogory['ID']].",'%') ";
							break;
							case 'checkbox':
								$extra['search'] .= '<INPUT type=checkbox name=discipline['.$category['ID'].'] value=Y>';
								if($_REQUEST['discipline'][$cateogory['ID']])
									$extra['WHERE'] .= " AND dr.CATEGORY_".$category['ID']." = 'Y' ";
							break;
							case 'numeric':
								$extra['search'] .= 'Between<INPUT type=text name=discipline_begin['.$category['ID'].'] size=3 maxlength=11 class=cell_small> &amp; <INPUT type=text name=discipline_end['.$category['ID'].'] size=3 maxlength=11 class=cell_small>';
								if($_REQUEST['discipline_begin'][$cateogory['ID']] && $_REQUEST['discipline_begin'][$cateogory['ID']])
									$extra['WHERE'] .= " AND dr.CATEGORY_".$category['ID']." BETWEEN '".$_REQUEST['discipline_begin'][$cateogory['ID']]."' AND '".$_REQUEST['discipline_end'][$cateogory['ID']]."' ";
							break;
							case 'multiple_checkbox':
							case 'multiple_radio':
							case 'select':
								$category['OPTIONS'] = str_replace("\n","\r",str_replace("\r\n","\r",$category['OPTIONS']));
								$category['OPTIONS'] = explode("\r",$category['OPTIONS']);

								$extra['search'] .= '<SELECT name=discipline['.$category['ID'].']><OPTION value="">Not Specified</OPTION>';
								foreach($category['OPTIONS'] as $option)
									$extra['search'] .= '<OPTION value="'.$option.'">'.$option.'</OPTION>';
								$extra['search'] .= '</SELECT>';
								if($category['TYPE']=='multiple_radio' && $_REQUEST['discipline'][$category['ID']])
									$extra['WHERE'] .= " AND dr.CATEGORY_".$category['ID']." = '".$_REQUEST['discipline'][$category['ID']]."' ";
								elseif($category['TYPE']=='multiple_checkbox' && $_REQUEST['discipline'][$category['ID']])
									$extra['WHERE'] .= " AND dr.CATEGORY_".$category['ID']." LIKE '%".$_REQUEST['discipline'][$category['ID']]."%' ";
							break;
						}
					}
				}*/
			break;
		}
	}
}
?>
