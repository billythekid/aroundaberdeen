@extends('layouts.app')

@section('content')

  <div id="site" class="site-edit">

    <h1>@{{ name }}</h1>
    (change)<input type="text" v-model="name">
    <p>This is where you can edit your site's map. You can add points of interest, set your map position and zoom level for best fit and more.</p>
    <div id="map" style="width:100%; height:450px"></div>
    <button v-on:click="zoomIn">Zoom In</button>
    <button v-on:click="zoomOut">Zoom Out</button>
    <button v-on:click="addPoint">Add @{{ name }}</button>
    <button v-on:click="saveSite" v-bind:disabled="saving">
      <span v-if="saving">Saving…</span><span v-else>Save this map</span>
    </button>
    <button v-on:click="showRoute">Get Route</button>
    <label><input type="checkbox" v-model="optimize">Optimize Route?</label>
    <button v-on:click="hideRoute">Delete Route</button>

    {{--<hr>--}}
    {{--<code>@{{ map.lat }} @{{ map.lng }} @{{ map.zoom }}</code>--}}
    <hr>
    <div v-if="points.length">
      <h2>@{{ pluralize(name) }}</h2>
      <button v-on:click="fitBounds">Fit map to @{{ pluralize(name).toLowerCase() }}</button>
      <hr>
      <hr>
      <draggable v-model="points" @end="savePointsOrder" :options="{handle:'.handle'}">
        <div v-for="point in points" class="marker">
          <i class="handle">⇅</i>
          <h3 v-text="point.name"></h3>
          <label>Rename: <input type="text" v-model="point.name" v-on:change="savePoint(point)"></label>
          <br>
          <button v-on:click="bounce(point)">
            <span v-if="!point.bouncing">Show</span><span v-else>Stop showing</span> @{{ point.name }} on map
          </button>
          <button v-on:click="removePoint(point)">Delete @{{ point.name }}</button>
          {{--<br>--}}
          {{--<code>lat: @{{ point.lat }}, lng: @{{ point.lng }}</code>--}}
          <hr>
        </div>
      </draggable>

    </div>

  </div>

  <div id="directions" class="site-directions"></div>

@endsection

