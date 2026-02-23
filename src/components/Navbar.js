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
import { useRouter, usePathname } from 'next/navigation'
import { useGoogleLogin } from '@react-oauth/google/dist';
import LoginModel from './LoginModel';
import SignUpModel from './SignUpModel';
import Forget from './ForgetPassword/Forget';
import useNavigation from '@/hooks/useNavigation';



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
    const pathname = usePathname()
    const [Username, SetLoginUsername] = useState('UserName')
    const { navigationItems, loading: navLoading } = useNavigation();


    // Initialize Bootstrap components on mount
    useEffect(() => {
        // Dynamically import Bootstrap JS only on client side
        if (typeof window !== 'undefined') {
            import('bootstrap/dist/js/bootstrap.bundle.min.js').then((bootstrap) => {
                // Store bootstrap globally for use throughout the component
                window.bootstrap = bootstrap;
            }).catch(err => {
                console.error('Error loading Bootstrap:', err);
            });
        }
    }, []);

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
        Cookies.remove('user-token');
        Cookies.remove('user-id');
        Cookies.remove('Login-user');
        Cookies.remove('user-email');
        router.push('/');
        setcount(prv => prv + 1);
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
                                                <Link
                                                    href="/subscription"
                                                    className="dropdown-item"
                                                    style={{ cursor: 'pointer', display: 'flex', alignItems: 'center', gap: '8px' }}
                                                >
                                                    <i className="fas fa-crown" style={{ color: '#126E97' }}></i>
                                                    My Subscription
                                                </Link>
                                            </li>
                                            <li><hr className="dropdown-divider" style={{ borderColor: 'rgba(255, 255, 255, 0.1)' }} /></li>
                                            <li>
                                                <Link
                                                    href="/profile"
                                                    className="dropdown-item"
                                                    style={{ cursor: 'pointer', display: 'flex', alignItems: 'center', gap: '8px' }}
                                                >
                                                    <i className="fas fa-user-circle" style={{ color: '#126E97' }}></i>
                                                    Profile
                                                </Link>
                                            </li>
                                            <li>
                                                <Link
                                                    href="/bookmarks"
                                                    className="dropdown-item"
                                                    style={{ cursor: 'pointer', display: 'flex', alignItems: 'center', gap: '8px' }}
                                                >
                                                    <i className="fas fa-bookmark" style={{ color: '#126E97' }}></i>
                                                    Saved / Bookmarks
                                                </Link>
                                            </li>
                                            <li><hr className="dropdown-divider" style={{ borderColor: 'rgba(255, 255, 255, 0.1)' }} /></li>
                                            <li>
                                                <button
                                                    className="dropdown-item"
                                                    type="button"
                                                    style={{ cursor: 'pointer', zIndex: 9999, display: 'flex', alignItems: 'center', gap: '8px' }}
                                                    onClick={() => handleLogOut()}
                                                >
                                                    <i className="fas fa-sign-out-alt" style={{ color: '#FF5252' }}></i>
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
                            style={{
                                width: '85%',
                                maxWidth: '320px',
                                minWidth: '280px',
                                background: 'linear-gradient(180deg, #1B1E27 0%, #0F1116 100%)',
                                boxShadow: '-4px 0 20px rgba(0, 0, 0, 0.5)'
                            }}
                        >
                            <div className="offcanvas-header" style={{
                                borderBottom: '1px solid rgba(255, 255, 255, 0.1)',
                                padding: '20px 24px'
                            }}>
                                <div style={{ width: '100%' }}>
                                    <div className="d-flex justify-content-between align-items-center mb-2">
                                        <h5 className="mb-0" style={{ 
                                            color: 'white', 
                                            fontSize: '18px',
                                            fontWeight: '600',
                                            fontFamily: 'Poppins'
                                        }}>Menu</h5>
                                        <button
                                            type="button"
                                            className="btn-close btn-close-white"
                                            data-bs-dismiss="offcanvas"
                                            aria-label="Close"
                                            style={{
                                                fontSize: '14px',
                                                opacity: 0.8,
                                                transition: 'opacity 0.2s ease'
                                            }}
                                            onMouseEnter={(e) => e.target.style.opacity = '1'}
                                            onMouseLeave={(e) => e.target.style.opacity = '0.8'}
                                        />
                                    </div>
                                    <div className="heading-wrap">
                                        <h6 style={{
                                            color: 'rgba(255, 255, 255, 0.7)',
                                            fontSize: '13px',
                                            fontWeight: '400',
                                            margin: 0,
                                            fontFamily: 'Poppins'
                                        }}>
                                            Welcome to <span style={{ color: '#44A6C5', fontWeight: '600' }}>Dr Outlier Radiology</span>
                                        </h6>
                                    </div>
                                </div>
                            </div>
                            <div className="offcanvas-body" style={{ padding: '16px 0' }}>
                                {/* Login/Logout Button - Mobile */}
                                <div className="d-block d-md-none px-4 mb-3">
                                    {!isAuthenticated ? (
                                        <button
                                            className="btn w-100"
                                            onClick={() => ShowLogin()}
                                            style={{ 
                                                background: 'linear-gradient(92.48deg, #44A6C5 3.13%, #1E4FFD 100%)',
                                                color: 'white',
                                                borderRadius: '10px',
                                                padding: '10px 20px',
                                                fontSize: '15px',
                                                fontWeight: '500',
                                                border: 'none',
                                                fontFamily: 'Poppins',
                                                transition: 'transform 0.2s ease, box-shadow 0.2s ease'
                                            }}
                                            onMouseEnter={(e) => {
                                                e.target.style.transform = 'translateY(-2px)';
                                                e.target.style.boxShadow = '0 4px 12px rgba(68, 166, 197, 0.4)';
                                            }}
                                            onMouseLeave={(e) => {
                                                e.target.style.transform = 'translateY(0)';
                                                e.target.style.boxShadow = 'none';
                                            }}
                                        >
                                            <i className="fa-solid fa-right-to-bracket me-2" />
                                            Login
                                        </button>
                                    ) : (
                                        <button
                                            className="btn w-100"
                                            onClick={() => handleLogOut()}
                                            style={{ 
                                                background: 'rgba(255, 59, 48, 0.1)',
                                                color: '#FF3B30',
                                                borderRadius: '10px',
                                                padding: '10px 20px',
                                                fontSize: '15px',
                                                fontWeight: '500',
                                                border: '1px solid rgba(255, 59, 48, 0.3)',
                                                fontFamily: 'Poppins',
                                                transition: 'all 0.2s ease'
                                            }}
                                            onMouseEnter={(e) => {
                                                e.target.style.background = 'rgba(255, 59, 48, 0.2)';
                                                e.target.style.borderColor = 'rgba(255, 59, 48, 0.5)';
                                            }}
                                            onMouseLeave={(e) => {
                                                e.target.style.background = 'rgba(255, 59, 48, 0.1)';
                                                e.target.style.borderColor = 'rgba(255, 59, 48, 0.3)';
                                            }}
                                        >
                                            <i className="fa-solid fa-right-from-bracket me-2" />
                                            Logout
                                        </button>
                                    )}
                                </div>

                                {/* Mobile Navigation */}
                                <ul className="navbar-nav d-block d-md-none" style={{ padding: '0 12px' }}>
                                    <li className="nav-item" style={{ marginBottom: '4px' }}>
                                        <Link 
                                            className={`nav-link ${pathname === '/' ? 'active' : ''}`} 
                                            onClick={hendleChecklogin} 
                                            href="/" 
                                            data-bs-dismiss="offcanvas"
                                            style={{
                                                color: pathname === '/' ? '#44A6C5' : 'rgba(255, 255, 255, 0.85)',
                                                padding: '12px 16px',
                                                borderRadius: '10px',
                                                fontSize: '15px',
                                                fontWeight: pathname === '/' ? '600' : '400',
                                                background: pathname === '/' ? 'rgba(68, 166, 197, 0.15)' : 'transparent',
                                                transition: 'all 0.2s ease',
                                                display: 'flex',
                                                alignItems: 'center',
                                                fontFamily: 'Poppins'
                                            }}
                                            onMouseEnter={(e) => {
                                                if (pathname !== '/') {
                                                    e.target.style.background = 'rgba(255, 255, 255, 0.05)';
                                                    e.target.style.color = 'white';
                                                }
                                            }}
                                            onMouseLeave={(e) => {
                                                if (pathname !== '/') {
                                                    e.target.style.background = 'transparent';
                                                    e.target.style.color = 'rgba(255, 255, 255, 0.85)';
                                                }
                                            }}
                                        >
                                            <i className="fa-solid fa-house" style={{ marginRight: '12px', fontSize: '16px' }} />
                                            Home
                                        </Link>
                                    </li>
                                    
                                    {/* Dynamic Navigation Items from API - Mobile */}
                                    {!navLoading && Array.isArray(navigationItems) && navigationItems.map((item) => {
                                        const isActive = pathname?.startsWith(item.url);
                                        return (
                                            <li key={item.id} className="nav-item" style={{ marginBottom: '4px' }}>
                                                <Link 
                                                    className={`nav-link ${isActive ? 'active' : ''}`} 
                                                    onClick={hendleChecklogin} 
                                                    href={item.url}
                                                    data-bs-dismiss="offcanvas"
                                                    style={{
                                                        color: isActive ? '#44A6C5' : 'rgba(255, 255, 255, 0.85)',
                                                        padding: '12px 16px',
                                                        borderRadius: '10px',
                                                        fontSize: '15px',
                                                        fontWeight: isActive ? '600' : '400',
                                                        background: isActive ? 'rgba(68, 166, 197, 0.15)' : 'transparent',
                                                        transition: 'all 0.2s ease',
                                                        display: 'flex',
                                                        alignItems: 'center',
                                                        fontFamily: 'Poppins'
                                                    }}
                                                    onMouseEnter={(e) => {
                                                        if (!isActive) {
                                                            e.target.style.background = 'rgba(255, 255, 255, 0.05)';
                                                            e.target.style.color = 'white';
                                                        }
                                                    }}
                                                    onMouseLeave={(e) => {
                                                        if (!isActive) {
                                                            e.target.style.background = 'transparent';
                                                            e.target.style.color = 'rgba(255, 255, 255, 0.85)';
                                                        }
                                                    }}
                                                >
                                                    {item.icon && <i className={item.icon} style={{marginRight: '12px', fontSize: '16px'}} />}
                                                    {item.title}
                                                </Link>
                                            </li>
                                        );
                                    })}

                                    {/* Saved Dropdown - Mobile */}
                                    <li className="nav-item dropdown" style={{ marginTop: '8px', marginBottom: '4px' }}>
                                        <Link
                                            className="nav-link dropdown-toggle"
                                            href="#"
                                            role="button"
                                            data-bs-toggle="dropdown"
                                            style={{
                                                color: 'rgba(255, 255, 255, 0.85)',
                                                padding: '12px 16px',
                                                borderRadius: '10px',
                                                fontSize: '15px',
                                                fontWeight: '400',
                                                background: 'transparent',
                                                transition: 'all 0.2s ease',
                                                display: 'flex',
                                                alignItems: 'center',
                                                justifyContent: 'space-between',
                                                fontFamily: 'Poppins'
                                            }}
                                            onMouseEnter={(e) => {
                                                e.target.style.background = 'rgba(255, 255, 255, 0.05)';
                                                e.target.style.color = 'white';
                                            }}
                                            onMouseLeave={(e) => {
                                                e.target.style.background = 'transparent';
                                                e.target.style.color = 'rgba(255, 255, 255, 0.85)';
                                            }}
                                        >
                                            <span>
                                                <i className="fa-solid fa-bookmark" style={{marginRight: '12px', fontSize: '16px'}} />
                                                Saved
                                            </span>
                                        </Link>
                                        <ul className="dropdown-menu" style={{
                                            background: '#252A3A',
                                            border: '1px solid rgba(255, 255, 255, 0.1)',
                                            borderRadius: '10px',
                                            padding: '8px',
                                            marginTop: '4px'
                                        }}>
                                            <li>
                                                <Link 
                                                    className="dropdown-item" 
                                                    onClick={hendleChecklogin} 
                                                    href="/spotters/save-spootters" 
                                                    data-bs-dismiss="offcanvas"
                                                    style={{
                                                        color: 'rgba(255, 255, 255, 0.85)',
                                                        padding: '10px 14px',
                                                        borderRadius: '8px',
                                                        fontSize: '14px',
                                                        fontFamily: 'Poppins',
                                                        transition: 'all 0.2s ease'
                                                    }}
                                                    onMouseEnter={(e) => {
                                                        e.target.style.background = 'rgba(68, 166, 197, 0.15)';
                                                        e.target.style.color = '#44A6C5';
                                                    }}
                                                    onMouseLeave={(e) => {
                                                        e.target.style.background = 'transparent';
                                                        e.target.style.color = 'rgba(255, 255, 255, 0.85)';
                                                    }}
                                                >
                                                    <i className="fa-solid fa-image" style={{marginRight: '10px', fontSize: '14px'}} />
                                                    Saved Spotters
                                                </Link>
                                            </li>
                                            <li>
                                                <Link 
                                                    className="dropdown-item" 
                                                    onClick={hendleChecklogin} 
                                                    href="/notes/save-notes" 
                                                    data-bs-dismiss="offcanvas"
                                                    style={{
                                                        color: 'rgba(255, 255, 255, 0.85)',
                                                        padding: '10px 14px',
                                                        borderRadius: '8px',
                                                        fontSize: '14px',
                                                        fontFamily: 'Poppins',
                                                        transition: 'all 0.2s ease'
                                                    }}
                                                    onMouseEnter={(e) => {
                                                        e.target.style.background = 'rgba(68, 166, 197, 0.15)';
                                                        e.target.style.color = '#44A6C5';
                                                    }}
                                                    onMouseLeave={(e) => {
                                                        e.target.style.background = 'transparent';
                                                        e.target.style.color = 'rgba(255, 255, 255, 0.85)';
                                                    }}
                                                >
                                                    <i className="fa-solid fa-book" style={{marginRight: '10px', fontSize: '14px'}} />
                                                    Saved Notes
                                                </Link>
                                            </li>
                                            <li>
                                                <Link 
                                                    className="dropdown-item" 
                                                    onClick={hendleChecklogin} 
                                                    href="/osce/save-osce" 
                                                    data-bs-dismiss="offcanvas"
                                                    style={{
                                                        color: 'rgba(255, 255, 255, 0.85)',
                                                        padding: '10px 14px',
                                                        borderRadius: '8px',
                                                        fontSize: '14px',
                                                        fontFamily: 'Poppins',
                                                        transition: 'all 0.2s ease'
                                                    }}
                                                    onMouseEnter={(e) => {
                                                        e.target.style.background = 'rgba(68, 166, 197, 0.15)';
                                                        e.target.style.color = '#44A6C5';
                                                    }}
                                                    onMouseLeave={(e) => {
                                                        e.target.style.background = 'transparent';
                                                        e.target.style.color = 'rgba(255, 255, 255, 0.85)';
                                                    }}
                                                >
                                                    <i className="fa-solid fa-stethoscope" style={{marginRight: '10px', fontSize: '14px'}} />
                                                    Saved OSCE
                                                </Link>
                                            </li>
                                            <li>
                                                <Link 
                                                    className="dropdown-item" 
                                                    onClick={hendleChecklogin} 
                                                    href="/ai-rad/save-ai-rad" 
                                                    data-bs-dismiss="offcanvas"
                                                    style={{
                                                        color: 'rgba(255, 255, 255, 0.85)',
                                                        padding: '10px 14px',
                                                        borderRadius: '8px',
                                                        fontSize: '14px',
                                                        fontFamily: 'Poppins',
                                                        transition: 'all 0.2s ease'
                                                    }}
                                                    onMouseEnter={(e) => {
                                                        e.target.style.background = 'rgba(68, 166, 197, 0.15)';
                                                        e.target.style.color = '#44A6C5';
                                                    }}
                                                    onMouseLeave={(e) => {
                                                        e.target.style.background = 'transparent';
                                                        e.target.style.color = 'rgba(255, 255, 255, 0.85)';
                                                    }}
                                                >
                                                    <i className="fa-solid fa-brain" style={{marginRight: '10px', fontSize: '14px'}} />
                                                    Saved AI-Rad
                                                </Link>
                                            </li>
                                            <li>
                                                <Link 
                                                    className="dropdown-item" 
                                                    onClick={hendleChecklogin} 
                                                    href="/practical-essentials/save-practical-essentials" 
                                                    data-bs-dismiss="offcanvas"
                                                    style={{
                                                        color: 'rgba(255, 255, 255, 0.85)',
                                                        padding: '10px 14px',
                                                        borderRadius: '8px',
                                                        fontSize: '14px',
                                                        fontFamily: 'Poppins',
                                                        transition: 'all 0.2s ease'
                                                    }}
                                                    onMouseEnter={(e) => {
                                                        e.target.style.background = 'rgba(68, 166, 197, 0.15)';
                                                        e.target.style.color = '#44A6C5';
                                                    }}
                                                    onMouseLeave={(e) => {
                                                        e.target.style.background = 'transparent';
                                                        e.target.style.color = 'rgba(255, 255, 255, 0.85)';
                                                    }}
                                                >
                                                    <i className="fa-solid fa-clipboard-check" style={{marginRight: '10px', fontSize: '14px'}} />
                                                    Saved Practical Essentials
                                                </Link>
                                            </li>
                                            <li>
                                                <Link 
                                                    className="dropdown-item" 
                                                    onClick={hendleChecklogin} 
                                                    href="/watch-and-learn/save-watch-and-lern" 
                                                    data-bs-dismiss="offcanvas"
                                                    style={{
                                                        color: 'rgba(255, 255, 255, 0.85)',
                                                        padding: '10px 14px',
                                                        borderRadius: '8px',
                                                        fontSize: '14px',
                                                        fontFamily: 'Poppins',
                                                        transition: 'all 0.2s ease'
                                                    }}
                                                    onMouseEnter={(e) => {
                                                        e.target.style.background = 'rgba(68, 166, 197, 0.15)';
                                                        e.target.style.color = '#44A6C5';
                                                    }}
                                                    onMouseLeave={(e) => {
                                                        e.target.style.background = 'transparent';
                                                        e.target.style.color = 'rgba(255, 255, 255, 0.85)';
                                                    }}
                                                >
                                                    <i className="fa-solid fa-video" style={{marginRight: '10px', fontSize: '14px'}} />
                                                    Saved Watch &amp; Learn
                                                </Link>
                                            </li>
                                            <li>
                                                <Link 
                                                    className="dropdown-item" 
                                                    onClick={hendleChecklogin} 
                                                    href="/quizora" 
                                                    data-bs-dismiss="offcanvas"
                                                    style={{
                                                        color: 'rgba(255, 255, 255, 0.85)',
                                                        padding: '10px 14px',
                                                        borderRadius: '8px',
                                                        fontSize: '14px',
                                                        fontFamily: 'Poppins',
                                                        transition: 'all 0.2s ease'
                                                    }}
                                                    onMouseEnter={(e) => {
                                                        e.target.style.background = 'rgba(68, 166, 197, 0.15)';
                                                        e.target.style.color = '#44A6C5';
                                                    }}
                                                    onMouseLeave={(e) => {
                                                        e.target.style.background = 'transparent';
                                                        e.target.style.color = 'rgba(255, 255, 255, 0.85)';
                                                    }}
                                                >
                                                    <i className="fa-solid fa-question-circle" style={{marginRight: '10px', fontSize: '14px'}} />
                                                    Saved Quizzes
                                                </Link>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>

                                {/* Desktop Navigation - Saved Items */}
                                <div className="d-none d-md-block">
                                    <div style={{ 
                                        padding: '0 16px',
                                        marginTop: '16px',
                                        marginBottom: '12px'
                                    }}>
                                        <h6 style={{
                                            color: 'rgba(255, 255, 255, 0.5)',
                                            fontSize: '12px',
                                            fontWeight: '600',
                                            textTransform: 'uppercase',
                                            letterSpacing: '0.5px',
                                            fontFamily: 'Poppins'
                                        }}>
                                            Saved Items
                                        </h6>
                                    </div>
                                    <ul className="navbar-nav" style={{ padding: '0 12px' }}>
                                        <li className="nav-item" style={{ marginBottom: '4px' }}>
                                            <Link 
                                                className="nav-link" 
                                                onClick={hendleChecklogin} 
                                                href="/spotters/save-spootters"
                                                style={{
                                                    color: 'rgba(255, 255, 255, 0.85)',
                                                    padding: '12px 16px',
                                                    borderRadius: '10px',
                                                    fontSize: '15px',
                                                    fontWeight: '400',
                                                    background: 'transparent',
                                                    transition: 'all 0.2s ease',
                                                    display: 'flex',
                                                    alignItems: 'center',
                                                    justifyContent: 'space-between',
                                                    fontFamily: 'Poppins'
                                                }}
                                                onMouseEnter={(e) => {
                                                    e.target.style.background = 'rgba(68, 166, 197, 0.15)';
                                                    e.target.style.color = '#44A6C5';
                                                    e.target.querySelector('i').style.transform = 'translateX(4px)';
                                                }}
                                                onMouseLeave={(e) => {
                                                    e.target.style.background = 'transparent';
                                                    e.target.style.color = 'rgba(255, 255, 255, 0.85)';
                                                    e.target.querySelector('i').style.transform = 'translateX(0)';
                                                }}
                                            >
                                                <span>
                                                    <i className="fa-solid fa-image" style={{marginRight: '12px', fontSize: '16px'}} />
                                                    Saved Spotters
                                                </span>
                                                <i className="fa-solid fa-chevron-right" style={{ fontSize: '14px', transition: 'transform 0.2s ease' }} />
                                            </Link>
                                        </li>
                                        <li className="nav-item" style={{ marginBottom: '4px' }}>
                                            <Link 
                                                className="nav-link" 
                                                onClick={hendleChecklogin} 
                                                href="/notes/save-notes"
                                                style={{
                                                    color: 'rgba(255, 255, 255, 0.85)',
                                                    padding: '12px 16px',
                                                    borderRadius: '10px',
                                                    fontSize: '15px',
                                                    fontWeight: '400',
                                                    background: 'transparent',
                                                    transition: 'all 0.2s ease',
                                                    display: 'flex',
                                                    alignItems: 'center',
                                                    justifyContent: 'space-between',
                                                    fontFamily: 'Poppins'
                                                }}
                                                onMouseEnter={(e) => {
                                                    e.target.style.background = 'rgba(68, 166, 197, 0.15)';
                                                    e.target.style.color = '#44A6C5';
                                                    e.target.querySelector('i.fa-chevron-right').style.transform = 'translateX(4px)';
                                                }}
                                                onMouseLeave={(e) => {
                                                    e.target.style.background = 'transparent';
                                                    e.target.style.color = 'rgba(255, 255, 255, 0.85)';
                                                    e.target.querySelector('i.fa-chevron-right').style.transform = 'translateX(0)';
                                                }}
                                            >
                                                <span>
                                                    <i className="fa-solid fa-book" style={{marginRight: '12px', fontSize: '16px'}} />
                                                    Saved Notes
                                                </span>
                                                <i className="fa-solid fa-chevron-right" style={{ fontSize: '14px', transition: 'transform 0.2s ease' }} />
                                            </Link>
                                        </li>
                                        <li className="nav-item" style={{ marginBottom: '4px' }}>
                                            <Link 
                                                className="nav-link" 
                                                onClick={hendleChecklogin} 
                                                href="/osce/save-osce"
                                                style={{
                                                    color: 'rgba(255, 255, 255, 0.85)',
                                                    padding: '12px 16px',
                                                    borderRadius: '10px',
                                                    fontSize: '15px',
                                                    fontWeight: '400',
                                                    background: 'transparent',
                                                    transition: 'all 0.2s ease',
                                                    display: 'flex',
                                                    alignItems: 'center',
                                                    justifyContent: 'space-between',
                                                    fontFamily: 'Poppins'
                                                }}
                                                onMouseEnter={(e) => {
                                                    e.target.style.background = 'rgba(68, 166, 197, 0.15)';
                                                    e.target.style.color = '#44A6C5';
                                                    e.target.querySelector('i.fa-chevron-right').style.transform = 'translateX(4px)';
                                                }}
                                                onMouseLeave={(e) => {
                                                    e.target.style.background = 'transparent';
                                                    e.target.style.color = 'rgba(255, 255, 255, 0.85)';
                                                    e.target.querySelector('i.fa-chevron-right').style.transform = 'translateX(0)';
                                                }}
                                            >
                                                <span>
                                                    <i className="fa-solid fa-stethoscope" style={{marginRight: '12px', fontSize: '16px'}} />
                                                    Saved OSCE
                                                </span>
                                                <i className="fa-solid fa-chevron-right" style={{ fontSize: '14px', transition: 'transform 0.2s ease' }} />
                                            </Link>
                                        </li>
                                        <li className="nav-item" style={{ marginBottom: '4px' }}>
                                            <Link 
                                                className="nav-link" 
                                                onClick={hendleChecklogin} 
                                                href="/ai-rad/save-ai-rad"
                                                style={{
                                                    color: 'rgba(255, 255, 255, 0.85)',
                                                    padding: '12px 16px',
                                                    borderRadius: '10px',
                                                    fontSize: '15px',
                                                    fontWeight: '400',
                                                    background: 'transparent',
                                                    transition: 'all 0.2s ease',
                                                    display: 'flex',
                                                    alignItems: 'center',
                                                    justifyContent: 'space-between',
                                                    fontFamily: 'Poppins'
                                                }}
                                                onMouseEnter={(e) => {
                                                    e.target.style.background = 'rgba(68, 166, 197, 0.15)';
                                                    e.target.style.color = '#44A6C5';
                                                    e.target.querySelector('i.fa-chevron-right').style.transform = 'translateX(4px)';
                                                }}
                                                onMouseLeave={(e) => {
                                                    e.target.style.background = 'transparent';
                                                    e.target.style.color = 'rgba(255, 255, 255, 0.85)';
                                                    e.target.querySelector('i.fa-chevron-right').style.transform = 'translateX(0)';
                                                }}
                                            >
                                                <span>
                                                    <i className="fa-solid fa-brain" style={{marginRight: '12px', fontSize: '16px'}} />
                                                    Saved AI-Rad
                                                </span>
                                                <i className="fa-solid fa-chevron-right" style={{ fontSize: '14px', transition: 'transform 0.2s ease' }} />
                                            </Link>
                                        </li>
                                        <li className="nav-item" style={{ marginBottom: '4px' }}>
                                            <Link 
                                                className="nav-link" 
                                                onClick={hendleChecklogin} 
                                                href="/practical-essentials/save-practical-essentials"
                                                style={{
                                                    color: 'rgba(255, 255, 255, 0.85)',
                                                    padding: '12px 16px',
                                                    borderRadius: '10px',
                                                    fontSize: '15px',
                                                    fontWeight: '400',
                                                    background: 'transparent',
                                                    transition: 'all 0.2s ease',
                                                    display: 'flex',
                                                    alignItems: 'center',
                                                    justifyContent: 'space-between',
                                                    fontFamily: 'Poppins'
                                                }}
                                                onMouseEnter={(e) => {
                                                    e.target.style.background = 'rgba(68, 166, 197, 0.15)';
                                                    e.target.style.color = '#44A6C5';
                                                    e.target.querySelector('i.fa-chevron-right').style.transform = 'translateX(4px)';
                                                }}
                                                onMouseLeave={(e) => {
                                                    e.target.style.background = 'transparent';
                                                    e.target.style.color = 'rgba(255, 255, 255, 0.85)';
                                                    e.target.querySelector('i.fa-chevron-right').style.transform = 'translateX(0)';
                                                }}
                                            >
                                                <span>
                                                    <i className="fa-solid fa-clipboard-check" style={{marginRight: '12px', fontSize: '16px'}} />
                                                    Saved Practical Essentials
                                                </span>
                                                <i className="fa-solid fa-chevron-right" style={{ fontSize: '14px', transition: 'transform 0.2s ease' }} />
                                            </Link>
                                        </li>
                                        <li className="nav-item" style={{ marginBottom: '4px' }}>
                                            <Link 
                                                className="nav-link" 
                                                onClick={hendleChecklogin} 
                                                href="/watch-and-learn/save-watch-and-lern"
                                                style={{
                                                    color: 'rgba(255, 255, 255, 0.85)',
                                                    padding: '12px 16px',
                                                    borderRadius: '10px',
                                                    fontSize: '15px',
                                                    fontWeight: '400',
                                                    background: 'transparent',
                                                    transition: 'all 0.2s ease',
                                                    display: 'flex',
                                                    alignItems: 'center',
                                                    justifyContent: 'space-between',
                                                    fontFamily: 'Poppins'
                                                }}
                                                onMouseEnter={(e) => {
                                                    e.target.style.background = 'rgba(68, 166, 197, 0.15)';
                                                    e.target.style.color = '#44A6C5';
                                                    e.target.querySelector('i.fa-chevron-right').style.transform = 'translateX(4px)';
                                                }}
                                                onMouseLeave={(e) => {
                                                    e.target.style.background = 'transparent';
                                                    e.target.style.color = 'rgba(255, 255, 255, 0.85)';
                                                    e.target.querySelector('i.fa-chevron-right').style.transform = 'translateX(0)';
                                                }}
                                            >
                                                <span>
                                                    <i className="fa-solid fa-video" style={{marginRight: '12px', fontSize: '16px'}} />
                                                    Saved Watch &amp; Learn
                                                </span>
                                                <i className="fa-solid fa-chevron-right" style={{ fontSize: '14px', transition: 'transform 0.2s ease' }} />
                                            </Link>
                                        </li>
                                        <li className="nav-item" style={{ marginBottom: '4px' }}>
                                            <Link 
                                                className="nav-link" 
                                                onClick={hendleChecklogin} 
                                                href="/quizora"
                                                style={{
                                                    color: 'rgba(255, 255, 255, 0.85)',
                                                    padding: '12px 16px',
                                                    borderRadius: '10px',
                                                    fontSize: '15px',
                                                    fontWeight: '400',
                                                    background: 'transparent',
                                                    transition: 'all 0.2s ease',
                                                    display: 'flex',
                                                    alignItems: 'center',
                                                    justifyContent: 'space-between',
                                                    fontFamily: 'Poppins'
                                                }}
                                                onMouseEnter={(e) => {
                                                    e.target.style.background = 'rgba(68, 166, 197, 0.15)';
                                                    e.target.style.color = '#44A6C5';
                                                    e.target.querySelector('i.fa-chevron-right').style.transform = 'translateX(4px)';
                                                }}
                                                onMouseLeave={(e) => {
                                                    e.target.style.background = 'transparent';
                                                    e.target.style.color = 'rgba(255, 255, 255, 0.85)';
                                                    e.target.querySelector('i.fa-chevron-right').style.transform = 'translateX(0)';
                                                }}
                                            >
                                                <span>
                                                    <i className="fa-solid fa-question-circle" style={{marginRight: '12px', fontSize: '16px'}} />
                                                    Saved Quizzes
                                                </span>
                                                <i className="fa-solid fa-chevron-right" style={{ fontSize: '14px', transition: 'transform 0.2s ease' }} />
                                            </Link>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </nav >
                    <div className="header-menu">
                        <nav className="navbar navbar-expand-md d-lg-flex d-none">
                            <div className="container">
                                <ul className="navbar-nav justify-content-between w-100 align-items-center">
                                    <li className="nav-item">
                                        <Link className={`nav-link ${pathname === '/' ? 'active' : ''}`} onClick={hendleChecklogin} href="/">
                                            Home
                                        </Link>
                                    </li>
                                    
                                    {/* Dynamic Navigation Items from API */}
                                    {!navLoading && Array.isArray(navigationItems) && navigationItems.map((item) => (
                                        <li key={item.id} className="nav-item">
                                            <Link 
                                                className={`nav-link ${pathname?.startsWith(item.url) ? 'active' : ''}`} 
                                                onClick={hendleChecklogin} 
                                                href={item.url}
                                            >
                                                {item.icon && <i className={item.icon} style={{marginRight: '5px'}} />}
                                                {item.title}
                                            </Link>
                                        </li>
                                    ))}



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