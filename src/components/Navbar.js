"use client"
import axios from 'axios'
import Cookies from 'js-cookie'
import React, { useEffect, useState } from 'react'
import Avatar from '@mui/material/Avatar';
import Link from 'next/link';
import { ToastContainer, toast } from 'react-toastify';
import TextField from '@mui/material/TextField';
import Autocomplete from '@mui/material/Autocomplete';
import { styled, lighten, darken } from '@mui/system';
import baseUrl from '@/Services/BaseUrl';
import * as Yup from "yup";
import { Formik, Form, Field, ErrorMessage } from "formik";
import { useRouter } from 'next/navigation'
import { useGoogleLogin } from '@react-oauth/google/dist';
import LoginModel from './LoginModel';
import SignUpModel from './SignUpModel';
import Forget from './ForgetPassword/Forget';



const debounce = (func, delay) => {
    let timeout;
    return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => func(...args), delay);
    };
};

function Navbar() {

    const [count, setcount] = useState(0)
    const router = useRouter()
    const [Username, SetLoginUsername] = useState('UserName')


    useEffect(() => {
        const IsUserExist = Cookies.get('user-token')
        const Username = Cookies.get('Login-user')
        if (IsUserExist) {
            setIsAuthenticated(true)
        } else {
            setIsAuthenticated(false)
        }

        if (Username) {
            SetLoginUsername(Username)
        }
    }, [])


    useEffect(() => {

        const IsUserExist = Cookies.get('user-token')
        if (IsUserExist) {
            setIsAuthenticated(true)
        } else {
            setIsAuthenticated(false)
        }
    }, [count])


    const handleLogOut = () => {
        toast.success("Logout Successfull");
        Cookies.remove('user-token')
        router.push('/')
        setcount(prv => prv + 1)
    }


    const [searchValue, setSearchValue] = useState(""); // Search input value
    const [options, setOptions] = useState([]); // Store API response options
    const [loading, setLoading] = useState(false); // Show loading spinner
    const [isAuthenticated, setIsAuthenticated] = useState(false);

    const handleSerch = debounce(async (query) => {
        try {
            const userToken = Cookies.get('user-token');
            const response = await axios.post(
                `${baseUrl}/api/search`,
                { title: query },
                {
                    headers: {
                        Authorization: `Bearer ${userToken}`,
                    },
                }
            );
            setSearchValue(response?.data?.data?.datalist)
            const serchdata = response?.data?.data?.datalist;

        } catch (error) {
            console.error("Error fetching search data:", error);
        } finally {
            setLoading(false);
        }
    }, 500); // 500ms delay for debounced API calls




    const handleSearch = (e) => {
        localStorage.setItem('serchdata', e)
    }


    const saveToLocalStorage = (data) => {
        try {
            // Clear existing data
            localStorage.removeItem("selectedData");

            // Save the new data
            const newData = [data]; // Wrap the data in an array if you want to maintain the array structure
            localStorage.setItem("selectedData", JSON.stringify(newData));
            // window.location.reload();
            console.log("Saved to localStorage:", data);
        } catch (error) {
            console.error("Error saving to localStorage:", error);
        }
    };



    const hendleChecklogin = (e) => {
        if (!isAuthenticated) {
            e.preventDefault();
            const myModal5 = new bootstrap.Modal(document.getElementById('myModal'));
            myModal5.show();
        }
        sessionStorage.setItem('is_saved', 'true')
    }
    const hendleCheckloginNav = (e) => {
        sessionStorage.removeItem('is_saved')
        if (!isAuthenticated) {
            e.preventDefault();
            const myModal5 = new bootstrap.Modal(document.getElementById('myModal'));
            myModal5.show();
        }
    }


    const ShowLogin = () => {
        const myModal4 = new bootstrap.Modal(document.getElementById('myModal'));
        myModal4.show();
    }

    return (
        <>
            <header className="header-wrapper">
                <div className="header-inner">
                    <nav className="navbar navbar-dark">
                        <div className="container">
                            <div className="col-lg-4 col-2">
                                <button
                                    className="navbar-toggler"
                                    type="button"
                                    data-bs-toggle="offcanvas"
                                    data-bs-target="#offcanvasDarkNavbar"
                                    aria-controls="offcanvasDarkNavbar"
                                    aria-label="Toggle navigation"
                                >
                                    {" "}
                                    <img src="/images/Toggle.svg" className="img-fluid" alt="Toggle" />
                                </button>
                            </div>
                            <div className="col-lg-4 col-7 text-center">
                                <Link className="navbar-brand col-md-2" href="/">
                                    <img
                                        src="/images/Header-Logo.webp"
                                        className="img-fluid"
                                        alt="Radiology"
                                    />
                                </Link>
                            </div>



                            <div className="col-lg-4 col-2 d-flex justify-content-end">
                                {isAuthenticated ? (
                                    <div className="btn-group">
                                        <div
                                            data-bs-toggle="dropdown"
                                            className="dropDown-wrap"
                                            style={{ cursor: 'pointer' }}
                                        >
                                            {/* For phone view, show Avatar instead of Username */}
                                            <div className="d-lg-none">
                                                {/* <Avatar  src="/images/avatar.png" /> */}
                                                <Avatar src="/images/avatar.png" />

                                            </div>

                                            {/* For large screens, show Username */}
                                            <div className="d-none d-lg-block">
                                                <img alt="Login Logo" src="/images/login-logo.svg" />
                                                {Username}
                                            </div>

                                            <i className="fa-solid fa-chevron-down d-none d-lg-block"></i>
                                        </div>
                                        <ul className="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <button
                                                    className="dropdown-item"
                                                    type="button"
                                                    style={{ cursor: 'pointer', zIndex: 9999 }}
                                                    onClick={() => handleLogOut()}
                                                >
                                                    Log Out
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                ) : (
                                    <button
                                        className="btn btn-link loginBtn d-lg-block d-none"
                                        data-bs-toggle="modal"
                                        data-bs-target="#myModal"
                                    >
                                        <i className="fa-solid fa-user" /> Login
                                    </button>
                                )}

                                <button className="btn btn-link searchBtn d-lg-none d-block">
                                    <i className="fa-solid fa-magnifying-glass" />
                                </button>
                            </div>



                            <div className="overlay" />
                            <div className="search-panel">
                                <div className="search-top-wrapper">
                                    <div className="row align-items-center">
                                        <div className="col-4">
                                            <i className="fa-solid fa-chevron-left closeBtn" />
                                        </div>
                                        <div className="col-4">
                                            <h6>Search</h6>
                                        </div>
                                        <div className="col-4"></div>
                                    </div>
                                </div>
                                <div className="search-bottom-wrapper">
                                    <input
                                        type="text"
                                        className="form-control mb-4"
                                        placeholder="Search here..."
                                        onChange={(e) => handleSerch(e.target.value)} // Handle search input change



                                    />

                                    <div className="taber-wrapper">
                                        <ul className="nav nav-tabs" id="myTab" role="tablist">
                                            <li className="nav-item" role="presentation">




                                                <button
                                                    className="nav-link active"
                                                    id="notes-tab"
                                                    data-bs-toggle="tab"
                                                    data-bs-target="#notes"
                                                    type="button"
                                                    role="tab"
                                                    aria-controls="notes"
                                                    aria-selected="true"
                                                >
                                                    Notes
                                                </button>



                                            </li>
                                            <li className="nav-item" role="presentation">
                                                <button
                                                    className="nav-link"
                                                    id="spotters-tab"
                                                    data-bs-toggle="tab"
                                                    data-bs-target="#spotters"
                                                    type="button"
                                                    role="tab"
                                                    aria-controls="spotters"
                                                    aria-selected="false"
                                                >
                                                    Spotters
                                                </button>
                                            </li>
                                            <li className="nav-item" role="presentation">
                                                <button
                                                    className="nav-link"
                                                    id="osce-tab"
                                                    data-bs-toggle="tab"
                                                    data-bs-target="#osce"
                                                    type="button"
                                                    role="tab"
                                                    aria-controls="osce"
                                                    aria-selected="false"
                                                >
                                                    OSCE
                                                </button>

                                            </li>
                                            <li className="nav-item" role="presentation">
                                                <button
                                                    className="nav-link"
                                                    id="ai-rad-tab"
                                                    data-bs-toggle="tab"
                                                    data-bs-target="#ai-rad"
                                                    type="button"
                                                    role="tab"
                                                    aria-controls="ai-rad"
                                                    aria-selected="false"
                                                >
                                                    AI-Rad
                                                </button>


                                            </li>
                                            <li className="nav-item" role="presentation">


                                                <button
                                                    className="nav-link"
                                                    id="watch-and-learn-tab"
                                                    data-bs-toggle="tab"
                                                    data-bs-target="#watch-and-learn"
                                                    type="button"
                                                    role="tab"
                                                    aria-controls="watch-and-learn"
                                                    aria-selected="false"
                                                >
                                                    Watch And Learn
                                                </button>

                                            </li>
                                            <li className="nav-item" role="presentation">

                                                <button
                                                    className="nav-link"
                                                    id="practical-essentials-tab"
                                                    data-bs-toggle="tab"
                                                    data-bs-target="#practical-essentials"
                                                    type="button"
                                                    role="tab"
                                                    aria-controls="practical-essentials"
                                                    aria-selected="false"
                                                >
                                                    Practical Essentials
                                                </button>

                                            </li>
                                        </ul>
                                        {/* Tab Content */}
                                        <div className="tab-content" id="myTabContent">




                                            <div
                                                className="tab-pane fade show active"
                                                id="notes"
                                                role="tabpanel"
                                                aria-labelledby="notes-tab"
                                            >




                                                <ul className="" style={{ overflow: 'scroll' }} >
                                                    {/* Notes Results */}
                                                    {searchValue?.notes?.length > 0 && (
                                                        <>

                                                            {searchValue.notes.map((elem) => (
                                                                <li key={elem.id} style={{ color: 'black', listStyle: 'none' }}>
                                                                    <Link
                                                                        href={`/search-result`}
                                                                        className="dropdown-item"
                                                                        onClick={() => saveToLocalStorage(elem)} // Save to localStorage on click
                                                                    >
                                                                        <h6 className="title" style={{ color: 'black' }}>{elem.title}</h6>
                                                                        <p className="module text-muted">Notes</p>                                                                </Link>
                                                                </li>
                                                            ))}
                                                        </>
                                                    )}

                                                </ul>


                                                <p>No results found</p>
                                            </div>



                                            <div
                                                className="tab-pane fade"
                                                id="spotters"
                                                role="tabpanel"
                                                aria-labelledby="spotters-tab"
                                            >


                                                <ul className="" style={{ overflow: 'scroll' }} >
                                                    {/* Notes Results */}
                                                    {searchValue?.spotters?.length > 0 && (
                                                        <>
                                                            {/* <h5 className="dropdown-item">Spotters</h5> */}
                                                            {searchValue.spotters.map((elem) => (
                                                                <li key={elem.id}>
                                                                    <Link
                                                                        href={`/search-result`}
                                                                        className="dropdown-item"
                                                                        onClick={() => saveToLocalStorage(elem)} // Save to localStorage on click
                                                                    >
                                                                        <h6 className="title" style={{ color: 'black' }} >{elem.title}</h6>
                                                                        <p className="module text-muted">Spotters</p>

                                                                    </Link>
                                                                </li>
                                                            ))}
                                                        </>
                                                    )}
                                                </ul>




                                            </div>
                                            <div
                                                className="tab-pane fade"
                                                id="osce"
                                                role="tabpanel"
                                                aria-labelledby="osce-tab"
                                            >


                                                {searchValue?.osce?.length > 0 && (
                                                    <>
                                                        {/* <h5 className="dropdown-item">Osce</h5> */}
                                                        {searchValue.osce.map((elem) => (
                                                            <li key={elem.id}>
                                                                <Link
                                                                    href={`/search-result`}
                                                                    className="dropdown-item"
                                                                    onClick={() => saveToLocalStorage(elem)} // Save to localStorage on click
                                                                >
                                                                    <h6 className="title" style={{ color: 'black' }} >{elem.title}</h6>
                                                                    <p className="module text-muted">Osce</p>

                                                                </Link>
                                                            </li>
                                                        ))}
                                                    </>
                                                )}




                                                {/* <p>No results found</p> */}
                                            </div>
                                            <div
                                                className="tab-pane fade"
                                                id="ai-rad"
                                                role="tabpanel"
                                                aria-labelledby="ai-rad-tab"
                                            >


                                                {searchValue?.munchies?.length > 0 && (
                                                    <>
                                                        {searchValue.munchies.map((elem) => (
                                                            <li key={elem.id}>
                                                                <Link
                                                                    href={`/search-result`}
                                                                    className="dropdown-item"
                                                                    onClick={() => saveToLocalStorage(elem)} // Save to localStorage on click
                                                                >
                                                                    <h6 className="title" style={{ color: 'black' }} >{elem.title}</h6>
                                                                    <p className="module text-muted">AI-Rad</p>

                                                                </Link>
                                                            </li>
                                                        ))}
                                                    </>
                                                )}

                                                <p>No results found</p>
                                            </div>
                                            <div
                                                className="tab-pane fade"
                                                id="watch-and-learn"
                                                role="tabpanel"
                                                aria-labelledby="watch-and-learn-tab"
                                            >

                                                {searchValue?.whatchandlearn?.length > 0 && (
                                                    <>
                                                        {/* <h5 className="dropdown-item">Watch And Learn  3</h5> */}
                                                        {searchValue.whatchandlearn.map((elem) => (
                                                            <li key={elem.id}>
                                                                <Link
                                                                    href={`/search-result`}
                                                                    className="dropdown-item"
                                                                    onClick={() => saveToLocalStorage(elem)} // Save to localStorage on click
                                                                >
                                                                    <h6 className="title" style={{ color: 'black' }} >{elem.title}</h6>
                                                                    <p className="module text-muted">Watch And Learn</p>

                                                                </Link>
                                                            </li>
                                                        ))}
                                                    </>
                                                )}


                                                <p>No results found</p>
                                            </div>
                                            <div
                                                className="tab-pane fade"
                                                id="practical-essentials"
                                                role="tabpanel"
                                                aria-labelledby="practical-essentials-tab"
                                            >



                                                {searchValue?.basics?.length > 0 && (
                                                    <>
                                                        {searchValue.basics.map((elem) => (
                                                            <li key={elem.id}>
                                                                <Link
                                                                    href={`/search-result`}
                                                                    className="dropdown-item"
                                                                    onClick={() => saveToLocalStorage(elem)} // Save to localStorage on click
                                                                >
                                                                    <h6 className="title" style={{ color: 'black' }} >{elem.title}</h6>
                                                                    <p className="module text-muted">Practical Essentials</p>

                                                                </Link>
                                                            </li>
                                                        ))}
                                                    </>
                                                )}



                                                <p>No results found</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            className="offcanvas offcanvas-end text-bg-dark"
                            tabIndex={-1}
                            id="offcanvasDarkNavbar"
                            aria-labelledby="offcanvasDarkNavbarLabel"
                        >
                            <div className="offcanvas-body">
                                <button
                                    type="button"
                                    className="btn-close btn-close-white"
                                    data-bs-dismiss="offcanvas"
                                    aria-label="Close"
                                >
                                    <i className="fa-solid fa-chevron-left" />
                                </button>
                                <div className="heading-wrap">
                                    <h5 className="mt-4">
                                        Welcome to
                                        <span>
                                            Dr Outlier <strong>Radiology</strong>
                                        </span>
                                    </h5>
                                </div>
                                <ul className="navbar-nav justify-content-end flex-grow-1 d-block d-md-none">

                                    {!isAuthenticated ? (
                                        <>
                                            <button
                                                className="btn btn-link LogoutBtn"
                                                onClick={() => ShowLogin()}
                                                style={{ backgroundColor: 'green' }}
                                            >
                                                Login
                                            </button>

                                        </>

                                    ) : (
                                        <>
                                            <button
                                                className="btn btn-link LogoutBtn"
                                                onClick={() => handleLogOut()}>

                                                LogOut
                                            </button>

                                        </>


                                    )}


                                    <li className="nav-item active">
                                        <Link className="nav-link" onClick={hendleChecklogin} href="/">
                                            Home{" "}
                                        </Link>
                                    </li>
                                    <li className="nav-item active">
                                        <Link className="nav-link" onClick={hendleChecklogin} href="/spotters">
                                            Spotters{" "}
                                        </Link>
                                    </li>
                                    <li className="nav-item">
                                        <Link className="nav-link" onClick={hendleChecklogin} href="/notes">
                                            Notes{" "}
                                        </Link>
                                    </li>
                                    <li className="nav-item">
                                        <Link className="nav-link" onClick={hendleChecklogin} href="/osce">
                                            OSCE{" "}
                                        </Link>
                                    </li>
                                    <li className="nav-item">
                                        <Link className="nav-link" onClick={hendleChecklogin} href="/ai-rad">
                                            AI-Rad
                                        </Link>
                                    </li>
                                    <li className="nav-item">
                                        <Link className="nav-link" onClick={hendleChecklogin} href="/practical-essentials">
                                            Practical Essentials
                                        </Link>
                                    </li>
                                    <li className="nav-item">
                                        <Link className="nav-link" onClick={hendleChecklogin} href="/watch-and-learn">
                                            Watch &amp; Learn{" "}
                                        </Link>
                                    </li>

                                    <li className="nav-item dropdown">
                                        <Link
                                            className="nav-link dropdown-toggle"
                                            href="#"
                                            role="button"
                                            data-bs-toggle="dropdown"
                                        >
                                            {" "}
                                            Saved{" "}
                                        </Link>
                                        <ul className="dropdown-menu">
                                            <li>
                                                <Link className="dropdown-item" onClick={hendleChecklogin} href="/spotters/save-spootters">
                                                    Saved Spotters
                                                </Link>
                                            </li>
                                            <li>
                                                <Link className="dropdown-item" onClick={hendleChecklogin} href="/notes/save-notes">
                                                    Saved Notes{" "}
                                                </Link>
                                            </li>
                                            <li>
                                                <Link className="dropdown-item" onClick={hendleChecklogin} href="/osce/save-osce">
                                                    Saved OSCE{" "}
                                                </Link>
                                            </li>
                                            <li>
                                                <Link className="dropdown-item" onClick={hendleChecklogin} href="/ai-rad/save-ai-rad">
                                                    Saved AI-Rad{" "}
                                                </Link>
                                            </li>
                                            <li>
                                                <Link className="dropdown-item" onClick={hendleChecklogin} href="/practical-essentials/save-practical-essentials">
                                                    Saved Practical Essentials{" "}
                                                </Link>
                                            </li>
                                            <li>
                                                <Link className="dropdown-item" onClick={hendleChecklogin} href="/watch-and-learn/save-watch-and-lern">
                                                    Saved Watch &amp; Learn{" "}
                                                </Link>
                                            </li>
                                            <li>
                                                <Link className="dropdown-item" onClick={hendleChecklogin} href="/quizora">
                                                    Saved Quizzes{' '}
                                                </Link>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                                <ul className="navbar-nav justify-content-end flex-grow-1 d-none d-md-block">
                                    <li className="nav-item active">
                                        <Link className="nav-link" onClick={hendleChecklogin} href="/spotters/save-spootters">
                                            Saved Spotters <i className="fa-solid fa-chevron-right" />
                                        </Link>
                                    </li>
                                    <li className="nav-item">
                                        <Link className="nav-link" onClick={hendleChecklogin} href="/notes/save-notes">
                                            Saved Notes <i className="fa-solid fa-chevron-right" />
                                        </Link>
                                    </li>
                                    <li className="nav-item">
                                        <Link className="nav-link" onClick={hendleChecklogin} href="/osce/save-osce">
                                            Saved OSCE <i className="fa-solid fa-chevron-right" />
                                        </Link>
                                    </li>
                                    <li className="nav-item">
                                        <Link className="nav-link" onClick={hendleChecklogin} href="/ai-rad/save-ai-rad">
                                            Saved AI-Rad <i className="fa-solid fa-chevron-right" />
                                        </Link>
                                    </li>
                                    <li className="nav-item">
                                        <Link className="nav-link" onClick={hendleChecklogin} href="/practical-essentials/save-practical-essentials">
                                            Saved Practical Essentials{" "}
                                            <i className="fa-solid fa-chevron-right" />
                                        </Link>
                                    </li>
                                    <li className="nav-item">
                                        <Link className="nav-link" onClick={hendleChecklogin} href="/watch-and-learn/save-watch-and-lern">
                                            Saved Watch &amp; Learn{" "}
                                            <i className="fa-solid fa-chevron-right" />
                                        </Link>
                                    </li>
                                    <li className="nav-item">
                                        <Link className="nav-link" onClick={hendleChecklogin} href="/quizora">
                                            Saved Quizzes{' '}
                                            <i className="fa-solid fa-chevron-right" />
                                        </Link>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </nav >
                    <div className="header-menu">
                        <nav className="navbar navbar-expand-md d-lg-flex d-none">
                            <div className="container">
                                <ul className="navbar-nav justify-content-between w-100 align-items-center">
                                    <li className="nav-item">
                                        <Link className="nav-link" onClick={hendleChecklogin} href="/">
                                            Home
                                        </Link>
                                    </li>
                                    <li className="nav-item">
                                        <Link className="nav-link" onClick={hendleChecklogin} href="/spotters">
                                            Spotters
                                        </Link>
                                    </li>
                                    <li className="nav-item">
                                        <Link className="nav-link" onClick={hendleChecklogin} href="/notes">
                                            NOtes
                                        </Link>
                                    </li>
                                    <li className="nav-item">
                                        <Link className="nav-link" onClick={hendleChecklogin} href="/osce">
                                            OSCE
                                        </Link>
                                    </li>
                                    <li className="nav-item">
                                        <Link className="nav-link" onClick={hendleChecklogin} href="/ai-rad">
                                            AI-Rad
                                        </Link>
                                    </li>
                                    <li className="nav-item">
                                        <Link className="nav-link" onClick={hendleChecklogin} href="/practical-essentials">
                                            Practical Essentials
                                        </Link>
                                    </li>
                                    <li className="nav-item">
                                        <Link className="nav-link" onClick={hendleChecklogin} href="/watch-and-learn">
                                            Watch &amp; Learn
                                        </Link>
                                    </li>
                                    <li className="nav-item">
                                        <Link className="nav-link" onClick={hendleCheckloginNav} href="/quizora">
                                            Quizora
                                        </Link>
                                    </li>



                                    <li className="nav-item">
                                        <div className="search dropdown">
                                            {/* Search Icon */}
                                            <i className="fa-solid fa-magnifying-glass" />

                                            {/* Search Input */}
                                            <input
                                                onClick={hendleChecklogin}
                                                type="search"
                                                placeholder="Search"
                                                className="form-control"
                                                autoComplete="off"
                                                data-bs-toggle="dropdown" // Enables Bootstrap dropdown functionality
                                                aria-expanded="false"
                                                onChange={(e) => handleSerch(e.target.value)} // Handle search input change
                                            />

                                            {/* Dropdown Menu */}
                                            <ul className="dropdown-menu w-100 custom-dropdown searchhhh">
                                                {/* Notes Results */}
                                                {searchValue?.notes?.length > 0 && (
                                                    <>
                                                        {/* <h5 className="dropdown-item" aria-disabled >Notes</h5> */}
                                                        {searchValue.notes.map((elem) => (
                                                            <li key={elem.id}>
                                                                <Link
                                                                    href={`/search-result`}
                                                                    className="dropdown-item"
                                                                    onClick={() => saveToLocalStorage(elem)} // Save to localStorage on click
                                                                >
                                                                    <h6 className="title">{elem.title}</h6>
                                                                    <p className="module text-muted">Notes</p>                                                                </Link>
                                                            </li>
                                                        ))}
                                                    </>
                                                )}

                                                {/* Spotters Results */}
                                                {searchValue?.spotters?.length > 0 && (
                                                    <>
                                                        {/* <h5 className="dropdown-item">Spotters</h5> */}
                                                        {searchValue.spotters.map((elem) => (
                                                            <li key={elem.id}>
                                                                <Link
                                                                    href={`/search-result`}
                                                                    className="dropdown-item"
                                                                    onClick={() => saveToLocalStorage(elem)} // Save to localStorage on click
                                                                >
                                                                    <h6 className="title">{elem.title}</h6>
                                                                    <p className="module text-muted">Spotters</p>

                                                                </Link>
                                                            </li>
                                                        ))}
                                                    </>
                                                )}
                                                {searchValue?.osce?.length > 0 && (
                                                    <>
                                                        {/* <h5 className="dropdown-item">Osce</h5> */}
                                                        {searchValue.osce.map((elem) => (
                                                            <li key={elem.id}>
                                                                <Link
                                                                    href={`/search-result`}
                                                                    className="dropdown-item"
                                                                    onClick={() => saveToLocalStorage(elem)} // Save to localStorage on click
                                                                >
                                                                    <h6 className="title">{elem.title}</h6>
                                                                    <p className="module text-muted">Osce</p>

                                                                </Link>
                                                            </li>
                                                        ))}
                                                    </>
                                                )}

                                                {searchValue?.munchies?.length > 0 && (
                                                    <>
                                                        {searchValue.munchies.map((elem) => (
                                                            <li key={elem.id}>
                                                                <Link
                                                                    href={`/search-result`}
                                                                    className="dropdown-item"
                                                                    onClick={() => saveToLocalStorage(elem)} // Save to localStorage on click
                                                                >
                                                                    <h6 className="title">{elem.title}</h6>
                                                                    <p className="module text-muted">AI-Rad</p>

                                                                </Link>
                                                            </li>
                                                        ))}
                                                    </>
                                                )}
                                                {searchValue?.whatchandlearn?.length > 0 && (
                                                    <>
                                                        {/* <h5 className="dropdown-item">Watch And Learn  3</h5> */}
                                                        {searchValue.whatchandlearn.map((elem) => (
                                                            <li key={elem.id}>
                                                                <Link
                                                                    href={`/search-result`}
                                                                    className="dropdown-item"
                                                                    onClick={() => saveToLocalStorage(elem)} // Save to localStorage on click
                                                                >
                                                                    <h6 className="title">{elem.title}</h6>
                                                                    <p className="module text-muted">Watch And Learn</p>

                                                                </Link>
                                                            </li>
                                                        ))}
                                                    </>
                                                )}
                                                {searchValue?.basics?.length > 0 && (
                                                    <>
                                                        {searchValue.basics.map((elem) => (
                                                            <li key={elem.id}>
                                                                <Link
                                                                    href={`/search-result`}
                                                                    className="dropdown-item"
                                                                    onClick={() => saveToLocalStorage(elem)} // Save to localStorage on click
                                                                >
                                                                    <h6 className="title">{elem.title}</h6>
                                                                    <p className="module text-muted">Practical Essentials</p>

                                                                </Link>
                                                            </li>
                                                        ))}
                                                    </>
                                                )}

                                                {/* No Results Found */}
                                                {(!searchValue?.notes?.length && !searchValue?.spotters?.length) && (
                                                    <li>
                                                        <span className="dropdown-item text-muted">No results found</span>
                                                    </li>
                                                )}
                                            </ul>
                                        </div>
                                    </li>



                                </ul>
                            </div>
                        </nav>
                    </div>
                </div >
            </header >




            <div className="modal" id="myModal">
                <LoginModel />
            </div>




            <div className="modal" id="myModal1">
                <SignUpModel />
            </div>




            <div className="modal" id="myModal3">
                <Forget />
            </div>





        </>
    )
}

export default Navbar