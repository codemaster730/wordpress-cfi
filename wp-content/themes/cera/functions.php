<?php
/**
 * Cera functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package cera
 */

define( 'CERA_VERSION', '1.1.14' );


/**
 * BASE
 */

if ( ! defined( 'CERA_GRAY_DARKEST' ) ) {
	define( 'CERA_GRAY_DARKEST', '#252537' );
}

if ( ! defined( 'CERA_GRAY_DARKER' ) ) {
	define( 'CERA_GRAY_DARKER', '#48465b' );
}

if ( ! defined( 'CERA_GRAY_DARK' ) ) {
	define( 'CERA_GRAY_DARK', '#48465b' );
}

if ( ! defined( 'CERA_GRAY' ) ) {
	define( 'CERA_GRAY', '#6c7293' );
}

if ( ! defined( 'CERA_GRAY_LIGHT' ) ) {
	define( 'CERA_GRAY_LIGHT', '#AFB2C1' );
}

if ( ! defined( 'CERA_GRAY_LIGHTER' ) ) {
	define( 'CERA_GRAY_LIGHTER', '#E9EFF3' );
}

if ( ! defined( 'CERA_GRAY_LIGHTEST' ) ) {
	define( 'CERA_GRAY_LIGHTEST', '#f0f3f4' );
}

if ( ! defined( 'CERA_BLACK_FADED' ) ) {
	define( 'CERA_BLACK_FADED', 'rgba(0, 0, 20, 0.05)' );
}

if ( ! defined( 'CERA_BRAND_INFO' ) ) {
	define( 'CERA_BRAND_INFO', '#007BFF' );
}

if ( ! defined( 'CERA_BRAND_SUCCESS' ) ) {
	define( 'CERA_BRAND_SUCCESS', '#0abb87' );
}

if ( ! defined( 'CERA_BRAND_WARNING' ) ) {
	define( 'CERA_BRAND_WARNING', '#ED6E21' );
}

if ( ! defined( 'CERA_BRAND_DANGER' ) ) {
	define( 'CERA_BRAND_DANGER', '#ED2121' );
}

if ( ! defined( 'CERA_BRAND_PRIMARY' ) ) {
	define( 'CERA_BRAND_PRIMARY', '#4E64DD' );
}

if ( ! defined( 'CERA_BRAND_PRIMARY_HOVER' ) ) {
	define( 'CERA_BRAND_PRIMARY_HOVER',  '#2C56C6' );
}

if ( ! defined( 'CERA_BRAND_SECONDARY' ) ) {
	define( 'CERA_BRAND_SECONDARY', '#d8e1f3' );
}

if ( ! defined( 'CERA_BRAND_SECONDARY_HOVER' ) ) {
	define( 'CERA_BRAND_SECONDARY_HOVER', '#4254b9' );
}

if ( ! defined( 'CERA_BODY_COLOR' ) ) {
	define( 'CERA_BODY_COLOR', CERA_GRAY );
}

if ( ! defined( 'CERA_BODY_BACKGROUND' ) ) {
	define( 'CERA_BODY_BACKGROUND', CERA_GRAY_LIGHTEST );
}

if ( ! defined( 'CERA_BACKGROUND_IMAGE' ) ) {
	define( 'CERA_BACKGROUND_IMAGE', '' );
}

if ( ! defined( 'CERA_BACKGROUND_IMAGE_REPEAT' ) ) {
	define( 'CERA_BACKGROUND_IMAGE_REPEAT', 'no-repeat' );
}

if ( ! defined( 'CERA_BACKGROUND_IMAGE_POSITION_X' ) ) {
	define( 'CERA_BACKGROUND_IMAGE_POSITION_X', 'center' );
}

if ( ! defined( 'CERA_BACKGROUND_IMAGE_POSITION_Y' ) ) {
	define( 'CERA_BACKGROUND_IMAGE_POSITION_Y', 'bottom' );
}

if ( ! defined( 'CERA_BACKGROUND_IMAGE_ATTACHMENT' ) ) {
	define( 'CERA_BACKGROUND_IMAGE_ATTACHMENT', 'fixed' );
}

if ( ! defined( 'CERA_BACKGROUND_IMAGE_SIZE' ) ) {
	define( 'CERA_BACKGROUND_IMAGE_SIZE', 'cover' );
}

if ( ! defined( 'CERA_LINK_COLOR' ) ) {
	define( 'CERA_LINK_COLOR', CERA_BRAND_PRIMARY );
}

if ( ! defined( 'CERA_LINK_HOVER_COLOR' ) ) {
	define( 'CERA_LINK_HOVER_COLOR', CERA_BRAND_PRIMARY_HOVER );
}

if ( ! defined( 'CERA_BORDER_RADIUS' ) ) {
	define( 'CERA_BORDER_RADIUS', .3 );
}

if ( ! defined( 'CERA_BORDER_WIDTH' ) ) {
	define( 'CERA_BORDER_WIDTH', 1 );
}


/**
 * TYPOGRAPHY
 */

if ( ! defined( 'CERA_FONT_FAMILY_BASE' ) ) {
	define( 'CERA_FONT_FAMILY_BASE', 'Poppins' );
}

if ( ! defined( 'CERA_FONT_FAMILY_HEADING' ) ) {
	define( 'CERA_FONT_FAMILY_HEADING', CERA_FONT_FAMILY_BASE );
}

if ( ! defined( 'CERA_FONT_FAMILY_DISPLAY_HEADING' ) ) {
	define( 'CERA_FONT_FAMILY_DISPLAY_HEADING', CERA_FONT_FAMILY_HEADING );
}

if ( ! defined( 'CERA_FONT_SIZE_BASE' ) ) {
	define( 'CERA_FONT_SIZE_BASE', '1rem' );
}

if ( ! defined( 'CERA_FONT_SIZE_HEADING1' ) ) {
	define( 'CERA_FONT_SIZE_HEADING1', '2rem' );
}

if ( ! defined( 'CERA_FONT_SIZE_HEADING2' ) ) {
	define( 'CERA_FONT_SIZE_HEADING2', '1.8rem' );
}

if ( ! defined( 'CERA_FONT_SIZE_HEADING3' ) ) {
	define( 'CERA_FONT_SIZE_HEADING3', '1.4rem' );
}

if ( ! defined( 'CERA_FONT_SIZE_HEADING4' ) ) {
	define( 'CERA_FONT_SIZE_HEADING4',  '1.25rem' );
}

if ( ! defined( 'CERA_FONT_SIZE_HEADING5' ) ) {
	define( 'CERA_FONT_SIZE_HEADING5', '1.05rem' );
}

if ( ! defined( 'CERA_FONT_SIZE_HEADING6' ) ) {
	define( 'CERA_FONT_SIZE_HEADING6', '0.95rem' );
}

if ( ! defined( 'CERA_FONT_SIZE_DISPLAY_HEADING1' ) ) {
	define( 'CERA_FONT_SIZE_DISPLAY_HEADING1', '2.85rem' );
}

if ( ! defined( 'CERA_FONT_SIZE_DISPLAY_HEADING2' ) ) {
	define( 'CERA_FONT_SIZE_DISPLAY_HEADING2', '2.15rem' );
}

if ( ! defined( 'CERA_FONT_SIZE_DISPLAY_HEADING3' ) ) {
	define( 'CERA_FONT_SIZE_DISPLAY_HEADING3', '1.85rem' );
}

if ( ! defined( 'CERA_FONT_SIZE_DISPLAY_HEADING4' ) ) {
	define( 'CERA_FONT_SIZE_DISPLAY_HEADING4', '1.4rem' );
}

if ( ! defined( 'CERA_FONT_WEIGHT_NORMAL' ) ) {
	define( 'CERA_FONT_WEIGHT_NORMAL', '400' );
}

if ( ! defined( 'CERA_FONT_WEIGHT_BOLD' ) ) {
	define( 'CERA_FONT_WEIGHT_BOLD', '600' );
}

if ( ! defined( 'CERA_FONT_WEIGHT_HEADING' ) ) {
	define( 'CERA_FONT_WEIGHT_HEADING', CERA_FONT_WEIGHT_BOLD );
}

if ( ! defined( 'CERA_FONT_WEIGHT_DISPLAY_HEADINGS' ) ) {
	define( 'CERA_FONT_WEIGHT_DISPLAY_HEADINGS', CERA_FONT_WEIGHT_BOLD );
}

if ( ! defined( 'CERA_LINE_HEIGHT_BASE' ) ) {
	define( 'CERA_LINE_HEIGHT_BASE', '1.5' );
}

if ( ! defined( 'CERA_LINE_HEIGHT_HEADING' ) ) {
	define( 'CERA_LINE_HEIGHT_HEADING', '1.2' );
}

if ( ! defined( 'CERA_LETTER_SPACING' ) ) {
	define( 'CERA_LETTER_SPACING', '0px' );
}

if ( ! defined( 'CERA_TYPOGRAPHY_TEXT_FONT_FONT_FAMILY' ) ) {
	define( 'CERA_TYPOGRAPHY_TEXT_FONT_FONT_FAMILY', CERA_FONT_FAMILY_BASE );
}

if ( ! defined( 'CERA_TYPOGRAPHY_TEXT_FONT_FONT_WEIGHT' ) ) {
	define( 'CERA_TYPOGRAPHY_TEXT_FONT_FONT_WEIGHT', CERA_FONT_WEIGHT_NORMAL );
}

if ( ! defined( 'CERA_TYPOGRAPHY_TEXT_FONT_FONT_SIZE' ) ) {
	define( 'CERA_TYPOGRAPHY_TEXT_FONT_FONT_SIZE', CERA_FONT_SIZE_BASE );
}

if ( ! defined( 'CERA_TYPOGRAPHY_TEXT_FONT_LINE_HEIGHT' ) ) {
	define( 'CERA_TYPOGRAPHY_TEXT_FONT_LINE_HEIGHT', CERA_LINE_HEIGHT_BASE );
}

if ( ! defined( 'CERA_TYPOGRAPHY_TEXT_FONT_LETTER_SPACING' ) ) {
	define( 'CERA_TYPOGRAPHY_TEXT_FONT_LETTER_SPACING', CERA_LETTER_SPACING );
}

