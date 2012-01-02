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

 $var1= explode("?",$_SERVER['REQUEST_URI']);

include("functions/ParamLib.php");
$url=validateQueryString($var1[1]);
if($url===FALSE || !$var1[1]){
 header('Location: index.php');
 }

//echo $_SERVER['REQUEST_URI'];
//exit;
error_reporting(1);
include "./Warehouse.php";

$tmp_REQUEST = $_REQUEST;
$_SESSION['Side_PHP_SELF'] = "Side.php";

$old_school = UserSchool();
$old_syear = UserSyear();
$old_period = UserCoursePeriod();
//$btnn = $_REQUEST['btnn'];
$btnn =optional_param('btnn','',PARAM_SPCL);
//$nsc = $_REQUEST['nsc'];
$nsc = optional_param('nsc','',PARAM_SPCL);
unset($_SESSION['smc']);
unset($_SESSION['g']);
unset($_SESSION['p']);
unset($_SESSION['smn']);
unset($_SESSION['sm']);
unset($_SESSION['sma']);
unset($_SESSION['smv']);
unset($_SESSION['s']);
unset($_SESSION['custom_count_sql']);
unset($_SESSION['inactive_stu_filter']);
unset($_SESSION['new_sql']);
unset($_SESSION['newsql']);
unset($_SESSION['newsql1']);
if(isset($_SESSION['stu_search']['sql'])){
    unset($_SESSION['stu_search']);
}
if(isset ($_SESSION['staf_search']['sql'])){
    unset ($_SESSION['staf_search']['sql']);
}

	
//if(($_REQUEST['school'] && $_REQUEST['school']!=$old_school) || ($_REQUEST['period'] && $_REQUEST['period']!=$old_period))
if((optional_param('school','',PARAM_SPCL) && optional_param('school','',PARAM_SPCL)!=$old_school) || (optional_param('period','',PARAM_SPCL) && optional_param('period','',PARAM_SPCL)!=$old_period))
{
	unset($_SESSION['student_id']);
	$_SESSION['unset_student'] = true;
	unset($_SESSION['staff_id']);
	//unset($_SESSION['UserMP']);
	unset($_REQUEST['mp']);
}

