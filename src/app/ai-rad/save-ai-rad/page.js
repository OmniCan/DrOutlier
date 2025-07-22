"use client"
import Footer from '@/components/Footer'
import Navbar from '@/components/Navbar'
import React, { useEffect, useState } from 'react'
import baseUrl from '@/Services/BaseUrl';

import Link from 'next/link';
import axios from 'axios';
import Cookies from 'js-cookie'
import Loader from '@/components/Loader';



function page() {

    const [userid, setUser] = useState('')
    const [allnotes, setspooterdetails] = useState([])
    const [spooterdetails, SaveAllnotes] = useState([])
    const [pagetitle, setSelectedTitle] = useState('')
    const [currentPage, setCurrentPage] = useState(1);

    const [loading, setLoading] = useState(false);

    const itemsPerPage = 1;
    const totalPages = Math.ceil(spooterdetails.length / itemsPerPage);

    const currentData = spooterdetails.slice(
        (currentPage - 1) * itemsPerPage,
        currentPage * itemsPerPage
    );



    const handlePageChange = (page) => {
        if (page > 0 && page <= totalPages) {
            setCurrentPage(page);
        }
    };


    console.log(spooterdetails, "spooterdetails")


    useEffect(() => {
        const user = Cookies.get('user-id');
        setUser(user)
        setLoading(true);

    }, [])



    const savespotters = (e) => {
        const cookies = Cookies.get('user-token');

        const formData = new FormData();

        formData.append('user_id', userid);
        formData.append('spotter_id', e);

        axios.post(`${baseUrl}/api/spotters/change-bookmark-status`, formData, {
            headers: {
                'Authorization': `Bearer ${cookies}`,
            }
        })
            .then((response) => {
                console.log(response.data);
            })
            .catch((error) => {
                console.error('There was an error!', error);
            });
    }

    useEffect(() => {
        const cookies = Cookies.get('user-token');
        axios.post(`${baseUrl}/api/spotters/list`, {}, {
            headers: {
                'Authorization': `Bearer ${cookies}`
            }
        }).then((e) => {
            setspooterdetails(e?.data?.data?.datalist?.data)
        })
    }, [])




    useEffect(() => {

        const cookies = Cookies.get('user-token');
        const userid = Cookies.get('user-id')
        axios.post(`${baseUrl}/api/category-munchie/get-munchie-bookmark?user_id=${userid}    `,
            {}, {
            headers: {
                'Authorization': `Bearer ${cookies}`
            }
        }).then((e) => {

            SaveAllnotes(e?.data?.data?.list?.data)
            setLoading(false);

        })
    }, [])


    useState(() => {
        if (currentData) {
        }
        setSelectedTitle()
    }, [currentData])





    const getPageRange = () => {
        let start = currentPage - 1;
        let end = currentPage + 1;

        if (start < 1) {
            start = 1;
            end = 3;
        }
        if (end > totalPages) {
            start = totalPages - 2 > 0 ? totalPages - 2 : 1;
            end = totalPages;
        }

        return Array.from({ length: end - start + 1 }, (_, index) => start + index);
    };





    return (

        <>
            <Navbar />
            {!loading ? (

                <div className="main-wrapper">



                    <section className="Macaroni-Sign-page pt-0 d-none d-lg-block">
                        <div className="container">
                            <div className="macaroni-top">
                                <div className="row">


                                    <div className="col-lg-6">
                                        <div className="content">
                                            <h2 className="text-white mb-0">
                                                {/* <div className="icon ">
                                                <i className="fa-solid fa-bookmark" />
                                            </div> */}
                                                {/* Spotters */}
                                                Saved AI-Rad
                                            </h2>
                                        </div>
                                    </div>


                                    <div className="col-lg-6">
                                        <div className="swiper-pagination">



                                            <nav aria-label="Page navigation">


                                                <ul className="pagination">
                                                    {/* Previous Button */}
                                                    <li className="page-item">
                                                        <a
                                                            href="#"
                                                            className="page-link"
                                                            onClick={() => handlePageChange(currentPage - 1)}
                                                            disabled={currentPage === 1}
                                                        >
                                                            «
                                                        </a>
                                                    </li>

                                                    {/* Render the page numbers */}
                                                    {getPageRange().map((page) => (
                                                        <li className="page-item" key={page}>
                                                            <a
                                                                href="#"
                                                                onClick={() => handlePageChange(page)}
                                                                className={`btn mx-1 ${currentPage === page ? 'btn-primary' : 'btn-outline-primary'}`}
                                                            >
                                                                {page}
                                                            </a>
                                                        </li>
                                                    ))}

                                                    {/* Next Button */}
                                                    <li className="page-item">
                                                        <a
                                                            href="#"
                                                            className="page-link"
                                                            onClick={() => handlePageChange(currentPage + 1)}
                                                            disabled={currentPage === totalPages}
                                                        >
                                                            »
                                                        </a>
                                                    </li>
                                                </ul>
                                            </nav>


                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>




                        {currentData.map((e, index) => (
                            <div key={index} className="container"
                                onChange={() => {
                                    console.log("Clicked Title:", e?.title); // Debugging click value
                                    setSelectedTitle(e?.title);
                                }}
                            >
                                <div
                                    className="macaroni-sign-wrap p-4"
                                    style={{ backgroundColor: "#fff" }}
                                >
                                    <div className="row">

                                        <div className="col-lg-12">
                                            <div className="macaroni-sign-inner">
                                                {/* <h3
                                                    style={{ color: 'black', display: 'flex', justifyContent: 'space-between' }}
                                                    onClick={() => savespotters(e.id)}
                                                >
                                                    {e?.title}
                                                    <div className="icon ">
                                                        <i className="fa-solid fa-bookmark" />
                                                    </div>

                                                </h3> */}
                                                {/* <p dangerouslySetInnerHTML={{ __html: e?.content }}></p> */}
                                                <h3
                                                    style={{ color: 'black', textAlign: 'center', marginTop: '15px' }}
                                                >No Content Available


                                                </h3>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ))}


                    </section >




                    <section className="Macaroni-sign-page-mobile p-0 d-block d-lg-none">
                        <div className="Macaroni-top">
                            <div className="container">
                                <div className="row">
                                    <Link href='/'>
                                        <div className="col-2">
                                            <i className="fa-solid fa-chevron-left" />
                                        </div>
                                        <div className="col-9 text-center">
                                            {/* <h6>Spotters</h6> */}
                                            <h6>Saved AI-Rad</h6>
                                        </div>
                                    </Link>
                                </div>
                            </div>
                        </div>


                        <div className="Macaroni-middle">











                            {currentData.map((e) => {
                                return (
                                    <>

                                        <div className="icon" onClick={() => savespotters(e.id)}>
                                            <i className="fa-solid fa-bookmark" />
                                        </div>
                                        {/* <div className="image">
                                            <img
                                                // src="images/Macaroni-Sign.webp"
                                                src={`${baseUrl}/assets/admin/images/spotters/${e.image}`}

                                                className="img-fluid w-100 mb-5"
                                                alt="Macaroni Sign"
                                            />
                                        </div> */}


                                        <div className="macaroni-sign-wrap " style={{ backgroundColor: "#fff" }}>
                                            <div className="container">
                                                <div className="row">
                                                    <div className="col-12">
                                                        {/* <i className="fa-solid fa-share" /> */}
                                                        <div className="macaroni-sign-inner">
                                                            <div className="swiper-container">
                                                                <div className="swiper-wrapper">


                                                                    <div className="swiper-slide">
                                                                        {/* <h6>{e?.title}</h6>




                                                                        <p dangerouslySetInnerHTML={{ __html: e?.content }}></p> */}

                                                                        <h3
                                                                            style={{ color: 'black', textAlign: 'center', marginTop: '15px' }}
                                                                        >No Content Available


                                                                        </h3>


                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>





                                    </>



                                )

                            })}


                            <div className="bottom-pagination">
                                <div className="swiper-pagination">


                                    <nav aria-label="Page navigation">
                                        <ul className="pagination justify-content-end mb-0">
                                            <li className="page-item">
                                                <a
                                                    onClick={() => handlePageChange(currentPage - 1)}
                                                >
                                                    «
                                                </a>
                                            </li>


                                            {Array.from({ length: totalPages }, (_, index) => (

                                                <li className="page-item">
                                                    <a
                                                        key={index}
                                                        onClick={() => handlePageChange(index + 1)}
                                                        className={`btn mx-1 ${currentPage === index + 1 ? "btn-primary" : "btn-outline-primary"
                                                            }`}
                                                    >
                                                        {index + 1}
                                                    </a>
                                                </li>

                                            ))}
                                            <li className="page-item">
                                                <a
                                                    className="page-link next"
                                                    href="#"
                                                    onClick={() => handlePageChange(currentPage + 1)}
                                                >
                                                    »
                                                </a>
                                            </li>
                                        </ul>
                                    </nav>


                                </div>
                            </div>
                        </div>
                    </section>
                </div >
            ) : (
                <Loader />
            )}
            <Footer />

        </>



    )
}

export default page