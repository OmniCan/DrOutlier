"use client"
import Footer from '@/components/Footer'
import Navbar from '@/components/Navbar'
import axios from 'axios'
import baseUrl from '@/Services/BaseUrl'
import React, { useEffect, useState } from 'react'
import Cookies from 'js-cookie'


import Link from 'next/link'

import { useRouter } from 'next/navigation'






function page() {

    const [allnoteCatogery, SetAllNoteCatogery] = useState([])

    const [datapagedata, setnotedata] = useState([])
    const [catid, SetcatID] = useState('')
    const [titles, setTitles] = useState([]);


    const router = useRouter()


    console.log(allnoteCatogery, "allnoteCatogery")




    console.log(catid, "catid")


    console.log(titles, "titles")

    console.log(allnoteCatogery[0]?.child[0]?.id, 'firstcatid')




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
        axios.post(`${baseUrl}/api/category-munchie/list`, {}, {
            headers: {
                'Authorization': `Bearer ${cookies}`
            }
        })
            .then((response) => {
                SetAllNoteCatogery(response.data.data.datalist);
            })
            .catch((error) => {
                console.error(error);
            });
    }, []);






    const SetcatIDPhone = (e) => {
        localStorage.setItem('ai-rad-id', e)

        router.push('/ai-rad-phone')
    }



   

    console.log(datapagedata, 'datapagedata')



    return (


        <>
            <Navbar />


            <div className="main-wrapper">
                <section className="notes-page d-none d-lg-block">
                    <div className="container">
                        <div className="row">
                            <div className="col-md-4">
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
                                                    >
                                                        {item?.name} <span>(35 Topics)</span>
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
                                                                <li key={childItem.id} onClick={() => SetcatID(childItem.id)} >
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



                            <div className="col-md-8">
                                <div className="notes-inner">
                                    <div className="heading-top">
                                        <div className="row">
                                            <div className="col-md-9">
                                                <div className="title">
                                                    {/* <h2>CNS Tumor Classification</h2> */}
                                                    <h2>{titles}</h2>
                                                </div>
                                            </div>


                                            <div className="col-md-3">
                                                <div className="swiper-pagination">
                                                    <div className="swiper-button-next" onClick={() => SetcatID(catid - 1)} />
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
                                                    <div className="swiper-button-prev" onClick={() => SetcatID(catid + 1)} />
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
                                            <h6>AI-Rad</h6>
                                            <span>Select topic to know</span>
                                        </div>
                                        <div className="col-1" />
                                    </div>
                                </div>
                            </div>
                            <div className="list">
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
                                                    >
                                                        {/* CNS <span>(35 Topics)</span> */}

                                                        {item?.name} <span>(35 Topics)</span>


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

            <Footer />


        </>


    )
}

export default page