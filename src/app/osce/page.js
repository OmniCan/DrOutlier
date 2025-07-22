"use client"
import Footer from '@/components/Footer'
import Navbar from '@/components/Navbar'
import React, { useEffect, useState } from 'react'
import baseUrl from '@/Services/BaseUrl';

import Link from 'next/link';
import Cookies from 'js-cookie'
import axios from 'axios';
import Loader from '@/components/Loader';
import { toast } from 'react-toastify';
import { useRouter } from 'next/navigation'



function page() {

    // const [spooterdetails, setspooterdetails] = useState([])

    const [spooterdetails, setosce] = useState([])
    const [userid, setUser] = useState('')
    const [loading, setLoading] = useState(false);
    const router = useRouter()
    const [isActive, setIsActive] = useState(false);
    const [showAnswer, setShowAnswer] = useState(false);
    const [pagetitle, setSelectedTitle] = useState('')

    const [currentPage, setCurrentPage] = useState(1);
    const itemsPerPage = 1; // Adjust the number of items per page as needed

    // Calculate the total number of pages
    const totalPages = Math.ceil(spooterdetails?.length / itemsPerPage);

    // Get the data for the current page
    const currentData = spooterdetails?.slice(
        (currentPage - 1) * itemsPerPage,
        currentPage * itemsPerPage
    );

    // Handle page change
    const handlePageChange = (page) => {
        if (page > 0 && page <= totalPages) {
            setCurrentPage(page);
        }
        window.location.hash = `page${page}`; // Update the hash in the URL
        setShowAnswer(false)
    };



    useEffect(() => {
        setLoading(true);
        const IsUserExist = Cookies.get('user-token')
        if (!IsUserExist) {
            router.push('/')
        }
    }, []);





    useEffect(() => {
        const cookies = Cookies.get('user-token');
        axios.post(`${baseUrl}/api/osce/list-all`, {}, {
            headers: {
                'Authorization': `Bearer ${cookies}`
            }
        }).then((e) => {
            setosce(e?.data?.data?.datalist)
            setLoading(false);
        })

        const user = Cookies.get('user-id');
        setUser(user)

    }, [])


    useState(() => {
        if (currentData) {
        }
    }, [currentData])



    const saveosce = (e) => {
        const cookies = Cookies.get('user-token');
        // Create a FormData object
        const formData = new FormData();
        formData.append('user_id', userid);
        formData.append('osce_id', e);

        // Make the POST request with FormData
        axios.post(`${baseUrl}/api/osce/change-osce-bookmark`, formData, {
            headers: {
                'Authorization': `Bearer ${cookies}`,
            }
        }).then((response) => {
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



    const getPageRange = () => {
        const range = [];

        // Always include the first page
        range.push(1);

        // Add ellipsis if currentPage is far from the start
        if (currentPage > 3) {
            range.push("...");
        }

        // Add the currentPage and its neighbors
        for (let i = Math.max(2, currentPage - 1); i <= Math.min(totalPages - 1, currentPage + 1); i++) {
            range.push(i);
        }

        // Add ellipsis if currentPage is far from the end
        if (currentPage < totalPages - 2) {
            range.push("...");
        }

        // Always include the last page
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
                                                OSCE
                                            </h2>
                                        </div>
                                    </div>

                                    <div className="col-lg-8">
                                        <div className="swiper-pagination">

                                            <ul className="pagination">
                                                <li className={`page-item ${currentPage === 1 ? "disabled" : ""}`}>
                                                    <a
                                                        href="#"
                                                        className="page-link"
                                                        onClick={() => currentPage > 1 && handlePageChange(currentPage - 1)}
                                                    >
                                                        «
                                                    </a>
                                                </li>

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
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {currentData.map((e, index) => (
                            <div key={index} className="container"                            >
                                <div
                                    className="macaroni-sign-wrap p-4"
                                    style={{ backgroundColor: "#fff" }}
                                >
                                    <div className="row">
                                        <div className="col-lg-4 sticky-top">
                                            <div className="image">
                                                <img
                                                    src={`${baseUrl}/assets/admin/images/osce/${e.image}`}

                                                    className="img-fluid w-100 mb-4"
                                                    alt="Macaroni Sign"
                                                />
                                                {/* <h6 className="text-center">{e?.title}</h6> */}
                                            </div>
                                        </div>
                                        <div className="col-lg-8">
                                            <div className="macaroni-sign-inner">
                                                <div className='box-osce' >
                                                    <h5 style={{ color: 'black', display: 'flex', justifyContent: 'space-between' }}                                                    >
                                                        <span>
                                                            Question
                                                        </span>
                                                        <div className='ocse-btnQuiz-button'>
                                                            <div className='btnQuiz'>
                                                                <button className="btn bth-link btn-next btnQuiz"
                                                                    onClick={() => setShowAnswer(!showAnswer)}
                                                                >
                                                                    {showAnswer ? "Hide Answer" : "Show Answer"}
                                                                </button>
                                                            </div>
                                                            <div
                                                                className={`icon ${isActive ? "bookmark-active" : ""}`}
                                                                onClick={() => saveosce(e.id)}>
                                                                <i className="fa-solid fa-bookmark" />
                                                            </div>
                                                        </div>
                                                    </h5>

                                                    {e.question.map((e) => {
                                                        return (
                                                            <>
                                                                <p>{e.question}</p>
                                                            </>
                                                        )
                                                    })}
                                                </div>

                                                {showAnswer ? (
                                                    <div className='box'>
                                                        <h5 style={{ color: 'black' }} >Answer</h5>
                                                        {e.question.map((e) => {
                                                            return (
                                                                <>
                                                                    <p>{e.answer}</p>
                                                                </>
                                                            )
                                                        })}
                                                    </div>
                                                ) : null}
                                            </div>

                                        </div>
                                    </div>
                                    {e?.content && showAnswer && (
                                        <div className="" style={{ marginBottom: "20px" }}>
                                            <div className="content-explanation-box" style={{
                                                backgroundColor: "#f8f9fa",
                                                padding: "20px",
                                                borderRadius: "8px",
                                                marginTop: "20px",
                                                border: "1px solid #dee2e6",
                                                borderLeft: "4px solid #0d6efd",
                                                width: "100%"
                                            }}>
                                                <h4 style={{ marginBottom: "15px", color: "#2c4a87" }}>Explanation</h4>
                                                <div dangerouslySetInnerHTML={{ __html: e?.content }} />
                                            </div>
                                        </div>
                                    )}
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
                            {currentData.map((e) => {
                                return (
                                    <>
                                        <div
                                            className={`icon ${isActive ? "bookmark-active" : ""}`}
                                            onClick={() => saveosce(e.id)}>
                                            <i className="fa-solid fa-bookmark" />
                                        </div>
                                        <div className="image">
                                            <img
                                                src={`${baseUrl}/assets/admin/images/osce/${e.image}`}

                                                className="img-fluid w-100 mb-5"
                                                alt="Macaroni Sign"
                                            />
                                        </div>

                                        <div className="macaroni-sign-wrap " style={{ backgroundColor: "#fff" }}>
                                            <div className="container">
                                                <div className="row">
                                                    <div className="col-12">
                                                        <i className="fa-solid fa-share" onClick={handleShare} />
                                                        <div className="macaroni-sign-inner">
                                                            <div className="swiper-container">
                                                                <div className="swiper-wrapper">
                                                                    <div className="swiper-slide">
                                                                        <div className="macaroni-sign-inner">
                                                                            <div className='box-osce' >
                                                                                <div className='box-osce-show-answer' >
                                                                                    <h5 style={{ color: 'black' }} >
                                                                                        Question
                                                                                    </h5>
                                                                                    <div className='ocse-btnQuiz-button'>
                                                                                        <div className='btnQuiz'>
                                                                                            <button className="btn bth-link btn-next btnQuiz"
                                                                                                onClick={() => setShowAnswer(!showAnswer)}
                                                                                            >
                                                                                                {showAnswer ? "Hide Answer" : "Show Answer"}
                                                                                            </button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                {e.question.map((e) => {
                                                                                    return (
                                                                                        <>
                                                                                            <p>{e.question}</p>
                                                                                        </>
                                                                                    )
                                                                                })}
                                                                            </div>
                                                                            {e?.content && showAnswer && (
                                                                                <div className='box'>
                                                                                    <h5 style={{ color: 'black' }} >Answer</h5>
                                                                                    {e.question.map((e) => {
                                                                                        return (
                                                                                            <>
                                                                                                <p>{e.answer}</p>
                                                                                            </>
                                                                                        )
                                                                                    })}
                                                                                </div>
                                                                            )}
                                                                            {e?.content && showAnswer && (
                                                                                <div className="" style={{ marginBottom: "20px" }}>
                                                                                    <div className="content-explanation-box" style={{
                                                                                        backgroundColor: "#f8f9fa",
                                                                                        padding: "20px",
                                                                                        borderRadius: "8px",
                                                                                        marginTop: "20px",
                                                                                        border: "1px solid #dee2e6",
                                                                                        borderLeft: "4px solid #0d6efd",
                                                                                        width: "100%"
                                                                                    }}>
                                                                                        <h4 style={{ marginBottom: "15px", color: "#2c4a87" }}>Explanation</h4>
                                                                                        <div dangerouslySetInnerHTML={{ __html: e?.content }} />
                                                                                    </div>
                                                                                </div>
                                                                            )}
                                                                        </div>
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
                                            <li className="page-item previous ">
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