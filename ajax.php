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
//error_reporting(1);
//include('Redirect_root.php');
include("functions/ParamLib.php");
$url=validateQueryString(curPageURL());
if($url===FALSE){
 header('Location: index.php');
 }
error_reporting(E_ERROR);
$isajax="ajax";
$start_time = time();
include 'Warehouse.php';
array_rwalk($_REQUEST,'strip_tags');

//if(UserStudentID() && User('PROFILE')!='parent' && User('PROFILE')!='student' && substr($_REQUEST['modname'],0,6)!='Grades' && substr($_REQUEST['modname'],0,5)!='Users' && substr($_REQUEST['modname'],0,10)!='Attendance' )
//if(UserStudentID() && User('PROFILE')!='parent' && User('PROFILE')!='student' && substr(clean_param($_REQUEST['modname'],PARAM_NOTAGS),0,6)!='Grades' && substr(clean_param($_REQUEST['modname'],PARAM_NOTAGS),0,5)!='Users' && substr(clean_param($_REQUEST['modname'],PARAM_NOTAGS),0,10)!='Attendance' )
if(UserStudentID() && User('PROFILE')!='parent' && User('PROFILE')!='student' && substr(clean_param($_REQUEST['modname'],PARAM_NOTAGS),0,5)!='Users' && clean_param($_REQUEST['modname'],PARAM_NOTAGS)!='Students/AddUsers.php')// && substr(clean_param($_REQUEST['modname'],PARAM_NOTAGS),0,10)!='Attendance' )
{
	$RET = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME,MIDDLE_NAME,NAME_SUFFIX FROM STUDENTS WHERE STUDENT_ID='".UserStudentID()."'"));
        $count_student_RET=DBGet(DBQuery("SELECT COUNT(*) AS NUM FROM STUDENTS"));
        if($count_student_RET[1]['NUM']>1){
	DrawHeaderHome( 'Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.clean_param($_REQUEST['modcat'],PARAM_NOTAGS).'><font color=red>Remove</font></A>) | <A HREF=Modules.php?modname='.clean_param($_REQUEST['modname'],PARAM_NOTAGS).'&search_modfunc=list&next_modname=Students/Student.php&ajax=true&bottom_back=true&return_session=true target=body>Back to Student List</A>');
	  /*  if($_REQUEST['modname']!='Attendance/Administration.php')
	{
		DrawHeaderHome( 'Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Remove</font></A>) | <A HREF=Modules.php?modname='.$_REQUEST['modname'].'&search_modfunc=list&next_modname='.$_REQUEST['modname'].'&ajax=true&bottom_back=true&return_session=true target=body>Back to Student List</A>');
	}*/
        }else if($count_student_RET[1]['NUM']==1){
            DrawHeaderHome( 'Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.clean_param($_REQUEST['modcat'],PARAM_NOTAGS).'><font color=red>Remove</font></A>) ');
        }

}
if(UserStaffID() && User('PROFILE')=='admin' && substr(clean_param($_REQUEST['modname'],PARAM_NOTAGS),0,6)!='Grades' && substr(clean_param($_REQUEST['modname'],PARAM_NOTAGS),0,8)!='Students' && substr(clean_param($_REQUEST['modname'],PARAM_NOTAGS),0,10)!='Attendance')
{
	//if(UserStudentID())
	//	echo '<IMG SRC=assets/pixel_trans.gif height=2>';
    //$Modname_Attn = 'Users/TeacherPrograms.php?include=Attendance/Missing_Attendance.php';
    $Modname_Attn='Users/TeacherPrograms.php';
    $Modname_Pro = 'Users/TeacherPrograms.php?include=Grades/ProgressReports.php';
        if((!UserStudentID() || substr(clean_param($_REQUEST['modname'],PARAM_NOTAGS),0,5)=='Users') && substr(clean_param($_REQUEST['modname'],PARAM_NOTAGS),0,25)!=$Modname_Attn && clean_param($_REQUEST['modname'],PARAM_NOTAGS)!='Users/AddStudents.php' && !clean_param($_REQUEST['miss_attn'],PARAM_NOTAGS))
        {
            $RET = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME FROM STAFF WHERE STAFF_ID='".UserStaffID()."'"));
            DrawHeaderHome( 'Selected User: '.$RET[1]['FIRST_NAME'].'&nbsp;'.$RET[1]['LAST_NAME'].' (<A HREF=Side.php?staff_id=new&modcat='.clean_param($_REQUEST['modcat'],PARAM_NOTAGS).'><font color=red>Remove</font></A>) | <A HREF=Modules.php?modname='.clean_param($_REQUEST['modname'],PARAM_NOTAGS).'&search_modfunc=list&next_modname=Users/User.php&ajax=true&bottom_back=true&return_session=true target=body>Back to User List</A>');
        }
}

