const $ = require('jquery');
require('datatables.net');

export default class campaignReport {

  constructor(element) {
    let options = []
    $(element).DataTable(options)
  }

}