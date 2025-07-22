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
        const user = Cookies.get('user-id');
        setUser(user)

    }, [])


    const savespotters = (e) => {
        // Get the user token from cookies
        const cookies = Cookies.get('user-token');

        // Create a FormData object
        const formData = new FormData();

        // Append user_id and blog_id to the FormData object
        formData.append('user_id', userid);
        formData.append('spotter_id', e);

        // Make the POST request with FormData
        axios.post(`${baseUrl}/api/spotters/change-bookmark-status`, formData, {
            headers: {
                'Authorization': `Bearer ${cookies}`,
                // 'Content-Type': 'multipart/form-data' // Axios will automatically set the correct content type
            }
        }).then((response) => {
            console.log(response.data);
            toast.success(response.data.message);

        })
            .catch((error) => {
                console.error('There was an error!', error);
            });

    }






    useEffect(() => {
        const catid = localStorage.getItem('category-id')
        const cookies = Cookies.get('user-token');

        if (catid) {


            axios.post(`${baseUrl}/api/note/category-notes?page=1&category=${catid}`, {}, {

                headers: {
                    'Authorization': `Bearer ${cookies}`
                }
            })
                .then((response) => {
                    setnotedata(response?.data?.data?.notes?.data);
                    setTitles(response?.data?.data?.notes?.data[0]?.title);
                    // setTitles()
                })
                .catch((error) => {
                    console.error(error);
                });
        }
    }, [])




    return (

        <>
            <Navbar />
            <div className="main-wrapper">
                <section className="inner-list-wrapper">
                    <div className="inner-list-top">
                        <div className="container">
                            <div className="row">
                                <Link href='/notes' >
                                    <div className="col-2">
                                        <i className="fa-solid fa-chevron-left" />
                                    </div>

                                </Link>

                                <div className="col-9 text-center">
                                    {/* <h6>CNS Tumor Classification</h6> */}
                                    <h6>{titles}</h6>

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



                                    {datapagedata.map((e) => {
                                        return (

                                            <>

                                                <div className="heading">
                                                    {/* <h6>CNS Tumor Classification</h6> */}
                                                    <h5 className='heading-text'>{titles}</h5>


                                                    {/* <div className="icon"> */}
                                                    <div className="icon" onClick={() => savespotters(e.id)}>

                                                        <i className="fa-solid fa-bookmark" />
                                                    </div>
                                                </div>

                                                <h6>{titles}</h6>

                                                <p dangerouslySetInnerHTML={{ __html: e?.content }}></p>
                                            </>

                                        )
                                    })}



                                    <h6>{titles}</h6>
              
                                    <p>
                                        {datapagedata.content}
                                    </p>



                                    


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