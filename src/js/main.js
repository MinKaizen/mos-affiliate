import MosaTable from './components/MosaTable';

const components = [
  {
    class: MosaTable,
    selector: '.js-commissions-table'
  },
  {
    class: MosaTable,
    selector: '.js-campaign-report'
  },
  {
    class: MosaTable,
    selector: '.js-referrals-table'
  }
]

// Initialise components
components.forEach(component => {
  if (document.querySelector(component.selector)) {
    document.querySelectorAll(component.selector).forEach(element => {
      new component.class(element)
    })
  }
})