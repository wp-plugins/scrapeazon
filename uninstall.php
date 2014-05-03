<?php
if(!defined('ABSPATH')&& !defined('WP_UNINSTALL_PLUGIN'))
{
    exit();
}

$szUser = wp_get_current_user();

// Remove settings stored in database
delete_option('scrape-aws-access-key-id');
delete_option('scrape-aws-secret-key');
delete_option('scrape-amz-assoc-id');
delete_option('scrape-getmethod');
delete_option('scrape-getcountry');
delete_option('scrape-responsive');

// Remove ScrapeAZon hidden notices
$szMeta_type  = 'user';
$szUser_id    = 0;
$szMeta_value = '';
$szDelete_all = true;

delete_metadata( $szMeta_type, $szUser_id, 'scrapeazon_ignore_FileGetEnabled', $szMeta_value, $szDelete_all );
delete_metadata( $szMeta_type, $szUser_id, 'scrapeazon_ignore_CurlEnabled', $szMeta_value, $szDelete_all );
delete_metadata( $szMeta_type, $szUser_id, 'scrapeazon_ignore_CurlDisabled', $szMeta_value, $szDelete_all );
?>