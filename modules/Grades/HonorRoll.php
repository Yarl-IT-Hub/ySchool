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
include 'modules/Grades/DeletePromptX.fnc.php';
DrawBC("Gradebook > ".ProgramTitle());
if($_REQUEST['modfunc']=='save' && $_REQUEST['honor_roll'])
{
	if(count($_REQUEST['st_arr']))
	{
            if($_REQUEST['honor_roll']!=986)
               {
                                    $SCHOOL_RET = DBGet(DBQuery("SELECT * from SCHOOLS where ID = '".UserSchool()."'"));
                                    $scale=$SCHOOL_RET[1]['REPORTING_GP_SCALE'];
                                    $honor=DBGet(DBQuery("SELECT VALUE  FROM PROGRAM_CONFIG WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' AND PROGRAM = 'Honor_Roll' ORDER BY VALUE DESC"));
                                    $honor_gpa1=$_REQUEST['honor_roll'];
                                    foreach($honor as $gp_val)
                                    {
                                        $gpa_value[]=$gp_val['VALUE'];
                                    }
                                        foreach($gpa_value as $gpa_val_key=>$gpa_val)
                                        {
                                                if($gpa_val==$honor_gpa1)
                                                {
                                                     $key=$gpa_val_key;
                                                }
                                        }
                                        if($key!==0)
                                            {

                                                if($gpa_value[$key+1]>$honor_gpa1)
                                                {
                                                    $honor_gpa2=$gpa_value[$key+1];
                                                }
                                                else
                                                {
                                                    $honor_gpa2=$gpa_value[$key-1];
                                                }
                                        }
                                        elseif($key==0)
                                        {
                                            $honor_gpa2=100;
                                        }
                                            $st_list = '\''.implode('\',\'',$_REQUEST['st_arr']).'\'';
                                            $extra['WHERE'] = " AND s.STUDENT_ID IN ($st_list)";

                                            $mp_RET = DBGet(DBQuery("SELECT TITLE,END_DATE FROM SCHOOL_QUARTERS WHERE MARKING_PERIOD_ID='".UserMP()."'"));
                                            $school_info_RET = DBGet(DBQuery("SELECT TITLE,PRINCIPAL FROM SCHOOLS WHERE ID='".UserSchool()."' AND SYEAR='".UserSyear()."'"));
                                            $extra['SELECT'] = ",coalesce(s.COMMON_NAME,s.FIRST_NAME) AS NICK_NAME";
                                            $extra['SELECT'] .= ",(SELECT SORT_ORDER FROM SCHOOL_GRADELEVELS WHERE ID=ssm.GRADE_ID) AS SORT_ORDER";
                                             $extra['FROM'] .= ',STUDENT_REPORT_CARD_GRADES srg';
                                             $extra['SELECT'] .= ',(SELECT pc.TITLE FROM PROGRAM_CONFIG pc WHERE  pc.VALUE=(SELECT if((ROUND(AVG(grade_percent))>='.$honor_gpa1.' and ROUND(AVG(grade_percent))<'.$honor_gpa2.'),'.$honor_gpa1.',"")  FROM
                                                `STUDENT_REPORT_CARD_GRADES` WHERE `MARKING_PERIOD_ID`='.UserMP().'
                                                and `STUDENT_ID`=ssm.STUDENT_ID) )AS HONOR_ROLL';
                                            $extra['WHERE'] .= 'AND ((SELECT ROUND(AVG(grade_percent)) FROM
                                                `STUDENT_REPORT_CARD_GRADES` WHERE `MARKING_PERIOD_ID`='.UserMP().'
                                                and `STUDENT_ID`=ssm.STUDENT_ID)>='.$honor_gpa1.' ) AND ((SELECT ROUND(AVG(grade_percent)) FROM
                                                `STUDENT_REPORT_CARD_GRADES` WHERE `MARKING_PERIOD_ID`='.UserMP().'
                                                and `STUDENT_ID`=ssm.STUDENT_ID)<'.$honor_gpa2.' )  GROUP BY s.STUDENT_ID';
                                          #  $extra['SELECT'] .= ",(SELECT rg.HONOR_ROLL  FROM STUDENT_REPORT_CARD_GRADES sg,COURSE_PERIODS cp,REPORT_CARD_GRADES rg WHERE sg.STUDENT_ID=s.STUDENT_ID AND cp.SYEAR=ssm.SYEAR AND sg.SYEAR=ssm.SYEAR AND sg.MARKING_PERIOD_ID='".UserMP()."' AND cp.COURSE_PERIOD_ID=sg.COURSE_PERIOD_ID AND cp.DOES_HONOR_ROLL='Y' AND rg.GRADE_SCALE_ID=cp.GRADE_SCALE_ID AND sg.REPORT_CARD_GRADE_ID=rg.ID AND  rg.HONOR_ROLL IS NOT NULL)  AS HONOR_ROLL";
                                            $extra['SELECT'] .= ",(SELECT CONCAT(st.LAST_NAME,', ',coalesce(st.FIRST_NAME)) FROM STAFF st,COURSE_PERIODS cp,SCHOOL_PERIODS p,SCHEDULE ss WHERE st.STAFF_ID=cp.TEACHER_ID AND cp.PERIOD_id=p.PERIOD_ID AND p.ATTENDANCE='Y' AND cp.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID AND ss.STUDENT_ID=s.STUDENT_ID AND ss.SYEAR='".UserSyear()."' AND ss.MARKING_PERIOD_ID IN(".GetAllMP('QTR',GetCurrentMP('QTR',DBDate(),false)).") AND (ss.START_DATE<='".DBDate()."' AND (ss.END_DATE>='".DBDate()."' OR ss.END_DATE IS NULL)) ORDER BY p.SORT_ORDER LIMIT 1) AS TEACHER";
                                            $extra['SELECT'] .= ",(SELECT cp.ROOM FROM COURSE_PERIODS cp,SCHOOL_PERIODS p,SCHEDULE ss WHERE cp.PERIOD_id=p.PERIOD_ID AND p.ATTENDANCE='Y' AND cp.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID AND ss.STUDENT_ID=s.STUDENT_ID AND ss.SYEAR='".UserSyear()."' AND ss.MARKING_PERIOD_ID IN(".GetAllMP('QTR',GetCurrentMP('QTR',DBDate(),false)).") AND (ss.START_DATE<='".DBDate()."' AND (ss.END_DATE>='".DBDate()."' OR ss.END_DATE IS NULL)) ORDER BY p.SORT_ORDER LIMIT 1) AS ROOM";
                                            $extra['ORDER_BY'] = 'HONOR_ROLL,SORT_ORDER DESC,ROOM,FULL_NAME';
               }
               elseif($_REQUEST['honor_roll']==986)
                                {
                                            $SCHOOL_RET = DBGet(DBQuery("SELECT * from SCHOOLS where ID = '".UserSchool()."'"));
                                            $scale=$SCHOOL_RET[1]['REPORTING_GP_SCALE'];
                                            $st_list = '\''.implode('\',\'',$_REQUEST['st_arr']).'\'';
                                            $extra['WHERE'] = " AND s.STUDENT_ID IN ($st_list)";
                                            $mp_RET = DBGet(DBQuery("SELECT TITLE,END_DATE FROM SCHOOL_QUARTERS WHERE MARKING_PERIOD_ID='".UserMP()."'"));
                                            $school_info_RET = DBGet(DBQuery("SELECT TITLE,PRINCIPAL FROM SCHOOLS WHERE ID='".UserSchool()."' AND SYEAR='".UserSyear()."'"));
                                             $extra['SELECT'] = ",coalesce(s.COMMON_NAME,s.FIRST_NAME) AS NICK_NAME";
                                            $extra['SELECT'] .= ",(SELECT SORT_ORDER FROM SCHOOL_GRADELEVELS WHERE ID=ssm.GRADE_ID) AS SORT_ORDER";
                                            $extra['SELECT'] .= ',(SELECT pc.TITLE FROM PROGRAM_CONFIG pc WHERE pc.VALUE=
                                                                (SELECT if((ROUND(AVG(grade_percent))>=
                                                                (SELECT pc.VALUE FROM PROGRAM_CONFIG pc WHERE pc.VALUE<=
                                                                (SELECT ROUND(AVG(grade_percent)) FROM `STUDENT_REPORT_CARD_GRADES` WHERE `MARKING_PERIOD_ID`='.UserMP().' and `STUDENT_ID`=ssm.STUDENT_ID) order by pc.value desc limit 1)
                                                                and ROUND(AVG(grade_percent))<
                                                                (SELECT pc.VALUE FROM PROGRAM_CONFIG pc WHERE pc.VALUE
                                                                >(SELECT ROUND(AVG(grade_percent)) FROM `STUDENT_REPORT_CARD_GRADES` WHERE `MARKING_PERIOD_ID`= '.UserMP().'
                                                                and `STUDENT_ID`=ssm.STUDENT_ID) order by pc.value asc limit 1)),(SELECT pc.VALUE FROM PROGRAM_CONFIG pc WHERE pc.VALUE>(SELECT ROUND(AVG(grade_percent)) FROM `STUDENT_REPORT_CARD_GRADES` WHERE `MARKING_PERIOD_ID`='.UserMP().'
                                                                and `STUDENT_ID`=ssm.STUDENT_ID) order by pc.value asc limit 1),(SELECT pc.VALUE FROM PROGRAM_CONFIG pc WHERE pc.VALUE<=(SELECT ROUND(AVG(grade_percent)) FROM `STUDENT_REPORT_CARD_GRADES` WHERE `MARKING_PERIOD_ID`='.UserMP().'
                                                                and `STUDENT_ID`=ssm.STUDENT_ID) order by pc.value desc limit 1))
                                                                FROM `STUDENT_REPORT_CARD_GRADES` WHERE `MARKING_PERIOD_ID`='.UserMP().' and `STUDENT_ID`=ssm.STUDENT_ID) )AS HONOR_ROLL';
                                              $extra['SELECT'] .= ",(SELECT CONCAT(st.LAST_NAME,', ',coalesce(st.FIRST_NAME)) FROM STAFF st,COURSE_PERIODS cp,SCHOOL_PERIODS p,SCHEDULE ss WHERE st.STAFF_ID=cp.TEACHER_ID AND cp.PERIOD_id=p.PERIOD_ID AND p.ATTENDANCE='Y' AND cp.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID AND ss.STUDENT_ID=s.STUDENT_ID AND ss.SYEAR='".UserSyear()."' AND ss.MARKING_PERIOD_ID IN(".GetAllMP('QTR',GetCurrentMP('QTR',DBDate(),false)).") AND (ss.START_DATE<='".DBDate()."' AND (ss.END_DATE>='".DBDate()."' OR ss.END_DATE IS NULL)) ORDER BY p.SORT_ORDER LIMIT 1) AS TEACHER";
                                            $extra['SELECT'] .= ",(SELECT cp.ROOM FROM COURSE_PERIODS cp,SCHOOL_PERIODS p,SCHEDULE ss WHERE cp.PERIOD_id=p.PERIOD_ID AND p.ATTENDANCE='Y' AND cp.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID AND ss.STUDENT_ID=s.STUDENT_ID AND ss.SYEAR='".UserSyear()."' AND ss.MARKING_PERIOD_ID IN(".GetAllMP('QTR',GetCurrentMP('QTR',DBDate(),false)).") AND (ss.START_DATE<='".DBDate()."' AND (ss.END_DATE>='".DBDate()."' OR ss.END_DATE IS NULL)) ORDER BY p.SORT_ORDER LIMIT 1) AS ROOM";
                                            $extra['ORDER_BY'] = 'HONOR_ROLL,SORT_ORDER DESC,ROOM,FULL_NAME';
                                }
                $RET = GetStuList($extra);
                if($_REQUEST['list'])
		{
                                        
                            echo '<CENTER>';
			echo '<TABLE width=80%>';
			echo '<TR align=center><TD colspan=6><B>'.sprintf(('%s Honor Roll'),$school_info_RET[1]['TITLE']).' </B></TD></TR>';
			echo '<TR align=center><TD colspan=6>&nbsp;</TD></TR>';
                           $columns = array('FULL_NAME'=>'Student','STUDENT_ID'=>'Student ID','ALT_ID'=>'Alternate ID','GRADE_ID'=>'Grade','PHONE'=>'Phone','HONOR_ROLL'=>'Honor Roll');
                            ListOutputPrint_Report($RET,$columns);
		}
                else
                {

			$options = '--webpage --quiet -t pdf12 --jpeg --no-links --portrait --footer t --header . --left 0.5in --top 0.5in --bodyimage '.($htmldocAssetsPath?$htmldocAssetsPath:'assets/').'hr_bg.jpg --fontsize 10 --textfont times';
			$handle = PDFStart();
			echo '<!-- MEDIA SIZE 8.5x11in -->';
			echo '<!-- MEDIA LANDSCAPE YES -->';
			foreach($RET as $student)
			{
				echo '<CENTER>';
				echo '<TABLE>';
				echo '<TR align=center><TD><FONT size=1><BR><BR><BR><BR><BR><BR><BR><BR></FONT></TD></TR>';
				echo '<TR align=center><TD><FONT size=3>We hereby recognize</FONT></TD><TR>';
				echo '<TR align=center ><TD ><div style="font-family:Arial; font-size:13px; padding:0px 12px 0px 12px;"><div style="font-size:18px;">'.$student['NICK_NAME'].' '.$student['LAST_NAME'].'</div></div></TD><TR>';
				//echo '<TR align=center><TD><FONT size=3>Who has completed all the academic<BR>requirements for<BR>'.$student['SCHOOL'].' '.($student['HIGH_HONOR']=='Y'?$high_honor:$honor).' Honor Roll</FONT></TD><TR>';
				echo '<TR align=center><TD><FONT size=3>'.'Who has completed all the academic<BR>requirements for<BR>'.$school_info_RET[1]['TITLE'].' '.($student['HONOR_ROLL']).' Honor Roll</FONT></TD><TR>';
				echo '</TABLE>';

				echo '<TABLE width=80%>';
				echo '<TR><TD width=65%><FONT size=1><BR></TD></TR>';
				echo '<TR><TD><FONT size=4>'.$student['TEACHER'].'<BR></FONT><FONT size=0>Teacher</FONT></TD>';
				echo '<TD><FONT size=3>'.$mp_RET[1]['TITLE'].'<BR></FONT><FONT size=0>Marking Period</FONT></TD></TR>';
				//echo '<TR><TD><FONT size=4>'.$student['PRINCIPAL'].'<BR></FONT><FONT size=0>Principal</FONT></TD>';
				echo '<TR><TD><FONT size=4>'.$school_info_RET[1]['PRINCIPAL'].'<BR></FONT><FONT size=0>Principal</FONT></TD>';
				echo '<TD><FONT size=3>'.date('F j, Y',strtotime($mp_RET[1]['END_DATE'])).'<BR></FONT><FONT size=0>Date</FONT></TD></TR>';
				echo '</TABLE>';
				echo '</CENTER>';
                                echo "<div style=\"page-break-before: always;\"></div>";
                       		echo '<!-- NEW PAGE -->';
                                
			}
			PDFStop($handle);
                }
	}
	else
		BackPrompt('You must choose at least one student');
}
elseif($_REQUEST['modfunc']=='save' )
{
    echo '<font color=red>First setup the Honor Roll(Grades->Setup->Honor Roll Setup)..</font>';
}
if(!$_REQUEST['modfunc'])
{   
if($_REQUEST['search_modfunc']=='list')
	{
            echo "<FORM action=for_export.php?modname=$_REQUEST[modname]&modfunc=save&include_inactive=$_REQUEST[include_inactive]&honor_roll=$_REQUEST[honor_roll]&_openSIS_PDF=true method=POST target=_blank>";
            $extra['header_right'] = SubmitButton('Create Honor Roll for Selected Students','','class=btn_xlarge');

            $extra['extra_header_left'] = '<TABLE>';

            $extra['extra_header_left'] .= '<TR><TD><INPUT type=radio name=list value="" checked>Certificates</TD></TR>';
            $extra['extra_header_left'] .= '<TR><TD><INPUT type=radio name=list value=list>List</TD></TR>';

            $extra['extra_header_left'] .= '</TABLE>';
	}
        if(!isset($_REQUEST['_openSIS_PDF']))
	{
		$extra['SELECT'] = ",s.STUDENT_ID AS CHECKBOX";
		$extra['functions'] = array('CHECKBOX'=>'_makeChooseCheckbox');
		$extra['columns_before'] = array('CHECKBOX'=>'</A><INPUT type=checkbox value=Y name=controller checked onclick="checkAll(this.form,this.form.controller.checked,\'st_arr\');"><A>');
	}
        $extra['link'] = array('FULL_NAME'=>false);
	$extra['new'] = true;
	$extra['options']['search'] = false;
	$extra['force_search'] = true;
        Widgets('course');
       MyWidgets('honor_roll');
      Search('student_id',$extra);
        if($_REQUEST['search_modfunc']=='list')
	{
	if($_SESSION['count_stu']!=0)	
            echo '<BR><CENTER>'.SubmitButton('Create Honor Roll for Selected Students','','class=btn_xlarge').'</CENTER>';
		echo "</FORM>";
	}
}

