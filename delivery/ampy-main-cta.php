<?php
/**
 * Ampy — Main CTA-block (ring-only)
 * Shortcode: [ampy_main_cta]
 *
 * FluentSnippets / WordPress + Bricks-kontext.
 * Ekar blockets markup wrapper-scopad under rot-elementet .ampy-mcta.
 * De låsta .mcta__*-BEM-klasserna behålls under wrappern så den separata
 * ampy-main-cta.css (som du assemblerar) kan scopas mot .ampy-mcta.
 *
 * JS: INGEN. Blocket är helt JS-fritt (puls-ringen är CSS-animation).
 *
 * Data: rubrik, brödtext, telefon (visat + tel:), Google-URL, betyg och
 * Edvin-bilden kommer från shortcode-attribut ELLER ACF (get_field), med
 * sane defaults = den låsta copyn. Self-hostade assets via filtrerbar
 * bas-URL (ampy_main_cta_asset_base_url), aldrig hårdkodad extern domän.
 *
 * @package Ampy
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ampy_main_cta_asset_base_url' ) ) {
	/**
	 * Filtrerbar bas-URL för self-hostade assets.
	 *
	 * @return string
	 */
	function ampy_main_cta_asset_base_url() {
		return apply_filters( 'ampy_main_cta_asset_base_url', '/wp-content/uploads/ampy/' );
	}
}

if ( ! function_exists( 'ampy_main_cta_asset_url' ) ) {
	/**
	 * Löser en asset-referens till en URL.
	 * Absoluta URL:er (http(s):// eller //host) och rot-relativa (/…) lämnas orörda;
	 * ett rent filnamn läggs på bas-URL:en.
	 *
	 * @param string $value Filnamn eller URL.
	 * @return string
	 */
	function ampy_main_cta_asset_url( $value ) {
		$value = trim( (string) $value );

		if ( '' === $value ) {
			return '';
		}

		if ( preg_match( '#^(https?:)?//#i', $value ) || 0 === strpos( $value, '/' ) ) {
			return $value;
		}

		return trailingslashit( ampy_main_cta_asset_base_url() ) . ltrim( $value, '/' );
	}
}

if ( ! function_exists( 'ampy_main_cta_field' ) ) {
	/**
	 * Hämtar ett fältvärde: shortcode-attribut → ACF (get_field) → default.
	 *
	 * @param array  $atts    Sammanfogade shortcode-attribut.
	 * @param string $key     Attribut-nyckel.
	 * @param string $acf_key ACF-fältnyckel.
	 * @param string $default Låst default.
	 * @return string
	 */
	function ampy_main_cta_field( $atts, $key, $acf_key, $default ) {
		if ( isset( $atts[ $key ] ) && '' !== trim( (string) $atts[ $key ] ) ) {
			return (string) $atts[ $key ];
		}

		if ( $acf_key && function_exists( 'get_field' ) ) {
			$acf = get_field( $acf_key );
			if ( is_string( $acf ) && '' !== trim( $acf ) ) {
				return $acf;
			}
		}

		return $default;
	}
}

if ( ! function_exists( 'ampy_main_cta_register_assets' ) ) {
	/**
	 * Registrerar blockets CSS (self-hostad; @font-face för Outfit lever i filen).
	 * Versionshash via filemtime → cache-buster.
	 */
	function ampy_main_cta_register_assets() {
		$css_url  = apply_filters( 'ampy_main_cta_css_url', content_url( 'uploads/ampy/ampy-main-cta.css' ) );
		$css_path = apply_filters( 'ampy_main_cta_css_path', WP_CONTENT_DIR . '/uploads/ampy/ampy-main-cta.css' );

		$ver = ( is_string( $css_path ) && file_exists( $css_path ) ) ? (string) filemtime( $css_path ) : '1.0.0';

		wp_register_style( 'ampy-main-cta', esc_url_raw( $css_url ), array(), $ver );
	}
	add_action( 'wp_enqueue_scripts', 'ampy_main_cta_register_assets' );
}

