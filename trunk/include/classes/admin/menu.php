<?php
	
	
	class hw_admin_menu{
		
		private $pages = array();
		
		/** @var hw_admin_menu_page[] */
		private $_admin_menu_pages = array();
		/** @var hw_admin_submenu_page[] */
		private $_admin_submenu_pages = array();
		/** @var hw_admin_options_page[] */
		private $_admin_option_pages = array();
		/** @var hw_admin_theme_page[] */
		private $_admin_theme_pages = array();
		
		private $_sections = array();
		
		
		/**
		 * Возвращает объект для работы со страницей опций
		 * @param string $slug
		 * @return hw_admin_menu_page
		 */
		public function give_page( $slug ){
			$slug_sanitize = sanitize_file_name( strtolower( $slug ) );
			if( !array_key_exists( $slug_sanitize, $this->_admin_menu_pages ) ){
				$this->_admin_menu_pages[ $slug_sanitize ] = new hw_admin_menu_page( $slug );
				$this->pages[ $slug_sanitize ] = $this->_admin_menu_pages[ $slug_sanitize ];
			}
			return $this->_admin_menu_pages[ $slug_sanitize ];
		}
		
		
		/**
		 * Возвращает объект для работы со страницей опций
		 * @param $slug
		 * @param null $parentSlug
		 * @return hw_admin_submenu_page
		 */
		public function give_subpage( $slug, $parentSlug = null ){
			$slug_sanitize = sanitize_file_name( strtolower( $slug ) );
			if( !array_key_exists( $slug_sanitize, $this->_admin_submenu_pages ) ){
				$this->_admin_submenu_pages[ $slug_sanitize ] = new hw_admin_submenu_page( $slug, $parentSlug );
				$this->pages[ $slug_sanitize ] = $this->_admin_submenu_pages[ $slug_sanitize ];
			}
			return $this->_admin_submenu_pages[ $slug_sanitize ];
		}
		
		
		/**
		 * Возвращает объект для работы со страницей опций
		 * @param $slug
		 * @return hw_admin_options_page
		 */
		public function give_options_page( $slug ){
			$slug_sanitize = sanitize_file_name( strtolower( $slug ) );
			if( !array_key_exists( $slug_sanitize, $this->_admin_option_pages ) ){
				$this->_admin_option_pages[ $slug_sanitize ] = new hw_admin_options_page( $slug );
				$this->pages[ $slug_sanitize ] = $this->_admin_option_pages[ $slug_sanitize ];
			}
			return $this->_admin_option_pages[ $slug_sanitize ];
		}
		
		
		/**
		 * Возвращает объект для работы со страницей опций
		 * @param $slug
		 * @return hw_admin_theme_page
		 */
		public function give_theme_page( $slug ){
			$slug_sanitize = sanitize_file_name( strtolower( $slug ) );
			if( !array_key_exists( $slug_sanitize, $this->_admin_theme_pages ) ){
				$this->_admin_theme_pages[ $slug_sanitize ] = new hw_admin_theme_page( $slug );
				$this->pages[ $slug_sanitize ] = $this->_admin_theme_pages[ $slug_sanitize ];
			}
			return $this->_admin_theme_pages[ $slug_sanitize ];
		}
		
		
		/**
		 * @param $menu_slug
		 * @return bool|hw_admin_menu_abstract
		 */
		public function get( $menu_slug ){
			$menu_slug = sanitize_file_name( strtolower( $menu_slug ) );
			if( array_key_exists( $menu_slug, $this->pages ) ){
				return $this->pages[ $menu_slug ];
			}
			return new hw_admin_menu_abstract();
		}
		
		
		/**
		 * @param $section_slug
		 * @param string $options_slug
		 * @param null|string $section_title
		 * @return hw_admin_menu_section
		 */
		public function give_options( $options_slug = 'options-general.php', $section_slug = '', $section_title = null ){
			$section_slug_sanitize = sanitize_file_name( strtolower( $section_slug ) );
			if( !array_key_exists( $section_slug_sanitize, $this->_sections ) ){
				$section = new hw_admin_menu_section( $section_slug, $options_slug );
				$section->title( $section_title );
				$this->_sections[ $section_slug_sanitize ] = $section;
			}
			return $this->_sections[ $section_slug_sanitize ];
		}
		
		
		/**
		 * @param bool $pages
		 * @param bool $sections
		 * @param bool $subpages
		 * @param bool $options
		 * @param bool $themes
		 * @return hw_admin_menu_abstract[]|hw_admin_menu_section[]
		 */
		public function get_pages( $pages = true, $sections = true, $subpages = true, $options = true, $themes = true ){
			$R = array();
			if( $pages )
				$R = array_merge( $R, $this->_admin_menu_pages );
			if( $subpages )
				$R = array_merge( $R, $this->_admin_submenu_pages );
			if( $options )
				$R = array_merge( $R, $this->_admin_option_pages );
			if( $themes )
				$R = array_merge( $R, $this->_admin_theme_pages );
			if( $sections )
				$R = array_merge( $R, $this->_sections );
			return $R;
		}
		
	}
	
	
	class hw_admin_menu_abstract{
		
		protected $page_title;
		protected $menu_title;
		protected $capability = 'administrator';
		protected $menu_slug = '';
		protected $function_echo;
		
		use hw_inputs_home_functions;
		
		
		public function __construct( $slug = null, $additionData = null ){
			if( !is_null( $slug ) && trim( $slug ) != '' ){
				$slug_sanitize = sanitize_file_name( strtolower( $slug ) );
				$this->menu_slug = $slug_sanitize;
				$this->menu_title = $slug;
				$this->page_title = $slug;
			}
			$this->inputs_home_make( array( 'options', $this->menu_slug ) );
			$this->inputs_name_prepend( $this->menu_slug . '-' );
			$this->init( $additionData );
			add_action( 'admin_menu', array( $this, 'add_action_admin_menu' ) );
			add_action( 'admin_init', array( $this, 'register_setting' ) );
		}
		
		
		protected function init( $additionData ){
		}
		
		
		public function __call( $name, $arguments ){
			switch( $name ){
				case 'add_action_admin_menu':
					$this->add_action_admin_menu();
					break;
				case 'the_page':
					$this->the_page();
					break;
				case 'register_setting':
					foreach( $this->get_fields() as $input ){
						if( $input instanceof hw_input ){
							register_setting( $this->menu_slug, $input->name() );
						}
					}
					break;
			}
		}
		
		
		/**
		 *
		 */
		protected function add_action_admin_menu(){
		}
		
		
		/**
		 * @param null $set
		 * @return hw_admin_menu_abstract|mixed
		 */
		public function inputs_prepend( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}
		
		
		/**
		 * Возвращает / устанавливает значение
		 * @param string|null $set
		 * @return null|string|hw_admin_menu|hw_admin_menu_abstract|hw_admin_menu_page
		 */
		public function page_title( $set = null ){
			if( !is_null( $set ) ){
				$this->page_title = $set;
				return $this;
			}
			return $this->page_title;
		}
		
		
		/**
		 * Возвращает / устанавливает значение
		 * @param null|string $set
		 * @return null|string|hw_admin_menu|hw_admin_menu_abstract|hw_admin_menu_page
		 */
		public function menu_title( $set = null ){
			if( !is_null( $set ) ){
				$this->menu_title = $set;
				return $this;
			}
			return $this->menu_title;
		}
		
		
		/**
		 * Возвращает / устанавливает значение
		 * @param array|string|int|null $set
		 * @return null|string|hw_admin_menu|hw_admin_menu_abstract|hw_admin_menu_page
		 */
		public function capability( $set = null ){
			if( !is_null( $set ) ){
				$this->capability = $set;
				return $this;
			}
			return $this->capability;
		}
		
		
		/**
		 * Возвращает / устанавливает значение
		 * @param string $set
		 * @return null|string|hw_admin_menu|hw_admin_menu_abstract|hw_admin_menu_page
		 */
		public function menu_slug( $set = null ){
			if( !is_null( $set ) ){
				$this->menu_slug = $set;
				return $this;
			}
			return $this->menu_slug;
		}
		
		
		/**
		 * Возвращает / устанавливает функцию
		 * @param callable $set
		 * @return null|string|hw_admin_menu|hw_admin_menu_abstract|hw_admin_menu_page
		 */
		public function function_echo( $set = null ){
			if( !is_null( $set ) ){
				$this->function_echo = $set;
				return $this;
			}
			return $this->function_echo;
		}
		
		
		protected function the_page(){
			if( is_callable( $this->function_echo ) ){
				call_user_func( $this->function_echo );
			} elseif( is_string( $this->function_echo ) ) {
				echo $this->function_echo;
			} elseif( $this->have_fields() ) {
				///set values
				foreach( $this->get_fields() as $field ){
					$field->value( get_option( $field->name(), $field->default_value() ) );
				}
				$form = hiweb()->forms()->give( $this->menu_slug )->template( 'options' )->settings_group( $this->menu_slug )->submit( true )->action( 'options.php' );
				$form->add_fields( $this->get_fields() );
				?>
				<div class="wrap"><h1><?php echo $this->page_title ?></h1><?php
				$form->the();
				?></div><?php
			} else {
				echo '<div class="wrap"><h1>' . $this->page_title . '</h1><div class="notice notice-info"><p>This is empty options page</p><p>Add new field by PHP-code:<br><code>hiweb()->admin()->menu()->give_page("' . $this->menu_slug . '")->add_field("fieldId");</code></p></div></div>';
			}
		}
		
		
	}
	
	
	class hw_admin_menu_page extends hw_admin_menu_abstract{
		
		private $icon_url;
		private $position;
		
		
		protected function add_action_admin_menu(){
			add_menu_page( $this->page_title, $this->menu_title, $this->capability, $this->menu_slug, array(
				$this, 'the_page'
			), $this->icon_url, $this->position );
		}
		
		
		/**
		 * Возвращает / устанавливает значение
		 * @param string $set
		 * @return null|string|hw_admin_menu|hw_admin_menu_page
		 */
		public function icon_url( $set = null ){
			if( !is_null( $set ) ){
				$this->icon_url = $set;
				return $this;
			}
			return $this->icon_url;
		}
		
		
		/**
		 * Возвращает / устанавливает значение
		 * @param string $set
		 * @return null|string|$this
		 */
		public function position( $set = null ){
			if( !is_null( $set ) ){
				$this->position = $set;
				return $this;
			}
			return $this->position;
		}
	}
	
	
	class hw_admin_submenu_page extends hw_admin_menu_abstract{
		
		private $parent_slug;
		
		
		protected function init( $additionData ){
			if( $additionData instanceof hw_admin_menu_abstract ){
				$this->parent_slug = $additionData->menu_slug();
			} else
				$this->parent_slug = $additionData;
		}
		
		
		protected function add_action_admin_menu(){
			add_submenu_page( $this->parent_slug, $this->page_title, $this->menu_title, $this->capability, $this->menu_slug, array(
				$this, 'the_page'
			) );
		}
		
		
		/**
		 * Возвращает / устанавливает значение
		 * @param string $set
		 * @return null|string|$this
		 */
		public function parent_slug( $set = null ){
			if( !is_null( $set ) ){
				$this->parent_slug = $set;
				return $this;
			}
			return $this->parent_slug;
		}
	}
	
	
	class hw_admin_options_page extends hw_admin_menu_abstract{
		
		protected function add_action_admin_menu(){
			add_options_page( $this->page_title, $this->menu_title, $this->capability, $this->menu_slug, array(
				$this, 'the_page'
			) );
		}
		
	}
	
	
	class hw_admin_theme_page extends hw_admin_menu_abstract{
		
		protected function add_action_admin_menu(){
			add_theme_page( $this->page_title, $this->menu_title, $this->capability, $this->menu_slug, array(
				$this, 'the_page'
			) );
		}
	}
	
	
	class hw_admin_menu_section{
		
		private $id = '';
		private $title;
		private $parent_slug;
		private $parent_slug_short;
		private $inputs = array();
		///
		private $pattern_slug = '/options-(.*)(\.php)$/';
		
		
		use hw_inputs_home_functions;
		
		
		public function __construct( $id, $parent_slug = 'options-general.php' ){
			$this->id = sanitize_file_name( strtolower( $id ) );
			if( trim( $this->id ) == '' )
				$this->id = 'hw_admin_menu_sections_' . $parent_slug;
			if( preg_match( $this->pattern_slug, $parent_slug, $math ) > 0 ){
				$this->parent_slug = $parent_slug;
				$this->parent_slug_short = $math[1];
				add_action( 'admin_init', array( $this, 'add_settings_section' ) );
				add_action( 'admin_init', array( $this, 'register_setting' ) );
			}
			$this->inputs_home_make( array( 'options', $id, $parent_slug ) );
		}
		
		
		public function __call( $name, $arguments ){
			switch( $name ){
				case 'register_setting':
					foreach( $this->get_fields() as $input ){
						if( $input instanceof hw_input ){
							register_setting( $this->id, $input->name() );
						}
					}
					break;
				case 'add_settings_section':
					add_settings_section( $this->id, $this->title, array( $this, 'the_fields' ), $this->parent_slug_short ); //todo!!!
					break;
				case 'the_fields':
					$this->the_fields();
					break;
			}
		}
		
		
		/**
		 * @param null|string $set
		 * @return string|null|hw_admin_menu_section
		 */
		public function title( $set = null ){
			if( !is_null( $set ) ){
				$this->{__FUNCTION__} = $set;
				return $this;
			}
			return $this->{__FUNCTION__};
		}
		
		
		protected function the_fields(){
			$form = hiweb()->form( $this->id )->template( 'options' )->settings_group( $this->id );
			foreach($this->get_fields() as $field){
				$field->value( get_option($field->name(), $field->default_value()) );
			}
			$form->add_fields( $this->get_fields() );
			$form->the_noform();
		}
		
	}