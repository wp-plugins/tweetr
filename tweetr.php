<?php
/*
Plugin Name: Tweetr
Plugin URI: http://www.robmcghee.com/tweetr/
Description: Display the latest tweets from a Twitter account
Author: Rob McGhee
Version: 1.0
Author URI: http://www.llygoden.com/
*/

function tweeter() {

	$options = get_option("widget_tweeter"); //get the options setup for the widgit
	
	$username = $options['tUser']; // Your twitter username
	$tweetnum = $options['tNum']; // Number of tweets you want to fetch
	$i = 1; // i is set to 1
	$feed = file_get_contents("http://search.twitter.com/search.atom?q=from:" . $username . "&rpp=" . $tweetnum); // get the twitter feed

	$stepOne = explode("<content type=\"html\">", $feed); 
	while ($i < count($stepOne)){ // we set i to start at one to miss the first line of junk
		$stepTwo = explode("</content>", $stepOne[$i]);
		
		$tweet = $stepTwo[0];
		$tweet = str_replace("&lt;", "<", $tweet);
		$tweet = str_replace("&gt;", ">", $tweet);
		$tweet = str_replace("&amp;", "&", $tweet);
		$tweet = str_replace("&apos;", "&#39;", $tweet);
		$tweet = str_replace("&quot;", "", $tweet); //blank the quote
		$tweet = str_replace("#fb", "", $tweet);
		$tweet = str_replace("&#34;", "", $tweet); // just in case the other type crops up
		
		if ($tweet != ""){ // stop a blank line appearing first off
			if ($i > 1){ 
				echo "<br />"; // put a gap between items
			}
			echo $tweet . "<br />";
		}
		$i++; // increase i to fetch the next tweet
	}

}

function widget_tweeter($args){
	extract($args);
	echo $before_widget;
	echo $before_title;?>Tweetr<?php echo $after_title;
	tweeter();
	echo $after_widget;
}

function tweeter_control(){

	$options = get_option("widget_tweeter");
	if (!is_array( $options )){
		$options = array(
		'tUser' => 'llygoden',
		'tNum' => '1');
	}

	if ($_POST['tweeter-Submit']){
		$options['tUser'] = htmlspecialchars($_POST['tweeter-tUser']);
		$options['tNum'] = htmlspecialchars($_POST['tweeter-tNum']);
		update_option("widget_tweeter", $options);
	}
?>
  <p>
    <label for="tweeter-tUser">Twitter Username: </label>
    <input type="text" id="tweeter-tUser" name="tweeter-tUser" value="<?php echo $options['tUser'];?>" />
  </p>
  <p>
	<label for="tweeter-tNum">Number of Tweets to pull: </label>
	<input type="text" id="tweeter-tNum" name="tweeter-tNum" value="<?php echo $options['tNum']; ?>" />
	<input type="hidden" id="tweeter-Submit" name="tweeter-Submit" value="1" />
  </p>

  <?php
}

function tweeter_init(){
	register_sidebar_widget("Tweetr", "widget_tweeter");
	register_widget_control("Tweetr", "tweeter_control", 300, 200);	
}
 
add_action("plugins_loaded", "tweeter_init");


?>