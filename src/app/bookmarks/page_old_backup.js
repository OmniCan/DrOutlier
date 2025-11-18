'use client';

import { useEffect, useState } from 'react';
import Navbar from '@/components/Navbar';
import Footer from '@/components/Footer';
import Cookies from 'js-cookie';
import Link from 'next/link';

export default function BookmarksPage() {
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  const [activeTab, setActiveTab] = useState('notes');

  useEffect(() => {
    const token = Cookies.get('user-token');
    setIsAuthenticated(!!token);
  }, []);

  const tabs = [
    { id: 'notes', label: 'Notes', icon: 'fas fa-file-alt', color: '#4CAF50' },
    { id: 'spotters', label: 'Spotters', icon: 'fas fa-image', color: '#2196F3' },
    { id: 'osce', label: 'OSCE', icon: 'fas fa-stethoscope', color: '#FF9800' },
    { id: 'quizora', label: 'Quizora', icon: 'fas fa-question-circle', color: '#9C27B0' },
  ];

  return (
    <>
      <Navbar />
      
      <div className="main-wrapper">
        <section style={{ minHeight: '80vh', paddingTop: '40px', paddingBottom: '60px' }}>
          <div className="container">
            {/* Page Header */}
            <div className="mb-5">
              <h1 className="text-white mb-2" style={{ fontSize: '32px', fontWeight: '700' }}>
                <i className="fas fa-bookmark me-3" style={{ color: '#FFA500' }}></i>
                Saved & Bookmarks
              </h1>
              <p style={{ color: 'rgba(255, 255, 255, 0.70)', fontSize: '16px' }}>
                Access all your saved content in one place
              </p>
            </div>

            {!isAuthenticated ? (
              <div className="text-center" style={{ minHeight: '300px', paddingTop: '100px' }}>
                <h3 className="text-white mb-4">Please Login to View Bookmarks</h3>
                <button 
                  onClick={() => {
                    const myModal = new bootstrap.Modal(document.getElementById('myModal'));
                    myModal.show();
                  }} 
                  className="loginBtn"
                >
                  Login Now
                </button>
              </div>
            ) : (
              <>
                {/* Tabs */}
                <div className="mb-4">
                  <div className="d-flex gap-2 flex-wrap">
                    {tabs.map((tab) => (
                      <button
                        key={tab.id}
                        onClick={() => setActiveTab(tab.id)}
                        className="btn"
                        style={{
                          background: activeTab === tab.id 
                            ? tab.color 
                            : 'rgba(255, 255, 255, 0.1)',
                          color: '#fff',
                          border: 'none',
                          padding: '12px 24px',
                          borderRadius: '8px',
                          fontWeight: '500',
                          transition: 'all 0.3s ease',
                        }}
                      >
                        <i className={`${tab.icon} me-2`}></i>
                        {tab.label}
                      </button>
                    ))}
                  </div>
                </div>

                {/* Content */}
                <div style={{
                  background: '#282D41',
                  borderRadius: '15px',
                  padding: '60px 40px',
                  textAlign: 'center',
                  border: '1px solid rgba(255, 255, 255, 0.1)',
                  minHeight: '400px',
                  display: 'flex',
                  flexDirection: 'column',
                  alignItems: 'center',
                  justifyContent: 'center',
                }}>
                  <div style={{
                    width: '80px',
                    height: '80px',
                    margin: '0 auto 20px',
                    background: `${tabs.find(t => t.id === activeTab)?.color}33`,
                    borderRadius: '50%',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                  }}>
                    <i 
                      className={tabs.find(t => t.id === activeTab)?.icon} 
                      style={{ 
                        fontSize: '36px', 
                        color: tabs.find(t => t.id === activeTab)?.color 
                      }}
                    ></i>
                  </div>
                  
                  <h4 className="text-white mb-3" style={{ fontSize: '20px', fontWeight: '600' }}>
                    No Saved {tabs.find(t => t.id === activeTab)?.label} Yet
                  </h4>
                  
                  <p style={{ 
                    color: 'rgba(255, 255, 255, 0.60)', 
                    marginBottom: '24px',
                    maxWidth: '400px',
                    fontSize: '15px',
                  }}>
                    Start bookmarking your favorite {tabs.find(t => t.id === activeTab)?.label.toLowerCase()} 
                    to access them quickly from here
                  </p>

                  <div className="d-flex gap-2 flex-wrap justify-content-center">
                    {activeTab === 'notes' && (
                      <Link href="/notes" className="loginBtn">
                        <i className="fas fa-file-alt me-2"></i>
                        Browse Notes
                      </Link>
                    )}
                    {activeTab === 'spotters' && (
                      <Link href="/spotters" className="loginBtn">
                        <i className="fas fa-image me-2"></i>
                        Browse Spotters
                      </Link>
                    )}
                    {activeTab === 'osce' && (
                      <Link href="/osce" className="loginBtn">
                        <i className="fas fa-stethoscope me-2"></i>
                        Browse OSCE
                      </Link>
                    )}
                    {activeTab === 'quizora' && (
                      <Link href="/quizora" className="loginBtn">
                        <i className="fas fa-question-circle me-2"></i>
                        Take Quizzes
                      </Link>
                    )}
                  </div>
                </div>

                {/* Info Box */}
                <div className="mt-4" style={{
                  background: 'rgba(18, 110, 151, 0.1)',
                  border: '1px solid rgba(18, 110, 151, 0.3)',
                  borderRadius: '10px',
                  padding: '20px',
                }}>
                  <div className="d-flex align-items-start">
                    <i className="fas fa-info-circle me-3 mt-1" style={{ color: '#126E97', fontSize: '20px' }}></i>
                    <div>
                      <h6 className="text-white mb-2">How to Bookmark Content</h6>
                      <p style={{ color: 'rgba(255, 255, 255, 0.70)', fontSize: '14px', marginBottom: 0 }}>
                        Click the bookmark icon <i className="fas fa-bookmark" style={{ color: '#FFA500' }}></i> on any note, spotter, 
                        OSCE case, or quiz question to save it for quick access later. All your bookmarked content will appear here 
                        organized by category.
                      </p>
                    </div>
                  </div>
                </div>
              </>
            )}
          </div>
        </section>
      </div>

      <Footer />
    </>
  );
}
