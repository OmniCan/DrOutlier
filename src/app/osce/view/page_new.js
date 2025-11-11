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

function Page() {
    const [osceData, setOsceData] = useState(null)
    const [categoryName, setCategoryName] = useState('')
    const [userid, setUser] = useState('')
    const [loading, setLoading] = useState(false);
    const [isBookmarked, setIsBookmarked] = useState(false);
    const [showAnswer, setShowAnswer] = useState(false);
    const [zoomLevel, setZoomLevel] = useState(1);
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

    useEffect(() => {
        if (!userid) return;

        const osceId = searchParams.get('osceId');
        const categoryId = searchParams.get('id');
        
        if (!osceId) return;

        const cookies = Cookies.get('user-token');
        
        // Fetch single OSCE details
        const formData = new FormData();
        formData.append('osce_id', osceId);
        
        axios.post(`${baseUrl}/api/osce/get-osce-by-id`, formData, {
            headers: {
                'Authorization': `Bearer ${cookies}`
            }
        }).then((response) => {
            setOsceData(response?.data?.data);
            setLoading(false);
        }).catch((error) => {
            console.error('Error fetching OSCE:', error);
            setLoading(false);
        });

        // Get category name
        if (categoryId) {
            axios.post(`${baseUrl}/api/osce/category-osce`, { category_id: categoryId }, {
                headers: {
                    'Authorization': `Bearer ${cookies}`
                }
            }).then((response) => {
                setCategoryName(response?.data?.data?.category?.name || 'OSCE');
            }).catch((error) => {
                console.error('Error fetching category:', error);
            });
        }

        // Check if bookmarked
        const bookmarkFormData = new FormData();
        bookmarkFormData.append('user_id', userid);
        
        axios.post(`${baseUrl}/api/osce/get-osce-bookmark`, bookmarkFormData, {
            headers: {
                'Authorization': `Bearer ${cookies}`
            }
        }).then((response) => {
            const bookmarkedList = response?.data?.data?.list?.data || [];
            const isBookmarked = bookmarkedList.some(osce => osce.id.toString() === osceId.toString());
            setIsBookmarked(isBookmarked);
        }).catch((error) => {
            console.error('Error fetching bookmarks:', error);
        });

    }, [userid, searchParams])

    const saveOsce = () => {
        const osceId = searchParams.get('osceId');
        const cookies = Cookies.get('user-token');
        const formData = new FormData();
        formData.append('user_id', userid);
        formData.append('osce_id', osceId);

        axios.post(`${baseUrl}/api/osce/change-osce-bookmark`, formData, {
            headers: {
                'Authorization': `Bearer ${cookies}`,
            }
        }).then((response) => {
            toast.success(response.data.message || 'Bookmark updated!');
            setIsBookmarked(!isBookmarked);
        }).catch((error) => {
            console.error('Error updating bookmark:', error);
            toast.error('Failed to update bookmark');
        });
    }

    const handleZoomIn = () => {
        setZoomLevel(prev => Math.min(prev + 0.2, 3));
    }

    const handleZoomOut = () => {
        setZoomLevel(prev => Math.max(prev - 0.2, 0.5));
    }

    return (
        <>
            <Navbar />
            {!loading && osceData ? (
                <div style={{ width: '100%', minHeight: '100vh', position: 'relative', background: 'white' }}>
                    {/* Header */}
                    <div style={{ 
                        width: '100%', 
                        height: '62px', 
                        background: '#282D41',
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center'
                    }}>
                        <div style={{ 
                            color: 'white', 
                            fontSize: '26px', 
                            fontFamily: 'Poppins', 
                            fontWeight: '700' 
                        }}>
                            {categoryName || osceData.title}
                        </div>
                    </div>

                    {/* Main Content Container */}
                    <div style={{ 
                        maxWidth: '1440px',
                        margin: '0 auto',
                        padding: '40px 20px',
                        display: 'flex',
                        gap: '20px',
                        flexWrap: 'wrap',
                        justifyContent: 'center'
                    }}>
                        {/* Image Section */}
                        <div style={{ 
                            width: '523px',
                            maxWidth: '100%',
                            display: 'flex',
                            flexDirection: 'column'
                        }}>
                            <div style={{ 
                                width: '100%',
                                height: '485px',
                                background: '#1B1A1A',
                                borderTopLeftRadius: '24px',
                                borderTopRightRadius: '24px',
                                position: 'relative',
                                overflow: 'hidden'
                            }}>
                                <img
                                    src={`${baseUrl}/assets/admin/images/osce/${osceData.image}`}
                                    alt="OSCE"
                                    style={{
                                        width: '100%',
                                        height: '100%',
                                        objectFit: 'contain',
                                        transform: `scale(${zoomLevel})`,
                                        transition: 'transform 0.3s ease'
                                    }}
                                />
                                {/* Bookmark Icon */}
                                <div 
                                    onClick={saveOsce}
                                    style={{ 
                                        padding: '8px', 
                                        position: 'absolute',
                                        right: '21px',
                                        top: '21px',
                                        background: 'white',
                                        borderRadius: '12px',
                                        cursor: 'pointer',
                                        boxShadow: '0 2px 8px rgba(0,0,0,0.1)'
                                    }}>
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill={isBookmarked ? "url(#gradient)" : "none"} stroke={isBookmarked ? "none" : "url(#gradient)"} strokeWidth="2">
                                        <defs>
                                            <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                                <stop offset="0%" style={{stopColor: '#44A6C5', stopOpacity: 1}} />
                                                <stop offset="100%" style={{stopColor: '#1E4FFD', stopOpacity: 1}} />
                                            </linearGradient>
                                        </defs>
                                        <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            
                            {/* Zoom Controls */}
                            <div style={{ 
                                width: '100%',
                                height: '63px',
                                background: '#1B1A1A',
                                borderBottomRightRadius: '24px',
                                borderBottomLeftRadius: '24px',
                                borderTop: '1px solid #126E97',
                                display: 'flex',
                                alignItems: 'center',
                                justifyContent: 'center',
                                gap: '18px'
                            }}>
                                <button 
                                    onClick={handleZoomOut}
                                    style={{ 
                                        padding: '8px 28px',
                                        background: 'rgba(52.72, 52.72, 52.72, 0.20)',
                                        borderRadius: '8px',
                                        border: '1px solid #353535',
                                        display: 'flex',
                                        alignItems: 'center',
                                        gap: '12px',
                                        cursor: 'pointer',
                                        color: '#D9D9D9',
                                        fontSize: '16px',
                                        fontFamily: 'Poppins',
                                        fontWeight: '600'
                                    }}>
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#FEFFFF" strokeWidth="2.5">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <path d="m21 21-4.35-4.35"></path>
                                        <line x1="8" y1="11" x2="14" y2="11"></line>
                                    </svg>
                                    Zoom-Out
                                </button>
                                
                                <button 
                                    onClick={handleZoomIn}
                                    style={{ 
                                        padding: '8px 28px',
                                        background: 'rgba(52.72, 52.72, 52.72, 0.20)',
                                        borderRadius: '8px',
                                        border: '1px solid #353535',
                                        display: 'flex',
                                        alignItems: 'center',
                                        gap: '12px',
                                        cursor: 'pointer',
                                        color: '#D9D9D9',
                                        fontSize: '16px',
                                        fontFamily: 'Poppins',
                                        fontWeight: '600'
                                    }}>
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#FEFFFF" strokeWidth="2.5">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <path d="m21 21-4.35-4.35"></path>
                                        <line x1="11" y1="8" x2="11" y2="14"></line>
                                        <line x1="8" y1="11" x2="14" y2="11"></line>
                                    </svg>
                                    Zoom-In
                                </button>
                            </div>
                        </div>

                        {/* Questions/Answers Section */}
                        <div style={{ 
                            width: '523px',
                            maxWidth: '100%',
                            minHeight: '548px',
                            background: '#F5F5F5',
                            borderRadius: '24px',
                            padding: '17px 24px',
                            display: 'flex',
                            flexDirection: 'column',
                            gap: '12px'
                        }}>
                            {osceData.question && osceData.question.map((q, idx) => (
                                <div key={idx} style={{ 
                                    display: 'flex',
                                    flexDirection: 'column',
                                    gap: '8px'
                                }}>
                                    <div>
                                        <span style={{ 
                                            color: 'black',
                                            fontSize: '16px',
                                            fontFamily: 'Poppins',
                                            fontWeight: '600',
                                            lineHeight: '23px'
                                        }}>Question  </span>
                                        <span style={{ 
                                            color: 'black',
                                            fontSize: '16px',
                                            fontFamily: 'Poppins',
                                            fontWeight: '400',
                                            lineHeight: '23px'
                                        }}>{q.question}</span>
                                    </div>
                                    
                                    {showAnswer && q.answer && (
                                        <div>
                                            <span style={{ 
                                                color: '#101421',
                                                fontSize: '16px',
                                                fontFamily: 'Poppins',
                                                fontWeight: '600',
                                                lineHeight: '23px'
                                            }}>Answer</span>
                                            <span style={{ 
                                                color: 'rgba(0, 0, 0, 0.60)',
                                                fontSize: '16px',
                                                fontFamily: 'Poppins',
                                                fontWeight: '400',
                                                lineHeight: '23px'
                                            }}>  {q.answer}</span>
                                        </div>
                                    )}
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* Show/Hide Answer Button - Centered */}
                    <div style={{
                        display: 'flex',
                        justifyContent: 'center',
                        margin: '20px 0'
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
                                fontWeight: '600',
                                boxShadow: '0 4px 15px rgba(68, 166, 197, 0.4)',
                                transition: 'all 0.3s ease'
                            }}>
                            {showAnswer ? "Hide Answer" : "Show Answer"}
                        </button>
                    </div>

                    {/* Explanation Section */}
                    {showAnswer && osceData?.content && (
                        <div style={{
                            maxWidth: '1140px',
                            margin: '0 auto',
                            padding: '48px 227px',
                            display: 'flex',
                            flexDirection: 'column',
                            alignItems: 'center',
                            gap: '10px'
                        }}>
                            <div style={{
                                width: '100%',
                                color: 'black',
                                fontSize: '16px',
                                fontFamily: 'Poppins',
                                fontWeight: '600',
                                textAlign: 'center',
                                marginBottom: '10px'
                            }}>
                                Explanation
                            </div>
                            <div 
                                style={{
                                    width: '100%',
                                    color: 'rgba(0, 0, 0, 0.60)',
                                    fontSize: '16px',
                                    fontFamily: 'Poppins',
                                    fontWeight: '400',
                                    lineHeight: '1.6'
                                }}
                                dangerouslySetInnerHTML={{ __html: osceData?.content }} 
                            />
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

export default Page
