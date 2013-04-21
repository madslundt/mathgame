<?php
/*
Plugin Name: User score
Plugin URI: 
Description: View user scores in a simple table
Author: Mads Lundt
Version: 1.0
Author URI: 
*/
    class userScore_widgets extends WP_Widget {
        function userScore_widgets() {  

        }

        function widget($args, $instance) {
            extract($args, EXTR_SKIP);
            $title = ($instance['title']) ? $instance['title'] : 'User Score Table';

            echo $before_widget;
            echo $before_title . $title . $after_title;
            get_template_part('overall-user-highscore');
        }  

        function update() {

        }

        function form() {

        }
    }  
?>