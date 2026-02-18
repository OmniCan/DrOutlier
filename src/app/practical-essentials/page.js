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
    const [loading, setLoading] = useState(true);
    const [categories, setCategories] = useState([])
    const [error, setError] = useState(null)
    const router = useRouter()

    useEffect(() => {
        // Consolidated effect - check auth and fetch data in one go
        const IsUserExist = Cookies.get('user-token')
        if (!IsUserExist) {
            router.push('/')
            return;
        }

        const user = Cookies.get('user-id');
        if (user) {
            setUser(user);
        }

        const cookies = Cookies.get('user-token');
        console.log('Fetching Practical Essentials categories from:', `${baseUrl}/api/basic-category/list`);
        
        axios.post(`${baseUrl}/api/basic-category/list`, {}, {
            headers: {
                'Authorization': `Bearer ${cookies}`
            }
        }).then((response) => {
            console.log('Practical Essentials Categories API Response:', response);
            console.log('Categories Data:', response?.data);
            
            // The API returns datalist directly
            const allCategories = response?.data?.data?.datalist || [];
            console.log('Extracted categories:', allCategories);
            
            setCategories(allCategories);
            setError(null);
            setLoading(false);
        }).catch((error) => {
            console.error('Error fetching Practical Essentials categories:', error);
            console.error('Error response:', error.response);
            setError(error.response?.data?.message || 'Failed to load categories');
            setLoading(false);
        })
    }, [])

    return (
        <>
            <Navbar />
            {!loading ? (
                <div className="main-wrapper">
                    <section className="Macaroni-Sign-page pt-0 d-none d-lg-block">
                        <div className="container-fluid px-0">
                            <div className="macaroni-top">
                                <div className="container">
                                    <div className="row align-items-center">
                                        <div className="col-lg-6">
                                            <div className="content">
                                                <h2 className="text-white mb-0">
                                                    Practical Essentials
                                                </h2>
                                            </div>
                                        </div>
                                        <div className="col-lg-6 text-end">
                                            <h6 className="text-white mb-0">Select Category</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="container">
                            <div className="row justify-content-center mt-4">
                                <div className="col-lg-12">
                                    <div className="row g-4">
                                        {categories && categories.length > 0 ? (
                                            categories?.map((category, index) => (
                                                <div className="col-md-4 col-sm-6 col-6" key={category.id} style={{ padding: '18px', flex: '0 0 20%', maxWidth: '20%' }}>
                                                    <Link href={`/practical-essentials/chapters?id=${category.id}`}>
                                                        <div className="box" style={{ 
                                                            display: 'flex', 
                                                            flexDirection: 'column', 
                                                            alignItems: 'center', 
                                                            justifyContent: 'center',
                                                            position: 'relative',
                                                            cursor: 'pointer',
                                                            transition: 'all 0.3s ease'
                                                        }}
                                                        onMouseEnter={(e) => {
                                                            e.currentTarget.style.transform = 'translateY(-10px)';
                                                        }}
                                                        onMouseLeave={(e) => {
                                                            e.currentTarget.style.transform = 'translateY(0)';
                                                        }}>
                                                            <div style={{ 
                                                                width: '100%',
                                                                aspectRatio: '1',
                                                                position: 'relative',
                                                                borderRadius: '20px',
                                                                overflow: 'hidden',
                                                                display: 'flex',
                                                                alignItems: 'center',
                                                                justifyContent: 'center'
                                                            }}>
                                                                <div style={{
                                                                    width: '100%',
                                                                    height: '100%',
                                                                    position: 'absolute',
                                                                    top: 0,
                                                                    left: 0,
                                                                    filter: `hue-rotate(${index * 60}deg)`
                                                                }}>
                                                                    <DotLottieReact
                                                                        src="/animantion/Blue circle 2.json"
                                                                        loop
                                                                        autoplay
                                                                        style={{ width: '100%', height: '100%' }}
                                                                    />
                                                                </div>
                                                                
                                                                <div style={{
                                                                    position: 'absolute',
                                                                    top: '50%',
                                                                    left: '50%',
                                                                    transform: 'translate(-50%, -50%)',
                                                                    zIndex: 1,
                                                                    textAlign: 'center',
                                                                    width: '80%',
                                                                    color: 'white',
                                                                    fontSize: '18px',
                                                                    fontFamily: 'Poppins',
                                                                    fontWeight: 'normal',
                                                                    lineHeight: '1.2'
                                                                }}>
                                                                    {category.name}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </Link>
                                                </div>
                                            ))
                                        ) : (
                                            <div className="col-12 text-center py-5">
                                                <p className="text-white">No Practical Essentials categories found</p>
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    {/* Mobile View */}
                    <section className="Macaroni-Sign-page pt-0 d-block d-lg-none">
                        <div className="container-fluid px-0">
                            <div className="macaroni-top">
                                <div className="container">
                                    <div className="row align-items-center">
                                        <div className="col-12 text-center">
                                            <div className="content">
                                                <h2 className="text-white mb-2">
                                                    Practical Essentials
                                                </h2>
                                                <h6 className="text-white mb-0">Select Category</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="Macaroni-middle" style={{ paddingTop: '20px' }}>
                            <div className="container">
                                <div className="row g-3">
                                    {categories && categories.length > 0 ? (
                                        categories?.map((category, index) => (
                                            <div className="col-6" key={category.id}>
                                                <Link href={`/practical-essentials/chapters?id=${category.id}`}>
                                                    <div style={{
                                                        display: 'flex',
                                                        alignItems: 'center',
                                                        justifyContent: 'center',
                                                        flexDirection: 'column',
                                                        padding: '15px',
                                                        borderRadius: '15px',
                                                        position: 'relative',
                                                        minHeight: '180px'
                                                    }}>
                                                        <DotLottieReact
                                                            src="/animantion/Blue circle 2.json"
                                                            loop
                                                            autoplay
                                                            style={{ 
                                                                width: '140px', 
                                                                height: '140px',
                                                                filter: `hue-rotate(${index * 60}deg)`
                                                            }}
                                                        />
                                                        <h6 style={{ 
                                                            position: 'absolute',
                                                            top: '50%',
                                                            left: '50%',
                                                            transform: 'translate(-50%, -50%)',
                                                            color: 'white',
                                                            fontSize: '14px',
                                                            fontWeight: 'normal',
                                                            margin: '0',
                                                            width: '75%',
                                                            textAlign: 'center',
                                                            wordWrap: 'break-word',
                                                            lineHeight: '1.3'
                                                        }}>{category.name}</h6>
                                                    </div>
                                                </Link>
                                            </div>
                                        ))
                                    ) : (
                                        <div className="col-12 text-center py-5">
                                            <p className="text-white">No categories found</p>
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>
                    </section>

                    <Footer />
                </div>
            ) : (
                <Loader />
            )}
        </>
    )
}

export default page
