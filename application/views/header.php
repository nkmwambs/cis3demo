<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="description" content="CodeIgniter AWS S3 Integration is a CodeIgniter Library which provides an easy way to integrate AWS S3 in a CodeIgniter project.">
        <meta name="keywords" content="S3,Codeigniter,Codeigniter S3,S3 integration,CI S3 integration, CI S3, CI integration">
        <meta name="author" content="Scriptigniter">
        <title>Codeigniter AWS S3 integration library: <?php echo $page_title ?></title>
        <link href="<?php echo site_url("css/bootstrap-combined.min.css");?>" rel="stylesheet"/>
        <link href="<?php //echo site_url("bootstrap/css/bootstrap-theme.min.css");?>" rel="stylesheet"/>
        
<!--        <link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css" rel="stylesheet"/>-->
            
                <link rel="stylesheet" href="<?php echo site_url("google-code-prettify/prettify.css") ?>" media="screen"/>
                <script src="<?php echo site_url("google-code-prettify/prettify.js");?>"></script>
                <link rel="shortcut icon" type="image/x-icon" href="<?php echo site_url("images/favicon.ico");?>" />
                <script src="<?php echo site_url("google-code-prettify/jquery.js");?>"></script>
                <script>document.createElement('section');var duration='500',easing='swing';</script>                
                <script src="<?php echo site_url("google-code-prettify/script.js");?>"></script>
                
                </head>
                <body>
                    <div class="container">
                        <div class="header"> 
                            <!--<div style="height:25px;" class="text-center alert">
                          </div>  
                            --> <!-- end .header --></div>
                        <div class="container-fluid" style="margin-top:10px;" >
                            <div class="row-fluid">
                                <div class="span2"> 
                                    <!--Sidebar content--> 
                                </div>
                                <div class="span10">
                                    <!--Body content-->
                                    <div class="content" style="min-height:500px">
                                        <a href="<?php echo site_url("cis3integration"); ?>" class="btn btn-xs <?php echo $this->uri->segment(2)==""?"btn-primary":""; ?>">Demo1</a>
                                        <a href="<?php echo site_url("cis3integration/manual_upload_demo"); ?>" class="btn btn-xs <?php echo $this->uri->segment(2)=="manual_upload_demo"?"btn-primary":""; ?>">Demo2</a>
                                        <a href="<?php echo site_url("cis3integration/copy_s3_file"); ?>" class="btn btn-xs <?php echo $this->uri->segment(2)=="copy_s3_file"?"btn-primary":""; ?>">Copy S3 File Demo</a>
                                        <a href="<?php echo site_url("cis3integration/create_bucket/cis3demotestingbucket"); ?>" class="btn btn-xs <?php echo $this->uri->segment(2)=="create_bucket"?"btn-primary":""; ?>">Create a Bucket Demo</a>
                                        <a href="<?php echo site_url("cis3integration/create_presigned_url"); ?>" class="btn btn-xs <?php echo $this->uri->segment(2)=="create_presigned_url"?"btn-primary":""; ?>">Create Presigned URL</a>
                                        <a href="<?php echo site_url("cis3integration/browser_upload"); ?>" class="btn btn-xs <?php echo $this->uri->segment(2)=="browser_upload"?"btn-primary":""; ?>">Browser Upload</a>
                                        <!-- <span style="float:right">
                                            <a href="http://codecanyon.net/item/codeigniter-aws-s3-integration-library/4993914?ref=scriptigniter"><strong>Buy The Script</strong></a>
                                        </span> -->

