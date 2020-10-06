<form method="post" action="">
  <input type="hidden" name="uap_campaign_nonce" value="<?php echo wp_create_nonce( 'uap_campaign_nonce' );?>" />
  <input type="text" name="campaign_name"/>
  <input type="submit" name="save" value="<?php _e('Add New', 'uap');?>" class="uap-js-submit-campaign" />
</form>

<script>
jQuery('.uap-js-submit-campaign').on( 'click', function(e){
  e.preventDefault();
  jQuery.ajax({
    type : "post",
    url : decodeURI(ajax_url),
    data : {
      action						: "uap_ajax_save_campaign",
      campaignName			: jQuery('[name=campaign_name]').val(),
    },
    success: function (response) {
      location.reload();
    }
  });
})
</script>