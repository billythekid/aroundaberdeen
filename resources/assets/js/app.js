/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.pluralize = require('pluralize');

window.Vue = require('vue');

window.GoogleMapsLoader = require('google-maps');

GoogleMapsLoader.KEY = 'AIzaSyB6VIMLxObqAjef-80mf3O0bocrzlogizA';
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// Vue.component('example-component', require('./components/ExampleComponent.vue'));

// Vue.component('point-of-interest', require('./components/MarkerComponent.vue'));
Vue.component('draggable', require('vuedraggable'));