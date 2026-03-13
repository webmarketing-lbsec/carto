<?php
/**
 * Plugin Name: Prezzi Case Prefabbricate - Filtro Modelli
 * Description: Aggiunge uno shortcode per filtrare gli articoli con menu a tendina multilivello sulle categorie dei modelli di case prefabbricate.
 * Version: 1.0.0
 * Author: Codex
 * Text Domain: prezzi-case-filtri
 */

if (! defined('ABSPATH')) {
    exit;
}

final class Prezzi_Case_Filtri
{
    private const NONCE_ACTION = 'pcf_filter_posts';

    /**
     * Struttura di default delle categorie radice da usare nei filtri.
     *
     * @return array<string, array{label: string, root_slug: string}>
     */
    private function get_filter_groups(): array
    {
        $groups = [
            'housing_type' => [
                'label' => __('Tipologia abitativa', 'prezzi-case-filtri'),
                'root_slug' => 'tipologia-abitativa',
            ],
            'floors' => [
                'label' => __('Numero di piani', 'prezzi-case-filtri'),
                'root_slug' => 'numero-di-piani',
            ],
            'bedrooms' => [
                'label' => __('Numero di camere da letto', 'prezzi-case-filtri'),
                'root_slug' => 'numero-di-camere-da-letto',
            ],
            'bathrooms' => [
                'label' => __('Numero di bagni', 'prezzi-case-filtri'),
                'root_slug' => 'numero-di-bagni',
            ],
            'price_range' => [
                'label' => __('Range di prezzo', 'prezzi-case-filtri'),
                'root_slug' => 'range-di-prezzo',
            ],
        ];

        /**
         * Permette di personalizzare i gruppi filtro.
         *
         * Esempio:
         * add_filter('pcf_filter_groups', static function (array $groups): array {
         *     $groups['bathrooms']['root_slug'] = 'bagni';
         *     return $groups;
         * });
         */
        return (array) apply_filters('pcf_filter_groups', $groups);
    }

    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'register_assets']);
        add_shortcode('prefab_house_filter', [$this, 'render_shortcode']);

        add_action('wp_ajax_pcf_filter_posts', [$this, 'ajax_filter_posts']);
        add_action('wp_ajax_nopriv_pcf_filter_posts', [$this, 'ajax_filter_posts']);
    }

    public function register_assets(): void
    {
        $version = '1.0.0';

        wp_register_style(
            'pcf-style',
            plugin_dir_url(__FILE__) . 'assets/css/prezzi-case-filtri.css',
            [],
            $version
        );

        wp_register_script(
            'pcf-script',
            plugin_dir_url(__FILE__) . 'assets/js/prezzi-case-filtri.js',
            [],
            $version,
            true
        );
    }

    /**
     * @param array<string, mixed> $atts
     */
    public function render_shortcode(array $atts = []): string
    {
        wp_enqueue_style('pcf-style');
        wp_enqueue_script('pcf-script');

        $groups = $this->get_filter_groups();
        $instance_id = 'pcf-' . wp_generate_password(8, false, false);

        wp_localize_script('pcf-script', 'pcfData', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce(self::NONCE_ACTION),
            'action' => 'pcf_filter_posts',
            'labels' => [
                'loading' => __('Caricamento risultati...', 'prezzi-case-filtri'),
                'noResults' => __('Nessun risultato trovato.', 'prezzi-case-filtri'),
                'error' => __('Si è verificato un errore durante la ricerca.', 'prezzi-case-filtri'),
            ],
        ]);

        ob_start();
        ?>
        <div class="pcf-wrapper" data-instance-id="<?php echo esc_attr($instance_id); ?>">
            <form class="pcf-form" autocomplete="off">
                <div class="pcf-fields-grid">
                    <?php foreach ($groups as $key => $config) : ?>
                        <?php $this->render_dropdown_field($key, $config); ?>
                    <?php endforeach; ?>
                </div>

                <div class="pcf-actions">
                    <button type="submit" class="pcf-btn pcf-btn-primary"><?php esc_html_e('Cerca modelli', 'prezzi-case-filtri'); ?></button>
                    <button type="reset" class="pcf-btn pcf-btn-secondary"><?php esc_html_e('Reset filtri', 'prezzi-case-filtri'); ?></button>
                </div>
            </form>

            <div class="pcf-results" aria-live="polite"></div>
        </div>
        <?php

        return (string) ob_get_clean();
    }

    /**
     * @param array{label?: string, root_slug?: string} $config
     */
    private function render_dropdown_field(string $key, array $config): void
    {
        $label = isset($config['label']) ? (string) $config['label'] : ucfirst($key);
        $root_slug = isset($config['root_slug']) ? sanitize_title((string) $config['root_slug']) : '';

        $root_term = $root_slug !== '' ? get_term_by('slug', $root_slug, 'category') : false;
        $root_id = ($root_term instanceof WP_Term) ? (int) $root_term->term_id : 0;

        $terms = $this->get_term_tree($root_id);
        ?>
        <div class="pcf-field">
            <label for="pcf-<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label>
            <select id="pcf-<?php echo esc_attr($key); ?>" name="filters[<?php echo esc_attr($key); ?>]" data-filter-key="<?php echo esc_attr($key); ?>">
                <option value=""><?php esc_html_e('Qualsiasi', 'prezzi-case-filtri'); ?></option>
                <?php foreach ($terms as $term_data) : ?>
                    <option value="<?php echo esc_attr((string) $term_data['id']); ?>">
                        <?php echo esc_html(str_repeat('— ', (int) $term_data['depth']) . $term_data['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php
    }

    /**
     * @return array<int, array{id: int, name: string, depth: int}>
     */
    private function get_term_tree(int $root_id = 0): array
    {
        $terms = get_terms([
            'taxonomy' => 'category',
            'hide_empty' => false,
            'parent' => $root_id,
            'orderby' => 'name',
            'order' => 'ASC',
        ]);

        if (is_wp_error($terms) || empty($terms)) {
            return [];
        }

        $result = [];

        foreach ($terms as $term) {
            if (! $term instanceof WP_Term) {
                continue;
            }

            $result[] = [
                'id' => (int) $term->term_id,
                'name' => (string) $term->name,
                'depth' => 0,
            ];

            $result = array_merge($result, $this->get_term_descendants((int) $term->term_id, 1));
        }

        return $result;
    }

    /**
     * @return array<int, array{id: int, name: string, depth: int}>
     */
    private function get_term_descendants(int $parent_id, int $depth): array
    {
        $children = get_terms([
            'taxonomy' => 'category',
            'hide_empty' => false,
            'parent' => $parent_id,
            'orderby' => 'name',
            'order' => 'ASC',
        ]);

        if (is_wp_error($children) || empty($children)) {
            return [];
        }

        $result = [];

        foreach ($children as $child) {
            if (! $child instanceof WP_Term) {
                continue;
            }

            $result[] = [
                'id' => (int) $child->term_id,
                'name' => (string) $child->name,
                'depth' => $depth,
            ];

            $result = array_merge($result, $this->get_term_descendants((int) $child->term_id, $depth + 1));
        }

        return $result;
    }

    public function ajax_filter_posts(): void
    {
        check_ajax_referer(self::NONCE_ACTION, 'nonce');

        $raw_filters = isset($_POST['filters']) ? (array) $_POST['filters'] : [];
        $selected_terms = [];

        foreach ($raw_filters as $value) {
            $term_id = absint($value);
            if ($term_id > 0) {
                $selected_terms[] = $term_id;
            }
        }

        $selected_terms = array_values(array_unique($selected_terms));

        $query_args = [
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => 12,
            'ignore_sticky_posts' => true,
        ];

        if (! empty($selected_terms)) {
            $query_args['category__and'] = $selected_terms;
        }

        $query = new WP_Query($query_args);

        ob_start();

        if ($query->have_posts()) {
            echo '<div class="pcf-cards">';
            while ($query->have_posts()) {
                $query->the_post();
                ?>
                <article class="pcf-card">
                    <?php if (has_post_thumbnail()) : ?>
                        <a class="pcf-card-thumb" href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium'); ?></a>
                    <?php endif; ?>

                    <div class="pcf-card-body">
                        <h3 class="pcf-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <p class="pcf-card-meta"><?php echo esc_html(get_the_date()); ?></p>
                    </div>
                </article>
                <?php
            }
            echo '</div>';
            wp_reset_postdata();
        } else {
            echo '<p class="pcf-no-results">' . esc_html__('Nessun risultato trovato.', 'prezzi-case-filtri') . '</p>';
        }

        $html = (string) ob_get_clean();

        wp_send_json_success([
            'html' => $html,
            'count' => (int) $query->found_posts,
        ]);
    }
}

new Prezzi_Case_Filtri();
