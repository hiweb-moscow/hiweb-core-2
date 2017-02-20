<?php


	/**
	 * Class hw_context
	 */
	class hw_context{

		use hw_hidden_methods_props;


		/**
		 * Конвертирует (подготавливает) контекст
		 * @param $context
		 * @return hw_context_current_prepare
		 */
		public function prepare( $context ){
			return new hw_context_current_prepare( $context );
		}


		/**
		 * @return bool
		 */
		public function is_frontend_page(){
			return ( $_SERVER['SCRIPT_FILENAME'] == hiweb()->dir_base . '/index.php' ) && !$this->is_rest_api();
		}


		/**
		 * @return bool
		 */
		public function is_backend_page(){
			return is_admin();
		}


		/**
		 * @return bool
		 */
		public function is_login_page(){
			return array_key_exists( $GLOBALS['pagenow'], array_flip( [
				'wp-login.php', 'wp-register.php'
			] ) );
		}


		/**
		 * @return bool
		 */
		public function is_ajax(){
			return ( defined( 'DOING_AJAX' ) && DOING_AJAX );
		}


		/**
		 * @return bool
		 */
		public function is_rest_api(){
			$dirs = hiweb()->path()->url_info()['dirs_arr'];
			return reset( $dirs ) == 'wp-json';
		}


		public function get_current_page(){
			return $this->get_current_page();
		}


	}


	class hw_context_current_prepare{

		public $source_context = null;
		public $type;
		public $id;
		public $object;


		use hw_hidden_methods_props;


		public function __construct( $context ){
			$this->source_context = $context;
			if( is_bool( $context ) && function_exists( 'get_queried_object' ) ){
				$context = get_queried_object();
			}
			///
			if( is_numeric( $context ) ){
				$context = get_post( $this->id );
			} elseif( is_array( $context ) && count( $context ) == 2 ) {
			} elseif( is_string( $context ) ) {
				$this->type = 'options';
				$this->id = $context;
				///hiweb()->admin()->menu()->get($context); //todo получить объект страницы опций
			}
			///
			if( is_object( $context ) ){
				$this->object = $context;
				switch( get_class( $context ) ){
					case 'WP_Post':
						$this->type = 'post';
						$this->id = $context->ID;
						break;
					case 'WP_Term':
						$this->type = 'term';
						$this->id = $context->term_id;
						break;
					default:
						hiweb()->console()->warn( 'Не предусмотренный тип объекта [' . get_class( $context ) . ']', true );
						break;
				}
			} else {
				hiweb()->console()->warn( [
					'Не удалось определить тип контекста [' . gettype( $this->source_context ) . ']', $this->source_context
				], true );
				return false;
			}
		}


	}