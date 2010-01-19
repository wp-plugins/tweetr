<?php
/*
Plugin Name: Tweetr
Plugin URI: http://www.robmcghee.com/tweetr/
Description: Display the latest tweets from a Twitter account
Author: Rob McGhee
Version: 1.5
Author URI: http://www.llygoden.com/
*/

function tweeter() {

	$options = get_option("widget_tweeter"); //get the options setup for the widgit
	
	$username = $options['tUser']; // Your twitter username
	if ($options['tInc']) {
		$tweetnum = $options['tNum'] + 1; // Number of tweets you want to fetch
		$i = 2; // we need to skip the first tweet
	}else{
		$tweetnum = $options['tNum']; // Number of tweets you want to fetch
		$i = 1; // i is set to 1
	}
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
			
			if ($options['tInc']){
				if ($i>2){
					echo "<br />"; // put a gap between items
				}
			}elseif($i > 1){
				echo "<br />";
			}
			echo $tweet . "<br />";
		}
		$i++; // increase i to fetch the next tweet
	}

}

function widget_tweeter($args){
	$options = get_option("widget_tweeter"); // get the widget options
	extract($args);
	echo $before_widget;
	echo $before_title;
	echo $options['tTitle']; // Use the title specified by the user
	echo $after_title;
	tweeter();
	echo $after_widget;
}

function tweeter_control(){

	$options = get_option("widget_tweeter");
	if (!is_array( $options )){
		$options = array(
		'tTitle' => 'Tweetr',
		'tUser' => 'llygoden',
		'tNum' => '1',
		'tInc' => '');
	}
	
	if ($options['tTitle'] == ""){
		$options['tTitle'] = "Tweetr";
	}
	
	if ($_POST['tweeter-Submit']){
		$options['tTitle'] = stripslashes(htmlspecialchars($_POST['tweeter-tTitle'])); // title to display in the sidebar
		$options['tUser'] = stripslashes(htmlspecialchars($_POST['tweeter-tUser'])); // Twitter Username
		$options['tNum'] = stripslashes(htmlspecialchars($_POST['tweeter-tNum'])); // Number of Tweets to pull
		$options['tInc'] = $_POST['tweeter-tInc']; // Exclude latest Tweet
		update_option("widget_tweeter", $options);
	}
?>
   <p>
    <label for="tweeter-tTitle">Widget Title: </label>
    <input type="text" id="tweeter-tTitle" name="tweeter-tTitle" value="<?php echo $options['tTitle'];?>" />
  </p>
  <p>
    <label for="tweeter-tUser">Twitter Username: </label>
    <input type="text" id="tweeter-tUser" name="tweeter-tUser" value="<?php echo $options['tUser'];?>" />
  </p>
  <p>
	<label for="tweeter-tNum">Number of Tweets to pull: </label>
	<input type="text" id="tweeter-tNum" name="tweeter-tNum" value="<?php echo $options['tNum']; ?>" />
  </p>
  <p>
    <label for="tweeter-tInc">Exclude Latest Tweet: </label>
	<input type="checkbox" id="tweeter-tInc" name="tweeter-tInc" value="check" <?php if($options['tInc']){ echo "checked='checked'";}?> />
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