<?php
/**
 * Plugin Name: Participants Database
 * Plugin URI: https://xnau.com/wordpress-plugins/participants-database
 * Description: Plugin for managing a database of participants, members or volunteers
 * Author: Roland Barker, xnau webdesign
 * Version: 9.7.9.7
 * Author URI: https://xnau.com
 * License: GPL3
 * Text Domain: participants-database
 * Domain Path: /languages
 */
/*
 * 
 * 
 * 
 * Copyright 2011, 2012, 2013, 2014, 2015, 2016, 2017, 2018 Roland Barker xnau webdesign  (email : webdesign@xnau.com)
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License, version 3, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 */
if ( !defined( 'ABSPATH' ) )
  exit;
// register the class autoloading
spl_autoload_register( 'PDb_class_loader' );

/**
 * main static class for running the plugin
 * 
 * @category   WordPress Plugins
 * @package    wordPress
 * @author     Roland Barker <webdesign@xnau.com>
 * @copyright  2011 - 2018 7th Veil, LLC
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GPL2
 * @version    Release: 1.8
 * 
 */
class Participants_Db extends PDb_Base {

  /**
   * @var string sets the min PHP version level required
   */
  const min_php_version = '5.3';

  /**
   *
   * unique slug for the plugin; this is same as the plugin directory name
   * 
   * @var string unique slug for the plugin
   */
  const PLUGIN_NAME = 'participants-database';

  /**
   * @var string name of the single record query var
   */
  public static $single_query;

  /**
   * @var string name of the record edit query var
   */
  public static $record_query;

  /**
   *  display title
   * @var string
   */
  public static $plugin_title;

  /**
   * basename of the main participants index table
   * @var string
   */
  public static $participants_table;

  /**
   *  base name of the table for all associated values
   * @var string
   */
  public static $fields_table;

  /**
   * name of the table for groups defninitions
   * @var string
   */
  public static $groups_table;

  /**
   * to create a new database version, change this value to the new version number. 
   * This will trigger a database update in the PDb_Init class
   * 
   * @var string current Db version
   */
  public static $db_version = '1.0';

  /**
   * name of the WP option where the current db version is stored
   * @var string
   */
  public static $db_version_option = 'PDb_Db_version';

  /**
   *  current version of plugin
   * @var string
   */
  public static $plugin_version;

  /**
   * name of the WP plugin options
   * @var string
   */
  public static $participants_db_options;

  /**
   * name of the default settings option
   * @var string
   */
  public static $default_options;

  /**
   * plugin option values $name => $value
   * @var array
   */
  public static $plugin_options;

  /**
   * plugin settings object
   * @var object
   */
  public static $Settings;

  /**
   * name of the plugin admin page
   * @var string
   */
  public static $plugin_page;

  /**
   * path to the plugin root ditectory
   * @var string
   */
  public static $plugin_path;

  /**
   * a general-use prefix to set a namespace
   *
   * @var string
   */
  public static $prefix = 'pdb-';

  /**
   * duplicate of $prefix for backwards compatibility
   * @var string
   */
  public static $css_prefix;

  /**
   * the PDb_FormValidation object
   * @var PDb_FormValidation
   */
  public static $validation_errors;

  /**
   * an admin status or error message
   * @var string
   */
  static $admin_message = '';

  /**
   * the type of admin message
   * @var string
   */
  static $admin_message_type;

  /**
   * name of the transient record used to hold the last recor
   * @var string
   */
  public static $last_record;

  /**
   * status code for the last record processed
   * @var string
   */
  public static $insert_status;

  /**
   * header to include with plugin email
   * @var strings
   */
  public static $email_headers;

  /**
   * enclosure character to use
   * @var string
   */
  public static $CSV_enclosure = '"';

  /**
   * list of reserved field names
   * @var array
   */
  public static $reserved_names = array('source', 'subsource', 'id', 'private_id', 'record_link', 'action', 'submit', 'submit-button', 'name', 'day', 'month', 'year', 'hour', 'date', 'minute', 'email-regex', 'combo_search');

  /**
   * true while sending an email
   * @var bool
   */
  public static $sending_email = false;

  /**
   * set of internationalized words
   * @var array
   */
  public static $i18n = array();

  /**
   * the last method used to parse a date
   * 
   * @var string
   */
  public static $date_mode;

  /**
   * index for tracking multiple instances of a shortcode
   * @var int
   */
  public static $instance_index = 1;

  /**
   * @var string name of the list pagination variable
   */
  public static $list_page = 'listpage';

  /**
   * holds the WP session object
   * 
   * @var PDb_Session
   */
  public static $session;

  /**
   * this is set once per plugin instantiation, then all instances are expected to use this instead of running their own queries
   * 
   * @var array of PDb_Form_Field_Def objects, indexed by field name
   */
  public static $fields = array();

  /**
   * @var string context string for the main submission nonce
   */
  public static $main_submission_nonce_key = 'main_submission';

  /**
   * @var int the number of characters to use in the private ID
   */
  public static $private_id_length = 5;

  /**
   * @var int maximum number of emails to send per session
   * 
   * this must be small enough to prevent a script timeout and/or stay under the 
   * typical email rate limit for shared hosting.
   */
  public static $mass_email_session_limit = 100;
  
  /**
   * @var string option flag for admin notices that are shown only once
   */
  const one_time_notice_flag = 'pdb-one-time-notice-shown';

  /**
   * initializes the static class
   * 
   * sets up the class autoloading, configuration values, hooks, filters and shortcodes
   * 
   * @global object $wpdb
   */
  public static function initialize()
  {
    // set the plugin version
    self::$plugin_version = self::_get_plugin_data( 'Version' );

    // define some locations
    self::$default_options = self::$prefix . 'default_options';
    self::$plugin_page = self::PLUGIN_NAME;
    self::$plugin_path = plugin_dir_path( __FILE__ );

    self::$last_record = self::$prefix . 'last_record';
    self::$css_prefix = self::$prefix;

    // install/deactivate and uninstall methods are handled by the PDB_Init class
    register_activation_hook( __FILE__, array('PDb_Init', 'on_activate') );
    register_deactivation_hook( __FILE__, array('PDb_Init', 'on_deactivate') );
    register_uninstall_hook( __FILE__, array('PDb_Init', 'on_uninstall') );

    // admin plugin list display
    add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array(__CLASS__, 'add_plugin_action_links') );
    add_filter( 'plugin_row_meta', array(__CLASS__, 'add_plugin_meta_links'), 10, 2 );
    add_filter( 'all_plugins', array( __CLASS__, 'filter_plugin_data' ) );

    // set the WP hooks to finish setting up the plugin
    add_action( 'plugins_loaded', array(__CLASS__, 'setup_source_names'), 1 );
    add_action( 'plugins_loaded', array(__CLASS__, 'init'), 5 );
    add_action( 'wp', array(__CLASS__, 'check_for_shortcode'), 1 );
    add_action( 'wp', array(__CLASS__, 'remove_rel_link') );

    add_filter( 'admin_body_class', array(__CLASS__, 'add_admin_body_class') );
    add_filter( 'body_class', array(__CLASS__, 'add_body_class') );
    add_action( 'admin_menu', array(__CLASS__, 'plugin_menu') );
    add_action( 'admin_init', array(__CLASS__, 'admin_init') );
    add_action( 'admin_init', array(__CLASS__, 'reg_page_setting_fix') );
    add_action( 'wp_enqueue_scripts', array(__CLASS__, 'register_assets'), 1 );

    add_action( 'wp_loaded', array(__CLASS__, 'process_page_request') ); // wp_loaded
    //
    add_action( 'admin_enqueue_scripts', array(__CLASS__, 'admin_includes') );
    // this is only fired if there is a plugin shortcode on the page
    add_action( 'pdb-shortcode_present', array(__CLASS__, 'add_shortcode_includes') );

    /**
     * MULTISITE
     * 
     * this is so the Participants Database table names are in sync with the current network blog
     */
    add_action( 'switch_blog', array(__CLASS__, 'setup_source_names' ) );
    // set up the database for any new blogs
    add_action( 'wpmu_new_blog', array( 'PDb_Init', 'new_blog' ) );
    add_action( 'delete_blog', array( 'PDb_Init', 'delete_blog' ), 10, 2 );

    add_filter( 'wp_headers', array(__CLASS__, 'control_caching') );
    /**
     * @since 1.6.3
     * added global constant to enable multilingual content of the type that qtranslate-x 
     * employs, where all translations are in the same content and get filtered by 
     * the chosen locale value
     * 
     * we needed to do this because the callback is somewhat expensive and most installs 
     * are not multilingual
     * 
     * the PDB_MULTILINGUAL constant must be set to 1 or true to enable the multilingual 
     * filter
     * 
     * the pdb-translate_string filter can be used in other ways: all display strings 
     * are passed through it
     */
    if ( defined( 'PDB_MULTILINGUAL' ) && (bool) PDB_MULTILINGUAL === true ) {
      add_filter( self::$prefix . 'translate_string', array(__CLASS__, 'string_static_translation'), 20 );
    }

    // handles ajax request from list filter
    add_action( 'wp_ajax_pdb_list_filter', array(__CLASS__, 'pdb_list_filter') );
    add_action( 'wp_ajax_nopriv_pdb_list_filter', array(__CLASS__, 'pdb_list_filter') );

    // define our shortcodes
    foreach( self::plugin_shortcode_list() as $tag ) {
      add_shortcode( $tag, array(__CLASS__, 'print_shortcode') );
    }
    
    // if we have a PDB shortcode in the content, set up a filter
    add_action( 'pdb-shortcode_present', function () {
      add_filter( 'the_content', array( 'Participants_Db', 'fix_shortcode_special_chars' ), 5 );
    } );
    
    // set the debug global if not already
    self::set_debug_mode();
    
    // action for handling the list columns UI
    add_action( 'wp_ajax_' . PDb_Manage_List_Columns::action, 'PDb_Manage_List_Columns::process_request' );

