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
#error_reporting(1);
error_reporting(0);

include("functions/del_directory.fnc.php");
include("functions/ParamLib.php");
include("remove_backup.php");
$url=validateQueryString(curPageURL());
if($url===FALSE){
 header('Location: index.php');
 }
//if($_REQUEST['dis']=='fl_count'){
if(optional_param('dis','',PARAM_ALPHAEXT)=='fl_count')
{
$error[] = "Either your account is inactive or your access permission has been revoked. Please contact the school administration.
";
}
if(isset($_GET['ins']))

	$install = optional_param('ins','',PARAM_ALPHAEXT);
	
	
if($install == 'comp')
{
	if (is_dir('install')) 
	{
		$dir = 'install/'; // IMPORTANT: with '/' at the end
		$remove_directory = delete_directory($dir);
	}
}

require_once('Warehouse.php');

//if($_REQUEST['modfunc']=='logout')
if(optional_param('modfunc','',PARAM_ALPHAEXT)=='logout')
{
    if($_SESSION)
    {
        DBQuery("DELETE FROM LOG_MAINTAIN WHERE SESSION_ID = '".$_SESSION['X']."'");
        header("Location: $_SERVER[PHP_SELF]?modfunc=logout".(($_REQUEST['reason'])?'&reason='.$_REQUEST['reason']:''));
       // header("Location: $_SERVER[PHP_SELF]?modfunc=logout".optional_param('reason','',PARAM_ALPHAEXT)?'&reason='.optional_param('reason','',PARAM_ALPHAEXT):'');
    }
    session_destroy();
}

//if($_REQUEST['register'])
if(optional_param('register','',PARAM_NOTAGS))
{
    if(optional_param('R1','',PARAM_ALPHA)=='register')
    header("Location:register.php");
}