//if($_REQUEST['modfunc']=='update' && $_POST)
if(optional_param('modfunc','',PARAM_SPCL)=='update' && $_POST)
{
	
	//===================
		
	
	//if(User('PROFILE')=='teacher' &&($_REQUEST['school']!=$old_school || $nsc=='NT'))
	if(User('PROFILE')=='teacher' &&(optional_param('school','',PARAM_SPCL)!=$old_school || $nsc=='NT'))
 	{
		
		
		//if($_REQUEST['act']=='school')
		if(optional_param('act','',PARAM_SPCL)=='school')
		{
			//$_SESSION['UserSchool'] = $_REQUEST['school'];
			 $_SESSION['UserSchool'] = optional_param('school','',PARAM_SPCL);
 			DBQuery("UPDATE STAFF SET CURRENT_SCHOOL_ID='".UserSchool()."' WHERE STAFF_ID='".User('STAFF_ID')."'");
  		
                                        unset($_SESSION['UserSyear']);
		unset($_SESSION['UserMP']);
		unset($_SESSION['UserSubject']);
		unset($_SESSION['UserCourse']);
		unset($_SESSION['UserCoursePeriod']);
		
                                        $school_years_RET=DBGet(DBQuery("SELECT MAX(sy.SYEAR) AS SYEAR FROM SCHOOL_YEARS sy,STAFF s WHERE s.SYEAR=sy.SYEAR AND SCHOOL_ID=".UserSchool()." AND  s.USERNAME=(SELECT USERNAME FROM STAFF WHERE STAFF_ID='$_SESSION[STAFF_ID]')"));
                                        $_SESSION['UserSyear']=$school_years_RET[1]['SYEAR'];

                                        $RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE FROM SCHOOL_QUARTERS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER"));
                                        if(!isset($_SESSION['UserMP']))
                                        {
                                                $_SESSION['UserMP'] = GetCurrentMP('QTR',DBDate());
		}
                                        if(!$RET)
                                        {
                                            $RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE FROM SCHOOL_SEMESTERS WHERE SCHOOL_ID='" . UserSchool() . "' AND SYEAR='" . UserSyear() . "' ORDER BY SORT_ORDER"));
                                            if (!isset($_SESSION['UserMP'])) {
                                                $_SESSION['UserMP'] = GetCurrentMP('SEM', DBDate());
                                            }
                                        }

                                        if(!$RET)
                                        {
                                            $RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE FROM SCHOOL_YEARS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER"));
                                            if  (!isset($_SESSION['UserMP'])) {
                                                $_SESSION['UserMP'] = GetCurrentMP('FY', DBDate());
                                            }	
                                        }
		
		}
		//elseif($_REQUEST['act']=='syear')
		elseif(optional_param('act','',PARAM_SPCL)=='syear')
		{
                                        unset($_SESSION['student_id']);
                                        unset($_SESSION['_REQUEST_vars']['student_id']);
                                        $_SESSION['UserSyear'] = optional_param('syear','',PARAM_SPCL);
			unset($_SESSION['UserMP']);
			unset($_SESSION['UserSubject']);
			unset($_SESSION['UserCourse']);
			unset($_SESSION['UserCoursePeriod']);
                                        $RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE FROM SCHOOL_QUARTERS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER"));
                                        if(!isset($_SESSION['UserMP']))
                                        {
                                                $_SESSION['UserMP'] = GetCurrentMP('QTR',DBDate());
                                        }	
                                        if(!$RET)
                                        {
                                            $RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE FROM SCHOOL_SEMESTERS WHERE SCHOOL_ID='" . UserSchool() . "' AND SYEAR='" . UserSyear() . "' ORDER BY SORT_ORDER"));
                                            if (!isset($_SESSION['UserMP'])) {
                                                $_SESSION['UserMP'] = GetCurrentMP('SEM', DBDate());
                                            }
                                        }
			
                                        if(!$RET)
                                        {
                                            $RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE FROM SCHOOL_YEARS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER"));
                                            if  (!isset($_SESSION['UserMP'])) {
                                                $_SESSION['UserMP'] = GetCurrentMP('FY', DBDate());
		}
                                        }
			
		}
		//elseif($_REQUEST['act']=='mp')
		elseif(optional_param('act','',PARAM_SPCL)=='mp')
		{
			
			$_SESSION['UserMP'] = optional_param('mp','',PARAM_SPCL);
			unset($_SESSION['UserSubject']);
			unset($_SESSION['UserCourse']);
			unset($_SESSION['UserCoursePeriod']);
		}
		//elseif($_REQUEST['act']=='subject')
		elseif(optional_param('act','',PARAM_SPCL)=='subject')
		{
			$_SESSION['UserSubject'] = optional_param('subject','',PARAM_SPCL);
			unset($_SESSION['UserCourse']);
			unset($_SESSION['UserCoursePeriod']);
  		}
		//elseif($_REQUEST['act']=='course')
		elseif(optional_param('act','',PARAM_SPCL)=='course')
		{
			$_SESSION['UserCourse'] =clean_param($_REQUEST['course'],PARAM_NOTAGS);
			unset($_SESSION['UserCoursePeriod']);
		}
		//elseif($_REQUEST['act']=='period')
		elseif(optional_param('act','',PARAM_SPCL)=='period')
		{
			$_SESSION['UserCoursePeriod'] =optional_param('period','',PARAM_SPCL);
		}

	
	}
	
	//===================
 
                   if (User('PROFILE') == 'admin' && (clean_param($_REQUEST['school'], PARAM_NOTAGS) != $old_school || $nsc == 'NT')) 
                   {
                        if ($_POST['school']) 
                        {
                            $_SESSION['UserSchool'] = clean_param($_REQUEST['school'], PARAM_NOTAGS);
                            DBQuery("UPDATE STAFF SET CURRENT_SCHOOL_ID='" . UserSchool() . "' WHERE STAFF_ID='" . $_SESSION['STAFF_ID']. "'");
                            unset($_SESSION['UserSyear']);
                            unset($_SESSION['UserMP']);
 
                            $school_years_RET=DBGet(DBQuery("SELECT MAX(sy.SYEAR) AS SYEAR FROM SCHOOL_YEARS sy,STAFF s WHERE s.SYEAR=sy.SYEAR AND SCHOOL_ID=".UserSchool()." AND  s.USERNAME=(SELECT USERNAME FROM STAFF WHERE STAFF_ID='$_SESSION[STAFF_ID]')"));
                            $_SESSION['UserSyear']=$school_years_RET[1]['SYEAR'];
 
                            $RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE FROM SCHOOL_QUARTERS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER"));
                            if(!isset($_SESSION['UserMP']))
                            {
                                    $_SESSION['UserMP'] = GetCurrentMP('QTR',DBDate());
                            }	
                            if(!$RET)
                            {
                                $RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE FROM SCHOOL_SEMESTERS WHERE SCHOOL_ID='" . UserSchool() . "' AND SYEAR='" . UserSyear() . "' ORDER BY SORT_ORDER"));
                                if (!isset($_SESSION['UserMP'])) {
                                    $_SESSION['UserMP'] = GetCurrentMP('SEM', DBDate());
                                }
                            }
 
                            if(!$RET)
	{
                                $RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE FROM SCHOOL_YEARS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER"));
                                if  (!isset($_SESSION['UserMP'])) {
                                    $_SESSION['UserMP'] = GetCurrentMP('FY', DBDate());
	}
                            }
                        } 
	
                        elseif ($_POST['syear']) 
                        {
                            unset($_SESSION['student_id']);
                            unset($_SESSION['_REQUEST_vars']['student_id']);
                            $_SESSION['UserSyear'] = optional_param('syear', '', PARAM_SPCL);
                            unset($_SESSION['UserMP']);
                            $RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE FROM SCHOOL_QUARTERS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER"));
                            if(!isset($_SESSION['UserMP']))
                            {
                                    $_SESSION['UserMP'] = GetCurrentMP('QTR',DBDate());
                            }	
                            if(!$RET)
                            {
                                $RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE FROM SCHOOL_SEMESTERS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER"));
                                if(!isset($_SESSION['UserMP']))
                                {
                                        $_SESSION['UserMP'] = GetCurrentMP('SEM',DBDate());
                                }	
                            }

                            if(!$RET)
                            {
                                $RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE FROM SCHOOL_YEARS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER"));
                                if(!isset($_SESSION['UserMP']))
                                {
                                        $_SESSION['UserMP'] = GetCurrentMP('FY',DBDate());
                                }	
                            }
                        }
                        
                        elseif ($_POST['mp']) 
                        {
                            $_SESSION['UserMP'] = optional_param('mp', '', PARAM_SPCL);
                        }
                    }
	
	
//	if(User('PROFILE')=='admin' && $nsc=='TT')
//	{
//		// --------------------------------------------------------------- School Session Assign ------------------------------------------------------------------ //
//		$QI = DBQuery("SELECT ID,TITLE FROM SCHOOLS ORDER BY ID DESC");
//		$RET = DBGet($QI);
//		//print_r($RET);
//		//echo $RET[1]['ID'];
//		//echo "<br>";
//		//echo $RET[1]['TITLE'];
//		// --------------------------------------------------------------- School Session Assign ------------------------------------------------------------------ //
//		//$_SESSION['UserSchool'] = $_REQUEST['school'];
//		$_SESSION['UserSchool'] = $RET[1]['ID'];
//		DBQuery("UPDATE STAFF SET CURRENT_SCHOOL_ID='".UserSchool()."' WHERE STAFF_ID='".User('STAFF_ID')."'");
//	}

//        if(User('PROFILE')=='admin')
//         {
//           $school_years=DBGet(DBQuery("SELECT SYEAR FROM SCHOOL_YEARS WHERE SCHOOL_ID=".UserSchool()));
//           if(count($school_years)>1)
//            //$_SESSION['UserSyear'] = $_REQUEST['syear'];
//			 $_SESSION['UserSyear'] = optional_param('syear','',PARAM_SPCL);
//            else
//	   $_SESSION['UserSyear'] = $school_years[1]['SYEAR'];
//         }
//         else
//		 //$_SESSION['UserSyear'] = $_REQUEST['syear'];
// $_SESSION['UserSyear'] = optional_param('syear','',PARAM_SPCL);
//
//        // $_SESSION['UserCoursePeriod'] = $_REQUEST['period']
//        $_SESSION['UserCoursePeriod'] = optional_param('period','',PARAM_SPCL);
//		
//	//if($_REQUEST['mp'])	
//	if(optional_param('mp','',PARAM_SPCL))
//		//$_SESSION['UserMP'] = $_REQUEST['mp'];
//	      $_SESSION['UserMP'] = optional_param('mp','',PARAM_SPCL);
    if (User('PROFILE') == 'parent' || User('PROFILE')=='student') 
         {
        if ($_POST['syear'])
        {
            $_SESSION['UserSyear'] = optional_param('syear', '', PARAM_SPCL);
            unset ($_SESSION['UserMP']);
            $RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE FROM SCHOOL_QUARTERS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER"));
            if(!isset($_SESSION['UserMP']))
            {
                    $_SESSION['UserMP'] = GetCurrentMP('QTR',DBDate());
         }
            if(!$RET)
            {
                $RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE FROM SCHOOL_SEMESTERS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER"));
                if(!isset($_SESSION['UserMP']))
                {
                        $_SESSION['UserMP'] = GetCurrentMP('SEM',DBDate());
                }	
            }

            if(!$RET)
            {
                $RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE FROM SCHOOL_YEARS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER"));
                if(!isset($_SESSION['UserMP']))
                {
                        $_SESSION['UserMP'] = GetCurrentMP('FY',DBDate());
                }	
            }
		
        }
        elseif($_POST['student_id'])
	{
            unset ($_SESSION['UserMP']);
            $_SESSION['student_id'] = optional_param('student_id', '', PARAM_ALPHANUM);
        }
        elseif($_POST['mp'])
        {
             $_SESSION['UserMP'] = optional_param('mp', '', PARAM_SPCL);
        }
		//if($_SESSION['student_id']!=$_REQUEST['student_id'])
//            if ($_SESSION['student_id'] != optional_param('student_id', '', PARAM_ALPHANUM))
//                unset($_SESSION['UserMP']);
		//$_SESSION['student_id'] = $_REQUEST['student_id'];
        
	}
	echo "<script language=javascript>parent.body.location='".str_replace('&amp;','&',PreparePHP_SELF($_SESSION['_REQUEST_vars']))."';</script>";
}//---------------------------- Updaet End-------------------------------------------

