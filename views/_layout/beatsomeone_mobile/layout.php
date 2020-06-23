<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta property="fb:app_id" content="579999516228616"/>

    <meta property="og:type" content="website"/>
    <meta property="og:image" content="<?php echo html_escape(element('og_image', $layout)); ?>"/>
    <meta property="og:url" content="<?php echo html_escape(element('og_url', $layout)); ?>"/>
    <meta property="og:description" content="<?php echo html_escape(element('og_description', $layout)); ?>"/>
    <meta property="og:title" content="<?php echo html_escape(element('og_title', $layout)); ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <title><?php echo html_escape(element('page_title', $layout)); ?></title>

    <link rel="stylesheet" type="text/css" href="/dist/chunk-common.css" />
    <script src="/dist/chunk-common.js?v=<?php echo time(); ?>"></script>
    <script src="/dist/chunk-vendors.js?v=<?php echo time(); ?>"></script>
    <script src="/src/common.js?v=<?php echo time(); ?>"></script>

    <?php echo $this->managelayout->display_css(); ?>
</head>
<body>

<div id="app">
    <?php if (isset($yield))echo $yield; ?>
</div>

</body>


<script type="text/javascript">
    $(document).on('click', '.viewpcversion', function(){
        Cookies.set('device_view_type', 'desktop', { expires: 1 });
    });
    $(document).on('click', '.viewmobileversion', function(){
        Cookies.set('device_view_type', 'mobile', { expires: 1 });
    });
</script>

<?php echo $this->managelayout->display_js(); ?>

<?php echo $this->managelayout->display_script(); ?>

</html>

