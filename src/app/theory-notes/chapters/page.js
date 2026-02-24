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

function TheoryNotesChapters() {
    const [userid, setUser] = useState('')
    const [loading, setLoading] = useState(true);
    const [chapters, setChapters] = useState([])
    const [categoryName, setCategoryName] = useState('')
    const [error, setError] = useState(null)
    const router = useRouter()
    const searchParams = useSearchParams()
    const categoryId = searchParams.get('id')

    useEffect(() => {
        // Consolidated effect - check auth and fetch data
        const IsUserExist = Cookies.get('user-token')
        if (!IsUserExist) {
            router.push('/')
            return;
        }

        const user = Cookies.get('user-id');
        if (user) {
            setUser(user);
        }

        if (!categoryId) {
            console.log('No category ID found');
            setLoading(false);
            setError('Category ID not found');
            return;
        }

        const cookies = Cookies.get('user-token');
        
        console.log('Fetching Theory Notes chapters for category:', categoryId);
        
        axios.post(`${baseUrl}/api/theory-notes/chapters`, {
            category_id: categoryId
        }, {
            headers: {
                'Authorization': `Bearer ${cookies}`
            }
        }).then((response) => {
            console.log('Theory Notes Chapters API Response:', response);
            
            const chaptersList = response?.data?.data?.chapters || [];
            setCategoryName(response?.data?.data?.category_name || 'Chapters');
            setChapters(chaptersList);
            setError(null);
            setLoading(false);
        }).catch((error) => {
            console.error('Error fetching Theory Notes chapters:', error);
            setError(error.response?.data?.message || 'Failed to load chapters');
            setLoading(false);
        });
    }, [categoryId])

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
                                                    {categoryName}
                                                </h2>
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
                                <div className="col-lg-10">
                                    <div className="list-wrapper" style={{ display: 'flex', flexDirection: 'column', gap: '15px' }}>
                                        {chapters && chapters.length > 0 ? (
                                            chapters?.map((chapter, index) => (
                                                <Link 
                                                    href={
                                                        chapter.first_item_id 
                                                            ? `/theory-notes/view?id=${chapter.id}&itemId=${chapter.first_item_id}&parentId=${categoryId}#page1`
                                                            : `/theory-notes/category?id=${chapter.id}&parentId=${categoryId}`
                                                    } 
                                                    key={chapter.id} 
                                                    style={{ textDecoration: 'none' }}
                                                >
                                                    <div className="list-item" style={{ 
                                                        display: 'flex', 
                                                        alignItems: 'center', 
                                                        padding: '20px 30px',
                                                        background: 'rgba(255, 255, 255, 0.05)',
                                                        backdropFilter: 'blur(10px)',
                                                        border: '1px solid rgba(255, 255, 255, 0.1)',
                                                        borderRadius: '15px',
                                                        transition: 'all 0.3s ease',
                                                        cursor: 'pointer',
                                                        position: 'relative',
                                                        overflow: 'hidden'
                                                    }}
                                                    onMouseEnter={(e) => {
                                                        e.currentTarget.style.background = 'rgba(255, 255, 255, 0.1)';
                                                        e.currentTarget.style.transform = 'translateX(10px)';
                                                        e.currentTarget.style.boxShadow = '0 8px 25px rgba(30, 79, 253, 0.3)';
                                                    }}
                                                    onMouseLeave={(e) => {
                                                        e.currentTarget.style.background = 'rgba(255, 255, 255, 0.05)';
                                                        e.currentTarget.style.transform = 'translateX(0)';
                                                        e.currentTarget.style.boxShadow = 'none';
                                                    }}>
                                                        <div style={{ 
                                                            position: 'relative',
                                                            width: '60px',
                                                            height: '60px',
                                                            flexShrink: 0,
                                                            marginRight: '20px'
                                                        }}>
                                                            <DotLottieReact
                                                                src="/animantion/Blue circle 2.json"
                                                                loop
                                                                autoplay
                                                                style={{ 
                                                                    width: '100%', 
                                                                    height: '100%',
                                                                    filter: chapter.color ? `hue-rotate(${chapter.color})` : 'none'
                                                                }}
                                                            />
                                                        </div>
                                                        <div style={{ flex: 1 }}>
                                                            <h6 style={{ 
                                                                color: 'white',
                                                                fontSize: '18px',
                                                                fontWeight: '600',
                                                                margin: '0',
                                                                lineHeight: '1.4'
                                                            }}>{chapter.name}</h6>
                                                        </div>
                                                        <div style={{ marginLeft: '20px', flexShrink: 0 }}>
                                                            <i className="fa-solid fa-chevron-right" style={{ color: 'white', fontSize: '18px', opacity: 0.7 }} />
                                                        </div>
                                                    </div>
                                                </Link>
                                            ))
                                        ) : (
                                            <div className="col-12 text-center py-5">
                                                <p className="text-white">No chapters found in this category.</p>
                                                <Link href="/theory-notes" style={{
                                                    padding: '12px 30px',
                                                    background: 'linear-gradient(150deg, #44A6C5 0%, #1E4FFD 100%)',
                                                    borderRadius: '12px',
                                                    color: 'white',
                                                    textDecoration: 'none',
                                                    fontSize: '16px',
                                                    fontFamily: 'Poppins',
                                                    fontWeight: '600',
                                                    display: 'inline-block'
                                                }}>
                                                    Back to Categories
                                                </Link>
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
                                    <Link href='/theory-notes'>
                                        <div className="col-4">
                                            <i className="fa-solid fa-chevron-left" />
                                        </div>
                                        <div className="col-4 text-center">
                                            <h6>{categoryName}</h6>
                                        </div>
                                        <div className="col-4" />
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <div className="Macaroni-middle" style={{ paddingTop: '20px' }}>
                            <div className="container">
                                <div className="list-wrapper" style={{ display: 'flex', flexDirection: 'column', gap: '12px' }}>
                                    {chapters && chapters.length > 0 ? (
                                        chapters?.map((chapter, index) => (
                                            <Link 
                                                href={
                                                    chapter.first_item_id 
                                                        ? `/theory-notes/view?id=${chapter.id}&itemId=${chapter.first_item_id}&parentId=${categoryId}#page1`
                                                        : `/theory-notes/category?id=${chapter.id}&parentId=${categoryId}`
                                                } 
                                                key={chapter.id} 
                                                style={{ textDecoration: 'none' }}
                                            >
                                                <div className="list-item" style={{ 
                                                    display: 'flex', 
                                                    alignItems: 'center', 
                                                    padding: '15px 20px',
                                                    background: 'rgba(255, 255, 255, 0.05)',
                                                    backdropFilter: 'blur(10px)',
                                                    border: '1px solid rgba(255, 255, 255, 0.1)',
                                                    borderRadius: '12px',
                                                    transition: 'all 0.3s ease',
                                                    position: 'relative'
                                                }}>
                                                    <div style={{ 
                                                        position: 'relative',
                                                        width: '45px',
                                                        height: '45px',
                                                        flexShrink: 0,
                                                        marginRight: '15px'
                                                    }}>
                                                        <DotLottieReact
                                                            src="/animantion/Blue circle 2.json"
                                                            loop
                                                            autoplay
                                                            style={{ 
                                                                width: '100%', 
                                                                height: '100%',
                                                                filter: chapter.color ? `hue-rotate(${chapter.color})` : 'none'
                                                            }}
                                                        />
                                                    </div>
                                                    <div style={{ flex: 1 }}>
                                                        <h6 style={{ 
                                                            color: 'white',
                                                            fontSize: '15px',
                                                            fontWeight: '600',
                                                            margin: '0',
                                                            lineHeight: '1.3'
                                                        }}>{chapter.name}</h6>
                                                    </div>
                                                    <div style={{ marginLeft: '10px', flexShrink: 0 }}>
                                                        <i className="fa-solid fa-chevron-right" style={{ color: 'white', fontSize: '14px', opacity: 0.7 }} />
                                                    </div>
                                                </div>
                                            </Link>
                                        ))
                                    ) : (
                                        <div className="col-12 text-center py-5">
                                            <p>No chapters found.</p>
                                            <Link href="/theory-notes" style={{
                                                padding: '10px 20px',
                                                background: '#1E4FFD',
                                                borderRadius: '8px',
                                                color: 'white',
                                                textDecoration: 'none',
                                                display: 'inline-block',
                                                marginTop: '10px'
                                            }}>
                                                Back to Categories
                                            </Link>
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

export default TheoryNotesChapters