if ( ! function_exists( 'ampy_main_cta_shortcode' ) ) {
	/**
	 * Renderar Main CTA-blocket.
	 *
	 * @param array $atts Shortcode-attribut.
	 * @return string
	 */
	function ampy_main_cta_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'heading_lead'     => '',
				'heading_accent_1' => '',
				'heading_accent_2' => '',
				'body'             => '',
				'phone_display'    => '',
				'phone_tel'        => '',
				'cta_label'        => '',
				'rating'           => '',
				'google_url'       => '',
				'google_aria'      => '',
				'image'            => '',
				'image_alt'        => '',
				'bgwave'           => '',
				'wave'             => '',
			),
			$atts,
			'ampy_main_cta'
		);

		/* Ladda CSS:en först när blocket faktiskt används (dedupas på handle → multi-instans-säkert). */
		wp_enqueue_style( 'ampy-main-cta' );

		/* ---- Fält (attr → ACF → låst default) ---- */
		$heading_lead     = ampy_main_cta_field( $atts, 'heading_lead', 'ampy_mcta_heading_lead', 'Prata med en elektriker' );
		$heading_accent_1 = ampy_main_cta_field( $atts, 'heading_accent_1', 'ampy_mcta_heading_accent_1', 'inom 60' );
		$heading_accent_2 = ampy_main_cta_field( $atts, 'heading_accent_2', 'ampy_mcta_heading_accent_2', 'sekunder!' );

		$body = ampy_main_cta_field(
			$atts,
			'body',
			'ampy_mcta_body',
			'Känn dig trygg med kunnig hjälp, precis när du behöver den. Prata direkt med en erfaren elektriker som lyssnar på ditt behov och guidar dig till en säker, smidig lösning.'
		);

		$phone_display = ampy_main_cta_field( $atts, 'phone_display', 'ampy_mcta_phone_display', '010-265 79 79' );
		$phone_tel     = ampy_main_cta_field( $atts, 'phone_tel', 'ampy_mcta_phone_tel', '+46102657979' );
		$cta_label     = ampy_main_cta_field( $atts, 'cta_label', 'ampy_mcta_cta_label', 'Ring 010-265 79 79' );

		$rating      = ampy_main_cta_field( $atts, 'rating', 'ampy_mcta_rating', '5,0' );
		$google_url  = ampy_main_cta_field( $atts, 'google_url', 'ampy_mcta_google_url', 'https://www.google.com/maps/place/Ampy/@59.3576299,17.9842061,17z/data=!3m1!4b1!4m6!3m5!1s0x2bec1ce5c4ed9ce9:0xfce1752e84a1bfee!8m2!3d59.3576272!4d17.986781!16s%2Fg%2F11ypjy9rrm' );
		$google_aria = ampy_main_cta_field( $atts, 'google_aria', 'ampy_mcta_google_aria', '5,0 på Google – Ampys betyg, 5 av 5 stjärnor. Läs recensionerna (öppnas i ny flik)' );

		/* Bild: attr/ACF. Stöd för ACF image-array (url) och attachment-ID utöver filnamn/URL. */
		$image_raw = '';
		if ( isset( $atts['image'] ) && '' !== trim( (string) $atts['image'] ) ) {
			$image_raw = (string) $atts['image'];
		} elseif ( function_exists( 'get_field' ) ) {
			$acf_img = get_field( 'ampy_mcta_image' );
			if ( is_array( $acf_img ) && ! empty( $acf_img['url'] ) ) {
				$image_raw = (string) $acf_img['url'];
			} elseif ( is_numeric( $acf_img ) ) {
				$src = wp_get_attachment_image_url( (int) $acf_img, 'full' );
				if ( $src ) {
					$image_raw = $src;
				}
			} elseif ( is_string( $acf_img ) && '' !== trim( $acf_img ) ) {
				$image_raw = $acf_img;
			}
		}
		if ( '' === $image_raw ) {
			$image_raw = 'edvin.webp';
		}
		$image_url = ampy_main_cta_asset_url( $image_raw );
		$image_alt = ampy_main_cta_field( $atts, 'image_alt', 'ampy_mcta_image_alt', 'Edvin, elektriker på Ampy' );

		$bgwave_url = ampy_main_cta_asset_url( ampy_main_cta_field( $atts, 'bgwave', 'ampy_mcta_bgwave', 'Vector-3.svg' ) );
		$wave_url   = ampy_main_cta_asset_url( ampy_main_cta_field( $atts, 'wave', 'ampy_mcta_wave', 'overlay.svg' ) );

		/* Rik text: snäv allowlist (ingen inline-style, inga länkar smugglas in). */
		$rich_allowed = array(
			'br'     => array(),
			'strong' => array(),
			'em'     => array(),
		);
		$body_html = wp_kses( $body, $rich_allowed );

		ob_start();
		?>
		<section class="ampy-mcta">
			<div class="mcta__card">
					<img class="mcta__bgwave" src="<?php echo esc_url( $bgwave_url ); ?>" alt="" aria-hidden="true" width="652" height="273">

					<div class="mcta__text">
						<h2 class="mcta__h"><?php echo esc_html( $heading_lead ); ?> <span class="grad"><?php echo esc_html( $heading_accent_1 ); ?><br class="br-d"> <?php echo esc_html( $heading_accent_2 ); ?></span></h2>
						<p class="mcta__p"><?php echo $body_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sanerad via wp_kses ovan. ?></p>
						<div class="mcta__action">
							<div class="mcta__cta">
								<a class="btn-ring" href="<?php echo esc_url( 'tel:' . $phone_tel, array( 'tel' ) ); ?>">
									<span class="btn-ring__chip" aria-hidden="true">
										<svg width="17" height="17" viewBox="0.45 -0.49 19 20" fill="none"><path d="M11.9 4.2c.8.2 1.5.6 2 1.1.6.6 1 1.3 1.1 2.1M11.9.8c1.6.2 3.1 1 4.2 2.2 1.2 1.2 1.9 2.7 2.1 4.4M17.4 14.1v2.5c0 .5-.2.9-.5 1.2-.3.3-.8.5-1.2.4-2.4-.3-4.8-1.2-6.8-2.6-1.9-1.3-3.5-3-4.8-5-1.3-2.2-2.2-4.6-2.4-7.2 0-.5.1-.9.4-1.2.3-.3.7-.5 1.2-.5h2.4c.8 0 1.5.6 1.6 1.4.1.8.3 1.6.6 2.3.2.6.1 1.3-.4 1.8l-1 1c1.1 2.1 2.8 3.8 4.7 5l1-1c.5-.5 1.2-.6 1.8-.4.7.3 1.5.5 2.3.6.8.1 1.4.8 1.4 1.7Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
									</span>
									<?php echo esc_html( $cta_label ); ?>
								</a>
							</div>
							<a class="g-row" href="<?php echo esc_url( $google_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $google_aria ); ?>">
								<svg class="g-icon" width="17" height="17" viewBox="0 0 48 48" aria-hidden="true"><path fill="#FFC107" d="M43.6 20.1H42V20H24v8h11.3C33.7 32.7 29.2 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.9 1.2 8 3l5.7-5.7C34.2 6.1 29.3 4 24 4 13 4 4 13 4 24s9 20 20 20 20-9 20-20c0-1.3-.1-2.6-.4-3.9z"/><path fill="#FF3D00" d="m6.3 14.7 6.6 4.8C14.7 15.1 19 12 24 12c3.1 0 5.9 1.2 8 3l5.7-5.7C34.2 6.1 29.3 4 24 4 16.3 4 9.7 8.3 6.3 14.7z"/><path fill="#4CAF50" d="M24 44c5.2 0 9.9-2 13.4-5.2l-6.2-5.2C29.2 35.1 26.7 36 24 36c-5.2 0-9.6-3.3-11.3-8l-6.5 5C9.5 39.6 16.2 44 24 44z"/><path fill="#1976D2" d="M43.6 20.1H42V20H24v8h11.3c-.8 2.2-2.2 4.2-4.1 5.6l6.2 5.2C41.4 34.8 44 29.9 44 24c0-1.3-.1-2.6-.4-3.9z"/></svg>
								<span class="g-label"><strong><?php echo esc_html( $rating ); ?></strong> på Google</span>
								<span class="stars" aria-hidden="true">
									<svg viewBox="0 0 24 24"><path d="m12 2 3.1 6.3 6.9 1-5 4.9 1.2 6.9L12 17.8 5.8 21l1.2-6.9-5-4.9 6.9-1z"/></svg>
									<svg viewBox="0 0 24 24"><path d="m12 2 3.1 6.3 6.9 1-5 4.9 1.2 6.9L12 17.8 5.8 21l1.2-6.9-5-4.9 6.9-1z"/></svg>
									<svg viewBox="0 0 24 24"><path d="m12 2 3.1 6.3 6.9 1-5 4.9 1.2 6.9L12 17.8 5.8 21l1.2-6.9-5-4.9 6.9-1z"/></svg>
									<svg viewBox="0 0 24 24"><path d="m12 2 3.1 6.3 6.9 1-5 4.9 1.2 6.9L12 17.8 5.8 21l1.2-6.9-5-4.9 6.9-1z"/></svg>
									<svg viewBox="0 0 24 24"><path d="m12 2 3.1 6.3 6.9 1-5 4.9 1.2 6.9L12 17.8 5.8 21l1.2-6.9-5-4.9 6.9-1z"/></svg>
								</span>
							</a>
						</div>
					</div>

					<figure class="mcta__media">
						<img class="photo" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" width="933" height="1400" fetchpriority="high">
						<img class="mcta__wave" src="<?php echo esc_url( $wave_url ); ?>" alt="" aria-hidden="true" width="277" height="100">
					</figure>
				</div>
		</section>
		<?php
		return trim( ob_get_clean() );
	}
	add_shortcode( 'ampy_main_cta', 'ampy_main_cta_shortcode' );
}
