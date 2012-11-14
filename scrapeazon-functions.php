<?php
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

<table width="650" border="0">
<tr><td valign="top" align="left" width="300">AWS Access Key ID</td>
<td valign="top" align="left"><input type="text" name="scrape-aws-access-key-id" id="scrape-aws-access-key-id" size="80" value="<?php echo get_option('scrape-aws-access-key-id');?>" /><td>
</tr>
<tr>
<td valign="top" align="left" width="300">AWS Secret Key</td>
<td valign="top" align="left"><input type="text" name="scrape-aws-secret-key" id="scrape-aws-secret-key" size="80" value="<?php echo get_option('scrape-aws-secret-key');?>" /></td>
</tr>
<tr>
<td valign="top" align="left" width="300">Amazon.com Associate ID</td>
<td valign="top" align="left"><input type="text" name="scrape-amz-assoc-id" id="scrape-amz-assoc-id" size="80" value="<?php echo get_option('scrape-amz-assoc-id');?>" /></td>
</tr>
<tr>
<td valign="top" align="left"><input type="checkbox" name="scrape-getmethod" id="scrape-getmethod" value="1" <?php echo get_option('scrape-getmethod');?> /></td>
<td valign="top" align="left" width="300">If you receive cURL errors when attempting to retrieve reviews, check this box to use file_get_contents instead.</td>
</tr>
</table>
<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes');?>"/></p>
</form>

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
   if((!preg_match('/^[\w\W]*$/i', $newKey))||(strlen($newKey)!=40)) {
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
      'height' => ''
	  ), $scrapeAtts ) );
   return scrapeazon_scrape($attributes,$asin,$border,$width,$height);
}

function scrapeazon_scrape($attributes,$asin,$border,$width,$height) {
   $scrape_aws_key = get_option('scrape-aws-access-key-id');
   $scrape_aws_secret = get_option('scrape-aws-secret-key');
   $scrape_aws_affiliate = get_option('scrape-amz-assoc-id');
   $scrape_getmethod = get_option('scrape-getmethod');
   
   if((preg_match('/\w/',$asin))&&(strlen($asin)==10)) {
   
     // construct the Amazon URL to retrieve the data
     $host = "webservices.amazon.com";
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
        $Result = simplexml_load_string($xml);
        if(preg_match('/^True$/i',$Result->Items->Item->CustomerReviews->HasReviews)) {
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
              if($Result->Error->Message) {
                 $scrape_message = "<div id=\"scrape-error\"><h2>A ScrapeAZon Error Occurred</h2> " . $Result->Error->Code . ": " . $Result->Error->Message . "</div>\n";
              } else {
                 $scrape_message = "\n<!-- ScrapeAZon did not find any available reviews. -->\n";
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