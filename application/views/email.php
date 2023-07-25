<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1.0, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Language" content="en" />
    <meta name="msapplication-TileColor" content="#2d89ef">
    <meta name="theme-color" content="#4188c9">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">

    <link rel="icon" type="image/x-icon" sizes="32x32" href="<?php echo base_url('/assets/images/logo/'); ?><?php echo get_admin_setting('site_favicon'); ?>" />
    
    <style type="text/css">
      body { margin: 0; font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans",sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji"; font-size: 1rem; font-weight: 400; line-height: 1.5; color: #212529; text-align: left; background-color: #fff; } .h-100{ height: 100% !important; } .w-100{ width: 100% !important; } .container { max-width: 1140px; } .mt-0, .my-0 { margin-top: 0!important; } .row {  -ms-flex-wrap: wrap; flex-wrap: wrap; margin-right: -15px; margin-left: -15px; } .text-center { text-align: center!important; } .p-5 { padding: 3rem!important; } .align-self-center { -ms-flex-item-align: center!important; align-self: center!important; } .font-weight-bold { font-weight: 700!important; } .text-white { color: #fff!important; } .p-5 { padding: 3rem!important; } .h1, h1 { font-size: 2.5rem; } .h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6 { margin-bottom: .5rem; font-weight: 500; line-height: 1.2;} h1, h2, h3, h4, h5, h6 { margin-top: 0; margin-bottom: .5rem; } .pb-4, .py-4 { padding-bottom: 1.5rem!important; } .col { -ms-flex-preferred-size: 0; flex-basis: 0; -ms-flex-positive: 1; flex-grow: 1; max-width: 100%; } p { margin-top: 0; margin-bottom: 1rem; } a { color: #FF7800; } .container{width:100%;padding-right:15px;padding-left:15px;margin-right:auto;margin-left:auto} @media (min-width:576px){.container{max-width:540px}} @media (min-width:768px){.container{max-width:720px}} @media (min-width:992px){.container{max-width:960px}} @media (min-width:1200px){.container{max-width:1140px}} .container-fluid{width:100%;padding-right:15px;padding-left:15px;margin-right:auto;margin-left:auto} .container { width: 100%; padding-right: 15px; padding-left: 15px; margin-right: auto; margin-left: auto; }
      article, aside, figcaption, figure, footer, header, hgroup, main, nav, section { display: block; }
      .pt-4, .py-4 { padding-top: 1.5rem!important; }
      .col-md-12 {  max-width: 100%; }
    </style>


  </head>
  <body class="loading-overlay-showing h-100">
    <div class="body">
      <div role="main" class="main ">
        <div class="w-100  ">
          <div role="main" class="main">
            <?php 
              $header_background = get_admin_setting('mail_template_header_image');
              $header_background = (isset($header_background) && $header_background) ? base_url('/assets/images/logo/'.$header_background) : base_url('/assets/images/headertemplate.png');
              $footer_background = get_admin_setting('mail_template_footer_image');
              $footer_background = (isset($footer_background) && $footer_background) ? base_url('/assets/images/logo/'.$footer_background) : base_url('/assets/images/footertemplate.png');
            ?>
            <section  style="background-size: cover; background-image: url(<?php echo $header_background; ?>);">
              <div class="container">
                <div class="row mt-0">
                  <div class="col-md-12 align-self-center p-static order-2 text-center p-5">
                    <h1 class="text-9 font-weight-bold text-white p-5"><a class="text-white" target="_blank" href="<?php echo base_url()?>"><?php echo get_admin_setting('site_name'); ?> </a></h1>
                    <span class="sub-title text-white"><?php echo $subject; ?></span>
                  </div>
                </div>
              </div>
            </section>
            <div class="container py-4">
              <div class="row">
                <div class="col">
                  <?php echo ($html) ?>
                </div>
              </div>
            </div>


            <section  style="background-size: cover; background-image: url(<?php echo $footer_background; ?>);">
              <div class="container">
                <div class="row mt-0">
                  <div class="col-md-12 align-self-center p-static order-2 text-center p-5">
                    <h6 class="sub-title text-white p-5">Thanks</h6>
                  </div>
                </div>
              </div>
            </section>

          </div>
        </div>
      </div>
      
    </div> 
  </body>
</html>