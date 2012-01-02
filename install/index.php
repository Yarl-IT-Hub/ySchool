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
$page_name= basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>openSIS Installer</title>
<link rel="stylesheet" href="../styles/installer.css" type="text/css" />
</head>
<body>
<div class="clear"></div>
 <div class="clear"></div>
  <div class="clear"></div>
   <div class="clear"></div>
   <div class="clear"></div>
 <div class="clear"></div>
<table style="height:100%;width:100%;" width='100%' border='0' cellspacing='0' cellpadding='0'>
  <tr>
    <td valign='middle' height='100%'><table class='wrapper' border='0' cellspacing='0' cellpadding='0' align='center'>
        <tr>
          <td class='header'><table width='100%' border='0' cellspacing='0' cellpadding='0' class='logo_padding'>
              <tr>
                <td><img src='../assets/osis_logo.png' height='63' width='152' border='0' /></td>
                <td align='right'><a href='http://www.os4ed.com' target=_blank ><img src='../assets/os4ed_logo.png' height='62' width='66' border='0'/></a></td>
              </tr>
            </table></td>
        </tr>
        <tr>
          <td class='content'>
          <!--<div class="animated_bg">-->
			<table width='100%' border='0' cellspacing='0' cellpadding='0'>
              <tr>
                <td>
                	<table width='100%' border='0' cellspacing='0' cellpadding='0'>
                    <tr>
                      <td class='padding'>
<?php
$url = 'step0.php';

if($_GET["upreq"] == 'true')
{
    $url .= '?upreq=true';
}
?>
                          <iframe src="<?php echo $url; ?>" scrolling="no" frameborder="0" style="background-color:transparent; height:271px; width:100%"></iframe>
                       </td>
                    </tr>
                  </table>
                  </td>
              </tr>
            </table>
          <!--</div>-->
            </td>
            </tr>
        <tr>
          <td class='footer' valign='top'><table width='100%' border='0' cellspacing='0' cellpadding='0'>
              <tr>
                <td class='margin'></td>
              </tr>
              <tr>
                <td align='center' class='copyright'>Copyright  &copy; 2011 Open Solutions for Education, Inc. (<a href='http://www.os4ed.com' target='_blank'>OS4Ed</a>). openSIS is licensed under the <a href='http://www.gnu.org/licenses/gpl.html' target='_blank'>GPL License</a>.</td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
</table>
</body>
</html>
