// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
import { getAuth } from "firebase/auth";

// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries
// Your web app's Firebase configuration

const firebaseConfig = {
    apiKey: "AIzaSyDTPhJY6Bgmu0JlqVrROTkw8UPluKPyACo",
    authDomain: "droutlier-2025.firebaseapp.com",
    projectId: "droutlier-2025",
    storageBucket: "droutlier-2025.firebasestorage.app",
    messagingSenderId: "860507986531",
    appId: "1:860507986531:web:5e9985c34d1b88ba0bf656",
    measurementId: "G-HPMLHPXS2B"
  };

// Initialize Firebase
const app = initializeApp(firebaseConfig);

// Initialize Firebase Authentication and get a reference to the service
export const auth = getAuth(app);
export default app;