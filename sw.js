self.addEventListener("push", function (event) {
  console.log("push event", event);
  if (event.data) {
    var data = event.data.json();
    console.log("data", data);
    self.registration.showNotification(data.notification.title, {
      body: data.notification.body,
      icon: data.notification.icon,
      vibrate: data.notification.vibrate,
      data: data.notification.data,
      actions: data.notification.actions,
    });
  } else {
    console.log("Push event but no data");
  }
});

self.addEventListener("notificationclick", function (event) {
  console.log("EVENT", event);
  var notification = event.notification;
  var action = event.action;

  if (action === "answer") {
    // Navigate to the "answer" page
    event.waitUntil(clients.openWindow("answer.html"));
  } else {
    // Do something for other actions, if needed
  }

  notification.close();
});
