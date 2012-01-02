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
include("functions/ParamLib.php");
$url=validateQueryString(curPageURL());
if($url===FALSE){
 header('Location: index.php');
 }
include('Redirect_root.php'); 
error_reporting(E_ERROR);
$start_time = time();
include 'Warehouse.php';
array_rwalk($_REQUEST,'strip_tags');

$css = getCSS();
echo "<link rel=stylesheet type=\"text/css\" href=\"assets/stylesheet.css\">";
echo "<link rel='stylesheet' type='text/css' href='themes/".trim($css)."/".trim($css).".css'>";
echo "<BODY border=0 style= \"margin:0px;\" class=block_bg >";

if(!isset($_REQUEST['_openSIS_PDF']))
{
	Warehouse('header');

	//if(strpos($_REQUEST['modname'],'misc/')===false && $_REQUEST['modname']!='Students/Student.php' && $_REQUEST['modname']!='School_Setup/Calendar.php' && $_REQUEST['modname']!='Scheduling/Schedule.php' && $_REQUEST['modname']!='Attendance/Percent.php' && $_REQUEST['modname']!='Attendance/Percent.php?list_by_day=true' && $_REQUEST['modname']!='Scheduling/MassRequests.php' && $_REQUEST['modname']!='Scheduling/MassSchedule.php' && $_REQUEST['modname']!='Student_Billing/Fees.php')
	
	
	//if(strpos($_REQUEST['modname'],'misc/')===false)
	if(strpos(optional_param('modname','',PARAM_NOTAGS),'misc/')===false)
		
		/* This is removed for Use
		echo '<script language="JavaScript">if(window == top  && (!window.opener || window.opener.location.href.substring(0,(window.opener.location.href.indexOf("&")!=-1?window.opener.location.href.indexOf("&"):window.opener.location.href.replace("#","").length))!=window.location.href.substring(0,(window.location.href.indexOf("&")!=-1?window.location.href.indexOf("&"):window.location.href.replace("#","").length)))) window.location.href = "index.php";</script>';
		*/
		
		

	echo '<DIV id="Migoicons" style="visibility:hidden;position:absolute;z-index:1000;top:-100"></DIV>';
	echo "<TABLE width=100% border=0 cellpadding=4 height=100%><TR><TD valign=middle align=center height=100% >";
	PopTable_wo_header('header');
}

if(clean_param($_REQUEST['modname'],PARAM_NOTAGS))
{
	if($_REQUEST['_openSIS_PDF']=='true')
		ob_start();
	//if(strpos($_REQUEST['modname'],'?')!==false)
	if(strpos(optional_param('modname','',PARAM_NOTAGS),'?')!==false)
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

//	if($_REQUEST['LO_save']!='1' && !isset($_REQUEST['_openSIS_PDF']) && (strpos($modname,'misc/')===false || $modname=='misc/Registration.php' || $modname=='misc/Export.php' || $modname=='misc/Portal.php'))
    if(optional_param('LO_save','',PARAM_INT)!='1' && !isset($_REQUEST['_openSIS_PDF']) && (strpos(optional_param($modname,'',PARAM_NOTAGS),'misc/')===false || $modname=='misc/Registration.php' || $modname=='misc/Export.php' || $modname=='misc/Portal.php'))
		$_SESSION['_REQUEST_vars'] = $_REQUEST;

	$allowed = false;
	include 'Menu.php';
	foreach($_openSIS['Menu'] as $modcat=>$programs)
	{
	//	if($_REQUEST['modname']==$modcat.'/Search.php')
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

	if($allowed)
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
			//DBQuery("INSERT INTO HACKING_LOG (HOST_NAME,IP_ADDRESS,LOGIN_DATE,VERSION,PHP_SELF,DOCUMENT_ROOT,SCRIPT_NAME,MODNAME,USERNAME) values('$_SERVER[SERVER_NAME]','$ip','".date('Y-m-d')."','$openSISVersion','$_SERVER[PHP_SELF]','$_SERVER[DOCUMENT_ROOT]','$_SERVER[SCRIPT_NAME]','$_REQUEST[modname]','".User('USERNAME')."')");
		    DBQuery("INSERT INTO HACKING_LOG (HOST_NAME,IP_ADDRESS,LOGIN_DATE,VERSION,PHP_SELF,DOCUMENT_ROOT,SCRIPT_NAME,MODNAME,USERNAME) values('$_SERVER[SERVER_NAME]','$ip','".date('Y-m-d')."','$openSISVersion','$_SERVER[PHP_SELF]','$_SERVER[DOCUMENT_ROOT]','$_SERVER[SCRIPT_NAME]','".optional_param('modname','',PARAM_NOTAGS)."','".User('USERNAME')."')");
        	Warehouse('footer');
			if($openSISNotifyAddress)
				//mail($openSISNotifyAddress,'HACKING ATTEMPT',"INSERT INTO HACKING_LOG (HOST_NAME,IP_ADDRESS,LOGIN_DATE,VERSION,PHP_SELF,DOCUMENT_ROOT,SCRIPT_NAME,MODNAME,USERNAME) values('$_SERVER[SERVER_NAME]','$ip','".date('Y-m-d')."','$openSISVersion','$_SERVER[PHP_SELF]','$_SERVER[DOCUMENT_ROOT]','$_SERVER[SCRIPT_NAME]','$_REQUEST[modname]','".User('USERNAME')."')");
			   mail($openSISNotifyAddress,'HACKING ATTEMPT',"INSERT INTO HACKING_LOG (HOST_NAME,IP_ADDRESS,LOGIN_DATE,VERSION,PHP_SELF,DOCUMENT_ROOT,SCRIPT_NAME,MODNAME,USERNAME) values('$_SERVER[SERVER_NAME]','$ip','".date('Y-m-d')."','$openSISVersion','$_SERVER[PHP_SELF]','$_SERVER[DOCUMENT_ROOT]','$_SERVER[SCRIPT_NAME]','".optional_param('modname','',PARAM_NOTAGS)."','".User('USERNAME')."')");
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


if(!isset($_REQUEST['_openSIS_PDF']))
{
PopTable('footer');
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

?>