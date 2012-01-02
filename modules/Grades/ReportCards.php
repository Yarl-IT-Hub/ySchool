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
include('../../Redirect_modules.php');
include 'modules/Grades/config.inc.php';

if($_REQUEST['modfunc']=='save')
{
	if(count($_REQUEST['mp_arr']) && count($_REQUEST['st_arr']))
	{
	$mp_list = '\''.implode('\',\'',$_REQUEST['mp_arr']).'\'';
	$last_mp = end($_REQUEST['mp_arr']);
	$st_list = '\''.implode('\',\'',$_REQUEST['st_arr']).'\'';
	$extra['WHERE'] = " AND s.STUDENT_ID IN ($st_list)";

	$extra['SELECT'] .= ",rpg.TITLE as GRADE_TITLE,sg1.GRADE_PERCENT,sg1.COMMENT as COMMENT_TITLE,sg1.STUDENT_ID,sg1.COURSE_PERIOD_ID,sg1.MARKING_PERIOD_ID,c.TITLE as COURSE_TITLE,rc_cp.TITLE AS TEACHER,sp.SORT_ORDER";
	if($_REQUEST['elements']['period_absences']=='Y')
		$extra['SELECT'] .= ",rc_cp.DOES_ATTENDANCE,
				(SELECT count(*) FROM ATTENDANCE_PERIOD ap,ATTENDANCE_CODES ac
					WHERE ac.ID=ap.ATTENDANCE_CODE AND ac.STATE_CODE='A' AND ap.COURSE_PERIOD_ID=sg1.COURSE_PERIOD_ID AND ap.STUDENT_ID=ssm.STUDENT_ID) AS YTD_ABSENCES,
				(SELECT count(*) FROM ATTENDANCE_PERIOD ap,ATTENDANCE_CODES ac
					WHERE ac.ID=ap.ATTENDANCE_CODE AND ac.STATE_CODE='A' AND ap.COURSE_PERIOD_ID=sg1.COURSE_PERIOD_ID AND sg1.MARKING_PERIOD_ID=ap.MARKING_PERIOD_ID AND ap.STUDENT_ID=ssm.STUDENT_ID) AS MP_ABSENCES";
        if($_REQUEST['elements']['gpa']=='Y')
            $extra['SELECT'] .=",sg1.weighted_gp as GPA";
        if($_REQUEST['elements']['comments']=='Y')
                $extra['SELECT'] .= ',s.gender AS GENDER,s.common_name AS NICKNAME';
	$extra['FROM'] .= ",STUDENT_REPORT_CARD_GRADES sg1 LEFT OUTER JOIN REPORT_CARD_GRADES rpg ON (rpg.ID=sg1.REPORT_CARD_GRADE_ID),
					COURSE_PERIODS rc_cp,COURSES c,SCHOOL_PERIODS sp";
	$extra['WHERE'] .= " AND sg1.MARKING_PERIOD_ID IN (".$mp_list.")
					AND rc_cp.COURSE_PERIOD_ID=sg1.COURSE_PERIOD_ID AND c.COURSE_ID = rc_cp.COURSE_ID AND sg1.STUDENT_ID=ssm.STUDENT_ID AND sp.PERIOD_ID=rc_cp.PERIOD_ID";
	$extra['ORDER'] .= ",sp.SORT_ORDER,c.TITLE";
	$extra['functions']['TEACHER'] = '_makeTeacher';
	$extra['group']	= array('STUDENT_ID','COURSE_PERIOD_ID','MARKING_PERIOD_ID');

	$RET = GetStuList($extra);

	if($_REQUEST['elements']['comments']=='Y')
	{
		// GET THE COMMENTS
		unset($extra);
		$extra['WHERE'] = " AND s.STUDENT_ID IN ($st_list)";
		$extra['SELECT_ONLY'] = "s.STUDENT_ID,sc.COURSE_PERIOD_ID,sc.MARKING_PERIOD_ID,sc.REPORT_CARD_COMMENT_ID,sc.COMMENT,(SELECT SORT_ORDER FROM REPORT_CARD_COMMENTS WHERE ID=sc.REPORT_CARD_COMMENT_ID) AS SORT_ORDER";
		$extra['FROM'] = ",STUDENT_REPORT_CARD_COMMENTS sc";
		$extra['WHERE'] .= " AND sc.STUDENT_ID=s.STUDENT_ID AND sc.MARKING_PERIOD_ID='$last_mp'";
		$extra['ORDER_BY'] = 'SORT_ORDER';
		$extra['group'] = array('STUDENT_ID','COURSE_PERIOD_ID','MARKING_PERIOD_ID');
		$comments_RET = GetStuList($extra);
		//echo '<pre>'; var_dump($comments_RET); echo '</pre>'; exit;

		$all_commentsA_RET = DBGet(DBQuery("SELECT ID,TITLE,SORT_ORDER FROM REPORT_CARD_COMMENTS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' AND COURSE_ID IS NOT NULL AND COURSE_ID='0' ORDER BY SORT_ORDER,ID"),array(),array('ID'));
		$commentsA_RET = DBGet(DBQuery("SELECT ID,TITLE,SORT_ORDER FROM REPORT_CARD_COMMENTS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' AND COURSE_ID IS NOT NULL AND COURSE_ID!='0'"),array(),array('ID'));
		$commentsB_RET = DBGet(DBQuery("SELECT ID,TITLE,SORT_ORDER FROM REPORT_CARD_COMMENTS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' AND COURSE_ID IS NULL"),array(),array('ID'));
	}

	if($_REQUEST['elements']['mp_tardies']=='Y' || $_REQUEST['elements']['ytd_tardies']=='Y')
	{
		// GET THE ATTENDANCE
		unset($extra);
		$extra['WHERE'] = " AND s.STUDENT_ID IN ($st_list)";
		$extra['SELECT_ONLY'] = "ap.SCHOOL_DATE,ap.COURSE_PERIOD_ID,ac.ID AS ATTENDANCE_CODE,ap.MARKING_PERIOD_ID,ssm.STUDENT_ID";
		$extra['FROM'] = ",ATTENDANCE_CODES ac,ATTENDANCE_PERIOD ap";
		$extra['WHERE'] .= " AND ac.ID=ap.ATTENDANCE_CODE AND (ac.DEFAULT_CODE!='Y' OR ac.DEFAULT_CODE IS NULL) AND ac.SYEAR=ssm.SYEAR AND ap.STUDENT_ID=ssm.STUDENT_ID";
		$extra['group'] = array('STUDENT_ID','ATTENDANCE_CODE','MARKING_PERIOD_ID');
		$attendance_RET = GetStuList($extra);
	}

	if($_REQUEST['elements']['mp_absences']=='Y' || $_REQUEST['elements']['ytd_absences']=='Y')
	{
		// GET THE DAILY ATTENDANCE
		unset($extra);
		$extra['WHERE'] = " AND s.STUDENT_ID IN ($st_list)";
		$extra['SELECT_ONLY'] = "ad.SCHOOL_DATE,ad.MARKING_PERIOD_ID,ad.STATE_VALUE,ssm.STUDENT_ID";
		$extra['FROM'] = ",ATTENDANCE_DAY ad";
		$extra['WHERE'] .= " AND ad.STUDENT_ID=ssm.STUDENT_ID AND ad.SYEAR=ssm.SYEAR AND (ad.STATE_VALUE='0.0' OR ad.STATE_VALUE='.5') AND ad.SCHOOL_DATE<='".GetMP($last_mp,'END_DATE')."'";
		$extra['group'] = array('STUDENT_ID','MARKING_PERIOD_ID');
		$attendance_day_RET = GetStuList($extra);
	}

	if($_REQUEST['mailing_labels']=='Y')
	{
		// GET THE ADDRESSES
		unset($extra);
		$extra['WHERE'] = " AND s.STUDENT_ID IN ($st_list)";
		$extra['SELECT'] = 's.STUDENT_ID,s.ALT_ID';
		Widgets('mailing_labels',true);
		$extra['SELECT_ONLY'] = $extra['SELECT'];
		$extra['SELECT'] = '';
		$extra['group'] = array('STUDENT_ID','ADDRESS_ID');
		$addresses_RET = GetStuList($extra);
	}

	if(count($RET))
	{
		$columns = array('COURSE_TITLE'=>'Course');
		if($_REQUEST['elements']['teacher']=='Y')
			$columns += array('TEACHER'=>'Teacher');
		if($_REQUEST['elements']['period_absences']=='Y')
			$columns += array('ABSENCES'=>'Abs<BR>YTD / MP');
		if(count($_REQUEST['mp_arr'])>4)
			$mp_TITLE = 'SHORT_NAME';
		else
			$mp_TITLE = 'TITLE';
		foreach($_REQUEST['mp_arr'] as $mp)
			$columns[$mp] = GetMP($mp,$mp_TITLE);
		if($_REQUEST['elements']['comments']=='Y')
		{
			foreach($all_commentsA_RET as $comment)
				$columns['C'.$comment[1]['ID']] = $comment[1]['TITLE'];
			$columns['COMMENT'] = 'Comment';
		}
                                    if($_REQUEST['elements']['gpa']=='Y')
                                        $columns['GPA'] = 'GPA';
		$handle = PDFStart();
		foreach($RET as $student_id=>$course_periods)
		{
			echo "<table width=100%  style=\" font-family:Arial; font-size:12px;\" >";
			echo "<tr><td  style=\"font-size:15px; font-weight:bold; padding-top:20px;\">". GetSchool(UserSchool())."<div style=\"font-size:12px;\">Student Report Card</div></td><td align=right style=\"padding-top:20px\">". ProperDate(DBDate()) ."<br \>Powered by openSIS</td></tr><tr><td colspan=2 style=\"border-top:1px solid #333;\">&nbsp;</td></tr></table>";
			echo '<!-- MEDIA SIZE 8.5x11in -->';
			$comments_arr = array();
			$comments_arr_key = count($all_commentsA_RET)>0;
			unset($grades_RET);
			$i = 0;
			foreach($course_periods as $course_period_id=>$mps)
			{
				$i++;
				$grades_RET[$i]['COURSE_TITLE'] = $mps[key($mps)][1]['COURSE_TITLE'];
				$grades_RET[$i]['TEACHER'] = $mps[key($mps)][1]['TEACHER'];
                                                                        if($_REQUEST['elements']['gpa']=='Y')
                                                                            $grades_RET[$i]['GPA'] = $mps[key($mps)][1]['GPA'];
				foreach($_REQUEST['mp_arr'] as $mp)
				{
					if($mps[$mp])
					{
						$grades_RET[$i][$mp] = $mps[$mp][1]['GRADE_TITLE'];
						if($_REQUEST['elements']['percents']=='Y' && $mps[$mp][1]['GRADE_PERCENT']>0)
							$grades_RET[$i][$mp] .= '<br>'.$mps[$mp][1]['GRADE_PERCENT'].'%';
						$last_mp = $mp;
					}
				}
				if($_REQUEST['elements']['period_absences']=='Y')
					if($mps[$last_mp][1]['DOES_ATTENDANCE'])
						$grades_RET[$i]['ABSENCES'] = $mps[$last_mp][1]['YTD_ABSENCES'].' / '.$mps[$last_mp][1]['MP_ABSENCES'];
					else
						$grades_RET[$i]['ABSENCES'] = 'n/a';
				if($_REQUEST['elements']['comments']=='Y')
				{
					$sep = '';
					foreach($comments_RET[$student_id][$course_period_id][$last_mp] as $comment)
					{
						if($all_commentsA_RET[$comment['REPORT_CARD_COMMENT_ID']])
							$grades_RET[$i]['C'.$comment['REPORT_CARD_COMMENT_ID']] = $comment['COMMENT']!=' '?$comment['COMMENT']:'&middot;';
						else
						{
							if($commentsA_RET[$comment['REPORT_CARD_COMMENT_ID']])
							{
								$grades_RET[$i]['COMMENT'] .= $sep.$commentsA_RET[$comment['REPORT_CARD_COMMENT_ID']][1]['SORT_ORDER'];
								$grades_RET[$i]['COMMENT'] .= '('.($comment['COMMENT']!=' '?$comment['COMMENT']:'&middot;').')';
								$comments_arr_key = true;
							}
							else
								$grades_RET[$i]['COMMENT'] .= $sep.$commentsB_RET[$comment['REPORT_CARD_COMMENT_ID']][1]['SORT_ORDER'];
							$sep = ', ';
							$comments_arr[$comment['REPORT_CARD_COMMENT_ID']] = $comment['SORT_ORDER'];
						}
					}
					if($mps[$last_mp][1]['COMMENT_TITLE'])
						$grades_RET[$i]['COMMENT'] .= $sep.$mps[$last_mp][1]['COMMENT_TITLE'];
				}
			}
			asort($comments_arr,SORT_NUMERIC);

			if($_REQUEST['mailing_labels']=='Y')
				if($addresses_RET[$student_id] && count($addresses_RET[$student_id]))
					$addresses = $addresses_RET[$student_id];
				else
					$addresses = array(0=>array(1=>array('STUDENT_ID'=>$student_id,'ADDRESS_ID'=>'0','MAILING_LABEL'=>'<BR><BR>')));
			else
				$addresses = array(0=>array());

			foreach($addresses as $address)
			{
				unset($_openSIS['DrawHeader']);
				if($_REQUEST['mailing_labels']=='Y')
					echo '<BR><BR><BR>';
				//DrawHeader(Config('TITLE').' Report Card');
				echo '<table border=0>';
				echo '<tr><td>Student Name :</td>';
				echo '<td>'.$mps[key($mps)][1]['FULL_NAME'].'</td></tr>';
				echo '<tr><td>Student ID :</td>';
				echo '<td>'.$mps[key($mps)][1]['STUDENT_ID'].'</td></tr>';
                                echo '<tr><td>Alternate ID :</td>';
				echo '<td>'.$mps[key($mps)][1]['ALT_ID'].'</td></tr>';
				echo '<tr><td>Student Grade :</td>';
				echo '<td>'.$mps[key($mps)][1]['GRADE_ID'].'</td></tr>';
				echo '</table>';
				//DrawHeader($mps[key($mps)][1]['FULL_NAME'],$mps[key($mps)][1]['STUDENT_ID']);
				//DrawHeader($mps[key($mps)][1]['GRADE_ID'],GetSchool(UserSchool()));

				$count_lines = 3;
				if($_REQUEST['elements']['mp_absences']=='Y')
				{
					$count = 0;
					foreach($attendance_day_RET[$student_id][$last_mp] as $abs)
						$count += 1-$abs['STATE_VALUE'];
					$mp_absences = 'Absences in '.GetMP($last_mp,'TITLE').': '.$count;
				}
				if($_REQUEST['elements']['ytd_absences']=='Y')
				{
					$count = 0;
					foreach($attendance_day_RET[$student_id] as $mp_abs)
						foreach($mp_abs as $abs)
							$count += 1-$abs['STATE_VALUE'];
					DrawHeader('Absences this year: '.$count,$mp_absences);
					$count_lines++;
				}
				elseif($_REQUEST['elements']['mp_absences']=='Y')
				{
					DrawHeader($mp_absences);
					$count_lines++;
				}

				if($_REQUEST['elements']['mp_tardies']=='Y')
				{
					$count = 0;
					foreach($attendance_RET[$student_id][$_REQUEST['mp_tardies_code']][$last_mp] as $abs)
						$count++;
					$mp_tardies = 'Tardy in '.GetMP($last_mp,'TITLE').': '.$count;
				}
				if($_REQUEST['elements']['ytd_tardies']=='Y')
				{
					$count = 0;
					foreach($attendance_RET[$student_id][$_REQUEST['ytd_tardies_code']] as $mp_abs)
						foreach($mp_abs as $abs)
							$count++;
					DrawHeader('Tardy this year: '.$count,$mp_tardies);
					$count_lines++;
				}
				elseif($_REQUEST['elements']['mp_tardies']=='Y')
				{
					DrawHeader($mp_tardies);
					$count_lines++;
				}

				if($_REQUEST['mailing_labels']=='Y')
				{
					//DrawHeader(ProperDate(DBDate()));
					$count_lines++;
					for($i=$count_lines;$i<=6;$i++)
						echo '<BR>';
					echo '<TABLE border=0><TR><TD >Student Mailling Label:</TD><TD>'.$address[1]['MAILING_LABEL'].'</TD></TR></TABLE>';
				}
				#echo '<BR>';
				ListOutputPrint($grades_RET,$columns,'','',array(),array(),array('print'=>false));
				
				if($_REQUEST['elements']['comments']=='Y' && ($comments_arr_key || count($comments_arr)))
				{
					$gender = substr($mps[key($mps)][1]['GENDER'],0,1);
					$personalizations = array('^n'=>($mps[key($mps)][1]['NICKNAME']?$mps[key($mps)][1]['NICKNAME']:$mps[key($mps)][1]['FIRST_NAME']),
								'^s'=>($gender=='M'?'his':($gender=='F'?'her':'his/her')) );

					echo '<TABLE width=100%><TR><TD colspan=2><b>Explanation of Comment Codes</b></TD>';
					$i = 0;
					if($comments_arr_key)
						foreach($commentsA_select as $key=>$comment)
						{
							if($i++%3==0)
								echo '</TR><TR valign=top>';
							echo '<TD>('.($key!=' ' ? $key : '&middot;').'): '.$comment[2].'</TD>';
						}
					foreach($comments_arr as $comment=>$so)
					{
						if($i++%3==0)
							echo '</TR><TR valign=top>';
						if($commentsA_RET[$comment])
							echo '<TD width=33%><small>'.$commentsA_RET[$comment][1]['SORT_ORDER'].': '.str_replace(array_keys($personalizations),$personalizations,$commentsA_RET[$comment][1]['TITLE']).'</small></TD>';
						else
							echo '<TD width=33%><small>'.$commentsB_RET[$comment][1]['SORT_ORDER'].': '.str_replace(array_keys($personalizations),$personalizations,$commentsB_RET[$comment][1]['TITLE']).'</small></TD>';
					}
					echo '</TR></TABLE>';
				}
				echo '<!-- NEW PAGE -->';
				echo "<div style=\"page-break-before: always;\"></div>";
			}
		}
		PDFStop($handle);
	}
	else
		BackPrompt('Missing grades or No Students were found.');
	}
	else
		BackPrompt('You must choose at least one student and marking period.');
}

