@extends('layouts.app')

@section('content')

  <div id="site">
    <h1>@{{ name }}</h1>
    (change)<input type="text" v-model="name">
    <p>This is where you can edit your site's map. You can add points of interest, set your map position and zoom level for best fit and more.</p>
    <div id="map" style="height: 350px;"></div>
    <button v-on:click="zoomIn">Zoom In</button>
    <button v-on:click="zoomOut">Zoom Out</button>
    <button v-on:click="addPoint">Add Point of Interest</button>
    <button v-on:click="saveSite">Save this map</button>

    <hr>
    <code>@{{ map.lat }} @{{ map.lng }} @{{ map.zoom }}</code>
  </div>

@endsection

@section('scripts')
  <script>
    let site = new Vue({
      el: '#site',
      data: {
        id: {{ $site->id }},
        name: "{{ $site->name }}",
        map: {!! $site->map !!},
        googleMap: {},
        defaultMarkerOptions: {}, // added on mount

        points: [],

      },
      methods: {
        zoomIn: function () {
          if (this.map.zoom < 23) {
            this.map.zoom = this.map.zoom + 1;
            this.googleMap.setZoom(this.map.zoom);
            this.saveMap()
          }
        },
        zoomOut: function () {
          if (this.map.zoom > 1) {
            this.map.zoom = this.map.zoom - 1;
            this.googleMap.setZoom(this.map.zoom);
          }
        },
        addPoint: function () {

        },

        saveSite: function () {
          axios.post('{{ route('site.update', $site) }}', {
            '_method': 'PATCH',
            'name': this.name,
            'map': this.map,
            'points': this.points
          }).then(function (response) {
            console.log(response);
          }).catch(function (error) {
            console.log(error);
          });

        },

      },
      mounted: function () {
        GoogleMapsLoader.load(function (google) {
          this.googleMap = new google.maps.Map(document.getElementById('map'), {
            center: {lat: this.map.lat, lng: this.map.lng},
            zoom: this.map.zoom,
            zoomControl: false,
            disableDefaultUI: true,
            styles: [{
              "featureType": "poi",
              "elementType": "all",
              "stylers": [{"visibility": "off"}]
            }]
          });

          this.googleMap.addListener('center_changed', function () {
            this.map.lat = this.googleMap.getCenter().lat();
            this.map.lng = this.googleMap.getCenter().lng();
          }.bind(this));

          this.defaultMarkerOptions = {
            "animation": google.maps.Animation.DROP,
            "clickable": true,
            "draggable": true,
            "map": this.googleMap,
            "position": this.googleMap.getCenter(),
            "visible": true
          }

        }.bind(this));


      }
    });
  </script>
@endsection