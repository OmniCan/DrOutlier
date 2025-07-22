"use client"
import Footer from '@/components/Footer'
import Navbar from '@/components/Navbar'
import React, { useEffect, useState } from 'react'
import baseUrl from '@/Services/BaseUrl';
import Link from 'next/link';
import axios from 'axios';
import Cookies from 'js-cookie'
import Loader from '@/components/Loader';
import { toast } from 'react-toastify';
import { useRouter } from 'next/navigation'

function page() {
    const [userid, setUser] = useState('')
    const [loading, setLoading] = useState(false);
    const [isActive, setIsActive] = useState(false);
    const [spooterdetails, setspooterdetails] = useState([])
    const router = useRouter()

    useEffect(() => {
        setLoading(true);
        const IsUserExist = Cookies.get('user-token')
        if (!IsUserExist) {
            router.push('/')
        }
    }, []);

    const [pagetitle, setSelectedTitle] = useState('')
    const [currentPage, setCurrentPage] = useState(1);
    const itemsPerPage = 1;

    const totalPages = Math?.ceil(spooterdetails?.length / itemsPerPage);

    const currentData = spooterdetails?.slice(
        (currentPage - 1) * itemsPerPage,
        currentPage * itemsPerPage
    );

    const handlePageChange = (page) => {
        if (page > 0 && page <= totalPages) {
            setCurrentPage(page);
        }
        window.location.hash = `page${page}`; // Update the hash in the URL

    };


    useEffect(() => {
        const hash = window.location.hash;
        if (hash.startsWith("#page")) {
            const page = parseInt(hash.replace("#page", ""), 10);
            if (!isNaN(page)) {
                setCurrentPage(page);
            }
        }
    }, []);


    useEffect(() => {
        const user = Cookies.get('user-id');
        setUser(user)
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
                toast.success(response.data.message);
                setIsActive(true);
                setTimeout(() => {
                    setIsActive(false);
                }, 6000);
            })
            .catch((error) => {
                console.error('There was an error!', error);
            });
    }


    useEffect(() => {

        const cookies = Cookies.get('user-token');
        axios.post(`${baseUrl}/api/spotters/list-all`, {}, {
            headers: {
                'Authorization': `Bearer ${cookies}`
            }
        }).then((e) => {
            setspooterdetails(e?.data?.data?.datalist)
            setLoading(false);
        })
    }, [])



    useState(() => {
        if (currentData) {
        }
        setSelectedTitle()
    }, [currentData])



    const getPageRange = () => {
        const range = [];
        range.push(1);

        if (currentPage > 3) {
            range.push("...");
        }

        for (let i = Math.max(2, currentPage - 1); i <= Math.min(totalPages - 1, currentPage + 1); i++) {
            range.push(i);
        }

        if (currentPage < totalPages - 2) {
            range.push("...");
        }

        if (totalPages > 1) {
            range.push(totalPages);
        }

        return range;
    };



    const handleShare = async () => {
        const currentUrl = `${window.location.origin}${window.location.pathname}#page${currentPage}`;

        if (navigator.share) {
            try {
                await navigator.share({
                    title: "Check this out!",
                    text: `I'm on page ${currentPage}. Check it out!`,
                    url: currentUrl,
                });
                console.log("Content shared successfully");
            } catch (error) {
                console.error("Error sharing content:", error);
            }
        } else {
            try {
                await navigator.clipboard.writeText(currentUrl);
                alert("URL copied to clipboard! Share it with your friends.");
            } catch (error) {
                console.error("Failed to copy URL:", error);
                alert("Unable to copy URL. Please try manually.");
            }
        }
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


                                    <div className="col-lg-4">
                                        <div className="content">
                                            <h2 className="text-white mb-0">

                                                Spotters
                                            </h2>
                                        </div>
                                    </div>


                                    <div className="col-lg-8">
                                        <div className="swiper-pagination">



                                            <nav aria-label="Page navigation">


                                                <ul className="pagination">
                                                    {/* Previous button */}
                                                    <li className={`page-item ${currentPage === 1 ? "disabled" : ""}`}>
                                                        <a
                                                            href="#"
                                                            className="page-link"
                                                            onClick={() => currentPage > 1 && handlePageChange(currentPage - 1)}
                                                        >
                                                            «
                                                        </a>
                                                    </li>

                                                    {/* Page numbers */}
                                                    {getPageRange().map((page, index) =>
                                                        page === "..." ? (
                                                            <li className="page-item" key={`ellipsis-${index}`}>
                                                                <span className="page-link">...</span>
                                                            </li>
                                                        ) : (
                                                            <li
                                                                className={`page-item ${currentPage === page ? "active" : ""}`}
                                                                key={page}
                                                            >
                                                                <a
                                                                    href="#"
                                                                    onClick={() => handlePageChange(page)}
                                                                    className={`btn mx-1 ${currentPage === page ? "btn-primary" : "btn-outline-primary"}`}
                                                                >
                                                                    {page}
                                                                </a>
                                                            </li>
                                                        )
                                                    )}

                                                    {/* Next button */}
                                                    <li className={`page-item ${currentPage === totalPages ? "disabled" : ""}`}>
                                                        <a
                                                            href="#"
                                                            className="page-link"
                                                            onClick={() => currentPage < totalPages && handlePageChange(currentPage + 1)}
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




                        {currentData?.map((e, index) => (
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
                                        <div className="col-lg-4 sticky-top">
                                            <div className="image">
                                                <img
                                                    // src="images/Macaroni-Sign.webp"
                                                    src={`${baseUrl}/assets/admin/images/spotters/${e.image}`}

                                                    className="img-fluid w-100 mb-4"
                                                    alt="Macaroni Sign"
                                                />
                                                {/* <h5 className="text-center">
                                                    {e?.title}
                                                </h5> */}
                                            </div>
                                        </div>
                                        <div className="col-lg-8">
                                            <div className="macaroni-sign-inner">
                                                <h3
                                                    style={{ color: 'black', display: 'flex', justifyContent: 'space-between' }}

                                                >
                                                    {e?.title}
                                                    <div
                                                        className={`icon ${isActive ? "bookmark-active" : ""}`}
                                                        onClick={() => savespotters(e.id)}>
                                                        <i className="fa-solid fa-bookmark" />
                                                    </div>

                                                </h3>
                                                <p dangerouslySetInnerHTML={{ __html: e?.content }}></p>
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


                                        <div className="col-4">
                                            <i className="fa-solid fa-chevron-left" />
                                        </div>
                                        <div className="col-4 text-center">
                                            <h6>Spotters</h6>
                                        </div>
                                        <div className="col-4" />

                                    </Link>

                                </div>
                            </div>
                        </div>


                        <div className="Macaroni-middle">

                            {currentData?.map((e) => {
                                return (
                                    <>

                                        <div
                                            className={`icon ${isActive ? "bookmark-active" : ""}`}
                                            onClick={() => savespotters(e.id)}>
                                            <i className="fa-solid fa-bookmark" />
                                        </div>
                                        <div className="image">
                                            <img
                                                // src="images/Macaroni-Sign.webp"
                                                src={`${baseUrl}/assets/admin/images/spotters/${e.image}`}

                                                className="img-fluid w-100 mb-5"
                                                alt="Macaroni Sign"
                                            />
                                        </div>


                                        <div className="macaroni-sign-wrap " style={{ backgroundColor: "#fff" }}>
                                            <div className="container">
                                                <div className="row">
                                                    <div className="col-12">
                                                        <div className='logo-with-share' >
                                                            <img className=' img-fluid' src="/images/add-logo.png" alt="" />
                                                        </div>

                                                        <div className='title-in-header'>
                                                            <h5 >{e?.title}</h5>


                                                        </div>

                                                        <i className="fa-solid fa-share" onClick={handleShare} />

                                                        <div className="macaroni-sign-inner">
                                                            <div className="">
                                                                <div className="">


                                                                    {/* <div className="">
                                                                        <p dangerouslySetInnerHTML={{ __html: e?.content }}></p>
                                                                    </div> */}

                                                                    <div style={{ width: '100%' }} className="html-content">
                                                                        <div dangerouslySetInnerHTML={{ __html: e?.content }}></div>
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
                                            <li className="page-item previous">
                                                <a
                                                    className="page-link"
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
                                            <li className="page-item next">
                                                <a
                                                    className="page-link"
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