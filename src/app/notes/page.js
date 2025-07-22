"use client"
import Footer from '@/components/Footer'
import Navbar from '@/components/Navbar'
import axios from 'axios'
import baseUrl from '@/Services/BaseUrl'
import React, { useEffect, useState } from 'react'
import Cookies from 'js-cookie'

import sampleData from "../../../src/components/Datajnd/data.json"; // Adjust the path as needed

import Detailsdata from "../../../src/components/Datajnd/detailsdata.json"; // Adjust the path as needed
import Link from 'next/link'

import { useRouter } from 'next/navigation'
import Loader from '@/components/Loader'
import { toast } from 'react-toastify'






function page() {

    const [allnoteCatogery, SetAllNoteCatogery] = useState([])
    const [loading, setLoading] = useState(false);
    const [activeIndex, setActiveIndex] = useState(0); // Default to the first accordion being open

    const [userid, setUser] = useState('')
    const [isActive, setIsActive] = useState(false);

    const [datapagedata, setnotedata] = useState([])
    const [catid, SetcatID] = useState('')
    const [titles, setTitles] = useState([]);


    const router = useRouter()


    useEffect(() => {
        const user = Cookies.get('user-id');
        setUser(user)

        setLoading(true);


        const IsUserExist = Cookies.get('user-token')
        if (!IsUserExist) {
            router.push('/')
        }


    }, [])


    const savenotes = (e) => {

        const cookies = Cookies.get('user-token');
        axios.post(`${baseUrl}/api/note/change-note-bookmark-status`,
            {
                user_id: userid,
                blog_id: e
            },
            {
                headers: {
                    'Authorization': `Bearer ${cookies}`
                }
            }
        ).then((response) => {
            console.log(response.data);
            toast.success(response.data.message);

            setIsActive(true);
            setTimeout(() => {
                setIsActive(false);
            }, 6000);



        }).catch((error) => {
            console.error('There was an error!', error);
        });
    };


    const isExpanded = false; // All items collapsed by default


    useEffect(() => {
        const cookies = Cookies.get('user-token');
        if (catid) {
            setLoading(true);
            axios.post(`${baseUrl}/api/note/category-notes?page=1&category=${catid}`, {}, {

                headers: {
                    'Authorization': `Bearer ${cookies}`
                }
            })
                .then((response) => {
                    setnotedata(response?.data?.data?.notes?.data);
                    setTitles(response?.data?.data?.notes?.data[0]?.title);
                    setLoading(false);

                    // setTitles()

                })
                .catch((error) => {
                    console.error(error);
                });
        }
    }, [catid]);






    useEffect(() => {
        const cookies = Cookies.get('user-token');
        axios.post(`${baseUrl}/api/note/list`, {}, {
            headers: {
                'Authorization': `Bearer ${cookies}`
            }
        })
            .then((response) => {
                SetAllNoteCatogery(response.data.data.datalist);
                SetcatID(response.data.data.datalist[0]?.child[0]?.id)


            })
            .catch((error) => {
                console.error(error);
            });
    }, []);



    const datalist = sampleData.data.datalist


    const SetcatIDPhone = (e) => {
        localStorage.setItem('category-id', e)
        router.push('/note-details')
    }


    const handleAccordionClick = (index) => {
        setActiveIndex(index === activeIndex ? null : index); // Close if already active
    };


    return (<>


        <>


            <Navbar />
            {!loading ? (


                <div className="main-wrapper">
                    <section className="notes-page d-none d-lg-block section">
                        <div className="container">



                            <div className="macaroni-top">
                                <div className="row">


                                    <div className="col-lg-12">
                                        <div className="content">
                                            <h2 className="text-white mb-0">

                                                Notes
                                            </h2>
                                        </div>
                                    </div>

                                </div>
                            </div>


                            <div className="row mt-4 mb-5">
                                <div className="col-md-4">
                                    <div className="accordion" id="faqAccordion">
                                    {allnoteCatogery.map((item, index) => {
                const collapseId = `collapse-${index}`;
                const headingId = `heading-${index}`;
                const isExpanded = activeIndex === index; // Check if this accordion is active

                return (
                    <div className="accordion-item" key={item.id}>
                        <h2 className="accordion-header" id={headingId}>
                            <button
                                className={`accordion-button ${isExpanded ? '' : 'collapsed'}`}
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target={`#${collapseId}`}
                                aria-expanded={isExpanded}
                                aria-controls={collapseId}
                                style={{ backgroundColor: `${item.color}` }}
                                onClick={() => handleAccordionClick(index)}
                            >
                                <p style={{ color: 'white' }}>
                                    {item?.name} <span>({item?.child?.length || 0} Topics)</span>
                                </p>
                            </button>
                        </h2>

                        <div
                            id={collapseId}
                            className={`accordion-collapse collapse ${isExpanded ? 'show' : ''}`}
                            aria-labelledby={headingId}
                            data-bs-parent="#faqAccordion"
                        >
                            <div className="accordion-body">
                                <ul>
                                    {item.child.map((childItem) => (
                                        <li
                                            key={childItem.id}
                                            onClick={() => SetcatID(childItem.id)}
                                            style={{
                                                backgroundColor: catid === childItem.id ? 'green' : '',
                                                borderRadius: '5px',
                                                paddingLeft: '5px',
                                            }}
                                        >
                                            <a href="#">{childItem.name}</a>
                                        </li>
                                    ))}
                                </ul>
                            </div>
                        </div>
                    </div>
                );
            })}
                                    </div>
                                </div>




                                <div className="col-md-8 mb-5">
                                    <div className="notes-inner">

                                        <div className="heading-top">
                                            <div className="row">
                                                <div className="col-md-9">
                                                    <div className="title">

                                                        <div
                                                            className={`icon ${isActive ? "bookmark-active" : ""}`}
                                                            onClick={() => savenotes(catid)} >
                                                            <i className="fa-solid fa-bookmark" />
                                                        </div>


                                                        <h2>{titles}</h2>


                                                    </div>
                                                </div>


                                                {/* <div className="col-md-3">
                                                    <div className="swiper-pagination">
                                                        <div className="swiper-button-next" onClick={() => SetcatID(catid - 1)} >
                                                            <Link href='/' >
                                                                <i class="fa-solid fa-chevron-right"></i>
                                                            </Link>

                                                        </div>
                                                        <div className="swiper-pagination" >
                                                            <ul>
                                                                <li>
                                                                    <a href="#"></a>
                                                                </li>

                                                                <li>
                                                                    <a href="#"></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <div className="swiper-button-prev" onClick={() => SetcatID(catid + 1)} >
                                                            <i class="fa-solid fa-chevron-left"></i>

                                                        </div>

                                                    </div>
                                                </div> */}
                                            </div>
                                        </div>




                                        <div className="note-content">
                                            <div className="swiper-container">
                                                <div className="swiper-wrapper">
                                                    <div className="swiper-slide">
                                                        {datapagedata?.map((e) => {



                                                            return (
                                                                <>
                                                                    <p dangerouslySetInnerHTML={{ __html: e?.content }}></p>
                                                                </>
                                                            )
                                                        })}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>





                    <section className="notes-mobile-page d-block d-lg-none p-0 pb-4">
                        <div className="container">
                            <div className="row">
                                <div className="heading-lsit">
                                    <div className="container">
                                        <div className="row">
                                            <Link href='/' >
                                                <div className="col-2">
                                                    <i className="fa-solid fa-chevron-left" />
                                                </div>
                                            </Link>

                                            <div className="col-9 text-center">
                                                <h6>Notes</h6>
                                                <span>Select topic to know</span>
                                            </div>
                                            <div className="col-1" />
                                        </div>
                                    </div>
                                </div>
                                <div className="list">
                                    <div className="accordion" id="faqAccordion">

                                        {allnoteCatogery.map((item, index) => {

                                            const collapseId = `collapse-${index}`; // Unique ID for each item
                                            const headingId = `heading-${index}`;   // Unique ID for heading

                                            return (
                                                <div className="accordion-item" key={item.id}>
                                                    <h2 className="accordion-header" id={headingId}>
                                                        <button
                                                            className="accordion-button"
                                                            type="button"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target={`#${collapseId}`}
                                                            aria-expanded="false"
                                                            aria-controls={collapseId}
                                                            style={{ backgroundColor: `${item.color}` }}


                                                        >

                                                            <p style={{ color: 'white' }}  >
                                                                {item?.name} <span>({item?.child?.length || 0} Topics)</span>
                                                            </p>

                                                        </button>
                                                    </h2>
                                                    <div
                                                        id={collapseId}
                                                        className="accordion-collapse collapse"
                                                        aria-labelledby={headingId}
                                                        data-bs-parent="#faqAccordion"
                                                    >
                                                        <div className="accordion-body">
                                                            <ul>
                                                                {item.child.map((childItem) => (
                                                                    <li key={childItem.id}
                                                                        onClick={() => SetcatIDPhone(childItem.id)}
                                                                        style={{ backgroundColor: catid === childItem.id ? 'green' : '', borderRadius: '5px', paddingLeft: '5px' }}

                                                                    >
                                                                        <a >
                                                                            {childItem.name}
                                                                        </a>
                                                                    </li>
                                                                ))}
                                                            </ul>
                                                        </div>
                                                    </div>


                                                </div>

                                            )
                                        })}









                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>



            ) : (
                <Loader />
            )}




            <Footer />


        </>

    </>

    )
}

export default page