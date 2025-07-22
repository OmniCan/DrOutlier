"use client"
import Footer from '@/components/Footer'
import Navbar from '@/components/Navbar'
import React, { useEffect, useState } from 'react'
import Link from 'next/link'
import axios from 'axios'
import Cookies from 'js-cookie'
import baseUrl from '@/Services/BaseUrl'
import { toast } from 'react-toastify'



function page() {



    const [datapagedata, setnotedata] = useState([])
    const [catid, SetcatID] = useState('')
    const [titles, setTitles] = useState([]);
    const [userid, setUser] = useState('')


    console.log(datapagedata, "cns- tumoor")
    console.log(titles, "titles")






    useEffect(() => {
        const catid = localStorage.getItem('ai-rad-id')
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
    }, [])


    useEffect(() => {
        const user = Cookies.get('user-id');
        setUser(user)

    }, [])

    const savenotes = (e) => {


        console.log('res', e)

        const cookies = Cookies.get('user-token');

        // Create a FormData object
        const formData = new FormData();

        formData.append('user_id', userid);
        formData.append('munchie_id', e);

        // Make the POST request with FormData
        axios.post(`${baseUrl}/api/category-munchie/change-munchie-bookmark-status`, formData, {
            headers: {
                'Authorization': `Bearer ${cookies}`,
            }
        })
            .then((response) => {
                console.log(response.data);
                toast.success(response.data.message);

            })
            .catch((error) => {
                console.error('There was an error!', error);
            });

    }




    return (

        <>
            <Navbar />

            <div className="main-wrapper">
                <section className="inner-list-wrapper">
                    <div className="inner-list-top">
                        <div className="container">
                            <div className="row" style={{ display: 'flex' }} >
                                <Link href='/ai-rad' >
                                    <div className="col-2">
                                        <i className="fa-solid fa-chevron-left" />
                                    </div>
                                </Link>

                                <div className="col-9 text-center">
                                    <h6 style={{color : 'white'}}>{titles}</h6>
                                </div>
                                <div className="col-1" />
                            </div>
                        </div>
                    </div>
                    <div className="page-list">
                        <div className="container">
                            <div className="row">
                                <div className="col-12">
                                    <h6>page1/1</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="content">
                        <div className="container">
                            <div className="row">
                                <div className="col-12">






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
                                                        <>

                                                            <div className="heading">
                                                                <h5 className='heading-text'>{titles}</h5>


                                                                <div className="icon" onClick={() => savenotes(e.id)} >
                                                                    <i className="fa-solid fa-bookmark" />
                                                                </div>
                                                            </div>

                                                            <div style={{ width: '100%' }} key={index} className="html-content">
                                                                <div dangerouslySetInnerHTML={{ __html: e?.content }}></div>
                                                            </div>


                                                        </>
                                                    );
                                                })}
                                            </>
                                        )
                                    }

                                    {/* 
                                    <h6>{titles}</h6>



                                    <p>
                                        {datapagedata.content}
                                    </p>

 */}




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