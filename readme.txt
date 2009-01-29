=== Plugin Name ===
Contributors: whomwah 
Donate Link: http://pledgie.org/campaigns/2699
Tags: twitter, simple, tweet
Requires at least: 2.6
Tested up to: 2.7
Stable tag: 1.0 

A Wordpress plugin that posts a tweet to Twitter when you publish a blog post

== Description ==

TweetMe is a simple Wordpress Plugin that posts a tweet to [Twitter](http://twitter.com/) when you publish a blog post. You decide what format the tweet takes by modifying sample string like below. You have access to 2 special tags **#title#** and **#link#** which are replaced with the post title and the condensed link respectivly, so in the example below:

 `New Blog Post: #title# #link#`

would be sent to Twitter as:

 `New Blog Post: My wondeful website http://bit.ly/3QFFTt`

That's it, once set you can carry on writing posts as usual, and when you publish it, TweetMe will twitter about it. The link is shortened to around 18 characters using the [bit.ly](http://bit.ly/ "shorten the length of an URL") service. TweetMe adds a custom field to your post called has_tweeted. You can remove this field to allow the post to be tweeted again.

== Frequently Asked Questions ==

= Does it work with earlier versions of Wordpress? =

Not sure is the simple answer, I run 2.7. If it works with ealier versions, please let me know via [Whomwah.com](http://whomwah.com).

= If I update a blog post that is already published, will it tweet again? =

No, the conditions it will tweet in are:

* You have just published a blog post for the first time
* You have just updated a blog post that was published before you installed TweetMe
* You have deleted the 'has_tweeted' custom field on a post that has been tweeted about 

= Can I contribute to TweetMe? =

Sure, the code lives at the [TweetMe github porject page](http://whomwah.github.com/tweetme "TweetMe via Github"). You can download or fork it there.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload `tweetme.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==

1. TweetMe Configuration screen. This is the only screen you use.
