<?php
/*
Plugin Name: TweetMe
Plugin URI: http://whomwah.github.com/tweetme/ 
Description: TweetMe posts a tweet to <a href="http://twitter.com">Twitter</a> when you publish a blog post
Version: 1.0
Author: Duncan Robertson 
Author URI: http://whomwah.com
*/


function post_to_twitter($tweet) {
  $method = "statuses/update.json?status=".urlencode(stripslashes($tweet));

  if (strlen($tweet) > 140)
    error_log("Tweet too long, Max 140 chars!", 0);
    return false;

  tweetme_twitter_api_call($method);
}

function tweetme_twitter_api_call($method) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, "http://twitter.com/$method");
  curl_setopt($ch, CURLOPT_VERBOSE, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
  curl_setopt($ch, CURLOPT_USERPWD, base64_decode(get_option('twitteruser_encrypted')));
  curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
  curl_setopt($ch, CURLOPT_POST, 1);

  $result = curl_exec($ch);
  $resultArray = curl_getinfo($ch);
  curl_close($ch);

  if ($resultArray['http_code'] == "200") {
    error_log("Success!", 0);
    return true;
  } else {
    error_log("Error: ".$resultArray['http_code'], 0);
    return false;
  }
}

function tweetme($post_ID)  {
  if (!add_post_meta($post_ID, 'has_tweeted', '1', true)) {
    return $post_ID;
  }

  $link = tweetme_bitly_link($post_ID);
	$text = str_replace( '#title#', $_POST['post_title'], get_option('tweetme-text'));

  if (preg_match("/\#link\#/i", $text))
	  $text = str_replace( '#link#', tweetme_bitly_link($link), $text);
	
	if ($text != '')
		post_to_twitter($text);
  
  return $post_ID;
}

function tweetme_bitly_link($id) {
  $link = get_permalink($id);
	return file_get_contents('http://bit.ly/api?url=' . urlencode($link));
}

function tweetme_management() {
  if (get_option('tweetme-text') == '')
		update_option('tweetme-text', 'New Blog Post: #title# #link#');

	if ($_POST['submit-type'] == 'options') {
		update_option('tweetme-text', $_POST['tweetme-text']);
		echo("<div style='width:75%;padding:10px; margin-top:20px; color:#fff;background-color:#2d2;'>Thanks, Configuration Successfully updated!</div>");
	} else if ($_POST['submit-type'] == 'login') {
	  update_option('tweetmeAuthorised', '0');
		update_option('twitteruser', $_POST['twitteruser']);
	  update_option('twitteruser_encrypted', base64_encode($_POST['twitteruser'].':'.$_POST['twitterpass']));
		if (($_POST['twitteruser'] != '') and ($_POST['twitterpass'] != '') and tweetme_twitter_api_call('account/verify_credentials.json')) {
	    update_option('tweetmeAuthorised', '1');
			echo("<div style='width:75%;padding:10px; margin-top:20px; color:#fff;background-color:#2d2;'>Thanks, Twitter details Successfully updated!</div>");
		} else {
		  update_option('twitteruser_encrypted', '');
			echo("<div style='width:75%;padding:10px; margin-top:20px; color:#fff;background-color:red;'>Error, You must provide your correct Twitter login and password!</div>");
		}
	}
?>

<div class="wrap">
  <h2>TweetMe Settings</h2>

  <p>The information below decides how and what actually gets posted to your <a href="http://twitter.com">Twitter</a> feed. The link to the post is shortened to around 18 characters using the <a href="http://bit.ly/app/tools">bit.ly</a> service. Note that Twitter only allows a maximum of 140 characters per tweet. TweetMe adds a custom field to your post called <em>has_tweeted</em>. You can remove this field to allow the post to be tweeted again.</p>

  <h3>Tweet Configuration</h3>
	<form method="post">
	<p><label for="tweetme-text">The text that makes up the tweet ( <span class="setting-description">use <strong>#title#</strong> as placeholder for the posts title, and <strong>#link#</strong> for the generated link</span> )</label></p>
	<input type="text" name="tweetme-text" id="tweetme-text" class="regular-text" size="50" maxlength="122" value="<?php echo(get_option('tweetme-text')) ?>" />
   <p>An Example: <span class="setting-description">New Blog Post: My wondeful website http://bit.ly/3QFFTt</span></p>

	<input type="hidden" name="submit-type" value="options">
  <p class="submit"><input type="submit" name="Submit" class="button-primary" value="Save Changes" /></p>
	</form>

	<h3>Your Twitter Account Details ( <small><?php if (get_option('tweetmeAuthorised') == '1') { echo('authorised'); } else { echo('not yet authorised'); } ?></small> )</h3>
	<form method="post">

  <p><label for="twitterlogin">Twitter Email Address:</label>
  <input class="regular-text code" size="30" type="text" name="twitteruser" id="twitteruser" value="<?php echo(get_option('twitteruser')) ?>" /></p>
  <p><label for="twitterpass">Twitter Password:</label>
  <input class="regular-text" size="20" type="password" name="twitterpass" id="twitterpass" value="<?php echo(get_option('twitterpass')) ?>" /></p>

	<input type="hidden" name="submit-type" value="login">
  <p class="submit"><input type="submit" name="Submit" class="button-primary" value="Save Details" /></p>

	</form>
</div>

<?php
}

function add_tweetme_admin_page() {
	if ( function_exists('add_submenu_page') )
		add_submenu_page('plugins.php', 
      __('TweetMe Configuration'), __('TweetMe Config'), 
      'manage_options', 'tweetme-config', 'tweetme_management');
}

add_action('publish_post', 'tweetme');
add_action('admin_menu', 'add_tweetme_admin_page');

?>