//if(!$_SESSION['UserSyear'])
//	$_SESSION['UserSyear'] = $DefaultSyear;

if(!$_SESSION['student_id'] && User('PROFILE')=='student')
	$_SESSION['student_id'] = $_SESSION['STUDENT_ID'];

if(!$_SESSION['UserSchool'])
{
	if(User('PROFILE')=='admin' && (!User('SCHOOLS') || strpos(User('SCHOOLS'),','.User('CURRENT_SCHOOL_ID').',')!==false))
		$_SESSION['UserSchool'] = User('CURRENT_SCHOOL_ID');
	elseif(User('PROFILE')=='student')
		$_SESSION['UserSchool'] = trim(User('SCHOOLS'),',');
	elseif(User('PROFILE')=='teacher')
	{
		
            $mp = GetAllMP('QTR',UserMP());

            if(!isset($mp))
              $mp = GetAllMP('SEM',UserMP());

            if(!isset($mp))
              $mp = GetAllMP('FY',UserMP());
            
            
                $QI = DBQuery("SELECT cp.SCHOOL_ID FROM COURSE_PERIODS cp, SCHOOL_PERIODS sp,COURSES c WHERE c.COURSE_ID=cp.COURSE_ID AND cp.PERIOD_ID=sp.PERIOD_ID AND cp.SYEAR='".UserSyear()."' AND cp.TEACHER_ID='".User('STAFF_ID')."''".(UserMP()?' AND cp.MARKING_PERIOD_ID IN ('.$mp.')':'')."' ORDER BY sp.SORT_ORDER LIMIT 1");
		$RET = DBGet($QI);
		$_SESSION['UserSchool'] = $RET[1]['SCHOOL_ID'];
	}
}

