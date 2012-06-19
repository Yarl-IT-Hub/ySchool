<%--
    (C) Copyright 2012-2013 hSenid Software International (Pvt) Limited.
    All Rights Reserved.
 
    These materials are unpublished, proprietary, confidential source code of
    hSenid Software International (Pvt) Limited and constitute a TRADE SECRET
    of hSenid Software International (Pvt) Limited.
 
    hSenid Software International (Pvt) Limited retains all title to and intellectual
    property rights in these materials.
 
   $LastChangedDate$
   $LastChangedBy$
   $LastChangedRevision$
--%>


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

    <%--<title><sitemesh:write property='title'/></title>--%>
    <title>ySchool - A school management system</title>
    <script type="text/javascript" src="/yschool/js/yschool/yschool.js"></script>
    <sitemesh:write property='head'/>
</head>
<body>

<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container-fluid">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <a class="brand" href="#">ySchool</a>

            <div class="nav-collapse">
                <ul class="nav">
                    <li class="active"><a href="#">Home</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
                <p class="navbar-text pull-right">Logged in as <a href="#">username</a></p>
            </div>
            <!--/.nav-collapse -->
        </div>
    </div>
</div>


<div class="container-fluid">
    <div class="row-fluid">
        <div class="span3">
            <div class="well sidebar-nav">
                <ul class="nav nav-list">
                    <li class="nav-header">Manage</li>
                    <li id="student" class="active"><a href="/yschool/faces/student/student.xhtml">Student</a></li>
                    <li><a href="#">Staff</a></li>
                    <li><a href="parent.html">Parents</a></li>
                    <li><a href="#">Admin users</a></li>
                    <li class="nav-header">Assign</li>
                    <li><a href="#">Teachers to classes</a></li>
                    <li id="add-student-to-class"><a href="/yschool/faces/student/add-student-class.xhtml">Add students to class</a></li>
                    <li><a href="#">Assign subjects to Students</a></li>
                    <li><a href="#">Marks Management</a></li>
                    <li><a href="#">Link</a></li>
                    <li><a href="#">Link</a></li>
                    <li class="nav-header">Configure</li>
                    <li><a href="#">Subjects</a></li>
                    <li><a href="#">Terms</a></li>
                    <li><a href="#">School notifications</a></li>
                    <li><a href="#">Manage time table</a></li>
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
        <p><![CDATA[&copy;]> ySchool 2012</p>
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