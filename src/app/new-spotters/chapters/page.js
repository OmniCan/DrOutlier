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

function NewSpottersChapters() {
    const [userid, setUser] = useState('')
    const [loading, setLoading] = useState(false);
    const [chapters, setChapters] = useState([])
    const [categoryName, setCategoryName] = useState('')
    const router = useRouter()
    const searchParams = useSearchParams()
    const categoryId = searchParams.get('id')

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
        if (!categoryId) {
            console.log('No category ID found');
            return;
        }

        const cookies = Cookies.get('user-token');
        
        console.log('Fetching New Spotters chapters for category:', categoryId);
        
        axios.post(`${baseUrl}/api/new-spotters/chapters`, {
            category_id: categoryId
        }, {
            headers: {
                'Authorization': `Bearer ${cookies}`
            }
        }).then((response) => {
            console.log('New Spotters Chapters API Response:', response);
            
            const chaptersList = response?.data?.data?.chapters || [];
            setCategoryName(response?.data?.data?.category_name || 'Chapters');
            setChapters(chaptersList);
            setLoading(false);
        }).catch((error) => {
            console.error('Error fetching New Spotters chapters:', error);
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
                                <div className="col-lg-12">
                                    <div className="row g-4">
                                        {chapters && chapters.length > 0 ? (
                                            chapters?.map((chapter, index) => (
                                                <div className="col-md-4 col-sm-6 col-6" key={chapter.id} style={{ padding: '18px', flex: '0 0 20%', maxWidth: '20%' }}>
                                                    <Link href={`/new-spotters/category?id=${chapter.id}&parentId=${categoryId}`}>
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
                                                                    filter: chapter.color ? `hue-rotate(${chapter.color})` : 'none'
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
                                                            }}>{chapter.name}</h6>
                                                        </div>
                                                    </Link>
                                                </div>
                                            ))
                                        ) : (
                                            <div className="col-12 text-center py-5">
                                                <p className="text-white">No chapters found in this category.</p>
                                                <Link href="/new-spotters" style={{
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
                                    <Link href='/new-spotters'>
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
                                <div className="row g-3">
                                    {chapters && chapters.length > 0 ? (
                                        chapters?.map((chapter, index) => (
                                            <div className="col-6" key={chapter.id}>
                                                <Link href={`/new-spotters/category?id=${chapter.id}`}>
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
                                                                filter: chapter.color ? `hue-rotate(${chapter.color})` : 'none'
                                                            }}
                                                        />
                                                        <h6 style={{ 
                                                            position: 'relative',
                                                            color: 'white',
                                                            fontSize: '14px',
                                                            fontWeight: '600',
                                                            margin: '0',
                                                            zIndex: 1
                                                        }}>{chapter.name}</h6>
                                                    </div>
                                                </Link>
                                            </div>
                                        ))
                                    ) : (
                                        <div className="col-12 text-center py-5">
                                            <p>No chapters found.</p>
                                            <Link href="/new-spotters" style={{
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

export default NewSpottersChapters
