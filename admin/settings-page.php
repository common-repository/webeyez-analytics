<?php
/**
 * Webeyez: Settings page
 *
 * Settings page for Webeyez plugin.
 *
 * @package Webeyez
 */

// $webeyez_user = get_option( 'webeyez_user' );
$webeyez_key = new \Webeyez\Controllers\Base_Controller();
$webeyez_key = $webeyez_key->get_key_cookie();

?>

<div class="webeyez-wrapper">

	<a href="https://www.webeyez.com" class="webeyez-logo" target="_blank"><img
				src="<?php echo esc_js( plugins_url( 'img/logo.png', __FILE__ ) ); ?>" alt=""></a>

	<h2><?php esc_attr_e( 'Welcome to Webeyez Analytics', 'webeyez' ); ?></h2>

	<div class="description-text">
		<p class="webeyez-text"><?php esc_attr_e( 'Thanks for implementing Webeyez, you are one step away from monitoring your business and technical performance.', 'webeyez' ); ?></p>
		<p class="webeyez-text"><?php esc_attr_e( 'Where can I find the key?', 'webeyez' ); ?></p>
		<ul>
			<li><b><?php esc_attr_e( 'Registered customer', 'webeyez' ); ?></b>
				<?php esc_attr_e( ' - Click ', 'webeyez' ); ?><a href="https://portal.webeyez.com/help/implementation/woocommerce"><?php esc_attr_e( 'HERE', 'webeyez' ); ?></a>
				<?php esc_attr_e( 'to copy the client key.', 'webeyez' ); ?>
			</li>
			<li><b><?php esc_attr_e( 'New to Webeyez', 'webeyez' ); ?></b>
				<?php esc_attr_e( ' - Register to Webeyez by clicking ', 'webeyez' ); ?><a href="https://portal.webeyez.com/register"> <?php esc_html_e( 'HERE', 'webeyez' ); ?></a>.
				<?php esc_attr_e( 'After registration ', 'webeyez' ); ?><a href="https://portal.webeyez.com/help/implementation/woocommerce"> <?php esc_html_e( 'COPY', 'webeyez' ); ?></a>
				<?php esc_attr_e( 'to copy the client key.', 'webeyez' ); ?>
			</li>
		</ul>
	</div>

	<div class="forms-container">

		<div class="forms-col">

			<form id="webeyez_update_key" novalidate autocomplete="off" autocorrect="off" autocapitalize="off"
				  spellcheck="false">

				<h4>
					<?php esc_attr_e( 'Save client key', 'webeyez' ); ?>
				</h4>

				<div class="form-row">
					<input type="text" name="webeyez_key" id="webeyez_key" placeholder="<?php translate( 'Client key', 'webeyez' ); ?>" autocomplete="new-client-key" value="<?php echo esc_attr( $webeyez_key ); ?>">
				</div>

				<div class="form-row row-submit">
					<button type="submit" class="webeyez-btn">
						<span><?php esc_attr_e( 'Save key', 'webeyez' ); ?></span>
						<svg class="webeyez-loader" width="14px" height="14px" xmlns="http://www.w3.org/2000/svg"
							 viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" fill="#fff">
							<path ng-attr-d="{{config.pathCmd}}" ng-attr-fill="{{config.color}}" stroke="none"
								  d="M10 50A40 40 0 0 0 90 50A40 46 0 0 1 10 50" transform="rotate(5.598 50 53)">
								<animateTransform attributeName="transform" type="rotate" calcMode="linear"
												  values="0 50 53;360 50 53" keyTimes="0;1" dur="1s" begin="0s"
												  repeatCount="indefinite"></animateTransform>
							</path>
						</svg>
					</button>
					<?php wp_nonce_field( 'webeyez_key', 'webeyez_key_nonce' ); ?>
				</div>

				<div class="form-message"></div>

			</form>

		</div>

	</div>

	<div class="webeyez-copyright">
		<?php esc_attr_e( 'By signing up you agree to our', 'webeyez' ); ?>
		<a href="https://www.webeyez.com/terms-of-service" target="_blank"><?php esc_attr_e( 'Terms of Service', 'webeyez' ); ?></a>
		<?php esc_attr_e( 'and', 'webeyez' ); ?>
		<a href="https://www.webeyez.com/privacy-policy" target="_blank"><?php esc_attr_e( 'Privacy Policy', 'webeyez' ); ?></a>
	</div>

</div>
