<?php
/**
 * Plugin Name: Simple Widget Factory
 * Plugin URI: 
 * Description: This is a plugin to create custom widgets areas per the need 
 * Version: 1.0.0
 * Author: Tridib Dawn
 * Author URI: 
 * License: GPLv2
 * Text Domain: simple_widget_factory
 */
// exit if accessed directly
if(!defined( 'ABSPATH')) 
{
	exit;
}
// define plugin version if not defined
if(!defined('SIMPLE_WIDGET_FACTORY_PLUGIN_VERSION')) 
{
	define('SIMPLE_WIDGET_FACTORY_PLUGIN_VERSION','1.0.0');
}
// define plugin directory
if(!defined( 'SIMPLE_WIDGET_FACTORY_PLUGIN')) 
{
	define('SIMPLE_WIDGET_FACTORY_PLUGIN', plugin_dir_path( __FILE__ ));
}
// define plugin url
if(!defined('SIMPLE_WIDGET_FACTORY_PLUGIN_URL')) 
{
	define('SIMPLE_WIDGET_FACTORY_PLUGIN_URL', plugin_dir_url( __FILE__ ));
}
// additional styles for plugins
if(!defined('SIMPLE_WIDGET_FACTORY_PLUGIN_CSS_URI')) {
		define('SIMPLE_WIDGET_FACTORY_PLUGIN_CSS_URI', plugins_url('assets/css/',__FILE__ ));
}
// additional scripts for plugins
if(!defined('SIMPLE_WIDGET_FACTORY_PLUGIN_JS_URI')) {
		define('SIMPLE_WIDGET_FACTORY_PLUGIN_JS_URI', plugins_url('assets/js/',__FILE__ ));
}
/**************************************************************************/
/******************** Class Name: Widget_Factory **************************/
/**************************************************************************/
//	class Widget_Factory starts here
class Simple_Widget_Factory 
{
	/**********************************************************************/
	/******************* Function Name: construct() ***********************/
	/**********************************************************************/
	// construt() function starts here
	public function __construct()
	{
		add_action('admin_menu', array($this,'simple_widget_factory_settings_menu'));	
		add_action( 'widgets_init',array($this,'simple_widget_dynamic_sidebars'),10);
		add_action( 'admin_enqueue_scripts', array($this,'simple_widget_factory_style'));
		add_action( 'admin_notices', array($this,'simple_widget_admin_notices' ),35);
	}	// end of function
	/***********************************************************************/
	/*************** Function Name: widget_factory_style() *****************/
	/***********************************************************************/
	// widget_factory_style() function starts here
	public function simple_widget_factory_style() 
	{
		wp_register_style('widget_factory_style', SIMPLE_WIDGET_FACTORY_PLUGIN_CSS_URI.'style.css', false, SIMPLE_WIDGET_FACTORY_PLUGIN_VERSION );  // aditional style added with 'style.css'
		wp_enqueue_style('widget_factory_style');
		wp_enqueue_script('widget_factory_script', SIMPLE_WIDGET_FACTORY_PLUGIN_JS_URI.'script.js',array('jquery'), SIMPLE_WIDGET_FACTORY_PLUGIN_VERSION);	// aditional js file included with 'script.js'
	}	// end of function
	/***********************************************************************/	
	/***********************************************************************/
	/************ Function Name: widget_factory_settings_menu() ************/
	/***********************************************************************/
	// widget_factory_settings_menu() function starts here
	public function simple_widget_factory_settings_menu() 
	{
		add_menu_page('Widget Plugin Settings', 'Simple Widget Factory', 'manage_options', 'widget-factory', array($this,'simple_widget_factory_init'),SIMPLE_WIDGET_FACTORY_PLUGIN_URL.'images/widgets.png',30);
	}  //  end of function	
	/***********************************************************************/
	/************** Function Name : widget_dynamic_sidebars() **************/
	/***********************************************************************/
	// widget_dynamic_sidebars() function starts here
	public function simple_widget_dynamic_sidebars()
	{
		$widgets=array();
		if(get_option('widget_factory_areas')!='')
		{
			$widgets=get_option('widget_factory_areas');
		}
		if(!empty($widgets) && is_array($widgets))
		{
			foreach($widgets as $w)
			{
				if($w['widget_id']=='')
				{
					continue;
				}
				register_sidebar( array(
					'name'          => $w['widget_name'],
					'id'            => $w['widget_id'],
					'description'   => '',
					'before_widget' => '<div class="col-xs-12 col-sm-6 col-md-'.$w['widget_columns'].' column-4"><div id="%1$s" class="widget widget-footer  %2$s">',
					'after_widget'  => '</div></div>',
					'before_title'  => '<h4 class="widget-title">',
					'after_title'   => '</h4>',
				) );	
			}
		}
	}	// end of function
	/************************************************************************/
	/****************** Function Name: widget_factory_init ******************/
	/************************************************************************/
	// widget_factory_init() functions starts here
	public function simple_widget_factory_init()
	{
		static $magicclass;
		static $magicmsg;
		// IF CONDITION STARTS HERE
		if($_SERVER['REQUEST_METHOD']=='POST') 
		{
			$column = $_POST['coloumnarea'];
			$col_val = '';
			//	switch case starts here
			switch($column)
			{
				case 1:
					$col_val=12;
					break;
				case 2:
					$col_val=6;
					break;
				case 3:
					$col_val=4;
					break;
				case 4:
					$col_val=3;
					break;
				default:
					$col_val='';
			}	// switch case condition ends here
			$arr['widget_id']=str_replace(' ','-',strtolower(sanitize_text_field($_POST['widgettitle'])));
			$arr['widget_name']=sanitize_text_field($_POST['widgettitle']);
			if(!empty($arr['widget_name']))
			{
				update_post_meta($post->ID, 'widgettitle', $arr['widget_name']);
			}
			$arr['widget_columns']=$col_val;
			// $arr['extra_class']=isset($_POST['extra_class'])?$_POST['extra_class']:'';
			if(isset($_POST['extra_class']))
			{
				$arr['extra_class']=sanitize_text_field($_POST['extra_class']);
			}
			$widgets=array();
			// if condition starts
			if(get_option('widget_factory_areas')!='')
			{
				$widgets=get_option('widget_factory_areas');
			}	// if condition ends here
			$widgets[]=$arr;
			update_option( 'widget_factory_areas', $widgets );	// adding widget area on valid given data
		}	//	IF CONDITION ENDS HERE
?>
	<!-----------------------|-------------------------|---------------------->
	<!-----------------------| PLUGIN BODY STARTS HERE |---------------------->
	<!-----------------------|-------------------------|---------------------->
	<h1 style="align: center;"><?php esc_html_e('Simple Widget Factory Settings', 'simple_widget_factory'); ?></h1>
	<form action="?page=widget-factory" method="post">
		<div class="body-container">
		<div class="row-container">
			<label class="textarea-label" for="inputbox"><?php esc_html_e('Widget Area Name:','simple_widget_factory'); ?></label>
				<br>
			<input type="text" name="widgettitle" class="inputbox">
				<br>
			<div class="textarea-description"><?php esc_html_e('Enter your Widget Area Name', 'simple_widget_factory'); ?></div>
		</div>
		<div class="row-container">
			<label class="textarea-label" for="inputbox"><?php esc_html_e('Widget Area Column:', 'simple_widget_factory'); ?></label>
				<br>
			<input type="number" min="1" max="4" step="1" name="coloumnarea" class="inputbox">
				<br>
			<div class="textarea-description"><?php esc_html_e('Enter your Widget Area Column ( Value: 1 - 4 )', 'simple_widget_factory'); ?></div>
		</div>
			<br>
		<input type="submit" class="button button-primary" value="Add Widget Area">
	</div>
	</form>
	<!-------------------------|-----------------------|------------------------>
	<!-------------------------| PLUGIN BODY ENDS HERE |------------------------>
	<!-------------------------|-----------------------|------------------------>
<?php
	}	//	end of function
	/******************************************************************************/
	/********************** Function Name: simple_widget_admin_notices() **********/
	/******************************************************************************/
	// simple_widget_admin_notices() starts 
	function simple_widget_admin_notices() 
	{	
		$allowed_tags_before_after=array('div' => array('class'=>array()),'p' => array('class'=>array()));
		// if condition starts
		if($_SERVER['REQUEST_METHOD']=='POST')
		{
			$arr['widget_name']=sanitize_text_field($_POST['widgettitle']);
			$arr['widget_col']=sanitize_text_field($_POST['coloumnarea']);
			$flag=1;
			// if condition starts
			if($arr['widget_col']=='')
			{
				$class = 'simple_widget_notice notice notice-error is-dismissible	';
				$message = esc_html__( "Widget Area Column cannot be empty", "simple_widget_factory" );				
				$flag=0;
			}	// if condition ends
			// if condition starts
			if($arr['widget_name']=='')
			{
				$class = 'simple_widget_notice notice notice-error is-dismissible	';
				$message = esc_html__( "Widget Area Name cannot be empty", "simple_widget_factory" );				
				$flag=0;
			}	// if condition ends
			// if condition starts
			if($flag==1)
			{
				$class = 'simple_widget_notice notice notice-success is-dismissible	';
				$message = esc_html__( "Widget Area created successfully", "simple_widget_factory" );
			}	// if condition ends
			$str=sprintf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 	
			printf('%s'	,wp_kses($str,$allowed_tags_before_after));
		}	// if condition ends
	}	// end of function   		
}   // end of class
new Simple_Widget_Factory(); //creating a object of class