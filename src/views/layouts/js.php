<?php


?>
<script type="application/javascript">

$(document).ready(function () {
	setActiveUrl();
	postAuthButton();
	postCompleteButton();
	postTermButton();
	postEndpointButton();
	postSaleButton();
	postRefundButton();
	doExpandIcons();
	doSubmitHide();
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

function postCompleteButton() {

	var $div = $('div.post-complete');
	var $button = $('button.post-complete');
	var $pre = $('pre.post-complete');
	var $fields = $div.find('.form-control');
	var url = '<?= URL::route('ff-bank-em-gate') ?>';

	$button.off('click');
	$button.on('click', function () {
		$button.button('loading');
		var json = {type: 'complete'};
		json = jsonFromInputs($fields, json);
		$.post(url, json, function (data) {
			$pre.html(data);
			$button.button('reset');
		});
	});

}

function postSaleButton() {

	var $div = $('div.post-sale');
	var $button = $('button.post-sale');
	var $pre = $('pre.post-sale');
	var $fields = $div.find('.form-control');
	var url = '<?= URL::route('ff-bank-em-gate') ?>';

	$button.off('click');
	$button.on('click', function () {
		$button.button('loading');
		var json = {type: 'sale'};
		json = jsonFromInputs($fields, json);
		$.post(url, json, function (data) {
			$pre.html(data);
			$button.button('reset');
		});
	});

}

function postRefundButton() {

	var $div = $('div.post-refund');
	var $button = $('button.post-refund');
	var $pre = $('pre.post-refund');
	var $fields = $div.find('.form-control');
	var url = '<?= URL::route('ff-bank-em-gate') ?>';

	$button.off('click');
	$button.on('click', function () {
		$button.button('loading');
		var json = {type: 'refund'};
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
	var $button = $('button.post-endpoint');
	var urlSign = '<?= URL::route('ff-bank-em-sign') ?>';

	$button.off('click');
	$button.on('click', function () {
		$button.button('loading');

		var json = {type: 'endpoint'};
		json = jsonFromInputs($fields, json);

		// get sign, submit form
		$.post(urlSign, json, function (data) {
			json.input.sign = data;
			var params = $.param(json.input);
			var $modal = $('#pay-online');
			$modal.find('iframe').attr('src', '<?= URL::route('ff-bank-em-endpoint') ?>?' + params);
			$modal.modal({});
			$button.button('reset');
		});

	});

}

function backToShop() {

	var $link = $('.link-to-shop');
	$link.off('click');
	$link.on('click', function () {
		try {
			opener.location.reload();
		} catch (e) {
		}
		return false;
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

function doExpandIcons() {
	var $icons = $('h3.panel-title a');
	$icons.off('click');
	$icons.on('click', function () {
		var $link = $(this);
		var $icon = $link.find('i');
		var $panel = $link.parents('div.panel.panel-primary');
		var closed = $icon.hasClass('fa-expand');
		var $body = $panel.find('.panel-body');
		if (closed) {
			$icon.removeClass('fa-expand');
			$icon.addClass('fa-compress');
			$body.fadeIn();
		} else {
			$icon.addClass('fa-expand');
			$icon.removeClass('fa-compress');
			$body.fadeOut();
		}
		return false;
	});
}

function doSubmitHide() {
	var $buttons = $('form .submit-hide');
	$buttons.off('click');
	$buttons.on('click', function(){
		var $thisBtn = $(this);
		$thisBtn.button('loading');
		$thisBtn.parents('form').addClass('submit-hide-form');
		setTimeout(submitParentForm, 1000);
		return false;
	});
}

function submitParentForm()
{
	$('form.submit-hide-form').submit();
}

</script>