if ( ! defined( 'CERA_TYPOGRAPHY_TEXT_FONT_TEXT_TRANSFORM' ) ) {
	define( 'CERA_TYPOGRAPHY_TEXT_FONT_TEXT_TRANSFORM', 'none' );
}

if ( ! defined( 'CERA_TYPOGRAPHY_TEXT_COLOR' ) ) {
	define( 'CERA_TYPOGRAPHY_TEXT_COLOR', CERA_BODY_COLOR );
}

if ( ! defined( 'CERA_TYPOGRAPHY_TEXT_SELECTION_BACKGROUND_COLOR' ) ) {
	define( 'CERA_TYPOGRAPHY_TEXT_SELECTION_BACKGROUND_COLOR', 'rgba(36, 92, 197, 0.18)' );
}

if ( ! defined( 'CERA_TYPOGRAPHY_HEADING_FONT_FONT_FAMILY' ) ) {
	define( 'CERA_TYPOGRAPHY_HEADING_FONT_FONT_FAMILY', CERA_FONT_FAMILY_HEADING );
}

if ( ! defined( 'CERA_TYPOGRAPHY_HEADING_FONT_FONT_WEIGHT' ) ) {
	define( 'CERA_TYPOGRAPHY_HEADING_FONT_FONT_WEIGHT', CERA_FONT_WEIGHT_HEADING );
}

if ( ! defined( 'CERA_TYPOGRAPHY_HEADING_FONT_LETTER_SPACING' ) ) {
	define( 'CERA_TYPOGRAPHY_HEADING_FONT_LETTER_SPACING', CERA_LETTER_SPACING );
}

if ( ! defined( 'CERA_TYPOGRAPHY_HEADING_FONT_TEXT_TRANSFORM' ) ) {
	define( 'CERA_TYPOGRAPHY_HEADING_FONT_TEXT_TRANSFORM', 'none' );
}

if ( ! defined( 'CERA_TYPOGRAPHY_HEADING_COLOR' ) ) {
	define( 'CERA_TYPOGRAPHY_HEADING_COLOR', CERA_GRAY_DARK );
}

if ( ! defined( 'CERA_TYPOGRAPHY_HEADING1_FONT_FONT_SIZE' ) ) {
	define( 'CERA_TYPOGRAPHY_HEADING1_FONT_FONT_SIZE', CERA_FONT_SIZE_HEADING1 );
}

if ( ! defined( 'CERA_TYPOGRAPHY_HEADING1_FONT_LINE_HEIGHT' ) ) {
	define( 'CERA_TYPOGRAPHY_HEADING1_FONT_LINE_HEIGHT', CERA_LINE_HEIGHT_HEADING );
}

if ( ! defined( 'CERA_TYPOGRAPHY_HEADING2_FONT_FONT_SIZE' ) ) {
	define( 'CERA_TYPOGRAPHY_HEADING2_FONT_FONT_SIZE', CERA_FONT_SIZE_HEADING2 );
}

if ( ! defined( 'CERA_TYPOGRAPHY_HEADING2_FONT_LINE_HEIGHT' ) ) {
	define( 'CERA_TYPOGRAPHY_HEADING2_FONT_LINE_HEIGHT', CERA_LINE_HEIGHT_HEADING );
}

if ( ! defined( 'CERA_TYPOGRAPHY_HEADING3_FONT_FONT_SIZE' ) ) {
	define( 'CERA_TYPOGRAPHY_HEADING3_FONT_FONT_SIZE', CERA_FONT_SIZE_HEADING3 );
}

if ( ! defined( 'CERA_TYPOGRAPHY_HEADING3_FONT_LINE_HEIGHT' ) ) {
	define( 'CERA_TYPOGRAPHY_HEADING3_FONT_LINE_HEIGHT', CERA_LINE_HEIGHT_HEADING );
}

if ( ! defined( 'CERA_TYPOGRAPHY_HEADING4_FONT_FONT_SIZE' ) ) {
	define( 'CERA_TYPOGRAPHY_HEADING4_FONT_FONT_SIZE', CERA_FONT_SIZE_HEADING4 );
}

if ( ! defined( 'CERA_TYPOGRAPHY_HEADING4_FONT_LINE_HEIGHT' ) ) {
	define( 'CERA_TYPOGRAPHY_HEADING4_FONT_LINE_HEIGHT', CERA_LINE_HEIGHT_HEADING );
}

if ( ! defined( 'CERA_TYPOGRAPHY_HEADING5_FONT_FONT_SIZE' ) ) {
	define( 'CERA_TYPOGRAPHY_HEADING5_FONT_FONT_SIZE', CERA_FONT_SIZE_HEADING5 );
}

if ( ! defined( 'CERA_TYPOGRAPHY_HEADING5_FONT_LINE_HEIGHT' ) ) {
	define( 'CERA_TYPOGRAPHY_HEADING5_FONT_LINE_HEIGHT', CERA_LINE_HEIGHT_HEADING );
}

if ( ! defined( 'CERA_TYPOGRAPHY_HEADING6_FONT_FONT_SIZE' ) ) {
	define( 'CERA_TYPOGRAPHY_HEADING6_FONT_FONT_SIZE', CERA_FONT_SIZE_HEADING6 );
}

if ( ! defined( 'CERA_TYPOGRAPHY_HEADING6_FONT_LINE_HEIGHT' ) ) {
	define( 'CERA_TYPOGRAPHY_HEADING6_FONT_LINE_HEIGHT', CERA_LINE_HEIGHT_HEADING );
}

if ( ! defined( 'CERA_TYPOGRAPHY_DISPLAY_HEADING_FONT_FONT_FAMILY' ) ) {
	define( 'CERA_TYPOGRAPHY_DISPLAY_HEADING_FONT_FONT_FAMILY', CERA_FONT_FAMILY_DISPLAY_HEADING );
}

if ( ! defined( 'CERA_TYPOGRAPHY_DISPLAY_HEADING_FONT_FONT_WEIGHT' ) ) {
	define( 'CERA_TYPOGRAPHY_DISPLAY_HEADING_FONT_FONT_WEIGHT', CERA_FONT_WEIGHT_DISPLAY_HEADINGS );
}

if ( ! defined( 'CERA_TYPOGRAPHY_DISPLAY_HEADING_FONT_LETTER_SPACING' ) ) {
	define( 'CERA_TYPOGRAPHY_DISPLAY_HEADING_FONT_LETTER_SPACING', CERA_LETTER_SPACING );
}

if ( ! defined( 'CERA_TYPOGRAPHY_DISPLAY_HEADING_FONT_TEXT_TRANSFORM' ) ) {
	define( 'CERA_TYPOGRAPHY_DISPLAY_HEADING_FONT_TEXT_TRANSFORM', 'none' );
}

if ( ! defined( 'CERA_TYPOGRAPHY_DISPLAY_HEADING_COLOR' ) ) {
	define( 'CERA_TYPOGRAPHY_DISPLAY_HEADING_COLOR', CERA_GRAY_DARK );
}

if ( ! defined( 'CERA_TYPOGRAPHY_DISPLAY_HEADING1_FONT_FONT_SIZE' ) ) {
	define( 'CERA_TYPOGRAPHY_DISPLAY_HEADING1_FONT_FONT_SIZE', CERA_FONT_SIZE_DISPLAY_HEADING1 );
}

if ( ! defined( 'CERA_TYPOGRAPHY_DISPLAY_HEADING1_FONT_LINE_HEIGHT' ) ) {
	define( 'CERA_TYPOGRAPHY_DISPLAY_HEADING1_FONT_LINE_HEIGHT', CERA_LINE_HEIGHT_HEADING );
}

if ( ! defined( 'CERA_TYPOGRAPHY_DISPLAY_HEADING2_FONT_FONT_SIZE' ) ) {
	define( 'CERA_TYPOGRAPHY_DISPLAY_HEADING2_FONT_FONT_SIZE', CERA_FONT_SIZE_DISPLAY_HEADING2 );
}

if ( ! defined( 'CERA_TYPOGRAPHY_DISPLAY_HEADING2_FONT_LINE_HEIGHT' ) ) {
	define( 'CERA_TYPOGRAPHY_DISPLAY_HEADING2_FONT_LINE_HEIGHT', CERA_LINE_HEIGHT_HEADING );
}

if ( ! defined( 'CERA_TYPOGRAPHY_DISPLAY_HEADING3_FONT_FONT_SIZE' ) ) {
	define( 'CERA_TYPOGRAPHY_DISPLAY_HEADING3_FONT_FONT_SIZE', CERA_FONT_SIZE_DISPLAY_HEADING3 );
}

if ( ! defined( 'CERA_TYPOGRAPHY_DISPLAY_HEADING3_FONT_LINE_HEIGHT' ) ) {
	define( 'CERA_TYPOGRAPHY_DISPLAY_HEADING3_FONT_LINE_HEIGHT', CERA_LINE_HEIGHT_HEADING );
}

if ( ! defined( 'CERA_TYPOGRAPHY_DISPLAY_HEADING4_FONT_FONT_SIZE' ) ) {
	define( 'CERA_TYPOGRAPHY_DISPLAY_HEADING4_FONT_FONT_SIZE', CERA_FONT_SIZE_DISPLAY_HEADING4 );
}

if ( ! defined( 'CERA_TYPOGRAPHY_DISPLAY_HEADING4_FONT_LINE_HEIGHT' ) ) {
	define( 'CERA_TYPOGRAPHY_DISPLAY_HEADING4_FONT_LINE_HEIGHT', CERA_LINE_HEIGHT_HEADING );
}

if ( ! defined( 'CERA_TYPOGRAPHY_SUBHEADING_FONT_FONT_FAMILY' ) ) {
	define( 'CERA_TYPOGRAPHY_SUBHEADING_FONT_FONT_FAMILY', CERA_FONT_FAMILY_BASE );
}

if ( ! defined( 'CERA_TYPOGRAPHY_SUBHEADING_FONT_FONT_WEIGHT' ) ) {
	define( 'CERA_TYPOGRAPHY_SUBHEADING_FONT_FONT_WEIGHT', CERA_FONT_WEIGHT_BOLD );
}

if ( ! defined( 'CERA_TYPOGRAPHY_SUBHEADING_FONT_FONT_SIZE' ) ) {
	define( 'CERA_TYPOGRAPHY_SUBHEADING_FONT_FONT_SIZE', '0.9rem' );
}

