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
function button($type,$text='',$link='',$width='',$extra)
{
	if($type=='dot')
	{
		$button = '<TABLE border=0 cellpadding=0 cellspacing=0 height='.$width.' width='.$width.' bgcolor=#'.$text.'><TR><TD>';
		$button .= '<IMG SRC=assets/dot.gif height='.$width.' width='.$width.' border=0 vspace=0 hspace=0>';
		$button .= '</TD></TR></TABLE>';
	}
	else
	{
		if($text)
			$button = '<TABLE border=0 cellpadding=0 cellspacing=0 height=10><TR><TD>';
		if($link)
			$button .= "<A HREF=".$link." onclick='grabA(this); return false;'>";
		$button .= "<IMG SRC='assets/".$type."_button.gif' ".($width?"width=$width":'')." ".$extra." border=0 vspace=0 >";
		if($link)
			$button .= '</A>';

		if($text)
		{
			$button .= "</TD><TD valign=middle>&nbsp;";
			$button .= "<b>";
			if($link)
				$button .= "&nbsp;<A HREF=".$link." onclick='grabA(this); return false;'>";
			$button .= $text;
			if($link)
				$button .= '</A>';
			$button .= "</b>";
			$button .= "</TD>";
			$button .= "</TR></TABLE>";
		}
	}

	return $button;
}



function button_missing_atn($type,$text='',$link='',$cur_cp_id='',$width='',$extra)
{
	if($type=='dot')
	{
		$button = '<TABLE border=0 cellpadding=0 cellspacing=0 height='.$width.' width='.$width.' bgcolor=#'.$text.'><TR><TD>';
		$button .= '<IMG SRC=assets/dot.gif height='.$width.' width='.$width.' border=0 vspace=0 hspace=0>';
		$button .= '</TD></TR></TABLE>';
	}
	else
	{
		if($text)
			$button = '<TABLE border=0 cellpadding=0 cellspacing=0 height=10><TR><TD>';
		if($link)
			$button .= "<A HREF=".$link." onclick='grabA(this); return false;' onclick=>";
		#$button .= "<IMG SRC='assets/".$type."_button.gif' ".($width?"width=$width":'')." ".$extra." border=0 vspace=0 >";
		
		if($_SESSION['take_mssn_attn']){
		$button .="<b><span onclick=javascript:document.getElementById('".$cur_cp_id."').selected='selected';>".'Take Attendance'."</span></b>";
	
		}else{
		$button .="<b>Take Attendance</b>";
		}
		if($link)
			$button .= '</A>';

		if($text)
		{
			$button .= "</TD><TD valign=middle>&nbsp;";
			$button .= "<b>";
			if($link)
				$button .= "&nbsp;<A HREF=".$link." onclick='grabA(this); return false;'>";
			$button .= $text;
			if($link)
				$button .= '</A>';
			$button .= "</b>";
			$button .= "</TD>";
			$button .= "</TR></TABLE>";
		}
	}

	return $button;
}


?>
