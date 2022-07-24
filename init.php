<?php
/*
Plugin Name: Goso Slider
Plugin URI: http://gosodesign.com/
Description: Add new 2 styles slider for Authow theme. Activate this plugin, add new slides and go to Appearance > Customize > Featured Slider Options > Choose Goso Slider Style 1 or Goso Slider Style 2 to use
Version: 1.1
Author: GosoDesign
Author URI: http://themeforest.net/user/gosodesign?ref=gosodesign
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Define
 */
define( 'GOSO_SLIDER_DIR', plugin_dir_path( __FILE__ ) );
define( 'GOSO_SLIDER_URL', plugin_dir_url( __FILE__ ) );

/**
 * Goso_Slider_Main_Class Class
 *
 * This class will run main modules of plugin
 */
if ( ! class_exists( 'Goso_Slider_Main_Class' ) ) :

	class Goso_Slider_Main_Class {

		/**
		 * Global plugin version
		 */
		static $version = '1.0';

		/**
		 * Goso_Slider_Main_Class Constructor.
		 *
		 * @access public
		 * @return Goso_Slider_Main_Class
		 * @since  1.0
		 */
		public function __construct() {
			// Include files
			include_once( 'goso-slider-func.php' );

			// Register GosoSlider Post Type
			add_action( 'init', array( $this, 'register_gososlider_post_type' ) );

			// Add gososlider meta
			add_action( 'add_meta_boxes', array( $this, 'gososlider_meta_boxes' ) );

			// Change default column title
			add_filter( 'manage_goso_slider_posts_columns', array( $this, 'goso_slider_modify_table_title' ) );

			// Add columns to manage columns gososlider
			add_filter( 'manage_edit-goso_slider_columns', array( $this, 'add_columns_goso_slider' ) );

			// Custom columns gososlider
			add_action( 'manage_goso_slider_posts_custom_column', array( $this, 'goso_slider_custom_columns' ), 10, 2 );

			// Ajax action to update reorder
			add_action( 'wp_ajax_goso_update_slide_order', array( $this, 'goso_slider_update_order' ) );

			// Reorder default columns gososlider in backend
			add_filter( 'pre_get_posts', array( $this, 'set_goso_slider_admin_order' ) );
		}

		/**
		 * Register GosoSlider Post Type
		 * @since 1.0
		 */
		public function register_gososlider_post_type() {
			$labels = array(
				'name'          => __( 'Slides', 'taxonomy general name', 'gosodesign' ),
				'singular_name' => __( 'Slide', 'gosodesign' ),
				'search_items'  => __( 'Search Slides', 'gosodesign' ),
				'all_items'     => __( 'All Slides', 'gosodesign' ),
				'parent_item'   => __( 'Parent Slide', 'gosodesign' ),
				'edit_item'     => __( 'Edit Slide', 'gosodesign' ),
				'update_item'   => __( 'Update Slide', 'gosodesign' ),
				'add_new_item'  => __( 'Add New Slide', 'gosodesign' ),
				'menu_name'     => __( 'Goso Slider', 'gosodesign' )
			);

			$gososlider_icon = GOSO_SLIDER_URL . '/images/goso-icon.png';

			$args = array(
				'labels'              => $labels,
				'singular_label'      => __( 'Goso Slider', 'gosodesign' ),
				'public'              => false,
				'show_ui'             => true,
				'hierarchical'        => false,
				'menu_position'       => 10,
				'menu_icon'           => $gososlider_icon,
				'exclude_from_search' => true,
				'supports'            => false
			);

			register_post_type( 'goso_slider', $args );
		}

		/**
		 * Add gososlider meta boxes
		 *
		 * @access public
		 * @return void
		 * @since  1.0
		 */
		public function gososlider_meta_boxes() {
			$meta_box = array(
				'id'          => 'gososlider-meta',
				'title'       => __( 'Slide Settings', 'gosodesign' ),
				'description' => __( 'Choose image and fill to fields bellow to save all slide settings', 'gosodesign' ),
				'post_type'   => 'goso_slider',
				'context'     => 'normal',
				'priority'    => 'high',
				'fields'      => array(
					array(
						'name' => __( 'Slide Image', 'gosodesign' ),
						'desc' => __( 'The image should be between 1600px - 2000px in width and have a minimum height of 650px for best results. Click the "Upload" button to begin uploading your image', 'gosodesign' ),
						'id'   => '_goso_slider_image',
						'type' => 'file',
						'std'  => ''
					),
					array(
						'name' => __( 'Slide Title', 'gosodesign' ),
						'desc' => __( 'Fill the slide title', 'gosodesign' ),
						'id'   => '_goso_slider_title',
						'type' => 'text',
						'std'  => ''
					),
					array(
						'name' => __( 'Slide Title Color', 'gosodesign' ),
						'desc' => __( 'Color for Slide Title', 'gosodesign' ),
						'id'   => '_goso_slider_title_color',
						'type' => 'color',
						'std'  => '#ffffff'
					),
					array(
						'name' => __( 'Slide Caption', 'gosodesign' ),
						'desc' => __( 'Fill the slide caption', 'gosodesign' ),
						'id'   => '_goso_slider_caption',
						'type' => 'textarea',
						'std'  => ''
					),
					array(
						'name' => __( 'Slide Caption Color', 'gosodesign' ),
						'desc' => __( 'Color for Slide Caption', 'gosodesign' ),
						'id'   => '_goso_slider_caption_color',
						'type' => 'color',
						'std'  => '#ffffff'
					),
					array(
						'name' => __( 'Button Text', 'gosodesign' ),
						'desc' => __( 'If you would like a button to appear below your slide caption, please fill the text for it here.', 'gosodesign' ),
						'id'   => '_goso_slider_button',
						'type' => 'text',
						'std'  => ''
					),
					array(
						'name' => __( 'Button Background Color', 'gosodesign' ),
						'desc' => __( 'Background for Button', 'gosodesign' ),
						'id'   => '_goso_slider_button_bg',
						'type' => 'color',
						'std'  => '#BF9F5A'
					),
					array(
						'name' => __( 'Button Text Color', 'gosodesign' ),
						'desc' => __( 'Text Color for Button', 'gosodesign' ),
						'id'   => '_goso_slider_button_text_color',
						'type' => 'color',
						'std'  => '#ffffff'
					),
					array(
						'name' => __( 'Button Link', 'gosodesign' ),
						'desc' => __( 'Fill the URL for slide button here.', 'gosodesign' ),
						'id'   => '_goso_slider_button_url',
						'type' => 'text',
						'std'  => ''
					),
					array(
						'name'    => __( 'Slide Alignment', 'gosodesign' ),
						'desc'    => __( 'Select the alignment for your caption and button.', 'gosodesign' ),
						'id'      => '_goso_slide_alignment',
						'type'    => 'select',
						'options' => array(
							'left'   => 'Left',
							'center' => 'Center',
							'right'  => 'Right',
						),
						'std'     => 'center'
					),
					array(
						'name'    => __( 'Elements Animation', 'gosodesign' ),
						'desc'    => __( 'Choose Animation of Slide title, Caption and Button when slide is active', 'gosodesign' ),
						'id'      => '_goso_slide_element_animation',
						'type'    => 'select',
						'options' => array(
							'fadeInUp'    => 'fadeInUp',
							'fadeInDown'  => 'fadeInDown',
							'fadeInLeft'  => 'fadeInLeft',
							'fadeInRight' => 'fadeInRight',
						),
						'std'     => 'fadeInUp'
					)
				)
			);
			
			//$callback = create_function( '$post,$meta_box', 'gososlider_create_meta_box( $post, $meta_box["args"] );' );
			
			function gososlider_metabox_slider_callback( $post, $meta_box ) {
				gososlider_create_meta_box( $post, $meta_box["args"] );
			}
			
			add_meta_box( $meta_box['id'], $meta_box['title'], 'gososlider_metabox_slider_callback', $meta_box['post_type'], $meta_box['context'], $meta_box['priority'], $meta_box );
		}

		/**
		 * Change title default for Actions
		 *
		 * @access public
		 * @return array new $defaults
		 * @since  1.0
		 */
		public function goso_slider_modify_table_title( $defaults ) {
			$defaults['title'] = 'Actions';

			return $defaults;
		}

		/**
		 * Add thumbnail & caption columns
		 *
		 * @access public
		 * @return array $columns
		 * @since  1.0
		 */
		public function add_columns_goso_slider( $columns ) {
			$column_thumbnail = array( 'thumbnail' => 'Thumbnail' );
			$column_caption   = array( 'caption' => 'Caption' );
			$columns          = array_slice( $columns, 0, 1, true ) + $column_thumbnail + array_slice( $columns, 1, null, true );
			$columns          = array_slice( $columns, 0, 2, true ) + $column_caption + array_slice( $columns, 2, null, true );

			return $columns;
		}

		/**
		 * Enqueue media to use choose image in a slide
		 *
		 * @access public
		 * @return void
		 * @since  1.0
		 */
		public function goso_slider_custom_columns( $column, $post_id ) {
			switch ( $column ) {
				case 'thumbnail':
					$thumbnail = get_post_meta( $post_id, '_goso_slider_image', true );

					if ( ! empty( $thumbnail ) ) {
						echo '<a href="' . get_admin_url() . 'post.php?post=' . $post_id . '&action=edit"><img class="slider-thumb" src="' . $thumbnail . '" /></a>';
					}
					else {
						echo '<a href="' . get_admin_url() . 'post.php?post=' . $post_id . '&action=edit"><img class="slider-thumb" src="' . get_template_directory() . '/images/no-thumb.jpg" /></a>' . '<strong><a class="row-title" href="' . get_admin_url() . 'post.php?post=' . $post_id . '&action=edit">' . __( 'No image', 'gosodesign' ) . '</a></strong>';
					}
					break;

				case 'caption':
					$caption = get_post_meta( $post_id, '_goso_slider_caption', true );
					$title   = get_post_meta( $post_id, '_goso_slider_title', true );
					echo '<h2>' . $title . '</h2><p>' . $caption . '</p>';
					break;

				default:
					break;
			}
		}

		/**
		 * Reorder ajax callback
		 * @return void
		 * @since 1.1
		 */
		public function goso_slider_update_order() {
			global $wpdb;

			$post_type = $_POST['postType'];
			$order     = $_POST['order'];

			if ( ! is_array( $order ) || $post_type != 'goso_slider' )
				return;

			foreach ( $order as $menu_order => $post_id ) {
				$post_id    = intval( str_ireplace( 'post-', '', $post_id ) );
				$menu_order = intval( $menu_order );

				wp_update_post( array(
					'ID'         => stripslashes( htmlspecialchars( $post_id ) ),
					'menu_order' => stripslashes( htmlspecialchars( $menu_order ) )
				) );
			}
			die( '1' );
		}

		/**
		 * Order the default goso slider page correctly
		 * @return void
		 * @since 1.0
		 */
		public function set_goso_slider_admin_order( $wp_query ) {
			if ( is_admin() ) {
				$post_type = '';
				if( isset( $wp_query->query['post_type'] ) ):
					$post_type = $wp_query->query['post_type'];
				endif;

				if ( $post_type == 'goso_slider' ) {
					$wp_query->set( 'orderby', 'menu_order' );
					$wp_query->set( 'order', 'ASC' );
				}
			}
		}

	}

	new Goso_Slider_Main_Class();

endif; // End Check if Class Not Exists