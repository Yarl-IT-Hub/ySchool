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

function DrawHeader($left='',$right='',$center='')
{	global $_openSIS;

	if(!isset($_openSIS['DrawHeader']))
		$_openSIS['DrawHeader'] = '';

	if($_openSIS['DrawHeader'] == '')
	{
		$attribute = 'b';
		$font_color = '';
		
	}
	else
	{
		$attribute = '';
		$font_color = '';
	}

	echo '<TABLE width=100%  border=0 cellpadding=2 cellspacing=2 align=center><TR>';
	if($left)
		echo '<TD '.$_openSIS['DrawHeader'].' align=left class=drawheader><'.$attribute.'>'.$left.'</'.substr($attribute,0,4).'></TD>';
	if($center)
		echo '<TD '.$_openSIS['DrawHeader'].' align=center class=drawheader ><'.$attribute.'>'.$center.'</'.$attribute.'></TD>';
	if($right)
		echo '<TD align=right class=drawheader'.$_openSIS['DrawHeader'].' ><'.$attribute.'>'.$right.'</'.substr($attribute,0,4).'></TD>';
	echo '</TR></TABLE>';

	if($_openSIS['DrawHeaderHome'] == '' && !$_REQUEST['_openSIS_PDF'])
		$_openSIS['DrawHeaderHome'] = ' style="border:0;border-style: none none none none;"';
	//$_openSIS['DrawHeader'] = '';
	else
	//	$_openSIS['DrawHeader'] = ' style="border:1;border-style: none none solid none;"';
		$_openSIS['DrawHeaderHome'] = '';
}
?>