/*if((!$_SESSION['UserMP'] || ($_REQUEST['school'] && $_REQUEST['school'] != $old_school) || ($_REQUEST['syear'] && $_REQUEST['syear'] != $old_syear) || ($_REQUEST['period'] && $_REQUEST['period'] != $old_period)) && User('PROFILE')!='parent'){
	$_SESSION['UserMP'] = GetCurrentMP('QTR',DBDate());
}*/
//if(($_REQUEST['school'] && $_REQUEST['school'] != $old_school) || ($_REQUEST['syear'] && $_REQUEST['syear'] != $old_syear))
if((optional_param('school','',PARAM_SPCL) && optional_param('school','',PARAM_SPCL) != $old_school) || (optional_param('syear','',PARAM_SPCL) && optional_param('syear','',PARAM_SPCL) != $old_syear))
{
	unset($_SESSION['UserPeriod']);
	unset($_SESSION['UserCoursePeriod']);
}

//if($_REQUEST['student_id']=='new')
if(optional_param('student_id','',PARAM_ALPHANUM)=='new')
{
	unset($_SESSION['student_id']);
	unset($_SESSION['_REQUEST_vars']['student_id']);
	unset($_SESSION['_REQUEST_vars']['search_modfunc']);
	
	#------------- New added ----------------------------#
	
	if($_SESSION['_REQUEST_vars']['modfunc']=='choose_course')
    	unset($_SESSION['_REQUEST_vars']['modfunc']);
	#------------- New added ----------------------------#
        #------------- New added on 09.12.2011 For removing old date----------------------------#
	if($_SESSION['_REQUEST_vars']['modname']=='Attendance/Administration.php')
                 {
                      unset($_SESSION['_REQUEST_vars']['month_date']);
                      unset($_SESSION['_REQUEST_vars']['day_date']);
                      unset($_SESSION['_REQUEST_vars']['year_date']);
                 }
        #------------- New added on 09.12.2011 ----------------------------#
	
	echo "<script language=javascript>parent.body.location='".str_replace('&amp;','&',PreparePHP_SELF($_SESSION['_REQUEST_vars']))."';</script>";
}
//if($_REQUEST['student_id']=='new')
if(optional_param('school_id','',PARAM_ALPHANUM)=='new')
{
	unset($_SESSION['UserSchool']);
	echo "<script language=javascript>parent.body.location='".str_replace('&amp;','&',PreparePHP_SELF($_SESSION['_REQUEST_vars']))."';</script>";
	/*
	unset($_SESSION['student_id']);
	unset($_SESSION['_REQUEST_vars']['student_id']);
	unset($_SESSION['_REQUEST_vars']['search_modfunc']);
	*/
}

