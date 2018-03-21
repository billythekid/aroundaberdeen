@extends('layouts.app')

@section('content')
  <div class="site-container">
    <h1 style="width: 100%;">{{ title_case($site->name) }} {{ title_case(config('app.name')) }}</h1>
  </div>

  <div id="site" style="width: 100%;">

    <div class="map-wrapper" style="margin: 4rem 0; position: sticky; top: 0; z-index: 2;">
      <div id="map" style="width:100%; height:450px;"></div>
    </div>

    <div class="site-container">
      <h2>{{ str_plural($site->name,count($site->map->points)) }}</h2>
      <div class="points">
        <div v-for="point in points">
          <h3>@{{ point.name }}</h3>
          <button v-on:click="bounce(point)" v-text="point.bouncing ? 'Stop Bouncing Pin' : 'Bounce Pin'"></button>
          <button v-on:click="zoom(point)" v-text="point.zoomed ? 'Zoom Out' : 'Zoom To ' + point.name"
                  v-if="!zoomedToPoint || point.zoomed"></button>
          <hr>
        </div>
      </div>

    </div>

  </div>


  <div id="directions" style="width: 100%;"></div>



@endsection

@section('scripts')
  <script>
    new Vue({
      el: '#site',
      data: {
        map: {!! $site->map !!},
        points: {!! $site->map->points !!},
        markers: [],
        defaultMarkerOptions: {},
        zoomedToPoint: false
      },
      computed: {
        waypoints: function () {
          let waypoints = [];
          _.forEach(this.markers, function (marker) {
            waypoints.push({location: marker.getPosition()});
          });
          return _.dropRight(_.tail(waypoints));
        },
      },
      methods: {
        addMarkers: function () {
          for (let i = 0; i < this.markers.length; i++) {
            this.markers[i].setMap(null); // take them off the map
          }
          this.markers = []; // then delete them
          for (let i = 0; i < this.points.length; i++) {
            let marker = new google.maps.Marker(this.defaultMarkerOptions);
            marker.setPosition({lat: this.points[i].lat, lng: this.points[i].lng});
            marker.setTitle(this.points[i].name);
            marker.addListener('click', function () {
              // TODO - click listener, show the info about this marker
            }.bind(this));
            marker.setMap(window.map);
            this.markers.push(marker);
            this.points[i].marker = marker;
          }
        },

        bounce: function (point) {
          point.bouncing = !point.bouncing;
          if (point.bouncing) {
            point.marker.setAnimation(google.maps.Animation.BOUNCE);
          } else {
            point.marker.setAnimation(null);
          }
          this.$forceUpdate();
        },

        zoom: function (point) {
          if (this.zoomedToPoint) {
            window.map.setZoom({{ $site->map->zoom }});
            window.map.panTo({lat: {{ $site->map->lat }}, lng: {{ $site->map->lng }}});
            this.zoomedToPoint = false;
            point.zoomed = false;
          }
          else {
            window.map.setZoom(18);
            window.map.panTo(point.marker.position);
            this.zoomedToPoint = true;
            point.zoomed = true;
          }
        },

        iterate: function (obj) {
          let walked = [];
          let stack = [{obj: obj, stack: ''}];
          while (stack.length > 0) {
            let item = stack.pop();
            let obj = item.obj;
            for (let property in obj) {
              if (obj.hasOwnProperty(property)) {
                if (typeof obj[property] == "object") {
                  let alreadyFound = false;
                  for (let i = 0; i < walked.length; i++) {
                    if (walked[i] === obj[property]) {
                      alreadyFound = true;
                      break;
                    }
                  }
                  if (!alreadyFound) {
                    if (obj[property] && obj[property].hasOwnProperty('lat') && obj[property].hasOwnProperty('lng')) {
                      obj[property] = new google.maps.LatLng(obj[property].lat, obj[property].lng);
                    }
                    walked.push(obj[property]);
                    stack.push({obj: obj[property], stack: item.stack + '.' + property});
                  }
                }
              }
            }
          }
        },
      },
      mounted: function () {
        GoogleMapsLoader.load(function (google) {
          window.map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: {{ $site->map->lat }}, lng: {{ $site->map->lng }}},
            zoom: {{ $site->map->zoom }},
            zoomControl: false,
            disableDefaultUI: true,
            styles: [{
              "featureType": "poi",
              "elementType": "all",
              "stylers": [{"visibility": "off"}]
            }]
          });
          window.directionsService = new google.maps.DirectionsService;
          window.directionsDisplay = new google.maps.DirectionsRenderer({
            map: window.map,
            panel: document.getElementById('directions'),
            suppressMarkers: true
          });

          this.defaultMarkerOptions = {
            "animation": google.maps.Animation.DROP,
            "clickable": true,
            "draggable": false,
            "visible": true
          }

          this.addMarkers();

          if (this.map.route.hasOwnProperty('routes')) {
            this.fixRouteLatLng();
            window.directionsDisplay.setDirections(this.map.route);
          }

        }.bind(this));
      }
    });
  </script>
@endsection