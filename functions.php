<?php

// WLO-Config
require_once(get_template_directory().'/functions/wlo-config.php');

// Register scripts and stylesheets
require_once(get_template_directory().'/functions/enqueue-scripts.php');

// Register custom posts
require_once(get_template_directory().'/functions/custom-posts.php');

// Register custom features
require_once(get_template_directory().'/functions/features.php');

// Register portal features
require_once(get_template_directory().'/functions/portal-features.php');

// custom image sizes
require_once(get_template_directory().'/functions/image-sizes.php');

// Register custom menus and menu walkers
require_once(get_template_directory().'/functions/menu.php');

// Gutenberg Blocks
require_once(get_template_directory().'/functions/gutenberg-blocks.php');

// Custom Taxonomies
require_once(get_template_directory().'/functions/custom-taxonomies.php');

// Register Autoload ACF Fields
require_once(get_template_directory().'/functions/acf-autoload.php');

// Register Custom API Endpoint
require_once(get_template_directory().'/functions/rest-api.php');

// Register ajax functions
require_once(get_template_directory().'/functions/ajax.php');


// Handle User inbox for edu-sharing
//require_once(get_template_directory().'/functions/users-inbox.php');

// Handle User Regristration / LDAP
//require_once(get_template_directory().'/functions/users.php');
