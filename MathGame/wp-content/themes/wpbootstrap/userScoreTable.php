<?php
    add_action( 'widgets_init', 'register_user_score_widget' ); 

    function register_user_score_widget() {  
        register_widget( 'userScoreTable' );  
    }  

    class userScoreTable extends WP_Widget {
        function userScoreTable() {  
            $widget_ops = array( 'classname' => 'User score', 'description' => __('A widget that displays top 5 user scores ', 'wpbootstrap') );  
            $control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'userscore-widget' );  
            $this->WP_Widget( 'userscore-widget', __('User score Widget', 'wpbootstrap'), $widget_ops, $control_ops );  

            get_template_part(overall-user-highscore); 
        }
    }  
?>