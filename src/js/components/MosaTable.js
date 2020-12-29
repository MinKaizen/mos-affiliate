const $ = require('jquery');
require( 'datatables.net-dt' );
require( 'datatables.net-buttons-dt' );
require( 'datatables.net-buttons/js/buttons.html5.js' );

const options = {
  dom: '<"actions"Bf>rt<"pagination"lp><"clear">',
  buttons: [
    {
      extend: 'csv',
      text: 'Export',
      className: 'datatables-export-button'
    },
  ],
  language: {
    emptyTable: "(No data)"
  }
}

export default class MosaTable {
  constructor(element) {
    $(element).DataTable(options)
  }
}