if ( ! defined( 'CERA_TYPOGRAPHY_SUBHEADING_FONT_LINE_HEIGHT' ) ) {
	define( 'CERA_TYPOGRAPHY_SUBHEADING_FONT_LINE_HEIGHT', CERA_LINE_HEIGHT_BASE );
}

if ( ! defined( 'CERA_TYPOGRAPHY_SUBHEADING_FONT_LETTER_SPACING' ) ) {
	define( 'CERA_TYPOGRAPHY_SUBHEADING_FONT_LETTER_SPACING', '2px' );
}

if ( ! defined( 'CERA_TYPOGRAPHY_SUBHEADING_FONT_TEXT_TRANSFORM' ) ) {
	define( 'CERA_TYPOGRAPHY_SUBHEADING_FONT_TEXT_TRANSFORM', 'uppercase' );
}

if ( ! defined( 'CERA_TYPOGRAPHY_SUBHEADING_COLOR' ) ) {
	define( 'CERA_TYPOGRAPHY_SUBHEADING_COLOR', CERA_GRAY );
}

if ( ! defined( 'CERA_TYPOGRAPHY_LINK_COLOR' ) ) {
	define( 'CERA_TYPOGRAPHY_LINK_COLOR', CERA_LINK_COLOR );
}

if ( ! defined( 'CERA_TYPOGRAPHY_LINK_HOVER_COLOR' ) ) {
	define( 'CERA_TYPOGRAPHY_LINK_HOVER_COLOR', CERA_LINK_HOVER_COLOR );
}

if ( ! defined( 'CERA_TYPOGRAPHY_BLOCKQUOTE_FONT_FONT_FAMILY' ) ) {
	define( 'CERA_TYPOGRAPHY_BLOCKQUOTE_FONT_FONT_FAMILY', CERA_FONT_FAMILY_BASE );
}

if ( ! defined( 'CERA_TYPOGRAPHY_BLOCKQUOTE_FONT_FONT_WEIGHT' ) ) {
	define( 'CERA_TYPOGRAPHY_BLOCKQUOTE_FONT_FONT_WEIGHT', CERA_FONT_WEIGHT_NORMAL );
}

if ( ! defined( 'CERA_TYPOGRAPHY_BLOCKQUOTE_FONT_FONT_SIZE' ) ) {
	define( 'CERA_TYPOGRAPHY_BLOCKQUOTE_FONT_FONT_SIZE', '1.25rem' );
}

if ( ! defined( 'CERA_TYPOGRAPHY_BLOCKQUOTE_FONT_LINE_HEIGHT' ) ) {
	define( 'CERA_TYPOGRAPHY_BLOCKQUOTE_FONT_LINE_HEIGHT', CERA_LINE_HEIGHT_BASE );
}

if ( ! defined( 'CERA_TYPOGRAPHY_BLOCKQUOTE_FONT_LETTER_SPACING' ) ) {
	define( 'CERA_TYPOGRAPHY_BLOCKQUOTE_FONT_LETTER_SPACING', CERA_LETTER_SPACING );
}

if ( ! defined( 'CERA_TYPOGRAPHY_BLOCKQUOTE_FONT_TEXT_TRANSFORM' ) ) {
	define( 'CERA_TYPOGRAPHY_BLOCKQUOTE_FONT_TEXT_TRANSFORM', 'none' );
}

if ( ! defined( 'CERA_TYPOGRAPHY_BLOCKQUOTE_FONT_TEXT_ALIGN' ) ) {
	define( 'CERA_TYPOGRAPHY_BLOCKQUOTE_FONT_TEXT_ALIGN', 'left' );
}

if ( ! defined( 'CERA_TYPOGRAPHY_BLOCKQUOTE_COLOR' ) ) {
	define( 'CERA_TYPOGRAPHY_BLOCKQUOTE_COLOR', '#ffffff' );
}

if ( ! defined( 'CERA_TYPOGRAPHY_BLOCKQUOTE_BACKGROUND_COLOR' ) ) {
	define( 'CERA_TYPOGRAPHY_BLOCKQUOTE_BACKGROUND_COLOR', CERA_BRAND_PRIMARY );
}

if ( ! defined( 'CERA_TYPOGRAPHY_BLOCKQUOTE_ICON_COLOR' ) ) {
	define( 'CERA_TYPOGRAPHY_BLOCKQUOTE_ICON_COLOR', '#ffffff' );
}

if ( ! defined( 'CERA_TYPOGRAPHY_BLOCKQUOTE_BORDER_COLOR' ) ) {
	define( 'CERA_TYPOGRAPHY_BLOCKQUOTE_BORDER_COLOR', CERA_BRAND_PRIMARY );
}

if ( ! defined( 'CERA_TYPOGRAPHY_BLOCKQUOTE_MARGIN' ) ) {
	define( 'CERA_TYPOGRAPHY_BLOCKQUOTE_MARGIN', 2 );
}


/**
 * BUTTONS
 */

if ( ! defined( 'CERA_BUTTON_PRIMARY_COLOR' ) ) {
	define( 'CERA_BUTTON_PRIMARY_COLOR', '#ffffff' );
}

if ( ! defined( 'CERA_BUTTON_PRIMARY_BACKGROUND_COLOR' ) ) {
	define( 'CERA_BUTTON_PRIMARY_BACKGROUND_COLOR', CERA_BRAND_PRIMARY );
}

if ( ! defined( 'CERA_BUTTON_PRIMARY_BORDER_COLOR' ) ) {
	define( 'CERA_BUTTON_PRIMARY_BORDER_COLOR', CERA_BRAND_PRIMARY );
}

if ( ! defined( 'CERA_BUTTON_PRIMARY_HOVER_COLOR' ) ) {
	define( 'CERA_BUTTON_PRIMARY_HOVER_COLOR', CERA_BUTTON_PRIMARY_COLOR );
}

if ( ! defined( 'CERA_BUTTON_PRIMARY_HOVER_BACKGROUND_COLOR' ) ) {
	define( 'CERA_BUTTON_PRIMARY_HOVER_BACKGROUND_COLOR', CERA_BRAND_PRIMARY_HOVER );
}

if ( ! defined( 'CERA_BUTTON_PRIMARY_HOVER_BORDER_COLOR' ) ) {
	define( 'CERA_BUTTON_PRIMARY_HOVER_BORDER_COLOR', CERA_BLACK_FADED );
}

if ( ! defined( 'CERA_BUTTON_SECONDARY_COLOR' ) ) {
	define( 'CERA_BUTTON_SECONDARY_COLOR', CERA_BRAND_PRIMARY );
}

if ( ! defined( 'CERA_BUTTON_SECONDARY_BACKGROUND_COLOR' ) ) {
	define( 'CERA_BUTTON_SECONDARY_BACKGROUND_COLOR', CERA_BRAND_SECONDARY );
}

if ( ! defined( 'CERA_BUTTON_SECONDARY_BORDER_COLOR' ) ) {
	define( 'CERA_BUTTON_SECONDARY_BORDER_COLOR', CERA_BRAND_SECONDARY );
}

if ( ! defined( 'CERA_BUTTON_SECONDARY_HOVER_COLOR' ) ) {
	define( 'CERA_BUTTON_SECONDARY_HOVER_COLOR', CERA_BUTTON_PRIMARY_COLOR );
}

if ( ! defined( 'CERA_BUTTON_SECONDARY_HOVER_BACKGROUND_COLOR' ) ) {
	define( 'CERA_BUTTON_SECONDARY_HOVER_BACKGROUND_COLOR', CERA_BRAND_SECONDARY_HOVER );
}

if ( ! defined( 'CERA_BUTTON_SECONDARY_HOVER_BORDER_COLOR' ) ) {
	define( 'CERA_BUTTON_SECONDARY_HOVER_BORDER_COLOR', CERA_BLACK_FADED );
}

if ( ! defined( 'CERA_BUTTON_LINE_HEIGHT' ) ) {
	define( 'CERA_BUTTON_LINE_HEIGHT', '1.25rem' );
}

if ( ! defined( 'CERA_BUTTON_BORDER_WIDTH' ) ) {
	define( 'CERA_BUTTON_BORDER_WIDTH', 2 );
}

if ( ! defined( 'CERA_BUTTON_BORDER_RADIUS' ) ) {
	define( 'CERA_BUTTON_BORDER_RADIUS', CERA_BORDER_RADIUS );
}

if ( ! defined( 'CERA_BUTTON_PADDING_Y' ) ) {
	define( 'CERA_BUTTON_PADDING_Y', .8 );
}

if ( ! defined( 'CERA_BUTTON_PADDING_X' ) ) {
	define( 'CERA_BUTTON_PADDING_X', 1.25 );
}

if ( ! defined( 'CERA_BUTTON_FONT_LETTER_SPACING' ) ) {
	define( 'CERA_BUTTON_FONT_LETTER_SPACING', '0' );
}

if ( ! defined( 'CERA_BUTTON_FONT_TEXT_TRANSFORM' ) ) {
	define( 'CERA_BUTTON_FONT_TEXT_TRANSFORM', 'none' );
}

if ( ! defined( 'CERA_BUTTON_FONT_SIZE' ) ) {
	define( 'CERA_BUTTON_FONT_SIZE', '15px' );
}

if ( ! defined( 'CERA_BUTTON_FONT_VARIANT' ) ) {
	define( 'CERA_BUTTON_FONT_VARIANT', CERA_FONT_WEIGHT_BOLD );
}


/**
 * SECTIONS
 */

if ( ! defined( 'CERA_SECTION_PADDING_Y' ) ) {
	define( 'CERA_SECTION_PADDING_Y', 5 );
} // %
if ( ! defined( 'CERA_SECTION_BACKGROUND_COLOR' ) ) {
	define( 'CERA_SECTION_BACKGROUND_COLOR', CERA_GRAY_LIGHTEST );
}

if ( ! defined( 'CERA_SECTION_WIDGET_BACKGROUND_COLOR' ) ) {
	define( 'CERA_SECTION_WIDGET_BACKGROUND_COLOR', CERA_GRAY_LIGHTEST );
}


/**
 * NAVIGATION
 */

if ( ! defined( 'CERA_NAVIGATION_FONT_FAMILY' ) ) {
	define( 'CERA_NAVIGATION_FONT_FAMILY', CERA_FONT_FAMILY_BASE );
}

