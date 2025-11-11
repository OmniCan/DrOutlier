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
    const [osceData, setOsceData] = useState(null)
    const [userid, setUser] = useState('')
    const [loading, setLoading] = useState(false);
    const [isBookmarked, setIsBookmarked] = useState(false);
    const [showAnswer, setShowAnswer] = useState(false);
    const [allChapters, setAllChapters] = useState([])
    const [currentChapterIndex, setCurrentChapterIndex] = useState(-1)
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

        // Fetch all categories to find siblings for chapter navigation
        if (categoryId) {
            axios.post(`${baseUrl}/api/osce/list`, {}, {
                headers: {
                    'Authorization': `Bearer ${cookies}`
                }
            }).then((listResponse) => {
                const categories = listResponse?.data?.data?.datalist || [];
                let currentCategory = null;

                // Search for current category in parent categories and their children
                for (const cat of categories) {
                    if (cat.id.toString() === categoryId.toString()) {
                        currentCategory = cat;
                        break;
                    }
                    if (cat.child) {
                        currentCategory = cat.child.find(sub => sub.id.toString() === categoryId.toString());
                        if (currentCategory) break;
                    }
                }

                // If found and has parent, get sibling chapters
                if (currentCategory && currentCategory.parent_id && currentCategory.parent_id !== 0) {
                    const parentCategory = categories.find(cat => cat.id.toString() === currentCategory.parent_id.toString());
                    
                    if (parentCategory && parentCategory.child) {
                        setAllChapters(parentCategory.child);
                        
                        const currentIndex = parentCategory.child.findIndex(ch => ch.id.toString() === categoryId.toString());
                        setCurrentChapterIndex(currentIndex);
                    }
                }
            }).catch((error) => {
                console.error('Error fetching categories for navigation:', error);
            });
        }

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

    const handleShare = async () => {
        const currentUrl = window.location.href;

        if (navigator.share) {
            try {
                await navigator.share({
                    title: "Check this OSCE!",
                    text: `Check out this OSCE case!`,
                    url: currentUrl,
                });
            } catch (error) {
                console.error("Error sharing content:", error);
            }
        } else {
            try {
                await navigator.clipboard.writeText(currentUrl);
                alert("URL copied to clipboard! Share it with your friends.");
            } catch (error) {
                console.error("Failed to copy URL:", error);
            }
        }
    };

    return (
        <>
            <Navbar />
            {!loading && osceData ? (
                <div className="main-wrapper">
                    <section className="Macaroni-Sign-page pt-0 d-none d-lg-block">
                        <div className="container">
                            <div className="macaroni-top">
                                <div className="row">
                                    <div className="col-lg-4">
                                        <div className="content">
                                            <h2 className="text-white mb-0">OSCE</h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="container">
                            <div className="macaroni-sign-wrap p-4" style={{ backgroundColor: "#fff" }}>
                                <div className="row">
                                    <div className="col-lg-4 sticky-top">
                                        <div className="image">
                                            <img
                                                src={`${baseUrl}/assets/admin/images/osce/${osceData.image}`}
                                                className="img-fluid w-100 mb-4"
                                                alt="OSCE Image"
                                            />
                                        </div>
                                    </div>
                                    <div className="col-lg-8">
                                        <div className="macaroni-sign-inner">
                                            <div className='box-osce'>
                                                <h5 style={{ color: 'black', display: 'flex', justifyContent: 'space-between' }}>
                                                    <span>Question</span>
                                                    <div className='ocse-btnQuiz-button'>
                                                        <div className='btnQuiz'>
                                                            <button className="btn bth-link btn-next btnQuiz"
                                                                onClick={() => setShowAnswer(!showAnswer)}
                                                            >
                                                                {showAnswer ? "Hide Answer" : "Show Answer"}
                                                            </button>
                                                        </div>
                                                        <div
                                                            className={`icon ${isBookmarked ? "bookmark-active" : ""}`}
                                                            onClick={saveOsce}>
                                                            <i className="fa-solid fa-bookmark" />
                                                        </div>
                                                    </div>
                                                </h5>

                                                {osceData.question && osceData.question.map((q, idx) => (
                                                    <p key={idx}>{q.question}</p>
                                                ))}
                                            </div>

                                            {showAnswer && (
                                                <div className='box'>
                                                    <h5 style={{ color: 'black' }}>Answer</h5>
                                                    {osceData.question && osceData.question.map((q, idx) => (
                                                        <p key={idx}>{q.answer}</p>
                                                    ))}
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                </div>
                                
                                {osceData?.content && showAnswer && (
                                    <div className="" style={{ marginBottom: "20px" }}>
                                        <div className="content-explanation-box" style={{
                                            backgroundColor: "#f8f9fa",
                                            padding: "20px",
                                            borderRadius: "8px",
                                            marginTop: "20px",
                                            border: "1px solid #dee2e6",
                                            borderLeft: "4px solid #0d6efd",
                                            width: "100%"
                                        }}>
                                            <h4 style={{ marginBottom: "15px", color: "#2c4a87" }}>Explanation</h4>
                                            <div dangerouslySetInnerHTML={{ __html: osceData?.content }} />
                                        </div>
                                    </div>
                                )}
                            </div>

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
                    </section>

                    <section className="Macaroni-sign-page-mobile p-0 d-block d-lg-none">
                        <div className="Macaroni-top">
                            <div className="container">
                                <div className="row">
                                    <Link href='/osce'>
                                        <div className="col-4">
                                            <i className="fa-solid fa-chevron-left" />
                                        </div>
                                        <div className="col-4 text-center">
                                            <h6>OSCE</h6>
                                        </div>
                                        <div className="col-4" />
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <div className="Macaroni-middle">
                            <div
                                className={`icon ${isBookmarked ? "bookmark-active" : ""}`}
                                onClick={saveOsce}>
                                <i className="fa-solid fa-bookmark" />
                            </div>
                            <div className="image">
                                <img
                                    src={`${baseUrl}/assets/admin/images/osce/${osceData.image}`}
                                    className="img-fluid w-100 mb-5"
                                    alt="OSCE Image"
                                />
                            </div>

                            <div className="macaroni-sign-wrap" style={{ backgroundColor: "#fff" }}>
                                <div className="container">
                                    <div className="row">
                                        <div className="col-12">
                                            <i className="fa-solid fa-share" onClick={handleShare} />
                                            <div className="macaroni-sign-inner">
                                                <div className='box-osce'>
                                                    <div className='box-osce-show-answer'>
                                                        <h5 style={{ color: 'black' }}>Question</h5>
                                                        <div className='ocse-btnQuiz-button'>
                                                            <div className='btnQuiz'>
                                                                <button className="btn bth-link btn-next btnQuiz"
                                                                    onClick={() => setShowAnswer(!showAnswer)}
                                                                >
                                                                    {showAnswer ? "Hide Answer" : "Show Answer"}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {osceData.question && osceData.question.map((q, idx) => (
                                                        <p key={idx}>{q.question}</p>
                                                    ))}
                                                </div>
                                                
                                                {showAnswer && (
                                                    <div className='box'>
                                                        <h5 style={{ color: 'black' }}>Answer</h5>
                                                        {osceData.question && osceData.question.map((q, idx) => (
                                                            <p key={idx}>{q.answer}</p>
                                                        ))}
                                                    </div>
                                                )}
                                                
                                                {osceData?.content && showAnswer && (
                                                    <div className="" style={{ marginBottom: "20px" }}>
                                                        <div className="content-explanation-box" style={{
                                                            backgroundColor: "#f8f9fa",
                                                            padding: "20px",
                                                            borderRadius: "8px",
                                                            marginTop: "20px",
                                                            border: "1px solid #dee2e6",
                                                            borderLeft: "4px solid #0d6efd",
                                                            width: "100%"
                                                        }}>
                                                            <h4 style={{ marginBottom: "15px", color: "#2c4a87" }}>Explanation</h4>
                                                            <div dangerouslySetInnerHTML={{ __html: osceData?.content }} />
                                                        </div>
                                                    </div>
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

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
