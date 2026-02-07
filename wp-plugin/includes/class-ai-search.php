<?php
namespace FTM_AI_Search;

if (!defined('ABSPATH')) exit;

require_once FTM_AI_SEARCH_PATH . 'includes/class-ftm-ai-settings.php';
require_once FTM_AI_SEARCH_PATH . 'includes/class-ftm-ai-rest.php';

final class Plugin {
    private static $instance = null;

    public static function instance(): self {
        if (self::$instance === null) self::$instance = new self();
        return self::$instance;
    }

    public function init(): void {
        (new Settings())->init();
        (new Rest())->init();
    }
}
