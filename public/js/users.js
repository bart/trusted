define(['jquery'], function () {
		$(function() {
			$('.editUserBtn').click(function() {
				userId = $(this).data('user-id');

				$.get('/user/' + userId, function(data){
					$('#edit-username').val(data.username);
					domainItems = data.domains.join(', ');
					$('#edit-domains').val(domainItems);
					if(data.isAdmin === true) {
						$('#edit-isAdmin').attr('checked', true);
					}
					$('#edit-userId').val(userId);
				});
			});
		});
	}
);