if ( ! defined( 'CERA_NAVIGATION_FONT_WEIGHT' ) ) {
	define( 'CERA_NAVIGATION_FONT_WEIGHT', CERA_FONT_WEIGHT_BOLD );
}

if ( ! defined( 'CERA_NAVIGATION_FONT_SIZE' ) ) {
	define( 'CERA_NAVIGATION_FONT_SIZE', '.9rem' );
}

if ( ! defined( 'CERA_NAVIGATION_LINE_HEIGHT' ) ) {
	define( 'CERA_NAVIGATION_LINE_HEIGHT', '1.25' );
}

if ( ! defined( 'CERA_NAVIGATION_LETTER_SPACING' ) ) {
	define( 'CERA_NAVIGATION_LETTER_SPACING', '0px' );
}

if ( ! defined( 'CERA_NAVIGATION_SUBSETS' ) ) {
	define( 'CERA_NAVIGATION_SUBSETS', array( 'latin-ext' ) );
}

if ( ! defined( 'CERA_NAVIGATION_TEXT_TRANSFORM' ) ) {
	define( 'CERA_NAVIGATION_TEXT_TRANSFORM', 'none' );
}

if ( ! defined( 'CERA_NAVIGATION_PADDING_Y' ) ) {
	define( 'CERA_NAVIGATION_PADDING_Y', 1.8 );
}

if ( ! defined( 'CERA_NAVIGATION_BACKGROUND' ) ) {
	define( 'CERA_NAVIGATION_BACKGROUND', CERA_GRAY_DARKEST );
}

if ( ! defined( 'CERA_NAVIGATION_BORDER_COLOR' ) ) {
	define( 'CERA_NAVIGATION_BORDER_COLOR', CERA_GRAY_LIGHTER );
}

if ( ! defined( 'CERA_NAVIGATION_BORDER_BOTTOM_WIDTH' ) ) {
	define( 'CERA_NAVIGATION_BORDER_BOTTOM_WIDTH', 0 );
}

if ( ! defined( 'CERA_NAVIGATION_BORDER_TOP_WIDTH' ) ) {
	define( 'CERA_NAVIGATION_BORDER_TOP_WIDTH', 0 );
}

if ( ! defined( 'CERA_NAVIGATION_ITEM_COLOR' ) ) {
	define( 'CERA_NAVIGATION_ITEM_COLOR', '#ffffff' );
}

if ( ! defined( 'CERA_NAVIGATION_ITEM_ACTIVE_BACKGROUND_COLOR' ) ) {
	define( 'CERA_NAVIGATION_ITEM_ACTIVE_BACKGROUND_COLOR',  'rgba(0,0,0,.15)' );
}

if ( ! defined( 'CERA_NAVIGATION_ITEM_COLOR_ACTIVE' ) ) {
	define( 'CERA_NAVIGATION_ITEM_COLOR_ACTIVE', '#ffffff' );
}

if ( ! defined( 'CERA_NAVIGATION_SUB_MENU_ITEM_BACKGROUND_COLOR' ) ) {
	define( 'CERA_NAVIGATION_SUB_MENU_ITEM_BACKGROUND_COLOR', CERA_NAVIGATION_BACKGROUND );
}

if ( ! defined( 'CERA_NAVIGATION_SUB_MENU_ITEM_COLOR' ) ) {
	define( 'CERA_NAVIGATION_SUB_MENU_ITEM_COLOR', CERA_NAVIGATION_ITEM_COLOR );
}

if ( ! defined( 'CERA_NAVIGATION_STICK_TO_TOP_BACKGROUND' ) ) {
	define( 'CERA_NAVIGATION_STICK_TO_TOP_BACKGROUND', 'rgba(255,255,255,1)' );
}

if ( ! defined( 'CERA_NAVIGATION_MOBILE_BACKGROUND' ) ) {
	define( 'CERA_NAVIGATION_MOBILE_BACKGROUND', CERA_NAVIGATION_BACKGROUND );
}

if ( ! defined( 'CERA_NAVIGATION_SEARCH_FORM_DISPLAYED' ) ) {
	define( 'CERA_NAVIGATION_SEARCH_FORM_DISPLAYED', true );
}

if ( ! defined( 'CERA_NAVIGATION_LAYOUT' ) ) {
	define( 'CERA_NAVIGATION_LAYOUT', 'fixed-left' );
}

if ( ! defined( 'CERA_NAVIGATION_POSITION' ) ) {
	define( 'CERA_NAVIGATION_POSITION', 'inside-top' );
}

if ( ! defined( 'CERA_NAVIGATION_CONTAINER_LAYOUT' ) ) {
	define( 'CERA_NAVIGATION_CONTAINER_LAYOUT', 'fluid' );
}

if ( ! defined( 'CERA_NAVIGATION_STICK_TO_TOP' ) ) {
	define( 'CERA_NAVIGATION_STICK_TO_TOP', true );
}

if ( ! defined( 'CERA_NAVIGATION_SEARCH_FORM_PLACEHOLDER_COLOR' ) ) {
	define( 'CERA_NAVIGATION_SEARCH_FORM_PLACEHOLDER_COLOR', 'rgba(0, 0, 0, 0.5)' );
}

if ( ! defined( 'CERA_NAVIGATION_SEARCH_FORM_BACKGROUND_COLOR' ) ) {
	define( 'CERA_NAVIGATION_SEARCH_FORM_BACKGROUND_COLOR', 'rgba(255, 255, 255, 0)' );
}

if ( ! defined( 'CERA_NAVIGATION_SEARCH_FORM_COLOR' ) ) {
	define( 'CERA_NAVIGATION_SEARCH_FORM_COLOR', 'rgba(0, 0, 0, 0.7)' );
}

if ( ! defined( 'CERA_NAVIGATION_SEARCH_FORM_ACTIVE_BACKGROUND_COLOR' ) ) {
	define( 'CERA_NAVIGATION_SEARCH_FORM_ACTIVE_BACKGROUND_COLOR', 'rgba(113, 121, 142, 0.8)' );
}

if ( ! defined( 'CERA_NAVIGATION_SEARCH_FORM_ACTIVE_COLOR' ) ) {
	define( 'CERA_NAVIGATION_SEARCH_FORM_ACTIVE_COLOR', '#ffffff' );
}


/**
 * HERO
 */

// @codingStandardsIgnoreStart
$allowed_html = array(
	'em'     => array( 'class' => array() ),
	'i'      => array( 'class' => array() ),
	'b'      => array( 'class' => array() ),
	'strong' => array( 'class' => array() ),
	'ins'    => array( 'class' => array() ),
	'del'    => array( 'class' => array() ),
	'span'   => array( 'class' => array() ),
	'br'     => array( 'class' => array() ),
	'a'      => array( 'href'  => array(), 'title' => array(), 'class' => array() ),
);
// @codingStandardsIgnoreEnd

if ( ! defined( 'CERA_HERO_TITLE' ) ) {
	define( 'CERA_HERO_TITLE', wp_kses( __( 'Intranet & Extranet, <br/>the simple way', 'cera' ), $allowed_html ) );
}

if ( ! defined( 'CERA_HERO_SUBTITLE' ) ) {
	define( 'CERA_HERO_SUBTITLE', wp_kses( __( 'Take full advantage of an intranet or extranet <br/>platform for your business. In a few steps.', 'cera' ), $allowed_html ) );
}

if ( ! defined( 'CERA_HERO_TEXT' ) ) {
	define( 'CERA_HERO_TEXT', wp_kses( "", $allowed_html ) );
}

if ( ! defined( 'CERA_HERO_TITLE_FONT_SIZE' ) ) {
	define( 'CERA_HERO_TITLE_FONT_SIZE', '3.3rem' );
}

if ( ! defined( 'CERA_HERO_LAYOUT' ) ) {
	define( 'CERA_HERO_LAYOUT', '6-6-cols-left-reverse' );
}

if ( ! defined( 'CERA_HERO_CONTAINER_LAYOUT' ) ) {
	define( 'CERA_HERO_CONTAINER_LAYOUT', 'classic' );
}

if ( ! defined( 'CERA_HERO_FULL_SCREEN_DISPLAYED' ) ) {
	define( 'CERA_HERO_FULL_SCREEN_DISPLAYED', false );
}

if ( ! defined( 'CERA_HERO_TITLE_FONT_FAMILY' ) ) {
	define( 'CERA_HERO_TITLE_FONT_FAMILY', CERA_FONT_FAMILY_DISPLAY_HEADING );
}

if ( ! defined( 'CERA_HERO_TITLE_FONT_WEIGHT' ) ) {
	define( 'CERA_HERO_TITLE_FONT_WEIGHT', 600 );
}

if ( ! defined( 'CERA_HERO_TITLE_LINE_HEIGHT' ) ) {
	define( 'CERA_HERO_TITLE_LINE_HEIGHT', CERA_LINE_HEIGHT_HEADING );
}

if ( ! defined( 'CERA_HERO_TITLE_LETTER_SPACING' ) ) {
	define( 'CERA_HERO_TITLE_LETTER_SPACING', 0 );
}

if ( ! defined( 'CERA_HERO_TITLE_TEXT_TRANSFORM' ) ) {
	define( 'CERA_HERO_TITLE_TEXT_TRANSFORM', 'none' );
}

if ( ! defined( 'CERA_HERO_TITLE_COLOR' ) ) {
	define( 'CERA_HERO_TITLE_COLOR', CERA_GRAY_DARKEST );
}

if ( ! defined( 'CERA_HERO_TITLE_DISPLAYED' ) ) {
	define( 'CERA_HERO_TITLE_DISPLAYED', true );
}

if ( ! defined( 'CERA_HERO_SUBTITLE_FONT_FAMILY' ) ) {
	define( 'CERA_HERO_SUBTITLE_FONT_FAMILY', CERA_FONT_FAMILY_BASE );
}

if ( ! defined( 'CERA_HERO_SUBTITLE_FONT_WEIGHT' ) ) {
	define( 'CERA_HERO_SUBTITLE_FONT_WEIGHT', CERA_FONT_WEIGHT_NORMAL );
}

if ( ! defined( 'CERA_HERO_SUBTITLE_FONT_SIZE' ) ) {
	define( 'CERA_HERO_SUBTITLE_FONT_SIZE', '1.1rem' );
}

