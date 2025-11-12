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
import { useRouter, useSearchParams } from 'next/navigation'

function page() {
    const [userid, setUser] = useState('')
    const [loading, setLoading] = useState(false);
    const [munchieDetails, setMunchieDetails] = useState([])
    const [categoryName, setCategoryName] = useState('')
    const [bookmarkedMunchies, setBookmarkedMunchies] = useState({});
    const [allChapters, setAllChapters] = useState([])
    const [currentChapterIndex, setCurrentChapterIndex] = useState(-1)
    const [parentCategoryId, setParentCategoryId] = useState(null)
    const router = useRouter()
    const searchParams = useSearchParams()
    const categoryId = searchParams.get('id')
    const munchieId = searchParams.get('munchieId')

    useEffect(() => {
        setLoading(true);
        const IsUserExist = Cookies.get('user-token')
        if (!IsUserExist) {
            router.push('/')
        }
    }, []);

    const [currentPage, setCurrentPage] = useState(1);
    const itemsPerPage = 1;

    const totalPages = Math?.ceil(munchieDetails?.length / itemsPerPage);

    const currentData = munchieDetails?.slice(
        (currentPage - 1) * itemsPerPage,
        currentPage * itemsPerPage
    );

    const handlePageChange = (page) => {
        if (page > 0 && page <= totalPages) {
            setCurrentPage(page);
        }
        window.location.hash = `page${page}`;
    };

    useEffect(() => {
        const hash = window.location.hash;
        if (hash.startsWith("#page")) {
            const page = parseInt(hash.replace("#page", ""), 10);
            if (!isNaN(page)) {
                setCurrentPage(page);
            }
        }
    }, []);

    useEffect(() => {
        const user = Cookies.get('user-id');
        setUser(user)
    }, [])

    const saveMunchie = (munchieId) => {
        const cookies = Cookies.get('user-token');
        const formData = new FormData();
        formData.append('user_id', userid);
        formData.append('munchie_id', munchieId);

        axios.post(`${baseUrl}/api/category-munchie/change-munchie-bookmark-status`, formData, {
            headers: {
                'Authorization': `Bearer ${cookies}`,
            }
        })
            .then((response) => {
                toast.success(response.data.message);
                
                setBookmarkedMunchies(prev => ({
                    ...prev,
                    [munchieId]: !prev[munchieId]
                }));
            })
            .catch((error) => {
                console.error('There was an error!', error);
                toast.error('Failed to update bookmark status');
            });
    }

    // Helper function to fix image URLs in content
    const fixImageUrls = (content) => {
        if (!content) return content;
        
        // Replace old domain URLs with the correct baseUrl
        return content
            .replace(/https?:\/\/lab2\.invoidea\.in\/outlier\//g, `${baseUrl}/`)
            .replace(/https?:\/\/admin\.droutlier\.com\//g, `${baseUrl}/`);
    }

    useEffect(() => {
        if (!categoryId || !userid) {
            return;
        }

        const cookies = Cookies.get('user-token');
        
        // Fetch munchies list
        axios.post(`${baseUrl}/api/category-munchie/category-munchie?category=${categoryId}`, {}, {
            headers: {
                'Authorization': `Bearer ${cookies}`
            }
        }).then((categoryResponse) => {
            const munchiesList = categoryResponse?.data?.data?.notes?.data || [];
            
            // Fix image URLs in all munchie content
            const fixedMunchiesList = munchiesList.map(munchie => ({
                ...munchie,
                content: fixImageUrls(munchie.content)
            }));
            
            setMunchieDetails(fixedMunchiesList);
            
            // Fetch bookmarked munchies
            axios.post(`${baseUrl}/api/category-munchie/get-munchie-bookmark?user_id=${userid}`, {}, {
                headers: {
                    'Authorization': `Bearer ${cookies}`
                }
            }).then((bookmarkResponse) => {
                const savedMunchies = bookmarkResponse?.data?.data?.list?.data || [];
                const bookmarksMap = {};
                savedMunchies.forEach((munchie) => {
                    bookmarksMap[munchie.id] = true;
                });
                setBookmarkedMunchies(bookmarksMap);
            }).catch((error) => {
                console.error("Error fetching bookmarked munchies:", error);
            });
            
            // Fetch full category list to get parent_id and sibling chapters
            axios.post(`${baseUrl}/api/category-munchie/list`, {}, {
                headers: {
                    'Authorization': `Bearer ${cookies}`
                }
            }).then((listResponse) => {
                console.log('Categories list response:', listResponse);
                
                // The API returns datalist directly, not datalist.data
                const categories = listResponse?.data?.data?.datalist || [];
                console.log('All categories:', categories);
                
                // Find current category in all categories (including subcategories)
                let currentCategory = null;
                for (const cat of categories) {
                    if (cat.id.toString() === categoryId.toString()) {
                        currentCategory = cat;
                        setCategoryName(cat.name || 'AI-RAD');
                        break;
                    }
                    if (cat.child) {
                        currentCategory = cat.child.find(sub => sub.id.toString() === categoryId.toString());
                        if (currentCategory) {
                            setCategoryName(currentCategory.name || 'AI-RAD');
                            break;
                        }
                    }
                }
                
                console.log('Current category found:', currentCategory);
                console.log('Current category parent_id:', currentCategory?.parent_id);
                
                if (currentCategory && currentCategory.parent_id && currentCategory.parent_id !== 0) {
                    setParentCategoryId(currentCategory.parent_id);
                    
                    // Find parent category and get all chapters
                    const parentCategory = categories.find(cat => cat.id.toString() === currentCategory.parent_id.toString());
                    
                    console.log('Parent category:', parentCategory);
                    console.log('Parent category children:', parentCategory?.child);
                    
                    if (parentCategory && parentCategory.child) {
                        const chapters = parentCategory.child || [];
                        console.log('All chapters for navigation:', chapters);
                        console.log('Number of chapters:', chapters.length);
                        
                        setAllChapters(chapters);
                        
                        // Find current chapter index
                        const currentIndex = chapters.findIndex(ch => ch.id.toString() === categoryId.toString());
                        console.log('Current chapter index:', currentIndex);
                        
                        setCurrentChapterIndex(currentIndex);
                    }
                } else {
                    console.log('Category has no parent_id or parent_id is 0 - this is a parent category');
                }
            }).catch((error) => {
                console.error('Error fetching categories:', error);
            });
            
            // If munchieId is provided in URL, find its index and set as current page
            if (munchieId) {
                const index = munchiesList.findIndex(munchie => munchie.id.toString() === munchieId.toString());
                if (index !== -1) {
                    setCurrentPage(index + 1);
                }
            }
            
            setLoading(false);
        }).catch((error) => {
            console.error('Error fetching munchies:', error);
            setLoading(false);
        });
    }, [categoryId, userid, munchieId])

    return (
        <>
            <Navbar />
            {!loading ? (
                <div className="main-wrapper" style={{ background: '#1B1E27', minHeight: '100vh' }}>
                    
                    {/* Category Title Bar */}
                    <div style={{ 
                        width: '100%', 
                        height: '62px', 
                        background: '#282D41',
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center'
                    }}>
                        <h2 style={{ 
                            color: 'white', 
                            fontSize: '26px', 
                            fontFamily: 'Poppins', 
                            fontWeight: '700',
                            margin: 0
                        }}>
                            {categoryName}
                        </h2>
                    </div>

                    {currentData && currentData.length > 0 ? (
                        currentData?.map((e, index) => (
                            <div key={index}>
                                {/* Container-width White Background */}
                                <div className="container-fluid px-0 px-lg-3">
                                    <div className="container px-0 px-lg-3">
                                        <div style={{ 
                                            background: 'white',
                                            padding: '40px 0',
                                            borderRadius: '0'
                                        }}>
                                            {/* Dark Content Viewer Card */}
                                            <div style={{ 
                                                background: '#1B1A1A', 
                                                borderRadius: '24px',
                                                overflow: 'hidden',
                                                marginBottom: '30px',
                                                marginLeft: '15px',
                                                marginRight: '15px'
                                            }}
                                                className="mx-lg-5"
                                            >
                                                {/* Content Section */}
                                                <div style={{
                                                    position: 'relative',
                                                    width: '100%',
                                                    background: '#1B1A1A',
                                                    padding: '40px 30px',
                                                    minHeight: '400px'
                                                }}>
                                                    {/* Title */}
                                                    <h3 style={{
                                                        color: 'white',
                                                        fontSize: '24px',
                                                        fontFamily: 'Poppins',
                                                        fontWeight: '600',
                                                        marginBottom: '30px',
                                                        paddingRight: '50px'
                                                    }}>
                                                        {e.title}
                                                    </h3>
                                                    
                                                    {/* Content */}
                                                    <div 
                                                        style={{
                                                            color: 'rgba(255, 255, 255, 0.85)',
                                                            fontSize: '16px',
                                                            fontFamily: 'Poppins',
                                                            fontWeight: '400',
                                                            lineHeight: '1.8'
                                                        }}
                                                        dangerouslySetInnerHTML={{ __html: e?.content }}
                                                    />
                                                    
                                                    {/* Bookmark Icon */}
                                                    <div 
                                                        onClick={() => saveMunchie(e.id)}
                                                        style={{
                                                            position: 'absolute',
                                                            top: '20px',
                                                            right: '20px',
                                                            padding: '8px',
                                                            background: 'white',
                                                            borderRadius: '12px',
                                                            border: '1px solid rgba(255, 255, 255, 0.60)',
                                                            display: 'flex',
                                                            justifyContent: 'center',
                                                            alignItems: 'center',
                                                            cursor: 'pointer',
                                                            transition: 'all 0.3s ease',
                                                            width: '40px',
                                                            height: '40px'
                                                        }}
                                                        onMouseEnter={(event) => {
                                                            event.currentTarget.style.transform = 'scale(1.1)';
                                                            event.currentTarget.style.boxShadow = '0 4px 12px rgba(0,0,0,0.3)';
                                                        }}
                                                        onMouseLeave={(event) => {
                                                            event.currentTarget.style.transform = 'scale(1)';
                                                            event.currentTarget.style.boxShadow = 'none';
                                                        }}
                                                    >
                                                        {bookmarkedMunchies[e.id] ? (
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                                                        ) : (
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path 
                                                                    d="M19 21L12 16L5 21V5C5 4.46957 5.21071 3.96086 5.58579 3.58579C5.96086 3.21071 6.46957 3 7 3H17C17.5304 3 18.0391 3.21071 18.4142 3.58579C18.7893 3.96086 19 4.46957 19 5V21Z" 
                                                                    stroke="url(#bookmarkGradientOutline)"
                                                                    strokeWidth="2"
                                                                    strokeLinecap="round"
                                                                    strokeLinejoin="round"
                                                                />
                                                                <defs>
                                                                    <linearGradient id="bookmarkGradientOutline" x1="5" y1="3" x2="19" y2="21" gradientUnits="userSpaceOnUse">
                                                                        <stop offset="0%" stopColor="#44A6C5"/>
                                                                        <stop offset="100%" stopColor="#1E4FFD"/>
                                                                    </linearGradient>
                                                                </defs>
                                                            </svg>
                                                        )}
                                                    </div>
                                                </div>
                                            </div>

                                            {/* Navigation Buttons */}
                                            <div style={{
                                                display: 'flex',
                                                justifyContent: 'space-between',
                                                alignItems: 'center',
                                                padding: '0 30px 20px 30px'
                                            }}
                                                className="px-lg-5"
                                            >
                                                {/* Previous Button */}
                                                {currentPage > 1 ? (
                                                    <button 
                                                        onClick={() => handlePageChange(currentPage - 1)}
                                                        style={{
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
                                                            transition: 'all 0.3s ease'
                                                        }}
                                                        onMouseEnter={(e) => e.currentTarget.style.transform = 'translateY(-2px)'}
                                                        onMouseLeave={(e) => e.currentTarget.style.transform = 'translateY(0)'}
                                                    >
                                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M15 19l-7-7 7-7" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                                        </svg>
                                                        Previous
                                                    </button>
                                                ) : (
                                                    <div></div>
                                                )}

                                                {/* Page Counter */}
                                                <div style={{
                                                    color: '#1B1A1A',
                                                    fontSize: '16px',
                                                    fontFamily: 'Poppins',
                                                    fontWeight: '500'
                                                }}>
                                                    {currentPage} / {totalPages}
                                                </div>

                                                {/* Next Button */}
                                                {currentPage < totalPages ? (
                                                    <button 
                                                        onClick={() => handlePageChange(currentPage + 1)}
                                                        style={{
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
                                                            transition: 'all 0.3s ease'
                                                        }}
                                                        onMouseEnter={(e) => e.currentTarget.style.transform = 'translateY(-2px)'}
                                                        onMouseLeave={(e) => e.currentTarget.style.transform = 'translateY(0)'}
                                                    >
                                                        Next
                                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M9 5l7 7-7 7" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                                        </svg>
                                                    </button>
                                                ) : (
                                                    <div></div>
                                                )}
                                            </div>

                                            {/* Chapter Navigation */}
                                            {allChapters.length > 0 && (
                                                <div style={{
                                                    display: 'flex',
                                                    justifyContent: 'space-between',
                                                    alignItems: 'center',
                                                    padding: '20px 30px',
                                                    borderTop: '2px solid #E8E8E8'
                                                }}
                                                    className="px-lg-5"
                                                >
                                                    {/* Previous Chapter Button */}
                                                    {currentChapterIndex > 0 ? (
                                                        <Link href={`/ai-rad/category?id=${allChapters[currentChapterIndex - 1].id}`}>
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
                                                                transition: 'all 0.3s ease'
                                                            }}
                                                                onMouseEnter={(e) => e.currentTarget.style.transform = 'translateY(-2px)'}
                                                                onMouseLeave={(e) => e.currentTarget.style.transform = 'translateY(0)'}
                                                            >
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
                                                        <Link href={`/ai-rad/category?id=${allChapters[currentChapterIndex + 1].id}`}>
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
                                                                marginLeft: 'auto',
                                                                transition: 'all 0.3s ease'
                                                            }}
                                                                onMouseEnter={(e) => e.currentTarget.style.transform = 'translateY(-2px)'}
                                                                onMouseLeave={(e) => e.currentTarget.style.transform = 'translateY(0)'}
                                                            >
                                                                Next Chapter
                                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M9 5l7 7-7 7" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                                                </svg>
                                                            </button>
                                                        </Link>
                                                    )}
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ))
                    ) : (
                        <div className="container">
                            <div className="row">
                                <div className="col-12 text-center py-5">
                                    <p className="text-white">No content found.</p>
                                    <Link href="/ai-rad" style={{
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
                            </div>
                        </div>
                    )}
                </div>
            ) : (
                <Loader />
            )}
            <Footer />
        </>
    )
}

export default page
