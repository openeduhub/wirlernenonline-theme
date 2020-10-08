<?php

// WLO-Config
require_once(get_template_directory().'/functions/wlo-config.php');

// Register scripts and stylesheets
require_once(get_template_directory().'/functions/enqueue-scripts.php');

require_once(get_template_directory().'/functions/custom-posts.php');

// custom image sizes
require_once(get_template_directory().'/functions/gutenberg-blocks.php');

// custom image sizes
require_once(get_template_directory().'/functions/image-sizes.php');

// Register custom menus and menu walkers
require_once(get_template_directory().'/functions/menu.php');

// Gutenberg Blocks
require_once(get_template_directory().'/functions/gutenberg-blocks.php');

// Register custom features
require_once(get_template_directory().'/functions/features.php');

// Register Custom API Endpoint
require_once(get_template_directory().'/functions/rest-api.php');

// Register Autoload ACF Fields
require_once(get_template_directory().'/functions/acf-autoload.php');

// Handle User Regristration / LDAP
//require_once(get_template_directory().'/functions/users.php');
