function accordion() {
  $(document).ready(function() {
    $("#accordion").accordion({ collapsible: true, active: false });
    $( ".selector" ).accordion({ collapsible: true });
    $( ".selector" ).accordion({ animated: 'bounceslide' });
    $( ".selector" ).accordion({ fillSpace: true });
    $( ".selector" ).accordion({ clearStyle: true });
  });
} //accordion()

function slide() {
	$(document).ready(function() {
		$(".head").click(function(){
			$(this).next(".body").slideToggle(500)
			return false;
		});
	});
} //slide()
