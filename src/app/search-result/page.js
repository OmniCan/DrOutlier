"use client"
import Footer from '@/components/Footer'
import Navbar from '@/components/Navbar'
import React, { useEffect, useState } from 'react'
import baseUrl from '@/Services/BaseUrl';

// import spooter from "../../../src/components/Datajnd/spooter.json"; // Adjust the path as needed
import Link from 'next/link';
import axios from 'axios';
import Cookies from 'js-cookie'



function page() {


    const [spooterdetails, setspooterdetails] = useState([])
    const [savedData, setSavedData] = useState([]);


    // const spooterdetails = spooter.data.datalist.data

    console.log(spooterdetails, "spooterdetails")

    const [pagetitle, setSelectedTitle] = useState('')

    const [currentPage, setCurrentPage] = useState(1);
    const itemsPerPage = 1; // Adjust the number of items per page as needed

    // Calculate the total number of pages
    const totalPages = Math.ceil(spooterdetails.length / itemsPerPage);

    // Get the data for the current page
    const currentData = spooterdetails.slice(
        (currentPage - 1) * itemsPerPage,
        currentPage * itemsPerPage
    );

    // Handle page change
    const handlePageChange = (page) => {
        setCurrentPage(page);
    };




    useEffect(() => {

        const cookies = Cookies.get('user-token');
        
        axios.post(`${baseUrl}/api/spotters/list`, {}, {
            headers: {
                'Authorization': `Bearer ${cookies}`
            }
        }).then((e) => {
            setspooterdetails(e?.data?.data?.datalist?.data)
            // console.log(e.data.data)
        })

    }, [])




    useState(() => {
        if (currentData) {
            console.log(currentData, "Currentdata , From Usestae")
        }
        setSelectedTitle()
    }, [currentData])


    console.log(pagetitle, "pagetitle")



    useEffect(() => {
        const fetchData = () => {
          const data = localStorage.getItem("selectedData");
          if (data) {
            setSavedData(JSON.parse(data)); // Parse the data and update state
          }
        };
        fetchData();
      }, []);


   console.log(savedData[0] , "savedData")      




    return (

        <>
            <Navbar />

            <div className="main-wrapper">



                <section className="Macaroni-Sign-page pt-0 d-none d-lg-block">
                    <div className="container">
                        <div className="macaroni-top">
                            <div className="row">
                                <div className="col-lg-12">
                                    <div className="content">
                                        <h2 className="text-white mb-0">
                                        
                                            {/* Macaroni Sign */}
                                            {savedData[0]?.title}
                                        </h2>
                                    </div>
                                </div>


                                <div className="col-lg-8">
                                    <div className="swiper-pagination">



                                       
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
                                            <h6>{savedData[0]?.title}</h6>
                                            {/* <p dangerouslySetInnerHTML={{ __html: e?.content }}></p> */}
                                            <p dangerouslySetInnerHTML={{ __html: savedData[0]?.content }}></p>

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




{/* 
                        <div className="icon ">
                            <i className="fa-solid fa-bookmark" />
                        </div> */}





                        {currentData.map((e) => {
                            return (


                                <>
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
                                                    <i className="fa-solid fa-share" />
                                                    <div className="macaroni-sign-inner">
                                                        <div className="swiper-container">
                                                            <div className="swiper-wrapper">


                                                                <div className="swiper-slide">
                                                                    {/* <h6>Lorem ipsum dolor sit amet</h6> */}
                                                                    {/* <h6>{e?.title}</h6> */}
                                                                    <h6>{savedData[0]?.title}</h6>

                                                                    <p dangerouslySetInnerHTML={{ __html: savedData[0]?.content }}></p>

                                                                  
                                                                    {/* <p dangerouslySetInnerHTML={{ __html: e?.content }}></p> */}

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

            <Footer />

        </>



    )
}

export default page