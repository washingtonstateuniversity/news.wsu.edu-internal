/**
 * Handle the submission of the announcements form at news.wsu.edu
 *
 * announcementSubmission.ajaxurl is available to us in the global scope
 */
( function( $, window ) {

	/**
	 * Perform very basic validation on the email address.
	 *
	 * @param email string containing email address to test.
	 * @returns {boolean} True if pattern matches. False for failure.
	 */
	let validate_email = function( email ) {
		let pattern = /\S+@\S+/;
		return pattern.test( email );
	};

	// Add datepicker elements to all date inputs.
	$( "#announcement-form-date" ).datepicker();

	/**
	 * Handle the submission of the form as well as basic validation of the
	 * content before submitting the ajax request.
	 */
	$( "#announcement-form-submit" ).click( function( e ) {

		// Don't actually submit the form.
		e.preventDefault();

		// Trigger the tinyMCE save method on the editor so that the textarea is filled properly.
		window.tinyMCE.get( "announcement-form-text" ).save();

		/**
		 * The title of the announcement.
		 *
		 * @type {string}
		 */
		let form_title = $( "#announcement-form-title" ).val();

		/**
		 * The email address associated with the announcement.
		 *
		 * @type {string}
		 */
		let form_email = $( "#announcement-form-email" ).val();

		/**
		 * The content of the announcement.
		 *
		 * @type {string}
		 */
		let form_text  = $( "#announcement-form-text" ).val();

		/**
		 * The date on which the announcement should be published.
		 *
		 * @type {array}
		 */
		let form_date = $( "#announcement-form-date" ).val();

		if ( "" === form_title ) {
			window.alert( "Please provide a title for the announcement." );
			return 0;
		}

		if ( false === validate_email( form_email ) ) {
			window.alert( "Please provide a contact email for the announcement." );
			return 0;
		}

		if ( "" === form_text ) {
			window.alert( "Please provide content for the announcement." );
			return 0;
		}

		if ( "" === form_date ) {
			window.alert( "Please enter the date on which this announcement should be published." );
			return 0;
		}

		// Build the data for our ajax call
		let data = {
			action: "submit_announcement",
			title:  form_title,
			text:   form_text,
			email:  form_email,
			date:  form_date,
			other:  $( "#announcement-form-other" ).val()
		};

		// Make the ajax call
		$.post( window.announcementSubmission.ajaxurl, data, function( response ) {
			if ( "success" === response ) {
				$( "#announcement-submission-form" ).html( "<span class=\"announcement-success\">Announcement submitted for approval.</span>" );
			} else {
				window.alert( "Error submitting form. Please try again." );
			}
		} );
	} );
} )( jQuery, window );
