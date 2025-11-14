"use client"
import Footer from '@/components/Footer'
import Navbar from '@/components/Navbar'
import React, { useEffect, useState } from 'react'
import baseUrl from '@/Services/BaseUrl';
import Link from 'next/link';
import axios from 'axios';
import Cookies from 'js-cookie'
import Loader from '@/components/Loader';
import { useSearchParams } from 'next/navigation';
import { useRouter } from 'next/navigation'
import { DotLottieReact } from '@lottiefiles/dotlottie-react';

function page() {
    const searchParams = useSearchParams();
    const categoryId = searchParams.get('id');
    const [userid, setUser] = useState('')
    const [loading, setLoading] = useState(false);
    const [chapters, setChapters] = useState([])
    const [categoryName, setCategoryName] = useState('Chapters')
    const router = useRouter()

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
        
        console.log('Fetching Practical Essentials chapters for category:', categoryId);
        
        axios.post(`${baseUrl}/api/basic-category/list`, {}, {
            headers: {
                'Authorization': `Bearer ${cookies}`
            }
        }).then((response) => {
            console.log('Practical Essentials Categories API Response:', response);
            
            // The API returns datalist directly
            const allCategories = response?.data?.data?.datalist || [];
            console.log('All categories:', allCategories);
            
            // Find the selected parent category
            const selectedCategory = allCategories.find(cat => cat.id.toString() === categoryId.toString());
            
            console.log('Selected category:', selectedCategory);
            
            if (selectedCategory) {
                setCategoryName(selectedCategory.name || 'Chapters');
                setChapters(selectedCategory.child || []);
            }
            
            setLoading(false);
        }).catch((error) => {
            console.error('Error fetching Practical Essentials chapters:', error);
            setLoading(false);
        });
    }, [categoryId])

    // Generate color variations for chapters
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
                                                    <Link href={`/practical-essentials/category?id=${chapter.id}`}>
                                                        <div className="box" style={{ 
                                                            display: 'flex', 
                                                            flexDirection: 'column', 
                                                            alignItems: 'center', 
                                                            justifyContent: 'center',
                                                            position: 'relative',
                                                            cursor: 'pointer',
                                                            transition: 'all 0.3s ease'
                                                        }}
                                                        onMouseEnter={(e) => {
                                                            e.currentTarget.style.transform = 'translateY(-10px)';
                                                        }}
                                                        onMouseLeave={(e) => {
                                                            e.currentTarget.style.transform = 'translateY(0)';
                                                        }}>
                                                            <div style={{ 
                                                                width: '100%',
                                                                aspectRatio: '1',
                                                                position: 'relative',
                                                                borderRadius: '20px',
                                                                overflow: 'hidden',
                                                                display: 'flex',
                                                                alignItems: 'center',
                                                                justifyContent: 'center'
                                                            }}>
                                                                <div style={{
                                                                    width: '100%',
                                                                    height: '100%',
                                                                    position: 'absolute',
                                                                    top: 0,
                                                                    left: 0,
                                                                    filter: `hue-rotate(${colors[index % colors.length]})`
                                                                }}>
                                                                    <DotLottieReact
                                                                        src="/animantion/Blue circle 2.json"
                                                                        loop
                                                                        autoplay
                                                                        style={{ width: '100%', height: '100%' }}
                                                                    />
                                                                </div>
                                                                
                                                                <div style={{
                                                                    position: 'absolute',
                                                                    top: '50%',
                                                                    left: '50%',
                                                                    transform: 'translate(-50%, -50%)',
                                                                    zIndex: 1,
                                                                    textAlign: 'center',
                                                                    width: '80%',
                                                                    color: 'white',
                                                                    fontSize: '18px',
                                                                    fontFamily: 'Poppins',
                                                                    fontWeight: 'normal',
                                                                    lineHeight: '1.2'
                                                                }}>
                                                                    {chapter.name}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </Link>
                                                </div>
                                            ))
                                        ) : (
                                            <div className="col-12 text-center py-5">
                                                <p className="text-white">No chapters found in this category</p>
                                                <Link href="/practical-essentials" style={{
                                                    padding: '12px 30px',
                                                    background: 'linear-gradient(150deg, #44A6C5 0%, #1E4FFD 100%)',
                                                    borderRadius: '12px',
                                                    color: 'white',
                                                    textDecoration: 'none',
                                                    display: 'inline-block',
                                                    fontFamily: 'Poppins',
                                                    fontWeight: '600'
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
                    <section className="Macaroni-Sign-page pt-0 d-block d-lg-none">
                        <div className="container-fluid px-0">
                            <div className="macaroni-top">
                                <div className="container">
                                    <div className="row align-items-center">
                                        <div className="col-12 text-center">
                                            <div className="content">
                                                <h2 className="text-white mb-2">
                                                    {categoryName}
                                                </h2>
                                                <h6 className="text-white mb-0">Select Chapter</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="Macaroni-middle" style={{ paddingTop: '20px' }}>
                            <div className="container">
                                <div className="row g-3">
                                    {chapters && chapters.length > 0 ? (
                                        chapters?.map((chapter, index) => (
                                            <div className="col-6" key={chapter.id}>
                                                <Link href={`/practical-essentials/category?id=${chapter.id}`}>
                                                    <div style={{
                                                        display: 'flex',
                                                        alignItems: 'center',
                                                        justifyContent: 'center',
                                                        flexDirection: 'column',
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
                                                            position: 'absolute',
                                                            top: '50%',
                                                            left: '50%',
                                                            transform: 'translate(-50%, -50%)',
                                                            color: 'white',
                                                            fontSize: '14px',
                                                            fontWeight: 'normal',
                                                            margin: '0',
                                                            width: '75%',
                                                            textAlign: 'center',
                                                            wordWrap: 'break-word',
                                                            lineHeight: '1.3'
                                                        }}>{chapter.name}</h6>
                                                    </div>
                                                </Link>
                                            </div>
                                        ))
                                    ) : (
                                        <div className="col-12 text-center py-5">
                                            <p className="text-white">No chapters found</p>
                                            <Link href="/practical-essentials" style={{
                                                padding: '12px 30px',
                                                background: 'linear-gradient(150deg, #44A6C5 0%, #1E4FFD 100%)',
                                                borderRadius: '12px',
                                                color: 'white',
                                                textDecoration: 'none',
                                                display: 'inline-block',
                                                fontFamily: 'Poppins',
                                                fontWeight: '600'
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

export default page
