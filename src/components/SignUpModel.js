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




function SignUpModel() {


    const [isAuthenticated, setIsAuthenticated] = useState(false);


    const [formValues, setFormValues] = useState({
        firstname: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    const [errors, setErrors] = useState({});
    const [isSubmitting, setIsSubmitting] = useState(false);



    const validationSchemaa = Yup.object({
        firstname: Yup.string()
            .required('Full Name is required')
            .min(2, 'Full Name should be at least 2 characters long'),
        email: Yup.string()
            .required('E-mail/Phone Number is required')
            .email('Please enter a valid email'),
        password: Yup.string()
            .required('Password is required')
            .min(8, 'Password should be at least 8 characters long'),
        password_confirmation: Yup.string()
            .required('Confirm Password is required')
            .oneOf([Yup.ref('password'), null], 'Passwords must match'),
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

    const handleSignUp = (e) => {
        e.preventDefault();
        setIsSubmitting(true);

        // Validate form
        validationSchemaa
            .validate(formValues, { abortEarly: false })
            .then(() => {
                setErrors({});
                const formData = new FormData();

                formData.append('firstname', formValues.firstname);
                formData.append('email', formValues.email);
                formData.append('password', formValues.password);
                formData.append('password_confirmation', formValues.password_confirmation);

                axios.post(`${baseUrl}/api/register`, formData)
                    .then((response) => {
                        console.log('Your account has been created successfully', response);

                        if (response.data.status === "success") {
                            toast.success('Your account has been Created Successfully');
                            document.querySelector('.signup-modal').click();
                            const myModal4 = new bootstrap.Modal(document.getElementById('myModal'));
                            myModal4.show();
                            // } else if (response.data.status === "error") {
                        } else {
                            if (response.data.message && response.data.message.error) {
                                const errorMessage = response.data.message.error[0];
                                toast.error(errorMessage);
                            } else {
                                toast.error("An error occurred. Please try again.");
                            }
                        }

                    })
                    .catch((error) => {
                        console.error('Error creating user', error);
                    });
            })
            .catch((err) => {
                // Capture validation errors
                const newErrors = {};
                err.inner.forEach((error) => {
                    newErrors[error.path] = error.message;
                });
                setErrors(newErrors);
                setIsSubmitting(false);
            });
    };


    const ShowLogin = () => {
        const myModal4 = new bootstrap.Modal(document.getElementById('myModal'));
        myModal4.show();
    }




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
            const formDataToSend = new FormData();

            formDataToSend.append('token', tokenResponse.access_token);

            axios.post(`${baseUrl}/api/google-login`, formDataToSend)
                .then((response) => {
                    if (response.status === 200) {
                        toast.success("Login Successfully")
                    } else if (response.status === 201) {

                        toast.error("Invalid")
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



    return (


        <>


            <div className="modal-dialog">
                <div className="modal-content">
                    <button type="button" className="btn-close signup-modal" data-bs-dismiss="modal">
                        <i className="fa-solid fa-xmark" />
                    </button>
                    <div className="modal-header">
                        <img
                            src="/images/signup-logo.svg"
                            className="img-fluid"
                            alt="Signup Logo"
                        />
                        <h2 className="modal-title">SIGN UP</h2>
                    </div>
                    <div className="modal-body">

                        <form onSubmit={handleSignUp}>
                            <div className="mb-2 mt-3">
                                <input
                                    type="text"
                                    className="form-control"
                                    id="firstname"
                                    placeholder="Full Name"
                                    name="firstname"
                                    value={formValues.firstname}
                                    onChange={handleChange}
                                />
                                {errors.firstname && <div className="text-danger">{errors.firstname}</div>}
                            </div>

                            <div className="mb-2">
                                <input
                                    type="text"
                                    className="form-control"
                                    id="email"
                                    placeholder="E-mail/Phone Number"
                                    name="email"
                                    value={formValues.email}
                                    onChange={handleChange}
                                />
                                {errors.email && <div className="text-danger">{errors.email}</div>}
                            </div>

                            <div className="mb-2">
                                <input
                                    type="password"
                                    className="form-control"
                                    id="password"
                                    placeholder="Password"
                                    name="password"
                                    value={formValues.password}
                                    onChange={handleChange}
                                />
                                {errors.password && <div className="text-danger">{errors.password}</div>}
                            </div>

                            <div className="mb-2">
                                <input
                                    type="password"
                                    className="form-control"
                                    id="password_confirmation"
                                    placeholder="Confirm Password"
                                    name="password_confirmation"
                                    value={formValues.password_confirmation}
                                    onChange={handleChange}
                                />
                                {errors.confirmPassword && (
                                    <div className="text-danger">{errors.password_confirmation}</div>
                                )}
                            </div>

                            <button
                                type="submit"
                                className="btn btn-primary mt-2"
                            // disabled={isSubmitting}
                            >
                                Sign Up
                            </button>

                            <div className='bottom'>
                                <span>
                                    Already a member?{' '}
                                    <a
                                        href="#"
                                        data-bs-toggle="modal"
                                        data-bs-target="#myModal"
                                    >
                                        Sign In
                                    </a>
                                </span>
                            </div>
                            <span>or</span>
                            <button className="btn btn-link googleBtn mt-2" onClick={() => onsubmitgoogle()}>
                                <span>
                                    <img
                                        src="/images/logos_google-icon.svg"
                                        className="img-fluid"
                                        alt="Google"
                                    />
                                    Continue with Google
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </>

    )
}

export default SignUpModel