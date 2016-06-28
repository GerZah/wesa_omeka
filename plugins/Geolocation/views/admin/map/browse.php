<?php 
queue_css_file('geolocation-items-map');
    
$title = __("Browse Items on the Map").' (' . html_escape($totalItems).' '.__('total').')';

echo head(array('title' => $title));
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
                   '<span style="display:inline-block;"><h4>' . __("Select Map Overlay:") . ' </h4></span> '.
                   '<span style="display:inline-block;"><form>'.
                       get_view()->formSelect('geolocation[overlay]', $overlay, null, $overlays["jsSelect"] ).
                   '</form></span>'.
                   '<span class="ovlOpacSlider" style="display:inline-block; width: 10em; margin-left:1em;"></span>'.
               '</div>';
      }
    ?>
</div>

<div id="search_block">
    <?php echo items_search_form(array('id'=>'search'), $_SERVER['REQUEST_URI']); ?>
</div><!-- end search_block -->

<?php echo foot(); ?>
