jQuery(document).ready(function($) {
  window.API_get = (url, callback ) => {
    return $.get(url, callback);
  }
  window.API_post = (url, callback ) => {
    return $.post(url, callback);
  }
  window.template_trio = (value) => {
    return `
      <div class="cell zume-trio-card">
          <div class="zume-trio-card-content" data-link="${value.link}">
              <div class="zume-trio-card-title">
                  ${value.label}
              </div>
              <div class="zume-trio-card-value">
                  ${value.value}
              </div>
          </div>
          <div class="zume-trio-card-footer">
              <div class="grid-x">
                  <div class="cell small-6 zume-goal ${value.goal_valence}">
                      GOAL
                  </div>
                  <div class="cell small-6 zume-trend ${value.trend_valence}">
                      TREND
                  </div>
              </div>
          </div>
      </div>
    `;
  }

  // window.load_stats = ( url, target ) => {
  //   let range = jQuery('#range').val()
  //   window.API_get( url+'?range='+range, function( data ) {
  //     console.log( data )
  //   })
  //   return true
  //   // window.phase_data = []
  //   // function get_range_stats( day ) {
  //   //   jQuery.get( window.site_url+'?filter=', function(data){
  //   //     window.phase_data = data
  //   //     console.log(data)
  //   //     jQuery('.loading-spinner').removeClass('active')
  //   //   })
  //   // }
  //   // get_range_stats( range_select.val() )
  //   // range_select.on('change', function(){
  //   //   get_range_stats( jQuery(this).val() )
  //   //   jQuery('.loading-spinner').addClass('active')
  //   // })
  // }


})
