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
import { toast } from 'react-toastify';

function page() {
    const [userid, setUser] = useState('')
    const [loading, setLoading] = useState(false);
    const [osces, setOsces] = useState([])
    const [categoryName, setCategoryName] = useState('')
    const [bookmarkedOsces, setBookmarkedOsces] = useState({})
    const [allChapters, setAllChapters] = useState([])
    const [currentChapterIndex, setCurrentChapterIndex] = useState(-1)
    const [parentCategoryId, setParentCategoryId] = useState(null)
    const [currentPage, setCurrentPage] = useState(1)
    const [totalPages, setTotalPages] = useState(1)
    const [totalOsces, setTotalOsces] = useState(0)
    const router = useRouter()
    const searchParams = useSearchParams()

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

    // Function to fetch OSCE items with pagination
    const fetchOsceItems = (page = 1) => {
        const categoryId = searchParams.get('id');
        if (!categoryId || !userid) return;

        const cookies = Cookies.get('user-token');
        
        const formData = new FormData();
        formData.append('category_id', categoryId);
        formData.append('page', page);
        
        setLoading(true);
        
        axios.post(`${baseUrl}/api/osce/category-osce`, formData, {
            headers: {
                'Authorization': `Bearer ${cookies}`
            }
        }).then((response) => {
            console.log('OSCE Category Full Response:', response?.data);
            
            // Backend returns: { status, data: { list: {data, current_page, last_page, total}, category } }
            const listData = response?.data?.data?.list || {};
            const osceList = listData.data || [];
            
            console.log('Paginated List Object:', listData);
            console.log('OSCE Items:', osceList);
            console.log('Pagination Info:', {
                current_page: listData.current_page,
                last_page: listData.last_page,
                total: listData.total
            });
            
            setOsces(osceList);
            setCurrentPage(listData.current_page || 1);
            setTotalPages(listData.last_page || 1);
            setTotalOsces(listData.total || 0);
            setCategoryName(response?.data?.data?.category?.name || '');
            setLoading(false);
        }).catch((error) => {
            console.error('Error fetching OSCE items:', error);
            setLoading(false);
        });
    };

    useEffect(() => {
        if (!userid) return;

        const categoryId = searchParams.get('id');
        if (!categoryId) return;

        const cookies = Cookies.get('user-token');
        
        // Fetch OSCE items
        fetchOsceItems(1);

        // Fetch bookmarked OSCEs
        const bookmarkFormData = new FormData();
        bookmarkFormData.append('user_id', userid);
        
        axios.post(`${baseUrl}/api/osce/get-osce-bookmark`, bookmarkFormData, {
            headers: {
                'Authorization': `Bearer ${cookies}`
            }
        }).then((response) => {
            const bookmarkedList = response?.data?.data?.list?.data || [];
            const bookmarkMap = {};
            bookmarkedList.forEach(osce => {
                bookmarkMap[osce.id] = true;
            });
            setBookmarkedOsces(bookmarkMap);
        }).catch((error) => {
            console.error('Error fetching bookmarks:', error);
        });

        // Fetch all categories to find siblings for chapter navigation
        axios.post(`${baseUrl}/api/osce/list`, {}, {
            headers: {
                'Authorization': `Bearer ${cookies}`
            }
        }).then((listResponse) => {
            console.log('OSCE Categories for Navigation:', listResponse?.data);
            
            // Try different response structures
            const categories = listResponse?.data?.data?.datalist || 
                             listResponse?.data?.data?.list || 
                             listResponse?.data?.datalist || 
                             listResponse?.data?.list || 
                             [];
            
            console.log('All Categories:', categories);
            let currentCategory = null;

            // Search for current category in parent categories and their children
            for (const cat of categories) {
                if (cat.id.toString() === categoryId.toString()) {
                    currentCategory = cat;
                    break;
                }
                if (cat.child && Array.isArray(cat.child)) {
                    currentCategory = cat.child.find(sub => sub.id.toString() === categoryId.toString());
                    if (currentCategory) break;
                }
            }

            console.log('Current Category:', currentCategory);

            // If found and has parent, get sibling chapters
            if (currentCategory && currentCategory.parent_id && currentCategory.parent_id !== 0) {
                const parentCategory = categories.find(cat => cat.id.toString() === currentCategory.parent_id.toString());
                
                console.log('Parent Category:', parentCategory);
                
                if (parentCategory && parentCategory.child) {
                    setAllChapters(parentCategory.child);
                    setParentCategoryId(parentCategory.id);
                    
                    const currentIndex = parentCategory.child.findIndex(ch => ch.id.toString() === categoryId.toString());
                    setCurrentChapterIndex(currentIndex);
                    
                    console.log('Sibling Chapters:', parentCategory.child);
                    console.log('Current Index:', currentIndex);
                }
            }
        }).catch((error) => {
            console.error('Error fetching categories for navigation:', error);
        });

    }, [userid, searchParams])

    const saveOsce = (osceId) => {
        const cookies = Cookies.get('user-token');
        const formData = new FormData();
        formData.append('user_id', userid);
        formData.append('osce_id', osceId);

        axios.post(`${baseUrl}/api/osce/change-osce-bookmark`, formData, {
            headers: {
                'Authorization': `Bearer ${cookies}`,
            }
        }).then((response) => {
            toast.success(response.data.message || 'Bookmark updated successfully!');
            
            // Toggle bookmark state
            setBookmarkedOsces(prev => ({
                ...prev,
                [osceId]: !prev[osceId]
            }));
        }).catch((error) => {
            console.error('Error updating bookmark:', error);
            toast.error('Failed to update bookmark');
        });
    }

    // Color variations for OSCEs
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
                                            <h6 className="text-white mb-0">Select OSCE</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="container">
                            <div className="row justify-content-center mt-4">
                                <div className="col-lg-12">
                                    <div className="row g-4">
                                        {osces && osces.length > 0 ? (
                                            osces?.map((osce, index) => (
                                                <div className="col-md-4 col-sm-6 col-6" key={osce.id} style={{ padding: '12px', flex: '0 0 16.666%', maxWidth: '16.666%' }}>
                                                    <Link href={`/osce/view?id=${searchParams.get('id')}&osceId=${osce.id}#page1`}>
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
                                                                {osce.title || `OSCE ${index + 1}`}
                                                            </h5>
                                                        </div>
                                                    </Link>
                                                    <div
                                                        className={`icon ${bookmarkedOsces[osce.id] ? "bookmark-active" : ""}`}
                                                        onClick={() => saveOsce(osce.id)}
                                                        style={{
                                                            position: 'absolute',
                                                            top: '10px',
                                                            right: '20px',
                                                            cursor: 'pointer',
                                                            fontSize: '24px',
                                                            color: bookmarkedOsces[osce.id] ? '#ffc107' : '#6c757d',
                                                            zIndex: 10
                                                        }}
                                                    >
                                                        <i className="fa-solid fa-bookmark" />
                                                    </div>
                                                </div>
                                            ))
                                        ) : (
                                            <div className="col-12 text-center py-5">
                                                <p className="text-white">No OSCE items found.</p>
                                            </div>
                                        )}
                                    </div>

                                    {/* Pagination Controls - Desktop */}
                                    {totalPages > 1 && (
                                        <div className="row mt-4">
                                            <div className="col-12">
                                                <div style={{
                                                    display: 'flex',
                                                    justifyContent: 'center',
                                                    alignItems: 'center',
                                                    gap: '10px',
                                                    marginBottom: '20px'
                                                }}>
                                                    <button
                                                        onClick={() => fetchOsceItems(currentPage - 1)}
                                                        disabled={currentPage === 1}
                                                        style={{
                                                            padding: '10px 20px',
                                                            fontSize: '14px',
                                                            fontWeight: '600',
                                                            color: 'white',
                                                            background: currentPage === 1 ? '#6c757d' : 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                                                            border: 'none',
                                                            borderRadius: '8px',
                                                            cursor: currentPage === 1 ? 'not-allowed' : 'pointer',
                                                            opacity: currentPage === 1 ? 0.5 : 1
                                                        }}
                                                    >
                                                        Previous
                                                    </button>

                                                    <span style={{
                                                        color: 'white',
                                                        fontSize: '16px',
                                                        fontWeight: '600',
                                                        padding: '0 15px'
                                                    }}>
                                                        Page {currentPage} of {totalPages} ({totalOsces} total items)
                                                    </span>

                                                    <button
                                                        onClick={() => fetchOsceItems(currentPage + 1)}
                                                        disabled={currentPage === totalPages}
                                                        style={{
                                                            padding: '10px 20px',
                                                            fontSize: '14px',
                                                            fontWeight: '600',
                                                            color: 'white',
                                                            background: currentPage === totalPages ? '#6c757d' : 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                                                            border: 'none',
                                                            borderRadius: '8px',
                                                            cursor: currentPage === totalPages ? 'not-allowed' : 'pointer',
                                                            opacity: currentPage === totalPages ? 0.5 : 1
                                                        }}
                                                    >
                                                        Next
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    )}

                                    {/* Chapter Navigation Buttons - Desktop */}
                                    {allChapters.length > 0 && currentChapterIndex !== -1 && (
                                        <div className="container mt-5">
                                            <div style={{
                                                display: 'flex',
                                                justifyContent: 'space-between',
                                                alignItems: 'center',
                                                marginTop: '30px',
                                                marginBottom: '30px'
                                            }}>
                                                {currentChapterIndex > 0 ? (
                                                    <Link href={`/osce/category?id=${allChapters[currentChapterIndex - 1].id}`}>
                                                        <button style={{
                                                            display: 'flex',
                                                            alignItems: 'center',
                                                            gap: '8px',
                                                            padding: '15px 30px',
                                                            fontSize: '16px',
                                                            fontWeight: '600',
                                                            color: 'white',
                                                            background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                                                            border: 'none',
                                                            borderRadius: '30px',
                                                            cursor: 'pointer',
                                                            transition: 'all 0.3s ease',
                                                            boxShadow: '0 4px 15px rgba(102, 126, 234, 0.4)'
                                                        }}>
                                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                                                                <path d="M19 12H5M12 19l-7-7 7-7"/>
                                                            </svg>
                                                            Previous Chapter
                                                        </button>
                                                    </Link>
                                                ) : (
                                                    <div></div>
                                                )}
                                                
                                                {currentChapterIndex < allChapters.length - 1 && (
                                                    <Link href={`/osce/category?id=${allChapters[currentChapterIndex + 1].id}`}>
                                                        <button style={{
                                                            display: 'flex',
                                                            alignItems: 'center',
                                                            gap: '8px',
                                                            padding: '15px 30px',
                                                            fontSize: '16px',
                                                            fontWeight: '600',
                                                            color: 'white',
                                                            background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                                                            border: 'none',
                                                            borderRadius: '30px',
                                                            cursor: 'pointer',
                                                            transition: 'all 0.3s ease',
                                                            boxShadow: '0 4px 15px rgba(102, 126, 234, 0.4)',
                                                            marginLeft: 'auto'
                                                        }}>
                                                            Next Chapter
                                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                                                                <path d="M5 12h14M12 5l7 7-7 7"/>
                                                            </svg>
                                                        </button>
                                                    </Link>
                                                )}
                                            </div>
                                        </div>
                                    )}
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
                                    {osces && osces.length > 0 ? (
                                        osces?.map((osce, index) => (
                                            <div className="col-6 col-sm-6 mb-3" key={osce.id} style={{ position: 'relative' }}>
                                                <Link href={`/osce/view?id=${searchParams.get('id')}&osceId=${osce.id}#page1`}>
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
                                                                {osce.title || `OSCE ${index + 1}`}
                                                            </h6>
                                                        </div>
                                                    </div>
                                                </Link>
                                                <div
                                                    className={`icon ${bookmarkedOsces[osce.id] ? "bookmark-active" : ""}`}
                                                    onClick={() => saveOsce(osce.id)}
                                                    style={{
                                                        position: 'absolute',
                                                        top: '5px',
                                                        right: '15px',
                                                        cursor: 'pointer',
                                                        fontSize: '20px',
                                                        color: bookmarkedOsces[osce.id] ? '#ffc107' : '#6c757d',
                                                        zIndex: 10
                                                    }}
                                                >
                                                    <i className="fa-solid fa-bookmark" />
                                                </div>
                                            </div>
                                        ))
                                    ) : (
                                        <div className="col-12 text-center py-5">
                                            <p className="text-white">No OSCE items found.</p>
                                        </div>
                                    )}
                                </div>

                                {/* Pagination Controls - Mobile */}
                                {totalPages > 1 && (
                                    <div style={{
                                        display: 'flex',
                                        flexDirection: 'column',
                                        alignItems: 'center',
                                        gap: '15px',
                                        marginTop: '20px',
                                        marginBottom: '20px',
                                        padding: '0 15px'
                                    }}>
                                        <div style={{
                                            display: 'flex',
                                            gap: '10px',
                                            alignItems: 'center'
                                        }}>
                                            <button
                                                onClick={() => fetchOsceItems(currentPage - 1)}
                                                disabled={currentPage === 1}
                                                style={{
                                                    padding: '8px 16px',
                                                    fontSize: '14px',
                                                    fontWeight: '600',
                                                    color: 'white',
                                                    background: currentPage === 1 ? '#6c757d' : 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                                                    border: 'none',
                                                    borderRadius: '8px',
                                                    cursor: currentPage === 1 ? 'not-allowed' : 'pointer',
                                                    opacity: currentPage === 1 ? 0.5 : 1
                                                }}
                                            >
                                                Previous
                                            </button>

                                            <button
                                                onClick={() => fetchOsceItems(currentPage + 1)}
                                                disabled={currentPage === totalPages}
                                                style={{
                                                    padding: '8px 16px',
                                                    fontSize: '14px',
                                                    fontWeight: '600',
                                                    color: 'white',
                                                    background: currentPage === totalPages ? '#6c757d' : 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                                                    border: 'none',
                                                    borderRadius: '8px',
                                                    cursor: currentPage === totalPages ? 'not-allowed' : 'pointer',
                                                    opacity: currentPage === totalPages ? 0.5 : 1
                                                }}
                                            >
                                                Next
                                            </button>
                                        </div>
                                        
                                        <span style={{
                                            color: 'white',
                                            fontSize: '14px',
                                            fontWeight: '600'
                                        }}>
                                            Page {currentPage} of {totalPages} ({totalOsces} total)
                                        </span>
                                    </div>
                                )}

                                {/* Chapter Navigation Buttons - Mobile */}
                                {allChapters.length > 0 && currentChapterIndex !== -1 && (
                                    <div style={{
                                        display: 'flex',
                                        justifyContent: 'space-between',
                                        alignItems: 'center',
                                        marginTop: '20px',
                                        marginBottom: '20px',
                                        padding: '0 15px'
                                    }}>
                                        {currentChapterIndex > 0 ? (
                                            <Link href={`/osce/category?id=${allChapters[currentChapterIndex - 1].id}`}>
                                                <button style={{
                                                    display: 'flex',
                                                    alignItems: 'center',
                                                    gap: '6px',
                                                    padding: '12px 20px',
                                                    fontSize: '14px',
                                                    fontWeight: '600',
                                                    color: 'white',
                                                    background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                                                    border: 'none',
                                                    borderRadius: '25px',
                                                    cursor: 'pointer'
                                                }}>
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                                                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                                                    </svg>
                                                    Previous
                                                </button>
                                            </Link>
                                        ) : (
                                            <div></div>
                                        )}
                                        
                                        {currentChapterIndex < allChapters.length - 1 && (
                                            <Link href={`/osce/category?id=${allChapters[currentChapterIndex + 1].id}`}>
                                                <button style={{
                                                    display: 'flex',
                                                    alignItems: 'center',
                                                    gap: '6px',
                                                    padding: '12px 20px',
                                                    fontSize: '14px',
                                                    fontWeight: '600',
                                                    color: 'white',
                                                    background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                                                    border: 'none',
                                                    borderRadius: '25px',
                                                    cursor: 'pointer',
                                                    marginLeft: 'auto'
                                                }}>
                                                    Next
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                                                        <path d="M5 12h14M12 5l7 7-7 7"/>
                                                    </svg>
                                                </button>
                                            </Link>
                                        )}
                                    </div>
                                )}
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
