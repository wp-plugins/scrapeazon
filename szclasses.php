<?php
/* szclasses.php is part of the ScrapeAZon plugin for WordPress
 * 
 * This file is distributed as part of the ScrapeAZon plugin for WordPress
 * and is not intended to be used apart from that package. You can download
 * the entire ScrapeAZon plugin from the WordPress plugin repository at
 * http://wordpress.org/plugins/scrapeazon/
 */

/* 
 * Copyright 2011-2014	James R. Hanback, Jr.  (email : james@jameshanback.com)
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

class szRequirements
{
    public $szCurlEnabled     = 'Client URL (cURL) is enabled on your server. ScrapeAZon can use it to retrieve reviews.';
    public $szCurlDisabled    = 'Client URL (cURL) is either <a href="http://us2.php.net/manual/en/curl.setup.php">not installed or not enabled</a> on your server. ScrapeAZon might not be able to retrieve reviews.';
    public $szFileGetEnabled  = 'PHP fopen wrappers (file_get_contents) are enabled on your server. For security, <a href="http://www.php.net/manual/en/filesystem.configuration.php#ini.allow-url-fopen" target="_blank">disable fopen wrappers</a> and <a href="http://us2.php.net/manual/en/curl.setup.php">use cURL</a> instead.';
    public $szNoRetrieval     = 'Neither client URL (cURL) nor fopen wrappers (file_get_contents) are enabled on your server. ScrapeAZon will not be able to retrieve reviews.';
    
    public function szLoadLocal()
    {
        load_plugin_textdomain('scrapeazon',false,basename(dirname(__FILE__)).'/lang');
    }

    public function szCurlCheck()
    {
        return (in_array('curl',get_loaded_extensions())) ? true : false;
    }
    
    public function szCurlExecCheck($szDisabled)
    {
        $szDisabled = explode(', ', ini_get('disable_functions'));
        return !in_array('curl_exec', $szDisabled);
    }
    
    public function szFileGetCheck()
    {
        return (ini_get('allow_url_fopen')) ? true : false;
    }
    
    public function szRestoreNotices()
    {
        $szUser = wp_get_current_user();
        if(isset($_GET['restore_szNotices']) && '1' == $_GET['restore_szNotices'])
        {
            delete_user_meta($szUser->ID, 'scrapeazon_ignore_FileGetEnabled','true');
            delete_user_meta($szUser->ID, 'scrapeazon_ignore_CurlEnabled','true');
            delete_user_meta($szUser->ID, 'scrapeazon_ignore_CurlDisabled','true');
        }
    }

    public function szHideNotices()
    {
        $szUser = wp_get_current_user();
        if(isset($_GET['ignore_FileGetEnabled']) && '0' == absint($_GET['ignore_FileGetEnabled']))
        {
            add_user_meta($szUser->ID, 'scrapeazon_ignore_FileGetEnabled','true',true);
        }
        if(isset($_GET['ignore_CurlEnabled']) && '0' == absint($_GET['ignore_CurlEnabled']))
        {
            add_user_meta($szUser->ID, 'scrapeazon_ignore_CurlEnabled','true',true);
        }
        if(isset($_GET['ignore_CurlDisabled']) && '0' == absint($_GET['ignore_CurlDisabled']))
        {
            add_user_meta($szUser->ID, 'scrapeazon_ignore_CurlDisabled','true',true);
        }
    }
        
    public function szShowNotices()
    {
        $szScreenID = get_current_screen()->id;
        $szUser = wp_get_current_user();
        $szDisabled = 0;
        $szRestoreNotices = (isset($_GET['restore_szNotices'])=='1') ? '1' : '0'; 
        
        if ('1' == absint($szRestoreNotices)) 
        {
            $this->szRestoreNotices();
        }
        
        if($szScreenID == 'settings_page_scrapeaz-options') {
            if((!($this->szCurlExecCheck($szDisabled)))&&(!($this->szFileGetCheck())))
            {
                printf('<div class="error"><p>%s</p></div>', __($this->szNoRetrieval,'scrapeazon'),'ScrapeAZon');
            }
            if ( ! get_user_meta($szUser->ID, 'scrapeazon_ignore_FileGetEnabled') ) {
                if($this->szFileGetCheck())
                {
                    printf('<div class="error"><p>' . __($this->szFileGetEnabled,'scrapeazon') . ' | <a href="%1$s">' . __('Hide Notice','scrapeazon') . '</a></p></div>', '?page=scrapeaz-options&ignore_FileGetEnabled=0');
                }
            }
            if ( ! get_user_meta($szUser->ID, 'scrapeazon_ignore_CurlEnabled') ) {
                if(($this->szCurlExecCheck($szDisabled))&&($this->szCurlCheck()))
                {
                    printf('<div class="updated"><p>' . __($this->szCurlEnabled,'scrapeazon') . ' | <a href="%1$s">' . __('Hide Notice','scrapeazon') . '</a></p></div>', '?page=scrapeaz-options&ignore_CurlEnabled=0');
                }
            }
            if ( ! get_user_meta($szUser->ID, 'scrapeazon_ignore_CurlDisabled') ) {
                if((!($this->szCurlExecCheck($szDisabled)))||((!($this->szCurlCheck()))))
                {
                    printf('<div class="updated"><p>' . __($this->szCurlDisabled,'scrapeazon') . ' | <a href="%1$s">' . __('Hide Notice','scrapeazon') . '</a></p></div>', '?page=scrapeaz-options&ignore_CurlDisabled=0');
                }
            }
        }
    }
}

class szWPOptions
{
    public $szAccessKey      = '';
    public $szSecretKey      = '';
    public $szAssocId        = '';
    public $szRetrieveMethod = '';
    public $szCountryId      = '';
    public $szResponsive     = '';
    public $szCountries      = array("--","AT","CA","CN","DE","ES","FR","IN","IT","JP","UK","US");
    public $szCacheExpire    = 12;
    public $szClearCache     = 0;
    public $szOptionsPage    = 'scrapeaz-options';

    public function szCleanCache()
    {
        global $wpdb;
        $szDBquery = 'SELECT option_name FROM ' . $wpdb->options . ' WHERE option_name LIKE \'_transient_timeout_szT-%\';';
        $szCleanDB = $wpdb->get_col($szDBquery);
        foreach ($szCleanDB as $szTransient) {
            $szDBKey = str_replace('_transient_timeout_','',$szTransient);
            delete_transient($szDBKey);
        }
    }

    public function szRequireStyles()
    {
        // Load responsive stylesheet if required
        if($this->getResponsive())
        {
            $szStylesheet = plugins_url('szstyles.css',__FILE__);
            wp_register_style('scrape-styles',$szStylesheet);
            wp_enqueue_style('scrape-styles');
        }
        return true;
    }

    public function szOptionsLink($szLink) 
    {
        $szSettingsLink  = '<a href="' . admin_url() . 'admin.php?page=scrapeaz-options">' . __('Settings','scrapeazon') . '</a> | ';
        $szSettingsLink .= '<a href="' . admin_url() . 'admin.php?page=scrapeaz-tests">' . __('Test','scrapeazon') . '</a>';
        array_unshift($szLink,$szSettingsLink);
        return $szLink;
    }

    public function szOptionsScreen()
    {
        $szScreen = get_current_screen();
        return ($szScreen->id == 'scrapeazon-options') ? true : false;
    }

    public function setAccessKey($newval)
    {
        $this->szAccessKey = (strlen(trim($newval))!=20) ? '' : trim($newval);
        return sanitize_text_field($this->szAccessKey);
    }
    
    public function getAccessKey()
    {
        $this->szAccessKey = get_option('scrape-aws-access-key-id','');
        return sanitize_text_field($this->szAccessKey);
    }
    
    public function setSecretKey($newval)
    {
        $this->szSecretKey = (strlen(trim($newval))!=40) ? '' : trim($newval);
        return $this->szSecretKey;
    }
    
    public function getSecretKey()
    {
        $this->szSecretKey = get_option('scrape-aws-secret-key','');
        return sanitize_text_field($this->szSecretKey);
    }
    
    public function setAssocID($newval)
    {
        $this->szAssocID = (!preg_match('/^[A-Z0-9\_\-]*$/i', trim($newval))) ? '' : trim($newval);
        return sanitize_text_field($this->szAssocID);
    }
    
    public function getAssocID()
    {
        $this->szAssocID = get_option('scrape-amz-assoc-id','');
        return sanitize_text_field($this->szAssocID);
    }
    
    public function setRetrieveMethod($newval)
    {        
        // Upgrading from 1.x?
        $this->szRetrieveMethod = (trim($newval)=='checked') ? 1 : trim($newval);
        return absint($this->szRetrieveMethod);
    }
    
    public function getRetrieveMethod()
    {
        $this->szRetrieveMethod = get_option('scrape-getmethod','');
        return $this->szRetrieveMethod;
    }
    
    public function setCountryID($newval)
    {
        $this->szCountryID = (!in_array(trim($newval),$this->szCountries)) ? '--' : trim($newval);
        return sanitize_text_field($this->szCountryID);
    }
    
    public function getCountryID()
    {
        $this->szCountryID = get_option('scrape-getcountry','');
        return sanitize_text_field($this->szCountryID);
    }
    
    public function setResponsive($newval)
    {
        $this->szResponsive = trim($newval);
        return absint($this->szResponsive);
    }
    
    public function getResponsive()
    {
        $this->szResponsive = get_option('scrape-responsive','0');
        return absint($this->szResponsive);
    }
    
    public function setCacheExpire($newval)
    {
        $this->szCacheExpire = (! empty($newval)) ? trim($newval) : 12;
        return absint($this->szCacheExpire);
    }
    
    public function getCacheExpire()
    {
        $this->szCacheExpire = get_option('scrape-perform','12');
        return absint($this->szCacheExpire);
    }
    
    public function setClearCache($newval)
    {
        $this->szClearCache = (! empty($newval)) ? trim($newval) : 0;
        return absint($this->szClearCache);
    }
    
    public function getClearCache()
    {
        $szClear = get_option('scrape-clearcache','0');
        update_option('scrape-clearcache','0');
        return($szClear);
    }
       
    public function szOptionsCallback()
    {
        $sz1      = __('In order to access customer review data from Amazon.com, you must have an Amazon.com Associate ID, an Amazon Web Services (AWS) Access Key Id, and an AWS Secret Key. You can obtain an Associate ID by signing up to be an ','scrapeazon');
        $sz2      = __('Amazon.com affiliate','scrapeazon');
        $sz3      = __('. You can obtain the AWS credentials by signing up to use the ','scrapeazon');
        $sz4      = __('Product Advertising API','scrapeazon');
        $szFormat = '<p>%s<a href="https://affiliate-program.amazon.com/" target="_blank">%s</a>%s<a href="https://affiliate-program.amazon.com/gp/advertising/api/detail/main.html" target="_blank">%s</a>.</p>';

        printf($szFormat,$sz1,$sz2,$sz3,$sz4);
    }
    
    public function szTestCallback()
    {
        $sz1      = __('If you have correctly configured ScrapeAZon, you should see an iframe below that contains Amazon.com reviews for the Kindle short story ','scrapeazon');
        $sz2      = __('located here','scrapeazon');
        $sz3      = __(' on Amazon. The shortcode used to produce this test is ','scrapeazon');
        $sz4      = __('. If you see reviews in the iframe below, ScrapeAZon is configured correctly and should work on your site. If you see no data or if you see an error displayed below, please double-check your configuration.','scrapeazon');
        $szFormat = '<p>%s<a href="http://www.amazon.com/Dislike-Isaac-Thorne-ebook/dp/B00HPCF5VU" target="_blank">%s</a>%s<code>[scrapeazon asin="B00HPCF5VU" width="500" height="400" border="false" country="us"]</code>%s</p>';

        printf($szFormat,$sz1,$sz2,$sz3,$sz4);
    }
    
    public function szPerformCallback()
    {
        $sz1      = __('WARNING!');
        $sz2      = __('You should make a backup of your WordPress database before attempting to use the <strong>Clear Cache</strong> option. The <strong>Clear Cache</strong> option attempts to delete data directly from the WordPress database and is therefore dangerous. Use the <strong>Clear Cache</strong> option with caution.','scrapeazon');
        $szFormat = '<p><h2>%s</h2></p><p>%s</p>';

        printf($szFormat,$sz1,$sz2);
    }
    
    public function szUsageCallback()
    {
        echo '<p><b>' . __('Shortcode','scrapeazon') .'</b>: <code>[scrapeazon asin="<i>amazon.com-product-number</i>"]</code></p>';

        $sz1      = __('Insert the above shortcode into any page or post where you want Amazon.com customer reviews to appear. Replace ','scrapeazon');
        $sz2      = __(' with the product ASIN or ISBN-10 to retrieve and display the reviews for that product.','scrapeazon');
        $sz3      = __('For a more detailed and complete overview of how ScrapeAZon works, click the "Help" tab on the upper right of the ScrapeAZon settings page.','scrapeazon');
        $szFormat = '<p>%s<code><i>amazon.com-product-number</i></code>%s</p><p>%s</p>';

        printf($szFormat,$sz1,$sz2,$sz3);
    }
    
    public function szRegisterSettings() 
    {
        register_setting('scrapeazon-options','scrape-aws-access-key-id',array(&$this, 'setAccessKey'));
        register_setting('scrapeazon-options','scrape-aws-secret-key',array(&$this, 'setSecretKey'));
        register_setting('scrapeazon-options','scrape-amz-assoc-id',array(&$this, 'setAssocID'));
        register_setting('scrapeazon-options','scrape-getmethod',array(&$this, 'setRetrieveMethod'));
        register_setting('scrapeazon-options','scrape-getcountry',array(&$this, 'setCountryID'));
        register_setting('scrapeazon-options','scrape-responsive',array(&$this, 'setResponsive'));
        register_setting('scrapeazon-perform','scrape-perform',array(&$this, 'setCacheExpire'));
        register_setting('scrapeazon-perform','scrape-clearcache',array(&$this, 'setClearCache'));
    }
       
    public function szAddAdminPage() 
    {
        $szOptionsPage = add_submenu_page('options-general.php','ScrapeAZon','ScrapeAZon','manage_options','scrapeaz-options',array(&$this, 'szGetOptionsScreen'));
        $szTestingPage = add_submenu_page('scrapeaz-options','Tests','Tests','manage_options','scrapeaz-tests',array(&$this, 'szGetOptionsScreen'));
        $szCachingPage = add_submenu_page('scrapeaz-perform','Performance','Performance','manage_options','scrapeaz-perform',array(&$this, 'szGetOptionsScreen'));
        $szUsingPage   = add_submenu_page('scrapeaz-options','Usage','Usage','manage_options','scrapeaz-usages',array(&$this, 'szGetOptionsScreen'));    
        add_action('load-' . $szOptionsPage, array(&$this, 'szAddHelp'));
        add_action('load-' . $szTestingPage, array(&$this, 'szAddHelp'));
        add_action('load-' . $szCachingPage, array(&$this, 'szAddHelp'));
        add_action('load-' . $szUsingPage, array(&$this, 'szAddHelp'));       
    }
 
    public function szGetOptionsScreen() 
    {
        switch(get_admin_page_title())
        {
            case 'ScrapeAZon':
                 $_GET['tab'] = 'scrapeazon_retrieval_section';
                 break;
            case 'Tests':
                 $_GET['tab'] = 'scrapeazon_test_section';
                 break;
            case 'Performance':
                 $_GET['tab'] = 'scrapeazon_perform_section';
                 break;
            case 'Usage':
                 $_GET['tab'] = 'scrapeazon_usage_section';
                 break;
        }
        // Settings navigation tabs
        if( isset( $_GET[ 'tab' ] ) ) {
            $active_tab = isset( $_GET[ 'tab' ] ) ? sanitize_text_field($_GET[ 'tab' ]) : 'scrapeazon_retrieval_section';
        }
        echo '<h2 class="nav-tab-wrapper"><a href="' . admin_url() .'admin.php?page=scrapeaz-options&tab=scrapeazon_retrieval_section" class="nav-tab ';
        echo $active_tab == 'scrapeazon_retrieval_section' ? 'nav-tab-active' : '';
        echo '">ScrapeAZon</a><a href="' . admin_url() .'admin.php?page=scrapeaz-tests&tab=scrapeazon_test_section" class="nav-tab ';
        echo $active_tab == 'scrapeazon_test_section' ? 'nav-tab-active' : '';
        echo '">Tests</a><a href="' . admin_url() .'admin.php?page=scrapeaz-perform&tab=scrapeazon_perform_section" class="nav-tab ';
        echo $active_tab == 'scrapeazon_perform_section' ? 'nav-tab-active' : '';
        echo '">Performance</a><a href="' . admin_url() .'admin.php?page=scrapeaz-usages&tab=scrapeazon_usage_section" class="nav-tab ';
        echo $active_tab == 'scrapeazon_usage_section' ? 'nav-tab-active' : '';
        echo '">Usage</a></h2>';
        
        // Settings form


        add_settings_section(
            'scrapeazon_retrieval_section',
            __('ScrapeAZon Settings','scrapeazon'),
            array(&$this, 'szOptionsCallback'),
            'scrapeaz-options'
        );
               
        add_settings_section(
            'scrapeazon_test_section',
            __('ScrapeAZon Test Frame','scrapeazon'),
            array(&$this, 'szTestCallback'),
            'scrapeaz-tests'
        );
        
        add_settings_section(
            'scrapeazon_perform_section',
            __('ScrapeAZon Performance','scrapeazon'),
            array(&$this, 'szPerformCallback'),
            'scrapeaz-perform'
        );
               
        add_settings_section(
            'scrapeazon_usage_section',
            __('ScrapeAZon Usage','scrapeazon'),
            array(&$this, 'szUsageCallback'),
            'scrapeaz-usages'
        );
        
        // Create settings fields
        $this->getOptionsForm();   
        switch($active_tab)
        {
            case 'scrapeazon_retrieval_section':
                 echo '<form method="post" action="options.php">';
                 settings_fields('scrapeazon-options');
                 do_settings_sections('scrapeaz-options');
                 echo get_submit_button();
                 echo '</form>';
                 break;
            case 'scrapeazon_test_section':
                 settings_fields('scrapeazon-tests');
                 do_settings_sections('scrapeaz-tests');
                 break;
            case 'scrapeazon_perform_section':
                 echo '<form method="post" action="options.php">';
                 settings_fields('scrapeazon-perform');
                 do_settings_sections('scrapeaz-perform');
                 echo get_submit_button();
                 echo '</form>';
                 break;
            case 'scrapeazon_usage_section':
                 settings_fields('scrapeazon-usages');
                 do_settings_sections('scrapeaz-usages');
                 break;
        }

    }
    
    public function szAWSKeyIDField($args)
    {
        $szField  = '<input type="text" id="scrape-aws-access-key-id" name="scrape-aws-access-key-id" value="';
        $szField .= $this->getAccessKey();
        $szField .= '"/><br />';
        $szField .= '<label for="scrape-aws-access-key-id"> '  . sanitize_text_field($args[0]) . '</label>';
        echo $szField;
    }
    
    public function szAWSSecretField($args)
    {
        $szField  = '<input type="text" id="scrape-aws-secret-key" name="scrape-aws-secret-key" value="';
        $szField .= $this->getSecretKey();
        $szField .= '"/><br />';
        $szField .= '<label for="scrape-aws-secret-key"> '  . sanitize_text_field($args[0]) . '</label>';
        echo $szField;
    }
    
    public function szAWSAssocField($args)
    {
        $szField  = '<input type="text" id="scrape-amz-assoc-id" name="scrape-amz-assoc-id" value="';
        $szField .= $this->getAssocID();
        $szField .= '"/><br />';
        $szField .= '<label for="scrape-amz-assoc-id"> '  . sanitize_text_field($args[0]) . '</label>';
        echo $szField;
    }
    
    public function szMethodField($args)
    {
        $szField  = '<input type="checkbox" name="scrape-getmethod" id="scrape-getmethod" value="1" ' .
                    checked(1, $this->getRetrieveMethod(), false) .
                    $this->getRetrieveMethod() .
                    ' /><br />';
        $szField .= '<label for="scrape-getmethod"> '  . sanitize_text_field($args[0]) . '</label>';
        echo $szField;
    }
    
    public function szCountryField($args)
    {
	    $szField = '<select id="scrape-getcountry" name="scrape-getcountry">';
	    foreach($this->szCountries as $szDDitem) 
	    {
		    $szFieldSelected = (($this->getCountryID())==$szDDitem) ? ' selected="selected"' : '';
		    $szField .= '<option value="' .
		                sanitize_text_field($szDDitem) .
		                '"' .
		                sanitize_text_field($szFieldSelected) .
		                '>' .
		                sanitize_text_field($szDDitem) .
		                '</option>';
	    }
	    $szField .= '</select><br />';
        $szField .= '<label for="scrape-getcountry"> '  . sanitize_text_field($args[0]) . '</label>';
	    echo $szField;
    }
    
    public function szCacheExpireField($args)
    {
	    $szField = '<select id="scrape-perform" name="scrape-perform">';
	    for($x=1;$x<24;$x++)
	    {
	        $szFieldSelected = ($this->getCacheExpire()==$x) ? ' selected="selected"' : '';
	        $szField .= '<option value="' .
	                    absint($x) .
	                    '"' .
	                    sanitize_text_field($szFieldSelected) .
	                    '>' .
	                    absint($x) .
	                    '</option>';
	    }
	    $szField .= '</select> Hours<br />';
        $szField .= '<label for="scrape-perform"> '  . sanitize_text_field($args[0]) . '</label>';
	    echo $szField;
    }
    
    public function szResponsiveField($args)
    {
        $szField  = '<input type="checkbox" name="scrape-responsive" id="scrape-responsive" value="1" ' .
                     checked(1, $this->getResponsive(), false) .
                     ' /><br />';
        $szField .= '<label for="scrape-responsive"> '  . sanitize_text_field($args[0]) . '</label>';
        echo $szField;
    }
    
    public function szRestoreNoticesField($args)
    {
        $szField  = '<a href="?page=scrapeaz-options&restore_szNotices=1">' . __('Restore Hidden ScrapeAZon Notices','scrapeazon') . '</a><br />';
        $szField .= '<label for="scrape-restore"> ' . sanitize_text_field($args[0]) . '</label>';
        echo $szField;
    }
    
    public function szAWSTestField()
    {
        echo do_shortcode('[scrapeazon asin="B00HPCF5VU" width="500" height="400" border="false" country="us"]');
    }
    
    public function szClearCacheField($args)
    {
        $szField  = '<input type="checkbox" name="scrape-clearcache" id="scrape-clearcache" value="1" /><br />';
        $szField .= '<label for="scrape-clearcache"> '  . sanitize_text_field($args[0]) . '</label>';
        echo $szField;
    }
    
    public function getOptionsForm()
    {
        add_settings_field(
            'scrape-aws-access-key-id',
            __('AWS Access Key ID','scrapeazon'),
            array(&$this, 'szAWSKeyIDField'),
            'scrapeaz-options',
            'scrapeazon_retrieval_section',
            array(
                __('Enter your 20-character AWS Access Key.','scrapeazon')
            )
        );

        add_settings_field(
            'scrape-amz-secret-key',
            __('AWS Secret Key','scrapeazon'),
            array(&$this, 'szAWSSecretField'),
            'scrapeaz-options',
            'scrapeazon_retrieval_section',
            array(
                __('Enter your 40-character AWS Secret Key.','scrapeazon')
            )
        );
        
        add_settings_field(
            'scrape-aws-assoc-id',
            __('Amazon Associate ID','scrapeazon'),
            array(&$this, 'szAWSAssocField'),
            'scrapeaz-options',
            'scrapeazon_retrieval_section',
            array(
                __('Enter your Amazon Advertising Associate ID.','scrapeazon')
            )
        );
        
        add_settings_field(
            'scrape-getcountry',
            __('Amazon Country ID','scrapeazon'),
            array(&$this, 'szCountryField'),
            'scrapeaz-options',
            'scrapeazon_retrieval_section',
            array(
                __('Select the country code for the Amazon International API from which you want to pull reviews.','scrapeazon')
            )
        );
                
        add_settings_field(
            'scrape-getmethod',
            __('Use File_Get_Contents<br><span style="color:red">(not recommended)</span>','scrapeazon'),
            array(&$this, 'szMethodField'),
            'scrapeaz-options',
            'scrapeazon_retrieval_section',
            array(
                __('Select this checkbox to use fopen wrappers if your host does not support cURL (not recommended).','scrapeazon')
            )
        );
        
        add_settings_field(
            'scrape-responsive',
            __('Use Responsive Style','scrapeazon'),
            array(&$this, 'szResponsiveField'),
            'scrapeaz-options',
            'scrapeazon_retrieval_section',
            array(
                __('Select this checkbox to enable ScrapeAZon styles for sites with responsive design.','scrapeazon')
            )
        );
        
        add_settings_field(
            'scrape-restore-notices',
            __('Restore Notices','scrapeazon'),
            array(&$this, 'szRestoreNoticesField'),
            'scrapeaz-options',
            'scrapeazon_retrieval_section',
            array(
                __('Click this link to restore any important ScrapeAZon Settings notifications you might have hidden.','scrapeazon')
            )
        );
        
        add_settings_field(
            'scrape-aws-test-field',
            __('Test Frame','scrapeazon'),
            array(&$this, 'szAWSTestField'),
            'scrapeaz-tests',
            'scrapeazon_test_section',
            array(
                __('ScrapeAZon Test Frame.','scrapeazon')
            )
        );
        
        add_settings_field(
            'scrape-perform',
            __('Cache Expires In','scrapeazon'),
            array(&$this, 'szCacheExpireField'),
            'scrapeaz-perform',
            'scrapeazon_perform_section',
            array(
                __('The number of hours that should pass before cached Amazon API calls expire. Cannot be more than 23 hours. Default is 12.','scrapeazon')
            )
        );
        
        add_settings_field(
            'scrape-clear-cache-field',
            __('Clear Cache','scrapeazon'),
            array(&$this, 'szClearCacheField'),
            'scrapeaz-perform',
            'scrapeazon_perform_section',
            array(
                __('Clears ScrapeAZon transient data.','scrapeazon')
            )
        );
    }
    
    public function szAddHelp($szContextHelp)
    {
        $szOverview     = '<p>' .
                          __('The ScrapeAZon plugin retrieves Amazon.com customer reviews for products you choose and displays them in pages or posts on your WordPress blog by way of a WordPress shortcode.','scrapeazon') .
                          '</p> <p>' .
                          __('You must be a participant in both the Amazon.com Affiliate Program and the Amazon.com Product Advertising API in order to use this plugin. Links to Amazon.com forms to join those programs are available on the ScrapeAZon Settings page.','scrapeazon') .
                          '</p> <p><strong>' .
                          __('WARNING','scrapeazon') .
                          '</strong>: ' .
                          __('If your PHP implementation does not have client URL (cURL) installed and enabled, you should not attempt to use this plugin. You might be able to use the plugin without cURL if your PHP implementation has fopen wrappers enabled. However, fopen wrappers can be a security risk.','scrapeazon') .
                          '</p>';
        $szSettingsUse  = '<p>' .
                          __('The following ScrapeAZon Settings fields are ','scrapeazon') .
                          '<strong>' .
                          __('required','scrapeazon') .
                          '</strong>:<ul><li><strong>AWS Access Key</strong>: ' .
                          __('A 20-character key assigned to you by the AWS Product Advertising API.','scrapeazon') .
                          '</li><li><strong>AWS Secret Key</strong>: ' .
                          __('A 40-character secret key assigned to you by the Amazon Product Advertising API.','scrapeazon') .
                          '</li><li><strong>Amazon Associate ID</strong>: ' .
                          __('The short string of characters that identifies your Amazon associate account.','scrapeazon') .
                          '</li></ul></p><p>' .
                          __('The following ScrapeAZon Settings are optional','scrapeazon') .
                          ':<ul><li><strong>Amazon Country ID</strong>: ' .
                          __('If you select a country here, you will globally enable that country for all your ScrapeAZon shortcodes. If you leave it blank, ScrapeAZon shortcodes will default to reviews from Amazon US unless the ','scrapeazon') .
                          '<code>country</code>' .
                          __(' parameter is specified in the shortcode.','scrapeazon') .
                          '</li><li><strong>Use File_Get_Contents</strong>: ' .
                          __('If cURL is enabled on your site, DO NOT select this checkbox. If your site does not support cURL, you can select this checkbox to use fopen wrappers instead. However, fopen wrappers are a security risk. Consider installing cURL.','scrapeazon') .
                          '</li><li><strong>Use Responsive Style</strong>: ' .
                          __('Selecting this checkbox loads a default ScrapeAZon style sheet that will attempt to scale output for sites that have a responsive design. If you specify the ','scrapeazon') .
                          '<code>width</code> ' .
                          __('and ','scrapeazon') .
                          '<code>height</code> ' .
                          __('parameters in a shortcode, the containing element will default to that width and height.','scrapeazon') .
                          '</li></ul></p>';
        $szShortcodeUse = '<p>' .
                          __('Type the shortcode ','scrapeazon') .
                          '<code>[scrapeazon asin="<i>amazon-asin-number</i>"]</code>,' .
                          __(' where ','scrapeazon') .
                          '<i>amazon-asin-number</i> ' .
                          __('is the ASIN or ISBN-10 of the product reviews you want to retrieve. The shortcode must be issued in text format in your page or post, not Visual format. Otherwise, the quotation marks inside the shortcode might be rendered incorrectly.','scrapeazon') .
                          '</p><p>' .
                          __('You can also issue the ScrapeAZon shortcode with one of the following identifers instead of using an ASIN','scrapeazon') .
                          ':<ul><li><code>isbn</code>: ' .
                          __('Retrieves reviews by using an International Standard Book Number (ISBN) value','scrapeazon') .
                          '</li><li><code>upc</code>: ' .
                          __('Retrieves reviews by using a Universal Product Code (UPC) value','scrapeazon') .
                          '</li><li><code>ean</code>: ' .
                          __('Retrieves reviews by using a European Article Number (EAN) value','scrapeazon') .
                          '</li></ul></p><p>' .
                          __('You can also issue the ScrapeAZon shortcode with the following additional parameters','scrapeazon') .
                          ':<ul><li><code>width</code>: ' .
                          __('Specifies the width of the reviews iframe, or of the containing element if the responsive option is enabled.','scrapeazon') .
                          '</li><li><code>height</code>: ' .
                          __('Specifies the height of the reviews iframe, or of the containing element if the responsive option is enabled.','scrapeazon') .
                          '</li><li><code>border</code>: ' .
                          __('When set to ','scrapeazon') .
                          '<code>false</code>, ' .
                          __('disables the border that some browsers automatically add to iframes.','scrapeazon') .
                          '</li><li><code>country</code>: ' .
                          __('Overrides the global country setting on the Settings page. Use the two-character country code for the Amazon International site from which you want to obtain reviews.','scrapeazon') .
                          '</li><li><code>noblanks</code>: ' .
                          __('When set to ','scrapeazon') .
                          '<code>true</code> ' .
                          __('prevents ScrapeAZon from displaying an iframe for products that have no reviews. By default, ScrapeAZon displays an iframe that contains Amazon\'s "Be the first to review this item" page.','scrapeazon') .
                          '</li><li><code>url</code>: ' .
                          __('When set to ','scrapeazon') .
                          '<code>true</code>, ' .
                          __('returns ONLY the iFrame source URL, not the iFrame itself. This can be useful if you want more advanced control over the iframe element attributes because you can include the shortcode within the SRC attribute of a manually coded iframe tag. Default value is','scrapeazon') .
                          '<code>false</code>' . 
                          '</li></ul></p>';
        $szTestsUse     = '<p>' .
                          __('After you have saved your ScrapeAZon Settings by clicking the ','scrapeazon') .
                          '<strong>' .
                          __('Save Changes','scrapeazon') .
                          '</strong> ' .
                          __('button, you can click the ','scrapeazon') .
                          '<strong>' .
                          __('Tests','scrapeazon') .
                          '</strong> ' .
                          __('tab to view some sample reviews frames based on your settings.','scrapeazon') .
                          '</p><p>' . 
                          __('If you do not see sample Amazon output on this tab, your ScrapeAZon settings might be incorrect.','scrapeazon') .
                          '</p>';
        $szPerfUse      = '<p>' .
                          __('By default, ScrapeAZon caches Amazon API calls for 12 hours to enhance site performance. ','scrapeazon') .
                          __('You can adjust the amount of time ScrapeAZon caches this data by adjusting the','scrapeazon') .
                          '<strong>' .
                          __('Cache Expires In','scrapeazon') .
                          '</strong> ' .
                          __('value to the number of hours you want the cached data to persist.','scrapeazon') .
                          '</p><p>' . 
                          __('You can also choose to clear the existing cached data from the WordPress database. However, you should always back up your WordPress database before attempting to delete data in bulk.','scrapeazon') .
                          '</p><p>' .
                          __('Please be aware that if you are using a caching plugin, such as W3 Total Cache, with object caching enabled, the Clear Cache option will not do anything. You will need to clear the object cache by using the caching plugin\'s clear cache feature.','scrapeazon') .
                          '</p>';
    
        $szScreen   = get_current_screen();           
        $szScreen->add_help_tab(array(
            'id'      => 'szOverviewTab',
            'title'   => __('Overview','scrapeazon'),
            'content' => $szOverview,
        ));
        $szScreen->add_help_tab(array(
            'id'      => 'szSettingsUseTab',
            'title'   => __('Settings','scrapeazon'),
            'content' => $szSettingsUse,
        ));
        $szScreen->add_help_tab(array(
            'id'      => 'szShortcodeUseTab',
            'title'   => __('Shortcode','scrapeazon'),
            'content' => $szShortcodeUse,
        ));
        $szScreen->add_help_tab(array(
            'id'      => 'szTestsUseTab',
            'title'   => __('Tests Tab','scrapeazon'),
            'content' => $szTestsUse,
        ));
        $szScreen->add_help_tab(array(
            'id'      => 'szPerfUseTab',
            'title'   => __('Performance Tab','scrapeazon'),
            'content' => $szPerfUse,
        ));
        return $szContextHelp;
    }
    
    public function szShowNPNotices()
    {
        if($this->getClearCache()=='1') 
        {
            add_settings_error( 'scrapeazon-notices', 'scrape-cache-cleared', __('Cache cleared', 'scrapeazon'), 'updated' );
            $this->szCleanCache();
        }
        settings_errors('scrapeazon-notices');
    }
}

class szWidget extends WP_Widget {
    public function __construct() 
    {
	    parent::__construct(
		    'sz_widget', 
		    __('Amazon Reviews','scrapeazon'),
		    array( 'description' => __( 'Display Amazon.com reviews for a product you specify.','scrapeazon'), )
	    );
    }
    
    public function widget($args, $instance)
    {  
		$title    = apply_filters( 'widget_title', $instance['title'] );
		$szBArray = array('true','false');

		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
		if( isset ($instance[ 'asin' ]) )
		{
		    if(empty($instance['itype']))
		    {
		        $instance['itype'] = 'asin';
		    }
		    $szSCode = '[scrapeazon ' . strip_tags($instance['itype']) . '="' . strip_tags($instance['asin']) . '"';
		    if(($instance['width']!=0) && isset ($instance[ 'width' ]) )
		    {
		        $szSCode .= ' width="' . asbint($instance[ 'width' ]) . '"';
		    }
		    if(($instance['height']!=0) && isset ($instance[ 'height' ]) )
		    {
		        $szSCode .= ' height="' . absint($instance[ 'height' ]) . '"';
		    }
		    if((in_array($instance['border'],$szBArray)) && isset ($instance[ 'border' ]) )
		    {
		        $szSCode .= ' border="' . strip_tags($instance[ 'border' ]) . '"';
		    }
		    $szSCode .= ' iswidget="' . $instance['asin'] . $instance['height'] . $instance['width'] . '"]';
		    echo do_shortcode($szSCode);
		}
		echo $args['after_widget'];
    }
    
    public function form($instance)
    {
		if ( isset( $instance[ 'title' ] ) ) 
		{
			$title = $instance[ 'title' ];
		}
		else 
		{
			$title = __( 'Amazon Reviews', 'scrapeazon' );
		}
		if ( isset( $instance[ 'itype'] ) )
		{
		    $itype = $instance['itype'];
		}
		else
		{
		    $itype = 'asin';
		}
		if ( isset( $instance[ 'asin' ] ) ) 
		{
		    $asin = $instance[ 'asin' ];
		}
		else
		{
		    $asin = '';
		}
		if ( isset( $instance[ 'width' ] ) ) 
		{
		    $width = $instance[ 'width' ];
		}
		else
		{
		    $width = '';
		}
		if ( isset( $instance[ 'height' ] ) ) 
		{
		    $height = $instance[ 'height' ];
		}
		else
		{
		    $height = '';
		}
		if ( isset( $instance[ 'border' ] ) ) 
		{
		    $border = $instance[ 'border' ];
		}
		else
		{
		    $border = '';
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:','scrapeazon'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
 		<label for="<?php echo $this->get_field_id( 'itype' ); ?>"><?php _e('ID Type:','scrapeazon'); ?></label>
        <select class="widefat" id="<?php echo $this->get_field_id( 'itype' ); ?>" name="<?php echo $this->get_field_name( 'itype' ); ?>">
        <option value="asin" <?php echo (esc_attr($itype)=='asin') ? 'selected' : ''; ?>>ASIN</option>
        <option value="isbn" <?php echo (esc_attr($itype)=='isbn') ? 'selected' : ''; ?>>ISBN</option>
        <option value="upc" <?php echo (esc_attr($itype)=='upc') ? 'selected' : ''; ?>>UPC</option>
        <option value="ean" <?php echo (esc_attr($itype)=='ean') ? 'selected' : ''; ?>>EAN</option>
        </select>
 		<label for="<?php echo $this->get_field_id( 'asin' ); ?>"><?php _e('ID:','scrapeazon'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'asin' ); ?>" name="<?php echo $this->get_field_name( 'asin' ); ?>" type="text" value="<?php echo esc_attr( $asin ); ?>">
 		<label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e('Width (in pixels):','scrapeazon'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" type="text" value="<?php echo esc_attr( $width ); ?>">
 		<label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e('Height (in pixels):','scrapeazon' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" type="text" value="<?php echo esc_attr( $height ); ?>">
 		<label for="<?php echo $this->get_field_id( 'border' ); ?>"><?php _e('Border (true/false):','scrapeazon' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'border' ); ?>" name="<?php echo $this->get_field_name( 'border' ); ?>" type="text" value="<?php echo esc_attr( $border ); ?>">
		</p>
		<?php 
    }
    
    public function update($new_instance, $old_instance)
    {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['itype'] = ( ! empty( $new_instance['itype'] ) ) ? strip_tags( $new_instance['itype'] ) : '';
		$instance['asin']  = ( ! empty( $new_instance['asin'] ) ) ? strip_tags( $new_instance['asin'] ) : '';
		$instance['width']  = ( ! empty( $new_instance['width'] ) ) ? strip_tags( $new_instance['width'] ) : '';
		$instance['height']  = ( ! empty( $new_instance['height'] ) ) ? strip_tags( $new_instance['height'] ) : '';
		$instance['border']  = ( ! empty( $new_instance['border'] ) ) ? strip_tags( $new_instance['border'] ) : '';
		return $instance;
    }
    
}

class szShortcode
{   
    public function szGetDomain($szCountryId)
    {
        switch ($szCountryId) {
        case "AT" :
             return '.de';
             break;
        case "CA" :
             return '.ca';
             break;
        case "CN" :
             return '.cn';
             break;
        case "DE" :
             return '.de';
             break;  
        case "ES" :
             return '.es';
             break;
        case "FR" :
             return '.fr';
             break;
        case "IN" :
             return '.in';
             break;  
        case "IT" :
             return '.it';
             break;
        case "JP" :
             return '.co.jp';
             break;
        case "UK" :
             return '.co.uk';
             break;
        case "US" :
             return '.com';
             break;
        case "--" :
             return '.com';
             break;  
        }  
    }

    public function szIsSSL()
    {
        $szSSL = (isset($_SERVER['HTTPS'])) || (is_ssl()) ? 'https://' : 'http://';
        return $szSSL;
    }

    public function szGetSignature($szHost,$szPath,$szQuery,$szSecret)
    {
         /* 
         The following 10 lines of code were adapted from a function found online at
         http://randomdrake.com/2009/07/27/amazon-aws-api-rest-authentication-for-php-5/
         */
     
         parse_str($szQuery,$pQuery);
         ksort($pQuery);
         foreach ($pQuery as $parameter => $value) {
             $parameter     = str_replace("%7E", "~", rawurlencode($parameter));
             $value         = str_replace("%7E", "~", rawurlencode($value));
             $query_array[] = $parameter . '=' . $value;
         }
         $newSZQuery = implode('&', $query_array);
         $szSigString = "GET\n{$szHost}\n{$szPath}\n{$newSZQuery}";
         $szSignature = urlencode(base64_encode(hash_hmac('sha256', $szSigString, $szSecret, true)));
         
         return "?{$newSZQuery}&Signature={$szSignature}";
    }
    
    public function szGetIDType($szASIN,$szUPC,$szISBN,$szEAN)
    {
        $szItemType = $szASIN . '&IdType=ASIN';
        
        if(! empty($szEAN))  { $szItemType = sanitize_text_field($szEAN) . '&IdType=EAN&SearchIndex=All'; }
        if(! empty($szUPC))  { $szItemType = sanitize_text_field($szUPC) . '&IdType=UPC&SearchIndex=All'; }
        if(! empty($szISBN)) { $szItemType = sanitize_text_field($szISBN) . '&IdType=ISBN&SearchIndex=All'; }
        
        return $szItemType;
    }

    public function szAmazonURL($szASIN,$szUPC,$szISBN,$szEAN,$szCountry)
    {
        $szSets   = new szWPOptions();
        $szSecret = $szSets->getSecretKey();
        
        $szSSLR   = $this->szIsSSL();
        
        $szItemID = $this->szGetIDType($szASIN,$szUPC,$szISBN,$szEAN);
        
        $szUCCountry = strtoupper($szCountry);
        $szDomain = (in_array($szUCCountry,$szSets->szCountries)) ? $this->szGetDomain($szUCCountry) : $this->szGetDomain($szSets->getCountryID());
        
        $szHost = 'webservices.amazon' . $szDomain;
        
        $szPath = '/onca/xml';
        $szQuery= 'AssociateTag=' . 
                  $szSets->getAssocID() .
                  '&Availability=Available' .
                  '&AWSAccessKeyId=' . 
                  $szSets->getAccessKey() .
                  '&Condition=All' .
                  '&IncludeReviewsSummary=True' .
                  '&ItemId=' . 
                  $szItemID . 
                  '&MerchantId=All' .
                  '&Operation=ItemLookup' .   
                  '&ResponseGroup=Reviews' .
                  '&Service=AWSECommerceService' .
                  '&Timestamp=' . 
                  gmdate("Y-m-d\TH:i:s\Z") .
                  '&Version=2013-08-01'; 

         $szAWSURI = $szSSLR . $szHost . $szPath . $this->szGetSignature($szHost,$szPath,$szQuery,$szSecret);
         
         unset($szSets);

         return $szAWSURI;   
    }

    public function szCallAmazonAPI($szURL)
    {
        $szSets      = new szWPOptions();
        $szRetries   = 0;
        $szSCCode    = "500";
        
        if($szSets->getRetrieveMethod()!=1) 
        {
           $szCurl = curl_init();
           curl_setopt($szCurl, CURLOPT_URL, $szURL);
           curl_setopt($szCurl, CURLOPT_RETURNTRANSFER, true);
        }
        
        while(($szRetries<5)&&(!preg_match("/200/",$szSCCode))) {
            usleep(500000*pow($szRetries,2));
            if($szSets->getRetrieveMethod()==1)
            {
                $szXML = file_get_contents($szURL);
                $szSCCode = $http_response_header[0];
            }
            else 
            {
                $szXML = curl_exec($szCurl);
                $szSCCode = curl_getinfo($szCurl,CURLINFO_HTTP_CODE);
            }
            $szRetries = $szRetries + 1;
        }
        
        if($szSets->getRetrieveMethod()!=1) 
        {
            curl_close($szCurl);
        }
        
        unset($szSets);

        return $szXML;
    }
    
    public function szRetrieveFrameURL($szResults)
    {
        $szIFrameURL='';
        //$szResults = simplexml_load_string($szXML);
        if($szResults->Items->Item->CustomerReviews->HasReviews) 
        {
            $szIFrameURL = str_replace('http://',$this->szIsSSL(),$szResults->Items->Item->CustomerReviews->IFrameURL);
        }
        else
        {
            if($szResults->Items->Request->Errors->Error->Message)
            { 
                echo '<div class="scrape-error">';
                echo $szResults->Items->Request->Errors->Error->Code . ': ' . $szResults->Items->Request->Errors->Error->Message;
                echo '</div>';
            }
        }
        return $szIFrameURL;
    }
    
    public function szShowDisclaimer($szWidth,$szRespBool)
    {
        // Make sure disclaimer is the same width as the iframe/responsive container
        $szDWidth = ($this->szMatchDigits($szWidth)) ? ' style="width:' . $szWidth . 'px;" ' : '';
        $szDisclaimer = '<div id="scrapezon-disclaimer" class="scrape-api"' . $szDWidth . '>' .
                        __('CERTAIN CONTENT THAT APPEARS ON THIS SITE COMES FROM AMAZON SERVICES LLC. THIS CONTENT IS PROVIDED \'AS IS\' AND IS SUBJECT TO CHANGE OR REMOVAL AT ANY TIME.','scrapeazon') .
                        '</div>';
        return $szDisclaimer;
    }
    
    public function szMatchDigits($szParam)
    {
       $szMatches = ((preg_match('/^\d*$/',$szParam))&&(! is_null($szParam))&&(! empty($szParam))) ? true : false;
       return $szMatches;
    }

    public function szShowIFrame($szNoBlanks,$szBorder,$szWidth,$szHeight,$szFrameURL,$szHasReviews)
    {
        $szOutput  = '';
        if((false===(strtolower($szHasReviews)=='true'))&&(true===(strtolower($szNoBlanks)=='true')))
        {
            $szOutput = '';
        } else {
            $szSets      = new szWPOptions();
            $szRespBool  = absint($szSets->getResponsive());
            if($szRespBool) 
            {
                $szOutput .= '<div id="scrapeazon-wrapper" class="scrapeazon-responsive"';
                if (($this->szMatchDigits($szWidth)) || ($this->szMatchDigits($szHeight)))
                { 
                    $szOutput .= ' style="';
                    $szOutput .= ($this->szMatchDigits($szWidth)) ? 'width:' . absint($szWidth) . 'px;' : '';
                    $szOutput .= ($this->szMatchDigits($szHeight)) ? 'height:' . absint($szHeight) . 'px;' : '';
                    $szOutput .= '"';
                }
                 $szOutput .= '>';
            }
        
            $szOutput .= '<iframe id="scrapeazon-iframe" class="scrapeazon-reviews" src="' . 
                         esc_url($szFrameURL) .
                         '" ';
            $szOutput .= (strtolower($szBorder)=='true') ? 'frameborder="1" ' : 'frameborder="0" ';
            $szOutput .= ((!$szRespBool)&&($this->szMatchDigits($szWidth))) ? 'width="' . absint($szWidth) . '" ' : '';
            $szOutput .= ((!$szRespBool)&&($this->szMatchDigits($szHeight))) ? 'height="' . absint($szHeight) . '" ' : '';
            $szOutput .= '></iframe>';
            $szOutput .= ($szRespBool) ? '</div>' : '';
            $szOutput .= $this->szShowDisclaimer($szWidth,$szRespBool);
        
            unset($szSets);
        }
        return $szOutput;
    }
    
    public function szShowURL($szFrameURL)
    {
        return esc_url($szFrameURL);
    }
    
    public function szTransientID($szSCAtts)
    {
           $szScreen     = (is_admin()) ? get_current_screen()->id : '';
           $szTransient  = "szT-";
           if ($szScreen != 'admin_page_scrapeaz-tests')
           {
               $szTransient .= ($szSCAtts["iswidget"]!='false') ? $szSCAtts["iswidget"] : $szSCAtts["asin"] . $szSCAtts["isbn"] . $szSCAtts["upc"] . $szSCAtts["ean"] . $szSCAtts["width"] . $szSCAtts["height"];
           } else {
               $szTransient .= 'testpanel';
           }
           $szTransient   = (strlen($szTransient) > 40) ? substr($szTransient,0,40) : $szTransient;
           return $szTransient;
    }

    public function szParseShortcode($szAtts)
    {       
        // When does our cache expire?
        $szSets            = new szWPOptions();
        if((! $szSets->getAccessKey())||(! $szSets->getSecretKey())||(! $szSets->getAssocID()))
        {
             echo '';
        } else {
            $szTransientExpire = $szSets->getCacheExpire();
        
            $szSCAtts = shortcode_atts( array(
                     'asin'       => '',
                     'upc'        => '',
                     'isbn'       => '',
                     'ean'        => '',
                     'border'     => 'false',
                     'width'      => '',
                     'height'     => '',
                     'country'    => 'us',
                     'url'        => 'false',
                     'noblanks'   => 'false',
                     'iswidget'   => 'false'
	               ), $szAtts);
	           
	        $szTransientID = $this->szTransientID($szSCAtts);
	               
            if ((false === ($szOutput = get_transient($szTransientID)))||($szTransientID=='szT-testpanel'))
            {
                $szURL        = $this->szAmazonURL($szSCAtts['asin'],$szSCAtts['upc'],$szSCAtts['isbn'],$szSCAtts['ean'],$szSCAtts['country']);
                $szXML        = $this->szCallAmazonAPI($szURL);
                $szResults    = simplexml_load_string($szXML);
                $szHasReviews = $szResults->Items->Item->CustomerReviews->HasReviews;

                $szFrameURL   = $this->szRetrieveFrameURL($szResults);
                      
                if(true === ($szSCAtts['url']==strtolower('true'))) 
                {
                    set_transient ($szTransientID, $this->szShowURL($szFrameURL), $szTransientExpire * HOUR_IN_SECONDS);
                    return get_transient($szTransientID);
                } else {
                    if($szTransientID=='szT-testpanel')
                    {
                        return $this->szShowIFrame($szSCAtts['noblanks'],$szSCAtts['border'],$szSCAtts['width'],$szSCAtts['height'],$szFrameURL,$szHasReviews);
                    } else {
                        set_transient ($szTransientID, $this->szShowIFrame($szSCAtts['noblanks'],$szSCAtts['border'],$szSCAtts['width'],$szSCAtts['height'],$szFrameURL,$szHasReviews), $szTransientExpire * HOUR_IN_SECONDS );
                        return get_transient($szTransientID);
                    }
                }

            } else {
                return get_transient($szTransientID);
            }
        }
        unset($szSets);
    }
}

?>