//if($_REQUEST['staff_id']=='new')
if(optional_param('staff_id','',PARAM_ALPHANUM)=='new')
{
	unset($_SESSION['miss_attn']);
	unset($_SESSION['staff_id']);
	unset($_SESSION['_REQUEST_vars']['staff_id']);
	unset($_SESSION['_REQUEST_vars']['search_modfunc']);
	echo "<script language=javascript>parent.body.location='".str_replace('&amp;','&',PreparePHP_SELF($_SESSION['_REQUEST_vars']))."';</script>";
}
unset($_REQUEST['modfunc']);

echo "
<HTML>
	<head>
		<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />
		<meta http-equiv=\"Content-Style-Type\" content=\"text/css\" />
		<link rel=stylesheet type=\"text/css\" href=\"assets/stylesheet.css\">
		<script language=\"JavaScript\" type=\"text/javascript\">
		
		</script>
		<title>".Config('TITLE')."</title>
	</head>
	<BODY background=assets/themes/".Preferences('THEME')."/bg.jpg leftmargin=6 marginwidth=4 topmargin=0 ".(clean_param($_REQUEST['modcat'],PARAM_NOTAGS)?"onload=openMenu('".clean_param($_REQUEST['modcat'],PARAM_NOTAGS)."');":'').">";

// User Information
echo "<TABLE border=0 cellpadding=0 cellspacing=0 width=100%><TR><TD height=30>";
echo '<A HREF=index.php target=_top>'.DrawPNG('themes/'.Preferences('THEME').'/logo.png','border=0 width=160').'</A>';
echo "</TD></TR><TR>";
echo "<TD class=BoxContents style='border: inset #C9C9C9 2px; background-image:url(\"assets/bg.gif\")' width=100% valign=top>
	<FORM action=Side.php?modfunc=update method=POST>
	<INPUT type=hidden name=modcat value='' id=modcat_input>
	<b>".User('NAME')."</b>
	<BR>".date('l F j, Y')."
	<BR>";