//if($_REQUEST['USERNAME']&& $_REQUEST['PASSWORD'])
if(optional_param('USERNAME','',PARAM_RAW) && optional_param('PASSWORD','',PARAM_RAW))
{
    db_start();
	
	# --------------------------- Seat Count Update Start ------------------------------------------ #
    //DBQuery("CALL SEAT_COUNT()");
    //DBQuery("CALL SEAT_FILL()");
	
    $course_name = DBGet(DBQuery("SELECT DISTINCT(COURSE_PERIOD_ID)FROM SCHEDULE WHERE  END_DATE <'".date("Y-m-d")."' AND  DROPPED =  'N' "));

         foreach($course_name as $column=>$value)
         {
             $course_count = DBGet(DBQuery("SELECT *  FROM SCHEDULE WHERE  COURSE_PERIOD_ID='".$value[COURSE_PERIOD_ID]."' AND  END_DATE <'".date("Y-m-d")."'AND  DROPPED =  'N' "));
              for($i=1;$i<=count($course_count);$i++)
                    {
                        DBQuery("CALL SEAT_FILL()");
                        DBQuery("UPDATE COURSE_PERIODS SET filled_seats=filled_seats-1 WHERE COURSE_PERIOD_ID IN (SELECT COURSE_PERIOD_ID FROM SCHEDULE WHERE end_date IS NOT NULL AND END_DATE  <'".date("Y-m-d")."' AND  DROPPED='N' AND COURSE_PERIOD_ID='".$value[COURSE_PERIOD_ID]."')");
			DBQuery(" UPDATE SCHEDULE SET  DROPPED='Y' WHERE END_DATE  IS NOT NULL AND COURSE_PERIOD_ID='".$value[COURSE_PERIOD_ID]."' AND END_DATE  <'".date("Y-m-d")."'AND   DROPPED =  'N' AND  STUDENT_ID='".$course_count[$i][STUDENT_ID]."'");
                    }
         }
	
	# ---------------------------- Seat Count Update End ------------------------------------------- #
	
	
	
     $username = mysql_real_escape_string(optional_param('USERNAME','',PARAM_RAW));
    # $password = md5($_REQUEST['PASSWORD']);
       if($password==optional_param('PASSWORD','',PARAM_RAW))
    $password = str_replace("\'","",md5(optional_param('PASSWORD','',PARAM_RAW)));
	$password = str_replace("&","",md5(optional_param('PASSWORD','',PARAM_RAW)));
	$password = str_replace("\\","",md5(optional_param('PASSWORD','',PARAM_RAW)));
	
	$student_disable_storeproc_RET = DBGet(DBQuery("SELECT s.STUDENT_ID FROM STUDENTS s,STUDENT_ENROLLMENT se WHERE UPPER(s.USERNAME)=UPPER('$username') AND UPPER(s.PASSWORD)=UPPER('$password') AND se.STUDENT_ID=s.STUDENT_ID LIMIT 1"));
	if($student_disable_storeproc_RET[1]['STUDENT_ID']){
	DBQuery("SELECT STUDENT_DISABLE('".$student_disable_storeproc_RET[1]['STUDENT_ID']."')");
	}
$maintain_RET=DBGet(DBQuery("SELECT SYSTEM_MAINTENANCE_SWITCH FROM SYSTEM_PREFERENCE_MISC LIMIT 1"));
	 $maintain=$maintain_RET[1];
	   $login_RET = DBGet(DBQuery("SELECT USERNAME,PROFILE,STAFF_ID,CURRENT_SCHOOL_ID,LAST_LOGIN,FIRST_NAME,LAST_NAME,PROFILE,IS_DISABLE,PROFILE_ID,FAILED_LOGIN FROM STAFF WHERE SYEAR=(SELECT MAX(SYEAR) FROM STAFF WHERE UPPER(USERNAME)=UPPER('$username') AND UPPER(PASSWORD)=UPPER('$password')) AND UPPER(USERNAME)=UPPER('$username') AND UPPER(PASSWORD)=UPPER('$password')"));
           $loged_staff_id = $login_RET[1]['STAFF_ID'];
           if($login_RET[1]['PROFILE']=='parent')
           {
               $is_inactive= DBGet(DBQuery("SELECT se.ID FROM STUDENT_ENROLLMENT se,STUDENTS_JOIN_USERS sju WHERE sju.STUDENT_ID= se.STUDENT_ID AND sju.STAFF_ID=$loged_staff_id AND se.SYEAR=(SELECT MAX(SYEAR) FROM STUDENT_ENROLLMENT WHERE STUDENT_ID=sju.STUDENT_ID) AND CURRENT_DATE>=se.START_DATE AND (CURRENT_DATE<=se.END_DATE OR se.END_DATE IS NULL)"));
               if(!$is_inactive)
               {
                  session_destroy(); 
		  header("location:index.php?modfunc=logout&dis=fl_count");
               }
           }

    $student_RET = DBGet(DBQuery("SELECT s.USERNAME,s.STUDENT_ID,s.LAST_LOGIN,s.IS_DISABLE,s.FAILED_LOGIN,se.SYEAR FROM STUDENTS s,STUDENT_ENROLLMENT se WHERE UPPER(s.USERNAME)=UPPER('$username') AND UPPER(s.PASSWORD)=UPPER('$password') AND se.STUDENT_ID=s.STUDENT_ID AND se.SYEAR=(SELECT MAX(SYEAR) FROM STUDENT_ENROLLMENT WHERE STUDENT_ID=s.STUDENT_ID) AND CURRENT_DATE>=se.START_DATE AND (CURRENT_DATE<=se.END_DATE OR se.END_DATE IS NULL)"));

    if($maintain['SYSTEM_MAINTENANCE_SWITCH']==Y && ( $login_RET || $student_RET)){   
 $login=$login_RET[1];
 if(($login && $login['PROFILE_ID']!=1)||$login['PROFILE_ID']==0){
  header("Location:index.php?maintain=Y");
  exit;
 }
 }
    if(!$login_RET && !$student_RET)
    {
        $admin_RET = DBGet(DBQuery("SELECT STAFF_ID FROM STAFF WHERE PROFILE='$username' AND SYEAR=(SELECT MAX(SYEAR) FROM STAFF WHERE PROFILE='$username') AND UPPER(PASSWORD)=UPPER('$password')"));  // Uid and Password Checking
        if($admin_RET)
        {
             $login_RET = DBGet(DBQuery("SELECT USERNAME,PROFILE,STAFF_ID,CURRENT_SCHOOL_ID,LAST_LOGIN,FIRST_NAME,LAST_NAME,PROFILE_ID,FAILED_LOGIN,IS_DISABLE FROM STAFF WHERE SYEAR=(SELECT MAX(SYEAR) FROM STAFF WHERE UPPER(USERNAME)=UPPER('$username') AND UPPER(PASSWORD)=UPPER('$password')) AND UPPER(USERNAME)=UPPER('$username')"));
            $student_RET = DBGet(DBQuery("SELECT s.FIRST_NAME as FIRST_NAME,s.USERNAME,s.STUDENT_ID,s.LAST_LOGIN,s.FAILED_LOGIN FROM STUDENTS s,STUDENT_ENROLLMENT se WHERE UPPER(s.USERNAME)=UPPER('$username') AND se.STUDENT_ID=s.STUDENT_ID AND se.SYEAR=(SELECT MAX(SYEAR) FROM STUDENT_ENROLLMENT WHERE STUDENT_ID=s.STUDENT_ID) AND CURRENT_DATE>=se.START_DATE AND (CURRENT_DATE<=se.END_DATE OR se.END_DATE IS NULL)"));
        }
    }
	if($login_RET && $login_RET[1]['IS_DISABLE']!='Y')
    {
        $_SESSION['STAFF_ID'] = $login_RET[1]['STAFF_ID'];
        $_SESSION['LAST_LOGIN'] = $login_RET[1]['LAST_LOGIN'];
         #$failed_login = $login_RET[1]['FAILED_LOGIN'];
       # $_SESSION['ACC_EXP_DATE'] = $login_RET[1]['ACC_EXP_DATE'];
		#$_SESSION['USER_ACTIVITY_CHK'] = $login_RET[1]['USER_ACTIVITY_CHK'];
        $syear_RET=DBGet(DBQuery("SELECT MAX(SYEAR) AS SYEAR FROM SCHOOL_YEARS WHERE SCHOOL_ID=".$login_RET[1]['CURRENT_SCHOOL_ID']));
        $_SESSION['UserSyear'] =$syear_RET[1]['SYEAR'];
        $_SESSION['UserSchool']=$login_RET[1]['CURRENT_SCHOOL_ID'];
		$_SESSION['PROFILE_ID'] = $login_RET[1]['PROFILE_ID'];
		$_SESSION['FIRST_NAME'] = $login_RET[1]['FIRST_NAME'];
       	$_SESSION['LAST_NAME'] = $login_RET[1]['LAST_NAME'];
		$_SESSION['PROFILE'] = $login_RET[1]['PROFILE'];
		$_SESSION['USERNAME'] = $login_RET[1]['USERNAME'];
		$_SESSION['FAILED_LOGIN'] = $login_RET[1]['FAILED_LOGIN'];
		$_SESSION['CURRENT_SCHOOL_ID'] = $login_RET[1]['CURRENT_SCHOOL_ID'];
		#$_SESSION['IS_DISABLED'] = $login_RET[1]['IS_DISABLED'];
		$_SESSION['USERNAME'] = optional_param('USERNAME','',PARAM_RAW);
		$_SESSION['PASSWORD'] = optional_param('PASSWORD',' ',PARAM_RAW);
        # --------------------- Set Session Id Start ------------------------- #
		$_SESSION['X'] = session_id();
		$random = rand();
	# ---------------------- Set Session Id End -------------------------- #
	DBQuery("INSERT INTO LOG_MAINTAIN( value, session_id) values($random, '".$_SESSION['X']."')");

	$r_id_min = DBGet(DBQuery("SELECT MIN(id) as MIN_ID FROM LOG_MAINTAIN WHERE SESSION_ID = '".$_SESSION['X']."'"));
	$row_id_min = $r_id_min[1]['MIN_ID'];

	$val_min_id = DBGet(DBQuery("SELECT VALUE FROM LOG_MAINTAIN WHERE ID = $row_id_min"));
	$value_min_id = $val_min_id[1]['VALUE'];

	$r_id_max = DBGet(DBQuery("SELECT MAX(id) as MAX_ID FROM LOG_MAINTAIN WHERE SESSION_ID = '".$_SESSION['X']."'"));
	$row_id_max = $r_id_max[1]['MAX_ID'];

	$val_max_id = DBGet(DBQuery("SELECT VALUE FROM LOG_MAINTAIN WHERE ID = $row_id_max"));
	$value_max_id = $val_max_id[1]['VALUE'];
################################## For Inserting into Log tables  ######################################
//if($_REQUEST['USERNAME']!='' && $_REQUEST['PASSWORD']!='' && $value_min_id == $value_max_id)
		if(optional_param('USERNAME','',PARAM_RAW)!='' && optional_param('PASSWORD','',PARAM_RAW)!='' && $value_min_id == $value_max_id)
		{
			
			if ($_SERVER['HTTP_X_FORWARDED_FOR']){
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			
			
			$date=date("Y-m-d H:i:s");
			DBQuery("INSERT INTO LOGIN_RECORDS (SYEAR,STAFF_ID,FIRST_NAME,LAST_NAME,PROFILE,USER_NAME,LOGIN_TIME,FAILLOG_COUNT,IP_ADDRESS,STATUS,SCHOOL_ID) values('$_SESSION[UserSyear]','$_SESSION[STAFF_ID]','$_SESSION[FIRST_NAME]','$_SESSION[LAST_NAME]','$_SESSION[PROFILE]','$_SESSION[USERNAME]','$date','$_SESSION[FAILED_LOGIN]','$ip','Success',$_SESSION[CURRENT_SCHOOL_ID])");
			
			
			/*$date=date("Y-m-d H:i:s");
			DBQuery("INSERT INTO LOGIN_RECORDS (SYEAR,STAFF_ID,FIRST_NAME,LAST_NAME,PROFILE,USER_NAME,LOGIN_TIME,FAILLOG_COUNT,IP_ADDRESS,STATUS,SCHOOL_ID) values('$DefaultSyear','$_SESSION[STAFF_ID]','$_SESSION[FIRST_NAME]','$_SESSION[LAST_NAME]','$_SESSION[PROFILE]','$_SESSION[USERNAME]','$date','$_SESSION[FAILED_LOGIN]','$_SERVER[REMOTE_ADDR]','Success',$_SESSION[CURRENT_SCHOOL_ID])");*/
		}

		$disable=$_SESSION['IS_DISABLED'];
		$failed_login= $_SESSION['FAILED_LOGIN'];
		$profile_id = $_SESSION['PROFILE_ID'];

		$admin_failed_count = DBGet(DBQuery("SELECT FAIL_COUNT FROM SYSTEM_PREFERENCE_MISC"));
		$ad_f_cnt = $admin_failed_count[1]['FAIL_COUNT'];

		if ($ad_f_cnt && $ad_f_cnt!=0 && $failed_login>$ad_f_cnt && $profile_id!=1)
		{

		  DBQuery("UPDATE STAFF SET IS_DISABLE='Y' WHERE STAFF_ID='".$_SESSION['STAFF_ID']."' AND SYEAR='$_SESSION[UserSyear]' AND PROFILE_ID!=1");

		  session_destroy();
		  #header("location:index.php?modfunc=logout");
		  header("location:index.php?modfunc=logout&dis=fl_count");
		 }



		if($disable==true)
		{
		  session_destroy();
		 # header("location:index.php?modfunc=logout");
		  header("location:index.php?modfunc=logout&dis=fl");
		}
		$activity = DBGet(DBQuery("SELECT ACTIVITY_DAYS FROM SYSTEM_PREFERENCE_MISC"));
		$activity = $activity[1]['ACTIVITY_DAYS'];
		$last_login=$_SESSION['LAST_LOGIN'];
		$date1 = date("Y-m-d H:m:s");
		$date2 = $last_login; //  yyyy/mm/dd
		$days = (strtotime($date1) - strtotime($date2)) / (60 * 60 * 24);

		if ( $activity && $activity!=0 && $days>$activity && $profile_id!=1 && $last_login)
		{
		  DBQuery("UPDATE STAFF SET IS_DISABLE='Y' WHERE STAFF_ID='".$_SESSION['STAFF_ID']."' AND SYEAR='$_SESSION[UserSyear]' AND PROFILE_ID!=1");

		  session_destroy();
		  #header("location:index.php?modfunc=logout");
		  header("location:index.php?modfunc=logout&dis=fl_count");
		 }


############################################################################################
      $failed_login = $login_RET[1]['FAILED_LOGIN'];
        if($admin_RET)
        DBQuery("UPDATE STAFF SET LAST_LOGIN=CURRENT_TIMESTAMP WHERE STAFF_ID='".$admin_RET[1]['STAFF_ID']."'");
        else
        DBQuery("UPDATE STAFF SET LAST_LOGIN=CURRENT_TIMESTAMP,FAILED_LOGIN=NULL WHERE STAFF_ID='".$login_RET[1]['STAFF_ID']."'");

        if(Config('LOGIN')=='No')
        {
            require('soaplib/nusoap.php');
            $parameters = array($_SERVER['SERVER_NAME'], $_SERVER['SERVER_ADDR'], $openSISVersion, $_SERVER['PHP_SELF'], $_SERVER['DOCUMENT_ROOT'], $_SERVER['SCRIPT_NAME']);
            $s = new nusoap_client('http://register.os4ed.com/register.php');
            $result = $s->call('installlog',$parameters);

            DBQuery("UPDATE config SET LOGIN='Y'");
        }
    }
    #elseif($login_RET && $login_RET[1]['PROFILE']=='none')
	elseif(($login_RET && $login_RET[1]['IS_DISABLE']=='Y') || ($student_RET && $student_RET[1]['IS_DISABLE']=='Y'))
	{
		$error[] = "Either your account is inactive or your access permission has been revoked. Please contact the school administration.
";
	}
    elseif($student_RET)
    {
	
		$failed_login= $student_RET[1]['FAILED_LOGIN'];

		$admin_failed_count = DBGet(DBQuery("SELECT FAIL_COUNT FROM SYSTEM_PREFERENCE_MISC"));
		$ad_f_cnt = $admin_failed_count[1]['FAIL_COUNT'];

		if ($ad_f_cnt && $ad_f_cnt!=0 && $failed_login>$ad_f_cnt)
		{

		  DBQuery("UPDATE STUDENTS SET IS_DISABLE='Y' WHERE STUDENT_ID='".$student_RET[1]['STUDENT_ID']."' ");

		  session_destroy();
		  
		  header("location:index.php?modfunc=logout&dis=fl_count");
		 }
	
	    $_SESSION['STUDENT_ID'] = $student_RET[1]['STUDENT_ID'];
        $_SESSION['LAST_LOGIN'] = $student_RET[1]['LAST_LOGIN'];
                      $_SESSION['UserSyear'] = $student_RET[1]['SYEAR'];
		$activity = DBGet(DBQuery("SELECT ACTIVITY_DAYS FROM SYSTEM_PREFERENCE_MISC"));
		$activity = $activity[1]['ACTIVITY_DAYS'];
		$last_login=$_SESSION['LAST_LOGIN'];
		$date1 = date("Y-m-d H:m:s");
		$date2 = $last_login; //  yyyy/mm/dd
		$days = (strtotime($date1) - strtotime($date2)) / (60 * 60 * 24);

		if ( $activity && $activity!=0 && $days>$activity && $profile_id!=1 && $last_login)
		{
		  DBQuery("UPDATE STUDENTS SET IS_DISABLE='Y' WHERE STUDENT_ID='".$student_RET[1]['STUDENT_ID']."' ");

		  session_destroy();
		  
		  header("location:index.php?modfunc=logout&dis=fl_count");
		 }
		
        $failed_login = $student_RET[1]['FAILED_LOGIN'];
        if($admin_RET)
        DBQuery("UPDATE STAFF SET LAST_LOGIN=CURRENT_TIMESTAMP WHERE STAFF_ID='".$admin_RET[1]['STAFF_ID']."'");
        else
        DBQuery("UPDATE STUDENTS SET LAST_LOGIN=CURRENT_TIMESTAMP,FAILED_LOGIN=NULL WHERE STUDENT_ID='".$student_RET[1]['STUDENT_ID']."'");
    }
    else
    {  /* cleaning using other parameters other than ALPHAEXT is not working----*/
        DBQuery("UPDATE STAFF SET FAILED_LOGIN=".db_case(array('FAILED_LOGIN',"''",'1','FAILED_LOGIN+1'))." WHERE UPPER(USERNAME)=UPPER('".optional_param('USERNAME', 0, PARAM_ALPHAEXT)."') AND SYEAR='$_SESSION[UserSyear]'");
        DBQuery("UPDATE STUDENTS SET FAILED_LOGIN=".db_case(array('FAILED_LOGIN',"''",'1','FAILED_LOGIN+1'))." WHERE UPPER(USERNAME)=UPPER('".optional_param('USERNAME', 0, PARAM_ALPHAEXT)."')");
         #  $error[] = "Incorrect username or password. Please try again.";
    #	DBQuery("UPDATE STAFF SET FAILED_LOGIN=".db_case(array('FAILED_LOGIN',"''",'1','FAILED_LOGIN+1'))." WHERE UPPER(USERNAME)=UPPER('$_REQUEST[USERNAME]') AND SYEAR='$DefaultSyear'");
	#	DBQuery("UPDATE STUDENTS SET FAILED_LOGIN=".db_case(array('FAILED_LOGIN',"''",'1','FAILED_LOGIN+1'))." WHERE UPPER(USERNAME)=UPPER('$_REQUEST[USERNAME]')");
		
		
		if ($_SERVER['HTTP_X_FORWARDED_FOR']){
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
		
		
		$faillog_time=date("Y-m-d h:i:s");
		//DBQuery("INSERT INTO LOGIN_RECORDS (USER_NAME,FAILLOG_TIME,IP_ADDRESS,SYEAR,STATUS) values('$_REQUEST[USERNAME]','$faillog_time','$ip','$DefaultSyear','Failed')");
		
		DBQuery("INSERT INTO LOGIN_RECORDS (USER_NAME,FAILLOG_TIME,IP_ADDRESS,SYEAR,STATUS) values('".optional_param('USERNAME','',PARAM_ALPHAEXT)."','$faillog_time','$ip','$_SESSION[UserSyear]','Failed')"); 
		
		
		$max_id = DBGet(DBQuery("SELECT MAX(id) FROM LOGIN_RECORDS"));
		$m_id= $max_id[1]['MAX'];
		if($faillog_time)
		DBQuery("UPDATE LOGIN_RECORDS SET LOGIN_TIME=FAILLOG_TIME WHERE USER_NAME='".optional_param('USERNAME','',PARAM_ALPHAEXT)."' AND ID='".$m_id."'");

        $error[] = "Incorrect username or password. Please try again.";
    }
}

//if($_REQUEST['modfunc']=='create_account')
if(optional_param('modfunc','',PARAM_ALPHA)=='create_account')
{
    Warehouse('header');
    $_openSIS['allow_edit'] = true;
    if($_REQUEST['staff']['USERNAME'])
    $_REQUEST['modfunc'] = 'update';
    else
    $_REQUEST['staff_id'] = 'new';
    include('modules/Users/User.php');

    if(!$_REQUEST['staff']['USERNAME'])
    Warehouse('footer_plain');
    else
    {
        $note[] = 'Your account has been created.  You will be notified by email when it is verified by school administration and you can log in.';
        session_destroy();
    }
}

if(!$_SESSION['STAFF_ID'] && !$_SESSION['STUDENT_ID'] && $_REQUEST['modfunc']!='create_account')
{
    //Login
    require "login.inc.php";
}
elseif($_REQUEST['modfunc']!='create_account')
{
    echo "
        <HTML>
            <HEAD><TITLE>".Config('TITLE')."</TITLE><link rel=\"shortcut icon\" href=\"favicon.ico\"></HEAD>";
    echo "<noscript><META http-equiv=REFRESH content='0;url=index.php?modfunc=logout&reason=javascript' /></noscript>";
    echo "<frameset id=mainframeset rows='*,0' border=0 framespacing=0>
                <frameset cols='0,*' border=0>
                    <frame name='side' src='' frameborder='0' />
                    <frame name='body' src='Modules.php?modname=".($_REQUEST['modname']='misc/Portal.php')."&failed_login=$failed_login' frameborder='0' style='border: inset #C9C9C9 2px' />
                </frameset>
                <frame name='help' src='' />
            </frameset>
        </HTML>";
}
?>
