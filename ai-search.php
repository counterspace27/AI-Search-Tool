<?php
/**
 * Plugin Name: FTM AI Search
 * Description: AI-style Q&A search for FamilyTreeMagazine.com using self-hosted models and OpenSearch (no OpenAI).
 * Version: 0.1.0
 * Author: Counterspace
 */

if (!defined('ABSPATH')) exit;

define('FTM_AI_SEARCH_VERSION', '0.1.0');
define('FTM_AI_SEARCH_PATH', plugin_dir_path(__FILE__));
define('FTM_AI_SEARCH_URL', plugin_dir_url(__FILE__));

require_once FTM_AI_SEARCH_PATH . 'includes/class-ftm-ai-search.php';

function ftm_ai_search_boot() {
    \FTM_AI_Search\Plugin::instance()->init();
}
add_action('plugins_loaded', 'ftm_ai_search_boot');
