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
    const [loading, setLoading] = useState(false);
    const [spotters, setSpotters] = useState([])
    const [categoryName, setCategoryName] = useState('')
    const [bookmarkedSpotters, setBookmarkedSpotters] = useState({});
    const [allChapters, setAllChapters] = useState([])
    const [currentChapterIndex, setCurrentChapterIndex] = useState(-1)
    const [parentCategoryId, setParentCategoryId] = useState(null)
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
        if (!categoryId || !userid) {
            console.log('No category ID or user ID found');
            return;
        }

        const cookies = Cookies.get('user-token');
        
        console.log('Fetching spotters for category:', categoryId);
        
        // Fetch spotters list and bookmarked spotters in parallel
        Promise.all([
            axios.post(`${baseUrl}/api/spotters/list-by-category`, {
                category_id: categoryId
            }, {
                headers: {
                    'Authorization': `Bearer ${cookies}`
                }
            }),
            axios.post(`${baseUrl}/api/spotters/get-bookmark?user_id=${userid}`, {}, {
                headers: {
                    'Authorization': `Bearer ${cookies}`
                }
            })
        ]).then(([categoryResponse, bookmarkedResponse]) => {
            console.log('Spotters API Response:', categoryResponse);
            console.log('Bookmarked API Response:', bookmarkedResponse);
            
            const spottersList = categoryResponse?.data?.data?.datalist || [];
            const bookmarkedList = bookmarkedResponse?.data?.data?.list?.data || [];
            const category = categoryResponse?.data?.data?.category;
            
            setSpotters(spottersList);
            setCategoryName(categoryResponse?.data?.data?.category_name || 'Spotters');
            
            // If category has parent_id, fetch all sibling chapters
            if (category && category.parent_id && category.parent_id !== 0) {
                setParentCategoryId(category.parent_id);
                
                axios.post(`${baseUrl}/api/spotters/chapters`, {
                    category_id: category.parent_id
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
            
            // Create a Set of bookmarked spotter IDs for quick lookup
            const bookmarkedIds = new Set(bookmarkedList.map(spotter => spotter.id));
            
            // Initialize bookmark status for each spotter
            const bookmarkStatus = {};
            spottersList.forEach(spotter => {
                bookmarkStatus[spotter.id] = bookmarkedIds.has(spotter.id);
            });
            
            console.log('Initial bookmark status:', bookmarkStatus);
            setBookmarkedSpotters(bookmarkStatus);
            
            setLoading(false);
        }).catch((error) => {
            console.error('Error fetching data:', error);
            
            // Fallback: try just the category list if bookmarked fetch fails
            axios.post(`${baseUrl}/api/spotters/list-by-category`, {
                category_id: categoryId
            }, {
                headers: {
                    'Authorization': `Bearer ${cookies}`
                }
            }).then((e) => {
                const spottersList = e?.data?.data?.datalist || [];
                setSpotters(spottersList);
                setCategoryName(e?.data?.data?.category_name || 'Spotters');
                
                // Initialize with empty bookmark status
                const bookmarkStatus = {};
                spottersList.forEach(spotter => {
                    bookmarkStatus[spotter.id] = false;
                });
                setBookmarkedSpotters(bookmarkStatus);
                
                setLoading(false);
            }).catch((err) => {
                console.error('Error fetching spotters:', err);
                setLoading(false);
            });
        });
    }, [categoryId, userid])


    // Generate color variations for spotters (similar to categories)
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
                                            <h6 className="text-white mb-0">Select Spotter</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="container">
                            <div className="row justify-content-center mt-4">
                                <div className="col-lg-12">
                                    <div className="row g-4">
                                        {spotters && spotters.length > 0 ? (
                                            spotters?.map((spotter, index) => (
                                                <div className="col-md-4 col-sm-6 col-6" key={spotter.id} style={{ padding: '12px', flex: '0 0 16.666%', maxWidth: '16.666%' }}>
                                                    <Link href={`/spotters/view?id=${categoryId}&spotterId=${spotter.id}#page${index + 1}`}>
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
                                                            <h6 style={{ 
                                                                position: 'absolute',
                                                                top: '50%',
                                                                left: '50%',
                                                                transform: 'translate(-50%, -50%)',
                                                                color: 'white',
                                                                fontSize: '12px',
                                                                fontWeight: '600',
                                                                margin: '0',
                                                                width: '80%',
                                                                wordWrap: 'break-word',
                                                                lineHeight: '1.2'
                                                            }}>{spotter.title}</h6>
                                                            
                                                            {/* Bookmark Icon */}
                                                            {bookmarkedSpotters[spotter.id] && (
                                                                <div style={{
                                                                    position: 'absolute',
                                                                    top: '8px',
                                                                    right: '8px',
                                                                    background: 'white',
                                                                    borderRadius: '8px',
                                                                    padding: '4px',
                                                                    display: 'flex',
                                                                    alignItems: 'center',
                                                                    justifyContent: 'center',
                                                                    boxShadow: '0 2px 8px rgba(0,0,0,0.2)'
                                                                }}>
                                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path 
                                                                            d="M19 21L12 16L5 21V5C5 4.46957 5.21071 3.96086 5.58579 3.58579C5.96086 3.21071 6.46957 3 7 3H17C17.5304 3 18.0391 3.21071 18.4142 3.58579C18.7893 3.96086 19 4.46957 19 5V21Z" 
                                                                            fill="url(#bookmarkGradient)"
                                                                        />
                                                                        <defs>
                                                                            <linearGradient id="bookmarkGradient" x1="5" y1="3" x2="19" y2="21" gradientUnits="userSpaceOnUse">
                                                                                <stop offset="0%" stopColor="#44A6C5"/>
                                                                                <stop offset="100%" stopColor="#1E4FFD"/>
                                                                            </linearGradient>
                                                                        </defs>
                                                                    </svg>
                                                                </div>
                                                            )}
                                                        </div>
                                                    </Link>
                                                </div>
                                            ))
                                        ) : (
                                            <div className="col-12 text-center py-5">
                                                <p className="text-white">No spotters found in this category.</p>
                                                <Link href="/spotters" style={{
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
                                                    <Link href={`/spotters/category?id=${allChapters[currentChapterIndex - 1].id}`}>
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
                                                    <Link href={`/spotters/category?id=${allChapters[currentChapterIndex + 1].id}`}>
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
                                    <Link href='/spotters'>
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
                                    {spotters && spotters.length > 0 ? (
                                        spotters?.map((spotter, index) => (
                                            <div className="col-6" key={spotter.id}>
                                                <Link href={`/spotters/view?id=${categoryId}&spotterId=${spotter.id}#page${index + 1}`}>
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
                                                        }}>{spotter.title}</h6>
                                                        
                                                        {/* Bookmark Icon */}
                                                        {bookmarkedSpotters[spotter.id] && (
                                                            <div style={{
                                                                position: 'absolute',
                                                                top: '10px',
                                                                right: '10px',
                                                                background: 'white',
                                                                borderRadius: '8px',
                                                                padding: '4px',
                                                                display: 'flex',
                                                                alignItems: 'center',
                                                                justifyContent: 'center',
                                                                boxShadow: '0 2px 8px rgba(0,0,0,0.2)'
                                                            }}>
                                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path 
                                                                        d="M19 21L12 16L5 21V5C5 4.46957 5.21071 3.96086 5.58579 3.58579C5.96086 3.21071 6.46957 3 7 3H17C17.5304 3 18.0391 3.21071 18.4142 3.58579C18.7893 3.96086 19 4.46957 19 5V21Z" 
                                                                        fill="url(#bookmarkGradientMobile)"
                                                                    />
                                                                    <defs>
                                                                        <linearGradient id="bookmarkGradientMobile" x1="5" y1="3" x2="19" y2="21" gradientUnits="userSpaceOnUse">
                                                                            <stop offset="0%" stopColor="#44A6C5"/>
                                                                            <stop offset="100%" stopColor="#1E4FFD"/>
                                                                        </linearGradient>
                                                                    </defs>
                                                                </svg>
                                                            </div>
                                                        )}
                                                    </div>
                                                </Link>
                                            </div>
                                        ))
                                    ) : (
                                        <div className="col-12 text-center py-5">
                                            <p className="text-white">No spotters found in this category.</p>
                                        </div>
                                    )}
                                </div>
                            </div>
                            
                            {/* Chapter Navigation Buttons for Mobile */}
                            {allChapters.length > 0 && (
                                <div className="container mt-4 mb-4">
                                    <div className="row">
                                        <div className="col-12">
                                            <div style={{ 
                                                display: 'flex', 
                                                justifyContent: 'space-between',
                                                alignItems: 'center',
                                                padding: '0 10px'
                                            }}>
                                                {/* Previous Chapter Button */}
                                                {currentChapterIndex > 0 ? (
                                                    <Link href={`/spotters/category?id=${allChapters[currentChapterIndex - 1].id}`}>
                                                        <button style={{ 
                                                            padding: '12px 20px',
                                                            background: 'linear-gradient(150deg, #44A6C5 0%, #1E4FFD 100%)',
                                                            borderRadius: '12px',
                                                            border: 'none',
                                                            cursor: 'pointer',
                                                            color: 'white',
                                                            fontSize: '14px',
                                                            fontFamily: 'Poppins',
                                                            fontWeight: '600',
                                                            display: 'flex',
                                                            alignItems: 'center',
                                                            gap: '8px'
                                                        }}>
                                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M15 19l-7-7 7-7" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                                            </svg>
                                                            Previous
                                                        </button>
                                                    </Link>
                                                ) : (
                                                    <div></div>
                                                )}

                                                {/* Next Chapter Button */}
                                                {currentChapterIndex < allChapters.length - 1 && (
                                                    <Link href={`/spotters/category?id=${allChapters[currentChapterIndex + 1].id}`}>
                                                        <button style={{ 
                                                            padding: '12px 20px',
                                                            background: 'linear-gradient(150deg, #44A6C5 0%, #1E4FFD 100%)',
                                                            borderRadius: '12px',
                                                            border: 'none',
                                                            cursor: 'pointer',
                                                            color: 'white',
                                                            fontSize: '14px',
                                                            fontFamily: 'Poppins',
                                                            fontWeight: '600',
                                                            display: 'flex',
                                                            alignItems: 'center',
                                                            gap: '8px',
                                                            marginLeft: 'auto'
                                                        }}>
                                                            Next
                                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                </div>
            ) : (
                <Loader />
            )}
            <Footer />
        </>
    )
}

export default page