@section('scripts')
  <script>
    let site = new Vue({
      el: '#site',
      data: {
        id: {{ $site->id }},
        name: "{{ $site->name }}",
        map: {!! $site->map !!},
        points: {!! $site->map->points !!},
        markers: [],
        googleMap: {},
        directionsService: {},
        directionsRenderer: {},
        defaultMarkerOptions: {}, // added on mount

        optimize: false,
        saving: false,
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
        bounce: function (point) {
          point.bouncing = !point.bouncing;
          if (point.bouncing) {
            point.marker.setAnimation(google.maps.Animation.BOUNCE);
          } else {
            point.marker.setAnimation(null);
          }
          this.$forceUpdate();
        },
        markerTitle: function (point) {
          if (point.marker) {
            point.marker.setTitle(point.name);
            return point.marker.getTitle();
          }
          return '';
        },
        fitBounds: function () {
          let bounds = new google.maps.LatLngBounds();
          for (let i = 0; i < this.markers.length; i++) {
            bounds.extend(this.markers[i].getPosition());
          }
          this.googleMap.fitBounds(bounds);
        },
        pluralize: function (word) {
          return window.pluralize(word);
        },
        zoomIn: function () {
          if (this.map.zoom < 23) {
            this.map.zoom = this.map.zoom + 1;
            this.googleMap.setZoom(this.map.zoom);
          }
        },
        zoomOut: function () {
          if (this.map.zoom > 1) {
            this.map.zoom = this.map.zoom - 1;
            this.googleMap.setZoom(this.map.zoom);
          }
        },
        addPoint: function () {
          axios.post('{{ route('point.store') }}', {
            '_method': 'POST',
            'map': this.map.id
          }).then(function (response) {
              this.points.push(response.data);
              this.addMarkers();
              // console.log(response.data);
            }.bind(this)
          ).catch(function (error) {
            // console.log(error);
          }.bind(this));
        },
        savePointsOrder: function () {
          axios.post('{{ route('savePointsOrder') }}', {
            '_method': 'PATCH',
            'map': this.map.id,
            'points': _.map(this.points, 'id'),
          }).then(function (response) {
              this.points = response.data;
              this.addMarkers();
              // console.log(response.data);
            }.bind(this)
          ).catch(function (error) {
            // console.log(error);
          }.bind(this));
        },

        removePoint: function (point) {
          axios.post('/point/' + point.id, {
            '_method': 'DELETE',
          }).then(function (response) {
              this.points = response.data;
              this.addMarkers();
              // console.log(response.data);
            }.bind(this)
          ).catch(function (error) {
            // console.log(error);
          }.bind(this));
        },

        addMarkers: function () {
          for (let i = 0; i < this.markers.length; i++) {
            this.markers[i].setMap(null); // take them off the map
          }
          this.markers = []; // then delete them

          for (let i = 0; i < this.points.length; i++) {
            this.points[i].bouncing = false;
            let marker = new google.maps.Marker(this.defaultMarkerOptions);
            marker.setPosition({lat: this.points[i].lat, lng: this.points[i].lng});
            marker.setTitle(this.points[i].name);
            marker.addListener('click', function () {
              this.points[i].bouncing = false;
              marker.setAnimation(null);
              this.$forceUpdate();
            }.bind(this));
            marker.addListener('dragend', function (dragevent) {
              this.points[i].lat = marker.getPosition().lat();
              this.points[i].lng = marker.getPosition().lng();
              this.savePoint(this.points[i]);
            }.bind(this));
            this.points[i].marker = marker;
            this.markers.push(marker);
          }
        },
        hideRoute: function () {
          this.directionsRenderer.setMap(null);
          this.directionsRenderer.setDirections({});
          this.directionsRenderer.setPanel(null);
          this.map.route = {};
          this.$forceUpdate();
          this.saveSite();
        },
        showRoute: function () {
          waypoints = this.waypoints;
          if (this.waypoints.length > 23) {
            waypoints = [];
          }
          this.directionsRenderer.setMap(null);
          this.directionsService.route({
            origin: _.first(this.markers).getPosition(),
            waypoints: waypoints,
            optimizeWaypoints: this.optimize,
            destination: _.last(this.markers).getPosition(),
            travelMode: 'WALKING'
          }, function (response, status) {
            if (status === 'OK') {
              this.directionsRenderer.setDirections(response);
              this.directionsRenderer.setMap(this.googleMap);
              this.directionsRenderer.setPanel(document.getElementById('directions'));
              this.map.route = this.directionsRenderer.getDirections();
              // console.log(response);
            } else {
              window.alert('Directions request failed due to ' + status);
            }
          }.bind(this));
        },

        saveSite: function () {
          if (this.saving) {
            return;
          }
          this.saving = true;
          axios.post('{{ route('site.update', $site) }}', {
            '_method': 'PATCH',
            'name': this.name,
            'map': this.map,
            'route': this.map.route,
          }).then(function (response) {
            setTimeout(function () {
              this.saving = false;
            }.bind(this), 500);
            // console.log(response.data);
          }.bind(this)).catch(function (error) {
            // console.log(error);
          }.bind(this));

        },

        savePoint: function (point) {
          this.markerTitle(point);

          axios.post('/point/' + point.id, {
            '_method': 'PATCH',
            'name': point.name,
            'lat': point.lat,
            'lng': point.lng
          }).then(function (response) {
            // console.log(response.data);
          }.bind(this)).catch(function (error) {
            // console.log(error);
          }.bind(this));
        },

        fixRouteLatLng() {
            this.iterate(this.map.route);
            // TODO - rename the route legs to the point names
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

          this.directionsService = new google.maps.DirectionsService;
          this.directionsRenderer = new google.maps.DirectionsRenderer({
            draggable: true,
            map: this.googleMap,
            panel: document.getElementById('directions'),
            suppressMarkers: true
          });

          this.directionsRenderer.addListener('directions_changed', function () {
            this.map.route = this.directionsRenderer.getDirections();
            this.$forceUpdate();
          }.bind(this));

          this.googleMap.addListener('dragend', function () {
            this.map.lat = this.googleMap.getCenter().lat();
            this.map.lng = this.googleMap.getCenter().lng();
            this.saveSite();
          }.bind(this));

          this.googleMap.addListener('center_changed', function () {
            this.map.lat = this.googleMap.getCenter().lat();
            this.map.lng = this.googleMap.getCenter().lng();
          }.bind(this));

          this.googleMap.addListener('zoom_changed', function () {
            this.map.zoom = this.googleMap.getZoom();
            this.saveSite();
          }.bind(this));

          this.defaultMarkerOptions = {
            // "animation": google.maps.Animation.DROP,
            "clickable": true,
            "draggable": true,
            "map": this.googleMap,
            "position": this.googleMap.getCenter(),
            "visible": true
          };

          this.addMarkers();

          if (this.map.route !== []) {
            this.directionsRenderer.setMap(null);
            this.fixRouteLatLng();
            this.directionsRenderer.setDirections(this.map.route);
            this.directionsRenderer.setMap(this.googleMap);
          }

        }.bind(this));


      }
    });
  </script>
@endsection