function _makeChooseCheckbox($value,$title)
{
	return '<INPUT type=checkbox name=st_arr[] value='.$value.' checked>';
}
function MyWidgets($item)
{	global $extra,$THIS_RET;

	switch($item)
	{
		case 'honor_roll':
                                if($_REQUEST['honor_roll']!=986 && $_REQUEST['honor_roll'])
                                {
                                   $honor=DBGet(DBQuery("SELECT VALUE  FROM PROGRAM_CONFIG WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' AND PROGRAM = 'Honor_Roll' ORDER BY VALUE DESC"));
                                    $honor_gpa1=$_REQUEST['honor_roll'];
                                    foreach($honor as $gp_val)
                                    {
                                        $gpa_value[]=$gp_val['VALUE'];
                                    }
                                        foreach($gpa_value as $gpa_val_key=>$gpa_val)
                                        {
                                                if($gpa_val==$honor_gpa1)
                                                {
                                                     $key=$gpa_val_key;
                                                }
                                        }
                                        if($key!==0)
                                            {
                                        
                                                if($gpa_value[$key+1]>$honor_gpa1)
                                                {
                                                    $honor_gpa2=$gpa_value[$key+1];
                                                }
                                                else
                                                {
                                                    $honor_gpa2=$gpa_value[$key-1];
                                                }
                                        }
                                        if($honor_gpa2)
                                        {

                                             $extra['FROM'] .= ',STUDENT_REPORT_CARD_GRADES srg';
                                             $extra['SELECT'] .= ',(SELECT pc.TITLE FROM PROGRAM_CONFIG pc WHERE  pc.VALUE=(SELECT if((ROUND(AVG(grade_percent))>='.$honor_gpa1.' and ROUND(AVG(grade_percent))<'.$honor_gpa2.'),'.$honor_gpa1.',"")  FROM
                                                `STUDENT_REPORT_CARD_GRADES` WHERE `MARKING_PERIOD_ID`='.UserMP().'
                                                and `STUDENT_ID`=ssm.STUDENT_ID) )AS HONOR_ROLL';
                                            $extra['WHERE'] .= 'AND ((SELECT ROUND(AVG(grade_percent)) FROM
                                                `STUDENT_REPORT_CARD_GRADES` WHERE `MARKING_PERIOD_ID`='.UserMP().'
                                                and `STUDENT_ID`=ssm.STUDENT_ID)>='.$honor_gpa1.' ) AND ((SELECT ROUND(AVG(grade_percent)) FROM
                                                `STUDENT_REPORT_CARD_GRADES` WHERE `MARKING_PERIOD_ID`='.UserMP().'
                                                and `STUDENT_ID`=ssm.STUDENT_ID)<'.$honor_gpa2.' )  GROUP BY s.STUDENT_ID';
                                      }
                                      else
                                      {         $honor_gpa2=100;
                                               $extra['SELECT'] .= ',(SELECT pc.TITLE FROM PROGRAM_CONFIG pc WHERE  pc.VALUE=(SELECT if((ROUND(AVG(grade_percent))>='.$honor_gpa1.' and ROUND(AVG(grade_percent))<'.$honor_gpa2.'),'.$honor_gpa1.',"") FROM
                                                `STUDENT_REPORT_CARD_GRADES` WHERE `MARKING_PERIOD_ID`='.UserMP().'
                                                and `STUDENT_ID`=ssm.STUDENT_ID))AS HONOR_ROLL';
                                                $extra['FROM'] .= ',STUDENT_REPORT_CARD_GRADES srg';
                                                 $extra['WHERE'] .= 'AND ((SELECT ROUND(AVG(grade_percent)) FROM
                                                `STUDENT_REPORT_CARD_GRADES` WHERE `MARKING_PERIOD_ID`='.UserMP().'
                                                and `STUDENT_ID`=ssm.STUDENT_ID)>='.$honor_gpa1.' ) AND ((SELECT ROUND(AVG(grade_percent)) FROM
                                                `STUDENT_REPORT_CARD_GRADES` WHERE `MARKING_PERIOD_ID`='.UserMP().'
                                                and `STUDENT_ID`=ssm.STUDENT_ID)<'.$honor_gpa2.' )  GROUP BY s.STUDENT_ID';
                                      }
                                       $extra['columns_after'] =array('HONOR_ROLL'=>'Honor Roll');
                               }
                               elseif($_REQUEST['honor_roll']==986)
                                {
                                     $extra['SELECT'] .= ',(SELECT pc.TITLE FROM PROGRAM_CONFIG pc WHERE pc.VALUE=
                                                                (SELECT if((ROUND(AVG(grade_percent))>=
                                                                (SELECT pc.VALUE FROM PROGRAM_CONFIG pc WHERE pc.VALUE<=
                                                                (SELECT ROUND(AVG(grade_percent)) FROM `STUDENT_REPORT_CARD_GRADES` WHERE `MARKING_PERIOD_ID`='.UserMP().' and `STUDENT_ID`=ssm.STUDENT_ID) order by pc.value desc limit 1)
                                                                and ROUND(AVG(grade_percent))<
                                                                (SELECT pc.VALUE FROM PROGRAM_CONFIG pc WHERE pc.VALUE
                                                                >(SELECT ROUND(AVG(grade_percent)) FROM `STUDENT_REPORT_CARD_GRADES` WHERE `MARKING_PERIOD_ID`= '.UserMP().'
                                                                and `STUDENT_ID`=ssm.STUDENT_ID) order by pc.value asc limit 1)),(SELECT pc.VALUE FROM PROGRAM_CONFIG pc WHERE pc.VALUE>(SELECT ROUND(AVG(grade_percent)) FROM `STUDENT_REPORT_CARD_GRADES` WHERE `MARKING_PERIOD_ID`='.UserMP().'
                                                                and `STUDENT_ID`=ssm.STUDENT_ID) order by pc.value asc limit 1),(SELECT pc.VALUE FROM PROGRAM_CONFIG pc WHERE pc.VALUE<=(SELECT ROUND(AVG(grade_percent)) FROM `STUDENT_REPORT_CARD_GRADES` WHERE `MARKING_PERIOD_ID`='.UserMP().'
                                                                and `STUDENT_ID`=ssm.STUDENT_ID) order by pc.value desc limit 1))
                                                                FROM `STUDENT_REPORT_CARD_GRADES` WHERE `MARKING_PERIOD_ID`='.UserMP().' and `STUDENT_ID`=ssm.STUDENT_ID) )AS HONOR_ROLL';
//                                    $extra['SELECT'] .= ',(SELECT pc.TITLE FROM PROGRAM_CONFIG pc WHERE pc.VALUE=(SELECT ROUND(AVG(grade_percent)) FROM
//                                                `STUDENT_REPORT_CARD_GRADES` WHERE `MARKING_PERIOD_ID`='.UserMP().'
//                                                and `STUDENT_ID`=ssm.STUDENT_ID))AS HONOR_ROLL';
                                    $extra['columns_after'] =array('HONOR_ROLL'=>'Honor Roll');
                               }
                                        $option =DBGet(DBQuery('SELECT TITLE,VALUE  FROM PROGRAM_CONFIG WHERE SCHOOL_ID=\''.UserSchool().'\' AND SYEAR=\''.UserSyear().'\' AND PROGRAM = "Honor_Roll" ORDER BY VALUE'));
//                                        $options['986']='All';
                                        foreach($option as $option_value){
                                                      $options[$option_value['VALUE']]=$option_value['TITLE'];
                                                  }
                                        $extra['search'] .= '<TR><TD align=right width=120>Honor Roll</TD><TD>'.SelectInput("",'honor_roll','',$options,false,'').'</TD></TR>';
                                        break;
	}
}
?>