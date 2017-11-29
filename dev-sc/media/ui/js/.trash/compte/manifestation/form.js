$(function(){

	$('form.check').parsley().subscribe('parsley:form:validate', function (formInstance) {

		var type = $('select[name="category"]')
			, cat = $('select[name="type"]')
			, msg = $('.invalid-form-error-message');

		// Clean
		msg.html('');

		// Si TYPE + CAT ne sont pas vide => ok
		if (type.val() && cat.val()) return;

		// else stop form submission
		formInstance.submitEvent.preventDefault();

		// and display a gentle message
		msg.html("Vous devez choisir un genre et un type").addClass("filled");

		return;
	});

})
