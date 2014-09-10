define(['jquery'], function () {
		$(function() {
			$('.showCertBtn').click(function() {
				certId = $(this).data('cert-id');

				$.get('/cert/' + certId, function(data){
					$('#certificate-wrapper').html(data);
					$('#downloadCertLink').attr('href', '/cert/' + certId + '/download');
				});
			});

			$('.showKeyBtn').click(function() {
				certId = $(this).data('cert-id');

				$.get('/cert/' + certId + '/key', function(data){
					$('#key-wrapper').html(data);
					$('#downloadKeyLink').attr('href', '/cert/' + certId + '/key/download');
				});
			});
		});
	}
);
