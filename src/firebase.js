// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
import { getAuth } from "firebase/auth";

// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries
// Your web app's Firebase configuration

const firebaseConfig = {
    apiKey: "AIzaSyB1VbT-ol84foPtZ3BKeXqGVqfzzbyYBjE",
    authDomain: "test-db1a0.firebaseapp.com",
    projectId: "test-db1a0",
    storageBucket: "test-db1a0.firebasestorage.app",
    messagingSenderId: "468666640276",
    appId: "1:468666640276:web:e6e80af9b1307a7206d181",
    measurementId: "G-X8VY7GQ9DT"
  };

// Initialize Firebase
const app = initializeApp(firebaseConfig);

// Initialize Firebase Authentication and get a reference to the service
export const auth = getAuth(app);
export default app;