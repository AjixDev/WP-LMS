<?php
class CodeSandboxPublic
{
    public function __construct()
    {
        add_shortcode('code_sandboxes', array($this, 'display_sandboxes'));
    }

    public function display_sandboxes()
    {
        // Get selected sandboxes from options
        $selected_sandboxes = get_option('code_sandbox_selected_sandboxes');

        // Fetch sandbox details using CodeSandboxAPI
        $api = new CodeSandboxAPI(get_option('code_sandbox_api_key'));
        $sandboxes = $api->get_sandboxes();

        // Filter sandboxes based on selected sandboxes (if needed)

        // Output HTML for displaying sandboxes
        ob_start();
        ?>
    <div class="code-sandbox-container">
        <?php foreach ($sandboxes as $sandbox) : ?>
        <div class="code-sandbox">
          <iframe src="<?php echo $sandbox['view_url']; ?>" title="<?php echo $sandbox['title']; ?>"></iframe>
        </div>
        <?php endforeach; ?>
    </div>
        <?php
        return ob_get_clean();
    }
}
