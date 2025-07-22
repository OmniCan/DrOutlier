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
import OtpInput from 'react-otp-input';




function Forget() {


    const [isAuthenticated, setIsAuthenticated] = useState(false);
    const [ActiveOtpmodel, setActiveOtpmodel] = useState(false)
    const [otp, setOtp] = useState(null);
    const [isotpverified, setisotpverified] = useState(false)

    const [forgetemail, Setforgetemail] = useState('')

    const [formValues, setFormValues] = useState({
        firstname: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    const [errors, setErrors] = useState({});
    const [isSubmitting, setIsSubmitting] = useState(false);


    const changepasword = (e) => {
        e.preventDefault()



        const formData = new FormData();
        formData.append('email', formValues.email); // Assuming `formValues` contains the email
        formData.append('password', formValues.password);
        formData.append('password_confirmation', formValues.password_confirmation);
        formData.append('token', otp);

        axios.post(`${baseUrl}/api/password/reset`, formData).then((response) => {
            if (response.data.status == 'success') {
                console.log('passwordrested ')
                toast.success('Password Changed Successfully');
                setisotpverified(false)
                setActiveOtpmodel(false)

                setOtp(null)
                document.querySelector('.closeforget').click();

                const myModal4 = new bootstrap.Modal(document.getElementById('myModal'));
                myModal4.show();
                setFormValues({ ...formValues, email: '' });


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

    }



    const validationSchemaa = Yup.object({
        // firstname: Yup.string()
        //     .required('Full Name is required')
        //     .min(2, 'Full Name should be at least 2 characters long'),
        email: Yup.string()
            .required('E-mail/Phone Number is required')
            .email('Please enter a valid email'),

    });


    useEffect(() => {

        const IsUserExist = Cookies.get('user-token')

        if (IsUserExist) {
            setIsAuthenticated(true)
        } else {
            setIsAuthenticated(false)
        }
    }, [])



    const OtpSubmit = (e) => {
        // e.preventDefault(); // Uncomment if needed to prevent default form submission behavior


        // Create a FormData object
        const formData = new FormData();
        formData.append('email', formValues.email); // Assuming `formValues` contains the email
        formData.append('code', otp); // Assuming `otp` is the variable holding the OTP

        axios.post(`${baseUrl}/api/password/verify-code`, formData)
            .then(response => {
                if (response.data.status === "success") {
                    toast.success('OTP Verified');
                    console.log('success')
                    setisotpverified(true)
                    setActiveOtpmodel(false)

                }

                else if (response.data.status === "error") {
                    if (response.data.message && response.data.message.error) {
                        // Extract the first error message from the array
                        const errorMessage = response.data.message.error[0];
                        toast.error(errorMessage);
                    } else {
                        // Fallback message if no specific error is provided
                        toast.error("An error occurred. Please try again.");
                    }
                }


                console.log('Response:', response.data);
                // Handle success
            })
            .catch(error => {
                console.error('Error:', error);
                // Handle error
            });
    };


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
                // Form is valid
                setErrors({});



                // Create a new FormData object
                const formData = new FormData();

                formData.append('value', formValues.email);
                // formData.append('password', formValues.password);
                // formData.append('password_confirmation', formValues.password_confirmation);

                // Send POST request with FormData
                axios.post(`${baseUrl}/api/password/email`, formData)
                    .then((response) => {
                        console.log('User created successfully', response);
                        Setforgetemail(formValues?.email)
                        if (response.data.status === "success") {
                            toast.success('OTP Sent');
                            setActiveOtpmodel(true)

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

    const validationSchema = Yup.object({

        //   .required("E-mail/Phone Number is required"),
        password: Yup.string().required("Password is required"),
    });


    const hendleChecklogin = (e) => {
        if (!isAuthenticated) {
            e.preventDefault();
            const myModal5 = new bootstrap.Modal(document.getElementById('myModal'));
            myModal5.show();
        }
    }






    return (


        <>





            <div className="modal-dialog">
                <div className="modal-content">
                    <button type="button" className="btn-close closeforget " data-bs-dismiss="modal">
                        <i className="fa-solid fa-xmark" />
                    </button>
                    <div className="modal-header">
                        <img
                            src="/images/signup-logo.svg"
                            className="img-fluid"
                            alt="Signup Logo"
                        />
                        <h2 className="modal-title">Forgot Password</h2>
                    </div>



                    {!isotpverified ? (
                        <>

                            <div className="modal-body mt-4 ">

                                <form onSubmit={handleSignUp}>


                                    <div className="mb-3">
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


                                    {!ActiveOtpmodel ? (
                                        <>
                                            <button
                                                type="submit"
                                                className="btn btn-primary mt-5"
                                            // disabled={isSubmitting}
                                            >
                                                Continue
                                            </button>
                                        </>
                                    ) : (
                                        <>

                                        </>
                                    )}

                                </form>


                                {ActiveOtpmodel ? (

                                    <>
                                        <form action={OtpSubmit}>
                                            <div className="mb-3">
                                                <OtpInput
                                                    value={otp}
                                                    onChange={setOtp}
                                                    inputStyle={{ width: '100%', height: '3rem', padding: '.5rem', borderRadius: '9px' }}
                                                    skipDefaultStyles
                                                    numInputs={6}
                                                    renderSeparator={<span>-</span>}
                                                    renderInput={(props) => <input {...props} />}
                                                />
                                            </div>

                                            <button
                                                type="submit"
                                                className="btn btn-primary mt-5"
                                            // disabled={isSubmitting}
                                            >
                                                Verify
                                            </button>
                                        </form>



                                    </>

                                ) : (

                                    null
                                )}
                            </div>
                        </>


                    ) : (

                        <>

                            <div className="modal-body">

                                {/* <form onSubmit={changepasword} > */}
                                <form onSubmit={changepasword}>



                                    <div className="mb-3">
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

                                    <div className="mb-3">
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
                                        className="btn btn-primary mt-5"
                                    // disabled={isSubmitting}
                                    >
                                        Continue
                                    </button>
                                </form>

                            </div>



                        </>

                    )}





                </div>
            </div>

        </>

    )
}

export default Forget