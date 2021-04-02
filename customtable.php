<?php
/*
 * Plugin name: WordPress shortcode custom table
 * Description: вывод формы через shortcode с записью в созданную таблицу в базе данных
 * Author: Rezonov
 */


/*
 * Функция для создания таблицы в базе данных. Срабатывает в момент активации плагина
 */

register_activation_hook(__FILE__, 'plugin_activate');
function plugin_activate()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE `{$wpdb->base_prefix}clients_from_form` (
          id int NOT NULL AUTO_INCREMENT,
          user_mail varchar(100) NOT NULL, 
          created_at datetime NOT NULL,
          PRIMARY KEY  (id)
        ) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    dbDelta($sql);
    $success = empty($wpdb->last_error);

    return $success;
}

/*
 * Функция деактивации.
 * P.S. Более логично переписать удаление таблицы в хук для удаления плагина совсем. Но для dev'a решил оставить так.
 */
register_deactivation_hook(__FILE__, 'plugin_deactivate');

function plugin_deactivate()
{
    global $wpdb;
    $sql = "DROP TABLE `{$wpdb->base_prefix}clients_from_form`";
    $wpdb->query($sql);
}



/*
 * Раздел работы с шорткодом
 */
add_action( 'wp_enqueue_scripts', 'my_scripts_method' );
function my_scripts_method(){
    wp_enqueue_script( 'jquery');
    wp_enqueue_script( 'scriptplugin', plugins_url('/assets/script.js', __FILE__));
    wp_localize_script( 'scriptplugin', 'my_ajax_object',
        array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
}

function shortcode_function()
{
//    ob_start();
    ?>
    <form class="form_go">
        <table>
            <tr>
                <td><input type="text" class="email" value="" placeholder="Введите Ваш email"/></td>
            </tr>
            <tr>
                <td><input type="submit" value="Сохранить" /></td>
            </tr>
        </table>
    </form>
<?php

  //  return ob_get_clean();
}
add_shortcode('rezonov_shortcode', 'shortcode_function');

add_action( 'wp_ajax_sendmail_form', 'sendmail_form' );
function sendmail_form(){
    $value = (!empty($_POST['email']) ? trim(esc_sql($_POST['email'])) : null);
    if($value) {
        global $wpdb;
        $wpdb->insert(
            '{$wpdb->base_prefix}clients_from_form',
            array(
                'email' => $_POST['email'],
                'createdat' => $_POST['']
            )
        );
        echo $value;

    } else {
        echo "false";
    }
    wp_die(); // выход нужен для того, чтобы в ответе не было ничего лишнего, только то что возвращает функция
}
?>
