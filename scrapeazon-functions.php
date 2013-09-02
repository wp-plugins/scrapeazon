<?php
function scrape_check_for_curl() {
   if(in_array('curl',get_loaded_extensions())) {
      return true;
   } else {
      return false;
   }
}
function scrape_check_for_fopen() {
   if(ini_get('allow_url_fopen')) {
      return true;
   } else {
      return false;
   }
}
function scrape_curl_exec_enabled() {
  $disabled = explode(', ', ini_get('disable_functions'));
  return !in_array('curl_exec', $disabled);
}
/* 
   The following function was adapted from 
   http://bavotasan.com/2009/a-settings-link-for-your-wordpress-plugins/ 
*/
function scrapeazon_settings_link($links) {
  $settings_link = '<a href="options-general.php?page=scrapeaz-options">Settings</a>';
  array_unshift($links,$settings_link);
  return $links;
  
}
function scrapeazon_api_compliance($scrape_api) {
   $scrape_api = '<div class="scrape-api">CERTAIN CONTENT THAT APPEARS ON THIS SITE COMES FROM AMAZON SERVICES LLC. THIS CONTENT IS PROVIDED \'AS IS\' AND IS SUBJECT TO CHANGE OR REMOVAL AT ANY TIME.</div>';
   return $scrape_api;
}

function scrapeazon_admin_add_page() {
   add_options_page('ScrapeAzon Settings','ScrapeAZon','manage_options','scrapeaz-options','scrapeazon_options');
}

function scrapeazon_options() {
   if(!current_user_can('manage_options')) {
      wp_die( __('You do not have sufficient permissions to access this page.'));
   }
   echo '<div class="wrap">';
   echo '<h2>ScrapeAZon Settings</h2>';
   echo '<form method="post" action="options.php">';
   settings_fields('scrapeazon_options');
?>

<p>In order to access customer review data from Amazon.com, you must have an Amazon.com Associate ID, an Amazon Web Services (AWS) Access Key Id, and an AWS Secret Key. You can obtain an Associate ID by signing up to be an <a href="https://affiliate-program.amazon.com/" target="_blank">Amazon.com affiliate</a>. You can obtain the AWS credentials by signing up to use the <a href="https://affiliate-program.amazon.com/gp/advertising/api/detail/main.html" target="_blank">Product Advertising API</a>.</p>

<table border="0">
<tr><td valign="top" align="left" width="200">AWS Access Key ID</td>
<td valign="top" align="left"><input type="text" name="scrape-aws-access-key-id" id="scrape-aws-access-key-id" size="80" value="<?php echo get_option('scrape-aws-access-key-id');?>" /><td>
</tr>
<tr>
<td valign="top" align="left" width="200">AWS Secret Key</td>
<td valign="top" align="left"><input type="text" name="scrape-aws-secret-key" id="scrape-aws-secret-key" size="80" value="<?php echo get_option('scrape-aws-secret-key');?>" /></td>
</tr>
<tr>
<td valign="top" align="left" width="200">Amazon.com Associate ID</td>
<td valign="top" align="left"><input type="text" name="scrape-amz-assoc-id" id="scrape-amz-assoc-id" size="80" value="<?php echo get_option('scrape-amz-assoc-id');?>" /></td>
</tr>
<tr>
<td valign="top" align="left" width="200">Country Code</td>
<td valign="top" align="left"><?php
	$scrape_dditems = array("--","AT", "CA", "CN", "DE", "ES", "FR", "IN", "IT", "JP", "UK","US");
	echo "<select id='scrape-getcountry' name='scrape-getcountry'>";
	foreach($scrape_dditems as $dditem) {
		$selected = (get_option('scrape-getcountry')==$dditem) ? 'selected="selected"' : '';
		echo "<option value='$dditem' $selected>$dditem</option>";
	}
	echo "</select>";
?>
</td>
</tr>
<tr>
<td valign="top" align="left" width="200">Requirements Check</td>
<td valign="top" align="left">
<?php
$green = "<strong>cURL Status:</strong> ";
$red = "<strong>file_get_contents Status:</strong> ";
if((scrape_check_for_curl())&&(scrape_curl_exec_enabled())) {
   $green .= '<span style="color:green">Available and enabled</span>';
} elseif ((scrape_check_for_curl())&&(!(scrape_curl_exec_enabled()))) {
   $green .= '<span style="color:red">Available, but not enabled</span>';
} else {
   $green = '<span style="color:red">Not available</span>';
} 
if(scrape_check_for_fopen()) {
   $red .= '<span style="color:orange">Enabled</span>';
   if((scrape_check_for_curl())&&(scrape_curl_exec_enabled())) {
      $red .= '<span style="color:orange">, but you should use cURL</span>';
   }
} else {
   $red .= '<span style="color:lime">Disabled</span>';
} 
echo $green . '<br>';
echo $red;
?>
</td>
</tr>
<tr>
<td valign="top" align="left"><input type="checkbox" name="scrape-getmethod" id="scrape-getmethod" value="1" <?php echo get_option('scrape-getmethod');?> /></td>
<td valign="top" align="left" width="300">Select this checkbox to use file_get_contents instead of cURL (<span style="color:red">not recommended</span>).</td>
</tr>
</table>
<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes');?>"/></p>
</form>

<h2>ScrapeAZon Test Frame</h2>
<p>After you click Save Changes, you should see an iframe that contains data from Amazon.com below. If you see the data, ScrapeAZon is configured correctly and should work on your site. If you see no data or you see an error displayed below, please double-check your configuration.</p>

<?php echo do_shortcode('[scrapeazon asin="B006T3HM6W"]') ?>

<h2>ScrapeAZon Usage</h2>

<p><b>Shortcode</b>: <code>[scrapeazon asin="&lt;amazon.com product number&gt;"]</code></p>

<p>Insert the above shortcode into any page or post where you want Amazon.com customer reviews to appear. Replace <code>&lt;amazon.com product number&gt;</code> with the product's ASIN or ISBN-10 to retrieve and display the reviews for that product.</p>

<p><b>CSS</b>: <code>.scrapeazon-reviews</code></p>

<p>AWS returns customer reviews as an iframe URL. You can style the iframe by adding a class named <code>.scrapeazon-reviews</code> to your theme's CSS file. However, you cannot style the internal content of the iframe.</p>
<p>You can also style the iframe by including the following parameters in the shortcode:</p>
<ul>
<li>border="false": disables the default border that some browsers add to iframes</li>
<li>width="&lt;number&gt;": configures the width of the iframe to the desired number of pixels</li>
<li>height="&lt;number&gt;": configures the height of the iframe to the desired number of pixels</li>
</ul>

</div>
<?php
}

