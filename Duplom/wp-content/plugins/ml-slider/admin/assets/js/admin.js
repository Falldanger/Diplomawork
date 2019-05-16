jQuery(function($) {
    
        /**
         * Allow the user to click on the element to select it.
         *
         * @param string elm Element The html element to be selected
         */
        var metaslider_select_text = function (elm) {
            var range;
            var selection;

            // Most browsers will be able to select the text
            if (window.getSelection) {
                selection = window.getSelection();
                range = document.createRange();
                range.selectNodeContents(elm);
                selection.removeAllRanges();
                selection.addRange(range);
            } else if (document.body.createTextRange) {
                range = document.body.createTextRange();
                range.moveToElementText(elm);
                range.select();
            }

            // Some browsers will be able to copy the text too!
            try {
                if (document.execCommand('copy')) {
                    var notice = new MS_Notification(metaslider.success_language, metaslider.copied_language, undefined, 'is-success');
                    notice.fire(2000);
                }
            } catch (err) {
                console.warn('MetaSlider: Couldn\'t copy the text');
            }
        }

        // Select the shortcode on click
        $('.ms-shortcode').on('click', function () {
            metaslider_select_text(this);
        });

        // Select the entire codeblock when the button is clicked
		document.getElementById('ms-copy-all') && 
			document.getElementById('ms-copy-all').addEventListener('click', function (event) {
				event.preventDefault()
				metaslider_select_text(document.getElementById('ms-entire-code'))
        })

		// Update the shortcode when the button is clicked (id, title)
		document.getElementById('ms-copy-type') && 
			document.getElementById('ms-copy-type').addEventListener('click', function (event) {
				event.preventDefault()
				
				// Hide the current shortcode text
				$('#ms-shortcode-' + $(this).data('type')).css('display', 'none')

				// update the button name
				$(this).prop('title', 'Show ' + $(this).data('type'))

				// Set the expected shortcode text
				if ('title' === $(this).data('type')) {
					$(this).data('type', 'id')
				} else {
					$(this).data('type', 'title')
				}

				// Show the shortcode text
				$('#ms-shortcode-' + $(this).data('type')).css('display', 'inline')
        })

        /**
         * Filter out spaces when copying the shortcode.
         */
		document.getElementById('ms-entire-code') &&
			document.getElementById('ms-entire-code').addEventListener('copy', function(event) {
				var text = window.getSelection()
					.toString().split("'")
					.map(function(string, index) {
						return string.trim()
					}).join("'")
            event.clipboardData.setData('text/plain', text)
            event.preventDefault()
        })

        /**
         * Event listening to media library edits
         */
        var media_library_events = {
            loaded: false,
            /**
             * Attaches listenTo event to the library collection
             * 
             * @param modal object wp.media modal 
             */
            attach_event: function(modal) {
                var library = modal.state().get('library');
                modal.listenTo(library, 'change', function(model) { 
                    media_library_events.update_slide_infos({
                        id: model.get('id'),
                        caption: model.get('caption'),
                        title: model.get('title'),
                        alt: model.get('alt'),
                    });
                });
            },
            /**
             * Updates slide caption and other infos when a media is edited in a modal
             * 
             * @param object infos 
             */
            update_slide_infos: function(infos) {
                var $slides = $('.slide').filter(function(i){
                    return $(this).data('attachment-id') == infos.id;
                });
                infos.caption ? $('.caption .default', $slides).html(infos.caption) : $('.caption .default', $slides).html('&nbsp;');
                infos.title ? $('.title .default', $slides).html(infos.title) : $('.title .default', $slides).html('&nbsp;');
                infos.alt ? $('.alt .default', $slides).html(infos.alt) : $('.alt .default', $slides).html('&nbsp;');
            }
        };
        
        /**
         * UI for adding a slide. Managed through the WP media upload UI
         * Event managed here.
         */
        var create_slides = window.create_slides = wp.media.frames.file_frame = wp.media({
            multiple: 'add',
            frame: 'post',
            library: {type: 'image'}
        });
        create_slides.on('insert', function() {
            MetaSlider_Helpers.loading(true)
            
            var slide_ids = [];
            create_slides.state().get('selection').map(function(media) {
                slide_ids.push(media.toJSON().id);
			});
			
			// Remove the events for image APIs
			remove_image_apis()
    
            var data = {
                action: 'create_image_slide',
                slider_id: window.parent.metaslider_slider_id,
                selection: slide_ids,
                _wpnonce: metaslider.create_slide_nonce
            };

            // TODO: Create micro feedback to the user. 
            // TODO: Adding lots of slides locks up the page due to 'resizeSlides' event
            $.ajax({
                url: metaslider.ajaxurl, 
                data: data,
                type: 'POST',
                beforeSend: function() { MetaSlider_Helpers.loading(true); },
                complete: function() { MetaSlider_Helpers.loading(false); },
                error: function(response) {    
                    alert(response.responseJSON.data.message);
                },
                success: function(response) {
    
					/**
					 * Echo Slide on success
					 * TODO: instead have it return data and use JS to render it
					 */
					$('.metaslider table#metaslider-slides-list').append(response)
					MetaSlider_Helpers.loading(false)
					$('.metaslider table#metaslider-slides-list').trigger('resizeSlides')
					$(document).trigger('metaslider/slides-added')
				}
            });
		});

        /**
         * Starts to watch the media library for changes 
         */
        create_slides.on('attach', function() {
            if (!media_library_events.loaded) {
                media_library_events.attach_event(create_slides);
            }
		});
		
		/**
		 * Fire events when the modal is opened
		 * Available events: create_slides.on('all', function (e) { console.log(e) })
		 */
		// This is also a little "hack-ish" but necessary since we are accessing the UI indirectly
		create_slides.on('open activate uploader:ready', function() {
			add_image_apis()
		})
		create_slides.on('deactivate close', function() {
			remove_image_apis()
		})

        /**
         * I for changing slide image. Managed through the WP media upload UI
         * Initialized dynamically due to multiple slides.
         */
        var update_slide_frame;
    
        /**
         * Opens the UI for the slide selection.
         */
        $('.metaslider').on('click', '.add-slide', function(event) {
            event.preventDefault();
            create_slides.open();
    
            // Remove the Media Library tab (media_upload_tabs filter is broken in 3.6)
            // TODO investigate if this is needed
            $(".media-menu a:contains('Media Library')").remove();
        });

        /**
         * Handles changing an image when edited by the user.
         */
        $('.metaslider').on('click', '.update-image', function(event) {
            event.preventDefault();
            var $this = $(this);
            var current_id = $this.data('attachment-id');

            /**
             * Opens up a media window showing images
             */
			update_slide_frame = window.update_slide_frame = wp.media.frames.file_frame = wp.media({
                title: MetaSlider_Helpers.capitalize(metaslider.update_image),
                library: {type: 'image'},
                button: {
                    text: MetaSlider_Helpers.capitalize($this.attr('data-button-text'))
                }
            });

            /**
             * Selects current image
             */
            update_slide_frame.on('open', function() {
                if (current_id) {
                    var selection = update_slide_frame.state().get('selection');
					selection.reset([wp.media.attachment(current_id)]);

					// Add various image APIs
					add_image_apis($this.data('slideType'), $this.data('slideId'))
                }
            });

            /**
             * Starts to watch the media library for changes 
             */            
            update_slide_frame.on('attach', function() {
                if (!media_library_events.loaded) {
                    media_library_events.attach_event(update_slide_frame);
                }
            });
            
            /**
             * Open media modal
             */
            update_slide_frame.open();
            
            /**
             * Handles changing an image in DB and UI
             */
            update_slide_frame.on('select', function() {
                var selection = update_slide_frame.state().get('selection');
                selection.map(function(attachment) {
                    attachment = attachment.toJSON();
                    new_image_id = attachment.id;
                    selected_item = attachment;
				});
				
				// Remove the events for image APIs
				remove_image_apis()

                /**
                 * Updates the meta information on the slide
                 */
                var data = { 
                    action: 'update_slide_image',
                    _wpnonce: metaslider.update_slide_image_nonce,
                    slide_id: $this.data('slideId'),
                    slider_id: window.parent.metaslider_slider_id,
                    image_id: new_image_id
                };
                
                $.ajax({
                    url: metaslider.ajaxurl, 
                    data: data,
                    type: 'POST',
                    beforeSend: function() { MetaSlider_Helpers.loading(true); },
                    complete: function() {MetaSlider_Helpers.loading(false); },
                    error: function(response) {    
                        alert(response.responseJSON.data.message);
                    },
                    success: function(response) {
                       /**
                        * Updates the image on success
                        */
                        $('#slide-' + $this.data('slideId') + ' .thumb')
                            .css('background-image', 'url(' + response.data.thumbnail_url + ')');
                        // set new attachment ID
                        var $edited_slide_elms = $('#slide-' + $this.data('slideId') + ', #slide-' + $this.data('slideId') + ' .update-image');
                        $edited_slide_elms.data('attachment-id', selected_item.id);
                        
                        if (response.data.thumbnail_url) {
                            $('#slide-' + $this.data('slideId')).trigger('metaslider/attachment/updated', response.data);
                        }

                        // update default infos to new image
                        media_library_events.update_slide_infos({
                            id: selected_item.id,
                            caption: selected_item.caption,
                            title: selected_item.title,
                            alt: selected_item.alt,
                        });
                        $(".metaslider table#metaslider-slides-list").trigger('resizeSlides');
                    }
                });
			});

			update_slide_frame.on('close', function() {
				remove_image_apis()
			})
			create_slides.on('close', function() {
				remove_image_apis()
			})
		})

	/**
	 * Add all the image APIs. Add events everytime the modal is open
	 * TODO: refactor out hard-coded unsplash (can wait until we add a second service)
	 * TODO: right now this replaces the content pane. It might take some time but look for more native integration
	 * TODO: It gets a little bit buggy when someone triggers a download and clicks around. Maybe not important.
	 */
	var unsplash_api_events = function(event) {
		event.preventDefault()

		// Some things shouldn't happen when we're about to reload
		if (window.metaslider.about_to_reload) return

		// Set this tab as active
		$(this).addClass('active').siblings().removeClass('active')

		// If the image api container exists we don't want to create it again
		if ($('#image-api-container').length) return

		// Move the content and trigger vue to fetch the data
		// Add a container to house the content
		$(this).parents('.media-frame-router').siblings('.media-frame-content').append('<div id="image-api-container"></div>')

		// Add content to the container
		$('#image-api-container').append('<metaslider-external source="unsplash" :slideshow-id="' + window.parent.metaslider_slider_id + '" :slide-id="' + window.metaslider.slide_id + '" slide-type="' + (window.metaslider.slide_type || 'image') + '"></metaslider-external>')
		
		// Tell our app to render a new component
		$(window).trigger('metaslider/initialize_external_api', {
			'selector': '#image-api-container'
		})

		// Discard these
		delete window.metaslider.slide_id
		delete window.metaslider.slide_type
	}
	var add_image_apis = function (slide_type, slide_id) {

		// This is the pro layer screen (not currently used)
		if ($('.media-menu-item.active:contains("Layer")').length) {
			// If this is the layer slide screen and pro isnt installed, exit
			if (!window.metaslider.pro_supports_imports) return
			window.metaslider.slide_type = 'layer'
		}

		// If slide type is set then override the above because we're just updating an image
		if (slide_type) {
			window.metaslider.slide_type = slide_type
		}

		window.metaslider.slide_id = slide_id

		// Unsplash - First remove potentially leftover tabs in case the WP close event doesn't fire
		$('.unsplash-tab').remove()
		$('.media-frame-router .media-router').append('<a href="#" id="unsplash-tab" class="unsplash-tab">Unsplash Library</a>')
		$('.toplevel_page_metaslider').on('click', '.unsplash-tab', unsplash_api_events)

		// Each API will fake the container, so if we click on a native WP container, we should delete the API container
		$('.media-frame-router .media-router .media-menu-item').on('click', function() {

			// Destroy the component (does clean up)
			$(window).trigger('metaslider/destroy_external_api')

			// Additionally set the active tab
			$(this).addClass('active').siblings().removeClass('active')
		})
	}
	
	/**
	 * Remove tab and events for api type images. Add this when a modal closes to avoid duplicate events
	 */
	var remove_image_apis = function() {

		// Some things shouldn't happen when we're about to reload
		if (window.metaslider.about_to_reload) return

		// Tell tell components they are about to be removed
		$(window).trigger('metaslider/destroy_external_api')

		$('.toplevel_page_metaslider').off('click', '.unsplash-tab', unsplash_api_events)
		$('.unsplash-tab').remove()

		// Since we will destroy the container each time we should add the active class to whatever is first
		$('.media-frame-router .media-router > a').first().trigger('click')
	}

        /** 
        * Handles changing caption mode
        */
        $('.metaslider').on('change', '.js-inherit-from-image', function(e){
            var $this = $(this);
            var $parent = $this.parents('.can-inherit');
            var input = $parent.children('textarea,input[type=text]');
            var default_item = $parent.children('.default');
            if ($this.is(':checked')) {
                $parent.addClass('inherit-from-image');
            } else {
                $parent.removeClass('inherit-from-image');
                input.focus();
                if ('' === input.val()) {
                    if (0 === default_item.find('.no-content').length) {
                        input.val(default_item.html());
                    }
                }
            }
    
        });

        /**
         * delete a slide using ajax (avoid losing changes)
         */
        $(".metaslider").on('click', '.delete-slide', function(event) {
            event.preventDefault();
            var $this = $(this);
            var data = {
                action: 'delete_slide',
                _wpnonce: metaslider.delete_slide_nonce,
                slide_id: $this.data('slideId'),
                slider_id: window.parent.metaslider_slider_id
            };

            // Set the slider state to deleting
            $this.parents('#slide-' + $this.data('slideId'))
                 .removeClass('ms-restored')
                 .addClass('ms-deleting')
                 .append('<div class="ms-delete-overlay"><i style="height:24px;width:24px"><svg class="ms-spin" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-loader"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/></svg></i></div>');
            $this.parents('#slide-' + $this.data('slideId'))
                 .find('.ms-delete-status')
                 .remove();
            
            $.ajax({
                url: metaslider.ajaxurl, 
                data: data,
                type: 'POST',
                error: function(response) {

                    // Delete failed. Remove delete state UI
                    alert(response.responseJSON.data.message);
                    $slide = $this.parents('#slide-' + $this.data('slideId'));
                    $slide.removeClass('ms-deleting');
                    $slide.find('.ms-delete-overlay').remove();
                },
                success: function(response) {
                    var count = 10;

                    // Remove deleting state and add a deleted state with restore option
                    setTimeout(function() {
                        $slide = $this.parents('#slide-' + $this.data('slideId'));
                        $slide.addClass('ms-deleted')
                             .removeClass('ms-deleting')
                             .find('.metaslider-ui-controls').append(
                                '<button class="undo-delete-slide" title="' + metaslider.restore_language + '" data-slide-id="' + $this.data('slideId') + '">' + metaslider.restore_language + '</button>'
                        );

                        // Grab the image from the slide
                        var img = $slide.find('.thumb').css('background-image')
                                        .replace(/^url\(["']?/, '')
                                        .replace(/["']?\)$/, '');

                        // If the image is the same as the URL then it's empty (external slide type)
                        img = (window.location.href === img) ? '' : img;
                        
                        // Send a notice to the user
                        var notice = new MS_Notification(metaslider.deleted_language, metaslider.click_to_undo_language, img);

                        // Fire the notice and set callback to undo
                        notice.fire(10000, function() {
                            jQuery('#slide-' + $this.data('slideId'))
                                .addClass('hide-status')
                                .find('.undo-delete-slide').trigger('click');
                        });

                        // If the trash link isn't there, add it in (without counter)
                        if ('none' == $('.restore-slide-link').css('display')) {
                            $('.restore-slide-link').css('display', 'inline');
                        }
                    }, 1000);
                }
            });
        });

        /**
         * delete a slide using ajax (avoid losing changes)
         */
        $(".metaslider").on('click', '.undo-delete-slide, .trash-view-restore', function(event) {
            event.preventDefault();
            var $this = $(this);
            var data = {
                action: 'undelete_slide',
                _wpnonce: metaslider.undelete_slide_nonce,
                slide_id: $this.data('slideId'),
                slider_id: window.parent.metaslider_slider_id
            };

            // Remove undo button
            $('#slide-' + $this.data('slideId')).find('.undo-delete-slide').html('');

            // Set the slider state to deleting
            $this.parents('#slide-' + $this.data('slideId'))
                 .removeClass('ms-deleted')
                 .addClass('ms-deleting')
                 .css('padding-top', '31px')
                 .append('<div class="ms-delete-overlay"><i style="height:24px;width:24px"><svg class="ms-spin" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-loader"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/></svg></i></div>');
            $this.parents('#slide-' + $this.data('slideId'))
                 .find('.ms-delete-status')
                 .remove();
            $this.parents('#slide-' + $this.data('slideId'))
                 .find('.delete-slide')
                 .focus();

            $.ajax({
                url: metaslider.ajaxurl, 
                data: data,
                type: 'POST',
                error: function(response) {
                    
                    // Undelete failed. Remove delete state UI
                    $slide = $this.parents('#slide-' + $this.data('slideId'));
                    $slide.removeClass('ms-restoring').addClass('ms-deleted');
                    $slide.find('.ms-delete-overlay').remove();

                    // If there was a WP error, this should be populated:
                    if (response.responseJSON) {
                        alert(response.responseJSON.data.message);
                    } else {
                        alert('There was an error with the server and the action could not be completed.');
                    }
                },
                success: function(response) {

                    // Restore to original state
                    $slide = $this.parents('#slide-' + $this.data('slideId'));
                    $slide.addClass('ms-restored')
                    $slide.removeClass('ms-deleting')
                          .find('.undo-delete-slide, .trash-view-restore').remove();
                    $slide.find('.ms-delete-overlay').remove();
                    $('#slide-' + $this.data('slideId') + ' h4').after('<span class="ms-delete-status is-success">' + metaslider.restored_language + '</span>');

                    // We can try to remove the buton actions too (trashed view)
                    $('#slide-' + $this.data('slideId')).find('.row-actions.trash-btns').html('');

                    // Grab the image from the slide
                    var img = $slide.find('.thumb').css('background-image')
                                    .replace(/^url\(["']?/, '')
                                    .replace(/["']?\)$/, '');

                    // If the image is the same as the URL then it's empty (external slide type)
                    img = (window.location.href === img) ? '' : img;

                    // Send a success notification
                    var notice = new MS_Notification(metaslider.restored_language, '', img, 'is-success');
                    
                    // Fire the notice
                    notice.fire(5000);
                }
            });
        });
        
        // Enable the correct options for this slider type
        var switchType = function(slider) {
            $('.metaslider .option:not(.' + slider + ')').attr('disabled', 'disabled').parents('tr').hide();
            $('.metaslider .option.' + slider).removeAttr('disabled').parents('tr').show();
            $('.metaslider input.radio:not(.' + slider + ')').attr('disabled', 'disabled');
            $('.metaslider input.radio.' + slider).removeAttr('disabled');
    
            $('.metaslider .showNextWhenChecked:visible').parent().parent().next('tr').hide();
            $('.metaslider .showNextWhenChecked:visible:checked').parent().parent().next('tr').show();
    
            // make sure that the selected option is available for this slider type
            if ($('.effect option:selected').attr('disabled') === 'disabled') {
                $('.effect option:enabled:first').attr('selected', 'selected');
            }
    
            // make sure that the selected option is available for this slider type
            if ($('.theme option:selected').attr('disabled') === 'disabled') {
                $('.theme option:enabled:first').attr('selected', 'selected');
            }
        };
    
        // enable the correct options on page load
        switchType($(".metaslider .select-slider:checked").attr("rel"));
    
        var toggleNextRow = function(checkbox) {
            if(checkbox.is(':checked')){
                checkbox.parent().parent().next("tr").show();
            } else {
                checkbox.parent().parent().next("tr").hide();
            }
        }
    
        toggleNextRow($(".metaslider .showNextWhenChecked"));
    
        $(".metaslider .showNextWhenChecked").on("change", function() {
            toggleNextRow($(this));
        });
    
        // mark the slide for resizing when the crop position has changed
        $(".metaslider").on('change', '.left tr.slide .crop_position', function() {
            $(this).closest('tr').data('crop_changed', true);
        });
    
        // handle slide libary switching
        $(".metaslider .select-slider").on("click", function() {
            switchType($(this).attr("rel"));
        });
    
        // return a helper with preserved width of cells
        var metaslider_sortable_helper = function(e, ui) {
            ui.children().each(function() {
                $(this).width($(this).width());
            });
            return ui;
        };
    
        // drag and drop slides, update the slide order on drop
        $(".metaslider table#metaslider-slides-list > tbody").sortable({
            helper: metaslider_sortable_helper,
            handle: "td.col-1",
            stop: function() {
                $(".metaslider table#metaslider-slides-list").trigger("updateSlideOrder");
                $("#ms-save").click();
            }
        });
    
        // bind an event to the slides table to update the menu order of each slide
        $(".metaslider table#metaslider-slides-list").live("updateSlideOrder", function(event) {
            $("tr", this).each(function() {
                $("input.menu_order", $(this)).val($(this).index());
            });
        });

        $("input.width, input.height").on('change', function(e) {
            $(".metaslider table#metaslider-slides-list").trigger('metaslider/size-has-changed', {
                width: $("input.width").val(),
                height: $("input.height").val()
            });
        });

        // bind an event to the slides table to update the menu order of each slide
        $(".metaslider table#metaslider-slides-list").live("resizeSlides", function(event) {
            var slideshow_width = $("input.width").val();
            var slideshow_height = $("input.height").val();
    
            $("tr.slide input[name='resize_slide_id']", this).each(function() {
                $this = $(this);
    
                var thumb_width = $this.attr("data-width");
                var thumb_height = $this.attr("data-height");
                var slide_row = $(this).closest('tr');
                var crop_changed = slide_row.data('crop_changed');
    
                if (thumb_width != slideshow_width || thumb_height != slideshow_height || crop_changed) {
                    $this.attr("data-width", slideshow_width);
                    $this.attr("data-height", slideshow_height);
    
                    var data = {
                        action: "resize_image_slide",
                        slider_id: window.parent.metaslider_slider_id,
                        slide_id: $this.attr("data-slide_id"),
                        _wpnonce: metaslider.resize_nonce
                    };
    
                    $.ajax({
                        type: "POST",
                        data : data,
                        async: false,
                        cache: false,
                        url: metaslider.ajaxurl,
                        success: function(response) {
                            if (crop_changed) {
                                slide_row.data('crop_changed', false);
                            }
                            if (response.data.thumbnail_url) {
                                $this.closest('tr.slide').trigger('metaslider/attachment/updated', response.data);
                            }
                        }
                    });
                }
            });
        });
    
        $(document).ajaxStop(function() {
            $(".metaslider .spinner").hide().css('visibility', '');
            $(".metaslider button[type=submit]").removeAttr("disabled");
        });
    
        $(".useWithCaution").on("change", function(){
            if(!this.checked) {
                return alert(metaslider.useWithCaution);
            }
        });
    
        // helptext tooltips
        $('.tipsy-tooltip').tipsy({className: 'msTipsy', live: true, delayIn: 500, html: true, gravity: 'e'})
		$('.tipsy-tooltip-top').tipsy({live: true, delayIn: 500, html: true, gravity: 's'})
		$('.tipsy-tooltip-bottom').tipsy({ live: true, delayIn: 500, html: true, gravity: 'n' })
    
        // Select input field contents when clicked
        $(".metaslider .shortcode input, .metaslider .shortcode textarea").on('click', function() {
            this.select();
        });
    
        // return lightbox width
        var getLightboxWidth = function() {
            var width = parseInt($('input.width').val(), 10);
    
            if ($('.carouselMode').is(':checked')) {
                width = '75%';
            }
    
            return width;
        };
    
        // return lightbox height
        var getLightboxHeight = function() {
            var height = parseInt($('input.height').val(), 10);
            var thumb_height = parseInt($('input.thumb_height').val(), 10);
            if (isNaN(height)) {
                height = '70%';
            } else {
                height = height + 50;
                
                if (!isNaN(thumb_height) && 'thumbs' == $('input[name="settings[navigation]"]:checked').val()) {
                    height = height + thumb_height;
                }
            }
            return height;
        };
    
    
        // IE10 treats placeholder text as the actual value of a textarea
        // http://stackoverflow.com/questions/13764607/html5-placeholder-attribute-on-textarea-via-$-in-ie10
        var fixIE10PlaceholderText = function() {
            $("textarea").each(function() {
                if ($(this).val() == $(this).attr('placeholder')) {
                    $(this).val('');
                }
            });
        };
    
        $(".metaslider .ms-toggle .hndle, .metaslider .ms-toggle .handlediv").on('click', function() {
            $(this).parent().toggleClass('closed');
        });

        // Switch tabs within a slide on space press
        $('.metaslider-ui').on('keypress', 'ul.tabs > li > a', function(event) {
            if (32 === event.which) {
                event.preventDefault();
                $(':focus').trigger('click');
            }
        });

        // Event to switch tabs within a slide
        $(".metaslider-ui").on('click', 'ul.tabs > li > a', function(event) {
            event.preventDefault();
            var tab = $(this);

            // Hide all the tabs
            tab.parents('.metaslider-ui-inner')
               .children('.tabs-content')
               .find('div.tab').hide();
               
               // Show the selected tab
               tab.parents('.metaslider-ui-inner')
               .children('.tabs-content')
               .find('div.' + tab.data('tab_id')).show();

            // Add the class
            tab.parent().addClass("selected")
               .siblings().removeClass("selected");
        });

        // Switch slider types when on the label and pressing enter
        $('.metaslider-ui').on('keypress', '.slider-lib-row label', function (event) {
            if (32 === event.which) {
                event.preventDefault();
                $('.slider-lib-row #' + $(this).attr('for')).trigger('click');
            }
        });
    
	// AJAX save & preview
	$(".metaslider form").find("button[type=submit]").on("click", function(e) {
		e.preventDefault()
		$(".metaslider .spinner").show().css('visibility', 'visible')
		$(".metaslider input[type=submit]").attr("disabled", "disabled")

		// update slide order
		$(".metaslider table#metaslider-slides-list").trigger('updateSlideOrder')
		fixIE10PlaceholderText();

		// get some values from elements on the page:
		var the_form = $(this).parents("form")
		var url = the_form.attr("action")
		var button = $(this)

		var form_data = new FormData()
		the_form.serializeArray().forEach(function(data) {
			form_data.append(data.name, data.value)
		})

		$.ajax({
			type: "POST",
			data: form_data,
			cache: false,
			contentType: false,
			processData: false,
			url: url,
			success: function(data) {
				var response = $(data)
				$.when($(".metaslider table#metaslider-slides-list").trigger("resizeSlides")).done(function() {

					$("button[data-thumb]", response).each(function() {
						var $this = $(this)
						var editor_id = $this.attr("data-editor_id")
						$("button[data-editor_id=" + editor_id + "]")
							.attr("data-thumb", $this.attr("data-thumb"))
							.attr("data-width", $this.attr("data-width"))
							.attr("data-height", $this.attr("data-height"))
					});
					fixIE10PlaceholderText()

					// Send a message that vuejs can use to fire the preview
					// .prop and .data return undefined, so using attr
					if (button.attr('preview-id')) {
						$(window).trigger('metaslider/show-preview-' + button.attr('preview-id'))
					}
				})
			}
		})
	})

    // UI/Feedback
    // Events for the slideshow title
    $('.metaslider .nav-tab-active input[name="title"]').on('focusin', function() {

        // Expand the input box when a user wants to edit a slider title
        $(this).css('width', ($(this).val().length + 1) * 9);
    }).on('focusout', function() {

        // Retract and save the slideshow title
        $(this).css('width', 150);
        $("#ms-save").trigger('click');
    }).on('keypress', function() {

        // Pressing enter on the slide title saves it and focuses outside.
        if (13 === event.which) {
            event.preventDefault();
            $("#ms-save").trigger('click');
            $("button.add-slide").focus();
        }
    });


    // Bind the slider title & dropdown to the input.
    $('.metaslider input[name="title"]').on('input', function(event) {
        event.preventDefault();

        var title = new MS_Binder(".slider-title > h3");
        title.bind($(this).val());
		
		var shortcode_title = new MS_Binder("#ms-shortcode-title");
		shortcode_title.bind('title="' + $(this).val() + '"')

        var dropdown = document.querySelector('select[name="select-slideshow"]');
        if (dropdown) {
            var dropdownselectedoption = dropdown.options[dropdown.selectedIndex];
            dropdownselectedoption.text = $(this).val();
        }
	});
});

/**
 * Various helper functions to use throughout
 */
var MetaSlider_Helpers = {

    /**
     * Various helper functions to use throughout
     *
     * @param  string string A string to capitalise
     * @return string Returns capitalised string
     */
    capitalize: function(string) {
        return string.replace(/\b\w/g, function(l) { return l.toUpperCase(); });
    },

    /**
     * Sets some basic loading state UI elements of the app. Currently,
     * it only enables or disables the input and shows a loading spinner.#
     *
     * @param boolean state UI Elemetns
     */
    loading: function(state) {
        if (state) {
            jQuery(".metaslider .spinner").show().css('visibility', 'visible');
            jQuery(".metaslider button[type=submit]").attr('disabled', 'disabled');
        } else {
            jQuery(".metaslider .spinner").hide().css('visibility', '');
            jQuery(".metaslider button[type=submit]").removeAttr("disabled");
        }
    }
};

/**
 * Simple view binder
 * var elm = new MS_Binder("#selector");
 * elm.bind(200);
 */
var MS_Binder = function(selector) {
    this.dom = document.querySelector(selector);
    this.value = null;
};
 
MS_Binder.prototype.bind = function(value){
    if (value === this.value) return;
    
    this.value = value;
    this.dom.innerText = this.value;
};

/**
 * Simple notifications
 * var notice = new MS_Notification("Slide Deleted", "click to undo", 'img.jpg', 'success');
 * Can use a custom function for the callback as well
 * requires jQuery
 */
var MS_Notification = function(message, submessage, image, _classname) {
    this.panel = document.getElementById('ms-notifications');
    if (!this.panel) {
        this.panel = document.createElement('div');
        this.panel.id = "ms-notifications";
    }
    this.notice = jQuery('<div class="ms-notification"><div class="ms-notification-content"><h3></h3><p></p></div><div class="img"></div></div>');
    this.notice.find('h3').text(message);
    this.notice.find('p').text(submessage);

    // If there is an image, let's add it.
    if (('undefined' !== typeof image) && image.length) {
        this.notice.addClass('has-image')
        .find('.img')
        .append('<img width=50 height=50 src="' + image + '">');
    }

    // TODO add an option for svg
    // If an extra class is set, set it
    ('undefined' !== typeof _classname) && this.notice.addClass(_classname);
    
    // Append the panel to the body and
    jQuery(this.panel).appendTo(jQuery('body'));
};

/**
 * Hide a notification
 */
MS_Notification.prototype.hide = function() {
    var _this = this;
    _this.notice.addClass('finished');
    this.notice.fadeOut(500, function () {
        _this.notice.remove();
    });
};

/**
 * Launch a notification and add a click event
 *
 * @param int      delay    the time in milliseconds
 * @param Function callback a method on the object or anon function
 */
MS_Notification.prototype.fire = function(delay, callback) {
    var _this = this;
    var _callback = ('undefined' !== typeof callback) ? callback : 'hide';

    // Prepend this to the notification stack
    this.notice.prependTo(this.panel);

    // Automatically hide after the delay
    this.timeout = setTimeout(function() {
        _this.hide();
    }, delay);

    // Clear this timeout on click
    this.notice.on('click', function() {
        clearTimeout(_this.timeout);
    });

    // Pause the timeout on hover
    this.notice.on('mouseenter', function() {
        clearTimeout(_this.timeout);
    });
    
    // Restart the timeout after leaving
    this.notice.on('mouseleave', function() {
        _this.timeout = setTimeout(function() {
            _this.hide();
        }, delay);
    });

    // If callback is a method
    if (MS_Notification.prototype.hasOwnProperty(_callback)) {
        this.notice.on('click', function() {
            if ('hide' !== _callback) {
                _this.hide();
            }
            MS_Notification.call(_this[_callback]());
        });
    } else {

        // If the callback is a custom function
        this.notice.on('click', function() {
            _this.hide();
            _callback();
        });
    }
};
