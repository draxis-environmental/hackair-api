<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>HackAir - Reset your Password</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <style>
        /*!
 * Start Bootstrap - Freelancer v3.3.7+1 (http://startbootstrap.com/template-overviews/freelancer)
 * Copyright 2013-2016 Start Bootstrap
 * Licensed under MIT (https://github.com/BlackrockDigital/startbootstrap/blob/gh-pages/LICENSE)
 */
        body {
            font-family: 'Lato', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            overflow-x: hidden;
        }

        p {
            font-size: 20px;
        }

        p.small {
            font-size: 16px;
        }

        a,
        a:hover,
        a:focus,
        a:active,
        a.active {
            color: #18BC9C;
            outline: none;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: "Montserrat", "Helvetica Neue", Helvetica, Arial, sans-serif;
            text-transform: uppercase;
            font-weight: 700;
        }

        hr.star-light,
        hr.star-primary {
            padding: 0;
            border: none;
            border-top: solid 5px;
            text-align: center;
            max-width: 250px;
            margin: 25px auto 30px;
        }

        hr.star-light:after,
        hr.star-primary:after {

            font-family: FontAwesome;
            display: inline-block;
            position: relative;
            top: -0.8em;
            font-size: 2em;
            padding: 0 0.25em;
        }

        hr.star-light {
            border-color: white;
        }

        hr.star-light:after {
            background-color: #2C3E50;
            color: white;
        }

        hr.star-primary {
            border-color: #2C3E50;
        }

        hr.star-primary:after {
            background-color: white;
            color: #2C3E50;
        }

        .img-centered {
            margin: 0 auto;
        }

        header {
            text-align: center;
            background: #6cc7cd;
            color: white;
        }

        header .container {
            padding-top: 20px;
            padding-bottom: 50px;
        }

        header img {
            display: block;
            margin: 0 auto 20px;
        }

        header .intro-text .name {
            display: block;
            font-family: "Montserrat", "Helvetica Neue", Helvetica, Arial, sans-serif;
            text-transform: uppercase;
            font-weight: 700;
            font-size: 2em;
        }

        header .intro-text .skills {
            font-size: 1.25em;
            font-weight: 300;
        }

        @media (min-width: 768px) {
            header .container {
                padding-top: 20px;
                padding-bottom: 100px;
            }

            header .intro-text .name {
                font-size: 4.75em;
            }

            header .intro-text .skills {
                font-size: 1.75em;
            }
        }

        .navbar-custom {
            background: #2C3E50;
            font-family: "Montserrat", "Helvetica Neue", Helvetica, Arial, sans-serif;
            text-transform: uppercase;
            font-weight: 700;
            border: none;
        }

        .navbar-custom a:focus {
            outline: none;
        }

        .navbar-custom .navbar-brand {
            color: white;
        }

        .navbar-custom .navbar-brand:hover,
        .navbar-custom .navbar-brand:focus,
        .navbar-custom .navbar-brand:active,
        .navbar-custom .navbar-brand.active {
            color: white;
        }

        .navbar-custom .navbar-nav {
            letter-spacing: 1px;
        }

        .navbar-custom .navbar-nav li a {
            color: white;
        }

        .navbar-custom .navbar-nav li a:hover {
            color: #18BC9C;
            outline: none;
        }

        .navbar-custom .navbar-nav li a:focus,
        .navbar-custom .navbar-nav li a:active {
            color: white;
        }

        .navbar-custom .navbar-nav li.active a {
            color: white;
            background: #18BC9C;
        }

        .navbar-custom .navbar-nav li.active a:hover,
        .navbar-custom .navbar-nav li.active a:focus,
        .navbar-custom .navbar-nav li.active a:active {
            color: white;
            background: #18BC9C;
        }

        .navbar-custom .navbar-toggle {
            color: white;
            text-transform: uppercase;
            font-size: 10px;
            border-color: white;
        }

        .navbar-custom .navbar-toggle:hover,
        .navbar-custom .navbar-toggle:focus {
            background-color: #18BC9C;
            color: white;
            border-color: #18BC9C;
        }

        @media (min-width: 768px) {
            .navbar-custom {
                padding: 25px 0;
                -webkit-transition: padding 0.3s;
                -moz-transition: padding 0.3s;
                transition: padding 0.3s;
            }

            .navbar-custom .navbar-brand {
                font-size: 2em;
                -webkit-transition: all 0.3s;
                -moz-transition: all 0.3s;
                transition: all 0.3s;
            }

            .navbar-custom.affix {
                padding: 10px 0;
            }

            .navbar-custom.affix .navbar-brand {
                font-size: 1.5em;
            }
        }

        section {
            padding: 100px 0;
        }

        section h2 {
            margin: 0;
            font-size: 3em;
        }

        section.success {
            background: #18BC9C;
            color: white;
        }

        @media (max-width: 767px) {
            section {
                padding: 75px 0;
            }

            section.first {
                padding-top: 75px;
            }
        }

        #portfolio .portfolio-item {
            margin: 0 0 15px;
            right: 0;
        }

        #portfolio .portfolio-item .portfolio-link {
            display: block;
            position: relative;
            max-width: 400px;
            margin: 0 auto;
        }

        #portfolio .portfolio-item .portfolio-link .caption {
            background: rgba(24, 188, 156, 0.9);
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: all ease 0.5s;
            -webkit-transition: all ease 0.5s;
            -moz-transition: all ease 0.5s;
        }

        #portfolio .portfolio-item .portfolio-link .caption:hover {
            opacity: 1;
        }

        #portfolio .portfolio-item .portfolio-link .caption .caption-content {
            position: absolute;
            width: 100%;
            height: 20px;
            font-size: 20px;
            text-align: center;
            top: 50%;
            margin-top: -12px;
            color: white;
        }

        #portfolio .portfolio-item .portfolio-link .caption .caption-content i {
            margin-top: -12px;
        }

        #portfolio .portfolio-item .portfolio-link .caption .caption-content h3,
        #portfolio .portfolio-item .portfolio-link .caption .caption-content h4 {
            margin: 0;
        }

        #portfolio * {
            z-index: 2;
        }

        @media (min-width: 767px) {
            #portfolio .portfolio-item {
                margin: 0 0 30px;
            }
        }

        .floating-label-form-group {
            position: relative;
            margin-bottom: 0;
            padding-bottom: 0.5em;
            border-bottom: 1px solid #eeeeee;
        }

        .floating-label-form-group input,
        .floating-label-form-group textarea {
            z-index: 1;
            position: relative;
            padding-right: 0;
            padding-left: 0;
            border: none;
            border-radius: 0;
            font-size: 1.5em;
            background: none;
            box-shadow: none !important;
            resize: none;
        }

        .floating-label-form-group label {
            display: block;
            z-index: 0;
            position: relative;
            top: 2em;
            margin: 0;
            font-size: 0.85em;
            line-height: 1.764705882em;
            vertical-align: middle;
            vertical-align: baseline;
            opacity: 0;
            -webkit-transition: top 0.3s ease, opacity 0.3s ease;
            -moz-transition: top 0.3s ease, opacity 0.3s ease;
            -ms-transition: top 0.3s ease, opacity 0.3s ease;
            transition: top 0.3s ease, opacity 0.3s ease;
        }

        .floating-label-form-group:not(:first-child) {
            padding-left: 14px;
            border-left: 1px solid #eeeeee;
        }

        .floating-label-form-group-with-value label {
            top: 0;
            opacity: 1;
        }

        .floating-label-form-group-with-focus label {
            color: #18BC9C;
        }

        form .row:first-child .floating-label-form-group {
            border-top: 1px solid #eeeeee;
        }

        footer {
            color: white;
        }

        footer h3 {
            margin-bottom: 30px;
        }

        footer .footer-above {
            padding-top: 50px;
            background-color: #2C3E50;
        }

        footer .footer-col {
            margin-bottom: 50px;
        }

        footer .footer-below {
            padding: 25px 0;
            background-color: #233140;
        }

        .btn-outline {
            color: white;
            font-size: 20px;
            border: solid 2px white;
            background: transparent;
            transition: all 0.3s ease-in-out;
            margin-top: 15px;
        }

        .btn-outline:hover,
        .btn-outline:focus,
        .btn-outline:active,
        .btn-outline.active {
            color: #18BC9C;
            background: white;
            border: solid 2px white;
        }

        .btn-primary {
            color: white;
            background-color: #2C3E50;
            border-color: #2C3E50;
            font-weight: 700;
        }

        .btn-primary:hover,
        .btn-primary:focus,
        .btn-primary:active,
        .btn-primary.active,
        .open .dropdown-toggle.btn-primary {
            color: white;
            background-color: #1a242f;
            border-color: #161f29;
        }

        .btn-primary:active,
        .btn-primary.active,
        .open .dropdown-toggle.btn-primary {
            background-image: none;
        }

        .btn-primary.disabled,
        .btn-primary[disabled],
        fieldset[disabled] .btn-primary,
        .btn-primary.disabled:hover,
        .btn-primary[disabled]:hover,
        fieldset[disabled] .btn-primary:hover,
        .btn-primary.disabled:focus,
        .btn-primary[disabled]:focus,
        fieldset[disabled] .btn-primary:focus,
        .btn-primary.disabled:active,
        .btn-primary[disabled]:active,
        fieldset[disabled] .btn-primary:active,
        .btn-primary.disabled.active,
        .btn-primary[disabled].active,
        fieldset[disabled] .btn-primary.active {
            background-color: #2C3E50;
            border-color: #2C3E50;
        }

        .btn-primary .badge {
            color: #2C3E50;
            background-color: white;
        }

        .btn-success {
            color: white;
            background-color: #18BC9C;
            border-color: #18BC9C;
            font-weight: 700;
        }

        .btn-success:hover,
        .btn-success:focus,
        .btn-success:active,
        .btn-success.active,
        .open .dropdown-toggle.btn-success {
            color: white;
            background-color: #128f76;
            border-color: #11866f;
        }

        .btn-success:active,
        .btn-success.active,
        .open .dropdown-toggle.btn-success {
            background-image: none;
        }

        .btn-success.disabled,
        .btn-success[disabled],
        fieldset[disabled] .btn-success,
        .btn-success.disabled:hover,
        .btn-success[disabled]:hover,
        fieldset[disabled] .btn-success:hover,
        .btn-success.disabled:focus,
        .btn-success[disabled]:focus,
        fieldset[disabled] .btn-success:focus,
        .btn-success.disabled:active,
        .btn-success[disabled]:active,
        fieldset[disabled] .btn-success:active,
        .btn-success.disabled.active,
        .btn-success[disabled].active,
        fieldset[disabled] .btn-success.active {
            background-color: #18BC9C;
            border-color: #18BC9C;
        }

        .btn-success .badge {
            color: #18BC9C;
            background-color: white;
        }

        .btn-social {
            display: inline-block;
            height: 50px;
            width: 50px;
            border: 2px solid white;
            border-radius: 100%;
            text-align: center;
            font-size: 20px;
            line-height: 45px;
        }

        .btn:focus,
        .btn:active,
        .btn.active {
            outline: none;
        }


    </style>

</head>

<body id="page-top" class="index">

<!-- Navigation -->

<!-- Header -->
<header>

    <div class="container">
        <div class="row">

            <div class="col-lg-12">

                <div class="intro-text">

                    <div class="container" style="color:#2C3E50">
                        <div class="row">
                            <div class="col-sm-12">
                                <img class="img-responsive" src="" alt="">


                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 col-sm-offset-3" >
                                <?php
                                if($success == 1) { ?>
                                <p class="text-center" style="color:green;font-weight:bold">Your password has been changed successfully</p>
                                <?php
                                }
                                else {
                                ?>
                                    <p class="text-center" style="color:red;font-weight:bold">Password does not match the confirm password</p>
                                <?php
                                } ?>

                                <div class="row">

                                    <div class="col-sm-6">
                                        &nbsp;
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        &nbsp;
                                    </div>
                                </div>

                            </div><!--/col-sm-6-->
                        </div><!--/row-->
                    </div>


                    <hr class="star-light">
                </div>
            </div>
        </div>
    </div>
</header>


</body>

</html>
