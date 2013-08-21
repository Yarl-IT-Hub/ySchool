<%@ page contentType="text/html;charset=UTF-8" language="java" %>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="/yschool/css/bootstrap.css" rel="stylesheet">

    <style type="text/css">
        body {
            padding-top: 60px;
            padding-bottom: 40px;
        }

        .sidebar-nav {
            padding: 9px 0;
        }
    </style>

    <link href="/yschool/css/bootstrap-responsive.css" rel="stylesheet">
    <link rel="shortcut icon" href="/yschool/images/favicon.ico">
    <link rel="apple-touch-icon" href="/yschool/images/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/yschool/images/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/yschool/images/apple-touch-icon-114x114.png">

    <title><sitemesh:write property='title'/></title>
    <script type="text/javascript" src="/yschool/js/yschool/yschool.js"></script>
    <sitemesh:write property='head'/>
</head>
<body>

<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container-fluid">

            <a class="brand" href="/yschool/faces/home/myHome.xhtml">ySchool Home</a>

            <div class="nav-collapse">
                <ul class="nav pull-right">
                    <li class="dropdown">
                        <a href="#"
                           class="dropdown-toggle"
                           data-toggle="dropdown">
                            userName
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="#">My Account</a></li>
                            <li><a href="#">Log out</a></li>
                        </ul>
                    </li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>
            <!--/.nav-collapse -->
        </div>
    </div>
</div>


<div class="container-fluid">
    <div class="row-fluid">

        <div class="span2">
            <div class="well sidebar-nav">
                <ul class="nav nav-list">
                    <li id="student"><a href="/yschool/faces/student/studentHome.xhtml">Student</a></li>
                    <li id="staff"><a href="/yschool/faces/staff/staffHome.xhtml">Staff</a></li>
                    <li id="examinations"><a href="/yschool/faces/staff/examinationsHome.xhtml">Examinations</a></li>
                    <li id="arrangements"><a href="/yschool/faces/staff/arrangementsHome.xhtml">Arrangements</a></li>
                    <li id="settings"><a href="/yschool/faces/staff/settingsHome.xhtml">Settings</a></li>
                    <li id="analytics"><a href="/yschool/faces/staff/analyticsHome.xhtml">Analytics</a></li>


                </ul>
            </div>
            <!--/.well -->
        </div>
        <!--/span-->
        <sitemesh:write property='body'/>
    </div>
    <!--/row-->

    <hr/>

    <footer>
        <p><![CDATA[&copy;]> ySchool 2013</p>
    </footer>

</div>
<!--/.fluid-container-->


<!-- Le javascript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="/yschool/js/jquery.js"></script>
<script src="/yschool/js/jquery.dataTables.js"></script>
<script src="/yschool/js/bootstrap-transition.js"></script>
<script src="/yschool/js/bootstrap-alert.js"></script>
<script src="/yschool/js/bootstrap-modal.js"></script>
<script src="/yschool/js/bootstrap-dropdown.js"></script>
<script src="/yschool/js/bootstrap-scrollspy.js"></script>
<script src="/yschool/js/bootstrap-tab.js"></script>
<script src="/yschool/js/bootstrap-tooltip.js"></script>
<script src="/yschool/js/bootstrap-popover.js"></script>
<script src="/yschool/js/bootstrap-button.js"></script>
<script src="/yschool/js/bootstrap-collapse.js"></script>
<script src="/yschool/js/bootstrap-carousel.js"></script>
<script src="/yschool/js/bootstrap-typeahead.js"></script>
</body>
</html>