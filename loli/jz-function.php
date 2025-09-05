<?php
/*
Plugin Name: Disable Functions
Description: 禁止右键、禁止复制、禁止 F12（开发者工具）等功能，支持后台设置开启/关闭，使用 Bootstrap 弹窗效果。对管理员无效
Version: 1.2
Author: 小方块,有希
*/

// 创建插件设置
function disable_functions_menu() {
    add_menu_page(
        '禁用功能设置',
        '禁用功能',
        'manage_options',
        'disable-functions-settings',
        'disable_functions_settings_page',
        'dashicons-admin-tools',
        30
    );
}
add_action('admin_menu', 'disable_functions_menu');

// 插件设置页面内容
function disable_functions_settings_page() {
    ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <div class="wrap">
        <h1>禁用功能设置</h1>

        <?php
        // 显示设置保存后的成功提示消息
        settings_errors();
        ?>

        <form method="post" action="options.php">
            <?php
            settings_fields('disable_functions_settings_group');
            do_settings_sections('disable-functions-settings');
            ?>
            <table class="form-table">
                <tr>
                    <th scope="row">启用禁用复制</th>
                    <td>
                        <input type="checkbox" name="disable_copy" value="1" <?php checked( get_option('disable_copy'), 1 ); ?>>
                        <label for="disable_copy">启用禁用复制</label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">启用禁用右键</th>
                    <td>
                        <input type="checkbox" name="disable_right_click" value="1" <?php checked( get_option('disable_right_click'), 1 ); ?>>
                        <label for="disable_right_click">启用禁用右键</label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">启用禁用F12</th>
                    <td>
                        <input type="checkbox" name="disable_f12" value="1" <?php checked( get_option('disable_f12'), 1 ); ?>>
                        <label for="disable_f12">启用禁用F12</label>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// 注册插件设置
function disable_functions_register_settings() {
    register_setting('disable_functions_settings_group', 'disable_copy');
    register_setting('disable_functions_settings_group', 'disable_right_click');
    register_setting('disable_functions_settings_group', 'disable_f12');
    
    // 添加设置保存成功消息
    add_settings_error('disable_functions_settings_group', 'settings_updated', '设置已成功保存。', 'updated');
}
add_action('admin_init', 'disable_functions_register_settings');

// 在前端页面启用禁用功能
function disable_functions() {
    if (current_user_can('administrator')) {
        // 如果是管理员，不执行禁用功能
        return;
    }

    if (get_option('disable_copy') == 1) {
        disable_copy();
    }
    if (get_option('disable_right_click') == 1) {
        disable_right_click();
    }
    if (get_option('disable_f12') == 1) {
        disable_f12();
    }
}

// 禁止复制
function disable_copy() {
    echo '<script type="text/javascript">
        document.addEventListener("copy", function(e) {
            e.preventDefault();
            showBootstrapModal("禁止复制", "禁止复制功能已启用");
        });
    </script>';
}

// 禁止右键
function disable_right_click() {
    echo '<script type="text/javascript">
        document.addEventListener("contextmenu", function(e) {
            e.preventDefault();
            showBootstrapModal("禁止右键", "禁止右键功能已启用");
        });
    </script>';
}

// 禁止 F12
function disable_f12() {
    echo '<script type="text/javascript">
        document.addEventListener("keydown", function(e) {
            if (e.keyCode == 123) { // F12
                e.preventDefault();
                showBootstrapModal("禁止 F12", "禁止 F12 (开发者工具) 功能已启用");
            }
        });
    </script>';
}

// 启用禁用功能
add_action('wp_head', 'disable_functions');
?>