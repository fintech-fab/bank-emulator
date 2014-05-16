<?php


?>
<script type="application/javascript">

	$(document).ready(function () {
		setActiveUrl();
		postAuthButton();
		postTermButton();
		postEndpointButton();
	});


	function setActiveUrl() {
		var href = '<?= URL::current() ?>';
		var $links = $('.nav.navbar-nav');
		var $link = $links.find('a[href="' + href + '"]');
		$link.parent().addClass('active');
	}

	function postTermButton() {

		var $button = $('button.term-options');
		var $fields = $('.form-control.term-options');
		var url = '<?= URL::route('ff-bank-em-term-post') ?>';

		$button.off('click');
		$button.on('click', function () {
			$button.button('loading');
			var json = jsonFromInputs($fields);
			$.post(url, json, function (data) {
				alert(data);
				$button.button('reset');
			});
		});

	}

	function postAuthButton() {

		var $div = $('div.post-auth');
		var $button = $('button.post-auth');
		var $pre = $('pre.post-auth');
		var $fields = $div.find('.form-control');
		var url = '<?= URL::route('ff-bank-em-gate') ?>';

		$button.off('click');
		$button.on('click', function () {
			$button.button('loading');
			var json = {type: 'auth'};
			json = jsonFromInputs($fields, json);
			$.post(url, json, function (data) {
				$pre.html(data);
				$button.button('reset');
			});
		});

	}

	function postEndpointButton() {

		var $form = $('form.post-endpoint');
		var $fields = $form.find('.form-control');
		var $sign = $form.find('input[name="sign"]');
		var $button = $('button.post-endpoint');
		var urlSign = '<?= URL::route('ff-bank-em-sign') ?>';

		$button.off('click');
		$button.on('click', function () {
			$button.button('loading');

			var json = {type: 'endpoint'};
			json = jsonFromInputs($fields, json);

			// get sign, submit form
			$.post(urlSign, json, function (data) {
				$sign.val(data);
				$form.submit();
			});

		});

	}


	function jsonFromInputs($fields, json) {
		if (!json) {
			json = {};
		}
		if (!json.input) {
			json.input = {};
		}
		$fields.each(function () {
			json.input[$(this).attr('name')] = $(this).val();
		});
		return json;
	}


</script>