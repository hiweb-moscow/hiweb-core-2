<?php


	class hw_post_types{

		/** @var hw_post_type[] */
		private $types = array();

		use hw_inputs_home_multi_functions;


		public function __construct(){
			///Add metas...
			/*add_action( 'edit_form_top', array( $this, 'add_action_edit_form_top' ) );
			add_action( 'edit_form_before_permalink', array( $this, 'add_action_edit_form_before_permalink' ) );
			add_action( 'edit_form_after_title', array( $this, 'add_action_edit_form_after_title' ) );
			add_action( 'edit_form_after_editor', array( $this, 'add_action_edit_form_after_editor' ) );
			add_action( 'submitpage_box', array( $this, 'add_action_submitpage_box' ) );
			add_action( 'submitpost_box', array( $this, 'add_action_submitpage_box' ) );
			add_action( 'edit_page_form', array( $this, 'add_action_edit_form_advanced' ) );
			add_action( 'edit_form_advanced', array( $this, 'add_action_edit_form_advanced' ) );
			///Save Meta
			add_action( 'save_post', array( $this, 'add_action_save_post' ), 99999, 2 );*/
		}


		public function __call( $name, $arguments ){
			/*switch( $name ){
				case 'add_action_init_create':
					$this->add_action_init_create();
					break;
				case 'add_action_edit_form_top':
					$this->add_action_simple_fields( $arguments[0], 0 );
					break;
				case 'add_action_edit_form_before_permalink':
					$this->add_action_simple_fields( $arguments[0], 1 );
					break;
				case 'add_action_edit_form_after_title':
					$this->add_action_simple_fields( $arguments[0], 2 );
					break;
				case 'add_action_edit_form_after_editor':
					$this->add_action_simple_fields( $arguments[0], 3 );
					break;
				case 'add_action_submitpage_box':
					$this->add_action_simple_fields( $arguments[0], 5 );
					break;
				case 'add_action_edit_form_advanced':
					$this->add_action_simple_fields( $arguments[0], 4 );
					break;
				case 'add_action_save_post':
					$this->add_action_save_post( $arguments[1] );
					break;
			}*/
		}


		/**
		 * Возвращает корневой CPT класс для работы с кастомным типом поста
		 * @param $post_type
		 * @return hw_post_type
		 */
		public function give( $post_type ){
			$post_type_sanit = sanitize_file_name( strtolower( $post_type ) );
			if( !array_key_exists( $post_type_sanit, $this->types ) ){
				$this->types[ $post_type_sanit ] = new hw_post_type( $post_type );
			}
			return $this->types[ $post_type_sanit ];
		}


		/**
		 * @param null $post
		 * @param int  $position
		 * @internal param $args
		 */
		protected function add_action_simple_fields( $post = null, $position = 3 ){
			////
			if( !function_exists( 'get_current_screen' ) )
				return;
			if( get_current_screen()->base != 'post' )
				return;
			$fields = $this->get_fields( $position );
			if( $position == 3 ){
				$fields = array_merge( $fields, $this->get_fields() );
			}
			////
			if( is_array( $fields ) && count( $fields ) > 0 ){
				$inputs = array();
				foreach( $fields as $id => $input ){
					if( $post instanceof WP_Post ){
						$input->value( get_post_meta( $post->ID, $input->id(), true ) );
					}
					$inputs[ $id ] = $input;
				}
				$form = hiweb()->form();
				$form->add_fields( $inputs );
				if( $position == 5 )
					$form->template( 'compact' );
				$form->the_noform();
			}
		}


		/**
		 * @param $post
		 */
		protected function add_action_save_post( $post ){
			foreach( $this->get_fields() as $id => $input ){
				if( array_key_exists( $id, $_POST ) ){
					update_post_meta( $post->ID, $id, $_POST[ $id ] );
				}
			}
		}
	}


	class hw_post_type{

		use hw_inputs_home_multi_functions;

		private $_type;
		/** @var WP_Error|WP_Post_Type */
		private $_object;
		private $_defaults = array(
			'label' => null, 'labels' => array(), 'description' => '', 'public' => true, 'hierarchical' => false, 'exclude_from_search' => null, 'publicly_queryable' => null, 'show_ui' => true, 'show_in_menu' => null, 'show_in_nav_menus' => null, 'show_in_admin_bar' => null, 'menu_position' => null, 'menu_icon' => 'dashicons-sticky', 'capability_type' => 'post', 'capabilities' => array(), 'map_meta_cap' => null, 'supports' => array(), 'register_meta_box_cb' => null, 'taxonomies' => array(),
			'has_archive' => false, 'rewrite' => true, 'query_var' => true, 'can_export' => true, 'delete_with_user' => null, '_builtin' => false, '_edit_link' => 'post.php?post=%d',
		);
		///////PROPS
		private $label;
		private $labels = array();
		private $description;
		private $public;
		private $hierarchical;
		private $exclude_from_search;
		private $publicly_queryable;
		private $show_ui;
		private $show_in_menu;
		private $show_in_nav_menus;
		private $show_in_admin_bar;
		private $menu_position;
		private $menu_icon;
		private $capability_type;
		private $capabilities;
		private $map_meta_cap;
		private $supports;
		private $register_meta_box_cb;
		private $taxonomies;
		private $has_archive;
		private $rewrite;
		private $query_var;
		private $can_export;
		private $delete_with_user;
		private $_builtin;
		private $_edit_link;

		/** @var hw_input */
		private $fields = array();
		/** @var hw_meta_box[] */
		private $meta_boxes = array();
		/** @var  hw_taxonomy[] */
		private $_taxonomies = array();
		/** @var hw_meta_boxes[] */
		private $_meta_boxes = array();


		public function __construct( $post_type ){
			$this->_type = sanitize_file_name( strtolower( $post_type ) );
			$this->label = $post_type;
			$this->set_props();
			add_action( 'init', array( $this, 'add_action_init_create' ), 99999 );
			///Add metas...
			add_action( 'edit_form_top', array( $this, 'add_action_edit_form_top' ) );
			add_action( 'edit_form_before_permalink', array( $this, 'add_action_edit_form_before_permalink' ) );
			add_action( 'edit_form_after_title', array( $this, 'add_action_edit_form_after_title' ) );
			add_action( 'edit_form_after_editor', array( $this, 'add_action_edit_form_after_editor' ) );
			if( $post_type == 'page' )
				add_action( 'submitpage_box', array( $this, 'add_action_submitpage_box' ) ); else
				add_action( 'submitpost_box', array( $this, 'add_action_submitpage_box' ) );
			if( $post_type == 'page' )
				add_action( 'edit_page_form', array( $this, 'add_action_edit_form_advanced' ) ); else
				add_action( 'edit_form_advanced', array( $this, 'add_action_edit_form_advanced' ) );
			///Save Meta
			add_action( 'save_post', array( $this, 'add_action_save_post' ), 99999, 2 );
		}


		/**
		 * @param null $set
		 * @return $this
		 */
		public function description( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param null $set
		 * @return $this
		 */
		public function public_( $set = null ){
			if( !is_null( $set ) ){
				$this->public = $set;
				return $this;
			}
			return $this->public;
		}


		/**
		 * @param null $set
		 * @return $this
		 */
		public function hierarchical( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param null $set
		 * @return $this
		 */
		public function exclude_from_search( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param null $set
		 * @return $this
		 */
		public function publicly_queryable( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param null $set
		 * @return $this
		 */
		public function show_ui( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param null $set
		 * @return $this
		 */
		public function show_in_menu( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param null $set
		 * @return $this
		 */
		public function show_in_nav_menus( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param null $set
		 * @return $this
		 */
		public function show_in_admin_bar( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param null $set
		 * @return $this
		 */
		public function menu_position( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param null $set
		 * @return $this
		 */
		public function menu_icon( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param null $set
		 * @return $this
		 */
		public function capability_type( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param null $set
		 * @return $this
		 */
		public function map_meta_cap( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param null $set
		 * @return $this
		 */
		public function supports( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param null $set
		 * @return $this
		 */
		public function register_meta_box_cb( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param null $set
		 * @return $this
		 */
		public function taxonomies( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param null $set
		 * @return $this
		 */
		public function has_archive( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param null $set
		 * @return $this
		 */
		public function rewrite( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param null $set
		 * @return $this
		 */
		public function query_var( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param null $set
		 * @return $this
		 */
		public function can_export( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param null $set
		 * @return $this
		 */
		public function _edit_link( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param null $set
		 * @return $this
		 */
		public function _builtin( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param null $set
		 * @return $this
		 */
		public function delete_with_user( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}

		///////

		/**
		 * @param null $set
		 * @return $this
		 */
		public function label( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param null $set
		 * @return $this
		 */
		public function labels( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}


		/**
		 * @param string $key
		 * @param string $value
		 * @return $this
		 */
		public function labels_set( $key = 'name', $value = '' ){
			if( is_object( $this->labels ) )
				$this->labels->{$key} = $value; else $this->labels[ $key ] = $value;
			return $this;
		}


		public function __call( $name, $arguments ){
			switch( $name ){
				case 'add_action_init_create':
					$this->add_action_init_create();
					break;
				case 'add_action_edit_form_top':
					$this->add_action_simple_fields( $arguments[0], 0 );
					break;
				case 'add_action_edit_form_before_permalink':
					$this->add_action_simple_fields( $arguments[0], 1 );
					break;
				case 'add_action_edit_form_after_title':
					$this->add_action_simple_fields( $arguments[0], 2 );
					break;
				case 'add_action_edit_form_after_editor':
					$this->add_action_simple_fields( $arguments[0], 3 );
					break;
				case 'add_action_submitpage_box':
					$this->add_action_simple_fields( $arguments[0], 5 );
					break;
				case 'add_action_edit_form_advanced':
					$this->add_action_simple_fields( $arguments[0], 4 );
					break;
				case 'add_action_save_post':
					$this->add_action_save_post( $arguments[1] );
					break;
			}
		}


		/**
		 * @return string
		 */
		public function type(){
			return $this->_type;
		}


		/**
		 * Возвращает массив установок
		 * @return array
		 */
		public function props(){
			$R = array();
			foreach( $this->_defaults as $key => $def_value ){
				$R[ $key ] = ( !property_exists( $this, $key ) || is_null( $this->{$key} ) ) ? $def_value : $this->{$key};
			}
			return $R;
		}


		/**
		 * @return WP_Error|WP_Post_Type
		 */
		public function get(){
			return $this->_object;
		}


		/**
		 * @param string|int              $id
		 * @param hw_post_type_meta_boxes $hiweb_meta_boxes
		 * @return hw_post_type_meta_boxes
		 */
		public function add_meta_box( $id, $hiweb_meta_boxes = null ){
			if( !isset( $this->_meta_boxes[ $id ] ) ){
				if( $hiweb_meta_boxes[ $id ] instanceof hw_post_type_meta_boxes )
					$this->_meta_boxes = $hiweb_meta_boxes; else $this->_meta_boxes[ $id ] = new hw_post_type_meta_boxes( $id );
				$this->_meta_boxes[ $id ]->screen( $this->_type );
			}
			return $this->_meta_boxes[ $id ];
		}


		/**
		 * @param $name
		 * @return hw_taxonomy
		 */
		public function add_taxonomy( $name ){
			if( !isset( $this->_taxonomies[ $name ] ) ){
				$this->_taxonomies[ $name ] = hiweb()->taxonomies()->give( $name );
				$this->_taxonomies[ $name ]->object_type( $this->_type );
			}
			return $this->_taxonomies[ $name ];
		}


		/**
		 * @return hw_post_type_meta_boxes[]
		 */
		public function meta_boxes(){
			return $this->_meta_boxes;
		}


		/**
		 * @param null $post
		 * @param int  $position
		 * @internal param $args
		 */
		protected function add_action_simple_fields( $post = null, $position = 3 ){
			////
			if( !function_exists( 'get_current_screen' ) )
				return;
			if( get_current_screen()->base != 'post' )
				return;
			$fields = $this->get_fields( $position );
			if( $position == 3 ){
				$fields = array_merge( $fields, $this->get_fields() );
			}
			////
			if( is_array( $fields ) && count( $fields ) > 0 ){
				$inputs = array();
				foreach( $fields as $id => $input ){
					if( $post instanceof WP_Post ){
						$input->value( get_post_meta( $post->ID, $input->id(), true ) );
					}
					$inputs[ $id ] = $input;
				}
				$form = hiweb()->form();
				$form->add_fields( $inputs );
				if( $position == 5 )
					$form->template( 'compact' );
				$form->the_noform();
			}
		}


		/**
		 * @param        $id
		 * @param string $type
		 * @param null   $title    - титл поля
		 * @param int    $position - позиция блока полей: 0 - над титлом, 1 - под титлом, 2 - над редатором WYSIWYG, 3 - под редактором WYSIWYG (позиция normal), 4 - внизу (позиция advanced), 5 - позиция вверху сайдбара
		 * @return hw_input|hw_input_checkbox|hw_input_repeat|hw_input_text
		 */
		/*public function add_field( $id, $type = 'text', $title = null, $position = 3 ){
			$input = hiweb()->input( $id, $type );
			$input->title( $title );
			$this->fields[ $position ][ $id ] = $input;
			return $input;
		}*/

		/**
		 * Добавить поле / поля
		 * @param array $fields   - либо hw_input, либо array(hw_input, hw_input)
		 * @param int   $position - позиция блока полей: 0 - над титлом, 1 - под титлом, 2 - над редатором WYSIWYG, 3 - под редактором WYSIWYG (позиция normal), 4 - внизу (позиция advanced), 5 - позиция вверху сайдбара
		 * @return array|bool|hw_input[]
		 */
		/*public function add_fields( $fields = array(), $position = 3 ){
			if( $fields instanceof hw_input ){
				$fields = array( $fields );
			}
			if( !is_array( $fields ) )
				return false;
			foreach( $fields as $id => $field ){
				$this->fields[ $position ][ $id ] = $field;
			}
			return $this->get_fields();
		}*/

		protected function add_action_save_post( $post ){
			foreach( $this->get_fields() as $id => $input ){
				if( array_key_exists( $id, $_POST ) ){
					update_post_meta( $post->ID, $id, $_POST[ $id ] );
				}
			}
		}


		private function set_props(){
			if( post_type_exists( $this->_type ) ){
				$props = (array)get_post_type_object( $this->_type );
				foreach( $props as $key => $val ){
					if( property_exists( $this, $key ) ){
						if( $key != 'label' && $key != 'labels' ){
							$this->{$key} = $val;
						}
					}
				}
			}
		}


		/**
		 * Возвращает все поля для данного типа файлов
		 * @return array|hw_input[]
		 */
		/*public function get_fields(){
			$R = array();
			if( is_array( $this->fields ) )
				foreach( $this->fields as $position => $fields ){
					if( is_array( $fields ) )
						foreach( $fields as $id => $field ){
							if( $field instanceof hw_input ){
								$R[ $field->id() ] = $field;
							}
						}
				}
			return $R;
		}*/

		/**
		 * Процедура регистрации типа поста
		 * @return WP_Error|WP_Post_Type
		 */
		private function add_action_init_create(){
			if( post_type_exists( $this->_type ) ){
				global $wp_post_types, $_wp_post_type_features;

				foreach( $wp_post_types[ $this->_type ] as $key => $val ){
					if( property_exists( $this, $key ) ){
						if( $key == 'label' ){
							$wp_post_types[ $this->_type ]->{$key} = __( $this->{$key} );
						} elseif( $key == 'labels' ) {
							if( is_object( $val ) )
								foreach( $val as $label => $name ){
									if( isset( $this->labels[ $label ] ) ){
										$wp_post_types[ $this->_type ]->{$key}->{$label} = $this->labels[ $label ];
									}
								}
						} else {
							$wp_post_types[ $this->_type ]->{$key} = $this->{$key};
						}
					}
				}
				//If PT exist
				if( is_array( $this->supports ) && count( $this->supports ) > 0 )
					foreach( $_wp_post_type_features[ $this->_type ] as $support => $value ){
						if( !array_key_exists( $support, array_flip( $this->supports ) ) )
							unset( $_wp_post_type_features[ $this->_type ][ $support ] );
					}
			} else {
				//Register PT
				$this->_object = register_post_type( $this->_type, $this->props() );
			}
			return $this->get();
		}


	}


	class hw_post_type_meta_boxes{

		/** @var string */
		protected $_id;
		protected $title = '&nbsp;';
		protected $callback;
		protected $screen = array();
		protected $context = 'normal'; //normal, advanced или side
		protected $priority = 'default';
		protected $callback_args;
		protected $callback_save_post;
		/** @var hw_input[] */
		protected $fields;
		/** @var string */
		protected $fields_prefix = 'hw_wp_meta_boxes_';


		public function __construct( $id ){
			$this->_id = $id;
			$this->my_hooks();
		}


		/**
		 * Возвращает ID текущего мета-бокса
		 * @return string
		 */
		public function id(){
			return $this->_id;
		}


		/**
		 * @param $title
		 * @return $this
		 */
		public function title( $title = null ){
			if( is_null( $title ) )
				return $this->title; else $this->title = $title;
			return $this;
		}


		/**
		 * @param $callback
		 * @return $this
		 */
		public function callback( $callback = null ){
			if( is_null( $callback ) )
				return $this->callback; else $this->callback = $callback;
			return $this;
		}


		/**
		 * @param null $screen
		 * @param bool $append
		 * @return $this
		 */
		public function screen( $screen = null, $append = true ){
			$this->screen = is_array( $this->screen ) ? $this->screen : array( $this->screen );
			if( is_null( $screen ) ){
				return $this->screen;
			} else {
				if( !is_array( $screen ) )
					$screen = array( $screen );
				$this->screen = $append ? $this->screen + $screen : $screen;
			}
			return $this;
		}


		/**
		 * @param null $context
		 * @return $this
		 */
		public function context( $context = null ){
			if( is_null( $context ) )
				return $this->context; else $this->context = $context;
			return $this;
		}


		/**
		 * @param null $priority
		 * @return $this
		 */
		public function priority( $priority = null ){
			if( is_null( $priority ) )
				return $this->priority; else $this->priority = $priority;
			return $this;
		}


		/**
		 * @param null $callback_args
		 * @return $this
		 */
		public function callback_args( $callback_args = null ){
			if( is_null( $callback_args ) )
				return $this->callback_args; else $this->callback_args = $callback_args;
			return $this;
		}


		/**
		 * @param null $callback
		 * @return $this
		 */
		public function callback_save_post( $callback = null ){
			if( is_null( $callback ) )
				return $this->callback_save_post; else $this->callback_save_post = $callback;
			return $this;
		}


		public function __call( $name, $arguments ){
			switch( $name ){
				case 'add_action_add_meta_box':
					$this->add_action_add_meta_box( $arguments[0], isset( $arguments[1] ) ? $arguments[1] : null );
					break;
				case 'add_action_save_post':
					$this->add_action_save_post( $arguments[0] );
					break;
				case 'generate_meta_box':
					$this->generate_meta_box( $arguments[0], $arguments[1] );
					break;
			}
		}


		public function add_field( $id ){
			$this->fields[ $id ] = hiweb()->input()->make( $id );
			return $this->fields[ $id ];
		}


		/**
		 * @return hw_input[]
		 */
		public function fields(){
			return $this->fields;
		}


		/**
		 *
		 */
		protected function my_hooks(){
			add_action( 'add_meta_boxes', array( $this, 'add_action_add_meta_box' ), 10, 2 );
			add_action( 'save_post', array( $this, 'add_action_save_post' ), 10, 2 );
		}


		protected function add_action_add_meta_box( $post_type, $post = null ){
			add_meta_box( $this->_id, $this->title, is_null( $this->callback ) ? array( $this, 'generate_meta_box' ) : $this->callback, $this->screen, $this->context, $this->priority, $this->callback_args );
		}


		protected function add_action_save_post( $post_id = null ){
			if( !is_null( $this->callback_save_post ) )
				return call_user_func( $this->callback_save_post, $post_id ); else {
				if( is_array( $this->fields ) )
					foreach( $this->fields as $id => $field ){
						update_post_meta( $post_id, $field->name(), $_POST[ $field->name() ] );
					}
			}
			return $post_id;
		}


		protected function generate_meta_box( $post, $meta_box ){
			if( is_array( $this->fields ) )
				foreach( $this->fields as $id => $field ){
					if( $post instanceof WP_Post )
						$field->value( get_post_meta( $post->ID, $field->name(), true ) );
					?>
                    <p>
                        <strong><?php echo $field->label(); ?></strong>
                        <label class="screen-reader-text" for="<?php echo $id ?>"><?php echo $field->label() ?></label>
                    </p>
					<?php $field->the();
				} else ?><span>no fields</span><?php
		}
	}