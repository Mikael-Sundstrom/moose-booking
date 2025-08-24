<?php if (!defined('ABSPATH')) exit; ?>

<div class="wrap">
    <h1><?php echo get_admin_page_title(); ?></h1>

    <?php
    // Kolla vilken tab som är aktiv
    $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';
    ?>

    <h2 class="nav-tab-wrapper">
        <a href="?page=moosebooking-settings&tab=general" class="nav-tab <?php echo $active_tab === 'general' ? 'nav-tab-active' : ''; ?>">
            <?php esc_html_e('Email', 'moose-booking'); ?>
        </a>
        <a href="?page=moosebooking-settings&tab=integrations" class="nav-tab <?php echo $active_tab === 'integrations' ? 'nav-tab-active' : ''; ?>">
            <?php esc_html_e('Notifications', 'moose-booking'); ?>
        </a>
        <a href="?page=moosebooking-settings&tab=appearance" class="nav-tab <?php echo $active_tab === 'appearance' ? 'nav-tab-active' : ''; ?>">
            <?php esc_html_e('Appearance', 'moose-booking'); ?>
        </a>
        <a href="?page=moosebooking-settings&tab=limits" class="nav-tab <?php echo $active_tab === 'limits' ? 'nav-tab-active' : ''; ?>">
            <?php esc_html_e('Limits', 'moose-booking'); ?>
        </a>
        <a href="?page=moosebooking-settings&tab=about" class="nav-tab <?php echo $active_tab === 'about' ? 'nav-tab-active' : ''; ?>">
            <?php esc_html_e('About', 'moose-booking'); ?>
        </a>
    </h2>

    <?php
    // Visa innehållet beroende på aktiv flik
    switch ($active_tab) {
        case 'general':
            require_once plugin_dir_path(__FILE__) . 'tabs/email.php';
            break;
        case 'integrations':
            require_once plugin_dir_path(__FILE__) . 'tabs/notifications.php';
            break;
        case 'appearance':
            require_once plugin_dir_path(__FILE__) . 'tabs/appearance.php';
            break;
        case 'limits':
            require_once plugin_dir_path(__FILE__) . 'tabs/limits.php';
            break;
        case 'about':
            require_once plugin_dir_path(__FILE__) . 'tabs/about.php';
            break;
    }
    ?>
</div>
