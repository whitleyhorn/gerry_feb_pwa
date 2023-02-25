<?php
  require_once '../credentials.php';
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Push Notification Test</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="manifest" crossorigin="use-credentials" href="manifest.json">
    <style>
      /* Styles */
    </style>
  </head>
  <body>
    <h1>Push Notification PWA Test</h1>
    <p>Testing is fun!</p>
    <button id="send-notification-button" style="display: none">Send Notification</button>
  </body>
  <script>
  // Check if service worker is supported and register it
  const registerServiceWorker = async () => {
    if ("serviceWorker" in navigator) {
      try {
        const registration = await navigator.serviceWorker.register("/sw.js");
        if (registration.installing) {
          console.log("Service worker installing");
        } else if (registration.waiting) {
          console.log("Service worker installed");
        } else if (registration.active) {
          console.log("Service worker active");

          // Check if subscription already exists
          const existingSubscription = await checkSubscription();

console.log(existingSubscription);
          // If the subscription doesn't already exist, add it to the database
          if (!existingSubscription) {
            const subscription = await registration.pushManager.subscribe({ applicationServerKey: <?=json_encode($vapid['public']);?>, userVisibleOnly: true });
            await fetch("/subscribe.php", {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
              },
              body: JSON.stringify(subscription),
            });
          }
        }
      } catch (error) {
        console.error(`Registration failed with ${error}`);
      }
    }
  };

  registerServiceWorker();



    // Ask for user's permission to show notifications
    if (Notification.permission === 'default') {
      Notification.requestPermission().then(function(permission) {
        if (permission === 'granted') {
          console.log('Notification permission granted!');
        }
      });
    }

    // this button will initiate a test push notification. It can be used to confirm that the push notifications work at all, but is otherwise quite limited in its use, so the display should probably be set to "none" most of the time to avoid confusing the lawyers.
    /*
    const sendNotificationButton = document.getElementById(
      "send-notification-button"
    );
    sendNotificationButton.addEventListener("click", () => {
      if ("serviceWorker" in navigator && "PushManager" in window) {
        navigator.serviceWorker.getRegistration().then((registration) => {
          let options = {
            body: 'Incoming Call',
            icon: 'assets/icon.png',
            vibrate: [200, 100, 200, 100, 200, 100, 200],
            actions: [
              {
                action: "answer",
                icon: "assets/answer.png",
                title: "Answer"
              },
              {
                action: "cancel",
                icon: "assets/cancel.png",
                title: "Cancel"
              }
            ],
            data: {primaryKey: 1} // helps us know which notification was clicked
          };
          registration.showNotification("Incoming call", options)
        });
      }
    });
    */

    // *********
    async function checkSubscription() {
      try {
        const registration = await navigator.serviceWorker.ready;
        const subscription = await registration.pushManager.getSubscription();
        if (subscription) {
          const response = await fetch("check-subscription.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify(subscription)
          });
          const result = await response.json();
          if (result.status === "success") {
            return result.exists;
          } else {
            console.error(result.message);
            return false;
          }
        } else {
          console.log("No subscription found");
          return false;
        }
      } catch (err) {
        console.error(`Error checking subscription: ${err}`);
        return false;
      }
    }


  </script>
</html>

