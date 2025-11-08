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
    const [spooterdetails, setspooterdetails] = useState([])
    const [categoryName, setCategoryName] = useState('')
    const [showAnswer, setShowAnswer] = useState(false);
    const [bookmarkedSpotters, setBookmarkedSpotters] = useState({});
    const router = useRouter()
    const searchParams = useSearchParams()
    const categoryId = searchParams.get('id')
    const spotterId = searchParams.get('spotterId')

    useEffect(() => {
        setLoading(true);
        const IsUserExist = Cookies.get('user-token')
        if (!IsUserExist) {
            router.push('/')
        }
    }, []);

    const [currentPage, setCurrentPage] = useState(1);
    const itemsPerPage = 1;

    const totalPages = Math?.ceil(spooterdetails?.length / itemsPerPage);

    const currentData = spooterdetails?.slice(
        (currentPage - 1) * itemsPerPage,
        currentPage * itemsPerPage
    );

    const handlePageChange = (page) => {
        if (page > 0 && page <= totalPages) {
            setCurrentPage(page);
            setShowAnswer(false); // Reset answer visibility when changing pages
        }
        window.location.hash = `page${page}`; // Update the hash in the URL

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


    const savespotters = (spotterId) => {
        const cookies = Cookies.get('user-token');
        const formData = new FormData();
        formData.append('user_id', userid);
        formData.append('spotter_id', spotterId);

        console.log('Toggling bookmark for spotter:', spotterId, 'User:', userid);
        console.log('Current bookmark status:', bookmarkedSpotters[spotterId]);

        axios.post(`${baseUrl}/api/spotters/change-bookmark-status`, formData, {
            headers: {
                'Authorization': `Bearer ${cookies}`,
            }
        })
            .then((response) => {
                console.log('Bookmark response:', response.data);
                toast.success(response.data.message);
                
                // Toggle the bookmark status for this specific spotter
                setBookmarkedSpotters(prev => {
                    const newStatus = {
                        ...prev,
                        [spotterId]: !prev[spotterId]
                    };
                    console.log('New bookmark status:', newStatus);
                    return newStatus;
                });
            })
            .catch((error) => {
                console.error('There was an error!', error);
            });
    }


    useEffect(() => {
        if (!categoryId || !userid) {
            console.log('No category ID or user ID found');
            return;
        }

        const cookies = Cookies.get('user-token');
        const formData = new FormData();
        formData.append('category_id', categoryId);
        
        console.log('Fetching spotters for category:', categoryId, 'User:', userid);
        
        // Fetch spotters list and bookmarked spotters in parallel
        Promise.all([
            axios.post(`${baseUrl}/api/spotters/list-by-category`, formData, {
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
            
            setspooterdetails(spottersList);
            setCategoryName(categoryResponse?.data?.data?.category_name || 'Spotters');
            
            // Create a Set of bookmarked spotter IDs for quick lookup
            const bookmarkedIds = new Set(bookmarkedList.map(spotter => spotter.id));
            
            // Initialize bookmark status for each spotter
            const bookmarkStatus = {};
            spottersList.forEach(spotter => {
                console.log('Spotter:', spotter.id, 'Is bookmarked:', bookmarkedIds.has(spotter.id));
                bookmarkStatus[spotter.id] = bookmarkedIds.has(spotter.id);
            });
            
            console.log('Initial bookmark status:', bookmarkStatus);
            setBookmarkedSpotters(bookmarkStatus);
            
            // If spotterId is provided in URL, find its index and set as current page
            if (spotterId) {
                const index = spottersList.findIndex(spotter => spotter.id.toString() === spotterId.toString());
                if (index !== -1) {
                    setCurrentPage(index + 1);
                }
            }
            
            setLoading(false);
        }).catch((error) => {
            console.error('Error fetching data:', error);
            
            // Fallback: try just the category list if bookmarked fetch fails
            axios.post(`${baseUrl}/api/spotters/list-by-category`, formData, {
                headers: {
                    'Authorization': `Bearer ${cookies}`
                }
            }).then((e) => {
                const spottersList = e?.data?.data?.datalist || [];
                setspooterdetails(spottersList);
                setCategoryName(e?.data?.data?.category_name || 'Spotters');
                
                // Initialize with empty bookmark status
                const bookmarkStatus = {};
                spottersList.forEach(spotter => {
                    bookmarkStatus[spotter.id] = false;
                });
                setBookmarkedSpotters(bookmarkStatus);
                
                // If spotterId is provided in URL, find its index and set as current page
                if (spotterId) {
                    const index = spottersList.findIndex(spotter => spotter.id.toString() === spotterId.toString());
                    if (index !== -1) {
                        setCurrentPage(index + 1);
                    }
                }
                
                setLoading(false);
            }).catch((err) => {
                console.error('Error fetching spotters:', err);
                setLoading(false);
            });
        });
    }, [categoryId, userid, spotterId])


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
                                <div className="container">
                                    <div style={{ 
                                        background: 'white',
                                        padding: '40px',
                                        borderRadius: '0'
                                    }}>
                                        {/* Dark Image Viewer Card */}
                                        <div style={{ 
                                            background: '#1B1A1A', 
                                            borderRadius: '24px',
                                            overflow: 'hidden',
                                            marginBottom: '30px'
                                        }}>
                                            {/* Image Section with 16:9 ratio */}
                                            <div style={{
                                                position: 'relative',
                                                width: '100%',
                                                paddingTop: '56.25%', // 16:9 aspect ratio
                                                background: '#1B1A1A',
                                                overflow: 'hidden'
                                            }}>
                                                <img
                                                    src={`${baseUrl}/assets/admin/images/spotters/${e.image}`}
                                                    style={{ 
                                                        position: 'absolute',
                                                        top: '0',
                                                        left: '0',
                                                        width: '100%',
                                                        height: '100%',
                                                        objectFit: 'contain'
                                                    }}
                                                    alt={e?.title}
                                                />
                                                
                                                {/* Bookmark Icon */}
                                                <div 
                                                    onClick={() => savespotters(e.id)}
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
                                                        transition: 'all 0.3s ease'
                                                    }}
                                                >
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path 
                                                            d="M19 21L12 16L5 21V5C5 4.46957 5.21071 3.96086 5.58579 3.58579C5.96086 3.21071 6.46957 3 7 3H17C17.5304 3 18.0391 3.21071 18.4142 3.58579C18.7893 3.96086 19 4.46957 19 5V21Z" 
                                                            fill={bookmarkedSpotters[e.id] ? 'url(#bookmarkGradient)' : '#1E1E1E'}
                                                        />
                                                        <defs>
                                                            <linearGradient id="bookmarkGradient" x1="5" y1="3" x2="19" y2="21" gradientUnits="userSpaceOnUse">
                                                                <stop offset="0%" stopColor="#44A6C5"/>
                                                                <stop offset="100%" stopColor="#1E4FFD"/>
                                                            </linearGradient>
                                                        </defs>
                                                    </svg>
                                                </div>
                                            </div>

                                            {/* Navigation Footer */}
                                            <div style={{ 
                                                background: '#1B1A1A', 
                                                borderTop: '1px solid #126E97',
                                                padding: '12px 0',
                                                display: 'flex',
                                                alignItems: 'center',
                                                justifyContent: 'space-between',
                                                position: 'relative'
                                            }}>
                                                {/* Previous Button */}
                                                <div 
                                                    onClick={() => currentPage > 1 && handlePageChange(currentPage - 1)}
                                                    style={{ 
                                                        padding: '8px 38px',
                                                        display: 'flex',
                                                        alignItems: 'center',
                                                        gap: '12px',
                                                        cursor: currentPage > 1 ? 'pointer' : 'not-allowed',
                                                        opacity: currentPage > 1 ? 1 : 0.5
                                                    }}
                                                >
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M10.17 6.42001C10.3687 6.20675 10.4769 5.92468 10.4717 5.63323C10.4666 5.34178 10.3485 5.0637 10.1424 4.85758C9.9363 4.65146 9.65822 4.53339 9.36677 4.52825C9.07532 4.52311 8.79325 4.63129 8.57999 4.83001L3.32999 10.08C3.11931 10.291 3.00098 10.5769 3.00098 10.875C3.00098 11.1731 3.11931 11.4591 3.32999 11.67L8.57999 16.92C8.79325 17.1187 9.07532 17.2269 9.36677 17.2218C9.65822 17.2166 9.9363 17.0986 10.1424 16.8924C10.3485 16.6863 10.4666 16.4082 10.4717 16.1168C10.4769 15.8253 10.3687 15.5433 10.17 15.33L6.83999 12H12.375C14.0657 12 15.6872 12.6717 16.8828 13.8672C18.0783 15.0628 18.75 16.6843 18.75 18.375C18.75 18.6734 18.8685 18.9595 19.0795 19.1705C19.2905 19.3815 19.5766 19.5 19.875 19.5C20.1734 19.5 20.4595 19.3815 20.6705 19.1705C20.8815 18.9595 21 18.6734 21 18.375C21 17.2424 20.7769 16.1208 20.3434 15.0744C19.91 14.0279 19.2747 13.0771 18.4738 12.2762C17.6729 11.4753 16.7221 10.84 15.6756 10.4066C14.6292 9.97311 13.5076 9.75001 12.375 9.75001H6.83999L10.17 6.42001Z" fill="#FEFFFF"/>
                                                    </svg>
                                                    <span style={{ 
                                                        color: 'white', 
                                                        fontSize: '16px', 
                                                        fontFamily: 'Poppins', 
                                                        fontWeight: '400' 
                                                    }}>
                                                        Previous
                                                    </span>
                                                </div>

                                                {/* Page Counter */}
                                                <div style={{ 
                                                    position: 'absolute',
                                                    left: '50%',
                                                    transform: 'translateX(-50%)',
                                                    display: 'flex',
                                                    alignItems: 'center',
                                                    gap: '6px'
                                                }}>
                                                    <div style={{ 
                                                        background: 'rgba(0, 0, 0, 0.60)', 
                                                        borderRadius: '12px',
                                                        border: '1px solid #1B1A1A',
                                                        padding: '7px 25px'
                                                    }}>
                                                        <span style={{ 
                                                            color: 'white', 
                                                            fontSize: '16px', 
                                                            fontFamily: 'Poppins', 
                                                            fontWeight: '400' 
                                                        }}>
                                                            {currentPage}
                                                        </span>
                                                    </div>
                                                    <span style={{ 
                                                        color: 'rgba(255, 255, 255, 0.60)', 
                                                        fontSize: '16px', 
                                                        fontFamily: 'Poppins', 
                                                        fontWeight: '400' 
                                                    }}>
                                                        /
                                                    </span>
                                                    <div style={{ 
                                                        background: 'rgba(0, 0, 0, 0.60)', 
                                                        borderRadius: '12px',
                                                        border: '1px solid #1B1A1A',
                                                        padding: '7px 15px'
                                                    }}>
                                                        <span style={{ 
                                                            color: 'white', 
                                                            fontSize: '16px', 
                                                            fontFamily: 'Poppins', 
                                                            fontWeight: '400' 
                                                        }}>
                                                            {totalPages}
                                                        </span>
                                                    </div>
                                                </div>

                                                {/* Next Button */}
                                                <div 
                                                    onClick={() => currentPage < totalPages && handlePageChange(currentPage + 1)}
                                                    style={{ 
                                                        padding: '8px 38px',
                                                        display: 'flex',
                                                        alignItems: 'center',
                                                        gap: '12px',
                                                        cursor: currentPage < totalPages ? 'pointer' : 'not-allowed',
                                                        opacity: currentPage < totalPages ? 1 : 0.5
                                                    }}
                                                >
                                                    <span style={{ 
                                                        color: 'white', 
                                                        fontSize: '16px', 
                                                        fontFamily: 'Poppins', 
                                                        fontWeight: '400' 
                                                    }}>
                                                        Next
                                                    </span>
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M13.83 6.42001C13.6313 6.20675 13.5231 5.92468 13.5283 5.63323C13.5334 5.34178 13.6515 5.0637 13.8576 4.85758C14.0637 4.65146 14.3418 4.53339 14.6332 4.52825C14.9247 4.52311 15.2068 4.63129 15.42 4.83001L20.67 10.08C20.8807 10.291 20.999 10.5769 20.999 10.875C20.999 11.1731 20.8807 11.4591 20.67 11.67L15.42 16.92C15.2068 17.1187 14.9247 17.2269 14.6332 17.2218C14.3418 17.2166 14.0637 17.0986 13.8576 16.8924C13.6515 16.6863 13.5334 16.4082 13.5283 16.1168C13.5231 15.8253 13.6313 15.5433 13.83 15.33L17.16 12H11.625C9.93426 12 8.31275 12.6717 7.11721 13.8672C5.92166 15.0628 5.25001 16.6843 5.25001 18.375C5.25001 18.6734 5.13149 18.9595 4.92051 19.1705C4.70953 19.3815 4.42338 19.5 4.12501 19.5C3.82664 19.5 3.5405 19.3815 3.32952 19.1705C3.11854 18.9595 3.00001 18.6734 3.00001 18.375C3.00001 17.2424 3.22311 16.1208 3.65655 15.0744C4.09 14.0279 4.72531 13.0771 5.52622 12.2762C6.32712 11.4753 7.27794 10.84 8.32437 10.4066C9.3708 9.97311 10.4924 9.75001 11.625 9.75001H17.16L13.83 6.42001Z" fill="#FEFFFF"/>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>

                                        {/* Show/Hide Answer Button */}
                                        <div style={{ 
                                            display: 'flex', 
                                            justifyContent: 'center', 
                                            marginBottom: showAnswer ? '30px' : '0'
                                        }}>
                                            <button
                                                onClick={() => setShowAnswer(!showAnswer)}
                                                style={{ 
                                                    padding: '17px 34px',
                                                    background: 'linear-gradient(150deg, #44A6C5 0%, #1E4FFD 100%)',
                                                    borderRadius: '12px',
                                                    border: 'none',
                                                    cursor: 'pointer',
                                                    color: 'white',
                                                    fontSize: '20px',
                                                    fontFamily: 'Poppins',
                                                    fontWeight: '600'
                                                }}
                                            >
                                                {showAnswer ? 'Hide Answer' : 'Show Answer'}
                                            </button>
                                        </div>

                                        {/* Answer Section */}
                                        {showAnswer && (
                                            <div style={{ 
                                                padding: '0 100px'
                                            }}>
                                                {/* Answer Title */}
                                                <h4 style={{ 
                                                    color: 'black', 
                                                    fontSize: '16px', 
                                                    fontFamily: 'Poppins', 
                                                    fontWeight: '600',
                                                    marginBottom: '20px'
                                                }}>
                                                    {e?.title}
                                                </h4>
                                                
                                                {/* Answer Content */}
                                                <div style={{ 
                                                    color: 'rgba(0, 0, 0, 0.60)', 
                                                    fontSize: '16px', 
                                                    fontFamily: 'Poppins', 
                                                    fontWeight: '400',
                                                    lineHeight: '1.6'
                                                }}
                                                dangerouslySetInnerHTML={{ __html: e?.content }}>
                                                </div>
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </div>
                        ))
                    ) : (
                        <div className="container" style={{ maxWidth: '1140px', marginTop: '40px' }}>
                            <div style={{ background: 'white', borderRadius: '24px', padding: '60px' }}>
                                <div style={{ textAlign: 'center' }}>
                                    <h4 style={{ color: '#666', marginBottom: '20px' }}>No spotters found in this category</h4>
                                    <p style={{ color: '#999', marginBottom: '30px' }}>Please check back later or select another category.</p>
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
