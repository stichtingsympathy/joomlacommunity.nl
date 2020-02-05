ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	var readAll = $('[data-ed-notifications-read-all]');
	var notificationCounterWrapper = $('[data-ed-notifications-wrapper]');
	var notificationCounter = $('[data-ed-notifications-counter]');
	var notificationItems = $('[data-ed-notifications-items]');

	// Bind the read all actions
	readAll.on('click', function() {
		EasyDiscuss.ajax('site/views/notifications/markreadall').done(function(){

			// Reset notifications counter.
			notificationCounterWrapper.removeClass('has-new');
			notificationCounter.html(0);

			// Remove is-unread from notification's item.
			notificationItems.removeClass('is-unread');
			notificationItems.addClass('is-read');
		});
	});
});