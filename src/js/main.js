import CampaignReport from './components/CampaignReport';
import ReferralsTable from './components/ReferralsTable';

const components = [
  {
    class: CampaignReport,
    selector: '.js-campaign-report'
  },
  {
    class: ReferralsTable,
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