    /*
     * any plugins that require Participants Database should initialize on this action
     * 'participants-database_activated'
     */
    do_action( self::PLUGIN_NAME . '_activated' );
  }
  
  /**
   * provides a list of all plugin shortcodes
   * 
   * @filter 'pdb-plugin_shortcode_list' array
   * 
   * @return  array list of shortcode tags
   */
  public static function plugin_shortcode_list()
  {
    return self::apply_filters( 'plugin_shortcode_list', array(
        'pdb_record',
        'pdb_signup',
        'pdb_signup_thanks',
        'pdb_update_thanks',
        'pdb_request_link',
        'pdb_list',
        'pdb_single',
        'pdb_search',
        'pdb_total',
    ) );
  }
  
  /**
   * performs some needed character replacements
   * 
   * this is to prevent wptexturize from thinking the < or > characters designate an HTML tag
   * 
   * @version 1.7.5
   * @see https://core.trac.wordpress.org/ticket/29608
   * @see https://codex.wordpress.org/Shortcode_API#HTML
   * 
   * @param string  $content_page content
   * @return string
   */
  public static function fix_shortcode_special_chars( $content )
  {
    if ( strpos( $content, '[pdb_list' ) !== false || strpos( $content, '[pdb_total' ) !== false ) {
      // one of the two shortcodes are present, now do the replacements
      $content = preg_replace_callback( 
              '/\[pdb_(?:list|total)(.+)\]/', 
              function ($atts) { 
        return str_replace( array('<','>'), array('&lt;', '&gt;'), $atts[0] );
              }, 
                              $content );
    }
    return $content;
  }

  /**
   * sets up the database and options source names
   * 
   * fired early on the 'plugins_loaded' hook
   * 
   * @global wpdb $wpdb
   */
  public static function setup_source_names()
  {
//    if ( !is_null( self::$participants_table ) ) {
//      return;
//    }
    /*
     * these can be modified later with a filter hook
     * 
     * this allows things like multilingual field definitions or possibly even multiple databases
     * 
     * this must be in a plugin, a theme functions file will be too late!
     */
    global $wpdb;
    $table_basename = $wpdb->prefix . str_replace( '-', '_', self::PLUGIN_NAME );
    self::$participants_table = self::apply_filters( 'select_database_table', $table_basename );
    self::$fields_table = self::apply_filters( 'select_database_table', $table_basename . '_fields' );
    self::$groups_table = self::apply_filters( 'select_database_table', $table_basename . '_groups' );

    // also filter the name of the settings to use
    self::$participants_db_options = self::apply_filters( 'select_database_table', self::PLUGIN_NAME . '_options' );
    
    /**
     * @version 1.6.3
     * @filter pdb-single_query
     * @filter pdb-record_query
     */
    self::$single_query = self::apply_filters( 'single_query', 'pdb' );
    self::$record_query = self::apply_filters( 'record_query', 'pid' );
  }

  /**
   * runs any admin-only initializations
   */
  public static function admin_init()
  {
    /**
     * sets up the update notification and update detail screens
     */
    new PDb_Update_Notices( __FILE__ );
    
    /**
     * sets the admin notices class
     * 
     * admin notices are set with:
     * 
     * $notices = PDb_Admin_Notices::get_instance();
     * $notices->error( 'error message text' );
     */
    PDb_Admin_Notices::get_instance();
    
    
    
    // check the php version for possible warning
    self::php_version_warning();
    
  }

  /**
   * initializes the plugin in the WP environment, fired on the 'plugins_loaded' hook
   * 
   * @return null
   */
  public static function init()
  {
    
    /*
     * instantiate the settings class; this only sets up the settings definitions, 
     * the WP Settings API may not be available at this point, so we register the 
     * settings UI on the 'admin_menu' hook
     */
    self::$Settings = new PDb_Settings();
    
    // start sessions management
    self::$session = new PDb_Session();

    /*
     * set up the base reference object arrays
     * 
     * this is to reduce the number of db queries
     */
    self::_setup_fields();

    self::load_plugin_textdomain( __FILE__ );

    self::$plugin_title = self::apply_filters( 'plugin_title', __( 'Participants Database', 'participants-database' ) );

    self::_set_i18n();
    /**
     * @version 1.6 filter pdb-private_id_length
     */
    self::$private_id_length = self::apply_filters( 'private_id_length', self::$private_id_length );

    /*
     * checks for the need to update the DB
     * 
     * this is to allow for updates to occur in many different ways
     */
    if ( false === get_option( self::$db_version_option ) || get_option( self::$db_version_option ) != self::$db_version )
      PDb_Init::on_update();

    if ( self::plugin_setting_is_true( 'html_email' ) ) {
      $type = 'text/html; charset="' . get_option( 'blog_charset' ) . '"';
    } else {
      $type = 'text/plain; charset=us-ascii';
    }
    $email_headers = "From: " . self::$plugin_options['receipt_from_name'] . " <" . self::$plugin_options['receipt_from_address'] . ">\n" .
            "Content-Type: " . $type . "\n";

    self::$email_headers = self::apply_filters( 'email_headers', $email_headers );

    /**
     * any plugins that require Participants Database settings/database should use this hook
     * 
     * @version 1.6.3
     * @action participants-database_initialized
     */
    do_action( self::PLUGIN_NAME . '_initialized' );
  }

  /**
   * registers all scripts and stylesheets
   */
  public static function register_assets()
  {
    /*
     * normally, the custom CSS is written to a static css file, but on some systems, 
     * that doesn't work, so the fallback is to load the dynamic CSS file
     */
    if ( self::_set_custom_css() ) {
      $custom_css_file = 'PDb-custom.css';
    } else {
      $custom_css_file = 'custom_css.php';
    }
    
    /*
     * register frontend scripts and stylesheets
     */
    wp_register_style( self::$prefix . 'frontend', plugins_url( '/css/participants-database.css', __FILE__ ), array('dashicons'), self::$plugin_version );
    wp_register_style( 'custom_plugin_css', plugins_url( '/css/' . $custom_css_file, __FILE__ ), null, self::$Settings->option_version() );

    wp_register_script( self::$prefix . 'shortcode', plugins_url( 'js/shortcodes.js', __FILE__ ), array('jquery'), self::$plugin_version );
    wp_register_script( self::$prefix . 'list-filter', plugins_url( 'js/list-filter.js', __FILE__ ), array('jquery'), self::$plugin_version );
//    wp_register_script( self::$prefix . 'jq-placeholder', plugins_url( 'js/jquery.placeholder.min.js', __FILE__ ), array('jquery') );
    wp_register_script( self::$prefix . 'otherselect', plugins_url( 'js/otherselect.js', __FILE__ ), array('jquery'), self::$plugin_version );
  }

  /**
   * checks the current page for a plugin shortcode and fires an action if found
   * 
   * this function is fired on the 'wp' action
   * 
   * action fired is: pdb-shortcode_present
   */
  public static function check_for_shortcode()
  {
    global $post;
    /**
     * @filter pdb-shortcode_in_content
     * @param bool true if a plugin shortcode has been detected
     * @param WP_Post object the current post
     * @return bool true if plugin content is present
     */
    if ( is_object( $post ) && self::apply_filters( 'shortcode_in_content', preg_match( '/(?<!\[)\[pdb_/', $post->post_content ) > 0, $post ) ) {
      do_action( Participants_Db::$prefix . 'shortcode_present' );
    }
  }

  /**
   * processes the admin includes
   * 
   * uses WP hook 'admin_enqueue_scripts''
   * 
   * @param string $hook the admin menu hook as provided by the WP filter
   * @return null
   */
  public static function admin_includes( $hook )
  {

    /*
     * register admin scripts and stylesheets
     */
    wp_register_script( self::$prefix . 'cookie', plugins_url( 'js/jquery_cookie.js', __FILE__ ) );
    wp_register_script( self::$prefix . 'manage_fields', plugins_url( 'js/manage_fields.js', __FILE__ ), array('jquery', 'jquery-ui-core', 'jquery-ui-tabs', 'jquery-ui-sortable', 'jquery-ui-dialog', self::$prefix . 'cookie'), self::$plugin_version, true );
    wp_register_script( self::$prefix . 'settings_script', plugins_url( 'js/settings.js', __FILE__ ), array('jquery', 'jquery-ui-core', 'jquery-ui-tabs', self::$prefix . 'cookie'),  self::$plugin_version, true );
    wp_register_script( self::$prefix . 'record_edit_script', plugins_url( 'js/record_edit.js', __FILE__ ), array('jquery', 'jquery-ui-core', 'jquery-ui-tabs', self::$prefix . 'cookie'), self::$plugin_version, true );
//    wp_register_script( self::$prefix . 'jq-placeholder', plugins_url( 'js/jquery.placeholder.min.js', __FILE__ ), array('jquery') );
    wp_register_script( 'jq-doublescroll', plugins_url( 'js/jquery.doubleScroll.js', __FILE__ ), array('jquery', 'jquery-ui-widget') );
    wp_register_script( self::$prefix . 'admin', plugins_url( 'js/admin.js', __FILE__ ), array('jquery', 'jq-doublescroll', 'jquery-ui-sortable'), self::$plugin_version );
    wp_register_script( self::$prefix . 'otherselect', plugins_url( 'js/otherselect.js', __FILE__ ), array('jquery') );
    wp_register_script( self::$prefix . 'list-admin', plugins_url( 'js/list_admin.js', __FILE__ ), array('jquery', 'jquery-ui-dialog'), self::$plugin_version );
    wp_register_script( self::$prefix . 'aux_plugin_settings_tabs', plugins_url( '/js/aux_plugin_settings.js', __FILE__ ), array('jquery', 'jquery-ui-tabs', self::$prefix . 'admin', /*self::$prefix . 'jq-placeholder',*/ self::$prefix . 'cookie'), self::$plugin_version );
    wp_register_script( self::$prefix . 'debounce', plugins_url( 'js/jq_debounce.js', __FILE__ ), array('jquery') );
    wp_register_script( self::$prefix . 'admin-notices', plugins_url( 'js/pdb_admin_notices.js', __FILE__ ), array('jquery'), self::$plugin_version );
    wp_register_script( self::$prefix . 'debug', plugins_url( 'js/pdb_debug.js', __FILE__ ), array('jquery'), self::$plugin_version );
    //wp_register_script( 'datepicker', plugins_url( 'js/jquery.datepicker.js', __FILE__ ) );
    //wp_register_script( 'edit_record', plugins_url( 'js/edit.js', __FILE__ ) );
    if ( self::_set_admin_custom_css() ) {
      wp_register_style( 'custom_plugin_admin_css', plugins_url( '/css/PDb-admin-custom.css', __FILE__ ), false, self::$Settings->option_version() );
    }
    wp_register_style( self::$prefix . 'utility', plugins_url( '/css/xnau-utility.css', __FILE__ ), null, self::$plugin_version );
    wp_register_style( self::$prefix . 'global-admin', plugins_url( '/css/PDb-admin-global.css', __FILE__ ), false, self::$plugin_version );
    wp_register_style( self::$prefix . 'frontend', plugins_url( '/css/participants-database.css', __FILE__ ), null, self::$plugin_version );
    
    wp_register_style( self::$prefix . 'admin', plugins_url( '/css/PDb-admin.css', __FILE__ ), array( 'custom_plugin_admin_css' ), self::$plugin_version );

    if ( false !== stripos( $hook, 'participants-database' ) ) {
//      wp_enqueue_script( self::$prefix . 'jq-placeholder' );
      wp_enqueue_script( self::$prefix . 'admin' );
      wp_enqueue_script( self::$prefix . 'otherselect' );
    }

    if ( false !== stripos( $hook, 'participants-database-list_participants' ) ) {
//      wp_localize_script(self::$prefix.'list-admin', 'list_adminL10n', array(
//          'delete' => PDb_List_Admin::$i18n['delete_checked'],
//          'cancel' => PDb_List_Admin::$i18n['change'],
//          "record" => __("Do you really want to delete the selected record?", 'participants-database' ),
//          "records" => __("Do you really want to delete the selected records?", 'participants-database' ),
//      ));
//      wp_enqueue_script(self::$prefix.'list-admin');
    }

    if ( false !== stripos( $hook, 'participants-database_settings_page' ) ) {
      wp_enqueue_script( self::$prefix . 'settings_script' );
    }

    if ( false !== stripos( $hook, 'participants-database-edit_participant' ) ) {
      //wp_enqueue_script(self::$prefix.'record_edit_script');
    }

    if ( false !== stripos( $hook, 'participants-database-manage_fields' ) ) {
      wp_localize_script( self::$prefix . 'manage_fields', 'manageFields', array('uri' => $_SERVER['REQUEST_URI']) );
      wp_localize_script( self::$prefix . 'manage_fields', 'PDb_L10n', array(
          /* translators: don't translate the words in brackets {} */
          'must_remove' => '<h4>' . __( 'You must remove all fields from the {name} group before deleting it.', 'participants-database' ) . '</h4>',
          /* translators: don't translate the words in brackets {} */
          'delete_confirm' => '<h4>' . __( 'Delete the "{name}" {thing}?', 'participants-database' ) . '</h4>',
          'unsaved_changes' => __( "The changes you made will be lost if you navigate away from this page.", 'participants-database' ),
          'datatype_confirm' => '<h4 class="dashicons-before dashicons-info warning">' . __( 'Changing the form element on a field that has stored data can result in data loss.', 'participants-database' ) .'</h4><p><a href="https://wp.me/p48Sj5-Zb" target="_blank">' . __( 'More information here…', 'participants-database' ) . '</a></p>',
          'datatype_confirm_button' => __( 'Yes, change the form element', 'participants-database' ),
          'datatype_cancel_button' => __( 'No, don\'t change the form element', 'participants-database' ),
      ) );
      wp_enqueue_script( self::$prefix . 'manage_fields' );
    }

    // global admin enqueues
    wp_enqueue_style( 'pdb-global-admin' );
    wp_enqueue_style( 'pdb-utility' );

    // only incude these stylesheets on the plugin admin pages
    if ( false !== stripos( $hook, 'participants-database' ) ) {
      wp_enqueue_style( 'pdb-frontend' );
      wp_enqueue_style( 'pdb-admin' );
      wp_enqueue_style( 'custom_plugin_admin_css' );
    }
  }

  /**
   * adds the enqueueing callback for scripts and stylesheets on the frontend
   * 
   * this is triggered by the 'pdb-shortcode_present' hook
   */
  public static function add_shortcode_includes()
  {
    add_action( 'wp_enqueue_scripts', array(__CLASS__, 'include_assets'), 100 );
  }

  /**
   * include frontend JS and CSS
   * 
   * fired on WP hook 'wp_enqueue_scripts' 
   * 
   * @return null
   */
  public static function include_assets()
  {
    self::include_stylesheets();
    self::add_scripts();
  }

  /**
   * enqueues the frontend CSS
   * 
   * @return null
   */
  public static function include_stylesheets()
  {
    if ( self::plugin_setting_is_true( 'use_plugin_css' ) ) {
      wp_enqueue_style( 'pdb-frontend' );
    }
    wp_enqueue_style( 'custom_plugin_css' );
  }

  /**
   * enqueues the general plugin frontend scripts
   * 
   * @return null
   */
  public static function add_scripts()
  {
    wp_enqueue_script( self::$prefix . 'shortcode' );
//    wp_enqueue_script( self::$prefix . 'jq-placeholder' );
    wp_enqueue_script( self::$prefix . 'otherselect' );
  }

  /**
   * includes files for generating plugin admin pages  
   * 
   * grabs the name from the request and includes the file to display the page; 
   * this is the admin submenu callback
   * 
   * @static
   * @return null
   */
  public static function include_admin_file()
  {
    $file = str_replace( self::$plugin_page . '-', '', filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING ) ) . '.php';

    if ( is_file( plugin_dir_path( __FILE__ ) . $file ) ) {

      // we'll need this in the included file
      global $wpdb;

      include $file;
    }
  }

  /**
   * provides an URL for a single record
   * 
   * @param int $id the record id
   * @return  string  the URL
   */
  public static function single_record_url( $id )
  {
    $page = self::add_uri_conjunction( self::single_record_page() ) . self::$single_query . '=' . $id;
    /**
     * @filter pdb-single_record_url
     * @param string  single record page complete url
     * @param int record id
     * @return string URL to the record's single record page
     */
    return self::apply_filters( 'single_record_url', $page, $id );
  }
  
  /**
   * provides the base URL fo the single record page
   * 
   * @since 1.7.9
   * @return string URL
   */
  public static function single_record_page()
  {
    /**
     * @version 1.7
     * @filter  pdb-single_record_page sets the base page url of the single record page
     * @param string  single record page base url
     * @return string URL
     */
    return self::apply_filters( 'single_record_page', get_permalink( self::$plugin_options['single_record_page'] ) );
  }

  /**
   * shows the frontend edit screen called by the [pdb_record] shortcode
   *
   *
   * the ID of the record to show for editing can be provided one of three ways: 
   *    $_GET['pid'] (private link) or in the POST array (actively editing a record)
   *    $atts['id'](deprecated) or $atts['record_id'] (in the sortcode), or 
   *    self::$session->get('pdbid') (directly from the signup form)
   * 
   * 
   * @param array $atts array of attributes drawn from the shortcode
   * @return string the HTML of the record edit form
   */
  public static function print_record_edit_form( $atts )
  {
    $record_id = false;
    // get the pid from the get string if given (for backwards compatibility)
    $get_pid = filter_input( INPUT_GET, Participants_Db::$record_query, FILTER_SANITIZE_STRING );

    if ( empty( $get_pid ) ) {
      $get_pid = filter_input( INPUT_POST, Participants_Db::$record_query, FILTER_SANITIZE_STRING );
    }
    if ( !empty( $get_pid ) ) {
      $record_id = self::get_participant_id( $get_pid );
    }

    /*
     * get the id from the SESSION array. This will be present if the user has come 
     * from a form that is in a multi-page series
     * 
     * we don't use the PDb_Session::record_id method because that would compromize 
     * the security of the record edit form. Multi-page forms MUST have working 
     * sessions in order to function, the pdb-record_id_in_get_var filter can't 
     * be used to pass the ID in the GET var.
     * 
     */
    if ( $record_id === false ) {
      $record_id = self::get_record_id_by_term( 'id', self::$session->get('pdbid') );
    }

    if ( $record_id === false && (isset( $atts['id'] ) || isset( $atts['record_id'] )) ) {
      if ( isset( $atts['id'] ) & !isset( $atts['record_id'] ) ) {
        $atts['record_id'] = $atts['id'];
        unset( $atts['id'] );
      }
      $record_id = self::get_record_id_by_term( 'id', $atts['record_id'] );
    }

    $atts['record_id'] = $record_id;

    return PDb_Record::print_form( $atts );
  }

  /**
   * updates the "last_accessed" field in the database
   * 
   * @ver 1.5 added $wpdb->prepare()
   * 
   * @param int $id the record to update
   * @global $wpdb
   */
  private static function _record_access( $id )
  {

    global $wpdb;

    $sql = 'UPDATE ' . self::$participants_table . ' SET `last_accessed` = NOW() WHERE `id` = %s';

    return $wpdb->query( $wpdb->prepare( $sql, $id ) );
  }

  /**
   * sets the last_accessed timestamp
   * 
   * @param int $id id of the record to update
   */
  public static function set_record_access( $id )
  {
    self::_record_access( $id );
  }

  /**
   * common function for printing all shortcodes
   * 
   * @param array $params array of paramters passed in from the shortcode
   * @param string $content the content of the enclosure
   * @param string $tag the shortcode identification string
   * @return null 
   */
  public static function print_shortcode( $params, $content, $tag )
  {
    /*
     * $params will be an empty string for a shortcode with no attributes, make 
     * sure it's an array
     */
    $params = (array) $params; 
    if ( ! empty( $content ) ) {
      $params['content'] = $content;
    }
    /**
     * @version 1.6
     * 
     * 'pdb-shortcode_call_{$tag}' filter allows the shortcode atrributes to be 
     * altered before instantiating the shortcode object
     */
    $shortcode_parameters = self::apply_filters( 'shortcode_call_' . $tag, $params );

    switch ( $tag ) {
      case 'pdb_record':
        return self::print_record_edit_form( $shortcode_parameters );
        break;
      case 'pdb_signup':
        return self::print_signup_form( $shortcode_parameters );
        break;
      case 'pdb_signup_thanks':
      case 'pdb_update_thanks':
        return self::print_signup_thanks_form( $shortcode_parameters );
        break;
      case 'pdb_request_link':
        return self::print_retrieval_form( $shortcode_parameters );
        break;
      case 'pdb_list':
        return self::print_list( $shortcode_parameters );
        break;
      case 'pdb_single':
        return self::print_single_record( $shortcode_parameters );
        break;
      case 'pdb_search':
        return self::print_search_form( $shortcode_parameters );
        break;
      case 'pdb_total':
        return self::print_total( $shortcode_parameters );
        break;
    }
  }

  /**
   * prints a "total" value
   * 
   * called by the "pdb_total" shortcode. this is to print a total number of records, 
   * the number of records passing a filter, or an arithmetic sum of all the data 
   * passing a filter.
   * 
   * @param array $params the parameters passed in by the shortcode
   * @return string the output HTML
   */
  public static function print_total( $params )
  {

    $params['module'] = 'total';
    $params['list_limit'] = -1;

    return PDb_List::get_list( $params );
  }

  /**
   * prints a single record called by [pdb_list] shortcode
   * 
   * @param array $params the parameters passed in by the shortcode
   * @return string the output HTML
   */
  public static function print_list( $params )
  {

    $params['module'] = 'list';

    return PDb_List::get_list( $params );
  }

  /**
   * prints a list search form
   * 
   * @param array $params the parameters passed in by the shortcode
   * @return string the output HTML
   */
  public static function print_search_form( $params )
  {

    $params = (array) $params + array('module' => 'search', 'search' => true);

    return PDb_List::get_list( $params );
  }

  /**
   * prints a single record called by [pdb_single] shortcode
   * 
   * @param array $params the parameters passed in by the shortcode
   * @return string the output HTML
   */
  public static function print_single_record( $params )
  {

    // alias the 'id' attribute for backwards compatibility
    if ( isset( $params['id'] ) & !isset( $params['record_id'] ) ) {
      $params['record_id'] = $params['id'];
      unset( $params['id'] );
    }
    if ( isset( $params['record_id'] ) ) {
      $params['record_id'] = self::get_record_id_by_term( 'id', $params['record_id'] );
    }

    return PDb_Single::print_record( $params );
  }

  /**
   * prints a form from the Signup class
   * 
   * @param array $params the parameters from the shortcode
   * @return string the output HTML
   */
  public static function print_signup_class_form( $params )
  {

    $params['post_id'] = get_the_ID();

    return PDb_Signup::print_form( $params );
  }

  /**
   * prints a signup form
   * 
   * @param array $params the parameters passed in by the shortcode
   * @return string the output HTML
   */
  public static function print_signup_form( $params )
  {

    $params['module'] = 'signup';

    return self::print_signup_class_form( $params );
  }

  /**
   * prints the signup thanks form
   * 
   * @param array $params the parameters passed in by the shortcode
   * @return string the output HTML
   */
  public static function print_signup_thanks_form( $params )
  {

    $params['module'] = 'thanks';

    return self::print_signup_class_form( $params );
  }

  /**
   * prints the private ID retrieval form
   * 
   * @param array $params the parameters passed in by the shortcode
   * @return string the output HTML
   */
  public static function print_retrieval_form( $params )
  {

    $params['module'] = 'retrieve';

    return self::print_signup_class_form( $params );
  }

  /**
   * sets up the $fields array
   * 
   * @global wpdb $wpdb
   */
  private static function _setup_fields()
  {
    if ( empty( self::$fields ) ) :
      global $wpdb;
      self::$fields = array();
      $sql = 'SELECT v.* 
              FROM ' . self::$fields_table . ' v 
              ORDER BY v.order';
      $result = $wpdb->get_results( $sql );
      foreach ( $result as $column ) {
        self::$fields[$column->name] = new PDb_Form_Field_Def( $column );
      }
    endif;
  }

  /**
   * get all the attributes of a field by it's name
   * 
   * an attribute or comma-separated list of attributes can be specified if not, 
   * a default list of attributes is retrieved
   * 
   * @global object $wpdb
   * @param string $field the name of the field to get
   * @param string $atts depricated
   * @return stdClass 
   */
  public static function get_field_atts( $field = false, $atts = '*' )
  {
    return self::get_column( $field );
  }

  /**
   * get an array of groups
   *
   * @param string $column comma-separated list of columns to get, defaults to all (*)
   * @param mixed $exclude single group to exclude or array of groups to exclude
   * @return array returns an associative array column => value or indexed array 
   *               if only one column is specified in the $column argument
   */
  public static function get_groups( $column = '*', $exclude = false )
  {

    global $wpdb;

    $where = '';

    if ( $exclude ) {

      $where = ' WHERE `name` ';

      if ( is_array( $exclude ) ) {

        $where .= 'NOT IN ("' . implode( '","', $exclude ) . '") ';
      } else {

        $where .= '!= "' . $exclude . '" ';
      }
    }

    $sql = 'SELECT ' . $column . ' FROM ' . self::$groups_table . $where . ' ORDER BY `order`,`name` ASC';

    // are we looking for only one column?
    // if so, flatten the array
    if ( $column !== '*' and false === strpos( $column, ',' ) ) {

      $output = array();

      foreach ( $wpdb->get_results( $sql, ARRAY_A ) as $row )
        $output[] = $row[$column];

      return $output;
    } else {

      $group_index = array();

      $groups = $wpdb->get_results( $sql, ARRAY_A );

      // build an array indexed by the group's name
      foreach ( $groups as $group )
        $group_index[$group['name']] = $group;

      return $group_index;
    }
  }

  /**
   * gets the names of all the persistent fields
   * 
   * @return array of field names
   */
  public static function get_persistent()
  {

    return self::get_subset( 'persistent' );
  }

  /**
   * gets a list of field names/titles
   * 
   * assembles a list of columns from those columns set to display. Optionally, 
   * a list of fields can be supplied with an array. This allows fields that are 
   * not displayed to be included.
   * 
   * as of 1.5 fields named in the $fields array don't need to have their 'sortable' 
   * flag set in order to be included.
   *
   * @param string $type   if 'sortable' will only select fields flagged as sortable  
   * @param array  $fields array of field names defining the fields listed for the 
   *                       purpose of overriding the default selection
   * @param string $sort   sorting method to use, can be 'order' which uses the
   *                       defined group/field order, 'column' which uses the
   *                       current display column order or 'alpha' which sorts the
   *                       list alphabetially; defaults to 'column'
   * @return array of form: title => name
   */
  public static function get_field_list( $type = false, $fields = false, $sort = 'column' )
  {

    global $wpdb;

    $where_clauses = array();
    if ( $type == 'sortable' && !is_array( $fields ) ) {
      $where_clauses[] = 'f.sortable > 0';
    }
    if ( is_array( $fields ) ) {
      $where_clauses[] = 'f.name IN ("' . implode( '","', $fields ) . '")';
    } elseif ( !is_admin() ) {
      $where_clauses[] = 'f.display_column > 0 ';
    }

    $where = empty( $where_clauses ) ? '' : "WHERE " . implode( ' AND ', $where_clauses );

    switch ( $sort ) {
      case 'alpha':
        $sql = "
          SELECT f.name, REPLACE(f.title,'\\\','') as title
          FROM " . self::$fields_table . " f
          " . $where . "
          ORDER BY f.name";
        break;
      case 'order':
        $sql = "
          SELECT f.name, REPLACE(f.title,'\\\',''), g.order as title
          FROM " . self::$fields_table . " f
          INNER JOIN " . self::$groups_table . " g ON f.group = g.name
          " . $where . "
          ORDER BY g.order, f.order";
        break;
      default:
        $sql = "
          SELECT f.name, REPLACE(f.title,'\\\','') as title
          FROM " . self::$fields_table . " f
          " . $where . "
          ORDER BY f." . (is_admin() ? 'admin_column' : 'display_column');
    }

    $result = $wpdb->get_results( $sql, ARRAY_N );

    // construct an array of this form: title => name
    $return = array();
    foreach ( $result as $item ) {
      if ( isset( $return[$item[1]] ) ) {
        $key = self::title_key( $item[1], $item[0] );
      } else {
        $key = self::title_key( $item[1] );
      }
      $return[$key] = $item[0];
    }
    return $return;
  }

  /**
   * get the names of all the sortable fields
   * 
   * this checks the "sortable" column and collects the list of sortable columns
   * from those columns set to display. Optionally, a list of fields to include
   * can be supplied with an array. This allows fields that are not displayed to
   * be included.
   * 
   * @param array  $fields array of field names defining the fields listed for the 
   *                       purpose of overriding the default selection
   * @param string $sort   sorting method to use, can be 'order' which uses the
   *                       defined group/field order, 'column' which uses the
   *                       current display column order or 'alpha' which sorts the
   *                       list alphabetially; defaults to 'column'
   * @param return array
   */
  public static function get_sortables( $fields = false, $sort = 'column' )
  {

    return self::get_field_list( 'sortable', $fields, $sort );
  }

  /**
   * gets a subset of field names
   *
   * this function only works for boolean qualifiers or "column order" columns where
   * any number greater than 0 indicates the field is to be displayed in a column
   *
   * @param string the name of the qualifier to use to select a set of field names
   * @return array an indexed array of field names
   */
  private static function get_subset( $subset )
  {

    global $wpdb;

    $sql = "
			SELECT `name`
			FROM " . self::$fields_table . "
			WHERE `" . $subset . "` > 0";

    $result = $wpdb->get_results( $sql, ARRAY_N );

    // get the 2nd dimension of the array
    $return = array();
    foreach ( $result as $item )
      $return[] = $item[0];

    return $return;
  }

  /**
   * gets a single column object
   * 
   * @param string $name the column name
   * @return object|bool false if no field defined for the given name
   */
  public static function get_column( $name )
  {
    return isset( self::$fields[$name] ) ? self::$fields[$name] : false;
  }

  /**
   * checks a string against active columns to validate input
   * 
   * @var string $string the name to test
   */
  public static function is_column( $string )
  {

    return isset( self::$fields[$string] );
  }

  /**
   * checks a string against defined groups to validate a group name
   * 
   * @var string $string the name to test
   */
  public static function is_group( $string )
  {

    global $wpdb;

    $sql = 'SELECT COUNT(*)
		        FROM ' . self::$groups_table . ' g
            WHERE g.name = %s';

    $count = $wpdb->get_var( $wpdb->prepare( $sql, trim( $string ) ) );

    return $count > 0;
  }

  /**
   * gets a set of field attributes as filtered by context
   *
   * @global wpdb $wpdb
   * @param string|array $filter sets the context of the display and determines the 
   *                             set of columns to return, also accepts an array of 
   *                             column names
   * @return object the object is ordered first by the order of the group, then 
   *                by the field order
   */
  public static function get_column_atts( $filter = 'new' )
  {

    global $wpdb;

    if ( is_array( $filter ) ) {
      $where = 'WHERE v.name IN ("' . implode( '","', $filter ) . '")';
    } else {
      switch ( $filter ) {

        case 'signup':

          $where = 'WHERE v.signup = 1 AND v.form_element <> "placeholder"';
          break;

        case 'sortable':

          $where = 'WHERE v.sortable = 1 AND v.form_element <> "placeholder"';
          break;

        case 'CSV':

          $where = 'WHERE v.CSV = 1 ';
          break;

        case 'all':

          $where = '';
          break;

        case 'frontend_list':

          $where = 'WHERE v.display_column > 0 ';
          break;

        case 'frontend': // record and single modules

          $where = 'WHERE g.display = 1 AND v.form_element <> "placeholder"';
          break;

        case 'readonly':

          $where = 'WHERE v.group = "internal" OR v.readonly = 1';
          break;

        case 'backend':

          $where = 'WHERE v.name <> "id" AND v.form_element <> "captcha" AND v.form_element <> "placeholder"';
          if ( !current_user_can( self::plugin_capability( 'plugin_admin_capability', 'access admin field groups' ) ) ) {
            // don't show non-displaying groups to non-admin users
            // the "approved" field is an exception; it should be visible to editor users
            $where .= 'AND g.admin = 0 OR v.name = "approved"';
          }
          break;

        case 'new':
        default:

          $where = 'WHERE v.name <> "id"  AND v.form_element <> "captcha"';
      }
    }

    $sql = 'SELECT v.*, g.order FROM ' . self::$fields_table . ' v INNER JOIN ' . self::$groups_table . ' g ON v.group = g.name ' . $where . ' ORDER BY g.order, v.order';

    return $wpdb->get_results( $sql, OBJECT_K );
  }

  /**
   * builds an object of all participant values structured by groups and columns
   *
   * TODO: this function is DEPRICATED in favor of using the Shortcode class to render
   * shortcode output, but we have to leave it in here for the moment because
   * there may be modified templates using this function still in use
   * 
   * @param string $id the id number of the record
   * @param array $exclude an array of fields to ecplude
   * @return object containing all the field and their values, ordered by groups
   */
  public static function single_record_fields( $id, $exclude = '' )
  {

    global $wpdb;

    // get the groups object
    $sql = '
		    SELECT g.title, g.name, g.description  
		    FROM ' . self::$groups_table . ' g 
			WHERE g.display = 1 
			ORDER BY `order` ASC
			';

    $groups = $wpdb->get_results( $sql, OBJECT_K );

    if ( is_array( $exclude ) ) {

      $excludes = "AND v.name NOT IN ('" . implode( "','", $exclude ) . "') ";
    } else
      $excludes = '';

    // add the columns to each group
    foreach ( $groups as $group ) {

      $group->fields = $wpdb->get_results( 'SELECT v.name, v.title, v.form_element 
                                            FROM ' . self::$fields_table . ' v
                                            WHERE v.group = "' . $group->name . '"
                                            ' . $excludes . '
																						AND v.form_element != "hidden"  
                                            ORDER BY v.order
                                            ', OBJECT_K );

      // now get the participant value for the field
      foreach ( $group->fields as $field ) {

        $field->value = current( $wpdb->get_row( "SELECT `" . $field->name . "`
                                         FROM " . self::$participants_table . "
                                         WHERE `id` = '" . $id . "'", ARRAY_N ) );
      } // fields
    }// groups

    return $groups;
  }

  /**
   * processes a form submit
   *
   * this processes all record form submissions front-end and back-
   * 
   * @global wpdb $wpdb
   * 
   * @param array       $post           the array of new values (typically the $_POST array)
   * @param string      $action         the db action to be performed: insert or update
   * @param int|bool    $participant_id the id of the record to update. If it is false 
   *                                    or omitted, it creates a new record, if true, it 
   *                                    creates or updates the default record.
   * @param array|bool  $column_names   array of column names to process from the $post 
   *                                    array, if false, processes a preset set of columns
   *
   * @return int|bool   int ID of the record created or updated, bool false if submission 
   *                    does not validate
   */
  public static function process_form( $post, $action, $participant_id = false, $column_names = false )
  {
    /**
     * reject submissions that aren't properly tagged
     */
    if ( 
            !isset( $action ) || 
            !in_array( $action, array('insert', 'update') ) || 
            ( isset( $post['subsource'] ) && $post['subsource'] !== Participants_Db::PLUGIN_NAME )
            ) {
      return false;
            }

    $currently_importing_csv = isset( $_POST['csv_file_upload'] );

    global $wpdb;

    if ( !empty( $_FILES ) && !$currently_importing_csv ) {
      
//      error_log(__METHOD__.' files: '.print_r($_FILES,1));

      foreach ( $_FILES as $fieldname => $attributes ) {

        if ( UPLOAD_ERR_NO_FILE == $attributes['error'] )
          continue;

        $filepath = PDb_File_Uploads::upload( $fieldname, $attributes, $participant_id );

        if ( false !== $filepath ) {

          // place the path to the file in the field value
          $post[$fieldname] = $filepath;

          $_POST[$fieldname] = basename( $filepath );
        }
      }
    }
    /*
     * checks for a record with a matching field so we can exercise the
     * duplicate record preference
     * 
     * 0 - create new record
     * 1 - update matching record
     * 2 - show validation error
     */
    if ( $currently_importing_csv ) {
      // a CSV upload brings in it's own match preference
      $duplicate_record_preference = filter_input( INPUT_POST, 'match_preference', FILTER_SANITIZE_STRING );
      $match_field = filter_input( INPUT_POST, 'match_field', FILTER_SANITIZE_STRING );
    } else {
      $duplicate_record_preference = self::plugin_setting( 'unique_email', '0' );
      $match_field = self::plugin_setting( 'unique_field', 'id' );

      if ( self::plugin_setting('admin_edits_validated', '0' ) == '0' && is_admin() && self::current_user_has_plugin_role( 'admin', 'csv upload' ) ) {
        /*
         * set the preference to 0 if current user is an admin in the admin and not 
         * importing a CSV
         * 
         * this allows administrators to create new records without being affected 
         * by the duplicate record preference
         */
        $duplicate_record_preference = '0';
      }
    }

    /*
     * to prevent possible exposure of private data when using multipage forms we 
     * don't allow the "update" preference for multipage forms
     * 
     * we also don't allow the "insert" preference because duplicate records can be 
     * created if the user goes back to the signup form page
     */
    if ( self::is_multipage_form() ) {
      $duplicate_record_preference = '2';
    }

    if ( ( $action === 'insert' && $duplicate_record_preference !== '0' ) || ( $action === 'update' && $duplicate_record_preference === '2' ) ) {

      /**
       * @version 1.6.2.6
       * 
       * if we are adding a record in the admin, we don't perform a record update 
       * on a matching record if the intent is to add a new record
       */
      if ( is_admin() && $action === 'insert' && ( ! defined('DOING_AJAX') || ! DOING_AJAX ) && ! $currently_importing_csv ) {
        $duplicate_record_preference = '2';
      }

      $match_field_value = isset( $post[$match_field] ) ? filter_var( $post[$match_field], FILTER_SANITIZE_STRING ) : '';

      /*
       *  prevent updating record from matching itself if we are avoiding duplicates
       */
      $mask_id = $duplicate_record_preference === '2' ? $participant_id : 0;
      
      /*
       * if true, incoming record matches existing record or updated record matches another record
       */
      $record_match = $match_field_value !== '' && self::field_value_exists( $match_field_value, $match_field, $mask_id );
      
      /**
       * @since 1.6
       * the $record_match status variable is made available to a filter so a custom 
       * record matching method can be implemented
       * 
       * @since 1.7.8.11 added duplicate preference and csv import state
       * 
       * @filter pdb-incoming_record_match
       * @param bool  $record_match true if a matching record has been found
       * @param array $post         the submitted post data
       * @param int   $duplicate_record_preference 1 = update matched record, 2 = prevent duplicate
       * @param bool  $currently_importing_csv if true, a CSV import is being processed
       * 
       * @return bool true if a matching record is found
       */
      $record_match = self::apply_filters( 'incoming_record_match', $record_match, $post, $duplicate_record_preference, $currently_importing_csv );

      if ( $record_match ) {
        /*
         * we have found a match
         */
        switch ( $duplicate_record_preference ) {

          case '1': // update matching
            // record with same field value exists...get the id and update the existing record
            if ( 'id' == strtolower( $match_field ) )
              $participant_id = intval( $match_field_value );
            else
              $participant_id = self::_get_participant_id_by_term( $match_field, $match_field_value );
            // get the first one
            if ( is_array( $participant_id ) )
              $participant_id = current( $participant_id );
            
            /**
             * @since 1.7.2
             * @filter pdb-process_form_matched_record
             * 
             * @param int id of the matched record
             * @param array the submitted post data
             * 
             * @return int the id of the matched record
             */
            $participant_id = self::apply_filters('process_form_matched_record', $participant_id, $post );

            // set the update mode
            $action = 'update';
            /**
             * empty any private ID that signup assigned, the record will already have one
             * 
             * @version 1.6.2.6 we let the "add participants" script set the private ID
             */
            //$post['private_id'] = '';

            break;

          case '2': // error/skip

            /*
             * version 1.6.2.6 we removed check for multipage form so this validation 
             * now takes pace in a multipage form
             */
            // set the error message
            if ( !is_object( self::$validation_errors ) ) {
              self::$validation_errors = new PDb_FormValidation();
            }
            self::$validation_errors->add_error( $match_field, 'duplicate' );
            // we won't be saving this record
            $action = 'skip';
            // go on validating the rest of the form
            break;
        }
      } elseif ( $duplicate_record_preference === '1' and strtolower( $match_field ) === 'id' and is_numeric( $match_field_value ) ) {
        /*
         * if the "OVERWRITE" option is set to "id" and the record contains an id, use it to create the record
         */
        $participant_id = intval( $match_field_value );
        if ( $participant_id !== 0 ) {
          $action = 'insert';
        } else
          $participant_id = false;
      }
    } elseif ( $action === 'insert' && $duplicate_record_preference === '0' ) {
      /*
       * if the setting is to add a record, matching or not, we don't allow a Record 
       * ID or a private ID to be stored, we let the plugin assign them so that they 
       * will be certain to be unique
       */
      unset( $post['id'] );
      /**
       * 
       * @version 1.6.2.6 we let the "add participants" script set the private ID
       */
      //$post['private_id'] = '';
    }
    // set the insert status value
    self::$insert_status = $action;

    switch ( $action ) {

      case 'update':
        $sql = 'UPDATE ' . self::$participants_table . ' SET `date_updated` = NOW(), ';
        $where = " WHERE id = " . $participant_id;
        break;

      case 'insert':
        $sql = 'INSERT INTO ' . self::$participants_table . ' SET ';

        if ( !PDb_Date_Parse::is_mysql_timestamp( @$post['date_recorded'] ) ) {
          $sql .= ' `date_recorded` = NOW(), ';
        }
        if ( !PDb_Date_Parse::is_mysql_timestamp( @$post['date_updated'] ) ) {
          $sql .= ' `date_updated` = NOW(), ';
        }
        $where = '';
        break;

      case 'skip':
        // do nothing, this record won't be saved because there is a duplicate error
        $sql = '';
    }

    /*
     * determine the set of columns to process
     * 
     */
    $new_values = array();
    $column_data = array();

    if ( is_array( $column_names ) ) {
      $default_cols = ( $action === 'insert' ? array('private_id') : array() );
      $column_set = array_merge( $column_names, $default_cols );
    } else {

      /**
       * @filter pdb-post_action_override
       * @param the current $_POST action value
       * @return the action value to use: either "signup" or "update"
       */
      if ( self::apply_filters( 'post_action_override', filter_input( INPUT_POST, 'action', FILTER_SANITIZE_STRING ) ) === 'signup' ) {

        $column_set = 'signup';
      } else {

        $column_set = $action == 'update' ? ( is_admin() ? 'backend' : 'frontend' ) : ( $participant_id ? 'all' : 'new' );

      }
    }
    
    $columns = self::get_column_atts( $column_set );
    
    // gather the submit values and add them to the query
    foreach ( $columns as $column ) {
      /** @var object $column */
      /**
       * @action pdb-process_form_submission_column_{$fieldname}
       * 
       * @param object  $column the current column
       * @param array   $post   the current post array
       * 
       */
      do_action( 'pdb-process_form_submission_column_' . $column->name, $column, $post );

      // the validation object is only instantiated when this method is called
      // by a form submission
      if ( is_object( self::$validation_errors ) ) {
        self::$validation_errors->validate( ( isset( $post[$column->name] ) ? self::deep_stripslashes( $post[$column->name] ) : '' ), $column, $post, $participant_id );
      }
      
      $field = new PDb_Field_Item( $column );
      if ( array_key_exists($column->name, $post) ) {
        $field->set_value( $post[$column->name] );
      }
      

      /**
       * readonly field data is prevented from being saved by unauthorized users 
       * when not using the signup form
       */
      if (  
              $field->is_readonly() && 
              ! $field->is_hidden_field() && 
              self::current_user_has_plugin_role( 'editor', 'readonly access' ) === false && 
              self::apply_filters( 'post_action_override', filter_input( INPUT_POST, 'action', FILTER_SANITIZE_STRING ) ) !== 'signup'
              ) {
        $post[$column->name] = '';
      }
      $new_value = false;
          
      // first process the internal fields
      switch ( $column->name ) {

        case 'id':
          $new_value = $participant_id;
          break;

        case 'date_recorded':
        case 'date_updated':
        case 'last_accessed':
          // these are the internal record timestamps
          /*
           *  remove the value from the post data if it is already set in the sql
           */
          if ( strpos( $sql, $column->name ) !== false ) {
            unset( $post[$column->name] );
            $new_value = false;
            break;
          }
          /*
           * always include the value if importing a CSV
           * 
           * also parses the value if "pdb-edit_record_timestamps" filter is true
           */
          if ( $currently_importing_csv || self::apply_filters( 'edit_record_timestamps', false ) ) {
            $new_value = false;
            if ( isset( $post[$column->name] ) && ! empty( $post[$column->name] ) ) {
              if ( PDb_Date_Parse::is_mysql_timestamp( $post[$column->name] ) ) {
                // record it if it is a valid mysql timestamp
                $new_value = $post[$column->name];
              } else {
                // convert the date to a mysql timestamp
                $display_format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
                $timestamp = PDb_Date_Parse::timestamp( $post[$column->name], array('input_format' => $display_format), __METHOD__ . ' saving internal timestamp fields, standard format' );
                if ( ! $timestamp ) {
                  $timestamp = PDb_Date_Parse::timestamp( $post[$column->name], array(), __METHOD__ . ' saving internal timestamp fields' );
                } elseif ( $timestamp ) {
                  $new_value = PDb_Date_Display::get_mysql_timestamp( $timestamp );
                }
              }
            }
            break;
          }
          /*
           * skip the "last_accessed" field: it is set by the PDb_Record class
           */
          if ( $column->name === 'last_accessed' ) {
            $new_value = false;
            break;
          }
          /*
           * skip the "date_recorded" field: it is either set in the query or left alone
           */
          if ( $column->name === 'date_recorded' ) {
            $new_value = false;
          }
          break;

        case 'private_id':
          /*
           * supplied private ID is tested for validity and rejected if not valid
           * 
           * @filter pdb-private_id_validity
           * @param bool  test result using the regex
           * @return bool true if valid
           */
          if ( isset( $post['private_id'] ) && self::apply_filters( 'private_id_validity', preg_match( '#^\w{' . self::private_id_length() . ',}$#', $post['private_id'] ) === 1 ) && self::get_participant_id( $post['private_id'] ) === false ) {
            $new_value = $post['private_id'];
          } else {
            $new_value = $action == 'insert' ? self::generate_pid() : false;
          }

          break;

        default :

          if ( !isset( $post[$column->name] ) ) {
            continue;
          }

          switch ( $column->form_element ) {

            case 'multi-select-other':
            case 'multi-checkbox':
            case 'multi-dropdown':

              $new_value = self::_prepare_array_mysql( array_values( $field->get_value() ) );
              
              break;

            case 'link':
              
              /* translate the link markdown used in CSV files to the array format used in the database
               */
              if ( !is_array( $post[$column->name] ) ) {

                $new_value = self::_prepare_array_mysql( self::get_link_array( $post[$column->name] ) );
              } else {

                $new_value = self::_prepare_array_mysql( $post[$column->name] );
              }
              break;

            case 'rich-text':
              global $allowedposttags;
              $new_value = wp_kses( stripslashes( $post[$column->name] ), $allowedposttags );
              break;

            case 'date':

              if ( $post[$column->name] !== '' ) {
                $new_value = PDb_Date_Parse::timestamp( $post[$column->name], array(), __METHOD__ . ' date field value' );
              } else {
                $new_value = null;
              }
              break;

            case 'captcha':
              $new_value = false;
              break;

            case 'password':
              if ( !empty( $post[$column->name] ) && $post[$column->name] !== PDb_FormElement::dummy ) { // skip saving the password if it is the dummy
                $new_value = wp_hash_password( trim( $post[$column->name] ) );
              } else {
                $new_value = false;
              }
              break;
              
            case 'numeric':
            case 'decimal':
            case 'currency':
              if ( strlen( $post[$column->name] ) > 0 ) {
                $new_value = filter_var( $post[$column->name], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
              } else {
                $new_value = null;
              }
              break;

            case 'image-upload':
            case 'file-upload':

              if ( filter_input( INPUT_POST, $column->name . '-deletefile', FILTER_SANITIZE_STRING ) === 'delete' ) {
                if ( $participant_id && ( self::$plugin_options['file_delete'] == 1 || is_admin() ) ) {
                  $participant_record = self::get_participant($participant_id);
                  
                  self::delete_file( $participant_record[$column->name] );
                }
                unset( $_POST[$column->name] );
                $post[$column->name] = '';
              }
              $new_value = self::_prepare_string_mysql( trim( $post[$column->name] ) );
              break;

            default:

              if ( is_array( $post[$column->name] ) ) {

                $new_value = self::_prepare_array_mysql( $post[$column->name] );
              } else {

                $new_value = self::_prepare_string_mysql( trim( $post[$column->name] ) );
              }
          } // switch column_atts->form_element
      }  // swtich column_atts->name
      
      // don't update the value if importing a CSV and the incoming value is empty #1647
      if ( $currently_importing_csv && strlen( $new_value ) === 0 ) {
        $new_value = false;
      }

      /*
       * add the column and value to the sql; if it is bool false, skip it entirely. 
       * Nulls are added as true nulls
       */
      if ( $new_value !== false ) {

        if ( $new_value !== null ) {
          $new_values[] = $new_value;
        }
        $column_data[] = "`" . $column->name . "` = " . ( $new_value === null ? "NULL" : "%s" );
      }
    } // columns

    // if the validation object exists and there are errors, stop here
    if ( is_object( self::$validation_errors ) && self::$validation_errors->errors_exist() ) {

      // error_log( __METHOD__.' errors exist; returning');

      return false;
    } elseif ( !empty( self::$admin_message ) and 'error' == self::$admin_message_type ) {
      return false;
    }

    /*
     * @version 1.6
     * 
     * add in any missing default values
     */
    if ( $action === 'insert' ) {
      $all_columns = self::get_default_record( $currently_importing_csv === false );
      unset( $all_columns['private_id'], $all_columns['date_recorded'], $all_columns['date_updated'] );
      foreach ( $all_columns as $name => $value ) {
        $find_result = preg_grep( '/' . $name . '/', $column_data );
        if ( count( $find_result ) === 0 && $value != '' ) {
          // if a field with a defined default value is missing from the submission, add it in
          $column_data[] = "`$name` = %s";
          $new_values[] = $value;
        }
      }
    }

    // add in the column names
    $sql .= implode( ', ', $column_data );

    // add the WHERE clause
    $sql .= $where;
    
    // sanitize the values if including user input
    $query = strpos( $sql, '%s' ) !== false ? $wpdb->prepare( $sql, $new_values ) : $sql;

    $result = $wpdb->query( $query );

    if ( PDB_DEBUG )
      self::debug_log( __METHOD__ . ' storing record: ' . $wpdb->last_query );

    $db_error_message = '';
    if ( $result === 0 ) {
      $db_error_message = sprintf( self::$i18n['zero_rows_error'], $wpdb->last_query );
      self::$insert_status = 'error';
    } elseif ( $result === false ) {
      $db_error_message = sprintf( self::$i18n['database_error'], $wpdb->last_query, $wpdb->last_error );
      self::$insert_status = 'error';
    } else {
      // is it a new record?
      if ( $action === 'insert' ) {

        // get the new record id for the return
        $participant_id = $wpdb->insert_id;

        /*
         * is this record a new one created in the admin? This also applies to CSV 
         * imported new records
         */
        if ( is_admin() ) {
          // if in the admin hang on to the id of the last record for an hour
          set_transient( self::$last_record, $participant_id, HOUR_IN_SECONDS );
        }
      }
    }
    /*
     * set up user feedback
     */
    if ( is_admin() ) {
      if ( !$currently_importing_csv && $result ) {
        self::set_admin_message( ($action == 'insert' ? self::$i18n['added'] : self::$i18n['updated'] ), 'updated' );
      } elseif ( !empty( $db_error_message ) ) {
        self::set_admin_message( self::db_error_message( $db_error_message ), 'record-insert error' );
      }
    }
    /*
     * when a record is updated or added, make the cache stale so the new data will be used
     * 
     */
    PDb_Participant_Cache::is_now_stale( $participant_id );

    return $participant_id;
  }

  /**
   * provides a truncated database error message
   * 
   * @param string  the full error message
   * @return  string
   */
  public static function db_error_message( $message )
  {
    return rtrim( stristr( $message, 'on query:', true ), 'on query:' );
  }

  /**
   * parses the markdown string used to store the values for a link form element
   *
   * will also accept a bare URL. If the supplied string or URL does not validate 
   * as an URL, return the string
   *
   * @param string $markdown_string
   * @return array URL, linktext
   */
  public static function get_link_array( $markdown_string )
  {

    if ( preg_match( '#^<([^>]+)>$#', trim( $markdown_string ), $matches ) ) {
      return array($matches[1], '');
    } elseif ( preg_match( '#^\[([^\]]+)\]\(([^\)]+)\)$#', trim( $markdown_string ), $matches ) ) {
      $url = self::valid_url( $matches[2] ) ? $matches[2] : '';
      return array($url, $matches[1]);
    } else
      return self::valid_url( $markdown_string )  ? array($markdown_string, '') : array('', $markdown_string);
  }
  
  /**
   * validates a URL
   * 
   * @param string $url
   * @return bool true if the URL is valid
   */
  public static function valid_url( $url ) {
    /**
     * provides an way to add a custom URL validation
     * 
     * @filter pdb-validate_url
     * @param bool validation result
     * @param string URL
     * @return bool
     */
    return self::apply_filters('validate_url', filter_var( $url, FILTER_VALIDATE_URL, FILTER_NULL_ON_FAILURE ) !== false, $url );
  }

  /**
   * gets the default set of values
   * 
   * this does not include hidden fields
   * 
   * @version 1.6 placeholder elements are also excluded
   *
   * @global wpdb $wpdb
   * @param bool  $add_persistent if true, adds persistent field data
   * @return array name=>value
   */
  public static function get_default_record( $add_persistent = true )
  {
    $sql = 'SELECT f.name,f.default,f.form_element 
            FROM ' . self::$fields_table . ' f
            WHERE f.group != "internal" AND f.form_element != "placeholder"';

    global $wpdb;

    $result = $wpdb->get_results( $sql );

    $default_record = array();

    foreach ( $result as $column ) {

      if ( $column->form_element != 'hidden' )
        $default_record[$column->name] = $column->default;
    }

    // get the id of the last record stored
    $prev_record_id = get_transient( self::$last_record );

    if ( $add_persistent && is_admin() && $prev_record_id ) {

      $previous_record = self::get_participant( $prev_record_id );

      if ( $previous_record ) {

        $persistent_fields = self::get_persistent();

        foreach ( $persistent_fields as $persistent_field ) {

          if ( !empty( $previous_record[$persistent_field] ) ) {

            $default_record[$persistent_field] = $previous_record[$persistent_field];
          }
        }
      }
    }

    // fill in some convenience values
    global $current_user;

//    if ( is_object( $current_user ) ) $default_record['by'] = $current_user->display_name;
//    $default_record['when'] = date_i18n(self::$date_format);
    $default_record['private_id'] = self::generate_pid();
    PDb_Date_Display::reassert_timezone();
    $default_record['date_recorded'] = date( 'Y-m-d H:i:s' );
    /*
     * @version 1.6 stop setting date_updated on new record
     */
    // $default_record['date_updated'] = date('Y-m-d H:i:s');

    return $default_record;
  }

  /**
   * gets the data for a record from a cached dataset
   * 
   * this is optimized for operations that might require this to be called multiple 
   * times, it loads 100 (filtered value) records into the cache, and only performs 
   * the load if the request is for a record outide of the cached range. That range 
   * is then cached as well.
   *
   * @global object $wpdb
   * @param  string|bool $id the record ID; returns default record if omitted or bool false 
   * 
   * @return array|bool associative array of name=>value pairs; false if no record 
   *                    matching the ID was found 
   */
  public static function get_participant( $id )
  {

    if ( false === $id )
      return self::get_default_record();

    if ( self::apply_filters( 'use_participant_caching', true ) ) {
      return PDb_Participant_Cache::get_participant( $id );
    } else {
      return self::_get_participant( $id );
    }
  }

  /**
   * gets an array of record values
   *
   * as of 1.5.5 returns only registered columns
   *
   * @ver 1.5 added $wpdb->prepare
   * 
   * @var 1.6.2.6 added as alternative to cache
   *
   * @global object $wpdb
   * @param  string|bool $id the record ID; returns default record if omitted or bool false 
   * 
   * @return array|bool associative array of name=>value pairs; false if no record 
   *                    matching the ID was found 
   */
  private static function _get_participant( $id = false )
  {

    global $wpdb;

    $columns = array();
    foreach ( self::$fields as $field ) {
      $columns[] = $field->name;
    }

    $sql = 'SELECT p.' . implode( ',p.', $columns ) . ' FROM ' . self::$participants_table . ' p WHERE p.id = %s';

    $result = $wpdb->get_row( $wpdb->prepare( $sql, $id ), ARRAY_A );

    if ( is_array( $result ) ) {
      return array_merge( $result, array('id' => $id) );
    } else {
      return false;
    }
  }

  /**
   * gets a participant id by private ID
   *
   * @param string $pid the private ID for a record
   * 
   * @return int|bool the record ID or false
   *
   */
  public static function get_participant_id( $pid )
  {

    return self::_get_participant_id_by_term( 'private_id', $pid );
  }

  /**
   * finds the ID of a record given the value of one of it's fields. 
   * 
   * Returns the first of multiple matches
   * 
   * @param string $term the name of the field to use in matching the record
   * @param string $value the value to match
   * @return int|bool false if no valid id found
   */
  public static function get_record_id_by_term( $term, $value, $single = true )
  {

    return self::_get_participant_id_by_term( $term, $value, $single );
  }

  /**
   * gets a participant record id by term
   *
   * given an identifier, returns the id of the record identified. If there is
   * more than one record with the given term, returns the first one.
   *
   * @global object $wpdb
   * @param string $term the column to match
   * @param string $value the value to search for
   * @param bool   $single if true, return only one ID
   *
   * @return int|array|bool returns integer if one match, array of integers if multiple 
   *                        matches (and single is false), false if no match
   */
  private static function _get_participant_id_by_term( $term, $value, $single = true )
  {

    global $wpdb;

    if ( !self::is_column( $term ) )
      return false;

    $sql = 'SELECT p.id FROM ' . self::$participants_table . ' p WHERE p.' . $term . ' = %s';
    $result = $wpdb->get_results( $wpdb->prepare( $sql, $value ), ARRAY_N );

    if ( !is_array( $result ) )
      return false;

    $output = array();

    foreach ( $result as $id ) {
      $output[] = current( $id );
    }

    return $single ? current( $output ) : $output;
  }
  
  /**
   * provides the length of the private ID
   * 
   * @return int
   */
  public static function private_id_length()
  {
    return self::apply_filters( 'private_id_length', self::$private_id_length );
  }

  /**
   * generates a 5-character private ID
   *
   * the purpose here is to create a unique yet managably small and unguessable
   * (within reason) id number that can be included in a link to call up a 
   * specific record by a user.
   *
   * @return string unique alphanumeric ID
   */
  public static function generate_pid()
  {

    $pid = '';

    $chr_source = str_split('1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ');

    for ( $i = 0; $i < self::private_id_length(); $i++ ) {

      $pid .= $chr_source[array_rand( $chr_source )];
    }

    $pid = self::apply_filters( 'generate_private_id', $pid );
    
    // if by chance we've generated a string that has been used before, generate another
    return self::_id_exists( $pid, 'private_id' ) ? self::generate_pid() : $pid;
  }

  /**
   * tests for existence of record in main db
   *
   * @global object $wpdb
   * @param string $id the identifier to test
   * @param string $field the db field to test the $id value against
   * @return bool true if a record mathing the criterion exists
   */
  private static function _id_exists( $id, $field = 'id' )
  {

    global $wpdb;

    $id_exists = $wpdb->get_var( $wpdb->prepare( "SELECT EXISTS( SELECT 1 FROM " . self::$participants_table . " p WHERE p." . $field . " = '%s' LIMIT 1 )", $id ) );

    if ( NULL !== $id_exists )
      return $id_exists === '0' ? false : true;
    else {
      error_log( __METHOD__ . ' called with invalid arguments' );
      return false;
    }
  }

  /**
   * returns the next valid record id
   * 
   * the next id can be the next higher or lower. This function will wrap, so it 
   * always returns a valid id.
   * 
   * @global object $wpdb
   * @param string $id the current id
   * @param bool   $increment true for next higher, false for next lower
   * @return string the next valid id
   */
  public static function next_id( $id, $increment = true )
  {
    global $wpdb;
    $max = $wpdb->get_var( 'SELECT MAX(p.id) FROM ' . self::$participants_table . ' p' );
    $id = (int) $id;
    $inc = $increment ? 1 : -1;
    $id = $id + $inc;
    while ( !self::_id_exists( $id ) ) {
      $id = $id + $inc;
      if ( $id > $max )
        $id = 1;
      elseif ( $id < 1 )
        $id = $max;
    }
    return $id;
  }

  /**
   * tests for the presence of an email address in the records
   *
   * @param string $email the email address to search for
   * @return boolean true if email is found
   */
  public static function email_exists( $email )
  {

    if ( !empty( Participants_Db::$plugin_options['primary_email_address_field'] ) ) {
      return self::_id_exists( $email, Participants_Db::$plugin_options['primary_email_address_field'] );
    } else
      return false;
  }

  /**
   * returns > 0 if a record has a value matching the checked field
   *
   * @param string $value the value of the field to test
   * @param string $field the field to test
   * @param int $mask_id optional record id to exclude
   * @return bool true if there is a matching value for the field
   */
  public static function field_value_exists( $value, $field, $mask_id = 0 )
  {
    global $wpdb;

    $match_count = $wpdb->get_var( $wpdb->prepare( "SELECT EXISTS( SELECT 1 FROM " . self::$participants_table . " p WHERE p." . $field . " = '%s' AND p.id <> %s )", $value, $mask_id ) );

    return is_null($match_count) ? false : (bool) $match_count;
  }

  /**
   * prepares a serialized array for display
   * 
   * displays an array as a series of comma-separated strings
   * 
   * @param string $string
   * @return string the prepared string
   */
  public static function array_to_string_notation( $string )
  {

    $value = maybe_unserialize( $string );

    if ( !is_array( $value ) ) {
      return $value;
    }
    /**
     * @see PDb_Manage_Fields::prep_values_array()
     */
    $pair_delim = Participants_Db::apply_filters('field_options_pair_delim', '::' );
    $option_delim = Participants_Db::apply_filters('field_options_option_delim', ',' );

    if ( PDb_FormElement::is_assoc( $value ) ) {
      
      /*
       * here, we create a string representation of an associative array, using 
       * :: to denote a name=>value pair
       */
      $temp = array();
      foreach ( $value as $k => $v ) {
        $temp[] = $k . $pair_delim . $v;
      }
      $value = $temp;
    }

    return implode( $option_delim, $value );
  }

  /**
   * adds a blank field type record
   * 
   * @global object $wpdb
   * @param array $params the setup paramters for the new field
   * @return boolean 
   */
  public static function add_blank_field( $params )
  {
    // prevent spurious field creation
    if ( !isset( $params['name'] ) || empty( $params['name'] ) ) return;
    
    global $wpdb;
    $wpdb->hide_errors();

    $field_parameters = wp_parse_args( $params, array('form_element' => 'text-line') );

    $wpdb->insert( self::$fields_table, $field_parameters );

    if ( $wpdb->last_error ) {

      if ( PDB_DEBUG )
        self::debug_log( __METHOD__ . ' failed to add row ' . $params['name'] . ' with error: ' . $wpdb->last_error );

      return false;
    }

    // if this column does not exist in the DB, add it
    if ( count( $wpdb->get_results( "SHOW COLUMNS FROM `" . self::$participants_table . "` LIKE '" . $field_parameters['name'] . "'", ARRAY_A ) ) < 1 ) {

      if ( false === ( self::_add_db_column( $field_parameters ) ) ) {

        if ( PDB_DEBUG )
          self::debug_log( __METHOD__ . ' failed to add column with error: ' . $wpdb->last_error .'  data: ' . print_r( $field_parameters, true ) );

        return false;
      }
    }
    return true;
  }

  /**
   * adds a new column (field) to the databse
   * 
   * @global object $wpdb
   * @param array $atts a set of attributrs to define the new columns
   * @retun bool success of the operation
   */
  private static function _add_db_column( $atts )
  {

    global $wpdb;

    $datatype = PDb_FormElement::get_datatype( $atts );

    $sql = 'ALTER TABLE `' . self::$participants_table . '` ADD `' . $atts['name'] . '` ' . $datatype . ' NULL';

    return $wpdb->query( $sql );
  }

  /**
   * processes any POST requests
   * 
   * this is called on the 'init' hook
   * 
   * @global object $wpdb
   * @return null
   */
  public static function process_page_request()
  {

    $post_sanitize = array(
        'subsource' => FILTER_SANITIZE_STRING,
        'action' => FILTER_SANITIZE_STRING,
        'pdb_data_keys' => FILTER_SANITIZE_STRING,
        'submit_button' => FILTER_SANITIZE_STRING,
        'filename' => FILTER_SANITIZE_STRING,
        'base_filename' => FILTER_SANITIZE_STRING,
        'CSV_type' => FILTER_SANITIZE_STRING,
        'include_csv_titles' => FILTER_VALIDATE_BOOLEAN,
        'nocookie' => FILTER_VALIDATE_BOOLEAN,
        'previous_multipage' => FILTER_SANITIZE_STRING,
    );
    /*
     * $post_input is used for control functions, not for the dataset
     */
    $post_input = filter_input_array( INPUT_POST, $post_sanitize );

    // only process POST arrays from this plugin's pages
    if ( empty( $post_input['subsource'] ) || $post_input['subsource'] != self::PLUGIN_NAME or empty( $post_input['action'] ) )
      return;

    // add a filter to check the submission before anything is done with it
    if ( self::apply_filters( 'check_submission', true ) === false )
      return;

//     error_log( __METHOD__.' post:'.print_r( $_POST, true ) );

    /*
     * the originating page for a multipage form is saved in a session value
     * 
     * if this is an empty string, it is assumed the submission was not part of a multipage form series
     */
    self::$session->set( 'previous_multipage', $post_input['previous_multipage'] );

    /*
     * get the defined columns for the submitting shortcode (if any)
     * 
     * this is needed so that validation will be performed on the expected list 
     * of fields, not just what's found in the POST array
     */
    $columns = false;
    if ( !empty( $post_input['pdb_data_keys'] ) )
      $columns = self::get_data_key_columns( $post_input['pdb_data_keys'] );

    /*
     * instantiate the validation object if we need to. This is necessary
     * because another script can instantiate the object in order to add a
     * feedback message
     * 
     * we don't validate administrators in the admin
     */
    if ( !is_object( self::$validation_errors ) ) {
      if ( Participants_Db::is_form_validated() ) {
        self::$validation_errors = new PDb_FormValidation();
      }
    }

    switch ( $post_input['action'] ) :

      case 'update':
      case 'insert':

        /*
         * we are here for one of these cases:
         *   a) we're adding a new record in the admin
         *   b) a user is updating their record on the frontend
         *   c) an admin is updating a record
         *
         * signups are processed in the case 'signup' section
         * 
         * set the raw post array filters. We pass in the $_POST array, expecting 
         * a possibly altered copy of it to be returned
         * 
         * filter: pdb-before_submit_update
         * filter: pdb-before_submit_add
         */
        $post_data = self::apply_filters( 'before_submit_' . ($post_input['action'] === 'insert' ? 'add' : 'update'), $_POST );

        if ( isset( $_POST['id'] ) ) {
          $id = filter_input( INPUT_POST, 'id', FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)) );
        } elseif ( isset( $_GET['id'] ) ) {
          $id = filter_input( INPUT_GET, 'id', FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)) );
        } else {
          $id = false;
        }

        $participant_id = self::process_form( $post_data, $post_input['action'], $id, $columns );

        if ( false === $participant_id ) {

          /**
           * @action pdb-submission_not_verified
           * @param array submission data
           * @param int|bool record id or bool false
           * @param PDb_Form_Validation object
           */
          do_action('pdb-submission_not_verified', $post_data, $id, self::$validation_errors );
          // we have errors; go back to form and show errors
          return;
        }

        /*
         * set the stored record hook.
         * 
         * hook: pdb-after_submit_update
         * hook: pdb-after_submit_add
         */
        $wp_hook = self::$prefix . 'after_submit_' . ($post_input['action'] == 'insert' ? 'add' : 'update');
        do_action( $wp_hook, self::get_participant( $participant_id ) );

        /*
         * if we are submitting from the frontend, set the feedback message and 
         * send the update notification
         */
        if ( !is_admin() ) {

          /*
           * if the user is an admin, the validation object won't be instantiated, 
           * so we do that here so the feedback message can be shown.
           */
          if ( !is_object( self::$validation_errors ) )
            self::$validation_errors = new PDb_FormValidation();

          self::$validation_errors->add_error( '', self::$plugin_options['record_updated_message'] );

          if ( self::$plugin_options['send_record_update_notify_email'] && !self::is_multipage_form() ) {

            PDb_Template_Email::send( array(
                'to' => self::plugin_setting( 'email_signup_notify_addresses' ),
                'subject' => self::plugin_setting( 'record_update_email_subject' ),
                'template' => self::plugin_setting( 'record_update_email_body' ),
                'context' => 'record update notify',
                    ), $participant_id
            );
          }
          
          /*
           * if the "thanks page" is defined as another page, save the ID in a session variable and move to that page.
           */
          if ( isset( $post_data['thanks_page'] ) && $post_data['thanks_page'] != $_SERVER['REQUEST_URI'] ) {

            self::$session->set( 'pdbid', $post_data['id'] );
            self::$session->set( 'previous_multipage', $post_data['shortcode_page'] );
            
            $query_args = apply_filters( 'pdb-record_id_in_get_var', false ) ? array( self::$single_query => $post_data['id'] ) : array();

            $redirect = $post_data['thanks_page'];
            /**
             * this is to handle the special case where the frontend record form uses a separate 
             * thanks page using the [pdb_signup_thanks] shortcode
             */
            if ( $post_input['action'] == 'insert' && !self::is_multipage_form() ) {
              $query_args = array_merge( $query_args, array( 'action' => 'update' ) );
              //self::add_uri_conjunction( $redirect ) . 'action=update';
            }

            wp_redirect( add_query_arg( $query_args, $redirect ) );

            exit;
          }

          return;
        }

        // redirect according to which submit button was used
        switch ( $post_input['submit_button'] ) {

          case self::$i18n['apply'] :
            $redirect = get_admin_url() . 'admin.php?page=' . self::PLUGIN_NAME . '-edit_participant&id=' . $participant_id;
            break;

          case self::$i18n['next'] :
            $get_id = $post_input['action'] == 'update' ? '&id=' . self::next_id( $participant_id ) : '';
            $redirect = get_admin_url() . 'admin.php?page=' . self::PLUGIN_NAME . '-edit_participant' . $get_id;
            break;

          case self::$i18n['previous'] :
            $get_id = $post_input['action'] == 'update' ? '&id=' . self::next_id( $participant_id, false ) : '';
            $redirect = get_admin_url() . 'admin.php?page=' . self::PLUGIN_NAME . '-edit_participant' . $get_id;
            break;

          case self::$i18n['submit'] :
          default :
            $redirect = get_admin_url() . 'admin.php?page=' . self::PLUGIN_NAME;
        }
        wp_redirect( $redirect );
        exit;

      case 'output CSV':

        if ( ! self::csv_export_allowed() ) {
          die();
        }
        
        $header_row = array();
        $title_row = array();
        $data = array();
        $filename = !empty( $post_input['filename'] ) ? $post_input['filename'] : '';

        switch ( $post_input['CSV_type'] ) :

          // create a blank data array
          case 'blank':

            // add the header row
            foreach ( self::get_column_atts( 'CSV' ) as $column )
              $header_row[] = $column->name;
            $data[] = $header_row;

            $i = 2; // number of blank rows to create

            while ( $i > 0 ) {
              $data[] = array_fill_keys( $header_row, '' );
              $i--;
            }
            break;

          case 'participant list':

            global $wpdb;

            // gets the export field list from the session value or the default set for CSV exports
            $csv_columns = self::get_column_atts( self::$session->getArray( 'csv_export_fields', 'CSV' ) );
            self::$session->clear('csv_export_fields');
            $export_columns = array();

            foreach ( $csv_columns as $column ) {
              $field = self::$fields[$column->name];
              /* @var $field PDb_Form_Field_Def */
              $export_columns[] = sprintf( 'p.%s', $column->name );
              $header_row[] = $column->name;
              $title_row[] = $field->title();
            }
            
            $export_columns = implode( ', ', $export_columns );

            $data['header'] = $header_row;

            if ( $post_input['include_csv_titles'] )
              $data['titles'] = $title_row;

            $query = false;
            
            if ( is_admin() ) {
              global $current_user;
              $query = Participants_Db::$session->get( Participants_Db::$prefix . 'admin_list_query-' . $current_user->ID, PDb_List_Admin::default_query() );
              $query = str_replace( '*', ' ' . $export_columns . ' ', $query );
            } else {
              $query = self::$session->get('csv_export_query');
              if ( $query ) {
                $query = preg_replace( '#SELECT.+FROM#', 'SELECT ' . $export_columns . ' FROM', $query );
              }
              self::$session->clear('csv_export_query');
            }

            if ( $query ) {
              $data += self::_prepare_CSV_rows( $wpdb->get_results( $query, ARRAY_A ) );
            }

            break;

        endswitch; // CSV type

        if ( !empty( $filename ) ) {

          $base_filename = substr( $filename, 0, strpos( $filename, PDb_List_Admin::filename_datestamp() . '.csv' ) );

          /*
           * @version 1.6
           * base filename is now saved as a preference
           */
          global $user_ID;
          PDb_List_Admin::$user_settings = Participants_Db::$prefix . PDb_List_Admin::$user_settings . '-' . $user_ID;
          PDb_List_Admin::set_admin_user_setting( 'csv_base_filename', $base_filename );

          // create a file pointer connected to the output stream
          $output = fopen( 'php://output', 'w' );

          //header('Content-type: application/csv'); // some sources say it should be this
          header( 'Content-Type: text/csv; charset=utf-8' );
          header( "Cache-Control: no-store, no-cache" );
          header( 'Content-Disposition: attachment; filename="' . $filename . '"' );

          // output the data lines
          foreach ( $data as $line ) {
            fputcsv( $output, $line, ',', self::$CSV_enclosure );
          }

          fclose( $output );

          // we must terminate the script to prevent additional output being added to the CSV file
          exit;
        }

        return $data;

      case 'retrieve' :

        if ( self::nonce_check( filter_input( INPUT_POST, 'session_hash', FILTER_SANITIZE_STRING ), self::$main_submission_nonce_key ) ) {
          self::_process_retrieval();
        }
        return;

      case 'signup' :

        if ( !self::nonce_check( filter_input( INPUT_POST, 'session_hash', FILTER_SANITIZE_STRING ), self::$main_submission_nonce_key ) )
          return;

        $_POST['private_id'] = '';
        $columns[] = 'private_id';

        /*
         * route the $_POST data through a callback if defined
         * 
         * filter: pdb-before_submit_signup
         */
        $post_data = self::apply_filters( 'before_submit_signup', $_POST );



        /*
         * the signup form should update the current record if it is revisited during a multipage form session
         */
        $submit_action = 'insert';
        if ( self::$session->record_id() !== false ) {
          $submit_action = 'update';
        }

        // submit the data
        $post_data['id'] = self::process_form( $post_data, $submit_action, self::$session->record_id(), $columns );

        if ( false !== $post_data['id'] ) {

          /*
           * hook: pdb-after_submit_signup
           */
          $wp_hook = self::$prefix . 'after_submit_signup';
          do_action( $wp_hook, self::get_participant( $post_data['id'] ) );

          $redirect = apply_filters( 'pdb-record_id_in_get_var', false ) 
                  ? add_query_arg( Participants_Db::$single_query, $post_data['id'], $post_data['thanks_page'] ) 
                  : $post_data['thanks_page'];

          self::$session->set( 'pdbid', $post_data['id'] );
          self::$session->set( 'previous_multipage', $post_data['shortcode_page'] );

          wp_redirect( $redirect );

          exit;
        } else {
          
          /**
           * @action pdb-signup_submission_not_verified
           * @param array submission data
           * @param int|bool record id or bool false
           * @param PDb_Form_Validation object
           */
          do_action('pdb-signup_submission_not_verified', $post_data, $post_data['id'], self::$validation_errors );
        }

        return;

    endswitch; // $_POST['action']
  }

  /**
   * checks the nonce on a form submission
   * 
   * @uses filter pdb-nonce_verify
   * 
   * @param string $nonce the nonce value
   * @param string $context the context string
   * 
   * @return bool true if nonce passes
   */
  public static function nonce_check( $nonce, $context )
  {
    return self::apply_filters( 'nonce_verify', wp_verify_nonce( $nonce, self::nonce_key( $context ) ), $context );
  }

  /**
   * provides a key string for a nonce
   * 
   * @uses filter pdb-nonce_key
   * 
   * @param string $context a context string for the key string
   * 
   * @return string
   */
  public static function nonce_key( $context )
  {
    return self::apply_filters( 'nonce_key', self::$prefix . $context );
  }

  /**
   * provides a nonce string
   * 
   * @uses filter pdb-nonce
   * 
   * @param string $context an optional context string for the filter
   * 
   * @return string
   */
  public static function nonce( $context )
  {
    return self::apply_filters( 'nonce', wp_create_nonce( self::nonce_key( $context ) ) );
  }

  /**
   * tests a private link retrieval submission and send the link or sets an error
   * 
   * @return null
   */
  private static function _process_retrieval()
  {

    /*
     * we check a transient based on the user's IP; if the user tries more than 3 
     * times per day to get a private ID, they are blocked for 24 hours
     */
    $max_tries = Participants_Db::current_user_has_plugin_role( 'admin', 'retrieve link' ) ? 10000 : 3; // give the plugin admin unlimited tries
    $transient = self::$prefix . 'retrieve-count-' . str_replace( '.', '', self::user_ip() );
    $count = get_transient( $transient ) ? : 0; // set the count to 0 if no transient is set
    if ( $count > $max_tries ) {

// too many tries, come back tomorrow
      error_log( 'Participants Database Plugin: IP blocked for too many retrieval attempts from IP ' . self::user_ip() . ' in 24-hour period.' );
      return;
    }
    $count++;
    set_transient( $transient, $count, DAY_IN_SECONDS );

    $column = self::plugin_setting( 'retrieve_link_identifier', 'email' );

    if ( !is_object( self::$validation_errors ) ) {
      self::$validation_errors = new PDb_FormValidation();
    }

    if ( !isset( $_POST[$column] ) || empty( $_POST[$column] ) ) {
      self::$validation_errors->add_error( $column, 'empty' );
      return;
    }
    
    // a value was submitted, try to find a record with it
    $match_id = self::find_record_match( $column, $_POST );

    if ( is_numeric( $match_id ) ) {
      $participant_values = self::get_participant( $match_id );
    } else {
      self::$validation_errors->add_error( $column, 'identifier' );
      return;
    }
    // prepare an object for the filter to use
    $retrieve_link_email = new stdClass();
    $retrieve_link_email->body_template = self::plugin_setting( 'retrieve_link_email_body' );
    $retrieve_link_email->subject = self::plugin_setting( 'retrieve_link_email_subject' );
    $retrieve_link_email->recipient = $participant_values[self::plugin_setting( 'primary_email_address_field', 'email' )];
    /**
     * @version 1.6
     * 
     * @action pdb-before_send_retrieve_link_email
     * @param object  $retrieve_link_email
     * @param array   $participants_values
     */
    self::apply_filters( 'before_send_retrieve_link_email', $retrieve_link_email, $participant_values );
    if ( !empty( $retrieve_link_email->recipient ) ) {
      PDb_Template_Email::send( array(
          'to' => $retrieve_link_email->recipient,
          'subject' => $retrieve_link_email->subject,
          'template' => $retrieve_link_email->body_template,
          'context' => 'retrieve link email',
              ), $match_id
      );
    } else {
      error_log( __METHOD__ . ' primary email address field undefined' );
    }

    if ( self::plugin_setting_is_true( 'send_retrieve_link_notify_email' ) ) {

      PDb_Template_Email::send( array(
          'to' => self::plugin_setting( 'email_signup_notify_addresses' ),
          'subject' => self::plugin_setting( 'retrieve_link_notify_subject' ),
          'template' => self::plugin_setting( 'retrieve_link_notify_body' ),
          'context' => 'retrieve link notification',
              ), $match_id
      );
    }

//self::$validation_errors->add_error('', 'success');
    $_POST['action'] = 'success';
    return;
  }

  /**
   * processes a rich text string
   * 
   * runs it through the WP the_content filter if selected in the settings
   * 
   * @param string $input
   * @param string  $context  a context-identifying string
   * @return string
   */
  public static function process_rich_text( $string, $context = '' )
  {
    /**
     * @version 1.6.3
     * @filter  pdb-rich_text_auto_formatting
     * @param string  $string   the raw rich text
     * @param string  $context  a context identifier for the filter
     */
    $filtered_string = self::apply_filters( 'rich_text_auto_formatting', $string, $context );
    /**
     * @filter pdb-rich_text_filter_mode
     * @param string current filter mode setting
     * @param string the content to be processed
     * @return string filter mode to use
     */
    switch ( self::apply_filters( 'rich_text_filter_mode', Participants_Db::$plugin_options['enable_wpautop'], $string ) ) {
      case '1':
      case 'the_content':
        return self::rich_text_filter( $string );
      case 'wpautop':
        return wpautop($string);
      case 'wpautop+shortcodes':
        return do_shortcode( wpautop($string) );
      case '0':
      case 'none':
      default:
        return $string;
    }
  }

  /**
   * applies the rict text filter
   * 
   * this can be overridden; the filter is set in self::init()
   * 
   * @param string  $string
   * @return string
   */
  public static function rich_text_filter( $string )
  {
    /**
     * @filter pdb-rich_text_filter
     * @param string name of the filter to apply to rich text
     * @since 1.7.1.4
     */
    return apply_filters( self::apply_filters( 'rich_text_filter', 'the_content' ), $string );
  }

  /**
   * gets an array of readonly fields
   *
   * @return array
   */
  public static function get_readonly_fields()
  {

    $fields = array();

    foreach ( self::get_column_atts( 'readonly' ) as $column )
      $fields[] = $column->name;

    return $fields;
  }

  /**
   * returns the title attribute of a column
   * 
   * @param string $column field name
   * @return string
   */
  public static function column_title( $column )
  {

    $field = self::get_field_atts( $column, 'title' );

    return $field->title;
  }

  /**
   * prepares a set of rows for CSV output
   *
   * @param array $raw_array the raw array output from the query
   *
   * @return array of record arrays
   */
  private static function _prepare_CSV_rows( $raw_array )
  {

    $output = array();

    foreach ( $raw_array as $value ) {
      $output[] = self::_prepare_CSV_row( $value );
    }

    return $output;
  }

  /**
   * prepares a row of data for CSV output
   *
   * @param array $raw_array the raw array output from the query
   *
   * @return array with all the serialized arrays in human-readable form
   */
  private static function _prepare_CSV_row( $raw_array )
  {

    $output = array();

    // get the column attributes
    $columns = self::get_column_atts( 'CSV' );

    // iterate through the object as we iterate through the array
    $column = current( $columns );

    foreach ( $raw_array as $key => $value ) {

      $field_def = self::$fields[$key];
      /* @var $field_def PDb_Form_Field_Def */
      /**
       * filters the raw value of the field
       * 
       * subsequent processing can be skipped by setting the $column->form_element property to 'skip'
       * 
       * @version 1.7.1
       * @filter pdb-csv_export_value_raw
       * @param mixed the raw value
       * @param object the field object
       * @return mixed
       */
      $value = self::apply_filters( 'csv_export_value_raw', $value, $field_def );

      // process any other value types
      switch ( $field_def->form_element() ) {

        case 'date':

          if ( !empty( $value ) && is_numeric( $value ) ) {

            $value = PDb_Date_Display::get_date( $value, __METHOD__ );
          }
          break;

        case 'link':

          // flatten the array
          if ( is_serialized( $value ) ) {

            $link = unserialize( $value );
            if ( empty( $link[0] ) )
              $value = '';
            else {
              $pattern = empty( $link[1] ) ? '<%1$s>' : '[%2$s](%1$s)';
              $value = vsprintf( $pattern, $link );
            }
          }
          break;

        case 'rich-text':

          /*
           * what we need to do here is add the missing markup (wpautop does 
           * this) and then remove all line breaks and such so the whole thing 
           * looks like one field
           */
          $value = preg_replace( '/^\s+|\n|\r|\s+$/m', '', wpautop( $value, true ) );
          break;
        
        case 'skip':
          // do nothing
          break;

        default:
          
          $value = maybe_unserialize( $value );

          /*
           * as of version 1.7.9 multi-type fields export their values as a 
           * comma-separated list of values; values that contain a comma will use 
           * the &#44; entity to represent them
           */
          if ( $field_def->is_multi() ) {
            $value = implode( Participants_Db::apply_filters( 'stringify_array_glue', ', ' ), (array) $value );
          } elseif ( is_array( $value ) ) {
             // if it is an array, serialize it
            $value = html_entity_decode( serialize( $value ), ENT_QUOTES, "UTF-8" );
          } else {
            $value = html_entity_decode( $value, ENT_QUOTES, "UTF-8" );
          }
          
      }

      /*
       * decode HTML entities and convert line breaks to <br>, then pass to a filter 
       * for processing before being added to the output array
       */
      $output_value = Participants_Db::apply_filters( 'csv_export_value', str_replace( array("\n", "\r"), '<br />', stripslashes( $value ) ), $column );
      $output[$key] = $output_value;

      $column = next( $columns );
    }

    return $output;
  }

  /**
   * creates an anchor element with clickable link and href
   *
   * this is simply an interface to the xnau_FormElement function of the same name
   * 
   * @static
   * @param string $link the URI
   * @param string $linktext the clickable text (optional)
   * @param string $template the format of the link (optional)
   * @param array  $get an array of name=>value pairs to include in the get string
   *
   * @return string HTML or HTML-escaped string (if it's not a link)
   */
  public static function make_link( $link, $linktext = '', $template = false, $get = false )
  {

    $field = new stdClass();

    $field->link = $link;
    $field->value = $linktext === '' ? $link : $linktext;

    return PDb_FormElement::make_link( $field, $template, $get );
  }

  /**
   * provides an AJAX loading spinner element
   */
  public static function get_loading_spinner()
  {
    /**
     * @version 1.6.3
     * @filter pdb-loading_spinner_html
     */
    return self::apply_filters( 'loading_spinner_html', '<span class="ajax-loading"><img src="' . plugins_url( 'ui/ajax-loader.gif', __FILE__ ) . '" /></span>' );
  }

  /**
   * attempt to create the uploads directory
   *
   * sets an error if it fails
   * 
   * @param string $dir the name of the new directory
   */
  public static function _make_uploads_dir( $dir = '' )
  {

    $dir = empty( $dir ) ? Participants_Db::files_location() : $dir;
    $savedmask = umask( 0 );
    $status = true;
    if ( mkdir( Participants_Db::base_files_path() . $dir, 0755, true ) === false ) {

      if ( is_object( self::$validation_errors ) )
        self::$validation_errors->add_error( '', sprintf( __( 'The uploads directory (%s) could not be created.', 'participants-database' ), $dir ) );

      $status = false;
    }
    umask( $savedmask );
    return $status;
  }

  /**
   * builds a record edit link
   *
   * @param string $PID private id value
   * @return string private record URI
   */
  public static function get_record_link( $PID, $target_page = '' )
  {

    $target_page = $target_page === '' ? self::$plugin_options['registration_page'] : $target_page;

    if ( false === $registration_page = self::find_permalink( $target_page ) ) {
      //error_log( 'Participants Database: "Participant Record Page" setting is invalid.' );
      return '';
    }

    /**
     * @version 1.6.3
     * @filter pdb-record_edit_page
     */
    /**
     * @since 1.7.1.1
     * @filter pdb-record_edit_url
     * @param string the full URL to the record edit page with the query var
     * @param string the private ID value
     */
    return self::apply_filters( 'record_edit_url', self::add_uri_conjunction( self::apply_filters( 'record_edit_page', $registration_page ) ) . Participants_Db::$record_query . '=' . $PID, $PID );
  }

  /**
   * builds an admin record edit link
   * 
   * this is meant to be included in the admin notification for a new signup, 
   * giving them the ability to click the link and edit the new record
   * 
   * @param int $id the id of the new record
   * @return string the HREF for the record edit link
   */
  public static function get_admin_record_link( $id )
  {

    $path = 'admin.php?page=participants-database-edit_participant&action=edit&id=' . $id;

    return get_admin_url( NULL, $path );
  }

  /**
   * prints the list with filtering parameters applied 
   *
   * called by the wp_ajax_nopriv_pdb_list_filter action: this happens when a 
   * user submits a search or sort on a record list
   *
   * @return null
   */
  public static function pdb_list_filter()
  { 
    $multi = isset( $_POST['search_field'] ) && is_array( $_POST['search_field'] );
    $postinput = filter_input_array( INPUT_POST, self::search_post_filter( $multi ) );

    self::$instance_index = empty( $postinput['target_instance'] ) ? $postinput['instance_index'] : $postinput['target_instance'];

    /**
     * @version 1.6.3
     * we don't check nonces for list search/sort/pagination requests as these are 
     * generally made by not-logged-in users so there is no point in checking a 
     * nonce for them, it also can break AJAX functionality if page caching is in 
     * use
     */
//    if ( !self::nonce_check( $postinput['filterNonce'], PDb_List::$list_filter_nonce_key ) )
//      die( 'failed nonce check' );

    global $post;

    if ( !is_object( $post ) )
      $post = get_post( $postinput['postID'] );

    self::print_list_search_result( $post, self::$instance_index );

    do_action( Participants_Db::$prefix . 'list_ajax_complete', $post );

    exit;
  }

  /**
   * provides the list output from an AJAX search
   * 
   * @param object $post the current post object
   * @param int $instance the instance index of the targeted list
   * @return null
   */
  private static function print_list_search_result( $post, $instance )
  {
    
//    error_log(__METHOD__.' sess id: '.Participants_Db::$session->get_id().' 
//     
//session: '.print_r(Participants_Db::$session,1));
    
    /*
     * get the attributes array; these values were saved in the session array by 
     * the Shortcode class when it was instantiated
     */
    $session = self::$session->getArray( 'shortcode_atts' );
    
    $shortcode_atts = isset( $session[$post->ID]['list'][$instance] ) ? $session[$post->ID]['list'][$instance] : false;

    if ( ! is_array( $shortcode_atts ) ) {
      printf( 'failed to get session for list instance %s', $instance );
      return;
    }

    // add the AJAX filtering flag
    $shortcode_atts['filtering'] = 1;
    $shortcode_atts['module'] = 'list';

    // output the filtered shortcode content
    header( "Content-Type:	text/html" );
    echo PDb_List::get_list( $shortcode_atts );
    return;
  }

  /**
   * clears the list search
   * 
   * @return null
   */
  private static function clear_list_search()
  {
    self::$session->clear( 'shortcode_atts' );
  }

  /**
   * supplied for backwards compatibility
   * 
   * the original func has been superceded, but this will allow the old func to be used
   * 
   * @var string $value
   * @var string $form_element
   * @return string
   */
  public static function prep_field_for_display( $value, $form_element )
  {
    $field = (object) array(
                'value' => $value,
                'form_element' => $form_element,
                'module' => 'single', // probably not correct, but this is the generic option
    );
    return PDb_FormElement::get_field_value_display( $field );
  }
  
  /**
   * adds a validation arror message
   * 
   * @param string  $message
   * @param string  $fieldname name of the field involved (if any)
   * @return null
   */
  public static function validation_error( $message, $fieldname = '' )
  {
    self::_show_validation_error($message, $fieldname);
  }

  /**
   * shows a validation error message
   * 
   * @param string $error the message to show
   * @param string $name the field on which the error was called
   */
  private static function _show_validation_error( $error, $name = '', $overwrite = true )
  {
    if ( is_object( self::$validation_errors ) )
      self::$validation_errors->add_error( $name, $error, $overwrite );
    else
      self::set_admin_message( $error );
  }

  /**
   * sets up a few internationalization words
   */
  private static function _set_i18n()
  {

    self::$i18n = array(
        'submit' => __( 'Submit', 'participants-database' ),
        'apply' => __( 'Apply', 'participants-database' ),
        'next' => __( 'Next', 'participants-database' ),
        'previous' => __( 'Previous', 'participants-database' ),
        'updated' => __( 'The record has been updated.', 'participants-database' ),
        'added' => __( 'The new record has been added.', 'participants-database' ),
        'zero_rows_error' => __( 'No record was added on query: %s', 'participants-database' ),
        'database_error' => __( 'Database Error: %2$s on query: %1$s', 'participants-database' )
    );
  }

  /**
   * sets some custom body classes in the admin
   * 
   * @param array $classes
   */
  public static function add_admin_body_class( $class )
  {
    if ( self::has_dashicons() ) {
      $class .= ' has-dashicons ';
    }
    return $class;
  }

  /**
   * sets some custom body classes
   * 
   * @param array $classes
   */
  public static function add_body_class( $classes )
  {
    if ( self::has_dashicons() ) {
      $classes[] = 'has-dashicons';
    }
    global $post;
    $shortcodes = is_object( $post ) ? self::get_plugin_shortcodes( $post->post_content ) : '';
    if ( !empty( $shortcodes ) ) {
      $classes[] = 'participants-database-shortcode';
      foreach ( $shortcodes as $shortcode ) {
        $classes[] = $shortcode;
      }
    }
    return $classes;
  }

  /**
   * checks the WP version for the availability of dashicon fonts
   * 
   * @return bool true if the font is available
   */
  public static function has_dashicons()
  {
    return version_compare( get_bloginfo( 'version' ), '3.8', '>=' );
  }

  /**
   * prints an admin page heading
   *
   * @param text $text text to show if not the title of the plugin
   */
  public static function admin_page_heading( $text = false )
  {

    $text = $text ? $text : self::$plugin_title;
    ?>
    <div class="icon32" id="icon-users"></div><h2><?php echo $text ?></h2>
    <?php
    self::admin_message();
  }

  /**
   * sets up the plugin admin menus
   * 
   * @return null
   */
  public static function plugin_menu()
  {
    global $pagenow;
    if ( ($pagenow == 'admin.php' && filter_input( INPUT_GET, 'page' ) === 'participants-database_settings_page') || $pagenow == 'options.php' ) {
      /*
       * intialize the plugin settings for the plugin settings pages
       */
      self::$Settings->initialize();
    }

    /*
     * this allows the possibility of a child class handling the admin list display
     */
    $list_admin_classname = self::apply_filters( 'admin_list_classname', 'PDb_List_Admin' );

    // define the plugin admin menu pages
    add_menu_page(
            self::$plugin_title, self::$plugin_title, self::plugin_capability( 'record_edit_capability', 'main admin menu' ), self::PLUGIN_NAME, null
    );

    add_submenu_page(
            self::PLUGIN_NAME, self::plugin_label( 'list_participants' ), self::plugin_label( 'list_participants' ), self::plugin_capability( 'record_edit_capability', 'list participants' ), self::PLUGIN_NAME, //self::$plugin_page . '-list_participants', 
            array($list_admin_classname, 'initialize')
    );
    /**
     * this registers the edit participant page without adding it as a menu item
     */
    add_submenu_page(
            null, null, null, self::plugin_capability( 'record_edit_capability', 'edit participant' ), self::$plugin_page . '-edit_participant', array(__CLASS__, 'include_admin_file')
    );

    add_submenu_page(
            self::PLUGIN_NAME, self::plugin_label( 'add_participant' ), self::plugin_label( 'add_participant' ), self::plugin_capability( 'record_edit_capability', 'add participant' ), self::$plugin_page . '-add_participant', array(__CLASS__, 'include_admin_file')
    );

    add_submenu_page(
            self::PLUGIN_NAME, self::plugin_label( 'manage_fields' ), self::plugin_label( 'manage_fields' ), self::plugin_capability( 'plugin_admin_capability', 'manage fields' ), self::$plugin_page . '-manage_fields', array(__CLASS__, 'include_admin_file')
    );

    add_submenu_page(
            self::PLUGIN_NAME, self::plugin_label( 'manage_list_columns' ), self::plugin_label( 'manage_list_columns' ), self::plugin_capability( 'plugin_admin_capability', 'manage fields' ), self::$plugin_page . '-manage_list_columns', array(__CLASS__, 'include_admin_file')
    );

    add_submenu_page(
            self::PLUGIN_NAME, self::plugin_label( 'upload_csv' ), self::plugin_label( 'upload_csv' ), self::plugin_capability( 'plugin_admin_capability', 'upload csv' ), self::$plugin_page . '-upload_csv', array(__CLASS__, 'include_admin_file')
    );

    add_submenu_page(
            self::PLUGIN_NAME, self::plugin_label( 'plugin_settings' ), self::plugin_label( 'plugin_settings' ), self::plugin_capability( 'plugin_admin_capability', 'plugin settings' ), self::$plugin_page . '_settings_page', array(self::$Settings, 'show_settings_form')
    );

    add_submenu_page(
            self::PLUGIN_NAME, self::plugin_label( 'setup_guide' ), self::plugin_label( 'setup_guide' ), self::plugin_capability( 'plugin_admin_capability', 'setup guide' ), self::$plugin_page . '-setup_guide', array(__CLASS__, 'include_admin_file')
    );
  }

  /**
   * prints a credit footer for the plugin
   *
   * @return null
   */
  public static function plugin_footer()
  {
    $greeting = PDb_Live_Notification_Handler::greeting();
    /**
     * @version 1.6.3
     * @filter pdb-show_live_notifications
     * 
     */
    ?>
    <?php if ( $greeting && self::apply_filters( 'show_live_notifications', true ) ) : ?>
      <div id="PDb_greeting" class="pdb-footer padded widefat postbox pdb-live-notification">
      <?php echo wpautop( $greeting ); ?>
      </div>
      <?php endif; ?>
    <?php if ( self::apply_filters( 'show_plugin_colophon', true ) ) : ?>
    <div id="PDb_footer" class="pdb-footer widefat redfade postbox">
      <div class="section">
        <h4><?php echo 'Participants Database ', self::$plugin_version ?><br /><?php _e( 'WordPress Plugin', 'participants-database' ) ?></h4>
        <p><em><?php _e( 'Helping organizations manage their volunteers, members and participants.', 'participants-database' ) ?></em></p>
      </div>
      <div class="section">
        <h4><a class="glyph-link" href="http://xnau.com"><span class="icon-xnau-glyph"></span></a><?php _e( 'Developed by', 'participants-database' ) ?><br /><a href="http://xnau.com">xn<span class="lowast">&lowast;</span>au webdesign</a></h4>
        <p><?php _e( 'Suggestions or criticisms of this plugin? I&#39;d love to hear them: email ', 'participants-database' ) ?><a href="mailto:support@xnau.com">support@xnau.com.</a>
      </div>
      <div class="section">
        <p><?php printf( __( 'Please consider contributing to the continued support and development of this software by visiting %1$sthis plugin&#39;s page,%3$s giving the plugin a %2$srating%3$s or review, or dropping something in the %1$stip jar.%3$s Thanks!', 'participants-database' ), '<a href="http://xnau.com/wordpress-plugins/participants-database#donation-link">', '<a href="http://wordpress.org/extend/plugins/participants-database/">', '</a>' ) ?></p>
      </div>
    </div>
    <?php
    endif;
  }
  
  /**
   * provides translated strings for plugin labels
   * 
   * @param string  $key  the key string for the label to get
   * @return  string the translated string or the key string if not found
   */
  public static function plugin_label( $key )
  {
    $labels = self::apply_filters('plugin_labels', array(
        'add_participant' => __( 'Add Participant', 'participants-database' ),
        'list_participants' => __( 'List Participants', 'participants-database' ),
        'manage_fields' => __( 'Manage Database Fields', 'participants-database' ),
        'upload_csv' => __( 'Import CSV File', 'participants-database' ),
        'plugin_settings' => '<span class="dashicons dashicons-admin-generic"></span>' . __( 'Settings', 'participants-database' ),
        'setup_guide' => __( 'Setup Guide', 'participants-database' ),
        'add_record_title' => __( 'Add New Participant Record', 'participants-database' ),
        'edit_record_title' => __( 'Edit Existing Participant Record', 'participants-database' ),
        'list_participants_title' => __( 'List Participants', 'participants-database' ),
        'export_csv_title' => __( 'Export CSV', 'participants-database' ),
        'manage_list_columns' => __( 'Manage List Columns', 'participants-database' ),
    ) );
    return isset( $labels[$key] ) ? $labels[$key] : $key;
  }

  /**
   * parses the text header to extract plugin info
   * 
   * @param string $key the name of the field to get
   */
  private static function _get_plugin_data( $key = 'Name' )
  {

    if ( !defined( 'ABSPATH' ) )
      return '';

    if ( ! function_exists( 'get_plugin_data' ) ) {
      include_once ABSPATH . '/wp-admin/includes/plugin.php';
    }

    $plugin_data = get_plugin_data( __FILE__ );

    return $plugin_data[$key];
  }
  
  /**
   * fiters the plugin data
   * 
   * @param array $plugins
   * @return array
   */
  public static function filter_plugin_data( $plugins )
  {
    $key = plugin_basename( __FILE__ );
    $plugin_data = $plugins[$key];
    $filtered_data = array(
        'Name' => self::apply_filters( 'plugin_title', $plugin_data['Name'] ),
        'Title' => self::apply_filters( 'plugin_title', $plugin_data['Title'] ),
        'Description' => self::apply_filters( 'plugin_description', $plugin_data['Description'] ),
        'Author' => self::apply_filters( 'plugin_author', $plugin_data['Author'] ),
        'AuthorName' => self::apply_filters( 'plugin_author', $plugin_data['AuthorName'] ),
        'AuthorURI' => self::apply_filters( 'plugin_author_uri', $plugin_data['AuthorURI'] ),
    );
    $plugins[$key] = array_merge( $plugin_data, $filtered_data );
    return $plugins;
  }

  /**
   * filters the plugins action links shown on the plugins page to add a link to 
   * the settings page
   * 
   * @param array $links
   * @return array
   */
  public static function add_plugin_action_links( $links )
  {
    return array_merge( $links, array('settings' => '<a href="' . admin_url( 'admin.php?page=participants-database_settings_page' ) . '">' . __( 'Settings', 'participants-database' ) . '</a>') );
  }

  /**
   * adds links and modifications to plugin list meta row
   * 
   * @param array  $links
   * @param string $file
   * @return array
   */
  public static function add_plugin_meta_links( $links, $file )
  {

    // create link
    if ( $file == plugin_basename( __FILE__ ) ) {

      //error_log( ' meta links: '.print_r( $links,1 ));

      $links[1] = str_replace( self::_get_plugin_data( 'Author' ), 'xn*au webdesign', $links[1] );
//      $links[] = '<a href="http://wordpress.org/support/view/plugin-reviews/participants-database">' . __( 'Submit a rating or review', 'participants-database' ) . ' </a>';
//      $links[] = '<span style="color:#6B4001;">' . __( 'Free tech support and continued development relies on your support:', 'participants-database' ) . ' <a class="button xnau-contribute" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6C7FSX2DQFWY4">' . __( 'contribute', 'participants-database' ) . '</a></span>';
    }
    return $links;
  }

}

// class

/**
 * performs the class autoload
 * 
 * @param string $class the name of the class to be loaded
 */
function PDb_class_loader( $class )
{

  if ( !class_exists( $class ) ) {

    /**
     * allows class overrides
     * 
     * @filter pdb-autoloader_class_path
     * @param string the absolute path to the class file
     * @return string
     */
    $class_file = apply_filters( 'pdb-autoloader_class_path', plugin_dir_path( __FILE__ ) . 'classes/' . $class . '.class.php' );
        
    if ( is_file( $class_file ) ) {

      require_once $class_file;
    }
  }
}
    
/**
 * PHP version checks and notices before initializing the plugin
 */
if ( version_compare( PHP_VERSION, Participants_Db::min_php_version, '>=' ) ) {
  
  if ( is_null( Participants_Db::$plugin_version ) ) { // check for the uninitialized class
    Participants_Db::initialize();
  }
  
} else {

  add_action( 'admin_notices', 'pdb_handle_php_version_error' );

  add_action( 'admin_init', 'pdb_deactivate_plugin' );
}

function pdb_deactivate_plugin()
{
  deactivate_plugins( plugin_basename( __FILE__ ) );
}

function pdb_handle_php_version_error()
{
  echo '<div class="notice notice-error is-dismissible"><p><span class="dashicons dashicons-warning"></span>' . sprintf( __( 'Participants Database requires PHP version %s to function properly, you have PHP version %s. Please upgrade PHP. The Plugin has been auto-deactivated.', 'participants-database' ), Participants_Db::min_php_version, PHP_VERSION ) . '</p></div>';
  if ( isset( $_GET['activate'] ) ) {
    unset( $_GET['activate'] );
  }
}