echo "<center><div id='divErr'></div></center>";

if(!isset($_REQUEST['_openSIS_PDF']))
{
	Warehouse('header');

	//if(strpos($_REQUEST['modname'],'misc/')===false && $_REQUEST['modname']!='Students/Student.php' && $_REQUEST['modname']!='School_Setup/Calendar.php' && $_REQUEST['modname']!='Scheduling/Schedule.php' && $_REQUEST['modname']!='Attendance/Percent.php' && $_REQUEST['modname']!='Attendance/Percent.php?list_by_day=true' && $_REQUEST['modname']!='Scheduling/MassRequests.php' && $_REQUEST['modname']!='Scheduling/MassSchedule.php' && $_REQUEST['modname']!='Student_Billing/Fees.php')
	if(strpos(clean_param($_REQUEST['modname'],PARAM_NOTAGS),'misc/')===false)
		echo '<script language="JavaScript">if(window == top  && (!window.opener || window.opener.location.href.substring(0,(window.opener.location.href.indexOf("&")!=-1?window.opener.location.href.indexOf("&"):window.opener.location.href.replace("#","").length))!=window.location.href.substring(0,(window.location.href.indexOf("&")!=-1?window.location.href.indexOf("&"):window.location.href.replace("#","").length)))) window.location.href = "index.php";</script>';
	echo "<BODY marginwidth=0 leftmargin=0 border=0 onload='doOnload();' background=assets/bg.gif>";
	echo '<DIV id="Migoicons" style="visibility:hidden;position:absolute;z-index:1000;top:-100"></DIV>';
	echo "<TABLE width=100% height=100% border=0 cellpadding=0 align=center><TR><TD valign=top align=center>";
}

if(clean_param($_REQUEST['modname'],PARAM_NOTAGS))
{
	if($_REQUEST['_openSIS_PDF']=='true')
		ob_start();
	if(strpos($_REQUEST['modname'],'?')!==false)
	{
		$vars = substr($_REQUEST['modname'],(strpos($_REQUEST['modname'],'?')+1));
		$modname = substr($_REQUEST['modname'],0,strpos($_REQUEST['modname'],'?'));

		$vars = explode('?',$vars);
		foreach($vars as $code)
		{
			$code = decode_unicode_url("\$_REQUEST['".str_replace('=',"']='",$code)."';");
			eval($code);
		}
	}
	else
		$modname = $_REQUEST['modname'];

	if($_REQUEST['LO_save']!='1' && !isset($_REQUEST['_openSIS_PDF']) && (strpos($modname,'misc/')===false || $modname=='misc/Registration.php' || $modname=='misc/Export.php' || $modname=='misc/Portal.php'))
		$_SESSION['_REQUEST_vars'] = $_REQUEST;

	$allowed = false;
	include 'Menu.php';
	foreach($_openSIS['Menu'] as $modcat=>$programs)
	{
		if(clean_param($_REQUEST['modname'],PARAM_NOTAGS)==$modcat.'/Search.php')
		{
			$allowed = true;
			break;
		}
		foreach($programs as $program=>$title)
		{
			if(clean_param($_REQUEST['modname'],PARAM_NOTAGS)==$program)
			{
				$allowed = true;
				break;
			}
		}
	}
	if(substr(clean_param($_REQUEST['modname'],PARAM_NOTAGS),0,5)=='misc/')
		$allowed = true;

	if($allowed || $_SESSION['take_mssn_attn'])
	{
		if(Preferences('SEARCH')!='Y')
			$_REQUEST['search_modfunc'] = 'list';
		include('modules/'.$modname);
	}
	else
	{
		if(User('USERNAME'))
		{
			
			 
			if ($_SERVER['HTTP_X_FORWARDED_FOR']){
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			
			
			echo "You're not allowed to use this program! This attempted violation has been logged and your IP address was captured.";
			DBQuery("INSERT INTO HACKING_LOG (HOST_NAME,IP_ADDRESS,LOGIN_DATE,VERSION,PHP_SELF,DOCUMENT_ROOT,SCRIPT_NAME,MODNAME,USERNAME) values('$_SERVER[SERVER_NAME]','$ip','".date('Y-m-d')."','$openSISVersion','$_SERVER[PHP_SELF]','$_SERVER[DOCUMENT_ROOT]','$_SERVER[SCRIPT_NAME]','$_REQUEST[modname]','".User('USERNAME')."')");
			Warehouse('footer');
			if($openSISNotifyAddress)
				mail($openSISNotifyAddress,'HACKING ATTEMPT',"INSERT INTO HACKING_LOG (HOST_NAME,IP_ADDRESS,LOGIN_DATE,VERSION,PHP_SELF,DOCUMENT_ROOT,SCRIPT_NAME,MODNAME,USERNAME) values('$_SERVER[SERVER_NAME]','$ip','".date('Y-m-d')."','$openSISVersion','$_SERVER[PHP_SELF]','$_SERVER[DOCUMENT_ROOT]','$_SERVER[SCRIPT_NAME]','$_REQUEST[modname]','".User('USERNAME')."')");
			
			
			
			/*echo "You're not allowed to use this program! This attempted violation has been logged and your IP address was captured.";
			DBQuery("INSERT INTO HACKING_LOG (HOST_NAME,IP_ADDRESS,LOGIN_DATE,VERSION,PHP_SELF,DOCUMENT_ROOT,SCRIPT_NAME,MODNAME,USERNAME) values('$_SERVER[SERVER_NAME]','$_SERVER[REMOTE_ADDR]','".date('Y-m-d')."','$openSISVersion','$_SERVER[PHP_SELF]','$_SERVER[DOCUMENT_ROOT]','$_SERVER[SCRIPT_NAME]','$_REQUEST[modname]','".User('USERNAME')."')");
			Warehouse('footer');
			if($openSISNotifyAddress)
				mail($openSISNotifyAddress,'HACKING ATTEMPT',"INSERT INTO HACKING_LOG (HOST_NAME,IP_ADDRESS,LOGIN_DATE,VERSION,PHP_SELF,DOCUMENT_ROOT,SCRIPT_NAME,MODNAME,USERNAME) values('$_SERVER[SERVER_NAME]','$_SERVER[REMOTE_ADDR]','".date('Y-m-d')."','$openSISVersion','$_SERVER[PHP_SELF]','$_SERVER[DOCUMENT_ROOT]','$_SERVER[SCRIPT_NAME]','$_REQUEST[modname]','".User('USERNAME')."')");*/
		}
		exit;
	}

	if($_SESSION['unset_student'])
	{
		unset($_SESSION['unset_student']);
		unset($_SESSION['staff_id']);
	}
}

echo "<div id='cal' class='divcal'> </div>";



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
	echo '</BODY>';
	echo '</HTML>';
}


