const $ = require('jquery');
const dt = require('datatables.net');
const dts = require('datatables.net-dt');

export default class campaignReport {

  constructor(element) {
    let options = []
    $(element).DataTable(options)
  }

}