function scrapeazon_register_settings() {
   register_setting('scrapeazon_options','scrape-aws-access-key-id','scrapeazon_validate_access');
   register_setting('scrapeazon_options','scrape-aws-secret-key','scrapeazon_validate_secret');
   register_setting('scrapeazon_options','scrape-amz-assoc-id','scrapeazon_validate_affiliate');
   register_setting('scrapeazon_options','scrape-getmethod','scrapeazon_validate_getmethod');
   register_setting('scrapeazon_options','scrape-getcountry','scrapeazon_validate_getcountry');
}

function scrapeazon_validate_getcountry($input) {
   $newKey = trim($input);
   if(preg_match('/[A-Z\-][A-Z\-]/i',$input)) {
      $newKey=$input;
   } else {
      $newKey = 'US';
   }
   return $newKey;
}

function scrapeazon_validate_getmethod($input) {
   $newKey = trim($input);
   if($input==1) {
      $newKey='checked';
   } else {
      $newKey = '';
   }
   return $newKey;
}

function scrapeazon_validate_access($input) {
   $newKey = trim($input);
   if((!preg_match('/^[\w]*$/i',$newKey))||(strlen($newKey)!=20)) {
      $newKey = '';
   }
   return $newKey;
}

function scrapeazon_validate_secret($input) {
   $newKey = trim($input);
   if((!preg_match('/^[\w\W\/]*$/i', $newKey))||(strlen($newKey)!=40)) {
      $newKey = '';
   }
   return $newKey;
}

function scrapeazon_validate_affiliate($input) {
   $newKey = trim($input);
   if(!preg_match('/^[A-Z0-9\_\-]*$/i', $newKey)) {
      $newKey = '';
   }
   return $newKey;
}

function scrapeazon_shortcode($scrapeAtts) {
   extract( shortcode_atts( array(
      'attributes' => '',
      'asin' => '',
      'border' => '',
      'width' => '',
      'height' => '',
      'country' => ''
	  ), $scrapeAtts ) );
   return scrapeazon_scrape($attributes,$asin,$border,$width,$height,$country);
}