function decode_unicode_url($str)
{
  $res = '';

  $i = 0;
  $max = strlen($str) - 6;
  while ($i <= $max)
  {
    $character = $str[$i];
    if ($character == '%' && $str[$i + 1] == 'u')
    {
      $value = hexdec(substr($str, $i + 2, 4));
      $i += 6;

      if ($value < 0x0080) // 1 byte: 0xxxxxxx
        $character = chr($value);
      else if ($value < 0x0800) // 2 bytes: 110xxxxx 10xxxxxx
        $character =
            chr((($value & 0x07c0) >> 6) | 0xc0)
          . chr(($value & 0x3f) | 0x80);
      else // 3 bytes: 1110xxxx 10xxxxxx 10xxxxxx
        $character =
            chr((($value & 0xf000) >> 12) | 0xe0)
          . chr((($value & 0x0fc0) >> 6) | 0x80)
          . chr(($value & 0x3f) | 0x80);
    }
    else
      $i++;

    $res .= $character;
  }

  return $res . substr($str, $i);
}





function code2utf($num){
  if($num<128) 
    return chr($num);
  if($num<1024) 
    return chr(($num>>6)+192).chr(($num&63)+128);
  if($num<32768) 
    return chr(($num>>12)+224).chr((($num>>6)&63)+128)
          .chr(($num&63)+128);
  if($num<2097152) 
    return chr(($num>>18)+240).chr((($num>>12)&63)+128)
          .chr((($num>>6)&63)+128).chr(($num&63)+128);
  return '';
}

function unescape($strIn, $iconv_to = 'UTF-8') {
  $strOut = '';
  $iPos = 0;
  $len = strlen ($strIn);
  while ($iPos < $len) {
    $charAt = substr ($strIn, $iPos, 1);
    if ($charAt == '%') {
      $iPos++;
      $charAt = substr ($strIn, $iPos, 1);
      if ($charAt == 'u') {
        // Unicode character
        $iPos++;
        $unicodeHexVal = substr ($strIn, $iPos, 4);
        $unicode = hexdec ($unicodeHexVal);
        $strOut .= code2utf($unicode);
        $iPos += 4;
      }
      else {
        // Escaped ascii character
        $hexVal = substr ($strIn, $iPos, 2);
        if (hexdec($hexVal) > 127) {
          // Convert to Unicode 
          $strOut .= code2utf(hexdec ($hexVal));
        }
        else {
          $strOut .= chr (hexdec ($hexVal));
        }
        $iPos += 2;
      }
    }
    else {
      $strOut .= $charAt;
      $iPos++;
    }
  }
  if ($iconv_to != "UTF-8") {
    $strOut = iconv("UTF-8", $iconv_to, $strOut);
  }   
  return $strOut;
} 



?>