if(User('PROFILE')=='admin')
{
	$schools = substr(str_replace(",","','",User('SCHOOLS')),2,-2);
	$QI = DBQuery("SELECT ID,TITLE FROM SCHOOLS".($schools?" WHERE ID IN ($schools)":''));
	$RET = DBGet($QI);

//	if(!UserSchool())
//	{
//		$_SESSION['UserSchool'] = $RET[1]['ID'];
//		DBQuery("UPDATE STAFF SET CURRENT_SCHOOL_ID='".UserSchool()."' WHERE STAFF_ID='".User('STAFF_ID')."'");
//	}

	echo "<SELECT name=school onChange='document.forms[0].submit();' style='width:150;'>";
	foreach($RET as $school)
		echo "<OPTION value=$school[ID]".((UserSchool()==$school['ID'])?' SELECTED':'').">".$school['TITLE']."</OPTION>";

	echo "</SELECT><BR>";
}

if(1)
{
if(User('PROFILE')!='student')
	$sql = "SELECT DISTINCT sy.SYEAR FROM SCHOOL_YEARS sy,STAFF s WHERE s.SYEAR=sy.SYEAR AND s.USERNAME=(SELECT USERNAME FROM STAFF WHERE STAFF_ID='$_SESSION[STAFF_ID]')";
else
	$sql = "SELECT DISTINCT sy.SYEAR FROM SCHOOL_YEARS sy,STUDENT_ENROLLMENT se WHERE se.SYEAR=sy.SYEAR AND se.STUDENT_ID='$_SESSION[STUDENT_ID]'";
$years_RET = DBGet(DBQuery($sql));
}
else
$years_RET = array(1=>array('SYEAR'=>"$DefaultSyear"));

echo "<SELECT name=syear onChange='document.forms[0].submit();'>";
foreach($years_RET as $year)
	echo "<OPTION value=$year[SYEAR]".((UserSyear()==$year['SYEAR'])?' SELECTED':'').">$year[SYEAR]-".($year['SYEAR']+1)."</OPTION>";
echo '</SELECT><BR>';

if(User('PROFILE')=='parent')
{
	$RET = DBGet(DBQuery("SELECT sju.STUDENT_ID, CONCAT(s.LAST_NAME,', ',s.FIRST_NAME) AS FULL_NAME,se.SCHOOL_ID FROM STUDENTS s,STUDENTS_JOIN_USERS sju, STUDENT_ENROLLMENT se WHERE s.STUDENT_ID=sju.STUDENT_ID AND sju.STAFF_ID='".User('STAFF_ID')."' AND se.SYEAR=".UserSyear()." AND se.STUDENT_ID=sju.STUDENT_ID AND (('".DBDate()."' BETWEEN se.START_DATE AND se.END_DATE OR se.END_DATE IS NULL) AND '".DBDate()."'>=se.START_DATE)"));

	if(!UserStudentID())
		$_SESSION['student_id'] = $RET[1]['STUDENT_ID'];

	echo "<SELECT name=student_id onChange='document.forms[0].submit();'>";
	if(count($RET))
	{
		foreach($RET as $student)
		{
			echo "<OPTION value=$student[STUDENT_ID]".((UserStudentID()==$student['STUDENT_ID'])?' SELECTED':'').">".$student['FULL_NAME']."</OPTION>";
			if(UserStudentID()==$student['STUDENT_ID'])
				$_SESSION['UserSchool'] = $student['SCHOOL_ID'];
		}
	}
	echo "</SELECT><BR>";

	if(!UserMP())
		$_SESSION['UserMP'] = GetCurrentMP('QTR',DBDate());
}

