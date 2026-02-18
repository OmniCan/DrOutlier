"use client"
import Footer from '@/components/Footer'
import Navbar from '@/components/Navbar'
import React, { useEffect, useState } from 'react'
import baseUrl from '@/Services/BaseUrl';
import ApiCache from '@/Services/ApiCache';
import Link from 'next/link';
import axios from 'axios';
import Cookies from 'js-cookie'
import Loader from '@/components/Loader';
import { useRouter } from 'next/navigation'
import { DotLottieReact } from '@lottiefiles/dotlottie-react';

function page() {
    const [userid, setUser] = useState('')
    const [loading, setLoading] = useState(true);
    const [categories, setCategories] = useState([])
    const [error, setError] = useState(null)
    const router = useRouter()

    useEffect(() => {
        // Check auth and fetch data in one effect to avoid race conditions
        const IsUserExist = Cookies.get('user-token')
        if (!IsUserExist) {
            router.push('/')
            return;
        }

        const user = Cookies.get('user-id');
        if (!user) {
            setLoading(false);
            setError('User not found');
            return;
        }

        setUser(user);
        
        // Check cache first
        const cacheKey = `osce-categories-${user}`;
        const cachedData = ApiCache.get(cacheKey);
        
        if (cachedData) {
            console.log('Using cached OSCE categories');
            setCategories(cachedData.categories);
            setError(null);
            setLoading(false);
            return;
        }
        
        // Fetch categories
        const cookies = Cookies.get('user-token');
        
        axios.post(`${baseUrl}/api/osce/list`, {}, {
            headers: {
                'Authorization': `Bearer ${cookies}`
            }
        }).then((response) => {
            console.log('OSCE API Full Response:', response?.data);
            
            // Backend returns paginated data: { status, data: { current_page, data: [...], last_page, etc } }
            const paginatedData = response?.data?.data;
            let categoriesList = [];
            
            if (paginatedData) {
                // Check if it's a paginated response (has 'data' property with array)
                if (paginatedData.data && Array.isArray(paginatedData.data)) {
                    categoriesList = paginatedData.data;
                } else if (Array.isArray(paginatedData)) {
                    categoriesList = paginatedData;
                }
            }
            
            console.log('Categories List (all):', categoriesList);
            console.log('Categories count:', categoriesList.length);
            
            // Filter for parent categories only (parent_id = 0 or null) if needed
            if (Array.isArray(categoriesList) && categoriesList.length > 0) {
                // The API should already return only parent categories, but filter as safety measure
                const parentCategories = categoriesList.filter(cat => {
                    return cat.parent_id === 0 || cat.parent_id === '0' || cat.parent_id === null || !cat.parent_id;
                });
                console.log('Filtered Parent Categories:', parentCategories);
                const finalCategories = parentCategories.length > 0 ? parentCategories : categoriesList;
                setCategories(finalCategories);
                
                // Cache the result
                ApiCache.set(cacheKey, { categories: finalCategories });
                setError(null);
            } else {
                console.log('No categories found or categoriesList is not an array');
                setCategories([]);
                setError('No categories found');
            }
            
            setLoading(false);
        }).catch((error) => {
            console.error('Error fetching OSCE categories:', error);
            
            let errorMessage = 'Failed to load categories. Please try again.';
            
            if (error.response?.status === 429) {
                errorMessage = 'Too many requests. Please wait a moment and try again.';
            } else if (error.response?.status === 403) {
                errorMessage = 'Access denied. Please check your subscription or permissions.';
            } else if (error.response?.data?.message) {
                errorMessage = error.response.data.message;
            }
            
            setError(errorMessage);
            setCategories([]);
            setLoading(false);
        });
    }, [])

    // Color variations for categories
    const colors = ['0deg', '120deg', '240deg', '60deg', '180deg', '300deg'];

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
                                                <h2 className="text-white mb-0">OSCE</h2>
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
                                                <div className="col-md-4 col-sm-6 col-6" key={category.id} style={{ padding: '12px', flex: '0 0 16.666%', maxWidth: '16.666%' }}>
                                                    <Link href={`/osce/chapters?id=${category.id}`}>
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
                                                                    filter: `hue-rotate(${colors[index % colors.length]})`
                                                                }}
                                                            />
                                                            <h5 style={{
                                                                position: 'absolute',
                                                                top: '50%',
                                                                left: '50%',
                                                                transform: 'translate(-50%, -50%)',
                                                                color: 'white',
                                                                fontSize: '16px',
                                                                fontWeight: '600',
                                                                width: '80%',
                                                                margin: 0,
                                                                lineHeight: '1.2'
                                                            }}>
                                                                {category.name}
                                                            </h5>
                                                        </div>
                                                    </Link>
                                                </div>
                                            ))
                                        ) : (
                                            <div className="col-12 text-center py-5">
                                                {error ? (
                                                    <div>
                                                        <p className="text-white mb-3">{error}</p>
                                                        <button 
                                                            onClick={() => window.location.reload()} 
                                                            className="btn btn-primary"
                                                            style={{
                                                                background: 'linear-gradient(92.48deg, #44A6C5 3.13%, #1E4FFD 100%)',
                                                                border: 'none',
                                                                padding: '10px 24px',
                                                                borderRadius: '12px'
                                                            }}
                                                        >
                                                            Retry
                                                        </button>
                                                    </div>
                                                ) : (
                                                    <p className="text-white">No OSCE categories found.</p>
                                                )}
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section className="Macaroni-sign-page-mobile p-0 d-block d-lg-none">
                        <div className="Macaroni-top">
                            <div className="container">
                                <div className="row">
                                    <div className="col-lg-12">
                                        <h2 className="text-white">OSCE</h2>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="Macaroni-middle" style={{ paddingTop: '20px' }}>
                            <div className="container">
                                <div className="row">
                                    {categories && categories.length > 0 ? (
                                        categories?.map((category, index) => (
                                            <div className="col-6 col-sm-6 mb-3" key={category.id}>
                                                <Link href={`/osce/chapters?id=${category.id}`}>
                                                    <div style={{
                                                        display: 'flex',
                                                        flexDirection: 'column',
                                                        alignItems: 'center',
                                                        justifyContent: 'center',
                                                        textAlign: 'center',
                                                        minHeight: '180px'
                                                    }}>
                                                        <div style={{ position: 'relative', width: '100%' }}>
                                                            <DotLottieReact
                                                                src="/animantion/Blue circle 2.json"
                                                                loop
                                                                autoplay
                                                                style={{ 
                                                                    width: '140px',
                                                                    height: '140px',
                                                                    filter: `hue-rotate(${colors[index % colors.length]})`
                                                                }}
                                                            />
                                                            <h6 style={{
                                                                marginTop: '10px',
                                                                color: 'white',
                                                                fontSize: '14px',
                                                                marginBottom: '0',
                                                                fontWeight: '600',
                                                                width: '100%'
                                                            }}>
                                                                {category.name}
                                                            </h6>
                                                        </div>
                                                    </div>
                                                </Link>
                                            </div>
                                        ))
                                    ) : (
                                        <div className="col-12 text-center py-5">
                                            {error ? (
                                                <div>
                                                    <p className="text-white mb-3">{error}</p>
                                                    <button 
                                                        onClick={() => window.location.reload()} 
                                                        className="btn btn-primary"
                                                        style={{
                                                            background: 'linear-gradient(92.48deg, #44A6C5 3.13%, #1E4FFD 100%)',
                                                            border: 'none',
                                                            padding: '10px 24px',
                                                            borderRadius: '12px'
                                                        }}
                                                    >
                                                        Retry
                                                    </button>
                                                </div>
                                            ) : (
                                                <p className="text-white">No OSCE categories found.</p>
                                            )}
                                        </div>
                                    )}
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