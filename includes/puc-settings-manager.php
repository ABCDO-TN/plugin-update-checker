<?php
/**
 * Class PucSettingsManager
 * Handles the settings page for the Plugin Update Checker configuration.
 */
class PucSettingsManager {

    // The option name used to store settings in the database
    private $option_name = 'puc_update_settings';

    public function __construct() {
        // Hook into the admin menu to add the settings page
        add_action('admin_menu', array($this, 'add_plugin_page'));
        // Hook to register our settings
        add_action('admin_init', array($this, 'page_init'));
    }

    /**
     * Add options page under "Settings"
     */
    public function add_plugin_page() {
        add_options_page(
            'Github Updates Config', // Page Title
            'Update License',        // Menu Title (Requested: License)
            'manage_options',        // Capability
            'puc-updates-config',    // Menu Slug
            array($this, 'create_admin_page') // Callback function
        );
    }

    /**
     * Register settings and sanitization
     */
    public function page_init() {
        register_setting(
            'puc_option_group', // Option group
            $this->option_name, // Option name
            array($this, 'sanitize') // Sanitize callback
        );

        add_settings_section(
            'puc_setting_section', // ID
            'Repository Configuration', // Title
            array($this, 'section_info'), // Callback
            'puc-updates-config' // Page
        );

        add_settings_field(
            'update_type', 
            'Update Type', 
            array($this, 'update_type_callback'), 
            'puc-updates-config', 
            'puc_setting_section'
        );

        add_settings_field(
            'repo_url', 
            'GitHub Repository URL', 
            array($this, 'repo_url_callback'), 
            'puc-updates-config', 
            'puc_setting_section'
        );

        add_settings_field(
            'access_token', 
            'GitHub Access Token (Optional)', 
            array($this, 'access_token_callback'), 
            'puc-updates-config', 
            'puc_setting_section'
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize($input) {
        $new_input = array();
        
        if(isset($input['update_type']))
            $new_input['update_type'] = sanitize_text_field($input['update_type']);

        if(isset($input['repo_url']))
            $new_input['repo_url'] = esc_url_raw($input['repo_url']);

        if(isset($input['access_token']))
            $new_input['access_token'] = sanitize_text_field($input['access_token']);

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function section_info() {
        print 'Enter the details for your GitHub repository below:';
    }

    /** 
     * Get the settings option array
     */
    private function get_options() {
        return get_option($this->option_name);
    }

    /** 
     * Field: Update Type (Plugin, Theme, Both)
     */
    public function update_type_callback() {
        $options = $this->get_options();
        $val = isset($options['update_type']) ? $options['update_type'] : 'plugin';
        ?>
        <select name="<?php echo $this->option_name; ?>[update_type]">
            <option value="plugin" <?php selected($val, 'plugin'); ?>>Plugin</option>
            <option value="theme" <?php selected($val, 'theme'); ?>>Theme</option>
            <option value="both" <?php selected($val, 'both'); ?>>Both (Auto-detect)</option>
        </select>
        <p class="description">Select what this update checker is controlling.</p>
        <?php
    }

    /** 
     * Field: Repository URL
     */
    public function repo_url_callback() {
        $options = $this->get_options();
        $val = isset($options['repo_url']) ? $options['repo_url'] : '';
        echo '<input type="text" id="repo_url" name="'.$this->option_name.'[repo_url]" value="'.$val.'" class="regular-text" />';
        echo '<p class="description">E.g., https://github.com/username/repo-name</p>';
    }

    /** 
     * Field: Access Token
     */
    public function access_token_callback() {
        $options = $this->get_options();
        $val = isset($options['access_token']) ? $options['access_token'] : '';
        echo '<input type="password" id="access_token" name="'.$this->option_name.'[access_token]" value="'.$val.'" class="regular-text" />';
        echo '<p class="description">Leave empty for public repositories. Required for private ones.</p>';
    }

    /**
     * Options page callback
     */
    public function create_admin_page() {
        ?>
        <div class="wrap">
            <h1>Update Settings (الترخيص)</h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields('puc_option_group');
                // This prints out all settings sections
                do_settings_sections('puc-updates-config');
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }
}

// Initialize the settings manager
if(is_admin())
    $puc_settings_manager = new PucSettingsManager();