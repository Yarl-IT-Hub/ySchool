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
error_reporting(0);
include('Redirect_root.php');
include("functions/ParamLib.php");
$url=validateQueryString(curPageURL());
if($url===FALSE){
 header('Location: index.php');
 }

$isajax="modules";
//$btn = $_REQUEST['btn'];
 $btn = optional_param('btn','',PARAM_ALPHA);
if($btn == 'Update' || $btn == ''){
	$btn = 'old';
}
echo $nsc = optional_param('nsc','',PARAM_SPCL);
if($_REQUEST['new_school']!='true')
//if(optional_param('new_school','',PARAM_NOTAGS)!='true')
{
	$ns = "NT";
}
else
{
	$ns = "TT";
}

$handle=opendir("js");
while ($file = readdir($handle)) {
$filelst = "$filelst,$file";
}
closedir($handle);
$filelist = explode(",",$filelst);

if(count($filelist)>3)
{
for ($count=1;$count<count($filelist);$count++) {
$filename=$filelist[$count];
if(($filename != ".") && ($filename != "..") && ($filename!=""))
echo "<script src='js/".$filename."'></script>";
}
}
	
echo "<script type='text/javascript'>
	function changeColors(){ 
        
        var aTags = document.getElementsByTagName(\"a\"); 
		
		
        for(i=0;i<aTags.length;i++){ 
        	if(document.getElementsByTagName('a')[i].id=='hm')
                document.getElementsByTagName('a')[i].className = 'submenuitem'; 
        } 
	} 
	
		
</script>";	


error_reporting(1);
//error_reporting(E_ALL); // uncomment this to view error on page
$start_time = time();
include 'Warehouse.php';
$old_school = UserSchool();
$old_syear = UserSyear();

///Newly added
//if(!$_SESSION['UserSchool'])
//{
//	if(User('PROFILE')=='admin' && (!User('SCHOOLS') || strpos(User('SCHOOLS'),','.User('CURRENT_SCHOOL_ID').',')!==false))
//		$_SESSION['UserSchool'] = User('CURRENT_SCHOOL_ID');
//	elseif(User('PROFILE')=='student')
//		$_SESSION['UserSchool'] = trim(User('SCHOOLS'),',');
////}
//	elseif(User('PROFILE')=='teacher')
//	{
////		$mp = GetAllMP('QTR',UserMP());
////                if(!isset($mp))
////                  $mp = GetAllMP('SEM',UserMP());
////                if(!isset($mp))
////                  $mp = GetAllMP('FY',UserMP());
////                    $QI = DBQuery("SELECT cp.SCHOOL_ID FROM COURSE_PERIODS cp, SCHOOL_PERIODS sp,COURSES c WHERE c.COURSE_ID=cp.COURSE_ID AND cp.PERIOD_ID=sp.PERIOD_ID AND cp.SYEAR='".UserSyear()."' AND cp.TEACHER_ID='".User('STAFF_ID')."''".(UserMP()?' AND cp.MARKING_PERIOD_ID IN ('.$mp.')':'')."'  ORDER BY sp.SORT_ORDER LIMIT 1");
//                    $QI = DBQuery("SELECT cp.SCHOOL_ID FROM COURSE_PERIODS cp, SCHOOL_PERIODS sp,COURSES c WHERE c.COURSE_ID=cp.COURSE_ID AND cp.PERIOD_ID=sp.PERIOD_ID AND cp.SYEAR='".UserSyear()."' AND (cp.TEACHER_ID='".User('STAFF_ID')."' OR cp.SECONDARY_TEACHER_ID='".User('STAFF_ID')."')  ORDER BY sp.SORT_ORDER LIMIT 1");
//                    $RET = DBGet($QI);
//                    $_SESSION['UserSchool'] = $RET[1]['SCHOOL_ID'];
//	}
//}



//if((!$_SESSION['UserMP'] || ($_REQUEST['school'] && $_REQUEST['school'] != $old_school) || ($_REQUEST['syear'] && $_REQUEST['syear'] != $old_syear)) && User('PROFILE')!='parent')
if((!$_SESSION['UserMP'] || (optional_param('school','',PARAM_SPCL) && optional_param('school','',PARAM_SPCL) != $old_school) || (optional_param('syear',0,PARAM_SPCL) && optional_param('syear',0,PARAM_SPCL) != $old_syear)) && User('PROFILE')!='parent')
	$_SESSION['UserMP'] = GetCurrentMP('QTR',DBDate());
//// Newly added




array_rwalk($_REQUEST,'strip_tags');

if(!isset($_REQUEST['_openSIS_PDF']))
{
	Warehouse('header');
$css = trim(getCSS());
echo "<link rel='stylesheet' type='text/css' href='themes/".trim($css)."/".trim($css).".css'>";
echo "<link rel='stylesheet' type='text/css' href='styles/help.css'>";
if(strpos($_REQUEST['modname'],'misc/')===false)
		echo '<script language="JavaScript">if(window == top  && (!window.opener || window.opener.location.href.substring(0,(window.opener.location.href.indexOf("&")!=-1?window.opener.location.href.indexOf("&"):window.opener.location.href.replace("#","").length))!=window.location.href.substring(0,(window.location.href.indexOf("&")!=-1?window.location.href.indexOf("&"):window.location.href.replace("#","").length)))) window.location.href = "index.php";</script>';
	echo "<BODY onload='newLoad()' style='overflow-x:hidden; min-windth:900px;'>";
}		
		
echo "
<center>
  <table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"wrapper\">
    <tr>
      <td ><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
          <tr>
            <td class=\"banner\" valign=\"top\"><table width='100%' border='0' cellspacing='0' cellpadding='0'>
                <tr>
                  <td align=\"left\">&nbsp;</td>
                  <td align=\"right\"><div class=\"user_info\">".date('l F j, Y')." &nbsp;&nbsp;|&nbsp;&nbsp;".User('NAME')." &nbsp;&nbsp;|&nbsp;&nbsp; <a href='index.php?modfunc=logout' class='logout'>Log Out</a>";
/****************************************************************************************/
if(User('PROFILE')=='teacher')
{
	echo "<br /><br />
	<table cellspacing=\"0\"><tr><td><FORM name=head_frm id=head_frm action=Side.php?modfunc=update&btnn=$btn&nsc=$ns&act=school method=POST><INPUT type=hidden name=modcat value='' id=modcat_input>";

$schools = substr(str_replace(",","','",User('SCHOOLS')),2,-2);
							
$QI = DBQuery("SELECT ID,TITLE FROM SCHOOLS ORDER BY ID DESC");


$staff = $_SESSION['STAFF_ID'];
$sid = DBGet(DBQuery("SELECT SCHOOLS FROM STAFF WHERE STAFF_ID = $staff"));
$sch = $sid[1]['SCHOOLS'];
$count = substr_count( $sch, ',' );
$prv_count = $count - 1;
$sids = explode(",", $sch);


$schools = substr(str_replace(",","','",User('SCHOOLS')),2,-2);

$sql_school = "SELECT ID,TITLE FROM SCHOOLS WHERE ";
for($i=1; $i<$prv_count; $i++)
$sql_school.="ID=$sids[$i] or ";

$sql_school.="ID=$sids[$prv_count] ORDER BY ID DESC";


$QI = DBQuery($sql_school);
$RET = DBGet($QI);



//if(!UserSchool())
//{
//$_SESSION['UserSchool'] = $RET[1]['ID'];
//DBQuery("UPDATE STAFF SET CURRENT_SCHOOL_ID='".UserSchool()."' WHERE STAFF_ID='".User('STAFF_ID')."'");
//}

echo "<SELECT name=school onChange='this.form.submit();'>";
foreach($RET as $school){
    echo "<OPTION style='padding-right:8px;' value=$school[ID]".((UserSchool()==$school['ID'])?' SELECTED':'').">".$school['TITLE']."</OPTION>";
}
echo "</SELECT>";
					
//===================================================================================================

echo "</FORM></td><td></td>";
echo "<td><FORM name=head_frm id=head_frm action=Side.php?modfunc=update&btnn=$btn&nsc=$ns&act=syear method=POST><INPUT type=hidden name=modcat value='' id=modcat_input>";
$school_years_RET1=DBGet(DBQuery("SELECT START_DATE,END_DATE FROM SCHOOL_YEARS WHERE SCHOOL_ID=".UserSchool()));
$school_years_RET1=$school_years_RET1[1];
$school_years_RET1['START_DATE']=explode("-",$school_years_RET1['START_DATE']);
$school_years_RET1['START_DATE']=$school_years_RET1['START_DATE'][0];
$school_years_RET1['END_DATE']=explode("-",$school_years_RET1['END_DATE']);
$school_years_RET1['END_DATE']=$school_years_RET1['END_DATE'][0];
						 
 echo "<SELECT name=syear onChange='this.form.submit();' style='width:80;'>";
#foreach($years_RET as $year)
# {
if($school_years_RET1['END_DATE']==$school_years_RET1['START_DATE']+1)
{
    $school_years_RET=DBGet(DBQuery("SELECT START_DATE,END_DATE FROM SCHOOL_YEARS sy,STAFF s WHERE s.SYEAR=sy.SYEAR AND SCHOOL_ID=".UserSchool()." AND  s.USERNAME=(SELECT USERNAME FROM STAFF WHERE  STAFF_ID='$_SESSION[STAFF_ID]')"));

    foreach($school_years_RET as $school_years)
    {
        $school_years['START_DATE']=explode("-",$school_years['START_DATE']);
        echo $school_years['START_DATE']=$school_years['START_DATE'][0];
        echo $school_years['END_DATE']=explode("-",$school_years['END_DATE']);
        echo $school_years['END_DATE']=$school_years['END_DATE'][0];
        echo "<OPTION value=$school_years[START_DATE]".((UserSyear()==$school_years['START_DATE'])?' SELECTED':'')."> $school_years[START_DATE]-".($school_years['END_DATE'])."</OPTION>";
    }
}
else if($school_years_RET1['END_DATE']==$school_years_RET1['START_DATE'])
{
    if(UserSchool())
        $school_years_RET=DBGet(DBQuery("SELECT START_DATE,END_DATE FROM SCHOOL_YEARS sy ,STAFF s WHERE s.SYEAR=sy.SYEAR  and sy.SCHOOL_ID=".UserSchool()." AND s.USERNAME=(SELECT USERNAME FROM STAFF WHERE STAFF_ID='$_SESSION[STAFF_ID]')"));
    else
        $school_years_RET=DBGet(DBQuery("SELECT START_DATE,END_DATE FROM SCHOOL_YEARS sy ,STAFF s WHERE s.SYEAR=sy.SYEAR  AND s.USERNAME=(SELECT USERNAME FROM STAFF  WHERE STAFF_ID='$_SESSION[STAFF_ID]')"));

    foreach($school_years_RET as $school_years)
    {
        $school_years['START_DATE']=explode("-",$school_years['START_DATE']);
        $school_years_RET['START_DATE']=$school_years['START_DATE'][0];
        echo "<OPTION value=$school_years_RET[START_DATE]".((UserSyear()==$school_years_RET['START_DATE'])?' SELECTED':'').">$school_years_RET[START_DATE]</OPTION>";
    }
}

echo '</SELECT>';
echo "</FORM></td><td></td>";

echo "<td><FORM name=head_frm id=head_frm action=Side.php?modfunc=update&btnn=$btn&nsc=$ns&act=mp method=POST><INPUT type=hidden name=modcat value='' id=modcat_input>";

$RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE FROM SCHOOL_QUARTERS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER"));
if(!isset($_SESSION['UserMP']))
{
    $_SESSION['UserMP'] = GetCurrentMP('QTR',DBDate());
    $allMP='QTR';
}	
if(!$RET)
{
    $RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE FROM SCHOOL_SEMESTERS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER"));
    if(!isset($_SESSION['UserMP']))
    {
            $_SESSION['UserMP'] = GetCurrentMP('SEM',DBDate());
            $allMP='SEM';
    }	
}

if(!$RET)
{
    $RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE FROM SCHOOL_YEARS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER"));
    if(!isset($_SESSION['UserMP']))
    {
            $_SESSION['UserMP'] = GetCurrentMP('FY',DBDate());
            $allMP='FY';
    }	
}
	#print_r($RET);

echo "<SELECT name=mp onChange='this.form.submit();'>";
if(count($RET))
{
    if(!UserMP())
            $_SESSION['UserMP'] = $RET[1]['MARKING_PERIOD_ID'];

    foreach($RET as $quarter)
    {
                    echo "<OPTION value=$quarter[MARKING_PERIOD_ID]".(UserMP()==$quarter['MARKING_PERIOD_ID']?' SELECTED':'').">".$quarter['TITLE']."</OPTION>";

    }
}
echo "</SELECT>";
//Marking Period

echo '</FORM></td></tr>';
echo '</table>';
echo '<table cellspacing=\"0\">';
echo '<tr><td style="color:#fff;">Subject</td><td></td>
          <td style="color:#fff">Course</td><td></td>
		  <td style="color:#fff">Course Period</td></tr>';

echo "<tr><td><FORM name=head_frm id=head_frm action=Side.php?modfunc=update&btnn=$btn&nsc=$ns&act=subject method=POST><INPUT type=hidden name=modcat value='' id=modcat_input>";

$sub = DBQuery("SELECT DISTINCT cs.TITLE, cs.SUBJECT_ID,cs.SCHOOL_ID FROM COURSE_SUBJECTS as cs,COURSE_DETAILS as cd WHERE cs.SUBJECT_ID=cd.SUBJECT_ID AND cd.SYEAR='".UserSyear()."' AND (cd.TEACHER_ID='".User('STAFF_ID')."' OR cd.SECONDARY_TEACHER_ID='".User('STAFF_ID')."') AND cs.SCHOOL_ID='".UserSchool()."' AND cd.MARKING_PERIOD_ID IN (".GetAllMP($allMP,UserMP()).")");
$RET = DBGet($sub);

if(!UserSubject()){
    $_SESSION['UserSubject']=$RET[1]['SUBJECT_ID'];
}
echo "<SELECT name=subject onChange='this.form.submit();'>";
foreach($RET as $subject){
    echo "<OPTION id=$subject[SUBJECT_ID] value=$subject[SUBJECT_ID]".((UserSubject()==$subject['SUBJECT_ID'])?' SELECTED':'').">".$subject['TITLE']."</OPTION>";
}
echo "</SELECT>";
//===================================================================================================		
echo "</FORM></td><td></td>";

echo "<td><FORM name=head_frm id=head_frm action=Side.php?modfunc=update&btnn=$btn&nsc=$ns&act=course method=POST><INPUT type=hidden name=modcat value='' id=modcat_input>";
$course = DBQuery("SELECT DISTINCT cd.COURSE_TITLE, cd.COURSE_ID,cd.SUBJECT_ID,cd.SCHOOL_ID FROM COURSE_DETAILS cd WHERE (cd.TEACHER_ID='".User('STAFF_ID')."' OR cd.SECONDARY_TEACHER_ID='".User('STAFF_ID')."') AND cd.SYEAR='".UserSyear()."' AND cd.SCHOOL_ID='".UserSchool()."' AND cd.SUBJECT_ID='".UserSubject()."' AND cd.MARKING_PERIOD_ID IN (".GetAllMP($allMP,UserMP()).")");					
$RET = DBGet($course);
if(!UserCourse()){
    $_SESSION['UserCourse']=$RET[1]['COURSE_ID'];
}
echo "<SELECT name=course onChange='this.form.submit();'>";
foreach($RET as $course){
    echo "<OPTION id=$course[COURSE_ID] value=$course[COURSE_ID]".((UserCourse()==$course['COURSE_ID'])?' SELECTED':'').">".$course['COURSE_TITLE']."</OPTION>";
}
echo "</SELECT>";
//===================================================================================================							     					     
echo "</FORM></td><td></td>";

echo "<td><FORM name=head_frm id=head_frm action=Side.php?modfunc=update&btnn=$btn&nsc=$ns&act=period method=POST><INPUT type=hidden name=modcat value='' id=modcat_input>";		
						
//$QI = DBQuery("SELECT DISTINCT cp.PERIOD_ID,cp.COURSE_PERIOD_ID,sp.TITLE,sp.SHORT_NAME,c.SUBJECT_ID,cp.MARKING_PERIOD_ID,cp.DAYS,cp.SCHOOL_ID,sp.SORT_ORDER,c.TITLE AS COURSE_TITLE FROM COURSE_PERIODS cp, SCHOOL_PERIODS sp,COURSES c WHERE c.COURSE_ID=cp.COURSE_ID AND cp.PERIOD_ID=sp.PERIOD_ID AND cp.SYEAR='".UserSyear()."' AND cp.TEACHER_ID='".User('STAFF_ID')."' AND cp.SCHOOL_ID='".UserSchool()."' AND c.SUBJECT_ID='".UserSubject()."' AND cp.COURSE_ID='".UserCourse()."' ORDER BY sp.SORT_ORDER ");
$QI = DBQuery("SELECT COURSE_PERIOD_ID,COURSE_ID,TITLE,SCHOOL_ID,PERIOD_ID FROM COURSE_PERIODS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' AND COURSE_ID='".UserCourse()."' AND (TEACHER_ID='".User('STAFF_ID')."' OR SECONDARY_TEACHER_ID='".User('STAFF_ID')."') AND MARKING_PERIOD_ID IN (".GetAllMP($allMP,UserMP()).")");
$RET = DBGet($QI);

$fy_id = DBGet(DBQuery("SELECT MARKING_PERIOD_ID FROM SCHOOL_YEARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
$fy_id = $fy_id[1]['MARKING_PERIOD_ID'];

if(!UserCoursePeriod()){
    $_SESSION['UserCoursePeriod'] = $RET[1]['COURSE_PERIOD_ID'];
}	

echo "<SELECT name=period onChange='this.form.submit();'>";
foreach($RET as $period)
{
    echo "<OPTION id=$period[COURSE_PERIOD_ID] value=$period[COURSE_PERIOD_ID]".((UserCoursePeriod()==$period['COURSE_PERIOD_ID'])?' SELECTED':'').">".$period['TITLE']."</OPTION>";
    if(UserCoursePeriod()==$period['COURSE_PERIOD_ID'])
    {
        $_SESSION['UserPeriod'] = $period['PERIOD_ID'];
        $_SESSION['UserCoursePeriod'] = $period['COURSE_PERIOD_ID'];
    }
}
echo "</SELECT>";
echo "</FORM></td>";
echo '</tr></table></div>';
echo "</td></tr></table></td></tr>";
						
}  ##################Only for Teacher End##################	
if(User('PROFILE')!='teacher')
{				
echo "<br><br><table cellpadding=2 cellspacing=0><tr><td><FORM name=head_frm id=head_frm action=Side.php?modfunc=update&btnn=$btn&nsc=$ns method=POST>
<INPUT type=hidden name=modcat value='' id=modcat_input>
";
	
if(User('PROFILE')=='admin')
{
    $schools = substr(str_replace(",","','",User('SCHOOLS')),2,-2);
    #$QI = DBQuery("SELECT ID,TITLE FROM SCHOOLS ORDER BY ID DESC".($schools?" WHERE ID IN ($schools)":''));
    $QI = DBQuery("SELECT ID,TITLE FROM SCHOOLS ORDER BY ID DESC");

    // ---------------------------- School id start ----------------------------------------------------------------- //
    $staff = $_SESSION['STAFF_ID'];
    $sid = DBGet(DBQuery("SELECT SCHOOLS FROM STAFF WHERE STAFF_ID = $staff"));
    $sch = $sid[1]['SCHOOLS'];
    $count = substr_count( $sch, ',' );
    $prv_count = $count - 1;
    $sids = explode(",", $sch);
    // ----------------------------- School id end ------------------------------------------------------------------ //

    $schools = substr(str_replace(",","','",User('SCHOOLS')),2,-2);
    $sql_school = "SELECT ID,TITLE FROM SCHOOLS WHERE ";
    for($i=1; $i<$prv_count; $i++)
        $sql_school.="ID=$sids[$i] or ";

    $sql_school.="ID=$sids[$prv_count] ORDER BY ID DESC";

    $QI = DBQuery($sql_school);
    $RET = DBGet($QI);

//    if(!UserSchool())
//    {
//        $_SESSION['UserSchool'] = $RET[1]['ID'];
//        DBQuery("UPDATE STAFF SET CURRENT_SCHOOL_ID='".UserSchool()."' WHERE STAFF_ID='".User('STAFF_ID')."'");
//    }

    //                                if(!UserSchool())
    //                                {
    //                                        $_SESSION['UserSchool'] = $RET[1]['ID'];
    //                                        DBQuery("UPDATE STAFF SET CURRENT_SCHOOL_ID='".UserSchool()."' WHERE STAFF_ID='".User('STAFF_ID')."'");
    //                                }

    echo "<SELECT name=school onChange='this.form.submit();'>";
    foreach($RET as $school)
        echo "<OPTION  style='padding-right:8px;' value=$school[ID]".((UserSchool()==$school['ID'])?' SELECTED':'').">".$school['TITLE']."</OPTION>";

    echo "</SELECT>&nbsp;";
}

//if(1)
//{
//    if(User('PROFILE')!='student')
//        $sql = "SELECT DISTINCT sy.SYEAR FROM SCHOOL_YEARS sy,STAFF s WHERE s.SYEAR=sy.SYEAR AND s.USERNAME=(SELECT USERNAME FROM STAFF WHERE STAFF_ID='$_SESSION[STAFF_ID]')";
//    else
//        $sql = "SELECT DISTINCT sy.SYEAR FROM SCHOOL_YEARS sy,STUDENT_ENROLLMENT se WHERE se.SYEAR=sy.SYEAR AND se.STUDENT_ID='$_SESSION[STUDENT_ID]'";
//    $years_RET = DBGet(DBQuery($sql));
//}
//else
//    echo $years_RET = array(1=>array('SYEAR'=>"$DefaultSyear"));

if(User('PROFILE')=='parent')
{
    $RET = DBGet(DBQuery("SELECT sju.STUDENT_ID, se.SCHOOL_ID FROM STUDENTS s,STUDENTS_JOIN_USERS sju, STUDENT_ENROLLMENT se WHERE s.STUDENT_ID=sju.STUDENT_ID AND sju.STAFF_ID='".User('STAFF_ID')."' AND se.SYEAR=".UserSyear()." AND se.STUDENT_ID=sju.STUDENT_ID AND (('".DBDate()."' BETWEEN se.START_DATE AND se.END_DATE OR se.END_DATE IS NULL) AND '".DBDate()."'>=se.START_DATE)"));
    foreach($RET as $student)
        $_SESSION['UserSchool'] = $student['SCHOOL_ID'];
}
						
if(User('PROFILE')=='parent' || User('PROFILE')=='teacher')
{
    if(!$_SESSION['UserSchool'])
    {
        $sch_id = DBGet(DBQuery("SELECT CURRENT_SCHOOL_ID FROM STAFF WHERE STAFF_ID='".User('STAFF_ID')."'"));
        $sch_id = $sch_id[1]['CURRENT_SCHOOL_ID'];
        $_SESSION['UserSchool'] = $sch_id;
    }
}
						
echo '</FORM></td>';

echo "<td><FORM name=head_frm id=head_frm action=Side.php?modfunc=update&btnn=$btn&nsc=$ns method=POST>
<INPUT type=hidden name=modcat value='' id=modcat_input>";
#if(User('PROFILE')=='admin')
#{

$school_years_RET1=DBGet(DBQuery("SELECT START_DATE,END_DATE FROM SCHOOL_YEARS WHERE SCHOOL_ID=".UserSchool()));
$school_years_RET1=$school_years_RET1[1];
$school_years_RET1['START_DATE']=explode("-",$school_years_RET1['START_DATE']);
$school_years_RET1['START_DATE']=$school_years_RET1['START_DATE'][0];
$school_years_RET1['END_DATE']=explode("-",$school_years_RET1['END_DATE']);
$school_years_RET1['END_DATE']=$school_years_RET1['END_DATE'][0];
						
echo "<SELECT name=syear onChange='this.form.submit();'>";
#foreach($years_RET as $year)
# {
if($school_years_RET1['END_DATE']==$school_years_RET1['START_DATE']+1)
{
    if(User('PROFILE')=='student'){
        $school_years_RET=DBGet(DBQuery("SELECT DISTINCT sy.START_DATE,sy.END_DATE FROM SCHOOL_YEARS sy,STUDENT_ENROLLMENT se WHERE se.SYEAR=sy.SYEAR AND se.STUDENT_ID='$_SESSION[STUDENT_ID]' AND sy.SCHOOL_ID=".UserSchool()." "));
    }
    else{
        $school_years_RET=DBGet(DBQuery("SELECT START_DATE,END_DATE FROM SCHOOL_YEARS sy,STAFF s WHERE s.SYEAR=sy.SYEAR AND SCHOOL_ID=".UserSchool()." AND  s.USERNAME=(SELECT USERNAME FROM STAFF WHERE STAFF_ID='$_SESSION[STAFF_ID]')"));
    }
    foreach($school_years_RET as $school_years)
    {
        $school_years['START_DATE']=explode("-",$school_years['START_DATE']);
        $school_years['START_DATE']=$school_years['START_DATE'][0];
        $school_years['END_DATE']=explode("-",$school_years['END_DATE']);
        $school_years['END_DATE']=$school_years['END_DATE'][0];
        echo "<OPTION value=$school_years[START_DATE]".((UserSyear()==$school_years['START_DATE'])?' SELECTED':'')."> $school_years[START_DATE]-".($school_years['END_DATE'])."</OPTION>";
    }
}
else if($school_years_RET1['END_DATE']==$school_years_RET1['START_DATE'])
{
    if(User('PROFILE')=='student')
        $school_years_RET=DBGet(DBQuery("SELECT DISTINCT sy.START_DATE,sy.END_DATE FROM SCHOOL_YEARS sy,STUDENT_ENROLLMENT se WHERE se.SYEAR=sy.SYEAR AND se.STUDENT_ID='$_SESSION[STUDENT_ID]' AND sy.SCHOOL_ID=".UserSchool()." "));
    else
    {
        if(UserSchool())
            $school_years_RET=DBGet(DBQuery("SELECT START_DATE,END_DATE FROM SCHOOL_YEARS sy ,STAFF s WHERE s.SYEAR=sy.SYEAR  and sy.SCHOOL_ID=".UserSchool()." AND s.USERNAME=(SELECT USERNAME FROM STAFF WHERE STAFF_ID='$_SESSION[STAFF_ID]')"));
        else
            $school_years_RET=DBGet(DBQuery("SELECT START_DATE,END_DATE FROM SCHOOL_YEARS sy ,STAFF s WHERE s.SYEAR=sy.SYEAR  AND s.USERNAME=(SELECT USERNAME FROM STAFF  WHERE STAFF_ID='$_SESSION[STAFF_ID]')"));
    }
    foreach($school_years_RET as $school_years)
    {
        $school_years['START_DATE']=explode("-",$school_years['START_DATE']);
        $school_years_RET['START_DATE']=$school_years['START_DATE'][0];
        echo "<OPTION value=$school_years_RET[START_DATE]".((UserSyear()==$school_years_RET['START_DATE'])?' SELECTED':'').">$school_years_RET[START_DATE]</OPTION>";
    }
}
						#}
echo '</SELECT>&nbsp;';
echo '</FORM></td>';

echo "<td><FORM name=head_frm id=head_frm action=Side.php?modfunc=update&btnn=$btn&nsc=$ns method=POST>
<INPUT type=hidden name=modcat value='' id=modcat_input>";
if(User('PROFILE')=='parent')
{
    $RET = DBGet(DBQuery("SELECT sju.STUDENT_ID,CONCAT(s.LAST_NAME,', ',s.FIRST_NAME) AS FULL_NAME,se.SCHOOL_ID FROM STUDENTS s,STUDENTS_JOIN_USERS sju, STUDENT_ENROLLMENT se WHERE s.STUDENT_ID=sju.STUDENT_ID AND sju.STAFF_ID='".User('STAFF_ID')."' AND se.SYEAR=".UserSyear()." AND se.STUDENT_ID=sju.STUDENT_ID AND (('".DBDate()."' BETWEEN se.START_DATE AND se.END_DATE OR se.END_DATE IS NULL) AND '".DBDate()."'>=se.START_DATE)"));
    if(!UserStudentID())
        $_SESSION['student_id'] = $RET[1]['STUDENT_ID'];
    echo "<SELECT name=student_id onChange='this.form.submit();'>";
    if(count($RET))
    {
        foreach($RET as $student)
        {
            echo "<OPTION value=$student[STUDENT_ID]".((UserStudentID()==$student['STUDENT_ID'])?' SELECTED':'').">".$student['FULL_NAME']."</OPTION>";
            if(UserStudentID()==$student['STUDENT_ID'])
                $_SESSION['UserSchool'] = $student['SCHOOL_ID'];
        }
    }
    echo "</SELECT>&nbsp;";

    if(!UserMP())
        $_SESSION['UserMP'] = GetCurrentMP('QTR',DBDate());
}
echo '</FORM></td>';							
//                                    if(User('PROFILE')=='teacher')
//                                    {
//                                     //if(UserMP())
//                                    //	$QI = DBQuery("SELECT DISTINCT cp.PERIOD_ID,cp.COURSE_PERIOD_ID,sp.TITLE,sp.SHORT_NAME,cp.MARKING_PERIOD_ID,cp.DAYS,cp.SCHOOL_ID,sp.SORT_ORDER,c.TITLE AS COURSE_TITLE FROM COURSE_PERIODS cp, SCHOOL_PERIODS sp,COURSES c WHERE c.COURSE_ID=cp.COURSE_ID AND cp.PERIOD_ID=sp.PERIOD_ID AND cp.SYEAR='".UserSyear()."' AND cp.TEACHER_ID='".User('STAFF_ID')."' AND cp.MARKING_PERIOD_ID IN (".GetAllMP('QTR',UserMP()).") ORDER BY sp.SORT_ORDER ");
//                                    //else
//                                    $QI = DBQuery("SELECT DISTINCT cp.PERIOD_ID,cp.COURSE_PERIOD_ID,sp.TITLE,sp.SHORT_NAME,cp.MARKING_PERIOD_ID,cp.DAYS,cp.SCHOOL_ID,sp.SORT_ORDER,c.TITLE AS COURSE_TITLE FROM COURSE_PERIODS cp, SCHOOL_PERIODS sp,COURSES c WHERE c.COURSE_ID=cp.COURSE_ID AND cp.PERIOD_ID=sp.PERIOD_ID AND cp.SYEAR='".UserSyear()."' AND cp.TEACHER_ID='".User('STAFF_ID')."' ORDER BY sp.SORT_ORDER ");
//                                    $RET = DBGet($QI);
//                                    // get the fy marking period id, there should be exactly one fy marking period
//                                    $fy_id = DBGet(DBQuery("SELECT MARKING_PERIOD_ID FROM SCHOOL_YEARS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
//                                    $fy_id = $fy_id[1]['MARKING_PERIOD_ID'];
//
//                                    if(!UserPeriod())
//                                    $_SESSION['UserPeriod'] = $RET[1]['PERIOD_ID'];
//                                    if(!UserCoursePeriod())
//                                    $_SESSION['UserCoursePeriod'] = $RET[1]['COURSE_PERIOD_ID'];
//
//                            echo "<SELECT name=period onChange='document.forms[0].submit();'>";
//                                    foreach($RET as $period)
//                                    {
//                                      echo "<OPTION id=$period[COURSE_PERIOD_ID] value=$period[COURSE_PERIOD_ID]".((UserCoursePeriod()==$period['COURSE_PERIOD_ID'])?' SELECTED':'').">".$period['SHORT_NAME'].($period['MARKING_PERIOD_ID']!=$fy_id?' '.GetMP($period['MARKING_PERIOD_ID'],'SHORT_NAME'):'').(strlen($period['DAYS'])<5?' '.$period['DAYS']:'').' - '.$period['COURSE_TITLE']."</OPTION>";
//                                      if(UserCoursePeriod()==$period['COURSE_PERIOD_ID'])
//                                      {
//                                            #if($period['SCHOOL_ID']!=UserSchool())
//                                        #    unset($_SESSION['UserMP']);
//                                            $_SESSION['UserSchool'] = $period['SCHOOL_ID'];
//                                            $_SESSION['UserPeriod'] = $period['PERIOD_ID'];
//                                      }
//                                    }
//                                    echo "</SELECT>&nbsp;";
//                                   }
								
	
//For Marking Period
echo "<td><FORM name=head_frm id=head_frm action=Side.php?modfunc=update&btnn=$btn&nsc=$ns method=POST>
<INPUT type=hidden name=modcat value='' id=modcat_input>";
		
$RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE FROM SCHOOL_QUARTERS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER"));
if(!isset($_SESSION['UserMP']))
    $_SESSION['UserMP'] = GetCurrentMP('QTR',DBDate());

if(!$RET)
{
    $RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE FROM SCHOOL_SEMESTERS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER"));
    if(!isset($_SESSION['UserMP']))
        $_SESSION['UserMP'] = GetCurrentMP('SEM',DBDate());
}

if(!$RET)
{
    $RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE FROM SCHOOL_YEARS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER"));
    if(!isset($_SESSION['UserMP']))
        $_SESSION['UserMP'] = GetCurrentMP('FY',DBDate());
}
	#print_r($RET);
echo "<SELECT name=mp onChange='this.form.submit();'>";
if(count($RET))
{
    if(!UserMP())
        $_SESSION['UserMP'] = $RET[1]['MARKING_PERIOD_ID'];
    foreach($RET as $quarter)
        echo "<OPTION value=$quarter[MARKING_PERIOD_ID]".(UserMP()==$quarter['MARKING_PERIOD_ID']?' SELECTED':'').">".$quarter['TITLE']."</OPTION>";
}
echo "</SELECT>";
//Marking Period

echo '</FORM></td></tr></table></div>';
echo "</td></tr></table></td></tr>";
}##################Porfile Not Teacher End##########################################

if(UserStudentID() && User('PROFILE')!='parent' && User('PROFILE')!='student')
{
	$RET = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME,MIDDLE_NAME,NAME_SUFFIX FROM STUDENTS WHERE STUDENT_ID='".UserStudentID()."'"));
}
if(UserStaffID() && User('PROFILE')=='admin')
{
	if(UserStudentID())
	$RET = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME FROM STAFF WHERE STAFF_ID='".UserStaffID()."'"));
}

echo "

<tr>
            <td class=\"content\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                <tr>
                  <td align=\"center\" ><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"content_wrapper\">
                      <tr>
                        <td class=\"menubar\" style='padding-left:20px; padding-right:20px;'>";

#------------------------------------------------------------ menu Scroller _---------------------------------------------

  echo "  	  <div class='scrollable'>
	 <div id='cdnavheader'>
      <ul id='thumbs'>
";

 #------------------------------------------------------------ menu Scroller ends _---------------------------------------------

require('Menu.php');
echo "<li><a style='cursor:hand;' href='#' onmouseup='check_content(\"ajax.php?modname=misc/Portal.php\");' target=body onmousedown='document.getElementById(\"header\").innerHTML = \"Home\";document.getElementById(\"cframe\").src = \"Bottom.php?modcat=home\"'><span>" ."Home". "</span></a></li>";
foreach($_openSIS['Menu'] as $modcat=>$programs)
{
	if(count($_openSIS['Menu'][$modcat]))
	{
		$keys = array_keys($_openSIS['Menu'][$modcat]);
		$menu = false;
		foreach($keys as $key_index=>$file)
		{
			if(!is_numeric($file))
				$menu = true;
		}
		if(!$menu)
			continue;

		if(User('PROFILE')!='admin' && $modcat=="School_Setup")
		{
			echo "<li><a style='cursor:hand;' HREF=# onmouseup='check_content(\"ajax.php?modname=$modcat/Search.php\");' target=body onmousedown='document.getElementById(\"header\").innerHTML = \"".str_replace('_',' ',$modcat)."\"'><span>School Info</span></a></li>";
		}
		elseif(User('PROFILE')!='admin' && $modcat=="Users")
		{
			echo "<li><a style='cursor:hand;' HREF=# onmouseup='check_content(\"ajax.php?modname=$modcat/Search.php\");' target=body onmousedown='document.getElementById(\"header\").innerHTML = \"".str_replace('_',' ',$modcat)."\"'><span>My Info</span></a></li>";
		}
		elseif(User('PROFILE')=='student' && $modcat=="Students")
		{
			echo "<li><a style='cursor:hand;' HREF=# onmouseup='check_content(\"ajax.php?modname=$modcat/Search.php\");' target=body onmousedown='document.getElementById(\"header\").innerHTML = \"".str_replace('_',' ',$modcat)."\"'><span>My Info</span></a></li>";
		}
		elseif(User('PROFILE')=='student' && $modcat=="Scheduling")
		{
			echo "<li><a style='cursor:hand;' HREF=# onmouseup='check_content(\"ajax.php?modname=$modcat/Search.php\");' target=body onmousedown='document.getElementById(\"header\").innerHTML = \"".str_replace('_',' ',$modcat)."\"'><span>Schedule</span></a></li>";
		}
		else
		{
			echo "<li><a style='cursor:hand;' HREF=# onmouseup='check_content(\"ajax.php?modname=$modcat/Search.php\");' target=body onmousedown='document.getElementById(\"header\").innerHTML = \"".str_replace('_',' ',$modcat)."\";document.getElementById(\"cframe\").src = \"Bottom.php?modcat=".$modcat."\"'><span>".str_replace('_',' ',$modcat)."</span></a></li>";
		}


}
}

  #------------------------------------------------------------ menu Scroller _---------------------------------------------

echo"</ul></div></div>";




  echo "  </td></tr>";
 #------------------------------------------------------------ menu Scroller ends _---------------------------------------------



echo "<tr>
                        <td class=\"submenubar_bg\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                            <tr>
                              <td class=\"submenubar\" style='padding-left:10px; padding-right:10px;'>";
							  
echo "<div id='submenu_1' style='display:none;'>

<table cellspacing=0 celpadding=0 border=0 width=100%><tr><td width=60% valign=middle class='welcome'><b>Welcome to openSIS Student Information System</b></td><td width=40% class='version'>Version : ".$openSISVersion." | Release Date : ".$builddate."</td></tr></table> 

</div>"; 
$i = 2;
foreach($_openSIS['Menu'] as $modcat=>$programs)
{

	if(count($_openSIS['Menu'][$modcat]))
	{
		$keys = array_keys($_openSIS['Menu'][$modcat]);
		$menu = false;
		foreach($keys as $key_index=>$file)
		{
			if(!is_numeric($file))
				$menu = true;
		}
		if(!$menu)
			continue;



echo "<div id='submenu_".$i."' style='display:none'>"; 


		
		$int=0;
		$mm = 0;
		foreach($keys as $key_index=>$file)
		{
			
				
			$int = $int+1; 
			
			
		 				
			//if($_GET["student_id"]=="new")
			if(optional_param('student_id','',PARAM_ALPHANUM)=="new")
			
			{
				if($modcat=="Students")
		 	{	
		 	 	if($int==2)
		 	 	{
				$style="class='submenu_link'";
				}
				else
				{
				$style="class='submenuitem'";
				}
			}	
			
			}
			//elseif($_REQUEST["staff_id"]=="new")
			elseif(optional_param("staff_id",'',PARAM_ALPHANUM)=="new")
			{
			 
			 if($modcat=="Users")
		 	{	
		 	 	if($int==2)
		 	 	{
				$style="class='submenu_link'";
				}
				else
				{
				$style="class='submenuitem'";
				}
			}	
			
			 
			 
			 
			
			}else
			{
				//if($_REQUEST['modname']==$file)
				if(optional_param('modname','',PARAM_URL)==$file)
			{	
				$style="class='submenu_link'";

			}else
			{
			$style="class='submenuitem'";
			}
		
		}
		
		 	
		 	
			$title = $_openSIS['Menu'][$modcat][$file];

		
			
					
			if($mm==0)
			{
			if(substr($file,0,7)=='http://')
				echo "<A ".$style." HREF=$file target=body >$title</A> &nbsp;&nbsp;|&nbsp;&nbsp;";
			elseif(substr($file,0,7)=='HTTP://')
				echo "<A ".$style." HREF=$file target=_blank>$title</A> &nbsp;&nbsp;|&nbsp;&nbsp;";
			elseif(!is_numeric($file))
				if(User('PROFILE')=='student' && $title=="Student Info"){
				echo "<A ".$style." id=hm HREF=# onClick='check_content(\"ajax.php?modname=".$file." \");' target=body onmousedown='document.getElementById(\"header\").innerHTML = \"".str_replace('_',' ',$modcat)." >> "."$title\"' onmouseup=\"changeColors(); this.className='submenu_link'; document.getElementById('cframe').src='Bottom.php?modname=".$file."';\">My Information</A> &nbsp;&nbsp;|&nbsp;&nbsp;";	
				}
				elseif(User('PROFILE')=='student' && $title=="Schedule"){
				echo "<A ".$style." id=hm HREF=# onClick='check_content(\"ajax.php?modname=".$file." \");' target=body onmousedown='document.getElementById(\"header\").innerHTML = \"".str_replace('_',' ',$modcat)." >> "."$title\"' onmouseup=\"changeColors(); this.className='submenu_link'; document.getElementById('cframe').src='Bottom.php?modname=".$file."';\">My Schedule</A> &nbsp;&nbsp;|&nbsp;&nbsp;";	
				}				
				elseif(User('PROFILE')=='student' && $title=="Student Requests"){
				echo "<A ".$style." id=hm HREF=# onClick='check_content(\"ajax.php?modname=".$file." \");' target=body onmousedown='document.getElementById(\"header\").innerHTML = \"".str_replace('_',' ',$modcat)." >> "."$title\"' onmouseup=\"changeColors(); this.className='submenu_link'; document.getElementById('cframe').src='Bottom.php?modname=".$file."';\">My Requests</A> &nbsp;&nbsp;|&nbsp;&nbsp;";	
				}
				else
				{
				echo "<A ".$style." id=hm HREF=# onClick='check_content(\"ajax.php?modname=".$file." \");' target=body onmousedown='document.getElementById(\"header\").innerHTML = \"".str_replace('_',' ',$modcat)." >> "."$title\"' onmouseup=\"changeColors(); this.className='submenu_link'; document.getElementById('cframe').src='Bottom.php?modname=".str_replace('&', '?',$file)."';\">$title</A> &nbsp;&nbsp;|&nbsp;&nbsp;";
				}
			elseif($keys[$key_index+1] && !is_numeric($keys[$key_index+1]))
			{
			 $mm=$mm+1;
			echo '<label class="dd_menuitem" id="mm_'.$modcat.'_'.$mm.'"><b>'.$title.'</b>&nbsp;<img src="themes/'.trim($css).'/mnu_drpdwn.gif" />&nbsp;&nbsp;|&nbsp;&nbsp;</label>&nbsp;'.'<div id="menu_child_'.$modcat.'_'.$mm.'" style="position: absolute; visibility: hidden; width:200px;">';
			}

			}elseif($mm>0)
			{
			$menumm = $mm; 
			if(substr($file,0,7)=='http://')
				echo "<A id=dd class='dd_submenuitem' HREF=$file target=body >$title</A>";
			elseif(substr($file,0,7)=='HTTP://')
				echo "<A id=dd class='dd_submenuitem' HREF=$file target=_blank>$title</A>";
			elseif(!is_numeric($file))
				echo "<A id=dd class='dd_submenuitem' HREF=# onClick='check_content(\"ajax.php?modname=".$file." \");' target=body onmousedown='document.getElementById(\"header\").innerHTML = \"".str_replace('_',' ',$modcat)." >> "."$title\"' onmouseup=\"document.getElementById('cframe').src='Bottom.php?modname=".$file."';\">$title</A>";
			elseif($keys[$key_index+1] && !is_numeric($keys[$key_index+1]))
			{
			 $mm=$mm+1;
			echo '</div><label class="dd_menuitem" id="mm_'.$modcat.'_'.$mm.'"><b>'.$title.'</b>&nbsp;<img src="themes/'.trim($css).'/mnu_drpdwn.gif" />&nbsp;&nbsp;|&nbsp;&nbsp;</label>'.'<div id="menu_child_'.$modcat.'_'.$mm.'" style="position: absolute; visibility: hidden; width=200px;">';
			}
				
				echo '<script type="text/javascript">
createmenu("mm_'.$modcat.'_'.$menumm.'", "menu_child_'.$modcat.'_'.$menumm.'", "hover", "y", "pointer");
</script>';
			}
		
					
						
			

			
			
			echo "</b>";
	
		}
		
		

		
		echo "</div></div></DIV>";
		$i=$i+1;
	}
	
	
}	
echo "	</div> ";
echo '</tr></table></td></tr>';	
	

echo "
<tr >
                        <td class=\"pageheading_bg\"><div class=heading><table width='100%' cellpadding='0' cellspacing='0'><tr><td valign='top'>";


					
echo "				<div class=\"page_heading_breadcrumb\"><label id='header' name='header'></label>&nbsp;</td><td>";
## for opera echo '<div id="showhelp"><a href="javascript:void(0);" onclick="inter=setInterval(\'ShowBox(helpdiv, 380, 503, 630, 188, showhelp)\',1);return false;"><b>Help</b></a></div>';
echo '<div id="showhelp" style="padding-top:33px;"><a href="javascript:void(0);" onclick="inter=setInterval(\'ShowBox(helpdiv, 380, 499, 499, 211, showhelp)\',1);return false;"><b>Help</b></a> | <a href=# target="_blank"><b>Online Help</b></a></div></td></tr></table>';

echo '
	<div style="height:0px; margin-top:-6px; width=0px; position: absolute; overflow:hidden; visibility: hidden; text-align:left; " id="helpdiv">

<div class="help_div">
<table width=100% border=0 cellspacing=0 cellpadding=0><tr><td align=right style=" height:25px;  padding-right:5px;"><a href="javascript:void(0);" onclick="inter=setInterval(\'HideBox(helpdiv, showhelp)\',1);return false;"><b>Hide Help</b></a> | <a href=# target="_blank"><b>Online Help</b></a></td></tr></table>
</div>
<div style="background-image:url(themes/Black/help_top.gif); width:495px; height:17px;"></div>
<iframe id="cframe" src="'.str_replace('Modules', 'Bottom',  str_replace('&amp;', '?',PreparePHP_SELF($_REQUEST))).'" width="493" height=194px frameborder="0" scrolling="no" style="background-image:url(themes/Black/help_bg.gif); width:495px; background-repeat:repeat-y; background-color:transparent; text-align:left " >
</iframe>
<div style="background-image:url(themes/Black/help_bottom.gif); background-repeat:no-repeat; width:495px; height:10px;"></div>

</div>
';


echo "</div></div>";


echo "</td>
                      </tr>
<tr>
                        <td  valign=\"top\" class=\"txt_container_bg\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                            <tr>
                              <td class=\"txt_bg\">
							  
							  <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                              <tr>
                              <td class=\"txt_container\" valign=\"top\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                              <tr>
                              <td class=\"txt_padding\">
"	;

echo "<div id='content' name='content'>";

//For Student or User Informat

//=====================   For changing student id =====================


 #$admin_COMMON=" FROM STUDENTS s,STUDENT_ENROLLMENT ssm,ADDRESS a WHERE s.STUDENT_ID=ssm.STUDENT_ID  AND a.STUDENT_ID=s.STUDENT_ID AND ssm.SYEAR=".UserSyear()." AND ssm.SCHOOL_ID=".UserSchool()." ";
 if(User('PROFILE')=='admin')
	{
		$admin_COMMON_FROM=" FROM STUDENTS s, ADDRESS a,STUDENT_ENROLLMENT ssm ";
	   $admin_COMMON_WHERE=" WHERE s.STUDENT_ID=ssm.STUDENT_ID  AND a.STUDENT_ID=s.STUDENT_ID AND ssm.SYEAR=".UserSyear()." AND ssm.SCHOOL_ID=".UserSchool()." ";
	  // if($_REQUEST['mp_comment'] || $_SESSION['smc'])
	   if(optional_param('mp_comment','',PARAM_NOTAGS) || $_SESSION['smc'])
		{
			$admin_COMMON_FROM .=" ,STUDENT_MP_COMMENTS smc";
			$admin_COMMON_WHERE .=" AND smc.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['smc']='1';
		}
		  //if($_REQUEST['goal_description'] || $_REQUEST['goal_title'] || $_SESSION['g'])
		  if(optional_param('goal_description','',PARAM_NOTAGS) || optional_param('goal_title','',PARAM_NOTAGS) || $_SESSION['g'])
		{
			$admin_COMMON_FROM .=" ,GOAL g ";
			$admin_COMMON_WHERE .=" AND g.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['g']='1';
		}
		  //if($_REQUEST['progress_name'] || $_REQUEST['progress_description'] || $_SESSION['p'])
		  if(optional_param('progress_name','',PARAM_NOTAGS) ||optional_param('progress_description','',PARAM_NOTAGS) || $_SESSION['p'])
		{
			$admin_COMMON_FROM .=" ,PROGRESS p ";
			$admin_COMMON_WHERE .=" AND p.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['p']='1';
		}
		  //if($_REQUEST['doctors_note_comments'] || $_REQUEST['med_day'] || $_REQUEST['med_month'] || $_REQUEST['med_year'] || $_SESSION['smn'])
		  if(optional_param('doctors_note_comments','',PARAM_NOTAGS) || optional_param('med_day','',PARAM_NOTAGS) || optional_param('med_month','',PARAM_NOTAGS) || optional_param('med_year','',PARAM_NOTAGS) || $_SESSION['smn'])
		{
			$admin_COMMON_FROM .=" ,STUDENT_MEDICAL_NOTES smn ";
			$admin_COMMON_WHERE .=" AND smn.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['smn']='1';
		}
		  //if($_REQUEST['type'] || $_REQUEST['imm_comments'] || $_REQUEST['imm_day'] || $_REQUEST['imm_month'] || $_REQUEST['imm_year'] || $_SESSION['sm'])
		  if(optional_param('type','',PARAM_NOTAGS) || optional_param('imm_comments','',PARAM_NOTAGS) || optional_param('imm_day','',PARAM_NOTAGS) || optional_param('imm_month','',PARAM_NOTAGS) || optional_param('imm_year','',PARAM_NOTAGS) || $_SESSION['sm'])
		{
			$admin_COMMON_FROM .=" ,STUDENT_MEDICAL sm ";
			$admin_COMMON_WHERE .=" AND sm.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['sm']='1';
	
		}
		 // if($_REQUEST['ma_day'] || $_REQUEST['ma_month'] || $_REQUEST['ma_year'] || $_REQUEST['med_alrt_title'] || $_SESSION['sma'])
		  
		  if(optional_param('ma_day','',PARAM_NOTAGS) || optional_param('ma_month','',PARAM_NOTAGS) ||optional_param('ma_year','',PARAM_NOTAGS) || optional_param('med_alrt_title','',PARAM_NOTAGS) || $_SESSION['sma'])
		{
			$admin_COMMON_FROM .=" ,STUDENT_MEDICAL_ALERTS sma  ";
			$admin_COMMON_WHERE .=" AND sma.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['sma']='1';
	
		}
		// if($_REQUEST['nv_day'] || $_REQUEST['nv_month'] || $_REQUEST['nv_year'] || $_REQUEST['reason'] || $_REQUEST['result'] || $_REQUEST['med_vist_comments'] || $_SESSION['smv'])
		  if(optional_param('nv_day','',PARAM_NOTAGS) || optional_param('nv_month','',PARAM_NOTAGS) ||optional_param('nv_year','',PARAM_NOTAGS) || optional_param('reason','',PARAM_NOTAGS) || optional_param('result','',PARAM_NOTAGS)||  optional_param('med_vist_comments','',PARAM_NOTAGS) || $_SESSION['smv'])
		{
			$admin_COMMON_FROM .=" ,STUDENT_MEDICAL_VISITS smv   ";
			$admin_COMMON_WHERE .=" AND smv.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['smv']='1';
		}
		$admin_COMMON= $admin_COMMON_FROM . $admin_COMMON_WHERE;
  }
 
if(User('PROFILE')=='teacher')
	{
		   $teacher_COMMON_FROM=" FROM STUDENTS s, STUDENT_ENROLLMENT ssm, COURSE_PERIODS cp,
	SCHEDULE ss,ADDRESS a ";
	   $teacher_COMMON_WHERE=" WHERE a.STUDENT_ID=s.STUDENT_ID AND s.STUDENT_ID=ssm.STUDENT_ID AND ssm.STUDENT_ID=ss.STUDENT_ID AND ssm.SYEAR=cp.SYEAR AND ssm.SYEAR=ss.SYEAR AND cp.COURSE_ID=ss.COURSE_ID AND cp.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID AND ss.MARKING_PERIOD_ID IN (".GetAllMP('',$queryMP).")
						AND (cp.TEACHER_ID='".User('STAFF_ID')."' OR cp.SECONDARY_TEACHER_ID='".User('STAFF_ID')."') AND cp.COURSE_PERIOD_ID='".UserCoursePeriod()."' AND (ssm.START_DATE IS NOT NULL AND ('".DBDate()."'<=ssm.END_DATE OR ssm.END_DATE IS NULL)) AND ssm.SYEAR=".UserSyear()." AND ssm.SCHOOL_ID=".UserSchool()." ";
						
	  // if($_REQUEST['mp_comment'] || $_SESSION['smc'])
	   if(optional_param('mp_comment','',PARAM_SPCL) || $_SESSION['smc'])
		{
			$teacher_COMMON_FROM .=" ,STUDENT_MP_COMMENTS smc";
			$teacher_COMMON_WHERE .=" AND smc.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['smc']='1';
		}
		  //if($_REQUEST['goal_description'] || $_REQUEST['goal_title'] || $_SESSION['g'])
		  if(optional_param('goal_description','',PARAM_SPCL) || optional_param('goal_title','',PARAM_SPCL) || $_SESSION['g'])
		{
			$teacher_COMMON_FROM .=" ,GOAL g ";
			$teacher_COMMON_WHERE .=" AND g.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['g']='1';
		}
		  //if($_REQUEST['progress_name'] || $_REQUEST['progress_description'] || $_SESSION['p'])
		  if(optional_param('progress_name','',PARAM_NOTAGS) || optional_param('progress_description','',PARAM_NOTAGS) || $_SESSION['p'])
		{
			$teacher_COMMON_FROM .=" ,PROGRESS p ";
			$teacher_COMMON_WHERE .=" AND p.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['p']='1';
		}
		  //if($_REQUEST['doctors_note_comments'] || $_REQUEST['med_day'] || $_REQUEST['med_month'] || $_REQUEST['med_year'] || $_SESSION['smn'])
		   if(optional_param('doctors_note_comments','',PARAM_NOTAGS) || optional_param('med_day','',PARAM_NOTAGS) || optional_param('med_month','',PARAM_NOTAGS) ||optional_param('med_year','',PARAM_NOTAGS) || $_SESSION['smn'])
		{
			$teacher_COMMON_FROM .=" ,STUDENT_MEDICAL_NOTES smn ";
			$teacher_COMMON_WHERE .=" AND smn.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['smn']='1';
		}
		  //if($_REQUEST['type'] || $_REQUEST['imm_comments'] || $_REQUEST['imm_day'] || $_REQUEST['imm_month'] || $_REQUEST['imm_year'] || $_SESSION['sm'])
		  if(optional_param('type','',PARAM_NOTAGS) || optional_param('imm_comments','',PARAM_NOTAGS) ||optional_param('imm_day','',PARAM_NOTAGS) || optional_param('imm_month','',PARAM_NOTAGS) ||optional_param('imm_year','',PARAM_NOTAGS) || $_SESSION['sm'])
		{
			$teacher_COMMON_FROM .=" ,STUDENT_MEDICAL sm ";
			$teacher_COMMON_WHERE .=" AND sm.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['sm']='1';
	
		}
		 // if($_REQUEST['ma_day'] || $_REQUEST['ma_month'] || $_REQUEST['ma_year'] || $_REQUEST['med_alrt_title'] || $_SESSION['sma'])
		  if(optional_param('ma_day','',PARAM_NOTAGS) || optional_param('ma_month','',PARAM_NOTAGS) || optional_param('ma_year','',PARAM_NOTAGS) || optional_param('med_alrt_title','',PARAM_NOTAGS) || $_SESSION['sma'])
		{
			$teacher_COMMON_FROM .=" ,STUDENT_MEDICAL_ALERTS sma  ";
			$teacher_COMMON_WHERE .=" AND sma.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['sma']='1';
	
		}
		  //if($_REQUEST['nv_day'] || $_REQUEST['nv_month'] || $_REQUEST['nv_year'] || $_REQUEST['reason'] || $_REQUEST['result'] || $_REQUEST['med_vist_comments'] || $_SESSION['smv'])
		  if(optional_param('nv_day','',PARAM_NOTAGS) || optional_param('nv_month','',PARAM_NOTAGS) || optional_param('nv_year','',PARAM_NOTAGS) || optional_param('reason','',PARAM_NOTAGS) || optional_param('result','',PARAM_NOTAGS) || optional_param('med_vist_comments','',PARAM_NOTAGS) || $_SESSION['smv'])
		{
			$teacher_COMMON_FROM .=" ,STUDENT_MEDICAL_VISITS smv   ";
			$teacher_COMMON_WHERE .=" AND smv.STUDENT_ID=s.STUDENT_ID ";
			$_SESSION['smv']='1';
		}
		$teacher_COMMON= $teacher_COMMON_FROM . $teacher_COMMON_WHERE;
 }
 
/*  $teacher_COMMON="  FROM STUDENTS s, STUDENT_ENROLLMENT ssm, COURSE_PERIODS cp,
SCHEDULE ss
 WHERE s.STUDENT_ID=ssm.STUDENT_ID AND ssm.STUDENT_ID=ss.STUDENT_ID AND ssm.SYEAR=cp.SYEAR AND ssm.SYEAR=ss.SYEAR AND cp.COURSE_ID=ss.COURSE_ID AND cp.COURSE_PERIOD_ID=ss.COURSE_PERIOD_ID AND ss.MARKING_PERIOD_ID IN (".GetAllMP('',$queryMP).")
					AND cp.TEACHER_ID='".User('STAFF_ID')."' AND cp.COURSE_PERIOD_ID='".UserCoursePeriod()."' AND (ssm.START_DATE IS NOT NULL AND ('".DBDate()."'<=ssm.END_DATE OR ssm.END_DATE IS NULL)) AND ssm.SYEAR=".UserSyear()." AND ssm.SCHOOL_ID=".UserSchool()." ";
*/					
	if($_REQUEST['v'] && isset($_REQUEST['student_id']) && UserStudentID())
	{
		//$val=$_REQUEST['v'];
		$val=optional_param('v',0,PARAM_INT);
		if($val==1)
		 {
		 	if(User('PROFILE')=='admin')
			 {
			$s_id=DBGet(DBQuery("SELECT s.STUDENT_ID ".$admin_COMMON.$_SESSION['s']." ORDER BY s.LAST_NAME ASC LIMIT 1 "));
			 }
			 elseif(User('PROFILE')=='teacher')
			 {
			 $s_id=DBGet(DBQuery("SELECT s.STUDENT_ID ".$teacher_COMMON.$_SESSION['s']." ORDER BY s.LAST_NAME ASC LIMIT 1 "));
			 }
			unset($_SESSION['student_id']);
			$_SESSION['student_id']=$s_id[1]['STUDENT_ID'];
		 }
		elseif($val==2)
		 {
			if(User('PROFILE')=='admin')
			 {
			$s_ln = DBGet(DBQuery("SELECT LAST_NAME ".$admin_COMMON." AND s.STUDENT_ID =".UserStudentID()." ".$_SESSION['s']));
			$ln = $s_ln[1]['LAST_NAME'];
			$s_id=DBGet(DBQuery("SELECT s.STUDENT_ID ".$admin_COMMON." AND s.LAST_NAME <'".$ln."' ".$_SESSION['s']." ORDER BY s.LAST_NAME DESC LIMIT 1"));
			 }
			 elseif(User('PROFILE')=='teacher')
			 {
			 	$s_ln = DBGet(DBQuery("SELECT LAST_NAME ".$teacher_COMMON." AND s.STUDENT_ID =".UserStudentID()." ".$_SESSION['s']));
			$ln = $s_ln[1]['LAST_NAME'];
			$s_id=DBGet(DBQuery("SELECT s.STUDENT_ID ".$teacher_COMMON." AND s.LAST_NAME <'".$ln."' ".$_SESSION['s']." ORDER BY s.LAST_NAME DESC LIMIT 1"));
			 }
			unset($_SESSION['student_id']);
			$_SESSION['student_id']=$s_id[1]['STUDENT_ID'];
		 }
		elseif($val==3)
		 {
			if(User('PROFILE')=='admin')
			 {
			$s_ln = DBGet(DBQuery("SELECT LAST_NAME ".$admin_COMMON." AND s.STUDENT_ID =".UserStudentID()." ".$_SESSION['s']));
			$ln = $s_ln[1]['LAST_NAME'];
			$s_id=DBGet(DBQuery("SELECT s.STUDENT_ID ".$admin_COMMON." AND s.LAST_NAME >'".$ln."' ".$_SESSION['s']." ORDER BY s.LAST_NAME, FIRST_NAME ASC LIMIT 1"));
			 }
			 elseif(User('PROFILE')=='teacher')
			 {
			 $s_ln = DBGet(DBQuery("SELECT LAST_NAME ".$teacher_COMMON." AND s.STUDENT_ID =".UserStudentID()." ".$_SESSION['s']));
			$ln = $s_ln[1]['LAST_NAME'];
			$s_id=DBGet(DBQuery("SELECT s.STUDENT_ID ".$teacher_COMMON." AND s.LAST_NAME >'".$ln."' ".$_SESSION['s']." ORDER BY s.LAST_NAME, FIRST_NAME ASC LIMIT 1"));
			 }
			unset($_SESSION['student_id']);
			$_SESSION['student_id']=$s_id[1]['STUDENT_ID'];
		 }
		 elseif($val==4)
		 {
		 	if(User('PROFILE')=='admin')
			 {
			$s_id=DBGet(DBQuery("SELECT s.STUDENT_ID ".$admin_COMMON." ".$_SESSION['s']." ORDER BY s.LAST_NAME DESC LIMIT 1"));
			 }
			 elseif(User('PROFILE')=='admin')
			 {
			 $s_id=DBGet(DBQuery("SELECT s.STUDENT_ID ".$teacher_COMMON." ".$_SESSION['s']." ORDER BY s.LAST_NAME DESC LIMIT 1"));
			 }
			unset($_SESSION['student_id']);
			$_SESSION['student_id']=$s_id[1]['STUDENT_ID'];
		 }
	 }
 
 
 
//===================== End =============================================
 
if(UserStudentID() && User('PROFILE')!='parent' && User('PROFILE')!='student')
{
	$RET = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME,MIDDLE_NAME,NAME_SUFFIX FROM STUDENTS WHERE STUDENT_ID='".UserStudentID()."'"));
	$count_student_RET=DBGet(DBQuery("SELECT COUNT(*) AS NUM FROM STUDENTS"));
        if($count_student_RET[1]['NUM']>1){
	//DrawHeaderHome( 'Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Remove</font></A>) | <A HREF=Modules.php?modname=Students/Student.php&search_modfunc=list&next_modname=Students%2FStudent.php&ajax=true&bottom_back=true&return_session=true target=body>Back to Student List</A>');
	/*if($_REQUEST['modname']!='Attendance/Administration.php')
	{ 
	
	
	//DrawHeaderHome( 'Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Remove</font></A>) | <A HREF=Modules.php?modname='.$_REQUEST['modname'].'&search_modfunc=list&next_modname='.$_REQUEST['modname'].'&ajax=true&bottom_back=true&return_session=true target=body>Back to Student List</A>');
	
	
		DrawHeaderHome( 'Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.optional_param('modcat','',PARAM_NOTAGS).'><font color=red>Remove</font></A>) | <A HREF=Modules.php?modname='.optional_param('modname','',PARAM_NOTAGS).'&search_modfunc=list&next_modname='.optional_param('modname','',PARAM_NOTAGS).'&ajax=true&bottom_back=true&return_session=true target=body>Back to Student List</A>');
	
	}*/
        }else if($count_student_RET[1]['NUM']==1){
           // DrawHeaderHome( 'Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Remove</font></A>) ');
			
	 DrawHeaderHome( 'Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.optional_param('modcat','',PARAM_NOTAGS).'><font color=red>Remove</font></A>) ');
			
        }
}
if(UserStaffID() && User('PROFILE')=='admin')
{ 
	//if(UserStudentID())
	//	echo '<IMG SRC=assets/pixel_trans.gif height=2>';
	$RET = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME FROM STAFF WHERE STAFF_ID='".UserStaffID()."'"));
	//DrawHeaderHome( 'Selected User: '.$RET[1]['FIRST_NAME'].'&nbsp;'.$RET[1]['LAST_NAME'].' (<A HREF=Side.php?staff_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Remove</font></A>)');
	//DrawHeaderHome( 'Selected User: '.$RET[1]['FIRST_NAME'].'&nbsp;'.$RET[1]['LAST_NAME'].' (<A HREF=Side.php?staff_id=new&modcat='.optional_param('modcat','',PARAM_NOTAGS).'><font color=red>Remove</font></A>)');
}	

//
	

echo "<div id='update_panel'>";
echo "<center><div id='divErr'></div></center>";

	//if(strpos($_REQUEST['modname'],'misc/')===false && $_REQUEST['modname']!='Students/Student.php' && $_REQUEST['modname']!='School_Setup/Calendar.php' && $_REQUEST['modname']!='Scheduling/Schedule.php' && $_REQUEST['modname']!='Attendance/Percent.php' && $_REQUEST['modname']!='Attendance/Percent.php?list_by_day=true' && $_REQUEST['modname']!='Scheduling/MassRequests.php' && $_REQUEST['modname']!='Scheduling/MassSchedule.php' && $_REQUEST['modname']!='Student_Billing/Fees.php')
if(!isset($_REQUEST['_openSIS_PDF']))
{
		
	echo '<DIV id="Migoicons" style="visibility:hidden;position:absolute;z-index:1000;top:-100;"></DIV>';
	echo "<TABLE width=100% border=0 cellpadding=0><TR><TD valign=top align=center>";
}


if($_REQUEST['modname'])
{       /*******************back to list****************************/
        if($_REQUEST['bottom_back'] && $_SESSION['staff_id'])
                unset($_SESSION['staff_id']);
	if($_REQUEST['bottom_back'] && $_SESSION['student_id'])
            unset($_SESSION['student_id']);
        /************************************************/
        if($_REQUEST['_openSIS_PDF']=='true')
		ob_start();
	if(strpos($_REQUEST['modname'],'?')!==false)
	//if(strpos(optional_param('modname','',PARAM_NOTAGS),'?')!==false)
	{
		//$modname = substr($_REQUEST['modname'],0,strpos($_REQUEST['modname'],'?'));
		$modname = substr(optional_param('modname','',PARAM_NOTAGS),0,strpos(optional_param('modname','',PARAM_NOTAGS),'?'));
		//$vars = substr($_REQUEST['modname'],(strpos($_REQUEST['modname'],'?')+1));
		$vars = substr(optional_param('modname','',PARAM_NOTAGS),(strpos(optional_param('modname','',PARAM_NOTAGS),'?')+1));

		$vars = explode('?',$vars);
		foreach($vars as $code)
		{
			$code = explode('=',$code);
			$_REQUEST[$code[0]] = $code[1];
		}
	}
	else
		//$modname = $_REQUEST['modname'];
		$modname = optional_param('modname','',PARAM_NOTAGS);

	//if($_REQUEST['LO_save']!='1' && !isset($_REQUEST['_openSIS_PDF']) && (strpos($modname,'misc/')===false || $modname=='misc/Registration.php' || $modname=='misc/Export.php' || $modname=='misc/Portal.php'))
	if(optional_param('LO_save','',PARAM_INT)!='1' && !isset($_REQUEST['_openSIS_PDF']) && (strpos($modname,'misc/')===false || $modname=='misc/Registration.php' || $modname=='misc/Export.php' || $modname=='misc/Portal.php'))
		$_SESSION['_REQUEST_vars'] = $_REQUEST;

	$allowed = false;
	include 'Menu.php';
	foreach($_openSIS['Menu'] as $modcat=>$programs)
	{
		//if($_REQUEST['modname']==$modcat.'/Search.php')
		if(optional_param('modname','',PARAM_NOTAGS)==$modcat.'/Search.php')
		{
			$allowed = true;
			break;
		}
		foreach($programs as $program=>$title)
		{
			//if($_REQUEST['modname']==$program)
			if(optional_param('modname','',PARAM_NOTAGS)==$program)
			{
				$allowed = true;
				break;
			}
		}
	}
	//if(substr($_REQUEST['modname'],0,5)=='misc/')
	if(substr(optional_param('modname','',PARAM_NOTAGS),0,5)=='misc/')
		$allowed = true;

	if($allowed || $_SESSION['take_mssn_attn'])
	{
		//unset($_SESSION['take_mssn_attn']);
		if(Preferences('SEARCH')!='Y')
			$_REQUEST['search_modfunc'] = 'list';
		include('modules/'.$modname);
	}
	else
	{
		if(User('USERNAME'))
		{
			echo "You're not allowed to use this program! This attempted violation has been logged and your IP address was captured.";
			Warehouse('footer');
			
			if ($_SERVER['HTTP_X_FORWARDED_FOR']){
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			
			if($openSISNotifyAddress)
				mail($openSISNotifyAddress,'HACKING ATTEMPT',"INSERT INTO HACKING_LOG (HOST_NAME,IP_ADDRESS,LOGIN_DATE,VERSION,PHP_SELF,DOCUMENT_ROOT,SCRIPT_NAME,MODNAME,USERNAME) values('$_SERVER[SERVER_NAME]','$ip','".date('Y-m-d')."','$openSISVersion','$_SERVER[PHP_SELF]','$_SERVER[DOCUMENT_ROOT]','$_SERVER[SCRIPT_NAME]','$_REQUEST[modname]','".User('USERNAME')."')");
			if(false && function_exists('mysql_query'))
			
			
			/*if($openSISNotifyAddress)
				mail($openSISNotifyAddress,'HACKING ATTEMPT',"INSERT INTO HACKING_LOG (HOST_NAME,IP_ADDRESS,LOGIN_DATE,VERSION,PHP_SELF,DOCUMENT_ROOT,SCRIPT_NAME,MODNAME,USERNAME) values('$_SERVER[SERVER_NAME]','$_SERVER[REMOTE_ADDR]','".date('Y-m-d')."','$openSISVersion','$_SERVER[PHP_SELF]','$_SERVER[DOCUMENT_ROOT]','$_SERVER[SCRIPT_NAME]','$_REQUEST[modname]','".User('USERNAME')."')");
			if(false && function_exists('mysql_query'))*/
			{
				
			if ($_SERVER['HTTP_X_FORWARDED_FOR']){
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
				
				
				$link = @mysql_connect('os4ed.com','openSIS_log','openSIS_log');
				@mysql_select_db('openSIS_log');
				//@mysql_query("INSERT INTO HACKING_LOG (HOST_NAME,IP_ADDRESS,LOGIN_DATE,VERSION,PHP_SELF,DOCUMENT_ROOT,SCRIPT_NAME,MODNAME,USERNAME) values('$_SERVER[SERVER_NAME]','$ip','".date('Y-m-d')."','$openSISVersion','$_SERVER[PHP_SELF]','$_SERVER[DOCUMENT_ROOT]','$_SERVER[SCRIPT_NAME]','$_REQUEST[modname]','".User('USERNAME')."')");
				
				@mysql_query("INSERT INTO HACKING_LOG (HOST_NAME,IP_ADDRESS,LOGIN_DATE,VERSION,PHP_SELF,DOCUMENT_ROOT,SCRIPT_NAME,MODNAME,USERNAME) values('$_SERVER[SERVER_NAME]','$ip','".date('Y-m-d')."','$openSISVersion','$_SERVER[PHP_SELF]','$_SERVER[DOCUMENT_ROOT]','$_SERVER[SCRIPT_NAME]','".optional_param('modname','',PARAM_CLEAN)."','".User('USERNAME')."')");
				@mysql_close($link);
				
				
				/*$link = @mysql_connect('os4ed.com','openSIS_log','openSIS_log');
				@mysql_select_db('openSIS_log');
				@mysql_query("INSERT INTO HACKING_LOG (HOST_NAME,IP_ADDRESS,LOGIN_DATE,VERSION,PHP_SELF,DOCUMENT_ROOT,SCRIPT_NAME,MODNAME,USERNAME) values('$_SERVER[SERVER_NAME]','$_SERVER[REMOTE_ADDR]','".date('Y-m-d')."','$openSISVersion','$_SERVER[PHP_SELF]','$_SERVER[DOCUMENT_ROOT]','$_SERVER[SCRIPT_NAME]','$_REQUEST[modname]','".User('USERNAME')."')");
				@mysql_close($link);*/
			}
		}
		exit;
	}

	if($_SESSION['unset_student'])
	{
		unset($_SESSION['unset_student']);
		unset($_SESSION['staff_id']);
	}
}


if(!isset($_REQUEST['_openSIS_PDF']))
{
	echo '</TD></TR></TABLE>';
	for($i=1;$i<=$_openSIS['PrepareDate'];$i++)
	{
		echo '<script type="text/javascript">
    Calendar.setup({
        monthField     :    "monthSelect'.$i.'",
        dayField       :    "daySelect'.$i.'",
        yearField      :    "yearSelect'.$i.'",
        ifFormat       :    "%d-%b-%y",
        button         :    "trigger'.$i.'",
        align          :    "Tl",
        singleClick    :    true
    });
</script>';
	}


echo "</div>";

	echo "</td>
                                        </tr>
                                      </table></td>
                                  </tr>
                                </table></td>
                            </tr>
                          </table></td>
                      </tr>
                    </table></td>
                </tr>
              </table></td>
          </tr>

			<tr>
            <td class=\"footer\">
			<table width=\"100%\" border=\"0\">
  <tr>
    <td align='center' class='copyright'>
       <center>Copyright &copy; 2011 Open Solutions for Education, Inc. (<a href='http://www.os4ed.com' target='_blank'>OS4Ed</a>).
                openSIS is licensed under the <a href='http://www.gnu.org/licenses/gpl.html' target='_blank'>GPL License</a>.
                </center></td>
  </tr>
</table>
			</td>
          	</tr>
        </table></td>
    </tr>
  </table>
</center>
<div id='cal' style='position:absolute;'> </div>
</body>
</html>
	
	";

}


?>
