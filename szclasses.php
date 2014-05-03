<?php

class szRequirements
{
    public $szCurlEnabled = 'Client URL (cURL) is enabled on your server. ScrapeAZon can use it to retrieve reviews.';
    public $szCurlDisabled = 'Client URL (cURL) is either <a href="http://us2.php.net/manual/en/curl.setup.php">not installed or not enabled</a> on your server. ScrapeAZon might not be able to retrieve reviews.';
    public $szFileGetEnabled = 'PHP fopen wrappers (file_get_contents) are enabled on your server. For security, <a href="http://www.php.net/manual/en/filesystem.configuration.php#ini.allow-url-fopen" target="_blank">disable fopen wrappers</a> and <a href="http://us2.php.net/manual/en/curl.setup.php">use cURL</a> instead.';
    public $szNoRetrieval = 'Neither client URL (cURL) nor fopen wrappers (file_get_contents) are enabled on your server. ScrapeAZon will not be able to retrieve reviews.';

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
        
        if ('1' == absint($_GET['restore_szNotices'])) 
        {
            $this->szRestoreNotices();
        }
        
        if($szScreenID == 'settings_page_scrapeaz-options') {
            if((!($this->szCurlExecCheck($szDisabled)))&&(!($this->szFileGetCheck())))
            {
                printf('<div class="error"><p>%s</p></div>', __($this->szNoRetrieval,'scrapeazon-local'),'ScrapeAZon');
            }
            if ( ! get_user_meta($szUser->ID, 'scrapeazon_ignore_FileGetEnabled') ) {
                if($this->szFileGetCheck())
                {
                    printf('<div class="error"><p>' . __($this->szFileGetEnabled,'scrapeazon-local') . ' | <a href="%1$s">' . __('Hide Notice','scrapeazon-local') . '</a></p></div>', '?page=scrapeaz-options&ignore_FileGetEnabled=0');
                }
            }
            if ( ! get_user_meta($szUser->ID, 'scrapeazon_ignore_CurlEnabled') ) {
                if(($this->szCurlExecCheck($szDisabled))&&($this->szCurlCheck()))
                {
                    printf('<div class="updated"><p>' . __($this->szCurlEnabled,'scrapeazon-local') . ' | <a href="%1$s">' . __('Hide Notice','scrapeazon-local') . '</a></p></div>', '?page=scrapeaz-options&ignore_CurlEnabled=0');
                }
            }
            if ( ! get_user_meta($szUser->ID, 'scrapeazon_ignore_CurlDisabled') ) {
                if((!($this->szCurlExecCheck($szDisabled)))||((!($this->szCurlCheck()))))
                {
                    printf('<div class="updated"><p>' . __($this->szCurlDisabled,'scrapeazon-local') . ' | <a href="%1$s">' . __('Hide Notice','scrapeazon-local') . '</a></p></div>', '?page=scrapeaz-options&ignore_CurlDisabled=0');
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
    public $szOptionsPage    = 'scrapeaz-options';   
    
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
        $szSettingsLink  = '<a href="' . admin_url() . 'admin.php?page=scrapeaz-options">' . __('Settings','scrapeazon-local') . '</a> | ';
        $szSettingsLink .= '<a href="' . admin_url() . 'admin.php?page=scrapeaz-tests">' . __('Test','scrapeazon-local') . '</a>';
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
       
    public function szOptionsCallback()
    {
        $sz1      = __('In order to access customer review data from Amazon.com, you must have an Amazon.com Associate ID, an Amazon Web Services (AWS) Access Key Id, and an AWS Secret Key. You can obtain an Associate ID by signing up to be an ','scrapeazon-local');
        $sz2      = __('Amazon.com affiliate','scrapeazon-local');
        $sz3      = __('. You can obtain the AWS credentials by signing up to use the ','scrapeazon-local');
        $sz4      = __('Product Advertising API','scrapeazon-local');
        $szFormat = '<p>%s<a href="https://affiliate-program.amazon.com/" target="_blank">%s</a>%s<a href="https://affiliate-program.amazon.com/gp/advertising/api/detail/main.html" target="_blank">%s</a>.</p>';

        printf($szFormat,$sz1,$sz2,$sz3,$sz4);
    }
    
    public function szTestCallback()
    {
        //_e('<p>If you have correctly configured ScrapeAZon, you should see an iframe below that contains Amazon.com reviews for the Kindle short story <a href="http://www.amazon.com/Dislike-Isaac-Thorne-ebook/dp/B00HPCF5VU" target="_blank">located here</a> on Amazon. The shortcode used to produce this test is <code>[scrapeazon asin="B00HPCF5VU" width="500" height="400" border="false" country="us"]</code>. If you see reviews in the iframe below, ScrapeAZon is configured correctly and should work on your site. If you see no data or if you see an error displayed below, please double-check your configuration.</p>','scrapeazon-local');

        $sz1      = __('If you have correctly configured ScrapeAZon, you should see an iframe below that contains Amazon.com reviews for the Kindle short story ','scrapeazon-local');
        $sz2      = __('located here','scrapeazon-local');
        $sz3      = __(' on Amazon. The shortcode used to produce this test is ','scrapeazon-local');
        $sz4      = __('. If you see reviews in the iframe below, ScrapeAZon is configured correctly and should work on your site. If you see no data or if you see an error displayed below, please double-check your configuration.','scrapeazon-local');
        $szFormat = '<p>%s<a href="http://www.amazon.com/Dislike-Isaac-Thorne-ebook/dp/B00HPCF5VU" target="_blank">%s</a>%s<code>[scrapeazon asin="B00HPCF5VU" width="500" height="400" border="false" country="us"]</code>%s</p>';

        printf($szFormat,$sz1,$sz2,$sz3,$sz4);
    }
    
    public function szUsageCallback()
    {
        echo '<p><b>' . __('Shortcode','scrapeazon-local') .'</b>: <code>[scrapeazon asin="<i>amazon.com-product-number</i>"]</code></p>';

        $sz1      = __('Insert the above shortcode into any page or post where you want Amazon.com customer reviews to appear. Replace ','scrapeazon-local');
        $sz2      = __(' with the product ASIN or ISBN-10 to retrieve and display the reviews for that product.','scrapeazon-local');
        $sz3      = __('For a more detailed and complete overview of how ScrapeAZon works, click the "Help" tab on the upper right of the ScrapeAZon settings page.','scrapeazon-local');
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
    }
       
    public function szAddAdminPage() 
    {
        //$szOptionsPage = add_menu_page('ScrapeAZon','ScrapeAZon','manage_options','scrapeaz-options',array(&$this, 'szGetOptionsScreen'),((get_bloginfo('version')>3.7) ? 'dashicons-star-half' : ''));
        $szOptionsPage = add_submenu_page('options-general.php','ScrapeAZon','ScrapeAZon','manage_options','scrapeaz-options',array(&$this, 'szGetOptionsScreen'));
        $szTestingPage = add_submenu_page('scrapeaz-options','Tests','Tests','manage_options','scrapeaz-tests',array(&$this, 'szGetOptionsScreen'));
        $szUsingPage   = add_submenu_page('scrapeaz-options','Usage','Usage','manage_options','scrapeaz-usages',array(&$this, 'szGetOptionsScreen'));    
        add_action('load-' . $szOptionsPage, array(&$this, 'szAddHelp'));
        add_action('load-' . $szTestingPage, array(&$this, 'szAddHelp'));
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
        echo '">Tests</a><a href="' . admin_url() .'admin.php?page=scrapeaz-usages&tab=scrapeazon_usage_section" class="nav-tab ';
        echo $active_tab == 'scrapeazon_usage_section' ? 'nav-tab-active' : '';
        echo '">Usage</a></h2>';
        
        // Settings form
        echo '<form method="post" action="options.php">';

        add_settings_section(
            'scrapeazon_retrieval_section',
            __('ScrapeAZon Settings','scrapeazon-local'),
            array(&$this, 'szOptionsCallback'),
            'scrapeaz-options'
        );
               
        add_settings_section(
            'scrapeazon_test_section',
            __('ScrapeAZon Test Frame','scrapeazon-local'),
            array(&$this, 'szTestCallback'),
            'scrapeaz-tests'
        );
               
        add_settings_section(
            'scrapeazon_usage_section',
            __('ScrapeAZon Usage','scrapeazon-local'),
            array(&$this, 'szUsageCallback'),
            'scrapeaz-usages'
        );
        
        // Create settings fields
        $this->getOptionsForm();
        
        settings_fields('scrapeazon-options');
        switch($active_tab)
        {
            case 'scrapeazon_retrieval_section':
                 do_settings_sections('scrapeaz-options');
                 echo get_submit_button();
                 break;
            case 'scrapeazon_test_section':
                 do_settings_sections('scrapeaz-tests');
                 break;
            case 'scrapeazon_usage_section':
                 do_settings_sections('scrapeaz-usages');
                 break;
        }
        echo '</form>';
    }
    
    public function szAWSKeyIDField($args)
    {
        $szField  = '<input type="text" id="scrape-aws-access-key-id" name="scrape-aws-access-key-id" value="';
        $szField .= $this->getAccessKey();
        $szField .= '"/>';
        $szField .= '<label for="scrape-aws-access-key-id"> '  . sanitize_text_field($args[0]) . '</label>';
        echo $szField;
    }
    
    public function szAWSSecretField($args)
    {
        $szField  = '<input type="text" id="scrape-aws-secret-key" name="scrape-aws-secret-key" value="';
        $szField .= $this->getSecretKey();
        $szField .= '"/>';
        $szField .= '<label for="scrape-aws-secret-key"> '  . sanitize_text_field($args[0]) . '</label>';
        echo $szField;
    }
    
    public function szAWSAssocField($args)
    {
        $szField  = '<input type="text" id="scrape-amz-assoc-id" name="scrape-amz-assoc-id" value="';
        $szField .= $this->getAssocID();
        $szField .= '"/>';
        $szField .= '<label for="scrape-amz-assoc-id"> '  . sanitize_text_field($args[0]) . '</label>';
        echo $szField;
    }
    
    public function szMethodField($args)
    {
        $szField  = '<input type="checkbox" name="scrape-getmethod" id="scrape-getmethod" value="1" ' .
                    checked(1, $this->getRetrieveMethod(), false) .
                    $this->getRetrieveMethod() .
                    ' />';
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
	    $szField .= '</select>';
        $szField .= '<label for="scrape-getcountry"> '  . sanitize_text_field($args[0]) . '</label>';
	    echo $szField;
    }
    
    public function szResponsiveField($args)
    {
        $szField  = '<input type="checkbox" name="scrape-responsive" id="scrape-responsive" value="1" ' .
                     checked(1, $this->getResponsive(), false) .
                     ' />';
        $szField .= '<label for="scrape-responsive"> '  . sanitize_text_field($args[0]) . '</label>';
        echo $szField;
    }
    
    public function szRestoreNoticesField($args)
    {
        $szField  = '<a href="?page=scrapeaz-options&restore_szNotices=1">' . __('Restore Hidden ScrapeAZon Notices','scrapeazon-local') . '</a>';
        $szField .= '<label for="scrape-restore"> ' . sanitize_text_field($args[0]) . '</label>';
        echo $szField;
    }
    
    public function szAWSTestField()
    {
        echo do_shortcode('[scrapeazon asin="B00HPCF5VU" width="500" height="400" border="false" country="us"]');
    }
    
    public function getOptionsForm()
    {
        add_settings_field(
            'scrape-aws-access-key-id',
            __('AWS Access Key ID','scrapeazon-local'),
            array(&$this, 'szAWSKeyIDField'),
            'scrapeaz-options',
            'scrapeazon_retrieval_section',
            array(
                __('Enter your 20-character AWS Access Key.','scrapeazon-local')
            )
        );

        add_settings_field(
            'scrape-amz-secret-key',
            __('AWS Secret Key','scrapeazon-local'),
            array(&$this, 'szAWSSecretField'),
            'scrapeaz-options',
            'scrapeazon_retrieval_section',
            array(
                __('Enter your 40-character AWS Secret Key.','scrapeazon-local')
            )
        );
        
        add_settings_field(
            'scrape-aws-assoc-id',
            __('Amazon Associate ID','scrapeazon-local'),
            array(&$this, 'szAWSAssocField'),
            'scrapeaz-options',
            'scrapeazon_retrieval_section',
            array(
                __('Enter your Amazon Advertising Associate ID.','scrapeazon-local')
            )
        );
        
        add_settings_field(
            'scrape-getcountry',
            __('Amazon Country ID','scrapeazon-local'),
            array(&$this, 'szCountryField'),
            'scrapeaz-options',
            'scrapeazon_retrieval_section',
            array(
                __('Select the country code for the Amazon International API from which you want to pull reviews.','scrapeazon-local')
            )
        );
                
        add_settings_field(
            'scrape-getmethod',
            __('Use File_Get_Contents<br><span style="color:red">(not recommended)</span>','scrapeazon-local'),
            array(&$this, 'szMethodField'),
            'scrapeaz-options',
            'scrapeazon_retrieval_section',
            array(
                __('Select this checkbox to use fopen wrappers if your host does not support cURL <span style="color:red">(not recommended)</span>.','scrapeazon-local')
            )
        );
        
        add_settings_field(
            'scrape-responsive',
            __('Use Responsive Style','scrapeazon-local'),
            array(&$this, 'szResponsiveField'),
            'scrapeaz-options',
            'scrapeazon_retrieval_section',
            array(
                __('Select this checkbox to enable ScrapeAZon styles for sites with responsive design.','scrapeazon-local')
            )
        );
        
        add_settings_field(
            'scrape-restore-notices',
            __('Restore Notices','scrapeazon-local'),
            array(&$this, 'szRestoreNoticesField'),
            'scrapeaz-options',
            'scrapeazon_retrieval_section',
            array(
                __('Click this link to restore any important ScrapeAZon Settings notifications you might have hidden.','scrapeazon-local')
            )
        );
        
        add_settings_field(
            'scrape-aws-test-field',
            __('Test Frame','scrapeazon-local'),
            array(&$this, 'szAWSTestField'),
            'scrapeaz-tests',
            'scrapeazon_test_section',
            array(
                __('ScrapeAZon Test Frame.','scrapeazon-local')
            )
        );
    }
    
    public function szAddHelp($szContextHelp)
    {
        $szOverview     = '<p>' .
                          __('The ScrapeAZon plugin retrieves Amazon.com customer reviews for products you choose and displays them in pages or posts on your Wordpress blog by way of a Wordpress shortcode.','scrapeazon-local') .
                          '</p> <p>' .
                          __('You must be a participant in both the Amazon.com Affiliate Program and the Amazon.com Product Advertising API in order to use this plugin. Links to Amazon.com forms to join those programs are available on the ScrapeAZon Settings page.','scrapeazon-local') .
                          '</p> <p><strong>' .
                          __('WARNING','scrapeazon-local') .
                          '</strong>: ' .
                          __('If your PHP implementation does not have client URL (cURL) installed and enabled, you should not attempt to use this plugin. You might be able to use the plugin without cURL if your PHP implementation has fopen wrappers enabled. However, fopen wrappers can be a security risk.','scrapeazon-local') .
                          '</p>';
        $szSettingsUse  = '<p>' .
                          __('The following ScrapeAZon Settings fields are ','scrapeazon-local') .
                          '<strong>' .
                          __('required','scrapeazon-local') .
                          '</strong>:<ul><li><strong>AWS Access Key</strong>: ' .
                          __('A 20-character key assigned to you by the AWS Product Advertising API.','scrapeazon-local') .
                          '</li><li><strong>AWS Secret Key</strong>: ' .
                          __('A 40-character secret key assigned to you by the Amazon Product Advertising API.','scrapeazon-local') .
                          '</li><li><strong>Amazon Associate ID</strong>: ' .
                          __('The short string of characters that identifies your Amazon associate account.','scrapeazon-local') .
                          '</li></ul></p><p>' .
                          __('The following ScrapeAZon Settings are optional','scrapeazon-local') .
                          ':<ul><li><strong>Amazon Country ID</strong>: ' .
                          __('If you select a country here, you will globally enable that country for all your ScrapeAZon shortcodes. If you leave it blank, ScrapeAZon shortcodes will default to reviews from Amazon US unless the ','scrapeazon-local') .
                          '<code>country</code>' .
                          __(' parameter is specified in the shortcode.','scrapeazon-local') .
                          '</li><li><strong>Use File_Get_Contents</strong>: ' .
                          __('If cURL is enabled on your site, DO NOT select this checkbox. If your site does not support cURL, you can select this checkbox to use fopen wrappers instead. However, fopen wrappers are a security risk. Consider installing cURL.','scrapeazon-local') .
                          '</li><li><strong>Use Responsive Style</strong>: ' .
                          __('Selecting this checkbox loads a default ScrapeAZon style sheet that will attempt to scale output for sites that have a responsive design. If you specify the ','scrapeazon-local') .
                          '<code>width</code> ' .
                          __('and ','scrapeazon-local') .
                          '<code>height</code> ' .
                          __('parameters in a shortcode, the containing element will default to that width and height.','scrapeazon-local') .
                          '</li></ul></p>';
        $szShortcodeUse = '<p>' .
                          __('Type the shortcode ','scrapeazon-local') .
                          '<code>[scrapeazon asin="<i>amazon-asin-number</i>"]</code>,' .
                          __(' where ','scrapeazon-local') .
                          '<i>amazon-asin-number</i> ' .
                          __('is the ASIN or ISBN-10 of the product reviews you want to retrieve. The shortcode must be issued in text format in your page or post, not Visual format. Otherwise, the quotation marks inside the shortcode might be rendered incorrectly.','scrapeazon-local') .
                          '</p><p>' .
                          __('You can issue the ScrapeAZon shortcode with the following additional parameters','scrapeazon-local') .
                          ':<ul><li><code>width</code>: ' .
                          __('Specifies the width of the reviews iframe, or of the containing element if the responsive option is enabled.','scrapeazon-local') .
                          '</li><li><code>height</code>: ' .
                          __('Specifies the height of the reviews iframe, or of the containing element if the responsive option is enabled.','scrapeazon-local') .
                          '</li><li><code>border</code>: ' .
                          __('When set to ','scrapeazon-local') .
                          '<code>false</code>, ' .
                          __('disables the border that some browsers automatically add to iframes.','scrapeazon-local') .
                          '</li><li><code>country</code>: ' .
                          __('Overrides the global country setting on the Settings page. Use the two-character country code for the Amazon International site from which you want to obtain reviews.','scrapeazon-local') .
                          '</li></ul></p>';
        $szTestsUse     = '<p>' .
                          __('After you have saved your ScrapeAZon Settings by clicking the ','scrapeazon-local') .
                          '<strong>' .
                          __('Save Changes','scrapeazon-local') .
                          '</strong> ' .
                          __('button, you can click the ','scrapeazon-local') .
                          '<strong>' .
                          __('Tests','scrapeazon-local') .
                          '</strong> ' .
                          __('tab to view some sample reviews frames based on your settings.','scrapeazon-local') .
                          '</p><p>' . 
                          __('If you do not see sample Amazon output on this tab, your ScrapeAZon settings might be incorrect.','scrapeazon-local') .
                          '</p>';
    
        $szScreen   = get_current_screen();           
        $szScreen->add_help_tab(array(
            'id'      => 'szOverviewTab',
            'title'   => __('Overview','scrapeazon-local'),
            'content' => $szOverview,
        ));
        $szScreen->add_help_tab(array(
            'id'      => 'szSettingsUseTab',
            'title'   => __('Settings','scrapeazon-local'),
            'content' => $szSettingsUse,
        ));
        $szScreen->add_help_tab(array(
            'id'      => 'szShortcodeUseTab',
            'title'   => __('Shortcode','scrapeazon-local'),
            'content' => $szShortcodeUse,
        ));
        $szScreen->add_help_tab(array(
            'id'      => 'szTestsUseTab',
            'title'   => __('Tests Tab','scrapeazon-local'),
            'content' => $szTestsUse,
        ));
        return $szContextHelp;
    }
}

class szWidget extends WP_Widget {
    public function __construct() 
    {
	    parent::__construct(
		    'sz_widget', 
		    __('Amazon Reviews','scrapeazon-local'),
		    array( 'description' => __( 'Display Amazon.com reviews for a product you specify.','scrapeazon-local'), )
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
		    $szSCode = '[scrapeazon asin="' . strip_tags($instance['asin']) . '"';
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
		    $szSCode .= ']';
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
			$title = __( 'Amazon Reviews', 'scrapeazon-local' );
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
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:','scrapeazon-local'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
 		<label for="<?php echo $this->get_field_id( 'asin' ); ?>"><?php _e('ASIN:','scrapeazon-local'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'asin' ); ?>" name="<?php echo $this->get_field_name( 'asin' ); ?>" type="text" value="<?php echo esc_attr( $asin ); ?>">
 		<label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e('Width (in pixels):','scrapeazon-local'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" type="text" value="<?php echo esc_attr( $width ); ?>">
 		<label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e('Height (in pixels):','scrapeazon-local' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" type="text" value="<?php echo esc_attr( $height ); ?>">
 		<label for="<?php echo $this->get_field_id( 'border' ); ?>"><?php _e('Border (true/false):','scrapeazon-local' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'border' ); ?>" name="<?php echo $this->get_field_name( 'border' ); ?>" type="text" value="<?php echo esc_attr( $border ); ?>">
		</p>
		<?php 
    }
    
    public function update($new_instance, $old_instance)
    {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
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

    public function szAmazonURL($szASIN,$szCountry)
    {
        $szSets   = new szWPOptions();
        $szSecret = $szSets->getSecretKey();
        
        $szSSLR = $this->szIsSSL();
        
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
                  $szASIN .
                  '&MerchantId=All' .
                  '&Operation=ItemLookup' .   
                  '&ResponseGroup=Reviews' .
                  '&Service=AWSECommerceService' .
                  '&Timestamp=' . 
                  gmdate("Y-m-d\TH:i:s\Z") .
                  '&Version=2011-08-01'; 

         $szAWSURI = $szSSLR . $szHost . $szPath . $this->szGetSignature($szHost,$szPath,$szQuery,$szSecret);
         
         unset($szSets);
         
         return $szAWSURI;   
    }

    public function szCallAmazonAPI($szURL)
    {
        $szSets      = new szWPOptions();
        $szRetries   = 0;
        $szSCCode    = "500";
        
        while(($szRetries<5)&&($szCCode != "200")) {
            usleep(500000*pow($szRetries,2));
            if($szSets->getRetrieveMethod()==1)
            {
                $szXML = file_get_contents($szURL);
            }
            else 
            {
                $szCurl = curl_init();
                curl_setopt($szCurl, CURLOPT_URL, $szURL);
                curl_setopt($szCurl, CURLOPT_RETURNTRANSFER, true);
                $szXML = curl_exec($szCurl);
                $szCCode = curl_getinfo($szCurl,CURLINFO_HTTP_CODE);
                curl_close($szCurl);
            }
            $szRetries = $szRetries + 1;
        }

        unset($szSets);
        
        return $szXML;
    }
    
    public function szRetrieveFrameURL($szXML)
    {
        $szResults = simplexml_load_string($szXML);
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
                        __('CERTAIN CONTENT THAT APPEARS ON THIS SITE COMES FROM AMAZON SERVICES LLC. THIS CONTENT IS PROVIDED \'AS IS\' AND IS SUBJECT TO CHANGE OR REMOVAL AT ANY TIME.','scrapeazon-local') .
                        '</div>';
        return $szDisclaimer;
    }
    
    public function szMatchDigits($szParam)
    {
       $szMatches = ((preg_match('/^\d*$/',$szParam))&&(! is_null($szParam))) ? true : false;
       return $szMatches;
    }

    public function szShowIFrame($szBorder,$szWidth,$szHeight,$szFrameURL)
    {
        $szSets      = new szWPOptions();
        $szRespBool  = absint($szSets->getResponsive());
        
        $szOutput  = '';
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
                      $szFrameURL .
                      '" ';
        $szOutput .= (strtolower($szBorder)=='true') ? 'frameborder="1" ' : 'frameborder="0" ';
        $szOutput .= ((!$szRespBool)&&($this->szMatchDigits($szWidth))) ? 'width="' . absint($szWidth) . '" ' : '';
        $szOutput .= ((!$szRespBool)&&($this->szMatchDigits($szHeight))) ? 'height="' . absint($szHeight) . '" ' : '';
        $szOutput .= '></iframe>';
        $szOutput .= ($szRespBool) ? '</div>' : '';
        $szOutput .= $this->szShowDisclaimer($szWidth,$szRespBool);
        
        unset($szSets);
        
        return $szOutput;
    }

    public function szParseShortcode($szSCAtts)
    {
        extract( shortcode_atts( array(
                 'asin'       => '',
                 'border'     => '',
                 'width'      => '',
                 'height'     => '',
                 'country'    => ''
	           ), $szSCAtts) );
	           
        $szURL        = $this->szAmazonURL($szSCAtts["asin"],$szSCAtts["country"]);
        $szXML        = $this->szCallAmazonAPI($szURL);
        $szFrameURL   = $this->szRetrieveFrameURL($szXML);
        
        return $this->szShowIFrame($szSCAtts["border"],$szSCAtts["width"],$szSCAtts["height"],$szFrameURL);
    }
}

?>