function scrapeazon_scrape($attributes,$asin,$border,$width,$height,$country) {
   $scrape_aws_key = get_option('scrape-aws-access-key-id');
   $scrape_aws_secret = get_option('scrape-aws-secret-key');
   $scrape_aws_affiliate = get_option('scrape-amz-assoc-id');
   $scrape_getmethod = get_option('scrape-getmethod');
   $scrape_getcountry = get_option('scrape-getcountry');
   
   if($country=='') {
      if(($scrape_getcountry!='--')&&($scrape_getcountry!='')) {
         $country = $scrape_getcountry;
      } else {
         $country = "US";
      }
   }
   
   if((preg_match('/\w/',$asin))&&(strlen($asin)==10)) {
     // determine which amazon site to use
     $scrape_domain = ".com";
     $scrape_webservices = "webservices.amazon";
     switch($country) {
        case (preg_match('/AT/i',$country) ? true : false) :
             $scrape_domain = ".de";
             break;
        case (preg_match('/CA/i',$country) ? true : false) :
             $scrape_domain = ".ca";
             break;
        case (preg_match('/CN/i',$country) ? true : false) :
             $scrape_domain = ".cn";
             break;
        case (preg_match('/DE/i',$country) ? true : false) :
             $scrape_domain = ".de";
             break;        
        case (preg_match('/ES/i',$country) ? true : false) :
             $scrape_domain = ".es";
             break;
        case (preg_match('/FR/i',$country) ? true : false) :
             $scrape_domain = ".fr";
             break;
        case (preg_match('/IN/i',$country) ? true : false) :
             $scrape_domain = ".in";
             break;  
        case (preg_match('/IT/i',$country) ? true : false) :
             $scrape_domain = ".it";
             break;
        case (preg_match('/JP/i',$country) ? true : false) :
             $scrape_domain = ".co.jp";
             break;
        case (preg_match('/UK/i',$country) ? true : false) :
             $scrape_domain = ".co.uk";
             break;
        case (preg_match('/US/i',$country) ? true : false) :
             $scrape_domain = ".com";
             break;
     }
   
     // construct the Amazon URL to retrieve the data
     $host = $scrape_webservices . $scrape_domain;
     $path = "/onca/xml";
     $req = "AssociateTag=" . $scrape_aws_affiliate .
     "&Availability=Available" .
     "&AWSAccessKeyId=" . $scrape_aws_key .
     "&Condition=All" .
     "&IncludeReviewsSummary=True" .
     "&ItemId=" . $asin .
     "&MerchantId=All" .
     "&Operation=ItemLookup" .   
     "&ResponseGroup=Reviews" .
     "&Service=AWSECommerceService" .
     "&Timestamp=" . gmdate("Y-m-d\TH:i:s\Z") .
     "&Version=2011-08-01";
   
     /* 
        The following 10 lines of code were adapted from a function found online at
        http://randomdrake.com/2009/07/27/amazon-aws-api-rest-authentication-for-php-5/
     */
     parse_str($req, $query);
     ksort($query);
     foreach ($query as $parameter => $value) {
        $parameter = str_replace("%7E", "~", rawurlencode($parameter));
        $value = str_replace("%7E", "~", rawurlencode($value));
        $query_array[] = $parameter . '=' . $value;
     }
     $new_query = implode('&', $query_array);
   
     $signature_string = "GET\n{$host}\n{$path}\n{$new_query}";
     $signature = urlencode(base64_encode(hash_hmac('sha256', $signature_string, $scrape_aws_secret, true)));
     $uri = "http://{$host}{$path}?{$new_query}&Signature={$signature}";
     //echo '<a href="' . $uri . '" target="_blank">Direct Amazon API Link</a>';
     /* End of adapted code */

     if ((strlen($scrape_aws_key)==20)&&(strlen($scrape_aws_secret)==40)&&(strlen($scrape_aws_affiliate)>0))  {
        if(!preg_match('/checked/i',$scrape_getmethod)) {
           $ch = curl_init();
           curl_setopt($ch, CURLOPT_URL, $uri);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
           $xml = curl_exec($ch);
           curl_close($ch);
        } else {
           $xml = file_get_contents($uri);
        }
        // Throttle a bit in verison 1.0.9 and later
        sleep(1);
        
        // Load results
        $Result = simplexml_load_string($xml);
        if($Result->Items->Item->CustomerReviews->HasReviews) {
              $scrape_message = "<iframe class=\"scrapeazon-reviews\" src=\"" . $Result->Items->Item->CustomerReviews->IFrameURL . "\"";
              if((strlen($border)>0)||(strlen($width)>0)||(strlen($height)>0)) {
                 $scrape_message .= " style=\"";
                 if(preg_match('/false/i',$border)) {
                    $scrape_message .= "border:none;";
                 }
                 if(preg_match('/\d*/',$width)) {
                    $scrape_message .= "width:" . $width . "px;";
                 }
                 if(preg_match('/\d*/',$height)) {
                    $scrape_message .= "height:" . $height . "px;";
                 }
                 $scrape_message .= "\"";
              }
              
              $scrape_message .= "></iframe>";
              $scrape_message .= scrapeazon_api_compliance('');
        } else {
              // Error messages might also be $Result->Errors->Error->Message
              if($Result->Items->Request->Errors->Error->Message) {
                 $scrape_message = "<div id=\"scrape-error\"><h2>An Error Occurred</h2> " . $Result->Items->Request->Errors->Error->Code . ": " . $Result->Items->Request->Errors->Error->Message . "</div>\n";
                 $scrape_message .= '<br><br><a href="' . $uri . '" target="_blank">Click here to view the Amazon API Results</a>';
              }
              if($Result->Error->Message) {
                 $scrape_message = "<div id=\"scrape-error\"><h2>An Error Occurred</h2> " . $Result->Error->Code . ": " . $Result->Error->Message . "</div>\n";
                 $scrape_message .= '<br><br><a href="' . $uri . '" target="_blank">Click here to view the Amazon API Results</a>';
              }
        }
     } else {
        $scrape_message = "\n<!-- ScrapeAZon is not properly configured. -->\n";
     }
   } else {
      $scrape_message = "\n<!-- A valid ASIN was not provided or ScrapeAZon is not properly configured. -->\n";
   }
   return $scrape_message;

}
?>