<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?php wp_title('|', true, 'right'); bloginfo('name'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="google-site-verification" content="AffNJYgf1unXDQ3bvx2gILpYi9g01AE__V1juOHCPOM" />
    <?php seo_campus_nice(); ?>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php
// Ambil semua data dari options
    $header  = get_option('campus_header_settings', []);
    // Header Options
    $bg_top_header          = $header['bg_top_header'] ?? '#087396';
    $text_color_top_header  = $header['text_color_top_header'] ?? '#ffffff';
?>
    <div class="container-fluid" style="background-color: <?php echo esc_attr($bg_top_header); ?>;">
        <div class="container">
            <div class="header-top-custom">
                <?php get_top_header(); ?>
                <!-- Translate -->
                <?php if (shortcode_exists('gtranslate')) : ?>
                <div class="header-item">
                    <div class="translate-widget">
                        <?php echo do_shortcode('[gtranslate]'); ?>
                    </div>
                </div>
            <?php endif; ?>
                <!-- Search Icon -->
                <div class="header-item d-none d-md-block">
                    <a href="#" id="search-button-top" class="text-light fs-5">
                        <i class="bi bi-search" style="color:<?php echo esc_attr($text_color_top_header); ?>;"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php
// ===== LOGIKA HEADER PRODI =====
// Fungsi untuk mendapatkan parent teratas dari hirarki bawaan WP
function get_top_parent_id($post_id) {
    $parent_id = wp_get_post_parent_id($post_id);
    if ($parent_id) {
        return get_top_parent_id($parent_id);
    }
    return $post_id;
}

// Ambil setting dari theme options
$header_options         = get_option('campus_header_settings', []);
$bg_menu_header         = $header_options['bg_menu_header'] ?? '#181f4b';
$text_color_menu_header = $header_options['text_color_menu_header'] ?? '#ffffff';

// Default nilai
$logo_url   = '';
$site_title = get_bloginfo('name');
$logo_link  = home_url('/');
$menu_args  = [];

// Cek jika ini adalah halaman Page
if (is_page()) {
    $current_id = get_the_ID();

    // Tentukan master ID berdasarkan metabox
    if (get_post_meta($current_id, 'is_parent_prodi', true) === '1') {
        $master_id = $current_id;
    } elseif ($parent_prodi_id = get_post_meta($current_id, 'parent_prodi_id', true)) {
        $master_id = intval($parent_prodi_id);
    } else {
        $master_id = get_top_parent_id($current_id);
    }

    // Jika master ini adalah Home Prodi
    if (get_page_template_slug($master_id) === 'page-templates/home-prodi.php') {
        // Logo dari featured image master
        if (has_post_thumbnail($master_id)) {
            $logo_url = get_the_post_thumbnail_url($master_id, 'medium');
        }

        // Judul & Link logo dari master
        $site_title = get_the_title($master_id);
        $logo_link  = get_permalink($master_id);

        // Menu dari metabox master
        $menu_id = get_post_meta($master_id, 'menu_prodi_id', true);
        if (!empty($menu_id)) {
            $menu_args = [
                'menu'       => intval($menu_id),
                'container'  => false,
                'menu_class' => 'dropdown',
                'depth'      => 4,
                'fallback_cb'=> false,
            ];
        } else {
            $menu_args = [
                'theme_location' => 'header-menu',
                'container'      => false,
                'menu_class'     => 'dropdown',
                'depth'          => 4,
                'fallback_cb'    => false,
            ];
        }
    }
}

// Fallback untuk non-prodi
if (empty($logo_url) && empty($menu_args)) {
    $custom_logo_id = get_theme_mod('custom_logo');
    if ($custom_logo_id) {
        $logo_data = wp_get_attachment_image_src($custom_logo_id, 'full');
        $logo_url  = $logo_data[0] ?? '';
    }
    $menu_args = [
        'theme_location' => 'header-menu',
        'container'      => false,
        'menu_class'     => 'dropdown',
        'depth'          => 4,
        'fallback_cb'    => false,
    ];
}
?>

<header class="header d-flex align-items-center fixed-top" style="background-color: <?php echo esc_attr($bg_menu_header); ?>;">
    <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

        <!-- Logo -->
        <a class="logo d-flex align-items-center" href="<?php echo esc_url($logo_link); ?>">
            <?php if (!empty($logo_url)) : ?>
                <img style="max-height: 60px;" src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($site_title); ?>">
            <?php else : ?>
               <div class="text-light"><?php echo esc_attr($site_title); ?><p><?php bloginfo('description'); ?></p></div>
            <?php endif; ?>
        </a>

        <!-- Menu -->
        <nav id="navmenu" class="navmenu">
            <?php wp_nav_menu($menu_args); ?>
            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            <style>
                 .navmenu a  {
                    color: <?php echo esc_attr($text_color_menu_header); ?>;
                }
                .navmenu .dropdown ul.sub-menu {
                    background: <?php echo esc_attr($bg_top_header); ?>;
                }
                .navmenu .dropdown ul a {
                    color: <?php echo esc_attr($text_color_top_header); ?>;
                }
            </style>
        </nav>

    </div>
</header>



<!---------------------------
* Search Modal
----------------------------->
<div id="search-modal" class="search-modal">
  <div class="search-modal-content">
    <span class="close-search">&times;</span>
    <h2 class="search-title">Cari Sesuatu</h2>
    <form action="<?php echo esc_url( home_url('/') ); ?>" method="get">
      <input type="text" name="s" placeholder="Masukkan kata kunci..." autofocus>
      <button type="submit"><i class="bi bi-search"></i></button>
    </form>
  </div>
</div>

<!---------------------------
* Mobile Apps Menu
----------------------------->
<?php
if (wp_is_mobile()) :
    $menu_locations = get_nav_menu_locations();

    if (!empty($menu_locations['primary'])) {
        $menu = wp_get_nav_menu_items($menu_locations['primary']);
        $menu_items = [];

        if ($menu) {
            foreach ($menu as $item) {
                $menu_items[$item->menu_item_parent][] = $item;
            }
        ?>
        <nav class="bottom-navbar">
            <?php foreach ($menu_items[0] ?? [] as $item) :
                $has_children = !empty($menu_items[$item->ID]);
                $icon_class = trim(get_post_meta($item->ID, '_menu_item_icon', true));
                $icon_class = $icon_class ?: 'bi-circle';
            ?>
            <div class="bottom-nav-item-wrapper <?php echo $has_children ? 'has-children' : ''; ?>">
                <a href="<?php echo esc_url($has_children ? '#' : $item->url); ?>"
                   class="bottom-nav-item"
                   aria-haspopup="<?php echo $has_children ? 'true' : 'false'; ?>"
                   aria-expanded="false">
                    <i class="bi <?php echo esc_attr($icon_class); ?>"></i>
                    <span><?php echo esc_html($item->title); ?></span>
                    <?php if ($has_children) : ?>
                        <i class="bi bi-chevron-up submenu-toggle-icon"></i>
                    <?php endif; ?>
                </a>

                <?php if ($has_children) : ?>
                <div class="submenu-popup" id="submenu-<?php echo esc_attr($item->ID); ?>">
                    <div class="submenu-popup-inner">
                        <div class="submenu-header">
                            <span><?php echo esc_html($item->title); ?></span><button class="submenu-close">&times;</button>
                        </div>
                        <div class="submenu-grid">
                            <?php foreach ($menu_items[$item->ID] as $child) :
                                $child_icon_class = trim(get_post_meta($child->ID, '_menu_item_icon', true));
                                $child_icon_class = $child_icon_class ?: 'bi-circle';
                            ?>
                            <a href="<?php echo esc_url($child->url); ?>" class="submenu-grid-item">
                                <i class="bi <?php echo esc_attr($child_icon_class); ?>"></i>
                                <span><?php echo esc_html($child->title); ?></span>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>

            <!-- ✅ Search Icon as Final Menu Item -->
            <div class="bottom-nav-item-wrapper d-md-none">
                <a href="#" class="bottom-nav-item" id="search-button-mobile">
                    <i class="bi bi-search"></i>
                    <span>Cari</span>
                </a>
            </div>
        </nav>
        <?php
        }
    }
endif;
?>