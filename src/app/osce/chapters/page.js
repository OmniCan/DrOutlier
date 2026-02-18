"use client"
import Footer from '@/components/Footer'
import Navbar from '@/components/Navbar'
import React, { useEffect, useState } from 'react'
import baseUrl from '@/Services/BaseUrl';
import Link from 'next/link';
import axios from 'axios';
import Cookies from 'js-cookie'
import Loader from '@/components/Loader';
import { useRouter, useSearchParams } from 'next/navigation'
import { DotLottieReact } from '@lottiefiles/dotlottie-react';

function page() {
    const [userid, setUser] = useState('')
    const [loading, setLoading] = useState(true);
    const [chapters, setChapters] = useState([])
    const [categoryName, setCategoryName] = useState('')
    const [error, setError] = useState(null)
    const router = useRouter()
    const searchParams = useSearchParams()

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

        const categoryId = searchParams.get('id');
        if (!categoryId) {
            setLoading(false);
            setError('Category ID not found');
            return;
        }

        const cookies = Cookies.get('user-token');
        
        axios.post(`${baseUrl}/api/osce/list`, {}, {
            headers: {
                'Authorization': `Bearer ${cookies}`
            }
        }).then((response) => {
            console.log('OSCE Chapters - Full Response:', response?.data);
            
            // Handle paginated response structure
            let categoriesList = response?.data?.data?.data || 
                                response?.data?.data || 
                                [];
            
            console.log('Categories List:', categoriesList);
            console.log('Looking for category ID:', categoryId);
            
            // Find the selected category by ID
            const selectedCategory = Array.isArray(categoriesList) 
                ? categoriesList.find(cat => cat.id.toString() === categoryId.toString())
                : null;
            
            console.log('Selected Category:', selectedCategory);
            
            if (selectedCategory) {
                setCategoryName(selectedCategory.name);
                // Get child categories
                const childCategories = selectedCategory.child || [];
                console.log('Child Categories:', childCategories);
                console.log('Number of chapters:', childCategories.length);
                setChapters(childCategories);
                setError(null);
            } else {
                console.error('Category not found for ID:', categoryId);
                setError('Category not found');
                setChapters([]);
            }
            setLoading(false);
        }).catch((error) => {
            console.error('Error fetching OSCE chapters:', error);
            setError(error.response?.data?.message || 'Failed to load chapters. Please try again.');
            setChapters([]);
            setLoading(false);
        });
    }, [searchParams])

    // Color variations for chapters
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
                                                <h2 className="text-white mb-0">{categoryName || 'OSCE'}</h2>
                                            </div>
                                        </div>
                                        <div className="col-lg-6 text-end">
                                            <h6 className="text-white mb-0">Select Chapter</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="container">
                            <div className="row justify-content-center mt-4">
                                <div className="col-lg-12">
                                    <div className="row g-4">
                                        {chapters && chapters.length > 0 ? (
                                            chapters?.map((chapter, index) => (
                                                <div className="col-md-4 col-sm-6 col-6" key={chapter.id} style={{ padding: '12px', flex: '0 0 16.666%', maxWidth: '16.666%' }}>
                                                    <Link href={`/osce/category?id=${chapter.id}`}>
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
                                                                {chapter.name}
                                                            </h5>
                                                        </div>
                                                    </Link>
                                                </div>
                                            ))
                                        ) : (
                                            <div className="col-12 text-center py-5">
                                                <p className="text-white">No chapters found.</p>
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
                                        <h2 className="text-white">{categoryName || 'OSCE'}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="Macaroni-middle" style={{ paddingTop: '20px' }}>
                            <div className="container">
                                <div className="row">
                                    {chapters && chapters.length > 0 ? (
                                        chapters?.map((chapter, index) => (
                                            <div className="col-6 col-sm-6 mb-3" key={chapter.id}>
                                                <Link href={`/osce/category?id=${chapter.id}`}>
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
                                                                {chapter.name}
                                                            </h6>
                                                        </div>
                                                    </div>
                                                </Link>
                                            </div>
                                        ))
                                    ) : (
                                        <div className="col-12 text-center py-5">
                                            <p className="text-white">No chapters found.</p>
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