if ( ! defined( 'CERA_HERO_SUBTITLE_LINE_HEIGHT' ) ) {
	define( 'CERA_HERO_SUBTITLE_LINE_HEIGHT', CERA_LINE_HEIGHT_BASE );
}

if ( ! defined( 'CERA_HERO_SUBTITLE_LETTER_SPACING' ) ) {
	define( 'CERA_HERO_SUBTITLE_LETTER_SPACING', 0 );
}

if ( ! defined( 'CERA_HERO_SUBTITLE_TEXT_TRANSFORM' ) ) {
	define( 'CERA_HERO_SUBTITLE_TEXT_TRANSFORM', 'none' );
}

if ( ! defined( 'CERA_HERO_SUBTITLE_DISPLAYED' ) ) {
	define( 'CERA_HERO_SUBTITLE_DISPLAYED', true );
}

if ( ! defined( 'CERA_HERO_TEXT_FONT_FAMILY' ) ) {
	define( 'CERA_HERO_TEXT_FONT_FAMILY', CERA_FONT_FAMILY_BASE );
}

if ( ! defined( 'CERA_HERO_TEXT_FONT_WEIGHT' ) ) {
	define( 'CERA_HERO_TEXT_FONT_WEIGHT', '600' );
}

if ( ! defined( 'CERA_HERO_TEXT_FONT_SIZE' ) ) {
	define( 'CERA_HERO_TEXT_FONT_SIZE', '.9em' );
}

if ( ! defined( 'CERA_HERO_TEXT_LINE_HEIGHT' ) ) {
	define( 'CERA_HERO_TEXT_LINE_HEIGHT', CERA_LINE_HEIGHT_BASE );
}

if ( ! defined( 'CERA_HERO_TEXT_LETTER_SPACING' ) ) {
	define( 'CERA_HERO_TEXT_LETTER_SPACING', 0 );
}

if ( ! defined( 'CERA_HERO_TEXT_TRANSFORM' ) ) {
	define( 'CERA_HERO_TEXT_TRANSFORM', 'none' );
}

if ( ! defined( 'CERA_HERO_TEXT_DISPLAYED' ) ) {
	define( 'CERA_HERO_TEXT_DISPLAYED', false );
}

if ( ! defined( 'CERA_HERO_BUTTON_DISPLAYED' ) ) {
	define( 'CERA_HERO_BUTTON_DISPLAYED', true );
}

if ( ! defined( 'CERA_HERO_BACKGROUND_GRADIENT_DISPLAYED' ) ) {
	define( 'CERA_HERO_BACKGROUND_GRADIENT_DISPLAYED', false );
}

if ( ! defined( 'CERA_HERO_BACKGROUND_GRADIENT_FIRST_COLOR' ) ) {
	define( 'CERA_HERO_BACKGROUND_GRADIENT_FIRST_COLOR', CERA_BRAND_PRIMARY );
}

if ( ! defined( 'CERA_HERO_BACKGROUND_GRADIENT_SECOND_COLOR' ) ) {
	define( 'CERA_HERO_BACKGROUND_GRADIENT_SECOND_COLOR', 'rgba(36, 93, 198, 0)' );
}

if ( ! defined( 'CERA_HERO_BACKGROUND_GRADIENT_DIRECTION' ) ) {
	define( 'CERA_HERO_BACKGROUND_GRADIENT_DIRECTION', '-80deg' );
}

if ( ! defined( 'CERA_HERO_BACKGROUND_GRADIENT_POSITION' ) ) {
	define( 'CERA_HERO_BACKGROUND_GRADIENT_POSITION', '10' );
}

if ( ! defined( 'CERA_HERO_BACKGROUND' ) ) {
	define( 'CERA_HERO_BACKGROUND', 'rgba(0,0,0,0)' );
}

if ( ! defined( 'CERA_HERO_SUBTITLE_COLOR' ) ) {
	define( 'CERA_HERO_SUBTITLE_COLOR', CERA_GRAY );
}

if ( ! defined( 'CERA_HERO_TEXT_COLOR' ) ) {
	define( 'CERA_HERO_TEXT_COLOR', 'rgba(255,255,255,0.85)' );
}

if ( ! defined( 'CERA_HERO_PADDING_Y' ) ) {
	define( 'CERA_HERO_PADDING_Y', 8 );
}

if ( ! defined( 'CERA_HERO_BACKGROUND_SECONDARY' ) ) {
	define( 'CERA_HERO_BACKGROUND_SECONDARY', 'rgba(255,255,255,0)' );
}

if ( ! defined( 'CERA_HERO_COLOR_SCHEME' ) ) {
	define( 'CERA_HERO_COLOR_SCHEME', 'none' );
}

if ( ! defined( 'CERA_HERO_BUTTON_COLOR' ) ) {
	define( 'CERA_HERO_BUTTON_COLOR', '#ffffff' );
}

if ( ! defined( 'CERA_HERO_BUTTON_BACKGROUND_COLOR' ) ) {
	define( 'CERA_HERO_BUTTON_BACKGROUND_COLOR', CERA_BRAND_PRIMARY );
}

if ( ! defined( 'CERA_HERO_BUTTON_BORDER_COLOR' ) ) {
	define( 'CERA_HERO_BUTTON_BORDER_COLOR', CERA_HERO_BUTTON_BACKGROUND_COLOR );
}

if ( ! defined( 'CERA_HERO_BUTTON_HOVER_BACKGROUND_COLOR' ) ) {
	define( 'CERA_HERO_BUTTON_HOVER_BACKGROUND_COLOR', CERA_BRAND_PRIMARY_HOVER );
}

if ( ! defined( 'CERA_HERO_BUTTON_HOVER_COLOR' ) ) {
	define( 'CERA_HERO_BUTTON_HOVER_COLOR', CERA_HERO_BUTTON_COLOR );
}

if ( ! defined( 'CERA_HERO_BUTTON_HOVER_BORDER_COLOR' ) ) {
	define( 'CERA_HERO_BUTTON_HOVER_BORDER_COLOR', CERA_BRAND_PRIMARY_HOVER );
}

/**
 * HEADER
 */

if ( ! defined( 'CERA_HEADER_PADDING_Y' ) ) {
	define( 'CERA_HEADER_PADDING_Y', 4 );
}

if ( ! defined( 'CERA_BIG_HEADER_PADDING_Y' ) ) {
	define( 'CERA_BIG_HEADER_PADDING_Y', 4 );
}

if ( ! defined( 'CERA_HEADER_BACKGROUND' ) ) {
	define( 'CERA_HEADER_BACKGROUND', 'rgba(37, 37, 56, .75)' );
}

if ( ! defined( 'CERA_CUSTOM_HEADER_LAYOUT' ) ) {
	define( 'CERA_CUSTOM_HEADER_LAYOUT', '12-cols-center' );
}

if ( ! defined( 'CERA_CUSTOM_HEADER_CONTAINER_LAYOUT' ) ) {
	define( 'CERA_CUSTOM_HEADER_CONTAINER_LAYOUT', 'narrow' );
}

if ( ! defined( 'CERA_CUSTOM_HEADER_BACKGROUND_COLOR' ) ) {
	define( 'CERA_CUSTOM_HEADER_BACKGROUND_COLOR', CERA_HEADER_BACKGROUND );
}


/**
 * CONTENT
 */

if ( ! defined( 'CERA_CONTENT_PADDING_Y' ) ) {
	define( 'CERA_CONTENT_PADDING_Y', 3 );
}

if ( ! defined( 'CERA_CONTENT_BACKGROUND' ) ) {
	define( 'CERA_CONTENT_BACKGROUND', CERA_GRAY_LIGHTEST );
}

if ( ! defined( 'CERA_WRAPPER_LAYOUT' ) ) {
	define( 'CERA_WRAPPER_LAYOUT', 'classic' );
}


/**
 * PREFOOTER
 */

if ( ! defined( 'CERA_PREFOOTER_BACKGROUND_IMAGE' ) ) {
	define( 'CERA_PREFOOTER_BACKGROUND_IMAGE', '' );
}

if ( ! defined( 'CERA_PREFOOTER_LAYOUT' ) ) {
	define( 'CERA_PREFOOTER_LAYOUT', '4-4-4-cols-left' );
}

if ( ! defined( 'CERA_PREFOOTER_CONTAINER_LAYOUT' ) ) {
	define( 'CERA_PREFOOTER_CONTAINER_LAYOUT', 'fluid' );
}

if ( ! defined( 'CERA_PREFOOTER_PADDING_Y' ) ) {
	define( 'CERA_PREFOOTER_PADDING_Y', 4 );
}

if ( ! defined( 'CERA_PREFOOTER_MOBILE_DISPLAYED' ) ) {
	define( 'CERA_PREFOOTER_MOBILE_DISPLAYED', true );
}

if ( ! defined( 'CERA_PREFOOTER_BACKGROUND_COLOR' ) ) {
	define( 'CERA_PREFOOTER_BACKGROUND_COLOR', '#ffffff' );
}

if ( ! defined( 'CERA_PREFOOTER_HEADING_COLOR' ) ) {
	define( 'CERA_PREFOOTER_HEADING_COLOR', CERA_GRAY_DARK );
}

if ( ! defined( 'CERA_PREFOOTER_COLOR' ) ) {
	define( 'CERA_PREFOOTER_COLOR', CERA_GRAY_LIGHT );
}

if ( ! defined( 'CERA_PREFOOTER_LINK_COLOR' ) ) {
	define( 'CERA_PREFOOTER_LINK_COLOR', CERA_GRAY_LIGHT );
}

if ( ! defined( 'CERA_PREFOOTER_LINK_HOVER_COLOR' ) ) {
	define( 'CERA_PREFOOTER_LINK_HOVER_COLOR', CERA_GRAY );
}

if ( ! defined( 'CERA_PREFOOTER_BORDER_TOP_COLOR' ) ) {
	define( 'CERA_PREFOOTER_BORDER_TOP_COLOR', CERA_GRAY_LIGHTER );
}

if ( ! defined( 'CERA_PREFOOTER_BORDER_TOP_WIDTH' ) ) {
	define( 'CERA_PREFOOTER_BORDER_TOP_WIDTH', 1 );
}

if ( ! defined( 'CERA_PREFOOTER_BORDER_BOTTOM_COLOR' ) ) {
	define( 'CERA_PREFOOTER_BORDER_BOTTOM_COLOR', CERA_GRAY_LIGHTER );
}

