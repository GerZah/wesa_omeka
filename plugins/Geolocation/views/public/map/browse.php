<?php
queue_css_file('geolocation-items-map');
queue_css_file('jquery-ui');

$title = __('Browse Items on the Map') . ' ' . __('(%s total)', $totalItems);
echo head(array('title' => $title, 'bodyclass' => 'map browse'));
?>

<h1><?php echo $title; ?></h1>

<nav class="items-nav navigation secondary-nav">
    <?php echo public_nav_items(); ?>
</nav>

<?php
echo item_search_filters();
echo pagination_links();
?>

<div id="geolocation-browse">
    <?php echo $this->googleMap('map_browse', array('list' => 'map-links', 'params' => $params)); ?>
    <div id="map-links"><h2><?php echo __('Find An Item on the Map'); ?></h2></div>
    <?php
      $overlays = GeolocationPlugin::GeolocationConvertOverlayJsonForUse();
      if ($overlays) {
          $overlay = -1;
          echo '<div>'.
                   '<span style="display:inline-block;"><h3>' . __("Select Map Overlay:") . ' </h3></span> '.
                   '<span style="display:inline-block;"><form>'.
                       get_view()->formSelect('geolocation[overlay]', $overlay, null, $overlays["jsSelect"] ).
                   '</form></span>'.
                   '<span class="ovlOpacSlider"></span>'.
               '</div>';
      }
    ?>
</div>

<?php echo foot(); ?>
