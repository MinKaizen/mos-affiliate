import CampaignReport from './components/CampaignReport';

const components = [
  {
    class: CampaignReport,
    selector: '.js-campaign-report'
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