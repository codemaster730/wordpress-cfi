self.addEventListener('push', function(event) {
    var data = event.data.json();
    var title = data.title;
    delete data.title;
    self.registration.showNotification(title, data);
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    event.waitUntil(clients.matchAll({
        type: "window"
    }).then(function(clientList) {
        for (var i = 0; i < clientList.length; i++) {
            var client = clientList[i];
            if (client.url == event.notification.data.url && 'focus' in client)
                return client.focus();
        }
        if (clients.openWindow)
            return clients.openWindow(event.notification.data.url);
    }));
});