if ( ! defined( 'CERA_PREFOOTER_BORDER_BOTTOM_WIDTH' ) ) {
	define( 'CERA_PREFOOTER_BORDER_BOTTOM_WIDTH', 0 );
}


/**
 * FOOTER
 */

if ( ! defined( 'CERA_FOOTER_BACKGROUND_IMAGE' ) ) {
	define( 'CERA_FOOTER_BACKGROUND_IMAGE', '' );
}

if ( ! defined( 'CERA_FOOTER_LAYOUT' ) ) {
	define( 'CERA_FOOTER_LAYOUT', '6-6-cols-left-right' );
}

if ( ! defined( 'CERA_FOOTER_CONTAINER_LAYOUT' ) ) {
	define( 'CERA_FOOTER_CONTAINER_LAYOUT', 'fluid' );
}

if ( ! defined( 'CERA_FOOTER_PADDING_Y' ) ) {
	define( 'CERA_FOOTER_PADDING_Y', 2 );
}

if ( ! defined( 'CERA_FOOTER_MOBILE_DISPLAYED' ) ) {
	define( 'CERA_FOOTER_MOBILE_DISPLAYED', true );
}

if ( ! defined( 'CERA_FOOTER_BACKGROUND_COLOR' ) ) {
	define( 'CERA_FOOTER_BACKGROUND_COLOR', CERA_GRAY_DARK );
}

if ( ! defined( 'CERA_FOOTER_HEADING_COLOR' ) ) {
	define( 'CERA_FOOTER_HEADING_COLOR', '#FFFFFF' );
}

if ( ! defined( 'CERA_FOOTER_COLOR' ) ) {
	define( 'CERA_FOOTER_COLOR', CERA_GRAY_LIGHTER );
}

if ( ! defined( 'CERA_FOOTER_LINK_COLOR' ) ) {
	define( 'CERA_FOOTER_LINK_COLOR', CERA_GRAY_LIGHTER );
}

if ( ! defined( 'CERA_FOOTER_LINK_HOVER_COLOR' ) ) {
	define( 'CERA_FOOTER_LINK_HOVER_COLOR', '#FFFFFF' );
}

if ( ! defined( 'CERA_FOOTER_BORDER_TOP_WIDTH' ) ) {
	define( 'CERA_FOOTER_BORDER_TOP_WIDTH', 0 );
}

if ( ! defined( 'CERA_FOOTER_BORDER_TOP_COLOR' ) ) {
	define( 'CERA_FOOTER_BORDER_TOP_COLOR', '#292e2f' );
}

if ( ! defined( 'CERA_FOOTER_BORDER_BOTTOM_WIDTH' ) ) {
	define( 'CERA_FOOTER_BORDER_BOTTOM_WIDTH', 0 );
}

if ( ! defined( 'CERA_FOOTER_BORDER_BOTTOM_COLOR' ) ) {
	define( 'CERA_FOOTER_BORDER_BOTTOM_COLOR', 'rgba(255,255,255,0)' );
}


/**
 * ARCHIVES
 */

if ( ! defined( 'CERA_CARD_BACKGROUND' ) ) {
	define( 'CERA_CARD_BACKGROUND', '#ffffff' );
}

if ( ! defined( 'CERA_CARD_PADDING' ) ) {
	define( 'CERA_CARD_PADDING', 25 );
}

if ( ! defined( 'CERA_CARD_MARGIN' ) ) {
	define( 'CERA_CARD_MARGIN', 15 );
}

if ( ! defined( 'CERA_CARD_BORDER_RADIUS' ) ) {
	define( 'CERA_CARD_BORDER_RADIUS', CERA_BORDER_RADIUS );
}

if ( ! defined( 'CERA_CARD_BORDER_WIDTH' ) ) {
	define( 'CERA_CARD_BORDER_WIDTH', 0 );
}

if ( ! defined( 'CERA_CARD_BORDER_COLOR' ) ) {
	define( 'CERA_CARD_BORDER_COLOR', 'rgba(0,0,0,0)' );
}

if ( ! defined( 'CERA_BOX_SHADOW_X_OFFSET' ) ) {
	define( 'CERA_BOX_SHADOW_X_OFFSET', 0 );
}

if ( ! defined( 'CERA_BOX_SHADOW_Y_OFFSET' ) ) {
	define( 'CERA_BOX_SHADOW_Y_OFFSET', 0 );
}

if ( ! defined( 'CERA_BOX_SHADOW_BLUR_RADIUS' ) ) {
	define( 'CERA_BOX_SHADOW_BLUR_RADIUS', 15 );
}

if ( ! defined( 'CERA_BOX_SHADOW_SPREAD_RADIUS' ) ) {
	define( 'CERA_BOX_SHADOW_SPREAD_RADIUS', 0 );
}

if ( ! defined( 'CERA_BOX_SHADOW_COLOR' ) ) {
	define( 'CERA_BOX_SHADOW_COLOR', 'rgba(82, 63, 105, .05)' );
}

if ( ! defined( 'CERA_CARD_COLOR' ) ) {
	define( 'CERA_CARD_COLOR', CERA_GRAY );
}

if ( ! defined( 'CERA_CARD_TITLE_COLOR' ) ) {
	define( 'CERA_CARD_TITLE_COLOR', CERA_GRAY_DARK );
}

if ( ! defined( 'CERA_CARD_LINK_COLOR' ) ) {
	define( 'CERA_CARD_LINK_COLOR', CERA_CARD_COLOR );
}

if ( ! defined( 'CERA_CARD_LINK_HOVER_COLOR' ) ) {
	define( 'CERA_CARD_LINK_HOVER_COLOR', CERA_LINK_HOVER_COLOR );
}

if ( ! defined( 'CERA_ARCHIVE_LAYOUT' ) ) {
	define( 'CERA_ARCHIVE_LAYOUT', '12-cols-left' );
}

if ( ! defined( 'CERA_ARCHIVE_POSTS_LAYOUT' ) ) {
	define( 'CERA_ARCHIVE_POSTS_LAYOUT', '4-4-4-cols-classic' );
}

if ( ! defined( 'CERA_ARCHIVE_CONTAINER_LAYOUT' ) ) {
	define( 'CERA_ARCHIVE_CONTAINER_LAYOUT', 'fluid' );
}

if ( ! defined( 'CERA_ARCHIVE_POSTS_HEIGHT_EQUALIZED' ) ) {
	define( 'CERA_ARCHIVE_POSTS_HEIGHT_EQUALIZED', false );
}

if ( ! defined( 'CERA_ARCHIVE_CUSTOM_HEADER_LAYOUT' ) ) {
	define( 'CERA_ARCHIVE_CUSTOM_HEADER_LAYOUT', '6-6-cols-left-reverse' );
}

if ( ! defined( 'CERA_ARCHIVE_CUSTOM_HEADER_CONTAINER_LAYOUT' ) ) {
	define( 'CERA_ARCHIVE_CUSTOM_HEADER_CONTAINER_LAYOUT', 'fluid' );
}


/**
 * CONTROLS
 */

if ( ! defined( 'CERA_CONTROL_BACKGROUND_COLOR' ) ) {
	define( 'CERA_CONTROL_BACKGROUND_COLOR', CERA_BODY_BACKGROUND );
}

if ( ! defined( 'CERA_CONTROL_COLOR' ) ) {
	define( 'CERA_CONTROL_COLOR', CERA_GRAY_DARK );
}

if ( ! defined( 'CERA_CONTROL_PLACEHOLDER_COLOR' ) ) {
	define( 'CERA_CONTROL_PLACEHOLDER_COLOR', CERA_GRAY );
}

if ( ! defined( 'CERA_CONTROL_BORDER_COLOR' ) ) {
	define( 'CERA_CONTROL_BORDER_COLOR', 'rgba(255,255,255,0)' );
}

if ( ! defined( 'CERA_CONTROL_FOCUS_BACKGROUND_COLOR' ) ) {
	define( 'CERA_CONTROL_FOCUS_BACKGROUND_COLOR', CERA_BODY_BACKGROUND );
}

if ( ! defined( 'CERA_CONTROL_FOCUS_COLOR' ) ) {
	define( 'CERA_CONTROL_FOCUS_COLOR', CERA_GRAY_DARK );
}

if ( ! defined( 'CERA_CONTROL_FOCUS_BORDER_COLOR' ) ) {
	define( 'CERA_CONTROL_FOCUS_BORDER_COLOR', CERA_BODY_BACKGROUND );
}

if ( ! defined( 'CERA_CONTROL_BORDER_WIDTH' ) ) {
	define( 'CERA_CONTROL_BORDER_WIDTH', '0' );
}

if ( ! defined( 'CERA_CONTROL_BORDER_RADIUS' ) ) {
	define( 'CERA_CONTROL_BORDER_RADIUS', CERA_BUTTON_BORDER_RADIUS );
}


/**
 * PAGINATION
 */

if ( ! defined( 'CERA_PAGINATION_BACKGROUND_COLOR' ) ) {
	define( 'CERA_PAGINATION_BACKGROUND_COLOR', '#ffffff' );
}

if ( ! defined( 'CERA_PAGINATION_HOVER_BACKGROUND_COLOR' ) ) {
	define( 'CERA_PAGINATION_HOVER_BACKGROUND_COLOR', CERA_BRAND_PRIMARY );
}

if ( ! defined( 'CERA_PAGINATION_COLOR' ) ) {
	define( 'CERA_PAGINATION_COLOR', CERA_GRAY_DARK );
}

if ( ! defined( 'CERA_PAGINATION_HOVER_COLOR' ) ) {
	define( 'CERA_PAGINATION_HOVER_COLOR', '#ffffff' );
}

if ( ! defined( 'CERA_PAGINATION_BORDER_COLOR' ) ) {
	define( 'CERA_PAGINATION_BORDER_COLOR', CERA_GRAY_LIGHTER );
}

if ( ! defined( 'CERA_PAGINATION_HOVER_BORDER_COLOR' ) ) {
	define( 'CERA_PAGINATION_HOVER_BORDER_COLOR', CERA_PAGINATION_HOVER_BACKGROUND_COLOR );
}


/**
 * 404
 */

if ( ! defined( 'CERA_404_PADDING_Y' ) ) {
	define( 'CERA_404_PADDING_Y', 0 );
}