if(!$_REQUEST['modfunc'])
{
	DrawBC("Gradebook >> ".ProgramTitle());

	if($_REQUEST['search_modfunc']=='list')
	{
		echo "<FORM action=for_export.php?modname=$_REQUEST[modname]&modfunc=save&include_inactive=$_REQUEST[include_inactive]&_openSIS_PDF=true method=POST target=_blank>";
		#$extra['header_right'] = '<INPUT type=submit value=\'Create Report Cards for Selected Students\'>';

		$attendance_codes = DBGet(DBQuery("SELECT SHORT_NAME,ID FROM ATTENDANCE_CODES WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND (DEFAULT_CODE!='Y' OR DEFAULT_CODE IS NULL) AND TABLE_NAME='0'"));
		#PopTable_wo_header ('header');
		#$extra['extra_header_left'] = PopTable_wo_header ('header');
		$extra['extra_header_left'] = '<TABLE>';
		$extra['extra_header_left'] .= '<TR><TD colspan=2><b>Include on Report Card:</b></TD></TR>';

		$extra['extra_header_left'] .= '<TR><TD></TD><TD><TABLE>';
		$extra['extra_header_left'] .= '<TR>';
		$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=elements[teacher] value=Y CHECKED>Teacher</TD>';
		$extra['extra_header_left'] .= '<TD></TD>';
		$extra['extra_header_left'] .= '</TR><TR>';
		$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=elements[comments] value=Y CHECKED>Comments</TD>';
		$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=elements[percents] value=Y>Percents</TD>';
		$extra['extra_header_left'] .= '</TR><TR>';
		$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=elements[ytd_absences] value=Y CHECKED>Year-to-date Daily Absences</TD>';
		$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=elements[mp_absences] value=Y'.(GetMP(UserMP(),'SORT_ORDER')!=1?' CHECKED':'').'>Daily Absences this quarter</TD>';
		$extra['extra_header_left'] .= '</TR><TR>';
		$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=elements[ytd_tardies] value=Y>Other Attendance Year-to-date: <SELECT name="ytd_tardies_code">';
		foreach($attendance_codes as $code)
			$extra['extra_header_left'] .= '<OPTION value='.$code['ID'].'>'.$code['SHORT_NAME'].'</OPTION>';
		$extra['extra_header_left'] .= '</SELECT></TD>';
		$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=elements[mp_tardies] value=Y>Other Attendance this quarter: <SELECT name="mp_tardies_code">';
		foreach($attendance_codes as $code)
			$extra['extra_header_left'] .= '<OPTION value='.$code['ID'].'>'.$code['SHORT_NAME'].'</OPTION>';
		$extra['extra_header_left'] .= '</SELECT></TD>';
		$extra['extra_header_left'] .= '</TR><TR>';
		$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=elements[period_absences] value=Y>Period-by-period absences</TD>';
		$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=elements[gpa] value=Y>GPA</TD>';
		$extra['extra_header_left'] .= '</TR>';
		$extra['extra_header_left'] .= '</TABLE></TD></TR>';

		#$mps_RET = DBGet(DBQuery("SELECT SEMESTER_ID,MARKING_PERIOD_ID,SHORT_NAME FROM SCHOOL_QUARTERS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER"),array(),array('SEMESTER_ID'));
		
		$mps_RET = DBGet(DBQuery("SELECT SEMESTER_ID,MARKING_PERIOD_ID,SHORT_NAME FROM SCHOOL_QUARTERS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER"),array(),array('SEMESTER_ID'));
		
		
if(!$mps_RET)
{
		$mps_RET = DBGet(DBQuery("SELECT YEAR_ID,MARKING_PERIOD_ID,SHORT_NAME FROM SCHOOL_SEMESTERS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER"),array(),array('YEAR_ID'));
}

if(!$mps_RET)
{
		$mps_RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,SHORT_NAME FROM SCHOOL_YEARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER"),array(),array('MARKING_PERIOD_ID'));
}
		

		
		$extra['extra_header_left'] .= '<TR><TD align=right>Marking Periods</TD><TD><TABLE><TR><TD><TABLE>';
		foreach($mps_RET as $sem=>$quarters)
		{
			#$extra['extra_header_left'] .= '<TR>';
			foreach($quarters as $qtr)
			{
				$pro = GetChildrenMP('PRO',$qtr['MARKING_PERIOD_ID']);
				if($pro)
				{
					$pros = explode(',',str_replace("'",'',$pro));
					foreach($pros as $pro)
						if(GetMP($pro,'DOES_GRADES')=='Y')
							$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=mp_arr[] value='.$pro.'>'.GetMP($pro,'SHORT_NAME').'</TD>';
				}
				$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=mp_arr[] value='.$qtr['MARKING_PERIOD_ID'].'>'.$qtr['SHORT_NAME'].'</TD>';
			}
//			if(GetMP($sem,'DOES_EXAM')=='Y')
//				$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=mp_arr[] value=E'.$sem.'>'.GetMP($sem,'SHORT_NAME').' Exam</TD>';
//			if(GetMP($sem,'DOES_GRADES')=='Y')
//				$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=mp_arr[] value='.$sem.'>'.GetMP($sem,'SHORT_NAME').'</TD>';
			if(GetMP($sem,'DOES_EXAM')=='Y')
				$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=mp_arr[] value=E'.$sem.'>'.GetMP($sem,'SHORT_NAME').' Exam</TD>';
                                                    if(GetMP($sem,'DOES_GRADES')=='Y' && $sem != $quarters[1]['MARKING_PERIOD_ID'])
				$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=mp_arr[] value='.$sem.'>'.GetMP($sem,'SHORT_NAME').'</TD>';
			#$extra['extra_header_left'] .= '</TR>';
		}
		$extra['extra_header_left'] .= '</TABLE></TD>';
		if($sem)
		{
			$fy = GetParentMP('FY',$sem);
			$extra['extra_header_left'] .= '<TD><TABLE><TR>';
			if(GetMP($fy,'DOES_EXAM')=='Y')
				$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=mp_arr[] value=E'.$fy.'>'.GetMP($fy,'SHORT_NAME').' Exam</TD>';
			if(GetMP($fy,'DOES_GRADES')=='Y')
				$extra['extra_header_left'] .= '<TD><INPUT type=checkbox name=mp_arr[] value='.$fy.'>'.GetMP($fy,'SHORT_NAME').'</TD>';
			$extra['extra_header_left'] .= '</TR></TABLE></TD>';
		}
		$extra['extra_header_left'] .= '</TD></TR></TABLE></TR>';
		Widgets('mailing_labels',true);
		$extra['extra_header_left'] .= $extra['search'];
		$extra['search'] = '';
		$extra['extra_header_left'] .= '</TABLE>';
		#$extra['extra_header_left'] .= PopTable ('footer');
		#$extra['extra_header_left'] .= '</TABLE>';
		#$extra['extra_header_left'] .= '<div style=" position:relative; top:40px; left:520px"><INPUT type=submit class=btn_xxlarge value=\'Create Report Cards for Selected Students\'></div>';
	}

	$extra['link'] = array('FULL_NAME'=>false);
	$extra['SELECT'] = ",s.STUDENT_ID AS CHECKBOX";
	$extra['functions'] = array('CHECKBOX'=>'_makeChooseCheckbox');
	$extra['columns_before'] = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller checked onclick="checkAll(this.form,this.form.controller.checked,\'st_arr\');"><A>');
	$extra['options']['search'] = false;
	$extra['new'] = true;
	//$extra['force_search'] = true;

	Widgets('course');
	Widgets('gpa');
	Widgets('class_rank');
	Widgets('letter_grade');

	Search('student_id',$extra,'true');
	if($_REQUEST['search_modfunc']=='list')
	{
		if($_SESSION['count_stu']!=0)
		echo '<BR><CENTER><INPUT type=submit class=btn_xxlarge value=\'Create Report Cards for Selected Students\'></CENTER>';
		echo "</FORM>";
	}
}

function _makeChooseCheckbox($value,$title)
{
	return '<INPUT type=checkbox name=st_arr[] value='.$value.' checked>';
}

function _makeTeacher($teacher,$column)
{
	return substr($teacher,strrpos(str_replace(' - ',' ^ ',$teacher),'^')+2);
}
?>