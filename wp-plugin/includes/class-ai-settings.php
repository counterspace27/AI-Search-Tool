<?php
namespace FTM_AI_Search;

if (!defined('ABSPATH')) exit;

final class Settings {
    const OPTION_KEY = 'ftm_ai_search_settings';

    public function init(): void {
        add_action('admin_menu', [$this, 'admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function admin_menu(): void {
        add_options_page(
            'FTM AI Search',
            'FTM AI Search',
            'manage_options',
            'ftm-ai-search',
            [$this, 'render_page']
        );
    }

    public function register_settings(): void {
        register_setting(self::OPTION_KEY, self::OPTION_KEY, [
            'type' => 'array',
            'sanitize_callback' => [$this, 'sanitize'],
            'default' => [],
        ]);
    }

    public function sanitize($input): array {
        $out = [];
        $out['opensearch_url'] = isset($input['opensearch_url']) ? esc_url_raw($input['opensearch_url']) : '';
        $out['embed_url']      = isset($input['embed_url']) ? esc_url_raw($input['embed_url']) : '';
        $out['llm_url']        = isset($input['llm_url']) ? esc_url_raw($input['llm_url']) : '';
        $out['redis_url']      = isset($input['redis_url']) ? sanitize_text_field($input['redis_url']) : '';
        $out['post_types']     = isset($input['post_types']) && is_array($input['post_types'])
            ? array_values(array_map('sanitize_key', $input['post_types']))
            : [];
        return $out;
    }

    public static function get(): array {
        $settings = get_option(self::OPTION_KEY, []);
        return is_array($settings) ? $settings : [];
    }

    public function render_page(): void {
        if (!current_user_can('manage_options')) return;

        $settings = self::get();
        $post_types = get_post_types(['public' => true], 'objects');

        ?>
        <div class="wrap">
            <h1>FTM AI Search</h1>
            <form method="post" action="options.php">
                <?php settings_fields(self::OPTION_KEY); ?>

                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label for="opensearch_url">OpenSearch URL</label></th>
                        <td><input name="<?php echo esc_attr(self::OPTION_KEY); ?>[opensearch_url]" id="opensearch_url" type="url" class="regular-text" value="<?php echo esc_attr($settings['opensearch_url'] ?? ''); ?>"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="embed_url">Embeddings URL</label></th>
                        <td><input name="<?php echo esc_attr(self::OPTION_KEY); ?>[embed_url]" id="embed_url" type="url" class="regular-text" value="<?php echo esc_attr($settings['embed_url'] ?? ''); ?>"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="llm_url">LLM URL</label></th>
                        <td><input name="<?php echo esc_attr(self::OPTION_KEY); ?>[llm_url]" id="llm_url" type="url" class="regular-text" value="<?php echo esc_attr($settings['llm_url'] ?? ''); ?>"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="redis_url">Redis URL</label></th>
                        <td><input name="<?php echo esc_attr(self::OPTION_KEY); ?>[redis_url]" id="redis_url" type="text" class="regular-text" value="<?php echo esc_attr($settings['redis_url'] ?? ''); ?>"></td>
                    </tr>
                    <tr>
                        <th scope="row">Indexed Post Types</th>
                        <td>
                            <?php foreach ($post_types as $pt): ?>
                                <label style="display:block; margin: 2px 0;">
                                    <input
                                        type="checkbox"
                                        name="<?php echo esc_attr(self::OPTION_KEY); ?>[post_types][]"
                                        value="<?php echo esc_attr($pt->name); ?>"
                                        <?php checked(in_array($pt->name, $settings['post_types'] ?? [], true)); ?>
                                    >
                                    <?php echo esc_html($pt->labels->singular_name . ' (' . $pt->name . ')'); ?>
                                </label>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                </table>

                <?php submit_button('Save Settings'); ?>
            </form>
        </div>
        <?php
    }
}
