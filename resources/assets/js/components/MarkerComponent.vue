<template>
  <div class="marker">

    <h3>{{ markerName }}</h3>
    <input type="text" v-model="markerName" v-on:keyup="emitName">

    <pre>
      lat: {{ lat }}, lng: {{ lng }}
    </pre>
  </div>
</template>


<script>
  export default {
    props: [
      'id',
      'name',
      'lat',
      'lng'
    ],
    data: function () {
      return {
        markerName: this.name
      }
    },
    methods: {
      emitName: function(){
        this.$emit('emitname', { name: this.markerName, id: this.id});
      }
    },
    mounted() {
      console.log('Marker mounted.')
    }
  }
</script>
