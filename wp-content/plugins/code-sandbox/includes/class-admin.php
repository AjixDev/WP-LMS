<?php
class CodeSandboxAdmin
{
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function add_plugin_page()
    {
        add_options_page('CodeSandbox Settings', 'CodeSandbox', 'manage_options', 'code-sandbox-settings', array($this, 'options_page'));
    }

    public function register_settings()
    {
        register_setting('code-sandbox-settings-group', 'code_sandbox_api_key');
        register_setting('code-sandbox-settings-group', 'code_sandbox_selected_sandboxes', 'sanitize_text_field'); // Sanitize user input
    }

    public function options_page()
    {
        ?>
    <div class="wrap">
      <h1>CodeSandbox Settings</h1>
      <form method="post" action="options.php">
        <?php settings_fields('code-sandbox-settings-group'); ?>
        <?php do_settings_sections('code-sandbox-settings-group'); ?>
        <table class="form-table">
          <tr>
            <th scope="row"><label for="code_sandbox_api_key">API Key</label></th>
            <td><input type="text" name="code_sandbox_api_key" id="code_sandbox_api_key" value="<?php echo esc_attr(get_option('code_sandbox_api_key')); ?>" /></td>
          </tr>
          </table>
        <?php submit_button(); ?>
      </form>
    </div>
        <?php
    }
}
