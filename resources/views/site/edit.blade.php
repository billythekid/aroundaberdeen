@extends('layouts.app')

@section('content')
  <h1>{{ $site->name }}</h1>
  <p>This is where you can edit your site's map. You can add points of interest, set your map position and zoom level for best fit and more.</p>

  <div id="site">
    <div id="map" style="height: 350px;"></div>
    <button v-on:click="zoomIn">zoom in</button>
    <button v-on:click="zoomOut">zoom out</button>
    <hr>
    <div class="debug" style="position:absolute;top:45%;left:101%"><pre>
Debug Console
-------------

zoom: @{{ zoom }}
lat: @{{ lat }}
lng: @{{ lng }}</pre>
    </div>
  </div>

@endsection

@section('scripts')
  <script>
    var site = new Vue({
      el: '#site',
      data: {
        map: {},
        lat: 57.14459820700167,
        lng: -2.1058481488738607,
        zoom: 16
      },
      methods: {
        zoomIn: function () {
          if (this.zoom < 23) {
            this.zoom = this.zoom + 1;
            this.map.setZoom(this.zoom);
          }
        },
        zoomOut: function () {
          if (this.zoom > 1) {
            this.zoom = this.zoom - 1;
            this.map.setZoom(this.zoom);
          }
        },
        addMarker: function () {

        },

      },
      mounted: function () {
        GoogleMapsLoader.load(function (google) {
          this.map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: this.lat, lng: this.lng},
            zoom: this.zoom,
            zoomControl: false,
            disableDefaultUI: true
          });

          this.map.addListener('center_changed', function () {
            this.lat = this.map.getCenter().lat();
            this.lng = this.map.getCenter().lng();
          }.bind(this))

        }.bind(this));


      }
    });
  </script>
@endsection