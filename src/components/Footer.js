"use client"
import React, { useEffect, useState } from 'react'
import Link from 'next/link'
import LoginModel from './LoginModel';
import SignUpModel from './SignUpModel';
import Cookies from 'js-cookie';

function Footer() {

    const [isAuthenticated, setIsAuthenticated] = useState(false);


    useEffect(() => {

        const IsUserExist = Cookies.get('user-token')

        if (IsUserExist) {
            setIsAuthenticated(true)
        } else {
            setIsAuthenticated(false)
        }
    }, [])


    const hendleChecklogin = (e) => {
        if (!isAuthenticated) {
            e.preventDefault();
            const myModal5 = new bootstrap.Modal(document.getElementById('myModal'));
            myModal5.show();
        }
    }













    return (
        <>
            <footer className="footer-wrapper">
                <div className="container">
                    <div className="row">
                        <div className="col-lg-4">
                            <div className="footer-logo">
                                <Link href="/">
                                    <img
                                        src="/images/Footer-Logo.webp"
                                        className="img-fluid"
                                        alt="Radiology"
                                    />
                                </Link>
                            </div>
                        </div>
                        <div className="col-lg-8">
                            <div className="social-media-wrapper">
                                <div className="social-media">
                                    <ul>
                                        <li>
                                            <a target='_blank' href="https://api.whatsapp.com/send?phone=918554872707">
                                                <img
                                                    src="/images/whatsapp-icon.webp"
                                                    className="img-fluid"
                                                    alt="Whatsapp"
                                                />
                                            </a>
                                        </li>
                                        <li>
                                            <a href="https://www.youtube.com/@droutlier" target='_blank'>
                                                <img
                                                    src="/images/youtube-icon.svg"
                                                    className="img-fluid"
                                                    alt="Youtube"
                                                />
                                            </a>
                                        </li>
                                        <li>
                                            <a href="https://www.instagram.com/dr.outlier/" target="_blank" rel="noopener noreferrer">
                                                <img
                                                    src="/images/instagram-icon.webp"
                                                    className="img-fluid"
                                                    alt="Instagram"
                                                />
                                            </a>

                                        </li>
                                    </ul>
                                </div>




                                <div className='play-app' style={{ display: 'flex', gap: '5px' }} >

                                    <div className="google-pay">
                                        <button
                                            className="btn link-btn appstore"
                                            onClick={() => {
                                                if (window.matchMedia('(display-mode: standalone)').matches) {
                                                    alert('The app is already installed!');
                                                } else {
                                                    alert('Use your browser menu to "Add to Home Screen".');
                                                }
                                                
                                            }}
                                        >
                                            <div style={{ display: 'flex' }}>
                                                <img src="/images/aapstore.svg" alt="App Store Icon" />
                                            </div>
                                        </button>
                                    </div>


                                    <div className="google-pay">
                                        <button
                                            className="btn link-btn "
                                            onClick={() => {
                                                // Trigger file download
                                                const link = document.createElement('a');
                                                link.href = '/DrOutlier.apk'; // Replace with the actual APK file URL
                                                link.download = 'DrOutlier.apk'; // Optional: Suggest a file name
                                                document.body.appendChild(link);
                                                link.click();
                                                document.body.removeChild(link);
                                            }}
                                            style={{ textDecoration: 'none', padding: '0px' }}
                                        >
                                            <div style={{ display: 'flex' }}>

                                                <img src="/images/playstore.svg" alt="" />
                                            </div>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div className="footer-menu">
                                <ul>
                                    <li>
                                        <Link href="/spotters" onClick={hendleChecklogin} >Spotters</Link>
                                    </li>
                                    <li>
                                        <Link href="notes" onClick={hendleChecklogin} >NOtes</Link>
                                    </li>
                                    <li>
                                        <Link href="/osce" onClick={hendleChecklogin} >OSCE</Link>
                                    </li>
                                    <li>
                                        <Link href="/ai-rad" onClick={hendleChecklogin} >AI-Rad</Link>
                                    </li>
                                    <li>
                                        <Link href="/practical-essentials" onClick={hendleChecklogin} >Practical Essentials</Link>
                                    </li>
                                    <li>
                                        <Link href="/watch-and-learn" onClick={hendleChecklogin} >Watch &amp; Learn</Link>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div className="copyRight">
                        <div className="row">
                            <div className="col-lg-6">
                                <ul>
                                    <li>
                                        <a href="tel:+918554872707">
                                            <i className="fa-solid fa-phone"></i> +91-8554872707
                                        </a>
                                    </li>
                                    <li>
                                        <a href="mailto:droutlierradiology@gmail.com">
                                            <i className="fa-solid fa-envelope"></i> droutlierradiology@gmail.com
                                        </a>
                                    </li>

                                </ul>
                            </div>
                            <div className="col-lg-6">
                                <p>© 2025&nbsp;Dr Outlier</p>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>

            <div className="modal" id="myModal">
                <LoginModel />
            </div>

            <div className="modal" id="myModal1">
                <SignUpModel />
            </div>




        </>
    )
}

export default Footer