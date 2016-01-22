$(document).ready(function () {
	history.replaceState([window.location.pathname, $('.active').find('a').attr('href')], "", window.location.pathname + window.location.search);
	check($('a[href]').not('.autoexempt'));
	window.onpopstate = function (event) {
		if (event.state[0] != null) {
			var url = "/"+(event.state[0] === "/" ? "content/" : "content/" + event.state[0]);
			$.get(url, function (data) {
				if (data.split(/\r\n|\r|\n/).length == 1) {
					document.location = data;
					return;
				}
				document.title = data.substring(0, data.indexOf('\n'));
				var content=$('#content');
				content.html(data.substr(data.indexOf('\n')));
				check(content.find('a[href]').not('.autoexempt'));
				$('li').removeClass('active');
				if (event.state[1] != null) {
					$('#menu').find('a[href="' + event.state[1] + '"]').parent().addClass('active');
				}
			}).fail(function (jqXHR) {
				fail(jqXHR, event.state[0]);
			});
		}
	}
});
function check(target) {
	target.each(function () {
		$(this).click(function () {
			if ($(this).attr('href').substring(0, 4) !== "http") {
				var link = $(this);
				if(link.hasClass('autoexempt')) return;
				$.get("/"+($(this).attr("href") === "/" ? "content/" : "content/" + $(this).attr("href")), function (data) {
					if (data.split(/\r\n|\r|\n/).length == 1) {
						document.location = data;
						return;
					}
					$('li').removeClass('active');
					if (link.parent().prop('nodeName') === "LI") {
						link.parent().addClass('active');
					} else {
						var li = $('#menu').find('a[href="' + link.attr('href') + '"]');
						if (li !== null) {
							li.parent().addClass('active');
						}
					}
					history.pushState([link.attr("href"), $('.active').find('a').attr('href')], "", link.attr("href"));
					document.title = data.substring(0, data.indexOf('\n'));
					var content=$('#content');
					content.html(data.substr(data.indexOf('\n')));
					check(content.find('a[href]').not('.autoexempt'));
				}).fail(function (jqXHR) {
					fail(jqXHR, link.attr('href'));
				});
				return false;
			}
		});
	});
}
function fail(jqXHR, url) {
	var status = jqXHR.status;
	var codes = [];
	codes[200] = ['404 Not Found', 'Uh oh! Your page was not found.'];
	codes[403] = ['403 Forbidden', 'For some reason, you are forbidden to view this content.'];
	codes[404] = ['404 Not Found', 'Uh oh! Your page was not found.'];
	codes[405] = ['405 Method Not Allowed', 'The method specified in the Request-Line is not allowed for the specified resource.'];
	codes[408] = ['408 Request Timeout', 'Your browser failed to sent a request in the time allowed by the server.'];
	codes[500] = ['500 Internal Server Error', 'The request was unsuccessful due to an unexpected condition encountered by the server.'];
	codes[502] = ['502 Bad Gateway', 'The server received an invalid response from the upstream server while trying to fulfill the request.'];
	codes[504] = ['504 Gateway Timeout', 'The upstream server failed to send a request in the time allowed by the server.'];
	codes[0] = [status, 'An unknown error occurred.'];
	if (codes[status] == null) {
		status = 0;
	}
	history.pushState([url, null], "Error", url);
	$('li').removeClass('active');
	$('#content').html("<div>\
					<h1>" + codes[status][0] + "</h1>\
					<p>" + codes[status][1] + "</p>\
					</div>");
}