jQuery(document).ready(function($){
	var counter = $('.ssbcounterfieldinpt').val();
	if (counter <= 1) {
$('.ssb_rmv_column').hide();
}

$('.ssb_add_column').on('click', function(event) {
event.preventDefault();
$('#loadingimg').show();
var counter = $('.ssb_nameidentifier').length;
var numb = counter*1+1;
var ssbdupe = $('div#grand_ssb_form_input>div#parent_ssb_form_input:last').clone(true);
var columer = ssbdupe.find('div.ssb_nameidentifier').html('<br/><hr><b>Column '+numb+'</b>');
var selecpage = ssbdupe.find("select[name*='ssb_page_namelistval']").attr({'name':'ssb_page_namelistval'+numb});
var limitwords = ssbdupe.find("input[name*='ssb_words_limit']").attr({'name':'ssb_words_limit'+numb});
var imageicon = ssbdupe.find("input[name*='ssb_image_oricon']").attr({'name':'ssb_image_oricon'+numb});

	var ssbpid = $('.ssb_postid').val();
	$('.ssbcounterfieldinpt').val(counter*1+1);
	
	var data = {
		action: 'ssb_action',
		cache: false,
		showcaserboxe_ajax_nonce: showcaserboxe_ajax_script_vars.showcaserboxe_ajax_nonce,		
		postid: ssbpid,
		ssbcolcount: counter
	};

	$.post(ajaxurl, data, function(response) {
	var counter = $('.ssbcounterfieldinpt').val();
	if (numb > 1) {
$('.ssb_rmv_column').show();
}
$('#loadingimg').hide();
ssbdupe.insertAfter('div#grand_ssb_form_input>div#parent_ssb_form_input:last').columer.selecpage.limitwords.imageicon;
});

});

// Remove Column
$('.ssb_rmv_column').on('click', function(e) {
e.preventDefault();
$('#loadingimg').show();
	var coundec = $('.ssbcounterfieldinpt').val();
	var ssbdecid = $('.ssb_postid').val();
	$('.ssbcounterfieldinpt').val(coundec*1-1);
	var data = {
		action: 'decrease_action',
		cache: false,
		showcaserboxe_ajax_nonce: showcaserboxe_ajax_script_vars.showcaserboxe_ajax_nonce,		
		postdecid: ssbdecid,
		ssbcoldec: coundec
	};

	$.post(ajaxurl, data, function(response) {
var coundec = $('.ssbcounterfieldinpt').val();
if (coundec <= 1) {
$('.ssb_rmv_column').hide();
} else {
$('.ssb_rmv_column').show();
}
$('#loadingimg').hide();	
$('div#grand_ssb_form_input>div#parent_ssb_form_input:last').remove();
});
});
});