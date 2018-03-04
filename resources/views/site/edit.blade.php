@extends('layouts.app')

@section('content')

  <div id="site">
    <h1>@{{ name }}</h1>
    (change)<input type="text" v-model="name">
    <p>This is where you can edit your site's map. You can add points of interest, set your map position and zoom level for best fit and more.</p>
    <div id="map" style="width:100%; height:450px"></div>
    <button v-on:click="zoomIn">Zoom In</button>
    <button v-on:click="zoomOut">Zoom Out</button>
    <button v-on:click="addPoint">Add @{{ name }}</button>
    <button v-on:click="saveSite" v-bind:disabled="saving"><span v-if="saving">Saving…</span><span v-else>Save this map</span>
    </button>

    <hr>
    <code>@{{ map.lat }} @{{ map.lng }} @{{ map.zoom }}</code>
    <hr>
    <div v-if="points.length">
      <h2>@{{ pluralize(name) }}</h2>
      <point-of-interest v-for="point in points" :key="point.id" v-on:emitname="retitleMarker"
        :name="point.name"
        :lat="point.lat"
        :lng="point.lng"
        :id="point.id"
      ></point-of-interest>
    </div>
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
        points: {!! $site->map->points !!},
        googleMap: {},
        defaultMarkerOptions: {}, // added on mount


        saving: false,
      },
      methods: {
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
              console.log(response.data);
            }.bind(this)
          ).catch(function (error) {
            console.log(error);
          }.bind(this));
        },

        addMarkers: function () {
          for (point in this.points) {
            if (this.points.hasOwnProperty(point)) {
              console.log({lat: this.points[point].lat, lng: this.points[point].lng});
              let marker = new google.maps.Marker(this.defaultMarkerOptions);
              marker.setPosition({lat: this.points[point].lat, lng: this.points[point].lng});
              marker.setTitle(this.points[point].name);
              marker.addListener('dragend', function (dragevent) {
                this.points[point].lat = marker.getPosition().lat();
                this.points[point].lng = marker.getPosition().lng();
              }.bind(this));
              this.points[point].marker = marker;
            }
          }
        },
        retitleMarker: function (payload) {
          let point = this.points.filter(function (point) {
            return (point.id === payload.id);
          })[0];
          point.name = payload.name;
          point.marker.setTitle(payload.name);
          this.savePoint(point);
        },

        saveSite: function () {
          if (this.saving) {
            return;
          }
          this.saving = true;
          axios.post('{{ route('site.update', $site) }}', {
            '_method': 'PATCH',
            'name': this.name,
            'map': this.map
          }).then(function (response) {
            setTimeout(function () {
              this.saving = false;
            }.bind(this), 500);
            console.log(response.data);
          }.bind(this)).catch(function (error) {
            console.log(error);
          }.bind(this));

        },

        savePoint: function (point) {
          axios.post('/point/' + point.id, {
            '_method': 'PATCH',
            'name': point.name,
            'lat': point.lat,
            'lng': point.lng
          }).then(function (response) {
            console.log(response.data);
          }.bind(this)).catch(function (error) {
            console.log(error);
          }.bind(this));
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

          this.googleMap.addListener('dragend', function () {
            this.map.lat = this.googleMap.getCenter().lat();
            this.map.lng = this.googleMap.getCenter().lng();
            this.saveSite();
          }.bind(this));

          this.googleMap.addListener('zoom_changed', function () {
            this.map.zoom = this.googleMap.getZoom();
            this.saveSite();
          }.bind(this));

          this.defaultMarkerOptions = {
            "animation": google.maps.Animation.DROP,
            "clickable": true,
            "draggable": true,
            "map": this.googleMap,
            "position": this.googleMap.getCenter(),
            "visible": true
          };

          this.addMarkers();

        }.bind(this));


      }
    });
  </script>
@endsection