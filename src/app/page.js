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
import LoginModel from '@/components/LoginModel';
import SignUpModel from '@/components/SignUpModel';
import { DotLottieReact } from '@lottiefiles/dotlottie-react';
// import icon 



function page() {


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
        // Form is valid
        setErrors({});



        // Create a new FormData object
        const formData = new FormData();

        // Append form values to FormData object
        formData.append('firstname', formValues.firstname);
        formData.append('email', formValues.email);
        formData.append('password', formValues.password);
        formData.append('password_confirmation', formValues.password_confirmation);

        // Send POST request with FormData
        axios.post(`${baseUrl}/api/register`, formData)
          .then((response) => {
            console.log('User created successfully', response);
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
    // username: Yup.string()
    //   .username("Invalid username format")
    //   .required("E-mail/Phone Number is required"),
    password: Yup.string().required("Password is required"),
  });


  const handleSubmit = async (values) => {
    try {
      // Create FormData instance
      const formData = new FormData();
      Object.keys(values).forEach((key) => {
        formData.append(key, values[key]);
      });

      const response = await axios.post(`${baseUrl}/api/login`, formData, {
        // headers: {
        //   "Content-Type": "multipart/form-data", // Ensure correct content type
        // },
      });

      const usertoken = response.data.data?.access_token
      const user = response?.data?.data?.user?.id

      if (user) {
        Cookies.set('user-id', user);
      }

      if (usertoken) {
        Cookies.set('user-token', usertoken);
        window.location.reload()
      }

      console.log("Token:", usertoken);
      // Redirect or show success message
    } catch (error) {
      console.error("Login failed:", error.response?.data || error.message);
      // Handle error (e.g., show error message)
    }
  };


  const hendleChecklogin = (e) => {
    if (!isAuthenticated) {
      e.preventDefault();
      const myModal5 = new bootstrap.Modal(document.getElementById('myModal'));
      myModal5.show();
    }
  }



  return (
    <>
      <Navbar />
      <div className="main-wrapper">
        <section className='' >
          <div className="container">
            <div className="row">
              <div className="col-lg-8">
                <div className="content">
                  <h1 className="text-white">Welcome to Dr. Outlier Radiology</h1>
                  {/* <p>
                    Dr. Outlier Radiology platform is dedicated to providing
                    comprehensive, accurate, and up-to-date information about
                    radiology, including diagnostic imaging, medical technologies,
                    and expert advice. Whether you're a medical professional,
                    student, or simply interested in understanding how medical
                    imaging works, we are here to guide you through the world of
                    radiology.
                  </p> */}
                  <p>
                    Struggling to pass your MD, DNB, or DMRD exams?
                    Welcome to Dr. Outlier—where we turn your exam panic into progress! From theory that reads like ancient
                    Sanskrit to practicals that feel like a WWE showdown with your examiner, we’ve got you covered.
                    This app is like your nerdy best friend who knows all the answers but doesn’t rub it in your face.
                    Need to memorize? Check. Need to impress in practicals? Double-check.
                    Need to fake confidence? Oh, we’ve mastered that too. So buckle up, future radiology rockstars—because with
                    Dr. Outlier, failing is harder than passing!"
                  </p>
                  {/* <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus
                    ultricies massa eu est fermentum, ac auctor nulla varius.
                  </p> */}
                </div>
                <div className="row">
                  <div className="col-lg-10 m-auto">
                    <div className="row">
                      <div className="col-lg-4 col-6">
                        <Link onClick={hendleChecklogin} href='/spotters'>

                          <div className="box" style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', textAlign: 'center', height: '100%' }}>
                            <DotLottieReact
                              src="/animantion/Blue circle 2.json"
                              loop
                              autoplay
                              style={{ width: '174px', height: '182px' }}
                            />
                            <h6 style={{ marginTop: '10px', }}>SPOTTERS</h6>
                          </div>

                        </Link>

                      </div>
                      <div className="col-lg-4 col-6">
                        <Link onClick={hendleChecklogin} href='/notes'>

                          <div className="box" style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', textAlign: 'center', height: '100%' }}>
                            {/* <img
                              src="/images/Notes.webp"
                              className="img-fluid"
                              alt="Notes"
                            /> */}
                            <DotLottieReact
                              src="/animantion/Green circle.json"
                              loop
                              autoplay
                              style={{ width: '174px', height: '182px' }}
                            />
                            <h6>NOTES</h6>
                          </div>

                        </Link>

                      </div>
                      <div className="col-lg-4 col-6">
                        <Link onClick={hendleChecklogin} href='/osce'>

                          <div className="box" style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', textAlign: 'center', height: '100%' }}>
                            {/* <img
                              src="/images/OSCE.webp"
                              className="img-fluid"
                              alt="OSCE"
                            /> */}

                            <DotLottieReact
                              src="/animantion/Blue circle 2.json"
                              loop
                              autoplay
                              style={{
                                width: '174px',
                                height: '182px',
                                filter: 'hue-rotate(180deg)', // Adjust the degree for the desired color
                              }}
                            />
                            <h6>OSCE</h6>
                          </div>
                        </Link>

                      </div>
                      <div className="col-lg-4 col-6">
                        <Link onClick={hendleChecklogin} href='/ai-rad'>

                          <div className="box" style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', textAlign: 'center', height: '100%' }}>
                            {/* <img
                              src="/images/Munchies-and-Nuggets.webp"
                              className="img-fluid"
                              alt="Munchies & Nuggets"
                            /> */}

                            <DotLottieReact
                              src="/animantion/green.json"
                              loop
                              autoplay
                              style={{
                                width: '174px',
                                height: '182px',
                                filter: 'hue-rotate(180deg)', // Adjust the degree for the desired color


                              }}

                            />
                            <h6>
                              AI-Rad
                            </h6>
                          </div>
                        </Link>
                      </div>
                      <div className="col-lg-4 col-6">
                        <Link onClick={hendleChecklogin} href='/practical-essentials'>

                          <div className="box" style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', textAlign: 'center', height: '100%' }}>

                            {/* <img
                              src="/images/back-to-basics.webp"
                              className="img-fluid"
                              alt="Back To Basics"
                            /> */}

                            <DotLottieReact
                              src="/animantion/green.json"
                              loop
                              autoplay
                              style={{
                                width: '174px',
                                height: '182px',
                                filter: 'green(180deg)', // Adjust the degree for the desired color


                              }}

                            />
                            <h6>
                              Practical <br /> Essentials
                            </h6>
                          </div>
                        </Link>

                      </div>
                      <div className="col-lg-4 col-6">
                        <Link onClick={hendleChecklogin} href='/watch-and-learn'>

                          <div className="box" style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', textAlign: 'center', height: '100%' }}>
                            {/* <img
                              src="/images/watch-and-learn.webp"
                              className="img-fluid"
                              alt="Watch & Learn"
                            /> */}

                            <DotLottieReact
                              src="/animantion/Grey circle.json"
                              loop
                              autoplay
                              style={{ width: '174px', height: '182px' }}
                            />
                            <h6>
                              WATCH &amp; <br /> LEARN
                            </h6>
                          </div>
                        </Link>

                      </div>

                      <div className="col-lg-4 col-6">
                        <Link onClick={hendleChecklogin} href='/quizora'>

                          <div className="box" style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', textAlign: 'center', height: '100%' }}>
                            {/* <img
                              src="/images/watch-and-learn.webp"
                              className="img-fluid"
                              alt="Watch & Learn"
                            /> */}

                            <DotLottieReact
                              src="/animantion/Blue circle 2.json"
                              loop
                              autoplay
                              style={{
                                width: '174px',
                                height: '182px',
                                filter: 'hue-rotate(223deg)', // Adjust the degree for the desired color
                              }}
                            />
                            <h6>
                              QUIZORA
                            </h6>
                          </div>
                        </Link>

                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div className="col-lg-4">
                <div className="image">
                  <img
                    src="/images/Dr-Outlier-Radiology.webp"
                    className="img-fluid w-100"
                    alt="Dr Outlier Radiology"
                  />
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>


      <Footer />


      <div className="modal" id="myModal">
        <LoginModel />
      </div>


      <div className="modal" id="myModal1">
        <SignUpModel />
      </div>
    </>
  )
}

export default page