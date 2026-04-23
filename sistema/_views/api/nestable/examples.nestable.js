/*
Name: 			UI Elements / Nestable - Examples
Written by: 	Okler Themes - (http://www.okler.net)
Theme Version: 	1.4.0
*/

(function( $ ) {

	'use strict';
		
	/*
	Update Output
	*/
	var updateOutput = function (e) {
		var list = e.length ? e : $(e.target),
			output = list.data('output');

		if (window.JSON) {
			output.val(window.JSON.stringify(list.nestable('serialize')));
		} else {
			output.val('JSON browser support required for this demo.');
		}
	};
	
	var updateOutput_rodape = function (e) {
		var list = e.length ? e : $(e.target),
			output_rodape = list.data('output_rodape');
			
		if (window.JSON) {
			output_rodape.val(window.JSON.stringify(list.nestable('serialize')));
		} else {
			output_rodape.val('JSON browser support required for this demo.');
		}
	};

	/*
	Nestable 1
	*/
	$('#nestable').nestable({
		group: 1
	}).on('change', updateOutput);

	$('#nestable_rodape').nestable({
		group: 1
	}).on('change', updateOutput_rodape);

	/*
	Output Initial Serialised Data
	*/
	$(function() {
		updateOutput($('#nestable').data('output', $('#nestable-output')));
		updateOutput_rodape($('#nestable_rodape').data('output_rodape', $('#nestable-output_rodape')));
	});

}).apply(this, [ jQuery ]);