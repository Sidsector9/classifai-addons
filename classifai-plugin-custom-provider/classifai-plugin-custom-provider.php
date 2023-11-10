<?php

use \Classifai\Providers\Provider;
/**
 * Plugin Name: Classifai plugin custom provider
 */

add_action( 'before_classifai_init', function() {
	register_a_new_provider();
} );

function register_a_new_provider() {
	/**
	 * Step 1:
	 *
	 * Register your provider class (TenupAITextGenerator) with Classifai.
	 */
	add_filter( 'classifai_language_processing_service_providers', function( $service_providers ) {
		$service_providers[] = 'TenupAITextGenerator';
		return $service_providers;
	} );

	/**
	 * Step 2:
	 *
	 * Register your provider ID (TenupAITextGenerator::ID) with the Title Generation feature
	 * so that it shows up in the provider <select> setting.
	 */
	add_filter( 'classifai_feature_title_generation_providers', function( $providers ) {
		$providers[ TenupAITextGenerator::ID ] = __( '10up AI Text Generator', 'classifai' );

		return $providers;
	} );

	/**
	 * Step 3:
	 *
	 * If TenupAITextGenerator requires setting data, register the setting key with its default value
	 * with Classifai for the specific feature (i.e.; Title Generation).
	 */
	add_filter( 'classifai_feature_title_generation_get_default_settings', function( $settings ) {
		$settings[ TenupAITextGenerator::ID ] = [
			'10up_text_gen_key' => '',
			'authenticated'     => false,
		];

		return $settings;
	} );


	/**
	 * Step 4:
	 *
	 * Now that the setting key is registered, we can add a settings field.
	 * We are adding a key called "10up_text_gen_key" to the "Title Generation" feature
	 * for the TenupAITextGenerator provider.
	 */
	add_action( 'classifai_feature_title_generation_provider_setup_fields_sections', function( $feature_instance ) {
		$settings = $feature_instance->get_settings( TenupAITextGenerator::ID );

		add_settings_field(
			'10up_text_gen_key',
			$args['label'] ?? esc_html__( 'Text Generator key', 'classifai' ),
			[ $feature_instance, 'render_input' ],
			$feature_instance->get_option_name(),
			$feature_instance->get_option_name() . '_section',
			[
				'option_index'  => TenupAITextGenerator::ID,
				'label_for'     => '10up_text_gen_key',
				'default_value' => $settings[ '10up_text_gen_key' ],
				'input_type'    => 'password',
				'description'   => esc_html__( 'Enter the Text Generator API key' ),
				'class'         => 'classifai-provider-field hidden' . ' provider-scope-' . TenupAITextGenerator::ID, // Important to add this.
			]
		);
	} );

	/**
	 * Step 5:
	 *
	 * Perform sanitization for the settings added by this provider.
	 */
	add_filter( 'classifai_feature_title_generation_sanitize_settings', function( $new_settings, $settings ) {
		if ( isset( $new_settings[ TenupAITextGenerator::ID ] ) ) {
			$new_settings[ TenupAITextGenerator::ID ]['10up_text_gen_key'] = sanitize_text_field( $new_settings[ TenupAITextGenerator::ID ]['10up_text_gen_key'] ?? $settings[ TenupAITextGenerator::ID ]['10up_text_gen_key'] );
			$new_settings[ TenupAITextGenerator::ID ]['authenticated']     = sanitize_text_field( $new_settings[ TenupAITextGenerator::ID ]['authenticated'] ?? $settings[ TenupAITextGenerator::ID ]['authenticated'] );
		}

		return $new_settings;
	}, 10, 2 );

	/**
	 * The TenupAITextGenerator class.
	 *
	 * This class should define the REST callback to your provider.
	 */
	class TenupAITextGenerator extends Provider {
		const ID = '10upai_text_generator';
		public function __construct( $feature_instance ) {
			parent::__construct(
				'10up AI',
				'Text Generator',
				'10upai_text_generator'
			);

			$this->feature_instance = $feature_instance;

			add_action( 'rest_api_init', [ $this, 'register_endpoints' ] );
		}

		/**
		 * Here you can enqueue assets for this provider.
		 * This method is called by the Feature class.
		 */
		public function register() {
			return;
		}


		/**
		 * Here you can register your REST API endpoints.
		 */
		public function register_endpoints() {}

		/**
		 * You can get the settings for this provider by doing the following:
		 */
		public function your_provider_callback() {
			$feature = new \Classifai\Features\TitleGeneration();

			/**
			 * Check if the feature is enabled for the current provider.
			 */
			$is_enabled = $feature->is_feature_enabled();

			if ( $is_enabled ) {
				// Early return or do something else.
			}

			/**
			 * Retrieves all settings (feature and provider level).
			 */
			$settings = $feature->get_settings();

			/**
			 * Retrieves all settings for this provider.
			 */
			$provider_settings = $settings[ static::ID ];

			// Do something with the settings.
		}
	}
}
