$(function() {
    $( "#slider-number-range" ).slider({
      range: true,
      min: -100,
      max: 100,
      values: [ 1, 12 ],
      slide: function( event, ui ) {
        $( "#numbersBetween" ).val(ui.values[ 0 ] + " : " + ui.values[ 1 ] );
		$( "#slider-bonus-number" ).slider("option", "min", $( "#slider-number-range" ).slider( "values", 0 ));
		$( "#slider-bonus-number" ).slider("option", "max", $( "#slider-number-range" ).slider( "values", 1 ));
		if ($( "#slider-bonus-number" ).slider("value") < $( "#slider-number-range" ).slider( "values", 0 )) {
			$( "#slider-bonus-number" ).slider({ value: $( "#slider-number-range" ).slider( "values", 0 ) });
		} else if ($( "#slider-bonus-number" ).slider("value") > $( "#slider-number-range" ).slider( "values", 1 )) {
			$( "#slider-bonus-number" ).slider({ value: $( "#slider-number-range" ).slider( "values", 1 ) });
		}
		$( "#bonusNumber" ).val($( "#slider-bonus-number" ).slider( "value" ));
      }
    });
    $( "#numbersBetween" ).val( $( "#slider-number-range" ).slider( "values", 0 ) +
      " : " + $( "#slider-number-range" ).slider( "values", 1 ) );
  
  $( "#slider-car-speed" ).slider({
      value:3,
      min: 1,
      max: 10,
      slide: function( event, ui ) {
        $( "#carSpeed" ).val( ui.value );
      }
    });
    $( "#carSpeed" ).val($( "#slider-car-speed" ).slider( "value" ) );
  
  $( "#slider-car-timer" ).slider({
      value:30,
      min: 3,
      max: 90,
      slide: function( event, ui ) {
        $( "#carTimer" ).val( ui.value + ' <?php _e('seconds', 'wpbootstrap'); ?>');
      }
    });
    $( "#carTimer" ).val($( "#slider-car-timer" ).slider( "value" ) + ' <?php _e('second', 'wpbootstrap'); ?>' + 's');
	
  $( "#slider-build-timer" ).slider({
      value:10,
      min: 3,
      max: 30,
      slide: function( event, ui ) {
        $( "#buildTimer" ).val( ui.value + ' <?php _e('seconds', 'wpbootstrap'); ?>');
      }
    });
    $( "#buildTimer" ).val($( "#slider-build-timer" ).slider( "value" ) + ' <?php _e('second', 'wpbootstrap'); ?>' + 's' );
	
  $( "#slider-bridge-pillar" ).slider({
      value:2,
      min: 2,
      max: 20,
      slide: function( event, ui ) {
        $( "#bridgePillar" ).val( ui.value);
		var bridge_points = $('#bridgePoints');
		var count = $('#bridgePoints p').size() + 1;
		
		while (count > 0) {
			$('#appendedPrependedInput').parent().remove();
			count--;
		}
		
		while (count < ui.value) {
			count++;
			if (count % 5 == 1) {
				$('<p>').appendTo(bridge_points);
			}
			$('<div class="input-prepend" id="bridgepointclass"><span class="add-on">' + count + '</span>' + '<input class="span1" id="appendedPrependedInput" type="text"></div>').appendTo(bridge_points);
			
			if (count % 5 == 0) {
				$('</p>').appendTo(bridge_points);
			}
		}
	   }
    });
    $( "#bridgePillar" ).val($( "#slider-bridge-pillar" ).slider( "value" ));
	
  $( "#slider-bonus-number" ).slider({
      value:7,
      min: $( "#slider-number-range" ).slider( "values", 0 ),
      max: $( "#slider-number-range" ).slider( "values", 1 ),
      slide: function( event, ui ) {
        $( "#bonusNumber" ).val( ui.value);
		$( "#slider-bonus-number" ).slider("option", "min", $( "#slider-number-range" ).slider( "values", 0 ));
		$( "#slider-bonus-number" ).slider("option", "max", $( "#slider-number-range" ).slider( "values", 1 ));
      }
    });
    $( "#bonusNumber" ).val($( "#slider-bonus-number" ).slider( "value" ));
    
  $( "#slider-number-bubbles" ).slider({
      value:5,
      min: 1,
      max: 10,
      slide: function( event, ui ) {
        $( "#numberBubbles" ).val( ui.value);
      }
    });
    $( "#numberBubbles" ).val($( "#slider-number-bubbles" ).slider( "value" ));
    
  });