var logos_attrs = ['title', 'images', 'layout', 'carousel', 'slideseconds', 'tooltip', 'grayscale', 'animation', 'extra_class', 'visibility'];

function get_blox_element_logos($content, $attrs){
	
	return '<div class="blox_item blox_logos" '+($attrs!=undefined ? $attrs : '')+'> \
				<div class="blox_item_title"> \
					<i class="fa-picture-o"></i> \
					<span class="blox_item_title">logos / Image Slider</span> \
					'+ get_blox_actions() +' \
				</div> \
				<div class="blox_item_content" style="display: none;">'+($content!=undefined ? $content : '')+'</div> \
			</div>';
}


function parse_shortcode_logos($content){
	$content = wp.shortcode.replace( 'blox_logos', $content, function(data){
		var attrs = '';
		jQuery.each(data.attrs.named, function(key, value){
			if( value!=undefined && value!='undefined' && value!='' ){
				attrs += key+'="'+ value +'" ';
			}
		})
		
		return get_blox_element_logos(data.content, attrs);
	});
	return $content;
}

function revert_shortcode_logos($content){
	$content.find('.blox_logos').each(function(){
		attr = '';
		var temp_val = '';

		for (var i = 0; i < logos_attrs.length; i++) {
            temp_val = jQuery(this).attr(logos_attrs[i])+'';
            if( temp_val!='undefined' && temp_val!='' ){
                attr += ' '+ logos_attrs[i] +'="'+ temp_val +'"';
            }
        }
		
		jQuery(this).replaceWith('[blox_logos'+attr+']'+jQuery(this).find('> .blox_item_content').html()+'[/blox_logos]');
	});
	return $content;
}


function add_event_blox_element_logos(){
	
	jQuery('.blox_logos').each(function(){
		var $this = jQuery(this);
		
		$this.find('.blox_item_actions .action_edit').unbind('click')
			.click(function(){

				var form_element = [
                			{
                				type: 'input',
                				id: 'logos_title',
                				label: 'Title',
                				value: $this.attr('title')
                			},
                			{
                				type: 'gallery',
                				id: 'logos_images',
                				label: 'Insert logos Images',
                				value: $this.attr('images')
                			},                			
                			{
                				type: 'select',
                				id: 'logos_layout',
                				label: 'Layout Type',
                				value: $this.attr('layout'),
                				options: [
                					{ value: 'default', label: '4 columns' },
                					{ value: '1', label: '1 column' },
                					{ value: '2', label: '2 columns' },
                					{ value: '3', label: '3 columns' },
                					{ value: '5', label: '5 columns' },
                					{ value: 'default', label: '6 columns' },
                					{ value: 'default', label: '7 columns' },
                					{ value: 'default', label: '8 columns' },
                					{ value: 'default', label: '9 columns' },
                					{ value: 'default', label: '10 columns' },
                					{ value: 'default', label: '11 columns' },
                					{ value: 'default', label: '12 columns' }                					
                				]
                			},
                			{
				                type: 'checkbox_flat',
				                id: 'logo_carousel',
				                label: 'Carousel',
				                value: $this.attr('carousel'),
				                options: [
				                    { value: 'yes', label: 'Yes' },
				                    { value: 'no', label: 'No' }
				                ]
				            },
				            {
				                type: 'number',
				                id: 'logo_slideseconds',
				                label: 'Autoplay seconds',
				                std: 5,
				                value: $this.attr('slideseconds'),
				                description: '0 means no auto sliding'
				            },
                			{
				                type: 'checkbox_flat',
				                id: 'logo_grayscale',
				                label: 'Grayscale',
				                value: $this.attr('grayscale'),
				                options: [
				                    { value: 'yes', label: 'Yes' },
				                    { value: 'no', label: 'No' }
				                ]
				            },
                			{
				                type: 'checkbox_flat',
				                id: 'logo_tooltip',
				                label: 'Hover Tooltip by Caption',
				                value: $this.attr('tooltip'),
				                options: [
				                    { value: 'yes', label: 'Yes' },
				                    { value: 'no', label: 'No' }
				                ]
				            }
            			];

                show_blox_form('Edit logos / Image slider', form_element, function($form){
                    $this.attr('title', jQuery('#logos_title').val() );
	                $this.attr('images', jQuery('#logos_images').val() );
	        		$this.attr('layout', jQuery('#logos_layout').val() );
	        		$this.attr('carousel', jQuery('#logos_carousel').val() );
	        		$this.attr('tooltip', jQuery('#logos_tooltip').val() );
	        		$this.attr('slideseconds', jQuery('#logos_slideseconds').val() );
	        		$this.attr('grayscale', jQuery('#logos_grayscale').val() );
                },
	            {
	                target: $this,
                	extra_field: true,
                	visibility: true
	            });
                
			});
		
		$this.find('.blox_item_actions .action_clone').unbind('click')
			.click(function(){
				$this.after($this.clone());
				add_event_blox_element_logos();
			});
			
		$this.find('.blox_item_actions .action_remove').unbind('click')
			.click(function(){
				$this.remove();
			});
	});
	
}
