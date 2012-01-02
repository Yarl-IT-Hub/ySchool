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
DrawBC("Gradebook > ".ProgramTitle());
if (!$_REQUEST['LO_sort']) {
    $_REQUEST['LO_sort']="CUM_RANK";
    $_REQUEST['LO_direction']=1;
}
if($_REQUEST['search_modfunc'] == 'list')
{
	if(!$_REQUEST['mp'] && GetMP(UserMP(),'POST_START_DATE'))
		$_REQUEST['mp'] = UserMP();
	elseif(strpos(GetAllMP('QTR',UserMP()),str_replace('E','',$_REQUEST['mp']))===false && strpos(GetChildrenMP('PRO',UserMP()),"'".$_REQUEST['mp']."'")===false && GetMP(UserMP(),'POST_START_DATE'))
		$_REQUEST['mp'] = UserMP();
	
	if(!$_REQUEST['mp'] && GetMP(GetParentMP('SEM',UserMP()),'POST_START_DATE'))
		$_REQUEST['mp'] = GetParentMP('SEM',UserMP());	

	$sem = GetParentMP('SEM',UserMP());
	$pro = GetChildrenMP('PRO',UserMP());
	$pros = explode(',',str_replace("'",'',$pro));
	$pro_grading = false;
	$pro_select = '';
	foreach($pros as $pro)
	{
		if(GetMP($pro,'POST_START_DATE'))
		{
			if(!$_REQUEST['mp'])
			{
				$_REQUEST['mp'] = $pro;
				$current_RET = DBGet(DBQuery("SELECT g.STUDENT_ID,g.REPORT_CARD_GRADE_ID,g.REPORT_CARD_COMMENT_ID,g.COMMENT FROM STUDENT_REPORT_CARD_GRADES g,COURSE_PERIODS cp WHERE cp.COURSE_PERIOD_ID=g.COURSE_PERIOD_ID AND cp.COURSE_PERIOD_ID='$course_period_id' AND g.MARKING_PERIOD_ID='".$_REQUEST['mp']."'"),array(),array('STUDENT_ID'));
			}
			$pro_grading = true;
			$pro_select .= "<OPTION value=".$pro.(($pro==$_REQUEST['mp'])?' SELECTED':'').">".GetMP($pro)."</OPTION><OPTION value=".$sem.(('E'.$sem==$_REQUEST['mp'])?' SELECTED':'').">".GetMP($sem).' Exam</OPTION>';
		}
	}
    //bjj keeping search terms
    $PHP_tmp_SELF = PreparePHP_SELF();
   
	echo "<FORM action=$PHP_tmp_SELF method=POST>";
	$mps_select = "<SELECT name=mp onChange='this.form.submit();'>";
	
	if(GetMP(UserMP(),'POST_START_DATE'))
		$mps_select .= "<OPTION value=".UserMP().">".GetMP(UserMP())."</OPTION>";
	elseif($_REQUEST['mp']==UserMP())
		$_REQUEST['mp'] = $sem;
	
	if(GetMP($sem,'POST_START_DATE'))
		$mps_select .= "<OPTION value=".$sem.(($sem==$_REQUEST['mp'])?' SELECTED':'').">".GetMP($sem)."</OPTION>";
	
	if($pro_grading)
		$mps_select .= $pro_select;
		
	$mps_select .= '</SELECT>';
	DrawHeader($mps_select);
}

Widgets('course');
Widgets('gpa');
Widgets('class_rank');
Widgets('letter_grade'); 

//$extra['SELECT'] .= ',sgc.GPA,sgc.WEIGHTED_GPA,sgc.CLASS_RANK';
$extra['SELECT'] .= ',sms.cum_weighted_factor, sms.cum_unweighted_factor, sms.cum_rank';

if(strpos($extra['FROM'],'STUDENT_MP_STATS sms')===false)
{
	$extra['FROM'] .= ',STUDENT_MP_STATS sms';
	$extra['WHERE'] .= " AND sms.STUDENT_ID=ssm.STUDENT_ID AND sms.MARKING_PERIOD_ID='".$_REQUEST['mp']."'";
}
$extra['columns_after'] = array('CUM_UNWEIGHTED_FACTOR'=>'Unweighted GPA','CUM_WEIGHTED_FACTOR'=>'Weighted GPA','CUM_RANK'=>'Class Rank');
$extra['link']['FULL_NAME'] = false;
$extra['new'] = true;
$extra['functions'] = array('CUM_UNWEIGHTED_FACTOR'=>'_roundGPA','CUM_WEIGHTED_FACTOR'=>'_roundGPA');

if(User('PROFILE')=='parent' || User('PROFILE')=='student')
	$_REQUEST['search_modfunc'] = 'list';
$SCHOOL_RET = DBGet(DBQuery("SELECT * from SCHOOLS where ID = '".UserSchool()."'"));
Search('student_id',$extra,'true');

function _roundGPA($gpa,$column)
{   GLOBAL $SCHOOL_RET;
	return round($gpa*$SCHOOL_RET[1]['REPORTING_GP_SCALE'],3);
}
?>