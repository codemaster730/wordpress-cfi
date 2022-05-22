jQuery( function ( $ ) {
	const $grimlockLoginForms = $( '#grimlock-login-form-modal, #grimlock-login-form-inline' );
	if ( $grimlockLoginForms.length ) {
		const $pwdInput = $grimlockLoginForms.find( '#user_pass' );

		const $pwdVisibilityButton = $(
			'<button type="button" class="password-visibility">' +
				'<span class="dashicons dashicons-visibility" aria-hidden="true"></span>' +
			'</button>',
		);

		$grimlockLoginForms.find( '.login-password' ).append( $pwdVisibilityButton );

		$pwdVisibilityButton.on( 'click', function () {
			const inputType = $pwdInput.attr( 'type' );

			$pwdVisibilityButton.find( '.dashicons' )
				.removeClass( inputType === 'password' ? 'dashicons-visibility' : 'dashicons-hidden' )
				.addClass( inputType === 'password' ? 'dashicons-hidden' : 'dashicons-visibility' );

			$pwdInput.attr( 'type', inputType === 'password' ? 'text' : 'password' );
		} );

		const $inlineLoginForm = $( '#grimlock-login-form-inline' );
		if ( $inlineLoginForm.length ) {
			$inlineLoginForm.find( '#user_login' ).attr( 'placeholder', $inlineLoginForm.find( 'label[for="user_login"]' ).text() );
			$inlineLoginForm.find( '#user_pass' ).attr( 'placeholder', $inlineLoginForm.find( 'label[for="user_pass"]' ).text() );
		}
	}
} );
