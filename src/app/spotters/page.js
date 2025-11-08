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
import { DotLottieReact } from '@lottiefiles/dotlottie-react';

function page() {
    const [userid, setUser] = useState('')
    const [loading, setLoading] = useState(false);
    const [categories, setCategories] = useState([])
    const router = useRouter()

    useEffect(() => {
        setLoading(true);
        const IsUserExist = Cookies.get('user-token')
        if (!IsUserExist) {
            router.push('/')
        }
    }, []);

    useEffect(() => {
        const user = Cookies.get('user-id');
        setUser(user)
    }, [])

    useEffect(() => {
        const cookies = Cookies.get('user-token');
        console.log('Fetching categories from:', `${baseUrl}/api/spotters/categories`);
        
        axios.post(`${baseUrl}/api/spotters/categories`, {}, {
            headers: {
                'Authorization': `Bearer ${cookies}`
            }
        }).then((response) => {
            console.log('Categories API Response:', response);
            console.log('Categories Data:', response?.data);
            console.log('Categories Array:', response?.data?.data);
            setCategories(response?.data?.data || [])
            setLoading(false);
        }).catch((error) => {
            console.error('Error fetching categories:', error);
            console.error('Error response:', error.response);
            setLoading(false);
        })
    }, [])


    return (

        <>



            <Navbar />
            {!loading ? (



                <div className="main-wrapper">



                    <section className="Macaroni-Sign-page pt-0 d-none d-lg-block">
                        <div className="container">


                            <div className="macaroni-top">
                                <div className="row">


                                    <div className="col-lg-12">
                                        <div className="content">
                                            <h2 className="text-white mb-4 text-center">

                                                Spotters Categories
                                            </h2>
                                        </div>
                                    </div>


                                </div>
                            </div>


                            <div className="row">
                                <div className="col-lg-10 m-auto">
                                    <div className="row">
                                        {categories && categories.length > 0 ? (
                                            categories?.map((category, index) => (
                                                <div className="col-lg-4 col-6 mb-4" key={category.id}>
                                                    <Link href={`/spotters/category?id=${category.id}`}>
                                                        <div className="box" style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', textAlign: 'center', height: '100%' }}>
                                                            <DotLottieReact
                                                                src="/animantion/Blue circle 2.json"
                                                                loop
                                                                autoplay
                                                                style={{ 
                                                                    width: '174px', 
                                                                    height: '182px',
                                                                    filter: category.color ? `hue-rotate(${category.color})` : 'none'
                                                                }}
                                                            />
                                                            <h6 style={{ marginTop: '10px' }}>{category.name}</h6>
                                                        </div>
                                                    </Link>
                                                </div>
                                            ))
                                        ) : (
                                            <div className="col-12 text-center">
                                                <p className="text-white">No categories found. Please check the API endpoint or database.</p>
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </div>




                        </div>




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
                                            <h6>Spotters Categories</h6>
                                        </div>
                                        <div className="col-4" />

                                    </Link>

                                </div>
                            </div>
                        </div>


                        <div className="Macaroni-middle">
                            <div className="container">
                                <div className="row">
                                    {categories && categories.length > 0 ? (
                                        categories?.map((category, index) => (
                                            <div className="col-6 mb-4" key={category.id}>
                                                <Link href={`/spotters/category?id=${category.id}`}>
                                                    <div className="box" style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', textAlign: 'center', height: '100%' }}>
                                                        <DotLottieReact
                                                            src="/animantion/Blue circle 2.json"
                                                            loop
                                                            autoplay
                                                            style={{ 
                                                                width: '120px', 
                                                                height: '120px',
                                                                filter: category.color ? `hue-rotate(${category.color})` : 'none'
                                                            }}
                                                        />
                                                        <h6 style={{ marginTop: '10px', fontSize: '14px' }}>{category.name}</h6>
                                                    </div>
                                                </Link>
                                            </div>
                                        ))
                                    ) : (
                                        <div className="col-12 text-center">
                                            <p className="text-white">No categories found. Please check the API endpoint or database.</p>
                                        </div>
                                    )}
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