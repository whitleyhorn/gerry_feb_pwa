let clickedNotifications = new Map();
const notificationRepeatCount = 4;
const notificationDuration = 4400;

self.addEventListener("push", function (event) {
  console.log("push event", event);
  if (event.data) {
    var data = event.data.json();
    console.log("data", data);
    const tag = data.notification.tag;
    self.registration.showNotification(data.notification.title, {
      body: data.notification.body,
      icon: data.notification.icon,
      vibrate: data.notification.vibrate,
      data: data.notification.data,
      actions: data.notification.actions,
      tag: tag,
      renotify: data.notification.renotify,
    });
    // Set up interval to show notification again if it hasn't been clicked
    clickedNotifications.set(tag, false);
    let notificationCounter = 0;
    let intervalId = setInterval(function () {
      if (
        !clickedNotifications.get(tag) &&
        notificationCounter < notificationRepeatCount
      ) {
        self.registration.showNotification(data.notification.title, {
          body: data.notification.body,
          icon: data.notification.icon,
          vibrate: data.notification.vibrate,
          data: data.notification.data,
          actions: data.notification.actions,
          tag: tag,
          renotify: data.notification.renotify,
        });
        notificationCounter++;
      } else {
        clearInterval(intervalId);
        clickedNotifications.delete(tag);
      }
    }, notificationDuration);
  } else {
    console.log("Push event but no data");
  }
});

self.addEventListener("notificationclick", function (event) {
  console.log("EVENT", event);
  var notification = event.notification;
  var action = event.action;
  const tag = notification.tag;

  if (action === "answer") {
    // Navigate to the "answer" page
    event.waitUntil(clients.openWindow("answer.php"));
  } else {
    // Do something for other actions, if needed
  }

  notification.close();
  clickedNotifications.set(tag, true);
});
