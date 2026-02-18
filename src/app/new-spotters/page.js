"use client"
import Footer from '@/components/Footer'
import Navbar from '@/components/Navbar'
import React, { useEffect, useState } from 'react'
import baseUrl from '@/Services/BaseUrl';
import Link from 'next/link';
import axios from 'axios';
import Cookies from 'js-cookie'
import Loader from '@/components/Loader';
import { useRouter } from 'next/navigation'
import { DotLottieReact } from '@lottiefiles/dotlottie-react';

function NewSpottersCategories() {
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
        console.log('Fetching New Spotters categories from:', `${baseUrl}/api/new-spotters/categories`);
        
        axios.post(`${baseUrl}/api/new-spotters/categories`, {}, {
            headers: {
                'Authorization': `Bearer ${cookies}`
            }
        }).then((response) => {
            console.log('New Spotters Categories API Response:', response);
            setCategories(response?.data?.data || [])
            setError(null);
            setLoading(false);
        }).catch((error) => {
            console.error('Error fetching New Spotters categories:', error);
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
                                                    New Spotters Categories
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
                                                    <Link href={`/new-spotters/chapters?id=${category.id}`}>
                                                        <div className="box" style={{ 
                                                            display: 'flex', 
                                                            alignItems: 'center', 
                                                            justifyContent: 'center', 
                                                            textAlign: 'center', 
                                                            borderRadius: '15px',
                                                            transition: 'all 0.3s ease',
                                                            cursor: 'pointer',
                                                            position: 'relative',
                                                            width: '100%',
                                                            aspectRatio: '1 / 1'
                                                        }}>
                                                            <DotLottieReact
                                                                src="/animantion/Blue circle 2.json"
                                                                loop
                                                                autoplay
                                                                style={{ 
                                                                    width: '100%', 
                                                                    height: '100%',
                                                                    filter: category.color ? `hue-rotate(${category.color})` : 'none'
                                                                }}
                                                            />
                                                            <h6 style={{ 
                                                                position: 'absolute',
                                                                top: '50%',
                                                                left: '50%',
                                                                transform: 'translate(-50%, -50%)',
                                                                color: 'white',
                                                                fontSize: '16px',
                                                                fontWeight: '600',
                                                                margin: '0',
                                                                width: '75%',
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
                        </div>
                    </section>

                    {/* Mobile View */}
                    <section className="Macaroni-sign-page-mobile p-0 d-block d-lg-none">
                        <div className="Macaroni-top">
                            <div className="container">
                                <div className="row">
                                    <div className="col-4"></div>
                                    <div className="col-4 text-center">
                                        <h6>New Spotters</h6>
                                    </div>
                                    <div className="col-4" />
                                </div>
                            </div>
                        </div>

                        <div className="Macaroni-middle" style={{ paddingTop: '20px' }}>
                            <div className="container">
                                <div className="row g-3">
                                    {categories && categories.length > 0 ? (
                                        categories?.map((category, index) => (
                                            <div className="col-6" key={category.id}>
                                                <Link href={`/new-spotters/chapters?id=${category.id}`}>
                                                    <div className="box" style={{ 
                                                        display: 'flex', 
                                                        flexDirection: 'column', 
                                                        alignItems: 'center', 
                                                        justifyContent: 'center', 
                                                        textAlign: 'center', 
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
                                                                width: '100%', 
                                                                height: '100%',
                                                                position: 'absolute',
                                                                filter: category.color ? `hue-rotate(${category.color})` : 'none'
                                                            }}
                                                        />
                                                        <h6 style={{ 
                                                            position: 'relative',
                                                            color: 'white',
                                                            fontSize: '14px',
                                                            fontWeight: '600',
                                                            margin: '0',
                                                            zIndex: 1
                                                        }}>{category.name}</h6>
                                                    </div>
                                                </Link>
                                            </div>
                                        ))
                                    ) : (
                                        <div className="col-12 text-center py-5">
                                            <p>No categories found</p>
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

export default NewSpottersCategories
