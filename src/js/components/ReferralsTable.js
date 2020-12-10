const $ = require('jquery');
require( 'datatables.net-dt' );
require( 'datatables.net-buttons-dt' );
require( 'datatables.net-buttons/js/buttons.html5.js' );

export default class ReferralsTable {

  constructor(element) {
    let options = {
      dom: 'Bfrtip',
      buttons: [
        {
            extend: 'csv',
            text: 'Download CSV',
            className: 'exportButtonClassnameChangeThisLater'
        },
      ]
    }
    $(element).DataTable(options)
  }

}