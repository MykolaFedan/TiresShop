<?php

class googleMap extends waMapAdapter
{

    public function getName()
    {
        return 'Google Maps';
    }

    protected function getByAddress($address, $options = array())
    {
        $locale = wa()->getLocale();
        $zoom = ifset($options['zoom'], 10);
        if (!empty($options['static'])) {
            return '<a target="_blank" href="//maps.google.com/maps?q='.urlencode($address).'&z='.$zoom.'">'.$this->getStaticImg($address, $options).'</a>';
        } else {
            $id = uniqid();
            $width = ifset($options['width'], '100%');
            $height = ifset($options['height'], '400px');
            $address = json_encode($address);
            $html = <<<HTML
<div id="google-map-{$id}" class="map" style="width:{$width}; height: {$height}"></div>
<script type="text/javascript">
$(function () {
        if (!window.init_google_maps) {
            window.init_google_maps = function () {
                $(window).trigger('init_google_maps');
            }
        }
        $(window).one('init_google_maps', function () {
            var geocoder = new google.maps.Geocoder();
            geocoder.geocode( { 'address': {$address}}, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    var map =  new google.maps.Map(document.getElementById('google-map-{$id}'), {
                        center: new google.maps.LatLng(55.753994, 37.622093),
                        zoom: {$zoom}
                    });
                    map.setCenter(results[0].geometry.location);
                    var marker = new google.maps.Marker({
                        map: map,
                        title: {$address},
                        position: results[0].geometry.location
                    });
                }
            });
        });
        if (!(window.google)) {
            $.getScript('https://maps.googleapis.com/maps/api/js?sensor=false&lang={$locale}&callback=init_google_maps')
        } else {
            init_google_maps();
        }
});
</script>
HTML;
            return $html;
        }
    }

    private function getStaticImg($address, $options = array())
    {
        $zoom = ifset($options['zoom'], 10);
        $width = ifset($options['width'], '600');
        $height = ifset($options['height'], '400');
        $size = (int)$width.'x'.(int)$height;
        return '<img src="//maps.googleapis.com/maps/api/staticmap?center='.urlencode($address).'.&zoom='.$zoom.'&size='.$size.'&markers=color:red%7Clabel:A%7C'.urlencode($address).'&sensor=false" />';
    }

    protected function getByLatLng($lat, $lng, $options = array())
    {

    }
}