<?php
namespace FTM_AI_Search;

if (!defined('ABSPATH')) exit;

final class Rest {
    public function init(): void {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes(): void {
        register_rest_route('ftm-ai/v1', '/query', [
            'methods' => 'POST',
            'callback' => [$this, 'handle_query'],
            'permission_callback' => '__return_true', // public endpoint; rate limiting later
            'args' => [
                'query' => ['required' => true, 'type' => 'string'],
                'mode' => ['required' => false, 'type' => 'string', 'default' => 'auto'],
                'max_sources' => ['required' => false, 'type' => 'integer', 'default' => 6],
            ],
        ]);
    }

    public function handle_query(\WP_REST_Request $request) {
        $t0 = microtime(true);

        $query = trim((string) $request->get_param('query'));
        $mode = (string) $request->get_param('mode');
        $max_sources = max(1, min(10, (int) $request->get_param('max_sources')));

        if ($query === '') {
            return new \WP_REST_Response(['error' => 'Query is required.'], 400);
        }

        // Classic WP related results (stub)
        $related = [];
        $wpq = new \WP_Query([
            's' => $query,
            'posts_per_page' => 5,
            'post_status' => 'publish',
        ]);

        foreach ($wpq->posts as $p) {
            $related[] = [
                'title' => get_the_title($p),
                'url' => get_permalink($p),
                'snippet' => wp_trim_words(wp_strip_all_tags($p->post_excerpt ?: $p->post_content), 26),
            ];
        }

        $total_ms = (int) round((microtime(true) - $t0) * 1000);

        return new \WP_REST_Response([
            'answer' => '',
            'sources' => [],
            'related' => $related,
            'confidence' => 0.0,
            'meta' => [
                'cached' => false,
                'latency_ms' => [
                    'embed' => 0,
                    'retrieve' => 0,
                    'generate' => 0,
                    'total' => $total_ms,
                ],
            ],
        ], 200);
    }
}
