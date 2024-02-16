<?php
/**
 * Plugin Name: Meta Mosaic Blueprint
 * Description: Meta Mosaic Blueprint is an advanced WordPress plugin designed to easily create image schema sitemaps for your website. It follows the latest standards set by Google's schema for image sitemaps, which can be found at https://www.google.com/schemas/sitemap-image/1.1/sitemap-image.xsd.
 * Version: 1.0
 * Author: Mayur Bhatt
 */

// Hook into WordPress activation and deactivation
register_activation_hook(__FILE__, 'image_sitemap_activate');
register_deactivation_hook(__FILE__, 'image_sitemap_deactivate');

// Activation function
function image_sitemap_activate() {
    // Add any activation tasks here
}

// Deactivation function
function image_sitemap_deactivate() {
    // Add any deactivation tasks here
}

// Hook into WordPress init action to run the code
add_action('init', 'generate_image_sitemap');

// Function to generate the image sitemap
function generate_image_sitemap() {
    $image_data = get_image_data(); // Get image data

    // Generate XML content
    $xml_content = generate_xml($image_data);

    // Save the XML content to a file
    file_put_contents(ABSPATH . 'sitemap-image.xsd', $xml_content);
}

// Function to get image data
function get_image_data() {
    $image_data = array();

    // Query to get all images
    $images = get_posts(array(
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'posts_per_page' => -1,
    ));

    foreach ($images as $image) {
        // Collect image data
        $image_data[] = array(
            'loc' => wp_get_attachment_url($image->ID),
            'caption' => get_post_meta($image->ID, '_wp_attachment_image_alt', true),
            'geo_location' => 'United States', // You may need to enhance this based on your needs
            'title' => get_the_title($image->ID),
            'license' => 'https://creativecommons.org/licenses/by-nc-nd/4.0/',
        );
    }

    return $image_data;
}

// Function to generate XML content
function generate_xml($image_data) {
    $xml_content = '<?xml version="1.0" encoding="utf-8"?>
    <xsd:schema
    xmlns:xsd="http://www.w3.org/2001/XMLSchema"
    targetNamespace="http://www.google.com/schemas/sitemap-image/1.1"
    xmlns="http://www.google.com/schemas/sitemap-image/1.1"
    elementFormDefault="qualified">

    <xsd:annotation>
      <xsd:documentation>
        XML Schema for the Image Sitemap extension. This schema defines the
        Image-specific elements only; the core Sitemap elements are defined
        separately.
    
        Help Center documentation for the Image Sitemap extension:
    
          https://developers.google.com/search/docs/advanced/sitemaps/image-sitemaps
    
        Copyright 2010 Google Inc. All Rights Reserved.
      </xsd:documentation>
    </xsd:annotation>';

    foreach ($image_data as $image) {
        $xml_content .= '
        <xsd:element name="image">
          <xsd:complexType>
            <xsd:sequence>
              <xsd:element name="loc" type="xsd:anyURI">' . esc_html($image['loc']) . '</xsd:element>
              <xsd:element name="caption" type="xsd:string" minOccurs="0">' . esc_html($image['title']) . '</xsd:element>
              <xsd:element name="geo_location" type="xsd:string" minOccurs="0">' . esc_html($image['geo_location']) . '</xsd:element>
              <xsd:element name="title" type="xsd:string" minOccurs="0">' . esc_html($image['title']) . '</xsd:element>
              <xsd:element name="license" type="xsd:anyURI" minOccurs="0">' . esc_html($image['license']) . '</xsd:element>
            </xsd:sequence>
          </xsd:complexType>
        </xsd:element>';
    }

    $xml_content .= '</xsd:schema>';

    return $xml_content;
}
