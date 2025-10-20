<?php
/**
 * Plugin Name: Life Freedom System
 * Plugin URI: https://yoursite.com
 * Description: Ett holistiskt poäng- och belöningssystem för att uppnå frihet och autonomi
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yoursite.com
 * License: GPL v2 or later
 * Text Domain: life-freedom-system
 * Domain Path: /languages
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('LFS_VERSION', '1.0.0');
define('LFS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('LFS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('LFS_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main Plugin Class
 */
class Life_Freedom_System {
    
    /**
     * Instance of this class
     */
    private static $instance = null;
    
    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
        $this->load_dependencies();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('init', array($this, 'load_textdomain'));
        add_action('init', array($this, 'register_post_types'));
        add_action('init', array($this, 'register_taxonomies'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        
        // Activation and deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    /**
     * Load dependencies
     */
    private function load_dependencies() {
        require_once LFS_PLUGIN_DIR . 'includes/class-lfs-meta-boxes.php';
        require_once LFS_PLUGIN_DIR . 'includes/class-lfs-calculations.php';
        require_once LFS_PLUGIN_DIR . 'includes/class-lfs-dashboard.php';
        require_once LFS_PLUGIN_DIR . 'includes/class-lfs-rewards.php';
        require_once LFS_PLUGIN_DIR . 'includes/class-lfs-financial.php';
        require_once LFS_PLUGIN_DIR . 'includes/class-lfs-activity-templates.php';
        
        // Initialize classes
        LFS_Meta_Boxes::get_instance();
        LFS_Calculations::get_instance();
        LFS_Dashboard::get_instance();
        LFS_Rewards::get_instance();
        LFS_Financial::get_instance();
        LFS_Activity_Templates::get_instance();
    }
    
    /**
     * Load plugin textdomain
     */
    public function load_textdomain() {
        load_plugin_textdomain('life-freedom-system', false, dirname(LFS_PLUGIN_BASENAME) . '/languages');
    }
    
    /**
     * Register Custom Post Types
     */
    public function register_post_types() {
        
        // Life Activity CPT
        register_post_type('life_activity', array(
            'labels' => array(
                'name' => __('Aktiviteter', 'life-freedom-system'),
                'singular_name' => __('Aktivitet', 'life-freedom-system'),
                'add_new' => __('Lägg till aktivitet', 'life-freedom-system'),
                'add_new_item' => __('Lägg till ny aktivitet', 'life-freedom-system'),
                'edit_item' => __('Redigera aktivitet', 'life-freedom-system'),
                'new_item' => __('Ny aktivitet', 'life-freedom-system'),
                'view_item' => __('Visa aktivitet', 'life-freedom-system'),
                'search_items' => __('Sök aktiviteter', 'life-freedom-system'),
                'not_found' => __('Inga aktiviteter hittades', 'life-freedom-system'),
                'not_found_in_trash' => __('Inga aktiviteter i papperskorgen', 'life-freedom-system'),
                'menu_name' => __('Aktiviteter', 'life-freedom-system'),
            ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'menu_icon' => 'dashicons-yes-alt',
            'supports' => array('title', 'editor'),
            'has_archive' => false,
            'rewrite' => false,
            'capability_type' => 'post',
            'show_in_rest' => true,
        ));
        
        // Project CPT
        register_post_type('lfs_project', array(
            'labels' => array(
                'name' => __('Projekt', 'life-freedom-system'),
                'singular_name' => __('Projekt', 'life-freedom-system'),
                'add_new' => __('Lägg till projekt', 'life-freedom-system'),
                'add_new_item' => __('Lägg till nytt projekt', 'life-freedom-system'),
                'edit_item' => __('Redigera projekt', 'life-freedom-system'),
                'menu_name' => __('Projekt', 'life-freedom-system'),
            ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'menu_icon' => 'dashicons-portfolio',
            'supports' => array('title', 'editor'),
            'has_archive' => false,
            'rewrite' => false,
            'show_in_rest' => true,
        ));
        
        // Reward CPT
        register_post_type('lfs_reward', array(
            'labels' => array(
                'name' => __('Belöningar', 'life-freedom-system'),
                'singular_name' => __('Belöning', 'life-freedom-system'),
                'add_new' => __('Lägg till belöning', 'life-freedom-system'),
                'add_new_item' => __('Lägg till ny belöning', 'life-freedom-system'),
                'edit_item' => __('Redigera belöning', 'life-freedom-system'),
                'menu_name' => __('Belöningar', 'life-freedom-system'),
            ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_icon' => 'dashicons-awards',
            'supports' => array('title', 'editor', 'thumbnail'),
            'has_archive' => false,
            'rewrite' => false,
            'show_in_rest' => true,
        ));
        
        // Financial Transaction CPT
        register_post_type('lfs_transaction', array(
            'labels' => array(
                'name' => __('Transaktioner', 'life-freedom-system'),
                'singular_name' => __('Transaktion', 'life-freedom-system'),
                'add_new' => __('Lägg till transaktion', 'life-freedom-system'),
                'add_new_item' => __('Lägg till ny transaktion', 'life-freedom-system'),
                'edit_item' => __('Redigera transaktion', 'life-freedom-system'),
                'menu_name' => __('Transaktioner', 'life-freedom-system'),
            ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_icon' => 'dashicons-money-alt',
            'supports' => array('title'),
            'has_archive' => false,
            'rewrite' => false,
            'show_in_rest' => true,
        ));
        
        // Milestone CPT
        register_post_type('lfs_milestone', array(
            'labels' => array(
                'name' => __('Milstolpar', 'life-freedom-system'),
                'singular_name' => __('Milstolpe', 'life-freedom-system'),
                'add_new' => __('Lägg till milstolpe', 'life-freedom-system'),
                'add_new_item' => __('Lägg till ny milstolpe', 'life-freedom-system'),
                'edit_item' => __('Redigera milstolpe', 'life-freedom-system'),
                'menu_name' => __('Milstolpar', 'life-freedom-system'),
            ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'menu_icon' => 'dashicons-flag',
            'supports' => array('title', 'editor'),
            'has_archive' => false,
            'rewrite' => false,
            'show_in_rest' => true,
        ));
    }
    
    /**
     * Register Taxonomies
     */
    public function register_taxonomies() {
        
        // Activity Category
        register_taxonomy('activity_category', 'life_activity', array(
            'labels' => array(
                'name' => __('Kategorier', 'life-freedom-system'),
                'singular_name' => __('Kategori', 'life-freedom-system'),
                'add_new_item' => __('Lägg till kategori', 'life-freedom-system'),
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => false,
            'show_in_rest' => true,
        ));
        
        // Activity Type
        register_taxonomy('activity_type', 'life_activity', array(
            'labels' => array(
                'name' => __('Aktivitetstyper', 'life-freedom-system'),
                'singular_name' => __('Aktivitetstyp', 'life-freedom-system'),
            ),
            'hierarchical' => false,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_rest' => true,
        ));
        
        // Work Context
        register_taxonomy('work_context', 'life_activity', array(
            'labels' => array(
                'name' => __('Arbetskontext', 'life-freedom-system'),
                'singular_name' => __('Kontext', 'life-freedom-system'),
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_rest' => true,
        ));
        
        // Reward Level
        register_taxonomy('reward_level', 'lfs_reward', array(
            'labels' => array(
                'name' => __('Belöningsnivåer', 'life-freedom-system'),
                'singular_name' => __('Nivå', 'life-freedom-system'),
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_rest' => true,
        ));
        
        // Transaction Type
        register_taxonomy('transaction_type', 'lfs_transaction', array(
            'labels' => array(
                'name' => __('Transaktionstyper', 'life-freedom-system'),
                'singular_name' => __('Typ', 'life-freedom-system'),
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_rest' => true,
        ));
        
        // Account
        register_taxonomy('lfs_account', 'lfs_transaction', array(
            'labels' => array(
                'name' => __('Konton', 'life-freedom-system'),
                'singular_name' => __('Konto', 'life-freedom-system'),
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_rest' => true,
        ));
        
        // Life Phase
        register_taxonomy('life_phase', array('life_activity', 'lfs_project', 'lfs_milestone'), array(
            'labels' => array(
                'name' => __('Livsfaser', 'life-freedom-system'),
                'singular_name' => __('Livsfas', 'life-freedom-system'),
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_rest' => true,
        ));
        
        // Priority Level
        register_taxonomy('priority_level', array('life_activity', 'lfs_project'), array(
            'labels' => array(
                'name' => __('Prioritetsnivåer', 'life-freedom-system'),
                'singular_name' => __('Prioritet', 'life-freedom-system'),
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_rest' => true,
        ));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        // Main menu page
        add_menu_page(
            __('Life Freedom System', 'life-freedom-system'),
            __('Freedom System', 'life-freedom-system'),
            'manage_options',
            'life-freedom-system',
            array($this, 'render_dashboard_page'),
            'dashicons-chart-line',
            3
        );
        
        // Dashboard submenu (duplicate of main)
        add_submenu_page(
            'life-freedom-system',
            __('Dashboard', 'life-freedom-system'),
            __('Dashboard', 'life-freedom-system'),
            'manage_options',
            'life-freedom-system'
        );
        
        // Activities submenu
        add_submenu_page(
            'life-freedom-system',
            __('Aktiviteter', 'life-freedom-system'),
            __('Aktiviteter', 'life-freedom-system'),
            'manage_options',
            'edit.php?post_type=life_activity'
        );
        
        // Projects submenu
        add_submenu_page(
            'life-freedom-system',
            __('Projekt', 'life-freedom-system'),
            __('Projekt', 'life-freedom-system'),
            'manage_options',
            'edit.php?post_type=lfs_project'
        );
        
        // Rewards submenu
        add_submenu_page(
            'life-freedom-system',
            __('Belöningar', 'life-freedom-system'),
            __('Belöningar', 'life-freedom-system'),
            'manage_options',
            'lfs-rewards',
            array($this, 'render_rewards_page')
        );
        
        // Financial submenu
        add_submenu_page(
            'life-freedom-system',
            __('Ekonomi', 'life-freedom-system'),
            __('Ekonomi', 'life-freedom-system'),
            'manage_options',
            'lfs-financial',
            array($this, 'render_financial_page')
        );
        
        // Milestones submenu
        add_submenu_page(
            'life-freedom-system',
            __('Milstolpar', 'life-freedom-system'),
            __('Milstolpar', 'life-freedom-system'),
            'manage_options',
            'edit.php?post_type=lfs_milestone'
        );
        
        // Settings submenu
        add_submenu_page(
            'life-freedom-system',
            __('Inställningar', 'life-freedom-system'),
            __('Inställningar', 'life-freedom-system'),
            'manage_options',
            'lfs-settings',
            array($this, 'render_settings_page')
        );
        
        // User Guide submenu
        add_submenu_page(
            'life-freedom-system',
            __('Användarguide', 'life-freedom-system'),
            __('📖 Användarguide', 'life-freedom-system'),
            'read',
            'lfs-user-guide',
            array($this, 'render_user_guide_page')
        );
        
        // Points Guidelines submenu
        add_submenu_page(
            'life-freedom-system',
            __('Poängriktlinjer', 'life-freedom-system'),
            __('📊 Poängriktlinjer', 'life-freedom-system'),
            'read',
            'lfs-points-guidelines',
            array($this, 'render_points_guidelines_page')
        );
    }
    
    /**
     * Render dashboard page
     */
    public function render_dashboard_page() {
        include LFS_PLUGIN_DIR . 'admin/views/dashboard.php';
    }
    
    /**
     * Render rewards page
     */
    public function render_rewards_page() {
        include LFS_PLUGIN_DIR . 'admin/views/rewards.php';
    }
    
    /**
     * Render financial page
     */
    public function render_financial_page() {
        include LFS_PLUGIN_DIR . 'admin/views/financial.php';
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        include LFS_PLUGIN_DIR . 'admin/views/settings.php';
    }
    
    /**
     * Render user guide page
     */
    public function render_user_guide_page() {
        include LFS_PLUGIN_DIR . 'admin/views/user-guide.php';
    }
    
    /**
     * Render points guidelines page
     */
    public function render_points_guidelines_page() {
        include LFS_PLUGIN_DIR . 'admin/views/points-guidelines.php';
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        // Only load on our plugin pages
        if (strpos($hook, 'life-freedom-system') === false && 
            strpos($hook, 'lfs-') === false &&
            !in_array(get_post_type(), array('life_activity', 'lfs_project', 'lfs_reward', 'lfs_transaction', 'lfs_milestone'))) {
            return;
        }
        
        // Chart.js
        wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js', array(), '4.4.0', true);
        
        // Plugin CSS
        wp_enqueue_style('lfs-admin-css', LFS_PLUGIN_URL . 'assets/css/admin.css', array(), LFS_VERSION);
        
        // Plugin JS
        wp_enqueue_script('lfs-admin-js', LFS_PLUGIN_URL . 'assets/js/admin.js', array('jquery', 'chart-js'), LFS_VERSION, true);
        
        // Localize script
        wp_localize_script('lfs-admin-js', 'lfsData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('lfs_nonce'),
            'i18n' => array(
                'error' => __('Ett fel uppstod', 'life-freedom-system'),
                'success' => __('Genomfört!', 'life-freedom-system'),
            ),
        ));
    }
    
    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        wp_enqueue_style('lfs-frontend-css', LFS_PLUGIN_URL . 'assets/css/frontend.css', array(), LFS_VERSION);
        wp_enqueue_script('lfs-frontend-js', LFS_PLUGIN_URL . 'assets/js/frontend.js', array('jquery'), LFS_VERSION, true);
    }
    
    /**
     * Activate plugin
     */
    public function activate() {
        // Register post types and taxonomies
        $this->register_post_types();
        $this->register_taxonomies();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Create default terms
        $this->create_default_terms();
        
        // Set default options
        $this->set_default_options();
        
        // Create default activity templates
        $templates = LFS_Activity_Templates::get_instance();
        $templates->create_default_templates();
    }
    
    /**
     * Create default terms
     */
    private function create_default_terms() {
        // Activity categories
        $categories = array('Arbete', 'Hemma', 'Fritid', 'Ekonomi', 'Träning');
        foreach ($categories as $cat) {
            if (!term_exists($cat, 'activity_category')) {
                wp_insert_term($cat, 'activity_category');
            }
        }
        
        // Work contexts
        $contexts = array('Heltidsjobb', 'Eget projekt', 'Hemma', 'Fritid');
        foreach ($contexts as $context) {
            if (!term_exists($context, 'work_context')) {
                wp_insert_term($context, 'work_context');
            }
        }
        
        // Life phases
        $phases = array('Survival', 'Stabilisering', 'Autonomi');
        foreach ($phases as $phase) {
            if (!term_exists($phase, 'life_phase')) {
                wp_insert_term($phase, 'life_phase');
            }
        }
        
        // Reward levels
        $levels = array('Nivå 0 - Gratis', 'Nivå 1 - Daglig', 'Nivå 2 - Vecka', 'Nivå 3 - Månad', 'Nivå 4 - Milstolpe');
        foreach ($levels as $level) {
            if (!term_exists($level, 'reward_level')) {
                wp_insert_term($level, 'reward_level');
            }
        }
        
        // Accounts
        $accounts = array('Hyra & Fasta utgifter', 'Mat & Hem', 'Elias Vardagspott', 'Oförutsett', 'Sparande & Investering', 'Resor & Semester', 'Belöningskonto');
        foreach ($accounts as $account) {
            if (!term_exists($account, 'lfs_account')) {
                wp_insert_term($account, 'lfs_account');
            }
        }
    }
    
    /**
     * Set default options
     */
    private function set_default_options() {
        $defaults = array(
            'lfs_current_phase' => 'survival',
            'lfs_points_per_kr' => 0.5, // Survival mode: 10 points = 5 kr
            'lfs_weekly_fp_goal' => 500,
            'lfs_weekly_bp_goal' => 300,
            'lfs_weekly_sp_goal' => 400,
            'lfs_reward_account_percent' => 2, // Start with 2% until stable
        );
        
        foreach ($defaults as $key => $value) {
            if (get_option($key) === false) {
                add_option($key, $value);
            }
        }
    }
    
    /**
     * Deactivate plugin
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
}

/**
 * Initialize plugin
 */
function lfs_init() {
    return Life_Freedom_System::get_instance();
}

// Start the plugin
lfs_init();