var staticAssets = [
	'/img/back.png',
	'/fonts/MYRIADPRO-REGULAR.woff',
	'/js/svg.js'
];
var version = '0.1';
var cacheName = version + '::' + 'staticAssets';
var cacheNameNonStatic = version + '::' + 'liveAssets';
this.addEventListener('install', function (event) {
	event.waitUntil(
		caches.open(cacheName).then(function (cache) {
			return cache.addAll(staticAssets);
		})
	);
});
this.addEventListener('fetch', function (event) {
	event.respondWith(
		caches.open(cacheName).then(function (cacheS) {
			return cacheS.match(event.request).then(function (response) {
				if (response !== undefined) {
					return response;
				}
				return caches.open(cacheNameNonStatic).then(function (cache) {
					return fetch(event.request).then(function (response) {
						var t = response.clone();
						cache.put(event.request, t);
						return response;
					}, function () {
						return cache.match(event.request).then(function (response) {
							if (response == undefined) {
								throw new Error();
							}
							return response;
						});
					})
				})
			})
		})
	);
});