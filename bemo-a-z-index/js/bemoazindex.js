jQuery.noConflict();
(function($) 
{
	//Show / Hide content fields
	$( "form#azindex_enter input#content" ).click(function() {

		var content = $("form#azindex_enter input#content").filter(":checked").val();
		
		if(content == '1')	//This makes the target stop working
		{
			$('div.filter_specific').hide();					
			$('div.content_specific').show();
		}
		else
		{
			$('div.filter_specific').show();					
			$('div.content_specific').hide();
		}
	});
	
	//
	$('form#azindex_enter select[name=posttype]').change(function()  {
		var optionSelected = $(this).find("option:selected");
		var valueSelected  = optionSelected.val();
		
		$('option.category_options').hide();
		
		var taxonomies = optionSelected.attr('taxonomies').split(' ');
		
		$( "option.category_options" ).each(function() {
			var category = $( this ).attr("taxonomy");
			
			var arrayLength = taxonomies.length;
				
			for (var i = 0; i < arrayLength; i++) 
			{
				if (taxonomies[i] == category)
					$( this ).show();
			}
		});
	});

	
	
	//Add the shortcode
	function addShortcode(ed,dialog)
	{
		var azindex_string = '[azindex';
		
		var debugging = $("input#debugging").filter(":checked").val();
		var content = $("input#content").filter(":checked").val();
		
		var filter = $("select[name=filter]").val();
		var posttype  = $("select[name=posttype]").val();
		var categories = $("select[name=categories]").val();
		var postcount = $("select[name=postcount]").val();
		var ordering = $("select[name=ordering]").val();
		var direction = $("select[name=direction]").val();
		
		if($('#index').val() != '')
			azindex_string += ' index="' + $('#index').val() + '"';
			
		if($('#all').val() != '')
			azindex_string += ' all="'+ $('#all').val() + '"';			

		if($('#prefix').val() != '')
			azindex_string += ' prefix="'+ $('#prefix').val() + '"';			

		if($('#suffix').val() != '')
			azindex_string += ' suffix="'+ $('#suffix').val() + '"';			

		if(debugging == '1')
			azindex_string += ' debug="true"';
		
		console.log('Target is : ' + $("select#target").val() + ' wont work without this ! ');
		if($("select#target").val() != '')
			azindex_string += ' target="'+ $("select#target").val() + '"';
		
		if(content == '1')
		{
			azindex_string += ' content="true"';
			
			if(filter != '')
				azindex_string += ' filter="'+ filter + '"';

			if(posttype != '')
				azindex_string += ' posttype="'+ posttype + '"';

			if(categories != '')
				azindex_string += ' category="'+ categories + '"';

			if(postcount != '')
				azindex_string += ' postcount="'+ postcount + '"';

			if(ordering != '')
				azindex_string += ' ordering="'+ ordering + '"';

			if(direction != '')
				azindex_string += ' direction="'+ direction + '"';
				
			if($('#template').val() != '')
				azindex_string += ' template="' + $('#template').val() + '"';
		}

/*			
		if($('#ignoreprefixes').val() != '')
			azindex_string += ' ignoreprefixes="' + $('#ignoreprefixes').val() + '"';
*/					
		
		azindex_string += ']';
		
		ed.selection.setContent(azindex_string + ed.selection.getContent() );
		dialog.dialog( "close" );
	}
	
    tinymce.create('tinymce.plugins.azindex', {
        init : function(ed, url) {
            ed.addButton('azindex', {
                title : 'Add an A-Z Index',
                image : url+'/image.png',
                onclick : function() {
					
					$('div.content_specific').hide();
					$('option.category_options').hide();
					
					dialog = $( "#dialog-form" ).dialog({
					  height: 600,
					  width: 350,
					  modal: true,
					  buttons: {
						"Add A-Z Index": function() {
							addShortcode(ed,dialog);
						},
						Cancel: function() {
						  dialog.dialog( "close" );
						}
					  },
					  close: function() {
						form[ 0 ].reset();
//						allFields.removeClass( "ui-state-error" );
					  }
					});
				 
					form = dialog.find( "form" ).on( "submit", function( event ) {
					  event.preventDefault();
					  alert('Submit');
					});
 
                }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
    });
    tinymce.PluginManager.add('azindex', tinymce.plugins.azindex);
})(jQuery);
