<?php
/*
Plugin Name: In-Page Post List
Plugin Script: inpagepostlist.php
Plugin URI: http://www.instantcommute.com/in-page-post-list/
Description: Display a list of posts inside a page or a post. For example if you
have a page that talks about dogs, you can list all related posts by typing
[inpagepostlist_search dogs]. Currently you can search by term, by tag, and by
category. Also, each method of search has an optional "count=number_of_posts".
By default 10 items are shown. You can show a larger or smaller number of posts
by changing that option.
[inpagepostlist_search dogs count=3] (shows 3 posts that contain the search term dog)
[inpagepostlist_tag animals] (shows 10 posts tagged as animals)
[inpagepostlist_category Dog Stories count=30] (shows 30 posts in the category Dog Stories)
Version: 1.0
License: GPL
Author: Michael Nehring
Author URI: http://www.instantcommute.com

=== RELEASE NOTES ===
2011-08-20 - v1.0 - first version
*/

/*
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
Online: http://www.gnu.org/licenses/gpl.txt
*/


	add_filter('the_content', 'inpagepostlist_filter');
	
	function inpagepostlist_filter($text){
		global $id;
		$cid=$id;
		$addcomment=false;
		
		//Do inpagepostlist_search
		$pattern = "|\[inpagepostlist_search\s([^]]+)\]|U";
		preg_match_all ( $pattern , $text , &$matches );
		foreach (array_unique ($matches[1]) as $term) {
			$countpattern="/count=[0-9]{1,}/";
			$maxcount=10;
			$term2=$term;
			if(preg_match($countpattern, $term, $countmatches)){
				$term2=str_replace($countmatches[0],"",$term);
				$maxcount=str_replace("count=","",$countmatches[0]);
			}
			$urlterm=urlencode($term2);
			$insertedtext="";
			$insertedtext.="<ul>";
			$maxcount+=1;//Used to add an extra post, since the current post shows up;
			$search_query = new WP_Query();
			$search_posts = $search_query->query("s=$urlterm&showposts=$maxcount");
			$cnt=0;
			while ($search_query->have_posts()) : $search_query->the_post();
				if($cid!=get_the_ID() && $cnt<$maxcount-1){
					$cnt++;
					$insertedtext.="<li><a href='".get_permalink()."'>".get_the_title()."</a></li>";
				}
			endwhile;
			$insertedtext.="</ul>";
			$text = str_replace('[inpagepostlist_search '.$term.']', "$insertedtext", $text);
			$addcomment=true;
		}
		//End inpagepostlist_search
		
		//Do inpagepostlist_tag
		$pattern = "|\[inpagepostlist_tag\s([^]]+)\]|U";
		preg_match_all ( $pattern , $text , &$matches );
		foreach (array_unique ($matches[1]) as $term) {
			$countpattern="/count=[0-9]{1,}/";
			$maxcount=10;
			$term2=$term;
			if(preg_match($countpattern, $term, $countmatches)){
				$term2=str_replace($countmatches[0],"",$term);
				$maxcount=str_replace("count=","",$countmatches[0]);
			}
			$urlterm=urlencode($term2);
			$insertedtext="";
			$insertedtext.="<ul>";
			$maxcount+=1;//Used to add an extra post, since the current post shows up;
			$search_query = new WP_Query();
			$search_posts = $search_query->query("tag=$urlterm&showposts=$maxcount");
			$cnt=0;
			while ($search_query->have_posts()) : $search_query->the_post();
				if($cid!=get_the_ID() && $cnt<$maxcount-1){
					$cnt++;
					$insertedtext.="<li><a href='".get_permalink()."'>".get_the_title()."</a></li>";
				}
			endwhile;
			$insertedtext.="</ul>";
			$text = str_replace('[inpagepostlist_tag '.$term.']', "$insertedtext", $text);
			$addcomment=true;
		}
		//End inpagepostlist_tag
		
		//Do inpagepostlist_category
		$pattern = "|\[inpagepostlist_category\s([^]]+)\]|U";
		preg_match_all ( $pattern , $text , &$matches );
		foreach (array_unique ($matches[1]) as $term) {
			$countpattern="/count=[0-9]{1,}/";
			$maxcount=10;
			$term2=$term;
			if(preg_match($countpattern, $term, $countmatches)){
				$term2=str_replace($countmatches[0],"",$term);
				$maxcount=str_replace("count=","",$countmatches[0]);
			}
			$urlterm=urlencode($term2);
			$insertedtext="";
			$insertedtext.="<ul>";
			$maxcount+=1;//Used to add an extra post, since the current post shows up;
			$search_query = new WP_Query();
			$catid=get_cat_id("$term2");
			$search_posts = $search_query->query("cat=$$catid&showposts=$maxcount");
			$cnt=0;
			while ($search_query->have_posts()) : $search_query->the_post();
				if($cid!=get_the_ID() && $cnt<$maxcount-1){
					$cnt++;
					$insertedtext.="<li><a href='".get_permalink()."'>".get_the_title()."</a></li>";
				}
			endwhile;
			$insertedtext.="</ul>";
			$text = str_replace('[inpagepostlist_category '.$term.']', "$insertedtext", $text);
			$addcomment=true;
		}
		//End inpagepostlist_category
		
		
		if($addcomment==true){
			//Comment out the next line to remove the credit
			$text.="<p style='font-size:0.6em'><a href='http://www.instantcommute.com'>Post list by InstantCommute.com</a></p>";
		}
		return($text);
	}

?>