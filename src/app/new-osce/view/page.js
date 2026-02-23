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
import { Document, Page, pdfjs } from 'react-pdf';
import 'react-pdf/dist/esm/Page/AnnotationLayer.css';
import 'react-pdf/dist/esm/Page/TextLayer.css';

// Configure PDF.js worker
pdfjs.GlobalWorkerOptions.workerSrc = `//unpkg.com/pdfjs-dist@${pdfjs.version}/build/pdf.worker.min.js`;

function NewOsceViewer() {
    const [userid, setUser] = useState('')
    const [loading, setLoading] = useState(false);
    const [items, setItems] = useState([])
    const [categoryName, setCategoryName] = useState('')
    const [bookmarkedItems, setBookmarkedItems] = useState({});
    const [allChapters, setAllChapters] = useState([])
    const [currentChapterIndex, setCurrentChapterIndex] = useState(-1)
    const [currentItemIndex, setCurrentItemIndex] = useState(0);
    const [expandedChapters, setExpandedChapters] = useState({});
    const [chapterItems, setChapterItems] = useState({}); // Store items for each chapter
    const router = useRouter()
    const searchParams = useSearchParams()
    const categoryId = searchParams.get('id')  // This is the chapter/module ID
    const itemId = searchParams.get('itemId')
    const parentId = searchParams.get('parentId') // Parent category ID (e.g., Practice = 1)

    // PDF viewer state
    const [pdfUrl, setPdfUrl] = useState('');
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const [numPages, setNumPages] = useState(null);
    const [pageNumber, setPageNumber] = useState(1);
    const [scale, setScale] = useState(1.1);
    const [pageWidth, setPageWidth] = useState(null);

    const currentItem = items[currentItemIndex];

    // Toggle chapter expansion in sidebar
    const toggleChapter = (chapterId) => {
        setExpandedChapters(prev => ({
            ...prev,
            [chapterId]: !prev[chapterId]
        }));
    };
    
    // Navigate to previous item
    const goToPrevItem = () => {
        if (currentItemIndex > 0) {
            setCurrentItemIndex(prev => prev - 1);
            setPageNumber(1); // Reset to first page
        }
    };
    
    // Navigate to next item  
    const goToNextItem = () => {
        if (currentItemIndex < items.length - 1) {
            setCurrentItemIndex(prev => prev + 1);
            setPageNumber(1); // Reset to first page
        }
    };

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

    // PDF document load success
    const onDocumentLoadSuccess = ({ numPages }) => {
        setNumPages(numPages);
        setPageNumber(1);
    };

    // Zoom functions
    const zoomIn = () => {
        setScale(prevScale => Math.min(prevScale + 0.2, 3));
    };

    const zoomOut = () => {
        setScale(prevScale => Math.max(prevScale - 0.2, 0.5));
    };

    const fitToWidth = () => {
        // Calculate scale to fit page to container width
        const container = document.getElementById('pdf-container');
        if (container) {
            const containerWidth = container.offsetWidth - 80; // Account for padding
            setPageWidth(containerWidth);
            setScale(null); // Will use width prop
        }
    };

    // Page navigation
    const goToPrevPage = () => {
        setPageNumber(prev => Math.max(prev - 1, 1));
    };

    const goToNextPage = () => {
        setPageNumber(prev => Math.min(prev + 1, numPages));
    };

    // Initialize page width on mount
    useEffect(() => {
        const updateWidth = () => {
            const container = document.getElementById('pdf-container');
            if (container) {
                const containerWidth = container.offsetWidth - 80;
                setPageWidth(containerWidth);
            }
        };
        
        // Set initial width after a short delay to ensure container is rendered
        setTimeout(updateWidth, 100);
        
        window.addEventListener('resize', updateWidth);
        return () => window.removeEventListener('resize', updateWidth);
    }, []);

    // Fetch item data
    useEffect(() => {
        if (!categoryId || !userid) {
            console.log('Missing params:', { categoryId, userid });
            return;
        }

        const cookies = Cookies.get('user-token');
        
        console.log('Fetching items for chapter:', categoryId);
        
        // Fetch items list
        axios.post(`${baseUrl}/api/new-osce/items-by-chapter`, {
            chapter_id: categoryId
        }, {
            headers: {
                'Authorization': `Bearer ${cookies}`
            }
        }).then((response) => {
            console.log('Items API Response:', response.data);
            const itemsList = response?.data?.data?.items || [];
            console.log('Items List:', itemsList);
            setItems(itemsList);
            
            // Get category info
            if (itemsList.length > 0) {
                // Set PDF URL for first item by default
                if (itemsList[0].pdf_file) {
                    const pdfPath = `${baseUrl}/assets/admin/images/new_osce_pdf/${itemsList[0].pdf_file}`;
                    const proxyUrl = `/api/proxy-pdf?url=${encodeURIComponent(pdfPath)}`;
                    setPdfUrl(proxyUrl);
                }
                
                // Get category name
                if (itemsList[0].categories) {
                    setCategoryName(itemsList[0].categories.name || '');
                } else if (itemsList[0].chapter) {
                    setCategoryName(itemsList[0].chapter.name || '');
                }
            }

            // If itemId is provided in URL, find that item and set the index
            if (itemId && itemsList.length > 0) {
                const itemIndex = itemsList.findIndex(it => it.id.toString() === itemId.toString());
                if (itemIndex !== -1) {
                    setCurrentItemIndex(itemIndex);
                    if (itemsList[itemIndex].pdf_file) {
                        const pdfPath = `${baseUrl}/assets/admin/images/new_osce_pdf/${itemsList[itemIndex].pdf_file}`;
                        const proxyUrl = `/api/proxy-pdf?url=${encodeURIComponent(pdfPath)}`;
                        setPdfUrl(proxyUrl);
                    }
                }
            }
            
            // Store items for current chapter
            setChapterItems(prev => ({
                ...prev,
                [categoryId]: itemsList
            }));
            
            // Get parent category ID - use URL param first, then try from item data
            let parentCategoryId = parentId;
            if (!parentCategoryId && itemsList.length > 0) {
                // Try to get parent category ID from item
                parentCategoryId = itemsList[0].category_id || 
                                   itemsList[0].parent_category_id ||
                                   itemsList[0].categories?.parent_id ||
                                   itemsList[0].chapter?.category_id;
            }
            
            // Fetch chapters for chapter navigation using POST (same as chapters page)
            // If we have parentId, use it. Otherwise try to get all categories
            console.log('Fetching chapters with parent category ID:', parentCategoryId);
            
            if (parentCategoryId) {
                axios.post(`${baseUrl}/api/new-osce/chapters`, {
                    category_id: parentCategoryId
                }, {
                    headers: {
                        'Authorization': `Bearer ${cookies}`
                    }
                }).then((chaptersResponse) => {
                    console.log('Chapters API Response:', chaptersResponse.data);
                    const chapters = chaptersResponse?.data?.data?.chapters || [];
                    console.log('Chapters List:', chapters);
                    
                    if (chapters.length > 0) {
                        setAllChapters(chapters);
                        
                        // Find current chapter index
                        const currentIndex = chapters.findIndex(ch => ch.id.toString() === categoryId.toString());
                        setCurrentChapterIndex(currentIndex);
                        
                        // Auto-expand current chapter
                        setExpandedChapters(prev => ({
                            ...prev,
                            [categoryId]: true
                        }));
                    } else {
                        createDefaultChapter();
                    }
                }).catch((error) => {
                    console.error('Error fetching chapters:', error);
                    createDefaultChapter();
                });
            } else {
                // No parent ID - try to get all new-osce categories
                axios.get(`${baseUrl}/api/new-osce/categories`, {
                    headers: {
                        'Authorization': `Bearer ${cookies}`
                    }
                }).then((categoriesResponse) => {
                    console.log('Categories API Response:', categoriesResponse.data);
                    const categories = categoriesResponse?.data?.data?.categories || [];
                    
                    // For each category, try to fetch its chapters
                    if (categories.length > 0) {
                        // Try the first category to get chapters
                        axios.post(`${baseUrl}/api/new-osce/chapters`, {
                            category_id: categories[0].id
                        }, {
                            headers: {
                                'Authorization': `Bearer ${cookies}`
                            }
                        }).then((chaptersResponse) => {
                            const chapters = chaptersResponse?.data?.data?.chapters || [];
                            if (chapters.length > 0) {
                                setAllChapters(chapters);
                                const currentIndex = chapters.findIndex(ch => ch.id.toString() === categoryId.toString());
                                setCurrentChapterIndex(currentIndex);
                                setExpandedChapters(prev => ({ ...prev, [categoryId]: true }));
                            } else {
                                createDefaultChapter();
                            }
                        }).catch(() => createDefaultChapter());
                    } else {
                        createDefaultChapter();
                    }
                }).catch((error) => {
                    console.error('Error fetching categories:', error);
                    createDefaultChapter();
                });
            }
            
            function createDefaultChapter() {
                console.log('Creating default chapter entry');
                const chapterName = itemsList[0]?.chapter?.name || 
                                   itemsList[0]?.categories?.name || 
                                   categoryName || 
                                   'Current Module';
                const defaultChapter = {
                    id: categoryId,
                    name: chapterName
                };
                setAllChapters([defaultChapter]);
                setExpandedChapters({ [categoryId]: true });
            }
            
            setLoading(false);
        }).catch((error) => {
            console.error('Error fetching item:', error);
            setLoading(false);
        });
    }, [itemId, categoryId, userid])

    const saveBookmark = (itemId) => {
        const cookies = Cookies.get('user-token');
        const formData = new FormData();
        formData.append('user_id', userid);
        formData.append('item_id', itemId);

        axios.post(`${baseUrl}/api/new-osce/toggle-bookmark`, formData, {
            headers: {
                'Authorization': `Bearer ${cookies}`,
            }
        })
            .then((response) => {
                toast.success(response.data.message);
                // Toggle the bookmark status for this specific item
                setBookmarkedItems(prev => ({
                    ...prev,
                    [itemId]: !prev[itemId]
                }));
            })
            .catch((error) => {
                console.error('There was an error!', error);
                toast.error('Failed to update bookmark');
            });
    }

    // Update PDF URL when current item changes
    useEffect(() => {
        if (currentItem && currentItem.pdf_file) {
            const pdfPath = `${baseUrl}/assets/admin/images/new_osce_pdf/${currentItem.pdf_file}`;
            console.log('Loading PDF from:', pdfPath);
            const proxyUrl = `/api/proxy-pdf?url=${encodeURIComponent(pdfPath)}`;
            console.log('Using proxy URL:', proxyUrl);
            setPdfUrl(proxyUrl);
            setPageNumber(1); // Reset to first page when switching items
        }
    }, [currentItemIndex, items]);

    // Handle PDF load error
    const onDocumentLoadError = (error) => {
        console.error('Error loading PDF:', error);
        toast.error('Failed to load PDF. Please check the file path.');
    };

    return (
        <>
            <style jsx global>{`
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
                
                @keyframes slideInLeft {
                    0% { 
                        transform: translateX(-100%);
                        opacity: 0;
                    }
                    100% { 
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
                
                .hide-scrollbar::-webkit-scrollbar {
                    display: none;
                }
                
                .hide-scrollbar {
                    -ms-overflow-style: none;
                    scrollbar-width: none;
                }
            `}</style>
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

                    {currentItem ? (
                        <div className="container-fluid px-0 px-lg-3">
                            <div className="container px-0 px-lg-3">
                                <div style={{ 
                                    background: 'white',
                                    padding: '40px 0',
                                    borderRadius: '0'
                                }}>
                                    {/* Main Viewer Container */}
                                    <div style={{ 
                                        background: '#1B1A1A', 
                                        borderRadius: '24px',
                                        overflow: 'hidden',
                                        marginBottom: '30px',
                                        marginLeft: '15px',
                                        marginRight: '15px',
                                        display: 'flex',
                                        height: '700px',
                                        maxHeight: '85vh'
                                    }}
                                        className="mx-lg-5"
                                    >
                                        {/* Left Sidebar - Chapter Navigation */}
                                        {sidebarOpen && allChapters.length > 0 && (
                                            <div style={{
                                                width: '320px',
                                                minWidth: '320px',
                                                background: '#1B1E27',
                                                overflowY: 'auto',
                                                flexDirection: 'column',
                                                justifyContent: 'flex-start',
                                                alignItems: 'flex-start',
                                                display: 'flex',
                                                animation: 'slideInLeft 0.3s ease-out',
                                                borderRight: '1px solid rgba(255, 255, 255, 0.1)'
                                            }}
                                                className="d-none d-lg-flex"
                                            >
                                                {allChapters.map((chapter, index) => {
                                                    const isActiveChapter = chapter.id.toString() === categoryId.toString();
                                                    const isExpanded = expandedChapters[chapter.id];
                                                    // For active chapter, use the loaded items. For other chapters, we don't have items loaded.
                                                    const displayItems = isActiveChapter ? items : [];
                                                    
                                                    return (
                                                        <div key={chapter.id} style={{ alignSelf: 'stretch' }}>
                                                            {/* Chapter Header */}
                                                            {isActiveChapter && isExpanded ? (
                                                                // Active & Expanded Chapter
                                                                <div 
                                                                    style={{
                                                                        paddingLeft: 20,
                                                                        paddingRight: 20,
                                                                        paddingTop: 22,
                                                                        paddingBottom: 22,
                                                                        background: '#44A6C5',
                                                                        overflow: 'hidden',
                                                                        outline: '1px rgba(255, 255, 255, 0.30) solid',
                                                                        justifyContent: 'flex-start',
                                                                        alignItems: 'center',
                                                                        gap: 10,
                                                                        display: 'flex',
                                                                        flexDirection: 'column',
                                                                        cursor: 'pointer'
                                                                    }}
                                                                >
                                                                    <div 
                                                                        style={{
                                                                            width: '100%',
                                                                            justifyContent: 'space-between',
                                                                            alignItems: 'center',
                                                                            display: 'flex'
                                                                        }}
                                                                        onClick={() => toggleChapter(chapter.id)}
                                                                    >
                                                                        <div style={{
                                                                            color: 'white',
                                                                            fontSize: 15,
                                                                            fontFamily: 'Poppins',
                                                                            fontWeight: '500',
                                                                            wordWrap: 'break-word'
                                                                        }}>
                                                                            {chapter.name}
                                                                        </div>
                                                                        <svg 
                                                                            width="24" 
                                                                            height="12" 
                                                                            viewBox="0 0 24 12" 
                                                                            fill="none" 
                                                                            xmlns="http://www.w3.org/2000/svg"
                                                                            style={{
                                                                                transform: 'rotate(180deg)',
                                                                                transition: 'transform 0.3s ease'
                                                                            }}
                                                                        >
                                                                            <path d="M6.57999 9.54801L5.51999 8.48701L11.297 2.70801C11.3896 2.61486 11.4996 2.54093 11.6209 2.49048C11.7421 2.44003 11.8722 2.41406 12.0035 2.41406C12.1348 2.41406 12.2648 2.44003 12.3861 2.49048C12.5073 2.54093 12.6174 2.61486 12.71 2.70801L18.49 8.48701L17.43 9.54701L12.005 4.12301L6.57999 9.54801Z" fill="white"/>
                                                                        </svg>
                                                                    </div>
                                                                        
                                                                    {/* Items inside expanded active chapter */}
                                                                    {displayItems.length > 0 && (
                                                                        <div style={{
                                                                            width: '100%',
                                                                            flexDirection: 'column',
                                                                            justifyContent: 'flex-start',
                                                                            alignItems: 'flex-start',
                                                                            gap: 12,
                                                                            display: 'flex',
                                                                            marginTop: 15,
                                                                            paddingTop: 15,
                                                                            borderTop: '1px solid rgba(255,255,255,0.2)'
                                                                        }}>
                                                                            {displayItems.map((item, itemIndex) => {
                                                                                const isActiveItem = currentItem && currentItem.id === item.id;
                                                                                return (
                                                                                    <div 
                                                                                        key={item.id}
                                                                                        onClick={(e) => {
                                                                                            e.stopPropagation();
                                                                                            setCurrentItemIndex(itemIndex);
                                                                                        }}
                                                                                        style={{
                                                                                            alignSelf: 'stretch',
                                                                                            color: isActiveItem ? '#101421' : 'rgba(255,255,255,0.9)',
                                                                                            fontSize: 14,
                                                                                            fontFamily: 'Poppins',
                                                                                            fontWeight: isActiveItem ? '600' : '400',
                                                                                            wordWrap: 'break-word',
                                                                                            cursor: 'pointer',
                                                                                            padding: '8px 12px',
                                                                                            borderRadius: '8px',
                                                                                            background: isActiveItem ? 'rgba(255,255,255,0.9)' : 'transparent',
                                                                                            transition: 'all 0.2s ease'
                                                                                        }}
                                                                                        onMouseEnter={(e) => {
                                                                                            if (!isActiveItem) {
                                                                                                e.currentTarget.style.background = 'rgba(255,255,255,0.1)';
                                                                                            }
                                                                                        }}
                                                                                        onMouseLeave={(e) => {
                                                                                            if (!isActiveItem) {
                                                                                                e.currentTarget.style.background = 'transparent';
                                                                                            }
                                                                                        }}
                                                                                    >
                                                                                        {item.title || `Content ${itemIndex + 1}`}
                                                                                    </div>
                                                                                );
                                                                            })}
                                                                        </div>
                                                                    )}
                                                                </div>
                                                            ) : (
                                                                // Default/Collapsed Chapter
                                                                <div 
                                                                    style={{
                                                                        alignSelf: 'stretch',
                                                                        paddingLeft: 20,
                                                                        paddingRight: 20,
                                                                        paddingTop: 22,
                                                                        paddingBottom: 22,
                                                                        background: isActiveChapter ? '#44A6C5' : '#1B1E27',
                                                                        overflow: 'hidden',
                                                                        outline: '1px rgba(255, 255, 255, 0.30) solid',
                                                                        flexDirection: 'column',
                                                                        justifyContent: 'flex-start',
                                                                        alignItems: 'flex-start',
                                                                        gap: 10,
                                                                        display: 'flex',
                                                                        cursor: 'pointer',
                                                                        transition: 'background 0.3s ease'
                                                                    }}
                                                                    onClick={() => toggleChapter(chapter.id)}
                                                                    onMouseEnter={(e) => {
                                                                        if (!isActiveChapter) {
                                                                            e.currentTarget.style.background = '#252A3A';
                                                                        }
                                                                    }}
                                                                    onMouseLeave={(e) => {
                                                                        if (!isActiveChapter) {
                                                                            e.currentTarget.style.background = '#1B1E27';
                                                                        }
                                                                    }}
                                                                >
                                                                    <div style={{
                                                                        alignSelf: 'stretch',
                                                                        justifyContent: 'space-between',
                                                                        alignItems: 'center',
                                                                        display: 'flex'
                                                                    }}>
                                                                        <div style={{
                                                                            color: 'white',
                                                                            fontSize: 15,
                                                                            fontFamily: 'Poppins',
                                                                            fontWeight: '500',
                                                                            wordWrap: 'break-word'
                                                                        }}>
                                                                            {chapter.name}
                                                                        </div>
                                                                        <svg 
                                                                            width="24" 
                                                                            height="12" 
                                                                            viewBox="0 0 24 12" 
                                                                            fill="none" 
                                                                            xmlns="http://www.w3.org/2000/svg"
                                                                            style={{
                                                                                transform: isExpanded ? 'rotate(180deg)' : 'rotate(0deg)',
                                                                                transition: 'transform 0.3s ease'
                                                                            }}
                                                                        >
                                                                            <path d="M6.57999 9.54801L5.51999 8.48701L11.297 2.70801C11.3896 2.61486 11.4996 2.54093 11.6209 2.49048C11.7421 2.44003 11.8722 2.41406 12.0035 2.41406C12.1348 2.41406 12.2648 2.44003 12.3861 2.49048C12.5073 2.54093 12.6174 2.61486 12.71 2.70801L18.49 8.48701L17.43 9.54701L12.005 4.12301L6.57999 9.54801Z" fill="white"/>
                                                                        </svg>
                                                                    </div>
                                                                </div>
                                                            )}
                                                        </div>
                                                    );
                                                })}
                                            </div>
                                        )}

                                        {/* Main PDF Viewer */}
                                        <div style={{
                                            flex: 1,
                                            position: 'relative',
                                            display: 'flex',
                                            flexDirection: 'column',
                                            overflow: 'hidden'
                                        }}>
                                            {/* Top Controls - Bookmark - Fixed Position */}
                                            <div style={{
                                                position: 'absolute',
                                                top: '20px',
                                                right: '20px',
                                                zIndex: 1000,
                                                display: 'flex',
                                                gap: '10px',
                                                pointerEvents: 'auto'
                                            }}>
                                                {/* Toggle Sidebar (Desktop only) */}
                                                <div 
                                                    onClick={() => setSidebarOpen(!sidebarOpen)}
                                                    style={{
                                                        padding: '8px 12px',
                                                        background: sidebarOpen ? 'linear-gradient(92.48deg, #44A6C5 3.13%, #1E4FFD 100%)' : 'white',
                                                        borderRadius: '12px',
                                                        border: sidebarOpen ? '1px solid #44A6C5' : '1px solid rgba(255, 255, 255, 0.60)',
                                                        cursor: 'pointer',
                                                        transition: 'all 0.3s ease',
                                                        display: 'flex',
                                                        alignItems: 'center',
                                                        justifyContent: 'center'
                                                    }}
                                                    className="d-none d-lg-flex"
                                                    title={sidebarOpen ? "Close Sidebar" : "Open Sidebar"}
                                                    onMouseEnter={(e) => {
                                                        if (!sidebarOpen) {
                                                            e.currentTarget.style.background = 'rgba(68, 166, 197, 0.1)';
                                                            e.currentTarget.style.borderColor = '#44A6C5';
                                                        }
                                                    }}
                                                    onMouseLeave={(e) => {
                                                        if (!sidebarOpen) {
                                                            e.currentTarget.style.background = 'white';
                                                            e.currentTarget.style.borderColor = 'rgba(255, 255, 255, 0.60)';
                                                        }
                                                    }}
                                                >
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M3 12H21M3 6H21M3 18H21" stroke={sidebarOpen ? "#FFFFFF" : "#1E1E1E"} strokeWidth="2" strokeLinecap="round"/>
                                                    </svg>
                                                </div>
                                                
                                                {/* Bookmark Icon */}
                                                <div 
                                                    onClick={() => currentItem && saveBookmark(currentItem.id)}
                                                    style={{
                                                        padding: '8px 12px',
                                                        background: 'white',
                                                        borderRadius: '12px',
                                                        border: '1px solid rgba(255, 255, 255, 0.60)',
                                                        cursor: 'pointer',
                                                        transition: 'all 0.3s ease'
                                                    }}
                                                >
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path 
                                                            d="M19 21L12 16L5 21V5C5 4.46957 5.21071 3.96086 5.58579 3.58579C5.96086 3.21071 6.46957 3 7 3H17C17.5304 3 18.0391 3.21071 18.4142 3.58579C18.7893 3.96086 19 4.46957 19 5V21Z" 
                                                            fill={currentItem && bookmarkedItems[currentItem.id] ? 'url(#bookmarkGradient)' : '#1E1E1E'}
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

                                            {/* PDF Display Area with Side Navigation */}
                                            <div id="pdf-container" style={{
                                                flex: '1 1 auto',
                                                display: 'flex',
                                                justifyContent: 'center',
                                                alignItems: 'center',
                                                padding: '20px',
                                                background: '#1B1A1A',
                                                position: 'relative',
                                                overflow: 'hidden',
                                                minHeight: 0
                                            }}>
                                                {/* Fixed 16:9 Container with Scroll */}
                                                <div style={{
                                                    width: '100%',
                                                    height: '100%',
                                                    maxWidth: '100%',
                                                    maxHeight: '100%',
                                                    aspectRatio: '16 / 9',
                                                    position: 'relative',
                                                    overflow: 'auto',
                                                    background: '#2A2A2A',
                                                    margin: 'auto',
                                                    borderRadius: '8px',
                                                    scrollbarWidth: 'none',
                                                    msOverflowStyle: 'none'
                                                }}
                                                className="hide-scrollbar"
                                                >
                                                    {/* Inner wrapper with padding for scroll space */}
                                                    <div style={{
                                                        width: 'fit-content',
                                                        height: 'fit-content',
                                                        minWidth: '100%',
                                                        minHeight: '100%',
                                                        display: 'flex',
                                                        justifyContent: 'center',
                                                        alignItems: 'center',
                                                        padding: '40px',
                                                        boxSizing: 'border-box'
                                                    }}>
                                                        {pdfUrl ? (
                                                            <Document
                                                                file={pdfUrl}
                                                                onLoadSuccess={onDocumentLoadSuccess}
                                                                onLoadError={onDocumentLoadError}
                                                                loading={
                                                                    <div style={{ color: 'white', textAlign: 'center', padding: '40px' }}>
                                                                        <div style={{
                                                                            width: '40px',
                                                                            height: '40px',
                                                                            border: '4px solid rgba(68, 166, 197, 0.3)',
                                                                            borderTop: '4px solid #44A6C5',
                                                                            borderRadius: '50%',
                                                                            margin: '0 auto 20px',
                                                                            animation: 'spin 1s linear infinite'
                                                                        }}></div>
                                                                        <p style={{ fontSize: '16px', fontFamily: 'Poppins' }}>Loading PDF...</p>
                                                                    </div>
                                                            }
                                                            error={
                                                                <div style={{ color: 'white', textAlign: 'center', padding: '40px' }}>
                                                                    <p style={{ fontSize: '16px', fontFamily: 'Poppins', marginBottom: '10px' }}>Error loading PDF. Please try again.</p>
                                                                    <p style={{ fontSize: '14px', color: 'rgba(255,255,255,0.6)' }}>File: {currentItem?.pdf_file}</p>
                                                                    <p style={{ fontSize: '12px', color: 'rgba(255,255,255,0.5)', marginTop: '10px', wordBreak: 'break-all' }}>Path: {pdfUrl}</p>
                                                                </div>
                                                            }
                                                        >
                                                                <Page
                                                                    pageNumber={pageNumber}
                                                                    scale={scale}
                                                                    width={scale === null ? pageWidth : undefined}
                                                                    renderTextLayer={true}
                                                                    renderAnnotationLayer={true}
                                                                />
                                                            </Document>
                                                        ) : (
                                                            <div style={{ color: 'white', textAlign: 'center' }}>
                                                                <p>No PDF available</p>
                                                            </div>
                                                        )}
                                                    </div>
                                                </div>
                                            </div>

                                            {/* PDF Page Controls - Fixed Section */}
                                            {pdfUrl && numPages && (
                                                <div style={{ 
                                                    background: '#1B1A1A', 
                                                    borderTop: '1px solid #126E97',
                                                    padding: '20px 30px',
                                                    display: 'flex',
                                                    alignItems: 'center',
                                                    justifyContent: 'space-between',
                                                    gap: '15px',
                                                    flexWrap: 'wrap',
                                                    minHeight: '80px',
                                                    flexShrink: 0
                                                }}>
                                                    {/* Previous Page */}
                                                    <button
                                                        onClick={goToPrevPage}
                                                        disabled={pageNumber <= 1}
                                                        style={{
                                                            padding: '10px 20px',
                                                            background: pageNumber <= 1 ? 'rgba(255,255,255,0.1)' : 'linear-gradient(92.48deg, #44A6C5 3.13%, #1E4FFD 100%)',
                                                            border: 'none',
                                                            borderRadius: '12px',
                                                            color: 'white',
                                                            fontSize: '14px',
                                                            fontFamily: 'Poppins',
                                                            cursor: pageNumber <= 1 ? 'not-allowed' : 'pointer',
                                                            opacity: pageNumber <= 1 ? 0.5 : 1,
                                                            transition: 'all 0.3s',
                                                            display: 'flex',
                                                            alignItems: 'center',
                                                            gap: '8px'
                                                        }}
                                                    >
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M10.17 6.42001C10.3687 6.20675 10.4769 5.92468 10.4717 5.63323C10.4666 5.34178 10.3485 5.0637 10.1424 4.85758C9.9363 4.65146 9.65822 4.53339 9.36677 4.52825C9.07532 4.52311 8.79325 4.63129 8.57999 4.83001L3.32999 10.08C3.11931 10.291 3.00098 10.5769 3.00098 10.875C3.00098 11.1731 3.11931 11.4591 3.32999 11.67L8.57999 16.92C8.79325 17.1187 9.07532 17.2269 9.36677 17.2218C9.65822 17.2166 9.9363 17.0986 10.1424 16.8924C10.3485 16.6863 10.4666 16.4082 10.4717 16.1168C10.4769 15.8253 10.3687 15.5433 10.17 15.33L6.83999 12H12.375C14.0657 12 15.6872 12.6717 16.8828 13.8672C18.0783 15.0628 18.75 16.6843 18.75 18.375C18.75 18.6734 18.8685 18.9595 19.0795 19.1705C19.2905 19.3815 19.5766 19.5 19.875 19.5C20.1734 19.5 20.4595 19.3815 20.6705 19.1705C20.8815 18.9595 21 18.6734 21 18.375C21 17.2424 20.7769 16.1208 20.3434 15.0744C19.91 14.0279 19.2747 13.0771 18.4738 12.2762C17.6729 11.4753 16.7221 10.84 15.6756 10.4066C14.6292 9.97311 13.5076 9.75001 12.375 9.75001H6.83999L10.17 6.42001Z" fill="#FEFFFF"/>
                                                        </svg>
                                                        <span className="d-none d-md-inline">Previous Page</span>
                                                    </button>

                                                    {/* Center Group - Page Counter and Zoom */}
                                                    <div style={{
                                                        display: 'flex',
                                                        alignItems: 'center',
                                                        gap: '15px',
                                                        flex: '0 0 auto'
                                                    }}>
                                                        {/* Page Counter */}
                                                        <div style={{
                                                            color: 'white',
                                                            fontSize: '16px',
                                                            fontFamily: 'Poppins',
                                                            fontWeight: '500',
                                                            padding: '10px 20px',
                                                            background: 'rgba(255,255,255,0.05)',
                                                            borderRadius: '12px',
                                                            border: '1px solid rgba(255,255,255,0.1)'
                                                        }}>
                                                            {pageNumber} / {numPages}
                                                        </div>

                                                    {/* Zoom Out */}
                                                    <button
                                                        onClick={zoomOut}
                                                        disabled={scale <= 0.5}
                                                        style={{
                                                            padding: '10px 15px',
                                                            background: 'rgba(255,255,255,0.05)',
                                                            border: '1px solid rgba(255,255,255,0.1)',
                                                            borderRadius: '12px',
                                                            color: 'white',
                                                            fontSize: '18px',
                                                            cursor: scale <= 0.5 ? 'not-allowed' : 'pointer',
                                                            opacity: scale <= 0.5 ? 0.5 : 1,
                                                            display: 'flex',
                                                            alignItems: 'center',
                                                            justifyContent: 'center'
                                                        }}
                                                        title="Zoom Out"
                                                    >
                                                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M5.9785 8.51779H11.0571M16.7142 16.7142L13.9551 13.9551M8.53707 15.7885C13.1785 15.7885 15.7885 13.1785 15.7885 8.53707C15.7885 3.89564 13.1785 1.28564 8.53707 1.28564C3.89564 1.28564 1.28564 3.89564 1.28564 8.53707C1.28564 13.1785 3.89564 15.7885 8.53707 15.7885Z" stroke="#FEFFFF" strokeWidth="1.28571" strokeLinecap="round" strokeLinejoin="round"/>
                                                        </svg>
                                                    </button>

                                                    {/* Zoom In */}
                                                    <button
                                                        onClick={zoomIn}
                                                        disabled={scale >= 3}
                                                        style={{
                                                            padding: '10px 15px',
                                                            background: 'rgba(255,255,255,0.05)',
                                                            border: '1px solid rgba(255,255,255,0.1)',
                                                            borderRadius: '12px',
                                                            color: 'white',
                                                            fontSize: '18px',
                                                            cursor: scale >= 3 ? 'not-allowed' : 'pointer',
                                                            opacity: scale >= 3 ? 0.5 : 1,
                                                            display: 'flex',
                                                            alignItems: 'center',
                                                            justifyContent: 'center'
                                                        }}
                                                        title="Zoom In"
                                                    >
                                                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M8.53707 5.9965V11.0764M5.9965 8.53707H11.0764M16.7142 16.7142L13.9551 13.9551M8.53707 15.7885C13.1785 15.7885 15.7885 13.1785 15.7885 8.53707C15.7885 3.89564 13.1785 1.28564 8.53707 1.28564C3.89564 1.28564 1.28564 3.89564 1.28564 8.53707C1.28564 13.1785 3.89564 15.7885 8.53707 15.7885Z" stroke="#FEFFFF" strokeWidth="1.28571" strokeLinecap="round" strokeLinejoin="round"/>
                                                        </svg>
                                                    </button>

                                                    </div>

                                                    {/* Next Page */}
                                                    <button
                                                        onClick={goToNextPage}
                                                        disabled={pageNumber >= numPages}
                                                        style={{
                                                            padding: '10px 20px',
                                                            background: pageNumber >= numPages ? 'rgba(255,255,255,0.1)' : 'linear-gradient(92.48deg, #44A6C5 3.13%, #1E4FFD 100%)',
                                                            border: 'none',
                                                            borderRadius: '12px',
                                                            color: 'white',
                                                            fontSize: '14px',
                                                            fontFamily: 'Poppins',
                                                            cursor: pageNumber >= numPages ? 'not-allowed' : 'pointer',
                                                            opacity: pageNumber >= numPages ? 0.5 : 1,
                                                            transition: 'all 0.3s',
                                                            display: 'flex',
                                                            alignItems: 'center',
                                                            gap: '8px'
                                                        }}
                                                    >
                                                        <span className="d-none d-md-inline">Next Page</span>
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M13.83 6.42001C13.6313 6.20675 13.5231 5.92468 13.5283 5.63323C13.5334 5.34178 13.6515 5.0637 13.8576 4.85758C14.0637 4.65146 14.3418 4.53339 14.6332 4.52825C14.9247 4.52311 15.2068 4.63129 15.42 4.83001L20.67 10.08C20.8807 10.291 20.999 10.5769 20.999 10.875C20.999 11.1731 20.8807 11.4591 20.67 11.67L15.42 16.92C15.2068 17.1187 14.9247 17.2269 14.6332 17.2218C14.3418 17.2166 14.0637 17.0986 13.8576 16.8924C13.6515 16.6863 13.5334 16.4082 13.5283 16.1168C13.5231 15.8253 13.6313 15.5433 13.83 15.33L17.16 12H11.625C9.93426 12 8.31275 12.6717 7.11721 13.8672C5.92166 15.0628 5.25001 16.6843 5.25001 18.375C5.25001 18.6734 5.13149 18.9595 4.92051 19.1705C4.70953 19.3815 4.42338 19.5 4.12501 19.5C3.82664 19.5 3.5405 19.3815 3.32952 19.1705C3.11854 18.9595 3.00001 18.6734 3.00001 18.375C3.00001 17.2424 3.22311 16.1208 3.65655 15.0744C4.09 14.0279 4.72531 13.0771 5.52622 12.2762C6.32712 11.4753 7.27794 10.84 8.32437 10.4066C9.3708 9.97311 10.4924 9.75001 11.625 9.75001H17.16L13.83 6.42001Z" fill="#FEFFFF"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            )}

                                            {/* Item Navigation Controls */}
                                            {items.length > 1 && (
                                                <div style={{
                                                    background: '#1B1A1A',
                                                    padding: '20px',
                                                    display: 'flex',
                                                    alignItems: 'center',
                                                    justifyContent: 'center',
                                                    gap: '20px'
                                                }}>
                                                    {/* Previous Item */}
                                                    <div 
                                                        onClick={goToPrevItem}
                                                        style={{ 
                                                            padding: '8px 38px',
                                                            display: 'flex',
                                                            alignItems: 'center',
                                                            gap: '12px',
                                                            cursor: currentItemIndex > 0 ? 'pointer' : 'not-allowed',
                                                            opacity: currentItemIndex > 0 ? 1 : 0.5,
                                                            background: 'rgba(255, 255, 255, 0.05)',
                                                            borderRadius: '12px',
                                                            border: '1px solid rgba(255, 255, 255, 0.1)'
                                                        }}
                                                    >
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M10.17 6.42001C10.3687 6.20675 10.4769 5.92468 10.4717 5.63323C10.4666 5.34178 10.3485 5.0637 10.1424 4.85758C9.93631 4.65146 9.65822 4.53339 9.36677 4.52825C9.07532 4.52311 8.79325 4.63129 8.58 4.83001L3.33 10.08C3.11929 10.291 3.00098 10.5769 3.00098 10.875C3.00098 11.1731 3.11929 11.4591 3.33 11.67L8.58 16.92C8.79325 17.1187 9.07532 17.2269 9.36677 17.2218C9.65822 17.2166 9.93631 17.0986 10.1424 16.8924C10.3485 16.6863 10.4666 16.4082 10.4717 16.1168C10.4769 15.8253 10.3687 15.5433 10.17 15.33L6.84 12H12.375C14.0657 12 15.6872 12.6717 16.8828 13.8672C18.0783 15.0628 18.75 16.6843 18.75 18.375C18.75 18.6734 18.8685 18.9595 19.0795 19.1705C19.2905 19.3815 19.5766 19.5 19.875 19.5C20.1734 19.5 20.4595 19.3815 20.6705 19.1705C20.8815 18.9595 21 18.6734 21 18.375C21 17.2424 20.7769 16.1208 20.3435 15.0744C19.91 14.0279 19.2747 13.0771 18.4738 12.2762C17.6729 11.4753 16.7221 10.84 15.6756 10.4066C14.6292 9.97311 13.5076 9.75001 12.375 9.75001H6.84L10.17 6.42001Z" fill="#FEFFFF"/>
                                                        </svg>
                                                        <span style={{ 
                                                            color: 'white', 
                                                            fontSize: '16px', 
                                                            fontFamily: 'Poppins', 
                                                            fontWeight: '400' 
                                                        }}
                                                            className="d-none d-md-inline"
                                                        >
                                                            Previous
                                                        </span>
                                                    </div>

                                                    {/* Item Counter */}
                                                    <div style={{
                                                        display: 'flex',
                                                        alignItems: 'center',
                                                        gap: '10px'
                                                    }}>
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
                                                                {currentItemIndex + 1}
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
                                                                {items.length}
                                                            </span>
                                                        </div>
                                                    </div>

                                                    {/* Next Item */}
                                                    <div 
                                                        onClick={goToNextItem}
                                                        style={{ 
                                                            padding: '8px 38px',
                                                            display: 'flex',
                                                            alignItems: 'center',
                                                            gap: '12px',
                                                            cursor: currentItemIndex < items.length - 1 ? 'pointer' : 'not-allowed',
                                                            opacity: currentItemIndex < items.length - 1 ? 1 : 0.5,
                                                            background: 'rgba(255, 255, 255, 0.05)',
                                                            borderRadius: '12px',
                                                            border: '1px solid rgba(255, 255, 255, 0.1)'
                                                        }}
                                                    >
                                                        <span style={{ 
                                                            color: 'white', 
                                                            fontSize: '16px', 
                                                            fontFamily: 'Poppins', 
                                                            fontWeight: '400' 
                                                        }}
                                                            className="d-none d-md-inline"
                                                        >
                                                            Next
                                                        </span>
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M13.83 6.42001C13.6313 6.20675 13.5231 5.92468 13.5283 5.63323C13.5334 5.34178 13.6515 5.0637 13.8576 4.85758C14.0637 4.65146 14.3418 4.53339 14.6332 4.52825C14.9247 4.52311 15.2068 4.63129 15.42 4.83001L20.67 10.08C20.8807 10.291 20.999 10.5769 20.999 10.875C20.999 11.1731 20.8807 11.4591 20.67 11.67L15.42 16.92C15.2068 17.1187 14.9247 17.2269 14.6332 17.2218C14.3418 17.2166 14.0637 17.0986 13.8576 16.8924C13.6515 16.6863 13.5334 16.4082 13.5283 16.1168C13.5231 15.8253 13.6313 15.5433 13.83 15.33L17.16 12H11.625C9.93426 12 8.31275 12.6717 7.11721 13.8672C5.92166 15.0628 5.25001 16.6843 5.25001 18.375C5.25001 18.6734 5.13149 18.9595 4.92051 19.1705C4.70953 19.3815 4.42338 19.5 4.12501 19.5C3.82664 19.5 3.5405 19.3815 3.32952 19.1705C3.11854 18.9595 3.00001 18.6734 3.00001 18.375C3.00001 17.2424 3.22311 16.1208 3.65655 15.0744C4.09 14.0279 4.72531 13.0771 5.52622 12.2762C6.32712 11.4753 7.27794 10.84 8.32437 10.4066C9.3708 9.97311 10.4924 9.75001 11.625 9.75001H17.16L13.83 6.42001Z" fill="#FEFFFF"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                            )}
                                        </div>
                                    </div>

                                    {/* Chapter Navigation Buttons */}
                                    {allChapters.length > 0 && (
                                        <div style={{
                                            display: 'flex', 
                                            justifyContent: 'space-between',
                                            alignItems: 'center',
                                            marginTop: '30px',
                                            padding: '0 15px',
                                            gap: '20px'
                                        }}
                                            className="px-lg-5"
                                        >
                                            {/* Previous Chapter Button */}
                                            {currentChapterIndex > 0 ? (
                                                <Link href={`/new-osce/category?id=${allChapters[currentChapterIndex - 1].id}${parentId ? `&parentId=${parentId}` : ''}`}>
                                                    <button style={{ 
                                                        padding: '12px 24px',
                                                        background: 'linear-gradient(92.48deg, #44A6C5 3.13%, #1E4FFD 100%)',
                                                        borderRadius: '12px',
                                                        border: 'none',
                                                        cursor: 'pointer',
                                                        color: 'white',
                                                        fontSize: '16px',
                                                        fontFamily: 'Poppins',
                                                        fontWeight: '500',
                                                        display: 'flex',
                                                        alignItems: 'center',
                                                        gap: '8px',
                                                        transition: 'all 0.3s ease'
                                                    }}
                                                        onMouseEnter={(e) => {
                                                            e.currentTarget.style.transform = 'translateY(-2px)';
                                                            e.currentTarget.style.boxShadow = '0 6px 20px rgba(68, 166, 197, 0.4)';
                                                        }}
                                                        onMouseLeave={(e) => {
                                                            e.currentTarget.style.transform = 'translateY(0)';
                                                            e.currentTarget.style.boxShadow = 'none';
                                                        }}
                                                    >
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M10.17 6.42001C10.3687 6.20675 10.4769 5.92468 10.4717 5.63323C10.4666 5.34178 10.3485 5.0637 10.1424 4.85758C9.9363 4.65146 9.65822 4.53339 9.36677 4.52825C9.07532 4.52311 8.79325 4.63129 8.57999 4.83001L3.32999 10.08C3.11931 10.291 3.00098 10.5769 3.00098 10.875C3.00098 11.1731 3.11931 11.4591 3.32999 11.67L8.57999 16.92C8.79325 17.1187 9.07532 17.2269 9.36677 17.2218C9.65822 17.2166 9.9363 17.0986 10.1424 16.8924C10.3485 16.6863 10.4666 16.4082 10.4717 16.1168C10.4769 15.8253 10.3687 15.5433 10.17 15.33L6.83999 12H12.375C14.0657 12 15.6872 12.6717 16.8828 13.8672C18.0783 15.0628 18.75 16.6843 18.75 18.375C18.75 18.6734 18.8685 18.9595 19.0795 19.1705C19.2905 19.3815 19.5766 19.5 19.875 19.5C20.1734 19.5 20.4595 19.3815 20.6705 19.1705C20.8815 18.9595 21 18.6734 21 18.375C21 17.2424 20.7769 16.1208 20.3434 15.0744C19.91 14.0279 19.2747 13.0771 18.4738 12.2762C17.6729 11.4753 16.7221 10.84 15.6756 10.4066C14.6292 9.97311 13.5076 9.75001 12.375 9.75001H6.83999L10.17 6.42001Z" fill="#FEFFFF"/>
                                                        </svg>
                                                        <span className="d-none d-md-inline">Previous Chapter</span>
                                                    </button>
                                                </Link>
                                            ) : (
                                                <div></div>
                                            )}

                                            {/* Next Chapter Button */}
                                            {currentChapterIndex < allChapters.length - 1 && (
                                                <Link href={`/new-osce/category?id=${allChapters[currentChapterIndex + 1].id}${parentId ? `&parentId=${parentId}` : ''}`}>
                                                    <button style={{ 
                                                        padding: '12px 24px',
                                                        background: 'linear-gradient(92.48deg, #44A6C5 3.13%, #1E4FFD 100%)',
                                                        borderRadius: '12px',
                                                        border: 'none',
                                                        cursor: 'pointer',
                                                        color: 'white',
                                                        fontSize: '16px',
                                                        fontFamily: 'Poppins',
                                                        fontWeight: '500',
                                                        display: 'flex',
                                                        alignItems: 'center',
                                                        gap: '8px',
                                                        marginLeft: 'auto',
                                                        transition: 'all 0.3s ease'
                                                    }}
                                                        onMouseEnter={(e) => {
                                                            e.currentTarget.style.transform = 'translateY(-2px)';
                                                            e.currentTarget.style.boxShadow = '0 6px 20px rgba(68, 166, 197, 0.4)';
                                                        }}
                                                        onMouseLeave={(e) => {
                                                            e.currentTarget.style.transform = 'translateY(0)';
                                                            e.currentTarget.style.boxShadow = 'none';
                                                        }}
                                                    >
                                                        <span className="d-none d-md-inline">Next Chapter</span>
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M13.83 6.42001C13.6313 6.20675 13.5231 5.92468 13.5283 5.63323C13.5334 5.34178 13.6515 5.0637 13.8576 4.85758C14.0637 4.65146 14.3418 4.53339 14.6332 4.52825C14.9247 4.52311 15.2068 4.63129 15.42 4.83001L20.67 10.08C20.8807 10.291 20.999 10.5769 20.999 10.875C20.999 11.1731 20.8807 11.4591 20.67 11.67L15.42 16.92C15.2068 17.1187 14.9247 17.2269 14.6332 17.2218C14.3418 17.2166 14.0637 17.0986 13.8576 16.8924C13.6515 16.6863 13.5334 16.4082 13.5283 16.1168C13.5231 15.8253 13.6313 15.5433 13.83 15.33L17.16 12H11.625C9.93426 12 8.31275 12.6717 7.11721 13.8672C5.92166 15.0628 5.25001 16.6843 5.25001 18.375C5.25001 18.6734 5.13149 18.9595 4.92051 19.1705C4.70953 19.3815 4.42338 19.5 4.12501 19.5C3.82664 19.5 3.5405 19.3815 3.32952 19.1705C3.11854 18.9595 3.00001 18.6734 3.00001 18.375C3.00001 17.2424 3.22311 16.1208 3.65655 15.0744C4.09 14.0279 4.72531 13.0771 5.52622 12.2762C6.32712 11.4753 7.27794 10.84 8.32437 10.4066C9.3708 9.97311 10.4924 9.75001 11.625 9.75001H17.16L13.83 6.42001Z" fill="#FEFFFF"/>
                                                        </svg>
                                                    </button>
                                                </Link>
                                            )}
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>
                    ) : (
                        <div className="container" style={{ maxWidth: '1140px', marginTop: '40px' }}>
                            <div style={{ background: 'white', borderRadius: '24px', padding: '60px' }}>
                                <div style={{ textAlign: 'center' }}>
                                    <h4 style={{ color: '#666', marginBottom: '20px' }}>Content not found</h4>
                                    <p style={{ color: '#999', marginBottom: '30px' }}>Please select another item or return to the category page.</p>
                                    <Link href={`/new-osce/category?id=${categoryId}${parentId ? `&parentId=${parentId}` : ''}`} style={{
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
                                        Back to Chapter
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

export default NewOsceViewer
