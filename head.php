<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<!--[if lt IE 9]>
	<script src="<?php echo esc_url( get_template_directory_uri() ); ?>/js/html5.js"></script>
	<![endif]-->

	<!-- Favicons -->
	<link rel="apple-touch-icon" sizes="180x180" href="<?php echo get_stylesheet_directory_uri() ?>/dist/assets/img/favicons/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="<?php echo get_stylesheet_directory_uri() ?>/dist/assets/img/favicons/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="<?php echo get_stylesheet_directory_uri() ?>/dist/assets/img/favicons/favicon-16x16.png">
	<link rel="manifest" href="<?php echo get_stylesheet_directory_uri() ?>/dist/assets/img/favicons/site.webmanifest">
	<link rel="mask-icon" href="<?php echo get_stylesheet_directory_uri() ?>/dist/assets/img/favicons/safari-pinned-tab.svg" color="#5bbad5">
	<link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri() ?>/dist/assets/img/favicons/favicon.ico">
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-config" content="<?php echo get_stylesheet_directory_uri() ?>/dist/assets/img/favicons/browserconfig.xml">
	<meta name="theme-color" content="#ffffff">
	<script>
	theme_directory = "<?php echo get_template_directory_uri() ?>";
	</script>
    <script>
        var gaProperty = 'UA-91648560-4';
        var disableStr = 'ga-disable-' + gaProperty;
        if (document.cookie.indexOf(disableStr + '=true') > -1) {
            window[disableStr] = true;
        }
        function gaOptout() {
            document.cookie = disableStr + '=true; expires=Thu, 31 Dec 2099 23:59:59 UTC; path=/';
            window[disableStr] = true;
            alert('Das Tracking ist jetzt deaktiviert');
        }
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-XXXXXXXX-X', 'auto');
        ga('set', 'anonymizeIp', true);
        ga('send', 'pageview');
    </script
	<?php wp_head(); ?>
</head>
