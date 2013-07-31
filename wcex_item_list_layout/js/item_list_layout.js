
jQuery(function($){
		wcexILL = {
			del : function ( id ) {
				if( confirm( 'Okay to delete Layout_' + id + ' ?' ) ) {
					return true;
				}else{
					return false;	
				}
			},
			
			style : function ( id ) {
				var style = $("input[name=style_"+id+"]:checked").val();
				if( 'showcase' == style){
					$("#width_"+id).focus();
				}else if( 'list' == style){
					$("#width_l_"+id).focus();
				}
			}
		};
});