if ( ! defined( 'CERA_404_FULL_SCREEN_DISPLAYED' ) ) {
	define( 'CERA_404_FULL_SCREEN_DISPLAYED', true );
}

if ( ! defined( 'CERA_404_BACKGROUND_COLOR' ) ) {
	define( 'CERA_404_BACKGROUND_COLOR', '#ffffff' );
}

if ( ! defined( 'CERA_404_THUMBNAIL' ) ) {
	define( 'CERA_404_THUMBNAIL', get_stylesheet_directory_uri() . '/assets/images/pages/page-404.jpg' );
}

if ( ! defined( 'CERA_404_TITLE' ) ) {
	define( 'CERA_404_TITLE', esc_html__( '404', 'cera' ) );
}

if ( ! defined( 'CERA_404_TITLE_COLOR' ) ) {
	define( 'CERA_404_TITLE_COLOR', CERA_GRAY_DARK );
}

if ( ! defined( 'CERA_404_TITLE_FORMAT' ) ) {
	define( 'CERA_404_TITLE_FORMAT', 'display-1' );
}

if ( ! defined( 'CERA_404_SUBTITLE' ) ) {
	define( 'CERA_404_SUBTITLE', esc_html__( 'Page not found.', 'cera' ) );
}

if ( ! defined( 'CERA_404_SUBTITLE_COLOR' ) ) {
	define( 'CERA_404_SUBTITLE_COLOR', CERA_GRAY );
}

if ( ! defined( 'CERA_404_TEXT' ) ) {
	define( 'CERA_404_TEXT', esc_html__( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'cera' ) );
}

if ( ! defined( 'CERA_404_TEXT_COLOR' ) ) {
	define( 'CERA_404_TEXT_COLOR', CERA_GRAY );
}

if ( ! defined( 'CERA_404_BUTTON_DISPLAYED' ) ) {
	define( 'CERA_404_BUTTON_DISPLAYED', true );
}

if ( ! defined( 'CERA_404_BUTTON_COLOR' ) ) {
	define( 'CERA_404_BUTTON_COLOR', CERA_BUTTON_PRIMARY_COLOR );
}

if ( ! defined( 'CERA_404_BUTTON_BACKGROUND_COLOR' ) ) {
	define( 'CERA_404_BUTTON_BACKGROUND_COLOR', CERA_BUTTON_PRIMARY_BACKGROUND_COLOR );
}

if ( ! defined( 'CERA_404_BUTTON_BORDER_COLOR' ) ) {
	define( 'CERA_404_BUTTON_BORDER_COLOR', CERA_404_BUTTON_BACKGROUND_COLOR );
}

if ( ! defined( 'CERA_404_BUTTON_HOVER_BACKGROUND_COLOR' ) ) {
	define( 'CERA_404_BUTTON_HOVER_BACKGROUND_COLOR', CERA_BUTTON_PRIMARY_HOVER_BACKGROUND_COLOR );
}

if ( ! defined( 'CERA_404_BUTTON_HOVER_COLOR' ) ) {
	define( 'CERA_404_BUTTON_HOVER_COLOR', CERA_BUTTON_PRIMARY_COLOR );
}

if ( ! defined( 'CERA_404_BUTTON_HOVER_BORDER_COLOR' ) ) {
	define( 'CERA_404_BUTTON_HOVER_BORDER_COLOR', CERA_404_BUTTON_HOVER_BACKGROUND_COLOR );
}

if ( ! defined( 'CERA_404_LAYOUT' ) ) {
	define( 'CERA_404_LAYOUT', '6-6-cols-left' );
}

if ( ! defined( 'CERA_404_CONTAINER_LAYOUT' ) ) {
	define( 'CERA_404_CONTAINER_LAYOUT', 'fluid' );
}


/**
 * LOADER
 */

if ( ! defined( 'CERA_LOADER_COLOR' ) ) {
	define( 'CERA_LOADER_COLOR', CERA_BRAND_PRIMARY );
}

if ( ! defined( 'CERA_LOADER_BACKGROUND_COLOR' ) ) {
	define( 'CERA_LOADER_BACKGROUND_COLOR', '#ffffff' );
}


/**
 * BUDDYPRESS
 */

if ( ! defined( 'CERA_BUTTON_ACTION_BACKGROUND_COLOR' ) ) {
	define( 'CERA_BUTTON_ACTION_BACKGROUND_COLOR', '#ffffff' );
}

if ( ! defined( 'CERA_BUTTON_ACTION_LOVE_COLOR' ) ) {
	define( 'CERA_BUTTON_ACTION_LOVE_COLOR', CERA_BRAND_PRIMARY );
}

if ( ! defined( 'CERA_BUTTON_ACTION_MESSAGE_COLOR' ) ) {
	define( 'CERA_BUTTON_ACTION_MESSAGE_COLOR', '#DF1D5A' );
}

if ( ! defined( 'CERA_BUTTON_ACTION_SUCCESS_COLOR' ) ) {
	define( 'CERA_BUTTON_ACTION_SUCCESS_COLOR', CERA_BRAND_SUCCESS );
}

if ( ! defined( 'CERA_BUTTON_ACTION_DANGER_COLOR' ) ) {
	define( 'CERA_BUTTON_ACTION_DANGER_COLOR', CERA_BRAND_DANGER );
}

if ( ! defined( 'CERA_BUTTON_ACTION_MISC_COLOR' ) ) {
	define( 'CERA_BUTTON_ACTION_MISC_COLOR', CERA_GRAY_DARK );
}

if ( ! defined( 'CERA_DEFAULT_PROFILE_COVER_IMAGE' ) ) {
	define( 'CERA_DEFAULT_PROFILE_COVER_IMAGE', get_stylesheet_directory_uri() . '/assets/images/covers/member-cover.jpg' );
}

if ( ! defined( 'CERA_DEFAULT_GROUP_COVER_IMAGE' ) ) {
	define( 'CERA_DEFAULT_GROUP_COVER_IMAGE', get_stylesheet_directory_uri() . '/assets/images/covers/group-cover.jpg' );
}

if ( ! defined( 'CERA_PROFILE_HEADER_BACKGROUND_COLOR' ) ) {
	define( 'CERA_PROFILE_HEADER_BACKGROUND_COLOR', 'rgba(37, 37, 55, 0.45)' );
}


/**
 * THE EVENTS CALENDAR
 */

if ( ! defined( 'CERA_THE_EVENTS_CALENDAR_TITLE' ) ) {
	define( 'CERA_THE_EVENTS_CALENDAR_TITLE', esc_html__( 'Community Events', 'cera' ) );
}

if ( ! defined( 'CERA_THE_EVENTS_CALENDAR_DESCRIPTION' ) ) {
	define( 'CERA_THE_EVENTS_CALENDAR_DESCRIPTION', esc_html__( 'Take part in our special community events to meet new people or just have fun!', 'cera' ) );
}

if ( ! defined( 'CERA_THE_EVENTS_CALENDAR_CUSTOM_HEADER_DISPLAYED' ) ) {
	define( 'CERA_THE_EVENTS_CALENDAR_CUSTOM_HEADER_DISPLAYED', true );
}

if ( ! defined( 'CERA_THE_EVENTS_CALENDAR_CUSTOM_HEADER_LAYOUT' ) ) {
	define( 'CERA_THE_EVENTS_CALENDAR_CUSTOM_HEADER_LAYOUT', '6-6-cols-left-reverse' );
}

if ( ! defined( 'CERA_THE_EVENTS_CALENDAR_CUSTOM_HEADER_CONTAINER_LAYOUT' ) ) {
	define( 'CERA_THE_EVENTS_CALENDAR_CUSTOM_HEADER_CONTAINER_LAYOUT', 'fluid' );
}

if ( ! defined( 'CERA_THE_EVENTS_CALENDAR_CUSTOM_HEADER_BACKGROUND_IMAGE' ) ) {
	define( 'CERA_THE_EVENTS_CALENDAR_CUSTOM_HEADER_BACKGROUND_IMAGE', get_stylesheet_directory_uri() . '/assets/images/pages/header-default-events.jpg' );
}

if ( ! defined( 'CERA_THE_EVENTS_CALENDAR_CUSTOM_HEADER_PADDING_Y' ) ) {
	define( 'CERA_THE_EVENTS_CALENDAR_CUSTOM_HEADER_PADDING_Y', CERA_HEADER_PADDING_Y );
}

if ( ! defined( 'CERA_THE_EVENTS_CALENDAR_CONTENT_PADDING_Y' ) ) {
	define( 'CERA_THE_EVENTS_CALENDAR_CONTENT_PADDING_Y', CERA_CONTENT_PADDING_Y );
}

if ( ! defined( 'CERA_THE_EVENTS_CALENDAR_CONTAINER_LAYOUT' ) ) {
	define( 'CERA_THE_EVENTS_CALENDAR_CONTAINER_LAYOUT', 'fluid' );
}

if ( ! defined( 'CERA_ARCHIVE_THE_EVENTS_CALENDAR_LAYOUT' ) ) {
	define( 'CERA_ARCHIVE_THE_EVENTS_CALENDAR_LAYOUT', '9-3-cols-left' );
}

if ( ! defined( 'CERA_SINGLE_THE_EVENTS_CALENDAR_LAYOUT' ) ) {
	define( 'CERA_SINGLE_THE_EVENTS_CALENDAR_LAYOUT', '12-cols-left' );
}

/**
 * WOOCOMMERCE
 */

if ( ! defined( 'CERA_ARCHIVE_PRODUCT_CUSTOM_HEADER_LAYOUT' ) ) {
	define( 'CERA_ARCHIVE_PRODUCT_CUSTOM_HEADER_LAYOUT', '12-cols-left' );
}

if ( ! defined( 'CERA_ARCHIVE_PRODUCT_CUSTOM_HEADER_CONTAINER_LAYOUT' ) ) {
	define( 'CERA_ARCHIVE_PRODUCT_CUSTOM_HEADER_CONTAINER_LAYOUT', 'fluid' );
}

if ( ! defined( 'CERA_ARCHIVE_PRODUCT_CONTAINER_LAYOUT' ) ) {
	define( 'CERA_ARCHIVE_PRODUCT_CONTAINER_LAYOUT', 'fluid' );
}

