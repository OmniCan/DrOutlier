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

function NewExamCasesCategory() {
    const [userid, setUser] = useState('')
    const [loading, setLoading] = useState(true);
    const [items, setItems] = useState([])
    const [categoryName, setCategoryName] = useState('')
    const [allChapters, setAllChapters] = useState([])
    const [currentChapterIndex, setCurrentChapterIndex] = useState(-1)
    const [parentCategoryId, setParentCategoryId] = useState(null)
    const [error, setError] = useState(null)
    const router = useRouter()
    const searchParams = useSearchParams()
    const categoryId = searchParams.get('id')
    const parentIdFromUrl = searchParams.get('parentId')
    
    useEffect(() => {
        // Consolidated effect - check auth and fetch all data
        const IsUserExist = Cookies.get('user-token')
        if (!IsUserExist) {
            router.push('/')
            return;
        }

        const user = Cookies.get('user-id');
        if (user) {
            setUser(user);
        }

        // Set parentCategoryId from URL if available
        if (parentIdFromUrl) {
            setParentCategoryId(parentIdFromUrl);
        }

        if (!categoryId) {
            setLoading(false);
            setError('Category ID not found');
            return;
        }

        const cookies = Cookies.get('user-token');
        
        // Fetch items for this chapter
        axios.post(`${baseUrl}/api/new-exam-cases/items-by-chapter`, {
            chapter_id: categoryId
        }, {
            headers: {
                'Authorization': `Bearer ${cookies}`
            }
        }).then((response) => {
            const itemsList = response?.data?.data?.items || [];
            const chapterName = response?.data?.data?.chapter_name || '';
            
            setItems(itemsList);
            setCategoryName(chapterName);
            setError(null);
            
            // Use parentId from URL first, then try to get from items
            let parentId = parentIdFromUrl;
            if (!parentId && itemsList.length > 0) {
                parentId = itemsList[0].category_id || 
                          itemsList[0].parent_category_id ||
                          itemsList[0].categories?.parent_id ||
                          itemsList[0].chapter?.category_id;
            }
            
            if (parentId) {
                setParentCategoryId(parentId);
                
                // Fetch all chapters using parent category
                axios.post(`${baseUrl}/api/new-exam-cases/chapters`, {
                    category_id: parentId
                }, {
                    headers: {
                        'Authorization': `Bearer ${cookies}`
                    }
                }).then((chaptersResponse) => {
                    const chapters = chaptersResponse?.data?.data?.chapters || [];
                    setAllChapters(chapters);
                    
                    // Find current chapter index
                    const currentIndex = chapters.findIndex(ch => ch.id.toString() === categoryId.toString());
                    setCurrentChapterIndex(currentIndex);
                }).catch((error) => {
                    console.error('Error fetching chapters:', error);
                });
            }
            
            setLoading(false);
        }).catch((error) => {
            console.error('Error fetching items:', error);
            setError(error.response?.data?.message || 'Failed to load items');
            setLoading(false);
        });
    }, [categoryId, parentIdFromUrl])

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
                                                <h2 className="text-white mb-0">
                                                    {categoryName}
                                                </h2>
                                            </div>
                                        </div>
                                        <div className="col-lg-6 text-end">
                                            <h6 className="text-white mb-0">Select Content</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="container">
                            <div className="row justify-content-center mt-4">
                                <div className="col-lg-12">
                                    <div style={{ display: 'flex', flexDirection: 'column', gap: '20px' }}>
                                        {items && items.length > 0 ? (
                                            items?.map((item, index) => (
                                                <Link key={item.id} href={`/new-exam-cases/view?id=${categoryId}&itemId=${item.id}${parentCategoryId ? `&parentId=${parentCategoryId}` : ''}#page${index + 1}`} style={{ textDecoration: 'none' }}>
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
                                                                    filter: `hue-rotate(${colors[index % colors.length]})`
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
                                                            }}>{item.title}</h6>
                                                        </div>
                                                        <div style={{ marginLeft: '20px', flexShrink: 0 }}>
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M9 18l6-6-6-6" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </Link>
                                            ))
                                        ) : (
                                            <div className="col-12 text-center py-5">
                                                <p className="text-white">No content found in this chapter.</p>
                                                <Link href="/new-exam-cases" style={{
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
                            
                            {/* Chapter Navigation Buttons */}
                            {allChapters.length > 0 && (
                                <div className="container mt-5 mb-4">
                                    <div className="row">
                                        <div className="col-12">
                                            <div style={{ 
                                                display: 'flex', 
                                                justifyContent: 'space-between',
                                                alignItems: 'center',
                                                padding: '0 20px'
                                            }}>
                                                {/* Previous Chapter Button */}
                                                {currentChapterIndex > 0 ? (
                                                    <Link href={`/new-exam-cases/category?id=${allChapters[currentChapterIndex - 1].id}${parentCategoryId ? `&parentId=${parentCategoryId}` : ''}`}>
                                                        <button style={{ 
                                                            padding: '15px 30px',
                                                            background: 'linear-gradient(150deg, #44A6C5 0%, #1E4FFD 100%)',
                                                            borderRadius: '12px',
                                                            border: 'none',
                                                            cursor: 'pointer',
                                                            color: 'white',
                                                            fontSize: '16px',
                                                            fontFamily: 'Poppins',
                                                            fontWeight: '600',
                                                            display: 'flex',
                                                            alignItems: 'center',
                                                            gap: '10px'
                                                        }}>
                                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M15 19l-7-7 7-7" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                                            </svg>
                                                            Previous Chapter
                                                        </button>
                                                    </Link>
                                                ) : (
                                                    <div></div>
                                                )}

                                                {/* Next Chapter Button */}
                                                {currentChapterIndex < allChapters.length - 1 && (
                                                    <Link href={`/new-exam-cases/category?id=${allChapters[currentChapterIndex + 1].id}${parentCategoryId ? `&parentId=${parentCategoryId}` : ''}`}>
                                                        <button style={{ 
                                                            padding: '15px 30px',
                                                            background: 'linear-gradient(150deg, #44A6C5 0%, #1E4FFD 100%)',
                                                            borderRadius: '12px',
                                                            border: 'none',
                                                            cursor: 'pointer',
                                                            color: 'white',
                                                            fontSize: '16px',
                                                            fontFamily: 'Poppins',
                                                            fontWeight: '600',
                                                            display: 'flex',
                                                            alignItems: 'center',
                                                            gap: '10px',
                                                            marginLeft: 'auto'
                                                        }}>
                                                            Next Chapter
                                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M9 5l7 7-7 7" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                                            </svg>
                                                        </button>
                                                    </Link>
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            )}
                        </div>
                    </section>

                    <section className="Macaroni-sign-page-mobile p-0 d-block d-lg-none">
                        <div className="Macaroni-top">
                            <div className="container">
                                <div className="row">
                                    <Link href='/new-exam-cases'>
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
                                    {items && items.length > 0 ? (
                                        items?.map((item, index) => (
                                            <div className="col-6" key={item.id}>
                                                <Link href={`/new-exam-cases/view?id=${categoryId}&itemId=${item.id}${parentCategoryId ? `&parentId=${parentCategoryId}` : ''}#page${index + 1}`}>
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
                                                                width: '140px', 
                                                                height: '140px',
                                                                filter: `hue-rotate(${colors[index % colors.length]})`
                                                            }}
                                                        />
                                                        <h6 style={{ 
                                                            marginTop: '10px', 
                                                            fontSize: '14px',
                                                            color: 'white',
                                                            fontWeight: '600',
                                                            marginBottom: '0',
                                                            textAlign: 'center',
                                                            wordBreak: 'break-word',
                                                            width: '100%'
                                                        }}>{item.title}</h6>
                                                    </div>
                                                </Link>
                                            </div>
                                        ))
                                    ) : (
                                        <div className="col-12 text-center py-5">
                                            <p className="text-white">No content found in this chapter.</p>
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

export default NewExamCasesCategory
