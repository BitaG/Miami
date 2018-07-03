<?php

/*
if (class_exists('MultiPostThumbnails')) {
 
new MultiPostThumbnails(array(
'label' => 'Secondary Image',
'id' => 'secondary-image',
'post_type' => 'post'
 ) );
 
 }
*/

/*DEFAULT*/
add_theme_support( 'title-tag' );
add_theme_support( 'post-thumbnails' );
set_post_thumbnail_size( 250, 150 );
add_image_size( 'thumb-400', 400, 400, true );




//STYLE
add_action( 'wp_print_styles', 'add_styles' );
if (!function_exists( 'add_styles' )){
    function add_styles(){
        if(is_admin()) return false;
        wp_enqueue_style( 'bootstrap4', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css' );
	    wp_enqueue_style( 'animate', get_template_directory_uri().'/css/animate.css' );
	    wp_enqueue_style( 'icons', get_template_directory_uri().'/css/icons.css' );
		wp_enqueue_style( 'main', get_template_directory_uri().'/style.css' );
    }
}

//SCRIPT
add_action( 'wp_footer', 'add_scripts' );
if (!function_exists( 'add_scripts' )){
	function add_scripts(){
	    if(is_admin()) return false;
	    wp_deregister_script( 'jquery' );
	    wp_enqueue_script( 'jquery','https://code.jquery.com/jquery-3.3.1.min.js','','',true );
	    wp_enqueue_script( 'bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js','','',true );
	    wp_enqueue_script( 'main', get_template_directory_uri().'/js/main.js','','',true );
	}
}

//////////////////////////////////////////////////////////////
//G - menu position
register_nav_menus(array( 'top' => 'top' ));

//////////////////////////////////////////////////////////////
//G - remove emoji
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
add_filter( 'tiny_mce_plugins', 'disable_wp_emojis_in_tinymce' );
function disable_wp_emojis_in_tinymce( $plugins ){
    if ( is_array($plugins) ){
        return array_diff( $plugins, array( 'wpemoji' ));
    } else {
        return array();
    }
}

//////////////////////////////////////////////////////////////
//G - pagination
if (!function_exists( 'pagination' )){
    function pagination(){
        global $wp_query;
        $big    = 999999999;
        $links  = paginate_links( array(
                'base'              => str_replace($big,'%#%',esc_url(get_pagenum_link($big))),
                'format'            => '?paged=%#%',
                'current'           => max(1, get_query_var('paged')),
                'type'              => 'array',
                'prev_text'         => 'Назад',
                'next_text'         => 'Вперед',
                'total'             => $wp_query->max_num_pages,
                'show_all'          => false,
                'end_size'          => 15,
                'mid_size'          => 15,
                'add_args'          => false,
                'add_fragment'      => '',
                'before_page_number'=> '',
                'after_page_number' => '' )
        );
        if( is_array( $links ) ){
            echo '<ul class="pagination">';
            foreach ( $links as $link ) {
                if ( strpos( $link, 'current' ) !== false ) echo "<li class='active'>$link</li>";
                else echo "<li>$link</li>";
            }
            echo '</ul>';
        }
    }
}

//////////////////////////////////////////////////////////////
//G - background
add_theme_support( 'custom-background', array(
        'default-color'         => '',
        'default-image'         => '',
        'wp-head-callback'      => '_custom_background_cb',
        'admin-head-callback'   => '',
        'admin-preview-callback'=> '' )
);

/******************************************************************************/
//G - customize
function bita_customize_register( $wp_customize ){
    $wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
    $wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
    $wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
    $wp_customize->get_section( 'title_tagline' )->priority     = '9';
    $wp_customize->get_section( 'title_tagline' )->title        = 'Домашняя страница';
    $wp_customize->get_section( 'title_tagline' )->panel        = 'bita_header_panel';
    $wp_customize->remove_control( 'header_textcolor' );
    $wp_customize->remove_control( 'display_header_text' );
    $wp_customize->remove_panel( 'widgets' );

    class Bita_Info extends WP_Customize_Control{
        public $type    = 'info';
        public $label   = '';
        public function render_content(){?>
            <h4 style="margin-top:30px;border-bottom:1px solid;padding:5px;color:#111;text-align: center; text-transform:uppercase;"><?php echo esc_html( $this->label );?></h4><?php
        }
    }

    $wp_customize->add_panel( 'bita_header_panel', array(
            'priority'          => 10,
            'capability'        => 'edit_theme_options',
            'title'             => 'bitabit'
        )
    );

    $wp_customize->add_setting( 'site_logo', array( 'sanitize_callback' => 'esc_url_raw' ));

    $wp_customize->add_control(
            new WP_Customize_Image_Control( $wp_customize, 'site_logo', array(
                    'label'     => 'Логотип',
                    'type'      => 'image',
                    'section'   => 'title_tagline',
                    'settings'  => 'site_logo',
                    'priority'  => 11
                )
            )
    );

//////////////////////////////////////////////////////////////
//G - logo_size
    $wp_customize->add_setting( 'logo_size', array(
            'sanitize_callback' => 'absint',
            'default'           => '40',
            'transport'         => 'postMessage'
        )
    );
    $wp_customize->add_control( 'logo_size', array(
            'type'              => 'number',
            'priority'          => 12,
            'section'           => 'title_tagline',
            'label'             => 'Размер лого',
            'input_attrs'       => array(
                        'min'   => 40,
                        'max'   => 120,
                        'step'  => 5,
                        'style' => 'margin-bottom: 15px; padding: 15px;'),
            )
    );

//////////////////////////////////////////////////////////////
//G - color
    $wp_customize->add_setting( 'mobile_color', array(
            'default'           => '#9FAFF1',
            'sanitize_callback' => 'sanitize_hex_color',
        )
    );
    $wp_customize->add_control(
            new WP_Customize_Color_Control( $wp_customize, 'mobile_color', array(
                    'label'     => 'Цвет мобильной версии',
                    'section'   => 'colors',
                    'priority'  => 12
            )
        )
    );

//////////////////////////////////////////////////////////////
// G - COPYRIGHT
    $wp_customize->add_section( 'bita_footer', array(
            'title'             => 'Футер',
            'priority'          => 90,
            'panel'             => 'bita_header_panel',
            'description'       => '<em>Footer (футер, подвал) – футер, он же подвал сайта – это блок в нижней части страницы, куда выносят полезную, но не первостепенную информацию. Как примеры можно привести: Данные о копирайте – (с) Имя компании 2018.</em>',
        )
    );
    $wp_customize->add_setting( 'copybit', array(
            'sanitize_callback' => 'absint',
            'default'           => 'bitabit',
            'transport'         => 'postMessage'
        )
    );

    $wp_customize->add_control( 'copybit', array(
            'type'              => 'text',
            'priority'          => 12,
            'section'           => 'bita_footer',
            'label'             => 'copyright',
            'description'       => 'Текст в подвале сайта',
        )
    );

//////////////////////////////////////////////////////////////
/// G - single_image
    $wp_customize->add_section( 'bita_single', array(
            'title'             => 'Single image',
            'priority'          => 30,
            'panel'             => 'bita_header_panel',
            'description'       => '<em>Выбирая картинку соблюдайте пропорции</em>',
        )
    );
    $wp_customize->add_setting( 'single_image', array( 'sanitize_callback' => 'esc_url_raw' ));
    $wp_customize->add_control(
            new WP_Customize_Image_Control( $wp_customize, 'single_image', array(
                'label'         => 'картинка',
                'type'          => 'image',
                'section'       => 'bita_single',
                'priority'      => 10,
            )
        )
    );

////text
    $wp_customize->add_setting( 'single_text', array( 'sanitize_callback' => 'bita_sanitize_text' ));
    $wp_customize->add_control( 'single_text', array(
            'label'             => 'текст',
            'section'           => 'bita_single',
            'type'              => 'text',
            'priority'          => 10
        )
    );

////link
    $wp_customize->add_setting( 'single_link', array( 'sanitize_callback' => 'bita_sanitize_text' ));
    $wp_customize->add_control( 'single_link', array(
            'label'             => 'ссылка',
            'section'           => 'bita_single',
            'type'              => 'text',
            'priority'          => 10
        )
    );

//////////////////////////////////////////////////////////////
//G - slider
    $wp_customize->add_section( 'bita_slider', array(
            'title'             => 'Слайдер',
            'priority'          => 40,
            'panel'             => 'bita_header_panel',
            'description'       => '<em>Слайдер поддерживает 5 слайдов с текстом и ссылкой.</em>',
        )
    );

////slide01
    $wp_customize->add_setting( 'bita_options[info]', array(
            'type'              => 'info_control',
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => 'esc_attr',
        )
    );
    $wp_customize->add_control(
            new bita_Info( $wp_customize, 's1', array(
                'label'         =>'1-й слайд',
                'section'       => 'bita_slider',
                'settings'      => 'bita_options[info]',
                'priority'      => 10
            ) )
    );
///////image
    $wp_customize->add_setting( 'slider_image_1', array( 'sanitize_callback' => 'esc_url_raw' ));
    $wp_customize->add_control(
            new WP_Customize_Image_Control( $wp_customize, 'slider_image_1', array(
                    'label'     => 'картинка',
                    'type'      => 'image',
                    'section'   => 'bita_slider',
                    'priority'  => 10,
            )
        )
    );
///////text
    $wp_customize->add_setting( 'slider_text_1', array( 'sanitize_callback' => 'bita_sanitize_text' ));
    $wp_customize->add_control( 'slider_text_1', array(
            'label'             => 'текст',
            'section'           => 'bita_slider',
            'type'              => 'text',
            'priority'          => 10
        )
    );
///////link
    $wp_customize->add_setting( 'slider_link_1', array( 'sanitize_callback' => 'bita_sanitize_text' ));
    $wp_customize->add_control( 'slider_link_1', array(
            'label'             => 'ссылка',
            'section'           => 'bita_slider',
            'type'              => 'text',
            'priority'          => 10
        )
    );

////slide02
    $wp_customize->add_setting( 'bita_options[info]', array(
            'type'              => 'info_control',
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => 'esc_attr',
        )
    );
    $wp_customize->add_control(
            new bita_Info( $wp_customize, 's2', array(
                    'label'     => '2-й слайд',
                    'section'   => 'bita_slider',
                    'settings'  => 'bita_options[info]',
                    'priority'  => 10
                ))
    );
///////image
    $wp_customize->add_setting( 'slider_image_2', array( 'sanitize_callback' => 'esc_url_raw' ));
    $wp_customize->add_control(
            new WP_Customize_Image_Control( $wp_customize, 'slider_image_2', array(
                    'label'     => 'картинка',
                    'type'      => 'image',
                    'section'   => 'bita_slider',
                    'priority'  => 10,
            )
        )
    );
///////text
    $wp_customize->add_setting( 'slider_text_2', array( 'sanitize_callback' => 'bita_sanitize_text' ));
    $wp_customize->add_control(
        'slider_text_2', array(
            'label'             => 'текст',
            'section'           => 'bita_slider',
            'type'              => 'text',
            'priority'          => 10
        )
    );
///////link
    $wp_customize->add_setting( 'slider_link_2', array( 'sanitize_callback' => 'bita_sanitize_text' ));
    $wp_customize->add_control( 'slider_link_2', array(
            'label'             => 'ссылка',
            'section'           => 'bita_slider',
            'type'              => 'text',
            'priority'          => 10
        )
    );



////slide03
    $wp_customize->add_setting('bita_options[info]', array(
            'type'              => 'info_control',
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => 'esc_attr',
        )
    );
    $wp_customize->add_control(
            new bita_Info( $wp_customize, 's3', array(
                    'label'     => '3-й слайд',
                    'section'   => 'bita_slider',
                    'settings'  => 'bita_options[info]',
                    'priority'  => 10
                ) )
    );
///////image
    $wp_customize->add_setting( 'slider_image_3', array( 'sanitize_callback' => 'esc_url_raw' ));
    $wp_customize->add_control(
        new WP_Customize_Image_Control( $wp_customize, 'slider_image_3', array(
                'label'         => 'картинка',
                'type'          => 'image',
                'section'       => 'bita_slider',
                'priority'      => 10,
            )
        )
    );
///////text
    $wp_customize->add_setting( 'slider_text_3', array( 'sanitize_callback' => 'bita_sanitize_text' ));
    $wp_customize->add_control( 'slider_text_3', array(
            'label'             => 'текст',
            'section'           => 'bita_slider',
            'type'              => 'text',
            'priority'          => 10
        )
    );
///////link
    $wp_customize->add_setting( 'slider_link_3', array( 'sanitize_callback' => 'bita_sanitize_text' ));
    $wp_customize->add_control( 'slider_link_3', array(
            'label'             => 'ссылка',
            'section'           => 'bita_slider',
            'type'              => 'text',
            'priority'          => 10
        )
    );
////slide04
    $wp_customize->add_setting( 'bita_options[info]', array(
            'type'              => 'info_control',
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => 'esc_attr',
        )
    );
    $wp_customize->add_control(
            new bita_Info( $wp_customize, 's4', array(
                    'label'     => '4-й слайд',
                'section'       => 'bita_slider',
                'settings'      => 'bita_options[info]',
                'priority'      => 10
        ) )
    );
///////image
    $wp_customize->add_setting( 'slider_image_4', array( 'sanitize_callback' => 'esc_url_raw' ));
    $wp_customize->add_control(
        new WP_Customize_Image_Control( $wp_customize, 'slider_image_4', array(
                'label'         => 'картинка',
                'type'          => 'image',
                'section'       => 'bita_slider',
                'priority'      => 10,
            )
        )
    );
///////text
    $wp_customize->add_setting( 'slider_text_4', array( 'sanitize_callback' => 'bita_sanitize_text' ));
    $wp_customize->add_control( 'slider_text_4', array(
            'label'             => 'текст',
            'section'           => 'bita_slider',
            'type'              => 'text',
            'priority'          => 10
        )
    );
///////link
    $wp_customize->add_setting( 'slider_link_4', array( 'sanitize_callback' => 'bita_sanitize_text' ));
    $wp_customize->add_control( 'slider_link_4', array(
            'label'             => 'ссылка',
            'section'           => 'bita_slider',
            'type'              => 'text',
            'priority'          => 10
        )
    );
////slide05
    $wp_customize->add_setting('bita_options[info]', array(
            'type'              => 'info_control',
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => 'esc_attr',
        )
    );
    $wp_customize->add_control(
            new bita_Info( $wp_customize, 's5', array(
                    'label'     => '5-й слайд',
                    'section'   => 'bita_slider',
                    'settings'  => 'bita_options[info]',
                    'priority'  => 10
        ) )
    );
///////image
    $wp_customize->add_setting( 'slider_image_5', array( 'sanitize_callback' => 'esc_url_raw' ));
    $wp_customize->add_control(
            new WP_Customize_Image_Control( $wp_customize, 'slider_image_5', array(
                    'label'     => 'картинка',
                    'type'      => 'image',
                    'section'   => 'bita_slider',
                    'priority'  => 10,
            )
        )
    );
///////text
    $wp_customize->add_setting( 'slider_text_5', array( 'sanitize_callback' => 'bita_sanitize_text' ));
    $wp_customize->add_control( 'slider_text_5', array(
            'label'             => 'текст',
            'section'           => 'bita_slider',
            'type'              => 'text',
            'priority'          => 10
        )
    );
///////link
    $wp_customize->add_setting( 'slider_link_5', array( 'sanitize_callback' => 'bita_sanitize_text' ));
    $wp_customize->add_control( 'slider_link_5', array(
            'label'             => 'ссылка',
            'section'           => 'bita_slider',
            'type'              => 'text',
            'priority'          => 10
        )
    );

    //end customize
}

add_action( 'customize_register', 'bita_customize_register' );

/* Sanitize Text */
function bita_sanitize_text( $input ){
    return wp_kses_post( force_balance_tags( $input ) );
}
?>