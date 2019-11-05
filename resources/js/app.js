try {
  require('./bootstrap');
  require('bootstrap');
  require('./datatable');
  require('datatables.net');
  window.Popper = require('popper.js').default;
  window.$ = window.jQuery = require('jquery');
} catch (e) {
  console.log(e)
}