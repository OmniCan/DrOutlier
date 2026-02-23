importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js');
   
firebase.initializeApp({
    apiKey: "AIzaSyCDVQ4usmDvRgBR_e9bmJLtzVpq8KLHlRQ",
    projectId: "analog-opus-419707",
    messagingSenderId: "550250185445",
    appId: "1:550250185445:android:695076c55fb00defed8a0c"
});
  
const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function({data:{title,body,icon}}) {
    return self.registration.showNotification(title,{body,icon});
});