if(User('PROFILE')=='teacher')
{/*
	//if(UserMP())
	//	$QI = DBQuery("SELECT DISTINCT cp.PERIOD_ID,cp.COURSE_PERIOD_ID,sp.TITLE,sp.SHORT_NAME,cp.MARKING_PERIOD_ID,cp.DAYS,cp.SCHOOL_ID,sp.SORT_ORDER,c.TITLE AS COURSE_TITLE FROM COURSE_PERIODS cp, SCHOOL_PERIODS sp,COURSES c WHERE c.COURSE_ID=cp.COURSE_ID AND cp.PERIOD_ID=sp.PERIOD_ID AND cp.SYEAR='".UserSyear()."' AND cp.TEACHER_ID='".User('STAFF_ID')."' AND cp.MARKING_PERIOD_ID IN (".GetAllMP('QTR',UserMP()).") ORDER BY sp.SORT_ORDER ");
	//else
	$QI = DBQuery("SELECT DISTINCT cp.PERIOD_ID,cp.COURSE_PERIOD_ID,sp.TITLE,sp.SHORT_NAME,cp.MARKING_PERIOD_ID,cp.DAYS,cp.SCHOOL_ID,sp.SORT_ORDER,c.TITLE AS COURSE_TITLE FROM COURSE_PERIODS cp, SCHOOL_PERIODS sp,COURSES c WHERE c.COURSE_ID=cp.COURSE_ID AND cp.PERIOD_ID=sp.PERIOD_ID AND cp.SYEAR='".UserSyear()."' AND cp.TEACHER_ID='".User('STAFF_ID')."' ORDER BY sp.SORT_ORDER");
	$RET = DBGet($QI);
	// get the fy marking period id's, there should be exactly one fy marking period per school
	$fy_RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,SCHOOL_ID FROM SCHOOL_YEARS WHERE SYEAR='".UserSyear()."'"),array(),array('SCHOOL_ID'));
	// only need quarters and semesters since years are suppressed and progress_periods aren't used
	$mp_RET  = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE,SHORT_NAME FROM SCHOOL_QUARTERS  WHERE SYEAR='".UserSyear()."'
			UNION     SELECT MARKING_PERIOD_ID,TITLE,SHORT_NAME FROM SCHOOL_SEMESTERS WHERE SYEAR='".UserSyear()."'"),array(),array('MARKING_PERIOD_ID'));

	if(!UserPeriod())
		$_SESSION['UserPeriod'] = $RET[1]['PERIOD_ID'];
	if(!UserCoursePeriod())
		$_SESSION['UserCoursePeriod'] = $RET[1]['COURSE_PERIOD_ID'];

	echo "<SELECT name=period onChange='document.forms[0].submit();' style='width:150;'>";
	foreach($RET as $period)
	{
		echo "<OPTION value=$period[COURSE_PERIOD_ID]".((UserCoursePeriod()==$period['COURSE_PERIOD_ID'])?' SELECTED':'').">".$period['SHORT_NAME'].($period['MARKING_PERIOD_ID']!=$fy_RET[$period['SCHOOL_ID']][1]['MARKING_PERIOD_ID']?' '.$mp_RET[$period['MARKING_PERIOD_ID']][1]['SHORT_NAME']:'').(strlen($period['DAYS'])<5?' '.$period['DAYS']:'').' - '.$period['COURSE_TITLE']."</OPTION>";
		if(UserCoursePeriod()==$period['COURSE_PERIOD_ID'])
		{
			if($period['SCHOOL_ID']!=UserSchool())
				unset($_SESSION['UserMP']);
			$_SESSION['UserSchool'] = $period['SCHOOL_ID'];
			$_SESSION['UserPeriod'] = $period['PERIOD_ID'];
		}
	}
	echo "</SELECT><BR>";
*/}

$RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE FROM SCHOOL_QUARTERS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER"));
echo "<SELECT name=mp onChange='document.forms[0].submit();'>";
if(count($RET))
{
	if(!UserMP())
		$_SESSION['UserMP'] = $RET[1]['MARKING_PERIOD_ID'];

	foreach($RET as $quarter)
		echo "<OPTION value=$quarter[MARKING_PERIOD_ID]".(UserMP()==$quarter['MARKING_PERIOD_ID']?' SELECTED':'').">".$quarter['TITLE']."</OPTION>";
}
echo "</SELECT>";
echo '</FORM>';
if(UserStudentID() && User('PROFILE')!='parent' && User('PROFILE')!='student')
{
	$RET = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME,MIDDLE_NAME,NAME_SUFFIX FROM STUDENTS WHERE STUDENT_ID='".UserStudentID()."'"));
	//echo '<TABLE border=0 cellpadding=0 cellspacing=0 width=100%><TR><TD width=19 valign=middle><A HREF=Side.php?student_id=new&modcat='.$_REQUEST['modcat'].'><IMG SRC=assets/x.gif height=17 border=0></A></TD><TD ><B><A HREF=Modules.php?modname=Students/Student.php&student_id='.UserStudentID().' target=body><font color=#FFFFFF size=-2>'.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].'</font></A></B></TD></TR></TABLE>';
	
	echo '<TABLE border=0 cellpadding=0 cellspacing=0 width=100%><TR><TD width=19 valign=middle><A HREF=Side.php?student_id=new&modcat='.optional_param('modcat','',PARAM_SPCL).'><IMG SRC=assets/x.gif height=17 border=0></A></TD><TD ><B><A HREF=Modules.php?modname=Students/Student.php&student_id='.UserStudentID().' target=body><font color=#FFFFFF size=-2>'.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].'</font></A></B></TD></TR></TABLE>';
}
if(UserStaffID() && User('PROFILE')=='admin')
{
	if(UserStudentID())
		echo '<IMG SRC=assets/pixel_trans.gif height=2>';
	$RET = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME FROM STAFF WHERE STAFF_ID='".UserStaffID()."'"));
	//echo '<TABLE border=0 cellpadding=0 cellspacing=0 width=100%><TR><TD bgcolor=#336633 width=19 valign=middle><A HREF=Side.php?staff_id=new&modcat='.$_REQUEST['modcat'].'><IMG SRC=assets/x.gif height=17 border=0></A></TD><TD bgcolor=#336633><B><A HREF=Modules.php?modname=Users/User.php&staff_id='.UserStaffID().' target=body><font color=#FFFFFF size=-2>'.$RET[1]['FIRST_NAME'].'&nbsp;'.$RET[1]['LAST_NAME'].'</font></A></B></TD></TR></TABLE>';
	
	echo '<TABLE border=0 cellpadding=0 cellspacing=0 width=100%><TR><TD bgcolor=#336633 width=19 valign=middle><A HREF=Side.php?staff_id=new&modcat='.optional_param('modcat','',PARAM_SPCL).'><IMG SRC=assets/x.gif height=17 border=0></A></TD><TD bgcolor=#336633><B><A HREF=Modules.php?modname=Users/User.php&staff_id='.UserStaffID().' target=body><font color=#FFFFFF size=-2>'.$RET[1]['FIRST_NAME'].'&nbsp;'.$RET[1]['LAST_NAME'].'</font></A></B></TD></TR></TABLE>';
}
echo '<BR>';

// Program Information
require('Menu.php');
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

		echo "<A style='cursor: pointer; cursor:hand;' onclick='openMenu(\"".$modcat."\");parent.body.location=\"Modules.php?modname=$modcat/Search.php\";'><IMG SRC=assets/icons/$modcat.gif height=20 border=0 align=absmiddle><font color=blue face='Verdana, Arial, sans-serif'><B>".str_replace('_',' ',$modcat)."</B></font></A><BR><DIV id=menu_visible".$modcat."></DIV>";
		echo "<DIV id=menu_hidden".$modcat." style=\"visibility:hidden;position:absolute;\"><TABLE width=100%>";
		//foreach($_openSIS['Menu'][$modcat] as $file=>$title)
		foreach($keys as $key_index=>$file)
		{
			$title = $_openSIS['Menu'][$modcat][$file];
			if(substr($file,0,7)=='http://')
				echo "<TR><TD width=20></TD><TD class=BoxContents><b>&rsaquo;</b> <A HREF=$file target=body onclick='javascript:parent.help.location=\"Bottom.php?modname=$file\"'><font color=blue>$title</font></A></TD></TR>";
			elseif(substr($file,0,7)=='HTTP://')
				echo "<TR><TD width=20></TD><TD class=BoxContents><b>&rsaquo;</b> <A HREF=$file target=_blank><font color=blue>$title</font></A></TD></TR>";
			elseif(!is_numeric($file))
				echo "<TR><TD width=20></TD><TD class=BoxContents><b>&rsaquo;</b> <A HREF=Modules.php?modname=$file target=body onclick='javascript:parent.help.location=\"Bottom.php?modname=$file\"'><font color=blue>$title</font></A></TD></TR>";
			elseif($keys[$key_index+1] && !is_numeric($keys[$key_index+1]))
				echo '<TR><TD colspan=2 height=3></TD></TR><TR><TD colspan=2 class=BoxContents> &nbsp; <b>'.$title.'</b></TD></TR>';
		}
		echo "</TABLE></DIV>";
	}
}

echo '</TD></TR></TABLE>';
echo '</BODY>';
echo '</HTML>';
?>