if ( ! defined( 'CERA_SINGLE_PRODUCT_CONTAINER_LAYOUT' ) ) {
	define( 'CERA_SINGLE_PRODUCT_CONTAINER_LAYOUT', 'fluid' );
}

/**
 * LEARNDASH
 */

if ( ! defined( 'CERA_LEARNDASH_LAYOUT' ) ) {
	define( 'CERA_LEARNDASH_LAYOUT', '9-3-cols-left' );
}

if ( ! defined( 'CERA_LEARNDASH_CONTAINER_LAYOUT' ) ) {
	define( 'CERA_LEARNDASH_CONTAINER_LAYOUT', 'fluid' );
}

if ( ! defined( 'CERA_LEARNDASH_CUSTOM_HEADER_LAYOUT' ) ) {
	define( 'CERA_LEARNDASH_CUSTOM_HEADER_LAYOUT', '12-cols-left' );
}

if ( ! defined( 'CERA_LEARNDASH_CUSTOM_HEADER_CONTAINER_LAYOUT' ) ) {
	define( 'CERA_LEARNDASH_CUSTOM_HEADER_CONTAINER_LAYOUT', 'fluid' );
}

/**
 * BBPRESS
 */

if ( ! defined( 'CERA_ARCHIVE_FORUM_TITLE' ) ) {
	define( 'CERA_ARCHIVE_FORUM_TITLE', esc_html__( 'Welcome to the Forum', 'cera' ) );
}

if ( ! defined( 'CERA_ARCHIVE_FORUM_DESCRIPTION' ) ) {
	define( 'CERA_ARCHIVE_FORUM_DESCRIPTION', esc_html__( 'Share your thoughts on several topics like lifestyle, social and leisure!', 'cera' ) );
}

if ( ! defined( 'CERA_ARCHIVE_FORUM_LAYOUT' ) ) {
	define( 'CERA_ARCHIVE_FORUM_LAYOUT', '9-3-cols-left' );
}

if ( ! defined( 'CERA_ARCHIVE_FORUM_CUSTOM_HEADER_BACKGROUND_IMAGE' ) ) {
	define( 'CERA_ARCHIVE_FORUM_CUSTOM_HEADER_BACKGROUND_IMAGE', get_stylesheet_directory_uri() . '/assets/images/pages/header-default-forum.jpg' );
}

if ( ! defined( 'CERA_ARCHIVE_FORUM_CUSTOM_HEADER_PADDING_Y' ) ) {
	define( 'CERA_ARCHIVE_FORUM_CUSTOM_HEADER_PADDING_Y', CERA_HEADER_PADDING_Y );
}

if ( ! defined( 'CERA_ARCHIVE_FORUM_CONTENT_PADDING_Y' ) ) {
	define( 'CERA_ARCHIVE_FORUM_CONTENT_PADDING_Y', CERA_CONTENT_PADDING_Y );
}

if ( ! defined( 'CERA_ARCHIVE_FORUM_CUSTOM_HEADER_DISPLAYED' ) ) {
	define( 'CERA_ARCHIVE_FORUM_CUSTOM_HEADER_DISPLAYED', true );
}

if ( ! defined( 'CERA_ARCHIVE_FORUM_CUSTOM_HEADER_LAYOUT' ) ) {
	define( 'CERA_ARCHIVE_FORUM_CUSTOM_HEADER_LAYOUT', '12-cols-left' );
}

if ( ! defined( 'CERA_ARCHIVE_FORUM_CUSTOM_HEADER_CONTAINER_LAYOUT' ) ) {
	define( 'CERA_ARCHIVE_FORUM_CUSTOM_HEADER_CONTAINER_LAYOUT', 'fluid' );
}

if ( ! defined( 'CERA_ARCHIVE_FORUM_CONTAINER_LAYOUT' ) ) {
	define( 'CERA_ARCHIVE_FORUM_CONTAINER_LAYOUT', 'fluid' );
}


/**
 * Require plugins for this theme
 */
require get_template_directory() . '/libs/tgm-plugin-activation/class-tgm-plugin-activation.php';
global $cera_tgm_plugin_activation;
$cera_tgm_plugin_activation = require get_template_directory() . '/inc/tgm-plugin-activation/class-cera-tgm-plugin-activation.php';

/**
 * Load Merlin
 */
require get_template_directory() . '/libs/merlin/vendor/autoload.php';
require get_template_directory() . '/libs/merlin/class-merlin.php';
require get_template_directory() . '/libs/themosaurus-merlin/class-themosaurus-merlin.php';
global $cera_merlin;
$cera_merlin = require get_template_directory() . '/inc/merlin/class-cera-merlin.php';

/**
 * Initialize all the things.
 */
global $cera;
$cera = require get_template_directory() . '/inc/class-cera.php';

/**
 * Custom template hooks and functions for this theme.
 */
require get_template_directory() . '/inc/template-functions.php';
require get_template_directory() . '/inc/template-hooks.php';

/**
 * Plugins integration.
 */

if ( class_exists( 'Grimlock' ) ) {
	global $cera_grimlock;
	$cera_grimlock = require get_template_directory() . '/inc/grimlock/class-cera-grimlock.php';
	require get_template_directory() . '/inc/grimlock/grimlock-template-functions.php';
	require get_template_directory() . '/inc/grimlock/grimlock-template-hooks.php';
}

if ( class_exists( 'Grimlock_Hero' ) ) {
	global $cera_grimlock_hero;
	$cera_grimlock_hero = require get_template_directory() . '/inc/grimlock-hero/class-cera-grimlock-hero.php';
}

if ( class_exists( 'Grimlock_The_Events_Calendar' ) ) {
	global $cera_grimlock_the_events_calendar;
	$cera_grimlock_the_events_calendar = require get_template_directory() . '/inc/grimlock-the-events-calendar/class-cera-grimlock-the-events-calendar.php';
}

if ( class_exists( 'Jetpack' ) ) {
	require get_template_directory() . '/inc/jetpack/jetpack-template-hooks.php';
}

if ( function_exists( 'wp_pagenavi' ) ) {
	require get_template_directory() . '/inc/wp-pagenavi/wp-pagenavi-template-functions.php';
	require get_template_directory() . '/inc/wp-pagenavi/wp-pagenavi-template-hooks.php';
}

if ( function_exists( 'yoast_breadcrumb' ) ) {
	require get_template_directory() . '/inc/wordpress-seo/wordpress-seo-template-functions.php';
	require get_template_directory() . '/inc/wordpress-seo/wordpress-seo-template-hooks.php';
}

if ( class_exists( 'Grimlock_WordPress_SEO' ) ) {
	global $cera_grimlock_wordpress_seo;
	$cera_grimlock_wordpress_seo = require get_template_directory() . '/inc/grimlock-wordpress-seo/class-cera-grimlock-wordpress-seo.php';
}

if ( class_exists( 'Projects' ) ) {
	require get_template_directory() . '/inc/projects-by-woothemes/projects-by-woothemes-template-functions.php';
	require get_template_directory() . '/inc/projects-by-woothemes/projects-by-woothemes-template-hooks.php';
}

if ( class_exists( 'Menu_Image_Plugin' ) ) {
	global $cera_menu_image;
	$cera_menu_image = require get_template_directory() . '/inc/menu-image/class-cera-menu-image.php';
}

if ( class_exists( 'WPMI' ) ) {
	global $cera_wp_menu_icons;
	$cera_wp_menu_icons = require get_template_directory() . '/inc/wp-menu-icons/class-cera-wp-menu-icons.php';
}

if ( function_exists( 'buddypress' ) ) {

	if ( ! class_exists( 'Youzer' ) && ! class_exists( 'Youzify' ) ) {
		define( 'BP_AVATAR_THUMB_WIDTH',        300 );
		define( 'BP_AVATAR_THUMB_HEIGHT',       300 );
		define( 'BP_AVATAR_FULL_WIDTH',         450 );
		define( 'BP_AVATAR_FULL_HEIGHT',        450 );
		define( 'BP_AVATAR_ORIGINAL_MAX_WIDTH', 999 );
		define( 'BP_AVATAR_DEFAULT',            get_stylesheet_directory_uri() . '/assets/images/avatars/user-avatar.png' );
		define( 'BP_AVATAR_DEFAULT_THUMB',      get_stylesheet_directory_uri() . '/assets/images/avatars/user-avatar-thumb.png' );
	}

	global $cera_buddypress;
	$cera_buddypress = require get_template_directory() . '/inc/buddypress/class-cera-buddypress.php';
}

if ( class_exists( 'Grimlock_BuddyPress' ) ) {
	global $cera_grimlock_buddypress;
	$cera_grimlock_buddypress = require get_template_directory() . '/inc/grimlock-buddypress/class-cera-grimlock-buddypress.php';
}

if ( class_exists( 'Grimlock_bbPress' ) ) {
	global $cera_grimlock_bbpress;
	$cera_grimlock_bbpress = require get_template_directory() . '/inc/grimlock-bbpress/class-cera-grimlock-bbpress.php';
}

if ( function_exists( 'bps_templates' ) ) {
	global $cera_bp_profile_search;
	$cera_bp_profile_search = require get_template_directory() . '/inc/bp-profile-search/class-cera-bp-profile-search.php';
}

if ( class_exists( 'Grimlock_Author_Avatars' ) ) {
	global $cera_grimlock_author_avatars;
	$cera_grimlock_author_avatars = require get_template_directory() . '/inc/grimlock-author-avatars/class-cera-grimlock-author-avatars.php';
}

if ( class_exists( 'Grimlock_WooCommerce' ) ) {
	global $cera_grimlock_woocommerce;
	$cera_grimlock_woocommerce = require get_template_directory() . '/inc/grimlock-woocommerce/class-cera-grimlock-woocommerce.php';
}

if ( class_exists( 'Grimlock_LearnDash' ) ) {
	global $cera_grimlock_learndash;
	$cera_grimlock_learndash = require get_template_directory() . '/inc/grimlock-learndash/class-cera-grimlock-learndash.php';
}

if ( class_exists( 'Grimlock_Login' ) ) {
	global $cera_grimlock_login;
	$cera_grimlock_login = require get_template_directory() . '/inc/grimlock-login/class-cera-grimlock-login.php';
}

/**
 * Note: Do not add any custom code here. Please use a custom plugin or a child theme
 * so that your customizations aren't lost during updates.
 *
 * @link https://doc.themosaurus.com/creating-child-theme/
 */
