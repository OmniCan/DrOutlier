"use client"
import Footer from '@/components/Footer'
import Navbar from '@/components/Navbar'
import axios from 'axios'
import baseUrl from '@/Services/BaseUrl'
import React, { useEffect, useState } from 'react'
import Cookies from 'js-cookie'



import Link from 'next/link'

import { useRouter } from 'next/navigation'
import Loader from '@/components/Loader'






function page() {

    const [allnoteCatogery, SetAllNoteCatogery] = useState([])

    const [datapagedata, setnotedata] = useState([])
    const [catid, SetcatID] = useState('')
    const [titles, setTitles] = useState([]);
    const [loading, setLoading] = useState(false);



    const router = useRouter()



    useEffect(() => {
        setLoading(true);

        const IsUserExist = Cookies.get('user-token')
        if (!IsUserExist) {
            router.push('/')
        }
    }, []);

    useEffect(() => {
        const cookies = Cookies.get('user-token');
        if (catid) {
            axios.post(`${baseUrl}/api/category-munchie/category-munchie?category=${catid}`, {}, {

                headers: {
                    'Authorization': `Bearer ${cookies}`
                }
            })
                .then((response) => {
                    setnotedata(response?.data?.data.notes.data);

                    console.log(response?.data?.data.notes.data, "munchdetails")

                    setTitles(response?.data?.data.notes.data[0]?.title);
                })
                .catch((error) => {
                    console.error(error);
                });
        }
    }, [catid]);


    useEffect(() => {
        const cookies = Cookies.get('user-token');
        axios.post(`${baseUrl}/api/watch-and-learn-category/list`, {}, {
            headers: {
                'Authorization': `Bearer ${cookies}`
            }
        })
            .then((response) => {
                SetAllNoteCatogery(response.data.data.datalist);
                setLoading(false);

            })
            .catch((error) => {
                console.error(error);
            });
    }, []);


    const SetcatIDPhone = (e) => {
        localStorage.setItem('ai-rad-id', e)

        router.push('/ai-rad-phone')
    }




    return (


        <>

            <Navbar />
            {!loading ? (

                <div className="main-wrapper">
                    <section className="notes-page d-none d-lg-block section  ">
                        <div className="container">


                            <div className="macaroni-top">
                                <div className="row">


                                    <div className="col-lg-4">
                                        <div className="content">
                                            <h2 className="text-white mb-0">

                                                Watch & Learn
                                            </h2>
                                        </div>
                                    </div>

                                </div>
                            </div>






                            <div className="row mt-4">
                                <div className="col-md-4">
                                    {/* FAQ Accordion */}
                                    <div className="accordion" id="faqAccordion">
                                        {allnoteCatogery.map((item, index) => {
                                            const collapseId = `collapse-${index}`;
                                            const headingId = `heading-${index}`;
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
                                                            </p>                                                        </button>
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
                                                                        style={{
                                                                            backgroundColor: childItem.id === catid ? 'purple' : 'transparent',
                                                                            color: childItem.id === catid ? 'white' : 'inherit'
                                                                        }}

                                                                        onClick={() => SetcatID(childItem.id)} >
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

                                    <div className="notes-inner" onClick={() => SetcatID(catid - 1)}>



                                        <div className="heading-top">
                                            <div className="row">
                                                <div className="col-md-9">
                                                    <div className="title">
                                                        <h2>{titles}</h2>
                                                    </div>
                                                </div>


                                                <div className="col-md-3">
                                                    <div className="swiper-pagination">
                                                        <div className="swiper-button-next" onClick={() => SetcatID(catid - 1)}>
                                                            <i class="fa-solid fa-chevron-right"></i>


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
                                                        <div className="swiper-button-prev" onClick={() => SetcatID(catid + 1)}>
                                                            <i class="fa-solid fa-chevron-left"></i>


                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>




                                        <div className="note-content">
                                            <div className="swiper-container">
                                                <div className="swiper-wrapper">
                                                    <div className="swiper-slide">
                                                        {
                                                            !datapagedata || datapagedata.length === 0 ? (
                                                                <>
                                                                    <h3
                                                                        style={{ color: 'black', textAlign: 'center', marginTop: '15px' }}
                                                                    >No Content Available</h3>
                                                                </>
                                                            ) : (
                                                                <>
                                                                    {datapagedata?.map((e, index) => {
                                                                        return (
                                                                            <p key={index} dangerouslySetInnerHTML={{ __html: e?.content }}></p>
                                                                        );
                                                                    })}
                                                                </>
                                                            )
                                                        }
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
                                                <h6>Watch And Learn</h6>
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
                                                            {/* CNS <span>(35 Topics)</span> */}

                                                            {item?.name} <span>({item?.child?.length || 0}  Topics)</span>


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
                                                                    <li key={childItem.id} onClick={() => SetcatIDPhone(childItem.id)} >
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


    )
}

export default page