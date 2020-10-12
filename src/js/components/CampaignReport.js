const $ = require('jquery');
require( 'datatables.net-dt' );
require( 'datatables.net-buttons-dt' );
require( 'datatables.net-buttons/js/buttons.html5.js' );

export default class campaignReport {

  constructor(element) {
    let options = {
      dom: 'Bfrtip',
      buttons: [
        {
            extend: 'csv',
            text: 'Download CSV'
        },
      ]
    }
    $(element).DataTable(options)
  }

}