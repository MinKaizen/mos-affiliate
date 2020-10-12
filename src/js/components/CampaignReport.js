const $ = require('jquery');
const dt = require('datatables.net');
const dts = require('datatables.net-dt');

export default class campaignReport {

  constructor() {
    $(document).ready(function() {
      $('.js-campaign-report').DataTable({
        buttons: [
          'copy', 'excel', 'pdf'
        ]
      });
    })
  }

}