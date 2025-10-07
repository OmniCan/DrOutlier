"use client"
import Footer from '@/components/Footer'
import Navbar from '@/components/Navbar'
import { createContext, use, useEffect, useState } from "react";
import baseUrl from '@/Services/BaseUrl';
import React from 'react'
import { Formik, Form, Field, ErrorMessage } from "formik";
import * as Yup from "yup";
import axios from "axios";
import Cookies from 'js-cookie';
import Link from 'next/link';
import { ToastContainer, toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import { useGoogleLogin } from '@react-oauth/google';
import { createUserWithEmailAndPassword } from 'firebase/auth';
import { auth } from '../firebase';


function LoginModel() {
    const [isAuthenticated, setIsAuthenticated] = useState(false);
    const [formValues, setFormValues] = useState({
        email: '',
        password: '',
    });

    const [errors, setErrors] = useState({});
    const [isSubmitting, setIsSubmitting] = useState(false);



    const validationSchemaa = Yup.object({
        email: Yup.string()
            .required('E-mail/Phone Number is required')
            .email('Please enter a valid email'),
        password: Yup.string()
            .required('Password is required')
            .min(8, 'Password should be at least 8 characters long'),
    });


    useEffect(() => {

        const IsUserExist = Cookies.get('user-token')

        if (IsUserExist) {
            setIsAuthenticated(true)
        } else {
            setIsAuthenticated(false)
        }
    }, [])

    // Handle input change
    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormValues({ ...formValues, [name]: value });
    };

    // Handle form submission




    const ShowLogin = () => {
        const myModal4 = new bootstrap.Modal(document.getElementById('myModal'));
        myModal4.show();
    }

    const validationSchema = Yup.object({
        email: Yup.string()
            .required("E-mail/Phone Number is required"),
        password: Yup.string().required("Password is required"),
    });


    const handleSubmit = async (values) => {
        try {
            console.log(values, "Submitted Values");

            // Create FormData instance
            const formData = new FormData();
            Object.keys(values).forEach((key) => {
                formData.append(key, values[key]);
            });

            // Send POST request
            const response = await axios.post(`${baseUrl}/api/login`, formData);

            if (response.data.status === "success") {
                toast.success('Login Successful');

                // Extract token and user ID from response
                const userToken = response.data.data?.access_token;
                const userId = response.data.data?.user?.id;
                const Loginuser = response?.data?.data?.user?.firstname

                console.log(userId, 'User ID');
                console.log(userToken, 'User Token');
                console.log(Loginuser, 'Login User');

                // Store user ID and token in cookies

                if (Loginuser) {
                    Cookies.set('Login-user', Loginuser);
                }


                if (userId) {
                    Cookies.set('user-id', userId);
                }

                if (userToken) {
                    Cookies.set('user-token', userToken);
                    window.location.reload(); // Reload the page after successful login
                }

            } else {
                // } else if (response.data.status === "error") {
                if (response.data.message && response.data.message.error) {
                    // Display the first error message
                    const errorMessage = response.data.message.error[0];
                    toast.error(errorMessage);
                } else {
                    // Fallback error message
                    toast.error("An error occurred. Please try again.");
                }
            }
        } catch (error) {
            // Handle network or server errors
            console.error("Login failed:", error.response?.data || error.message);

            // Provide a user-friendly error message
            const errorMessage = error.response?.data?.message || "Login failed due to a server error.";
            toast.error(errorMessage);
        }
    };



    const hendleChecklogin = (e) => {
        if (!isAuthenticated) {
            e.preventDefault();
            const myModal5 = new bootstrap.Modal(document.getElementById('myModal'));
            myModal5.show();
        }
    }


    const onsubmitgoogle = useGoogleLogin({

        onSuccess: async (tokenResponse) => {
            // SetformloadingStatus(true);
            const userInfo = await axios.get(
                'https://www.googleapis.com/oauth2/v3/userinfo',
                { headers: { Authorization: `Bearer ${tokenResponse.access_token}` } },
            );
            // console.log(tokenResponse, "tokenResponseo")
            const googletoken = tokenResponse?.access_token
            console.log(tokenResponse, "tokenResponse")
            const username = userInfo.data.given_name
            console.log(userInfo, "userInfo")

            const formDataToSend = new FormData();

            formDataToSend.append('token', tokenResponse.access_token);
            formDataToSend.append('first-name', username);


            // axios.post(`${baseUrl}/api/google-login`, formDataToSend)
            axios.post(`${baseUrl}/api/google-login`, formDataToSend, {
                headers: {
                    Authorization: `Bearer ${tokenResponse.access_token}`,
                },
            }).then((response) => {
                if (response.status === 200) {
                    toast.success('Login Successful');

                    // console.log(response?.data?.tokenResult)
                    const token = response?.data?.tokenResult
                    const userId = response?.data?.userFromDb?.id


                    if (username) {
                        Cookies.set('Login-user', username);
                    }

                    if (userId) {
                        Cookies.set('user-id', userId);
                    }


                    console.log(userId, "userId")

                    if (token) {
                        Cookies.set('user-token', token);
                        window.location.reload(); // Reload the page after successful login
                    }




                } else if (response.data.status === "error") {
                    if (response.data.message && response.data.message.error) {
                        // Extract the first error message from the array
                        const errorMessage = response.data.message.error[0];
                        toast.error(errorMessage);
                    } else {
                        // Fallback message if no specific error is provided
                        toast.error("An error occurred. Please try again.");
                    }
                }
            })
                .catch((error) => {
                    console.error('error', error);
                    if (error.response && error.response.data && error.response.data.notification) {
                    } else {
                        toast.error("An unexpected error occurred");
                    }
                });
        },
        onError: errorResponse => {
            console.error(errorResponse);
            toast.error("An unexpected error occurred");
        },
    });






    // const onsubmitgoogle = async (e) => {

    //     let email = 'gaurav@gmail.com'
    //     let password = 'gaurav123'


    //     // e.preventDefault()
    //     console.log('vvvvv')
    //     await createUserWithEmailAndPassword(auth, email, password)
    //         .then((userCredential) => {
    //             // Signed in
    //             const user = userCredential.user;
    //             console.log(user);
    //             navigate("/login")
    //             // ...
    //         })
    //         .catch((error) => {
    //             const errorCode = error.code;
    //             const errorMessage = error.message;
    //             console.log(errorCode, errorMessage);
    //             // ..
    //         });
    // }


    return (


        <>



            <div className="modal-dialog">
                <div className="modal-content">
                    <button type="button" className="btn-close" data-bs-dismiss="modal">
                        <i className="fa-solid fa-xmark" />
                    </button>
                    {/* Modal Header */}
                    <div className="modal-header">
                        <img
                            src="/images/signup-logo.svg"
                            className="img-fluid"
                            alt="Signup Logo"
                        />
                        <h2 className="modal-title">SIGN IN</h2>
                    </div>
                    {/* Modal body */}
                    <div className="modal-body">


                        <Formik
                            initialValues={{
                                email: "",
                                password: "",
                                remember: false,
                            }}
                            validationSchema={validationSchema}
                            onSubmit={handleSubmit}
                        >
                            {({ isSubmitting }) => (
                                <Form>
                                    <div className="mb-2 mt-3">
                                        <Field
                                            type="email"
                                            className="form-control"
                                            id="login-email"
                                            name="email"
                                            placeholder="E-mail/Phone Number"
                                        />
                                        <ErrorMessage
                                            name="email"
                                            component="div"
                                            className="text-danger"
                                        />
                                    </div>

                                    <div className="mb-2">
                                        <Field
                                            type="password"
                                            className="form-control"
                                            id="login-password"
                                            name="password"
                                            placeholder="Password"
                                        />
                                        <ErrorMessage
                                            name="password"
                                            component="div"
                                            className="text-danger"
                                        />
                                    </div>

                                    <div className="form-check mb-2">
                                        <Field
                                            className="form-check-input"
                                            type="checkbox"
                                            name="remember"
                                            id="login-remember"
                                        />
                                        <label className="form-check-label" htmlFor="remember">
                                            Remember me
                                        </label>
                                        <a href="#"
                                            data-bs-toggle="modal"
                                            data-bs-target="#myModal3"
                                            className="float-end">
                                            Forgot password?
                                        </a>
                                    </div>

                                    <button
                                        type="submit"
                                        className="btn btn-primary"
                                        disabled={isSubmitting}
                                    >
                                        {isSubmitting ? "Signing In..." : "Sign In"}
                                    </button>

                                    <div className="bottom mt-3">
                                        <span>
                                            New to Dr Outlier Radiology?
                                            <a href="#"
                                                className="bottom btn btn-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#myModal1"
                                            >
                                                Sign Up</a>
                                        </span>
                                    </div>

                                    <div className="mt-3 text-center">
                                        <span>or</span>
                                    </div>

                                    <button type="button" className="btn btn-link googleBtn mt-3" onClick={() => onsubmitgoogle()}>
                                        <img
                                            src="/images/logos_google-icon.svg"
                                            className="img-fluid"
                                            alt="Google"

                                        />{" "}
                                        Continue with Google
                                    </button>
                                </Form>
                            )}
                        </Formik>
                    </div>
                </div>
            </div>
        </>
    )
}

export default LoginModel