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
// if(!$_SESSION['STAFF_ID'] || !$_SESSION['STUDENT_ID'] || (strpos($_SERVER['PHP_SELF'],'index.php'))===false)
//	{
//		header('Location: index.php');
//		exit;
//	}
error_reporting(1);
include "./Warehouse.php";
$url=validateQueryString(curPageURL());
if($url===FALSE)
 {
 header('Location: index.php');
 }

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHA)=='print')
{
	$_REQUEST = $_SESSION['_REQUEST_vars'];
	$_REQUEST['_openSIS_PDF'] = true;
	if(strpos($_REQUEST['modname'],'?')!==false)
		$modname = substr($_REQUEST['modname'],0,strpos($_REQUEST['modname'],'?'));
	else
		$modname = $_REQUEST['modname'];
	ob_start();
	include('modules/'.$modname);
	if($htmldocPath)
	{
		if($htmldocAssetsPath)
			$html = eregi_replace('</?CENTER>','',str_replace('assets/',$htmldocAssetsPath,ob_get_contents()));
		else
			$html = eregi_replace('</?CENTER>','',ob_get_contents());
		ob_end_clean();

		// get a temp filename, and then change its extension from .tmp to .html to make htmldoc happy.
		$temphtml=tempnam('','html');
		$temphtml_tmp=substr($temphtml, 0, strrpos($temphtml, ".")).'html';
		rename($temphtml_tmp, $temphtml);

		$fp=@fopen($temphtml,"w+");
		if (!$fp)
			die("Can't open $temphtml");
		fputs($fp,'<HTML><BODY>'.$html.'</BODY></HTML>');
		@fclose($fp);

		header("Cache-Control: public");
		header("Pragma: ");
		header("Content-Type: application/pdf");
		header("Content-Disposition: inline; filename=\"".ProgramTitle().".pdf\"\n");

		$orientation = 'portrait';
		if($_REQUEST['expanded_view'] || $_SESSION['orientation'] == 'landscape')
		{
			$orientation = 'landscape';
			unset($_SESSION['orientation']);
		}
		passthru("$htmldocPath --webpage --quiet -t pdf12 --jpeg --no-links --$orientation --footer t --header . --left 0.5in --top 0.5in \"$temphtml\"");
		@unlink($temphtml);
	}
	else
	{
		$html = eregi_replace('</?CENTER>','',ob_get_contents());
		ob_end_clean();
		echo '<HTML><BODY>'.$html.'</BODY></HTML>';
	}
}
else
{
	echo "
	<HTML>
		<HEAD><TITLE>openSIS School Software</TITLE>
		<SCRIPT>
		size = 30;
		function expandFrame()
		{
			if(size==30)
			{
				parent.document.getElementById('mainframeset').rows=\"*,200\";
				size = 200;
			}
			else
			{
				parent.document.getElementById('mainframeset').rows=\"*,30\";
				size = 30;
			}
		}
		</SCRIPT>
		<link rel=stylesheet type=text/css href=styles/help.css>
		</HEAD>
		<BODY><table width=100%; cellspacing=0 cellpadding=0><tr><td>";
	echo '<CENTER>';

	echo '<TABLE cellspacing=0 cellpadding=2 class=help_bg><TR><td  colspan=2></td></TR><TR>';
	if($_SESSION['List_PHP_SELF'] && User('PROFILE')!='parent' && User('PROFILE')!='student')
		//echo '<TD width=50% valign=middle align=right style="padding-left:20px;"><A HREF='.$_SESSION['List_PHP_SELF'].'&bottom_back=true target=body><IMG SRC=images/back.gif border=0 vspace=0></A><A HREF='.$_SESSION['List_PHP_SELF'].'&bottom_back=true target=body>Back to Student List</A></TD>';
	if($_SESSION['Search_PHP_SELF'] && User('PROFILE')!='parent' && User('PROFILE')!='student')
		//echo '<TD width=50% valign=middle align=left style="padding-left:20px;"><A HREF='.$_SESSION['Search_PHP_SELF'].'&bottom_back=true target=body><IMG SRC=images/back.gif border=0 vspace=0></A><A HREF='.$_SESSION['Search_PHP_SELF'].'&bottom_back=true target=body>Back to Search Screen</A></TD>';
//	echo '<TD width=24><A HREF=Bottom.php?modfunc=print target=body><IMG SRC=assets/print.gif border=0 vspace=0></A></TD><TD valign=middle class=BottomButton><A HREF=Bottom.php?modfunc=print target=body>Print</A></TD><TD><A HREF=# onclick=expandFrame();return false;><IMG SRC=assets/help.gif border=0 vspace=0></A></TD><TD valign=middle class=BottomButton><A HREF=# onclick="expandFrame();return false;">Help</A></TD><TD><A HREF=index.php?modfunc=logout target=_top><IMG SRC=assets/logout.gif border=0 vspace=0 hspace=0></A></TD><TD valign=middle class=BottomButton><A HREF=index.php?modfunc=logout target=_top>Logout</A></TD>';
	
	echo '</TR></TABLE>';
	echo '</CENTER>';

	include 'Help.php';
	include 'Menu.php';
	$profile = User('PROFILE');
	echo '<div style=" width:470px; height:188px; background-color:transparent; overflow-x:hidden; overflow-y:scroll; text-align:left"><div style="padding:0px 12px 0px 12px;">';
                  if($_REQUEST['modcat'] && $_REQUEST['modname'])
	{
		echo '<b>'.str_replace('_',' ',$_REQUEST['modcat']);
		echo ' : '.$_openSIS['Menu'][$_REQUEST['modcat']][$_REQUEST['modname']];
		echo '</b>';
	}
                  if($help[$_REQUEST['modcat']] && !$_REQUEST['modname'])
	{
		if($student==true)
			$help[$_REQUEST['modcat']] = str_replace('your child','yourself',str_replace('your child\'s','your',$help[$_REQUEST['modcat']]));
                                    echo $help[$_REQUEST['modcat']];
                  }
	elseif($help[$_REQUEST['modname']])
	{
		if($student==true)
			$help[$_REQUEST['modname']] = str_replace('your child','yourself',str_replace('your child\'s','your',$help[$_REQUEST['modname']]));

		echo $help[$_REQUEST['modname']];
	}
	else
		echo $help['default'];
	echo '</div></DIV>';
	echo '</td></tr></table></BODY>';
	echo '</HTML>';
}
?>